<?php
defined('BASEPATH') OR exit('No direct script access allowed');
class Admin_model extends CI_Model {

	var $order = array('id' => 'asc'); // default order 
	public function __construct()
	{
		 parent::__construct();
		 $this->tblName = "tbl_admin";
	}

	public function doChangePwd($Id, $userPWD, $newPWD)
	{
		global $MYSQL;
		$strsql = sprintf("select * from %s where Id='$Id' ", $MYSQL['_adminDB']);
		$ret = $this->db->query($strsql)->row();
		if($ret){
			$salt = $ret->userPWDKey;
			$genPWD = crypt($userPWD, $salt);
			if($genPWD == $ret->userPWD){
				$salt = md5(date("YmdHis"));
				$genPWD = crypt($newPWD, $salt);
				$strsql = sprintf("UPDATE %s SET userPWD='$genPWD', userPWDKey='$salt' WHERE Id= '$Id' ", 
					$MYSQL['_adminDB']);
				$this->db->query($strsql);
				return TRUE;
			}
		}
		return FALSE;
	}
	private function _get_datatables_query($conAry, $srchAry, $orderAry, $kind='', $select='') {
		global $MYSQL;
		if($select !='') {
			$this->db->select($select);
		}
        $this->db->from($this->tblName);
		if($kind =='report') {
			$this->db->join($MYSQL['_jobDB'].' b', 'a.job_id = b.Id', 'left');
			$this->db->join($MYSQL['_userDB'].' c', 'a.user_id = c.Id', 'left');
		} else if($kind =='user') {
			$this->db->join($MYSQL['_companyDB'].' b', 'a.company_id = b.Id', 'left');
		} else if($kind =='job') {
			$this->db->join($MYSQL['_subjectDB'].' b', 'a.subject_id = b.Id', 'left');
		}
		if(!empty($conAry))
			$this->db->where( $conAry );
        $i = 0;
        foreach ($srchAry as $item) // loop column
        {
            if($_POST['search']['value']) // if datatable send POST for search
            {
                if($i===0) // first loop
                {
                    $this->db->group_start(); // open bracket. query Where with OR clause better with bracket. because maybe can combine with other WHERE with AND.
                    $this->db->like($item, $_POST['search']['value']);
                }
                else
                {
                    $this->db->or_like($item, $_POST['search']['value']);
                }
 
                if(count($srchAry) - 1 == $i) //last loop
                    $this->db->group_end(); //close bracket
            }
            $i++;
        }
         
        if(isset($_POST['order'])) // here order processing
        {
            $this->db->order_by($orderAry[$_POST['order']['0']['column']], $_POST['order']['0']['dir']);
        } 
        else if(isset($this->order))
        {
            $order = $this->order;
            $this->db->order_by(key($order), $order[key($order)]);
        }
    }

    public function count_filtered($conAry, $srchAry, $orderAry, $kind='', $select='') {
        $this->_get_datatables_query($conAry, $srchAry, $orderAry, $kind, $select);
        $query = $this->db->get();
        return $query->num_rows();
    }

	public function getDatas( $conAry, $orderBy='' ) {
		$this->db->from($this->tblName);
		if(!empty($conAry))
			$this->db->where( $conAry );
		if($orderBy !='') {
			$this->db->order_by($orderBy, 'ASC');
		}
		$ret = $this->db->get()->result();
		return $ret;
	}
	public function updateDataAry($field, $conAry, $updateAry) {
		if(!empty($updateAry)) {
			$this->db->where_in($field, $conAry);
			$this->db->update($this->tblName, $updateAry);
		}
		return $this->db->affected_rows();
	}

	public function updateData($conAry, $updateAry) {
		if(!empty($updateAry)) {
			$this->db->update($this->tblName, $updateAry, $conAry);
		}
		return $this->db->affected_rows();
	}
	public function updateOnOff($Id, $field)
    {
		global $MYSQL;
		$this->db->from($this->tblName);
		$this->db->set($field, '1-'.$field, FALSE);
		$this->db->where('Id', $Id);
		$this->db->update();
    }
	public function deleteByField($field, $value ) {
		$this->db->where($field, $value);
        $this->db->delete($this->tblName);
	}

	public function deleteRows( $field, $delAry ) {
		if(!empty($delAry)) {
			$this->db->where_in($field, $delAry);
			$this->db->delete($this->tblName);
		}
		return $this->db->affected_rows();
	}

