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
}
