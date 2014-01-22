<?php
/**
 * 
 */
class ThoiKhoaBieuModel extends BaseTable {
	private $maMonHocChung = array('505900', '505901', '125900', '150300003', '150300004');
	
	function __construct() {
		parent::init("thoi_khoa_bieu");
	}
	
	function __destruct() {
		parent::__destruct();
	}
	
	public function getDanhSachMonHocPhanBo($dothoc,$makhoa, $viewall = false)
	{
		$sqlstr="SELECT DISTINCT m.TEN, T.MA_MH, t.ma_can_bo,t.ma_can_bo_phu, 
			decode(T.THU, 9, null, 1, 'CN', t.thu) thu, T.TIET_BAT_DAU, T.TIET_KET_THUC, tuan_bat_dau,
			(TUAN_BAT_DAU)||'->'||(TUAN_KET_THUC) Tuan_hoc, T.PHONG,
			t.dot_hoc, so_tiet_lt, so_tiet_bt, so_tiet_th, so_tiet_tl, t.lop, '' NGAY_THI, b.ten_bo_mon, 
			get_thanh_vien(t.ma_can_bo) ten_cb_chinh,
			get_thanh_vien(t.ma_can_bo_phu) ten_cb_phu,
			t.ghi_chu, 
			t.khoa_xet_duyet,
			get_nganh_tkb(t.ma_can_bo, t.dot_hoc, t.ma_mh,t.lop) chuyen_nganh,
				(SELECT COUNT(*) FROM dang_ky_mon_hoc DK WHERE DK.DOT_HOC = t.dot_hoc AND DK.MA_MH = t.ma_mh
				AND DK.LOP=t.lop) SL ,
			t.khoa_duoc_pc_cbgd 
			FROM THOI_KHOA_BIEU t, MON_HOC m, BO_MON b, KHOA k
			WHERE t.MA_MH = m.MA_MH 
			AND t.MA_MH not in ('".implode("','", $this->maMonHocChung)."')
			AND (t.dot_hoc = to_date('".$dothoc."','dd-mm-yyyy')) 
			AND m.ma_bo_mon = b.ma_bo_mon and b.ma_khoa = k.ma_khoa ";
		
		if ($viewall == false) {
			$sqlstr .= " and k.ma_khoa = ".$makhoa; 
		}
		$sqlstr .= " ORDER BY t.khoa_duoc_pc_cbgd desc,chuyen_nganh, b.ten_bo_mon, thu, T.TIET_BAT_DAU asc,  m.TEN, t.lop"; 
		
		$check = $this->getQuery($sqlstr)
		->execute(false, array());
		
		$ret = array();
		
		if($check->itemsCount > 0){
			$ret = $check->result;
		}
		return $ret;
	}
	
