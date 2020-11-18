<?php
defined('BASEPATH') OR exit('No direct script access allowed');
header('Content-Type: application/json');

class Terminal_api extends CI_Controller {
	public function __construct()
	{
		parent::__construct();
		date_default_timezone_set('Africa/Lagos');
		
		$this->load->model('Admin_model');
		$this->load->model('Base_model');
		$this->load->model('User_model');
		$this->load->model('Game_model');
		$this->load->model('Option_model');
		$this->load->model('PlayerOption_model');
		$this->load->model('Setting_model');
		$this->load->model('Terminal_model');
		$this->load->model('TerminalOption_model');		
		$this->load->model('Week_model');
		$this->load->model('Prize_model');	
		$this->load->model('FundRequest_model');
		$this->load->model('Bet_model');
		$this->load->model('Summary_model');
		$this->load->model('DeleteRequest_model');		
		$this->load->model('Under_model');				
		$this->load->model('Calc_model');
	}

	public function reply($result, $message, $data)
	{
		$result = array('result'=>$result, 'message'=>$message, 'data'=>$data);
		echo json_encode($result);
	}

	private function checkLogin($req)
	{
		$sn = $req['sn'];
		if($sn=="") {
			$this->reply(1002, "sn required", null);
			return null;
		}

		$token = $req['token'];
		if($token=="")
		{
			$this->reply(1002, "token required", null);
			return null;
		}

		$terminal = $this->Terminal_model->getRow(array('terminal_no'=>$sn));
		if($terminal==null) {
			$this->reply(1002, "sn does not exist", null);
			return null;
		}
		if($terminal->status!=1) 
			return $this->reply(1002, "terminal is not allowed", null);

		if($token !=$terminal->token){
			$this->reply(1002, "token mismatch", null);
			return null;
		}
		return $terminal;
	}
	private function get_possible_unders($terminal)
	{
		$unders = $this->Under_model->getDatas(['status'=>1]);		
		$datas = [];
		foreach($unders as $under)
		{
			$mask = pow(2, $under->under -1);
			if($terminal->unders & $mask)
			{
				$datas[] = ['name'=>$under->name, 'under'=>	$under->under];
			}
		}
		return $datas;
	}

	private function checkMissedGames($gamelists, $bet)
	{
		$missed = array();		
		if($bet['type']=='Group')
		{
			foreach($bet['gamelist'] as $grp)
			{
				foreach($grp['list'] as $gameNo)
				{
					$bExist = false;
					foreach($gamelists as $game)
					{
						if($game->game_no==$gameNo)
						{
							$bExist = true;
							break;
						}
					}
					if($bExist==false) $missed[]=$gameNo;
				}
			}
		}
		else
		{
			foreach($bet['gamelist'] as $gameNo)
			{
				$bExist = false;
				foreach($gamelists as $game)
				{
					if($game->game_no==$gameNo)
					{
						$bExist = true;
						break;
					}
				}
				if($bExist==false) $missed[]=$gameNo;
			}
		}
		return $missed;
	}
	
	private function checkCombinationCount($bet)
	{
	    $under = $bet['under'];
	    $gamelists = $bet['gamelist'];
		if($bet['type']=='Group')
		{
			foreach($gamelists as $grp)
			{
			    $combines = $this->Calc_model->combination_count(count($grp['list']), $grp['under']);
			    if($combines > 15000)
			        return false;
			}
		}
		else
		{
		    $combines = $this->Calc_model->combination_count(count($gamelists), $under);
		    if($combines > 15000)
		        return false;
		}
		return true;
	}
	

	private function isSameBets($bet0, $bet1)	
	{
		if($bet0['type']!=$bet1['type']) return false;
		if($bet0['under']!=$bet1['under']) return false;
		if(count($bet0['gamelist']) != count($bet1['gamelist'])) return false;

		$len = count($bet0['gamelist']);
		if($bet0['type']=='Group')
		{
			for($iGrp = 0; $iGrp < $len; $iGrp++)
			{
				$grp0 = $bet0['gamelist'][$iGrp];
				$grp1 = $bet1['gamelist'][$iGrp];

				if($grp0['under'] != $grp1['under']) return false;
				if(count($grp0['list']) != count($grp1['list'])) return false;

				$len1 = count($grp0['list']);
				for($iGame=0; $iGame<$len1; $iGame++)
				{
					if($grp0['list'][$iGame] != $grp1['list'][$iGame]) return false;
				}
			}
		}
		else
		{
			for($iGame=0; $iGame<$len; $iGame++)
			{
				if($bet0['gamelist'][$iGame] != $bet1['gamelist'][$iGame]) return false;
			}
		}
		return true;
	}


	private function calc_line($under, $events)
	{
		$val =1 ;
		$division = 1;
		for($i=0; $i<$under; $i++)
		{
			$val = $val * ($events - $i);
			$division = $division * ($i + 1);
		}

		$lines = $val / $division;
		return $lines; 
	}
	
