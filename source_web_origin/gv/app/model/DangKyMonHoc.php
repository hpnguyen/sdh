<?php
/**
 * 
 */
class DangKyMonHocModel extends BaseTable {
		
	function __construct() {
		parent::init("dang_ky_mon_hoc");
	}
	
	function __destruct() {
		parent::__destruct();
	}
	
	public function getDanhSach($dothoc,$mamh,$lop)
	{
		$ret = array();
		// Lay danh sach hoc vien
		$sqlstr="SELECT dk.ma_hoc_vien, h.ho, h.ten, 
			decode(h.phai,'F','Nữ','Nam') PHAI, khoa, nvl(email_truong,email) email 
		FROM dang_ky_mon_hoc DK, hoc_vien h 
		WHERE DK.DOT_HOC = '".$dothoc."' 
			AND DK.MA_MH = '".$mamh."'
			AND DK.LOP= '".$lop."'
			AND dk.ma_hoc_vien = h.ma_hoc_vien
		ORDER BY h.ten";
		$check = $this->getQuery($sqlstr)
		->execute(false, array());
		
		if($check->itemsCount > 0){
			$ret = $check->result;
		}
		
		return $ret;
	}
}
