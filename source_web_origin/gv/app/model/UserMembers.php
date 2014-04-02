<?php
/**
 * 
 */
class UserMembersModel extends BaseTable {
	
	function __construct() {
		parent::init("user_members");
	}
	
	function __destruct() {
		parent::__destruct();
	}
	
	public function migrateUp()
	{
		/*
		CREATE TABLE user_members (
    		id number NOT NULL PRIMARY KEY,
    		username VARCHAR(30) NOT NULL,
    		email VARCHAR(50) NOT NULL,
    		password CHAR(128) NOT NULL,
    		salt CHAR(128) NOT NULL 
		);
		 */
		if (! $this->checkTableExist()){
			$primaryKeys = array('id');
			$fieldsData = array(
				'id' => array('NUMBER', null),
				'username'=> array('VARCHAR(30)','NOT NULL'),
				'email' => array('VARCHAR(50)','NOT NULL'),
				'password' => array('CHAR(128)','NOT NULL'),
				'salt' => array('CHAR(128)','NOT NULL'),
				'created_at' => array('timestamp', 'DEFAULT CURRENT_TIMESTAMP'),
				'updated_at' => array('timestamp', 'DEFAULT CURRENT_TIMESTAMP')
			);
			$this->getCreate($primaryKeys,$fieldsData)->execute(true, array());
		}
			
	}
	
	public function migrateDown()
	{
		if ($this->checkTableExist()){
			$this->getDrop()->execute(true, array());
		} 
	}
	public function doCreateUpdate($data)
	{
		if (is_array($data)){
			if (! isset($data['id'])) {
				//Insert new record
				$check = $this->getSelect('*')->execute(false, array());
				$data['id'] = $check->itemsCount > 0 ? $check->itemsCount + 1 : 1;
				
				$this->getInsert($data)->execute(true, array());
			}else{
				//Update record
				if(! isset($data['updated_at'])){
					$data['updated_at'] = 'CURRENT_TIMESTAMP';
				}
				
				$this->getUpdate($data)->where("id = ".$data['id'])->execute(true, array());
			}
			
			return $data['id'];
		}else{
			return null;
		}
	}
	
	public function checkUserExist($username)
	{
		$ret = null;
		if (! empty($username)){
			$check = $this->getSelect("*")->where("UPPER(username) = UPPER('".$username."')")->execute(false, array());
			if($check->itemsCount > 0){
				$row = $check->result[0];
				$ret = $row['id'];
			}
		}
		
		return $ret;
	}
	
	public function addNew($username, $email, $password,$random_salt = null)
	{
		//Insert new record
		if ($random_salt == null){
			$randomText = '';
			if (function_exists ('openssl_random_pseudo_bytes')){
				$randomText = openssl_random_pseudo_bytes(16);
			}
			$random_salt = hash('sha512', uniqid($randomText, TRUE));
		}
		 
		// Create salted password
		$password = hash('sha512', $password . $random_salt);
		
		// Insert the new user into the database
		$data = array(
			'username' => $username,
			'email' => $email,
			'password' => $password,
			'salt' => $random_salt
		);
		//check user
		$id = $this->checkUserExist($username);
		
		if($id != null){
			$data['id'] = $id;
		}
		
		return $this -> doCreateUpdate($data);
	}

	public function readByID($id) {
		$check = $this->getSelect("TO_CHAR(created_at ,'DD-MM-YYYY HH24:MI:SS') t_created_at,TO_CHAR(updated_at ,'DD-MM-YYYY HH24:MI:SS') t_updated_at, user_members.*")
		->where("id = ".$id)
		->execute(false, array());
		
		if($check->itemsCount > 0){
			return $check->result[0];
		}else{
			return null;
		}
	}
	
	public function readByEmail($email) {
		$check = $this->getSelect("TO_CHAR(created_at ,'DD-MM-YYYY HH24:MI:SS') t_created_at,TO_CHAR(updated_at ,'DD-MM-YYYY HH24:MI:SS') t_updated_at, user_members.*")
		->where("email = '".$email."'")
		->execute(false, array());
		
		if($check->itemsCount > 0){
			return $check->result[0];
		}else{
			return null;
		}
	}
	
	public function readByUsername($username, $upperAll = true) {
		if ($upperAll == true){
			$whereCondition  = "UPPER(username) = UPPER('".$username."')";
		}else{
			$whereCondition  = "username = '".$username."'";
		}
		$check = $this->getSelect("TO_CHAR(created_at ,'DD-MM-YYYY HH24:MI:SS') t_created_at,TO_CHAR(updated_at ,'DD-MM-YYYY HH24:MI:SS') t_updated_at, user_members.*")
		->where($whereCondition)
		->execute(false, array());
		
		if($check->itemsCount > 0){
			return $check->result[0];
		}else{
			return null;
		}
	}
	
	public function deleteByID($id) {
		$this->getDelete("id = ".$id)->execute(true, array());
	}
	
	public function listAll()
	{
		$check = $this->getSelect("TO_CHAR(created_at ,'DD-MM-YYYY HH24:MI:SS') t_created_at,TO_CHAR(updated_at ,'DD-MM-YYYY HH24:MI:SS') t_updated_at, user_members.*")
		->execute(false, array());
		
		return $check->result;
	}
}
