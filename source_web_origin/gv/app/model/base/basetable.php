<?php
if (!class_exists('DbFactory', false)) {
	require_once(ROOT_DIR.'app/model/base/dbfactory.php');
}

/**
 * 
 */
class BaseTable extends DbFactory {
	public $tableName;
	public $dbConnection;
	
	public function __construct() {
		
	}
	
	function __destruct() {
		DbFactory::getInstance()->close();
	}
	
	public function init($tableName) {
		$this->tableName = $tableName;
		$this->dbConnection = DbFactory::getInstance();
	}
	
	public function getQuery($string) {
		return $this->dbConnection->query($string);
	}
	
	public function getSelect($string) {
		return $this->dbConnection->select($this->tableName, $string);
	}
	
	public function getInsert($data) {
		return $this->dbConnection->insert($this->tableName,$data);
	}
	
	public function getUpdate($data) {
		return $this->dbConnection->update($this->tableName,$data);
	}
	
	public function getDelete($whereString = null) {
		if ($whereString == null) {
			return $this->dbConnection->delete($this->tableName);
		}else{
			return $this->dbConnection->delete($this->tableName)->where($whereString);
		}
	}
	
	public function getDrop() {
		return $this->dbConnection->drop($this->tableName);
	}
	
	public function getCreate($primaryFieldArray, $fieldArray) {
		/*//Example create database
		 	$model = new EmailTemplateModel();
			$model->getCreate(
				array('id','id2'),
				array(
					'id' => array('varchar2(100)', null),
					'id2' => array('varchar2(100)', null),
					'title'=> array('varchar2(200)',null),
					'content' => array('long varchar', null),
					'created_at' => array('timestamp', 'DEFAULT CURRENT_TIMESTAMP'),
					'updated_at' => array('timestamp', 'DEFAULT CURRENT_TIMESTAMP')
				)
			);
		 */
		return $this->dbConnection->create($this->tableName,$primaryFieldArray, $fieldArray);
	}
	
	public function getAll() {
		return $this->dbConnection->select($this->tableName,'*')->execute()->parse()->fetchAll();
	}
	
	public function getFirst() {
		return $this->getSelect('*')->execute()->parse()->fetchFirst();
	}
	
	public function getLast() {
		return $this->getSelect('*')->execute()->parse()->fetchLast();
	}
	
	public function getRow($index) {
		return $this->getSelect('*')->execute()->parse()->fetchRow($index);
	}
	
	public function checkTableExist() {
		$sql = "SELECT count(*) counter FROM dba_tables where table_name = '".strtoupper($this->tableName)."'";
		$ret = $this->getQuery($sql)->execute()->parse()->fetchAll();
		
		return (int) $ret[0]['counter'] > 0;
	}
	
	public function getTableColumns()
	{
		
	}
	
	public function checkTableColumnExist($column) {
		$model = new DualModel();
		$check = $model->checkTableColumnExist($this->tableName, $column);
		unset($model);
		return $check;
	}
}
