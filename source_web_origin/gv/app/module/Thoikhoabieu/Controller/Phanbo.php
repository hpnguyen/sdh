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
							$ghichu = str_replace("'", "''", $ghichu);
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
							$ghichu = str_replace("'", "''", $ghichu);
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
		// $test = new ConfigModel();
		// $t = $test->checkPhanBoCbgdHetHan();
		// var_dump($t);
		// unset($test);
		//var_dump(888888888);
		
		if (isset($_GET["hisid"])){
			session_id($_GET["hisid"]);
			session_start();
		}
		
		if (!isset($_SESSION['uidloginPortal'])){
			die('Đã hết phiên làm việc');
		}
		
		$macb = $_GET['m'];
		$a = $_GET['a'];
		$key = $_GET["k"];
		$madetai = $_GET["mdt"];
		
		if ($macb == '') {
			$macb = $_SESSION['macb'];
		}
		$template = new BaseTemplate("khcn/print/khcn_print_danh_gia_tmdt_m06_pdf","default/index");
		$template->macb = $macb;
		$template->a = $a;
		$template->key = $key;
		
		$modelCbgd = new CanBoGiangDayModel();
		$template->detailCbgd = $modelCbgd->getDetailForThuyetMinhDeTai($macb);
		
		$modelTmdt = new NckhThuyetMinhDeTaiModel();
		$macb = $_SESSION['macb'];
		$template->detailTmdt = $modelTmdt->getDetailForPrintPdf($madetai,$macb);
		// $rowNckhNoiDungKinhPhi =$template->detailTmdt['join_tables']['nckh_pb_noi_dung_kinh_phi'];
		// var_dump($template->detailTmdt);
		$templateContent = $template->contentTemplate();
		
		$mpdf=new mPDF('utf-8','A4'); 
		$mpdf->SetAutoFont();
		$mpdf->forcePortraitHeaders = true;
		$mpdf->WriteHTML($templateContent);
		$mpdf->Output();
		exit;
		die;
		$template->renderLayout(array('title' => '','content' => $templateContent));
	}
	
	private function register(){
		$error_msg = "";
		if (isset($_POST['username'], $_POST['email'], $_POST['p'])) {
			var_dump($_POST['username'], $_POST['email'], $_POST['p']);
			
			// Sanitize and validate the data passed in
			$username = filter_input(INPUT_POST, 'username', FILTER_SANITIZE_STRING);
			
			$email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL);
			
			$email = filter_var($email, FILTER_VALIDATE_EMAIL);
			
			if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
				// Not a valid email
				$error_msg .= '
				<p class="error">
					The email address you entered is not valid
				</p>';
			}
			
			$password = filter_input(INPUT_POST, 'p', FILTER_SANITIZE_STRING);
			
			if (strlen($password) != 128) {
				// The hashed pwd should be 128 characters long.
				// If it's not, something really odd has happened
				$error_msg .= '
				<p class="error">
					Invalid password configuration.
				</p>';
			}
			
			// Username validity and password validity have been checked client side.
			// This should should be adequate as nobody gains any advantage from
			// breaking these rules.
			//
			
			// die;
			// $prep_stmt = "SELECT id FROM members WHERE email = ? LIMIT 1";
			// $stmt = $mysqli->prepare($prep_stmt);
// 			
			// if ($stmt) {
				// $stmt->bind_param('s', $email);
				// $stmt->execute();
				// $stmt->store_result();
// 			
				// if ($stmt->num_rows == 1) {
					// // A user with this email address already exists
					// $error_msg .= '
					// <p class="error">
						// A user with this email address already exists.
					// </p>';
				// }
			// } else {
				// $error_msg .= '
				// <p class="error">
					// Database error
				// </p>';
			// }
			
			// TODO:
			// We'll also have to account for the situation where the user doesn't have
			// rights to do registration, by checking what type of user is attempting to
			// perform the operation.
			
			if (empty($error_msg)) {
			// Create a random salt
				$random_salt = hash('sha512', uniqid(openssl_random_pseudo_bytes(16), TRUE));
				
				// Create salted password
				$password = hash('sha512', $password . $random_salt);
				
				var_dump(5555,$password,$random_salt,$password);
				
				
				// // Insert the new user into the database
				// if ($insert_stmt = $mysqli->prepare("INSERT INTO members (username, email, password, salt) VALUES (?, ?, ?, ?)")) {
					// $insert_stmt->bind_param('ssss', $username, $email, $password, $random_salt);
					// // Execute the prepared query.
					// if (! $insert_stmt->execute()) {
						// header('Location: ../error.php?err=Registration failure: INSERT');
					// }
				// }
				// header('Location: ./register_success.php');
			}
			//For test 
			die;
		}
		
	}
	
	// public	function test2Action(){
		// $model = new NckhChuyenGiaTmdtModel();
		// $k = $model->getList();
		// var_dump($k);
	// }
	// public	function test2Action(){
		// // $model = new LoginAttemptsModel();
		// //$model = new UserMembersModel();
		// // $model->migrateUp();
		// //$model->migrateDown();
		// // $username = 'test_user2';
		// // $email = 'test2@example.com';
		// // $password = '00807432eae173f652f2064bdca1b61b290b52d40e429a7d295d76a71084aa96c0233b82f1feac45529e0726559645acaed6f3ae58a286b9f075916ebf66cacc';
