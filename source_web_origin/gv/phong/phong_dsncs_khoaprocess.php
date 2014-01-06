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
if (!allowPermisstion(base64_decode($_SESSION['uidloginPortal']), '020', $db_conn))
{
	die('Truy cập bất hợp pháp'); 
}

$usr = base64_decode($_SESSION['uidloginPortal']);
$makhoa = str_replace("'", "''",$_POST['khoa']);
$khoa = str_replace("'", "''",$_POST['k']);

if ($_REQUEST["a"]=='khoa-khoa') 
{
	$sqlstr="SELECT DISTINCT b.ma_khoa, k.ten_khoa
			FROM hoc_vien h, nganh n , bo_mon b, khoa k
			WHERE h.ma_bac = 'TS' 
			AND h.ma_nganh = n.ma_nganh
			AND n.ma_bo_mon = b.ma_bo_mon (+)
			AND b.ma_khoa = k.ma_khoa
			AND h.khoa = $khoa
			ORDER BY ten_khoa"; 
	$stmt = oci_parse($db_conn, $sqlstr);
	oci_execute($stmt);
	$n = oci_fetch_all($stmt, $resDM);
	oci_free_statement($stmt);
	
	for ($i = 0; $i < $n; $i++)
	{
		echo "<option value='".$resDM["MA_KHOA"][$i]."'>" .$resDM["TEN_KHOA"][$i]. "</option>";
	}
}
else if ($_REQUEST["a"]=='dsncs') 
{
	$sqlstr="	SELECT T.MA_HOC_VIEN, upper(T.HO) ho, upper(T.TEN) ten , 
				DECODE(T.NGAY_SINH, null, NGAY_SINH_KHONG_CHUAN, TO_CHAR(T.NGAY_SINH, 'dd/mm/yyyy')) Ngay_Sinh, 
				DECODE(T.PHAI, 'M', 'Nam ', 'Nữ') PHAI, TEN_TINH_TP NOI_SINH, N.TEN_NGANH, K.TEN_KHOA,
				DECODE(t.dang_hoc, '0', 'Nghỉ', '1', 'Đang học', '2' , 'Đã TN', '3', 'Chuyển trường', '4' , 'Bỏ học') TRANG_THAI
				FROM HOC_VIEN T, NGANH N, dm_tinh_tp p, BO_MON M, KHOA K 
				WHERE T.KHOA = $khoa 
				AND K.MA_KHOA = $makhoa
				AND T.MA_NGANH = N.MA_NGANH and upper(T.MA_BAC) = 'TS'
				AND N.MA_BO_MON = M.MA_BO_MON AND M.MA_KHOA = K.MA_KHOA 
				AND t.noi_sinh = p.ma_tinh_tp (+) 
				ORDER BY K.TEN_KHOA DESC, N.TEN_NGANH ASC, T.ten_eng, t.ho_eng"; 
	$stmt = oci_parse($db_conn, $sqlstr);
	oci_execute($stmt);
	$n = oci_fetch_all($stmt, $resDM);
	oci_free_statement($stmt);
	
	//echo $sqlstr;
	echo "
		<div align='center'><h2>Danh Sách Nghiên Cứu Sinh Khóa $khoa<br/>Khoa {$resDM["TEN_KHOA"][0]}</h2></div>
		<div style='margin-bottom:20px;'>
			<table id='phong_tableDSHocVienKhoa' name='phong_tableDSHocVienKhoa' width='100%' border='0'  cellspacing='0' class='ui-widget ui-widget-content ui-corner-top tableData' >
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
				<td align='center' class='ui-corner-tr'>Trạng thái</td>
			  </tr>
			  </thead>
			  <tbody>
	";
	for ($i = 0; $i < $n; $i++)
	{
		($i % 2) ? $classAlt="alt" : $classAlt="alt_";
		echo "<tr align='left' valign='middle' class=' ".$classAlt."' style='height:20px'>";				
		echo "<td align='center'>" .($i+1)."</td>";
		echo "<td align='left'>".$resDM["MA_HOC_VIEN"][$i]."</td>";
		echo "<td align='left'>".$resDM["HO"][$i]."</td>";
		echo "<td align='left'>".$resDM["TEN"][$i]."</td>";
		echo "<td align='left'>".$resDM["PHAI"][$i]."</td>";
		echo "<td align='center'>".$resDM["NGAY_SINH"][$i]."</td>";
		echo "<td align='left' style=''>{$resDM["NOI_SINH"][$i]}</td>";
		echo "<td align='left' style=''>{$resDM["TEN_NGANH"][$i]}</td>";
		echo "<td align='center' style=''>{$resDM["TRANG_THAI"][$i]}</td>";
		echo "</tr>";
	} 
	echo "
				</tbody>
			</table>
		</div>
	";
	
}
else if ($_REQUEST["a"]=='dsncsfile') 
{
	$sqlstr="	SELECT T.MA_HOC_VIEN, upper(T.HO) ho, upper(T.TEN) ten , 
				DECODE(T.NGAY_SINH, null, NGAY_SINH_KHONG_CHUAN, TO_CHAR(T.NGAY_SINH, 'dd/mm/yyyy')) Ngay_Sinh, 
				DECODE(T.PHAI, 'M', 'Nam ', 'Nữ') PHAI, TEN_TINH_TP NOI_SINH, N.TEN_NGANH, K.TEN_KHOA,
				DECODE(t.dang_hoc, '0', 'Nghỉ', '1', 'Đang học', '2' , 'Đã TN', '3', 'Chuyển trường', '4' , 'Bỏ học') TRANG_THAI,
				GET_DIEM_CD_1(T.MA_HOC_VIEN) diem_cd_1, GET_DIEM_CD_2(T.MA_HOC_VIEN) diem_cd_2, GET_DIEM_CD_3(T.MA_HOC_VIEN) diem_cd_3,
				GET_DIEM_CD_TQ(T.MA_HOC_VIEN) diem_cd_tq, decode(GET_KQ_BV_NN(T.MA_HOC_VIEN), '1','Đạt', '') kq_bv_nn_truong, decode(GET_KQ_BV_CS(T.MA_HOC_VIEN), '1','Đạt', '') kq_bv_cs_khoa,
				tong_tin_chi_tich_luy(T.MA_HOC_VIEN) tong_tich_luy
				FROM HOC_VIEN T, NGANH N, dm_tinh_tp p, BO_MON M, KHOA K 
				WHERE T.KHOA = $khoa 
				AND K.MA_KHOA = $makhoa
				AND T.MA_NGANH = N.MA_NGANH and upper(T.MA_BAC) = 'TS'
				AND N.MA_BO_MON = M.MA_BO_MON AND M.MA_KHOA = K.MA_KHOA 
				AND t.noi_sinh = p.ma_tinh_tp (+) 
				ORDER BY K.TEN_KHOA DESC, N.TEN_NGANH ASC, T.ten_eng, t.ho_eng";
		
	$stmt = oci_parse($db_conn, $sqlstr);oci_execute($stmt);$n = oci_fetch_all($stmt, $resDM);oci_free_statement($stmt);
	
	date_default_timezone_set('Asia/Ho_Chi_Minh');
	$today =date("d.m.Y");
	$time = date("H.i.s");
	
	$pathfile = "download/{$usr}_{$today}_{$time}_Khoa{$makhoa}_dsNCS_K{$khoa}.xlsx";
	
	/** Include PHPExcel */
	require_once '../../phpexcel/Classes/PHPExcel.php';
	$objPHPExcel = new PHPExcel();
	// Set document properties
	$objPHPExcel->getProperties()->setCreator("$usr")
								 ->setLastModifiedBy("$usr")
								 ->setTitle("DANH SÁCH NCS KHOA {$resDM["TEN_KHOA"][0]} - Khóa $khoa")
								 ->setSubject("DANH SÁCH NCS KHOA {$resDM["TEN_KHOA"][0]} - Khóa $khoa")
								 ->setDescription("")
								 ->setKeywords("")
								 ->setCategory("Danh sách nghiên cứu sinh");
	// Set default font
	$objPHPExcel->getDefaultStyle()->getFont()->setName('Arial')
											  ->setSize(10);
	$objPHPExcel->getActiveSheet()->setCellValue('A1', "Danh sách Nghiên Cứu Sinh - Khoa {$resDM["TEN_KHOA"][0]} - Khóa $khoa");
	$objPHPExcel->getActiveSheet()->setCellValue('A2', 'STT')
								  ->setCellValue('B2', 'Mã HV')
								  ->setCellValue('C2', 'Họ')
								  ->setCellValue('D2', 'Tên')
								  ->setCellValue('E2', 'Phái')
								  ->setCellValue('F2', 'Ngày sinh')
								  ->setCellValue('G2', 'Nơi sinh')
								  ->setCellValue('H2', 'Ngành')
								  ->setCellValue('I2', 'Tình trạng')
								  ->setCellValue('J2', 'Tổng chỉ tích lũy')
								  ->setCellValue('K2', 'Điểm CĐ 1')
								  ->setCellValue('L2', 'Điểm CĐ 2')
								  ->setCellValue('M2', 'Điểm CĐ 3')
								  ->setCellValue('N2', 'Điểm CĐ Tổng quan')
								  ->setCellValue('O2', 'KQ Bảo vệ HĐ Khoa (CS)')
								  ->setCellValue('P2', 'KQ Bảo vệ HĐ Trường (NN)');
	$objPHPExcel->getActiveSheet()->getStyle('A2:P2')->getFont()->setBold(true);
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
								  ->setCellValue("I$j", $resDM["TRANG_THAI"][$i])
								  ->setCellValue("J$j", $resDM["TONG_TICH_LUY"][$i])
								  ->setCellValue("K$j", $resDM["DIEM_CD_1"][$i])
								  ->setCellValue("L$j", $resDM["DIEM_CD_2"][$i])
								  ->setCellValue("M$j", $resDM["DIEM_CD_3"][$i])
								  ->setCellValue("N$j", $resDM["DIEM_CD_TQ"][$i])
								  ->setCellValue("O$j", $resDM["KQ_BV_CS_KHOA"][$i])
								  ->setCellValue("P$j", $resDM["KQ_BV_NN_TRUONG"][$i]);
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
	$objPHPExcel->getActiveSheet()->getColumnDimension('P')->setAutoSize(true);
	
	$objPHPExcel->getActiveSheet()->setTitle('Danh Nghiên Cứu Sinh');	
	$objPHPExcel->setActiveSheetIndex(0);	
	$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
	$objWriter->save($pathfile);

	echo '{"url":"phong/'.$pathfile.'"}';
}

?>

<?php
if (isset ($db_conn))
	oci_close($db_conn);
?>