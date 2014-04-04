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
				
				$whereCondition = "fk_ma_can_bo = '".$macb."' and ma_thuyet_minh_dt = '".$madetai.
				"' and check_het_han_phan_bien(ma_thuyet_minh_dt,fk_ma_can_bo) = 0";
				
				$this->getUpdate($data)->where($whereCondition)->execute(true, array());
			}
			
			return true;
		}else{
			return null;
		}
	}
	
	public function getListToFixInvalidCharacter(){
		$whereCondition = "(a1_tam_quan_trong like '%\%' OR a1_tam_quan_trong like '%''%' OR a1_tam_quan_trong like '%\"%' OR a1_tam_quan_trong like '%&#92;&quot;%' OR a1_tam_quan_trong like '%&#92;&#92;%' OR a1_tam_quan_trong like '&#92;&#39;%') 
		or (a2_chat_luong_nc like '%\%' OR a2_chat_luong_nc like '%''%' OR a2_chat_luong_nc like '%\"%' OR a2_chat_luong_nc like '%&#92;&quot;%' OR a2_chat_luong_nc like '%&#92;&#92;%' OR a2_chat_luong_nc like '%&#92;&#39;%') 
		or (a3_nlnc_csvc like '%\%' OR a3_nlnc_csvc like '%''%'  OR a3_nlnc_csvc like '%\"%'  OR a3_nlnc_csvc like '%&#92;&quot;%'  OR a3_nlnc_csvc like '%&#92;&#92;%' OR a3_nlnc_csvc like '%&#92;&#39;%')
		or (a4_kinh_phi_nx like '%\%' OR a4_kinh_phi_nx like '%''%' OR a4_kinh_phi_nx like '%\"%' OR a4_kinh_phi_nx like '%&#92;&quot;%' OR a4_kinh_phi_nx like '%&#92;&#92;%' OR a4_kinh_phi_nx like '%&#92;&#39;%')
		or (c_ket_luan like '%\%' OR c_ket_luan like '%''%' OR c_ket_luan like '%\"%' OR c_ket_luan like '%&#92;&quot;%' OR c_ket_luan like '%&#92;&#92;%' OR c_ket_luan like '%&#92;&#39;%')";
		
		$check = $this->getSelect("*")
		->where($whereCondition)
		->execute(false, array());
		
		if ($check->itemsCount > 0) {
			return $check->result;
		}else{
			return null;
		}
		
	}
	
}
