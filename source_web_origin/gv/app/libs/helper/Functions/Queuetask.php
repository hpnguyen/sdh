<?php
/**
 * 
 */
class HelperFunctionsQueuetask {
	
	function __construct() {
		
	}
	
	public function query($id = null)
	{
		$model = new QueueTaskModel();
		return $model->readByID($id);
	}
	
	public function executeTaskByID($id = null)
	{
		$ret = $this->query($id);
		
		if ($ret != null){
			$model = new QueueTaskModel();
			try {
				$reflectionMethod = new ReflectionMethod('HelperFunctionsQueuetask', $ret['function_name']);
				
				//Update task to status running
				$model->updateStatusToRunningByID($id);
				
				if ($ret['params_json'] == ''){
					$ret = $reflectionMethod->invokeArgs(new HelperFunctionsQueuetask(), array());
				}else{
					$param = json_decode($ret['params_json']);
					$ret = $reflectionMethod->invokeArgs(new HelperFunctionsQueuetask(), $param);
				}
				//Update task to status done success after run
				$model->updateStatusToDoneSuccessByID($id);
			} catch (Exception $e) {
				//Update task to status done error after run
				$model->updateStatusToDoneErrorByID($id, $e->getMessage());
			}
		}else{
			return null;
		}
	}
	
	public function add($functionName, $paramArray =  null)
	{
		$check = method_exists('HelperFunctionsQueuetask', $functionName);
		if ($check) {
			$model = new QueueTaskModel();
			return $model->addNew($functionName, $paramArray);
		} else {
			return false;
		}
	}
	
	public function getRowPendingTask($all = false)
	{
		$model = new QueueTaskModel();
		return $model->addNew($functionName, $paramArray);
	}
	
	public function exampleFunctionTest1()
	{
		echo "do function 1";
	}
	
	public function exampleFunctionTest2($param1 = null, $param2 = null)
	{
		//var_dump($param1,$param2);
		echo "do function 2";
	}
}