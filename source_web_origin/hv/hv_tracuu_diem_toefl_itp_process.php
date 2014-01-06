<?php
if (isset($_REQUEST["hisid"])){
	$sid = $_REQUEST["hisid"];
	session_id($sid);
	session_start();
}
if (!isset($_SESSION['hv_tra_cuu_toefl'])){
	die('Truy cập bất hợp pháp'); 
}

include "libs/connect.php";
include "libs/pgslibshv.php";
?>
<?php

$type = escape($_POST['w']);
$cmnd = escape($_POST['cmnd']);
$ngaythi = escape($_POST['n']);

if ($type=='cmnd_ngaythi-diemtoefl')
{
	if ($cmnd!='' && $ngaythi!='')
	{
		$sqlstr = "select ho || ' ' || ten ho_ten, to_char(ngay_sinh, 'dd/mm/yyyy') ngay_sinh, gio_thi, phong_thi, LISTENING, READING, STRUCTURES_WRITTEN, TOTAL
			from THI_AV_ETS where SO_CMND = '$cmnd' and ngay_thi = to_date('$ngaythi', 'DD/MM/YYYY')";
		//echo $sqlstr;	
		$stmt = oci_parse($db_conn, $sqlstr);oci_execute($stmt);$n=oci_fetch_all($stmt, $hocvien);oci_free_statement($stmt);
		
		if($n)
		{
			echo "<div align=center style='margin:0 0 10px 5px; font-size:14px;'><b>KẾT QUẢ THI TOEFL ITP</b></div>";
			
			echo "<table border='0' align=center cellspacing='0' cellpadding='3' class='ui-widget ui-widget-content ui-corner-top tableData'>";
			
			echo "<tr><td class='ui-widget-header'>Họ và tên</td> <td class='' style='font-weight:bold'>". $hocvien["HO_TEN"][0] ."</td></tr>";
			echo "<tr><td class='ui-widget-header'>Ngày sinh</td><td class=''>". $hocvien["NGAY_SINH"][0] ."</td></tr>";
			echo "<tr><td class='ui-widget-header'>Giờ thi</td><td class=''>". $hocvien["GIO_THI"][0] ."</td></tr>";
			echo "<tr><td class='ui-widget-header'>Phòng thi</td><td class=''>". $hocvien["PHONG_THI"][0]."</td></tr>";
			echo "<tr><td class='ui-widget-header'>Ngày thi</td><td class=''>". $ngaythi."</td></tr>";
			echo "<tr><td class='ui-widget-header'>LISTENING</td><td style='font-weight:bold'>". $hocvien["LISTENING"][0] ."</td></tr>";
			echo "<tr><td class='ui-widget-header'>READING</td><td style='font-weight:bold'>". $hocvien["READING"][0] ."</td></tr>";
			echo "<tr><td class='ui-widget-header'>STRUCTURES WRITTEN</td><td style='font-weight:bold'>". $hocvien["STRUCTURES_WRITTEN"][0] ."</td></tr>";
			echo "<tr><td class='ui-widget-header'>TOTAL</td><td style='font-weight:bold'>". $hocvien["TOTAL"][0] ."</td></tr>";
			
			echo "</table>";
		}
		else
		{
			echo "<div style='font-size:12px; font-weight:bold;'>Không tìm thấy thí sinh dự thi</div>";
		}
	}
}
?>

<?php 
if (isset ($db_conn))
	oci_close($db_conn);
?>