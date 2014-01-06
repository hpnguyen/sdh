<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
		<title>Phân bổ các bộ giảng dạy</title>
<style type="text/css">
body {
    margin: 0;
    padding: 5px;
    background-color: #FFFFFF;
    font: Arial, Helvetica, sans-serif,
    font-size: 10px;
    font-family:Arial, Helvetica, sans-serif
}
* {
    box-sizing: border-box;
    -moz-box-sizing: border-box;
}
.page {
    width: 29.7cm;
    min-height: 21cm;
    padding: 2cm;
    margin: 1cm auto;
    border: 1px #D3D3D3 solid;
    border-radius: 5px;
    background: white;
    box-shadow: 0 0 1px rgba(0, 0, 0, 0.1);
}
.subpage {
    padding: 1cm;
    border: 5px red solid;
    height: 256mm;
    outline: 2cm #FFEAEA solid;
}

@page {
    size: A4;
    margin: 0;
}
@media print {
    .page {
        margin-top: 0;
        margin-bottom: 0;
        
        border: initial;
        border-radius: initial;
        width: initial;
        min-height: initial;
        box-shadow: initial;
        background: initial;
        page-break-after: always;
    }
}
td.color {
    -moz-box-shadow:inset 0 0 0px 0 #222;
    -webkit-box-shadow:inset 0 0 0px 0 #222;
    box-shadow:inset 0 0 0px 0 #222;
    border: 1px solid #000;
    display: block;
}
table { 
	border-right: thin solid black;
	border-bottom: thin solid black;
}
td { 
	border-left: thin solid black;
	border-top: thin solid black;
}
</style>
	</head>
	<body>
		<div style='font-size:13px; font-weight:bold;' align=left>ĐẠI HỌC BÁCH KHOA - TP.HCM<br/>PHÒNG ĐÀO TẠO SAU ĐẠI HỌC<br/> &nbsp;</div>
		<div align="center"><h2>Phân công cán bộ giảng dạy <?php echo ($viewAll ? '' : '- cấp Khoa') ?><br/>Học kỳ <?php echo $hk ?></h2></div>	
<div align=left><strong>Ngày bắt đầu HK: <?php echo $nbd ?></strong></div>
<div style="font-size: 10px">
	<table width='100%' cellspacing='0'>
	<thead>
	  <tr style='font-weight:bold; height:20pt;'>
		<td  align='center' class='ui-corner-tl'>Thứ</td>
		<td>Mã MH</td>
		<td width="200px">Tên Môn Học</td>
		<td width="200px">Cán bộ giảng dạy chính</td>
		<td width="200px">Cán bộ giảng dạy phụ</td>
		<td width="125px">Ghi chú</td>
		<td  align='center'>Lớp</td>
		<!-- <td>LT</td>
		<td>BT</td>
		<td>TH</td>
		<td>TL</td> -->
		<td>SL Dự kiến</td>
		<td>Tiết học</td>
		<td>Phòng</td>
		<td  class='ui-corner-tr' align='center'>Tuần học</td>
		<td  align="center" width="200px">BM Quản lý MH</td>
		<td  align="center" width="200px">Chuyên ngành</td>
	  </tr>
	  </thead>
	  <tbody>
<?php
foreach($listItems as $i => $row)
{
	$classAlt = ($i % 2) ? "alt" : "alt_";
	echo "<tr align='left' valign='middle' class=' ".$classAlt."' style='height:20px;'>";				
	echo "<td align='center'><b>" .$row['thu']."</b></td>";
	echo "<td><span>".$row["ma_mh"]."</span></td>";
	echo "<td><span>".$row["ten"]."</span></td>";
	//Can bo giang day chinh
	echo "<td>";
	echo "<div class=\"list-canbo\">
	<span id=\"view-name-".$i."\" title=\"\">".$row["ten_cb_chinh"]."</span>
	</div>";
	echo "</td>";
	//Can bo giang day phu
	echo "<td>";
	echo "<div class=\"list-canbo-phu\">
	<span id=\"view-name-phu-".$i."\" title=\"\">".$row["ten_cb_phu"]."</span>
	</div>";
	echo "</td>";
	echo "<td align='left'><span>".$row["ghi_chu"]."</span></td>";
	echo "<td align='center'><b>".$row["lop"]."</b></td>";
	echo "<!--<td ><span>".$row["so_tiet_lt"]."</span></td>";
	echo "<td align='center'><span>".$row["so_tiet_bt"]."</span></td>";
	echo "<td align='center'><span>".$row["so_tiet_th"]."</span></td>";
	echo "<td align='center'><span>".$row["so_tiet_tl"]."</span></td>--!>";
	echo "<td><b>".$row["sl"]."</b></td>";
	echo "<td align='center'><b>".$row["tiet_bat_dau"].'-'.$row["tiet_ket_thuc"]."</b></td>";
	echo "<td><span>".$row["phong"]."</span></td>";
	echo "<td align='center'><b>".$row["tuan_hoc"]."</b></td>";
	echo "<td><span>".$row["ten_bo_mon"]."</span></td>";
	echo "<td><span>".$row["chuyen_nganh"]."</span></td>";
	echo "</tr>";
} 
?>
		</tbody>
	</table>
</div>
	</body>
</html>