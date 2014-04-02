<?php
/**
 * 
 */
class NhanSuModel extends BaseTable {
	const PHAN_BO_CAN_BO_ROLE_ID = 112;
	const PHAN_BO_CAN_BO_BO_MON_ROLE_ID = 113;
	const VIEW_ALL_PHAN_BO_CAN_BO_ROLE_ID = 114;
	const MEMBER_CAN_RESET_USER_PASSWORD_ROLE_ID = 116;
	const MEMBER_CAN_UPDATE_EMAIL_TEMPLATE_ROLE_ID = 120;
	const VIEW_TIEN_TRINH_HO_SO_ROLE_ID = 117;
	const VIEW_TIEN_TRINH_HO_SO_BY_KHOA_ROLE_ID = 121;
	const VIEW_ALL_TKB_ROLE_ID = 118;
	const VIEW_KLGD_KHOA_ROLE_ID = 122;
	const VIEW_KLGD_BO_MON_ROLE_ID = 118;
	
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
	
	public function checkKhoaViewTKB()
	{
		$user = base64_decode($_SESSION["uidloginPortal"]);
		
		$sqlstr="SELECT DISTINCT f.fk_ma_chuc_nang CHUC_NANG 
		FROM nhan_su n, ct_nhom_nhan_su ct, ct_nhom_nguoi_dung_portal f 
		WHERE upper(n.username)=upper('".$user."') 
		AND n.id=ct.fk_id_ns 
		AND ct.fk_ma_nhom = f.fk_ma_nhom 
		AND f.fk_ma_chuc_nang = ".self::VIEW_ALL_TKB_ROLE_ID;
		$check = $this->getQuery($sqlstr)
		->execute(false, array());
		$ret = false;
		
		if($check->itemsCount > 0){
			$ret = true;
		}
		return $ret;
	}
	public function checkRoleCanDoResetUserPassword()
	{
		$user = base64_decode($_SESSION["uidloginPortal"]);
		
		$sqlstr="SELECT DISTINCT f.fk_ma_chuc_nang CHUC_NANG 
		FROM nhan_su n, ct_nhom_nhan_su ct, ct_nhom_nguoi_dung_portal f 
		WHERE upper(n.username)=upper('".$user."') 
		AND n.id=ct.fk_id_ns 
		AND ct.fk_ma_nhom = f.fk_ma_nhom 
		AND f.fk_ma_chuc_nang = ".self::MEMBER_CAN_RESET_USER_PASSWORD_ROLE_ID;
		$check = $this->getQuery($sqlstr)
		->execute(false, array());
		$ret = false;
		
		if($check->itemsCount > 0){
			$ret = true;
		}
		return $ret;
	}
	
	public function checkRoleCanUpdateEmailTemplate()
	{
		$user = base64_decode($_SESSION["uidloginPortal"]);
		
		$sqlstr="SELECT DISTINCT f.fk_ma_chuc_nang CHUC_NANG 
		FROM nhan_su n, ct_nhom_nhan_su ct, ct_nhom_nguoi_dung_portal f 
		WHERE upper(n.username)=upper('".$user."') 
		AND n.id=ct.fk_id_ns 
		AND ct.fk_ma_nhom = f.fk_ma_nhom 
		AND f.fk_ma_chuc_nang = ".self::MEMBER_CAN_UPDATE_EMAIL_TEMPLATE_ROLE_ID;
		$check = $this->getQuery($sqlstr)
		->execute(false, array());
		$ret = false;
		
		if($check->itemsCount > 0){
			$ret = true;
		}
		return $ret;
	}
	public function checkRoleViewTienTrinhHoSo()
	{
		$user = base64_decode($_SESSION["uidloginPortal"]);
		
		$sqlstr="SELECT DISTINCT f.fk_ma_chuc_nang CHUC_NANG 
		FROM nhan_su n, ct_nhom_nhan_su ct, ct_nhom_nguoi_dung_portal f 
		WHERE upper(n.username)=upper('".$user."') 
		AND n.id=ct.fk_id_ns 
		AND ct.fk_ma_nhom = f.fk_ma_nhom 
		AND f.fk_ma_chuc_nang = ".self::VIEW_TIEN_TRINH_HO_SO_ROLE_ID;
		
		$check = $this->getQuery($sqlstr)
		->execute(false, array());
		$ret = false;
		
		if($check->itemsCount > 0){
			$ret = true;
		}
		return $ret;
	}
	
