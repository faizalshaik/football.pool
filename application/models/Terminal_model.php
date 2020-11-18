<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Terminal_model extends CI_Model {

	public function __construct()
	{
		parent::__construct();
		$this->tblName = 'tbl_terminal';
	}

	public function getDatas($conAry, $orderBy='') 
	{
		$this->db->from($this->tblName);

		if(!empty($conAry))
			$this->db->where( $conAry );
		if($orderBy !='') {
			$this->db->order_by($orderBy, 'ASC');
		}    	
		$ret = $this->db->get()->result();
		return $ret;
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
	
}


