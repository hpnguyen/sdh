<?php
if (isset($_REQUEST["hisid"])){
	session_id($_REQUEST["hisid"]);
	session_start();
}
if (!isset($_SESSION['uidloginPortal'])){
	die('Truy cập bất hợp pháp'); 
}

include "../libs/connect.php";
///include "utf8.inc";


$usr = base64_decode($_SESSION['uidloginPortal']);
$macb = $_SESSION['macb'];

if ($_REQUEST["act"]=='dsmonhoc') {
	$dothoc = str_replace ("'", "''",$_REQUEST['txtKhoaDSLop']);
	$sqlstr="	SELECT DISTINCT m.ten, t.ma_mh, t.lop, get_nganh_tkb(t.ma_can_bo, t.dot_hoc, t.ma_mh,t.lop) chuyen_nganh, t.dot_hoc,
						(SELECT COUNT(*) FROM dang_ky_mon_hoc DK WHERE DK.DOT_HOC = t.dot_hoc AND DK.MA_MH = t.ma_mh
						AND DK.LOP=t.lop) SL,
					TO_CHAR(t.dot_hoc,'DD/MM/YYYY') dot_hoc_f
				FROM THOI_KHOA_BIEU t, MON_HOC m, LICH_THI d
				WHERE T.MA_MH = m.MA_MH
				AND (t.dot_hoc = '$dothoc')
				AND (t.ma_can_bo='".$macb."' or t.ma_can_bo_phu ='".$macb."')
				AND d.dot_hoc(+) = t.dot_hoc
				and d.ma_mh(+) = t.ma_mh
				and d.lop(+)=t.lop
				ORDER BY t.lop, m.ten"; 
	$stmt = oci_parse($db_conn, $sqlstr);oci_execute($stmt);$n = oci_fetch_all($stmt, $resDM);oci_free_statement($stmt);
	$dot_hoc_f = $resDM["DOT_HOC_F"][0];
	
	for ($i = 0; $i < $n; $i++)
	{
		$floadDSLop = "onClick=\"loadDSLop('".$resDM["DOT_HOC"][$i]."','".$resDM["LOP"][$i]."','".$resDM["MA_MH"][$i]."')\" style='cursor:pointer'";
		($i % 2) ? $classAlt="alt" : $classAlt="alt_";
		echo "<tr align='left' valign='middle' class=' ".$classAlt."' style='height:20px' >";				
		echo "<td $floadDSLop align=center>" .($i+1)."</td>";
		echo "<td $floadDSLop align=left >".$resDM["TEN"][$i]."</td>";
		echo "<td $floadDSLop align=left >".$resDM["MA_MH"][$i]."</td>";
		echo "<td $floadDSLop align=center>".$resDM["LOP"][$i]."</td>";
		echo "<td $floadDSLop align=left >".$resDM["CHUYEN_NGANH"][$i]."</td>";
		echo "<td $floadDSLop align='center'>".$resDM["SL"][$i]."</td>";
		echo "<td align=center valign=middle style='width:50px;' > <a rel='tooltip' title='Tải về DS lớp' href=\"javascript: loadDSLopFile('".$resDM["DOT_HOC"][$i]."','".$resDM["LOP"][$i]."','".$resDM["MA_MH"][$i]."')\"> <img border='0' width='16' height='16' src='icons/save-icon.png' /> </a></td>";
		echo "</tr>"; 
	} 
	echo "
		<script type='text/javascript'>
			$('#dslopngaybatdauhk').text(' $dot_hoc_f (tuần 1)');
			$('a[rel=tooltip]').tooltip({ placement: 'top' });
			loadDSLop('".$resDM["DOT_HOC"][0]."','".$resDM["LOP"][0]."','".$resDM["MA_MH"][0]."');
		</script>
	";
}else if ($_REQUEST["act"]=='dslop') {

	$dothoc = $_REQUEST['dothoc'];
	$lop = $_REQUEST['lop']; 
	$monhoc =  $_REQUEST['monhoc'];

	$sqlstr="	SELECT dk.ma_hoc_vien, h.ho, h.ten, decode(h.phai,'F','Nữ','Nam') PHAI, khoa, nvl(email_truong,email) email
				FROM dang_ky_mon_hoc DK, hoc_vien h 
				WHERE DK.DOT_HOC = '".$dothoc."' 
				AND DK.MA_MH = '".$monhoc."'
				AND DK.LOP= '".$lop."'
				and dk.ma_hoc_vien = h.ma_hoc_vien
				order by h.ten";
	$stmt = oci_parse($db_conn, $sqlstr);
	oci_execute($stmt);
	$n = oci_fetch_all($stmt, $resDM);
	oci_free_statement($stmt);
	
	$sqlstr="select ten from mon_hoc where ma_mh = '".$monhoc."'";
	$stmt = oci_parse($db_conn, $sqlstr);
	oci_execute($stmt);
	oci_fetch_all($stmt, $resMH);
	oci_free_statement($stmt);
	
	$sqlstr="select (nam_hoc_tu || '-' || nam_hoc_den || '/HK ' || hoc_ky) nam_hoc
			from dot_hoc_nam_hoc_ky
			where dot_hoc  = '".$dothoc."'";
	$stmt = oci_parse($db_conn, $sqlstr);
	oci_execute($stmt);
	oci_fetch_all($stmt, $resKhoa);
	oci_free_statement($stmt);
?>
<div style='margin-bottom:10px;' align='left'> <strong>Danh sách lớp <?php echo $resMH['TEN'][0] ?>, Lớp <?php echo $lop;?>, Khóa <?php echo $resKhoa['NAM_HOC'][0] ?> </strong></div>
<table id='tableDSLop' name='tableDSLop' width='100%' border='0'  cellspacing='0' class='ui-widget ui-widget-content ui-corner-top tableData' >
<thead>
  <tr class='ui-widget-header heading' style='height:20pt;'>
	<td style='width:50px' class="ui-corner-tl" align='center'>STT</td>
	<td style='width:100px'>Mã Học Viên</td>
	<td style='width:150px;'>Họ</td>
	<td style='width:50px;'>Tên</td>           
	<td style='width:50px;' align='left'>Phái</td>
	<td align='left'>Khóa</td>
	<td class="ui-corner-tr" align='left'>Email</td>
  </tr>
  </thead>
  <tbody>
<?php
	//echo "<tr > <td colspan=5> ".$sqlstr." </td> </tr> ";
	for ($i = 0; $i < $n; $i++)
	{
		($i % 2) ? $classAlt="alt" : $classAlt="alt_";
		echo "<tr align='left' valign='middle' class=' ".$classAlt."' style='height:18px;'>";				
		echo "<td  align='center'>" .($i+1)."</td>";
		echo "<td>".$resDM["MA_HOC_VIEN"][$i]."</td>";
		echo "<td align='left'>".$resDM["HO"][$i]."</td>";
		echo "<td align='left'>".$resDM["TEN"][$i]."</td>";
		echo "<td  align='left'>".$resDM["PHAI"][$i]."</td>";
		echo "<td  align='left'>".$resDM["KHOA"][$i]."</td>";
		echo "<td  align='left'>".$resDM["EMAIL"][$i]."</td>";
		echo "</tr>";
	} 
?>
  </tbody>
</table>

<?php
}else if ($_REQUEST["act"]=='dslopfile') {
	
	//define('EOL',(PHP_SAPI == 'cli') ? PHP_EOL : '<br />');

	/** Include PHPExcel */
	require_once '../../phpexcel/Classes/PHPExcel.php';
	$objPHPExcel = new PHPExcel();
				  
	$dothoc = $_REQUEST['dothoc'];
	$lop = $_REQUEST['lop']; 
	$monhoc =  $_REQUEST['monhoc'];

	// Lay danh sach hoc vien
	$sqlstr="	SELECT dk.ma_hoc_vien, h.ho, h.ten, decode(h.phai,'F','Nữ','Nam') PHAI, khoa, nvl(email_truong,email) email 
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
	
	$pathfile = "download/{$usr}_{$today}_{$time}_dslopDOT_".$dothoc.'_MH_'.$monhoc.'_LOP_'.$lop.'.xlsx';
	//$pathfile = "download/{$usr}_{$today}_{$time}_dslopDOT_".$dothoc.'_MH_'.$monhoc.'_LOP_'.$lop.'.txt';
	//$fp = fopen($pathfile, 'w');
	//fwrite($fp,  'Mã HV' . chr(9) . 'Họ' . chr(9) . 'Tên' . chr(9) . 'Phái'. chr(9) . 'Khóa' . chr(9) . 'Email' .chr(13).chr(10) ) ;
	
	// Set document properties
	$objPHPExcel->getProperties()->setCreator("$usr")
								 ->setLastModifiedBy("$usr")
								 ->setTitle("DANH SACH LOP $lop - DOT $dothoc - MON HOC $monhoc")
								 ->setSubject("DANH SACH LOP $lop - DOT $dothoc - MON HOC $monhoc")
								 ->setDescription("")
								 ->setKeywords("")
								 ->setCategory("Danh sach lop");
	// Set default font
	$objPHPExcel->getDefaultStyle()->getFont()->setName('Arial')
											  ->setSize(10);
	$objPHPExcel->getActiveSheet()->setCellValue('A1', "Danh sách lớp {$resMH["TEN"][0]}, Lớp $lop, Khóa {$resKhoa["NAM_HOC"][0]}");
	$objPHPExcel->getActiveSheet()->setCellValue('A2', 'STT')
								  ->setCellValue('B2', 'Mã HV')
								  ->setCellValue('C2', 'Họ')
								  ->setCellValue('D2', 'Tên')
								  ->setCellValue('E2', 'Phái')
								  ->setCellValue('F2', 'Khóa')
								  ->setCellValue('G2', 'Email');
	$objPHPExcel->getActiveSheet()->getStyle('A2:G2')->getFont()->setBold(true);
	$objPHPExcel->getActiveSheet()->getStyle('A1')->getFont()->setBold(true);
	
	for ($i = 0; $i < $n; $i++)
	{
		//$tmp = $resDM["MA_HOC_VIEN"][$i] . chr(9) . $resDM["HO"][$i]. chr(9) . $resDM["TEN"][$i] . chr(9) .$resDM["PHAI"][$i]. chr(9) .$resDM["KHOA"][$i]. chr(9) .$resDM["EMAIL"][$i].chr(13).chr(10);
		//$tmp = mb_convert_encoding($tmp, "utf-8", "utf-8"); 
		//fwrite($fp, $tmp);
		$j=$i+3;
		$objPHPExcel->getActiveSheet()->setCellValue("A$j", ($j-2))
								  ->setCellValue("B$j", $resDM["MA_HOC_VIEN"][$i])
								  ->setCellValue("C$j", $resDM["HO"][$i])
								  ->setCellValue("D$j", $resDM["TEN"][$i])
								  ->setCellValue("E$j", $resDM["PHAI"][$i])
								  ->setCellValue("F$j", $resDM["KHOA"][$i])
								  ->setCellValue("G$j", $resDM["EMAIL"][$i]);
	}
	//fclose($fp);
	$objPHPExcel->getActiveSheet()->getColumnDimension('A')->setWidth(6);
	$objPHPExcel->getActiveSheet()->getColumnDimension('B')->setAutoSize(true);
	$objPHPExcel->getActiveSheet()->getColumnDimension('C')->setAutoSize(true);
	$objPHPExcel->getActiveSheet()->getColumnDimension('D')->setAutoSize(true);
	$objPHPExcel->getActiveSheet()->getColumnDimension('E')->setAutoSize(true);
	$objPHPExcel->getActiveSheet()->getColumnDimension('F')->setAutoSize(true);
	$objPHPExcel->getActiveSheet()->getColumnDimension('G')->setAutoSize(true);
	
	$objPHPExcel->getActiveSheet()->setTitle('Danh sách lớp');
	$objPHPExcel->setActiveSheetIndex(0);
	$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
	$objWriter->save($pathfile);

	echo '{"url":"gv/'.$pathfile.'"}';
}

?>

<?php
if (isset ($db_conn))
	oci_close($db_conn);
?>