<?php
/**
 * 
 */
class NckhPbDmNoiDungModel extends BaseTable {
	
	private $prefixTabA4DmKinhPhiNoiDungDanhGia = '';
	
	function __construct() {
		parent::init("nckh_pb_dm_noi_dung","_khcn");
		$config = Helper::getHelper('functions/util')->getDbFileConfig();
		$this->prefixTabA4DmKinhPhiNoiDungDanhGia = $config['prefix_value_tab_a4_nckh_pb_noi_dung'];
	}
	
	function __destruct() {
		parent::__destruct();
	}
	
	public function changePrefixTabA4DmKinhPhiNoiDungDanhGia($value){
		$this->prefixTabA4DmKinhPhiNoiDungDanhGia = $value;
	}
	
	public function getListTabA4KinhPhiPhanBien()
	{
		$check = $this->getSelect('*')
		->where("ma_nd like '".$this->prefixTabA4DmKinhPhiNoiDungDanhGia."%'")
		->order("ma_nd asc")
		->execute(false, array());
		
		$ret = array();
		
		if($check->itemsCount > 0){
			$ret = $check->result;
		}
		return $ret;
	}
	
	public function getDetailNckhPbDmNoiDung($madetai , $macb)
	{
		$sqlDmKinhPhi = "SELECT 
		id, stt, noi_dung, 
		decode(nhan_xet,'1','X','') nhan_xet_cao, 
		decode(nhan_xet,'0','X','') nhan_xet_thap, 
		kinh_phi_de_nghi,
		id_order_by
		FROM 	nckh_pb_dm_noi_dung a,
				nckh_pb_noi_dung_kinh_phi b
		WHERE a.ma_nd =  b.ma_nd
			AND b.ma_thuyet_minh_dt =  '".$madetai."'
			AND b.fk_ma_can_bo = '".$macb."'
			ORDER BY b.id_order_by asc, a.ma_nd asc";
			
		$check = $this->getQuery($sqlDmKinhPhi)->execute(false, array());
		
		$ret = array();
		
		if($check->itemsCount > 0){
			$ret = $check->result;
		}
		return $ret;
	}
}
