<?php
date_default_timezone_set('Asia/Ho_Chi_Minh');
$path = dirname(__FILE__);
// Include the main TCPDF library (search for installation path).
require_once($path.'/libs/tcpdf/examples/tcpdf_include.php');
// Include mPDF library.
require_once($path."/libs/mpdf57/mpdf.php");
//Add crontab manager
require_once($path."/libs/crontabmanager/src/CliTool.php");
require_once($path."/libs/crontabmanager/src/CronEntry.php");
require_once($path."/libs/crontabmanager/src/CrontabManager.php");
// Include PHPMailer.
require_once($path."/libs/PHPMailer/class.phpmailer.php");
require_once($path."/libs/PHPMailer/class.pop3.php");
require_once($path."/libs/PHPMailer/class.smtp.php");
// Include PhpStringParser
require_once($path."/libs/PhpStringParser/PhpStringParser.php");
//Log file
include $path.'/logs/logfile.php';
//Cronjob task base class
include $path.'/cronjobs/queueTaskBase.php';
//Add auto loader
require_once ($path.'/libs/res/auto_loader.php');
//Add route map
include $path.'/libs/res/route.php';
//Add front end class
include $path.'/libs/res/front.php';
//Add template object
include $path.'/template/index.php';
//Add base table
include $path.'/model/base/basetable.php';
//Add helper static class
include $path.'/libs/helper/helper.php';

$arrayOtions = array('-h','-a','-d','-r','-c');
list($option,$option1,$option2) = array(null,null,null); //the arguments passed
if (isset($argv[1])){
	$option1 = $argv[1];
}
if (isset($argv[2])){
	$option2 = $argv[2];
}
	
if ($argc <1 || ! in_array($option1, $arrayOtions)) {
	echo("Invalid arguments, please do as below to get more information.\n");
	print("Usage: php cron.php help\n");
}else{
	//Help option
	if($option1 == '-h'){
		echo "Usage: php cron.php [options] <name>
   [-h]            Get help.
   [-a]            Add cronjob file to system crontab.
   [-d]            Remove cronjob file out system crontab.
   [-u]            Update the changing in cronjob file to system crontab.
   [-r]  <name>    Run a cronjob.
   [-c]  <name>    Create a cronjob.
";
		exit;
	}
	
	if($option1 == '-a'){
		$crontab = new CrontabManager();
		$crontab->enableOrUpdate(ROOT_DIR.'app/config/cronfile.conf');
		$crontab->save();
		echo "Add cronjob file to system crontab done\n";
	}else if($option1 == '-d'){
		$crontab = new CrontabManager();
		$crontab->disable(ROOT_DIR.'app/config/cronfile.conf');
		$crontab->save();
		echo "Remove cronjob file out system crontab done\n";
	} else if($option1 == '-u') {
		$crontab = new CrontabManager();
		$crontab->disable(ROOT_DIR.'app/config/cronfile.conf');
		$crontab->save();
		$crontab->enableOrUpdate(ROOT_DIR.'app/config/cronfile.conf');
		$crontab->save();
		echo "Update cronjob file to system crontab done\n";
	} else if($option1 == '-c') {
		if (empty($option2) ){
			echo "Missing cronjob name\n";
		}else{
			$filename = $option2.'.php';
			$filepath = ROOT_DIR.'app/cronjobs/'.$filename;
			
			if(file_exists($filepath)){
				echo "This cronjob file is exist\n";
			}else{
//A default content for cronjob file
$content = '<?php
class Cronjob_'.ucfirst($option2).' extends Queuetaskbase {
	function __construct() {
		
	}
	
	/*
	 * Cronjob run function
	 */
	public	function run() {
		
	}
}';
				$fp = fopen($filepath,"wb");
				fwrite($fp,$content);
				fclose($fp);
				chmod($filepath, 0777);
			}
			
			echo "Create cronjob done\n";
		}
	} else {
		echo "Run cronjob: ".$option2."\n";
		//Ready run job
		$name = $option2;
		$fileName = $name.'.php';
		if (file_exists(ROOT_DIR.'app/cronjobs/'.$fileName)){
			include ROOT_DIR.'app/cronjobs/'.$fileName;
			$className = "Cronjob_".ucfirst($name);
			$job = new $className;
			$job->execute($name);
		}
		
		echo "End cronjob\n";
	}
}


