<?php
/**
 * 
 */
class ModuleLoginControllerIndex extends FrontController {
	
	function __construct() {
		
	}
	
	public	function indexAction(){
		$template = new BaseTemplate("login/index","default/blank");
		$template->renderTemplate();
	}
	
	public	function loginAction(){
		// Helper::getHelper('functions/userlogin')->sec_session_start(); // Our custom secure way of starting a PHP session.
		// session_destroy();
		if (isset($_POST['email'], $_POST['p'])) {
			$email = $_POST['email'];
			$password = $_POST['p']; // The hashed password.
			
			$ret = Helper::getHelper('functions/userlogin')->login($email, $password);
			
			if ($ret == true) {
				// Login success 
				//header('Location: ../protected_page.php');
				echo "Login success";
			} else {
				// Login failed 
				//header('Location: ../index.php?error=1');
				echo "Login failed";
			}
		} else {
			// The correct POST variables were not sent to this page. 
			echo 'Invalid Request';
		}
	}
	public	function registerAction(){
		$error_msg = "";
		if (isset($_POST['username'], $_POST['email'], $_POST['p'])) {
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
			$modelUserMembers = new UserMembersModel();
			$stmt = $modelUserMembers->readByEmail($email);
			
			if ($stmt->itemsCount == 1) {
				// A user with this email address already exists
				$error_msg .= '
				<p class="error">
					A user with this email address already exists.
				</p>';
			}

			
			// TODO:
			// We'll also have to account for the situation where the user doesn't have
			// rights to do registration, by checking what type of user is attempting to
			// perform the operation.
			
			if (empty($error_msg)) {
				//Add new user
				$modelUserMembers->addNew($username, $email, $password);
				
				$template = new BaseTemplate("login/register_success","default/blank");
				$template->renderTemplate();
			}
			return;
		}else{
			$template = new BaseTemplate("login/register","default/blank");
			$template->error_msg = $error_msg;
			$template->renderTemplate();
		}
	}
	
	public	function logoutAction(){
		// session_start();
		// session_unset();
		// session_destroy();
		$template = new BaseTemplate("login/logout","default/blank");
		$template->renderTemplate();
	}
	// public	function test2Action(){
		// echo "test2 action of ModuleThoikhoabieuControllerPhanbo";
// 		
		// $helper = Helper::getHelper('functions/queuetask');
		// $helper->add('exampleFunctionTest1');
// 		
		// $helper->add('exampleFunctionTest2',array('test 1111','test 2222'));
		// var_dump(11111111);
// 		
		// $helper->executeTaskByID(1);
		// $helper->executeTaskByID(2);
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
		// $content = file_get_contents('/home/hpnguyen/Working/svn_repository_source/gvbeta/app/template/view/mail/tkb.php');
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
}
