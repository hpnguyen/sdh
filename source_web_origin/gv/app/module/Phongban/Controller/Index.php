<?php
/**
 * 
 */
class ModulePhongbanControllerIndex extends FrontController {
	
	function __construct() {
		
	}
	
	public	function saveAction(){
		//var_dump(DbFactory::getInstance());
		// $model = new ThoiKhoaBieuModel();
		//var_dump($model);
		
		//var_dump($model->getFirst());
		
		$model = new TableTestModel();
		//$model->getInsert(array('id_nguyen' => 20))->execute();
		//$model->getUpdate(array('id_nguyen' => 21))->where('id_nguyen = 18')->execute();
		//var_dump($model->getAll());
		unset($model);
		//var_dump(DbFactory::getInstance());
		// $template = new BaseTemplate('default/index','default/index');
		// $template->content = '';
		// $template->params = $this->params;
		// $cont = $template->contentTemplate();
		// $template->renderLayout(array('cont' => $cont));
	}
}
