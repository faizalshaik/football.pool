<?php
defined('BASEPATH') OR exit('No direct script access allowed');
include("assets/global/admin.global.php");
class Cms extends CI_Controller {

	public function __construct()
	{
		 parent::__construct();
		 date_default_timezone_set('Africa/Lagos');
				  
		 $this->load->model('Admin_model');	
		 $this->load->model('Base_model');		 	 
		 $this->load->model('User_model');
		 $this->load->model('Bet_model');
		 $this->load->model('Summary_model');		 
		 $this->load->model('DeleteRequest_model');
		 $this->load->model('Game_model');
		 $this->load->model('Option_model');
		 $this->load->model('PlayerOption_model');
		 $this->load->model('Setting_model');
		 $this->load->model('Terminal_model');
		 $this->load->model('TerminalOption_model');
		 $this->load->model('Week_model');		 
		 $this->load->model('Prize_model');
		 $this->load->model('FundRequest_model');					 
		 $this->load->model('Under_model');	
	}
	public function index()
	{
		if($this->logonCheck()) {
			redirect('Cms/dashboard/', 'refresh');
		} 
	}
	public function login(){
		$this->load->view("admin/view_login");
	}

	public function logout(){
		$this->session->sess_destroy();
		redirect('Cms/', 'refresh');
	}

	public function auth_user() {
		global $MYSQL;
		$email = $this->input->post('email');
		$password = $this->input->post('password');
		$conAry = array('email' => $email);
		$ret = $this->Base_model->getRow('tbl_admin', $conAry);
		if(!empty($ret)){       
			if (password_verify($password, $ret->password)) {       
				$sess_data = array('user_id'=>$ret->Id, 'is_login'=>true, 'type'=>'admin');
				$this->session->set_userdata($sess_data);
				redirect('Cms/dashboard/', 'refresh');
			}
			else
			{
                redirect( 'Cms/login', 'refresh');
			}
		}
		else {
			$ret = $this->User_model->getRow(array('email'=>$email));
			if($ret==null)
			{
				redirect( 'Cms/login', 'refresh');
			}
			else   
			{	
				if($ret->type != 'staff' && $ret->type != 'agent')
				{
					redirect( 'Cms/login', 'refresh');
				}
				else 
				{
        			if ($password == $ret->password) {
    					$sess_data = array('user_id'=>$ret->Id, 'is_login'=>true, 'type'=>$ret->type);
    					$this->session->set_userdata($sess_data);
    					redirect('Cms/dashboard/', 'refresh');	
        			}
        			else
        			{
        			    redirect( 'Cms/login', 'refresh');
        			}
				}				
			}
		} 		
	}
	public function dashboard() {
		if($this->logonCheck()) {
			global $MYSQL;
			$param['uri'] = '';
			$param['kind'] = '';
			$param['user_type'] = $this->session->userdata('type');

			$this->load->view("admin/include/header", $param);	

			$data['total_bets'] = $this->Bet_model->getCounts(null);
			$data['total_agents'] = $this->User_model->getCounts(array('type'=>'agent'));
			$data['total_terminals'] = $this->Terminal_model->getCounts(null);

			$summaies = $this->Summary_model->getDatas(null);
			$total_sale = 0;
			$total_win = 0;
			$total_payable = 0;
			foreach($summaies as $summary)
			{
				$total_sale += $summary->sales;
				$total_win += $summary->win;
				$total_payable += $summary->payable;
			}


			$data['total_sale'] = $total_sale;
			$data['total_win'] = $total_win;
			$data['total_payable'] = $total_payable;
			$data['total_profit'] = $total_sale - $total_payable;

			$data['total_reqs'] = $this->DeleteRequest_model->getCounts(null);
			$data['total_approved'] = $this->DeleteRequest_model->getCounts(array('status'=>1));
			$data['total_dismissed'] = $this->DeleteRequest_model->getCounts(array('status'=>2));

			$this->load->view("admin/view_dashboard", $data);
			$this->load->view("admin/include/footer", $data);

		}
	}
	public function updateAccount() {
		if($this->logonCheck()){
			global $MYSQL;
			$type = $this->session->userdata('type');
			$email = $this->input->post('email');
			$password = $this->input->post('password');
			$id = $this->session->userdata('user_id');

			if($type=='admin')
			{
				$npass = password_hash($password, PASSWORD_DEFAULT);
				$updateAry = array('email'=>$email,
					'password'=>$npass,
					'modified'=>date('Y-m-d'));
				$ret = $this->Base_model->updateData($MYSQL['_adminDB'], array('Id'=>$id), $updateAry);
				if($ret > 0) 
					$this->session->set_flashdata('messagePr', 'Update Account Successfully..');
				else
					$this->session->set_flashdata('messagePr', 'Unable to Update Account..');	
			}
			else {
				$updateAry = array('email'=>$email,
					'password'=>$password);
				$ret = $this->User_model->updateData(array('Id'=>$id), $updateAry);
			}

			redirect('Cms/dashboard/', 'refresh');
		}
	}
	public function view_calc()
	{
		if(!$this->logonCheck()) return;

		$param['uri'] = 'view_calc';
		$param['kind'] = '';
		$param['table'] = '';
		$param['user_type'] = $this->session->userdata('type');		
		$this->load->view("admin/include/header", $param);
		$this->load->view("admin/view_calc");
		$this->load->view("admin/include/footer",$param);
	}	

