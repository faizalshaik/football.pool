<?php
ini_set("memory_limit", "256M");
defined('BASEPATH') or exit('No direct script access allowed');

class Cms_api extends CI_Controller
{
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
	}

	public function reply($status, $message, $data)
	{
		$result = array('status' => $status, 'message' => $message, 'data' => $data);
		echo json_encode($result);
	}
	public function ajaxDel()
	{
		if ($this->logonCheck()) {
			global $MYSQL;
			$Id = $this->input->post('Id');
			$tbl_Name = $this->input->post('tbl_Name');
			if ($tbl_Name != '') {
				$conAry = array('Id' => $Id);
				$updateAry = array('isdeleted' => '1');
				$this->Base_model->updateData($tbl_Name, $conAry, $updateAry);
				echo json_encode(array("status" => TRUE));
			} else {
				echo json_encode(array("status" => FALSE));
			}
		}
	}
	public function delUser()
	{
		if ($this->logonCheck()) {
			global $MYSQL;
			$Id = $this->input->post('Id');
			$tbl_Name = $this->input->post('tbl_Name');
			if ($tbl_Name != '') {
				$this->Base_model->deleteByField($tbl_Name, "Id", $Id);
				echo json_encode(array("status" => TRUE));
			} else {
				echo json_encode(array("status" => FALSE));
			}
		}
	}
	public function getDataById()
	{
		$this->logonCheck();

		$Id = $this->input->post("Id");
		$tableName = $this->input->post("tbl_Name");
		$ret = $this->Base_model->getRow($tableName, array('Id' => $Id));
		echo json_encode($ret);
	}

	public function getWeek()
	{
		$this->logonCheck();
		$week = $this->input->post("week");
		$ret = $this->Week_model->getRow(array('week_no' => $week));
		echo json_encode($ret);
	}

	public function delData()
	{
		$this->logonCheck();
		$Id = $this->input->post("Id");
		$tableName = $this->input->post("tbl_Name");
		$ret = $this->Base_model->deleteRow($tableName, array('Id' => $Id));
		echo "1";
	}

	public function shortString($lst)
	{
		//$lst = array(1,2,3,4,5,6,9,10,12,14,15,16,40);
		$prev = 0;
		$first = 0;
		$list = array();
		foreach ($lst as $ele) {
			if ($first == 0) $first = $ele;
			if ($prev != 0 && ($prev + 1) != $ele) {
				if ($first == $prev) $list[] = $first;
				else $list[] = $first . '-' . $prev;
				$first = $ele;
				$prev = 0;
			}
			$prev = $ele;
		}

		if ($first == $ele) $list[] = $first;
		else $list[] = $first . '-' . $prev;

		return implode(',', $list);
	}

	private function wrapping($org)
	{
		$result = "";
		$arr = str_split($org, 20);
		foreach ($arr as $l) {
			$result .= '<p>' . $l . '</p>';
		}
		return $result;
	}

	public function get_agents()
	{
		$this->logonCheck();

		$type = $this->session->userdata('type');
		$userId = $this->session->userdata('user_id');
		$cond = array('type' => 'agent');
		if ($type == 'agent') $cond['Id'] = $userId;
		else if ($type == 'staff') $cond['staff_id'] = $userId;
		$agentlits = $this->User_model->getDatas($cond);				

		$datas = $this->User_model->getDatas(array('type' => 'agent', 'status' => 1));
		$resData = array();
		foreach ($datas as $data) {

			if ($this->find_data($agentlits, $data->Id) == null)
				continue;

			$row = array();
			$row[] = $data->Id;
			$row[] = $data->user_id;
			$row[] = $data->password;
			$row[] = $data->email;
			$row[] = $data->firstname;
			$row[] = $data->lastname;
			$staff = $this->User_model->getRow(array('Id' => $data->staff_id));
			if ($staff != null) $row[] = $staff->user_id;
			else $row[] = "";
			$row[] = $data->phone;
			$row[] = $data->address;
			$row[] = $data->createdate;

			if($type=='admin')
			{
				$strAction = '<a href="javascript:void(0)" class="on-default edit-row" ' .
				'onclick="onEdit(' . $data->Id . ')" title="Edit" ><i class="fa fa-pencil"></i></a>' .
				'<a href="javascript:void(0)" class="on-default remove-row" ' .
				'onclick="onDelete(' . $data->Id . ')" title="Remove" ><i class="fa fa-trash-o"></i></a>';
			}else $strAction='';

			$row[] = $strAction;
			$resData[] = $row;
		}

		$output = array(
			"draw" => null,
			"recordsTotal" => count($resData),
			"recordsFiltered" => count($resData),
			"data" => $resData,
		);
		echo json_encode($output);
	}
	public function edit_agent()
	{
		$this->logonCheck();
		$data = $this->input->post();
		$data['type'] = 'agent';

		if ($data['Id'] > 0) {
			$row = $this->User_model->getRow(array('user_id' => $data['user_id']));
			if ($row && $row->Id != $data['Id']) return $this->reply(400, "User Id already exist", null);

			$row = $this->User_model->getRow(array('email' => $data['email']));
			if ($row  && $row->Id != $data['Id']) return $this->reply(400, "User Email already exist", null);
			$this->User_model->updateData(array('Id' => $data['Id']), $data);
		} else {
			unset($data['Id']);
			$data['createdate'] = date("Y-m-d H:i:s");
			$row = $this->User_model->getRow(array('user_id' => $data['user_id']));
			if ($row) return $this->reply(400, "User Id already exist", null);

			$row = $this->User_model->getRow(array('email' => $data['email']));
			if ($row) return $this->reply(400, "User Email already exist", null);
			$this->User_model->insertData($data);
		}
		$this->reply(200, "ok", null);
	}

	public function get_staffs()
	{
		$this->logonCheck();
		$datas = $this->User_model->getDatas(array('type' => 'staff', 'status' => 1));
		$resData = array();
		foreach ($datas as $data) {
			$row = array();
			$row[] = $data->Id;
			$row[] = $data->user_id;
			$row[] = $data->password;
			$row[] = $data->email;
			$row[] = $data->firstname;
			$row[] = $data->lastname;
			$row[] = $data->phone;
			$row[] = $data->address;
			$row[] = $data->createdate;
			$strAction = '<a href="javascript:void(0)" class="on-default edit-row" ' .
				'onclick="onEdit(' . $data->Id . ')" title="Edit" ><i class="fa fa-pencil"></i></a>' .
				'<a href="javascript:void(0)" class="on-default remove-row" ' .
				'onclick="onDelete(' . $data->Id . ')" title="Remove" ><i class="fa fa-trash-o"></i></a>';
			$row[] = $strAction;
			$resData[] = $row;
		}

		$output = array(
			"draw" => null,
			"recordsTotal" => count($resData),
			"recordsFiltered" => count($resData),
			"data" => $resData,
		);
		echo json_encode($output);
	}
	public function edit_staff()
	{
		$this->logonCheck();
		$data = $this->input->post();
		$data['type'] = 'staff';

		if ($data['Id'] > 0) {
			$row = $this->User_model->getRow(array('user_id' => $data['user_id']));
			if ($row && $row->Id != $data['Id']) return $this->reply(400, "User Id already exist", null);

			$row = $this->User_model->getRow(array('email' => $data['email']));
			if ($row  && $row->Id != $data['Id']) return $this->reply(400, "User Email already exist", null);
			$this->User_model->updateData(array('Id' => $data['Id']), $data);
		} else {
			unset($data['Id']);
			$data['createdate'] = date("Y-m-d H:i:s");
			$row = $this->User_model->getRow(array('user_id' => $data['user_id']));
			if ($row) return $this->reply(400, "User Id already exist", null);

			$row = $this->User_model->getRow(array('email' => $data['email']));
			if ($row) return $this->reply(400, "User Email already exist", null);
			$this->User_model->insertData($data);
		}
		$this->reply(200, "ok", null);
	}

	public function get_players()
	{
		$this->logonCheck();
		$type = $this->session->userdata('type');
		$userId = $this->session->userdata('user_id');
		$cond = array('type' => 'agent');
		if ($type == 'agent') $cond['Id'] = $userId;
		else if ($type == 'staff') $cond['staff_id'] = $userId;
		$agentlits = $this->User_model->getDatas($cond);

		$datas = $this->User_model->getDatas(array('type' => 'player', 'status' => 1));
		$resData = array();
		foreach ($datas as $data) {

			if ($this->find_data($agentlits, $data->agent_id) == null) continue;

			$row = array();
			$row[] = $data->Id;
			$row[] = $data->user_id;
			$row[] = $data->password;
			$row[] = $data->email;
			$row[] = $data->firstname;
			$row[] = $data->lastname;
			$row[] = $data->phone;
			$row[] = $data->address;
			$row[] = $data->createdate;

			$optsStr = "";
			$opts = $this->PlayerOption_model->getDatas(array('player_id' => $data->Id));
			foreach ($opts as $op) {
				$option = $this->Option_model->getRow(array('Id' => $op->option_id));
				if ($option == null) continue;

				$optStr = "<div class='row'><div class='col-md-5 text-right'><b>" . $under->under . ":</b>" . "</div><div class='col-md-2'>";
				if ($option->status == 1) $optStr .= "<i class='icon-check text-primary'></i></div>";
				else $optStr .= "<i class='icon-close text-danger'></i></div>";
				$optStr .= "<div calss='col-md-5'>" . $op->commision . "</div></div>";

				$optsStr .= $optStr;
			}

			$row[] = $optsStr;
			$strAction = '';
			if ($type == 'admin' || $type == 'agent')
				$strAction = '<a href="javascript:void(0)" class="on-default edit-row" ' .
					'onclick="onEdit(' . $data->Id . ')" title="Edit" ><i class="fa fa-pencil"></i></a>' .
					'<a href="javascript:void(0)" class="on-default remove-row" ' .
					'onclick="onDelete(' . $data->Id . ')" title="Remove" ><i class="fa fa-trash-o text-danger"></i></a>';
			$row[] = $strAction;
			$resData[] = $row;
		}

		$output = array(
			"draw" => null,
			"recordsTotal" => count($resData),
			"recordsFiltered" => count($resData),
			"data" => $resData,
		);
		echo json_encode($output);
	}
	public function edit_player()
	{
		$this->logonCheck();
		$data = $this->input->post();
		$data['type'] = 'player';

		if ($data['Id'] > 0) {

			$row = $this->User_model->getRow(array('user_id' => $data['user_id']));
			if ($row && $row->Id != $data['Id']) return $this->reply(400, "User Id already exist", null);

			$row = $this->User_model->getRow(array('email' => $data['email']));
			if ($row  && $row->Id != $data['Id']) return $this->reply(400, "User Email already exist", null);
			$this->User_model->updateData(array('Id' => $data['Id']), $data);
		} else {
			unset($data['Id']);
			$data['createdate'] = date("Y-m-d H:i:s");
			$row = $this->User_model->getRow(array('user_id' => $data['user_id']));
			if ($row!=null) 
				return $this->reply(400, "User Id already exist", $row);

			$row = $this->User_model->getRow(array('email' => $data['email']));
			if ($row) return $this->reply(400, "User Email already exist", null);
			$playerId = $this->User_model->insertData($data);

			//options 
			$opts = $this->Option_model->getDatas(null);
			foreach ($opts as $op) {
				$this->PlayerOption_model->insertData(array(
					'player_id' => $playerId,
					'option_id' => $op->Id,
					'commision' => $op->commision,
					'status' => $op->status
				));
			}
		}
		$this->reply(200, "ok", null);
	}

	public function get_player_options($playerId)
	{
		$this->logonCheck();
		$datas = $this->PlayerOption_model->getDatas(array('player_id' => $playerId));
		$resData = array();
		foreach ($datas as $data) {
			$opt = $this->Option_model->getRow(array('Id' => $data->option_id));
			if ($opt == null) continue;

			$row = array();
			$row[] = $opt->name;
			$strStatus = "";
			if ($opt->status == 1)
				$strStatus = "<i class='icon-check text-primary'></i>";
			else
				$strStatus = "<i class='icon-close text-danger'></i>";
			$row[] = $strStatus;
			$row[] = $data->commision;
			$strAction = '<a href="javascript:void(0)" class="on-default remove-row" ' .
				'onclick="onDeleteOption(' . $data->Id . ')" title="Remove" ><i class="fa fa-trash-o"></i></a>';
			$row[] = $strAction;
			$resData[] = $row;
		}

		$output = array(
			"draw" => null,
			"recordsTotal" => count($resData),
			"recordsFiltered" => count($resData),
			"data" => $resData,
		);
		echo json_encode($output);
	}
	public function edit_player_option()
	{
		$this->logonCheck();
		$data = $this->input->post();
		$row = $this->PlayerOption_model->getRow(array('player_id' => $data['player_id'], 'option_id' => $data['option_id']));
		if ($row) {
			$this->PlayerOption_model->updateData(
				array('Id' => $row->Id),
				array('commision' => $data['commision'])
			);
		} else
			$this->PlayerOption_model->insertData($data);
		$this->reply(200, "ok", null);
	}

	private function find_data($list, $id)
	{
		foreach ($list as $data) {
			if ($data->Id == $id) return $data;
		}
		return null;
	}


	public function get_terminals()
	{
		$this->logonCheck();

		$type = $this->session->userdata('type');
		$userId = $this->session->userdata('user_id');
		$cond = array('type' => 'agent');
		if ($type == 'agent') $cond['Id'] = $userId;
		else if ($type == 'staff') $cond['staff_id'] = $userId;
		$agents = $this->User_model->getDatas($cond);
		$unders = $this->Under_model->getDatas(null, 'under');

		$datas = $this->Terminal_model->getDatas(null);
		$resData = array();
		foreach ($datas as $data) {
			$row = array();
			$row[] = $data->terminal_no;
			$row[] = $data->password;
			$agent = $this->find_data($agents, $data->agent_id);

			if ($agent) $row[] = $agent->user_id;
			else $row[] = "";
			$row[] = number_format($data->credit_limit);
			//$row[] = number_format($data->max_stake);

			$undersStr = "";
			if ($data->status)
				$undersStr .= "<div class='row text-center'><span class='label label-success'>Active</span></div>";
			else
				$undersStr .= "<div class='row  text-center'><span class='label label-danger'>Disable</span></div>";

			
			foreach($unders as $under)
			{
				$mask = pow(2, ($under->under - 1));
				if($mask & $data->unders)
					$undersStr .= '<div class="row"><div class="col-md-6 text-right"><b>U'.$under->under.
						':</b></div><div class="col-md-6"><i class="icon-check text-primary"></i></div></div>';
			}
			$row[] = $undersStr;

			$strAction = "";
			if ($type = "admin" || $type = "agent")
				$strAction = '<a href="javascript:void(0)" class="on-default edit-row" ' .
					'onclick="onEdit(' . $data->Id . ')" title="Edit" ><i class="fa fa-pencil m-r-5"></i></a>' .
					'<a href="javascript:void(0)" class="on-default remove-row" ' .
					'onclick="onDelete(' . $data->Id . ')" title="Remove" ><i class="fa fa-trash-o text-danger m-r-5"></i></a>' .
					'<a href="javascript:void(0)" class="on-default remove-row" ' .
					'onclick="onEnable(' . $data->Id . ',1)" title="Enable" ><i class="icon-check m-r-5"></i></a>' .
					'<a href="javascript:void(0)" class="on-default remove-row" ' .
					'onclick="onEnable(' . $data->Id . ',0)" title="Disable" ><i class="icon-close text-pink m-r-5"></i></a>';
			$row[] = $strAction;
			$resData[] = $row;
		}

		$output = array(
			"draw" => null,
			"recordsTotal" => count($resData),
			"recordsFiltered" => count($resData),
			"data" => $resData,
		);
		echo json_encode($output);
	}
	public function edit_terminal()
	{
		$this->logonCheck();
		$data = $this->input->post();

		if ($data['Id'] > 0) {
			$row = $this->Terminal_model->getRow(array('terminal_no' => $data['terminal_no']));
			if ($row && $row->Id != $data['Id']) return $this->reply(400, "Terminal no already exist", null);
			$this->Terminal_model->updateData(array('Id' => $data['Id']), $data);
		} else {
			unset($data['Id']);
			$row = $this->Terminal_model->getRow(array('terminal_no' => $data['terminal_no']));
			if ($row) return $this->reply(400, "Terminal no already exist", null);
			$playerId = $this->Terminal_model->insertData($data);

			//options 
			$opts = $this->Option_model->getDatas(null);
			foreach ($opts as $op) {
				$this->TerminalOption_model->insertData(array(
					'terminal_id' => $playerId,
					'option_id' => $op->Id,
					'commision' => $op->commision,
					'status' => $op->status
				));
			}
		}
		$this->reply(200, "ok", null);
	}
	public function enable_terminal()
	{
		$this->logonCheck();
		$id = $this->input->post('Id');
		$status = $this->input->post('status');
		$this->Terminal_model->updateData(array('Id' => $id), array('status' => $status));
		$this->reply(200, "ok", null);
	}

	public function enable_all_terminal()
	{
		$this->logonCheck();
		$this->Terminal_model->updateData(null, array('status' => 1));
		$this->reply(200, "ok", null);
	}
	public function disable_all_terminal()
	{
		$this->logonCheck();
		$this->Terminal_model->updateData(null, array('status' => 0));
		$this->reply(200, "ok", null);
	}


	public function get_terminal_options($terminalId)
	{
		$this->logonCheck();
		$datas = $this->TerminalOption_model->getDatas(array('terminal_id' => $terminalId));
		$resData = array();
		foreach ($datas as $data) {
			$opt = $this->Option_model->getRow(array('Id' => $data->option_id));
			if ($opt == null) continue;

			$row = array();
			$row[] = $opt->name;
			$strStatus = "";
			if ($data->status == 1)
				$strStatus = "<i class='icon-check text-primary'></i>";
			else
				$strStatus = "<i class='icon-close text-danger'></i>";
			$row[] = $strStatus;
			$row[] = $data->commision;

			$strAction = '<a href="javascript:void(0)" class="on-default edit-row" ' .
				'onclick="onEnableOption(' . $data->Id . ',1)" title="Enable" ><i class="icon-check"></i></a>' .
				'<a href="javascript:void(0)" class="on-danger remove-row" ' .
				'onclick="onEnableOption(' . $data->Id . ',0)" title="Disable" ><i class="icon-close text-danger"></i></a>';
			$row[] = $strAction;
			$resData[] = $row;
		}

		$output = array(
			"draw" => null,
			"recordsTotal" => count($resData),
			"recordsFiltered" => count($resData),
			"data" => $resData,
		);
		echo json_encode($output);
	}
	public function edit_terminal_option()
	{
		$this->logonCheck();
		$data = $this->input->post();
		$row = $this->TerminalOption_model->getRow(array('terminal_id' => $data['terminal_id'], 'option_id' => $data['option_id']));
		if ($row) {
			$this->TerminalOption_model->updateData(
				array('Id' => $row->Id),
				array('commision' => $data['commision'])
			);
		} else
			$this->TerminalOption_model->insertData($data);
		$this->reply(200, "ok", null);
	}
	public function enable_terminal_option()
	{
		$this->logonCheck();
		$id = $this->input->post('Id');
		$status = $this->input->post('status');
		$this->TerminalOption_model->updateData(array('Id' => $id), array('status' => $status));
		$this->reply(200, "ok", null);
	}
	public function enable_under()
	{
		$this->logonCheck();
		$id = $this->input->post('Id');
		$status = $this->input->post('status');
		$this->Under_model->updateData(array('Id' => $id), array('status' => $status));
		$this->reply(200, "ok", null);
	}	
	public function get_unders()
	{
		//$this->logonCheck();
		$datas = $this->Under_model->getDatas(null);
		$resData = array();
		foreach ($datas as $data) {
			$row = array();
			$row[] = $data->under;
			$row[] = $data->name;
			$row[] = $data->commission."%";
			$row[] = $data->max_stake;
			if($data->status==1)
				$row[] = '<label class="label label-success">Enabled</label>';
			else 
				$row[] = '<label class="label label-danger">Disabled</label>';

			$strAction = '<a href="javascript:void(0)" class="on-default edit-row" ' .
				'onclick="onEdit(' . $data->Id . ')" title="Edit" ><i class="m-r-5 fa fa-pencil"></i></a>' .
				'<a href="javascript:void(0)" class="on-default remove-row" ' .
				'onclick="onDelete(' . $data->Id . ')" title="Remove" ><i class="fa m-r-5 fa-trash-o text-danger"></i></a>'.
				'<a href="javascript:void(0)" class="on-default remove-row" ' .
				'onclick="onEnable(' . $data->Id . ',1)" title="Enable" ><i class=" m-r-5 icon-check"></i></a>' .
				'<a href="javascript:void(0)" class="on-default remove-row" ' .
				'onclick="onEnable(' . $data->Id . ',0)" title="Disable" ><i class="icon-close text-pink"></i></a>';
				
			$row[] = $strAction;
			$resData[] = $row;
		}

		$output = array(
			"draw" => null,
			"recordsTotal" => count($resData),
			"recordsFiltered" => count($resData),
			"data" => $resData,
		);
		echo json_encode($output);
	}	
	public function edit_under()
	{
		$this->logonCheck();
		$data = $this->input->post();

		if ($data['Id'] > 0) {
			$row = $this->Under_model->getRow(array('name' => $data['name']));
			if ($row && $row->Id != $data['Id']) return $this->reply(400, "Under already exist", null);
			$this->Under_model->updateData(array('Id' => $data['Id']), $data);
		} else {
			unset($data['Id']);
			$row = $this->Under_model->getRow(array('name' => $data['name']));
			if ($row) return $this->reply(400, "Under already exist", null);
			$this->Under_model->insertData($data);
		}
		$this->reply(200, "ok", null);
	}




	public function get_terminal_distributions()
	{
		$this->logonCheck();

		$type = $this->session->userdata('type');
		$userId = $this->session->userdata('user_id');
		$cond = array('type' => 'agent');
		if ($type == 'agent') $cond['Id'] = $userId;
		else if ($type == 'staff') $cond['staff_id'] = $userId;
		$agentlits = $this->User_model->getDatas($cond);

		$resData = array();
		$staffs = $this->User_model->getDatas(array('type' => 'staff'));
		foreach ($staffs as $staff) {
			$agents = $this->User_model->getDatas(array('type' => 'agent', 'staff_id' => $staff->Id));
			foreach ($agents as $agent) {
				if ($this->find_data($agentlits, $agent->Id) == null)
					continue;

				$terminals = $this->Terminal_model->getDatas(array('agent_id' => $agent->Id));
				foreach ($terminals as $terminal) {
					$row = array();
					$row[] = $staff->user_id;
					$row[] = $agent->user_id;
					$row[] = $terminal->terminal_no;
					$resData[] = $row;
				}
			}
		}

		$output = array(
			"draw" => null,
			"recordsTotal" => count($resData),
			"recordsFiltered" => count($resData),
			"data" => $resData,
		);
		echo json_encode($output);
	}

	public function get_options()
	{
		$this->logonCheck();
		$datas = $this->Option_model->getDatas(null);
		$resData = array();
		foreach ($datas as $data) {
			$row = array();
			$row[] = $data->Id;
			$row[] = $data->name;
			$row[] = $data->commision;
			if ($data->status == 1)	$row[] = "<span class='label label-success'>Active</span>";
			else $row[] = "<span class='label label-danger'>Disable</span>";

			$strAction = '<a href="javascript:void(0)" class="on-default edit-row" ' .
				'onclick="onEdit(' . $data->Id . ')" title="Edit" ><i class="fa fa-pencil"></i></a>' .
				'<a href="javascript:void(0)" class="on-default remove-row" ' .
				'onclick="onDelete(' . $data->Id . ')" title="Remove" ><i class="fa fa-trash-o"></i></a>';
			$row[] = $strAction;
			$resData[] = $row;
		}

		$output = array(
			"draw" => null,
			"recordsTotal" => count($resData),
			"recordsFiltered" => count($resData),
			"data" => $resData,
		);
		echo json_encode($output);
	}
	public function edit_option()
	{
		$this->logonCheck();
		$data = $this->input->post();

		if ($data['Id'] > 0) {
			$row = $this->Option_model->getRow(array('name' => $data['name']));
			if ($row && $row->Id != $data['Id']) return $this->reply(400, "Option already exist", null);
			$this->Option_model->updateData(array('Id' => $data['Id']), $data);
		} else {
			unset($data['Id']);
			$row = $this->Option_model->getRow(array('name' => $data['name']));
			if ($row) return $this->reply(400, "Option already exist", null);
			$this->Option_model->insertData($data);
		}
		$this->reply(200, "ok", null);
	}

	public function edit_panel_prize()
	{
		$this->logonCheck();
		$prize = $this->input->post('prize');
		$this->Setting_model->updateData(array('name' => 'panel_prize'), array('value' => $prize));
		$this->reply(200, "ok", null);
	}
	
	public function edit_max_win()
	{
		$this->logonCheck();
		$maxwin = $this->input->post('maxwin');
		$this->Setting_model->updateData(array('name' => 'max_win'), array('value' => $maxwin));
		$this->reply(200, "ok", null);
	}
	

	public function edit_week($currentWeek = 0)
	{
		$this->logonCheck();
		$data = $this->input->post();

		//$currentWeek
		$row = $this->Week_model->getRow(array('week_no' => $data['week_no']));
		if ($row) {
			$this->Week_model->updateData(array('week_no' => $data['week_no']), $data);
		} else {
			$this->Week_model->insertData($data);
			//in this case add games automatically.
			$curWeekNo = $this->Setting_model->getCurrentWeekNo();
// 			$games = $this->Game_model->getDatas(array('week_no' => $curWeekNo));
// 			if (count($games) > 0) {
// 				foreach ($games as $game) {
// 					$this->Game_model->insertData(array(
// 						'game_no' => $game->game_no,
// 						'week_no' => $data['week_no'], 'home_team' => $game->home_team, 'away_team' => $game->away_team
// 					));
// 				}
// 			} else {
// 				for ($i = 1; $i < 50; $i++) {
// 					$this->Game_model->insertData(array('game_no' => $i, 'week_no' => $data['week_no']));
// 				}
// 			}
		}

		if ($currentWeek == 1) {
			$this->Setting_model->updateData(array('name' => 'current_week'), array('value' => $data['week_no']));
			$this->Terminal_model->updateData(['1' => 1], ['max_stake' => $data["max_stake"]]);
		}
		$this->reply(200, "ok", null);
	}
	public function edit_prize()
	{
		$this->logonCheck();
		$optionId = $this->input->post('optionId');
		$vals = array();
		$vals[] = $this->input->post('v3');
		$vals[] = $this->input->post('v4');
		$vals[] = $this->input->post('v5');
		$vals[] = $this->input->post('v6');
		$curWeekNo = $this->Setting_model->getCurrentWeekNo();

		for ($i = 0; $i < 4; $i++) {
			$row = $this->Prize_model->getRow(array('week_no' => $curWeekNo, 'option_id' => $optionId, 'under' => ($i + 3)));
			if ($row != null)
				$this->Prize_model->updateData(array('week_no' => $curWeekNo, 'option_id' => $optionId, 'under' => ($i + 3)), array('prize' => $vals[$i]));
			else
				$this->Prize_model->insertData(array('week_no' => $curWeekNo, 'option_id' => $optionId, 'under' => ($i + 3), 'prize' => $vals[$i]));
		}

		$this->reply(200, 'ok', null);
	}

	public function get_games($weekNo = 0)
	{
		$this->logonCheck();
		$curWeekNo = $this->Setting_model->getCurrentWeekNo();
		if ($weekNo == 0)
			$weekNo = $curWeekNo;

		$datas = $this->Game_model->getDatas(array('week_no' => $weekNo), 'game_no');
		$resData = array();
		foreach ($datas as $data) {
			$row = array();
			$row[] = $data->game_no;
			$row[] = $data->home_team;
			$row[] = $data->away_team;
			$row[] = $data->week_no;
			$row[] = $data->prize;

			if($data->status==1)
				$row[] = '<label class="label label-success">Enabled<label>';
			else
				$row[] = '<label class="label label-danger">Disabled<label>';

			$strAction = "";
			if($curWeekNo== $weekNo)
			{
				$strAction = '<a href="javascript:void(0)" class="on-default edit-row" ' .
				'onclick="onEdit(' . $data->Id . ')" title="Edit" ><i class="fa fa-pencil m-r-5"></i></a>' .
				'<a href="javascript:void(0)" class="on-default remove-row" ' .
				'onclick="onDelete(' . $data->Id . ')" title="Remove" ><i class="fa fa-trash-o text-pink m-r-5"></i></a>'.
				'<a href="javascript:void(0)" class="on-default remove-row" ' .
				'onclick="onEnable(' . $data->Id . ',1)" title="Enable" ><i class="icon-check m-r-5"></i></a>' .
				'<a href="javascript:void(0)" class="on-default remove-row" ' .
				'onclick="onEnable(' . $data->Id . ',0)" title="Disable" ><i class="icon-close text-pink m-r-5"></i></a>';
			}
			$row[] = $strAction;
			$resData[] = $row;
		}

		$output = array(
			"draw" => null,
			"recordsTotal" => count($resData),
			"recordsFiltered" => count($resData),
			"data" => $resData,
		);
		echo json_encode($output);
	}
	public function edit_game()
	{
		$this->logonCheck();
		$data = $this->input->post();

		if ($data['Id'] > 0) {
			$row = $this->Game_model->getRow(array('game_no' => $data['game_no'], 'week_no'=>$data['week_no']));
			if ($row && $row->Id != $data['Id']) return $this->reply(400, "Game number already exist", null);
			$this->Game_model->updateData(array('Id' => $data['Id']), $data);
		} else {
			unset($data['Id']);
			$row = $this->Game_model->getRow(array('game_no' => $data['game_no'], 'week_no'=>$data['week_no']));
			if ($row) return $this->reply(400, "Game number already exist", null);
			$this->Game_model->insertData($data);
		}
		$this->reply(200, "ok", null);
	}

	public function enable_game()
	{
		$this->logonCheck();
		$Id = $this->input->post('Id');
		$status = $this->input->post('status');
		$this->Game_model->updateData(array('Id' => $Id), array('status'=>$status));
		$this->reply(200, "ok", null);
	}	

	public function get_scores($weekNo = 0)
	{
		$this->logonCheck();
		$curWeekNo = $this->Setting_model->getCurrentWeekNo();
		if ($weekNo == 0)
			$weekNo = $curWeekNo;

		$datas = $this->Game_model->getDatas(array('week_no' => $weekNo), 'game_no');
		$resData = array();
		foreach ($datas as $data) {
			$row = array();
			$row[] = $data->game_no;
			$row[] = $data->home_team;
			$row[] = $data->away_team;
			$row[] = $data->week_no;
			$row[] = $data->home_score;
			$row[] = $data->away_score;
			if ($data->status == 1)
				$row[] = "<span><label class='label label-primary'>Active</label></span>";
			else
				$row[] = "<span><label class='label label-danger'>Disable</label></span>";

			$strAction = "";
			if ($weekNo == $curWeekNo) {
				$strAction = '<a href="javascript:void(0)" class="on-default edit-row" ' .
				'onclick="onEdit(' . $data->Id . ')" title="Edit" ><i class="fa fa-pencil"></i></a>';
			}
			$row[] = $strAction;
			$resData[] = $row;
		}

		$output = array(
			"draw" => null,
			"recordsTotal" => count($resData),
			"recordsFiltered" => count($resData),
			"data" => $resData,
		);
		echo json_encode($output);
	}
	public function edit_score()
	{
		$this->logonCheck();
		$data = $this->input->post();

		if ($data['Id'] > 0) {
			$row = $this->Game_model->getRow(array('game_no' => $data['game_no'],'week_no'=>$data['week_no']));
			if ($row && $row->Id != $data['Id']) return $this->reply(400, "Game number already exist", null);
			$this->Game_model->updateData(array('Id' => $data['Id']), $data);
		} else {
			unset($data['Id']);
			$row = $this->Game_model->getRow(array('game_no' => $data['game_no'],'week_no'=>$data['week_no']));
			if ($row) return $this->reply(400, "Game number already exist", null);
			$this->Game_model->insertData($data);
		}
		$this->reply(200, "ok", null);
	}

	public function get_user_wallet_status()
	{
		$this->logonCheck();

		$type = $this->session->userdata('type');
		$userId = $this->session->userdata('user_id');
		$cond = array('type' => 'agent');
		if ($type == 'agent') $cond['Id'] = $userId;
		else if ($type == 'staff') $cond['staff_id'] = $userId;
		$agentlits = $this->User_model->getDatas($cond);


		$datas = $this->User_model->getDatas(array('type' => 'player'));
		$resData = array();
		foreach ($datas as $data) {
			if ($this->find_data($agentlits, $data->agent_id) == null) continue;

			$row = array();
			$row[] = $data->Id;
			$row[] = $data->user_id;
			$row[] = $data->email;
			$row[] = $data->wallet;
			$resData[] = $row;
		}

		$output = array(
			"draw" => null,
			"recordsTotal" => count($resData),
			"recordsFiltered" => count($resData),
			"data" => $resData,
		);
		echo json_encode($output);
	}

	public function get_fund_requests()
	{
		$this->logonCheck();
		$datas = $this->FundRequest_model->getDatas(null);
		$resData = array();

		$no = 1;
		foreach ($datas as $data) {
			$user = $this->User_model->getRow(array('Id' => $data->user_id));
			if ($user == null) continue;

			$row = array();
			$row[] = $no;
			$row[] = $user->user_id;
			$row[] = $user->Id;
			$row[] = $data->bank_name;
			$row[] = $data->type;
			$row[] = $data->amount;
			$row[] = $data->dt;

			//status
			if ($data->status == 0)
				$row[] = "<span><label class='label label-success'>Pending</label></span>";
			else if ($data->status == 1)
				$row[] = "<span><label class='label label-primary'>Approved</label></span>";
			else if ($data->status == 2)
				$row[] = "<span><label class='label label-pink'>Canceled</label></span>";
			else if ($data->status == 3)
				$row[] = "<span><label class='label label-danger'>Deleted</label></span>";

			//action
			$strAction = '<a href="javascript:void(0)" class="on-default edit-row" ' .
				'onclick="onAction(' . $data->Id . ',1)" title="Approve" ><i class="icon-check text-primary"></i></a>' .
				'<a href="javascript:void(0)" class="on-default remove-row" ' .
				'onclick="onAction(' . $data->Id . ',2)" title="Cancel" ><i class="md md-clear text-warning"></i></a>' .
				'<a href="javascript:void(0)" class="on-default remove-row" ' .
				'onclick="onAction(' . $data->Id . ',3)" title="Remove" ><i class="fa fa-trash-o text-danger"></i></a>';
			$row[] = $strAction;

			$resData[] = $row;
			$no++;
		}
		$output = array(
			"draw" => null,
			"recordsTotal" => count($resData),
			"recordsFiltered" => count($resData),
			"data" => $resData,
		);
		echo json_encode($output);
	}

	public function edit_fund_request()
	{
		$this->logonCheck();
		$Id = $this->input->post('Id');
		$status = $this->input->post('status');
		$this->FundRequest_model->updateData(array('Id' => $Id), array('status' => $status));
		$this->reply(200, 'ok', null);
	}
	public function get_results($weekNo = 0)
	{
		$this->logonCheck();
		$curWeekNo = $this->Setting_model->getCurrentWeekNo();
		if ($weekNo == 0)
			$weekNo = $curWeekNo;
		$datas = $this->Game_model->getDatas(array('week_no' => $weekNo, 'status' => 1), 'game_no');
		$resData = array();

		$icons = ['fa-minus-circle text-info', 'fa-check-circle text-danger', 'fa-pause-circle-o text-warning'];
		$titles = ['Lost', 'Win', 'Panel'];
		foreach ($datas as $data) {
			$row = array();
			$row[] = $data->game_no;
			$row[] = $data->home_team;
			$row[] = $data->away_team;
			$row[] = $data->home_score;
			$row[] = $data->away_score;
			$row[] = $data->week_no;
			$row[] = $data->prize;

			// if($data->checked==1)
			// 	$row[] = '<span class="label label-table label-success">Checked</span>';
			// else 
			// 	$row[] = '<span class="label label-table label-inverse">Disabled</span>';
			//action
			$strAction = "";
			if ($weekNo == $curWeekNo) 
			{
				for($i=0; $i<3; $i++)
				{
					$class = "";
					if($data->checked==$i) $class = " check-selected";
					$strAct = '<a href="javascript:void(0)" onclick="onCheck(' . $data->Id . ','.$i.')" title="'.
					$titles[$i].'" ><i class="m-r-5 fa '.$icons[$i].$class.'" style="font-size: large;"></i></a>';
					$strAction .= $strAct;
				}
			}

			$row[] = $strAction;
			$resData[] = $row;
		}
		$output = array(
			"draw" => null,
			"recordsTotal" => count($resData),
			"recordsFiltered" => count($resData),
			"data" => $resData,
		);
		echo json_encode($output);
	}
	public function check_game()
	{
		$this->logonCheck();
		$Id = $this->input->post('Id');
		$checked = $this->input->post('checked');

		$this->Game_model->updateData(array('Id' => $Id), array('checked' => $checked));
		$this->reply(200, 'ok', null);
	}


	public function get_null_list()
	{
		$output = array(
			"draw" => null,
			"recordsTotal" => 0,
			"recordsFiltered" => 0,
			"data" => [],
		);
		echo json_encode($output);
	}
	public function get_bets_list($week = 0, $repeat = 0, $ticketNo = 'null', $betId = 0, $betAbove = 0, $fromDt = 'null', $toDt = 'null', $betStatus = 'Any', $agentId = 0, $terminalId = 0, $underId = 0)
	{
		$this->logonCheck();

		$no = $this->input->post("start");
		$length = $this->input->post("length");
		$terminalNO = $_POST['search']['value'];

		$terminals = $this->Terminal_model->getDatas(null);
		$players = $this->User_model->getDatas(array('status'=>1, 'type'=>'player'));

		$type = $this->session->userdata('type');
		$userId = $this->session->userdata('user_id');
		$cond = array('type' => 'agent');
		if ($type == 'agent') $cond['Id'] = $userId;
		else if ($type == 'staff') $cond['staff_id'] = $userId;
		$agentlits = $this->User_model->getDatas($cond);


		$condition = array('status' => 1);
		if ($week > 0) $condition['week'] = $week;
		//if ($ticketNo != 'null') $condition['ticket_no'] = $ticketNo;
		//if($betStatus!='')
		if ($agentId > 0) $condition['agent_id'] = $agentId;
		if ($terminalId > 0) $condition['terminal_id'] = $terminalId;
		if ($underId > 0) $condition['under'] = $underId;
		if ($betStatus != 'Any') $condition['win_result'] = $betStatus;
		//if ($amt > 0) $condition['stake_amount'] = $amt;
		if($betId > 0)$condition['bet_id'] = $betId;

		$resData = array();
		$bets = $this->Bet_model->getDatas($condition);

		//filter by time
		if ($fromDt != 'null' || $toDt != 'null') {
			$betts = array();
			foreach ($bets as $bet) {
				if ($fromDt != 'null' &&  $bet->bet_time < $fromDt) {
					continue;
				}
				if ($toDt != 'null' &&  $bet->bet_time > $toDt) {
					continue;
				}
				$betts[] = $bet;
			}
			$bets = $betts;
		}

		//calc repeat 
		// for ($i = 0; $i < count($bets); $i++) $bets[$i]->repeat = 1;
		// for ($i = 0; $i < count($bets); $i++) {
		// 	for ($j = 0; $j < count($bets); $j++) {
		// 		if ($i == $j) continue;
		// 		if ($bets[$i]->type != $bets[$j]->type) continue;
		// 		if ($bets[$i]->option_id != $bets[$j]->option_id) continue;
		// 		if ($bets[$i]->gamelist != $bets[$j]->gamelist) continue;
		// 		$bets[$i]->repeat++;
		// 	}
		// }

		$total = 0;
		foreach ($bets as $bet) {
			$agent = $this->find_data($agentlits, $bet->agent_id);
			if ( $agent == null) continue;		

			if ($repeat > 0 && $bet->repeats < $repeat) continue;
			if ($betAbove > 0 && $bet->stake_amount < $betAbove) continue;
			if($ticketNo != 'null' && strstr($bet->ticket_no, $ticketNo)==null)continue;


			$terminal = null;
			if($bet->terminal_id >0)
			{
				$terminal = $this->find_data($terminals, $bet->terminal_id);
				if($terminal == null) continue;
				
				if($terminalNO!="" && strstr($terminal->terminal_no, $terminalNO)==null) continue;
			}else if($terminalNO!="") continue;
	
			if($terminal == null) 
				continue;

			$total++;
			if ($total < $no || $total > ($no + $length)) continue;
			
			$under = $this->Under_model->getRow(array('under' => $bet->under));
			if ($under == null) continue;
			
			$row = array();
			$row[] = $bet->week;
			$row[] = $bet->bet_id;
			$row[] = $bet->under;

			$gamelist = "";
			if ($bet->type == 'Group') {
				$groups = json_decode($bet->gamelist, true);
				for ($iGrp = 0; $iGrp < count($groups); $iGrp++) {
					$line = $this->wrapping(chr(0x41 + $iGrp) . '(' . $groups[$iGrp]['under'] . '):' . $this->shortString($groups[$iGrp]['list']));
					$gamelist .= $line;
				}
			} else
				$gamelist = $this->wrapping($this->shortString(json_decode($bet->gamelist)));

			$row[] = $gamelist;
			$row[] = number_format($bet->apl, 2);
			$row[] = number_format($bet->stake_amount);
			$row[] = number_format($bet->won_amount);
			$row[] = $bet->ticket_no;
			
			$terminalNo = $terminal->terminal_no;
			$row[] = $terminalNo;
			$row[] = $agent->user_id;
			$row[] = $bet->bet_time;
			// $row[] = $bet->repeats;
			$strAction = "";
			if ($bet->status == 1 && ($type == 'admin' || $type == 'agent'))
				$strAction = '<a href="javascript:void(0)" class="on-default remove-row" ' .
					'onclick="onVoid(' . $bet->Id . ')" title="Void" ><i class="fa fa-trash-o text-danger">Void</i></a>';
			$row[] = $strAction;
			$resData[] = $row;
		}

		$output = array(
			"draw" => null,
			//"recordsTotal" => count($resData),
			"recordsTotal" => $total,
			"recordsFiltered" => $total,
			"data" => $resData,
		);
		echo json_encode($output);
	}

	public function get_result_bets_list($week = 0, $repeat = 0, $ticketNo = 'null', $amt = 0, $betAbove = 0, $fromDt = 'null', $toDt = 'null', $betStatus = 'Any', $agentId = 0, $terminalId = 0, $under = 0)
	{
		$this->logonCheck();
		$type = $this->session->userdata('type');
		$userId = $this->session->userdata('user_id');
		$cond = array('type' => 'agent');
		if ($type == 'agent') $cond['Id'] = $userId;
		else if ($type == 'staff') $cond['staff_id'] = $userId;
		$agentlits = $this->User_model->getDatas($cond);


		$no = $this->input->post('start');
		$length = $this->input->post('length');
		$terminalNO = $_POST['search']['value'];

		$terminals = $this->Terminal_model->getDatas(null);
		$players = $this->User_model->getDatas(array('status'=>1, 'type'=>'player'));
		$condition = array();
		if ($week > 0) $condition['week'] = $week;
		if ($ticketNo != 'null') $condition['ticket_no'] = $ticketNo;
		if ($amt > 0) $condition['stake_amount'] = $amt;
		//if($betStatus!='')
		if ($agentId > 0) $condition['agent_id'] = $agentId;
		if ($terminalId > 0) $condition['terminal_id'] = $terminalId;
		if ($under > 0) $condition['under'] = $under;
		if ($betStatus != 'Any') $condition['win_result'] = $betStatus;

		$resData = array();
		$bets = $this->Bet_model->getDatas($condition);

		//filter by time
		if ($fromDt != 'null' || $toDt != 'null') {
			$betts = array();
			foreach ($bets as $bet) {
				if ($fromDt != 'null' &&  $bet->bet_time < $fromDt) {
					continue;
				}
				if ($toDt != 'null' &&  $bet->bet_time > $toDt) {
					continue;
				}
				$betts[] = $bet;
			}
			$bets = $betts;
		}


		//calc repeat 
		// for ($i = 0; $i < count($bets); $i++) $bets[$i]->repeat = 1;
		// for ($i = 0; $i < count($bets); $i++) {
		// 	for ($j = 0; $j < count($bets); $j++) {
		// 		if ($i == $j) continue;
		// 		if ($bets[$i]->type != $bets[$j]->type) continue;
		// 		if ($bets[$i]->option_id != $bets[$j]->option_id) continue;
		// 		if ($bets[$i]->gamelist != $bets[$j]->gamelist) continue;
		// 		$bets[$i]->repeat++;
		// 	}
		// }

		$total = 0;
		foreach ($bets as $bet) {
			if($this->find_data($agentlits, $bet->agent_id)==null) continue;

			if ($betAbove > 0 && $bet->stake_amount < $betAbove) continue;			
			if ($repeat > 0 && $bet->repeats < $repeat) continue;
			$terminal = null;
			$user = null;

			if($bet->terminal_id > 0)
			{
				$terminal = $this->find_data($terminals, $bet->terminal_id);
				if($terminal == null) continue;
				if($terminalNO!="" && strstr($terminal->terminal_no, $terminalNO)==null) continue;	
			}else if($terminalNO!="") continue;

			if ($bet->user_id > 0) {
				$user = $this->find_data($players, $bet->user_id);
				if ($user == null)continue;
			}

			if($terminal == null && $user == null) continue;

			$total++;
			if ($total < $no || $total > ($no + $length)) continue;

			$under = $this->Under_model->getRow(array('under' => $bet->under));
			if ($under == null) continue;

			$row = array();
			$row[] = $bet->week;
			$row[] = $bet->bet_id;
			if ($user != null) {
				$row[] = $user->user_id;
			} else $row[] = '';


			$row[] = 'U'.$under->under;
			$row[] = 'U'.$bet->under;

			$gamelist = "";
			if ($bet->type == 'Group') {
				$groups = json_decode($bet->gamelist, true);
				for ($iGrp = 0; $iGrp < count($groups); $iGrp++) {
					$line = "<p>" . chr(0x41 + $iGrp) . '(' . $groups[$iGrp]['under'] . '):' . implode(',', $groups[$iGrp]['list']) . '</p>';
					$gamelist .= $line;
				}
			} else
				$gamelist = implode(',', json_decode($bet->gamelist));
			$row[] = $gamelist;

			//score list
			$gamelist = "";
			if ($bet->score_list != "") {
				if ($bet->type == 'Group') {
					$groups = json_decode($bet->score_list, true);
					for ($iGrp = 0; $iGrp < count($groups); $iGrp++) {
						$line = "<p>" . chr(0x41 + $iGrp) . '(' . $groups[$iGrp]['under'] . '):' . implode(',', $groups[$iGrp]['list']) . '</p>';
						$gamelist .= $line;
					}
				} else
					$gamelist = implode(',', json_decode($bet->score_list));
			}
			$row[] = $gamelist;

			$row[] = number_format($bet->apl,2);
			$row[] = number_format($bet->stake_amount);
			if ($bet->status == 1) $row[] = "<label class='label label-success'>Active</label>";
			else if ($bet->status == 2) $row[] = "<label class='label label-danger'>Void</label>";

			$row[] = $bet->win_result;
			if ($bet->won_amount > 0)
				$row[] = '<p class="text-danger">' . number_format($bet->won_amount,2) . '</p>';
			else $row[] = '';

			$terminalNo = "";
			if($terminal != null)
			{
				$terminalNo = $terminal->terminal_no;
				$row[] = str_replace($terminalNo, "", $bet->ticket_no);	
			}
			else
			{
				$row[] = str_replace($user->user_id, "", $bet->ticket_no);
			}
			$row[] = $terminalNo;

			$agent = $this->User_model->getRow(array('Id' => $bet->agent_id));
			if ($agent != null) $row[] = $agent->user_id;
			else $row[] = '';

			$row[] = $bet->repeats;
			$row[] = $bet->bet_time;
			$resData[] = $row;
		}

		$output = array(
			"draw" => null,
			"recordsTotal" => $total,
			"recordsFiltered" => $total,
			"data" => $resData,
		);
		echo json_encode($output);
	}


	public function get_result_summary0($week = 0, $repeat = 0, $ticketNo = 'null', $amt = 0, $betAbove = 0, $fromDt = 'null', $toDt = 'null', $betStatus = '', $agentId = 0, $terminalId = 0, $under = 0)
	{
		$this->logonCheck();
		$type = $this->session->userdata('type');
		$userId = $this->session->userdata('user_id');
		$cond = array('type' => 'agent');
		if ($type == 'agent') $cond['Id'] = $userId;
		else if ($type == 'staff') $cond['staff_id'] = $userId;
		$agentlits = $this->User_model->getDatas($cond);


		$condition = array();
		if ($week > 0) $condition['week_no'] = $week;
		// if($ticketNo!='null')$condition['ticket_no'] = $ticketNo;
		// if($amt > 0 )$condition['stake_amount'] = $amt;
		// //if($betStatus!='')
		if ($agentId > 0) $condition['agent_id'] = $agentId;
		if ($terminalId > 0) $condition['terminal_id'] = $terminalId;
		if ($under > 0) $condition['under'] = $under;

		$resData = array();
		$summaries = $this->Summary_model->getDatas($condition);

		$sales = array();
		$wins = array();
		$total_sale = 0;
		$total_payable = 0;
		$total_win = 0;
		$bal_agent = "";
		$bal_company = "";
		$status = "";

		if (count($summaries) == 0) {
			$output = array(
				"draw" => null,
				"recordsTotal" => 0,
				"recordsFiltered" => 0,
				"data" => [],
			);
			echo json_encode($output);
			return;
		}

		//$odd_summary = array();
		foreach ($summaries as $summary) {
			if($this->find_data($agentlits, $summary->agent_id)==null) continue;			

			$under = $this->Under_model->getRow(array('under' => $summary->under));
			if ($under == null) continue;
			
			if (isset($sales[$under->under])) $sales[$under->under] = $sales[$under->under] + $summary->sales;
			else $sales[$under->under] = $summary->sales;

			if (isset($wins[$under->under])) $wins[$under->under] = $wins[$under->under] + $summary->win;
			else $wins[$under->under] = $summary->win;

			$total_sale += $summary->sales;
			$total_payable += $summary->payable;
			$total_win += $summary->win;
		}

		if ($total_payable > $total_win) {
			$bal_company = $total_payable - $total_win;
			$status = '<label class="label label-success">green</label>';
		} else {
			$bal_agent = $total_win - $total_payable;
			$status = '<label class="label label-danger">red</label>';
		}

		// $agentId = "";
		// $agent=$this->User_model->getRow(array('Id'=>$terminal->agent_id));
		// if($agent!=null) $agentId= $agent->user_id;

		$resData = array();
		$row = array();

		$strtmp = "";
		foreach ($sales as $key => $value) {
			$strtmp .= '<p>U' . $key . ': ' . number_format($value) . '</p>';
		}
		$row[] = $strtmp;

		$row[] = number_format($total_sale);
		$row[] = number_format($total_payable);

		$strtmp = "";
		foreach ($wins as $key => $value) {
			$strtmp .= '<p>U' . $key . ': ' . number_format($value) . '</p>';
		}
		$row[] = $strtmp;

		if ($total_win > 0)
			$row[] = '<p class="text-danger">' . number_format($total_win) . '</p>';
		else $row[] = '';

		if($bal_agent !="")	$row[] = number_format($bal_agent);
		else $row[] = $bal_agent;

		if($bal_company !="")$row[] = number_format($bal_company);
		else $row[] = $bal_company;		

		$row[] = $status;
		$resData[] = $row;
		$output = array(
			"draw" => null,
			"recordsTotal" => count($resData),
			"recordsFiltered" => count($resData),
			"data" => $resData,
		);
		echo json_encode($output);
	}

	public function get_winner_bets_list($week = 0, $repeat = 0, $ticketNo = 'null', $amt = 0, $betAbove = 0, $fromDt = 'null', $toDt = 'null', $betStatus = 'Any', $agentId = 0, $terminalId = 0, $under = 0)
	{
		$this->logonCheck();
		$type = $this->session->userdata('type');
		$userId = $this->session->userdata('user_id');
		$cond = array('type' => 'agent');
		if ($type == 'agent') $cond['Id'] = $userId;
		else if ($type == 'staff') $cond['staff_id'] = $userId;
		$agentlits = $this->User_model->getDatas($cond);


		$no = $this->input->post('start');
		$length = $this->input->post('length');
		$terminalNO = $_POST['search']['value'];

		$terminals = $this->Terminal_model->getDatas(null);
		$players = $this->User_model->getDatas(array('status'=>1, 'type'=>'player'));		

		$condition = array('win_result' => 'Win');
		if ($week > 0) $condition['week'] = $week;
// 		if ($ticketNo != 'null') $condition['ticket_no'] = $ticketNo;
		if ($amt > 0) $condition['stake_amount'] = $amt;
		//if($betStatus!='')
		if ($agentId > 0) $condition['agent_id'] = $agentId;
		if ($terminalId > 0) $condition['terminal_id'] = $terminalId;
		if ($under > 0) $condition['under'] = $under;
		if ($betStatus != 'Any') $condition['win_result'] = $betStatus;

		$resData = array();
		$bets = $this->Bet_model->getDatas($condition);

		//filter by time
		if ($fromDt != 'null' || $toDt != 'null') {
			$betts = array();
			foreach ($bets as $bet) {
				if ($fromDt != 'null' &&  $bet->bet_time < $fromDt) {
					continue;
				}
				if ($toDt != 'null' &&  $bet->bet_time > $toDt) {
					continue;
				}
				$betts[] = $bet;
			}
			$bets = $betts;
		}


		//calc repeat 
		// for ($i = 0; $i < count($bets); $i++) $bets[$i]->repeat = 1;
		// for ($i = 0; $i < count($bets); $i++) {
		// 	for ($j = 0; $j < count($bets); $j++) {
		// 		if ($i == $j) continue;
		// 		if ($bets[$i]->type != $bets[$j]->type) continue;
		// 		if ($bets[$i]->option_id != $bets[$j]->option_id) continue;
		// 		if ($bets[$i]->gamelist != $bets[$j]->gamelist) continue;
		// 		$bets[$i]->repeat++;
		// 	}
		// }

		$total = 0;
		foreach ($bets as $bet) {
			//check agent
			if($this->find_data($agentlits, $bet->agent_id)==null) continue;
			
            if ($ticketNo != 'null' && !strstr($bet->ticket_no, $ticketNo))continue;


			//repeat
			if ($repeat > 0 && $bet->repeats < $repeat) continue;
			//won amount
			if ($betAbove > 0 && $bet->won_amount < $betAbove) continue;

			$terminal = null;
			$user = null;

			if($bet->terminal_id > 0)
			{
				$terminal = $this->find_data($terminals, $bet->terminal_id);
				if($terminal == null) continue;
				if($terminalNO!="" && strstr($terminal->terminal_no, $terminalNO)==null) continue;	
			}else if($terminalNO!="") continue;

			if ($bet->user_id > 0) {
				$user = $this->find_data($players, $bet->user_id);
				if ($user == null) continue;
			}
			if($user==null && $terminal == null) continue;

			$total++;
			if ($total < $no || $total > ($no + $length)) continue;

			$under = $this->Under_model->getRow(array('under' => $bet->under));
			if ($under == null) continue;
			

			$row = array();
			$row[] = $bet->week;
			$row[] = $bet->bet_id;
			$row[] = 'U'.$under->under;

			$gamelist = "";
			if ($bet->type == 'Group') {
				$groups = json_decode($bet->gamelist, true);
				for ($iGrp = 0; $iGrp < count($groups); $iGrp++) {
					$line = "<p>" . chr(0x41 + $iGrp) . '(' . $groups[$iGrp]['under'] . '):' . implode(',', $groups[$iGrp]['list']) . '</p>';
					$gamelist .= $line;
				}
			} else
				$gamelist = implode(',', json_decode($bet->gamelist));
			$row[] = $gamelist;

			//score list
			$gamelist = "";
			if ($bet->score_list != "") {
				if ($bet->type == 'Group') {
					$groups = json_decode($bet->score_list, true);
					for ($iGrp = 0; $iGrp < count($groups); $iGrp++) {
						$line = "<p>" . chr(0x41 + $iGrp) . '(' . $groups[$iGrp]['under'] . '):' . implode(',', $groups[$iGrp]['list']) . '</p>';
						$gamelist .= $line;
					}
				} else
					$gamelist = implode(',', json_decode($bet->score_list));
			}
			$row[] = $gamelist;

			$row[] = number_format($bet->apl,2);
			$row[] = number_format($bet->stake_amount);

			if ($bet->won_amount > 0)
				$row[] = '<p class="text-danger">' . number_format($bet->won_amount,2) . '</p>';
			else $row[] = '';

			$terminalNo = "";
			if($terminal != null)
			{
				$terminalNo = $terminal->terminal_no;
				$row[] = str_replace($terminalNo, "", $bet->ticket_no);	
			}
			else
			{
				$row[] = str_replace($user->user_id, "", $bet->ticket_no);
			}
			$row[] = $terminalNo;

			$agent = $this->User_model->getRow(array('Id' => $bet->agent_id));
			if ($agent != null) $row[] = $agent->user_id;
			else $row[] = '';

			$row[] = $bet->repeats;
			$row[] = $bet->bet_time;
			$resData[] = $row;
		}

		$output = array(
			"draw" => null,
			"recordsTotal" => $total,
			"recordsFiltered" => $total,
			"data" => $resData,
		);
		echo json_encode($output);
	}

	public function get_result_summary0_by_staff($week = 0, $staffId = 0)
	{
		$this->logonCheck();
		$type = $this->session->userdata('type');
		$userId = $this->session->userdata('user_id');
		$cond = array('type' => 'agent');
		if ($type == 'agent') $cond['Id'] = $userId;
		else if ($type == 'staff') $cond['staff_id'] = $userId;
		$agentlits = $this->User_model->getDatas($cond);


		$condition = array();
		if ($week > 0) $condition['week_no'] = $week;

		$resData = array();
		$summaries = array();
		$summares = $this->Summary_model->getDatas($condition);

		foreach ($summares as $summar) {
			$agent = $this->find_data($agentlits, $summar->agent_id);
			if ($agent == null)
				continue;
			if ($staffId > 0 && $staffId != $agent->staff_id)
				continue;
			$summaries[] = $summar;
		}

		$sales = array();
		$wins = array();
		$total_sale = 0;
		$total_payable = 0;
		$total_win = 0;
		$bal_agent = "";
		$bal_company = "";
		$status = "";

		if (count($summaries) == 0) {
			$output = array(
				"draw" => null,
				"recordsTotal" => 0,
				"recordsFiltered" => 0,
				"data" => [],
			);
			echo json_encode($output);
			return;
		}

		//$odd_summary = array();
		foreach ($summaries as $summary) {
			$under = $this->Under_model->getRow(array('under' => $summary->under));
			if ($under == null) continue;

			if (isset($sales[$under->under])) $sales[$under->under] = $sales[$under->under] + $summary->sales;
			else $sales[$under->under] = $summary->sales;

			if (isset($wins[$under->under])) $wins[$under->under] = $wins[$under->under] + $summary->win;
			else $wins[$under->under] = $summary->win;

			$total_sale += $summary->sales;
			$total_payable += $summary->payable;
			$total_win += $summary->win;
		}

		if ($total_payable > $total_win) {
			$bal_company = $total_payable - $total_win;
			$status = '<label class="label label-success">green</label>';
		} else {
			$bal_agent = $total_win - $total_payable;
			$status = '<label class="label label-danger">red</label>';
		}

		// $agentId = "";
		// $agent=$this->User_model->getRow(array('Id'=>$terminal->agent_id));
		// if($agent!=null) $agentId= $agent->user_id;

		$resData = array();
		$row = array();

		$strtmp = "";
		$tw = 0;
		foreach ($sales as $key => $value) {
		    $tw += $value;
			//$strtmp .= '<p>U' . $key . ': ' . number_format($value) . '</p>';
		}
		$row[] = number_format($tw);

		$row[] = number_format($total_sale);
		$row[] = number_format($total_payable);

        $tw = 0;
		$strtmp = "";
		foreach ($wins as $key => $value) {
		    $tw += $value;
			//$strtmp .= '<p>U' . $key . ': ' . number_format($value) . '</p>';
		}
		//$row[] = $strtmp;
		$row[] = number_format($tw);

		if ($total_win > 0)
			$row[] = '<p class="text-danger">' . number_format($total_win) . '</p>';
		else $row[] = '';

		if($bal_agent !="")	$row[] = number_format($bal_agent);
		else $row[] = $bal_agent;

		if($bal_company !="")$row[] = number_format($bal_company);
		else $row[] = $bal_company;		
		
		$row[] = $status;
		$resData[] = $row;
		$output = array(
			"draw" => null,
			"recordsTotal" => count($resData),
			"recordsFiltered" => count($resData),
			"data" => $resData,
		);
		echo json_encode($output);
	}

	public function get_result_summary_by_staff($week = 0, $staffId = 0)
	{
		$this->logonCheck();
		$type = $this->session->userdata('type');
		$userId = $this->session->userdata('user_id');
		$cond = array('type' => 'agent');
		if ($type == 'agent') $cond['Id'] = $userId;
		else if ($type == 'staff') $cond['staff_id'] = $userId;
		$agentlits = $this->User_model->getDatas($cond);
		
		
		$condition = array();
		if ($week > 0) $condition['week_no'] = $week;
		// if($ticketNo!='null')$condition['ticket_no'] = $ticketNo;
		// if($amt > 0 )$condition['stake_amount'] = $amt;
		// //if($betStatus!='')

		$resData = array();
		$summaries = $this->Summary_model->getDatas($condition);

		$staffs = array();
		//$odd_summary = array();
		foreach ($summaries as $summary) {
			$agent = $this->find_data($agentlits, $summary->agent_id);
			if ($agent == null) continue;
			if ($staffId > 0 &&  $staffId != $agent->staff_id) continue;

			$under = $this->Under_model->getRow(array('under' => $summary->under));
			if ($under == null) continue;

			$staff = $this->User_model->getRow(array('Id' => $agent->staff_id));
			if ($staff == null) continue;

			if (isset($staffs[$staff->Id]) == false)
				$row = array('staff' => $staff->user_id, 'sales' => array(), 'payables' => array(), 'wins' => array(), 'total_sale' => 0, 'total_payable' => 0, 'total_win' => 0);
			else $row = $staffs[$staff->Id];

			$sales = $row['sales'];
			$wins = $row['wins'];
			$payables = $row['payables'];

			if (isset($sales[$under->under])) $sales[$under->under] = $sales[$under->under] + $summary->sales;
			else $sales[$under->under] = $summary->sales;

			if (isset($wins[$under->under])) $wins[$under->under] = $wins[$under->under] + $summary->win;
			else $wins[$under->under] = $summary->win;

			if (isset($payables[$under->under])) $payables[$under->under] = $payables[$under->under] + $summary->payable;
			else $payables[$under->under] = $summary->payable;


			$row['total_sale'] += $summary->sales;
			$row['total_payable'] += $summary->payable;
			$row['total_win'] += $summary->win;
			$row['sales'] = $sales;
			$row['wins'] = $wins;
			$row['payables'] = $payables;
			$staffs[$staff->Id] = $row;
		}

		foreach ($staffs as $key => $data) {
			$row = array();

			if ($week == 0) $row[] = 'All';
			else $row[] = $week;
			$row[] = $data['staff'];

			//sales
			$sales = $data['sales'];
			$strtmp = "";
			$tw = 0;
			foreach ($sales as $key => $value) {
			    $tw += $value;
				//$strtmp .= '<p>U' . $key . ': ' . number_format($value) . '</p>';
			}
			$row[] = number_format($tw);
			//$row[] = $strtmp;

			//payables
			$sales = $data['payables'];
			$strtmp = "";
			$tw = 0;
			foreach ($sales as $key => $value) {
			    $tw += $value;
				//$strtmp .= '<p>U' . $key . ': ' . number_format($value) . '</p>';
			}
			$row[] = number_format($tw);
			//$row[] = $strtmp;

			//wins
			$wins = $data['wins'];
			$strtmp = "";
			$tw = 0;
			foreach ($wins as $key => $value) {
			    $tw += $value;
				//$strtmp .= '<p>U' . $key . ': ' . number_format($value) . '</p>';
			}
            $row[] = number_format($tw);			
			//$row[] = $strtmp;

			if ($data['total_win'] > 0)
				$row[] = '<p class="text-danger">' . number_format($data['total_win']) . '</p>';
			else $row[] = '';

			$bal_agent = "";
			$bal_company = "";
			$status = "";
			if ($data['total_payable'] > $data['total_win']) {
				$bal_company = number_format($data['total_payable'] - $data['total_win']);
				$status = '<label class="label label-success">green</label>';
			} else {
				$bal_agent = number_format($data['total_win'] - $data['total_payable']);
				$status = '<label class="label label-danger">red</label>';
			}
			$row[] = $bal_agent;
			$row[] = $bal_company;
			$row[] = $status;
			$resData[] = $row;
		}

		$output = array(
			"draw" => null,
			"recordsTotal" => count($resData),
			"recordsFiltered" => count($resData),
			"data" => $resData,
		);
		echo json_encode($output);
	}

	public function get_result_summary0_by_agent($week = 0, $staffId = 0, $agentId = 0, $terminalId = 0)
	{
		$this->logonCheck();

		$type = $this->session->userdata('type');
		$userId = $this->session->userdata('user_id');
		$cond = array('type' => 'agent');
		if ($type == 'agent') $cond['Id'] = $userId;
		else if ($type == 'staff') $cond['staff_id'] = $userId;
		$agentlits = $this->User_model->getDatas($cond);


		$condition = array();
		if ($week > 0) $condition['week_no'] = $week;
		if ($agentId > 0) $condition['agent_id'] = $agentId;
		if ($terminalId > 0) $condition['terminal_id'] = $terminalId;

		$resData = array();
		$summaries = array();
		$summares = $this->Summary_model->getDatas($condition);

		foreach ($summares as $summar) {
			$agent = $this->find_data($agentlits, $summar->agent_id);
			if($agent==null) continue;
			if ($staffId > 0 && $staffId != $agent->staff_id)
				continue;
			$summaries[] = $summar;
		}

		$sales = array();
		$wins = array();
		$total_sale = 0;
		$total_payable = 0;
		$total_win = 0;
		$bal_agent = "";
		$bal_company = "";
		$status = "";

		if (count($summaries) == 0) {
			$output = array(
				"draw" => null,
				"recordsTotal" => 0,
				"recordsFiltered" => 0,
				"data" => [],
			);
			echo json_encode($output);
			return;
		}

		//$odd_summary = array();
		foreach ($summaries as $summary) {
			$under = $this->Under_model->getRow(array('under' => $summary->under));
			if ($under == null) continue;

			if (isset($sales[$under->under])) $sales[$under->under] = $sales[$under->under] + $summary->sales;
			else $sales[$under->under] = $summary->sales;

			if (isset($wins[$under->under])) $wins[$under->under] = $wins[$under->under] + $summary->win;
			else $wins[$under->under] = $summary->win;

			$total_sale += $summary->sales;
			$total_payable += $summary->payable;
			$total_win += $summary->win;
		}

		if ($total_payable > $total_win) {
			$bal_company = $total_payable - $total_win;
			$status = '<label class="label label-success">green</label>';
		} else {
			$bal_agent = $total_win - $total_payable;
			$status = '<label class="label label-danger">red</label>';
		}

		// $agentId = "";
		// $agent=$this->User_model->getRow(array('Id'=>$terminal->agent_id));
		// if($agent!=null) $agentId= $agent->user_id;

		$resData = array();
		$row = array();

        $tw = 0;
		$strtmp = "";
		foreach ($sales as $key => $value) {
		    $tw += $value;
			//$strtmp .= '<p>U' . $key . ': ' . number_format($value) . '</p>';
		}
		$row[] = number_format($tw);

		$row[] = number_format($total_sale);
		$row[] = number_format($total_payable);

        $tw = 0;
		$strtmp = "";
		foreach ($wins as $key => $value) {
		    $tw += $value;
			//$strtmp .= '<p>U' . $key . ': ' . number_format($value) . '</p>';
		}
		$row[] = number_format($tw);

		if ($total_win > 0)
			$row[] = '<p class="text-danger">' . number_format($total_win) . '</p>';
		else $row[] = '';

		if($bal_agent !="")	$row[] = number_format($bal_agent);
		else $row[] = $bal_agent;

		if($bal_company !="")$row[] = number_format($bal_company);
		else $row[] = $bal_company;

		$row[] = $status;
		$resData[] = $row;
		$output = array(
			"draw" => null,
			"recordsTotal" => count($resData),
			"recordsFiltered" => count($resData),
			"data" => $resData,
		);
		echo json_encode($output);
	}

	public function get_result_summary_by_agent($week = 0, $staffId = 0, $agentId = 0, $terminalId = 0)
	{
		$this->logonCheck();
		$type = $this->session->userdata('type');
		$userId = $this->session->userdata('user_id');
		$cond = array('type' => 'agent');
		if ($type == 'agent') $cond['Id'] = $userId;
		else if ($type == 'staff') $cond['staff_id'] = $userId;
		$agentlits = $this->User_model->getDatas($cond);


		$condition = array();
		if ($week > 0) $condition['week_no'] = $week;
		if ($agentId > 0) $condition['agent_id'] = $agentId;
		if ($terminalId > 0) $condition['terminal_id'] = $terminalId;


		$resData = array();
		$summaries = $this->Summary_model->getDatas($condition);

		$staffs = array();
		//$odd_summary = array();
		foreach ($summaries as $summary) {
			if ($summary->terminal_id == 0) continue;
			$agent = $this->find_data($agentlits, $summary->agent_id);
			if ($agent == null) continue;

			$terminal = $this->Terminal_model->getRow(array('Id' => $summary->terminal_id));
			if ($terminal == null) continue;

			//get staff
			$staff_id = $agent->staff_id;
			if ($staffId > 0 &&  $staffId != $staff_id) continue;

			$staff = $this->User_model->getRow(array('Id' => $staff_id));
			if ($staff == null) continue;

			$under = $this->Under_model->getRow(array('under' => $summary->under));
			if ($under == null) continue;


			if (isset($staffs[$staff_id]) == false)
				$row = array(
					'staff' => $staff->user_id, 'agents' => array(),
					'total_sale' => 0, 'total_payable' => 0, 'total_win' => 0
				);
			else $row = $staffs[$staff_id];

			$agents = $row['agents'];

			if (isset($agents[$agent->Id]) == false)
				$rowAgent = array('agent' => $agent->user_id, 'terminals' => array());
			else $rowAgent = $agents[$agent->Id];

			$terminals = $rowAgent['terminals'];
			if (isset($terminals[$terminal->Id]) == false)
				$rowTerminal = array('terminal' => $terminal->terminal_no, 'sales' => array(), 'payables' => array(), 'wins' => array());
			else $rowTerminal = $terminals[$terminal->Id];

			$payables = $rowTerminal['payables'];
			$sales = $rowTerminal['sales'];
			$wins = $rowTerminal['wins'];

			if (isset($sales[$under->under])) $sales[$under->under] = $sales[$under->under] + $summary->sales;
			else $sales[$under->under] = $summary->sales;

			if (isset($wins[$under->under])) $wins[$under->under] = $wins[$under->under] + $summary->win;
			else $wins[$under->under] = $summary->win;

			if (isset($payables[$under->under])) $payables[$under->under] = $payables[$under->under] + $summary->payable;
			else $payables[$under->under] = $summary->payable;

			$rowTerminal['sales'] = $sales;
			$rowTerminal['wins'] = $wins;
			$rowTerminal['payables'] = $payables;
			$terminals[$terminal->Id] = $rowTerminal;
			$rowAgent['terminals'] = $terminals;
			$agents[$agent->Id] = $rowAgent;
			$row['agents'] = $agents;


			$row['total_sale'] += $summary->sales;
			$row['total_payable'] += $summary->payable;
			$row['total_win'] += $summary->win;
			$staffs[$staff_id] = $row;
		}


		foreach ($staffs as $key => $data) {

			foreach ($data['agents'] as $key1 => $agent) {
				foreach ($agent['terminals'] as $key2 => $terminal) {
					$row = array();
					if ($week == 0) $row[] = 'All';
					else $row[] = $week;
					$row[] = $data['staff'];
					$row[] = $agent['agent'];
					$row[] = $terminal['terminal'];

					//sales
					$sales = $terminal['sales'];
					$strtmp = "";
					$ts = 0;
					foreach ($sales as $key => $value) {
					    $ts += $value;
				// 		$strtmp .= '<p>U' . $key . ': ' . number_format($value) . '</p>';
					}
					$row[] = $strtmp.'<p>'.number_format($ts).'</p>';

					//payables
					$sales = $terminal['payables'];
					$strtmp = "";
					$tp = 0;
					foreach ($sales as $key => $value) {
					    $tp += $value;
				// 		$strtmp .= '<p>U' . $key . ': ' . number_format($value) . '</p>';
					}
					$row[] = $strtmp.'<p>'.number_format($tp).'</p>';
					//$row[] = $data['total_payable'];

					//wins
					$tw = 0;
					$wins = $terminal['wins'];
					$strtmp = "";
					foreach ($wins as $key => $value) {
					    $tw += $value;
				// 		$strtmp .= '<p>U' . $key . ': ' . number_format($value) . '</p>';
					}
					$row[] = $strtmp.'<p>'.number_format($tw).'</p>';

					if ($data['total_win'] > 0)
						$row[] = '<p class="text-danger">' . number_format($data['total_win']) . '</p>';
					else $row[] = '';

					// $bal_agent = "";
					// $bal_company = "";
					$status = "";
					if ($data['total_payable'] > $data['total_win']) {
						$bal_company = $data['total_payable'] - $data['total_win'];
						$status = '<label class="label label-success">green</label>';
					} else {
						$bal_agent = $data['total_win'] - $data['total_payable'];
						$status = '<label class="label label-danger">red</label>';
					}
					// $row[] = $bal_agent;
					// $row[] = $bal_company;
					$row[] = $status;
					$resData[] = $row;
				}
			}
		}

		$output = array(
			"draw" => null,
			"recordsTotal" => count($resData),
			"recordsFiltered" => count($resData),
			"data" => $resData,
		);
		echo json_encode($output);
	}

	public function get_result_summary0_by_terminal($week = 0, $agentId = 0, $terminalId = 0)
	{
		$this->logonCheck();

		$type = $this->session->userdata('type');
		$userId = $this->session->userdata('user_id');
		$cond = array('type' => 'agent');
		if ($type == 'agent') $cond['Id'] = $userId;
		else if ($type == 'staff') $cond['staff_id'] = $userId;
		$agentlits = $this->User_model->getDatas($cond);			

		$condition = array();
		if ($week > 0) $condition['week_no'] = $week;
		if ($agentId > 0) $condition['agent_id'] = $agentId;
		if ($terminalId > 0) $condition['terminal_id'] = $terminalId;

		$resData = array();
		$summaries = $this->Summary_model->getDatas($condition);

		$sales = array();
		$wins = array();
		$total_sale = 0;
		$total_payable = 0;
		$total_win = 0;
		$bal_agent = "";
		$bal_company = "";
		$status = "";

		if (count($summaries) == 0) {
			$output = array(
				"draw" => null,
				"recordsTotal" => 0,
				"recordsFiltered" => 0,
				"data" => [],
			);
			echo json_encode($output);
			return;
		}

		//$odd_summary = array();
		foreach ($summaries as $summary) {
			if($this->find_data($agentlits, $summary->agent_id)==null) continue;

			$under = $this->Under_model->getRow(array('under' => $summary->under));
			if ($under == null) continue;

			if (isset($sales[$under->under])) $sales[$under->under] = $sales[$under->under] + $summary->sales;
			else $sales[$under->under] = $summary->sales;

			if (isset($wins[$under->under])) $wins[$under->under] = $wins[$under->under] + $summary->win;
			else $wins[$under->under] = $summary->win;

			$total_sale += $summary->sales;
			$total_payable += $summary->payable;
			$total_win += $summary->win;
		}

		if ($total_payable > $total_win) {
			$bal_company = $total_payable - $total_win;
			$status = '<label class="label label-success">green</label>';
		} else {
			$bal_agent = $total_win - $total_payable;
			$status = '<label class="label label-danger">red</label>';
		}

		// $agentId = "";
		// $agent=$this->User_model->getRow(array('Id'=>$terminal->agent_id));
		// if($agent!=null) $agentId= $agent->user_id;

		$resData = array();
		$row = array();

		$strtmp = "";
		foreach ($sales as $key => $value) {
			$strtmp .= '<p>U' . $key . ': ' . number_format($value) . '</p>';
		}
		$row[] = $strtmp;

		$row[] = number_format($total_sale);
		$row[] = number_format($total_payable);

		$strtmp = "";
		foreach ($wins as $key => $value) {
			$strtmp .= '<p>U' . $key . ': ' . number_format($value) . '</p>';
		}
		$row[] = $strtmp;

		if ($total_win > 0)
			$row[] = '<p class="text-danger">' . number_format($total_win) . '</p>';
		else $row[] = '';

		if($bal_agent !="")	$row[] = number_format($bal_agent);
		else $row[] = $bal_agent;

		if($bal_company !="")$row[] = number_format($bal_company);
		else $row[] = $bal_company;
		
		$row[] = $status;
		$resData[] = $row;
		$output = array(
			"draw" => null,
			"recordsTotal" => count($resData),
			"recordsFiltered" => count($resData),
			"data" => $resData,
		);
		echo json_encode($output);
	}

	public function get_result_summary_by_terminal($week = 0, $agentId = 0, $terminalId = 0)
	{
		$this->logonCheck();
		$type = $this->session->userdata('type');
		$userId = $this->session->userdata('user_id');
		$cond = array('type' => 'agent');
		if ($type == 'agent') $cond['Id'] = $userId;
		else if ($type == 'staff') $cond['staff_id'] = $userId;
		$agentlits = $this->User_model->getDatas($cond);


		$condition = array();
		if ($week > 0) $condition['week_no'] = $week;
		if ($agentId > 0) $condition['agent_id'] = $agentId;
		if ($terminalId > 0) $condition['terminal_id'] = $terminalId;


		$resData = array();
		$summaries = $this->Summary_model->getDatas($condition);

		$staffs = array();
		//$odd_summary = array();
		foreach ($summaries as $summary) {
			if ($summary->terminal_id == 0) continue;
			if($this->find_data($agentlits, $summary->agent_id)==null) continue;

			$terminal = $this->Terminal_model->getRow(array('Id' => $summary->terminal_id));
			if ($terminal == null) continue;

			$agent = $this->User_model->getRow(array('Id' => $summary->agent_id));
			if ($agent == null) continue;

			$under = $this->Under_model->getRow(array('under' => $summary->under));
			if ($under == null) continue;

			if (isset($staffs[$terminal->Id]) == false)
				$row = array(
					'agent' => $agent->user_id, 'terminal' => $terminal->terminal_no,
					'sales' => array(), 'payables' => array(), 'wins' => array(), 'total_sale' => 0, 'total_payable' => 0, 'total_win' => 0
				);
			else $row = $staffs[$terminal->Id];

			$sales = $row['sales'];
			$wins = $row['wins'];
			$payables = $row['payables'];

			if (isset($sales[$under->under])) $sales[$under->under] = $sales[$under->under] + $summary->sales;
			else $sales[$under->under] = $summary->sales;

			if (isset($wins[$under->under])) $wins[$under->under] = $wins[$under->under] + $summary->win;
			else $wins[$under->under] = $summary->win;

			if (isset($payables[$under->under])) $payables[$under->under] = $payables[$under->under] + $summary->payable;
			else $payables[$under->under] = $summary->payable;

			$row['total_sale'] += $summary->sales;
			$row['total_payable'] += $summary->payable;
			$row['total_win'] += $summary->win;
			$row['sales'] = $sales;
			$row['wins'] = $wins;
			$row['payables'] = $payables;
			$staffs[$terminal->Id] = $row;
		}

		foreach ($staffs as $key => $data) {
			$row = array();

			if ($week == 0) $row[] = 'All';
			else $row[] = $week;
			$row[] = $data['agent'];
			$row[] = $data['terminal'];

			//sales
			$sales = $data['sales'];
			$strtmp = "";
			foreach ($sales as $key => $value) {
				$strtmp .= '<p>U' . $key . ': ' . number_format($value) . '</p>';
			}
			$row[] = $strtmp;

			//payables
			$sales = $data['payables'];
			$strtmp = "";
			foreach ($sales as $key => $value) {
				$strtmp .= '<p>U' . $key . ': ' . number_format($value) . '</p>';
			}
			$row[] = $strtmp;

			$row[] = $data['total_payable'];

			//wins
			$wins = $data['wins'];
			$strtmp = "";
			foreach ($wins as $key => $value) {
				$strtmp .= '<p class="text-danger">' . $key . ':' . number_format($value) . '</p>';
			}
			$row[] = $strtmp;

			if ($data['total_win'] > 0)
				$row[] = '<p class="text-danger">' . number_format($data['total_win']) . '</p>';
			else $row[] = '';

			$bal_agent = "";
			$bal_company = "";
			$status = "";
			if ($data['total_payable'] > $data['total_win']) {
				$bal_company = $data['total_payable'] - $data['total_win'];
				$status = '<label class="label label-success">green</label>';
			} else {
				$bal_agent = $data['total_win'] - $data['total_payable'];
				$status = '<label class="label label-danger">red</label>';
			}

			if($bal_agent!="")$row[] = number_format($bal_agent);
			else $row[] = $bal_agent;

			if($bal_company!="")$row[] = number_format($bal_company);
			else $row[] = $bal_company;
			
			$row[] = $status;
			$resData[] = $row;
		}

		$output = array(
			"draw" => null,
			"recordsTotal" => count($resData),
			"recordsFiltered" => count($resData),
			"data" => $resData,
		);
		echo json_encode($output);
	}

	public function get_delete_request($week = 0)
	{
		$this->logonCheck();

		$type = $this->session->userdata('type');
		$userId = $this->session->userdata('user_id');
		$cond = array('type' => 'agent');
		if ($type == 'agent') $cond['Id'] = $userId;
		else if ($type == 'staff') $cond['staff_id'] = $userId;
		$agentlits = $this->User_model->getDatas($cond);				



		$resData = array();
		$bets = array();
		$reqs = $this->DeleteRequest_model->getDatas(array('status' => 0));

		foreach ($reqs as $req) {
			$bet = $this->Bet_model->getRow(array('Id' => $req->bet_id));
			if ($bet == null) continue;
			if ($week > 0 && $bet->week != $week) continue;
			if($this->find_data($agentlits,$bet->agent_id)==null) continue;
			$bets[] = $bet;
		}

		$no = 1;
		foreach ($bets as $bet) {
		    
			$under = $this->Under_model->getRow(array('under' => $bet->under));
			if ($under == null) continue;

			$row = array();
			$row[] = $no;
			$row[] = $bet->bet_id;
			if ($bet->user_id > 0) {
				$user = $this->User_model->getRow(array('Id' => $bet->user_id));
				if ($user == null) $row[] = '';
				else $row[] = $user->user_id;
			} else $row[] = '';
			$row[] = 'U'.$under->under;
			$row[] = $bet->under;
			$row[] = $bet->week;
			$row[] = $bet->stake_amount;

			$gamelist = "";
			if ($bet->type == 'Group') {
				$groups = json_decode($bet->gamelist, true);
				for ($iGrp = 0; $iGrp < count($groups); $iGrp++) {
					$line = "<p>" . chr(0x41 + $iGrp) . '(' . $groups[$iGrp]['under'][0] . '):' . implode(',', $groups[$iGrp]['list']) . '</p>';
					$gamelist .= $line;
				}
			} else
				$gamelist = implode(',', json_decode($bet->gamelist));
			$row[] = $gamelist;


			$terminalNo = "";
			if ($bet->terminal_id > 0) {
				$terminal = $this->Terminal_model->getRow(array('Id' => $bet->terminal_id));
				if ($terminal != null) $terminalNo = $terminal->terminal_no;
			}
			$row[] = str_replace($terminalNo, "", $bet->ticket_no);
			$row[] = $terminalNo;

			$agent = $this->User_model->getRow(array('Id' => $bet->agent_id));
			if ($agent != null) $row[] = $agent->user_id;
			else $row[] = '';

			$row[] = $bet->bet_time;
			$row[] = '<a href="javascript:void(0)" class="on-default remove-row" ' .
				'onclick="onApprove(' . $bet->Id . ')" title="Cancel" ><i class="fa fa-check-circle text-info" style="font-size: large;"></i></a>';

			$row[] = '<a href="javascript:void(0)" class="on-default remove-row" ' .
				'onclick="onDissmiss(' . $bet->Id . ')" title="Cancel" ><i class="fa fa-minus-circle text-danger" style="font-size: large;"></i></a>';
			$resData[] = $row;
			$no++;
		}

		$output = array(
			"draw" => null,
			"recordsTotal" => count($resData),
			"recordsFiltered" => count($resData),
			"data" => $resData,
		);
		echo json_encode($output);
	}

	public function get_void_bets_list($week = 0, $repeat = 0, $ticketNo = 'null', $amt = 0, $betId = 'null', $fromDt = 'null', $toDt = 'null', $betStatus = 0, $agentId = 0, $terminalId = 0, $under = 0)
	{
		$this->logonCheck();
		$no = $this->input->post("start");
		$length = $this->input->post("length");

		$type = $this->session->userdata('type');
		$userId = $this->session->userdata('user_id');
		$cond = array('type' => 'agent');
		if ($type == 'agent') $cond['Id'] = $userId;
		else if ($type == 'staff') $cond['staff_id'] = $userId;
		$agentlits = $this->User_model->getDatas($cond);


		$condition = array();
		if ($week > 0) $condition['week'] = $week;
		if ($amt > 0) $condition['stake_amount'] = $amt;
		//if($betStatus!='')
		if ($agentId > 0) $condition['agent_id'] = $agentId;
		if ($terminalId > 0) $condition['terminal_id'] = $terminalId;
		if ($under > 0) $condition['under'] = $under;
		if ($betStatus != 'Any') $condition['win_result'] = $betStatus;

		$resData = array();
		$bets = $this->Bet_model->getDatas($condition);

		//filter by time
		if ($fromDt != 'null' || $toDt != 'null') {
			$betts = array();
			foreach ($bets as $bet) {
				if ($fromDt != 'null' &&  $bet->bet_time < $fromDt) {
					continue;
				}
				if ($toDt != 'null' &&  $bet->bet_time > $toDt) {
					continue;
				}
				$betts[] = $bet;
			}
			$bets = $betts;
		}

		//calc repeat 
		// for ($i = 0; $i < count($bets); $i++) $bets[$i]->repeat = 1;
		// for ($i = 0; $i < count($bets); $i++) {
		// 	for ($j = 0; $j < count($bets); $j++) {
		// 		if ($i == $j) continue;
		// 		if ($bets[$i]->type != $bets[$j]->type) continue;
		// 		if ($bets[$i]->option_id != $bets[$j]->option_id) continue;
		// 		if ($bets[$i]->gamelist != $bets[$j]->gamelist) continue;
		// 		$bets[$i]->repeat++;
		// 	}
		// }

		$total = 0;

		$resData = array();
		foreach ($bets as $bet) {
			if ($this->find_data($agentlits, $bet->agent_id) == null) continue;			
			if ($bet->status != 2) continue; //if not void
			if ($repeat > 0 && $bet->repeats < $repeat) continue;
			if($betId != 'null' && strstr($bet->bet_id, $betId)==null) continue;
			if ($ticketNo != 'null' && strstr($bet->ticket_no, $ticketNo)==null)  continue;

			//if($betAbove > 0 && $bet->stake_amount < $betAbove)continue;

			$total++;
			if ($total < $no || $total > ($no + $length)) continue;

			$under = $this->Under_model->getRow(array('under' => $bet->under));
			if ($under == null) continue;

			$row = array();
			$row[] = $bet->week;
			$row[] = $bet->bet_id;
			if ($bet->user_id > 0) {
				$user = $this->User_model->getRow(array('Id' => $bet->user_id));
				if ($user == null) $row[] = '';
				else $row[] = $user->user_id;
			} else $row[] = '';
			$row[] = 'U'.$under->under;
			$row[] = $bet->under;

			$gamelist = "";

			if ($bet->type == 'Group') {
				$groups = json_decode($bet->gamelist, true);
				for ($iGrp = 0; $iGrp < count($groups); $iGrp++) {
					$line = "<p>" . chr(0x41 + $iGrp) . '(' . $groups[$iGrp]['under'] . '):' . $this->shortString($groups[$iGrp]['list']) . '</p>';
					$gamelist .= $line;
				}
			} else
				$gamelist = $this->shortString(json_decode($bet->gamelist));


			$row[] = $gamelist;

			$row[] = number_format($bet->apl,2);
			$row[] = number_format($bet->stake_amount);

			if ($bet->status == 1)
				$row[] = '<label class="label label-success">Active</label>';
			else $row[] = '<label class="label label-danger">Void</label>';

			$terminalNo = "";
			if ($bet->terminal_id > 0) {
				$terminal = $this->Terminal_model->getRow(array('Id' => $bet->terminal_id));
				if ($terminal != null) $terminalNo = $terminal->terminal_no;

				$row[] = str_replace($terminalNo, "", $bet->ticket_no);
			}
			else if($user!=null)
			{
				$row[] = str_replace($user->user_id, "", $bet->ticket_no);
			}

			$row[] = $terminalNo;

			$agent = $this->User_model->getRow(array('Id' => $bet->agent_id));
			if ($agent != null) $row[] = $agent->user_id;
			else $row[] = '';

			$row[] = $bet->bet_time;
			$strAction = '';
			if ($type == 'admin' || $type == 'agent')
				$strAction = '<a href="javascript:void(0)" class="on-default remove-row" ' .
					'onclick="unVoid(' . $bet->Id . ')" title="UnVoid" ><i class="fa fa-undo text-info" style="font-size: large;"></i></a>';
			$row[] = $strAction;
			$resData[] = $row;
		}

		$output = array(
			"draw" => null,
			"recordsTotal" => $total,
			"recordsFiltered" => $total,
			"data" => $resData,
		);
		echo json_encode($output);
	}

	public function addTerminalCredit()
	{
		$this->logonCheck();
		$type = $this->session->userdata('type');
		if ($type != 'admin')
			return $this->reply(400, "permission error", null);

		$amount = $this->input->post('amount');
		$terminals = $this->Terminal_model->getDatas(array('status' => 1));
		foreach ($terminals as $terminal) {
			$this->Terminal_model->updateData(array('Id' => $terminal->Id), array('credit_limit' => $terminal->credit_limit + $amount));
		}
		$this->reply(200, "ok", null);
	}

	public function removeTerminalCredit()
	{
		$this->logonCheck();
		$type = $this->session->userdata('type');
		if ($type != 'admin')
			return $this->reply(400, "permission error", null);

		$amount = $this->input->post('amount');
		$terminals = $this->Terminal_model->getDatas(array('status' => 1));
		foreach ($terminals as $terminal) {
			$this->Terminal_model->updateData(array('Id' => $terminal->Id), array('credit_limit' => 0));
		}
		$this->reply(200, "ok", null);
	}

	public function clearDB()
	{
		$this->logonCheck();
		$type = $this->session->userdata('type');
		if ($type != 'admin')
			return $this->reply(400, "permission error", null);

		$token = $this->input->post('token');
		if ($token != "$#@!")
			return $this->reply(400, "invalid token", null);

		$week = $this->input->post('week');
		//first remove all delete requests
		$reqs = $this->DeleteRequest_model->getDatas(null);

		foreach ($reqs as $req) {
			$bet = $this->Bet_model->getRow(array('Id' => $req->bet_id));
			if ($bet == null) continue;
			if ($bet->week != $week) continue;
			$this->DeleteRequest_model->deleteRow(array('Id' => $req->Id));
		}

		//delete all bets in week.
		$this->Bet_model->deleteByField('week', $week);
		//delete all summary in week.
		$this->Summary_model->deleteByField('week_no', $week);

		//delete all games in week
		$this->Game_model->deleteByField('week_no', $week);

		//delete all prize in week
		$this->Prize_model->deleteByField('week_no', $week);

		//delete from week model
		$this->Week_model->deleteByField('week_no', $week);

		$this->reply(200, "ok", null);
	}
}
