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

if (!allowPermisstion(base64_decode($_SESSION['uidloginPortal']), '024', $db_conn))
{
	die('Truy cập bất hợp pháp'); 
}

$usr = base64_decode($_SESSION['uidloginPortal']);
$a = str_replace("'", "''",$_REQUEST["a"]);
$m = str_replace("'", "''",$_POST["m"]);

if ($a=='refreshdata')
{
	$sqlstr="SELECT cb.ma_can_bo, cb.shcc, cb.ho, cb.ten, decode(cb.phai, 'M','Nam' ,'F','Nữ') phai_cb, cb.ma_bo_mon, 
				to_char(cb.NGAY_SINH,'dd-mm-yyyy') NGAY_SINH, k.ten_khoa, bm.ten_bo_mon, l.log_nckh,
				to_char(l.ngay_duyet, 'hh24:mi dd/mm/yyyy') ngay_duyet, (l.ngay_duyet - l.time_strap) ngay_tru, 
				to_char(l.time_strap,'hh24:mi dd/mm/yyyy') time_strap_1, get_thanh_vien(cb.ma_can_bo) ho_ten
			FROM can_bo_giang_day cb, bo_mon bm, khoa k, NCKH_LOGS l
			WHERE cb.ma_bo_mon = bm.ma_bo_mon (+) AND bm.ma_khoa = k.ma_khoa (+)
			AND cb.fk_loai_can_bo in ('00', '02') AND cb.ma_can_bo = l.FK_MA_CAN_BO (+)
			ORDER BY ten_khoa, ten_bo_mon, ten, ho";
	$stmt = oci_parse($db_conn, $sqlstr);oci_execute($stmt);$n = oci_fetch_all($stmt, $resDM);oci_free_statement($stmt);
	$data='{
			"aaData":[';
	
	for ($i = 0; $i < $n; $i++)
	{
		$ngay_tru = $resDM["NGAY_TRU"][$i];
		$ngay_duyet = $resDM["NGAY_DUYET"][$i];
		$time_strap = $resDM["TIME_STRAP"][$i];
		$ho_ten = $resDM["HO_TEN"][$i];
		$log = "Cập nhật lần cuối: <b>{$resDM["TIME_STRAP_1"][$i]}</b><br/>Thay đổi: {$resDM["LOG_NCKH"][$i]}";
		if ($ngay_duyet=='' || $ngay_tru < 0)
		{
			$icon = "<img src='icons/red-ok-icon.png' onClick='updatePassed( getRowIndex(this) );' class=tooltips data-placement='left' title='$log' border=0 style='cursor: pointer'>";
			$linkduyet = "$icon";
		}
		else if (($ngay_duyet!='' && $time_strap == '') || $ngay_tru > 0)
		{
			$icon = "<img src='icons/green-ok-icon.png' class=tooltips data-placement='left' title='$ngay_duyet' border=0>";
			$linkduyet = "$icon";
		}
		
		$data.= '["'.$resDM["MA_CAN_BO"][$i].'", 
				  "'.$resDM["SHCC"][$i].'",
				  "'.escapeJsonString($resDM["HO"][$i]).'", 
				  "'.escapeJsonString($resDM["TEN"][$i]).'", 
				  "'.escapeJsonString($resDM["PHAI_CB"][$i]).'", 
				  "'.escapeJsonString($resDM["TEN_KHOA"][$i]).'",
				  "'.escapeJsonString($resDM["TEN_BO_MON"][$i]).'",
				  "'.escapeJsonString($linkduyet).'",
				  "<img src=\'icons/print-preview-icon24x24.png\' class=tooltips data-placement=left title=\'Xem Lý lịch khoa học của<br/>'.$ho_ten.'\' border=0 onClick=\'printLLKH( getRowIndex(this) ); \' style=\'cursor: pointer\'>"],';
	}
	
	$data=substr($data,0,-1);
	
	$data.='	]
			}';
	
	echo $data;
}
else if ($a=="set_ngayduyet")
{
	$sqlstr="update NCKH_LOGS set ngay_duyet=sysdate, log_nckh='' where fk_ma_can_bo = '$m'";
	
	$stmt = oci_parse($db_conn, $sqlstr);
	if (oci_execute($stmt))
	{
		$sqlstr="select to_char(ngay_duyet, 'hh24:mi dd/mm/yyyy') ngay_duyet from NCKH_LOGS where fk_ma_can_bo = '$m'";
		$stmt = oci_parse($db_conn, $sqlstr);oci_execute($stmt);$n = oci_fetch_all($stmt, $resDM);oci_free_statement($stmt);
		$ngay_duyet = $resDM["NGAY_DUYET"][0];
		echo "<img src='icons/green-ok-icon.png' class=tooltips data-placement='left' title='$ngay_duyet' border=0>";	
	}
	
}
?>

<?php
if (isset ($db_conn))
	oci_close($db_conn);
?>