	public function bets_list()
	{
		if(!$this->logonCheck()) return;
		$param['uri'] = 'bets_list';
		$param['kind'] = 'table';
		$param['table'] = 'tbl_bets';

		$curWeekNo = $this->Setting_model->getCurrentWeekNo();
		$param['curWeekNo'] = $curWeekNo;
		$param['curWeek'] = $this->Week_model->getRow(array('week_no'=>$curWeekNo));		
		$param['weeks'] = $this->Week_model->getDatas(null, "week_no");

		$type = $this->session->userdata('type');
		$userId = $this->session->userdata('user_id');		
		//get agents
		$cond = array('type' => 'agent', 'status'=>1);
		if ($type == 'agent') $cond['Id'] = $userId;
		else if ($type == 'staff') $cond['staff_id'] = $userId;
		$agentlits = $this->User_model->getDatas($cond);		
		$param['agents'] = $agentlits;

		//get terminals		
		$terminals = array();
		$rows = $this->Terminal_model->getDatas(array('status'=>1));
		foreach($rows as $row)
		{
			if($this->find_data($agentlits, $row->agent_id)==null) continue;
			$terminals[]= $row;
		}
		$param['terminals'] = $terminals;
		$param['unders'] = $this->Under_model->getDatas(array('status'=>1));

		$param['user_type'] = $this->session->userdata('type');
		$this->load->view("admin/include/header", $param);
		$this->load->view("admin/view_bets_list",$param);
		$this->load->view("admin/include/footer",$param);
	}
	
	public function result_summary()
	{
		if(!$this->logonCheck()) return;
		$param['uri'] = 'result_summary';
		$param['kind'] = 'table';
		$param['table'] = 'tbl_bets';
		$curWeekNo = $this->Setting_model->getCurrentWeekNo();
		$param['curWeekNo'] = $curWeekNo;
		$param['curWeek'] = $this->Week_model->getRow(array('week_no'=>$curWeekNo));
		$param['weeks'] = $this->Week_model->getDatas(null, "week_no");

		
		$type = $this->session->userdata('type');
		$userId = $this->session->userdata('user_id');		

		//get agents
		$cond = array('type' => 'agent', 'status'=>1);
		if ($type == 'agent') $cond['Id'] = $userId;
		else if ($type == 'staff') $cond['staff_id'] = $userId;
		$agentlits = $this->User_model->getDatas($cond);		
		$param['agents'] = $agentlits;

		//get terminals		
		$terminals = array();
		$rows = $this->Terminal_model->getDatas(array('status'=>1));
		foreach($rows as $row)
		{
			if($this->find_data($agentlits, $row->agent_id)==null) continue;
			$terminals[]= $row;
		}
		$param['terminals'] = $terminals;

		$param['unders'] = $this->Under_model->getDatas(array('status'=>1));
		$param['user_type'] = $this->session->userdata('type');

		$this->load->view("admin/include/header", $param);
		$this->load->view("admin/view_result_summary",$param);
		$this->load->view("admin/include/footer",$param);
	}