	public function checkRoleViewTienTrinhHoSoGroupByKhoa()
	{
		$user = base64_decode($_SESSION["uidloginPortal"]);
		
		$sqlstr="SELECT DISTINCT f.fk_ma_chuc_nang CHUC_NANG 
		FROM nhan_su n, ct_nhom_nhan_su ct, ct_nhom_nguoi_dung_portal f 
		WHERE upper(n.username)=upper('".$user."') 
		AND n.id=ct.fk_id_ns 
		AND ct.fk_ma_nhom = f.fk_ma_nhom 
		AND f.fk_ma_chuc_nang = ".self::VIEW_TIEN_TRINH_HO_SO_BY_KHOA_ROLE_ID;
		
		$check = $this->getQuery($sqlstr)
		->execute(false, array());
		$ret = false;
		
		if($check->itemsCount > 0){
			$ret = true;
		}
		return $ret;
	}
	
	public function checkRoleViewKlgdKhoa()
	{
		$user = base64_decode($_SESSION["uidloginPortal"]);
		
		$sqlstr="SELECT DISTINCT f.fk_ma_chuc_nang CHUC_NANG 
		FROM nhan_su n, ct_nhom_nhan_su ct, ct_nhom_nguoi_dung_portal f 
		WHERE upper(n.username)=upper('".$user."') 
		AND n.id=ct.fk_id_ns 
		AND ct.fk_ma_nhom = f.fk_ma_nhom 
		AND f.fk_ma_chuc_nang = ".self::VIEW_KLGD_KHOA_ROLE_ID;
		
		$check = $this->getQuery($sqlstr)
		->execute(false, array());
		$ret = false;
		
		if($check->itemsCount > 0){
			$ret = true;
		}
		return $ret;
	}
	
	public function checkRoleViewKlgdBoMon()
	{
		$user = base64_decode($_SESSION["uidloginPortal"]);
		
		$sqlstr="SELECT DISTINCT f.fk_ma_chuc_nang CHUC_NANG 
		FROM nhan_su n, ct_nhom_nhan_su ct, ct_nhom_nguoi_dung_portal f 
		WHERE upper(n.username)=upper('".$user."') 
		AND n.id=ct.fk_id_ns 
		AND ct.fk_ma_nhom = f.fk_ma_nhom 
		AND f.fk_ma_chuc_nang = ".self::VIEW_KLGD_BO_MON_ROLE_ID;
		
		$check = $this->getQuery($sqlstr)
		->execute(false, array());
		$ret = false;
		
		if($check->itemsCount > 0){
			$ret = true;
		}
		return $ret;
	}
	
	public function getDanhSachPhongSDH()
	{
		$sqlstr="SELECT id, ho || ' ' || ten ho_ten 
		FROM nhan_su 
		WHERE pdtsdh='1' 
		ORDER BY ten";
		$check = $this->getQuery($sqlstr)
		->execute(false, array());
		$ret = array();
		if($check->itemsCount > 0){
			$ret = $check->result;
		}
		return $ret;
	}
	
