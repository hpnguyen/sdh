<?php
/**
 * 
 */
class HelperDanhmucPhanBoCanboGiangDay {
	
	function __construct($argument) {
		
	}
	
	public function getCanBo($db_conn){
		$sqlstr="SELECT ma_can_bo, shcc, ten_bo_mon, ten_khoa, get_thanh_vien(c.ma_can_bo) cbgd
				FROM can_bo_giang_day c, bo_mon b, khoa k
				WHERE 	c.ma_bo_mon = b.ma_bo_mon
						and b.ma_khoa = k.ma_khoa
						and ma_hoc_vi in ('TS', 'TSK')
				ORDER BY	ten_khoa, ten_bo_mon, ten_eng, ho_eng"; 
		$util = new Db($db_conn);
		return $util->query($sqlstr)->execute()->fetchAll();
	}
	
	public function getMonHoc($db_conn,$dothoc,$makhoa){
		$sqlstr="	SELECT DISTINCT m.TEN, T.MA_MH, t.ma_can_bo, t.ma_can_bo_phu,
			decode(T.THU, 9, null, t.thu) thu, T.TIET_BAT_DAU, T.TIET_KET_THUC, tuan_bat_dau,
			(TUAN_BAT_DAU)||'->'||(TUAN_KET_THUC) Tuan_hoc, T.PHONG,
			t.dot_hoc, so_tiet_lt, so_tiet_bt, so_tiet_th, so_tiet_tl, t.lop, '' NGAY_THI, b.ten_bo_mon, get_thanh_vien(t.ma_can_bo) ten_cb,
			get_nganh_tkb(t.ma_can_bo, t.dot_hoc, t.ma_mh,t.lop) chuyen_nganh,
				(SELECT COUNT(*) FROM dang_ky_mon_hoc DK WHERE DK.DOT_HOC = t.dot_hoc AND DK.MA_MH = t.ma_mh
				AND DK.LOP=t.lop) SL
			FROM THOI_KHOA_BIEU t, MON_HOC m, BO_MON b, KHOA k
			WHERE T.MA_MH = m.MA_MH
			AND (t.dot_hoc = to_date('".$dothoc."','dd-mm-yyyy'))
			AND m.ma_bo_mon = b.ma_bo_mon and b.ma_khoa = k.ma_khoa and k.ma_khoa = '$makhoa'
			ORDER BY tuan_bat_dau, nvl(thu, 9), tiet_bat_dau , t.lop, m.TEN"; 
		$util = new Db($db_conn);
		return $util->query($sqlstr)->execute()->fetchAll();
	}
}
