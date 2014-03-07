<?php
if (isset($_REQUEST["hisid"])){
	session_id($_REQUEST["hisid"]);
	session_start();
}
if (!isset($_SESSION['uidloginPortal'])){
	die('Đã hết phiên làm việc'); 
}

include "../libs/connectnckhda.php";
include "../libs/connect.php";
include "../libs/pgslibs.php";
if (!allowPermisstion(base64_decode($_SESSION['uidloginPortal']), '051', $db_conn)) {die('Truy cập bất hợp pháp');}

$macb = str_replace("'", "''",$_REQUEST['mcb']);
$madt = str_replace("'", "''",$_REQUEST['mdt']);
$a = $_REQUEST['a'];
$key = $_REQUEST["k"];

if ($macb == ''){
	$macb = $_SESSION['macb'];
}

// Thong tin can bo phan bien de tai
$sqlstr="select cb.*, to_char(cb.NGAY_SINH,'dd-mm-yyyy') NGAY_SINH, decode(PHAI, 'M', 'Nam', 'F', 'Nữ') phai_desc, k.ten_khoa, bm.ten_bo_mon,
		v.ten_chuc_vu, bmql.ten_bo_mon ten_bo_mon_ql, qghv.ten_quoc_gia ten_nuoc_hv, hv.TEN ten_hv, cb.CHUYEN_MON_BC_BO_GDDT,
		decode(MA_HOC_HAM, 'GS','Giáo sư', 'PGS','Phó giáo sư', '') TEN_HOC_HAM, GET_THANH_VIEN(cb.ma_can_bo) HOTENCB,
		get_nam_dat_hv_cao_nhat(cb.ma_can_bo, cb.ma_hoc_vi) nam_dat_hv_cao_nhat
		from can_bo_giang_day cb, bo_mon bm, khoa k, dm_chuc_vu v, bo_mon bmql, quoc_gia qghv, dm_hoc_vi hv
		where cb.ma_bo_mon = bm.ma_bo_mon (+) and bm.ma_khoa = k.ma_khoa (+)
		and cb.fk_chuc_vu = v.ma_chuc_vu (+)
		and cb.ma_bo_mon_ql = bmql.ma_bo_mon (+)
		and cb.qg_dat_hoc_vi = qghv.ma_quoc_gia (+)
		and cb.ma_hoc_vi = hv.ma_hoc_vi (+)
		and cb.ma_can_bo='$macb'
		"; // and cb.ma_can_bo in (select FK_MA_CAN_BO from NCKH_PHAN_CONG_PHAN_BIEN where KQ_PHAN_HOI=1 and MA_THUYET_MINH_DT='$madt')
		
$stmt = oci_parse($db_conn, $sqlstr);oci_execute($stmt); oci_fetch_all($stmt, $cbgd); oci_free_statement($stmt);

// Thong tin thuyet minh
$sqlstr="SELECT tm.*,to_char(tm.CNDT_NGAY_SINH,'dd/mm/yyyy') CNDT_NGAY_SINH, to_char(tm.CNDT_NGAY_CAP,'dd/mm/yyyy') CNDT_NGAY_CAP, cdt.ten_cap, lhnc.TEN_LOAI_HINH_NC,
	to_char(tm.DCNDT_NGAY_SINH,'dd/mm/yyyy') DCNDT_NGAY_SINH, to_char(tm.DCNDT_NGAY_CAP,'dd/mm/yyyy') DCNDT_NGAY_CAP, n.USERNAME,
	to_char(tm.ngay_dang_ky, 'yyyy') nam_dang_ky
	FROM NCKH_THUYET_MINH_DE_TAI tm, CAP_DE_TAI cdt, NCKH_LOAI_HINH_NC lhnc, nhan_su n
	WHERE MA_THUYET_MINH_DT='$madt' and FK_CAP_DE_TAI = cdt.ma_cap(+) and FK_LOAI_HINH_NC = lhnc.MA_LOAI_HINH_NC(+)
	and tm.FK_MA_CAN_BO=n.FK_MA_CAN_BO(+)";
$stmt = oci_parse($db_conn_khcn, $sqlstr);oci_execute($stmt); oci_fetch_all($stmt, $tmdt); oci_free_statement($stmt);	

