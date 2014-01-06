<?php
if (isset($_REQUEST["hisid"])){
	session_id($_REQUEST["hisid"]);
	session_start();
}
if (!isset($_SESSION['uidloginhv'])){
	die('Truy cập bất hợp pháp'); 
}

include "libs/connect.php";

$usrSession = base64_decode($_SESSION["uidloginhv"]);
$usr=trim($_POST["usrname"]);
$pass=trim($_POST["pass"]);
$passnew = trim($_POST["passnew"]);

//echo  '{"msg":"'.$usr.'"}';

if ($usr!="" && $pass != "") 
{	
	if ($usrSession!=$usr){
		echo '{"msg":"Người Dùng và Mật Khẩu không chính xác", "error":"1"}';
	}
	else
	{
		$strsql="SELECT username FROM nguoi_dung 
				WHERE upper(username)=upper('".str_replace("'", "''",$usr)."') 
				AND pass='".str_replace("'", "''",$pass)."'";
		//echo  '{"msg":"'.base64_encode($strsql).'"}';	
		
		$oci_pa = oci_parse($db_conn, $strsql); //gan cau query
		oci_execute($oci_pa);
		$n=oci_fetch_all($oci_pa, $kt);//lay du lieu  
		oci_free_statement($oci_pa);
		
		if ($n>0 && $passnew!="") {
			$strsql="UPDATE nguoi_dung SET first_login=0, pass ='".str_replace("'", "''",$passnew)."'
			WHERE upper(username)=upper('".str_replace("'", "''",$usr)."') 
			AND pass='".str_replace("'", "''",$pass)."'";
			
			$oci_pa = oci_parse($db_conn,$strsql); //gan cau query
			oci_execute($oci_pa);
			
			echo  '{"msg":"Mật Khẩu đã thay đổi thành công", "error":"0"}';
		}
		else{
			echo '{"msg":"Người Dùng và Mật Khẩu không chính xác", "error":"1"}';
		}

	}
}
?>

<?php 
if (isset ($db_conn))
	oci_close($db_conn);
?>