	private function calcLine($bet)
	{
		$line = 1;
		if ($bet['type'] == "Nap/Perm") {
			$line = $this->calc_line($bet['under'], count($bet['gamelist']));
		}
		else if ($bet['type'] == "Group") {
			foreach($bet['gamelist'] as $grp)
			{
				$line *= $this->calc_line($grp['under'], count($grp['list']));
			}
		}
		return $line;
	}


	private function calcTerminalSummary($term_id, $agent_id, $under, $commission, $week_no) 
	{
		$summaryId ="";
		$summaryId = $term_id.'_'.$under.'_'.$week_no;

		$sales = 0;
		$win = 0;
		$bets = $this->Bet_model->getDatas(array('terminal_id'=>$term_id, 'under'=>$under, 'week'=>$week_no, 'status'=>1));

		foreach($bets as $bet)
		{
			$sales += $bet->stake_amount;
			$win += $bet->won_amount;
		}		

		$data = array('summary_id'=>$summaryId, 'terminal_id'=>$term_id, 'agent_id'=>$agent_id, 
			'under'=>$under, 'commission'=>$commission, 'week_no'=>$week_no, 
			'sales'=>$sales, 'win'=>$win, 'payable'=>$sales * $commission/100);

		$row = $this->Summary_model->getRow(array('summary_id'=>$summaryId));

		if($row)
		{
			$this->Summary_model->updateData(array('Id'=>$row->Id),$data);
		}
		else
		{
			$this->Summary_model->insertData($data);
		}

	}

	private function generateRandomString($length = 10) {
		$characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
		$charactersLength = strlen($characters);
		$randomString = '';
		for ($i = 0; $i < $length; $i++) {
			$randomString .= $characters[rand(0, $charactersLength - 1)];
		}
		return $randomString;
	}	

	public function login() 
	{
		$req = json_decode(file_get_contents('php://input'), true);		
		$sn = $req['sn'];
		if($sn=="")return $this->reply(1001, "sn required", null);
		$password = $req['password'];
		if($password=="")return $this->reply(1001, "password required", null);

		$terminal = $this->Terminal_model->getRow(array('terminal_no'=>$sn));
		if($terminal==null) return $this->reply(1002, "sn dose not exist", null);
		if($terminal->status!=1) return $this->reply(1002, "terminal is not allowed", null);
		if($terminal->password!=$password)return $this->reply(1002, "wrong password", null);

		$token = $this->generateRandomString(32);
		$this->Terminal_model->updateData(array('Id'=>$terminal->Id), array('token'=>$token));

		$curWeekNo = $this->Setting_model->getCurrentWeekNo();
		if($curWeekNo==0) return $this->reply(-1, "no current weekno", null);

		$curWeek = $this->Week_model->getRow(array('week_no'=>$curWeekNo));
		if($curWeek==null) return $this->reply(-1, "week does not exist.", null);

		$games = array();
		$datas = $this->Game_model->getDatas(array('week_no'=>$curWeekNo, 'status'=>1), "game_no");
		foreach($datas as $data) {
			$games[] = $data->game_no;
		}

		$this->reply(1, "success", array(
			'sn'=>$sn,
			'token'=>$token,
			'possible_under'=>$this->get_possible_unders($terminal),
			'games'=>$games,
			'week'=>$curWeekNo,
			'start_at'=>$curWeek->start_at,
			'close_at'=>$curWeek->close_at,
			'validity'=>$curWeek->close_at,
			'type'=>$curWeek->types,
			'void_bet'=>$curWeek->void_bet,
			'credit_limit'=>$terminal->credit_limit
		));
	}

	public function reset() 
	{
		$req = json_decode(file_get_contents('php://input'), true);
		$terminal = $this->checkLogin($req);
		if($terminal==null) return;

		$curWeekNo = $this->Setting_model->getCurrentWeekNo();
		if($curWeekNo==0) return $this->reply(-1, "no current weekno", null);

		$curWeek = $this->Week_model->getRow(array('week_no'=>$curWeekNo));
		if($curWeek==null) return $this->reply(-1, "week does not exist.", null);

		$games = array();
		$datas = $this->Game_model->getDatas(array('week_no'=>$curWeekNo, 'status'=>1), "game_no");
		foreach($datas as $data) {
			$games[] = $data->game_no;
		}

		$this->reply(1, "success", array(
			'sn'=>$terminal->terminal_no,
			'token'=>$terminal->token,
			'possible_under'=>$this->get_possible_unders($terminal),
			'games'=>$games,
			'week'=>$curWeekNo,
			'start_at'=>$curWeek->start_at,
			'close_at'=>$curWeek->close_at,
			'validity'=>$curWeek->close_at,
			'type'=>$curWeek->types,
			'void_bet'=>$curWeek->void_bet,
			'credit_limit'=>$terminal->credit_limit
		));	
	
	}		


