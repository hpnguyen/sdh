<?php
/**
 * 
 */
class ModuleTestControllerTest extends FrontController {
	
	function __construct() {
		
	}
	
	public	function indexAction(){
		// $model = new ConfigModel();
		// var_dump($model->getTest());
		$template = new BaseTemplate("simple/index","default/index");
		$template->year = 2013;
		$template->message = 'Test function';
		$templateContent = $template->contentTemplate();
		$template->renderLayout(array('title' => '','content' => $templateContent));
	}
	
	public	function pdfAction(){
		$template = new BaseTemplate("simple/index","default/index");
		$template->year = 2013;
		$template->message = 'Test function';
		$templateContent = $template->contentTemplate();
		
		$mpdf=new mPDF('utf-8','A4'); 
		$mpdf->SetAutoFont();
		$mpdf->forcePortraitHeaders = true;
		$mpdf->WriteHTML($templateContent);
		$mpdf->Output();
		exit;
		die;
	}
}
