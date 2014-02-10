<?php
/**
 * 
 */
class ModuleThoikhoabieuControllerPhanbo extends FrontController {
	
	function __construct() {
		
	}
	
	public	function indexAction(){
		$template = new BaseTemplate("tkb/pageprint","default/blank");
		$template->dothoc =  $this->getParam('dothoc');
		$template->makhoa = $this->getParam('makhoa');
		
		$nhanSu = new NhanSuModel();
		$viewAll = $nhanSu->checkViewAllPhanBoCanBo();
		unset($nhansu);
		
		//get list
		$model = new ThoiKhoaBieuModel();
		$template->listItems = $model->getDanhSachMonHocPhanBo($this->getParam('dothoc'),$this->getParam('makhoa'),$viewAll,$_GET['ktbs']);
		$template->hk = $this->getGet('hk');
		$template->nbd = $this->getGet('nbd');
		$template->viewAll = $viewAll;
		 
		unset($model);
		$template->renderTemplate();
		//$templateContent = $template->contentTemplate();
		//$template->renderLayout(array('title' => '','content' => $templateContent));
	}
	
	public	function indexbomonAction(){
		$template = new BaseTemplate("tkb/pageprint_bomon","default/blank");
		$template->dothoc =  $this->getParam('dothoc');
		$template->makhoa = $this->getParam('makhoa');
		//get list
		$model = new ThoiKhoaBieuModel();
		$template->listItems = $model->getDanhSachMonHocPhanBoBoMon($this->getParam('dothoc'),$_GET['ktbs']);
		$template->hk = $this->getGet('hk');
		$template->nbd = $this->getGet('nbd');
		unset($model);
		$template->renderTemplate();
	}
	
	public	function saveAction(){
		if ($this->checkLogin() && $this->isPost()){
				//Check role
				$nhanSu = new NhanSuModel();
				if ($nhanSu->checkPhanBoCanBo()) {
					unset($nhanSu);
					//Check user can edit or not
					$config = new ConfigModel();
					$canEdit = $config->checkPhanBoCbgdHetHan();
					unset($config);
					
					$model = new ThoiKhoaBieuModel();
					
					if($canEdit || ($canEdit == false && $model->expireCheckPhanBoCanBo($this->getParam('dothoc'),$this->getParam('lop'),$this->getParam('mamh')) == true)){
						if (in_array( $this->getParam('loai'), array('0','1','2','3'))){
							$cbgd  = $this->getPost('cbgd', null) == '' ? null : $this->getPost('cbgd', null);
							$ghichu  = $this->getPost('ghichu', null) == '' ? null : $this->getPost('ghichu', null);
							$duyet  = $this->getPost('duyet', '0') == '0' ? 0 : (int) $this->getPost('duyet', '0');
							
							$check = $model->phanBoCanBo(	$this->getParam('dothoc'),
															$this->getParam('lop'),
															$this->getParam('mamh'),
															(int) $this->getParam('loai'),
															$cbgd,
															$ghichu,
															$duyet
														);
							unset($model);
							
							if ($check) {
								$this->renderJSON(array('status' => 1, 'message' => 'Dữ liệu đã được cập nhật thành công'));
							}else{
								$this->renderJSON(array('status' => 0, 'message' => 'Không có dữ liệu để cập nhật'));
							}
							
							
						}else{
							$this->renderJSON(array('status' => 0, 'message' => 'Dữ liệu cập nhật không hợp lệ'));
						}
					}else{
						$this->renderJSON(array('status' => 0, 'message' => 'Hết thời hạn được phép cập nhật dữ liệu'));
					}
				}else{
					unset($nhanSu);
					$this->renderJSON(array('status' => 0, 'message' => 'Bạn không có quyền phân bổ cán bộ giảng dạy'));
				}
		}else{
			$this->renderJSON(array('status' => 0, 'message' => 'Yêu cầu kết nối không hợp lệ'));
		}
	}
	
	public function previewAction()
	{
		$nhanSu = new NhanSuModel();
		if ($this->checkLogin() && $nhanSu->checkPhanBoCanBo() ){
			$template = new BaseTemplate("tkb/khoa/phanbocbgd","default/index");
			unset($nhanSu);
			$tkb = new ThoiKhoaBieuModel();
			$template->listMonHoc =$tkb->getListHocKy();
			unset($tkb);
			$config = new ConfigModel();
			$template->dothoc =$config->getPhanBoCbgdDotHoc();
			unset($config);
			$templateContent = $template->contentTemplate();
			$template->renderLayout(array('title' => '','content' => $templateContent));
		}
	}
	
