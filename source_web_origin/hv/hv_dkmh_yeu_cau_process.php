<?php
if (isset($_REQUEST["hisid"])){
	session_id($_REQUEST["hisid"]);
	session_start();
}
if (!isset($_SESSION['uidloginhv'])){
	die('Truy cập bất hợp pháp'); 
}

include "libs/connect.php";
include "libs/pgslibshv.php";

function sendemail($emailaddress, $name, $subject, $content){
	// Tien trinh gui email
	$to  = $emailaddress; // note the comma
	//$to .= 'wez@example.com';
	
	// subject
	$subject = $subject;
	
	// message
	$message = "
	<html>
	<head>
	  <title>Phòng Đào Tạo Sau Đại Học - ĐHBK Tp.HCM: Thông tin Đăng Ký Môn Học</title>
	</head>
	<body>
		<p><strong>Đại học Bách Khoa TP.HCM <br/>Phòng Đào Tạo Sau Đại Học</strong></p>
		<p>$content</p>
		<p>Email này được gửi từ hệ thống email tự động. <br/>Xin vui lòng không trả lời email này. </p>
		<p>Trân trọng kính chào!</p>
	</body>
	</html>";
	
	// To send HTML mail, the Content-type header must be set
	$headers  = 'MIME-Version: 1.0' . "\r\n";
	$headers .= 'Content-type: text/html; charset=utf-8' . "\r\n";
	
	// Additional headers
	$headers .= "To: $name <$emailaddress>\r\n";
	$headers .= 'From: Phòng Đào Tạo Sau Đại Học <no_reply@hcmut.edu.vn>' . "\r\n";
	$headers .= 'Cc: ' . "\r\n";
	$headers .= 'Bcc: ' . "\r\n";
	
	return mail($to,'=?UTF-8?B?'.base64_encode($subject).'?=', $message, $headers);	
}


$usr = base64_decode($_SESSION["uidloginhv"]);
$pass = base64_decode($_SESSION["pidloginhv"]);
$result=allowUser($usr,$pass,$db_conn);
if ($result==0) {
	die('Truy cập bất hợp pháp');
}

date_default_timezone_set('Asia/Ho_Chi_Minh');

$type = $_POST['w'];
$khoa = $_POST['k'];
$dothoc = $_POST['d'];
$dothocf = $_POST['df'];
$hk = $_POST['h'];
$nganh = $_POST['n'];
$tennganh = $_POST['tn'];
$mahv = base64_decode($_SESSION["mahv"]);
$tenhv = $_POST['ht'];
$nganhhv = base64_decode($_SESSION["manganh"]);

$thu = array("1"=>"CN", "2"=>"Hai", "3"=>"Ba", "4"=>"Tư", "5"=>"Năm", "6"=>"Sáu", "7"=>"Bảy");

$folder = "./dkmh/logs/HK$hk/";
$pathfile = $folder.$tenhv." (". $mahv.").txt";

// Lấy tham số ngày hết hạn đkmh
$sqlstr="SELECT value , floor(sysdate - to_date(value,'dd/mm/yyyy')) het_han FROM config WHERE name='DKMH_NV_NGAY_HET_HAN'";
$stmt = oci_parse($db_conn, $sqlstr);oci_execute($stmt);$n = oci_fetch_all($stmt, $resDM);oci_free_statement($stmt);
$ngayhethanDKMH = $resDM["VALUE"][0];
$hethan = $resDM["HET_HAN"][0];

// Lấy tham số ngày bắt đầu đkmh
$sqlstr="SELECT value , (sysdate - to_date(value,'dd/mm/yyyy')) bat_dau FROM config WHERE name='DKMH_NV_NGAY_BAT_DAU'";
$stmt = oci_parse($db_conn, $sqlstr);oci_execute($stmt);$n = oci_fetch_all($stmt, $resDM);oci_free_statement($stmt);
$ngaybatdauDKMH = $resDM["VALUE"][0];
$batdau = $resDM["BAT_DAU"][0];

