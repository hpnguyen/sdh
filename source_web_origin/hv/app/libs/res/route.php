<?php
/**
 * 
 */
class Route {
	const ROUTE_INDEX = 'front.php';
	private $map = null;
	private $controler = null;
	private $params = null;
	
	function __construct() {
		//Get routing file config route.yml
		$this->map = Helper::getHelper('functions/util')->getRouteFileConfig();
		$this->getControllerClassFromURL();
	}
	
	public function getRouteIndex()
	{
		return self::ROUTE_INDEX;
	}
	
	private function getControllerClassFromURL(){
		try
		{
			$helper = Helper::getHelper('functions/util');
			$pageURL = $helper->curPageURL();
			$stringSplit = explode(self::ROUTE_INDEX, $pageURL);
			
			//Invalid URL
			if (count($stringSplit) != 2){
				throw new Exception('This URL is invalid');
			}
			
			$string = $stringSplit[1];
			$stringSplit2 = explode('/', $string);
			
			//Default controller
			$routeName = 'index';
			$controllerName = 'index';
			$actionName = 'index';
			
			//Set controller if is not same default
			if($stringSplit[1] != ''){
				$routeName = $stringSplit2[1];
				$controllerName = $stringSplit2[2];
				
				$actionStringReady = $stringSplit2[3];
				$actionStringReadyArray = explode('?', $actionStringReady);
				$actionStringReadySplit = explode('/', $actionStringReadyArray[0]);
				
				$actionName = $actionStringReadySplit[0];
			}
			
			if (! array_key_exists($routeName ,$this->map )){
				throw new Exception('This controller is not exist in routing map');
			}
			
			$routeMap = $this->map[$routeName];
			$keysArray = array_keys($routeMap);
			$className = $keysArray[0];
			$controllerName = ucfirst($controllerName);
			$controllerClassName = 'Module'.$className.'Controller'.$controllerName;
			
			//Check action is exist in file controller
			$check = method_exists($controllerClassName, $actionName.'Action');
			if (!$check) {
				throw new Exception('The controller action '.$function.' has been not defined.');
			}
			
			//Cut query string params
			$tmpSplitString = explode('?', $string);
			$string = $tmpSplitString[0];
			//Ready parse params
			$string2 = str_replace($routeName.'/'.strtolower($controllerName).'/'.$actionName.'/', '', $string);
			
			$routeDefaultParams = null;
			
			foreach($routeMap[$className][$controllerName] as $keyActionName => $actionParams){
				$plitKeyNameArray = explode(',', $keyActionName);
				if(in_array($actionName, $plitKeyNameArray)){
					$routeDefaultParams = $actionParams;
				}
			}
			
			$listParams = array();
			
			if($routeDefaultParams != null && is_array($routeDefaultParams)){
				$matchString = '';
				foreach ($routeDefaultParams as $key => $value) {
					$matchString .='\/'.$key.'\/(?P<'.$key.'>.*)';
				}
				$matchString = '/'.$matchString.'/';
				
				preg_match($matchString, $string2, $matches);
				
				foreach ($routeDefaultParams as $key => $value) {
					$listParams[$key] = empty($matches) ? $routeDefaultParams[$key] : $matches[$key];
				}
			
			}
			
			//Call controller
			$frontController = new FrontController( array(	"controller" => $controllerClassName, 
															"action" => $actionName, 
															"params" => $listParams));
			$frontController->run();
		}catch(Exception $e) {
			echo 'Message: ' .$e->getMessage();
		}
			
	}
}
