<?php
/**
 * 
 */
class ModulePhongbanControllerHoso extends FrontController {
	
	function __construct() {
		
	}
	
	private function initCheck() {
		if (! $this->checkLogin()) {
			die("Bạn chưa đăng nhập.");
		}
	}
	
	public	function tientrinhAction(){
		$this->initCheck();
		$makhoa = base64_decode($_SESSION['makhoa']);;
		$nam = $this->getPost('namnhan',date("Y"));
		$tinhtrang = $this->getPost('tinhtrang','');
		$title = 'Danh Sách Tình Trạng Yêu Cầu Học Vụ';
		$nhanSu = new NhanSuModel();
		
		if($nhanSu->checkRoleViewTienTrinhHoSo()){
			$template = new BaseTemplate("hocvu/list","default/index");
			$template->listNhanSuSdh = $nhanSu->getDanhSachPhongSDH();
			$template->namNhan = $nam;
			unset($nhanSu);
			
			$model = new HvuGiaiQuyetHvuModel();
			$template->listItems = $model->getDsTinhTrangHocVu($makhoa,$nam,$tinhtrang);
			
			if (count($template->listItems) == 0){
				$nam = $nam -1;
				$template->listItems = $model->getDsTinhTrangHocVu($makhoa,$nam,$tinhtrang);
			}
			
			if(isset($_POST['namnhan']) && isset($_POST['tinhtrang']) ){
				$templateContent = $template->contentTemplate();
				$template->renderLayout(array('title' => $title,'content' => $templateContent));
			}else{
				$modelHvuDmTinhTrang = new HvuDmTinhTrangModel();
				$templateMain = new BaseTemplate("hocvu/index","default/index");
				$templateMain->namNhan = $nam;
				$templateMain->listHvuDmTinhTrang = $modelHvuDmTinhTrang->getDanhMucTinhTrang();
				unset($modelHvuDmTinhTrang);
				
				$templateMain->listView = $template->contentTemplate();
				$templateContent = $templateMain->contentTemplate();
				$templateMain->renderLayout(array('title' => $title,'content' => $templateContent));
			}
			
		}else{
			unset($nhanSu);
			echo "Bạn không có quyền sử dụng chức năng này.";
		}
	}
}