	private function find_game($gamelist, $gmaeno)
	{
		foreach($gamelist as $game)
		{
			if($game->game_no == $gmaeno)
				return $game;
		}
		return null;
	}
	private function make_game_list($gamelist, $games)
	{
		$data = [];
		foreach($games as $game_no)
		{
			$game = $this->find_game($gamelist, $game_no);
			if($game)
				$data[] = 	' '.$game->game_no.'. '.$game->home_team.' vs '.$game->away_team.' '.$game->prize;
		}
		return $data;
	}

	private function make_game_list_for_group($gamelist, $games)
	{
		$data = [];
		foreach($games as $group)
		{
			$gms = [];
			foreach($group["list"] as $game_no)
			{
				$game = $this->find_game($gamelist, $game_no);
				if($game)
					$gms[] = 	' '.$game->game_no.'. '.$game->home_team.' vs '.$game->away_team.' '.$game->prize;
			}
			$data[] = ["under"=>$group["under"], "list"=>$gms];
		}
		return $data;
	}


	public function make_bet()
	{
	   // $contents = file_get_contents('php://input');
	   // file_put_contents("in.txt", $contents);
	   // return;
	    
	   // $contents = '{"sn":"82757041","token":"PnqhtdW3rrpRzk973xpdIp4iGlZmkhCw","bets":[{"stake_amount":100,"under":3,"type":"Nap/Perm","gamelist":[1,2,3]}]}';
	   // $req = json_decode($contents, true);
	    
 		$req = json_decode(file_get_contents('php://input'), true);
		$terminal = $this->checkLogin($req);
		if($terminal==null) return;

		$reqBets = $req['bets'];

		//return $this->reply(-1, "text process1", null);		
		$agent = $this->User_model->getRow(array('Id'=>$terminal->agent_id));
		if($agent==null)
			return $this->reply(-1, "no agent", null);

		//check current week
		$maxWin = $this->Setting_model->getMaxWin();
		$curWeekNo = $this->Setting_model->getCurrentWeekNo();
		if($curWeekNo==0)
			return $this->reply(-1, "no current week_no", null);
		$curWeek = $this->Week_model->getRow(array('week_no'=>$curWeekNo));
		if($curWeek==null)
			return $this->reply(-1, "no current week_no", null);

		$curDate = date("Y-m-d H:i:s");
    	if ($curDate < $curWeek->start_at || $curDate > $curWeek->close_at) 
			return $this->reply(-1, "coming soon", null);

		//return $this->reply(-1, "text process2", null);

		//generate ticket no
		$lastTicketNo = $terminal->last_ticket_no;
		if($lastTicketNo < 1000000) 
		{
			srand(time() + $terminal->Id);
			$lastTicketNo = rand(1000000,9999999);
		}		
		$ticketRealNo = $lastTicketNo + 1;
		if($ticketRealNo >= 10000000)
			$ticketRealNo = 1000000;

		$ticketNo =  sprintf('%05d', $terminal->Id) . $ticketRealNo;


		//get gamelist
		$gamelists = $this->Game_model->getDatas(array('week_no'=>$curWeekNo, 'status'=>1));

		//get old bets
		$bets = $this->Bet_model->getBets(array('terminal_id'=>$terminal->Id, 'week'=>$curWeekNo));
		//return $this->reply(-1, "text process3", null);

		$betSeq = 1;
		//check betting
		$resultBets = array();
		
		foreach($reqBets as $bet)
		{
			//check missed games			
			$missed = $this->checkMissedGames($gamelists, $bet);
			//return $this->reply(-1, "text process33", null);
			if(count($missed) > 0)
			{
				$resultBets[] = array(
					'result'=>1004,
					'message'=>implode(' ', $missed).' :mismatch games',
					'type'=>$bet['type'],
					'under'=>$bet['under'],
					'gamelist'=>$bet['gamelist'],
					'stake_amount'=>$bet['stake_amount']
				);
				continue;	
			}
			
			if(!$this->checkCombinationCount($bet))
			{
				$resultBets[] = array(
					'result'=>1004,
					'message'=>'Combination greater than 15,000 not allowed',
					'type'=>$bet['type'],
					'under'=>$bet['under'],
					'gamelist'=>$bet['gamelist'],
					'stake_amount'=>$bet['stake_amount']
				);
				continue;
			}

			//check stake
			// if ($bet['stake_amount'] < $terminal->min_stake) {
			// 	$resultBets[] = array(
			// 		'result'=>1004,
			// 		'message'=>'stake amount is less than min_stake',
			// 		'type'=>$bet['type'],
			// 		'under'=>$bet['under'],
			// 		'gamelist'=>$bet['gamelist'],
			// 		'stake_amount'=>$bet['stake_amount']	
			// 	);
			// 	continue;	
			// }

// 			if ($bet['stake_amount'] > $terminal->max_stake) {
// 				$resultBets[] = array(
// 					'result'=>1004,
// 					'message'=>'stake amount is greater than max_stake',
// 					'type'=>$bet['type'],
// 					'under'=>$bet['under'],
// 					'gamelist'=>$bet['gamelist'],
// 					'stake_amount'=>$bet['stake_amount']	
// 				);
// 				continue;	
// 			}

			if ($bet['stake_amount'] > $terminal->credit_limit) {
				$resultBets[] = array(
					'result'=>1004,
					'message'=>'credit lack',
					'type'=>$bet['type'],
					'under'=>$bet['under'],
					'gamelist'=>$bet['gamelist'],
					'stake_amount'=>$bet['stake_amount']	
				);
				continue;	
			}
			//return $this->reply(-1, "text process4", $bet);
			
			//stake check again include old bets
			$totalStake = 0;
			foreach($bets as $oldBet)
			{	
                //if($oldBet['status']==2)continue;			    
				if($this->isSameBets($oldBet,$bet))
					$totalStake += $oldBet['stake_amount'];
			}			
			//return $this->reply(-1, "text process44", $totalStake);
			//return;
			
			$under = $this->Under_model->getRow(['under'=>$bet['under']]);
			if(($totalStake + $bet['stake_amount']) > $under->max_stake)
			{
				$resultBets[] = array(
					'result'=>1004,
					'message'=>'stake amount is greater than under\'s max_stake',
					'type'=>$bet['type'],
					'under'=>$bet['under'],
					'gamelist'=>$bet['gamelist'],
					'stake_amount'=>$bet['stake_amount']
				);
				continue;
			}
			//return $this->reply(-1, "text process5", $bet);

			//line calc
			$line = $this->calcLine($bet);
			if($line==0)
			{
				$resultBets[] = array(
					'result'=>1003,
					'message'=>'apl is zero',
					'type'=>$bet['type'],
					'under'=>$bet['under'],
					'gamelist'=>$bet['gamelist'],
					'stake_amount'=>$bet['stake_amount']	
				);
				continue;				
			}

			//make new bet
			$newBet = array(
				'bet_id'=>$ticketNo.sprintf('%02d', $betSeq),
				'bet_time'=> $curDate,
				'ticket_no'=>$ticketNo,
				'terminal_id'=>$terminal->Id,
				'agent_id'=>$terminal->agent_id,
				'stake_amount'=>$bet['stake_amount'],
				'gamelist'=>$bet['gamelist'],
				'week'=>$curWeekNo,
				'under'=>$bet['under'],
				'type'=>$bet['type'],
				'apl'=>$bet['stake_amount'] / $line,
			);

			$newBet1 = array(
				'bet_id'=>$ticketRealNo.sprintf('%02d', $betSeq),
				'bet_time'=> $curDate,
				'ticket_no'=>$ticketRealNo,
				'terminal_id'=>$terminal->Id,
				'agent_id'=>$terminal->agent_id,
				'stake_amount'=>$bet['stake_amount'],
				'gamelist'=>$bet['gamelist'],
				'week'=>$curWeekNo,
				'under'=>$bet['under'],
				'type'=>$bet['type'],
				'apl'=>$bet['stake_amount'] / $line,
			);
			$betSeq ++;

			//save new bet
			$this->Bet_model->addNewBet($terminal->Id, $newBet);

			$commission = 0;
			if($under!=null) $commission = $under->commission;
			$this->calcTerminalSummary($terminal->Id,$terminal->agent_id, $under->under, $commission, $curWeekNo);
			//return $this->reply(-1, "text process6", $bet);

			//reduce user stake
			$terminal->credit_limit -= $bet['stake_amount'];
			$this->Terminal_model->updateData(array('Id'=>$terminal->Id), array('credit_limit'=>$terminal->credit_limit));


			$bets[]=$newBet;
			$maxwin = 0;
			$games = [];
			if($bet['type'] == 'Group')
			{
				$maxwin = $this->Calc_model->calc_win_for_group($bet['gamelist'], $bet['under'], $gamelists, $bet['stake_amount'], true);
				$games = $this->make_game_list_for_group($gamelists, $bet['gamelist']);
			}
			else
			{
				$maxwin = $this->Calc_model->calc_win($bet['gamelist'], $bet['under'], $gamelists, $bet['stake_amount'], true);
				$games = $this->make_game_list($gamelists, $bet['gamelist']);
			}
			
			if ($maxwin > $maxWin) $maxwin = $maxWin;
		
			$resultBets[] = array(
				'result'=>1,
				'message'=>'success',
				'type'=>$bet['type'],
				'under'=>$bet['under'],
				'gamelist'=>$games,
				'stake_amount'=>$bet['stake_amount'],
				'bet_id'=>$newBet1['bet_id'],
				'apl'=>$newBet1['apl'],
				'maxwin'=> number_format($maxwin, 2)
			);
		}

		$this->Terminal_model->updateData(array('Id'=>$terminal->Id), array('last_ticket_no'=>$ticketRealNo));
		$this->reply(1, "success", array(
			'ticket_no'=>$ticketRealNo,
			'possible_under'=>$this->get_possible_unders($terminal),
			'bet_time'=>$curDate,
			'week'=>$curWeekNo,
			'agent_id'=>$agent->user_id,
			'user_id'=>0,
			'terminal_id'=>$terminal->terminal_no,
			'bets'=>$resultBets
		));
	}	

