<?php
/**
 * 
 */
class ConfigModel extends BaseTable {
	
	const KH_BD = 'PC_CBGD_KH_NGAY_BD'; //Date start for khoa member can update
	const KH_KT = 'PC_CBGD_KH_NGAY_KT'; //Date end for khoa member can update
	const BM_BD = 'PC_CBGD_BM_NGAY_BD'; //Date start for bo mon member can update
	const BM_KT = 'PC_CBGD_BM_NGAY_KT'; //Date end for bo mon member can update
	const MIGRATION_VERSION = 'MIGRATION_VERSION'; //Store current migration version
	
	function __construct() {
		parent::init("config");
	}
	
	function __destruct() {
		parent::__destruct();
	}
	
	public function getTest()
	{
		$check = $this->getAll();
		var_dump($check);
	}
	
	public function getPhanBoCbgdDotHoc()
	{
		$check = $this->getSelect("to_date(value,'dd-mm-yyyy') pc_cbgd_dot_hoc")
		->where("name = 'PC_CBGD_DOT_HOC'")
		->execute(false, array());
		date_default_timezone_set('Asia/Ho_Chi_Minh');
		$ngayHetHan = date('d-m-Y', strtotime($check->result[0]['pc_cbgd_dot_hoc']));
		return $ngayHetHan;
	}
	
	public function getPhanBoCbgdHetHan()
	{
		$check = $this->getSelect("to_date(value,'dd-mm-yyyy') ". self::KH_KT)
		->where("name = '".self::KH_KT."'")
		->execute(false, array(),false);
		date_default_timezone_set('Asia/Ho_Chi_Minh');
		$ngayHetHan = date('d-m-Y', strtotime($check->result[0][self::KH_KT]));
		return $ngayHetHan;
	}
	
	public function checkPhanBoCbgdHetHan()
	{
		$check = $this->getSelect("to_date(value,'dd-mm-yyyy') ". self::KH_KT)
		->where("name = '".self::KH_KT."'")
		->execute(false, array(),false)
		->parse();
		 
		date_default_timezone_set('Asia/Ho_Chi_Minh');
		$ngayHetHan = strtotime($check->result[0][self::KH_KT]);
		$timeHienTai = strtotime(date('d-m-Y'));
		
		return $timeHienTai <= $ngayHetHan;
	}
	
	public function getPhanBoCbgdHetHanBoMon()
	{
		$check = $this->getSelect("to_date(value,'dd-mm-yyyy') ". self::BM_KT)
		->where("name = '".self::BM_KT."'")
		->execute(false, array(),false);
		date_default_timezone_set('Asia/Ho_Chi_Minh');
		$ngayHetHan = date('d-m-Y', strtotime($check->result[0][self::BM_KT]));
		return $ngayHetHan;
	}
	
	public function checkPhanBoCbgdHetHanBoMon()
	{
		$check = $this->getSelect("to_date(value,'dd-mm-yyyy') ". self::BM_KT)
		->where("name = '".self::BM_KT."'")
		->execute(false, array(),false)
		->parse();
		 
		date_default_timezone_set('Asia/Ho_Chi_Minh');
		$ngayHetHan = strtotime($check->result[0][self::BM_KT]);
		$timeHienTai = strtotime(date('d-m-Y'));
		
		return $timeHienTai <= $ngayHetHan;
	}
	
	public function checkInitialMigration()
	{
		$check = $this->getSelect('*')
		->where("name = '".self::MIGRATION_VERSION."'")
		->execute(false, array());
		
		if ($check->itemsCount < 1){
			echo "Insert config MIGRATION_VERSION\n";
			$data = array(	'name' => self::MIGRATION_VERSION, 
							'comments' => 'System current migration version');
			
			$this->getInsert($data)->execute(true, array());
		}
	}
	
	public function getLastVersion()
	{
		$check = $this->getSelect('*')
		->where("name = '".self::MIGRATION_VERSION."'")
		->execute(false, array());
		
		if ($check->itemsCount > 0){
			$last = $check->result[$check->itemsCount - 1];
			return $last['value'];
		}else{
			return null;
		}
	}
	
	public function updateMigrationVersion($version)
	{
		$data = array('value' => $version);
		$this->getUpdate($data)->where("name = '".self::MIGRATION_VERSION."'")->execute(true, array());
	}
}
