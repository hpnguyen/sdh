<?php
/**
 * 
 */
class MigrationVersionModel extends BaseTable {
	
	function __construct() {
		parent::init("migration_version");
		
	}
	
	function __destruct() {
		parent::__destruct();
	}
	
	public function getLastVersion()
	{
		$check = $this->getSelect('*')
		->execute(false, array());
		
		if ($check->itemsCount > 0){
			$last = $check->result[$check->itemsCount - 1];
			return $last['version'];
		}else{
			return null;
		}
	}
}