// Thong tin phan bien
$sqlstr="select A1_TAM_QUAN_TRONG, A2_CHAT_LUONG_NC, A3_NLNC_CSVC, A4_KINH_PHI_NX, C_KET_LUAN
		from NCKH_PB_NOI_DUNG
		where MA_THUYET_MINH_DT='$madt' and fk_ma_can_bo='$macb'";
$stmt = oci_parse($db_conn_khcn, $sqlstr);oci_execute($stmt); oci_fetch_all($stmt, $danhgiatmdt); oci_free_statement($stmt);

date_default_timezone_set('Asia/Ho_Chi_Minh');
$ngay =date("d");
$thang =date("m");
$nam =date("Y");
$z = 1;
$y = 1;

if ($a != 'print_tmdt_pdf')
{
?>
  <button id="print_tmdt_r01_btn_printpreview<?php echo $key; ?>">&nbsp;In ...</button> <button id="print_tmdt_r01_btn_printpdf<?php echo $key; ?>">&nbsp;In PDF...</button>
  <div align="center" style="margin-top:10px; font-family:Arial,Helvetica,sans-serif;" id="chitietttgv_tmdt_mau_r01<?php echo $key; ?>">
<?php
}else {
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Đại học Bách khoa Tp.HCM - Phòng Khoa học công nghệ</title>
</head>

<script src="http://www.grad.hcmut.edu.vn/js/jquery-1.8.3.min.js"></script>
<body style="font-family:Arial, Helvetica, sans-serif">
<?php 
}
?>
	<style type="text/css">
		.fontcontent {
			font-size: 13px;
			font-family: Arial, Helvetica, sans-serif;
			color: #000000;
			font-weight: normal;
			line-height: 1.5;
		}
		.bordertable {
			border-color: #000000; 
			border-width: 1px; 
			border-style: solid; 
			border-collapse:collapse;
		}
		.borderDOT  {
			border-color: #000000; 
			border-width: 1px; 
			border-style: dotted; 
			border-collapse:collapse;
		}
		P.breakhere {page-break-before: always}
	</style>
	
	<table width="100%"   cellspacing="0" cellpadding="0" class="ui-corner-all shawdow tableData fontcontent" >
      <tr>
        <td valign='top'> 
			<div align="left" style="margin-top:10px" class=fontcontent>
				&nbsp;&nbsp;&nbsp;ĐẠI HỌC QUỐC GIA TP.HCM<br/><B>TRƯỜNG ĐẠI HỌC BÁCH KHOA</B>
			</div>
        </td>
		<td valign='top'> 
			<div align="right"  style="margin-top:10px"></div>
        </td>
      </tr>
	  <tr>
        <td colspan=2 valign='top'> 
			<div align="center"  style="margin-top:40px; margin-bottom:20px">
				<font style="font-size:20px;"><b>PHIẾU NHẬN XÉT-ĐÁNH GIÁ THẨM ĐỊNH</b><br>ĐỀ TÀI KHOA HỌC VÀ CÔNG NGHỆ CẤP TRƯỜNG<br>NĂM <?php echo ($tmdt["NAM_DANG_KY"][0]+1); ?></font>
			</div>
        </td>
      </tr>
      <tr>
        <td colspan=2>
			<table class='borderDOT' style='width:100%;' class=fontcontent cellpadding="5" cellspacing="0" border=1>
				<tr class='borderDOT'>
					<td >
						<div align=left ><b>Tên đề tài (tiếng Việt):</b> <?php echo $tmdt["TEN_DE_TAI_VN"][0]; ?></div>
						<div align=left ><b>Chủ nhiệm đề tài:</b> <?php echo $tmdt["CNDT_HH_HV_HO_TEN"][0]; ?></div>
						<div align=left style=""><b>Loại hình: </b> <?php echo $tmdt["TEN_LOAI_HINH_NC"][0]; ?></div>
					</td>
				</tr>
				<tr class='borderDOT'>
					<td>
						<div align=left><b>Họ và tên người đánh giá:</b> <?php echo $cbgd["HOTENCB"][0]; ?></div>
						<div align=left>Cơ quan công tác:  <?php echo $cbgd["CO_QUAN_CONG_TAC"][0]; ?></div>
						<div align=left>Điện thoại: <?php echo $cbgd["DIEN_THOAI_CN"][0]; ?> Email: <?php echo $cbgd["EMAIL"][0]; ?></div>
						<div align=left>Số CMND: <?php echo $cbgd["SO_CMND"][0]; ?> hoặc MST: <?php echo $cbgd["MA_SO_THUE"][0]; ?></div>
						<div align=left>Số tài khoản: <?php echo $cbgd["SO_TAI_KHOAN"][0]; ?></div>
						<div align=left>Tại ngân hàng: <?php echo $cbgd["NGAN_HANG_MO_TK"][0]; ?></div>
					</td>
				</tr>
			</table>
			
			<table width="100%" style="margin-top:10px" border="0" align="center" cellpadding="5" cellspacing="0" class="fontcontent">
				<tr align="left" style="font-weight:bold">        
					<td align=left style="width:15px">A.</td><td >NHẬN XÉT</td><td rowspan=3></td>
				</tr>
				<tr align="left">        
					<td align=left valign=top colspan=2>
						<div>
							<b>A1. Tầm quan trọng của nghiên cứu: (a) Tính cấp thiết, tính mới, tính sáng tạo
							và khả năng ứng dụng của nghiên cứu; (b) Sự phù hợp với định hướng khoa học và công nghệ đã công bố hoặc đặt hàng.</b>
						</div>
						<div style="margin-top:10px">
							<?php echo $danhgiatmdt["A1_TAM_QUAN_TRONG"][0]; ?>
						</div>
					</td>
				</tr>
			</table>
			
			<p class="breakhere">
			<table width="100%" style="page-break-inside:auto; margin-top:10px" border="0" align="center" cellpadding="5" cellspacing="0" class="fontcontent">
				<tr align="left" >        
					<td align=left valign=top>
						<div class=fontcontent>
							<b>A2. Chất lượng nghiên cứu: (a) Mục tiêu, nội dung, phương pháp nghiên
							cứu phù hợp và mới để đạt được mục tiêu; (b) Đóng góp vào tri thức khoa học, có ảnh hưởng đối với xã hội; (c) Sản phẩm nghiên cứu
							phù hợp tiêu chí các loại đề tài đăng ký.</b>
						</div>
						<div style="margin-top:10px" class=fontcontent>
							<?php echo $danhgiatmdt["A2_CHAT_LUONG_NC"][0]; ?>
						</div>
						
						<div style="margin-top:15px" class=fontcontent>
							<b>A3. Năng lực nghiên cứu của chủ nhiệm và nhóm nghiên cứu; điều kiện cơ sở vật chất - kỹ thuật phục vụ nghiên cứu.</b>
						</div>
						<div style="margin-top:10px" class=fontcontent>
							<?php echo $danhgiatmdt["A3_NLNC_CSVC"][0]; ?>
						</div>
						
						<div style="margin-top:15px" class=fontcontent>
							<b>A4. Kinh phí</b>
						</div>
						<div style="margin-top:10px">
							<table class='borderDOT' style="width:100%" border=1 class=fontcontent>
								<thead>
									<tr class='borderDOT'>
										<th rowspan=2 align=center>TT</th><th rowspan=2 align=center class='borderDOT'>Nội dung đánh giá<br>(Căn cứ phụ lục giải trình các khoản chi)</th><th colspan=3 align=center>Nhận xét</th>
									</tr>
									<tr class='borderDOT'>
										<th align=center class='borderDOT'>Cao</th><th align=center class='borderDOT'>Thấp</th><th align=center>Kinh phí đề nghị</th>
									</tr>
								</thead>
								<tbody>
									<?php 	
									$sqlstr="SELECT ID, STT, ND.NOI_DUNG, decode(NHAN_XET,'1','X','') NHAN_XET_CAO, decode(NHAN_XET,'0','X','') NHAN_XET_THAP, KINH_PHI_DE_NGHI, ID_ORDER_BY 
									FROM NCKH_PB_NOI_DUNG_KINH_PHI kp, nckh_pb_dm_noi_dung nd 
									WHERE kp.ma_nd = nd.ma_nd and kp.ma_thuyet_minh_dt = '$madt' and kp.fk_ma_can_bo = '$macb'
									order by id_order_by";
									$stmt = oci_parse($db_conn_khcn, $sqlstr);
									
									oci_execute($stmt);$n = oci_fetch_all($stmt, $resDM);oci_free_statement($stmt);
									$tmp='';
									$tongkp = 0;
									for ($i = 0; $i < $n; $i++)
									{
										$tmp.="
										<tr class='borderDOT'>
											<td align=center>{$resDM["STT"][$i]}</td>
											<td align=left class='borderDOT'>{$resDM["NOI_DUNG"][$i]}</td>
											<td align=center class='borderDOT'>{$resDM["NHAN_XET_CAO"][$i]}</td>
											<td align=center class='borderDOT'>{$resDM["NHAN_XET_THAP"][$i]}</td>
											<td align=center>{$resDM["KINH_PHI_DE_NGHI"][$i]}</td>
										</tr>";
										$tongkp += $resDM["KINH_PHI_DE_NGHI"][$i];
									}
									$tmp.="
										<tr class='borderDOT'>
											<td align=center colspan=4><b>Tổng kinh phí đề nghị</b> (<em>triệu đồng</em>)</td>
											<td align=center class='borderDOT'>$tongkp</td>
										</tr>";
										
									echo $tmp;
									?>
								</tbody>
							</table>
						</div>
						<div style="margin-top:10px" class=fontcontent>
							<?php echo $danhgiatmdt["A4_KINH_PHI_NX"][0]; ?>
						</div>
						
					</td>
				</tr>
			</table>
        </td>
      </tr>
    </table>
	
	<p class="breakhere">
	<div style="width: 950px; height: 950px;/* Rotate div */
				-ms-transform:rotate(-90deg); /* IE 9 */
				-webkit-transform:rotate(-90deg); /* Chrome, Safari, Opera */
				transform:rotate(-90deg); /* Standard syntax */">
				
		<div class=fontcontent><b>B. ĐÁNH GIÁ</b></div>
		<table class='bordertable fontcontent' style="width:100%" border=1 cellpadding=5>
			<thead>
				<tr class='bordertable'>
					<th align=center>TT</th><th align=center>Nội dung đánh giá</th><th align=center>Thang điểm đánh giá</th><th align=center>Điểm đánh giá</th>
				</tr>
			</thead>
			<tbody>
				<?php 	
				$sqlstr="select a.stt, b.NOI_DUNG, a.THANG_DIEM_TRUONG, a.DIEM
				from nckh_pb_noi_dung_danh_gia a, nckh_pb_dm_noi_dung b 
				where a.ma_nd = b.ma_nd and a.MA_THUYET_MINH_DT = '$madt' and a.FK_MA_CAN_BO='$macb'
				order by a.ID_ORDER_BY";
				$stmt = oci_parse($db_conn_khcn, $sqlstr);
				
				oci_execute($stmt);$n = oci_fetch_all($stmt, $resDM);oci_free_statement($stmt);
				$tmp='';
				$tongdiem = 0;
				for ($i = 0; $i < $n; $i++)
				{
					$tmp.="
					<tr class='bordertable'>
						<td align=center>{$resDM["STT"][$i]}</td>
						<td align=left >{$resDM["NOI_DUNG"][$i]}</td>
						<td align=center >{$resDM["THANG_DIEM_TRUONG"][$i]}</td>
						<td align=center>{$resDM["DIEM"][$i]}</td>
					</tr>";
					$tongdiem += $resDM["DIEM"][$i];
				}
				$tmp.="
					<tr class='bordertable'>
						<td align=center colspan=2>Tổng cộng</td>
						<td align=center >100</td>
						<td align=center>$tongdiem/100</td>
					</tr>";
				echo $tmp;
				?>
			</tbody>
		</table>
		<table class='borderDOT fontcontent' cellpadding=5 border=1 style="margin-top: 10px">
			<thead>
				<th class='borderDOT' align=center>Xếp loại</th><th class='borderDOT' align=center>Tổng số điểm đánh giá</th>
			</thead>
			<tbody>
				<tr><td class='borderDOT' align=center>I</td><td class='borderDOT' align=center>Từ 86 điểm trở lên</td></tr>
				<tr><td class='borderDOT' align=center>II</td><td class='borderDOT' align=center>Từ 70 đến 85 điểm</td></tr>
				<tr><td class='borderDOT' align=center>II (Không đạt)</td><td class='borderDOT' align=center>Dưới 70 điểm</td></tr>
			</tbody>
		</table>
	</div>
	
	<p class="breakhere">
	<div>
		<b>C. KẾT LUẬN</b>
	</div>
	<div style="margin-top:10px" class='fontcontent' >
		<?php echo $danhgiatmdt["C_KET_LUAN"][0]; ?>
	</div>
	
	<table width=100% class=fontcontent style="margin-top: 15px">
		<tr>
			<td align=left valign=top width=50% >
				Cam kết: Người đánh giá cam kết thực hiện đánh giá khách quan, bảo mật thông tin đánh giá.
			</td>
			<td align=right width=50%>
				<div style="width:100%;" align=center>
					<span><em>TP.HCM, ngày <?php echo $ngay ?> tháng <?php echo $thang ?> năm <?php echo $nam ?></em></span><br>
					<b>Người đánh giá</b>
				</div>
			</td>
		</tr>
	</table>
	
	<script type="text/javascript">
	 
	</script>