// Hien cac mon hoc da dang ky 
if ($type=='monhocdadk')
{
	# mon hoc
	$sqlstr = "
		SELECT DISTINCT h.ma_hoc_vien, t.thu
			, d.ma_mh, m.ten ten_mh,  t.lop, h.khoa, n.ten_nganh, m.ma_loai
			, to_char(d.dot_hoc,'DD/MM/YYYY') DOT_HOC, to_char(d.ngay_dkmh,'DD/MM/YYYY HH24:MI') ngay_dkmh
			, lpad(tiet_bat_dau,2,'0') tiet_bat_dau, lpad(tiet_ket_thuc,2,'0') tiet_ket_thuc
			, lpad(tuan_bat_dau,2,'0') tuan_bat_dau, lpad(tuan_ket_thuc,2,'0') tuan_ket_thuc
			, m.so_tiet_bt + m.so_tiet_tl + m.so_tiet_lt stlt, m.so_tiet_th stth, m.SO_TIN_CHI,
			GET_SISO_DKMH_NGUYEN_VONG(t.LOP, d.MA_MH, d.DOT_HOC) SISO
		FROM hoc_vien h, dkmh_tham_do d, tkb_mh_tham_do t, nganh n, mon_hoc m
		WHERE d.ma_hoc_vien = h.ma_hoc_vien
		AND h.ma_nganh = n.ma_nganh AND d.dot_hoc = t.dot_hoc(+) 
		AND d.ma_mh = t.ma_mh(+) AND d.lop = t.lop(+) 
		AND d.ma_mh = m.ma_mh 
		AND m.ma_loai in ('1','2','3')
		AND h.ma_hoc_vien = '$mahv' 
		AND d.dot_hoc  = '$dothoc' 
		ORDER BY ngay_dkmh desc, ten_mh, tuan_bat_dau, t.thu, tiet_bat_dau, tiet_ket_thuc
	";
	$stmt = oci_parse($db_conn, $sqlstr);oci_execute($stmt);$n = oci_fetch_all($stmt, $resDM);oci_free_statement($stmt);
		
	$content = "
	
	<div style='margin-bottom:10px; font-size:12px;' align=left><b>Danh sách các môn học bạn đã đăng ký</b></div>
	
	<table id=tableMonHocDaDKTheoNV name=tableMonHocDaDKTheoNV width='100%' border='0'  cellspacing='0' cellpadding='3' class='ui-widget ui-widget-content ui-corner-top tableData' >
	<thead>
	  <tr class='ui-widget-header heading' style='height:20pt; font-weight:bold;'>
		<td class='ui-corner-tl' align='center'>Chọn</td>
		<td  align='center'>Ngày ĐK</td>
		<td  align='center'>Mã MH</td>
		<td align=left>Môn học</td>
		<td align=left>Thứ</td>
		<td align='center'>Tiết</td>
		<td align='center'>Tuần</td>
		<td align='center'>Lớp MH</td>
		<td align='center'>Tín chỉ</td>
		<td align='center'>LT</td>
		<td align='center'>TH</td>
		<td class='ui-corner-tr' align='right'>Sỉ số</td>
	  </tr>
	  </thead>
	  <tbody>
	";
	
	$classAlt="alt";
	$mamh_t = "";
	$scriptAddTKB = "";
	$numMH=0;
	$numTongTC=0;
	
	for ($i = 0; $i < $n; $i++)
	{
		if ($mamh_t!="{$resDM["MA_MH"][$i]} {$resDM["LOP"][$i]}")
		{
			if ($batdau<0 || $hethan>0)
				$inputcheckbox="";
			else
				$inputcheckbox="<input type=checkbox id=checkMHxoa$i name=checkMHxoa$i value=".base64_encode($resDM["MA_MH"][$i])." onClick='toggleClassDKMH(\"tableMonHocDaDKTheoNV\",($i+1));'/>";
				
			($classAlt=='alt') ? $classAlt="alt_" : $classAlt="alt";
			$content .= "<tr align='left' valign='top' class=' ".$classAlt."' style='height:20px;' >";
			$content .= "<td valign=middle align='center'>$inputcheckbox</td>";
			$content .= "<td valign=middle align=center>{$resDM["NGAY_DKMH"][$i]}</td>";
			$content .= "<td valign=middle align=center>{$resDM["MA_MH"][$i]}</td>";
			$content .= "<td valign=middle align='left'><b>{$resDM["TEN_MH"][$i]}</b> <input type=hidden id=tenMHxoa$i name=tenMHxoa$i value=".base64_encode($resDM["TEN_MH"][$i])." /></td>";
			$content .= "<td valign=middle align='left'>{$thu[$resDM["THU"][$i]]}</td>";
			$content .= "<td valign=middle align='center'>{$resDM["TIET_BAT_DAU"][$i]}-{$resDM["TIET_KET_THUC"][$i]}</td>";
			$content .= "<td valign=middle align=center><b>{$resDM["TUAN_BAT_DAU"][$i]}-{$resDM["TUAN_KET_THUC"][$i]}</b></td>";
			$content .= "<td valign=middle align='center'><b>{$resDM["LOP"][$i]}</b> <input type=hidden id=lopMHxoa$i name=lopMHxoa$i value=".base64_encode($resDM["LOP"][$i])." /> </td>";
			$content .= "<td valign=middle align=center><b>{$resDM["SO_TIN_CHI"][$i]}</b></td>";
			$content .= "<td valign=middle align='center'><b>{$resDM["STLT"][$i]}</b></td>";
			$content .= "<td valign=middle align=center><b>{$resDM["STTH"][$i]}</b></td>";
			$content .= "<td valign=middle align='right'><b>{$resDM["SISO"][$i]}</b></td>";
			$content .= "</tr>";
			
			//$scriptAddTKB .= "addMhTKB({$resDM["PARA_TKB"][$i]},\"{$resDM["TEN_MH"][$i]}\");";
			$numMH++;
			$numTongTC += $resDM["SO_TIN_CHI"][$i];
		}
		else
		{
			$content .= "<tr align='left' valign='top' class=' ".$classAlt."_noline' style='height:20px;'>";
			$content .= "<td valign=middle align='right'></td>";
			$content .= "<td valign=middle align=center></td>";
			$content .= "<td valign=middle align=center></td>";
			$content .= "<td valign=middle align='left'></td>";
			$content .= "<td valign=middle align='left'>{$thu[$resDM["THU"][$i]]}</td>";
			$content .= "<td valign=middle align='center'>{$resDM["TIET_BAT_DAU"][$i]}-{$resDM["TIET_KET_THUC"][$i]}</td>";
			$content .= "<td valign=middle align=center><b>{$resDM["TUAN_BAT_DAU"][$i]}-{$resDM["TUAN_KET_THUC"][$i]}</b></td>";
			$content .= "<td valign=middle align='left'></td>"; 
			$content .= "<td valign=middle align='center'></td>";
			$content .= "<td valign=middle align='left'></td>"; 
			$content .= "<td valign=middle align='center'></td>";
			$content .= "<td valign=middle align='center'></td>";
			$content .= "</tr>";
		}
		
		$mamh_t="{$resDM["MA_MH"][$i]} {$resDM["LOP"][$i]}";
	}
	$content .= "
		<tr>
			<td valign=middle align='left' colspan=8>Tổng số tín chỉ đã đăng ký: <b>$numTongTC</b></td>
		</tr>
	  </tbody>
	</table>
	
	
	<script type='text/javascript'>
		/*
		$scriptAddTKB
		*/
		//numMHdaDK = $numMH;
		//numTongSoTCdaDK = $numTongTC;
	</script>
	";
	
	if ($n>0)
	{
		echo $content;
	}
}

