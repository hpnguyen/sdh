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
	
	function escapeSpecialCharToHtmlCode($string){
		$temp = addslashes($string);
		$temp = str_replace("\\'", "&#39;", $temp);
		$temp = str_replace('\"', '&quot;', $temp);
		$temp = str_replace('\\\\', '&#92;', $temp);
		$temp = str_replace("&#92;&quot;", "&quot;", $temp);
		$temp = str_replace("&#92;&#92;", "&#92;", $temp);
		$temp = str_replace("&#92;&#39;", "&#39;", $temp);
		
		return $temp;
	}
	
	function trimSlashSpecialChar($string){
		$temp = str_replace("&#92;&quot;", "&quot;", $string);
		$temp = str_replace("&#92;&#92;", "&#92;", $temp);
		$temp = str_replace("&#92;&#39;", "&#39;", $temp);
		return $temp;
	}
	
	function getCASConfig(){
		if (function_exists ('yaml_parse_file')) {
			$filename = ROOT_DIR.'app/config/conf.yml';
			$config = yaml_parse_file ($filename);
		}else{
			require ROOT_DIR.'app/config/conf.php';
		}
		$ret = array();
		foreach ($config as $key => $value) {
			if (preg_match('/^php_cas_server_/', $key)){
				$ret[$key] = $value;
			}
		}
		return $ret;
	}
	
	function getCookieConfig(){
		if (function_exists ('yaml_parse_file')) {
			$filename = ROOT_DIR.'app/config/conf.yml';
			$config = yaml_parse_file ($filename);
		}else{
			require ROOT_DIR.'app/config/conf.php';
		}
		$ret = array();
		foreach ($config as $key => $value) {
			if (preg_match('/^cookie_/', $key)){
				$ret[$key] = $value;
			}
		}
		return $ret;
	}
	
	function getEncryptKeycodeConfig(){
		if (function_exists ('yaml_parse_file')) {
			$filename = ROOT_DIR.'app/config/conf.yml';
			$config = yaml_parse_file ($filename);
		}else{
			require ROOT_DIR.'app/config/conf.php';
		}
		
		return $config['server_encrypt_keycode'];
	}
	
	function getEncryptKeycode2Config(){
		if (function_exists ('yaml_parse_file')) {
			$filename = ROOT_DIR.'app/config/conf.yml';
			$config = yaml_parse_file ($filename);
		}else{
			require ROOT_DIR.'app/config/conf.php';
		}
		
		return $config['server_encrypt_keycode2'];
	}
	
	function reverse_escape($str)
	{
		$search=array("\\\\","\\0","\\n","\\r","\Z","\'",'\"');
		$replace=array("\\","\0","\n","\r","\x1a","'",'"');
		return str_replace($search,$replace,$str);
	}
	
	// \" \\ \/ \b \f \n \r \t \u
	function escapeJsonString($value) { # list from www.json.org: (\b backspace, \f formfeed)
		$escapers = array("\\", "/", '"', "\n", "\r", "\t", "\x08", "\x0c");
		$replacements = array("\\\\", "\\/", '\"', "\\n", "\\r", "\\t", "\\f", "\\b");
		$result = str_replace($escapers, $replacements, $value);
		return $result;
	}
}
