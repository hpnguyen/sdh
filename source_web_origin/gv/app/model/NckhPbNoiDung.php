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
			
			$dataCLOB = array(
				'a1_tam_quan_trong' => $data['a1_tam_quan_trong'],
				'a2_chat_luong_nc' => $data['a2_chat_luong_nc'],
				'a3_nlnc_csvc' => $data['a3_nlnc_csvc'],
				'a4_kinh_phi_nx' => $data['a4_kinh_phi_nx'],
				'c_ket_luan' => $data['c_ket_luan']
			);
			
			unset($data['a1_tam_quan_trong']);
			unset($data['a2_chat_luong_nc']);
			unset($data['a3_nlnc_csvc']);
			unset($data['a4_kinh_phi_nx']);
			unset($data['c_ket_luan']);
				
			if (! $this->checkRowExist($macb,$madetai)) {
				//Insert new record
				$this->getInsert($data)->execute(true, array());
			}
			
			//Update CLOB data
			$whereCondition = "fk_ma_can_bo = '".$macb."' and ma_thuyet_minh_dt = '".$madetai.
			"' and check_het_han_phan_bien(ma_thuyet_minh_dt,fk_ma_can_bo) = 0";
			// $whereCondition = "fk_ma_can_bo = '".$macb."' and ma_thuyet_minh_dt = '".$madetai."'";
			
			foreach ($dataCLOB as $colname => $value) {
				//if($value != null || $value != ''){
					$tempData= array();
					$tempData[$colname] = 'EMPTY_CLOB()';
					$this->getUpdate($tempData)->where($whereCondition)->executeCLOB($colname, $value);
				//}
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