	public function results() 
	{
		$req = json_decode(file_get_contents('php://input'), true);

		$terminal = $this->checkLogin($req);
		if($terminal==null) return;

		$curWeekNo = $req['week'];
		if($curWeekNo==0 || $curWeekNo=="")
		{
			$curWeekNo = $this->Setting_model->getCurrentWeekNo();
			if($curWeekNo==0) return $this->reply(-1, "no current weekno", null);	
		}

		$games = array();
		$datas = $this->Game_model->getDatas(array('week_no'=>$curWeekNo, 'status'=>1, 'checked'=>1), "game_no");
		foreach($datas as $data) {
			$games[] = $data->game_no;
		}
		$this->reply(1, "success", array('week'=>$curWeekNo, 'drawn'=>$games, 'possible_under'=>$this->get_possible_unders($terminal)));
	}
	
	public function reprint() 
	{
		$req = json_decode(file_get_contents('php://input'), true);
		$terminal = $this->checkLogin($req);
		if($terminal==null) return;

		$ticketNo = $req['ticket_no'];
		if($ticketNo=="" || $ticketNo=="0")
			$ticketNo = $terminal->last_ticket_no;
		
		$tsn = sprintf('%05d', $terminal->Id) .$ticketNo;
		$bets = $this->Bet_model->getBets(array('ticket_no'=>$tsn));
		if(count($bets)==0)return $this->reply(1002, "ticket_no dose not exist", null);

		$gamelists = $this->Game_model->getDatas(array('week_no'=>$bets[0]['week'], 'status'=>1));
		//remove first  5 chars
		for($i=0; $i<count($bets); $i++)
		{
			$bets[$i]['bet_id'] = substr($bets[$i]['bet_id'], 5);
			$bets[$i]['ticket_no'] = substr($bets[$i]['ticket_no'], 5);
			if($bets[$i]['type'] == 'Group')
			{
				$bets[$i]['maxwin'] = number_format($this->Calc_model->calc_win_for_group($bets[$i]['gamelist'], $bets[$i]['under'], $gamelists, $bets[$i]['stake_amount'], true), 2);
				$bets[$i]['gamelist'] = $this->make_game_list_for_group($gamelists, $bets[$i]['gamelist']);
			}
			else
			{
				$bets[$i]['maxwin'] = number_format($this->Calc_model->calc_win($bets[$i]['gamelist'], $bets[$i]['under'], $gamelists, $bets[$i]['stake_amount'], true), 2);
				$bets[$i]['gamelist'] = $this->make_game_list($gamelists, $bets[$i]['gamelist']);
			}
		}		

		$term = $this->Terminal_model->getRow(array('Id'=>$bets[0]['terminal_id']));
		if($term==null) return $this->reply(1002, "terminal dose not exist", null);

		$agentId="";
		$agent=$this->User_model->getRow(array('Id'=>$term->agent_id));
		if($agent!=null) $agentId = $agent->user_id;

		$this->reply(1, "success", array(
			'ticket_no'=>$ticketNo,
			'possible_under'=>$this->get_possible_unders($terminal),
			'bet_time'=>$bets[0]['bet_time'],			
			'week'=>$bets[0]['week'],
			'terminal_id'=>$term->terminal_no,
			'agent_id'=>$agentId,
			'bets'=>$bets
		));
	}
	
