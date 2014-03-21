<?php
/**
 * 
 */
class ModuleLoginControllerCas extends FrontController {
	const LOGIN_AS_CBGV = 0; //University officer
	const LOGIN_AS_SVDH = 1; //University student
	const LOGIN_AS_HVCH = 2; //Post graduate student
	
	function __construct() {
		$config = Helper::getHelper('functions/util')->getCASConfig();
		$cas_server_name = $config['php_cas_server_name'];
		$cas_server_port = $config['php_cas_server_port'];
		$cas_server_type = $config['php_cas_server_type'];
		
		// initialize phpCAS
		phpCAS::client(CAS_VERSION_2_0,$cas_server_name,$cas_server_port,$cas_server_type);
		// no SSL validation for the CAS server
		phpCAS::setNoCasServerValidation();
	}
	
	public	function indexAction(){
		if (isset($_GET['logout'])){
			$this->killLoginSession();
			$service = Helper::getHelper('functions/util')->getModuleActionRouteUrl('login/cas/destroy');
			phpCAS::logoutWithRedirectService($service);
		}
		
		if (! phpCAS::isAuthenticated()){
			// force CAS authentication
			phpCAS::forceAuthentication();
		}else{
			//Login success
			$this->createLoginSession();
		}
	}
	
	public	function destroyAction(){
		// $this->killLoginSession();
		$url = Helper::getHelper('functions/util')->baseURL();
		$this->redirect(200,$url);
	}
	
	private function createLoginSession(){
		$data = $this->getLoginData();
		
		if ($data['type'] == self::LOGIN_AS_HVCH){
			$modelNguoiDung = new NguoiDungModel();
			$realID  = substr("abcdef", 0, -1);
			$loginData = $modelNguoiDung->getDataOnLogin($data['id']);
			
			if($loginData == null){
				echo "Bạn login không hợp lệ";
				die;
			}
			
			$cookieConfig  = Helper::getHelper('functions/util')->getCookieConfig();
			
			$cookie_name = $cookieConfig['cookie_name'];
			$cookie_time = (int) $cookieConfig['cookie_seconds_per_hour'] * (int) $cookieConfig['cookie_hours_per_day'] * (int) $cookieConfig['cookie_days'];//30 Days
			
			$sid = $_REQUEST["hisid"];
			$link = $_REQUEST["l"];
			
			if ($sid!=""){
				session_id($sid);
			}
			session_start();
			$_SESSION["uidloginhv"]=base64_encode($loginData['username']);
				
			date_default_timezone_set('Asia/Ho_Chi_Minh');
			$today =date("d/m/Y");
			$time = date("H:i:s");
			$modelNguoiDung->updateLoginTime($loginData['username']);
			
			//Set cookie
			setcookie ($cookie_name, 'RemberMecookie=0', time()+$cookie_time);
			
			$url = Helper::getHelper('functions/util')->getGvRootURL()."/index.php?hisid=".session_id()."&l=".$link;
			//var_dump($_SESSION,$url);
			$this->redirect(200,$url);
		}else {
			$service = Helper::getHelper('functions/util')->getModuleActionRouteUrl('login/cas/error');
			phpCAS::logoutWithRedirectService($service);
		}
	}	
	
	public	function errorAction(){
		$template = new BaseTemplate("login/error","default/index");
		$template->url = Helper::getHelper('functions/util')->getGvRootURL();
		$templateContent = $template->contentTemplate();
		$template->renderLayout(array('title' => '','content' => $templateContent));
	}
	
	private function killLoginSession(){
		session_unset();
		session_destroy();
	}
	
	private function getCasAttributeValue($attributeName = null){
		$ret = phpCAS::getAttributes();
		// //Test for user is graduate student
		// $ret['hcmutPersonID'] = '511108026';
		
		if ($attributeName != null){
			if(isset($ret[$attributeName])){
				$ret = $ret[$attributeName];
			}else{
				$ret = null;
			}
		}
		
		return $ret;
	}
	
	private function getLoginData(){
		$numberID = $this->getCasAttributeValue('hcmutPersonID');
		$data = array('id' => $numberID, 'type' => self::LOGIN_AS_CBGV);
		
		if ($numberID != null && strlen($numberID) == 9){
			$data['type'] = self::LOGIN_AS_HVCH;
			//truncate the first number that is identify for graduate student from CAS server responding
			$data['id'] = substr($numberID, 1); 
		}else if($numberID != null && strlen($numberID) == 8){
			$data['type'] = self::LOGIN_AS_SVDH;
		}
		
		return $data;
	}
}
