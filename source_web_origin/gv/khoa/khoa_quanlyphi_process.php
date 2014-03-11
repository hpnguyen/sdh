<?php
if (isset($_REQUEST["hisid"])){
	session_id($_REQUEST["hisid"]);
	session_start();
}
if (!isset($_SESSION['uidloginPortal'])){
	die('Đã hết phiên làm việc'); 
}
include "../libs/connect.php";
include "../libs/pgslibs.php";
if (!allowPermisstion(base64_decode($_SESSION['uidloginPortal']), '111', $db_conn))
	die('Truy cập bất hợp pháp');

$macb = $_SESSION['macb'];
$dothoc = $_POST['txtKhoaQuanLyPhi'];
$hk = $_POST['h'];
$makhoa = base64_decode($_SESSION["makhoa"]);
$tenkhoa = $_SESSION["tenkhoa"];

$sqlstr="
select hinh_thuc_dao_tao, UPPER(ten_khoa) TEN_KHOA,  decode(hinh_thuc_dao_tao, 'CQ', 100, 'DT', 50, 'CD', 50, 'LV', 100, 'ON', 200/3, 'KL', 100) tyle, sum(quan_ly_phi) quan_ly_phi, d.ten_quan_ly_phi
from quan_ly_phi q, bo_mon b, khoa k, dm_quan_ly_phi d
where q.ma_bo_mon = b.ma_bo_mon(+) and b.ma_khoa = k.ma_khoa and dot_hoc = '$dothoc'
and q.hinh_thuc_dao_tao = d.ma_quan_ly_phi (+) and  k.ma_khoa = '$makhoa'
group by ten_khoa, hinh_thuc_dao_tao, d.ten_quan_ly_phi
order by ten_quan_ly_phi
";
		
$stmt = oci_parse($db_conn, $sqlstr);oci_execute($stmt);$n = oci_fetch_all($stmt, $resDM);oci_free_statement($stmt);

if ($n>0)
{
	echo "
	<div align='center'><h2>Quản lý phí học kỳ $hk<br/>Khoa $tenkhoa</h2></div>		
		
		<div align='right' style='margin-bottom:10px; margin-left:0px; font-size:80%'>
			Đơn vị tính: Đồng VND
		</div>
		<div style='margin-bottom:20px;'>
			<table id='tableQuanLyPhi' name='tableQuanLyPhi' width='100%' border='0' cellpadding='5'  cellspacing='0' class='ui-widget ui-widget-content ui-corner-top tableData' >
			<thead>
			  <tr  class='ui-widget-header heading' style='height:20pt; font-weight:bold'>
				<td align='center' title='Số thứ tự' class='ui-corner-tl'>STT</td>
				<td style='width:400px;'>Loại quản lý phí</td>
				<td align='right' title='Tổng học phí'>Tổng học phí</td>
				<td align='right' title=''>%</td>
				<td align='right' title='Quản lý phí'>Quản lý phí</td>
			  </tr>
			  </thead>
			  <tbody>
	";

	$tongHP=0;
	$tongQLP=0;
	$tongPT=0;
	$mamh='';
	$classAlt="alt";
	for ($i = 0; $i < $n; $i++)
	{
		($classAlt=="alt") ? $classAlt="alt_" : $classAlt="alt" ;
		$hphi = $resDM["QUAN_LY_PHI"][$i]*$resDM["TYLE"][$i];
		$phantram = 100/$resDM["TYLE"][$i];
		echo "<tr align='left' valign='middle' class=' ".$classAlt."' style='height:20px;'>";
		echo "<td align='center'>".($i+1)."</td>";
		echo "<td align='left'>{$resDM["TEN_QUAN_LY_PHI"][$i]}</td>";
		echo "<td align='right'>".number_format($hphi)."</td>";
		echo "<td align='right'>".number_format($phantram)."% </td>";
		echo "<td align='right'>".number_format($resDM["QUAN_LY_PHI"][$i])."</td>";
		echo "</tr>";
		$tongHP+=$hphi;
		$tongQLP+=$resDM["QUAN_LY_PHI"][$i];
		$tongPT+=$phantram;
	} 
		($classAlt=="") ? $classAlt="alt" : $classAlt="alt_";
		echo "<tr valign='middle' class='alt_' style='height:20px;'>";				
		echo "<td></td>";
		echo "<td align='right'><strong>Tổng cộng</strong></td>";
		echo "<td align='right'><strong>".number_format($tongHP)."</strong></td>";
		echo "<td align='right'></td>";
		echo "<td align='right'><strong>".number_format($tongQLP)."</strong></td>";
		echo "</tr>";

	echo "
			  </tbody>
			</table>
		</div>
	";
}
else
	echo "Chưa có quản lý phí";
	
if (isset ($db_conn))
	oci_close($db_conn);
?>