// // 		
		// // $model->addNew($username, $email, $password);
		// // die;
// 		
		// // $this->register();
		// // $template = new BaseTemplate("test/index","default/index");
		// // $contentHTML = $template->contentTemplate();
		// // $template->renderLayout(array('title' => '','content' => $contentHTML));
		// $template = new BaseTemplate("login/protected","default/blank");
		// $template->renderTemplate();
	// }
	// public	function test2Action(){
		// echo "test2 action of ModuleThoikhoabieuControllerPhanbo";
// 		
		// $helper = Helper::getHelper('functions/queuetask');
		// $helper->add('exampleFunctionTest1');
// 		
		// $helper->add('exampleFunctionTest2',array('test 1111','test 2222'));
		// var_dump(11111111);
// 		
		// //$helper->executeTaskByID(1);
		// //$helper->executeTaskByID(2);
		// die;
		// // $t = new ConfigModel();
		// // var_dump($t->checkTableColumnExist('value1111'));
		// // die;
		// $model = new EmailTemplateModel();
// 		
		// $ret = $model->getMailTemplate('gui_thong_bao_tkb');
// 		
		// var_dump($ret);
		// die;
		// $model = new EmailTemplateModel();
		// $model->migrateUp();
		// // Get comment of class
		// // $rc = new ReflectionClass('EmailTemplateModel');
		// // $text = $rc->getDocComment();
		// // $t1 = explode('@param', $text);
		// // var_dump($t1);
		// // die;
		// $content = file_get_contents('/home/hpnguyen/Working/svn_repository_source/gv/app/template/view/mail/tkb.php');
// 		
		// $p = new PhpStringParser(array());
		// $content =  $p->parse($content);
// 		
		// $template = new BaseTemplate("mail/tkb","default/index");
		// $contentHTML = $template->contentTemplate();
// 		
		// $data = array(
			// 'id' => 'gui_thong_bao_tkb',
			// 'title' => 'Thông báo đã có thời khóa biểu giảng dạy',
			// 'content' => $contentHTML
		// );
// 		
		// $model = new EmailTemplateModel();
// 		
		// $ret = $model->getMailTemplate('gui_thong_bao_tkb');
// 		
		// // $t = $model->checkTableExist();
		// // $model->getTableColumns();
		// // var_dump($t);
		// die;
		// //$model->deleteTemplate('gui_thong_bao_tkb');
		// //die;
