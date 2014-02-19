<?php
/**
 * Manage_mail_template
 */
class Migration_1389324080 {
	function __construct() {
		echo "Start migrate file 1389324080.php\n";
	}
					
	function __destruct() {
		echo "***************************************************************\n";
	}
	
	public	function up(){
		echo "function up\n";
		$model = new EmailTemplateModel();
		if (! $model->checkTableExist()){
			$model->migrateUp();
		}
	}
	
	public	function down(){
		echo "function down\n";
		$model = new EmailTemplateModel();
		if ($model->checkTableExist()){
			$model->migrateDown();
		}
	}
}