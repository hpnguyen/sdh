<?php
/**
 * 
 */
class NckhPhanCongPhanBienModel extends BaseTable {
	
	function __construct() {
		parent::init("nckh_phan_cong_phan_bien","_khcn");
	}
	
	function __destruct() {
		parent::__destruct();
	}
	
	public function getListYear($macb, $year = '')
	{
		$selectStr = "distinct TO_CHAR(ngay_phan_cong ,'YYYY') t_year, 
		(CASE WHEN TO_CHAR(ngay_phan_cong ,'YYYY') = '".$year."' THEN 'selected' ELSE '' END) AS selected";
		$orderStr = "t_year desc";
		$whereStr = "fk_ma_can_bo = '".$macb."'";
		
		$check = $this->getSelect($selectStr)->where($whereStr)->order($orderStr)->execute(false, array());
		// SELECT 
		// distinct TO_CHAR(ngay_phan_cong ,'YYYY') t_ngay_phan_cong, 
		// (CASE WHEN TO_CHAR(ngay_phan_cong ,'YYYY') = '' THEN 'selected' ELSE '' END) AS select_box 
		// FROM nckh_phan_cong_phan_bien WHERE fk_ma_can_bo = '000927' 
		// ORDER BY t_ngay_phan_cong desc;
		//echo $check->sql;
		
		if($check->itemsCount > 0){
			return $check->result;
		}else{
			return array();
		}
	}
	
	public function getListByMacb($macb)
	{
		$check = $this->getSelect('*')->where("fk_ma_can_bo = '".$macb."'")->execute(false, array());
		
		if ($check->itemsCount > 0){
			$last = $check->result[$check->itemsCount - 1];
			return $last['value'];
		}else{
			return null;
		}
	}
	
	public function checkEnableView($madetai, $macb) {
		$check = $this->getSelect("*")
		->where("ma_thuyet_minh_dt =  '".$madetai."' and fk_ma_can_bo = '".$macb."'")
		->execute(false, array());
		
		return $check->itemsCount > 0;
	}
	
	public function checkEnableSaveUpdate($madetai, $macb) {
		$check = $this->getSelect("*")
		->where("ma_thuyet_minh_dt =  '".$madetai."' and fk_ma_can_bo = '".$macb."'")
		->execute(false, array());
		
		$ret = false;
		if($check->itemsCount > 0){
			$row = $check->result[0];
			if ($row["kq_phan_hoi"] == '1'){
				$ret = true;
			}
		}
		return $ret;
	}
	
	public function updateYes($madetai, $macb) {
		$this->getUpdate(array('kq_phan_hoi' => 1, 'ngay_phan_hoi' => 'SYSDATE'))
		->where("ma_thuyet_minh_dt =  '".$madetai."' and fk_ma_can_bo = '".$macb."'")
		->execute(true, array());
	}
	
	public function updateNo($madetai, $macb) {
		$this->getUpdate(array('kq_phan_hoi' => 0, 'ngay_phan_hoi' => 'SYSDATE'))
		->where("ma_thuyet_minh_dt =  '".$madetai."' and fk_ma_can_bo = '".$macb."'")
		->execute(true, array());
	}
}