// 		
		// //$model->migrateDown();
		// //$model->migrateUp();		
		// $ret = $model->checkTemplateThongBaoTkb($data);
		// var_dump($ret);
		// /*
		// //$contentHTML = file_get_contents(ROOT_DIR.'app/libs/PHPMailer/examples/contents.html');
		// $template = new BaseTemplate("mail/tkb","default/index");
		// $template->hocky = "2/2013-2014";
		// $contentHTML = $template->contentTemplate();
		// $subject = "Thông báo đã có thời khóa biểu giảng dạy học kỳ ".$template->hocky;
		// $recipients = array(	array('hpnguyen@hcmut.edu.vn', 'Phu Nguyen'),
				// array('taint@hcmut.edu.vn', 'Ngo trung Tai'),
				// array('nttvi@hcmut.edu.vn', 'Nguyen Thi Tuong Vi')
		// );
		// //$attach = ROOT_DIR.'app/libs/PHPMailer/examples/images/phpmailer_mini.gif';
		// $attach = null;
		// Helper::getHelper('functions/mail')->sendMail($subject, $contentHTML, $recipients, null, null, $attach, null, 0);
		// */
	// }
	public	function test2Action(){
		echo 44444444444444444444;
		
		$emailTemplateModel = new EmailTemplateModel();
		$ret = $emailTemplateModel->getMailTemplate('gui_thong_bao_tkb');
		//Get email template
		$contentHTML = $ret['content'];
		
		//cr$ret eate file pdf email
		
		// $mpdf=new mPDF('utf-8','A4'); 
		// $mpdf->SetAutoFont();
		// $mpdf->forcePortraitHeaders = true;
		// $mpdf->WriteHTML($contentHTML);
		// $mpdf->Output(ROOT_DIR.'app/logs/files/thong_bao.pdf','F');
		// die;
		//******************************************************************
		//Get config data from file
		$config = Helper::getHelper('functions/util')->getDbFileConfig();
		//Mail subject
		$subject = $ret['title'];
		//CC email
		$ccList = array(array($config['mail_tkb_cc']));
		
		$attach = $emailTemplateModel->emailTemplateFilePathOfThongBao;	
		
		
		echo "[id,email] = [".$rowID.",".$email."]\n";
		$recipients = array(array('hpnguyen@hcmut.edu.vn'));
		//Send mail
		$ret = Helper::getHelper('functions/mail')->sendMail($subject, $contentHTML, $recipients, null, null, $attach, null, 0, false);
		
		//Echo the result
		echo $ret['message'];
		if ($ret['status']) {
			echo "Send mail success";
			//Set table gui_email status to success
			
		}else{
			echo "Send mail unsuccess";
		}
		
		die;
		
		$model = new NckhPbNoiDungModel();
		$check = $model->getListToFixInvalidCharacter();
		$help = Helper::getHelper('functions/util');
		while ($check != null) {
			$row = $check[0];
			$row['a1_tam_quan_trong'] = $help->escapeSpecialCharToHtmlCode($row['a1_tam_quan_trong']);
			$row['a1_tam_quan_trong'] = $help->trimSlashSpecialChar($row['a1_tam_quan_trong']);
			
			$row['a2_chat_luong_nc'] =  $help->escapeSpecialCharToHtmlCode($row['a2_chat_luong_nc']);
			$row['a2_chat_luong_nc'] =  $help->trimSlashSpecialChar($row['a2_chat_luong_nc']);
			
			$row['a3_nlnc_csvc'] =  $help->escapeSpecialCharToHtmlCode($row['a3_nlnc_csvc']);
			$row['a3_nlnc_csvc'] =  $help->trimSlashSpecialChar($row['a3_nlnc_csvc']);
			
			$row['a4_kinh_phi_nx'] =  $help->escapeSpecialCharToHtmlCode($row['a4_kinh_phi_nx']);
			$row['a4_kinh_phi_nx'] =  $help->trimSlashSpecialChar($row['a4_kinh_phi_nx']);
			
			$row['c_ket_luan'] =  $help->escapeSpecialCharToHtmlCode($row['c_ket_luan']);
			$row['c_ket_luan'] =  $help->trimSlashSpecialChar($row['c_ket_luan']);
			
			$model->doCreateUpdate($row);
			$check = $model->getListToFixInvalidCharacter();
		}
		var_dump($check);
		die;
		// $url ='http://sdh.localhost.com/gv/khcn/khcn_print_tmdt_r01_test.php?a=print_tmdt_fromtab&hisid=semdt72aibhj64o37qn3das300&m=20140006&k=xemPhanBienDeTaiListprint_tmdt_20140006';
		//$url = 'http://sdh.localhost.com/gv/khcn/khcn_print_danh_gia_tmdt_m01.php?a=print_tmdt_pdf&hisid=semdt72aibhj64o37qn3das300&mdt=20140006&mcb=0.1838&k=';
		//$url = 'http://sdh.localhost.com/gv/khcn/khcn_print_danh_gia_tmdt_m01_pdf.php?a=print_tmdt_pdf&hisid=semdt72aibhj64o37qn3das300&mdt=20140006&mcb=0.1838&k=';
		//$url = 'http://sdh.localhost.com/gv/front.php/tkb/phanbo/test/?a=print_tmdt_pdf&hisid=semdt72aibhj64o37qn3das300&mdt=20140006&mcb=0.1838&k=';
		$url = 'http://sdh.localhost.com/gv/front.php/tkb/phanbo/test';
		$ch = curl_init();
		$timeout = 400;
		curl_setopt($ch, CURLOPT_URL,$url);
		curl_setopt($ch, CURLOPT_USERAGENT, "Mozilla/4.0 (compatible; MSIE 8.0; Windows NT 6.0)");
		//curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 0);
		curl_setopt($ch, CURLOPT_SSL_VERIFYHOST,false);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER,false);
		curl_setopt($ch, CURLOPT_MAXREDIRS, 10);
		//curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
		curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 0);
		curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $timeout);
		//curl_setopt($ch, CURLOPT_STDERR, fopen('php://output', 'w+'));
		//curl_setopt($ch, CURLOPT_VERBOSE, true);
		curl_setopt($ch, CURLOPT_VERBOSE, 1);
		
		
		
		// curl_setopt($ch, CURLOPT_POST, 1);
		// curl_setopt($ch, CURLOPT_POSTFIELDS,$text);
		
		// in real life you should use something like:
		// curl_setopt($ch, CURLOPT_POSTFIELDS,
			// http_build_query(
				// array('a' => 'getmotanghiencuu',
					// 'hisid' => $_GET['hisid'],
					// 'm' => 20140006
				// )
		// ));
		
		
		
		
		// receive server response ...
		
		$server_output = curl_exec($ch);
		
		curl_close ($ch);
		echo $server_output;
	}
	// public	function test2Action(){
		// echo "test2 action of ModuleThoikhoabieuControllerPhanbo";
		// //$contentHTML = file_get_contents(ROOT_DIR.'app/libs/PHPMailer/examples/contents.html');
		// $template = new BaseTemplate("test/mpdf_1","default/index");
		// $contentHTML1 = $template->contentTemplate();
		// $mpdf=new mPDF('utf-8','A4'); 
		// $mpdf->SetAutoFont();
		// $mpdf->forcePortraitHeaders = true;
		// $mpdf->WriteHTML($contentHTML1);