// Tai nganh dang ky mon hoc
if ($type=='khoa-nganh')
{
	$sqlstr = "
				SELECT DISTINCT tkb.ma_nganh,n.ten_nganh 
				FROM tkb_mh_tham_do tkb, nganh n 
				WHERE tkb.ma_nganh=n.ma_nganh 
				AND dot_hoc = '$dothoc' and khoa = '$khoa'
				ORDER BY ten_nganh
	";
	$stmt = oci_parse($db_conn, $sqlstr);
	oci_execute($stmt);
	$n = oci_fetch_all($stmt, $resDM);
	oci_free_statement($stmt);
	for ($i = 0; $i < $n; $i++)
	{
		($resDM["MA_NGANH"][$i]==$nganhhv) ? $selected = "selected style='background-color: #075385; color:white;'" :	$selected = "";
		echo "<option value='".$resDM["MA_NGANH"][$i]."' $selected>" .$resDM["TEN_NGANH"][$i]. "</option>";
	}
}

// Tai danh sach mon hoc dang ky
if ($type=='khoa_nganh-monhoc')
{
	# mon hoc
	$sqlstr = "	
		SELECT DISTINCT tkb.ma_nganh, tkb.ma_mh, m.ten ten_mh, lop, to_char(tkb.dot_hoc,'DD/MM/YYYY') dot_hoc,  tkb.thu, 
			lpad(tkb.tiet_bat_dau, 2, '0') tiet_bat_dau, lpad(tkb.tiet_ket_thuc, 2, '0') tiet_ket_thuc, 
			lpad(tkb.tuan_bat_dau,2,'0') tuan_bat_dau, lpad(tkb.tuan_ket_thuc, 2, '0') tuan_ket_thuc, n.ten_nganh, 
			m.so_tiet_bt + m.so_tiet_tl + m.so_tiet_lt stlt, m.so_tiet_th stth, m.SO_TIN_CHI,
			GET_SISO_DKMH_NGUYEN_VONG(LOP, TKB.MA_MH, TKB.DOT_HOC) SISO
		FROM tkb_mh_tham_do tkb, nganh n, mon_hoc m
		WHERE tkb.ma_nganh=n.ma_nganh 
		AND tkb.ma_mh=m.ma_mh 
		AND tkb.dot_hoc ='$dothoc' 
		AND tkb.ma_nganh='$nganh' 
		AND tkb.khoa='$khoa'
		AND tkb.ma_mh not in (SELECT ma_mh FROM dkmh_tham_do 
								WHERE ma_hoc_vien='$mahv' AND dot_hoc = tkb.dot_hoc) 
		ORDER BY m.ten, lop, tkb.thu, tuan_bat_dau, tiet_bat_dau
	";
	$stmt = oci_parse($db_conn, $sqlstr);oci_execute($stmt);$n = oci_fetch_all($stmt, $resDM);oci_free_statement($stmt);
		
	echo "

	<div style='margin-bottom:20px; font-size:14px;' align=center>Khóa <b>$khoa</b> Đợt học <b>$dothocf</b><br/> 
				Ngành: <b>$tennganh</b>
	</div>
	
	<table id=tableDKMH name=tableDKMH width='100%' border='0'  cellspacing='0' cellpadding='3' class='ui-widget ui-widget-content ui-corner-top tableData'>
	<thead>
	  <tr class='ui-widget-header heading' style='height:20pt; font-weight:bold;'>
		<td class='ui-corner-tl' align='right'>Chọn</td>
		<td align='center'>Mã MH</td>
		<td align=left>Môn học</td>
		<td align='center'>Lớp MH</td>
		<td align='center'>Tín Chỉ</td>
		<td align='center'>LT</td>
		<td align='center'>TH</td>
		<td class='ui-corner-tr' align='right'>Sỉ số</td>
	  </tr>
	  </thead>
	  <tbody>
	";
	
	$classAlt="alt";
	
	for ($i = 0; $i < $n; $i++)
	{
		($classAlt=='alt') ? $classAlt="alt_" : $classAlt="alt";
		echo "<tr align='left' valign='top' class=' ".$classAlt."' style='height:20px;' >";
		echo "<td valign=middle align='right'><input type=checkbox id=checkMH$i name=checkMH$i value=".base64_encode($resDM["MA_MH"][$i])." onClick='toggleClassDKMH(\"tableDKMH\",($i+1)); ' /></td>";
		echo "<td valign=middle align=center>{$resDM["MA_MH"][$i]}</td>";
		echo "<td valign=middle align='left'><b>{$resDM["TEN_MH"][$i]}</b> <input type=hidden id=tenMH$i name=tenMH$i value=".base64_encode($resDM["TEN_MH"][$i])." /></td>";
		echo "<td valign=middle align='center'><b>{$resDM["LOP"][$i]}</b> <input type=hidden id=lopMH$i name=lopMH$i value=".base64_encode($resDM["LOP"][$i])." /> </td>";
		echo "<td valign=middle align='center'><b>{$resDM["SO_TIN_CHI"][$i]}</b> <input type=hidden id=tinchiMH$i name=tinchiMH$i value=".$resDM["SO_TIN_CHI"][$i]." /> </td>";
		echo "<td valign=middle align='center'><b>{$resDM["STLT"][$i]}</b></td>";
		echo "<td valign=middle align=center><b>{$resDM["STTH"][$i]}</b></td>";
		echo "<td valign=middle align='right'><b>{$resDM["SISO"][$i]}</b></td>";
		echo "</tr>";
	}
	echo "
	  </tbody>
	</table>
	";
	
	//echo "$sqlstr";
}

