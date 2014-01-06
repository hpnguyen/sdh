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
$dothoc = $_POST['d'];
$hk = $_POST['h'];
$type = $_POST['t'];
$makhoa = base64_decode($_SESSION['makhoa']);
$thu = array("1"=>"CN", "2"=>"Hai", "3"=>"Ba", "4"=>"Tư", "5"=>"Năm", "6"=>"Sáu", "7"=>"Bảy");
$usr = base64_decode($_SESSION['uidloginPortal']);

$sqlstr="
			SELECT 	distinct t.dot_hoc, t.khoa, k.ten_khoa, t.ma_mh, mh.ten, t.lop, t.thu, to_char(l.ngay_thi,'dd/mm/yyyy') ngay_thi,
					to_char(l.ngay_thi, 'D') thu_thi, l.phong_thi, l.gio_thi, ' ' ten_tat,
					t.tuan_bat_dau, t.tuan_ket_thuc,
					cb.ma_hoc_ham || ' ' || decode(ma_hoc_vi,'TSK','TSKH.','TH', 'ThS.',
					ma_hoc_vi) HHHV, cb.ma_can_bo, l.don_gia, l.sl_cb_coi_thi,
					ho_ten CBGD,
						(select count(distinct d.ma_hoc_vien)
						from dang_ky_mon_hoc d
						where d.dot_hoc = t.dot_hoc
						and d.ma_mh = t.ma_mh
						and d.lop = t.lop
						and d.dot_hoc =  '$dothoc'
						and t.dot_hoc = '$dothoc') sl_hv, b.ten_bo_mon, k.ten_khoa, l.ma_mh||
					l.lop mh_lop
			FROM thoi_khoa_bieu t, can_bo_giang_day cb, mon_hoc mh , bo_mon b, khoa k, lich_thi l
			WHERE t.ma_can_bo = cb.ma_can_bo
			AND t.ma_mh = mh.ma_mh
			AND t.dot_hoc =  '$dothoc'
			AND mh.ma_bo_mon = b.ma_bo_mon (+)
			AND b.ma_khoa = k.ma_khoa
			AND b.ma_khoa = '$makhoa'
			AND l.dot_hoc = t.dot_hoc
			AND l.ma_mh = t.ma_mh
			AND l.lop = t.lop
			AND t.tuan_ket_thuc || t.thu =	(	select max(t1.tuan_ket_thuc || t1.thu)
												from thoi_khoa_bieu t1
												where t1.ma_mh = t.ma_mh
												and t1.dot_hoc = t.dot_hoc
												and t1.lop = t.lop
												and t1.khoa = t.khoa
											)
			AND t.lop_tinh is null
			ORDER BY  k.ten_khoa, ngay_thi, gio_thi, ma_mh, lop
	"; 

