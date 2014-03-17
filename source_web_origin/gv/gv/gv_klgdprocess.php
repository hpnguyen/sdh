<?php
if (isset($_REQUEST["hisid"])){
	session_id($_REQUEST["hisid"]);
	session_start();
}
if (!isset($_SESSION['uidloginPortal'])){
	die('Truy cập bất hợp pháp'); 
}

include "../libs/connect.php";
include "../libs/pgslibsemail.php";
include "../libs/pgslibs.php";
?>
<?php
$search = array('\\',"'",'"');
$replace = array('\\\\',"\'","&quot;"); 

$searchdb = array("\\\\","\\'",'\\"', "'" );
$replacedb = array("\\","'", '"', "''");

$macb = $_SESSION['macb'];
$dothoc = $_POST['txtKhoaKLGD'];
$hk = $_POST['h'];
$w = $_POST['w'];

if ($w=="getKLGD")
{
	$sqlstr="select v.*, get_ten_tat_tkb(v.ma_can_bo, v.dot_hoc, v.ma_mh, v.lop) ten_lop
			from view_klgd v
			where v.ma_can_bo = '".$macb."'
			and v.cbgd not like '%TH - TN Bộ môn%'
			and v.dot_hoc = '".$dothoc."'"; 
	$stmt = oci_parse($db_conn, $sqlstr);oci_execute($stmt);$n = oci_fetch_all($stmt, $resDM);oci_free_statement($stmt);

	echo "
		<div align='center'><h2>Khối Lượng Giảng Dạy Sau Đại Học<br/>Học kỳ $hk</h2></div>
		
		<div style='margin-bottom:10px; margin-left:0px; font-size:80%' align=left> 
			Đối với thực hành: 20% [Tiết qui đổi] dành cho [THB] và 80% [Tiết qui đổi] dành cho [TH]<br/>
			Đối với tiểu luận: (số tiết tiểu luận / 15) * [(01->30 học viên)*1.5 + (31->60 học viên)*1 + (>60 học viên)*0.2]<br/>
			Giải thích: [HSLĐ] = lớp đông; [HSHH] = học hàm/học vị; [HSMG] = mời giảng; [HSBS] = bổ sung;
		</div>
		
		<div style='margin-bottom:20px;'>
			<table id='tableKLGD' name='tableKLGD' width='100%' border='0'  cellspacing='0' class='ui-widget ui-widget-content ui-corner-top tableData ' height='20'>
			<thead>
			  <tr class='ui-widget-header heading' style='height:20pt;font-weight:bold;'>
				<td align='center' class='ui-corner-tl'>Loại</td>
				<td style='width:400px;'>Môn Học</td>
				<td>Lớp</td>
				<td align='center'>Ngoài giờ</td>
				<td align='center' title='Số Học Viên'>Số HV</td>
				<td align='center' title='Số Tiết'>Tiết</td>
				<td align='center' title='Hệ Số Lớp Đông'>HSLĐ</td>
				<td align='center' title='Hệ Số Học Hàm - Học Vị'>HSHH</td>
				<td align='center' title='Hệ Số Mời Giảng'>HSMG</td>
				<td align='center' title='Hệ Số Bổ Sung'>HSBS</td>
				<td align='right' class='ui-corner-tr'>Tiết QĐ&nbsp;</td>
			  </tr>
			  </thead>
			  <tbody>
	";

	$tongTietQD=0;
	for ($i = 0; $i < $n; $i++)
	{
		$loai = $resDM["LOAI"][$i];
		if ($loai == 'TS' || $loai == 'LV')
		{
			$style = "cursor:pointer;";
			$javascript = "onclick='$(this).next(\"tr.showhide\").slideToggle();'";
		}
		($i % 2) ? $classAlt="alt" : $classAlt="alt_";
		echo "<tr align='left' valign='middle' class='fontcontent ".$classAlt."' style='height:25px; $style' $javascript >";
		echo "<td align='center' >" .$resDM['LOAI'][$i]."</td>";
		echo "<td>{$resDM["TEN_MH"][$i]}</td>";
		echo "<td>".$resDM["TEN_LOP"][$i]."</td>";
		echo "<td align='center'>".$resDM["NGOAI_GIO"][$i]."</td>";
		echo "<td align='center'>".$resDM["SO_HV"][$i]."</td>";
		echo "<td align='center'>".$resDM["SO_TIET"][$i]."</td>";
		echo "<td align='center'>".$resDM["HSLD"][$i]."</td>";
		echo "<td align='center'>".$resDM["HS_HH_HV"][$i]."</td>";
		echo "<td align='center'>".$resDM["HS_MOI_GIANG"][$i]."</td>";
		echo "<td align='center'>".$resDM["HS_BO_SUNG"][$i]."</td>";
		echo "<td align='right'>".$resDM["TIET_QD"][$i]."&nbsp;</td>";
		echo "</tr>";
		
		if ($loai == 'LV' || $loai == 'TS')
		{
			if ($loai == 'LV') {
				$sqlstr="select v.*, h.ho || ' ' || h.ten ho_ten, decode(h.PHAI, 'F','Nữ' , 'M', 'Nam') phaidesc, to_char(h.NGAY_SINH,'dd-mm-yyyy') NGAY_SINH,
				TEN_DE_TAI detai, SO_TIET_QD, decode(ctdt_loai(v.ma_hoc_vien), 1,'KL', 2,'GD', 3,'NC') ctdt
				from CHI_TIET_KLGD_LUAN_VAN v, HOC_VIEN h, LUAN_VAN_THAC_SY l
				where v.ma_can_bo = '$macb'	and v.dot_hoc = '$dothoc' and v.ma_hoc_vien = h.ma_hoc_vien
				and l.DOT_TINH_KLGD = '$dothoc' and l.ma_hoc_vien = h.ma_hoc_vien
				order by h.ho, h.ten";
			}
			else if ($loai == 'TS') {
				
				$sqlstr="select v.*, h.ho || ' ' || h.ten ho_ten, decode(h.PHAI, 'F','Nữ' , 'M', 'Nam') phaidesc, to_char(h.NGAY_SINH,'dd-mm-yyyy') NGAY_SINH,
				l.TEN_LUAN_AN detai, SO_TIET_QD, '' ctdt
				from CHI_TIET_KLGD_HD_NCS v, HOC_VIEN h, LUAN_AN_TIEN_SY l
				where v.ma_can_bo = '".$macb."'	and v.dot_hoc = '".$dothoc."' and v.ma_hoc_vien = h.ma_hoc_vien
				and l.ma_hoc_vien = h.ma_hoc_vien
				order by h.ho, h.ten"; 
			}
				
			
			$stmt = oci_parse($db_conn, $sqlstr);oci_execute($stmt);$k = oci_fetch_all($stmt, $resNCS);oci_free_statement($stmt);
			
			echo "
			<tr class='showhide ".$classAlt."' style='display:none'>
			<td colspan='11' >
				<div style='margin-left:10px;' align=right>
					<table cellpadding=3 style='with:100%'>
						<tr class='ui-widget-header heading' style='font-color: #aaa'><td>Mã</td> <td >Họ tên</td> <td>Phái</td> <td>Ngày sinh</td> <td style=''>Đề tài/Luận án</td><td align=center>CTĐT</td><td align=right >Tiết QĐ</td></tr>
			";
			for ($j = 0; $j < $k; $j++)
			{
				echo " <tr><td>{$resNCS["MA_HOC_VIEN"][$j]}</td> <td>{$resNCS["HO_TEN"][$j]}</td> <td>{$resNCS["PHAIDESC"][$j]}</td> <td>{$resNCS["NGAY_SINH"][$j]}</td> <td>{$resNCS["DETAI"][$j]}</td><td align=center>{$resNCS["CTDT"][$j]}</td><td align=right>{$resNCS["SO_TIET_QD"][$j]}</td></tr>";
			}
			echo "	</table>
				</div>
			</td>
			</tr>";
		}
		
		$tongTietQD+=$resDM["TIET_QD"][$i];
	}
		($classAlt=="") ? $classAlt="alt" : $classAlt="alt_";
		echo "<tr class='fontcontent ".$classAlt."'>";				
		echo "<td align='center'></td>";
		echo "<td align='center'></td>";
		echo "<td align='center'></td>";
		echo "<td align='center'></td>";
		echo "<td align='center'></td>";
		echo "<td align='center'></td>";
		echo "<td colspan='4' align='right'>&nbsp;<strong>Tổng số tiết qui đổi:</strong></td>";
		echo "<td align='right'><strong>".$tongTietQD."</strong>&nbsp;</td>";
		echo "</tr>";

	echo "
			  </tbody>
			</table>
		</div>
	";

	// Khoi tao phan hoi y kien	
	if ($n>0)
	{
		$sqlstr="insert into PHAN_HOI_KLGD(DOT_HOC, MA_CAN_BO, DONG_Y) select '$dothoc', '$macb', null from dual where 0 = (select count(*) from PHAN_HOI_KLGD where DOT_HOC = '$dothoc' and MA_CAN_BO = '$macb')";
		$stmt = oci_parse($db_conn, $sqlstr);oci_execute($stmt);oci_free_statement($stmt);
	}
	
	// Kiem tra thoi han phan hoi
	$hethanphanhoi = 1;
	$sqlstr="select 1 from dual 
			where upper(to_char(sysdate, 'yyyymmdd')) <= (select upper(to_char(to_date(value, 'dd-mon-yyyy'),'yyyymmdd')) from config where name='HAN_PHAN_HOI_KLGD')
			AND to_date('$dothoc', 'dd-mon-yy') = (select value from config where name='DOT_HOC_DKMH')"; 
	$stmt = oci_parse($db_conn, $sqlstr);oci_execute($stmt);$n = oci_fetch_all($stmt, $resDM);oci_free_statement($stmt);
	if ($n>0)
		$hethanphanhoi = 0;
	//	file_put_contents("logs.txt", "----------------------------------------------\n
	//				". date("H:i:s d.m.Y")." hethanphanhoi = $hethanphanhoi $sqlstr \n
	//				----------------------------------------------\n", FILE_APPEND);

	// Lay y kien phan hoi
	$sqlstr="select * from phan_hoi_klgd  where ma_can_bo = '".$macb."' and dot_hoc = '".$dothoc."'"; 
	$stmt = oci_parse($db_conn, $sqlstr);oci_execute($stmt);$n = oci_fetch_all($stmt, $resDM);oci_free_statement($stmt);
	if ($n>0)
	{
		$dongy = str_replace($search,$replace,$resDM["DONG_Y"][0]);
		//$phanhoi = str_replace($search,$replace,$resDM["PHAN_HOI"][0]);
		//$traloiphanhoi = str_replace($search,$replace,$resDM["TRA_LOI_PHAN_HOI"][0]);
		//$traloiphanhoi = str_replace(array("\r\n", "\r", "\n"), "<br>", $traloiphanhoi); 
		$traloiphanhoi = escapeWEB($resDM["TRA_LOI_PHAN_HOI"][0]);
		//$phanhoi = str_replace(array("\r\n", "\r", "\n"), "<br>", $phanhoi); 
		$phanhoi = escapeWEB($resDM["PHAN_HOI"][0]); 
		
		echo "
		<script type='text/javascript'>
			$( '#txtXacNhanKLGD' ).val('$dongy');
			$( '#divYKienPhanHoiKLGD' ).html('$phanhoi');
			klgd_updateXacNhanKLGD('$dongy');
		";
		if ($traloiphanhoi != '')
			echo "
			$( '#divTraloiYKienPhanHoiKLGD' ).html('$traloiphanhoi');
			$('#traloiykiendiv').show();
			";
		else
			echo "$('#traloiykiendiv').hide();";
		
		// Xu ly khi het han phan hoi
		if ($hethanphanhoi)
			echo "
			document.getElementById('txtXacNhanKLGD').disabled=true;
			$('#gv_klgd_btn_ykienphanhoi').hide();
			hethanykien = 1;
			";
		else
			echo "
			document.getElementById('txtXacNhanKLGD').disabled=false;
			$('#gv_klgd_btn_ykienphanhoi').show();
			hethanykien = 0;
			";
		// -------
		
		echo "
		</script>
		";
	}
}
else if ($w=="updateYKien")
{
	//$sqlstr = "select value from config where name = 'HAN_PHAN_HOI'";
	//$stmt = oci_parse($db_conn, $sqlstr);oci_execute($stmt);$n = oci_fetch_all($stmt, $resDM);oci_free_statement($stmt);
	//$hanphanhoi = $resDM["VALUE"][0];
	
	$dongy = str_replace($searchdb, $replacedb,$_POST['txtXacNhanKLGD']);
	$ykien = str_replace($searchdb, $replacedb,$_POST['txtYKienPhanHoiKLGD']);
	$sqlstr = "UPDATE phan_hoi_klgd SET dong_y = '$dongy', phan_hoi = '$ykien', ngay_phan_hoi = sysdate
	WHERE MA_CAN_BO = '$macb' AND DOT_HOC = '$dothoc'
	AND upper(to_char(sysdate, 'yyyymmdd')) <= (select upper(to_char(to_date(value, 'dd-mon-yyyy'),'yyyymmdd')) from config where name='HAN_PHAN_HOI_KLGD')
	AND to_date('$dothoc', 'dd-mon-yy') = (select value from config where name='DOT_HOC_DKMH')";
	
	//file_put_contents("logs.txt", "----------------------------------------------\n
	//				". date("H:i:s d.m.Y")." $sqlstr \n
	//				----------------------------------------------\n", FILE_APPEND);
	
	$stmt = oci_parse($db_conn, $sqlstr);
	if (oci_execute($stmt))
	{
		echo "Cập nhật ý kiến phản hồi thành công";
		if ($ykien!=''){
			//sendemail("lhttung@hcmut.edu.vn", "Le Huu Thanh Tung" , "Y kiến KLGD của $macb đợt $dothoc", "Y kiến KLGD của $macb đợt $dothoc:<p>$ykien</p>");
			//sendemail("taint@hcmut.edu.vn", "Ngo Trung Tai" , "Y kiến KLGD của $macb đợt $dothoc", "Y kiến KLGD của $macb đợt $dothoc:<p>$ykien</p>");
		}
	}
	else
		echo "Không thể cập nhật ý kiến phản hồi";
	oci_free_statement($stmt);
}

if (isset ($db_conn))
	oci_close($db_conn);
?>