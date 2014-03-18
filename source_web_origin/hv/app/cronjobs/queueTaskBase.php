<?php
class Queuetaskbase {
	function __construct() {
		
	}
	
	/*
	 * Cronjob main function
	 */
	public	function execute($name) {
		$path = dirname(__FILE__);
		$split = explode('/', $path);
		$maxCount = count($split);
		
		//set POST variables
		$url ='http://127.0.0.1/'.$split[$maxCount - 3].'/front.php/cronjob/index/index/name/'.$name;
		//open connection
		$ch = curl_init();
		//set the url, number of POST vars, POST data
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch,CURLOPT_POST,0);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		//execute post
		$result = curl_exec($ch);
		echo ($result == "" ? "" : $result."\n");
		//close connection
		curl_close($ch);
		echo "\n";
	}
}