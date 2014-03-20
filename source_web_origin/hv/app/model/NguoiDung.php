<?php
/**
 * 
 */
class NguoiDungModel extends BaseTable {
	
	function __construct() {
		parent::init("nguoi_dung");
	}
	
	function __destruct() {
		parent::__destruct();
	}
	
	public function getDataOnLogin($username)
	{
		$check = $this->getSelect("username, first_login, last_login")
		->where("username='".$username."'")
		->execute(false, array());
		if($check->itemsCount > 0){
			return $check->result[0];
		}else{
			return null;
		}
	}
	
	public function updateLoginTime($username)
	{
		$this->getUpdate(array('last_login' => 'SYSDATE'))
		->where("UPPER(username)=UPPER('".$username."')")
		->execute(true, array());
	}
}
