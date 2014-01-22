<?php
/**
 * 
 */
class UserTablesModel extends BaseTable {
	
	function __construct() {
		parent::init("user_tables");
	}
	
	function __destruct() {
		parent::__destruct();
	}
	
	public function checkInitialMigration()
	{
		$check = $this->getSelect('*')
		->where("table_name = 'MIGRATION_VERSION'")
		->execute(false, array());
		
		if ($check->itemsCount < 1){
			echo "Create table MIGRATION_VERSION\n";
			$check = $this->getQuery("CREATE TABLE MIGRATION_VERSION (VERSION VARCHAR2(200 CHAR) PRIMARY KEY )")
			->execute(true, array());
		}
	}
	
	public function getVersion()
	{
		$check = $this->getSelect('*')
		->where("table_name = 'MIGRATION_VERSION'")
		->execute(false, array());
		
		if ($check->itemsCount < 1){
			echo "Create table MIGRATION_VERSION\n";
			$check = $this->getQuery("CREATE TABLE MIGRATION_VERSION (VERSION VARCHAR2(200 CHAR) PRIMARY KEY )")
			->execute(true, array());
		}
	}
}