<?php
if ($a != 'print_tmdt_pdf'){
?>	
</div>
<?php 
} else {
?>
</body>
</html>

<script type="text/javascript">
	Number.prototype.formatMoney = function(c, d, t){
	//(123456789.12345).formatMoney(2, '.', ',');
		var n = this, 
		c = isNaN(c = Math.abs(c)) ? 2 : c, 
		d = d == undefined ? "." : d, 
		t = t == undefined ? "," : t, 
		s = n < 0 ? "-" : "", 
		i = parseInt(n = Math.abs(+n || 0).toFixed(c)) + "", 
		j = (j = i.length) > 3 ? j % 3 : 0;
	   return s + (j ? i.substr(0, j) + t : "") + i.substr(j).replace(/(\d{3})(?=\d)/g, "$1" + t) + (c ? d + Math.abs(n - i).toFixed(c).slice(2) : "");
	};
	
	function reverse_escapeJsonString (str, pBr) {	
		var nstr = str.replace(/\\\\/g, "\\");
		nstr = nstr.replace(/\\\//g, '/');
		nstr = nstr.replace(/\\"/g, '"');
		nstr = nstr.replace(/\\'/g, "'");
		nstr = nstr.replace(/\\\\n/g, '\n');
		nstr = nstr.replace(/\\\\r/g, '\r');
		nstr = nstr.replace(/\\\\t/g, '\t');
		nstr = nstr.replace(/\\\\f/g, '\x08');
		nstr = nstr.replace(/\\\\b/g, '\x0c');
		if (pBr){
			nstr = nstr.replace(/\n/g, '<br>');
		}
		return nstr;
	}
</script>
<?php 
} 
?>
<script type="text/javascript">
//jQuery.ajax
//$(document).ready(function(){
$(function(){
 var matmdt<?php echo $key;?>='<?php echo $madt; ?>', urlgetdata = '';
 var macb<?php echo $key;?>='<?php echo $macb; ?>';
 
 <?php
 if ($a == 'print_tmdt_fromtab'){
 ?>
 $( "#print_tmdt_r01_btn_printpreview<?php echo $key; ?>, #print_tmdt_r01_btn_printpdf<?php echo $key; ?>" ).button({ icons: {primary:'ui-icon ui-icon-print'} });
 $( "#print_tmdt_r01_btn_printpreview<?php echo $key; ?>" ).click(function(){
	var links = '';
	print_llkh_writeConsole(links + $("#chitietttgv_tmdt_mau_r01<?php echo $key; ?>").html(), 0, 'Thuyết minh đề tài - ĐHQG Mẫu R01 - ' + matmdt<?php echo $key; ?>);
	return false;
 });
 
 $( "#print_tmdt_r01_btn_printpdf<?php echo $key; ?>" ).click(function(){
	var a = encodeURIComponent('http://www.grad.hcmut.edu.vn/gvbeta/khcn/khcn_print_tmdt_r01.php?a=print_tmdt_pdf&hisid=eremqim7im97c46dvp2aijrec1&m=20130001&k=print_tmdt_1_22');
	var content = encodeURIComponent($("#chitietttgv_tmdt_mau_r01<?php echo $key; ?>").html());
	$.download('khcn/khcn_publishpdf.php', 'url_print='+content);
	
	return false;
 });
 $("#print_tmdt_r01_btn_printpdf<?php echo $key; ?>").button("disable");
 
 //gv_processing_diglog("open","Khoa học & Công nghệ" ,"Đang xử lý ... vui lòng chờ");
 urlgetdata = "khcn/khcn_thuyetminhdtkhcn_process.php";
 
 <?php
 }else {
	echo 'urlgetdata = "http://www.grad.hcmut.edu.vn/gv/khcn/khcn_thuyetminhdtkhcn_process.php";';
 }
 ?>
 
});
</script>

<?php 
if (isset ($db_conn))
	oci_close($db_conn);
if (isset ($db_conn_khcn))
	oci_close($db_conn_khcn);
?>