<?php
/**
 * 
 */
class ViewKlgdModel extends BaseTable {
	
	function __construct() {
		parent::init("view_klgd");
	}
	
	function __destruct() {
		parent::__destruct();
	}
	
	public function getTest() {
		$check = $this->getAll();
		var_dump($check);
	}
	
	public function getListKlgdByKhoaBomon($makhoa, $dothoc, $mabomon = null) {
		$sqlCanBoDetail ="SELECT c.*, 
			get_thanh_vien(c.ma_can_bo) hotencb
		FROM can_bo_giang_day c, bo_mon b
		WHERE  c.ma_can_bo in (
			select distinct ma_can_bo 
			from view_klgd 
			where 
				dot_hoc = '".$dothoc."' 
				and cbgd not like '%TH - TN Bộ môn%'
		)  
		and c.ma_bo_mon = b.ma_bo_mon
		and b.ma_khoa = ".$makhoa
		.($mabomon != null ? " and b.ma_bo_mon = '".$mabomon."'": "")."
		ORDER BY c.ma_can_bo asc";
		
		$check = $this->getQuery($sqlCanBoDetail)->execute(false, array());
		
		$ret = array();
		
		if($check->itemsCount > 0){
			$ret = $check->result;
			foreach ($ret as $key => $row) {
				$temp = $row;
				$macb = $temp['ma_can_bo'];
				
				$sqlstr="SELECT v.*, get_ten_tat_tkb(v.ma_can_bo, v.dot_hoc, v.ma_mh, v.lop) ten_lop
				FROM view_klgd v , can_bo_giang_day c, bo_mon b
				WHERE v.ma_can_bo = '".$macb."'
					and v.ma_can_bo = c.ma_can_bo 
					and c.ma_bo_mon = b.ma_bo_mon
					and b.ma_khoa = ".$makhoa
					.($mabomon != null ? " and b.ma_bo_mon = '".$mabomon."'": "")."
					and v.dot_hoc = '".$dothoc."' 
					and v.cbgd not like '%TH - TN Bộ môn%'
				ORDER BY v.ma_can_bo asc ";
				
				$rowDetail = $this->getQuery($sqlstr)->execute(false, array());
				$rowDetailNew = array();
				
				foreach ($rowDetail->result as $k2 => $row2) {
					$sqlstrDetail = "";
					if ($row2["loai"] == "LV") {
						$sqlstrDetail="SELECT v.*, h.ho || ' ' || h.ten ho_ten, 
							decode(h.phai, 'F','Nữ' , 'M', 'Nam') phaidesc, 
							to_char(h.ngay_sinh,'dd-mm-yyyy') ngay_sinh,
							ten_de_tai detai, so_tiet_qd, 
							decode(ctdt_loai(v.ma_hoc_vien), 1,'KL', 2,'GD', 3,'NC') ctdt
						FROM chi_tiet_klgd_luan_van v, 
							hoc_vien h, 
							luan_van_thac_sy l
						WHERE v.ma_can_bo = '".$macb."'
							and v.dot_hoc = '".$dothoc."' 
							and v.ma_hoc_vien = h.ma_hoc_vien
							and l.dot_tinh_klgd = '".$dothoc."' 
							and l.ma_hoc_vien = h.ma_hoc_vien
						ORDER BY h.ho, h.ten";
					}elseif ($row2["loai"] == "TS"){
						$sqlstrDetail="SELECT v.*, h.ho || ' ' || h.ten ho_ten, 
							decode(h.phai, 'F','Nữ' , 'M', 'Nam') phaidesc, 
							to_char(h.ngay_sinh,'dd-mm-yyyy') ngay_sinh,
							l.ten_luan_an detai, so_tiet_qd, '' ctdt
						FROM chi_tiet_klgd_hd_ncs v, 
							hoc_vien h, 
							luan_an_tien_sy l
						WHERE v.ma_can_bo = '".$macb."'	
							and v.dot_hoc = '".$dothoc."' 
							and v.ma_hoc_vien = h.ma_hoc_vien
							and l.ma_hoc_vien = h.ma_hoc_vien
						ORDER BY h.ho, h.ten";
					}
					
					if ($sqlstrDetail != ""){
						$rowDetailLvTs = $this->getQuery($sqlstrDetail)->execute(false, array());
						$row2['chi_tiet_klgd_lv_ts'] = $rowDetailLvTs->result;
					}else{
						$row2['chi_tiet_klgd_lv_ts'] = null;
					}
					
					$rowDetailNew[] = $row2;
				}
				
				$temp['view_klgd'] = $rowDetailNew;
				$ret[$key] = $temp;
			}
		}
		return $ret;
	}
}