// Dang ky mon hoc
if ($type=='dothoc_mahv-dkmonhoc')
{
	$today =date("d.m.Y"); $time = date("H:i:s");
	// To create the nested folder, the $recursive parameter 
	// to mkdir() must be specified.
	if (!mkdir($folder, 0, true)) {
	//	die('Failed to create folders...');
	}
	
	$fp = fopen($pathfile, 'a');
	fwrite($fp,  "$time $today".chr(13).chr(10) );
	fwrite($fp,  'PHÒNG ĐÀO TẠO SĐH - ĐHBK TPHCM'.chr(13).chr(10) );
	fwrite($fp,  "Đăng ký môn học theo nguyện vọng vào lúc: $time $today".chr(13).chr(10) );
	fwrite($fp,  "HV $tenhv (mshv: $mahv) đã ĐĂNG KÝ những môn học sau:" .chr(13).chr(10) );
	fwrite($fp,  'Môn học' . chr(9) . 'Lớp' . chr(9) . 'Mã HV' . chr(9) . 'Đợt học' .chr(13).chr(10) );
	
	$emailcontent = "
					<div align=center style='margin:10px 0 10px 0'><b>Đăng ký môn học theo nguyện vọng thành công</b></div>
					Đợt học: <b>".str_replace("/",".",$dothocf)."</b><br/>
					<b>HV $tenhv (mshv: $mahv)</b> đã đăng ký môn học theo nguyện vọng vào lúc: <b>$time $today</b>, danh sách môn học đã đăng ký: <br/>
					<table>
					<tr style='font-weight:bold;'><td align=left>Môn học</td><td align=center>Lớp</td><td>Mã HV</td></tr>";
	$MhDay = '';
	$dupMH = '';
	$MhDkOK = '';
	$quaTinChi = '';
	$numMHDK = 0;
	$tongTCchophep = 20;
	
	for($i=0;$i<$_POST["tongMH"];$i++)
	{
		if (isset($_POST["checkMH$i"]) && $_POST["checkMH$i"]!="")
		{
			$mhdk = base64_decode($_POST["checkMH$i"]);
			$tenMH = base64_decode($_POST["tenMH$i"]);
			$lopMHdk = base64_decode($_POST["lopMH$i"]);
			
			$numMHDK++;
			$Str_insert = "insert into dkmh_tham_do(ma_mh, lop, ma_hoc_vien, dot_hoc, ngay_dkmh) 
			 values ('$mhdk', '$lopMHdk', '$mahv','$dothoc', to_date('$today $time', 'DD.MM.YYYY HH24:MI:SS'))";
			//echo $Str_insert."<br>";
			$oci_insert = oci_parse($db_conn,$Str_insert); oci_execute($oci_insert); oci_free_statement($oci_insert);
			
			$tmp = "$mhdk - $tenMH" . chr(9) . $lopMHdk . chr(9) . $mahv . chr(9) . $dothoc .chr(13).chr(10);
			$MhDkOK .= "<tr><td align=left>$mhdk - $tenMH</td><td align=center>$lopMHdk</td><td>$mahv</td></tr>";
			//$tmp = mb_convert_encoding($tmp, "utf-8", "utf-8"); 
			fwrite($fp, $tmp);
			
			oci_free_statement($oci_test);
		}
	}
	
	$result = $emailcontent.$MhDkOK."</table>";
	
	if ($MhDkOK != '' || $dupMH != '' || $MhDay != '' || $quaTinChi!='')
	{		
		// In danh sach mon hoc da dang ky
		
		$sqlstr = "	
			SELECT DISTINCT d.ma_mh, m.ten ten_mh, t.lop, to_char(d.ngay_dkmh,'DD/MM/YYYY HH24:MI') ngay_dkmh, m.so_tin_chi
			FROM hoc_vien h, dang_ky_mon_hoc d, thoi_khoa_bieu t, mon_hoc m 
			WHERE d.ma_hoc_vien = h.ma_hoc_vien
			AND d.dot_hoc = t.dot_hoc(+) 
			AND d.ma_mh = t.ma_mh(+) AND d.lop = t.lop(+) 
			AND d.ma_mh = m.ma_mh 
			AND h.ma_hoc_vien = '$mahv' 
			AND d.dot_hoc  = '$dothoc' 
			ORDER BY ngay_dkmh desc, ten_mh
		";
		$stmt = oci_parse($db_conn, $sqlstr);oci_execute($stmt);$n = oci_fetch_all($stmt, $resDM);oci_free_statement($stmt);
		if ($n>0)
		{
			$result.="<div style='margin-top:10px'><b>Danh sách môn học bạn đã đăng ký</b></div>
			<table>
					<tr style='font-weight:bold;'><td align=left>Môn học</td><td align=center>Lớp</td><td align=center>Tín chỉ</td><td align=center>Ngày ĐKMH</td></tr>";
			
			fwrite($fp, "\nDanh sách môn học đã đăng ký\n");
			
			for ($i = 0; $i < $n; $i++)
			{
				$result .= "<tr><td align=left>{$resDM["MA_MH"][$i]} - {$resDM["TEN_MH"][$i]}</td><td align=center>{$resDM["LOP"][$i]}</td><td align=center>{$resDM["SO_TIN_CHI"][$i]}</td><td align=center>{$resDM["NGAY_DKMH"][$i]}</td></tr>";
				$tmp = "{$resDM["MA_MH"][$i]} - {$resDM["TEN_MH"][$i]}" . chr(9) . $resDM["LOP"][$i] . chr(9) . $resDM["NGAY_DKMH"][$i] .chr(13).chr(10);
				fwrite($fp, $tmp);
			}
			$result .= "</table>";
		}
		
		/*set_time_limit(3600); 
		if ($mahv != '03207104')
		{
			$kqemail = sendemail("5$mahv@stu.hcmut.edu.vn", "$tenhv ($mahv)", "Thong bao ket qua DKMH của HV $tenhv ($mahv)" , $result);
		}
		else
		{
			$kqemail = sendemail("taint@hcmut.edu.vn", "$tenhv ($mahv)", "Thong bao ket qua DKMH của HV $tenhv ($mahv)" , $result);
			//$kqemail = sendemail("nttvi@hcmut.edu.vn", "$tenhv ($mahv)", "Thong bao ket qua DKMH của HV $tenhv ($mahv)" , $result);
		}
		
		if (!$kqemail)
			fwrite($fp,  "\nKhông thể gửi email cho HV" );
		*/
	}
	
	// 
	
	
	fwrite($fp,  chr(13).chr(10) );
	fclose($fp);
	
	echo $result;
}

