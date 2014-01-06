<?php
if (isset($_REQUEST["hisid"])){
	session_id($_REQUEST["hisid"]);
	session_start();
}
//if (!isset($_SESSION['uidloginhv'])){
//	die('Truy cập bất hợp pháp'); 
//}

include "libs/connect.php";
?>

<?php

$type = $_REQUEST['w'];
$khoa = $_REQUEST['k'];
$dothoc = $_REQUEST['d'];
$hk = $_REQUEST['h'];
$ma_nganh = $_REQUEST['n'];
$thu = array("1"=>"CN", "2"=>"Hai", "3"=>"Ba", "4"=>"Tư", "5"=>"Năm", "6"=>"Sáu", "7"=>"Bảy");

if ($type=='hk-khoa')
{
	$sqlstr="SELECT DISTINCT khoa 
			FROM thoi_khoa_bieu
			WHERE khoa in (SELECT khoa FROM thoi_khoa_bieu t, lich_thi l 
							WHERE t.dot_hoc= l.dot_hoc AND t.ma_mh = l.ma_mh AND t.lop = l.lop AND t.dot_hoc='$dothoc' )
			ORDER BY khoa desc";
	$stmt = oci_parse($db_conn, $sqlstr);
	oci_execute($stmt);
	$n = oci_fetch_all($stmt, $resDM);
	oci_free_statement($stmt);

	for ($i = 0; $i < $n; $i++)
	{
		echo "<option value='{$resDM['KHOA'][$i]}'>{$resDM['KHOA'][$i]}</option>";
	}
}
elseif ($type=='khoa_hk-nganh')
{
	$sqlstr="SELECT distinct n.ma_nganh, ten_nganh 
			FROM thoi_khoa_bieu t, nganh n, lich_thi l
			WHERE n.ma_nganh = t.ma_nganh 
			AND t.dot_hoc= l.dot_hoc AND t.ma_mh = l.ma_mh AND t.lop = l.lop
			AND t.khoa = $khoa 
			AND t.dot_hoc = '$dothoc'
			ORDER BY ten_nganh";
			
	$stmt = oci_parse($db_conn, $sqlstr);
	oci_execute($stmt);
	$n = oci_fetch_all($stmt, $resDM);
	oci_free_statement($stmt);

	for ($i = 0; $i < $n; $i++)
	{
		echo "<option value='".$resDM["MA_NGANH"][$i]."'>" .$resDM["TEN_NGANH"][$i]. "</option>";
	}
}
elseif ($type=='khoa_hk_nganh-lichthi_nganh')
{
	$sqlstr="SELECT DISTINCT ten_nganh, l.ma_mh, ten_mh, l.lop, ma_can_bo, ho_ten, l.ngay_thi ngay, 
					to_char(l.ngay_thi,'DD/MM/YYYY') ngay_thi,
					to_char(l.ngay_thi,'d') thu, l.gio_thi, l.phong_thi
			FROM thoi_khoa_bieu t, nganh n, lich_thi l
			WHERE t.ma_nganh = n.ma_nganh 
			AND  t.ma_nganh = '$ma_nganh' 
			AND khoa = '$khoa'
			AND t.dot_hoc = '$dothoc'
			AND t.dot_hoc = l.dot_hoc
			AND t.ma_mh = l.ma_mh
			AND t.lop = l.lop
			ORDER BY ngay ASC, gio_thi ASC, lop, phong_thi, ten_mh";
	$stmt = oci_parse($db_conn, $sqlstr);
	oci_execute($stmt);
	$n = oci_fetch_all($stmt, $resDM);
	oci_free_statement($stmt);
	
	echo "
	<div style='margin-bottom:10px; font-size:14px;' align=center><b>LỊCH THI CAO HỌC HK $hk</b><br/> Ngành: <b>{$resDM['TEN_NGANH'][0]}</b></div>
	<table width='100%' border='0'  cellspacing='0' cellpadding='3' class='ui-widget ui-widget-content ui-corner-top tableData' >
	<thead>
	  <tr class='ui-widget-header heading' style='height:20pt;'>
		<td class='ui-corner-tl' align='left'>STT</td>
		<td >Môn học</td>
		<td >CBGD</td>
		<td align='center'>Lớp</td>
		<td align='left'>Thứ</td>
		<td align='center'>Ngày thi</td>
		<td align='center'>Giờ</td>
		<td class='ui-corner-tr' align='right'>Phòng</td>
	  </tr>
	  </thead>
	  <tbody>
	";
	$classAlt="alt";
	for ($i = 0; $i < $n; $i++)
	{
		($classAlt=='alt') ? $classAlt="alt_" : $classAlt="alt";
		
		echo "<tr align='left' valign='top' class=' ".$classAlt."' style='height:20px;'>";
		echo "<td valign=middle align=left>".($i+1)."</td>";
		echo "<td valign=middle align=left>({$resDM['MA_MH'][$i]}) - {$resDM['TEN_MH'][$i]}</td>";
		echo "<td valign=middle align='left'>{$resDM['HO_TEN'][$i]}</td>";
		echo "<td valign=middle align='center'>" .$resDM["LOP"][$i]."</td>";
		echo "<td valign=middle align='left'>".$thu[$resDM["THU"][$i]]."</td>";
		echo "<td valign=middle align='center'>".$resDM["NGAY_THI"][$i]."</td>";
		echo "<td valign=middle align='center'>".$resDM["GIO_THI"][$i]."</td>";
		echo "<td valign=middle align='right'><b>".$resDM["PHONG_THI"][$i]."</b></td>";
		echo "</tr>";
	}
	echo "
	  </tbody>
	</table>
	";
}
?>

<?php 
if (isset ($db_conn))
	oci_close($db_conn);
?>