<?php
/**
 * create_table_queue_task
 */
class Migration_1392975026 {
	function __construct() {
		echo "Start migrate file 1392975026.php\n";
	}
					
	function __destruct() {
		echo "***************************************************************\n";
	}
	
	public	function up(){
		echo "function up\n";
		$model = new QueueTaskModel();
		$model->migrateUp();
	}
	
	public	function down(){
		echo "function down ------\n";
		$model = new QueueTaskModel();
		$model->migrateDown();
	}
}