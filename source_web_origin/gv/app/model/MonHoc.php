<?php
/**
 * 
 */
class MonHocModel extends BaseTable {
	
	function __construct() {
		parent::init("mon_hoc");
	}
	
	function __destruct() {
		parent::__destruct();
	}
	
	public function getByMaMH($mamh)
	{
		$ret = null;
		$check = $this->getSelect("*")
		->where("ma_mh = '".$mamh."'")
		->execute(false, array());
		
		if($check->itemsCount > 0){
			$ret = $check->result[0];
		}
		
		return $ret;
	}
}
