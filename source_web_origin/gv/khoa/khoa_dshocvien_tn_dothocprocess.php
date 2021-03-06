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
if (!allowPermisstion(base64_decode($_SESSION['uidloginPortal']), '104', $db_conn))
{
	die('Truy cập bất hợp pháp'); 
}

$usr = base64_decode($_SESSION['uidloginPortal']);
$makhoa = str_replace("'", "''",base64_decode($_SESSION['makhoa']));
$dot = str_replace("'", "''",$_POST['d']);
$loaids = str_replace("'", "''",$_POST['loaids']);
$title = "";

if ($loaids=='dshocviendudktn'){
	$sqlstr="select value from config where name='DOT_CAP_BANG'";
	$stmt = oci_parse($db_conn, $sqlstr);oci_execute($stmt);$n = oci_fetch_all($stmt, $resDM);oci_free_statement($stmt);
	$dotcapbang = $resDM["VALUE"][0];
	
	$title = "Danh Sách Học Viên Đủ Điều Kiện Tốt Nghiệp đợt $dotcapbang";
	$sqlstr="	
			SELECT  k.ten_khoa, dot_cap_bang(h.ma_hoc_vien), h.MA_HOC_VIEN, h.HO, h.TEN , dot_cap_bang(h.ma_hoc_vien),
					DECODE(h.NGAY_SINH, null, NGAY_SINH_KHONG_CHUAN, TO_CHAR(h.NGAY_SINH, 'dd/mm/yyyy')) Ngay_Sinh, 
					DECODE(h.PHAI, 'M', 'Nam ', 'Nữ') PHAI, TEN_TINH_TP NOI_SINH, N.TEN_NGANH,
					DECODE(ctdt_loai(h.MA_HOC_VIEN), 1, 'GDMH-KLTN', 3 , 'Nghiên cứu' , 'GDMH-LVThs' )  huongdt, x.dat
			FROM hoc_vien h, nganh n, bo_mon b, khoa k, dm_tinh_tp t, xet_luan_van x
			WHERE h.ma_nganh = n.ma_nganh
			AND h.noi_sinh = t.ma_tinh_tp (+) 
			AND n.ma_bo_mon = b.ma_bo_mon
			AND b.ma_khoa = k.ma_khoa
			and h.ma_hoc_vien = x.ma_hoc_vien
			AND k.ma_khoa = '$makhoa'
			AND dot_cap_bang(h.ma_hoc_vien) is null and (x.dat > 1 or du_dk_cap_bang_bs(h.MA_HOC_VIEN)=1) 
			AND h.KHOA >= 2010
			AND ma_bac = 'TH' AND fk_hinh_thuc_dao_tao = 'CQ'
			ORDER BY ten_nganh, h.ho, h.ten";
	
	//echo $sqlstr;
			
}else if ($loaids=='dshocvientn'){
	$title = "Danh Sách Học Viên Đã Tốt Nghiệp Đợt $dot";
	$sqlstr="
			SELECT  k.ten_khoa, dot_cap_bang(h.ma_hoc_vien), h.MA_HOC_VIEN, h.HO, h.TEN , dot_cap_bang(h.ma_hoc_vien),
					DECODE(h.NGAY_SINH, null, NGAY_SINH_KHONG_CHUAN, TO_CHAR(h.NGAY_SINH, 'dd/mm/yyyy')) Ngay_Sinh, 
					DECODE(h.PHAI, 'M', 'Nam ', 'Nữ') PHAI, TEN_TINH_TP NOI_SINH, N.TEN_NGANH,
					DECODE(ctdt_loai(h.MA_HOC_VIEN), 1, 'GDMH-KLTN', 3 , 'Nghiên cứu' , 'GDMH-LVThs' )  huongdt
			FROM hoc_vien h, nganh n, bo_mon b, khoa k, dm_tinh_tp t
			WHERE h.ma_nganh = n.ma_nganh
			AND h.noi_sinh = t.ma_tinh_tp (+) 
			AND n.ma_bo_mon = b.ma_bo_mon
			AND b.ma_khoa = k.ma_khoa
			AND k.ma_khoa = '$makhoa'
			AND dot_cap_bang(h.ma_hoc_vien) = '$dot'
			AND ma_bac = 'TH'
			AND fk_hinh_thuc_dao_tao = 'CQ'
			ORDER BY ten_nganh, h.ho, h.ten";
}

