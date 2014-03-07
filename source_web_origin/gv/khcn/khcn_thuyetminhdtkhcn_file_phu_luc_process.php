<?php
if (isset($_REQUEST["hisid"])){
	session_id($_REQUEST["hisid"]);
	session_start();
}
if (!isset($_SESSION['uidloginPortal'])){
	die('Đã hết phiên làm việc'); 
}

include "../libs/connectnckhda.php";
include "../libs/pgslibs.php";

$khcn_usr = base64_decode($_SESSION["khcn_username"]);
if ($khcn_usr == ""){
	$khcn_usr = base64_decode($_SESSION["uidloginPortal"]);
}
//$usr = base64_decode($_SESSION["uidloginPortal"]);

$type = $_REQUEST['w'];

$uploaddir = "users/$khcn_usr/tmdt_phu_luc/";
$maxfilesize = "1MB";

/*
$strsql="SELECT dot_cap_bang('$mahv') dot_cap_bang FROM dual";
$oci_pa = oci_parse($db_conn,$strsql);oci_execute($oci_pa);$result=oci_fetch_all($oci_pa, $kt);oci_free_statement($oci_pa);
$dotcapbang = $kt["DOT_CAP_BANG"][0];
*/

if ($type=='uploadfile' && isset($_FILES["khcn_file"])){
	$filename = $_FILES["khcn_file"]["name"];
	$ma_tmdt = str_replace("'", "''", $_POST["khcn_file_ma_tmdt"]);
	if ($_FILES["khcn_file"]["error"] > 0){
		if ($_FILES["khcn_file"]["error"]=='2'){
			echo "Lỗi: File vượt quá kích thước tối đa $maxfilesize";
		}else{
			echo "Lỗi: " . $_FILES["khcn_file"]["error"];
		}
    }else{
		if (!mkdir('./'.$uploaddir, 0, true)) {	}
		$ext = pathinfo($filename); $ext= $ext['extension'];
		$filename = $ma_tmdt."_Giai_Trinh_Cac_Khoan_Chi.$ext";
		$link = "./khcn/".$uploaddir.$filename;
		
        //move the uploaded file to uploads folder;
        if (move_uploaded_file($_FILES["khcn_file"]["tmp_name"],'./'.$uploaddir.$filename)){
			echo "<a href='$link' target=_blank>$filename</a>";
			$strsql="update NCKH_THUYET_MINH_DE_TAI set PHU_LUC_GIAI_TRINH_LINK='$link' where MA_THUYET_MINH_DT = '$ma_tmdt'";
			$oci_pa = oci_parse($db_conn_khcn,$strsql);oci_execute($oci_pa);oci_free_statement($oci_pa);
		}else{
			echo "Lỗi: không thể ghi file lên server";
		}
    }
	//echo 'Here is some more debugging info:';
	//print_r($_FILES);
}

if ($type=='uploadfilevonkhac' && isset($_FILES["khcn_file_vonkhac"])){
	$filename = $_FILES["khcn_file_vonkhac"]["name"];
	$ma_tmdt = str_replace("'", "''", $_POST["khcn_file_vonkhac_ma_tmdt"]);
	if ($_FILES["khcn_file_vonkhac"]["error"] > 0){
		if ($_FILES["khcn_file_vonkhac"]["error"]==2){
			echo "Lỗi: File vượt quá kích thước tối đa $maxfilesize";
		}else{
			echo "Lỗi: " . $_FILES["khcn_file_vonkhac"]["error"];
		}
    }else{
		if (!mkdir('./'.$uploaddir, 0, true)) {	}
		$ext = pathinfo($filename); $ext= $ext['extension'];
		$filename = $ma_tmdt."_VB_Chung_Minh_NguonHuyDong_VonKhac.$ext";
		$link = "./khcn/".$uploaddir.$filename;

        //move the uploaded file to uploads folder;
        if (move_uploaded_file($_FILES["khcn_file_vonkhac"]["tmp_name"],'./'.$uploaddir.$filename)){
			echo "<a href='$link' target=_blank>$filename</a>";
			$strsql="update NCKH_THUYET_MINH_DE_TAI set VB_CHUNG_MINH_VON_KHAC_LINK='$link' where MA_THUYET_MINH_DT = '$ma_tmdt'";
			$oci_pa = oci_parse($db_conn_khcn,$strsql);oci_execute($oci_pa);oci_free_statement($oci_pa);
		}else{
			echo "Error: không thể ghi file lên server";
		}
    }
}