// 		
		// $template = new BaseTemplate("test/mpdf_2","default/index");
		// $contentHTML2 = $template->contentTemplate();
		// $mpdf->WriteHTML($contentHTML2);
// 		
		// //$mpdf->WriteHTML('<pagebreak orientaion="landscape" />');
		// $mpdf->WriteHTML('<pagebreak sheet-size="A4-L" />');
		// // $mpdf->WriteHTML('<pagebreak sheet-size="A4" />');
		// //$mpdf->WriteHTML('<tocpagebreak sheet-size="A4-L" toc-sheet-size="A4" toc-preHTML="This ToC should print on an A5 sheet" />');
		// $template = new BaseTemplate("test/mpdf_3","default/index");
		// $contentHTML3 = $template->contentTemplate();
		// $mpdf->WriteHTML($contentHTML3);
		// //$mpdf->WriteHTML('<tocentry content="A4 landscape" />');
// 		
		// $mpdf->WriteHTML('<pagebreak sheet-size="A4" />');
		// //$mpdf->WriteHTML('<tocpagebreak sheet-size="A4" toc-sheet-size="A4" toc-preHTML="This ToC should print on an A5 sheet" />');
		// $template = new BaseTemplate("test/mpdf_4","default/index");
		// $contentHTML4 = $template->contentTemplate();
		// $mpdf->WriteHTML($contentHTML4);
		// //$mpdf->WriteHTML('<tocentry content="A4 portrait" />');
// 		
		// $mpdf->Output();
		// exit;
		// die;
		// echo "hahahah";
		// //var_dump($contentHTML);
		// // $text = "a=getmotanghiencuu&hisid=".$_GET['hisid']."&m=20140006";
		// $url = 'http://sdh.localhost.com/gv/khcn/khcn_print_danh_gia_tmdt_m01.php?a=print_tmdt_pdf&hisid='.$_GET['hisid'].'&mdt=20140006&mcb=0.1838&k=';
		// $url = 'http://172.28.40.188/gv/khcn/khcn_print_danh_gia_tmdt_m01.php?a=print_tmdt_pdf&hisid=6meigtq68p79i14vc11snupih2&mdt=20140006&mcb=0.1838&k=';
		// $url = 'http://sdh.localhost.com/gv/front.php/tkb/phanbo/test';
		// $url = 'http://172.28.40.188/gv/khcn/khcn_print_danh_gia_tmdt_m01_pdf.php?a=print_tmdt_pdf&hisid=6meigtq68p79i14vc11snupih2&mdt=20140006&mcb=0.1838&k=';
		// $url = 'http://sdh.localhost.com/gv/khcn/khcn_print_tmdt_r01.php?a=print_tmdt_fromtab&hisid=semdt72aibhj64o37qn3das300&m=20140006&k=xemPhanBienDeTaiListprint_tmdt_20140006';
		// //$homepage = file_get_contents($url);
