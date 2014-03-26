<?php
/**
 * 
 */
class ModulePhongbanControllerKlgd extends FrontController {
	
	function __construct() {
		
	}
	
	private function initCheck() {
		if (! $this->checkLogin()) {
			die("Bạn chưa đăng nhập.");
		}
	}
	
	public	function indexAction(){
		$this->initCheck();
		//Check ROLE
		$modelNhanSu = new NhanSuModel();
		$checkRoleKhoa = $modelNhanSu->checkRoleViewKlgdKhoa();
		$checkRoleBoMon = $modelNhanSu->checkRoleViewKlgdBoMon();
		
		if ($checkRoleKhoa == false && $checkRoleBoMon == false){
			die("Bạn không được phép sử dụng chức năng này.");
		}
		
		$postData = $this->getPosts();
		$getsData = $this->getGets();
		
		if(isset($getsData['print'])){
			$templateFileName = "klgd/index_print";
		}else{
			$templateFileName = "klgd/index";
		}
		$template = new BaseTemplate($templateFileName,"default/blank");
		
		$template->formKey ='bo_mon_khoa_klgd';
		
		$template->viewSelectBoMon = $checkRoleKhoa;
		
		$template->maKhoa = base64_decode($_SESSION['makhoa']);
		
		$template->maBoMon = null;
		if($template->viewSelectBoMon == false){
			$modelCanBoGiangDay = new CanBoGiangDayModel();
			$template->maBoMon = $modelCanBoGiangDay->getUserLoginMaBoMon();
		}else{
			if ($postData == null && ! isset($getsData['bm'])){
				$modelCanBoGiangDay = new CanBoGiangDayModel();
				$template->maBoMon = $modelCanBoGiangDay->getUserLoginMaBoMon();
			}else if(isset($postData['bo_mon'])){
				$template->maBoMon = $postData['bo_mon'];
			}else if (isset($getsData['bm'])) {
				$template->maBoMon = $getsData['bm'];
			}
		}
		
		$modelDotHocNamHocKy = new DotHocNamHocKyModel();
		
		$template->dotHoc = null;
		$template->namHoc = null;
		if ($postData != null && isset($postData['dot_hoc'])){
			$template->dotHoc = $postData['dot_hoc'];
		}else if(isset($getsData['dh'])){
			$template->dotHoc = $getsData['dh'];
		}
		
		$template->listItemsDotHoc = $modelDotHocNamHocKy->getListDotHocKlgd($template->dotHoc);
		
		if ($postData == null && (! isset($getsData['dh']) && ! isset($getsData['bm']))){
			$firstRow = $template->listItemsDotHoc[0];
			$template->dotHoc = $firstRow['dot_hoc'];
			$template->namHoc = $firstRow['nam_hoc'];
		}else{
			foreach ($template->listItemsDotHoc as $k => $t) {
				if ($t['dot_hoc'] == $template->dotHoc){
					$template->namHoc = $t['nam_hoc'];
					break;
				}
			}
		}
		
		$model = new BoMonModel();
		$template->listItemsBoMon = $model->getListBomonKlgd($template->maKhoa,$template->maBoMon);
		
		$modelViewKlgd = new ViewKlgdModel();
		
		$template->listItems = $modelViewKlgd->getListKlgdByKhoaBomon($template->maKhoa, $template->dotHoc,$template->maBoMon);
		
		$templateContent = $template->contentTemplate();
		
		if (isset($_GET['print'])){
			$mpdf=new mPDF('',// mode - default ''
				'',    // format - A4, for example, default ''
				12,     // font size - default 0
				'',    // default font family
				10,    // margin_left
				10,    // margin right
				16,    // margin top
				16,    // margin bottom
				9,     // margin header
				9,     // margin footer
				'P'    // L - landscape, P - portrait
			);  
			$mpdf->SetTitle('Khối Lượng Giảng Dạy Cao Học');
			$mpdf->SetAutoFont();
			$mpdf->forcePortraitHeaders = true;
			$mpdf->WriteHTML($templateContent);
			$mpdf->Output();
			exit;
		}else{
			$template->renderLayout(array('title' => $title,'content' => $templateContent));
		}
	}
}
