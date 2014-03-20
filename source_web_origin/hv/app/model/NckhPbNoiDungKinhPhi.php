<?php
/**
 * 
 */
class NckhPbNoiDungKinhPhiModel extends BaseTable {
	
	function __construct() {
		parent::init("nckh_pb_noi_dung_kinh_phi","_khcn");
	}
	
	function __destruct() {
		parent::__destruct();
	}
	
	public function checkRowExist($macb, $madetai, $mand){
		$check = $this->getSelect('*')
		->where("fk_ma_can_bo = '".$macb."' and ma_thuyet_minh_dt = '".$madetai."' and ma_nd = '".$mand."'")
		->execute(false, array());
		
		return $check->itemsCount == 1;
	}
	
	public function doCreateUpdate($data)
	{
		if (is_array($data) && isset($data['fk_ma_can_bo']) && isset($data['ma_thuyet_minh_dt']) && isset($data['ma_nd'])){
			$macb = $data['fk_ma_can_bo'];
			$madetai = $data['ma_thuyet_minh_dt'];
			$mand = $data['ma_nd'];
			
			if (! $this->checkRowExist($macb,$madetai,$mand)) {
				//Insert new record
				//$this->getInsert($data)->execute(true, array());
			}else{
				//Update
				unset($data['fk_ma_can_bo']);
				unset($data['ma_thuyet_minh_dt']);
				unset($data['ma_nd']);
				unset($data['id']);
				unset($data['stt']);
				unset($data['id_order_by']);
				$data['update_timestamp'] ='CURRENT_TIMESTAMP';
				// var_dump($data);
				$whereCondition = "fk_ma_can_bo = '".$macb."' and ma_thuyet_minh_dt = '".$madetai."' and ma_nd = '".
				$mand."' and check_het_han_phan_bien(ma_thuyet_minh_dt,fk_ma_can_bo) = 0";
				
				$this->getUpdate($data)->where($whereCondition)->execute(true, array());
			}
			
			return true;
		}else{
			return null;
		}
	}
}
