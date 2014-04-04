<?php
/**
 * 
 */
class DotHocNamHocKyModel extends BaseTable {
	
	function __construct() {
		parent::init("dot_hoc_nam_hoc_ky");
	}
	
	function __destruct() {
		parent::__destruct();
	}
	
	public function getListDotHocKlgd($dothoc)
	{
		$check = $this->getSelect("(hoc_ky || '/' || nam_hoc_tu || '-' || nam_hoc_den) nam_hoc, dot_hoc, 
		CASE WHEN dot_hoc = '".$dothoc."' THEN 'selected' ELSE '' END as selected")
		->where("dot_hoc in (SELECT distinct dot_hoc FROM view_klgd)")
		->order("dot_hoc desc")
		->execute(false, array());
		
		$ret = array();
		
		if($check->itemsCount > 0){
			$ret = $check->result;
		}
		return $ret;
		
		return $check->itemsCount > 0;
	}
}
