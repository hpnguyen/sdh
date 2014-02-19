<?php
/**
 * 
 */
class DualModel extends BaseTable {
	
	function __construct() {
		parent::init("dual");
	}
	
	function __destruct() {
		parent::__destruct();
	}
	
	public function checkTableColumnExist($table, $column)
	{
		$check = $this->getSelect("1")
		->where(" exists (select 1 from user_tab_columns where table_name = '".strtoupper($table)."' and column_name = '".strtoupper($column)."')")
		->execute(false, array());
		
		return $check->itemsCount > 0;
	}
}
