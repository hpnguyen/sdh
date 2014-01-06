<?php
/**
 * 
 */
class NhanSuModel extends BaseTable {
	const PHAN_BO_CAN_BO_ROLE_ID = 112;
	const PHAN_BO_CAN_BO_BO_MON_ROLE_ID = 113;
	const VIEW_ALL_PHAN_BO_CAN_BO_ROLE_ID = 114;
	
	function __construct() {
		parent::init("nhan_su");
	}
	
	function __destruct() {
		parent::__destruct();
	}
	
	public function getPrivilleges($user)
	{
		$sqlstr="SELECT DISTINCT f.fk_ma_chuc_nang CHUC_NANG 
		FROM nhan_su n, ct_nhom_nhan_su ct, ct_nhom_nguoi_dung_portal f 
		WHERE upper(n.username)=upper('".$user."') AND n.id=ct.fk_id_ns AND ct.fk_ma_nhom = f.fk_ma_nhom";
		$check = $this->getQuery($sqlstr)
		->execute(false, array());
		$ret = array();
		if($check->itemsCount > 0){
			$ret = $check->result;
		}
		return $ret;
		
	}
	
	public function checkPhanBoCanBo()
	{
		$user = base64_decode($_SESSION["uidloginPortal"]);
		
		$sqlstr="SELECT DISTINCT f.fk_ma_chuc_nang CHUC_NANG 
		FROM nhan_su n, ct_nhom_nhan_su ct, ct_nhom_nguoi_dung_portal f 
		WHERE upper(n.username)=upper('".$user."') 
		AND n.id=ct.fk_id_ns 
		AND ct.fk_ma_nhom = f.fk_ma_nhom 
		AND f.fk_ma_chuc_nang = ".self::PHAN_BO_CAN_BO_ROLE_ID;
		$check = $this->getQuery($sqlstr)
		->execute(false, array());
		$ret = false;
		
		if($check->itemsCount > 0){
			$ret = true;
		}
		return $ret;
	}
	
	public function checkPhanBoCanBoBoMon()
	{
		$user = base64_decode($_SESSION["uidloginPortal"]);
		
		$sqlstr="SELECT DISTINCT f.fk_ma_chuc_nang CHUC_NANG 
		FROM nhan_su n, ct_nhom_nhan_su ct, ct_nhom_nguoi_dung_portal f 
		WHERE upper(n.username)=upper('".$user."') 
		AND n.id=ct.fk_id_ns 
		AND ct.fk_ma_nhom = f.fk_ma_nhom 
		AND f.fk_ma_chuc_nang = ".self::PHAN_BO_CAN_BO_BO_MON_ROLE_ID;
		$check = $this->getQuery($sqlstr)
		->execute(false, array());
		$ret = false;
		
		if($check->itemsCount > 0){
			$ret = true;
		}
		return $ret;
	}
	
	public function checkViewAllPhanBoCanBo()
	{
		$user = base64_decode($_SESSION["uidloginPortal"]);
		
		$sqlstr="SELECT DISTINCT f.fk_ma_chuc_nang CHUC_NANG 
		FROM nhan_su n, ct_nhom_nhan_su ct, ct_nhom_nguoi_dung_portal f 
		WHERE upper(n.username)=upper('".$user."') 
		AND n.id=ct.fk_id_ns 
		AND ct.fk_ma_nhom = f.fk_ma_nhom 
		AND f.fk_ma_chuc_nang = ".self::VIEW_ALL_PHAN_BO_CAN_BO_ROLE_ID;
		$check = $this->getQuery($sqlstr)
		->execute(false, array());
		$ret = false;
		
		if($check->itemsCount > 0){
			$ret = true;
		}
		return $ret;
	}
}
