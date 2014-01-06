<?php
/**
 * 
 */
class Helper  {
	private $filePath = null;
	function __construct() {
		$this->filePath = dirname(__FILE__);
		return $this;
	}
	static function getHelper($name)
	{
		$filePath =  dirname(__FILE__);
		$plitPath = explode('/', $name);
		$className = 'Helper';
		
		foreach ($plitPath as $key => $value) {
			$item = ucfirst($value);
			$plitPath[$key] = $item;
			$className  = $className.$item;
		}
		
		$filename =$filePath.'/'. implode('/', $plitPath).'.php';
		
		if(file_exists($filename)){
			require_once $filename;
			if(class_exists($className)){
				return new $className();
			}else{
				throw new Exception("No Have Helper");
			}
		}else{
			throw new Exception("Invalid Helper");
		}
	}
	
}