// upload file tool quan ly tmdt
if ($type=='uploadfile_ql' && isset($_FILES["khcn_ql_file"])){
	$filename = $_FILES["khcn_ql_file"]["name"];
	$ma_tmdt = str_replace("'", "''", $_POST["khcn_ql_file_ma_tmdt"]);
	if ($_FILES["khcn_ql_file"]["error"] > 0){
		if ($_FILES["khcn_ql_file"]["error"]=='2'){
			echo "Lỗi: File vượt quá kích thước tối đa $maxfilesize";
		}else{
			echo "Lỗi: " . $_FILES["khcn_ql_file"]["error"];
		}
    }else{
		if (!mkdir('./'.$uploaddir, 0, true)) {	}
		$ext = pathinfo($filename); $ext= $ext['extension'];
		$filename = $ma_tmdt."_Giai_Trinh_Cac_Khoan_Chi.$ext";
		$link = "./khcn/".$uploaddir.$filename;
		
        //move the uploaded file to uploads folder;
        if (move_uploaded_file($_FILES["khcn_ql_file"]["tmp_name"],'./'.$uploaddir.$filename)){
			echo "<a href='$link' target=_blank>$filename</a>";
			$strsql="update NCKH_THUYET_MINH_DE_TAI set PHU_LUC_GIAI_TRINH_LINK='$link' where MA_THUYET_MINH_DT = '$ma_tmdt'";
			$oci_pa = oci_parse($db_conn_khcn,$strsql);oci_execute($oci_pa);oci_free_statement($oci_pa);
		}else{
			echo "Lỗi: không thể ghi file lên server";
		}
    }
	//echo 'Here is some more debugging info:';
	//print_r($_FILES);
}

// upload file tool quan ly tmdt
if ($type=='uploadfilevonkhac_ql' && isset($_FILES["khcn_ql_file_vonkhac"])){
	$filename = $_FILES["khcn_ql_file_vonkhac"]["name"];
	$ma_tmdt = str_replace("'", "''", $_POST["khcn_ql_file_vonkhac_ma_tmdt"]);
	if ($_FILES["khcn_ql_file_vonkhac"]["error"] > 0){
		if ($_FILES["khcn_ql_file_vonkhac"]["error"]==2){
			echo "Lỗi: File vượt quá kích thước tối đa $maxfilesize";
		}else{
			echo "Lỗi: " . $_FILES["khcn_ql_file_vonkhac"]["error"];
		}
    }else{
		if (!mkdir('./'.$uploaddir, 0, true)) {	}
		$ext = pathinfo($filename); $ext= $ext['extension'];
		$filename = $ma_tmdt."_VB_Chung_Minh_NguonHuyDong_VonKhac.$ext";
		$link = "./khcn/".$uploaddir.$filename;

        //move the uploaded file to uploads folder;
        if (move_uploaded_file($_FILES["khcn_ql_file_vonkhac"]["tmp_name"],'./'.$uploaddir.$filename)){
			echo "<a href='$link' target=_blank>$filename</a>";
			$strsql="update NCKH_THUYET_MINH_DE_TAI set VB_CHUNG_MINH_VON_KHAC_LINK='$link' where MA_THUYET_MINH_DT = '$ma_tmdt'";
			$oci_pa = oci_parse($db_conn_khcn,$strsql);oci_execute($oci_pa);oci_free_statement($oci_pa);
		}else{
			echo "Error: không thể ghi file lên server";
		}
    }
}

if (isset ($db_conn_khcn))
	oci_close($db_conn_khcn);
?>