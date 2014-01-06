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
	$sqlstr="select distinct khoa from thoi_khoa_bieu
			where khoa in (select khoa from thoi_khoa_bieu where dot_hoc='$dothoc')
			order by khoa desc";
	$stmt = oci_parse($db_conn, $sqlstr);
	oci_execute($stmt);
	$n = oci_fetch_all($stmt, $resDM);
	oci_free_statement($stmt);

	for ($i = 0; $i < $n; $i++)
	{
		echo "<option value='".$resDM["KHOA"][$i]."'>" .$resDM["KHOA"][$i]. "</option>";
	}
}

if ($type=='khoa_hk-nganh')
{
	$sqlstr="SELECT distinct n.ma_nganh, ten_nganh 
			FROM thoi_khoa_bieu tkb, nganh n 
			WHERE n.ma_nganh = tkb.ma_nganh 
			AND tkb.khoa = $khoa 
			AND tkb.dot_hoc = '$dothoc'
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

if ($type=='khoa_hk_nganh-tkb_nganh')
{
	$sqlstr="SELECT distinct thu,ma_can_bo,ho_ten, phong, ten_nganh, tkb.DOT_TS,
			decode(HO_TEN_CBGD_PHU, null, '', ' & '||HO_TEN_CBGD_PHU) HO_TEN_CBGD_PHU, 
			ma_mh, lop, ten_mh,tiet_bat_dau,tiet_ket_thuc,tuan_bat_dau,tuan_ket_thuc, to_char(dot_hoc, 'DD/MM/YYYY') dot_hoc
			FROM thoi_khoa_bieu tkb, nganh n 
			WHERE n.ma_nganh=tkb.ma_nganh 
			AND tkb.ma_nganh = '$ma_nganh' 
			AND khoa =  $khoa
			AND dot_hoc = '$dothoc'
			ORDER BY lop, thu, tuan_bat_dau, tiet_bat_dau";
	$stmt = oci_parse($db_conn, $sqlstr);
	oci_execute($stmt);
	$n = oci_fetch_all($stmt, $resDM);
	oci_free_statement($stmt);
	
	echo "
	<div style='margin-bottom:10px; font-size:14px;' align=center><b>THỜI KHÓA BIỂU CAO HỌC KHÓA $khoa HK $hk</b><br/> Ngày bắt đầu học kỳ: <b>{$resDM['DOT_HOC'][0]} <i>(Tuần 1)</i></b> <br/>Ngành: <b>{$resDM['TEN_NGANH'][0]}</b></div>
	<table width='100%' border='0'  cellspacing='0' cellpadding='3' class='ui-widget ui-widget-content ui-corner-top tableData' >
	<thead>
	  <tr class='ui-widget-header heading' style='height:20pt;'>
		<td class='ui-corner-tl' align='left'>Thứ</td>
		<td >CBGD</td>
		<td >Môn học</td>
		<td align='right'>Lớp MH</td>
		<td align='right'>Đợt TS</td>
		<td align='right'>Tiết BĐ</td>
		<td align='right'>Tiết KT</td>
		<td align='right'>Phòng</td>
		<td align='right'>Tuần BĐ</td>
		<td class='ui-corner-tr' align='right'>Tuần KT</td>
	  </tr>
	  </thead>
	  <tbody>
	";
	$classAlt = 'alt';
	for ($i = 0; $i < $n; $i++)
	{
		($classAlt=='alt') ? $classAlt="alt_" : $classAlt="alt";
		
		echo "<tr align='left' valign='top' class=' ".$classAlt."' style='height:20px;'>";
		echo "<td valign=middle align=left style='margin-left:2px;'><b>".$thu[$resDM["THU"][$i]]."</b></td>";
		echo "<td valign=middle align='left'>".$resDM["HO_TEN"][$i] .$resDM["HO_TEN_CBGD_PHU"][$i]."</td>";
		echo "<td valign=middle align='left'>(" . $resDM["MA_MH"][$i] .") - ". $resDM["TEN_MH"][$i]."</td>";
		echo "<td valign=middle align='right'>".$resDM["LOP"][$i]."</td>";
		echo "<td valign=middle align='right'>".$resDM["DOT_TS"][$i]."</td>";
		echo "<td valign=middle align='right'>".$resDM["TIET_BAT_DAU"][$i]."</td>";
		echo "<td valign=middle align='right'>".$resDM["TIET_KET_THUC"][$i]."</td>";
		echo "<td valign=middle align='right'><b>".$resDM["PHONG"][$i]."</b></td>";
		echo "<td valign=middle align='right'><b>".$resDM["TUAN_BAT_DAU"][$i]."</b></td>";
		echo "<td valign=middle align='right'><b>".$resDM["TUAN_KET_THUC"][$i]."</b></td>";
		echo "</tr>";
	}
	echo "
	  </tbody>
	</table>
	";
	
	include "hv_tiethoc.php";
	
}
?>

<?php 
if (isset ($db_conn))
	oci_close($db_conn);
?>