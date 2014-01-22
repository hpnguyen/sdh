<?php
/**
 * 
 */
class ModuleMigrationControllerIndex extends FrontController {
	
	function __construct() {
		$userip = ($_SERVER['X_FORWARDED_FOR']) ? $_SERVER['X_FORWARDED_FOR'] : $_SERVER['REMOTE_ADDR'];
		if ($userip != '127.0.0.1'){
			die('Only request from local') ;
		}
	}
	
	public	function indexAction(){
		$model = new ConfigModel();
		$model->checkInitialMigration();
		$option1 = isset($_POST['option1']) ? $_POST['option1'] : null;
		$option2 = isset($_POST['option2']) ? $_POST['option2'] : null;
		
		$arrayOtions = array('-v','-c','-u','-ua','-d','-da');
		if(! in_array($option1, $arrayOtions)){
			echo "Invalid first option\n";
			unset($model);
		}else{
			$currentMigrationVersion = $model->getLastVersion();
			
			if($option1 == '-v'){
				if ($currentMigrationVersion != null){
					echo "Migrate version: ".$currentMigrationVersion."\n";
				}else{
					echo "Migrate version: 0\n";
				}
			}elseif($option1 == '-c'){
				if($option2 == null || $option2 == '') {
					echo "You miss name of migration file, can't create file migration\n";
				}else{
					$utc_str = gmdate("M d Y H:i:s", time());
					$utc = strtotime($utc_str);
					
					$filename = $utc.'.php';
					$filepath = ROOT_DIR.'app/migrate/'.$filename;
					
					if(file_exists($filepath)){
						echo "This migration file is exist\n";
					}else{
//A default content for migrate file
$content = '<?php
/**
 * '.$option2.'
 */
class Migration_'.$utc.' {
	function __construct() {
		echo "Start migrate file '.$utc.'.php\n";
	}
					
	function __destruct() {
		echo "***************************************************************\n";
	}
	
	public	function up(){
		echo "function up\n";
	}
	
	public	function down(){
		echo "function down\n";
	}
}';
						$fp = fopen($filepath,"wb");
						fwrite($fp,$content);
						fclose($fp);
						chmod($filepath, 0777);
						echo "Create migration file ".$filename."  done\n";
					}
				}
			}elseif($option1 == '-u'){
				//$currentMigrationVersion = '1389262706';
				//$currentMigrationVersion = null;
				//$option2 = '1389262793';
				//$option2 = null;
				$migrationFiles = $this->renewListMigrationFiles($this->getMigrationFiles(), $currentMigrationVersion, $option2);
				//var_dump($currentMigrationVersion,$option2,$migrationFiles);
				$this->executeMigrate($currentMigrationVersion,$migrationFiles);
			}elseif($option1 == '-ua'){
				//$currentMigrationVersion = '1389262706';
				//$currentMigrationVersion = null;
				$migrationFiles = $this->renewListMigrationFilesCaseForAll($this->getMigrationFiles(), $currentMigrationVersion);
				//var_dump($currentMigrationVersion,$migrationFiles);
				$this->executeMigrate($currentMigrationVersion,$migrationFiles);
			}elseif($option1 == '-d'){
				//$currentMigrationVersion = '1389262793';
				//$currentMigrationVersion = null;
				//$option2 = '1389262706';
				//$option2 = null;
				$migrationFiles = $this->renewListMigrationFiles($this->getMigrationFiles(false), $currentMigrationVersion, $option2);
				//var_dump($currentMigrationVersion,$option2,$migrationFiles);
				$this->executeMigrate($currentMigrationVersion,$migrationFiles, false);
				//check last
				if(count($migrationFiles) == 1) {
					$model->updateMigrationVersion(null);
				}
			}elseif($option1 == '-da'){
				//$currentMigrationVersion = '1389262793';
				//$currentMigrationVersion = null;
				$migrationFiles = $this->renewListMigrationFilesCaseForAll($this->getMigrationFiles(false), $currentMigrationVersion);
				//var_dump($currentMigrationVersion,$migrationFiles);
				$this->executeMigrate($currentMigrationVersion,$migrationFiles, false);
				$model->updateMigrationVersion(null);
			}
			
		}
		unset($model);
	}
	