	public function win_list() 
	{

		$req = json_decode(file_get_contents('php://input'), true);
		$terminal = $this->checkLogin($req);
		if($terminal==null) return;

		$count_per_page = 20;
		$current_page = $req['current_page'];
		if($current_page <= 0) return $this->reply(1002, 'current_page mismatch', null);
		
		$curWeekNo = $req['week'];
		if($curWeekNo==0 || $curWeekNo =='') $curWeekNo = $this->Setting_model->getCurrentWeekNo();
		if($curWeekNo==0) return $this->reply(-1, 'no current weekno', null);

		$cond = array('terminal_id'=>$terminal->Id, 'win_result'=>'Win', 'week'=>$curWeekNo);
		$ticketNo = $req['ticket_no'];
		if($ticketNo !="") $cond['ticket_no'] = sprintf('%05d', $terminal->Id) .$ticketNo;

		$win_list = array();
		$bets = $this->Bet_model->getDatas($cond, 'ticket_no');
		foreach($bets as $bet)
		{
			if( (!isset($win_list[$bet->ticket_no])))  
				$ticket = array('ticket_no'=>$bet->ticket_no, 'bet_id'=>array(), 'amount'=>array(), 'total_winning'=>0, 'bet_time'=>$bet->bet_time);
			else
				$ticket = $win_list[$bet->ticket_no];

			$ticket['bet_id'][]=substr($bet->bet_id, 5);
			$ticket['amount'][]=floor($bet->won_amount);
			$ticket['total_winning'] += floor($bet->won_amount);

			$win_list[$bet->ticket_no] = $ticket;
		}

		$lastPage = count($win_list)/$count_per_page;
		if(count($win_list) % $count_per_page) $lastPage++;

		$totalWon = 0;
		$page_list = array();
		if($current_page <= $lastPage)
		{
			$lastNum =  $current_page * $count_per_page;
			if($lastNum > count($win_list)) $lastNum = count($win_list);
			
			$i = 0;
			foreach($win_list as $tickNo=>$data)
			{
				if($i >= (($current_page-1) * $count_per_page) && $i < $lastNum)
				{
					$page_list[] = $data;
					$totalWon += $data['total_winning'];
				}
				$i++;	
			}
		}

		$agentId = "";
		$agent = $this->User_model->getRow(array('Id'=>$terminal->agent_id));
		if($agent!=null) $agentId = $agent->user_id;

		$this->reply(1, "success", array(
			'week'=>$curWeekNo,
			'possible_under'=>$this->get_possible_unders($terminal),			
			'agent_id'=>$agentId,
			'current_page'=>$current_page,
			'last_page'=>$lastPage,
			'win_list'=>$page_list,
			'total'=>$totalWon
		));
	}	

