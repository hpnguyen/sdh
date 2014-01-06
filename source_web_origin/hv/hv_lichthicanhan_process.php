<?php
if (isset($_REQUEST["hisid"])){
	session_id($_REQUEST["hisid"]);
	session_start();
}
if (!isset($_SESSION['uidloginhv'])){
	die('Truy cập bất hợp pháp'); 
}

include "libs/connect.php";

?>

<?php

$type = $_REQUEST['w'];
$khoa = $_REQUEST['k'];
$dothoc = $_REQUEST['d'];
$hk = $_REQUEST['h'];
$manganh = base64_decode($_SESSION["manganh"]);
$mahv = base64_decode($_SESSION["mahv"]);

$thu = array("1"=>"CN", "2"=>"Hai", "3"=>"Ba", "4"=>"Tư", "5"=>"Năm", "6"=>"Sáu", "7"=>"Bảy");

if ($type=='dothoc-lichthi_canhan')
{
	# thong tin lam luan van, hoc vien
	$sqlstr = "	SELECT ho || ' ' || ten ho_ten, khoa
				FROM 	hoc_vien
				WHERE ma_hoc_vien = '$mahv'";
	$stmt = oci_parse($db_conn, $sqlstr);oci_execute($stmt);$n = oci_fetch_all($stmt, $resDM);oci_free_statement($stmt);
	if ($n > 0 ) 
	{
		$ttcn = "<b><span style='margin-left:5px'>MSHV: $mahv</span><span style='margin-left:20px'>{$resDM["HO_TEN"][0]}</span><span style='margin-left:20px'>Khóa: {$resDM["KHOA"][0]}</span></b>";
	}
				
	$sqlstr="SELECT DISTINCT h.ma_hoc_vien
			, d.ma_mh, m.ten ten_mh,  t.lop, h.khoa, ten_nganh, d.dot_hoc
			, to_char(d.dot_hoc,'DD/MM/YYYY') DOT_HOC
			, ho_ten cbgd, lt.ngay_thi ngay
			, to_char(lt.ngay_thi,'DD/MM/YYYY') ngay_thi
			, to_char(lt.ngay_thi,'d') thu
			, lt.gio_thi, lt.phong_thi, d.nhom_lop
			FROM hoc_vien h, dang_ky_mon_hoc d, thoi_khoa_bieu t, nganh n, mon_hoc m, lich_thi lt
			WHERE t.ma_mh = lt.ma_mh
			AND t.dot_hoc = lt.dot_hoc
			AND t.lop = lt.lop
			AND d.nhom_lop = lt.nhom_lop
			AND d.ma_hoc_vien = h.ma_hoc_vien 
			AND h.ma_nganh = n.ma_nganh AND d.dot_hoc = t.dot_hoc(+) 
			AND d.ma_mh = t.ma_mh(+) AND d.lop = t.lop(+) 
			AND d.ma_mh = m.ma_mh 
			AND m.ma_loai <> '5' 
			AND d.ma_mh || d.ma_hoc_vien not in (SELECT di.ma_mh||di.ma_hoc_vien FROM diem di WHERE di.dot_hoc = d.dot_hoc AND di.ma_mh = d.ma_mh AND di.diem_lan_1 IN (14, 20))
			AND h.ma_hoc_vien = '$mahv' 
			AND d.dot_hoc  = '$dothoc' ORDER BY ngay";
	
	//di.diem_lan_1 IN (14, 20) 14: điểm hoảng thi, 20: rút môn học không bảo lưu học phí. Những môn này sẽ không có trong lịch thi cá nhân.
	
	$stmt = oci_parse($db_conn, $sqlstr);
	oci_execute($stmt);
	$n = oci_fetch_all($stmt, $resDM);
	oci_free_statement($stmt);
	
	echo "
	<div style='margin-bottom:20px; font-size:14px;' align=center><b>LỊCH THI CÁ NHÂN HK $hk</b><br/> 
				Ngành: <b>{$resDM['TEN_NGANH'][0]}</b>
	</div>
	<div style='margin-bottom:10px; font-size:14px;' align=left>$ttcn</div>
	<table width='100%' border='0'  cellspacing='0' cellpadding='3' class='ui-widget ui-widget-content ui-corner-top tableData' >
	<thead>
	  <tr class='ui-widget-header heading' style='height:20pt; font-weight:bold;'>
		<td class='ui-corner-tl' align='center'>STT</td>
		<td align=left>Môn học</td>
		<td align=left>CBGD</td>
		<td align='center'>Lớp</td>
		<td align='center'>Nhóm lớp MH</td>
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
		
		if ($resDM["LOP"][$i]!=$resDM["NHOM_LOP"][$i])
			$format = "color:red;";
		else
			$format="";
		
		echo "<tr align='left' valign='top' class=' ".$classAlt."' style='height:20px;'>";
		echo "<td valign=middle align=center>".($i+1)."</td>";
		echo "<td valign=middle align='left'>({$resDM["MA_MH"][$i]}) - {$resDM["TEN_MH"][$i]}</td>";
		echo "<td valign=middle align='left'>{$resDM["CBGD"][$i]}</td>";
		echo "<td valign=middle align='center'>{$resDM["LOP"][$i]}</td>";
		echo "<td valign=middle align='center' style='$format'>{$resDM["NHOM_LOP"][$i]}</td>";
		echo "<td valign=middle align=left><b>{$thu[$resDM["THU"][$i]]}</b></td>";
		echo "<td valign=middle align='center'><b>{$resDM["NGAY_THI"][$i]}</b></td>";
		echo "<td valign=middle align='center'><b>{$resDM["GIO_THI"][$i]}</b></td>";
		echo "<td valign=middle align='right'><b>{$resDM["PHONG_THI"][$i]}</b></td>";
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