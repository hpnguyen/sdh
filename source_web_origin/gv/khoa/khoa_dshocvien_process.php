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
if (!allowPermisstion(base64_decode($_SESSION['uidloginPortal']), '102', $db_conn))
{
	die('Truy cập bất hợp pháp'); 
}


$usr = base64_decode($_SESSION['uidloginPortal']);
$makhoa = str_replace("'", "''",base64_decode($_SESSION['makhoa']));
$khoa = str_replace("'", "''",$_POST['k']);

if ($_REQUEST["act"]=='dshocvien') 
{
	$sqlstr="	SELECT T.MA_HOC_VIEN, T.HO, T.TEN , 
				DECODE(T.NGAY_SINH, null, NGAY_SINH_KHONG_CHUAN, TO_CHAR(T.NGAY_SINH, 'dd/mm/yyyy')) Ngay_Sinh, 
				DECODE(T.PHAI, 'M', 'Nam ', 'Nữ') PHAI, TEN_TINH_TP NOI_SINH, N.TEN_NGANH, K.TEN_KHOA,
				DECODE(dot_cap_bang(T.MA_HOC_VIEN), NULL, '', 'X') TN,
				DECODE(ctdt_loai(T.MA_HOC_VIEN), 1, 'GDMH-KLTN', 3 , 'Nghiên cứu' , 'GDMH-LVThs' )  huongdt, T.DOT 
				FROM HOC_VIEN T, NGANH N, dm_tinh_tp p, BO_MON M, KHOA K 
				WHERE T.KHOA = $khoa 
				AND K.MA_KHOA = $makhoa
				AND T.MA_NGANH = N.MA_NGANH and T.MA_BAC = 'TH' AND T.FK_HINH_THUC_DAO_TAO = 'CQ' 
				AND N.MA_BO_MON = M.MA_BO_MON AND M.MA_KHOA = K.MA_KHOA 
				AND t.noi_sinh = p.ma_tinh_tp (+) 
				ORDER BY K.TEN_KHOA DESC, T.DOT DESC, N.TEN_NGANH ASC, T.ten_eng, t.ho_eng"; 
	$stmt = oci_parse($db_conn, $sqlstr);
	oci_execute($stmt);
	$n = oci_fetch_all($stmt, $resDM);
	oci_free_statement($stmt);
	
	//echo $sqlstr;
	
	echo "
	<div align='center'><h2>Danh Sách Học Viên Cao Học<br/>Khóa $khoa</h2></div>
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
			<td  align='center'>Đợt</td>
            <td align='left'>Ngành</td>
			<td align=left>Loại CTĐT</td>
			<td align='center' class='ui-corner-tr'>Đã TN</td>
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
		echo "<td align='center' style=''>{$resDM["DOT"][$i]}</td>";
		echo "<td align='left' style=''>{$resDM["TEN_NGANH"][$i]}</td>";
		echo "<td align='left' style=''>{$resDM["HUONGDT"][$i]}</td>";
		echo "<td align='center' style=''>{$resDM["TN"][$i]}</td>";
		echo "</tr>";
	} 
	
	echo "
	 </tbody>
        </table>
	</div>";
}
else if ($_REQUEST["act"]=='dshocvienfile') 
{
	$sqlstr="	SELECT T.MA_HOC_VIEN, T.HO, T.TEN , 
				DECODE(T.NGAY_SINH, null, NGAY_SINH_KHONG_CHUAN, TO_CHAR(T.NGAY_SINH, 'dd/mm/yyyy')) Ngay_Sinh, 
				DECODE(T.PHAI, 'M', 'Nam ', 'Nữ') PHAI, TEN_TINH_TP NOI_SINH, N.TEN_NGANH, K.TEN_KHOA,
				DECODE(dot_cap_bang(T.MA_HOC_VIEN), NULL, '', 'X') TN,
				DECODE(ctdt_loai(T.MA_HOC_VIEN), 1, 'GDMH-KLTN', 3 , 'Nghiên cứu' , 'GDMH-LVThs' )  huongdt,
				tong_tin_chi_tich_luy(t.ma_hoc_vien) tong_tich_luy,
				round(tinh_dtb_sdhbk(t.ma_hoc_vien),2) tb_tich_luy_mh,
				round(tinh_dtb_toan_khoa(t.ma_hoc_vien),2) diem_tb_toan_khoa,
				tong_toan_khoa(t.ma_hoc_vien) tong_chi_toab_khoa,
				dot_cap_bang(t.ma_hoc_vien) dot_cap_bang,
				ctdt_hv_nam(t.ma_hoc_vien) ctdt_hv
				FROM HOC_VIEN T, NGANH N, dm_tinh_tp p, BO_MON M, KHOA K 
				WHERE T.KHOA = $khoa 
				AND K.MA_KHOA = $makhoa
				AND T.MA_NGANH = N.MA_NGANH and T.MA_BAC = 'TH' AND T.FK_HINH_THUC_DAO_TAO = 'CQ' 
				AND N.MA_BO_MON = M.MA_BO_MON AND M.MA_KHOA = K.MA_KHOA 
				AND t.noi_sinh = p.ma_tinh_tp (+) 
				ORDER BY K.TEN_KHOA DESC, N.TEN_NGANH ASC, T.ten_eng, t.ho_eng";
				
	$stmt = oci_parse($db_conn, $sqlstr);
	oci_execute($stmt);
	$n = oci_fetch_all($stmt, $resDM);
	oci_free_statement($stmt);
	
	date_default_timezone_set('Asia/Ho_Chi_Minh');
	$today =date("d.m.Y");
	$time = date("H.i.s");
	
	//$pathfile = "download/{$usr}_{$today}_{$time}_Khoa{$makhoa}_dsHocVienK{$khoa}.txt";
	//$fp = fopen($pathfile, 'w');
	//fwrite($fp,  'Mã HV' . chr(9) . 'Họ' . chr(9) . 'Tên' . chr(9) . 'Phái' . chr(9) . 'Ngày Sinh' . chr(9) . 'Nơi Sinh' . chr(9) . 'Ngành' . chr(9). 'Loại CTĐT' . chr(9) . 'Đã TN'. chr(9) . 'Tổng chỉ tích lũy MH' . chr(9) . 'Trung bình tích lũy MH' . chr(9) . 'TB Toàn khóa' . chr(9) . 'Tổng chỉ toàn khóa' . chr(9) . 'Đợt cấp bằng' .chr(9). 'Thuộc CTĐT' .chr(13).chr(10) ) ;
	
	$pathfile = "download/{$usr}_{$today}_{$time}_Khoa{$makhoa}_dsHocVienK{$khoa}.xlsx";
	/** Include PHPExcel */
	require_once '../../phpexcel/Classes/PHPExcel.php';
	$objPHPExcel = new PHPExcel();
	// Set document properties
	$objPHPExcel->getProperties()->setCreator("$usr")
								 ->setLastModifiedBy("$usr")
								 ->setTitle("DANH SÁCH HỌC VIÊN KHOA {$resDM["TEN_KHOA"][0]} - KHÓA $khoa")
								 ->setSubject("DANH SÁCH HỌC VIÊN KHOA {$resDM["TEN_KHOA"][0]} - KHÓA $khoa")
								 ->setDescription("")
								 ->setKeywords("")
								 ->setCategory("Danh sách học viên");
	// Set default font
	$objPHPExcel->getDefaultStyle()->getFont()->setName('Arial')
											  ->setSize(10);
	$objPHPExcel->getActiveSheet()->setCellValue('A1', "Danh sách học viên Khoa {$resDM["TEN_KHOA"][0]} - Khóa $khoa");
	$objPHPExcel->getActiveSheet()->setCellValue('A2', 'STT')
								  ->setCellValue('B2', 'Mã HV')
								  ->setCellValue('C2', 'Họ')
								  ->setCellValue('D2', 'Tên')
								  ->setCellValue('E2', 'Phái')
								  ->setCellValue('F2', 'Ngày sinh')
								  ->setCellValue('G2', 'Nơi sinh')
								  ->setCellValue('H2', 'Ngành')
								  ->setCellValue('I2', 'Đã TN')
								  ->setCellValue('J2', 'Tổng chỉ tích lũy MH')
								  ->setCellValue('K2', 'Trung bình tích lũy MH')
								  ->setCellValue('L2', 'TB Toàn khóa')
								  ->setCellValue('M2', 'Tổng chỉ toàn khóa')
								  ->setCellValue('N2', 'Đợt cấp bằng')
								  ->setCellValue('O2', 'Thuộc CTĐT (Năm)');
	$objPHPExcel->getActiveSheet()->getStyle('A2:O2')->getFont()->setBold(true);
	$objPHPExcel->getActiveSheet()->getStyle('A1')->getFont()->setBold(true);
	
	for ($i = 0; $i < $n; $i++)
	{
		$j=$i+3;
		$objPHPExcel->getActiveSheet()->setCellValue("A$j", ($j-2))
								  ->setCellValue("B$j", $resDM["MA_HOC_VIEN"][$i])
								  ->setCellValue("C$j", $resDM["HO"][$i])
								  ->setCellValue("D$j", $resDM["TEN"][$i])
								  ->setCellValue("E$j", $resDM["PHAI"][$i])
								  ->setCellValue("F$j", $resDM["NGAY_SINH"][$i])
								  ->setCellValue("G$j", $resDM["NOI_SINH"][$i])
								  ->setCellValue("H$j", $resDM["TEN_NGANH"][$i])
								  ->setCellValue("I$j", $resDM["TN"][$i])
								  ->setCellValue("J$j", $resDM["TONG_TICH_LUY"][$i])
								  ->setCellValue("K$j", $resDM["TB_TICH_LUY_MH"][$i])
								  ->setCellValue("L$j", $resDM["DIEM_TB_TOAN_KHOA"][$i])
								  ->setCellValue("M$j", $resDM["TONG_CHI_TOAB_KHOA"][$i])
								  ->setCellValue("N$j", $resDM["DOT_CAP_BANG"][$i])
								  ->setCellValue("O$j", $resDM["CTDT_HV"][$i]);
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
	$objPHPExcel->getActiveSheet()->getColumnDimension('J')->setAutoSize(true);
	$objPHPExcel->getActiveSheet()->getColumnDimension('K')->setAutoSize(true);
	$objPHPExcel->getActiveSheet()->getColumnDimension('L')->setAutoSize(true);
	$objPHPExcel->getActiveSheet()->getColumnDimension('M')->setAutoSize(true);
	$objPHPExcel->getActiveSheet()->getColumnDimension('N')->setAutoSize(true);
	$objPHPExcel->getActiveSheet()->getColumnDimension('O')->setAutoSize(true);
	
	$objPHPExcel->getActiveSheet()->setTitle('Danh học viên');
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