	public function report() 
	{
	   // $contents = file_get_contents('php://input');
	   // file_put_contents("in.txt", $contents);
	   // return;
	   //$contents = '{"sn":"john","token":"d0jwrfFWNSY82YoAaaMxg6gpUVg7OURB","week":"1"}';
	   //$req = json_decode($contents, true);
	  
	   
	   $req = json_decode(file_get_contents('php://input'), true);
	   $terminal = $this->checkLogin($req);
	   if($terminal==null) return;

	   $curWeekNo = $req['week'];
	   if($curWeekNo==0 || $curWeekNo=="") $curWeekNo = $this->Setting_model->getCurrentWeekNo();
	   if($curWeekNo==0) return $this->reply(-1, 'no current weekno', null);

	   $curWeek = $this->Week_model->getRow(array('week_no'=>$curWeekNo));
	   if($curWeek==null) return $this->reply(-1, 'week does not exist.', null);

	   $summaries = $this->Summary_model->getDatas(array('week_no'=>$curWeekNo, 'terminal_id'=>$terminal->Id), 'under');
	   $bets = $this->Bet_model->getDatas(array('terminal_id'=>$terminal->Id, 'week'=>$curWeekNo, 'status'=>1));

	   $total_sale=0;
	   $total_payable=0;
	   $total_win= 0;
	   $odd_summary = array();
	   foreach($summaries as $summary)
	   {
		   $total_sale += $summary->sales;
		   $total_payable += $summary->payable;
		   $total_win += $summary->win;			

		   $count = 0;			
		   if($summary->sales >0)
		   {
			   foreach($bets as $bet){
				   if($summary->under== $bet->under) $count++;
			   }	
		   }
		   $odd_summary[]=array('under'=>$summary->under,
						   'count'=>$count, 
						   'sale'=>$summary->sales, 
						   'payable'=>$summary->payable,
						   'win'=>$summary->win);
	   }

	   $agentId = "";
	   $agent=$this->User_model->getRow(array('Id'=>$terminal->agent_id));
	   if($agent!=null) $agentId= $agent->user_id;

	   $gross = [];
	   $wins = [];
	   foreach($odd_summary as $sumer)
	   {
			$gross[] = 'U'.$sumer['under'].' ('.$sumer['count'].') = '.number_format($sumer['sale'], 2).' -> '.number_format($sumer['payable'], 2);
			$wins[] = 'U'.$sumer['under'].' ('.$sumer['count'].') = '.number_format($sumer['win'], 2);
	   }

	   $this->reply(1, 'success', array(
		   'week'=> $curWeekNo,
		   'close_at' => $curWeek->close_at,
		   'possible_under'=>$this->get_possible_unders($terminal),			
		   'agent_id'=> $agentId,
		   'gross'=>$gross,
		   'wins' =>$wins,
		   'total_payable' => number_format($total_payable, 2),
		   'total_win' => number_format($total_win, 2)
	   ));
	}
	
	public function credit_limit() {
		$req = json_decode(file_get_contents('php://input'), true);

		$terminal = $this->checkLogin($req);
		if($terminal==null) return;
		$this->reply(1, 'success', array('credit_limit'=>$terminal->credit_limit,
										'possible_under'=>$this->get_possible_unders($terminal)));
	}	

