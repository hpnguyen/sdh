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
}
