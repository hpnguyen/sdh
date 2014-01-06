<?php
//if (!isset($_SESSION['uidloginhv'])){
//	die('Truy cập bất hợp pháp'); 
//}

include "libs/connect.php";

?>

<?php

$type = $_REQUEST['w'];
$dothoc = $_REQUEST['d'];
$dothocF = $_REQUEST['df'];
$hk = $_REQUEST['h'];
$nganh = $_REQUEST['n'];
$embed = $_REQUEST['e'];

$qHK = "";
$titleHK = "";
if ($hk!='')
{
	$qHK = "AND c.hoc_ky=$hk";
	$titleHK = "HK $hk";
}
if ($type=='dothoc-nganh')
{
	$sqlstr="
		SELECT DISTINCT c.ma_nganh,ten_nganh 
		FROM ctdt_chuyen_doi c, nganh n 
		WHERE c.ma_nganh=n.ma_nganh 
		AND c.dot_hoc = '$dothoc'
		ORDER BY TEN_NGANH
	";
			
	$stmt = oci_parse($db_conn, $sqlstr);
	oci_execute($stmt);
	$n = oci_fetch_all($stmt, $resDM);
	oci_free_statement($stmt);

	for ($i = 0; $i < $n; $i++)
	{
		echo "<option value='".$resDM["MA_NGANH"][$i]."'>" .$resDM["TEN_NGANH"][$i]." (".$resDM["MA_NGANH"][$i].") </option>";
	}
}

if ($type=='dothoc_nganh-ctdt')
{
	$sqlstr="
		SELECT 	c.ma_nganh, ten_nganh, m.ma_mh, m.ten_mh, m.so_tiet, m.so_tiet_th
		FROM ctdt_chuyen_doi c, mon_hoc_chuyen_doi m, nganh n 
		WHERE c.ma_nganh=n.ma_nganh 
		AND c.ma_mh=m.ma_mh 
		AND c.ma_nganh='$nganh'
		AND c.dot_hoc='$dothoc'
		ORDER BY ten_mh
	";
	$stmt = oci_parse($db_conn, $sqlstr);
	oci_execute($stmt);
	$n = oci_fetch_all($stmt, $resDM);
	oci_free_statement($stmt);
	
	echo "
	<div style='margin-bottom:10px; font-size:14px;' align=center><b>CHƯƠNG TRÌNH ĐÀO TẠO CHUYỂN ĐỔI - BTKT ĐỢT HỌC $dothocF</b><br/>Chuyên ngành: <b>{$resDM['TEN_NGANH'][0]} </b></div>
	<table width='100%' border='0'  cellspacing='0' cellpadding='3' class='ui-widget ui-widget-content ui-corner-top tableData' >
	<thead>
	  <tr class='ui-widget-header' style='height:20pt;font-weight:bold;'>
		<td class='ui-corner-tl' align='left'>STT</td>
		<td >Mã MH</td>
		<td >Môn học</td>
		<td align='right'>Số Tiết LT</td>
		<td class='ui-corner-tr' align='center'>Số Tiêt TH</td>
	  </tr>
	  </thead>
	  <tbody>
	";
	$classAlt = 'alt';
	for ($i = 0; $i < $n; $i++)
	{
		($classAlt=='alt') ? $classAlt="alt_" : $classAlt="alt";
			
		echo "<tr align='left' valign=middle class=' ".$classAlt."' style='height:20px;'>";
		echo "<td align=left>".($i+1)."</td>";
		echo "<td align='left'>".$resDM["MA_MH"][$i]."</td>";
		echo "<td align='left'>".$resDM["TEN_MH"][$i]."</td>";
		echo "<td align='right'>".$resDM["SO_TIET"][$i]."</td>";
		echo "<td align='right'>".$resDM["SO_TIET_TH"][$i]."</td>";
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