<?php

/**
 *
 */
class HelperFunctionsUtil {

	function __construct() {

	}
	
	function curPageURL() {
		$pageURL = 'http';
		if (isset($_SERVER["HTTPS"]) && $_SERVER["HTTPS"] == "on") {
			$pageURL .= "s";
		}
		$pageURL .= "://";
		if ($_SERVER["SERVER_PORT"] != "80") {
			$pageURL .= $_SERVER["SERVER_NAME"] . ":" . $_SERVER["SERVER_PORT"] . $_SERVER["REQUEST_URI"];
		} else {
			$pageURL .= $_SERVER["SERVER_NAME"] . $_SERVER["REQUEST_URI"];
		}
		return $pageURL;
	}
	
	function baseURL() {
		$pageURL = 'http';
		if (isset($_SERVER["HTTPS"]) && $_SERVER["HTTPS"] == "on") {
			$pageURL .= "s";
		}
		$pageURL .= "://";
		if ($_SERVER["SERVER_PORT"] != "80") {
			$pageURL .= $_SERVER["SERVER_NAME"] . ":" . $_SERVER["SERVER_PORT"] ;
		} else {
			$pageURL .= $_SERVER["SERVER_NAME"] ;
		}
		return $pageURL;
	}
	function getGvRootURL() {
		$baseURL = self::baseURL();
		$curPageURL = self::curPageURL();
		$curPageURL = str_replace($baseURL.'/', '', $curPageURL);
		$pathArrayTmp = explode('/', $curPageURL, 2);
		$resultURL = $baseURL.'/'.$pathArrayTmp[0];
		return $resultURL;
	}
	
	function gvRootImageURL($path) {
		return self::getGvRootURL().'/images/'.$path;
	}
	
	function getRouteUrl($full = true){
		$ret = Route::getRouteIndex();
		if ($full) {
			$ret = self::baseURL().'/'.$ret;
		}
		return $ret;
	}
	
	function getModuleActionRouteUrl($url,$full = true){
		$ret = null;
		
		if ($full){
			$ret = self::getGvRootURL();
		}
		$ret .= "/front.php/".$url;
		
		return $ret;
	}
	
	function getDbFileConfig($dbConfigName = ''){
		if (function_exists ('yaml_parse_file')) {
			$filename = ROOT_DIR.'app/config/conf'.$dbConfigName.'.yml';
			$config = yaml_parse_file ($filename);
		}else{
			require ROOT_DIR.'app/config/conf'.$dbConfigName.'.php';
		}
		
		return $config;
	}
	
	function getRouteFileConfig(){
		if (function_exists ('yaml_parse_file')) {
			$filename = ROOT_DIR.'app/config/route.yml';
			$config = yaml_parse_file ($filename);
		}else{
			require ROOT_DIR.'app/config/route.php';
		}
		
		return $config;
	}
	
	function setSession($name,$value = null){
		$_SESSION[$name] = $value;
	}
	
	function getSession($name,$value = null){
		if (!isset($_SESSION[$name])) {
			return $value;
		}else{
			return $_SESSION[$name];
		}
	}
	
	function initJqplot($canvasId, $jqplotData,$jqplotOptionsString,$canvasStyle = null){
		$template = new BaseTemplate("default/jqplot","default/index");
		$template->canvasId = $canvasId; 
		$template->jqplotData = $jqplotData;
		$template->jqplotOptionsString = $jqplotOptionsString;
		$template->canvasStyle = $canvasStyle;
		return $template;
	}
}
