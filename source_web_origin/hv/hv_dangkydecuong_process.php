<?php
if (isset($_REQUEST["hisid"])){
	session_id($_REQUEST["hisid"]);
	session_start();
}
if (!isset($_SESSION['uidloginhv'])){
	die('Truy cập bất hợp pháp'); 
}

include "libs/connect.php";
include "libs/pgslibshv.php";

$type = $_POST['w'];
$khoa = $_POST['k'];
$dothoc = $_POST['d'];
$hk = $_POST['h'];
$manganh = base64_decode($_SESSION["manganh"]);
$mahv = base64_decode($_SESSION["mahv"]);

if ($type=='ds_cb_huongdan'){
	# thong tin lam luan van, hoc vien
	$sqlstr = "SELECT ma_can_bo, shcc, ten_bo_mon, ten_khoa, get_thanh_vien(c.ma_can_bo) cbgd
				FROM can_bo_giang_day c, bo_mon b, khoa k
				WHERE 	c.ma_bo_mon = b.ma_bo_mon
						and b.ma_khoa = k.ma_khoa
						and ma_hoc_vi in ('TS', 'TSK') and c.trang_thai=1
				ORDER BY	ten_khoa, ten_bo_mon, ten_eng, ho_eng";
	$stmt = oci_parse($db_conn, $sqlstr);oci_execute($stmt);$n = oci_fetch_all($stmt, $resDM);oci_free_statement($stmt);
			
	$data='{
			"dscanbo": [';
	
	for ($i = 0; $i < $n; $i++)
	{
		$data .= '{
					"value" : "'.$resDM["MA_CAN_BO"][$i].'",
					"label" : "'.escapeJsonString($resDM["CBGD"][$i]).'",
					"desc"  : "'.escapeJsonString($resDM["SHCC"][$i]).', '.escapeJsonString($resDM["TEN_BO_MON"][$i]).', '.escapeJsonString($resDM["TEN_KHOA"][$i]).'"
				  },';
	}
	
	if ($n>0) 
		$data=substr($data,0,-1);
		
	$data.=']
			}';
			
	echo $data;
}else if ($type=='dangkydecuong'){
	$huongnc = str_replace("'", "''",$_POST['dkdc_huongnghiencuu']);
	$hd1 = str_replace("'", "''",$_POST['dkdc_huongdan1_ma']);
	$hd2 = str_replace("'", "''",$_POST['dkdc_huongdan2_ma']);
	$ghichu = str_replace("'", "''",$_POST['dkdc_ghichu']);
	
	$sqlstr= "insert into DANG_KY_DE_CUONG(MA_HOC_VIEN, DOT_HOC, HUONG_NGHIEN_CUU, HUONG_DAN_1, HUONG_DAN_2, GHI_CHU)
	values('$mahv', '$dothoc', '$huongnc', '$hd1', '$hd2', '$ghichu')";
	
	$stmt = oci_parse($db_conn, $sqlstr);
	if (!oci_execute($stmt)){
		echo '{"success":"-1"}';
	}else{
		echo '{
			"success":"1", 
			"huongnc":"'.escapeJsonString($_POST['dkdc_huongnghiencuu']).'", 
			"ghichu":"'.escapeJsonString($_POST['dkdc_ghichu']).'",
			"hd1_ma":"'.escapeJsonString($_POST['dkdc_huongdan1_ma']).'",
			"hd2_ma":"'.escapeJsonString($_POST['dkdc_huongdan2_ma']).'",
			"hd1_ten":"'.escapeJsonString($_POST['dkdc_huongdan1']).'",
			"hd2_ten":"'.escapeJsonString($_POST['dkdc_huongdan2']).'"
		}';
	}
	oci_free_statement($stmt);
}else if ($type=='huydangkydecuong'){
	$ma = str_replace("'", "''",$_POST['m']);
	$sqlstr= "delete DANG_KY_DE_CUONG where DOT_HOC='$dothoc' and MA_HOC_VIEN = '$ma'";
	
	$stmt = oci_parse($db_conn, $sqlstr);
	if (!oci_execute($stmt)){
		echo '{"success":"-1"}';
	}else{
		echo '{"success":"1"}';
	}
}
if (isset ($db_conn))
	oci_close($db_conn);
?>