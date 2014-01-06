<?php
	ini_set('display_errors', '1');
	date_default_timezone_set('Asia/Ho_Chi_Minh');
	$date = new DateTime();
	$date->modify('+1 month');
	echo $date->format('d/m/Y');
?>