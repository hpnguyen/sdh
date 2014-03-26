<?php
/**
 * 
 */
class KhoaModel extends BaseTable {
	
	function __construct() {
		parent::init("khoa");
	}
	
	function __destruct() {
		parent::__destruct();
	}
	
	public function getTest()
	{
		$check = $this->getAll();
		var_dump($check);
	}
	
	public function getListKhoaKlgd($makhoa)
	{
		$sqlstr="SELECT k.ma_khoa_truong, k.ten_khoa, 
		CASE 
			WHEN k.ma_khoa = '".$makhoa."' 
			THEN 'selected' ELSE '' 
		END as selected 
		FROM khoa k 
		WHERE UPPER(k.ten_khoa) in (
			SELECT distinct ten_khoa 
			FROM view_klgd 
			WHERE view_klgd.cbgd not like '%TH - TN Bộ môn%'
		) 
		ORDER BY viet0dau_name(k.ten_khoa) asc";
		
		//echo $sqlstr;
		$check = $this->getQuery($sqlstr)->execute(false, array());
		
		$ret = array();
		
		if($check->itemsCount > 0){
			$ret = $check->result;
		}
		return $ret;
	}
}