// Huy dang ky mon hoc
if ($type=='dothoc_mahv-huy_dkmonhoc')
{
	date_default_timezone_set('Asia/Ho_Chi_Minh');
	$today =date("d.m.Y"); $time = date("H:i:s");
	
	// To create the nested folder, the $recursive parameter 
	// to mkdir() must be specified.
	if (!mkdir($folder, 0, true)) {
	//	die('Failed to create folders...');
	}
	//$pathfile = 'dkmh/logs/'.$mahv.'.txt';
	$fp = fopen($pathfile, 'a');
	fwrite($fp,  "$time $today" .chr(13).chr(10) );
	fwrite($fp,  'PHÒNG ĐÀO TẠO SĐH - ĐHBK TPHCM'.chr(13).chr(10) );
	fwrite($fp,  "HỦY đăng ký môn học theo nguyện vọng vào lúc: $time $today".chr(13).chr(10) );
	fwrite($fp,  "HV $tenhv (mshv: $mahv) đã HỦY các môn sau:" .chr(13).chr(10) );
	fwrite($fp,  'Môn học' . chr(9) . 'Lớp' . chr(9) . 'Mã HV' . chr(9) . 'Đợt học' .chr(13).chr(10) );
	
	$emailcontent = "
					<div align=center style='margin:10px 0 10px 0'><b>HỦY đăng ký môn học thành công</b></div>
					Đợt học: <b>".str_replace("/",".",$dothocf)."</b><br/>
					HV $tenhv (mshv: $mahv) đã <b>HỦY</b> đăng ký môn học theo nguyện vọng vào lúc: <b>$time $today</b>, danh sách môn học đã hủy:<br/>
					<table>
					<tr style='font-weight:bold;'><td align=left>Môn học</td><td align=center>Lớp</td><td>Mã HV</td></tr>";
	$MhDkOK = '';
	$numMHDK = 0;
	$maMHxoa = '';
	$tmp='';
	$scriptRemoveTKB = '';
	for($i=0;$i<$_POST["tongMH"];$i++)
	{
		if (isset($_POST["checkMHxoa$i"]) && $_POST["checkMHxoa$i"]!="")
		{
			$mhdk = base64_decode($_POST["checkMHxoa$i"]);
			$tenMH = base64_decode($_POST["tenMHxoa$i"]);
			$lopMHdk = base64_decode($_POST["lopMHxoa$i"]);
			$maMHxoa .= "'$mhdk',";
			$tmp .= "$mhdk - $tenMH" . chr(9) . $lopMHdk . chr(9) . $mahv . chr(9) . $dothoc .chr(13).chr(10);
			$MhDkOK .= "<tr><td align=left>$mhdk - $tenMH</td><td align=center>$lopMHdk</td><td>$mahv</td></tr>";
		}
	}
	$maMHxoa=substr($maMHxoa,0,-1);
	
	$sqlstr = "DELETE dkmh_tham_do 
			 WHERE ma_hoc_vien='$mahv' AND dot_hoc='$dothoc' AND ma_mh in ($maMHxoa)";
	$oci_delete = oci_parse($db_conn,$sqlstr); oci_execute($oci_delete);

	//echo $sqlstr;
	
	if ($MhDkOK != '')
	{
		$MhDkOK.="</table>";
		$result = $emailcontent.$MhDkOK;
		
		/*
		set_time_limit(3600); 
		if ($mahv != '03207104')
		{
			$kqemail = sendemail("5$mahv@stu.hcmut.edu.vn", "$tenhv ($mahv)", "Thong bao HUY ket qua DKMH của HV $tenhv ($mahv)" , $result);
		}
		else
		{
			$kqemail = sendemail("taint@hcmut.edu.vn", "$tenhv ($mahv)", "Thong bao HUY ket qua DKMH của HV $tenhv ($mahv)" , $result);
		}
		*/
		
		fwrite($fp, $tmp);
		//if (!$kqemail)
		//	fwrite($fp,  "\nKhông thể gửi email cho HV" );
			
		fwrite($fp,  chr(13).chr(10) );
		fclose($fp);
	}
	
	$result.= "
	<script type='text/javascript'>
		$scriptRemoveTKB
	</script>
	";
	
	echo $result;
}

if (isset ($db_conn))
	oci_close($db_conn);
?>