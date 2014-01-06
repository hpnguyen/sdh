<?php
if (isset($_REQUEST["hisid"])){
	session_id($_REQUEST["hisid"]);
	session_start();
}
if (!isset($_SESSION['uidloginPortal'])){
	die('Đã hết phiên làm việc'); 
}

include "../libs/connect.php";
include "../libs/pgslibs.php";

if (!allowPermisstion(base64_decode($_SESSION['uidloginPortal']), '001', $db_conn)){
	die('Truy cập bất hợp pháp'); 
}

$macb = $_SESSION['macb'];
$type = $_REQUEST['w'];
$foldername = "anh46";
$uploaddir = "./$foldername/";
$maxfilesize = "1MB";

if ($type=='uploadfilegv' && isset($_FILES["gv_file_chandung"])){
	$filename = $_FILES["gv_file_chandung"]["name"];
	
	if ($_FILES["gv_file_chandung"]["error"] > 0){
		if ($_FILES["gv_file_chandung"]["error"]=='2'){
			echo "Lỗi: File vượt quá kích thước tối đa $maxfilesize";
		}else{
			echo "Lỗi: " . $_FILES["gv_file_chandung"]["error"];
		}
    }else{
		
		
		if (!mkdir($uploaddir, 0, true)) {	}
		$ext = pathinfo($filename); $ext= $ext['extension'];
		$filename = str_replace(".", "_", $macb).".$ext";
		$uploadfile = $uploaddir . $filename;
		
        //move the uploaded file to uploads folder;
        if (move_uploaded_file($_FILES["gv_file_chandung"]["tmp_name"],$uploaddir.$filename)){
			$result = "./gv/$foldername/$filename";
			echo "$result";
			$strsql="UPDATE CAN_BO_GIANG_DAY SET HINH_ANH='$result' WHERE MA_CAN_BO = '$macb'";
			$oci_pa = oci_parse($db_conn,$strsql);oci_execute($oci_pa);oci_free_statement($oci_pa);
		}else{
			echo "Lỗi: không thể ghi file lên server";
		}
    }
	//echo 'Here is some more debugging info:';
	//print_r($_FILES);
}

if (isset ($db_conn))
	oci_close($db_conn);
?>