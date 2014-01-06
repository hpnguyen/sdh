<?php
if (isset($_REQUEST["hisid"])){
	session_id($_REQUEST["hisid"]);
	session_start();
}
if (!isset($_SESSION['uidloginhv'])){
	die('Truy cập bất hợp pháp'); 
}

include "libs/connect.php";

$email=trim($_POST["hv_info_email"]);
$diachi=trim($_POST["hv_info_diachi"]);
$dienthoai=trim($_POST["hv_info_dienthoai"]);
$donvi=trim($_POST["hv_info_donvicongtac"]);
$usr=trim($_POST["hv_info_usrname"]);
$pass=trim($_POST["hv_info_pass"]);

$cmnd=str_replace("'", "''",trim($_POST["hv_info_so_cmnd"]));
$ngaycap=str_replace("'", "''",trim($_POST["hv_info_ngaycap_cmnd"]));
$noicap=str_replace("'", "''",trim($_POST["hv_info_noicap_cmnd"]));
$sotk=str_replace("'", "''",trim($_POST["hv_info_so_tk"]));

//echo  '{"msg":"'.$usr.'"}';

if ($usr!="" && $pass != "") 
{	
	$strsql="SELECT username 
	FROM nguoi_dung 
	WHERE upper(username)=upper('".str_replace("'", "''",$usr)."') and pass='".str_replace("'", "''",$pass)."'";
	
	$oci_pa = oci_parse($db_conn, $strsql); //gan cau query
	oci_execute($oci_pa);
	$n=oci_fetch_all($oci_pa, $kt);//lay du lieu  
	
	if ($n>0)
	{
		$strsql="update hoc_vien set email ='".str_replace("'", "''",$email)."',
		dia_chi='".str_replace("'", "''",$diachi)."',
		dien_thoai='".str_replace("'", "''",$dienthoai)."',
		don_vi_cong_tac='".str_replace("'", "''",$donvi)."',
		so_cmnd = '$cmnd', ngay_cap=to_date('$ngaycap', 'dd/mm/yyyy'), noi_cap='$noicap', so_tai_khoan = '$sotk'
		where upper(ma_hoc_vien)=upper('".str_replace("'", "''",$usr)."')";
		$oci_pa = oci_parse($db_conn,$strsql);
		
		//file_put_contents("logs.txt", "$strsql");
		
		if (!oci_execute($oci_pa)){
			$e = oci_error($oci_pa);
			$msgerr = $e['message']. " sql: " . $e['sqltext'];
			die ('{"success":"-1", "msg":"'.escapeWEB($msgerr).'"}');
		}
		
		$strsql="update nguoi_dung set email ='".str_replace("'", "''",$email)."'
		where upper(USERNAME)=upper('".str_replace("'", "''",$usr)."')";
		$oci_pa = oci_parse($db_conn,$strsql);
		
		if (!oci_execute($oci_pa)){
			$e = oci_error($oci_pa);
			$msgerr = $e['message']. " sql: " . $e['sqltext'];
			die ('{"success":"-1", "msg":"'.escapeWEB($msgerr).'"}');
		}
		
		echo  '{"success":"1", "msg":"Đã thay đổi thông tin thành công"}';
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