	public function winner_list()
	{
		if(!$this->logonCheck()) return;
		$param['uri'] = 'winner_list';
		$param['kind'] = 'table';
		$param['table'] = 'tbl_bets';
		$curWeekNo = $this->Setting_model->getCurrentWeekNo();
		$param['curWeekNo'] = $curWeekNo;
		$param['curWeek'] = $this->Week_model->getRow(array('week_no'=>$curWeekNo));
		$param['weeks'] = $this->Week_model->getDatas(null, "week_no");

		$type = $this->session->userdata('type');
		$userId = $this->session->userdata('user_id');		
		//get agents
		$cond = array('type' => 'agent', 'status'=>1);
		if ($type == 'agent') $cond['Id'] = $userId;
		else if ($type == 'staff') $cond['staff_id'] = $userId;
		$agentlits = $this->User_model->getDatas($cond);		
		$param['agents'] = $agentlits;

		//get terminals		
		$terminals = array();
		$rows = $this->Terminal_model->getDatas(array('status'=>1));
		foreach($rows as $row)
		{
			if($this->find_data($agentlits, $row->agent_id)==null) continue;
			$terminals[]= $row;
		}
		$param['terminals'] = $terminals;


		$param['unders'] = $this->Under_model->getDatas(array('status'=>1));
		$param['user_type'] = $this->session->userdata('type');

		$this->load->view("admin/include/header", $param);
		$this->load->view("admin/view_winner_list",$param);
		$this->load->view("admin/include/footer",$param);
	}
	
	public function staff_sales()
	{
		if(!$this->logonCheck()) return;
			
		$param['uri'] = 'winner_list';
		$param['kind'] = 'table';
		$param['table'] = 'tbl_bets';
		$curWeekNo = $this->Setting_model->getCurrentWeekNo();
		$param['curWeekNo'] = $curWeekNo;
		$param['curWeek'] = $this->Week_model->getRow(array('week_no'=>$curWeekNo));
		$param['weeks'] = $this->Week_model->getDatas(null, "week_no");

		$type = $this->session->userdata('type');
		$userId = $this->session->userdata('user_id');		
		//get staffs
		$user = $this->User_model->getRow(array('Id'=>$userId));
		if($type=='agent')
			$param['staffs'] = $this->User_model->getDatas(array('Id'=>$user->staff_id));
		else if($type == 'staff')
			$param['staffs'] = array($user);
		else
			$param['staffs'] = $this->User_model->getDatas(array('type'=>'staff', 'status'=>1));

			
		$param['user_type'] = $this->session->userdata('type');

		$this->load->view("admin/include/header", $param);
		$this->load->view("admin/view_staff_sales",$param);
		$this->load->view("admin/include/footer",$param);
	}	
	public function agent_sales()
	{
		if(!$this->logonCheck()) return;
		$param['uri'] = 'agent_sales';
		$param['kind'] = 'table';
		$param['table'] = 'tbl_bets';
		$curWeekNo = $this->Setting_model->getCurrentWeekNo();
		$param['curWeekNo'] = $curWeekNo;
		$param['curWeek'] = $this->Week_model->getRow(array('week_no'=>$curWeekNo));
		$param['weeks'] = $this->Week_model->getDatas(null, "week_no");

		$type = $this->session->userdata('type');
		$userId = $this->session->userdata('user_id');		
		//get staffs
		$user = $this->User_model->getRow(array('Id'=>$userId));
		if($type=='agent')
			$param['staffs'] = $this->User_model->getDatas(array('Id'=>$user->staff_id));
		else if($type == 'staff')
			$param['staffs'] = array($user);
		else
			$param['staffs'] = $this->User_model->getDatas(array('type'=>'staff', 'status'=>1));

		//get agents
		$cond = array('type' => 'agent', 'status'=>1);
		if ($type == 'agent') $cond['Id'] = $userId;
		else if ($type == 'staff') $cond['staff_id'] = $userId;
		$agentlits = $this->User_model->getDatas($cond);		
		$param['agents'] = $agentlits;

		//get terminals		
		$terminals = array();
		$rows = $this->Terminal_model->getDatas(array('status'=>1));
		foreach($rows as $row)
		{
			if($this->find_data($agentlits, $row->agent_id)==null) continue;
			$terminals[]= $row;
		}
		$param['terminals'] = $terminals;


		$param['user_type'] = $this->session->userdata('type');

		$this->load->view("admin/include/header", $param);
		$this->load->view("admin/view_agent_sales",$param);
		$this->load->view("admin/include/footer",$param);
	}	

