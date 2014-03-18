<?php
class FrontController implements FrontControllerInterface {
	const DEFAULT_CONTROLLER = "IndexController";
	const DEFAULT_ACTION = "index";
	const LOGIN_SESSION_NAME = "uidloginPortal";
	const LOGIN_QUERY_STRING_NAME = "hisid";
	

	protected $controller = self::DEFAULT_CONTROLLER;
	protected $action = self::DEFAULT_ACTION;
	public $params = array();
	protected $basePath = "mybasepath/";

	public function __construct(array $options = array()) {
		//Check and start session
		$this->initSession();
		
		if (empty($options)) {
			$this -> parseUri();
		} else {
			if (isset($options["controller"])) {
				$this -> setController($options["controller"]);
			}
			if (isset($options["action"])) {
				$this -> setAction($options["action"]);
			}
			if (isset($options["params"])) {
				$this -> setParams($options["params"]);
			}
		}
		
		return $this;
	}

	protected function parseUri() {
		$path = trim(parse_url($_SERVER["REQUEST_URI"], PHP_URL_PATH), "/");
		$path = preg_replace('/[^a-zA-Z0-9]//', "", $path);
		if (strpos($path, $this -> basePath) === 0) {
			$path = substr($path, strlen($this -> basePath));
		}
		@list($controller, $action, $params) = explode("/", $path, 3);
		if (isset($controller)) {
			$this -> setController($controller);
		}
		if (isset($action)) {
			$this -> setAction($action);
		}
		if (isset($params)) {
			$this -> setParams(explode("/", $params));
		}
	}

	public function setController($controller) {
		if (!class_exists($controller)) {
			throw new InvalidArgumentException("The action controller '$controller' has not been defined.");
		}
		$this -> controller = $controller;
		return $this;
	}

	public function setAction($action) {
		$check = method_exists($this -> controller, $action.'Action');
		if (!$check) {
			throw new InvalidArgumentException("The controller action '$action' has been not defined.");
		}
		$this -> action = $action;
		return $this;
	}

	public function setParams(array $params) {
		$this -> params = $params;
		return $this;
	}
	
	public function getFrontObject() {
		return $this;
	}
	
	public function run() {
		$object = new $this->controller;
		call_user_func(array($object, 'Init'), $this -> params);
		call_user_func_array(array($object, $this -> action.'Action'), $this -> params);
	}
	
	public function init($params) {
		$this->params = $params;
	}
	
	public function initSession() {
		if (!isset($_SESSION) || session_status() == PHP_SESSION_NONE) {
			session_start();
		}
	}
	
	public function setSession($name,$value = null){
		$_SESSION[$name] = $value;
	}
	
	public function getSession($name,$value = null){
		if (!isset($_SESSION[$name])) {
			return $value;
		}else{
			return $_SESSION[$name];
		}
	}
	
	public function checkLogin(){
		if (!isset($_SESSION[self::LOGIN_SESSION_NAME])){
			return false; 
		}
		
		if (isset($_REQUEST[self::LOGIN_QUERY_STRING_NAME]))
		{
			$sid = $_REQUEST[self::LOGIN_QUERY_STRING_NAME];
			session_id($sid);
		}
		
		return true;
	}
	
	public function renderJSON($params)
	{
		header('Content-Type: application/json');
		$arrayString = array();
		foreach ($params as $key => $value) {
			$arrayString[] = '"'.$key.'":"'.$value.'"';
		}
		echo '{'.implode(',', $arrayString).'}';
	}
	
	public function isPost() {
		if ($_SERVER['REQUEST_METHOD'] === 'POST' ) {
		return true;
		} else {
		return false;
		}
		}

		public function getPosts() {
		return $_POST;
		}

		public function getPost($name, $value = null) {
		if (! isset($_POST[$name])){
		return $value;
		}else{
		return $_POST[$name];
		}
		}

		public function getGets() {
		return $_GET;
		}

		public function getGet($name, $value = null) {
		if (! isset($_GET[$name])){
		return $value;
		}else{
		return $_GET[$name];
		}
		}

		public function getParams() {
		return $this->params;
		}

		public function getParam($name, $value = null) {
		if (! isset($this->params[$name])){
		return $value;
		}else{
		return $this->params[$name];
		}
		}

		public function redirect($num,$url){
			static $http = array (
			100 => "HTTP/1.1 100 Continue",
			101 => "HTTP/1.1 101 Switching Protocols",
			200 => "HTTP/1.1 200 OK",
			201 => "HTTP/1.1 201 Created",
			202 => "HTTP/1.1 202 Accepted",
			203 => "HTTP/1.1 203 Non-Authoritative Information",
			204 => "HTTP/1.1 204 No Content",
			205 => "HTTP/1.1 205 Reset Content",
			206 => "HTTP/1.1 206 Partial Content",
			300 => "HTTP/1.1 300 Multiple Choices",
			301 => "HTTP/1.1 301 Moved Permanently",
			302 => "HTTP/1.1 302 Found",
			303 => "HTTP/1.1 303 See Other",
			304 => "HTTP/1.1 304 Not Modified",
			305 => "HTTP/1.1 305 Use Proxy",
			307 => "HTTP/1.1 307 Temporary Redirect",
			400 => "HTTP/1.1 400 Bad Request",
			401 => "HTTP/1.1 401 Unauthorized",
			402 => "HTTP/1.1 402 Payment Required",
			403 => "HTTP/1.1 403 Forbidden",
			404 => "HTTP/1.1 404 Not Found",
			405 => "HTTP/1.1 405 Method Not Allowed",
			406 => "HTTP/1.1 406 Not Acceptable",
			407 => "HTTP/1.1 407 Proxy Authentication Required",
			408 => "HTTP/1.1 408 Request Time-out",
			409 => "HTTP/1.1 409 Conflict",
			410 => "HTTP/1.1 410 Gone",
			411 => "HTTP/1.1 411 Length Required",
			412 => "HTTP/1.1 412 Precondition Failed",
			413 => "HTTP/1.1 413 Request Entity Too Large",
			414 => "HTTP/1.1 414 Request-URI Too Large",
			415 => "HTTP/1.1 415 Unsupported Media Type",
			416 => "HTTP/1.1 416 Requested range not satisfiable",
			417 => "HTTP/1.1 417 Expectation Failed",
			500 => "HTTP/1.1 500 Internal Server Error",
			501 => "HTTP/1.1 501 Not Implemented",
			502 => "HTTP/1.1 502 Bad Gateway",
			503 => "HTTP/1.1 503 Service Unavailable",
			504 => "HTTP/1.1 504 Gateway Time-out"
			);
			header($http[$num]);
			header ("Location: $url");
		}
}
	