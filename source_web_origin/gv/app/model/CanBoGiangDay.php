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
	
	public function getDetailForThuyetMinhDeTai($macb)
	{
		$sqlstr="SELECT cb.*, to_char(cb.NGAY_SINH,'dd-mm-yyyy') ngay_sinh, 
			decode(phai, 'M', 'Nam', 'F', 'Nữ') phai_desc, 
			k.ten_khoa, bm.ten_bo_mon,	v.ten_chuc_vu, bmql.ten_bo_mon ten_bo_mon_ql, 
			qghv.ten_quoc_gia ten_nuoc_hv, hv.ten ten_hv, cb.chuyen_mon_bc_bo_gddt,
			decode(ma_hoc_ham, 'GS','Giáo sư', 'PGS','Phó giáo sư', '') ten_hoc_ham, 
			get_thanh_vien(cb.ma_can_bo) hotencb,
			get_nam_dat_hv_cao_nhat(cb.ma_can_bo, cb.ma_hoc_vi) nam_dat_hv_cao_nhat
		FROM can_bo_giang_day cb, 
			bo_mon bm, khoa k, 
			dm_chuc_vu v, 
			bo_mon bmql, 
			quoc_gia qghv, 
			dm_hoc_vi hv
		WHERE cb.ma_bo_mon = bm.ma_bo_mon (+) 
			and bm.ma_khoa = k.ma_khoa (+)
			and cb.fk_chuc_vu = v.ma_chuc_vu (+)
			and cb.ma_bo_mon_ql = bmql.ma_bo_mon (+)
			and cb.qg_dat_hoc_vi = qghv.ma_quoc_gia (+)
			and cb.ma_hoc_vi = hv.ma_hoc_vi (+)
			and cb.ma_can_bo='".$macb."'";
		
		$check = $this->getQuery($sqlstr)->execute(false, array());
		$ret = array();
		
		if($check->itemsCount > 0){
			$ret = $check->result[0];
		}
		
		return $ret;
	}	
}