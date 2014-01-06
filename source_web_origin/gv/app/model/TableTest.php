<?php
/**
 * 
 */
class TableTestModel extends BaseTable {
	public $connection;
	
	function __construct() {
		parent::init("table_test");
		$this->connection = DbFactory::getInstance();
	}
	
	function __destruct() {
		parent::__destruct();
	}
	function test() {
		echo $this->tableName;
		//$sql = "select * from ".$this->tableName." where khoa = :khoa and ma_nganh = :ma_nganh and ma_mh = :ma_mh";
		$sql = "select * from ".$this->tableName."";
		//$test = $this->connection->query($sql)->execute(false,array(':khoa' => 2005, ':ma_nganh' => '605275', ':ma_mh' => '060205009'))
		$test = $this->connection->insert($sql)->execute()
		->parse()->fetchAll();
		var_dump($test);
	}
}
