<?php
if (isset($_REQUEST["hisid"])){
	session_id($_REQUEST["hisid"]);
	session_start();
}
if (!isset($_SESSION['uidloginPortal'])){
	die('Truy cập bất hợp pháp'); 
}

include "libs/connect.php";

$email=trim($_POST["acc_email"]);
$usr=trim($_POST["acc_usrname"]);
$pass=trim($_POST["acc_pass"]);

//echo  '{"msg":"'.$usr.'"}';

if ($usr!="" && $pass != "") 
{	
	$strsql="SELECT ho 
	FROM nhan_su 
	WHERE upper(username)=upper('".str_replace("'", "''",$usr)."') and password='".str_replace("'", "''",$pass)."'";
	
	$oci_pa = oci_parse($db_conn, $strsql); //gan cau query
	oci_execute($oci_pa);
	$n=oci_fetch_all($oci_pa, $kt);//lay du lieu  
	
	if ($n>0 && $email!="") 
	{
		
		$strsql="SELECT id
		FROM nhan_su 
		WHERE upper(username) <> upper('".str_replace("'", "''",$usr)."') 
		and upper(email)=upper('".str_replace("'", "''",$email)."')";
		$oci_pa = oci_parse($db_conn, $strsql); //gan cau query
		oci_execute($oci_pa);
		$n=oci_fetch_all($oci_pa, $kt);//lay du lieu  
		
		if ($n >0)
		{
			echo '{"msg":"Email này đã có người đăng ký"}';
		}
		else
		{
			$strsql="update nhan_su set email ='".str_replace("'", "''",$email)."'
			where upper(username)=upper('".str_replace("'", "''",$usr)."')";
			
			$oci_pa = ociparse($db_conn,$strsql); //gan cau query
			oci_execute($oci_pa);
			
			echo  '{"msg":"Đã lưu thay đổi"}';
		}
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