	private function find_data($list, $id)
	{
		foreach ($list as $data) {
			if ($data->Id == $id) return $data;
		}
		return null;
	}

	public function terminal_sales()
	{
		if(!$this->logonCheck()) return;
		$param['uri'] = 'terminal_sales';
		$param['kind'] = 'table';
		$param['table'] = 'tbl_bets';
		$curWeekNo = $this->Setting_model->getCurrentWeekNo();
		$param['curWeekNo'] = $curWeekNo;
		$param['curWeek'] = $this->Week_model->getRow(array('week_no'=>$curWeekNo));
		$param['weeks'] = $this->Week_model->getDatas(null, "week_no");	
		
		$type = $this->session->userdata('type');
		$userId = $this->session->userdata('user_id');
		$cond = array('type' => 'agent', 'status'=>1);
		if ($type == 'agent') $cond['Id'] = $userId;
		else if ($type == 'staff') $cond['staff_id'] = $userId;
		$agentlits = $this->User_model->getDatas($cond);
		
		$param['agents'] = $agentlits;

		$terminals = array();
		$rows = $this->Terminal_model->getDatas(array('status'=>1));
		foreach($rows as $row)
		{
			if($this->find_data($agentlits, $row->agent_id)==null) continue;
			$terminals[]= $row;
		}
		$param['terminals'] = $terminals;

		$param['user_type'] = $this->session->userdata('type');

		$this->load->view("admin/include/header", $param);
		$this->load->view("admin/view_terminal_sales",$param);
		$this->load->view("admin/include/footer",$param);
	}

