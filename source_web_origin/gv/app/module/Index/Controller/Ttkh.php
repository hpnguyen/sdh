<?php
/**
 * 
 */
class ModuleIndexControllerTtkh extends FrontController {
	
	function __construct() {
		
	}
	
	private function initCheck() {
		if (! $this->checkLogin()) {
			die("Bạn chưa đăng nhập.");
		}
	}
	
	public function indexAction(){
		$this->initCheck();
		$macb = $_SESSION['macb'];
		
		$modelCbgdDetail = new CanBoGiangDayModel();
		$listCbgd = $modelCbgdDetail->getDetailForTtkh($macb);
		unset($modelCbgdDetail);
		
		$template = new BaseTemplate("khcn/print/khcn_print_ttkh","default/index");
		$template->cbgd = $listCbgd;
		$templateContent = $template->contentTemplate();
		
		if(isset($_GET['pdf'])){
			$mpdf=new mPDF('utf-8','A4');
			$mpdf->SetTitle('Thông Tin Khoa Học');
			$mpdf->SetAutoFont();
			$mpdf->forcePortraitHeaders = true;
			// Define the Header/Footer before writing anything so they appear on the first page
			$mpdf->SetHTMLHeader('
			<table width="100%" style="vertical-align: bottom; font-family: serif; font-size: 6pt; color: #000000; font-style: italic; border-bottom: #000000 solid 0.5px"><tr>
			<td width="33%"></td>
			<td width="33%" align="center"></td>
			<td width="33%" style="text-align: right;">Thông Tin Khoa Học</td>
			</tr></table>');
			$mpdf->SetHTMLFooter('<table width="100%" style="vertical-align: bottom; font-family: serif; font-size: 6pt; color: #000000; font-style: italic; border-top: #000000 solid 0.5px"><tr>
			<td width="33%"><span style="font-style: italic;">{DATE d-m-Y H:i}</span></td>
			<td width="33%" align="center"></td>
			<td width="33%" style="text-align: right; font-style: italic;">Trang {PAGENO}/{nbpg}</td>
			</tr></table>');
			$mpdf->WriteHTML($templateContent);
			$mpdf->Output(str_replace(array(' ','.'),array('_','') , $listCbgd["hotencb"]).'_ttkh.pdf','I');
		}else{
			$template->renderLayout(array('title' => '','content' => $templateContent));
		}
	}
}