	private function executeMigrate($currentMigrationVersion,$migrationFiles, $runUpFuntion = true)
	{
		$lastItem = end($migrationFiles);
		if ($lastItem == $currentMigrationVersion.'.php'){
			echo "Migration version is currently, no migration run.\n";
			return;
		}elseif( $runUpFuntion == false && empty($currentMigrationVersion)){
			echo "System cannot downgrade. Your version is in beginning\n";
			return;
		}
		
		foreach ($migrationFiles as $key => $file) {
			$this->doMigrate($file,$runUpFuntion);
		}
	}
	
	private function doMigrate($file, $runUpFuntion = true)
	{
		$filepath =  ROOT_DIR.'app/migrate/'.$file;
		
		if(! file_exists($filepath)){
			echo "Migration file ".$file." is not exist\n";
			return;
		}
		
		include $filepath;
		$version = str_replace('.php', '', $file);
		$className = 'Migration_'.$version;
		if (class_exists($className)){
			$object = new $className;
			if ($runUpFuntion) {
				if (method_exists($object,'up')){
					$object->up();
				}else{
					echo "No have up method\n";
				}
			}else{
				if (method_exists($object,'down')){
					$object->down();
				}else{
					echo "No have down method\n";
				}
			}
			unset($object);
			$model = new ConfigModel();
			$model->updateMigrationVersion($version);
			unset($model);
			
		}
	}
	
	private function getMigrationFiles($asc = true)
	{
		$dir =  ROOT_DIR.'app/migrate';
		$ret = array();
		if ($version != null && $version != '' && ! file_exists($dir.'/'.$version.'.php')){
			return $ret;
		}
		
		$fileList = array();
		if (is_dir($dir)) {
			if ($dh = opendir($dir)) {
				
				while (($file = readdir($dh)) !== false) {
					if (($file != ".")&& ($file != "..") && ($file != ".svn"))
					{
						$fileList[] =$file;
					}
				}
				closedir($dh);
			}
		}
		// var_dump($fileList);
		if (count($fileList) > 0){
			if ($asc){
				sort($fileList);
			}else{
				rsort($fileList);
			}
		}
		
		return $fileList;
	}
	
	private function renewListMigrationFiles($listFiles, $currentMigrationVersion, $option2)
	{
		if ($currentMigrationVersion != null && ($option2 == null || $option2 == '')) {
			//Case current version is not null up/down to one
			$newList = array();
			foreach ($listFiles as $key => $value) {
				if ($currentMigrationVersion.'.php' == $value){
					if (isset($listFiles[$key + 1])){
						$newList[] = $listFiles[$key + 1];
					}
					break;
				}
			}
			$listFiles = $newList;
		}elseif($currentMigrationVersion != null && ! empty($option2)){
			//Case current version is not null up/down to one option version
			$newList = array();
			$start = -1;
			foreach ($listFiles as $key => $value) {
				if ($currentMigrationVersion.'.php' == $value){
					if (isset($listFiles[$key + 1])){
						$start = $key + 1;
					}else{
						break;
					}
				}
			}
			if ($start > 0){
				for ($i = $start; $i  < count($listFiles) ; $i ++) {
					$newList[] = $listFiles[$i];
					if ($option2.'.php' == $listFiles[$i]){
						break;
					}
				}
				
				$listFiles = $newList;
			}
		}
		return $listFiles;
	}
	
	private function renewListMigrationFilesCaseForAll($listFiles, $currentMigrationVersion)
	{
		//Case current version is not null up/down to one option version
		$newList = array();
		$start = -1;
		foreach ($listFiles as $key => $value) {
			if ($currentMigrationVersion.'.php' == $value){
				if (isset($listFiles[$key + 1])){
					$start = $key + 1;
				}else{
					break;
				}
			}
		}
		if ($start > 0){
			for ($i = $start; $i  < count($listFiles) ; $i ++) {
				$newList[] = $listFiles[$i];
			}
			
			$listFiles = $newList;
		}

		return $listFiles;
	}
}