	public function delete_requests()
	{
		if(!$this->logonCheck()) return;
		$param['uri'] = 'delete_requests';
		$param['kind'] = 'table';
		$param['table'] = 'tbl_bets';
		$curWeekNo = $this->Setting_model->getCurrentWeekNo();
		$param['curWeekNo'] = $curWeekNo;
		$param['curWeek'] = $this->Week_model->getRow(array('week_no'=>$curWeekNo));
		$param['weeks'] = $this->Week_model->getDatas(null, "week_no");				
		$param['user_type'] = $this->session->userdata('type');

		$this->load->view("admin/include/header", $param);
		$this->load->view("admin/view_delete_requests",$param);
		$this->load->view("admin/include/footer",$param);
	}
	public function void_bets()
	{
		if(!$this->logonCheck()) return;
		$param['uri'] = 'void_bets';
		$param['kind'] = 'table';
		$param['table'] = 'tbl_bets';
		$curWeekNo = $this->Setting_model->getCurrentWeekNo();
		$param['curWeekNo'] = $curWeekNo;
		$param['curWeek'] = $this->Week_model->getRow(array('week_no'=>$curWeekNo));
		$param['weeks'] = $this->Week_model->getDatas(null, "week_no");



		$type = $this->session->userdata('type');
		$userId = $this->session->userdata('user_id');
		$cond = array('type' => 'agent', 'status'=>1);
		if ($type == 'agent') $cond['Id'] = $userId;
		else if ($type == 'staff') $cond['staff_id'] = $userId;
		$agentlits = $this->User_model->getDatas($cond);		
		$param['agents'] = $agentlits;

		$terminals = array();
		$rows = $this->Terminal_model->getDatas(array('status'=>1));
		foreach($rows as $row)
		{
			if($this->find_data($agentlits, $row->agent_id)==null) continue;
			$terminals[]= $row;
		}
		$param['terminals'] = $terminals;




		$param['unders'] = $this->Under_model->getDatas(array('status'=>1));		
		$param['user_type'] = $this->session->userdata('type');
		
		$this->load->view("admin/include/header", $param);
		$this->load->view("admin/view_void_bets",$param);
		$this->load->view("admin/include/footer",$param);
	}

	public function agents()
	{
		if(!$this->logonCheck()) return;
		$param['uri'] = 'agents';
		$param['kind'] = 'table';
		$param['table'] = 'tbl_user';
		$param['staffs'] = $this->User_model->getDatas(array('type'=>'staff'));
		$param['user_type'] = $this->session->userdata('type');
		if($param['user_type']!='admin' && $param['user_type']!='staff')
		{
			redirect('Cms/dashboard/', 'refresh');			
			return;
		}



		$this->load->view("admin/include/header", $param);
		$this->load->view("admin/view_agents",$param);
		$this->load->view("admin/include/footer",$param);
	}	

	public function office_staffs()
	{
		if(!$this->logonCheck()) return;
		$param['uri'] = 'office_staffs';
		$param['kind'] = 'table';
		$param['table'] = 'tbl_user';
		$param['user_type'] = $this->session->userdata('type');
		if($param['user_type']!='admin')
		{
			redirect('Cms/dashboard/', 'refresh');			
			return;
		}


		$this->load->view("admin/include/header", $param);
		$this->load->view("admin/view_office_staffs",$param);
		$this->load->view("admin/include/footer",$param);
	}		

	public function online_players()
	{
		if(!$this->logonCheck()) return;
		$param['uri'] = 'online_users';
		$param['kind'] = 'table';
		$param['table'] = 'tbl_user';
		$param['user_type'] = $this->session->userdata('type');
		$param['agents'] = $this->User_model->getDatas(array('type'=>'agent', 'status'=>1));
				
		if($param['user_type']!='admin' && $param['user_type']!='agent')
		{
			redirect('Cms/dashboard/', 'refresh');			
			return;
		}

		$param['options'] = $this->Option_model->getDatas(null);

		$this->load->view("admin/include/header", $param);
		$this->load->view("admin/view_online_players",$param);
		$this->load->view("admin/include/footer",$param);
	}		

	public function terminals()
	{
		if(!$this->logonCheck()) return;
		$param['uri'] = 'terminals';
		$param['kind'] = 'table';
		$param['table'] = 'tbl_terminal';
		$param['user_type'] = $this->session->userdata('type');
		if($param['user_type']!='admin' && $param['user_type']!='agent')
		{
			redirect('Cms/dashboard/', 'refresh');			
			return;
		}

		$param['unders'] = $this->Under_model->getDatas(null);
		$param['agents'] = $this->User_model->getDatas(array('type'=>'agent', 'status'=>1));
		$this->load->view("admin/include/header", $param);
		$this->load->view("admin/view_terminals",$param);
		$this->load->view("admin/include/footer",$param);
	}	
	
