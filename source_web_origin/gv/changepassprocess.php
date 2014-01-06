<?php
if (isset($_REQUEST["hisid"])){
	session_id($_REQUEST["hisid"]);
	session_start();
}
if (!isset($_SESSION['uidloginPortal'])){
	die('Truy cập bất hợp pháp'); 
}

include "libs/connect.php";

$usr=trim($_POST["usrname"]);
$pass=trim($_POST["pass"]);
$passnew = trim($_POST["passnew"]);

//echo  '{"msg":"'.$usr.'"}';

if ($usr!="" && $pass != "") 
{	
	$strsql="SELECT ho 
	FROM nhan_su 
	WHERE upper(username)=upper('".str_replace("'", "''",$usr)."') and password='".str_replace("'", "''",$pass)."'";
	
	$oci_pa = oci_parse($db_conn, $strsql); //gan cau query
	oci_execute($oci_pa);
	$n=oci_fetch_all($oci_pa, $kt);//lay du lieu  
	
	if ($n>0 && $passnew!="") {
		
		$strsql="UPDATE nhan_su SET first_login=0, password='".str_replace("'", "''",$passnew)."'
		WHERE upper(username)=upper('".str_replace("'", "''",$usr)."') 
		AND password='".str_replace("'", "''",$pass)."'";
		
		$oci_pa = oci_parse($db_conn,$strsql); //gan cau query
		oci_execute($oci_pa);
		
		echo  '{"msg":"Mật Khẩu đã thay đổi"}';

	}
	else{
		echo '{"msg":"Người Dùng và Mật Khẩu không chính xác"}';
	}
	oci_free_statement($oci_pa);
}
?>

<?php 
if (isset ($db_conn))
	oci_close($db_conn);
?>