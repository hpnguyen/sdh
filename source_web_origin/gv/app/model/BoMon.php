<?php
/**
 * 
 */
class BoMonModel extends BaseTable {
	
	function __construct() {
		parent::init("bo_mon");
	}
	
	function __destruct() {
		parent::__destruct();
	}
	
	public function getListBomonKlgd($makhoa, $mabomon)
	{
		$sqlstr="SELECT b.ma_bo_mon, b.ten_bo_mon, k.ten_khoa ,
		CASE WHEN b.ma_bo_mon = ".$mabomon." THEN 'selected' ELSE '' END as selected
		FROM bo_mon b, khoa k
		WHERE b.ma_khoa = ".$makhoa."
		and b.ma_khoa = k.ma_khoa 
		ORDER BY viet0dau_name(b.ten_bo_mon) asc";
		
		//echo $sqlstr;
		$check = $this->getQuery($sqlstr)->execute(false, array());
		
		$ret = array();
		
		if($check->itemsCount > 0){
			$ret = $check->result;
		}
		return $ret;
	}
}
