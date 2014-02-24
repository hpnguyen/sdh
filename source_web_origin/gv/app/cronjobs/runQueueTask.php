<?php
class Cronjob_RunQueueTask extends Queuetaskbase {
	function __construct() {
		
	}
	
	/*
	 * Cronjob run function
	 */
	public function run() {
		date_default_timezone_set('Asia/Ho_Chi_Minh');
		echo "[".date("d-m-Y H:i:s")."]\n";
		//Get the first queue task which has status is pending
		$model = new QueueTaskModel();
		$row = $model->getRowsByStatus(0);
		
		if ($row != null){
			$rowID = $row['id'];
			Helper::getHelper('functions/queuetask')->executeTaskByID($rowID);
			//Get status after run
			$status = $model->getStatusByID($rowID, false);
			echo "\nRun task params [ id => ".$rowID."] : ".$status."";
		}else{
			echo "No have task to run";
		}
		unset($model);
	}
}