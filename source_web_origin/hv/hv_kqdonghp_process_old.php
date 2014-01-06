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


if ($type=='dothoc-kqdonghp')
{
	# thong tin lam luan van, hoc vien
	$sqlstr = "SELECT h.ho||' '||h.ten Ho_ten, khoa, 
						(select sum(sl) from 
						(SELECT count(*) sl
						   FROM chi_tiet_bien_lai_hoc_phi ct, hoc_phi_hoc_vien hp
						  WHERE hp.so_cuon = ct.so_cuon and hp.so_bien_lai = ct.so_bien_lai
							and hp.ma_hoc_vien = '$mahv'
							and hp.dot_hoc = '$dothoc'
						union all
						 select count(*) as sl 
						 from hoc_phi_luan_van lv
						 where lv.ma_hoc_vien = '$mahv'
						 and lv.dot_hoc = '$dothoc')
							) sl_dong, 
							
						(select sum(hp) from
						 (select sum(hoc_phi) hp from bao_luu_hoc_phi 
						 where ma_hoc_vien = '$mahv'
						 union all
						 select sum(hoc_phi) hp from bao_luu_hoc_phi_lvtn 
						 where ma_hoc_vien = '$mahv')) bluu,
						
						(select sum(hp) from
						(select sum(hoc_phi) hp from hoc_phi_hoc_vien 
						where ma_hoc_vien =  '$mahv' and 			
						dot_hoc = '$dothoc'
						union all
						select sum(hoc_phi) hp from hoc_phi_luan_van 
						where ma_hoc_vien =  '$mahv' and 			
						dot_hoc = '$dothoc'))
						da_dong, '$dothoc' dot_hien_tai
				FROM hoc_vien h 
				WHERE ma_hoc_vien = '$mahv'";
						
	$stmt = oci_parse($db_conn, $sqlstr);oci_execute($stmt);
	$n = oci_fetch_all($stmt, $hv);
	oci_free_statement($stmt);
	
	//echo $sqlstr;
	if ($n > 0 ) 
	{
		echo "
		<div align=center style='margin:0 0 15px 0; font-weight:bold'>KẾT QUẢ ĐÓNG HỌC PHÍ</div>
		<div align=left style='margin:0 0 5px 5px; font-weight:bold'>Học viên: {$hv["HO_TEN"][0]} (Mã số: $mahv)</div>
		";
		
		$sqlstr = "SELECT d.ma_mh, ten ten_mh, 
					(so_tiet_lt+so_tiet_tl+so_tiet_bt) LT, so_tiet_th TH, 
					web_gia_lt(d.ma_hoc_vien, d.ma_mh, d.dot_hoc) GIA_LT,
					web_gia_th(d.ma_hoc_vien, d.ma_mh, d.dot_hoc) GIA_TH,
					hoc_phi_mon_hoc(d.ma_hoc_vien, d.dot_hoc, d.ma_mh) hoc_phi_mh
					, hoc_phi_mon_hoc(d.ma_hoc_vien, d.dot_hoc, d.ma_mh) hoc_phi 				
					, decode(m.ma_loai, '5', 
						(select 'x' from hoc_phi_luan_van lv1
							where lv1.ma_hoc_vien = '$mahv'
							and dot_hoc  = '$dothoc'), 
						(select 'x' from chi_tiet_bien_lai_hoc_phi ct1, hoc_phi_hoc_vien hp1
						where hp1.so_bien_lai = ct1.so_bien_lai
						and hp1.so_cuon = ct1.so_cuon 
						and hp1.ma_hoc_vien = d.ma_hoc_vien 
						and hp1.dot_hoc = d.dot_hoc and ct1.ma_mh = d.ma_mh)) dong
					,(select 'x' from chi_tiet_bien_lai_hoc_phi ct, hoc_phi_hoc_vien hp
					   where hp.so_bien_lai = ct.so_bien_lai
						 and hp.so_cuon = ct.so_cuon 
						 and hp.ma_hoc_vien = d.ma_hoc_vien 
						 and hp.dot_hoc = d.dot_hoc and ct.ma_mh = d.ma_mh) da_dong		 
				   from dang_ky_mon_hoc d, mon_hoc m
				  where d.ma_mh = m.ma_mh and ma_hoc_vien = '$mahv' 
					and d.dot_hoc = '$dothoc'";
		//echo $sqlstr;			
		$stmt = oci_parse($db_conn, $sqlstr);
		oci_execute($stmt);
		$n = oci_fetch_all($stmt, $dkmh);
		oci_free_statement($stmt);
		if ($n>0)
		{
			echo "
			<table width='100%' border='0'  cellspacing='0' cellpadding='3' class='ui-widget ui-widget-content ui-corner-top tableData' >
			<thead>
			  <tr class='ui-widget-header heading' style='height:20pt; font-weight:bold'>
				<td class='ui-corner-tl' align='left'>Mã MH</td>
				<td align=left>Tên môn học</td>
				<td align=center>LT</td>
				<td align=right>Đơn giá LT</td>
				<td align=center>TH</td>
				<td align=right>Đơn giá TH</td>
				<td class='ui-corner-tr' align=right>Thành tiền</td>
			  </tr>
			  </thead>
			  <tbody>
			";
			
			$tong_hphi = 0;
			$classAlt="alt";
			for ($i = 0; $i < $n; $i++)
			{
				//if ($dkmh["DONG"][$i]!='x')
					$tong_hphi += $dkmh["HOC_PHI"][$i];
					
				($classAlt=='alt') ? $classAlt="alt_" : $classAlt="alt";
				echo "<tr class='$classAlt' style='height:20px;'>";
				echo "<td valign=middle align=left>{$dkmh["MA_MH"][$i]}</td>";
				echo "<td valign=middle align=left>{$dkmh["TEN_MH"][$i]}</td>";
				echo "<td valign=middle align=center>{$dkmh["LT"][$i]}</td>";
				echo "<td valign=middle align=right>". number_format($dkmh["GIA_LT"][$i])."</td>";
				echo "<td valign=middle align=center>{$dkmh["TH"][$i]}</td>";
				echo "<td valign=middle align=right>". number_format($dkmh["GIA_TH"][$i]) . "</td>";
				echo "<td valign=middle align=right>". number_format($dkmh["HOC_PHI"][$i]) ."</td>";				
				echo "</tr>";
			}
			echo "
			<tr style='font-weight:bold;' class='alt_'>
				<td colspan='5' align=left>- Học phí</td>
				<td align=right></td>
				<td align=right>".number_format($tong_hphi). "</td>
			</tr>";
			
			$hpdadong = $hv["DA_DONG"][0];
			$bluu = $hv["BLUU"][0];
			($bluu >= 0) ? $bluutext = "Bảo lưu học phí" : $bluutext = "Nợ học phí";
			echo "
			<tr style='font-weight:bold;' class='alt_'>
				<td colspan='5' align=left>- $bluutext</td>
				<td ></td>
				<td align=right>".number_format(abs($bluu))."</td>
			</tr>";
			
			if ($dothoc == $dothoc_ht)
			{
				echo "
				<tr style='font-weight:bold;' class='alt_'>
					<td colspan='5' align=left>- Đã đóng</td>
					<td align='right'></td>
					<td align=right>" . number_format($hpdadong) . "</td>
				</tr>";
				
				echo "
				<tr style='font-weight:bold;' class='alt_'>
					<td colspan='5' align=left>- Còn lại</td>
					<td ></td>
					<td align='right'>" . number_format($tong_hphi-$bluu-$hpdadong) . "</td>
				</tr>";
			}
			
			//echo "<tr class=alt><td colspan='8' align=left><b>- Số môn học đã đóng học phí <i> {$hv["SL_DONG"][0]} / $n</i></b></td></tr>";
			//echo "<tr  style='height:30pt;'><td colspan='7' align=center>* <i><u>Chú ý:</u> </i> Những môn không đóng học phí sẽ <font color=red><b>không có tên trong DANH SÁCH THI</b></font>
			//</td></tr>";
			
			echo "
			  </tbody>
			</table>
			";	
		}
	}
}
?>

<?php 
if (isset ($db_conn))
	oci_close($db_conn);
?>