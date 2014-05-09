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
		<td align='center'>Lớp/DS Lớp</td>
		<td align='center'>Tiết BĐ</td>
		<td align='center'>Tiết KT</td>
		<td align='left'>Phòng</td>
		<td align='center'>Tuần BĐ</td>
		<td class='ui-corner-tr' align='center'>Tuần KT</td>
	  </tr>
	  </thead>
	  <tbody>
	";
	$classAlt = 'alt';
	for ($i = 0; $i < $n; $i++)
	{
		($classAlt=='alt') ? $classAlt="alt_" : $classAlt="alt";
		
		if ($resDM["MA_LOAI"][$i]!='5'){
			$linkdslop = " / <a title='Tải về DS lớp' href=\"javascript: tkb_canhan_loadDSLopFile('$dothoc','".$resDM["LOP"][$i]."','".$resDM["MA_MH"][$i]."')\"> <img border='0' width='16' height='16' src='icons/save-icon.png' style='margin-bottom:-5px' /> </a>";
		}else{
			$linkdslop = "";
		}
		
		echo "<tr align='left' valign='top' class=' ".$classAlt."' style='height:20pt;'>";
		echo "<td valign=middle align=left><b>".$thu[$resDM["THU"][$i]]."</b></td>";
		echo "<td valign=middle align='left'>".$resDM["HO_TEN"][$i] .$resDM["CBGD"][$i]."</td>";
		echo "<td valign=middle align='left'>(" . $resDM["MA_MH"][$i] .") - ". $resDM["TEN_MH"][$i]."</td>";
		echo "<td valign=middle align='center'><b>".$resDM["LOP"][$i]."</b>$linkdslop</td>";
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
else if ($type=='dslopfile')
{
	//define('EOL',(PHP_SAPI == 'cli') ? PHP_EOL : '<br />');

	/** Include PHPExcel */
	require_once '../phpexcel/Classes/PHPExcel.php';
	$objPHPExcel = new PHPExcel();

	//$lop = $_REQUEST['lop'];
	$monhoc = $_REQUEST['monhoc'];
	$dothoc = $_REQUEST['d'];
	$mahv = base64_decode($_SESSION["mahv"]);
	$ddis = $_REQUEST['ddis'];
	
	// Lay lop hoc dua vao mon hoc, dot hoc, mahv
	$sqlstr="	SELECT lop 
				FROM dang_ky_mon_hoc 
				WHERE DOT_HOC = '".$dothoc."' 
				AND MA_MH = '".$monhoc."'
				and ma_hoc_vien = '".$mahv."'";
	$stmt = oci_parse($db_conn, $sqlstr);oci_execute($stmt);$n = oci_fetch_all($stmt, $resDM);oci_free_statement($stmt);
	$lop = $resDM["LOP"][0];

	// Lay danh sach hoc vien
	$sqlstr="	SELECT dk.ma_hoc_vien, h.ho, h.ten, decode(h.phai,'F','Nữ','Nam') PHAI, dk.lop, dk.ma_mh
				FROM dang_ky_mon_hoc DK, hoc_vien h 
				WHERE DK.DOT_HOC = '".$dothoc."' 
				AND DK.MA_MH = '".$monhoc."'
				AND DK.LOP= '".$lop."'
				and dk.ma_hoc_vien = h.ma_hoc_vien
				order by h.ten";
	$stmt = oci_parse($db_conn, $sqlstr);oci_execute($stmt);$n = oci_fetch_all($stmt, $resDM);oci_free_statement($stmt);
	
	// Lay ten mon hoc
	$sqlstr="select ten from mon_hoc where ma_mh = '".$monhoc."'";
	$stmt = oci_parse($db_conn, $sqlstr);oci_execute($stmt);oci_fetch_all($stmt, $resMH);oci_free_statement($stmt);
	
	// Lay nam hoc hoc ky
	$sqlstr="select (nam_hoc_tu || '-' || nam_hoc_den || '/HK ' || hoc_ky) nam_hoc from dot_hoc_nam_hoc_ky where dot_hoc  = '".$dothoc."'";
	$stmt = oci_parse($db_conn, $sqlstr);oci_execute($stmt);oci_fetch_all($stmt, $resKhoa);oci_free_statement($stmt);
	
	date_default_timezone_set('Asia/Ho_Chi_Minh');
	$today =date("d.m.Y");
	$time = date("H.i.s");
	
	$pathfile = "download/tmp/{$mahv}_{$today}_{$time}_dslopDOT_".$dothoc.'_MH_'.$monhoc.'_LOP_'.$lop.'.xlsx';
		
	// Set document properties
	$objPHPExcel->getProperties()->setCreator("$mahv")
								 ->setLastModifiedBy("$mahv")
								 ->setTitle("DANH SACH LOP $lop - DOT $dothoc - MON HOC $monhoc")
								 ->setSubject("DANH SACH LOP $lop - DOT $dothoc - MON HOC $monhoc")
								 ->setDescription("")
								 ->setKeywords("")
								 ->setCategory("Danh sach lop");
	// Set default font
	$objPHPExcel->getDefaultStyle()->getFont()->setName('Arial')
											  ->setSize(10);
	$objPHPExcel->getActiveSheet()->setCellValue('A1', "Danh sách lớp $lop, môn {$resMH["TEN"][0]}, Khóa {$resKhoa["NAM_HOC"][0]}");
	$objPHPExcel->getActiveSheet()->setCellValue('A2', 'STT')
								  ->setCellValue('B2', 'Mã HV')
								  ->setCellValue('C2', 'Họ')
								  ->setCellValue('D2', 'Tên')
								  ->setCellValue('E2', 'Phái');
	$objPHPExcel->getActiveSheet()->getStyle('A2:G2')->getFont()->setBold(true);
	$objPHPExcel->getActiveSheet()->getStyle('A1')->getFont()->setBold(true);
	
	for ($i = 0; $i < $n; $i++)
	{
		//$tmp = $resDM["MA_HOC_VIEN"][$i] . chr(9) . $resDM["HO"][$i]. chr(9) . $resDM["TEN"][$i] . chr(9) .$resDM["PHAI"][$i]. chr(9) .$resDM["KHOA"][$i]. chr(9) .$resDM["EMAIL"][$i].chr(13).chr(10);
		//$tmp = mb_convert_encoding($tmp, "utf-8", "utf-8"); 
		//fwrite($fp, $tmp);
		$j=$i+3;
		$objPHPExcel->getActiveSheet()->setCellValue("A$j", ($j-2))
								  ->setCellValue("B$j", "'".$resDM["MA_HOC_VIEN"][$i])
								  ->setCellValue("C$j", $resDM["HO"][$i])
								  ->setCellValue("D$j", $resDM["TEN"][$i])
								  ->setCellValue("E$j", $resDM["PHAI"][$i]);
	}
	//fclose($fp);
	$objPHPExcel->getActiveSheet()->getColumnDimension('A')->setWidth(6);
	$objPHPExcel->getActiveSheet()->getColumnDimension('B')->setAutoSize(true);
	$objPHPExcel->getActiveSheet()->getColumnDimension('C')->setAutoSize(true);
	$objPHPExcel->getActiveSheet()->getColumnDimension('D')->setAutoSize(true);
	$objPHPExcel->getActiveSheet()->getColumnDimension('E')->setAutoSize(true);
	
	$objPHPExcel->getActiveSheet()->setTitle('Danh sách lớp');
	$objPHPExcel->setActiveSheetIndex(0);
	$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
	$objWriter->save($pathfile);

	echo '{"url":"./'.$pathfile.'"}';
}
?>

<?php 
if (isset ($db_conn))
	oci_close($db_conn);
?>