	public function logout() {
		$req = json_decode(file_get_contents('php://input'), true);

		$terminal = $this->checkLogin($req);
		if($terminal==null) return;
		$token = $this->generateRandomString(32);
		$this->Terminal_model->updateData(array('Id'=>$terminal->Id), array('token'=>$token));
		$this->reply(1, 'success',null);
	}

	public function void_bet()
	{
		$req = json_decode(file_get_contents('php://input'), true);

		$terminal = $this->checkLogin($req);
		if($terminal==null) return;
		//check user
		$betId = sprintf('%05d', $terminal->Id).$req['bet_id'];		
		
		$bets = $this->Bet_model->getBets(array('bet_id'=>$betId, 'terminal_id'=>$terminal->Id));
		if(count($bets)==0)
			return $this->reply(-1, "bet_id dose not exist", null);

		$bet = $bets[0];

		$week = $this->Week_model->getRow(array('week_no'=>$bet['week']));
		if($week==null)
			return $this->reply(-1, "week dose not exist", null);

		$curDate = new DateTime();
		if($curDate->format('Y-m-d H:i:s') > $week->close_at)
			return $this->reply(1004, "bet does not change in past week", null);

		$curDate->sub(new DateInterval('PT'.$week->void_bet.'H'));
		if($curDate->format('Y-m-d H:i:s') > $bet['bet_time'])
			return $this->reply(1003, "void time passed", null);

		$gamelists = $this->Game_model->getDatas(array('week_no'=>$week->week_no, 'status'=>1));
		if($gamelists==null)
			return $this->reply(-1, "game does not exist", null);

		$missed = $this->checkMissedGames($gamelists, $bet);
		if(count($missed)>0)
			return $this->reply(1003, "void failed", null);

		//save deelte request
		$row = $this->DeleteRequest_model->getRow(array('bet_id'=>$bet['Id']));
		if($row!=null)
			return $this->reply(1003, "already requested", null);

		$this->DeleteRequest_model->insertData(array('bet_id'=>$bet['Id'], 'terminal_id'=>$terminal->Id, 'agent_id'=>$terminal->agent_id));

		//update bet status
		$this->Bet_model->updateData(array('bet_id'=>$betId), array('status'=>2));

		
		$commission = 0 ;
		$under = $this->Under_model->getRow(['under'=>$bet['under']]);
		if($under!=null) $commission = $under->commission;

		//return $this->reply(1003, "kkk", null);
		$this->calcTerminalSummary($terminal->Id, $terminal->agent_id, $bet['under'], $commission, $bet['week']);
		return $this->reply(1, "success", ['possible_under'=>$this->get_possible_unders($terminal)]);
	}

	public function password_change() 
	{
		$req = json_decode(file_get_contents('php://input'), true);
		$terminal = $this->checkLogin($req);
		if($terminal==null) return;

		$newPasswd = $req['new_password'];
		if($newPasswd=="")
			return $this->reply(1001, 'enter new password', null);	
		$this->Terminal_model->updateData(array('Id'=>$terminal->Id), array('password'=>$newPasswd));
		return $this->reply(1, 'success', ['possible_under'=>$this->get_possible_unders($terminal)]);
	}
	
	public function fixtures()
	{
		$req = json_decode(file_get_contents('php://input'), true);
		$terminal = $this->checkLogin($req);
		if($terminal==null) return;
		
		$page = 1;
		if(isset($req['page_no']))$page = $req['page_no'];

		$curWeekNo = $this->Setting_model->getCurrentWeekNo();
		$datas = $this->Game_model->getDatas(array('week_no'=>$curWeekNo, 'status'=>1), "game_no");
		$fixtures = [];
		
		$cnt_per_page = 50;
		$start = ($page-1) * $cnt_per_page;
		$end = $page * $cnt_per_page;
		
		for($i=0; $i<count($datas); $i++)
		{
		    if($i < $start) continue;
		    if($i >= $end) continue;
		    
		    $data = $datas[$i];
		    $fixtures[] = $data->game_no.'. '.$data->home_team.' vs '.$data->away_team.'  '. number_format($data->prize, 2);
		}
		
		$total_page = intval(count($datas) / $cnt_per_page);
		if(count($datas) % $cnt_per_page) $total_page ++;
		
		$this->reply(1, 'success', ['week'=>$curWeekNo, 
		                            'fixtures'=>$fixtures, 
		                            'page' => $page.' / '.$total_page.'  ('.count($datas).')',
		                            'possible_under'=>$this->get_possible_unders($terminal)]);
	}
	
