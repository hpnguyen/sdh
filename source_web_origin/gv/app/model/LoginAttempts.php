<?php
/**
 * 
 */
class LoginAttemptsModel extends BaseTable {
	
	function __construct() {
		parent::init("login_attempts");
	}	
	function __destruct() {
		parent::__destruct();
	}
	
	public function migrateUp()
	{
		/*
		CREATE TABLE login_attempts (
    		user_id number NOT NULL,
    		time VARCHAR(30) NOT NULL
		);
		 */
		if (! $this->checkTableExist()){
			$primaryKeys = array();
			$fieldsData = array(
				'user_id' => array('NUMBER', 'NOT NULL'),
				'time'=> array('VARCHAR(30)','NOT NULL'),
				'created_at' => array('timestamp', 'DEFAULT CURRENT_TIMESTAMP'),
				'updated_at' => array('timestamp', 'DEFAULT CURRENT_TIMESTAMP')
			);
			echo $this->sql;
			$this->getCreate($primaryKeys,$fieldsData)->execute(true, array());
		}
			
	}
	
	public function migrateDown()
	{
		if ($this->checkTableExist()){
			$this->getDrop()->execute(true, array());
		} 
	}
	
	public function addNew($user_id)
	{
		// Insert the new user into the database
		date_default_timezone_set('Asia/Ho_Chi_Minh');
		$now = time();
		$data = array( 'user_id' => $user_id, 'time' => ''.$now);
		$this->getInsert($data)->execute(true, array());
	}
	
	public function checkbrute($user_id) {
		// Get timestamp of current time
		date_default_timezone_set('Asia/Ho_Chi_Minh');
		$now = time();

		// All login attempts are counted from the past 2 hours.
		$valid_attempts = $now - (2 * 60 * 60);
		
		$sql = "SELECT time FROM login_attempts WHERE user_id = ".$user_id." AND time > '".$valid_attempts."'";
		
		$check = $this->getQuery($sql)->execute(false, array());
		
		// If there have been more than 5 failed logins
		if ($check->itemsCount > 5) {
			return true;
		} else {
			return false;
		}
	}
}
