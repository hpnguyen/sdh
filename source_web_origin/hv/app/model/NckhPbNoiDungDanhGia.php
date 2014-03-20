<?php
/**
 * 
 */
class NckhPbNoiDungDanhGiaModel extends BaseTable {
	
	function __construct() {
		parent::init("nckh_pb_noi_dung_danh_gia","_khcn");
	}
	
	function __destruct() {
		parent::__destruct();
	}
	
	public function checkRowExist($id, $macb, $madetai){
		$check = $this->getSelect('*')
		->where("id = ".$id." and fk_ma_can_bo = '".$macb."' and ma_thuyet_minh_dt = '".$madetai."'")
		->execute(false, array());
		
		return $check->itemsCount == 1;
	}
	
	public function doUpdate($data)
	{
		if (is_array($data) && isset($data['fk_ma_can_bo']) && isset($data['ma_thuyet_minh_dt']) && isset($data['id'])){
			$macb = $data['fk_ma_can_bo'];
			$madetai = $data['ma_thuyet_minh_dt'];
			$id = $data['id'];
			
			if ($this->checkRowExist($id,$macb,$madetai)) {
				//Update
				unset($data['fk_ma_can_bo']);
				unset($data['ma_thuyet_minh_dt']);
				unset($data['id']);
				$data['update_timestamp'] ='CURRENT_TIMESTAMP';
				// var_dump($data);
				
				$whereCondition = "id = ".$id." and fk_ma_can_bo = '".$macb."' 	and ma_thuyet_minh_dt = '".$madetai.
				"' and check_het_han_phan_bien(ma_thuyet_minh_dt,fk_ma_can_bo) = 0";
				
				$this->getUpdate($data)->where($whereCondition)->execute(true, array());
			}
			
			return true;
		}else{
			return null;
		}
	}
}
