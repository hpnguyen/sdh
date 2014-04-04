<?php
/**
 * 
 */
class UserTabColumns extends BaseTable {
	
	function __construct() {
		parent::init("user_tab_columns");
	}
	
	function __destruct() {
		parent::__destruct();
	}
	
	public function getColumnsCLOB($tableName,$columnName = null){
		$check = $this->getSelect('*')
		->where("table_name = '".strtoupper($tableName)."' 
		and data_type='CLOB'".($columnName != null ? " and column_name = '".strtoupper($columnName)."'" : ""))
		->execute(false, array());
		
		$ret = array();
		if ($check->itemsCount > 0){
			$ret = $check->result;
		}
		return $ret;
	}
}
