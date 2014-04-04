<?php
/**
 *
 */
class HelperFunctionsSessionlogin {

	function __construct() {

	}
	
	function createGvSession($modelNhanSu, $loginData){
		$cookieConfig  = Helper::getHelper('functions/util')->getCookieConfig();
			
		$cookie_name = $cookieConfig['cookie_name'];
		$cookie_time = (int) $cookieConfig['cookie_seconds_per_hour'] * (int) $cookieConfig['cookie_hours_per_day'] * (int) $cookieConfig['cookie_days'];//30 Days
		
		//error_reporting(1);
		$sid = $_REQUEST["hisid"];
		$link = $_REQUEST["l"];
		
		if ($sid!=""){
			session_id($sid);
			session_start();
		}
		//Clear SV data session
		unset($_SESSION["uidloginhv"]);
		//Clear old data
		unset($_SESSION["uidloginPortal"]);
		unset($_SESSION["makhoa"]);
		unset($_SESSION["tenkhoa"]);
		unset($_SESSION["macb"]);
		
		$_SESSION["uidloginPortal"]=base64_encode($loginData['username']);
		$_SESSION["makhoa"] = base64_encode($loginData['ma_khoa']);
		$_SESSION["tenkhoa"] = $loginData['ten_khoa'];
		$_SESSION["macb"] = $loginData['ma_can_bo'];
			
		date_default_timezone_set('Asia/Ho_Chi_Minh');
		$today =date("d/m/Y");
		$time = date("H:i:s");
		$modelNhanSu->updateLoginTime($loginData['username']);
		
		//Set cookie
		setcookie ($cookie_name, 'RemberMecookie=0', time()+$cookie_time);
		
		$url = Helper::getHelper('functions/util')->getGvRootURL()."/index.php?hisid=".session_id()."&l=".$link;
		$url = str_replace('/hvbeta/index.php', '/gvbeta/index.php', $url);
		$url = str_replace('/hv/index.php', '/gv/index.php', $url);
		return $url;
	}
	
	function createHvSession($modelNguoiDung,$loginData){
		$cookieConfig  = Helper::getHelper('functions/util')->getCookieConfig();
		
		$cookie_name = $cookieConfig['cookie_name'];
		$cookie_time = (int) $cookieConfig['cookie_seconds_per_hour'] * (int) $cookieConfig['cookie_hours_per_day'] * (int) $cookieConfig['cookie_days'];//30 Days
		
		$sid = $_REQUEST["hisid"];
		$link = $_REQUEST["l"];
		
		if ($sid!=""){
			session_id($sid);
		}
		session_start();
		//Clear SV data session
		unset($_SESSION["uidloginhv"]);
		//Clear old data
		unset($_SESSION["uidloginPortal"]);
		unset($_SESSION["makhoa"]);
		unset($_SESSION["tenkhoa"]);
		unset($_SESSION["macb"]);
		
		$_SESSION["uidloginhv"]=base64_encode($loginData['username']);
			
		date_default_timezone_set('Asia/Ho_Chi_Minh');
		$today =date("d/m/Y");
		$time = date("H:i:s");
		$modelNguoiDung->updateLoginTime($loginData['username']);
		
		//Set cookie
		setcookie ($cookie_name, 'RemberMecookie=0', time()+$cookie_time);
		
		$url = Helper::getHelper('functions/util')->getGvRootURL()."/index.php?hisid=".session_id()."&l=".$link;
		$url = str_replace('/gvbeta/index.php', '/hvbeta/index.php', $url);
		$url = str_replace('/gv/index.php', '/hv/index.php', $url);
		
		return $url;
	}
}