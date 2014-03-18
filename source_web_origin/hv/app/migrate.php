<?php
date_default_timezone_set('Asia/Ho_Chi_Minh');
// Include the main TCPDF library (search for installation path).
require_once('./libs/tcpdf/examples/tcpdf_include.php');
// Include mPDF library.
require_once("./libs/mpdf57/mpdf.php");
//Add auto loader
require_once ('./libs/res/auto_loader.php');
//Add route map
include './libs/res/route.php';
//Add front end class
include './libs/res/front.php';
//Add template object
include './template/index.php';
//Add base table
include './model/base/basetable.php';
//Add helper static class
include './libs/helper/helper.php';

$arrayOtions = array('-h','-v','-c','-u','-ua','-d','-da');
list($option,$option1,$option2) = $argv; //the arguments passed

if ($argc <=1 || ! in_array($option1, $arrayOtions)) {
	echo("Invalid arguments, please do as below to get more information.\n");
	print("Usage: php migration.php help\n");
}else{
	//Help option
	if($option1 == '-help' || $option1 == '-h'){
		echo "Usage: php migrate.php [options] <name|version>
   [-h]            Get help.
   [-v]            Get current migration version.
   [-c]  <name>    Create migration file. Name must be <xxxx_yyyy_zzzz> and unique.
   [-u]  <version> Upgrate current to option version.
   [-u]            Upgrate current up to one new version.
   [-ua] <version> Upgrate from version to the lastest version.
   [-ua]           Upgrate from current version to the lastest version.
   [-d]  <version> Downgrate from current version to option version.
   [-d]            Downgrate from current to one older version.
   [-da] <version> Downgrate from option version to the first version.
   [-da]           Downgrate from current version to the first version.\n
";
		exit;
	}
	//*******************************************************************************************
	//Init check folder migrate and table migrate
	echo "Start migrate\n";
	if (!is_dir(ROOT_DIR.'/app/migrate')) {
		echo "Create migration folder: ".ROOT_DIR."/app/migratete\n";
		mkdir(ROOT_DIR.'/app/migratete',0777);
	}
	
	$path = dirname(__FILE__);
	$split = explode('/', $path);
	$maxCount = count($split);
	//set POST variables
	$url ='http://127.0.0.1/'.$split[$maxCount - 2].'/front.php/migration/index/index';
	$fields = array("option1" => $option1, "option2" => $option2);
	
	//url-ify the data for the POST
	foreach($fields as $key=>$value) { $fields_string .= $key.'='.$value.'&'; }
	rtrim($fields_string, '&');
	//open connection
	$ch = curl_init();
	//set the url, number of POST vars, POST data
	curl_setopt($ch, CURLOPT_URL, $url);
	curl_setopt($ch,CURLOPT_POST, count($fields));
	curl_setopt($ch,CURLOPT_POSTFIELDS, $fields_string);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	//execute post
	$result = curl_exec($ch);
	echo ($result == "" ? "" : $result."\n");
	//close connection
	curl_close($ch);
	echo "migrate done\n\n";
}