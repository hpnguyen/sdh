<?php
/**
 * 
 */
class CapDeTaiModel extends BaseTable {
	
	function __construct() {
		parent::init("cap_de_tai");
	}
	
	function __destruct() {
		parent::__destruct();
	}
	
	public function getTest()
	{
		$check = $this->getAll();
		var_dump($check);
	}
	
	public function getThoiHanPhanBienDeTai()
	{
		$check = $this->getSelect("cap_de_tai.*, 
		to_char(pbdt_ngay_bd ,'DD-MM-YYYY') as t_pbdt_ngay_bd, 
		to_char(pbdt_ngay_kt ,'HH24:MI DD-MM-YYYY') as t_pbdt_ngay_kt,
		CASE WHEN SYSDATE >=  pbdt_ngay_bd and SYSDATE <=  pbdt_ngay_kt THEN 0
		ELSE 1
		END as het_han_phan_bien")
		->where("pbdt_ngay_bd is not null and pbdt_ngay_kt is not null")
		->order("ma_cap asc")
		->execute(false, array());
		$ret = array();
		
		if($check->itemsCount > 0){
			$ret = $check->result;
		}
		
		return $ret;
	}
}
