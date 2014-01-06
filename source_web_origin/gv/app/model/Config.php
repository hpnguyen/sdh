<?php
/**
 * 
 */
class ConfigModel extends BaseTable {
	
	const KH_BD = 'PC_CBGD_KH_NGAY_BD'; //Date start for khoa member can update
	const KH_KT = 'PC_CBGD_KH_NGAY_KT'; //Date end for khoa member can update
	const BM_BD = 'PC_CBGD_BM_NGAY_BD'; //Date start for bo mon member can update
	const BM_KT = 'PC_CBGD_BM_NGAY_KT'; //Date end for bo mon member can update
	
	function __construct() {
		parent::init("config");
	}
	
	function __destruct() {
		parent::__destruct();
	}
	
	public function getTest()
	{
		$check = $this->getAll();
		var_dump($check);
	}
	
	public function getPhanBoCbgdDotHoc()
	{
		$check = $this->getSelect("to_date(value,'dd-mm-yyyy') pc_cbgd_dot_hoc")
		->where("name = 'PC_CBGD_DOT_HOC'")
		->execute(false, array());
		date_default_timezone_set('Asia/Ho_Chi_Minh');
		$ngayHetHan = date('d-m-Y', strtotime($check->result[0]['pc_cbgd_dot_hoc']));
		return $ngayHetHan;
	}
	
	public function getPhanBoCbgdHetHan()
	{
		$check = $this->getSelect("to_date(value,'dd-mm-yyyy') ". self::KH_KT)
		->where("name = '".self::KH_KT."'")
		->execute(false, array(),false);
		date_default_timezone_set('Asia/Ho_Chi_Minh');
		$ngayHetHan = date('d-m-Y', strtotime($check->result[0][self::KH_KT]));
		return $ngayHetHan;
	}
	
	public function checkPhanBoCbgdHetHan()
	{
		$check = $this->getSelect("to_date(value,'dd-mm-yyyy') ". self::KH_KT)
		->where("name = '".self::KH_KT."'")
		->execute(false, array(),false)
		->parse();
		 
		date_default_timezone_set('Asia/Ho_Chi_Minh');
		$ngayHetHan = strtotime($check->result[0][self::KH_KT]);
		$timeHienTai = strtotime(date('d-m-Y'));
		
		return $timeHienTai <= $ngayHetHan;
	}
	
	public function getPhanBoCbgdHetHanBoMon()
	{
		$check = $this->getSelect("to_date(value,'dd-mm-yyyy') ". self::BM_KT)
		->where("name = '".self::BM_KT."'")
		->execute(false, array(),false);
		date_default_timezone_set('Asia/Ho_Chi_Minh');
		$ngayHetHan = date('d-m-Y', strtotime($check->result[0][self::BM_KT]));
		return $ngayHetHan;
	}
	
	public function checkPhanBoCbgdHetHanBoMon()
	{
		$check = $this->getSelect("to_date(value,'dd-mm-yyyy') ". self::BM_KT)
		->where("name = '".self::BM_KT."'")
		->execute(false, array(),false)
		->parse();
		 
		date_default_timezone_set('Asia/Ho_Chi_Minh');
		$ngayHetHan = strtotime($check->result[0][self::BM_KT]);
		$timeHienTai = strtotime(date('d-m-Y'));
		
		return $timeHienTai <= $ngayHetHan;
	}
}
