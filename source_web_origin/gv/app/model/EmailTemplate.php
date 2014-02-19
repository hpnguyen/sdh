<?php
/** 
* Table email_template model class
*
* @param  id varchar2(100) not null primary key
* @param  title varchar2(200)
* @param  content long varchar
* @param  created_at timestamp default current_timestamp
* @param  updated_at timestamp default current_timestamp
*/
class EmailTemplateModel extends BaseTable {
	
	function __construct() {
		parent::init("email_template");
	}
	
	function __destruct() {
		parent::__destruct();
	}
	
	public function migrateUp()
	{
		$primaryKeys = array('id');
		$fieldsData = array(
			'id' => array('varchar2(100)', null),
			'title'=> array('varchar2(200)',null),
			'content' => array('long varchar', null),
			'created_at' => array('timestamp', 'DEFAULT CURRENT_TIMESTAMP'),
			'updated_at' => array('timestamp', 'DEFAULT CURRENT_TIMESTAMP')
		);
		
		return $this->getCreate($primaryKeys,$fieldsData)->execute(true, array());
	}
	
	public function migrateDown()
	{
		return $this->getDrop()->execute(true, array());
	}
	
	public function checkTemplateThongBaoTkb($data)
	{
		if (is_array($data) && isset($data['id'])){
			$check = $this->getSelect('*')
			->where("id = '".$data['id']."'")
			->execute(false, array());
			
			if ($check->itemsCount < 1){
				$this->getInsert($data)->execute(true, array());
			}else{
				if(! isset($data['updated_at'])){
					$data['updated_at'] = 'CURRENT_TIMESTAMP';
				}
				$this->getUpdate($data)->where("id = '".$data['id']."'")->execute(true, array());
			}
			
			return true;
		}else{
			return false;
		}
	}
	
	public function deleteTemplate($id)
	{
		$this->getDelete("id = '".$id."'")->execute(true, array());
	}
	
	public function getMailTemplate($id)
	{
		$check = $this->getSelect('*')
		->where("id = '".$id."'")
		->execute(false, array());
		
		if($check->itemsCount > 0){
			return $check->result[0];
		}else{
			return null;
		}
	}
}
