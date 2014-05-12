<?php
/**
 * 
 */
class ModuleLoginControllerIndex extends FrontController {
		
	function __construct() {
		
	}
	
	public	function indexAction(){
		$templateFile = "login/index";
		if(isset($_GET['iframe'])){
			$templateFile .= '_iframe';
		}
		$template = new BaseTemplate($templateFile,"default/blank");
		$template->renderTemplate();
	}
	
	public	function loginAction(){
		// Helper::getHelper('functions/userlogin')->sec_session_start(); // Our custom secure way of starting a PHP session.
		// session_destroy();
		if (isset($_POST['username'], $_POST['p']) || isset($_GET['u'], $_GET['p'])) {
			$username = isset($_POST['username']) ? $_POST['username'] : $_GET['u'] ;
			$password = isset($_POST['p']) ? $_POST['p'] : $_GET['p']; // The hashed password.
			
			$modelNhanSu = new NhanSuModel();
			$modelNguoiDung = new NguoiDungModel();
			$rowNhanSu = $modelNhanSu->getByUsername($username);
			$rowNguoiDung = $modelNguoiDung->getByUsername($username);
			
			if ($rowNhanSu == null &&  $rowNguoiDung == null){
				$loginurl = null;
				if (isset($_GET['loginurl'])){
					$loginurl = urldecode($_GET['loginurl']);
				}else if (isset($_POST['loginurl'])){
					$loginurl = urldecode($_POST['loginurl']);
				}
				$template = new BaseTemplate("login/login_invalid","default/blank");
				$template->loginurl = $loginurl;
				$template->renderTemplate();
				return;
			}
			
			$email = $rowNhanSu != null ? $rowNhanSu['email'] : ($rowNguoiDung != null ? $rowNguoiDung['email']: $username) ; 
			$ret = Helper::getHelper('functions/userlogin')->login($username, $password);
			
			if ($ret == true) {
				// Login success 
				$this->loginsuccess($rowNhanSu,$rowNguoiDung);
			} else {
				$modelUserMembers =  new UserMembersModel();
				$user = $modelUserMembers->readByUsername($username);
				if ($user == null){
					$template = new BaseTemplate("login/inithash","default/blank");
					$template->url = Helper::getHelper('functions/util')->getModuleActionRouteUrl('login/index/gethashpassonlydata?u='.$username);
					
					if ($rowNhanSu != null) {
						$template->password = $rowNhanSu['password'];
					}else if($rowNguoiDung != null){
						$template->password = $rowNguoiDung['pass'];
					}else{
						$template->password = '';
					}
					
					$loginurlEncode = urlencode($_POST['loginurl']);
					$template->loginurlEncode = $loginurlEncode;
					
					$urlRelogin = Helper::getHelper('functions/util')->getModuleActionRouteUrl('login/index/login?u='.$username.'&p='.$password);
					$template->urlRelogin = $urlRelogin;
					$template->renderTemplate();
				}else{
					// Login failed
					$loginurl = null;
					if (isset($_GET['loginurl'])){
						$loginurl = urldecode($_GET['loginurl']);
					}else if (isset($_POST['loginurl'])){
						$loginurl = urldecode($_POST['loginurl']);
					}
					
					$template = new BaseTemplate("login/login_failed","default/blank");
					if ($loginurl == null){
						$template->loginurl = Helper::getHelper('functions/util')->getModuleActionRouteUrl('login/index/index');
					}else{
						$template->loginurl = $loginurl;
					}
					$template->renderTemplate();
				}
			}
		} else {
			// The correct POST variables were not sent to this page. 
			echo 'Invalid Request';
		}
	}
	
	private function loginsuccess($rowNhanSu,$rowNguoiDung){
		$loginurl = null;
		if (isset($_GET['loginurl'])){
			$loginurl = urldecode($_GET['loginurl']);
			
		}else if (isset($_POST['loginurl'])){
			$loginurl = urldecode($_POST['loginurl']);
		}
		
		$url = null;
		
		if ($rowNhanSu != null ){
			$url = $this->doLoginSuccessForNhanSu($rowNhanSu, $loginurl);
		}else if ($rowNguoiDung != null){
			$url = $this->doLoginSuccessForNguoiDung($rowNguoiDung);
		}
		
		if($url != null){
			if ($loginurl != null){
				$array = explode('iframe',$loginurl);
				
				if(count($array) > 1){
					$template = new BaseTemplate("login/login_success_iframe","default/blank");
					$template->url = $url;
					$template->renderTemplate();
					return;
				}
			}else{
				$this->redirect(200, $url);
			}
		}else{
			echo "Login success";
		}
		return;
	}
	
