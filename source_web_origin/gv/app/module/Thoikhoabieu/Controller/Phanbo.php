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
		$viewAllKhoa = $nhanSu->checkKhoaViewTKB();
		unset($nhansu);
		
		//get list
		$model = new ThoiKhoaBieuModel();
		if($viewAllKhoa){
			$viewAll = true;
			$template->listItems = $model->getDanhSachMonHocPhanBo($this->getParam('dothoc'),$this->getParam('makhoa'),false,$_GET['ktbs']);
		}else{
			$template->listItems = $model->getDanhSachMonHocPhanBo($this->getParam('dothoc'),$this->getParam('makhoa'),$viewAll,$_GET['ktbs']);
		}
		
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
		$khoaViewTKB = $nhanSu->checkKhoaViewTKB();
		if ($this->checkLogin() && ($nhanSu->checkPhanBoCanBo() || $nhanSu->checkViewAllPhanBoCanBo() || $khoaViewTKB) ){
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
			
			if($khoaViewTKB){
				$template->viewAll = true;
			}
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
		if ($this->checkLogin() && ($nhanSu->checkViewAllPhanBoCanBo() || $nhanSu->checkKhoaViewTKB())){
			
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
		// $t = new ConfigModel();
		// var_dump($t->checkTableColumnExist('value1111'));
		// die;
		$model = new EmailTemplateModel();
		
		$ret = $model->getMailTemplate('gui_thong_bao_tkb');
		
		var_dump($ret);
		die;
		$model = new EmailTemplateModel();
		$model->migrateUp();
		// Get comment of class
		// $rc = new ReflectionClass('EmailTemplateModel');
		// $text = $rc->getDocComment();
		// $t1 = explode('@param', $text);
		// var_dump($t1);
		// die;
		$content = file_get_contents('/home/hpnguyen/Working/svn_repository_source/gvbeta/app/template/view/mail/tkb.php');
		
		$p = new PhpStringParser(array());
		$content =  $p->parse($content);
		
		$template = new BaseTemplate("mail/tkb","default/index");
		$contentHTML = $template->contentTemplate();
		
		$data = array(
			'id' => 'gui_thong_bao_tkb',
			'title' => 'Thông báo đã có thời khóa biểu giảng dạy',
			'content' => $contentHTML
		);
		
		$model = new EmailTemplateModel();
		
		$ret = $model->getMailTemplate('gui_thong_bao_tkb');
		
		// $t = $model->checkTableExist();
		// $model->getTableColumns();
		// var_dump($t);
		die;
		//$model->deleteTemplate('gui_thong_bao_tkb');
		//die;
		
		//$model->migrateDown();
		//$model->migrateUp();		
		$ret = $model->checkTemplateThongBaoTkb($data);
		var_dump($ret);
		/*
		//$contentHTML = file_get_contents(ROOT_DIR.'app/libs/PHPMailer/examples/contents.html');
		$template = new BaseTemplate("mail/tkb","default/index");
		$template->hocky = "2/2013-2014";
		$contentHTML = $template->contentTemplate();
		$subject = "Thông báo đã có thời khóa biểu giảng dạy học kỳ ".$template->hocky;
		$recipients = array(	array('hpnguyen@hcmut.edu.vn', 'Phu Nguyen'),
				array('taint@hcmut.edu.vn', 'Ngo trung Tai'),
				array('nttvi@hcmut.edu.vn', 'Nguyen Thi Tuong Vi')
		);
		//$attach = ROOT_DIR.'app/libs/PHPMailer/examples/images/phpmailer_mini.gif';
		$attach = null;
		Helper::getHelper('functions/mail')->sendMail($subject, $contentHTML, $recipients, null, null, $attach, null, 0);
		*/
	}
}
