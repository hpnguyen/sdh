<?php
if (isset($_REQUEST["hisid"])){
	session_id($_REQUEST["hisid"]);
	session_start();
}
if (!isset($_SESSION['uidloginPortal'])){
	die('Đã hết phiên làm việc'); 
}

include "../libs/connect.php";
include "../libs/pgslibs.php";
if (!allowPermisstion(base64_decode($_SESSION['uidloginPortal']), '106', $db_conn))
{
	die('Truy cập bất hợp pháp'); 
}
?>

<?php

$type = $_REQUEST['w'];
//$khoa = $_REQUEST['k'];
//$dothoc = $_REQUEST['d'];
//$hk = $_REQUEST['h'];
$manganh = base64_decode($_SESSION["manganh"]);
$mahv = base64_decode($_SESSION["mahv"]);
$khoa = base64_decode($_SESSION["khoa"]);

$thu = array("1"=>"CN", "2"=>"Hai", "3"=>"Ba", "4"=>"Tư", "5"=>"Năm", "6"=>"Sáu", "7"=>"Bảy");

if ($type=='hv-diem')
{
	// Thong tin hoc vien
	$sqlstr = "	SELECT 	h.ho || ' ' || h.ten ho_ten, h.ma_hoc_vien, h.khoa, n.ten_nganh,
						decode(ctdt_hv_nam(h.ma_hoc_vien), 0, null,
						ctdt_hv_nam(h.ma_hoc_vien)) ctdt_hv,
						(select GHI_CHU from hoc_vien_ctdt c where c.ma_hoc_vien = h.ma_hoc_vien) ghi_chu_ctdt, 
						ctdt_loai(h.ma_hoc_vien) ctdt_loai,
						no_bai_bao(h.ma_hoc_vien) no_bai_bao,
						tong_tin_chi_tich_luy_2_0(h.ma_hoc_vien) tong_tc_2n,
						so_tin_chi_bs(h.ma_hoc_vien) so_tc_bs
				FROM hoc_vien h, nganh n
				WHERE h.ma_hoc_vien = '$mahv'
				AND h.ma_nganh = n.ma_nganh
				";
	$stmt = oci_parse($db_conn, $sqlstr);oci_execute($stmt);$n = oci_fetch_all($stmt, $resDM);oci_free_statement($stmt);
	if ($n > 0 ) 
	{
		$khoa = $resDM["KHOA"][0];
		$nganh = $resDM["TEN_NGANH"][0];
		echo "<div style='margin-bottom:10px; margin-left:0px;'>";
		echo "
		<div align=center style='margin-bottom:20px;'><b>BẢNG ĐIỂM TÍCH LŨY</b></div>
		<div align=left><b>Học viên: {$resDM["HO_TEN"][0]}</b> (Mã số: <b>{$resDM["MA_HOC_VIEN"][0]}</b>) <br/>Ngành <b>$nganh</b> - Khóa <b>$khoa</b></div>";
		if ($resDM["CTDT_LOAI"][0] == 3)
		{
			if ($resDM["NO_BAI_BAO"][0] == 'x')
				$nobaibao = "<span style='color:red;font-weight:bold'> - <em>Nợ bài báo</em></span>";
				
			echo "<div align=left style='margin-top:5px;'>Học CTĐT theo phương thức nghiên cứu $nobaibao</div>";
			
		}
		elseif ($resDM["CTDT_LOAI"][0] ==1)
			echo "<div align=left style='margin-top:5px;'>Học CTĐT theo phương thức giảng dạy môn học + Khóa luận TN </div>";
		else
			echo "<div align=left style='margin-top:5px;'>Học CTĐT theo phương thức giảng dạy môn học + LVThS </div>";
		
		if ($khoa > 2008)
		{
			$ghichu='';
			if ($resDM["GHI_CHU_CTDT"][0]!='')
				$ghichu = "; Ghi chú: {$resDM["GHI_CHU_CTDT"][0]}";
			
			echo "<div align=left  style='margin-top:5px;' >Thuộc CTĐT: <b>{$resDM["CTDT_HV"][0]} năm</b>$ghichu</div>";
			if ($resDM["CTDT_HV"][0]==2)
			{
				if ($resDM["TONG_TC_2N"][0]<$resDM["SO_TC_BS"][0])
					$tongtc2n = "<b><span style='color:red;'>{$resDM["TONG_TC_2N"][0]}</span>/{$resDM["SO_TC_BS"][0]}</b>";
				else
					$tongtc2n = "<b>{$resDM["TONG_TC_2N"][0]}/{$resDM["SO_TC_BS"][0]}</b>";
				echo "<div align=left  style='margin-top:5px;' >Tổng tín chỉ khối kiến thức bổ sung: $tongtc2n</div>";
			}
		}
		
		echo "</div>";
	}
	
	// Thong tin bang diem hoc vien
	$sqlstr="	SELECT d.ma_mh,ten,diem_lan_1,diem_lan_2, mh.so_tin_chi 
				FROM diem d,mon_hoc mh 
				WHERE d.ma_mh=mh.ma_mh AND ma_hoc_vien = '$mahv'";
	$stmt = oci_parse($db_conn, $sqlstr);
	oci_execute($stmt);
	$n = oci_fetch_all($stmt, $resDM);
	oci_free_statement($stmt);
	
	if ($n>0)
	{
		($khoa<2007) ? $diem2 = "Điểm 2" : $diem2 = "";
		
		echo "
		<table width='100%' border='0'  cellspacing='0' cellpadding='3' class='ui-widget ui-widget-content ui-corner-top tableData' >
		<thead>
		  <tr class='ui-widget-header heading' style='height:20pt; font-weight:bold;'>
			<td class='ui-corner-tl' align=left>Mã môn học</td>
			<td align=left>Tên môn học</td>
			<td align=center>Số tín chỉ</td>
			<td align=right>Điểm 1</td>
			<td align=right class='ui-corner-tr'>$diem2</td>
		  </tr>
		  </thead>
		  <tbody>
		";
		$classAlt="alt";
		for ($i = 0; $i < $n; $i++)
		{
			($classAlt=='alt') ? $classAlt="alt_" : $classAlt="alt";
			
			echo "<tr align='left' valign='top' class=' ".$classAlt."' style='height:20px;'>";
			echo "<td valign=middle align=left>{$resDM["MA_MH"][$i]}</td>";
			echo "<td valign=middle align=left>{$resDM["TEN"][$i]}</td>";
			echo "<td valign=middle align=center>{$resDM["SO_TIN_CHI"][$i]}</td>";
			echo "<td valign=middle align=right>{$resDM["DIEM_LAN_1"][$i]}</td>";
			echo "<td valign=middle align=right><b>{$thu[$resDM["DIEM_LAN_2"][$i]]}</b></td>";
			echo "</tr>";
		}
		echo "
			<tr><td colspan=5 align=left><b>Tổng số môn học: $i</b></td></tr>
		  </tbody>
		</table>
		";
	}else
	{
		echo "<div style='margin-top:5px;'><b>Học viên chưa có điểm môn học</b></div>";
	}
	
	// Diem chuyen cho hc vien
	$sqlstr="	SELECT d.ma_mh,ten, dot_hoc,diem, mh.so_tin_chi 
				FROM diem_chuyen d,mon_hoc mh 
				WHERE d.ma_mh=mh.ma_mh and d.diem <=10 and ma_hoc_vien = '$mahv'";
	$stmt = oci_parse($db_conn, $sqlstr);
	oci_execute($stmt);
	$n = oci_fetch_all($stmt, $resDM);
	oci_free_statement($stmt);
	if ($n>0)
	{
		echo "
		<div style='margin: 15px 0 10px 0px;' align=left><b>Điểm chuyển</b></div>
		<table width='100%' border='0'  cellspacing='0' cellpadding='3' class='ui-widget ui-widget-content ui-corner-top tableData' >
		<thead>
		  <tr class='ui-widget-header heading' style='height:20pt; font-weight:bold;'>
			<td class='ui-corner-tl' align=left>Mã môn học</td>
			<td align=left>Tên môn học</td>
			<td align=center>Số tín chỉ</td>
			<td align=left>Đợt học</td>
			<td align=right class='ui-corner-tr'>Điểm</td>
		  </tr>
		  </thead>
		  <tbody>
		";
		$classAlt="alt";
		for ($i = 0; $i < $n; $i++)
		{
			($classAlt=='alt') ? $classAlt="alt_" : $classAlt="alt";
			echo "<tr align='left' valign='top' class=' ".$classAlt."' style='height:20px;'>";
			echo "<td valign=middle align=left>{$resDM["MA_MH"][$i]}</td>";
			echo "<td valign=middle align=left>{$resDM["TEN"][$i]}</td>";
			echo "<td valign=middle align=center>{$resDM["SO_TIN_CHI"][$i]}</td>";
			echo "<td valign=middle align=left>{$resDM["DOT_HOC"][$i]}</td>";
			echo "<td valign=middle align=right><b>{$resDM["DIEM"][$i]}</b></td>";
			echo "</tr>";
		}
		echo "
			<tr><td colspan=5 align=left><b>Tổng số môn chuyển điểm: $i</b></td></tr>
		  </tbody>
		</table>
		";
	}
	
	// Diem dat - diem mien cho hoc vien
	$sqlstr="	SELECT d.ma_mh,ten,dot_hoc,diem, mh.so_tin_chi, decode(d.diem,15,'Đạt','Miễn') GHI_CHU
				FROM diem_chuyen d,mon_hoc mh
				WHERE d.ma_mh=mh.ma_mh and d.diem > 10 AND ma_hoc_vien = '$mahv'";
	$stmt = oci_parse($db_conn, $sqlstr);
	oci_execute($stmt);
	$n = oci_fetch_all($stmt, $resDM);
	oci_free_statement($stmt);
	if ($n>0)
	{
		echo "
		<div style='margin: 15px 0 10px 0px;' align=left><b>Điểm miễn - Điểm đạt</b></div>
		<table width='100%' border='0'  cellspacing='0' cellpadding='3' class='ui-widget ui-widget-content ui-corner-top tableData' >
		<thead>
		  <tr class='ui-widget-header heading' style='height:20pt; font-weight:bold;'>
			<td class='ui-corner-tl' align=left>Mã môn học</td>
			<td align=left>Tên môn học</td>
			<td align=center>Số tín chỉ</td>
			<td align=right>Điểm</td>
			<td align=center class='ui-corner-tr'>Ghi chú</td>
		  </tr>
		  </thead>
		  <tbody>
		";
		
		$classAlt="alt";
		for ($i = 0; $i < $n; $i++)
		{
			($classAlt=='alt') ? $classAlt="alt_" : $classAlt="alt";
			echo "<tr align='left' valign='top' class=' ".$classAlt."' style='height:20px;'>";
			echo "<td valign=middle align=left>{$resDM["MA_MH"][$i]}</td>";
			echo "<td valign=middle align=left>{$resDM["TEN"][$i]}</td>";
			echo "<td valign=middle align=center>{$resDM["SO_TIN_CHI"][$i]}</td>";
			echo "<td valign=middle align=right>{$resDM["DIEM"][$i]}</td>";
			echo "<td valign=middle align=center><b>{$resDM["GHI_CHU"][$i]}</b></td>";
			echo "</tr>";
		}
		echo "
			<tr><td colspan=5 align=left><b>Tổng số môn điểm miễn - điểm đạt: $i</b></td></tr>
		  </tbody>
		</table>
		";
	}
	
	// Tong ket
	$sqlstr="SELECT tong_tin_chi_tich_luy(h.ma_hoc_vien) tong_tich_luy,
					round(tinh_dtb_sdhbk(h.ma_hoc_vien),2) tich_luy_mh,
					so_tin_chi_lv(h.ma_hoc_vien) so_tin_chi_lv,
					diem_luan_van(h.ma_hoc_vien) diem_luan_van,
					tong_toan_khoa(h.ma_hoc_vien) tong_tk,
					dot_cap_bang(h.ma_hoc_vien) dot_cap_bang,
					(select dat from xet_luan_van where ma_hoc_vien='$mahv') dat_toan_khoa,
					round(tinh_dtb_toan_khoa(h.ma_hoc_vien),2) diem_tb, 
					ctdt_loai(h.ma_hoc_vien) ctdt_loai, no_bai_bao(h.ma_hoc_vien) no_bai_bao,
					so_tin_chi_kn(h.ma_hoc_vien) tong_tc_kn, kt_tong_chi_2_nam(h.ma_hoc_vien) kt_tc_2n,
					ten_de_tai(h.ma_hoc_vien) ten_de_tai, diem_av(h.ma_hoc_vien) diem_av,
					decode(loai_mien, 'DHNN_CQ', 'ĐHNN', 'DHNN',
					'ĐHNN', 'TOEFL_INS', 'TOEFL ITP', 'IBT', 'TOEFL iBT', 'DHQT_DHQG', 'ĐH
					Quốc tế - ĐHQG',  LOAI_MIEN) loai_mien,
					n.diem_goc, to_char(n.ngay_cap, 'dd/mm/yyyy') ngay_cap, to_char(n.ngay_het_han,'dd/mm/yyyy') ngay_het_han
			FROM hoc_vien h, ngoai_ngu_mien n
			WHERE h.ma_hoc_vien = '$mahv'
			AND h.ma_hoc_vien = n.ma_hoc_vien(+)
			";
	$stmt = oci_parse($db_conn, $sqlstr);
	oci_execute($stmt);
	$n = oci_fetch_all($stmt, $resDM);
	oci_free_statement($stmt);
	
	// Thong tin tổng kết
	if ($n>0)
	{
		echo "
		<div style='width:300px;font-size:100%;float:left;'>
			<div style='margin: 20px 0 10px 0px;' align=left>
				<div style='margin-top: 5px'>Tổng chỉ tích luỹ môn học: <b>{$resDM["TONG_TICH_LUY"][0]}</b></div>
				<div style='margin-top: 5px'>Trung bình tích luỹ môn học: <b>{$resDM["TICH_LUY_MH"][0]}</b></div>
			</div>";
			if ($resDM["DIEM_LUAN_VAN"][0] > 0)
			{
				if ($resDM["CTDT_LOAI"][0] == 1)
					$loaictdt = "khóa luận";
				else
					$loaictdt = "luận văn";
					
				echo "
				<div style='margin: 20px 0 10px 0px;' align=left>
					<div style='margin-top: 5px'>Tên đề tài: <b>{$resDM["TEN_DE_TAI"][0]}</b></div>
					<div style='margin-top: 5px'>Điểm $loaictdt: <b>{$resDM["DIEM_LUAN_VAN"][0]}</b></div>
					<div style='margin-top: 5px'>Số tín chỉ $loaictdt: <b>{$resDM["SO_TIN_CHI_LV"][0]}</b></div>
				</div>";
			}
			
			($resDM["TONG_TK"][0]<$resDM["TONG_TC_KN"][0]) ? $tongtctkcolor = "color=red" : $tongtctkcolor = "";
			
			if ($resDM["DOT_CAP_BANG"][0]!='')
				$dotcapbang = "<div style='margin-top: 5px; color:blue;'>Đợt cấp bằng: <b>{$resDM["DOT_CAP_BANG"][0]}</b></div>";
			else if ($khoa>=2008 && $resDM["DIEM_LUAN_VAN"][0] >=5 )
			{
				if ($resDM["DAT_TOAN_KHOA"][0]>1)
				{
					$dotcapbang = "<div style='margin-top: 5px; color:blue; font-weight:bold;'>Đủ điều kiện cấp bằng</div>";
					
				}else
				{
					if ($resDM["DIEM_AV"][0]<5 || $resDM["TONG_TK"][0] < $resDM["TONG_TC_KN"][0] || $resDM["KT_TC_2N"][0]!=1 || $resDM["NO_BAI_BAO"][0] != '')
					{
						$dotcapbang = "<div style='margin-top: 5px; color:red; font-weight:bold;'>Chưa đủ điều kiện cấp bằng</div>";
					}
				}
			}
				
			echo "
			<div style='margin: 20px 0 10px 0px;' align=left>
				<div style='margin-top: 5px'>Điểm trung bình toàn khóa: <b>{$resDM["DIEM_TB"][0]}</b></div>
				<div style='margin-top: 5px'>Tổng tín chỉ toàn khóa: <b><font $tongtctkcolor>{$resDM["TONG_TK"][0]}</font>/{$resDM["TONG_TC_KN"][0]}</b></div>
				$dotcapbang
			</div>
		</div>";
	}
	
	// Thong tin chung chi AV va Ghi chu
	if ($khoa>=2009)
	{
		$avdaura = "
			<div style='margin-top: 5px'>Loại miễn: <b>{$resDM["LOAI_MIEN"][0]} {$resDM["DIEM_GOC"][0]}</b></div>
			<div style='margin-top: 5px'>Ngày thi: <b>{$resDM["NGAY_CAP"][0]}</b></div>
			<div style='margin-top: 5px'>Ngày hết hạn: <b><font color=red>{$resDM["NGAY_HET_HAN"][0]}</font></b></div>
		";
	}
	else
	{
		$avdaura = "<div style='margin-top: 5px'>Điểm: {$resDM["DIEM_AV"][0]}</div>";
	}	
	echo "
	<div align='center' style='float:right;'>
		<div style='margin-right:0px; margin-top:20px; '>
			<div align='left' style='margin-bottom:5px;'><strong>Ngoại ngữ đầu ra</strong></div>
			<div style='margin-right:10px;' align=left>
				$avdaura
			</div>
		</div>
		<div style='font-size: 0.9em; margin-right:0px; margin-top:20px;font-style:italic;'>
			<div align='left' style='margin-bottom:5px;'><strong>Ghi chú</strong></div>
			<div style='width:180px;margin-right:10px; float:right;' align=left>
				Điểm 12 : Miễn <br/>
				Điểm 13 : Vắng thi <br/>
				Điểm 14 : Hoãn thi <br/>
				Điểm 15 : Đạt <br/>
				Điểm 20 : Rút môn học không được bảo lưu học phí 
			</div>
		</div>
	</div>
	
	<div class=clearfloat></div>
	";
	
}
?>

<?php 
if (isset ($db_conn))
	oci_close($db_conn);
?>