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

if ($type=='dothoc-tkb_canhan')
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
	
	# thong tin lam luan van, hoc vien
	$sqlstr = "SELECT DISTINCT l.ten_de_tai, d.ma_mh, d.ma_hoc_vien, q.so_quyet_dinh, h.ho || ' ' || h.ten ho_ten,
								(TO_CHAR(q.ngay_bat_dau_luan_van, 'DD/MM/YYYY') || '-' || TO_CHAR(q.ngay_nop_luan_van,'DD/MM/YYYY')) NGAY_LUAN_VAN,
								c.ho || ' ' || c.ten ho_ten, c2.ho || ' ' || c2.ten ho_ten2, to_char(dot_nhan_lv, 'dd/mm/yyyy') dot_nhan_lv,
								(select count(*) from hoc_phi_luan_van hp
								where hp.ma_hoc_vien = '$mahv' 
								and hp.dot_hoc = '$dothoc') hoc_phi
			FROM 	hoc_vien h, dang_ky_mon_hoc d, luan_van_thac_sy l,
					mon_hoc m, can_bo_giang_day c, can_bo_giang_day c2, QUYET_DINH_GIAO_DE_TAI q
			WHERE d.ma_hoc_vien = h.ma_hoc_vien
				AND l.huong_dan_chinh = c.ma_can_bo
				AND l.huong_dan_phu = c2.ma_can_bo(+)
				AND l.SO_QUYET_DINH_GIAO_DE_TAI = q.so_quyet_dinh(+)
				AND d.dot_hoc = '$dothoc' 
				AND l.ma_hoc_vien = h.ma_hoc_vien 
				AND l.dot_nhan_lv = '$dothoc' 
				AND m.ma_loai = '5' 
				AND h.ma_hoc_vien =  '$mahv' 
				AND d.dot_hoc  =  '$dothoc'";
	
	$stmt = oci_parse($db_conn, $sqlstr);
	oci_execute($stmt);
	$n = oci_fetch_all($stmt, $resDM);
	oci_free_statement($stmt);
	if ($n > 0 ) 
	{
		if ($lv["HO_TEN2"][0]!='')
			$cbhd2 = ", ".$resDM["HO_TEN2"][0];
		$ttlv = "
		<div align=left class='ui-widget ui-widget-content ui-corner-top tableData' style='margin-top:20px;'>
		<p><b>THÔNG TIN LUẬN VĂN THẠC SĨ</b>";
		$ttlv .= "<ul>";
		$ttlv .= "<li>Tên luận văn: {$resDM["TEN_DE_TAI"][0]}</li>";			
		$ttlv .= "<li>Cán bộ hướng dẫn: {$resDM["HO_TEN"][0]} $cbhd2</li>";
		if ($resDM["NGAY_LUAN_VAN"][0]!='-')
			$ttlv .= "<li>Thời gian thực hiện LV: {$resDM["NGAY_LUAN_VAN"][0]}</li>";
		/*
		if ($resDM["HOC_PHI"][0] > 0)
			$ttlv .= "<li>Học phí: đã đóng</li>";
		else
			$ttlv .= "<li>Học phí: <span style='color:red;'>chưa đóng</span></li>";
		*/
		$ttlv .= "</ul>
		</p>
		</div>";
	}
			
	$sqlstr="SELECT DISTINCT h.ma_hoc_vien, thu
			, d.ma_mh, m.ten ten_mh,  t.lop, phong, h.khoa, ten_nganh, m.ma_loai
			, to_char(d.dot_hoc,'DD/MM/YYYY') DOT_HOC
			, tiet_bat_dau, tiet_ket_thuc, tuan_bat_dau, tuan_ket_thuc, ho_ten cbgd

			, hoc_phi_mon_hoc(d.ma_hoc_vien, d.dot_hoc, d.ma_mh) hoc_phi_mh
			, (SELECT hoc_phi FROM hoc_phi_luan_van lv
				WHERE lv.ma_hoc_vien = '$mahv'
				AND lv.dot_hoc  = '$dothoc' ) hoc_phi_lv
			, decode(m.ma_loai, '5', 
				(SELECT hoc_phi FROM hoc_phi_luan_van lv
				WHERE lv.ma_hoc_vien = '$mahv'
				AND lv.dot_hoc  = '$dothoc' ), 
				hoc_phi_mon_hoc(d.ma_hoc_vien, d.dot_hoc, d.ma_mh)) hoc_phi 				
			, decode(m.ma_loai, '5', 
				(SELECT 'x' FROM hoc_phi_luan_van lv1
					WHERE lv1.ma_hoc_vien = '$mahv'
					AND lv1.dot_hoc  = '$dothoc' ), 
				(SELECT 'x' FROM chi_tiet_bien_lai_hoc_phi ct1, hoc_phi_hoc_vien hp1
				WHERE hp1.so_bien_lai = ct1.so_bien_lai
				AND hp1.so_cuon = ct1.so_cuon 
				AND hp1.ma_hoc_vien = d.ma_hoc_vien 
				AND hp1.dot_hoc = d.dot_hoc AND ct1.ma_mh = d.ma_mh)) dong
			, (SELECT 'x' FROM chi_tiet_bien_lai_hoc_phi ct, hoc_phi_hoc_vien hp
				WHERE hp.so_bien_lai = ct.so_bien_lai
				AND hp.so_cuon = ct.so_cuon
				AND hp.ma_hoc_vien = d.ma_hoc_vien
				AND hp.dot_hoc = d.dot_hoc AND ct.ma_mh = d.ma_mh) da_dong
			FROM hoc_vien h, dang_ky_mon_hoc d, thoi_khoa_bieu t, nganh n, mon_hoc m
			WHERE d.ma_hoc_vien = h.ma_hoc_vien
			AND h.ma_nganh = n.ma_nganh AND d.dot_hoc = t.dot_hoc(+) 
			AND d.ma_mh = t.ma_mh(+) AND d.lop = t.lop(+) 
			AND d.ma_mh = m.ma_mh AND d.ma_mh <> 'TAM_THU'
			AND h.ma_hoc_vien = '$mahv' 
			AND d.dot_hoc  = '$dothoc' 
			ORDER BY tuan_bat_dau, thu, tiet_bat_dau, tiet_ket_thuc";
	$stmt = oci_parse($db_conn, $sqlstr);
	oci_execute($stmt);
	$n = oci_fetch_all($stmt, $resDM);
	oci_free_statement($stmt);
	
	echo "
	<div style='margin-bottom:20px; font-size:14px;' align=center><b>THỜI KHÓA BIỂU CÁ NHÂN HK $hk</b><br/> 
				Ngày bắt đầu học kỳ: <b>{$resDM['DOT_HOC'][0]} (Tuần 1)</b><br/>
				Ngành: {$resDM['TEN_NGANH'][0]}
	</div>
	<div style='margin-bottom:10px; font-size:14px;' align=left>$ttcn</div>
	<table width='100%' border='0'  cellspacing='0' cellpadding='3' class='ui-widget ui-widget-content ui-corner-top tableData' >
	<thead>
	  <tr class='ui-widget-header heading' style='height:20pt;'>
		<td class='ui-corner-tl' align='left'>Thứ</td>
		<td >CBGD</td>
		<td >Môn học</td>
		<td align='center'>Lớp MH</td>
		<td align='center'>Tiết BĐ</td>
		<td align='center'>Tiết KT</td>
		<td align='left'>Phòng</td>
		<td align='center'>Tuần BĐ</td>
		<td class='ui-corner-tr' align='right'>Tuần KT</td>
	  </tr>
	  </thead>
	  <tbody>
	";
	$classAlt = 'alt';
	for ($i = 0; $i < $n; $i++)
	{
		($classAlt=='alt') ? $classAlt="alt_" : $classAlt="alt";
		
		echo "<tr align='left' valign='top' class=' ".$classAlt."' style='height:20px;'>";
		echo "<td valign=middle align=left><b>".$thu[$resDM["THU"][$i]]."</b></td>";
		echo "<td valign=middle align='left'>".$resDM["HO_TEN"][$i] .$resDM["CBGD"][$i]."</td>";
		echo "<td valign=middle align='left'>(" . $resDM["MA_MH"][$i] .") - ". $resDM["TEN_MH"][$i]."</td>";
		echo "<td valign=middle align='center'>".$resDM["LOP"][$i]."</td>";
		echo "<td valign=middle align='center'>".$resDM["TIET_BAT_DAU"][$i]."</td>";
		echo "<td valign=middle align='center'>".$resDM["TIET_KET_THUC"][$i]."</td>";
		echo "<td valign=middle align='left'><b>".$resDM["PHONG"][$i]."</b></td>";
		echo "<td valign=middle align='center'><b>".$resDM["TUAN_BAT_DAU"][$i]."</b></td>";
		echo "<td valign=middle align='center'><b>".$resDM["TUAN_KET_THUC"][$i]."</b></td>";
		echo "</tr>";
	}
	echo "
	  </tbody>
	</table>
	";
	echo $ttlv;
	
	include "hv_tiethoc.php";

}
?>

<?php 
if (isset ($db_conn))
	oci_close($db_conn);
?>