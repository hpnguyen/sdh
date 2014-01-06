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
$makhoa = base64_decode($_SESSION["makhoa"]);
$dothoc = $_REQUEST['khoa_txtKhoaKinhPhiTHTN'];
$hk = $_REQUEST['h'];

$sqlstr=
"select distinct c.dot_hoc, (k.ngan_sach+k.hoc_phi) don_gia, sl_cq, 0 sl_bd, c.ma_mh, m.ten, m.so_tiet_th, m.so_tiet_lt, (m.so_tiet_th*sl_cq*(k.ngan_sach+k.hoc_phi)) tong_cong
from kinh_phi_th_tn_cap c, kinh_phi_th_tn k, mon_hoc m
where c.dot_hoc = k.dot_hoc 
and c.ma_mh = m.ma_mh
and k.fk_hinh_thuc_dao_tao = 'CQ'
and c.dot_hoc = '$dothoc'
and c.ma_mh in (select distinct t.ma_mh
                from thoi_khoa_bieu t, mon_hoc m, bo_mon b
                where t.dot_hoc = ('$dothoc')
                and t.ma_mh = m.ma_mh
				and m.ma_bo_mon = b.ma_bo_mon
				and b.ma_khoa = '$makhoa')
union

select distinct c.dot_hoc, (k.ngan_sach+k.hoc_phi) don_gia, 0 sl_cq1, sl_bd, c.ma_mh, m.ten, m.so_tiet_th, m.so_tiet_lt, (m.so_tiet_th*sl_bd*(k.ngan_sach+k.hoc_phi)) tong_cong
from kinh_phi_th_tn_cap c, kinh_phi_th_tn k, mon_hoc m
where c.dot_hoc = k.dot_hoc 
and c.ma_mh = m.ma_mh
and k.fk_hinh_thuc_dao_tao = 'DT'
and c.dot_hoc = '".$dothoc."'
and c.ma_mh in (select distinct t.ma_mh
                from thoi_khoa_bieu t, mon_hoc m, bo_mon b
                where t.dot_hoc = ('$dothoc')
                and t.ma_mh = m.ma_mh
				and m.ma_bo_mon = b.ma_bo_mon
				and b.ma_khoa = '$makhoa')";
		
$stmt = oci_parse($db_conn, $sqlstr);
oci_execute($stmt);
$n = oci_fetch_all($stmt, $resDM);
oci_free_statement($stmt);

echo "
	<div align='center'><h2>Kinh Phí Thực Hành - Thí Nghiệm<br/>Học kỳ $hk</h2></div>	
		<div align='center' style='margin-bottom:0px; margin-left:0px; font-size:80%'>
			(Kèm quyết định số: 686/QĐ ĐHBK-ĐTSĐH ngày 23 tháng 3 năm 2012)
		</div>
		<div align='right' style='margin:0 5px 10px 0; font-size:80%'>
			Đơn vị tính: Đồng VND
		</div>
		<div style='margin-bottom:20px;'>
			<table id='khoa_tableKinhPhiTHTN' name='khoa_tableKinhPhiTHTN' width='100%' border='0'  cellspacing='0' cellpadding='3' class='ui-widget ui-widget-content ui-corner-top' >
			<thead>
			  <tr class='ui-widget-header heading' style='height:20pt; font-weight:bold'>
				<td align='left' title='Mã Số Môn Học' class='ui-corner-tl'>MSMH</td>
				<td style='width:400px;'>Môn Học</td>
				<td align='center' title='Số Tiết Lý Thuyết'>LT</td>
				<td align='center' title='Số Tiết Thực Hành'>TH</td>
				<td align='center' title='Số Học Viên Chính Quy'>Số HV CQ</td>
				<td align='center' title='Số Học Viên Bồi Dưỡng'>Số HV BD</td>
				<td align='right'>Đơn Giá</td>
				<td align='right' title='Tổng Cộng = Đơn Giá * Số HV * Số Tiết Thực Hành'>Tổng Cộng&nbsp;</td>
			  </tr>
			  </thead>
			  <tbody>
";

$tongTien=0;
$mamh='';
$classAlt="alt";
for ($i = 0; $i < $n; $i++)
{
	if ($resDM["TONG_CONG"][$i]>0){
		//($i % 2) ? $classAlt="alt" : $classAlt="alt_";
		($classAlt=="alt") ? $classAlt="alt_" : $classAlt="alt" ;
		echo "<tr align='left' valign='middle' class=' ".$classAlt."' style='height:20px;'>";
		if ($ma_mh != $resDM['MA_MH'][$i]){
			echo "<td  align='left'>" .$resDM['MA_MH'][$i]."</td><td >".$resDM["TEN"][$i]."</td>";
			$ma_mh = $resDM['MA_MH'][$i];
		}
		else
			echo "<td  align='center'></td><td ></td>";
		echo "<td align='center'>".$resDM["SO_TIET_LT"][$i]."</td>";
		echo "<td align='center'>".$resDM["SO_TIET_TH"][$i]."</td>";
		echo "<td align='center'>"; if ($resDM["SL_CQ"][$i]>0) echo $resDM["SL_CQ"][$i]; echo "</td>";
		echo "<td align='center'>"; if ($resDM["SL_BD"][$i]>0) echo $resDM["SL_BD"][$i]; echo "</td>";
		echo "<td align='right'>".number_format($resDM["DON_GIA"][$i])."</td>";
		echo "<td align='right'>".number_format($resDM["TONG_CONG"][$i])."&nbsp;</td>";
		echo "</tr>";
		$tongTien+=$resDM["TONG_CONG"][$i];
	}
} 
	//($classAlt=="alt_") ? $classAlt="alt" : $classAlt="alt_";
	$classAlt="alt_";
	echo "<tr class=' ".$classAlt."'>";				
	echo "<td></td>";
	echo "<td align='center'></td>";
	echo "<td align='center'></td>";
	echo "<td align='center'></td>";
	echo "<td align='center'></td>";
	echo "<td colspan='2' align='right'>&nbsp;<strong>Tổng số tiền:</strong></td>";
	echo "<td align='right'><strong>".number_format($tongTien)."</strong>&nbsp;</td>";
	echo "</tr>";
echo "
		  </tbody>
		</table>
	</div>
";


if (isset ($db_conn))
	oci_close($db_conn);
?>