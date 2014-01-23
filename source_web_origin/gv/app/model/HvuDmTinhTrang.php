<?php
/**
 * 
 */
class HvuDmTinhTrangModel extends BaseTable {
	
	function __construct() {
		parent::init("hvu_dm_tinh_trang");
	}
	
	function __destruct() {
		parent::__destruct();
	}
	
	public function getDanhMucTinhTrang()
	{
		$sqlstr="SELECT ma_tinh_trang, ten_tat	FROM hvu_dm_tinh_trang";
		
		//$check = $this->getQuery($sqlstr)
		$check = $this->getSelect("ma_tinh_trang, ten_tat")
		->execute(false, array());
		
		$ret = array();
		
		if($check->itemsCount > 0){
			$ret = $check->result;
		}
		return $ret;
	}
}