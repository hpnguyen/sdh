<?php
/**
 * 
 */
class HvuGiaiQuyetHvuModel extends BaseTable {
	
	function __construct() {
		parent::init("hvu_giai_quyet_hvu");
	}
	
	function __destruct() {
		parent::__destruct();
	}
	
	public function getDsTinhTrangHocVu($makhoa,$nam,$tinhtrang)
	{
		$whereTinhTrang = "";
		if ($tinhtrang != ""){
			if ($tinhtrang == '!2'){
				$whereTinhTrang = "and hvu.tinh_trang <> 2";
			}else{
				$whereTinhTrang = "and hvu.tinh_trang = ".$tinhtrang;
			}
		}
		
		$sqlstr="SELECT ma_gqhvu, noi_dung_yc,
		to_char(NGAY_TIEP_NHAN, 'dd-mm-yyyy') ngay_tiep_nhan,
		to_char(NGAY_HEN_TRA_KQ, 'dd-mm-yyyy') ngay_hen_tra_kq,
		(n.ten) ten_nguoi_giai_quyet, tt.ten_tinh_trang,
		hvu.ket_qua, hvu.so_luong, hvu.don_gia,
		to_char(ngay_tra_kq, 'dd-mm-yyyy hh:mi') ngay_tra_kq
		FROM hvu_giai_quyet_hvu hvu, 
		nhan_su n, 
		hvu_dm_tinh_trang tt
		WHERE fk_ma_hoc_vien = '".$makhoa."'
		and hvu.nguoi_giai_quyet = n.id(+) 
		and hvu.tinh_trang = tt.ma_tinh_trang(+) 
		and thung_rac is null
		".$whereTinhTrang." 
		and (hvu.NGAY_TIEP_NHAN between to_date('01/01/".$nam."', 'dd/mm/yyyy') and to_date('31/12/".$nam."', 'dd/mm/yyyy'))
		ORDER BY ma_gqhvu desc";
		
		$check = $this->getQuery($sqlstr)
		->execute(false, array());
		
		$ret = array();
		
		if($check->itemsCount > 0){
			$ret = $check->result;
		}
		return $ret;
	}
	
	public function getDsKhoa($makhoa)
	{
		$sqlstr="SELECT distinct nk.ma_khoa_truong, nk.ten_khoa , 
		CASE WHEN nk.ma_khoa_truong = '".$makhoa."' THEN 'selected' ELSE '' END as selected
		FROM hvu_giai_quyet_hvu hvu, khoa nk 
		WHERE hvu.fk_ma_hoc_vien = nk.ma_khoa_truong 
		and thung_rac is null 
		ORDER BY viet0dau_name(nk.ten_khoa) asc";
		
		$check = $this->getQuery($sqlstr)
		->execute(false, array());
		
		$ret = array();
		
		if($check->itemsCount > 0){
			$ret = $check->result;
		}
		return $ret;
	}
}