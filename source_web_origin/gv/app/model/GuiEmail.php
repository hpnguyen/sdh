<?php
/**
 * 
 */
class GuiEmailModel extends BaseTable {
	
	function __construct() {
		parent::init("gui_email");
	}
	
	function __destruct() {
		parent::__destruct();
	}
	
	public function getListSendMailTkb()
	{
		$check = $this->getSelect("*")
		->where("flag <> 1")
		//->where("email = 'hpnguyen@hcmut.edu.vn'")
		->order("id asc")
		->execute(false, array());
		
		$ret = array();
		if($check->itemsCount > 0){
			$ret = $check->result;
		}
		return $ret;
	}
	
	public function updateSendMailStatusForTkb($id)
	{
		$data = array('flag' => 1);
		$this->getUpdate($data)->where("id = ".$id)->execute(true, array());
	}
}