	public function getDanhSachMonHocPhanBoBoMon($dothoc)
	{
		$cbgd = new CanBoGiangDayModel();
		$mabomon = $cbgd->getUserLoginMaBoMon();
		
		unset($cbgd);
			
		$sqlstr="SELECT DISTINCT m.TEN, T.MA_MH, t.ma_can_bo,t.ma_can_bo_phu, 
			decode(T.THU, 9, null, 1, 'CN', t.thu) thu, T.TIET_BAT_DAU, T.TIET_KET_THUC, tuan_bat_dau,
			(TUAN_BAT_DAU)||'->'||(TUAN_KET_THUC) Tuan_hoc, T.PHONG,
			t.dot_hoc, so_tiet_lt, so_tiet_bt, so_tiet_th, so_tiet_tl, t.lop, '' NGAY_THI, b.ten_bo_mon, 
			get_thanh_vien(t.ma_can_bo) ten_cb_chinh,
			get_thanh_vien(t.ma_can_bo_phu) ten_cb_phu,
			t.ghi_chu,
			get_nganh_tkb(t.ma_can_bo, t.dot_hoc, t.ma_mh,t.lop) chuyen_nganh,
				(SELECT COUNT(*) FROM dang_ky_mon_hoc DK WHERE DK.DOT_HOC = t.dot_hoc AND DK.MA_MH = t.ma_mh
				AND DK.LOP=t.lop) SL ,
			t.khoa_duoc_pc_cbgd 
			FROM THOI_KHOA_BIEU t, MON_HOC m, BO_MON b, KHOA k
			WHERE t.MA_MH = m.MA_MH 
			AND t.MA_MH not in ('".implode("','", $this->maMonHocChung)."') 
			AND (t.dot_hoc = to_date('".$dothoc."','dd-mm-yyyy'))
			AND m.ma_bo_mon = b.ma_bo_mon and b.ma_khoa = k.ma_khoa and m.ma_bo_mon = ".$mabomon."
			ORDER BY t.khoa_duoc_pc_cbgd desc,chuyen_nganh, b.ten_bo_mon, thu, T.TIET_BAT_DAU,  m.TEN, t.lop"; 
		
		$check = $this->getQuery($sqlstr)
		->execute(false, array());
		
		$ret = array();
		
		if($check->itemsCount > 0){
			$ret = $check->result;
		}
		return $ret;
	}
	public function phanBoCanBo($dotHoc,$lop,$mamh,$loai,$cbgd,$ghichu,$duyet = null)
	{
		$check = $this->getSelect('*')
		->where("dot_hoc = to_date('".$dotHoc."','dd-mm-yyyy') and lop = ".$lop." and ma_mh = '".$mamh."'")
		->execute(false, array());
		
		if ($check->itemsCount < 1){
			
			return false;
		}else{
			$data = null;
			
			if ($loai == 0){
				$data = array('ma_can_bo' => $cbgd);
				if ($cbgd == '' || $cbgd == null){
					$data['khoa_xet_duyet'] = 0;
				}
			}elseif($loai == 1){
				$data = array('ma_can_bo_phu' => $cbgd);
			}elseif($loai == 2){
				$data = array('ghi_chu' => $ghichu);
			}elseif($loai == 3){
				$data = array('khoa_xet_duyet' => (int) $duyet);
			}
			
			if ($data != null) {
				//Log user update data
				$data['ngay_cap_nhat'] = 'SYSDATE';
				$data['user_update'] = base64_decode($_SESSION["uidloginPortal"]);
				
				$this->getUpdate($data)
				->where("dot_hoc = to_date('".$dotHoc."','dd-mm-yyyy') and lop = ".$lop." and ma_mh = '".$mamh."'")
				->execute(true, array());
			}
			
			return true;
		}
		
	}
	
	public function expireCheckPhanBoCanBo($dotHoc,$lop,$mamh)
	{
		$check = $this->getSelect('*')
		->where("dot_hoc = to_date('".$dotHoc."','dd-mm-yyyy') and lop = ".$lop." and ma_mh = '".$mamh."' and khoa_duoc_pc_cbgd = 1")
		->execute(false, array());
		
		$ret = false;
		if ($check->itemsCount > 0){
			$ret = true;
		}
		
		return $ret;
	}
	
	public function checkXetDuyetPhanBoCanBo($dotHoc,$lop,$mamh)
	{
		$check = $this->getSelect('*')
		->where("ma_can_bo is not null and dot_hoc = to_date('".$dotHoc."','dd-mm-yyyy') and lop = ".$lop." and ma_mh = '".$mamh."'")
		->execute(false, array());
		if ($check->itemsCount < 1){
			return false;
		}else{
			return true;
		}
	}
	public function getListHocKy()
	{
		$sqlstr="SELECT (hoc_ky || '/' || nam_hoc_tu || '-' || nam_hoc_den) nam_hoc, to_char(dot_hoc,'dd-mm-yyyy') dot_hoc
				FROM dot_hoc_nam_hoc_ky 
				ORDER BY nam_hoc_tu desc, dot_hoc desc"; 
		$check = $this->getQuery($sqlstr)
		->execute(false, array());
		
		$ret = array();
		if($check->itemsCount > 0){
			$ret = $check->result;
		}
		return $ret;
	}
}
