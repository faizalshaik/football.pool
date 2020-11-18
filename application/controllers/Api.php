<?php
	defined('BASEPATH') or exit('No direct script access allowed');

	class Api extends CI_Controller
	{
		public function __construct()
		{
			parent::__construct();
			date_default_timezone_set('Africa/Lagos');
			
			$this->load->model('Admin_model');
			$this->load->model('Base_model');
			$this->load->model('User_model');
			$this->load->model('Game_model');
			$this->load->model('Setting_model');
			$this->load->model('Terminal_model');
			$this->load->model('Week_model');
			$this->load->model('Prize_model');
			$this->load->model('FundRequest_model');
			$this->load->model('Bet_model');
			$this->load->model('Summary_model');
			$this->load->model('DeleteRequest_model');
			$this->load->model('Calc_model');
			$this->load->model('Under_model');
			
			ini_set('memory_limit', '4096M'); // or you could use 1G			
		}
		
		public function test1()
		{
		    $events = 16;
		    $under = 9;
    		$counts = $this->Calc_model->combination_count($events, $under);
    		echo $counts; 
		}

		public function reply($status, $message, $data)
		{
			$result = array('status' => $status, 'message' => $message, 'data' => $data);
			echo json_encode($result);
		}

		public function test()
		{
			$amount = 25000;
			$line = $this->calc_line(array(3), 3);
			echo $line . ', apl=' . $amount / $line;
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
			$terminalId = 0;
			$summaryId = $term_id.'_'.$under.'_'.$week_no;
	
			$sales = 0;
			$win = 0;
			$bets = $this->Bet_model->getDatas(array('terminal_id'=>$terminalId, 'under'=>$under, 'week'=>$week_no, 'status'=>1));
	
			foreach($bets as $bet)
			{
				$sales += $bet->stake_amount;
				$win += $bet->won_amount;
			}		
	
			$data = array('summary_id'=>$summaryId, 'terminal_id'=>$terminalId, 'agent_id'=>$agent_id, 
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



		private function classify_scores($games, $scores)
		{
			$scored = array();
			$unscored = array();
			foreach ($games as $game) {
				$exist = false;
				foreach ($scores as $score) {
					if ($game == $score) {
						$exist = true;
						break;
					}
				}
				if ($exist) $scored[] = $game;
				else $unscored[] = $game;
			}
			return array('good' => $scored, 'bad' => $unscored);
		}
		private function find_data($list, $id)
		{
			foreach ($list as $data) {
				if ($data->Id == $id) return $data;
			}
			return null;
		}

		private function get_under($unders, $under)
		{
			foreach($unders as $u)
			{
				if($u->under == $under) return $u;
			}
			return null;
		}
		
// 		public function test01()
// 		{
// 		    $curWeekNo = 1;
// 			$scores = $this->Game_model->getDatas(array('status' => 1, 'week_no' => $curWeekNo, 'checked' => 1));
// 			$scorelists = array();
// 			foreach ($scores as $game) $scorelists[] = $game->game_no;
// // 			echo json_encode($scorelists);
// 			$bets = $this->Bet_model->getBets(array('status' => 1, 'week' => $curWeekNo, 'bet_id'=>'00001876276101'));
// 			echo json_encode($bets);
			
// // 			$bet = $bets[0];
			
// // 			$classifiedData = $this->classify_scores($bet['gamelist'], $scorelists);
// // 			echo json_encode($classifiedData);
// 		}
		

		public function apply_game_result()
		{
			$this->logonCheck();

            $maxWin = $this->Setting_model->getMaxWin();
			$panelPrize = $this->Setting_model->getPanelPrize();
			$curWeekNo = $this->Setting_model->getCurrentWeekNo();
			$gamelists = $this->Game_model->getDatas(array('week_no'=>$curWeekNo, 'status'=>1));
			$unders = $this->Under_model->getDatas(null);


			$scores = $this->Game_model->getDatas(array('status' => 1, 'week_no' => $curWeekNo, 'checked' => 1));
			$scorelists = array();
			foreach ($scores as $game) $scorelists[] = $game->game_no;

			// $terminals = $this->Terminal_model->getDatas(array('status' => 1));
			// $players = $this->User_model->getDatas(array('status' => 1, 'type'=>'player'));
			$terminals = $this->Terminal_model->getDatas(null);
			//clear all summary
			$this->Summary_model->deleteRow(array('week_no'=>$curWeekNo));
			
			
            $this->Bet_model->updateData(array('status' => 2, 'week' => $curWeekNo), ['score_list'=>'[]', 'unscore_list' =>'[]', 'win_result' => '', 'won_amount' =>0]);
            
			foreach($terminals as $terminal)
			{
				$updateBetArray = array();
				$updateSummaryArray = array();	
				$bets = $this->Bet_model->getBets(array('status' => 1, 'week' => $curWeekNo, 'terminal_id'=>$terminal->Id));

				foreach ($bets as $bet) {
					//classify scoredlist
					$scoreList = null;
					$unscoreList = null;
					$won_amount = 0;
					if ($bet['type'] == "Nap/Perm") {
					    
					   // if(count($bet['gamelist']) > 30)
					   //     echo $bet['Id'];
					        
						$classifiedData = $this->classify_scores($bet['gamelist'], $scorelists);
						$scoreList = $classifiedData['good'];
						$unscoreList = $classifiedData['bad'];
				 		$won_amount = $this->Calc_model->calc_win($bet['gamelist'], $bet['under'], $gamelists, $bet['stake_amount'], false, $panelPrize);

					} else if ($bet['type'] == "Group") {
						$scoreList = array();
						$unscoreList = array();
						foreach ($bet['gamelist'] as $grp) {
							$classifiedData = $this->classify_scores($grp['list'], $scorelists);
							$scoreList[] = array('under' => $grp['under'], 'list' => $classifiedData['good']);
							$unscoreList[] = array('under' => $grp['under'], 'list' => $classifiedData['bad']);
						}
				 		$won_amount = $this->Calc_model->calc_win_for_group($bet['gamelist'], $bet['under'], $gamelists, $bet['stake_amount'], false, $panelPrize);
					}
					
					if($won_amount > $maxWin) 
					    $won_amount = $maxWin;

                    $win_result = "";
					if ($won_amount > 0)
						$win_result = "Win";
					
					$apl = $bet['stake_amount'] / $this->calcLine($bet);
					$updateBetArray[] = array('Id' => $bet['Id'],
							'score_list' => json_encode($scoreList),
							'unscore_list' => json_encode($unscoreList),
							'apl' => $apl,
							'win_result' => $win_result,
							'won_amount' => $won_amount);

				// 	summary making	
					$summaryId = $terminal->Id.'_'.$bet['under'].'_'.$bet['week'];


					$under = $this->get_under($unders, $bet['under']);
					$commission = 0;
					if($under) $commission = $under->commission;

					if(isset($updateSummaryArray[$summaryId]))
					 	$summary = $updateSummaryArray[$summaryId];
					else 
						 $summary = array('summary_id'=>$summaryId, 'terminal_id' => $terminal->Id, 'agent_id'=>$terminal->agent_id, 						 
						 'under'=>$bet['under'], 'commission'=>$commission, 'week_no'=>$bet['week'], 						 
						 'sales'=>0, 'win'=>0, 'payable'=>0);

					$summary['sales'] += $bet['stake_amount'];
					$summary['win'] += $won_amount;
					$summary['payable'] += $bet['stake_amount'] * $commission/100;
					$updateSummaryArray[$summaryId] = $summary;	
					
					if(count($updateBetArray) >1000)
					{
					    $this->Bet_model->updateBatch($updateBetArray);
					    $updateBetArray = [];
					}
				}
				
				//update bets 
				if(count($updateBetArray) >0)
					$this->Bet_model->updateBatch($updateBetArray);

				//insert sumarries
				$summaryArr = array();
				foreach($updateSummaryArray as $summaryId=>$summary)
					$summaryArr[] = $summary;
				if(count($summaryArr) >0)
					$this->Summary_model->insertBatch($summaryArr);					
			}

			$this->reply(200, "ok", null);
		}



		private function checkMissedGames($gamelists, $bet)
		{
			$missed = array();
			if ($bet['type'] == 'Group') {
				foreach ($bet['gamelist'] as $grp) {
					foreach ($grp['list'] as $gameNo) {
						$bExist = false;
						foreach ($gamelists as $game) {
							if ($game->game_no == $gameNo) {
								$bExist = true;
								break;
							}
						}
						if ($bExist == false) $missed[] = $gameNo;
					}
				}
			} else {
				foreach ($bet['gamelist'] as $gameNo) {
					$bExist = false;
					foreach ($gamelists as $game) {
						if ($game->game_no == $gameNo) {
							$bExist = true;
							break;
						}
					}
					if ($bExist == false) $missed[] = $gameNo;
				}
			}
			return $missed;
		}

		public function void_bet()
		{
			$this->logonCheck();
			$id = $this->input->post('Id');	
			$bets = $this->Bet_model->getBets(array('Id' => $id));

			if (count($bets) == 0)
				return $this->reply(-1, "bet_id dose not exist", null);
			$bet = $bets[0];
			$betId = $bet['Id'];

			$terminal = $this->Terminal_model->getRow(array('Id' => $bet['terminal_id']));
			if ($terminal == null)
				return $this->reply(-1, "terminal dose not exist", null);

			$week = $this->Week_model->getRow(array('week_no' => $bet['week']));
			if ($week == null)
				return $this->reply(-1, "week dose not exist", null);

			$curDate = new DateTime();
			if ($curDate->format('Y-m-d H:i:s') > $week->close_at)
				return $this->reply(1004, "bet does not change in past week", null);

			$curDate->sub(new DateInterval('PT' . $week->void_bet . 'H'));
			if ($curDate->format('Y-m-d H:i:s') > $bet['bet_time'])
				return $this->reply(1003, "void time passed", null);

			$gamelists = $this->Game_model->getDatas(array('week_no' => $week->week_no, 'status' => 1));
			if ($gamelists == null)
				return $this->reply(-1, "game does not exist", null);

			$missed = $this->checkMissedGames($gamelists, $bet);
			if (count($missed) > 0)
				return $this->reply(1003, "void failed", null);

			//save deelte request
			$row = $this->DeleteRequest_model->getRow(array('bet_id' => $betId));
			if ($row != null)
				return $this->reply(1003, "already requested", null);

			$this->DeleteRequest_model->insertData(array('bet_id' => $betId, 'terminal_id' => $terminal->Id, 'agent_id' => $terminal->agent_id));

			//update bet status
			$this->Bet_model->updateData(array('Id' => $betId), array('status' => 2));

			$under = $this->Under_model->getRow(['under'=>$bet['under']]);
			$commission = 0;
			if($under!=null) $commission = $under->commission;

			//return $this->reply(1003, "kkk", null);

			$this->calcTerminalSummary($terminal->Id, $terminal->agent_id, $bet['under'], $commission, $bet['week']);
			return $this->reply(200, "success", null);
		}

		public function unVoid_bet()
		{
			$this->logonCheck();
			$id = $this->input->post('Id');
			$bets = $this->Bet_model->getBets(array('Id' => $id));
			if (count($bets) == 0)
				return $this->reply(-1, "bet_id dose not exist", null);
			$bet = $bets[0];
			$betId = $bet['Id'];

			$terminal = $this->Terminal_model->getRow(array('Id' => $bet['terminal_id']));
			if ($terminal == null)
				return $this->reply(-1, "terminal dose not exist", null);

			$week = $this->Week_model->getRow(array('week_no' => $bet['week']));
			if ($week == null)
				return $this->reply(-1, "week dose not exist", null);

			$curDate = new DateTime();
			if ($curDate->format('Y-m-d H:i:s') > $week->close_at)
				return $this->reply(1004, "bet does not change in past week", null);

			$curDate->sub(new DateInterval('PT' . $week->void_bet . 'H'));
			if ($curDate->format('Y-m-d H:i:s') > $bet['bet_time'])
				return $this->reply(1003, "void time passed", null);

			$gamelists = $this->Game_model->getDatas(array('week_no' => $week->week_no, 'status' => 1));
			if ($gamelists == null)
				return $this->reply(-1, "game does not exist", null);

			$missed = $this->checkMissedGames($gamelists, $bet);
			if (count($missed) > 0)
				return $this->reply(1003, "void failed", null);

			$this->DeleteRequest_model->deleteRow(array('bet_id' => $betId));

			//update bet status
			$this->Bet_model->updateData(array('Id' => $betId), array('status' => 1));

			$under = $this->Under_model->getRow(['under'=>$bet['under']]);
			$commission = 0;
			if($under!=null) $commission = $under->commission;
			$this->calcTerminalSummary($terminal->Id, $terminal->agent_id, $bet['under'], $commission, $bet['week'], true);
			return $this->reply(200, "success", null);
		}
	}