	public function listAction()
	{
		$nhanSu = new NhanSuModel();
		
		if ($this->checkLogin() && ($nhanSu->checkPhanBoCanBo() || $nhanSu->checkViewAllPhanBoCanBo()) ){
			$template = new BaseTemplate("tkb/khoa/danhsach","default/index");
			$template->viewAll = $nhanSu->checkViewAllPhanBoCanBo();
			unset($nhanSu);
			
			
			$config = new ConfigModel();
			$template->hetHanCapNhat = $hetHanCapNhat = ! $config->checkPhanBoCbgdHetHan();
			$template->thoihan = $config->getPhanBoCbgdHetHan();
			unset($config);
			// if ($template->viewAll == false && $hetHanCapNhat) {
				// echo "Đã hết hạn cho phép phân bổ cán bộ giảng dạy";
				// return;
			// }
			
			$template->dothoc =  $this->getParam('dothoc');
			$macb = $_SESSION['macb'];
			$makhoa = base64_decode($_SESSION['makhoa']);
			$dothoc = $this->getGet('d');
			$hk = $this->getGet('h');
			$thu = array("1"=>"CN", "2"=>"Hai", "3"=>"Ba", "4"=>"Tư", "5"=>"Năm", "6"=>"Sáu", "7"=>"Bảy");
			
			$listMonHoc = new ThoiKhoaBieuModel();
			$template->listItems = $listMonHoc->getDanhSachMonHocPhanBo($dothoc,$makhoa,$template->viewAll);
			unset($listMonHoc);
			$listCanBo = new CanBoGiangDayModel();
			$template->listCanBo = $listCanBo->getCanBo();
			unset($listCanBo);
			
			
			$template->macb = $macb;
			$template->dothoc =  $dothoc;
			$template->makhoa = $makhoa;
			$template->hk = $hk;
			$template->thu = $thu;
			
			//Render content to layout
			$templateContent = $template->contentTemplate();
			$template->renderLayout(array('title' => '','content' => $templateContent));
		}else{
			unset($nhanSu);
			echo "Bạn chưa login hoặc không có quyền truy cập vào chức năng này";
		}
	}
	
	public	function savebomonAction(){
		if ($this->checkLogin() && $this->isPost()){
				//Check role
				$nhanSu = new NhanSuModel();
				if ($nhanSu->checkPhanBoCanBoBoMon()) {
					unset($nhanSu);
					//Check user can edit or not
					$config = new ConfigModel();
					$canEdit = $config->checkPhanBoCbgdHetHanBoMon();
					unset($config);
					
					$model = new ThoiKhoaBieuModel();
					
					if($canEdit || ($canEdit == false && $model->expireCheckPhanBoCanBo($this->getParam('dothoc'),$this->getParam('lop'),$this->getParam('mamh')) == true)){
						if ($this->getParam('loai') != '2' || $this->getParam('loai') != '1' || $this->getParam('loai') != '0'){
							$cbgd  = $this->getPost('cbgd', null) == '' ? null : $this->getPost('cbgd', null);
							$ghichu  = $this->getPost('ghichu', null) == '' ? null : $this->getPost('ghichu', null);
							
							$check = $model->phanBoCanBo(	$this->getParam('dothoc'),
															$this->getParam('lop'),
															$this->getParam('mamh'),
															(int) $this->getParam('loai'),
															$cbgd,
															$ghichu
														);
							unset($model);
							
							if ($check) {
								$this->renderJSON(array('status' => 1, 'message' => 'Dữ liệu đã được cập nhật thành công'));
							}else{
								$this->renderJSON(array('status' => 0, 'message' => 'Không có dữ liệu để cập nhật'));
							}
							
							
						}else{
							$this->renderJSON(array('status' => 0, 'message' => 'Dữ liệu cập nhật không hợp lệ'));
						}
					}else{
						$this->renderJSON(array('status' => 0, 'message' => 'Hết thời hạn được phép cập nhật dữ liệu'));
					}
				}else{
					unset($nhanSu);
					$this->renderJSON(array('status' => 0, 'message' => 'Bạn không có quyền phân bổ cán bộ giảng dạy'));
				}
		}else{
			$this->renderJSON(array('status' => 0, 'message' => 'Yêu cầu kết nối không hợp lệ'));
		}
	}
	
