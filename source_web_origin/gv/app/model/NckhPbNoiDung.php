<?php
/**
 * 
 */
class NckhPbNoiDungModel extends BaseTable {
	
	function __construct() {
		parent::init("nckh_pb_noi_dung","_khcn");
	}
	
	function __destruct() {
		parent::__destruct();
	}
	
	public function checkRowExist($macb, $madetai){
		$check = $this->getSelect('*')
		->where("fk_ma_can_bo = '".$macb."' and ma_thuyet_minh_dt = '".$madetai."'")
		->execute(false, array(':macb' => "'".$macb."'", ':madetai' => "'".$madetai."'"));
		
		return $check->itemsCount == 1;
	}
	
	public function doCreateUpdate($data)
	{
		if (is_array($data) && isset($data['fk_ma_can_bo']) && isset($data['ma_thuyet_minh_dt'])){
			$macb = $data['fk_ma_can_bo'];
			$madetai = $data['ma_thuyet_minh_dt'];
			
			if (! $this->checkRowExist($macb,$madetai)) {
				//Insert new record
				$this->getInsert($data)->execute(true, array());
			}else{
				//Update
				unset($data['fk_ma_can_bo']);
				unset($data['ma_thuyet_minh_dt']);
				$this->getUpdate($data)
				->where("fk_ma_can_bo = '".$macb."' and ma_thuyet_minh_dt = '".$madetai."'")
				->execute(true, array());
			}
			
			return true;
		}else{
			return null;
		}
	}
}
