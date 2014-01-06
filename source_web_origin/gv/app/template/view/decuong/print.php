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
		<div align="center"><h2>Danh sách đủ điều kiện nhận đề cương luận văn<br/>Học kỳ <?php echo $hk ?></h2></div>	
<div align=left><strong>Ngày bắt đầu HK: <?php echo $nbd ?></strong></div>
<div style="font-size: 10px">
	<table width='100%' cellspacing='0'>
	<thead>
	  <tr style='font-weight:bold; height:20pt;'>
		<td width="168px" align='center'>Tên ngành</td>
		<td width="48px" align="center">Mã HV</td>
		<td width="150px" align="center">Họ</td>
		<td width="50px" align="center">Tên</td>
		<td width="23px" align="center">TC tích lũy</td>
		<td width="100px" align="center">Loại CTĐT</td>
		<td width="163px" align="center">Hướng nghiên cứu</td>
		<td width="163px" align="center">Hướng Dẫn 1</td>
		<td width="163px" align="center">Hướng Dẫn 2</td>
		<td width="23px" align="center">Đợt xét</td>
		<td align="center" class='ui-corner-tr'>Ghi chú</td>
	  </tr>
	  </thead>
	  <tbody>
<?php
foreach($listItems as $i => $row)
{
	echo "<tr align='left' valign='middle'>";				
	echo "<td><span>".$row["ten_nganh"]."</span></td>";
	echo "<td><span>".$row["ma_hoc_vien"]."</span></td>";
	echo "<td><span>".$row["ho"]."</span></td>";
	echo "<td><span>".$row["ten"]."</span></td>";
	echo "<td><span>".$row["tong_chi_tich_luy"]."</span></td>";
	echo "<td><span>".$row["loai_ctdt"]."</span></td>";
	echo "<td><span>".$row["huong_nghien_cuu"]."</span></td>";
	echo "<td><span>".$row["huong_dan_1"]."</span></td>";
	echo "<td><span>".$row["huong_dan_2"]."</span></td>";
	echo "<td><span>".$row["dot_xet"]."</span></td>";
	echo "<td><span>".$row["ghi_chu"]."</span></td>";
	echo "</tr>";
} 
?>
		</tbody>
	</table>
</div>
	</body>
</html>