	private function doLoginSuccessForNhanSu($loginData, $url = null){
		$modelNhanSu = new NhanSuModel();
		$cookieConfig  = Helper::getHelper('functions/util')->getCookieConfig();
		
		$cookie_name = $cookieConfig['cookie_name'];
		$cookie_time = (int) $cookieConfig['cookie_seconds_per_hour'] * (int) $cookieConfig['cookie_hours_per_day'] * (int) $cookieConfig['cookie_days'];//30 Days
		
		//error_reporting(1);
		$sid = $_REQUEST["hisid"];
		$link = $_REQUEST["l"];
		
		$defaultTab = null;
		//Check the default tab
		if ($url != null){
			$array = explode('?', $url);
			$queryString = $array[1];
			$params = explode('&', $queryString);
			foreach ($params as $k => $v) {
				$getParams =  explode('=', $v);
				if ($getParams[0] == 'f'){
					$defaultTab = $getParams[1];
					break;
				}
			}
		}
		//Kill old session data
		$this->killLoginSession();
		
		session_start();
		
		$_SESSION["uidloginPortal"]=base64_encode($loginData['username']);
		$_SESSION["makhoa"] = base64_encode($loginData['ma_khoa']);
		$_SESSION["tenkhoa"] = $loginData['ten_khoa'];
		$_SESSION["macb"] = $loginData['ma_can_bo'];
		$_SESSION['useIframeLogin'] = true;
			
		date_default_timezone_set('Asia/Ho_Chi_Minh');
		$today =date("d/m/Y");
		$time = date("H:i:s");
		$modelNhanSu->updateLoginTime($loginData['username']);
		
		//Set cookie
		setcookie ($cookie_name, 'RemberMecookie=0', time()+$cookie_time);
		
		$url = Helper::getHelper('functions/util')->getGvRootURL()."/index.php?hisid=".session_id().($defaultTab != null? '&k='.$defaultTab : '')."&l=".$link;
		return $url;
	}
	
