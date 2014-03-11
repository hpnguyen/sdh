<?php
if (isset($_REQUEST["hisid"])){
	session_id($_REQUEST["hisid"]);
	session_start();
}
if (!isset($_SESSION['uidloginPortal'])){
	die('Truy cập bất hợp pháp'); 
}

include "../libs/connect.php";

$macb = $_SESSION['macb'];
$dothoc = $_REQUEST['gv_lichthi_txtKhoaThi'];
$hk = $_REQUEST['h'];

$sqlstr="	SELECT DISTINCT m.ten, t.ma_mh, t.lop, get_nganh_tkb(t.ma_can_bo, t.dot_hoc, t.ma_mh,t.lop) chuyen_nganh,
			d.gio_thi, TO_CHAR(d.ngay_thi,'DD/MM/YY') NGAY_THI, d.phong_thi,  TO_CHAR(t.dot_hoc,'DD/MM/YYYY') dot_hoc_f
			FROM THOI_KHOA_BIEU t, MON_HOC m, LICH_THI d
			WHERE T.MA_MH = m.MA_MH
			AND (t.dot_hoc = '$dothoc')
			AND t.ma_can_bo='".$macb."'
			AND d.dot_hoc(+) = t.dot_hoc
			and d.ma_mh(+) = t.ma_mh
			and d.lop(+)=t.lop
			ORDER BY m.ten"; 
$stmt = oci_parse($db_conn, $sqlstr);
oci_execute($stmt);
$n = oci_fetch_all($stmt, $resDM);
oci_free_statement($stmt);
$dot_hoc_f = $resDM["DOT_HOC_F"][0];
echo "
	<div align='center'><h2>Lịch Thi Cao Học<br/>Học kỳ $hk</h2></div>
	<div style='margin-bottom:20px;'>
		<div style='margin:0 0 10px 5px; ' align=left><strong>Ngày bắt đầu HK: <span id='thingaybatdauhk'>$dot_hoc_f (tuần 1)</span></strong></div>
		<table id='tableLichThi' name='tableLichThi' width='100%' border='0'  cellspacing='0' class='ui-widget ui-widget-content ui-corner-top tableData' >
        <thead>
          <tr class='ui-widget-header heading' style='height:20pt;font-weight:bold'>
            <td class='ui-corner-tl' align=center>STT</td>
            <td>Tên Môn Học</td>
			<td>Mã MH</td>
            <td  align='center'>Lớp</td>
            <td  align='left' style='width:300px;'>Chuyên ngành</td>
            <td>Giờ thi</td>
            <td>Ngày thi</td>
            <td  align='center' class='ui-corner-tr'>Phòng thi</td>
          </tr>
          </thead>
          <tbody>
";

for ($i = 0; $i < $n; $i++)
{
	($i % 2) ? $classAlt="alt" : $classAlt="alt_";
	echo "<tr align='left' valign='middle' class=' ".$classAlt."' style='height:20px;'>";				
	echo "<td  align='center'>" .($i+1)."</td>";
	echo "<td>".$resDM["TEN"][$i]."</td>";
	echo "<td>".$resDM["MA_MH"][$i]."</td>";
	echo "<td align='center'>".$resDM["LOP"][$i]."</td>";
	echo "<td>".$resDM["CHUYEN_NGANH"][$i]."</td>";
	echo "<td>".$resDM["GIO_THI"][$i]."</td>";
	echo "<td>".$resDM["NGAY_THI"][$i]."</td>";
	echo "<td align='center'>".$resDM["PHONG_THI"][$i]."</td>";
	echo "</tr>";
} 

echo "
		  </tbody>
		</table>
	</div>
";

echo "
	<div align='center' style='font-size: 0.8em;'>
		<div style='margin-right:0px; margin-top:20px; float:right;'>
			<div align='center' style='margin-bottom:5px;'><strong>Tối</strong></div>
			<div style='margin-right:10px; float:right;'>
				<font color=red>Tiết 14: 18:15</font> - 19:00<br/>
				Tiết 15: 19:00 - 19:45<br/>
				Tiết 16: 19:55 - 20:40
			</div>
		</div>
		<div style='margin-right:10px; margin-top:20px; float:right;  border-right: 1px solid #96c716;'>
			<div align='center' style='margin-bottom:5px;'><strong>Chiều</strong></div>
			<div style='margin-right:20px; float:left;'>
				<font color=red>Tiết 7: 12:30</font> - 13:15<br/>
				Tiết 8: 13:15 - 14:00<br/>
				Tiết 9: 14:10 - 14:55
			</div>
			<div style='margin-right:10px; float:right;'>
				Tiết 10: 15:05 - 15:50<br/>
				Tiết 11: 16:00 - 16:45<br/>
				Tiết 12: 16:45 - 17:30
			</div>
		</div>
		<div style='margin-right:10px; margin-top:20px; float:right;  border-right: 1px solid #96c716;'>
			<div align='center' style='margin-bottom:5px;'><strong>Sáng</strong></div>
			<div style='margin-right:20px; float:left ;'>
				<font color=red>Tiết 1: 06:30</font> - 07:15<br/>
				Tiết 2: 07:15 - 08:00<br/>
				Tiết 3: 08:10 - 08:55
			</div>
			<div style='margin-right:10px; float:right;'>
				Tiết 4: 09:05 - 09:50<br/>
				Tiết 5: 10:00 - 10:45<br/>
				Tiết 6: 10:45 - 11:30
			</div>
		</div>
	</div>
";

if (isset ($db_conn))
	oci_close($db_conn);
?>