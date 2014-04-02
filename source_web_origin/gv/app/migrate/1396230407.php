<?php
/**
 * create_table_user_members
 */
class Migration_1396230407 {
	function __construct() {
		echo "Start migrate file 1396230407.php\n";
	}
					
	function __destruct() {
		echo "***************************************************************\n";
	}
	
	public	function up(){
		echo "function up - create table user_members\n";
		$model = new UserMembersModel();
		$model->migrateUp();
	}
	
	public	function down(){
		echo "function down - drop table user_members\n";
		$model = new UserMembersModel();
		$model->migrateDown();
	}
}