	private function doLoginSuccessForNguoiDung($loginData){
		$modelNguoiDung = new NguoiDungModel();
		$cookieConfig  = Helper::getHelper('functions/util')->getCookieConfig();
			
		$cookie_name = $cookieConfig['cookie_name'];
		$cookie_time = (int) $cookieConfig['cookie_seconds_per_hour'] * (int) $cookieConfig['cookie_hours_per_day'] * (int) $cookieConfig['cookie_days'];//30 Days
		
		$sid = $_REQUEST["hisid"];
		$link = $_REQUEST["l"];
		
		//Kill old session data
		$this->killLoginSession();
		
		if ($sid!=""){
			session_id($sid);
		}
		session_start();
		
		
		$_SESSION["uidloginhv"]=base64_encode($loginData['username']);
		$_SESSION['useIframeLogin'] = true;
			
		date_default_timezone_set('Asia/Ho_Chi_Minh');
		$today =date("d/m/Y");
		$time = date("H:i:s");
		$modelNguoiDung->updateLoginTime($loginData['username']);
		
		//Set cookie
		setcookie ($cookie_name, 'RemberMecookie=0', time()+$cookie_time);
		
		$rootUrl = Helper::getHelper('functions/util')->getGvRootURL();
		$pos = strpos($rootUrl, 'gvbeta');
		
		if ($pos === false) {
			$rootUrl = Helper::getHelper('functions/util')->baseURL()."/hv";
		} else {
			$rootUrl = Helper::getHelper('functions/util')->baseURL()."/hvbeta";
		}
		
		$url = $rootUrl."/index.php?hisid=".session_id()."&l=".$link;
		return $url;
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
			//var_dump($password);die;
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
				if (isset($_GET['gs'])){
					$template = new BaseTemplate("login/index2","default/blank");
					$template->username = $username;
					$template->password = $_POST['p'];
					$template->renderTemplate();
					return;
				}else{
					$template = new BaseTemplate("login/register_success","default/blank");
					$template->renderTemplate();
				}
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
	
	public	function gethashpassAction(){
		$username = $_GET['u'];
		$password = null;
		
		if (! empty($username)){
			$model = new NhanSuModel();
			$ret = $model->getByUsername($username);
			if ( $ret == null){
				unset($model);
				$model = new NguoiDungModel();
				$ret = $model->getByUsername($username);
				
				if ($ret != null){
					$password = $ret['pass'];
				}
			}else{
				$password = $ret['password'];
			}
		}
		
		if (! empty($ret)){
			
			if(isset($_POST['h'])){
				$password = $_POST['h']; // The hashed password.
				
				$modelNhanSu = new NhanSuModel();
				$modelNguoiDung = new NguoiDungModel();
				$rowNhanSu = $modelNhanSu->getByUsername($username);
				$rowNguoiDung = $modelNguoiDung->getByUsername($username);
			
				if ($rowNhanSu == null &&  $rowNguoiDung == null){
					die('Invalid data');
					return;
				}
				$email = $rowNhanSu != null ? $rowNhanSu['email'] : ($rowNguoiDung != null ? $rowNguoiDung['email']: $username) ; 
				
				$modelUserMembers = new UserMembersModel();
				$modelUserMembers->addNew($username , $email , $password);
				
			
			}else{
				$template = new BaseTemplate("login/gethashpass","default/blank");
				$template->username = $username;
				$template->password = $password;
				$template->hashpassword = null;
				$template->renderTemplate();
			}
		}
	}
	
	public function inithashAction(){
		$username = $_GET['u'];
		$model = new NhanSuModel();
		$ret = $model->getByUsername($username);
		if ( $ret == null){
			unset($model);
			$model = new NguoiDungModel();
			$ret = $model->getByUsername($username);
			
			if ($ret != null){
				$password = $ret['pass'];
			}
		}else{
			$password = $ret['password'];
		}
		
		$template = new BaseTemplate("login/inithash","default/blank");
		$template->url = Helper::getHelper('functions/util')->getModuleActionRouteUrl('login/index/gethashpassonlydata?u='.$username);
		$template->password = $password;
		$template->renderTemplate();
	}
	public	function gethashpassonlydataAction(){
		$username = $_GET['u'];
		$password = $_GET['p'];
		
		$modelUserMembers =  new UserMembersModel();
		$user = $modelUserMembers->readByUsername($username);
		if ($user == null){
			$modelNhanSu = new NhanSuModel();
			$modelNguoiDung = new NguoiDungModel();
			$rowNhanSu = $modelNhanSu->getByUsername($username);
			$rowNguoiDung = $modelNguoiDung->getByUsername($username);
			
			if ($rowNhanSu == null &&  $rowNguoiDung == null){
				die('Invalid data');
				return;
			}
			
			$email = $rowNhanSu != null ? $rowNhanSu['email'] : ($rowNguoiDung != null ? $rowNguoiDung['email']: $username) ;
			if (empty($email)){
				$email = $username.'@email.com';
			}
			
			$modelUserMembers->addNew($username , $email , $password);
		}
	}
	
	public	function addAction(){
		$username = $_GET['u'];
		$password = null;
		$email = null;
		$ret = null;
		
		if (! empty($username)){
			$model = new NhanSuModel();
			$ret = $model->getByUsername($username);
			if ( $ret == null){
				unset($model);
				$model = new NguoiDungModel();
				$ret = $model->getByUsername($username);
				
				if ($ret != null){
					$username = $ret['username'];
					$password = $ret['pass'];
					$email = $ret['email'];
				}
			}else{
				$username = $ret['username'];
				$password = $ret['password'];
				$email = $ret['email'];
			}
		}
		
		if (! empty($ret)){
			$template = new BaseTemplate("login/add","default/blank");
			$template->username = $username;
			$template->password = $password;
			$template->email = empty($email) ? $username.'@test.com' : $email ;
			$template->renderTemplate();
		}
	}
	
	private function killLoginSession(){
		session_unset();
		session_destroy();
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
}