	public function getListNhanSu()
	{
		$sqlstr = "SELECT n.id, n.USERNAME, n.PASSWORD, n.FK_MA_CAN_BO, c.SHCC,
		decode(n.FK_MA_CAN_BO, null, n.ho || ' ' || n.ten,get_thanh_vien(n.FK_MA_CAN_BO)) ten_can_bo,
		b.TEN_BO_MON, decode(k.TEN_KHOA, null, k.TEN_KHOA, pb.TEN_KHOA) khoa
		FROM nhan_su n, can_bo_giang_day c, bo_mon b, khoa k, khoa pb
		WHERE n.FK_MA_CAN_BO = c.MA_CAN_BO
		and c.MA_BO_MON = b.MA_BO_MON
		and b.MA_KHOA = k.MA_KHOA
		and n.FK_MA_KHOA = pb.MA_KHOA
		ORDER BY k.ten_khoa, b.TEN_BO_MON, n.ten, n.ho";
		
		$check = $this->getQuery($sqlstr)
		->execute(false, array());
		$ret = array();
		if($check->itemsCount > 0){
			$ret = $check->result;
		}
		return $ret;
	}
	
	public function resetPassword($username)
	{
		$sqlstr = "SELECT n.id, n.USERNAME, n.PASSWORD, n.FK_MA_CAN_BO, c.SHCC,
		decode(n.FK_MA_CAN_BO, null, n.ho || ' ' || n.ten,get_thanh_vien(n.FK_MA_CAN_BO)) ten_can_bo,
		b.TEN_BO_MON, decode(k.TEN_KHOA, null, k.TEN_KHOA, pb.TEN_KHOA) khoa
		FROM nhan_su n, can_bo_giang_day c, bo_mon b, khoa k, khoa pb
		WHERE n.FK_MA_CAN_BO = c.MA_CAN_BO
		and c.MA_BO_MON = b.MA_BO_MON
		and b.MA_KHOA = k.MA_KHOA
		and n.FK_MA_KHOA = pb.MA_KHOA 
		and n.USERNAME = '".$username."'";
		
		$check = $this->getQuery($sqlstr)
		->execute(false, array());
		
		if($check->itemsCount > 0){
			$data = array('password' => $check->result[0]['shcc']);
			$this->getUpdate($data)
			->where("USERNAME = '".$username."'")
			->execute(true, array());
			
			return null;
		}else{
			return 'Không tồn tại người dùng này.';
		}
	}
	
	public function getByFkMaCanBo($fk_ma_can_bo){
		$check = $this->getSelect("*")->where("fk_ma_can_bo = '".$fk_ma_can_bo."'")->execute(false,array());
		if($check->itemsCount > 0){
			return $check->result[0];
		}else{
			return null;
		}
	}
	
	public function getDataOnLogin($id)
	{
		$sqlstr="SELECT n.fk_ma_khoa ma_khoa, n.username, n.fk_ma_can_bo ma_can_bo, k.ten_khoa
		FROM nhan_su n, khoa k, can_bo_giang_day c 
		WHERE c.ma_can_bo = n.fk_ma_can_bo(+)
		and n.fk_ma_khoa = k.ma_khoa(+)
		and c.shcc = '".$id."'";
		//echo $sqlstr;
		$check = $this->getQuery($sqlstr)
		->execute(false, array());
		if($check->itemsCount > 0){
			return $check->result[0];
		}else{
			return null;
		}
	}
	
	public function updateLoginTime($username)
	{
		$this->getUpdate(array('last_login' => 'SYSDATE'))
		->where("UPPER(username)=UPPER('".$username."')")
		->execute(true, array());
	}
	
	public function getByUsername($username = null)
	{
		$ret = null;
		if (! empty($username)){
			$sqlstr="SELECT n.*, k.ten_khoa 
			FROM nhan_su n, khoa k  
			WHERE UPPER(n.username)=UPPER('".$username."') 
			and n.fk_ma_khoa = k.ma_khoa(+)";
			
			$check = $this->getQuery($sqlstr)->execute(false, array());
			
			if($check->itemsCount > 0){
				$ret = $check->result[0];
			}
		}
		
		return $ret;
	}
}
