<?php
class Cronjob_Example {
	function __construct() {
		
	}
					
	public	function execute() {
		echo "Ready run example cronjob.\n";
		//Use log to write log
		$log = new logfile('cronjob_test_first');
		$log->write("Function run ");
		echo "End\n";
	}
}