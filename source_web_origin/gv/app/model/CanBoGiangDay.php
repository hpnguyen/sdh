<?php
/**
 * 
 */
class CanBoGiangDayModel extends BaseTable {
	
	function __construct() {
		parent::init("nhan_su");
	}
	
	function __destruct() {
		parent::__destruct();
	}
	
	public function getCanBo()
	{
		$sqlstr="SELECT ma_can_bo, shcc, ten_bo_mon, ten_khoa, get_thanh_vien(c.ma_can_bo) cbgd
				FROM can_bo_giang_day c, bo_mon b, khoa k
				WHERE 	c.ma_bo_mon = b.ma_bo_mon
						and b.ma_khoa = k.ma_khoa
						and ma_hoc_vi in ('TS', 'TSK') 
						and c.trang_thai = 1 
				ORDER BY	ten_khoa, ten_bo_mon, ten_eng, ho_eng";
		$check = $this->getQuery($sqlstr)
		->execute(false, array());
		$ret = array();
		if($check->itemsCount > 0){
			$ret = $check->result;
		}
		return $ret;
		
	}
	
	public function getUserLoginMaBoMon()
	{
		$user = base64_decode($_SESSION["uidloginPortal"]);
		$sqlstr="SELECT c.ma_bo_mon 
				FROM can_bo_giang_day c, nhan_su n 
				WHERE 	c.ma_can_bo = n.fk_ma_can_bo
				and upper(n.username)=upper('".$user."')";
		$check = $this->getQuery($sqlstr)
		->execute(false, array());
		$ret = null;
		
		if($check->itemsCount > 0){
			$ret = $check->result[0]['ma_bo_mon'];
		}
		
		return $ret;
	}	
}