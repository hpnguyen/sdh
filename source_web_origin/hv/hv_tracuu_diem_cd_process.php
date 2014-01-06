<?php
//if (!isset($_SESSION['uidloginhv'])){
//	die('Truy cập bất hợp pháp'); 
//}

include "libs/connect.php";
include "libs/pgslibshv.php";

?>

<?php

$type = escape($_POST['w']);
$mahv = escape($_POST['m']);
$ngaysinh = escape($_POST['n']);

if ($type=='mahv_ngaysinh-diemcd')
{
	$sqlstr="
		SELECT upper(ho || ' ' || ten) ho_ten, ten_nganh, ma_hoc_vien
		FROM hoc_vien_chuyen_doi h, nganh n
		WHERE ma_hoc_vien = '$mahv'
		AND h.ma_nganh = n.ma_nganh
	";
	$stmt = oci_parse($db_conn, $sqlstr);
	oci_execute($stmt);
	$n = oci_fetch_all($stmt, $resDM);
	oci_free_statement($stmt);
	
	if ($n>0)
	{
		$sqlstr="
			select d.ma_mh,ten_MH,diem_lan_1,diem_lan_2 
			from diem_chuyen_doi d,mon_hoc_chuyen_doi mh 
			where d.ma_mh=mh.ma_mh and ma_hoc_vien = '$mahv'
		";
		$stmt = oci_parse($db_conn, $sqlstr);
		oci_execute($stmt);
		$n = oci_fetch_all($stmt, $diemCD);
		oci_free_statement($stmt);
			
		echo "
		<div style='margin-bottom:10px; font-size:14px;' align=center><b>ĐIỂM CHUYỂN ĐỔI HV {$resDM['HO_TEN'][0]} (Mã: {$resDM['MA_HOC_VIEN'][0]})</b><br/>Chuyên ngành: <b>{$resDM['TEN_NGANH'][0]} </b></div>
		<table width='100%' border='0'  cellspacing='0' cellpadding='3' class='ui-widget ui-widget-content ui-corner-top tableData' >
		<thead>
		  <tr class='ui-widget-header' style='height:20pt;font-weight:bold;'>
			<td class='ui-corner-tl' align='left'>STT</td>
			<td >Mã MH</td>
			<td >Môn học</td>
			<td align='right'>Điểm lần 1</td>
			<td class='ui-corner-tr' align='right'>Điểm lần 2</td>
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
			echo "<td align='left'>".$diemCD["MA_MH"][$i]."</td>";
			echo "<td align='left'>".$diemCD["TEN_MH"][$i]."</td>";
			echo "<td align='right'><b>".$diemCD["DIEM_LAN_1"][$i]."</b></td>";
			echo "<td align='right'><b>".$diemCD["DIEM_LAN_2"][$i]."</b></td>";
			echo "</tr>";
		}
		echo "
		  </tbody>
		</table>
		";
	}
	else
	{
		echo "
			<div style='margin-bottom:10px; font-size:12px;' align=center><b>Không tìm thấy học viên chuyển đổi</b></div>
		";
	}
}
?>

<?php 
if (isset ($db_conn))
	oci_close($db_conn);
?>