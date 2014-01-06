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

$sqlstr="
SELECT max(dot_hoc) dot_hoc_max from dang_ky_mon_hoc where ma_hoc_vien='$mahv'
";
$stmt = oci_parse($db_conn, $sqlstr);
oci_execute($stmt);
$n = oci_fetch_all($stmt, $resDM);
oci_free_statement($stmt);

$dothoc_ht=$resDM["DOT_HOC_MAX"][0];

$sqlstr="
SELECT ngay_bat_dau_hk, ngay_ket_thuc_hk
FROM dot_hoc_nam_hoc_ky
WHERE dot_hoc = '$dothoc'
";
$stmt = oci_parse($db_conn, $sqlstr);
oci_execute($stmt);
$n = oci_fetch_all($stmt, $resDM);
oci_free_statement($stmt);

$ngay_bd_hk =$resDM["NGAY_BAT_DAU_HK"][0];
$ngay_kt_hk =$resDM["NGAY_KET_THUC_HK"][0];

if ($type=='dothoc-kqdonghp')
{
	# thong tin lam luan van, hoc vien
	$sqlstr = "SELECT h.ho||' '||h.ten Ho_ten, khoa
				FROM hoc_vien h 
				WHERE ma_hoc_vien = '$mahv'";
						
	$stmt = oci_parse($db_conn, $sqlstr);oci_execute($stmt);
	$n=oci_fetch_all($stmt, $hv);	oci_free_statement($stmt);
	
	$sqlstr = "
				SELECT du_dau_ky('$dothoc') dau_ky, du_cuoi_ky('$dothoc') cuoi_ky, sum(charge) phat_sinh, sum(payment) thanh_toan
				FROM transactions t
				WHERE t.ma_hoc_vien = '$mahv'
				AND (t.ngay between '$ngay_bd_hk' AND '$ngay_kt_hk')";
	$stmt = oci_parse($db_conn, $sqlstr);oci_execute($stmt);
	oci_fetch_all($stmt, $hocphi);	oci_free_statement($stmt);
	
	//echo $sqlstr;
	
	if ($n > 0 ) 
	{
		echo "
		<div align=center style='margin:0 0 15px 0; font-weight:bold'>KẾT QUẢ ĐÓNG HỌC PHÍ HK $hk</div>
		<div align=left style='margin:0 0 10px 5px; font-weight:bold'>Học viên: {$hv["HO_TEN"][0]} (Mã số: $mahv)</div>
		";
		
		// In so du dau ky , cuoi ky
		echo "
			<table width='100%' border='0'  cellspacing='0' cellpadding='3' class='ui-widget ui-widget-content ui-corner-top tableData' >
			  <thead>
			  <tr class='ui-widget-header heading' style='height:40px; font-weight:bold; font-size:13pt;'>
				<td class='ui-corner-tl' align=center>Nợ Đầu Kỳ</td>
				<td align=center>Phát Sinh</td>
				<td align=center>Thanh Toán</td>
				<td class='ui-corner-tr' align=center>Nợ Cuối Kỳ</td>
			  </tr>
			  </thead>
			  <tbody>
			  <tr style='height:40px; font-weight:bold; font-size:13pt;'>
				<td align=center>".number_format($hocphi["DAU_KY"][0])."</td>
				<td align=center>".number_format($hocphi["PHAT_SINH"][0])."</td>
				<td align=center>".number_format($hocphi["THANH_TOAN"][0])."</td>
				<td align=center>".number_format($hocphi["CUOI_KY"][0])."</td>
			  </tr>
			  </tbody>
			</table>
			";
		
		
		// Chi tiet phat sinh
		$sqlstr = "SELECT transaction_id, ma_hoc_vien, to_char(ngay, 'dd/mm/yyyy') ngay, noi_dung, payment, charge
				   FROM transactions
				  WHERE ma_hoc_vien = '$mahv' 
					AND (ngay BETWEEN '$ngay_bd_hk' AND '$ngay_kt_hk')";
		//echo $sqlstr;	
		$stmt = oci_parse($db_conn, $sqlstr);
		oci_execute($stmt);
		$n = oci_fetch_all($stmt, $tran);
		oci_free_statement($stmt);
		if ($n>0)
		{
			echo "
			<div align=left style='margin:20px 0 10px 5px; font-weight:bold'>Chi tiết phát sinh trong kỳ</div>
			<table width='100%' border='0'  cellspacing='0' cellpadding='3' class='ui-widget ui-widget-content ui-corner-top tableData' >
			<thead>
			  <tr class='ui-widget-header heading' style='height:20pt; font-weight:bold'>
				<td class='ui-corner-tl' align=left>Số GD</td>
				<td align=left>Ngày GD</td>
				<td align=left>Diễn giải</td>
				<td align=right>Phải Đóng</td>
				<td class='ui-corner-tr' align=right>Đã Thanh Toán</td>
			  </tr>
			  </thead>
			  <tbody>
			";
			
			$classAlt="alt";
			for ($i = 0; $i < $n; $i++)
			{
				$charge = number_format($tran["CHARGE"][$i]);
				$payment = number_format($tran["PAYMENT"][$i]);
				
				if ($charge==0)
					$charge = '';
				if ($payment==0)
					$payment='';
					
				($classAlt=='alt') ? $classAlt="alt_" : $classAlt="alt";
				echo "<tr class='$classAlt' style='height:20px;'>";
				echo "<td valign=top align=left>{$tran["TRANSACTION_ID"][$i]}</td>";
				echo "<td valign=top align=left>{$tran["NGAY"][$i]}</td>";
				echo "<td valign=top align=left>{$tran["NOI_DUNG"][$i]}</td>";
				echo "<td valign=top align=right><strong>$charge</strong></td>";
				echo "<td valign=top align=right><strong>$payment</strong></td>";
				echo "</tr>";
			}
			echo "
			  </tbody>
			</table>
			";	
		}
		// Ghi chu
		echo "
		<div style='margin: 20px 0 0 5px;' align=left>
			<div style='color:red; font-weight:bold;'>Ghi chú:</div>
			<div style='margin: 5px 0 0 5px;'>- Nợ Đầu Kỳ > 0 : Số tiền nợ cần phải đóng của học kỳ trước</div>
			<div style='margin: 5px 0 0 5px;'>- Nợ Đầu Kỳ < 0 : Số tiền còn dư của học kỳ trước</div>
			<div style='margin: 5px 0 0 5px;'>- Nợ Cuối Kỳ > 0 : Số tiền cần phải đóng trong học kỳ này</div>
			<div style='margin: 5px 0 0 5px;'>- Nợ Cuối Kỳ < 0 : Số tiền còn dư trong học kỳ này</div>
		</div>
		";
	}
}
?>

<?php 
if (isset ($db_conn))
	oci_close($db_conn);
?>