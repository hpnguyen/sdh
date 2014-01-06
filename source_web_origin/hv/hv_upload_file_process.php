<?php
if (isset($_REQUEST["hisid"])){
	session_id($_REQUEST["hisid"]);
	session_start();
}
if (!isset($_SESSION['uidloginhv'])){
	die('Đã hết phiên làm việc'); 
}

include "libs/connect.php";

$usr = base64_decode($_SESSION["uidloginhv"]);
$mahv = base64_decode($_SESSION["mahv"]);
$type = $_REQUEST['w'];
$maxfilesize = "1MB";

$strsql="SELECT dot_cap_bang('$mahv') dot_cap_bang FROM dual";
$oci_pa = oci_parse($db_conn,$strsql);oci_execute($oci_pa);$result=oci_fetch_all($oci_pa, $kt);oci_free_statement($oci_pa);
$dotcapbang = $kt["DOT_CAP_BANG"][0];

//file_put_contents("logs.txt", $dotcapbang);

if ($dotcapbang == ''){
	$strsql="SELECT value FROM config WHERE name='DOT_CAP_BANG'";
	$oci_pa = oci_parse($db_conn,$strsql);oci_execute($oci_pa);$result=oci_fetch_all($oci_pa, $kt);oci_free_statement($oci_pa);
	$dotcapbang = $kt["VALUE"][0];
}

$uploaddir = "hinhkyyeu/$dotcapbang/";

if ($type=='uploadhinhkyyeu' && isset($_FILES["hv_file_ky_yeu"])){
	$filename = $_FILES["hv_file_ky_yeu"]["name"];
	$ma_tmdt = str_replace("'", "''", $_POST["khcn_file_ma_tmdt"]);
	if ($_FILES["hv_file_ky_yeu"]["error"] > 0){
		if ($_FILES["hv_file_ky_yeu"]["error"]=='2'){
			echo "Lỗi: File vượt quá kích thước tối đa $maxfilesize";
		}else{
			echo "Lỗi: " . $_FILES["hv_file_ky_yeu"]["error"];
		}
    }else{
		if (!mkdir('./'.$uploaddir, 0, true)) {	}
		$ext = pathinfo($filename); $ext= $ext['extension'];
		$filename = "$mahv.$ext";
		$link = $uploaddir.$filename;
		
        //move the uploaded file to uploads folder;
        if (move_uploaded_file($_FILES["hv_file_ky_yeu"]["tmp_name"],'./'.$uploaddir.$filename)){
			echo "$link";
			//$strsql="update NCKH_THUYET_MINH_DE_TAI set PHU_LUC_GIAI_TRINH_LINK='$link' where MA_THUYET_MINH_DT = '$ma_tmdt'";
			//$oci_pa = oci_parse($db_conn,$strsql);oci_execute($oci_pa);oci_free_statement($oci_pa);
		}else{
			echo "Lỗi: không thể ghi file lên server, vui lòng liên hệ phòng ĐT SĐH.";
		}
    }
	//echo 'Here is some more debugging info:';
	//print_r($_FILES);
}

if (isset ($db_conn)){
	oci_close($db_conn);
}
?>