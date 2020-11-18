<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Bet_model extends CI_Model {

	public function __construct()
	{
		parent::__construct();
		$this->tblName = 'tbl_bets';
		$this->load->model('Option_model');		
	}

	public function getDatas($conAry, $orderBy='', $sort='ASC') 
	{
		$this->db->from($this->tblName);

		if(!empty($conAry))
			$this->db->where( $conAry );
		if($orderBy !='') {
			$this->db->order_by($orderBy, $sort);
		}    	
		$ret = $this->db->get()->result();
		return $ret;
	}

	public function getDatasWithSearch($conAry, $searchAry=null, $searchKey='', $orderBy='', $sort='ASC') 
	{
		$this->db->from($this->tblName);
		
		if(!empty($conAry))
			$this->db->where( $conAry );
		if($orderBy !='') {
			$this->db->order_by($orderBy, $sort);
		}

		$i = 0;
		if($searchKey !='' && count($searchAry) >0)
		{
			$this->db->group_start();
			foreach ($searchAry as $item) // loop column
			{
				if($i===0) // first loop
					$this->db->like($item, $searchKey);
				else
					$this->db->or_like($item, $searchKey);
				$i++;
			}
			$this->db->group_end(); //close bracket
		}

		$ret = $this->db->get()->result();
		return $ret;
	}

	public function updateBatch($updateArry)
	{
		$this->db->update_batch($this->tblName,$updateArry, 'Id');
	}
	
	public function updateData($conAry, $updateAry) 
	{
		if(!empty($updateAry)) {
			$this->db->update($this->tblName, $updateAry, $conAry);
		}
		return $this->db->affected_rows();
	}

	public function deleteRow( $conArry ) 
	{
		if(!empty($conArry)) {
			$this->db->where($conArry);
			$this->db->delete($this->tblName);
		}
	}	

	public function deleteByField($field, $value ) {
		$this->db->where($field, $value);
        $this->db->delete($this->tblName);
	}

	public function getCounts($conAry) {
    	$this->db->from($this->tblName);
		if(!empty($conAry))
			$this->db->where( $conAry );
		return $this->db->count_all_results();
    }

    public function insertData($data)
    {
        $this->db->insert($this->tblName, $data);
        return $this->db->insert_id();
    }

	public function getRow($conAry) 
	{
    	$this->db->from($this->tblName);
    	$this->db->where($conAry);
        $query = $this->db->get();
        return $query->row();
    }

    public function setField($field, $value, $conAry, $valueString=FALSE) {
    	$this->db->from($this->tblName);
		$this->db->set($field, $value, $valueString);
		$this->db->where($conAry);
		$this->db->update();
    }
    public function getDataById($Id)
    {
        $this->db->from($this->tblName);
        $this->db->where('Id',$Id);
        $query = $this->db->get();
        return $query->row();
	}

	public function addNewBet($terminalId,$newBet)
	{
		$data = array(
			'bet_id'=>$newBet['bet_id'],
			'bet_time'=> $newBet['bet_time'],
			'ticket_no'=>$newBet['ticket_no'],
			'terminal_id'=>$terminalId,
			'agent_id'=>$newBet['agent_id'],
			'stake_amount'=>$newBet['stake_amount'],
			'gamelist'=>json_encode($newBet['gamelist']),
			'week'=>$newBet['week'],
			'under'=>$newBet['under'],
			'type'=>$newBet['type'],
			'apl'=>$newBet['apl']
		);
		$this->insertData($data);
	}

	private function getOption($options, $id)
	{
		foreach($options as $opt)
		{
			if($id == $opt->Id)
				return $opt;
		}
		return null;
	}
	public function getBets($cond)
	{
		$datas = $this->getDatas($cond);
		$result = array();
		foreach($datas as $data)
		{			
			$newBet = get_object_vars($data);
			$row = array(
				'Id'=>$newBet['Id'],
				'bet_id'=>$newBet['bet_id'],
				'bet_time'=> $newBet['bet_time'],
				'ticket_no'=>$newBet['ticket_no'],
				'terminal_id'=>$newBet['terminal_id'],
				'user_id'=>$newBet['user_id'],
				'agent_id'=>$newBet['agent_id'],
				'stake_amount'=>$newBet['stake_amount'],
				'gamelist'=> json_decode($newBet['gamelist'], true),
				'week'=>$newBet['week'],
				'under'=>$newBet['under'],
				'type'=>$newBet['type'],
				'apl'=>$newBet['apl'],
				'status'=>$data->status,
				'win_result'=>$newBet['win_result'],
				'won_amount'=>$newBet['won_amount'],
				'repeats'=>$newBet['repeats']
			);
			$result[]= $row;
		}
		return $result;
	}


	
}


