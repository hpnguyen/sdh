<?php
/**
 * 
 */
class NckhChuyenGiaTmdtModel extends BaseTable {
		
	function __construct() {
		parent::init("nckh_chuyen_gia_tmdt","_khcn");
	}
	
	function __destruct() {
		parent::__destruct();
	}
	
	public function getList()
	{
		$check = $this->getSelect('*')
		->where("fk_ma_can_bo = '0.1838'")
		->execute(false, array());
		
		if ($check->itemsCount > 0){
			return $check->result;
		}else{
			return null;
		}
	}
}
