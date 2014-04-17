<?php
/**
 * 
 */
class ModuleVtvlControllerIndex extends FrontController {
		
	function __construct() {
		
	}
	
	private function initCheck($json = false) {
		if (! $this->checkLogin()){
			$text = 'Bạn chưa đăng nhập để sử dụng chức năng này.';
			if ($json == false){
				die($text);
			}else{
				$this->renderJSON(array('status' => 0, 'message' => $text));
				return;
			}
		}
	}
	
	public	function indexAction(){
		$this->initCheck();
		
		$view = $this->getGet('f',null);
		
		switch ($view) {
			case '6A':
				$this->renderViewForm6A();
				break;
			case '6C':
				$this->renderViewForm6C();
				break;
			default:
				break;
		}
	}
	
	public	function saveAction(){
		$this->initCheck();
		
		$view = $this->getGet('f',null);
		switch ($view) {
			case '6A':
				$this->saveForm6A();
				break;
			case '6C':
				$this->saveForm6C();
				break;
			default:
				break;
		}
	}
	
	private	function renderViewForm6A(){
		$template = new BaseTemplate("vtvl/index6A","default/index");
		$template->data = 'Data set by template 6A';
		$templateContent = $template->contentTemplate();
		
		if(isset($_GET['pdf'])){
			$mpdf=new mPDF('utf-8','A4');
			$mpdf->SetTitle('Phụ Lục Số 6A');
			$mpdf->SetAutoFont();
			$mpdf->forcePortraitHeaders = true;
			$mpdf->WriteHTML($templateContent);
			$mpdf->Output($macb.'_vtvl_phu_luc_so_6A.pdf','I');
		}else{
			$template->renderLayout(array('title' => '','content' => $templateContent));
		}
	}
	
	private	function saveForm6A(){
		$this->renderJSON(array('status' => 1, 'message' => 'Cập nhật dữ liệu thành công.'));
	}
	
	private	function renderViewForm6C(){
		$macb = $_SESSION['macb'];
		$template = new BaseTemplate("vtvl/index6C","default/index");
		$templateContent = $template->contentTemplate();
		
		if(isset($_GET['pdf'])){
			$mpdf=new mPDF('utf-8','A4');
			$mpdf->SetTitle('Phụ Lục Số 6C');
			$mpdf->SetAutoFont();
			$mpdf->forcePortraitHeaders = true;
			$mpdf->WriteHTML($templateContent);
			$mpdf->Output($macb.'_vtvl_phu_luc_so_6C.pdf','I');
		}else{
			$template->renderLayout(array('title' => '','content' => $templateContent));
		}
	}
	
	private	function saveForm6C(){
		$this->renderJSON(array('status' => 1, 'message' => 'Cập nhật dữ liệu thành công.'));
	}
}