if ($type == 'dothoc-lichthihk')
{
	
	$stmt = oci_parse($db_conn, $sqlstr);
	oci_execute($stmt);
	$n = oci_fetch_all($stmt, $resDM);
	oci_free_statement($stmt);

	echo "
		<div align='center'><h2>Lịch Thi Học Kỳ $hk</h2></div>
		<div style='margin-bottom:20px;'>
			<div style='margin:0 0 10px 5px; ' align=left><strong>Khoa: {$resDM["TEN_KHOA"][0]}</strong></div>
			<table id='tableLichThiHocKy' name='tableLichThiHocKy' width='100%' border='0' cellpadding=3 cellspacing='0' class='ui-widget ui-widget-content ui-corner-top tableData' >
			<thead>
			  <tr class='ui-widget-header heading' style='height:20pt;font-weight:bold'>
				<td class='ui-corner-tl' align=center>STT</td>
				<td>Mã MH</td>
				<td align=left style='width:300px'>Môn thi</td>
				<td align=center>Lớp</td>
				<td align=center>Ngày thi</td>
				<td align=center>Thứ</td>
				<td align=center>Giờ thi</td>
				<td align=center>Phòng thi</td>
				<td align=center>SL HV</td>
				<td align=left>CBGD</td>
				<td align=right>Đơn giá</td>
				<td align=center>SL CB<br/>coi thi</td>
				<td align=right class='ui-corner-tr'>Thành tiền</td>
			  </tr>
			  </thead>
			  <tbody>
	";

	for ($i = 0; $i < $n; $i++)
	{
		($i % 2) ? $classAlt="alt" : $classAlt="alt_";
		echo "<tr align='left' valign='top' class=' ".$classAlt."' style='height:22px;'>";				
		echo "<td  align='center'>" .($i+1)."</td>";
		echo "<td>".$resDM["MA_MH"][$i]."</td>";
		echo "<td align=left>".$resDM["TEN"][$i]."</td>";
		echo "<td align=center>".$resDM["LOP"][$i]."</td>";
		echo "<td align=center>".$resDM["NGAY_THI"][$i]."</td>";
		echo "<td align=center>".$thu[$resDM["THU_THI"][$i]]."</td>";
		echo "<td align=center>".$resDM["GIO_THI"][$i]."</td>";
		echo "<td align=center>".$resDM["PHONG_THI"][$i]."</td>";
		echo "<td align=center>".$resDM["SL_HV"][$i]."</td>";
		echo "<td align=left>".$resDM["CBGD"][$i]."</td>";
		echo "<td align=right>".number_format($resDM["DON_GIA"][$i])."</td>";
		echo "<td align=center>".$resDM["SL_CB_COI_THI"][$i]."</td>";
		echo "<td align=right>".number_format($resDM["DON_GIA"][$i]*$resDM["SL_CB_COI_THI"][$i])."</td>";
		
		echo "</tr>";
	} 

	echo "
			  </tbody>
			</table>
		</div>
	";

	echo "
		<div align='center' style='font-size: 0.8em;'>
			<div style='margin-right:0px; margin-top:20px; float:right;'>
				<div align='center' style='margin-bottom:5px;'><strong>Tối</strong></div>
				<div style='margin-right:10px; float:right;'>
					Tiết 14: 18:15 - 19:00<br/>
					Tiết 15: 19:05 - 19:50<br/>
					Tiết 16: 20:00 - 20:45
				</div>
			</div>
			<div style='margin-right:10px; margin-top:20px; float:right;  border-right: 1px solid #96c716;'>
				<div align='center' style='margin-bottom:5px;'><strong>Chiều</strong></div>
				<div style='margin-right:20px; float:left;'>
					Tiết 7: 12:30 - 13:15<br/>
					Tiết 8: 13:20 - 14:05<br/>
					Tiết 9: 14:15 - 15:00
				</div>
				<div style='margin-right:10px; float:right;'>
					Tiết 10: 15:05 - 15:50<br/>
					Tiết 11: 16:00 - 16:45<br/>
					Tiết 12: 16:50 - 17:35
				</div>
			</div>
			<div style='margin-right:10px; margin-top:20px; float:right;  border-right: 1px solid #96c716;'>
				<div align='center' style='margin-bottom:5px;'><strong>Sáng</strong></div>
				<div style='margin-right:20px; float:left ;'>
					Tiết 1: 06:30 - 07:15<br/>
					Tiết 2: 07:20 - 08:05<br/>
					Tiết 3: 08:15 - 09:00
				</div>
				<div style='margin-right:10px; float:right;'>
					Tiết 4: 09:05 - 09:50<br/>
					Tiết 5: 10:00 - 10:45<br/>
					Tiết 6: 10:50 - 11:35
				</div>
			</div>
		</div>
	";
}
else if ($type == 'dothoc-lichthihkfile')
{
	$stmt = oci_parse($db_conn, $sqlstr);oci_execute($stmt);$n = oci_fetch_all($stmt, $resDM);oci_free_statement($stmt);
	date_default_timezone_set('Asia/Ho_Chi_Minh');
	$today =date("d.m.Y");
	$time = date("H.i.s");
		
	$pathfile = "download/{$usr}_{$today}_{$time}_LichThiHocKy_".str_replace("/","_",$hk)."_KHOA_".$makhoa.".xlsx";
	
	/** Include PHPExcel */
	require_once '../../phpexcel/Classes/PHPExcel.php';
	$objPHPExcel = new PHPExcel();
	// Set document properties
	$objPHPExcel->getProperties()->setCreator("$usr")
								 ->setLastModifiedBy("$usr")
								 ->setTitle("LỊCH THI HỌC KỲ - HK $hk")
								 ->setSubject("LỊCH THI HỌC KỲ - HK $hk")
								 ->setDescription("")
								 ->setKeywords("")
								 ->setCategory("Lịch thi học kỳ");
	// Set default font
	$objPHPExcel->getDefaultStyle()->getFont()->setName('Arial')
											  ->setSize(10);
	$objPHPExcel->getActiveSheet()->setCellValue('A1', "Lịch thi học kỳ $hk");
	$objPHPExcel->getActiveSheet()->setCellValue('A2', 'STT')
								  ->setCellValue('B2', 'Mã MH')
								  ->setCellValue('C2', 'Môn thi')
								  ->setCellValue('D2', 'Lớp')
								  ->setCellValue('E2', 'Ngày thi')
								  ->setCellValue('F2', 'Thứ')
								  ->setCellValue('G2', 'Giờ thi')
								  ->setCellValue('H2', 'Phòng thi')
								  ->setCellValue('I2', 'SL HV')
								  ->setCellValue('J2', 'CBGD')
								  ->setCellValue('K2', 'Đơn giá')
								  ->setCellValue('L2', 'SL CB Coi thi')
								  ->setCellValue('M2', 'Thành tiền');
	$objPHPExcel->getActiveSheet()->getStyle('A2:M2')->getFont()->setBold(true);
	$objPHPExcel->getActiveSheet()->getStyle('A1')->getFont()->setBold(true);
	
	for ($i = 0; $i < $n; $i++)
	{
		$j=$i+3;
		$objPHPExcel->getActiveSheet()->setCellValue("A$j", ($j-2))
								  ->setCellValue("B$j", $resDM["MA_MH"][$i])
								  ->setCellValue("C$j", $resDM["TEN"][$i])
								  ->setCellValue("D$j", $resDM["LOP"][$i])
								  ->setCellValue("E$j", $resDM["NGAY_THI"][$i])
								  ->setCellValue("F$j", $resDM["THU_THI"][$i])
								  ->setCellValue("G$j", $resDM["GIO_THI"][$i])
								  ->setCellValue("H$j", $resDM["PHONG_THI"][$i])
								  ->setCellValue("I$j", $resDM["SL_HV"][$i])
								  ->setCellValue("J$j", $resDM["CBGD"][$i])
								  ->setCellValue("K$j", $resDM["DON_GIA"][$i])
								  ->setCellValue("L$j", $resDM["SL_CB_COI_THI"][$i])
								  ->setCellValue("M$j", ($resDM["DON_GIA"][$i]*$resDM["SL_CB_COI_THI"][$i]));
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
	
	$objPHPExcel->getActiveSheet()->setTitle('Lịch thi học kỳ');
	$objPHPExcel->setActiveSheetIndex(0);
	$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
	$objWriter->save($pathfile);

	echo '{"url":"khoa/'.$pathfile.'"}';
}
if (isset ($db_conn))
	oci_close($db_conn);
?>