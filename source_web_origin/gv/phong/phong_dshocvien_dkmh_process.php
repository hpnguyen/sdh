<?php
if (isset($_REQUEST["hisid"])){
	session_id($_REQUEST["hisid"]);
	session_start();
}
if (!isset($_SESSION['uidloginPortal'])){
	die('Truy cập bất hợp pháp'); 
}

include "../libs/connect.php";
include "../libs/pgslibs.php";

if (!allowPermisstion(base64_decode($_SESSION['uidloginPortal']), '019', $db_conn))
{
	die('Truy cập bất hợp pháp'); 
}

$usr = base64_decode($_SESSION['uidloginPortal']);
$makhoa = str_replace("'", "''",$_POST['khoa']);
$dot = str_replace("'", "''",$_POST['d']);
$hk = str_replace("'", "''",$_POST['h']);
$a = $_POST["a"];

$qKhoa = "";
if ($makhoa!='')
	$qKhoa = "AND k.ma_khoa = '$makhoa'";
	
if ($a=='dshocvien') 
{
	$sqlstr="	
			SELECT DISTINCT k.ten_khoa, h.MA_HOC_VIEN, h.HO, h.TEN , 
				DECODE(h.NGAY_SINH, null, NGAY_SINH_KHONG_CHUAN, TO_CHAR(h.NGAY_SINH, 'dd/mm/yyyy')) Ngay_Sinh, 
				DECODE(h.PHAI, 'M', 'Nam ', 'Nữ') PHAI, TEN_TINH_TP NOI_SINH, N.TEN_NGANH,
				DECODE(ctdt_loai(h.MA_HOC_VIEN), 1, 'GDMH-KLTN', 3 , 'Nghiên cứu' , 'GDMH-LVThs' )  huongdt,
				tinh_dtb_sdhbk(h.MA_HOC_VIEN) dtb_tich_luy, tong_tin_chi_tich_luy(h.MA_HOC_VIEN) tong_tc_tich_luy
			FROM hoc_vien h, dang_ky_mon_hoc d, nganh n, bo_mon b, khoa k, dm_tinh_tp t
			WHERE h.ma_hoc_vien = d.ma_hoc_vien
			AND d.dot_hoc = '$dot'
			$qKhoa
			AND h.ma_bac = 'TH'
			AND h.fk_hinh_thuc_dao_tao = 'CQ'
			AND h.ma_nganh = n.ma_nganh
			AND n.ma_bo_mon = b.ma_bo_mon
			AND b.ma_khoa = k.ma_khoa
			AND h.noi_sinh = t.ma_tinh_tp (+)
			ORDER BY TEN_NGANH, HO, TEN
			"; 
			
	$stmt = oci_parse($db_conn, $sqlstr);
	oci_execute($stmt);
	$n = oci_fetch_all($stmt, $resDM);
	oci_free_statement($stmt);
	
	if ($makhoa!='')
		$tieudekhoa = "<br/>Khoa {$resDM["TEN_KHOA"][0]}";
	else
		$tieudekhoa = '';
		
	//echo $sqlstr;
	echo "
		<div align='center'><h2>Danh Sách Học Viên ĐKMH HK $hk $tieudekhoa</h2></div>
		<div style='margin-bottom:20px;'>
			<table id='phong_tableDSHocVienKhoa' name='phong_tableDSHocVienKhoa' width='100%' border='0'  cellspacing='0' class='ui-widget ui-widget-content ui-corner-top tableData bordertable' >
			<thead>
			  <tr class='bordertable ui-widget-header' style='height:20pt;font-weight:bold;'>
				<td align='center' class='bordertable'>STT</td>
				<td class='bordertable'>Mã HV</td>
				<td class='bordertable'>Họ</td>
				<td class='bordertable' align='left'>Tên</td>
				<td class='bordertable' align='left' style=''>Phái</td>
				<td class='bordertable' align='center'>Ngày Sinh</td>
				<td class='bordertable' align='left'>Nơi Sinh</td>
				<td class='bordertable' align='left'>Ngành</td>
				<td class='bordertable' align=left >Loại CTĐT</td>
				<td class='bordertable' align='right'>ĐTB tích luỹ</td>
				<td class='bordertable' align='center'>Số TC tích luỹ</td>
			  </tr>
			  </thead>
			  <tbody>
	";
	for ($i = 0; $i < $n; $i++)
	{
		($i % 2) ? $classAlt="alt" : $classAlt="alt_";
		echo "<tr align='left' valign='middle' class='bordertable ".$classAlt." ' style='height:20px'>";				
		echo "<td class='bordertable' align='center'>" .($i+1)."</td>";
		echo "<td class='bordertable' align='left'>".$resDM["MA_HOC_VIEN"][$i]."</td>";
		echo "<td class='bordertable' align='left'>".$resDM["HO"][$i]."</td>";
		echo "<td class='bordertable' align='left'>".$resDM["TEN"][$i]."</td>";
		echo "<td class='bordertable' align='left'>".$resDM["PHAI"][$i]."</td>";
		echo "<td class='bordertable' align='center'>".$resDM["NGAY_SINH"][$i]."</td>";
		echo "<td class='bordertable' align='left' style=''>{$resDM["NOI_SINH"][$i]}</td>";
		echo "<td class='bordertable' align='left' style=''>{$resDM["TEN_NGANH"][$i]}</td>";
		echo "<td class='bordertable' align='left' style=''>{$resDM["HUONGDT"][$i]}</td>";
		echo "<td class='bordertable' align='right' style=''>".number_format($resDM["DTB_TICH_LUY"][$i], 2, ',', '.')."</td>";
		echo "<td class='bordertable' align='center' style=''>{$resDM["TONG_TC_TICH_LUY"][$i]}</td>";
		echo "</tr>";
	} 
	echo "
				</tbody>
			</table>
		</div>
	";
	
}
else if ($a=='dshocvienfile') 
{
	$sqlstr="
			SELECT DISTINCT k.ten_khoa, h.MA_HOC_VIEN, h.HO, h.TEN , 
				DECODE(h.NGAY_SINH, null, NGAY_SINH_KHONG_CHUAN, TO_CHAR(h.NGAY_SINH, 'dd/mm/yyyy')) Ngay_Sinh, 
				DECODE(h.PHAI, 'M', 'Nam ', 'Nữ') PHAI, TEN_TINH_TP NOI_SINH, N.TEN_NGANH,
				DECODE(ctdt_loai(h.MA_HOC_VIEN), 1, 'GDMH-KLTN', 3 , 'Nghiên cứu' , 'GDMH-LVThs' )  huongdt,
				tinh_dtb_sdhbk(h.MA_HOC_VIEN) dtb_tich_luy, tong_tin_chi_tich_luy(h.MA_HOC_VIEN) tong_tc_tich_luy
			FROM hoc_vien h, dang_ky_mon_hoc d, nganh n, bo_mon b, khoa k, dm_tinh_tp t
			WHERE h.ma_hoc_vien = d.ma_hoc_vien
			AND d.dot_hoc = '$dot'
			$qKhoa
			AND h.ma_bac = 'TH'
			AND h.fk_hinh_thuc_dao_tao = 'CQ'
			AND h.ma_nganh = n.ma_nganh
			AND n.ma_bo_mon = b.ma_bo_mon
			AND b.ma_khoa = k.ma_khoa
			AND h.noi_sinh = t.ma_tinh_tp (+)
			ORDER BY TEN_NGANH, HO, TEN
			";
				
	$stmt = oci_parse($db_conn, $sqlstr);
	oci_execute($stmt);
	$n = oci_fetch_all($stmt, $resDM);
	oci_free_statement($stmt);
	
	date_default_timezone_set('Asia/Ho_Chi_Minh');
	$today =date("d.m.Y");
	$time = date("H.i.s");
	
	//$pathfile = "download/{$usr}_{$today}_{$time}_Khoa{$makhoa}_dsHocVien_dkmh_hk_".str_replace("/","_",$hk).".txt";
	$pathfile = "download/{$usr}_{$today}_{$time}_Khoa{$makhoa}_dsHocVien_dkmh_hk_".str_replace("/","_",$hk).".xlsx";
	
	//$fp = fopen($pathfile, 'w');
	//fwrite($fp,  'Mã HV' . chr(9) . 'Họ' . chr(9) . 'Tên' . chr(9) . 'Phái' . chr(9) . 'Ngày Sinh' . chr(9) . 'Nơi Sinh' . chr(9) . 'Ngành'. chr(9) . 'Loại CTĐT' .chr(13).chr(10) ) ;
	
	/** Include PHPExcel */
	require_once '../../phpexcel/Classes/PHPExcel.php';
	$objPHPExcel = new PHPExcel();
	// Set document properties
	$objPHPExcel->getProperties()->setCreator("$usr")
								 ->setLastModifiedBy("$usr")
								 ->setTitle("DANH SÁCH HỌC VIÊN ĐKMH KHOA $makhoa - HK $hk")
								 ->setSubject("DANH SÁCH HỌC VIÊN ĐKMH KHOA $makhoa - HK $hk")
								 ->setDescription("")
								 ->setKeywords("")
								 ->setCategory("Danh sách học viên");
	// Set default font
	$objPHPExcel->getDefaultStyle()->getFont()->setName('Arial')
											  ->setSize(10);
	$objPHPExcel->getActiveSheet()->setCellValue('A1', "Danh sách học viên ĐKMH Khoa {$resDM["TEN_KHOA"][0]} - Học Kỳ $hk");
	$objPHPExcel->getActiveSheet()->setCellValue('A2', 'STT')
								  ->setCellValue('B2', 'Mã HV')
								  ->setCellValue('C2', 'Họ')
								  ->setCellValue('D2', 'Tên')
								  ->setCellValue('E2', 'Phái')
								  ->setCellValue('F2', 'Ngày sinh')
								  ->setCellValue('G2', 'Nơi sinh')
								  ->setCellValue('H2', 'Ngành')
								  ->setCellValue('I2', 'Loại CTĐT')
								  ->setCellValue('J2', 'ĐTB tích luỹ')
								  ->setCellValue('K2', 'Số TC tích luỹ');
	$objPHPExcel->getActiveSheet()->getStyle('A2:K2')->getFont()->setBold(true);
	$objPHPExcel->getActiveSheet()->getStyle('A1')->getFont()->setBold(true);
	
	for ($i = 0; $i < $n; $i++)
	{
		//$tmp = $resDM["MA_HOC_VIEN"][$i] . chr(9) . $resDM["HO"][$i]. chr(9) . $resDM["TEN"][$i]
		//.chr(9).$resDM["PHAI"][$i].chr(9).$resDM["NGAY_SINH"][$i].chr(9).$resDM["NOI_SINH"][$i]
		//.chr(9).$resDM["TEN_NGANH"][$i].chr(9).$resDM["HUONGDT"][$i].chr(13).chr(10);
		//fwrite($fp, $tmp);
		$j=$i+3;
		$objPHPExcel->getActiveSheet()->setCellValue("A$j", ($j-2))
								  ->setCellValue("B$j", $resDM["MA_HOC_VIEN"][$i])
								  ->setCellValue("C$j", $resDM["HO"][$i])
								  ->setCellValue("D$j", $resDM["TEN"][$i])
								  ->setCellValue("E$j", $resDM["PHAI"][$i])
								  ->setCellValue("F$j", $resDM["NGAY_SINH"][$i])
								  ->setCellValue("G$j", $resDM["NOI_SINH"][$i])
								  ->setCellValue("H$j", $resDM["TEN_NGANH"][$i])
								  ->setCellValue("I$j", $resDM["HUONGDT"][$i])
								  ->setCellValue("J$j", number_format($resDM["DTB_TICH_LUY"][$i], 2, ',', '.'))
								  ->setCellValue("K$j", $resDM["TONG_TC_TICH_LUY"][$i]);
	}
	//fclose($fp);
	
	//$enc = mb_detect_encoding($pathfile);
	//$pathfile = mb_convert_encoding($pathfile, "ASCII", $enc);
	//$pathfile = $pathfile;
	
	$objPHPExcel->getActiveSheet()->getColumnDimension('A')->setWidth(6);
	$objPHPExcel->getActiveSheet()->getColumnDimension('B')->setAutoSize(true);
	$objPHPExcel->getActiveSheet()->getColumnDimension('C')->setAutoSize(true);
	$objPHPExcel->getActiveSheet()->getColumnDimension('D')->setAutoSize(true);
	$objPHPExcel->getActiveSheet()->getColumnDimension('E')->setAutoSize(true);
	$objPHPExcel->getActiveSheet()->getColumnDimension('F')->setAutoSize(true);
	$objPHPExcel->getActiveSheet()->getColumnDimension('G')->setAutoSize(true);
	$objPHPExcel->getActiveSheet()->getColumnDimension('H')->setAutoSize(true);
	$objPHPExcel->getActiveSheet()->getColumnDimension('I')->setAutoSize(true);
	$objPHPExcel->getActiveSheet()->getColumnDimension('J')->setAutoSize(true);
	$objPHPExcel->getActiveSheet()->getColumnDimension('K')->setAutoSize(true);
	
	$objPHPExcel->getActiveSheet()->setTitle('Danh học viên ĐKMH');
	$objPHPExcel->setActiveSheetIndex(0);
	$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
	$objWriter->save($pathfile);

	echo '{"url":"phong/'.$pathfile.'"}';
}
else if ($a=='dot-khoa')
{
	$sqlstr="SELECT DISTINCT b.ma_khoa, k.ten_khoa
				FROM hoc_vien h, nganh n , bo_mon b, khoa k, dang_ky_mon_hoc d
				WHERE d.ma_hoc_vien = h.ma_hoc_vien
				AND h.khoa >= 2005 
				AND h.ma_nganh = n.ma_nganh
				AND n.ma_bo_mon = b.ma_bo_mon (+)
				AND b.ma_khoa = k.ma_khoa
				AND d.dot_hoc = '$dot'
				ORDER BY ten_khoa"; 
	$stmt = oci_parse($db_conn, $sqlstr);
	oci_execute($stmt);
	$n = oci_fetch_all($stmt, $resDM);
	oci_free_statement($stmt);
	
	echo "<option value=''>Tất cả Khoa</option>";
	for ($i = 0; $i < $n; $i++)
	{
		if ($i==0)
			$selected = "selected";
		else
			$selected = "";
			
		echo "<option value='".$resDM["MA_KHOA"][$i]."' $selected>" .$resDM["TEN_KHOA"][$i]. "</option>";
	}
}

?>

<?php
if (isset ($db_conn))
	oci_close($db_conn);
?>