	public function previewbomonAction()
	{
		$nhanSu = new NhanSuModel();
		if ($this->checkLogin() && $nhanSu->checkPhanBoCanBoBoMon() ){
			$template = new BaseTemplate("tkb/bomon/phanbocbgd","default/index");
			unset($nhanSu);
			$tkb = new ThoiKhoaBieuModel();
			$template->listMonHoc =$tkb->getListHocKy();
			unset($tkb);
			$config = new ConfigModel();
			$template->dothoc =$config->getPhanBoCbgdDotHoc();
			unset($config);
			$templateContent = $template->contentTemplate();
			$template->renderLayout(array('title' => '','content' => $templateContent));
		}else{
			unset($nhanSu);
			echo "Bạn chưa login hoặc không có quyền truy cập vào chức năng này";
		}
	}
	
	public function listbomonAction()
	{
		$nhanSu = new NhanSuModel();
		
		if ($this->checkLogin() && $nhanSu->checkPhanBoCanBoBoMon() ){
			unset($nhanSu);
			$template = new BaseTemplate("tkb/bomon/danhsach","default/index");
			
			$config = new ConfigModel();
			$template->hetHanCapNhat = $hetHanCapNhat = ! $config->checkPhanBoCbgdHetHanBoMon();
			$template->thoihan = $config->getPhanBoCbgdHetHanBoMon();
			unset($config);
			
			// if ($hetHanCapNhat) {
				// echo "Đã hết hạn cho phép phân bổ cán bộ giảng dạy";
				// return;
			// }
			
			$template->dothoc =  $this->getParam('dothoc');
			$macb = $_SESSION['macb'];
			$makhoa = base64_decode($_SESSION['makhoa']);
			$dothoc = $this->getGet('d');
			$hk = $this->getGet('h');
			$thu = array("1"=>"CN", "2"=>"Hai", "3"=>"Ba", "4"=>"Tư", "5"=>"Năm", "6"=>"Sáu", "7"=>"Bảy");
			
			$listMonHoc = new ThoiKhoaBieuModel();
			$template->listItems = $listMonHoc->getDanhSachMonHocPhanBoBoMon($dothoc);
			unset($listMonHoc);
			$listCanBo = new CanBoGiangDayModel();
			$template->listCanBo = $listCanBo->getCanBo();
			unset($listCanBo);
			
			
			$template->macb = $macb;
			$template->dothoc =  $dothoc;
			$template->makhoa = $makhoa;
			$template->hk = $hk;
			$template->thu = $thu;
			
			//Render content to layout
			$templateContent = $template->contentTemplate();
			$template->renderLayout(array('title' => '','content' => $templateContent));
		}else{
			unset($nhanSu);
			echo "Bạn chưa login hoặc không có quyền truy cập vào chức năng này";
		}
	}
	
	public function previewallAction()
	{
		$nhanSu = new NhanSuModel();
		if ($this->checkLogin() && $nhanSu->checkViewAllPhanBoCanBo() ){
			
			$template = new BaseTemplate("tkb/khoa/phanbocbgd","default/index");
			$template->viewAll = true;
			unset($nhanSu);
			$tkb = new ThoiKhoaBieuModel();
			$template->listMonHoc =$tkb->getListHocKy();
			unset($tkb);
			$config = new ConfigModel();
			$template->dothoc =$config->getPhanBoCbgdDotHoc();
			unset($config);
			$templateContent = $template->contentTemplate();
			$template->renderLayout(array('title' => '','content' => $templateContent));
		}else{
			unset($nhanSu);
			echo "Bạn chưa login hoặc không có quyền truy cập vào chức năng này";
		}
	}
	
	public	function testAction(){
		// var_dump($this->params);
		// var_dump($this->getSession('uidloginPortal'));
		// $this->checkLogin();
		//$helper = Helper::getHelper('functions/util');
		//$url = $helper->getGvRootURL()."/front.php/tkb/phanbo/test2?hisid=".$this->getGet('hisid');
		//echo $url; 
		//die;
		//$this->redirect(200,$url);
		$test = new ConfigModel();
		$t = $test->checkPhanBoCbgdHetHan();
		var_dump($t);
		unset($test);
	}
	
	public	function test2Action(){
		echo "test2 action of ModuleThoikhoabieuControllerPhanbo";
	}
}
