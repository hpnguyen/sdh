<?php
/**
 * create_table_login_attempts
 */
class Migration_1396230479 {
	function __construct() {
		echo "Start migrate file 1396230479.php\n";
	}
					
	function __destruct() {
		echo "***************************************************************\n";
	}
	
	public	function up(){
		echo "function up - create table login_attempts\n";
		$model = new LoginAttemptsModel();
		$model->migrateUp();
	}
	
	public	function down(){
		echo "function down - drop table login_attempts\n";
		$model = new LoginAttemptsModel();
		$model->migrateDown();
	}
}