	public function terminal_distribution()
	{
		if(!$this->logonCheck()) return;
		$param['uri'] = 'terminal_distribution';
		$param['kind'] = 'table';
		$param['table'] = 'tbl_bets';
		$param['user_type'] = $this->session->userdata('type');
		$this->load->view("admin/include/header", $param);
		$this->load->view("admin/view_terminal_distribution",$param);
		$this->load->view("admin/include/footer",$param);
	}	

	public function user_wallet_status()
	{
		if(!$this->logonCheck()) return;
		$param['uri'] = 'user_wallet_status';
		$param['kind'] = 'table';
		$param['table'] = 'tbl_bets';
		$param['user_type'] = $this->session->userdata('type');
		$this->load->view("admin/include/header", $param);
		$this->load->view("admin/view_user_wallet_status",$param);
		$this->load->view("admin/include/footer",$param);
	}	
	
	public function requests()
	{
		if(!$this->logonCheck()) return;
		$param['uri'] = 'requests';
		$param['kind'] = 'table';
		$param['table'] = 'tbl_bets';
		$param['user_type'] = $this->session->userdata('type');
		$this->load->view("admin/include/header", $param);
		$this->load->view("admin/view_requests",$param);
		$this->load->view("admin/include/footer",$param);
	}	
	
	public function matches()
	{
		if(!$this->logonCheck()) return;
		$curWeekNo = $this->Setting_model->getCurrentWeekNo();
		$param['curWeekNo'] = $curWeekNo;
		$param['weeks'] = $this->Week_model->getDatas(null, "week_no");

		$param['uri'] = 'matches';
		$param['kind'] = 'table';
		$param['table'] = 'tbl_game';
		$param['user_type'] = $this->session->userdata('type');
		if($param['user_type']!='admin')		
		{
			redirect('Cms/dashboard/', 'refresh');			
			return;
		}


		$this->load->view("admin/include/header", $param);
		$this->load->view("admin/view_matches",$param);
		$this->load->view("admin/include/footer",$param);
	}	

	public function scores()
	{
		if(!$this->logonCheck()) return;
		$curWeekNo = $this->Setting_model->getCurrentWeekNo();		
		$param['curWeekNo'] = $curWeekNo;
		$param['weeks'] = $this->Week_model->getDatas(null, "week_no");

		$param['uri'] = 'scores';
		$param['kind'] = 'table';
		$param['table'] = 'tbl_game';
		$param['user_type'] = $this->session->userdata('type');
		if($param['user_type']!='admin')		
		{
			redirect('Cms/dashboard/', 'refresh');			
			return;
		}


		$this->load->view("admin/include/header", $param);
		$this->load->view("admin/view_scores",$param);
		$this->load->view("admin/include/footer",$param);
	}	

