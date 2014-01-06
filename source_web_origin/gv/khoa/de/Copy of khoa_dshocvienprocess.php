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
$makhoa = str_replace("'", "''",base64_decode($_SESSION['makhoa']));
$khoa = str_replace("'", "''",$_POST['k']);

if ($_REQUEST["act"]=='dshocvien') 
{
	$sqlstr="	SELECT T.MA_HOC_VIEN, T.HO, T.TEN , 
				DECODE(T.NGAY_SINH, null, NGAY_SINH_KHONG_CHUAN, TO_CHAR(T.NGAY_SINH, 'dd/mm/yyyy')) Ngay_Sinh, 
				DECODE(T.PHAI, 'M', 'Nam ', 'Nữ') PHAI, TEN_TINH_TP NOI_SINH, N.TEN_NGANH, K.TEN_KHOA,
				DECODE(dot_cap_bang(T.MA_HOC_VIEN), NULL, '', 'X') TN,
				DECODE(ctdt_loai(T.MA_HOC_VIEN), 1, 'GDMH-KLTN', 3 , 'Nghiên cứu' , 'GDMH-LVThs' )  huongdt
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
	
	//echo $sqlstr;
	
	for ($i = 0; $i < $n; $i++)
	{
		($i % 2) ? $classAlt="alt" : $classAlt="";
		echo "<tr valign=middle class=' ".$classAlt."' style='height:20px;'>";				
		echo "<td align='center'>" .($i+1)."</td>";
		echo "<td align='left'>".$resDM["MA_HOC_VIEN"][$i]."</td>";
		echo "<td align='left'>".$resDM["HO"][$i]."</td>";
		echo "<td align='left'>".$resDM["TEN"][$i]."</td>";
		echo "<td align='left'>".$resDM["PHAI"][$i]."</td>";
		echo "<td align='center'>".$resDM["NGAY_SINH"][$i]."</td>";
		echo "<td align='left' style=''>{$resDM["NOI_SINH"][$i]}</td>";
		echo "<td align='left' style=''>{$resDM["TEN_NGANH"][$i]}</td>";
		echo "<td align='left' style=''>{$resDM["HUONGDT"][$i]}</td>";
		echo "<td align='center' style=''>{$resDM["TN"][$i]}</td>";
		echo "<td align='center' style=''></td>";
		echo "</tr>";
	} 
}
else if ($_REQUEST["act"]=='dshocvienfile') 
{
	$sqlstr="	SELECT T.MA_HOC_VIEN, T.HO, T.TEN , 
				DECODE(T.NGAY_SINH, null, NGAY_SINH_KHONG_CHUAN, TO_CHAR(T.NGAY_SINH, 'dd/mm/yyyy')) Ngay_Sinh, 
				DECODE(T.PHAI, 'M', 'Nam ', 'Nữ') PHAI, TEN_TINH_TP NOI_SINH, N.TEN_NGANH, K.TEN_KHOA,
				DECODE(dot_cap_bang(T.MA_HOC_VIEN), NULL, '', 'X') TN,
				DECODE(ctdt_loai(T.MA_HOC_VIEN), 1, 'GDMH-KLTN', 3 , 'Nghiên cứu' , 'GDMH-LVThs' )  huongdt
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
	
	$today =date("d.m.Y");
	$time = date("H.i.s");
	
	$pathfile = "download/{$usr}_{$today}_{$time}_Khoa{$makhoa}_dsHocVienK{$khoa}.txt";
	$fp = fopen($pathfile, 'w');
	fwrite($fp,  'Mã HV' . chr(9) . 'Họ' . chr(9) . 'Tên' . chr(9) . 'Phái' . chr(9) . 'Ngày Sinh' . chr(9) . 'Nơi Sinh' . chr(9) . 'Ngành' . chr(9). 'Hướng ĐT' . chr(9) . 'Đã TN' .chr(13).chr(10) ) ;
	for ($i = 0; $i < $n; $i++)
	{
		$tmp = $resDM["MA_HOC_VIEN"][$i] . chr(9) . $resDM["HO"][$i]. chr(9) . $resDM["TEN"][$i]
		.chr(9).$resDM["PHAI"][$i].chr(9).$resDM["NGAY_SINH"][$i].chr(9).$resDM["NOI_SINH"][$i]
		.chr(9).$resDM["TEN_NGANH"][$i].chr(9).$resDM["HUONGDT"][$i].chr(9).$resDM["TN"][$i].chr(13).chr(10);
		//$tmp = mb_convert_encoding($tmp, "utf-8", "utf-8"); 
		fwrite($fp, $tmp);
	}
	fclose($fp);
	
	//$enc = mb_detect_encoding($pathfile);
	//$pathfile = mb_convert_encoding($pathfile, "ASCII", $enc);
	
	//$pathfile = $pathfile;

	echo '{"url":"khoa/'.$pathfile.'"}';
}

?>

<?php
if (isset ($db_conn))
	oci_close($db_conn);
?>