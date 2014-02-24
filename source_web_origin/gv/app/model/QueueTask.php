<?php
/**
 * 
 */
class QueueTaskModel extends BaseTable {
	const STATUS_PENDING = 0;
	const STATUS_RUNNING = 1;
	const STATUS_DONE_SUCCESS = 2;
	const STATUS_DONE_ERROR = 3;
	
	function __construct() {
		parent::init("queue_task");
	}
	
	function __destruct() {
		parent::__destruct();
	}
	
	public function migrateUp()
	{
		if (! $this->checkTableExist()){
			$primaryKeys = array('id');
			$fieldsData = array(
				'id' => array('number', null),
				'function_name'=> array('varchar2(200)','NOT NULL'),
				'params_json' => array('varchar2(4000)', null),
				'status' => array('number(1)', 'DEFAULT 0'),
				'log' => array('varchar2(4000)', null),
				'created_at' => array('timestamp', 'DEFAULT CURRENT_TIMESTAMP'),
				'updated_at' => array('timestamp', 'DEFAULT CURRENT_TIMESTAMP')
			);
			$this->getCreate($primaryKeys,$fieldsData)->execute(true, array());
		}
	}
	
	public function migrateDown()
	{
		if ($this->checkTableExist()){
			$this->getDrop()->execute(true, array());
		} 
	}
	
	public function doCreateUpdate($data)
	{
		if (is_array($data)){
			if (! isset($data['id'])) {
				//Insert new record
				$check = $this->getSelect('*')->execute(false, array());
				$data['id'] = $check->itemsCount > 0 ? $check->itemsCount + 1 : 1;
				
				$this->getInsert($data)->execute(true, array());
			}else{
				//Update record
				if(! isset($data['updated_at'])){
					$data['updated_at'] = 'CURRENT_TIMESTAMP';
				}
				
				$this->getUpdate($data)->where("id = ".$data['id'])->execute(true, array());
			}
			
			return $data['id'];
		}else{
			return null;
		}
	}
	
	public function addNew($functionName, $paramArray = null)
	{
		//Insert new record
		$data = array();
		$data['function_name'] = $functionName;
		if ($paramArray != null && is_array($paramArray)){
			$data['params_json'] = ''.str_replace("'", "''", json_encode($paramArray));
		}
		return $this->doCreateUpdate($data);
	}
	
	public function readByID($id) {
		$check = $this->getSelect('*')->where("id = ".$id)->execute(false, array());
		
		if($check->itemsCount > 0){
			return $check->result[0];
		}else{
			return null;
		}
	}
	
	public function deleteByID($id) {
		$this->getDelete("id = ".$id)->execute(true, array());
	}
	/*
	 * @param 	id	Record primary key
	 * @return 	null	No have any record
	 * 			0		Pending
	 * 			1		Running
	 * 			2		Done success
	 * 			3		Done error
	 */
	public function getStatusByID($id ,$returnNumber = true){
		$check = $this->getSelect('*')->where("id = ".$id)->execute(false, array());
		
		if($check->itemsCount > 0){
			$item = $check->result[0];
			
			if ($returnNumber == true){
				//Return status by number
				return $item['status'];
			}else{
				//Return status by string
				$status = array('Pending', 'Running', 'Done success','Done error');
				return $status[$item['status']];
			}
		}else{
			return null;
		}
	}
	
	public function updateStatusToPendingByID($id){
		$data = array('status' => 0, 'updated_at' => 'CURRENT_TIMESTAMP');
		$this->getUpdate($data)->where("id = ".$id)->execute(true, array());
	}
	
	public function updateStatusToRunningByID($id){
		$data = array('status' => 1, 'updated_at' => 'CURRENT_TIMESTAMP');
		$this->getUpdate($data)->where("id = ".$id)->execute(true, array());
	}
	
	public function updateStatusToDoneSuccessByID($id){
		$data = array('status' => 2, 'updated_at' => 'CURRENT_TIMESTAMP');
		$this->getUpdate($data)->where("id = ".$id)->execute(true, array());
	}
	
	public function updateStatusToDoneErrorByID($id, $errorMessage = null){
		$data = array('status' => 3, 'log' => $errorMessage, 'updated_at' => 'CURRENT_TIMESTAMP');
		$this->getUpdate($data)->where("id = ".$id)->execute(true, array());
	}
	/*
	 * @param 	status	Status value in [0,1,2,3]
	 * @param 	all	Value is false: get first / Value is true: get all
	 * @param 	createdOrderCondition  Value is 'asc' or 'desc'
	 * @param 	updatedOrderCondition  Value is 'asc' or 'desc'
	 * @return 	Array or Null 
	 */
	public function getRowsByStatus($status, $all = false, $createdOrderCondition = 'asc', $updatedOrderCondition = 'asc'){
		$check = $this->getSelect('*')
		->where("status = ".$status)
		->order("created_at ".$createdOrderCondition.", updated_at ".$updatedOrderCondition)
		->execute(false, array());
		
		if ($check->itemsCount > 0){
			if ($all == false) {
				return $check->result[0];
			}else{
				return $check->result;
			}
		}else{
			return null;
		}
	}
	/*
	 * @param 	functionName	Name function string
	 * @param 	status		Status value in [0,1,2,3]
	 * @param 	all		Value is false: get first / Value is true: get all
	 * @param 	orCondition 	Value is false: combine with AND condition in where clause /Value is true: combine with OR condition in where clause  
	 * @param 	createdOrderCondition	Value is 'asc' or 'desc'
	 * @param 	updatedOrderCondition	Value is 'asc' or 'desc'
	 * @return 	Array or Null 
	 */
	public function getRowsByFunctionNameAndStatus($functionName, $status, $all = false, $orCondition = false, $createdOrderCondition = 'asc', $updatedOrderCondition = 'asc'){
		if ($orCondition == false){
			$combineWhereCondition = "AND";
		}else{
			$combineWhereCondition = "OR";
		}
		
		$whereCondition = "status = ".$status." ".$combineWhereCondition." function_name = '".$functionName."'";
		
		$check = $this->getSelect('*')
		->where($whereCondition)
		->order("created_at ".$createdOrderCondition.", updated_at ".$updatedOrderCondition)
		->execute(false, array());
		
		if ($check->itemsCount > 0){
			if ($all == false) {
				return $check->result[0];
			}else{
				return $check->result;
			}
		}else{
			return null;
		}
	}
}