	public function getTableDatas($conAry, $srchAry, $orderAry, $kind='', $select='') {
		$this->_get_datatables_query($conAry, $srchAry, $orderAry, $kind, $select);
        if($_POST['length'] != -1)
        	$this->db->limit($_POST['length'], $_POST['start']);
        $query = $this->db->get();
        return $query->result();
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
    public function getDataById($Id)
    {
        $this->db->from($this->tblName);
        $this->db->where('Id',$Id);
        $query = $this->db->get();
        return $query->row();
    }
    public function appInstall($phone) {
    	global $MYSQL;
    	$ret = $this->getRow(array('phone' => $phone));
    	if($ret) {
    		return TRUE;
    	}
    	$this->db->from($MYSQL['_customeDB']);
		$this->db->set('isInstall', '1', FALSE);
		$this->db->where('phone',$phone);
		$this->db->update();
		if($this->db->affected_rows() > 0)
			return TRUE;
		else 
			return FALSE;
    }
    public function getRow($conAry) {
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
    public function getSumCost($cate_id=''){
    	global $MYSQL;
    	$this->db->select_sum('price');
		$this->db->from($MYSQL['_paymentDB'].' a');
		$this->db->join($MYSQL['_subcateDB'].' b', 'a.subcate_id = b.Id', 'left');
		if($cate_id !='') {			 
			$this->db->where('b.cate_id', $cate_id);
		}
		return $this->db->get()->row();
    }
    public function getUserPaymentHistory($user_id) {
    	global $MYSQL;
    	$this->db->select('a.price, a.created, b.title');
		$this->db->from($MYSQL['_paymentDB'].' a');
		$this->db->join($MYSQL['_subcateDB'].' b', 'a.subcate_id = b.Id', 'left');
		if($user_id !='') {			 
			$this->db->where('a.user_id', $user_id);
		}
		$this->db->order_by('a.created', 'DESC');
		return $this->db->get()->result();
    }

    ////////////////////////////////////////////// API  /////////////////////////////////////////////////
    public function auth_user($email, $password) {
		global $MYSQL;
		$this->db->select('a.*, b.name as company, b.domain');
		$this->db->from($MYSQL['_userDB'].' a');
		$this->db->join($MYSQL['_companyDB'].' b', 'a.company_id = b.Id', 'left');
		$conAry = array('a.email'=>$email, 'a.isdeleted'=>'0');
		$this->db->where($conAry);
		$ret = $this->db->get()->result();
		if(!empty($ret)){
			if (password_verify($password, $ret[0]->password)) {       
				if($ret[0]->isactive == '0') {
					return 'not_active';
				}
				return $ret[0];
			} else {
				return 'not password';
			}
		}
		return FALSE;
	}

    public function auth_userByPass($password) {
		global $MYSQL;
		$this->db->select('a.*, b.name as company, b.domain');
		$this->db->from($MYSQL['_userDB'].' a');
		$this->db->join($MYSQL['_companyDB'].' b', 'a.company_id = b.Id', 'left');
		$conAry = array('a.digits'=>$password, 'a.isdeleted'=>'0');
		$this->db->where($conAry);
		$ret = $this->db->get()->result();
		if(!empty($ret)){
			if($ret[0]->isactive == '0')
				return 'not_active';
			return $ret[0];
		}
		return FALSE;
	}


	public function auth_id($user_id) {
		global $MYSQL;
		$this->db->select('a.*, b.name as company, b.domain');
		$this->db->from($MYSQL['_userDB'].' a');
		$this->db->join($MYSQL['_companyDB'].' b', 'a.company_id = b.Id', 'left');
		$conAry = array('a.Id'=>$user_id, 'a.isdeleted'=>'0');
		$this->db->where($conAry);
		$ret = $this->db->get()->result();
		if(!empty($ret)){
			return $ret[0];
		}
		return FALSE;
	}

	public function getSubCateList($cate_id) {
		global $MYSQL;
    	$this->db->select('a.*, b.name, b.cost');
		$this->db->from($MYSQL['_subcateDB'].' a');
		$this->db->join($MYSQL['_costDB'].' b', 'a.cost_id = b.Id', 'left');
		$this->db->where(array('a.cate_id'=>$cate_id ,'a.isactive'=>'1', 'a.isdeleted'=>'0'));
		$this->db->order_by('a.created', 'DESC');
		return $this->db->get()->result();
	}


	public function getUserIdByAuth($auth)
	{
		$this->db->from($MYSQL['_userDB']);
		$this->db->where('authkey', $auth);
		$ret = $this->db->get()->result();		
		if(!empty($ret)){
			return $ret[0]->Id;
		}
		return 0;
	}

	public function getUserProfile($userId)
	{
		$user = $this->auth_id($userId);
		if($user==FALSE) return null;
		return $user;
	}

	public function authBySteamId($steamId) {
		
		$this->db->from($this->tblName);
		$conAry = array('steam_id'=>$steamId);
		$this->db->where($conAry);
		$ret = $this->db->get()->result();
		if(!empty($ret)){
			return $ret[0];
		}
		return FALSE;
	}

	public function getOnlineUsers()
	{
        $tdate = date("Y-m-d H:i:s", time() - 600);
		$strQuery = "select * from ".$this->tblName." where last_login >'" .$tdate."'";		
        return $this->db->query($strQuery)->result();
	}
	
}


