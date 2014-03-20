<?php
/**
 * 
 */
class NckhConfigModel extends BaseTable {
	
	function __construct() {
		parent::init("config","_khcn");
	}
	
	function __destruct() {
		parent::__destruct();
	}
	
	public function getTest()
	{
		$check = $this->getAll();
		var_dump($check);
	}
}
