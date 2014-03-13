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

if (!allowPermisstion(base64_decode($_SESSION['uidloginPortal']), '001', $db_conn))
{
	die('Truy cập bất hợp pháp'); 
}

$macb = $_SESSION['macb'];

$type = $_REQUEST['w'];
/*
file_put_contents("logs.txt", "----------------------------------------------\n
			". date("H:i:s d.m.Y")." $type $macb \n
			----------------------------------------------\n", FILE_APPEND);
*/
if ($type=='uploadfilegv')
{
	$filename = str_replace(".", "_", $macb) . ".jpg"; //basename($_FILES['userfile_ttgv']['name']);
	$foldername = "anh46";
	$uploaddir = "./$foldername/";
	$uploadfile = $uploaddir . $filename; //basename($_FILES['userfile_ttgv']['name']);
	
	
	/*file_put_contents("logs.txt", "----------------------------------------------\n
			". date("H:i:s d.m.Y")." $uploadfile \n
			----------------------------------------------\n", FILE_APPEND);
	*/
	if (!mkdir($uploaddir, 0, true)) {
		//echo "error";
	}
	
	//echo $uploadfile;

	//echo '<pre>';
	if (move_uploaded_file($_FILES['userfile_ttgv']['tmp_name'], $uploadfile)) 
	{
		//echo "File is valid, and was successfully uploaded.\n";
		//echo "<img src='hinhkyyeu/$dotcapbang/$namefile' width=113 height=170 class='ui-widget-content ui-corner-all'/>";
		$result = "./gv/$foldername/$filename";
		$strsql="UPDATE CAN_BO_GIANG_DAY SET HINH_ANH='$result' WHERE MA_CAN_BO = '$macb'";
		$oci_pa = oci_parse($db_conn,$strsql);oci_execute($oci_pa);oci_free_statement($oci_pa);
		
		echo $result;
	} 
	else 
	{
		echo "error";
	}

}else if ($type=='uploadfilechukygv'){
	$filename = str_replace(".", "_", $macb)."_chu_ky.jpg"; //basename($_FILES['userfile_ttgv']['name']);
	$foldername = "anh46";
	$uploaddir = "./$foldername/";
	$uploadfile = $uploaddir . $filename; //basename($_FILES['userfile_ttgv']['name']);
	if (!mkdir($uploaddir, 0, true)){
		//echo "error";
	}
	if (move_uploaded_file($_FILES['userfile_ttgv']['tmp_name'], $uploadfile)){
		//echo "File is valid, and was successfully uploaded.\n";
		$result = "./gv/$foldername/$filename";
		$strsql="UPDATE CAN_BO_GIANG_DAY SET HINH_ANH_CHU_KY='$result' WHERE MA_CAN_BO = '$macb'";
		$oci_pa = oci_parse($db_conn,$strsql);oci_execute($oci_pa);oci_free_statement($oci_pa);
		echo $result;
	} 
	else{
		echo "error";
	}
}

if (isset ($db_conn))
	oci_close($db_conn);
?>