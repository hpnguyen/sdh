<?php
/**
 * 
 */
class DangKyDeCuongModel extends BaseTable {
		
	function __construct() {
		parent::init("dang_ky_de_cuong");
	}
	
	function __destruct() {
		parent::__destruct();
	}
	
	public function getDanhSach($dothoc,$makhoa)
	{
		$ret = array();
		
		if ($dothoc != null) {
			$sqlstr = "	SELECT 	n.ten_nganh, h.ma_hoc_vien, h.ho, h.ten, k.ten_khoa,
								tong_tin_chi_tich_luy(d.ma_hoc_vien) tong_chi_tich_luy,
								decode(ctdt_loai(h.ma_hoc_vien), '1', 'GD MH + Khóa luận', '2', 'GD MH + LVThS', 'Nghiên cứu') loai_ctdt, 
								d.huong_nghien_cuu, get_thanh_vien(d.huong_dan_1) huong_dan_1, 
								get_thanh_vien(d.huong_dan_2) huong_dan_2, d.dot_xet, d.ghi_chu
						FROM dang_ky_de_cuong d, hoc_vien h, nganh n, bo_mon b, khoa k 
						WHERE d.ma_hoc_vien = h.ma_hoc_vien and h.ma_nganh = n.ma_nganh 
						and n.ma_bo_mon = b.ma_bo_mon and b.ma_khoa = k.ma_khoa 
						and d.dot_hoc = to_date('".$dothoc."','dd-mm-yyyy') 
						and k.ma_khoa = ".$makhoa." 
						and du_dieu_kien = 1 
						ORDER BY ten_nganh, h.ten, h.ho";
			
			$check = $this->getQuery($sqlstr)
			->execute(false, array());
			
			
			
			if($check->itemsCount > 0){
				$ret = $check->result;
			}
		}
		
		return $ret;
	}
}
