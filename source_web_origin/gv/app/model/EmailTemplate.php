<?php
/**
 * 
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
		$sqlstr = "CREATE TABLE ". strtoupper($this->tableName)." 
		(	ID VARCHAR2(100) NOT NULL PRIMARY KEY,
			TITLE VARCHAR2(200),
			CONTENT LONG VARCHAR,
			CREATED_AT TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
			UPDATED_AT TIMESTAMP DEFAULT CURRENT_TIMESTAMP
		)";
		echo $sqlstr;
		return $this->getQuery($sqlstr)->execute(true, array());
	}
	
	public function migrateDown()
	{
		$sqlstr = "DROP TABLE ". strtoupper($this->tableName);
		echo $sqlstr;
		return $this->getQuery($sqlstr)->execute(true, array());
	}
	
	public function checkTemplateThongBaoTkb($data)
	{
		if (is_array($data) && isset($data['id'])){
			$check = $this->getSelect('*')
			->where("id = '".$data['id']."'")
			->execute(false, array());
			
			if ($check->itemsCount < 1){
				echo "Insert\n";
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
}
