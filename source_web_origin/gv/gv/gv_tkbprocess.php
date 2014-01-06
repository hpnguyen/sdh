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
$dothoc = $_REQUEST['d'];
$hk = $_REQUEST['h'];
$thu = array("1"=>"CN", "2"=>"Hai", "3"=>"Ba", "4"=>"Tư", "5"=>"Năm", "6"=>"Sáu", "7"=>"Bảy");

$sqlstr="	SELECT DISTINCT MON_HOC.TEN, T.MA_MH, t.ma_can_bo, 
			decode(T.THU, 9, null, t.thu) thu, T.TIET_BAT_DAU, T.TIET_KET_THUC, tuan_bat_dau,
			(TUAN_BAT_DAU)||'->'||(TUAN_KET_THUC) Tuan_hoc, T.PHONG,
			t.dot_hoc, so_tiet_lt, so_tiet_bt, so_tiet_th, so_tiet_tl, t.lop, TO_CHAR(d.ngay_thi,'DD/MM/YY') NGAY_THI, get_nganh_tkb(t.ma_can_bo, t.dot_hoc, t.ma_mh,t.lop) chuyen_nganh,
				(SELECT COUNT(*) FROM dang_ky_mon_hoc DK WHERE DK.DOT_HOC = t.dot_hoc AND DK.MA_MH = t.ma_mh
				AND DK.LOP=t.lop) SL
			FROM THOI_KHOA_BIEU t, MON_HOC, LICH_THI d
			WHERE T.MA_MH = MON_HOC.MA_MH
			AND (t.dot_hoc = to_date('".$dothoc."','dd-mm-yyyy'))
			AND (t.ma_can_bo='".$macb."' or t.ma_can_bo_phu='".$macb."')
			AND d.dot_hoc(+) = t.dot_hoc
			and d.ma_mh(+) = t.ma_mh
			and d.lop(+)=t.lop
			ORDER BY tuan_bat_dau, nvl(thu, 9), tiet_bat_dau , t.lop, MON_HOC.TEN"; 
$stmt = oci_parse($db_conn, $sqlstr);
oci_execute($stmt);
$n = oci_fetch_all($stmt, $resDM);
oci_free_statement($stmt);

/*file_put_contents("logs.txt", "----------------------------------------------\n
". date("H:i:s d.m.Y")." $sqlstr \n
----------------------------------------------\n", FILE_APPEND);
*/

echo "
	<div align='center'><h2>Thời Khóa Biểu Giảng Dạy Cao Học<br/>Học kỳ $hk</h2></div>
	<div style='margin-bottom:20px;'>
		<div style='margin:0 0 10px 5px; ' align=left><strong>Ngày bắt đầu HK: <span id='ngaybatdauhk'></span></strong></div>
		<table id='tableTKB' name='tableTKB' width='100%' border='0'  cellspacing='0' class='ui-widget ui-widget-content ui-corner-top tableData' >
		<thead>
		  <tr class='ui-widget-header heading' style='font-weight:bold; height:20pt;'>
			<td  align='center' class='ui-corner-tl'>Thứ</td>
			<td>Mã MH</td>
			<td>Tên Môn Học</td>
			<td  align='center'>Lớp</td>
			<td>LT</td>
			<td>BT</td>
			<td>TH</td>
			<td>TL</td>
			<td  align='center' style='width:200px;'>Chuyên ngành</td>
			<td>SL</td>
			<td>Tiết học</td>
			<td>Phòng</td>
			<td  align='center'>Tuần học</td>
			<td class='ui-corner-tr'>Ngày thi</td>
		  </tr>
		  </thead>
		  <tbody>
";
for ($i = 0; $i < $n; $i++)
{
	($i % 2) ? $classAlt="alt" : $classAlt="alt_";
	echo "<tr align='left' valign='middle' class=' ".$classAlt."' style='height:20px;'>";				
	echo "<td align='center'><b>" .$thu[$resDM['THU'][$i]]."</b></td>";
	echo "<td>".$resDM["MA_MH"][$i]."</td>";
	echo "<td>".$resDM["TEN"][$i]."</td>";
	echo "<td align='center'><b>".$resDM["LOP"][$i]."</b></td>";
	echo "<td >".$resDM["SO_TIET_LT"][$i]."</td>";
	echo "<td align='center'>".$resDM["SO_TIET_BT"][$i]."</td>";
	echo "<td align='center'>".$resDM["SO_TIET_TH"][$i]."</td>";
	echo "<td align='center'>".$resDM["SO_TIET_TL"][$i]."</td>";
	echo "<td>".$resDM["CHUYEN_NGANH"][$i]."</td>";
	echo "<td><b>".$resDM["SL"][$i]."</b></td>";
	echo "<td align='center'><b>".$resDM["TIET_BAT_DAU"][$i].'-'.$resDM["TIET_KET_THUC"][$i]."</b></td>";
	echo "<td>".$resDM["PHONG"][$i]."</td>";
	echo "<td align='center'><b>".$resDM["TUAN_HOC"][$i]."</b></td>";
	echo "<td>".$resDM["NGAY_THI"][$i]."</td>";
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