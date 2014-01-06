<?php
if (isset($_REQUEST["hisid"])){
	session_id($_REQUEST["hisid"]);
	session_start();
}
if (!isset($_SESSION['uidloginPortal'])){
	die('{"success":"-2", "msg":"Đã hết thời gian phiên làm việc, vui lòng đăng nhập lại."}');
}

include "../libs/connect.php";
include "../libs/connectdbweb.php";
include "../libs/pgslibs.php";

$macb = $_SESSION['macb'];
$makhoa = base64_decode($_SESSION['makhoa']);
$a = $_REQUEST['a'];

if ($a=='checksession'){
	die('{"success":"1"}'); 
}
else if ($a=='saveDKMHconf') {
	$from = str_replace ("'", "''", $_POST["from"]); // tu ngay
	$to = str_replace ("'", "''", $_POST["to"]); // den ngay
	$dothoc = str_replace ("'", "''", $_POST["dothoc"]);
	
	// update ngay bat dau
	$sqlstr="update config set value = '$from' WHERE name='DKMH_NGAY_BAT_DAU'";
	$stmt = oci_parse($db_conn, $sqlstr);
	if (oci_execute($stmt)){
		oci_free_statement($stmt);
		// update ngay het han
		$sqlstr="update config set value = '$to' WHERE name='DKMH_NGAY_HET_HAN'";
		$stmt = oci_parse($db_conn, $sqlstr);
		if (oci_execute($stmt)){
			oci_free_statement($stmt);
			
			$sqlstr="update config set value = '$dothoc' WHERE name='DOT_HOC_DKMH'";
			$stmt = oci_parse($db_conn, $sqlstr);
			oci_execute($stmt);
			//echo '{"success":"1"}';
		}else{
			$e = oci_error($stmt);
			$msgerr = $e['message']. " sql: " . $e['sqltext'];
			die( '{"success":"-1", "msg":"'.escapeJsonString($msgerr).'"}');
		}
	}else{
		$e = oci_error($stmt);
		$msgerr = $e['message']. " sql: " . $e['sqltext'];
		die( '{"success":"-1", "msg":"'.escapeJsonString($msgerr).'"}');
	}
	
	// update db web
	// update ngay bat dau
	$sqlstr="update config set value = '$from' WHERE name='DKMH_NGAY_BAT_DAU'";
	$stmt = oci_parse($db_conn_hv, $sqlstr);
	if (oci_execute($stmt)){
		oci_free_statement($stmt);
		// update ngay het han
		$sqlstr="update config set value = '$to' WHERE name='DKMH_NGAY_HET_HAN'";
		$stmt = oci_parse($db_conn_hv, $sqlstr);
		if (oci_execute($stmt)){
			oci_free_statement($stmt);
			
			$sqlstr="update config set value = '$dothoc' WHERE name='DOT_HOC_DKMH'";
			$stmt = oci_parse($db_conn_hv, $sqlstr);

			if (oci_execute($stmt)){
				echo '{"success":"1"}';
			}else{
				$e = oci_error($stmt);
				$msgerr = $e['message']. " sql: " . $e['sqltext'];
				die('{"success":"-1", "msg":"'.escapeJsonString($msgerr).'"}');
			}
		}else{
			$e = oci_error($stmt);
			$msgerr = $e['message']. " sql: " . $e['sqltext'];
			die( '{"success":"-1", "msg":"'.escapeJsonString($msgerr).'"}');
		}
	}else{
		$e = oci_error($stmt);
		$msgerr = $e['message']. " sql: " . $e['sqltext'];
		die( '{"success":"-1", "msg":"'.escapeJsonString($msgerr).'"}');
	}
}
else if ($a=='saveDKMHNVconf') {
	$from = str_replace ("'", "''", $_POST["from"]); // tu ngay
	$to = str_replace ("'", "''", $_POST["to"]); // den ngay
	
	// update ngay bat dau
	$sqlstr="update config set value = '$from' WHERE name='DKMH_NV_NGAY_BAT_DAU'";
	$stmt = oci_parse($db_conn, $sqlstr);
	if (oci_execute($stmt)){
		oci_free_statement($stmt);
		// update ngay het han
		$sqlstr="update config set value = '$to' WHERE name='DKMH_NV_NGAY_HET_HAN'";
		$stmt = oci_parse($db_conn, $sqlstr);
		if (oci_execute($stmt)){
			oci_free_statement($stmt);
			//echo '{"success":"1"}';
		}else{
			$e = oci_error($stmt);
			$msgerr = $e['message']. " sql: " . $e['sqltext'];
			echo '{"success":"-1", "msg":"'.escapeJsonString($msgerr).'"}';
		}
	}else{
		$e = oci_error($stmt);
		$msgerr = $e['message']. " sql: " . $e['sqltext'];
		echo '{"success":"-1", "msg":"'.escapeJsonString($msgerr).'"}';
	}
	
	// update db web
	// update ngay bat dau
	$sqlstr="update config set value = '$from' WHERE name='DKMH_NV_NGAY_BAT_DAU'";
	$stmt = oci_parse($db_conn_hv, $sqlstr);
	if (oci_execute($stmt)){
		oci_free_statement($stmt);
		// update ngay het han
		$sqlstr="update config set value = '$to' WHERE name='DKMH_NV_NGAY_HET_HAN'";
		$stmt = oci_parse($db_conn_hv, $sqlstr);
		if (oci_execute($stmt)){
			oci_free_statement($stmt);
			echo '{"success":"1"}';
		}else{
			$e = oci_error($stmt);
			$msgerr = $e['message']. " sql: " . $e['sqltext'];
			echo '{"success":"-1", "msg":"Connect web: '.escapeJsonString($msgerr).'"}';
		}
	}else{
		$e = oci_error($stmt);
		$msgerr = $e['message']. " sql: " . $e['sqltext'];
		echo '{"success":"-1", "msg":"Connect web: '.escapeJsonString($msgerr).'"}';
	}
}
else if ($a=='saveDKONTAPconf') {
	$to = str_replace ("'", "''", $_POST["to"]); // den ngay
	$tb = str_replace ("'", "''", $_POST["tb"]); // thong bao
	
	// update ngay het han dang ky on tap
	$sqlstr="update config set value = '$to' WHERE name='NGAY_HET_HAN_ON_DK'";
	$stmt = oci_parse($db_conn, $sqlstr);
	if (oci_execute($stmt)){
		oci_free_statement($stmt);
		$sqlstr="update config set value = '$tb' WHERE name='DK_ON_TAP_THONG_BAO'";
		$stmt = oci_parse($db_conn, $sqlstr);
		if (oci_execute($stmt)){
			oci_free_statement($stmt);
			//echo '{"success":"1"}';
		}else{
			$e = oci_error($stmt);
			$msgerr = $e['message']. " sql: " . $e['sqltext'];
			die ('{"success":"-1", "msg":"'.escapeJsonString($msgerr).'"}');
		}
	}else{
		$e = oci_error($stmt);
		$msgerr = $e['message']. " sql: " . $e['sqltext'];
		die ('{"success":"-1", "msg":"'.escapeJsonString($msgerr).'"}');
	}
	
	// update db web
	// update ngay het han dang ky on tap
	$sqlstr="update config set value = '$to' WHERE name='NGAY_HET_HAN_ON_DK'";
	$stmt = oci_parse($db_conn_hv, $sqlstr);
	if (oci_execute($stmt)){
		oci_free_statement($stmt);
		$sqlstr="update config set value = '$tb' WHERE name='DK_ON_TAP_THONG_BAO'";
		$stmt = oci_parse($db_conn_hv, $sqlstr);
		if (oci_execute($stmt)){
			oci_free_statement($stmt);
			echo '{"success":"1"}';
		}else{
			$e = oci_error($stmt);
			$msgerr = $e['message']. " sql: " . $e['sqltext'];
			die ('{"success":"-1", "msg":"'.escapeJsonString($msgerr).'"}');
		}
	}else{
		$e = oci_error($stmt);
		$msgerr = $e['message']. " sql: " . $e['sqltext'];
		die ('{"success":"-1", "msg":"Connect web: '.escapeJsonString($msgerr).'"}');
	}
}
else if ($a=='saveDKYCHVconf') {
	$allow = str_replace ("'", "''", $_POST["value1"]); // cho phep dang ky
	
	// update cho phep dang ky ychv online local
	$sqlstr="update config set value = '$allow' WHERE name='YCHVU_DK_CHO_PHEP'";
	$stmt = oci_parse($db_conn, $sqlstr);
	if (oci_execute($stmt)){
		oci_free_statement($stmt);
		// update cho phep dang ky ychv online web
		$sqlstr="update config set value = '$allow' WHERE name='YCHVU_DK_CHO_PHEP'";
		$stmt = oci_parse($db_conn_hv, $sqlstr);
		if (oci_execute($stmt)){
			oci_free_statement($stmt);
			echo '{"success":"1"}';
		}else{
			$e = oci_error($stmt);
			$msgerr = $e['message']. " sql: " . $e['sqltext'];
			die ('{"success":"-1", "msg":"'.escapeJsonString($msgerr).'"}');
		}
	}else{
		$e = oci_error($stmt);
		$msgerr = $e['message']. " sql: " . $e['sqltext'];
		die ('{"success":"-1", "msg":"'.escapeJsonString($msgerr).'"}');
	}
	
}
else if ($a=='saveDKDCconf') {
	$from = str_replace ("'", "''", $_POST["from"]); // tu ngay
	$to = str_replace ("'", "''", $_POST["to"]); // den ngay
	$dothoc = str_replace ("'", "''", $_POST["dothoc"]);
	// update ngay bat dau
	$sqlstr="update config set value = '$from' WHERE name='DK_DC_NGAY_BD'";
	$stmt = oci_parse($db_conn, $sqlstr);
	if (oci_execute($stmt)){
		oci_free_statement($stmt);
		// update ngay het han
		$sqlstr="update config set value = '$to' WHERE name='DK_DC_NGAY_KT'";
		$stmt = oci_parse($db_conn, $sqlstr);
		if (oci_execute($stmt)){
			oci_free_statement($stmt);
			
			$sqlstr="update config set value = '$dothoc' WHERE name='DK_DC_DOT_HOC'";
			$stmt = oci_parse($db_conn, $sqlstr);
			oci_execute($stmt);
			//echo '{"success":"1"}';
		}else{
			$e = oci_error($stmt);
			$msgerr = $e['message']. " sql: " . $e['sqltext'];
			die('{"success":"-1", "msg":"'.escapeJsonString($msgerr).'"}');
		}
	}else{
		$e = oci_error($stmt);
		$msgerr = $e['message']. " sql: " . $e['sqltext'];
		die('{"success":"-1", "msg":"'.escapeJsonString($msgerr).'"}');
	}
	
	// update db web
	// update ngay bat dau
	$sqlstr="update config set value = '$from' WHERE name='DK_DC_NGAY_BD'";
	$stmt = oci_parse($db_conn_hv, $sqlstr);
	if (oci_execute($stmt)){
		oci_free_statement($stmt);
		// update ngay het han
		$sqlstr="update config set value = '$to' WHERE name='DK_DC_NGAY_KT'";
		$stmt = oci_parse($db_conn_hv, $sqlstr);
		if (oci_execute($stmt)){
			oci_free_statement($stmt);
			
			$sqlstr="update config set value = '$dothoc' WHERE name='DK_DC_DOT_HOC'";
			$stmt = oci_parse($db_conn_hv, $sqlstr);
			if (oci_execute($stmt)){
				oci_free_statement($stmt);
				echo '{"success":"1"}';
			}else{
				$e = oci_error($stmt);
				$msgerr = $e['message']. " sql: " . $e['sqltext'];
				die('{"success":"-1", "msg":"'.escapeJsonString($msgerr).'"}');
			}
		}else{
			$e = oci_error($stmt);
			$msgerr = $e['message']. " sql: " . $e['sqltext'];
			die('{"success":"-1", "msg":"'.escapeJsonString($msgerr).'"}');
		}
	}else{
		$e = oci_error($stmt);
		$msgerr = $e['message']. " sql: " . $e['sqltext'];
		die('{"success":"-1", "msg":"'.escapeJsonString($msgerr).'"}');
	}
}

if (isset ($db_conn)){
	oci_close($db_conn);
}
if (isset ($db_conn_hv)){
	oci_close($db_conn_hv);
}
?>