	public function results()
	{
		if(!$this->logonCheck()) return;
		$curWeekNo = $this->Setting_model->getCurrentWeekNo();		
		$param['curWeekNo'] = $curWeekNo;
		$param['weeks'] = $this->Week_model->getDatas(null, "week_no");

		$param['uri'] = 'results';
		$param['kind'] = 'table';
		$param['table'] = 'tbl_bets';
		$param['user_type'] = $this->session->userdata('type');
		$param['user_type'] = $this->session->userdata('type');
		if($param['user_type']!='admin')		
		{
			redirect('Cms/dashboard/', 'refresh');			
			return;
		}


		$this->load->view("admin/include/header", $param);
		$this->load->view("admin/view_results",$param);
		$this->load->view("admin/include/footer",$param);
	}		
	public function prize_value()
	{
		if(!$this->logonCheck()) return;
		$param['uri'] = 'prize_value';
		$param['kind'] = '';
		$param['table'] = '';
		$param['user_type'] = $this->session->userdata('type');
		if($param['user_type']!='admin')		
		{
			redirect('Cms/dashboard/', 'refresh');			
			return;
		}


		$curWeekNo = $this->Setting_model->getCurrentWeekNo();
		$param['curWeekNo'] = $curWeekNo;
		$param['curWeek'] = $this->Week_model->getRow(array('week_no'=>$curWeekNo));
		$prizes = array();
		$opts = $this->Option_model->getDatas(null);
		$curWeekNo = $this->Setting_model->getCurrentWeekNo();				
		foreach($opts as $opt)
		{
			$row = array();
			$values = array();
			$row['option_id'] = $opt->Id;
			$row['option'] = $opt->name;
			
			for($i=0; $i<4; $i++)
			{
				$data = $this->Prize_model->getRow(array('week_no'=>$curWeekNo, 'option_id'=>$opt->Id, 'under'=>($i+3)));
				if($data) $values[]= $data->prize;
				else $values[]= 0;
			}
			$row['values'] = $values;
			$prizes[] = $row;
		}
		$param['prizes'] = $prizes;

		$this->load->view("admin/include/header", $param);
		$this->load->view("admin/view_prize_value",$param);
		$this->load->view("admin/include/footer",$param);
	}
	public function game_settings()
	{
		if(!$this->logonCheck()) return;
		$param['uri'] = 'game_settings';
		$param['kind'] = 'table';
		$param['table'] = 'tbl_under';

		$curWeekNo = $this->Setting_model->getCurrentWeekNo();
		$param['curWeekNo'] = $curWeekNo;
		$param['curWeek'] = $this->Week_model->getRow(array('week_no'=>$curWeekNo));
		$param['weeks'] = $this->Week_model->getDatas(null, "week_no");

		$param['voidBets'] = $this->Bet_model->getCounts(array('status'=>2));
		$param['curweekVoidBets'] = $this->Bet_model->getCounts(array('status'=>2, 'week'=>$curWeekNo));
		$param['curweekBets'] = $this->Bet_model->getCounts(array('status'=>1, 'week'=>$curWeekNo));


		$param['user_type'] = $this->session->userdata('type');
		if($param['user_type']!='admin')		
		{
			redirect('Cms/dashboard/', 'refresh');			
			return;
		}

		$curWk = 0;
		$dt = $this->Setting_model->getRow(array('name'=>'current_week'));
		if($dt!=null)$curWk = $dt->value;

		$param['curweek_no'] = $curWk;
		$param['curweek'] = $this->Week_model->getRow(array('week_no'=>$curWk));

		$curWeekSt = [];
		$curWeekMSt = [];
		$curWeekSt['week'] = $curWeekNo;
		$curWeekSt['voids'] = $this->Bet_model->getCounts(['week'=>$curWeekNo, 'status'=>0]);
		$curWeekSt['actives'] = $this->Bet_model->getCounts(['week'=>$curWeekNo, 'status'=>0]);
		$param['curWeekSt'] = $curWeekSt;
		$param['curWeekMSt'] = $curWeekSt;
		$param['panelPrize'] = $this->Setting_model->getPanelPrize();
		$param['maxWin'] = $this->Setting_model->getMaxWin();
		
		$this->load->view("admin/include/header", $param);
		$this->load->view("admin/view_game_settings",$param);
		$this->load->view("admin/include/footer",$param);

	}	

	public function personal_details()
	{
		if(!$this->logonCheck()) return;
		$param['uri'] = 'personal_details';
		$param['kind'] = '';
		$param['table'] = '';

		$param['user_type'] = $this->session->userdata('type');
		$id = $this->session->userdata('user_id');
		if($param['user_type']=='admin')
		{
			$param['user'] = $this->Base_model->getRow('admin', array('Id'=>$id));
		}
		else 
		{
			$param['user'] = $this->User_model->getRow(array('Id'=>$id));
		}		

		$this->load->view("admin/include/header", $param);
		$this->load->view("admin/view_personal_details",$param);
		$this->load->view("admin/include/footer",$param);
	}	
}