if ($_REQUEST["a"]=='dshocvien') 
{
	$stmt = oci_parse($db_conn, $sqlstr); oci_execute($stmt); $n = oci_fetch_all($stmt, $resDM); oci_free_statement($stmt);
	
	//echo $sqlstr;
	
	echo "
	<div align='center'><h2>$title <br/>Khoa {$resDM["TEN_KHOA"][0]}</h2></div>
	<div style='margin-bottom:20px;'>
		<table id='khoa_tableDSHocVien' name='khoa_tableDSHocVien' width='100%' border='0'  cellspacing='0' class='ui-widget ui-widget-content ui-corner-top tableData' >
        <thead>
          <tr class='ui-widget-header' style='height:20pt;font-weight:bold;'>
            <td align='center' class='ui-corner-tl'>STT</td>
            <td>Mã HV</td>
			<td>Họ</td>
            <td  align='left'>Tên</td>
            <td  align='left' style=''>Phái</td>
			<td  align='center'>Ngày Sinh</td>
			<td  align='left'>Nơi Sinh</td>
            <td align='left'>Ngành</td>
			<td align='center'>Số hiệu bằng</td>
			<td align=left class='ui-corner-tr'>Loại CTĐT</td>
          </tr>
          </thead>
          <tbody>
	";
	
	
	for ($i = 0; $i < $n; $i++)
	{
		($i % 2) ? $classAlt="alt" : $classAlt="alt_";
		echo "<tr valign=middle class=' ".$classAlt."' style='height:20px;'>";				
		echo "<td align='center'>" .($i+1)."</td>";
		echo "<td align='left'>".$resDM["MA_HOC_VIEN"][$i]."</td>";
		echo "<td align='left'>".$resDM["HO"][$i]."</td>";
		echo "<td align='left'>".$resDM["TEN"][$i]."</td>";
		echo "<td align='left'>".$resDM["PHAI"][$i]."</td>";
		echo "<td align='center'>".$resDM["NGAY_SINH"][$i]."</td>";
		echo "<td align='left' style=''>{$resDM["NOI_SINH"][$i]}</td>";
		echo "<td align='left' style=''>{$resDM["TEN_NGANH"][$i]}</td>";
		echo "<td align='center' style=''>{$resDM["SO_HIEU_BANG"][$i]}</td>";
		echo "<td align='left' style=''>{$resDM["HUONGDT"][$i]}</td>";
		echo "</tr>";
	} 
	
	echo "
	 </tbody>
        </table>
	</div>";
}
else if ($_REQUEST["a"]=='dshocvienfile') 
{
	$stmt = oci_parse($db_conn, $sqlstr);oci_execute($stmt);$n = oci_fetch_all($stmt, $resDM);oci_free_statement($stmt);
	
	date_default_timezone_set('Asia/Ho_Chi_Minh');
	$today =date("d.m.Y");
	$time = date("H.i.s");
	
	$pathfile = "download/{$usr}_{$today}_{$time}_Khoa{$makhoa}_dsHocVien_TN_Dot_$dot.xlsx";
	//$fp = fopen($pathfile, 'w');
	//fwrite($fp,  'Mã HV' . chr(9) . 'Họ' . chr(9) . 'Tên' . chr(9) . 'Phái' . chr(9) . 'Ngày Sinh' . chr(9) . 'Nơi Sinh' . chr(9) . 'Ngành' . chr(9). 'Loại CTĐT' .chr(13).chr(10) ) ;
	
	/** Include PHPExcel */
	require_once '../../phpexcel/Classes/PHPExcel.php';
	$objPHPExcel = new PHPExcel();
	// Set document properties
	$objPHPExcel->getProperties()->setCreator("$usr")
								 ->setLastModifiedBy("$usr")
								 ->setTitle("$title KHOA {$resDM["TEN_KHOA"][0]} - Đợt $dot")
								 ->setSubject("$title KHOA {$resDM["TEN_KHOA"][0]} - Đợt $dot")
								 ->setDescription("")
								 ->setKeywords("")
								 ->setCategory("Danh sách học viên tốt nghiệp");
	// Set default font
	$objPHPExcel->getDefaultStyle()->getFont()->setName('Arial')
											  ->setSize(10);
	$objPHPExcel->getActiveSheet()->setCellValue('A1', "$title Khoa {$resDM["TEN_KHOA"][0]} - Đợt $dot");
	$objPHPExcel->getActiveSheet()->setCellValue('A2', 'STT')
								  ->setCellValue('B2', 'Mã HV')
								  ->setCellValue('C2', 'Họ')
								  ->setCellValue('D2', 'Tên')
								  ->setCellValue('E2', 'Phái')
								  ->setCellValue('F2', 'Ngày sinh')
								  ->setCellValue('G2', 'Nơi sinh')
								  ->setCellValue('H2', 'Ngành')
								  ->setCellValue('I2', 'Số hiệu bằng')
								  ->setCellValue('J2', 'Loại CTĐT');
	$objPHPExcel->getActiveSheet()->getStyle('A2:J2')->getFont()->setBold(true);
	$objPHPExcel->getActiveSheet()->getStyle('A1')->getFont()->setBold(true);
	
	for ($i = 0; $i < $n; $i++)
	{
		$j=$i+3;
		$objPHPExcel->getActiveSheet()->setCellValue("A$j", ($j-2))
								  ->setCellValue("B$j", $resDM["MA_HOC_VIEN"][$i]." ")
								  ->setCellValue("C$j", $resDM["HO"][$i])
								  ->setCellValue("D$j", $resDM["TEN"][$i])
								  ->setCellValue("E$j", $resDM["PHAI"][$i])
								  ->setCellValue("F$j", $resDM["NGAY_SINH"][$i])
								  ->setCellValue("G$j", $resDM["NOI_SINH"][$i])
								  ->setCellValue("H$j", $resDM["TEN_NGANH"][$i])
								  ->setCellValue("I$j", $resDM["SO_HIEU_BANG"][$i])
								  ->setCellValue("J$j", $resDM["HUONGDT"][$i]);
	}
	$objPHPExcel->getActiveSheet()->getColumnDimension('A')->setWidth(6);
	$objPHPExcel->getActiveSheet()->getColumnDimension('B')->setAutoSize(true);
	$objPHPExcel->getActiveSheet()->getColumnDimension('C')->setAutoSize(true);
	$objPHPExcel->getActiveSheet()->getColumnDimension('D')->setAutoSize(true);
	$objPHPExcel->getActiveSheet()->getColumnDimension('E')->setAutoSize(true);
	$objPHPExcel->getActiveSheet()->getColumnDimension('F')->setAutoSize(true);
	$objPHPExcel->getActiveSheet()->getColumnDimension('G')->setAutoSize(true);
	$objPHPExcel->getActiveSheet()->getColumnDimension('H')->setAutoSize(true);
	$objPHPExcel->getActiveSheet()->getColumnDimension('I')->setAutoSize(true);
	$objPHPExcel->getActiveSheet()->getColumnDimension('j')->setAutoSize(true);
	
	$objPHPExcel->getActiveSheet()->setTitle($title);
	$objPHPExcel->setActiveSheetIndex(0);
	$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
	$objWriter->save($pathfile);

	echo '{"url":"khoa/'.$pathfile.'"}';
}

?>

<?php
if (isset ($db_conn))
	oci_close($db_conn);
?>