// 		
		// $ch = curl_init();
		// // curl_setopt($ch, CURLOPT_URL,"http://sdh.localhost.com/gv/khcn/khcn_thuyetminhdtkhcn_process.php");
		// $timeout = 400;
		// curl_setopt($ch, CURLOPT_URL,$url);
		// curl_setopt($ch, CURLOPT_USERAGENT, "Mozilla/4.0 (compatible; MSIE 8.0; Windows NT 6.0)");
		// curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
  		// curl_setopt($ch, CURLOPT_SSL_VERIFYHOST,false);
  		// curl_setopt($ch, CURLOPT_SSL_VERIFYPEER,false);
  		// curl_setopt($ch, CURLOPT_MAXREDIRS, 10);
  		// curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
  		// curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $timeout);
//   		
		// // curl_setopt($ch, CURLOPT_POST, 1);
		// // curl_setopt($ch, CURLOPT_POSTFIELDS,$text);
// 		
		// // in real life you should use something like:
		// // curl_setopt($ch, CURLOPT_POSTFIELDS,
			// // http_build_query(
				// // array('a' => 'getmotanghiencuu',
					// // 'hisid' => $_GET['hisid'],
					// // 'm' => 20140006
				// // )
		// // ));
// 		
// 		
// 		
// 		
		// // receive server response ...
// 		
		// $server_output = curl_exec($ch);
// 		
		// curl_close ($ch);
// 		
		// // further processing ....
		// // var_dump($server_output );
		// $mpdf=new mPDF('utf-8','A4'); 
// 		
		// $mpdf->WriteHTML($server_output);
		// $mpdf->Output();
		// exit;
		// die;
		// $hhtml = '
		// <htmlpageheader name="myHTMLHeaderOdd" style="display:none">
		// <div style="background-color:#BBEEFF" align="center"><b>&nbsp;{PAGENO}&nbsp;</b></div>
		// </htmlpageheader>
		// <htmlpagefooter name="myHTMLFooterOdd" style="display:none">
		// <div style="background-color:#CFFFFC" align="center"><b>&nbsp;{PAGENO}&nbsp;</b></div>
		// </htmlpagefooter>
		// <sethtmlpageheader name="myHTMLHeaderOdd" page="O" value="on" show-this-page="1" />
		// <sethtmlpagefooter name="myHTMLFooterOdd" page="O" value="on" show-this-page="1" />
		// ';
// 		
		// //==============================================================
		// $html = '
		// <h1>mPDF Page Sizes</h1>
		// <h3>Changing page (sheet) sizes within the document</h3>
		// ';
		// //==============================================================
		// //==============================================================
// 		
// 		
		// $mpdf=new mPDF('c','A4'); 
// 		
		// $mpdf->WriteHTML($hhtml);
// 		
		// $mpdf->WriteHTML($html);
		// $mpdf->WriteHTML('<p>This should print on an A4 (portrait) sheet</p>');
// 		
		// $mpdf->WriteHTML('<tocpagebreak sheet-size="A4-L" toc-sheet-size="A5" toc-preHTML="This ToC should print on an A5 sheet" />');
		// $mpdf->WriteHTML($html);
		// $mpdf->WriteHTML('<tocentry content="A4 landscape" /><p>This page appears just after the ToC and should print on an A4 (landscape) sheet</p>');
// 		
		// $mpdf->WriteHTML('<pagebreak sheet-size="A5-L" />');
		// $mpdf->WriteHTML($html);
		// $mpdf->WriteHTML('<tocentry content="A5 landscape" /><p>This should print on an A5 (landscape) sheet</p>');
// 		
		// $mpdf->WriteHTML('<pagebreak sheet-size="Letter" />');
		// $mpdf->WriteHTML($html);
		// $mpdf->WriteHTML('<tocentry content="Letter portrait" /><p>This should print on an Letter sheet</p>');
// 		
		// $mpdf->WriteHTML('<pagebreak sheet-size="150mm 150mm" />');
		// $mpdf->WriteHTML($html);
		// $mpdf->WriteHTML('<tocentry content="150mm square" /><p>This should print on a sheet 150mm x 150mm</p>');
// 		
		// $mpdf->WriteHTML('<pagebreak sheet-size="11.69in 8.27in" />');
		// $mpdf->WriteHTML($html);
		// $mpdf->WriteHTML('<tocentry content="A4 landscape (ins)" /><p>This should print on a sheet 11.69in x 8.27in = A4 landscape</p>');
// 		
// 		
		// $mpdf->Output();
		// exit;
// 
	// }
}
