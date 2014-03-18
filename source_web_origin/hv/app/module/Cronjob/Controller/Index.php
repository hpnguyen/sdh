<?php
/**
 * 
 */
class ModuleCronjobControllerIndex extends FrontController {
	
	function __construct() {
		$userip = ($_SERVER['X_FORWARDED_FOR']) ? $_SERVER['X_FORWARDED_FOR'] : $_SERVER['REMOTE_ADDR'];
		if ($userip != '127.0.0.1'){
			die('Only request from local') ;
		}
	}
	
	public	function indexAction(){
		$nameCronjob = $this->getParam('name',null);
		
		if ($nameCronjob == null){
			echo "Invalid cronjob";
			return;
		}
		
		$filepath =  ROOT_DIR.'app/cronjobs/'.$nameCronjob.".php";
		
		if(! file_exists($filepath)){
			echo "Cronjob file ".$file." is not exist";
			return;
		}
		
		include $filepath;
		
		$className = 'Cronjob_'.ucfirst($nameCronjob);
		if (! class_exists($className)){
			echo "Class ".$className." is not exist";
		}else{
			$object = new $className;
			$object->run();
			unset($object);
		}
	}
}