	public function void_list() 
	{
		$req = json_decode(file_get_contents('php://input'), true);

		$terminal = $this->checkLogin($req);
		if($terminal==null) return;

		$curWeekNo = $req['week'];
		if($curWeekNo==0) $curWeekNo = $this->Setting_model->getCurrentWeekNo();
		if($curWeekNo==0) return $this->reply(-1, 'no current weekno', null);

		$bets = $this->Bet_model->getBets(array('terminal_id'=>$terminal->Id, 'week'=>$curWeekNo, 'status'=>2));

		$agentId = "";
		$agent=$this->User_model->getRow(array('Id'=>$terminal->agent_id));
		if($agent!=null) $agentId= $agent->user_id;

		$results = array();
		foreach($bets as $bet)
		{
			$results[] = array('bet_id'=> substr($bet['bet_id'], 5), 'stake_amount'=>$bet['stake_amount']);
		}

		$this->reply(1, 'success', array('week'=>$curWeekNo, 'agent_id'=>$agentId, 'void_list'=>$results,'possible_under'=>$this->get_possible_unders($terminal)));
	}

	public function search() 
	{
		$req = json_decode(file_get_contents('php://input'), true);
				
		$terminal = $this->checkLogin($req);
		if($terminal==null) return;
		$curWeekNo = $req['week'];
		if($curWeekNo==0) $curWeekNo = $this->Setting_model->getCurrentWeekNo();
		if($curWeekNo==0) return $this->reply(-1, 'no current weekno', null);

		$searchWord = $req['searchword'];
		if($searchWord=="")return $this->reply(-1, 'no search word', null);

		$isTicket = $req['is_ticketid'];

		$cond = array('terminal_id'=>$terminal->Id, 'week'=>$curWeekNo);
		// if($isTicket==1)$cond['ticket_no'] = $searchWord;
		// else $cond['bet_id'] = $searchWord;

		$restBets = array();
		$bets = $this->Bet_model->getBets($cond);
		for($i = 0; $i<count($bets); $i++)
		{
			if($isTicket==1)
			{
				if(stristr($bets[$i]['ticket_no'], $searchWord)===FALSE) continue;
			}
			else if(stristr($bets[$i]['bet_id'], $searchWord)===FALSE)continue;

			// if($bets[$i]['win_result']=='')
			// {
			// 	unset($bets[$i]['win_result']);
			// 	unset($bets[$i]['won_amount']);
			// }

			if($bets[$i]['status']==2) 
				$bets[$i]['status'] = 'Void';
			else
			{
				if($bets[$i]['win_result']=='')
					$bets[$i]['status'] = 'Active';
				else
				{
					$bets[$i]['status'] = $bets[$i]['win_result'];
				}
			}
			
			$bets[$i]['bet_id'] = substr($bets[$i]['bet_id'], 5);
			$bets[$i]['ticket_no'] = substr($bets[$i]['ticket_no'], 5);
			$restBets[] = $bets[$i];
			if(count($restBets) >= 10) break;
		}
		$this->reply(1, 'success', array('week'=>$curWeekNo, 'search_result'=>$restBets, 'possible_under'=>$this->get_possible_unders($terminal)));
	}	
	
	public function ticket_list() 
	{
	    //file_put_contents("uploads/in.txt", file_get_contents('php://input'));
	    //return;
	    //$contents = '{"sn":"82163666","token":"HvwtYhUsGiPObTz2Jx34Lu7Hh7YOnx77"}';
	    //$req = json_decode($contents, true);
	    
	    
		$req = json_decode(file_get_contents('php://input'), true);

		$terminal = $this->checkLogin($req);
		if($terminal==null) return;
		$curWeekNo = $this->Setting_model->getCurrentWeekNo();
		if($curWeekNo==0) return $this->reply(-1, 'no current weekno', null);

		$bets = $this->Bet_model->getDatas(array('terminal_id'=>$terminal->Id, 'week'=>$curWeekNo), 'bet_time', 'DESC');
		$ticketList = array();
		foreach($bets as $bet)
		{				
			$ticketList[$bet->ticket_no] = 1;
			if(count($ticketList)>=7) break;
		}

		$tickets = array();
		foreach($ticketList as $tickno=>$val) $tickets[]=substr($tickno, 5);
		$this->reply(1, 'success', array('ticket_list'=>$tickets,'possible_under'=>$this->get_possible_unders($terminal)));
	}


	public function bet_counts() 
	{
		$req = json_decode(file_get_contents('php://input'), true);
				
		$terminal = $this->checkLogin($req);
		if($terminal== null) return;
		$curWeekNo = $req['week'];
		if($curWeekNo==0) $curWeekNo = $this->Setting_model->getCurrentWeekNo();
		if($curWeekNo==0) return $this->reply(-1, 'no current weekno', null);

		$betCnts = $this->Bet_model->getCounts(array('terminal_id'=>$terminal->Id, 'week'=>$curWeekNo, 'status'=>1));
		$this->reply(1, 'success', array('week'=>$curWeekNo, 'bets_counts'=>$betCnts, 'possible_under'=>$this->get_possible_unders($terminal)));
	}

}