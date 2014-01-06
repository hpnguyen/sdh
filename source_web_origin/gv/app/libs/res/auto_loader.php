<?php
$pathSplit = explode('app', dirname(__FILE__));
define('ROOT_DIR', $pathSplit[0]);

function __autoload($classname) {
	$stringSplit = explode('Controller', $classname);
	if (count($stringSplit) > 1) {
		$stringSplit = explode('Controller', $classname);
		$file = 'Index.php';
		if ($stringSplit[1] != '') {
			$file = $stringSplit[1].'.php';
		}
		
		$moduleName = str_replace('Module', '', $stringSplit[0]);
		
		try {
			
			require_once(ROOT_DIR.'app/module/'.$moduleName.'/Controller/'.$file);
			if (!class_exists($classname, false)) {
				throw new Exception('Class ' . $classname . ' not found');
			}
		}
		catch (Exception $e) {
			return eval("
				class $classname {
					function __construct(\$a=0, \$b=0, \$c=0, \$d=0, \$e=0, \$f=0, \$g=0, \$h=0, \$i=0)
					{
						throw new AutoloadExceptionRetranslator('$autoloadException');
					}
				}
			");
		}
	}else{
		$stringSplit = explode('Model', $classname);;
		if (count($stringSplit) > 1) {
			$stringSplit = explode('Model', $classname);
			$file = $stringSplit[0].'.php';
			
			try {
				if (!class_exists('BaseTable', false)) {
					require_once(ROOT_DIR.'app/model/base/basetable.php');
				}
				
				require_once(ROOT_DIR.'app/model/'.$file);
				if (!class_exists($classname, false)) {
					throw new Exception('Class ' . $classname . ' not found');
				}
			}
			catch (Exception $e) {
				return eval("
					class $classname {
						function __construct(\$a=0, \$b=0, \$c=0, \$d=0, \$e=0, \$f=0, \$g=0, \$h=0, \$i=0)
						{
							throw new AutoloadExceptionRetranslator('$autoloadException');
						}
					}
				");
			}
		}
	}
	
	$stringSplit3 = explode('Helper', $classname);
	$stringSplit4 = explode('Libs', $classname);
		
}

