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
if (!allowPermisstion(base64_decode($_SESSION['uidloginPortal']), '108', $db_conn))
{
	die('Truy cập bất hợp pháp'); 
}
$search = array('\\',"'",'"');
$replace = array('\\\\',"\'","&quot;"); 

$searchdb = array("\\\\","\\'",'\\"', "'");
$replacedb = array("\\","'", '"', "''");

$macb = $_SESSION['macb'];
$tenkhoa = $_SESSION["tenkhoa"];
$makhoa = base64_decode($_SESSION["makhoa"]);
$a = str_replace($searchdb,$replacedb,$_REQUEST['a']);
$m = str_replace($searchdb,$replacedb,$_POST["m"]);

if ($a == 'form')
{
?>
<div id="dialogGiayGioiThieu">
	<div align=center style="font-size:16px; margin-bottom:10px;"><b>Giấy Giới Thiệu</b></div>
	<form id=formGiayGioiThieu_khoa>
	<table width="100%" border="0" align="center" cellpadding="5" cellspacing="2">
	  <tr>
		<td align="right" class="heading" style="width:80px"><label for="txtMaHV_GGT">Mã học viên</label></td>
		<td>
			<input style="width:100px" class="text ui-widget-content ui-corner-all tableData" name="txtMaHV_GGT" id="txtMaHV_GGT" type="text" maxlength="10" placeholder=""/> <span style='font-weight:bold;margin-left:10px'>Họ và tên:</span> <span id=ho_ten_GGT style='font-weight:bold;margin-left:5px'></span>
		</td>
	  </tr>
	  <tr class="heading">
		<td align="right" ><label for="txtKinhGoi_GGT">Kính gửi</label></td>
		<td>
			<input style="width:100%" class="text ui-widget-content ui-corner-all tableData" name="txtKinhGoi_GGT" id="txtKinhGoi_GGT" type="text" maxlength="250" placeholder=""/>
		</td>
	  </tr>
	  <tr class="heading">
		<td align="right" ><label for="txtMucDich_GGT">Liên hệ v/v</label></td>
		<td>
			<input style="width:100%" class="text ui-widget-content ui-corner-all tableData" name="txtMucDich_GGT" id="txtMucDich_GGT" type="text" maxlength="500" placeholder=""/>
		</td>
	  </tr>
	  <tr>
		<td></td><td><button id="btn_printpreview_ggt" style='font-size:80%'>&nbsp;Xem bản In</button></td>
	  </tr>
	</table>
	</form>
	<div style="margin-top:5px" align="center" id="tipGGT" class="ui-corner-all validateTips"></div>
</div> <!--end dialogGiayGioiThieu -->

<script type="text/javascript">
var validateMaHV_GGT = 0;
$(function(){
	$("#btn_printpreview_ggt" ).button({ icons: {primary:'ui-icon ui-icon-print'} });
	
	var jtxtMaHV_GGT = $("#txtMaHV_GGT"),
	jtxtMucDich_GGT  = $("#txtMucDich_GGT"),
	jtxtKinhGoi_GGT  = $("#txtKinhGoi_GGT"),
	allFieldsGGT = $([]).add(jtxtMaHV_GGT).add(jtxtMucDich_GGT).add(jtxtKinhGoi_GGT),
	tipGGT	= $("#tipGGT");
	function updateTips_GGT( t ) {
		tipGGT
			.text( t )
			.addClass( "ui-state-highlight" );
		setTimeout(function() {
			tipGGT.removeClass( "ui-state-highlight", 1500 );
		}, 1000 );
	}
	function GGT_checkLength( o, n, min, max) {
		if (min==0 && (o.val().length==0))
		{	
			o.addClass( "ui-state-error" );
			o.focus();
			updateTips_GGT( "Thông tin " + n + " không được phép để trống.");
			return false;
		}else if (min==max && o.val().length<min){
			o.addClass( "ui-state-error" );
			o.focus();
			updateTips_GGT("Thông tin " + n + " phải đủ " + min + " ký tự.");
		}else if ( o.val().length > max || o.val().length < min ) {
			o.addClass( "ui-state-error" );
			o.focus();
			updateTips_GGT("Chiều dài của " + n + " từ " + min + " đến " + max + " ký tự.");
			return false;
		} else {
			return true;
		}
	}
	// Check Regexp
	function GGT_checkRegexp( o, regexp, n ) {
		//alert('a');
		if ( o.val()!='' && !( regexp.test( o.val() ) ) ) {
			o.addClass( "ui-state-error" );
			o.focus();
			updateTips_GGT(n);
			return false;
		} else {
			return true;
		}
	}
	
	$("#btn_printpreview_ggt").click(function(e){
		var bValid = true;
		
		allFieldsGGT.removeClass( "ui-state-error" );
		
		bValid = bValid && GGT_checkLength( jtxtMaHV_GGT, "\"Mã học viên\"", 0, 10);
		bValid = bValid && GGT_checkLength( jtxtKinhGoi_GGT, "\"Kính gởi\"", 0, 250);
		bValid = bValid && GGT_checkLength( jtxtMucDich_GGT, "\"Liên hệ\"", 0, 500);
		
		if (bValid && !validateMaHV_GGT)
			gv_open_msg_box("Mã học viên không chính xác. Vui lòng nhập lại", "alert");
			
		if (bValid && validateMaHV_GGT) {					
			gv_processing_diglog("open", "Đang in ...");
			tipGGT.html("");
			dataString = $("#formGiayGioiThieu_khoa").serialize()
			+ "&a=print&m="+$("#txtMaHV_GGT").val()+'&hisid=<?php echo $_REQUEST["hisid"];?>';
			$.ajax({
				type: "POST", url: "khoa/khoa_giaygioithieu.php", data: dataString,	dataType: "html",
				success: function(data) {
							gv_processing_diglog("close");
							print_llkh_writeConsole(data, 0, "Giấy giới thiệu - SĐH ĐHBK TP.HCM", 'style="font-family:Times New Roman,Arial,Helvetica,sans-serif;"',800,550);
						 }// end function(data)	
			}); // end .ajax
		}
		
		return false;

	});	
	
	$("#txtMaHV_GGT").change(function(e){
		$("#ho_ten_GGT").html("<img border='0' src='../images/ajax-loader.gif'/>");
		dataString = 'a=getname&hisid=<?php echo $_REQUEST["hisid"]; ?>'+'&m='+$("#txtMaHV_GGT").val();
		xreq = $.ajax({
		  type: 'POST', dataType: "json",url: 'khoa/khoa_giaygioithieu.php',data: dataString,
		  success: function(data) {
			if (data.error==0)
			{
				validateMaHV_GGT = 1;
				$("#ho_ten_GGT").html(data.hoten);
			}
			else
			{
				validateMaHV_GGT = 0;
				$("#ho_ten_GGT").html("<font color=red>Không tìm thấy học viên</font>");
			}
		  },
		  error: function(xhr, ajaxOptions, thrownError) {}
		});
	 });
	
	$('input[placeholder],textarea[placeholder]').placeholder();
});
</script>
<?php 
}
else if ($a == 'getname')
{
	$sqlstr="SELECT ho || ' ' || ten ho_ten, B.MA_KHOA
			FROM HOC_VIEN H, NGANH N, BO_MON B
			WHERE ma_hoc_vien = '$m' 
			AND H.MA_NGANH = N.MA_NGANH
			AND N.MA_BO_MON = B.MA_BO_MON
			AND B.MA_KHOA = '$makhoa'"; 
	
	//file_put_contents("logs.txt", $sqlstr);
	
	$stmt = oci_parse($db_conn, $sqlstr);oci_execute($stmt);$n = oci_fetch_all($stmt, $resDM);oci_free_statement($stmt);
	
	if ($resDM["HO_TEN"][0]!="")
		echo '{"hoten":"'.$resDM["HO_TEN"][0].'", "error":"0"}';
	else
		echo '{"hoten":"", "error":"1"}';
}
else if ($a == 'print')
{
	date_default_timezone_set('Asia/Ho_Chi_Minh');
	$ngay =date("d");
	$thang =date("m");
	$nam =date("Y");
	$date = new DateTime();
	$date->modify('+1 month');
	
	$kinhgoi = str_replace($searchdb,$replacedb,$_POST["txtKinhGoi_GGT"]);
	$mucdich = str_replace($searchdb,$replacedb,$_POST["txtMucDich_GGT"]);
	
	$sqlstr="SELECT upper(ho || ' ' || ten) ho_ten, decode(H.PHAI, 'F','chị' ,'M','anh') titlePhai, to_char(NGAY_SINH, 'dd/mm/yyyy') NGAY_SINH, T.TEN_TINH_TP, h.Khoa, n.TEN_NGANH,
			so_qd_khoa('GGT', H.MA_HOC_VIEN) SO_QD, get_hinh_thuc_dao_tao(H.MA_HOC_VIEN) LOAI_HV
			FROM HOC_VIEN H, DM_TINH_TP T, NGANH N, BO_MON B
			WHERE MA_HOC_VIEN = '$m' AND NOI_SINH = T.MA_TINH_TP(+) AND H.MA_NGANH = N.MA_NGANH AND N.MA_BO_MON = B.MA_BO_MON
			AND B.MA_KHOA = '$makhoa'";
	//file_put_contents("logs.txt", $sqlstr);
	$stmt = oci_parse($db_conn, $sqlstr);oci_execute($stmt);$n = oci_fetch_all($stmt, $hocvien);oci_free_statement($stmt);
	$sqlstr="BEGIN insert_so_qd_khoa('GGT', '$m', '$mucdich', '$kinhgoi', '{$hocvien["SO_QD"][0]}', null, null); END;";
	$stmt = oci_parse($db_conn, $sqlstr);
	if (oci_execute($stmt))
	{
?>
	<table width="100%"   cellspacing="0" cellpadding="0">
      <tr>
        <td valign='top'> 
			<div align="center" style="margin-top:10px">ĐẠI HỌC QUỐC GIA TP.HCM<br/><b>TRƯỜNG ĐẠI HỌC BÁCH KHOA</b><br/>-------------
			<br/> <span style="font-size:90%;"><em>Số: <?php echo $hocvien["SO_QD"][0];?></em></span>
			</div>
        </td>
		<td valign='top'> 
			<div align="center"  style="margin-top:10px" ><b>CỘNG HÒA XÃ HỘI CHỦ NGHĨA VIỆT NAM<br/>Độc lập - Tự do - Hạnh phúc</b><br/>-------------</div>
        </td>
      </tr>
	  <tr>
        <td colspan=2 valign='top'> 
		<div align="center"  style="margin-top:20px; margin-bottom:20px">
			<div style="font-size:140%; font-weight:bold; margin-bottom: 0px;">GIẤY GIỚI THIỆU</div>
			<div style="font-weight:bold;"><em>(Có giá trị đến <?php echo $date->format('d/m/Y'); ?>)</em></div>
		</div>
        </td>
      </tr>
      <tr>
        <td colspan=2>
      
			<table width="100%" border="0" align="center" cellpadding="5" cellspacing="0">
				<tr align="left">        
					<td align=left colspan=2 style="">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<b>Kính gởi: <?php echo $_POST["txtKinhGoi_GGT"];?></b></td>
				</tr>
				<tr align="left">        
					<td align=center colspan=2 style=""><b>HIỆU TRƯỞNG TRƯỜNG ĐẠI HỌC BÁCH KHOA - ĐHQG TP.HỒ CHÍ MINH</b></td>
				</tr>
				<tr align="left">        
					<td align=left style="">Trân trọng giới thiệu học viên <b><?php echo $hocvien["HO_TEN"][0];?></b></td>
					<td align=left>Mã học viên: <b><?php echo "$m";?></b></td>
				</tr>
				<tr align="left">
					<td align=left>Là <?php echo $hocvien["LOAI_HV"][0];?> Trường Đại Học Bách Khoa.</td>
					<td align=left>Khóa: <b><?php echo $hocvien["KHOA"][0];?></b></td>
				</tr>
				<tr align="left">
					<td align=left colspan=2>Chuyên ngành: <b><?php echo $hocvien["TEN_NGANH"][0];?></b></td>
				</tr>
				<tr align="left">
					<td align=left colspan=2>Đến liên hệ về việc: <?php echo $_POST["txtMucDich_GGT"];?></td>
				</tr>
				<tr align="left">
					<td align=left colspan=2>Kính đề nghị quý cơ quan tạo điều kiện để <?php echo "{$hocvien["TITLEPHAI"][0]} ". mb_convert_case($hocvien["HO_TEN"][0], MB_CASE_TITLE, "UTF-8");?> hoàn thành nhiệm vụ.</td>
				</tr>
				
				<tr>
					<td colspan=2 align=right>
						<table width=100%>
							<tr>
								<td align=left valign=top width=50% >
									<div style="width:200px; margin-top:20px" align=center>
										
									</div>
								</td>
								<td align=right width=50%>
									<div style="width:500px; margin-top:20px" align=center>
										<span><em>Tp.HCM, ngày <?php echo $ngay ?> tháng <?php echo $thang ?> năm <?php echo $nam ?></em></span><br/>
										<b>TL.HIỆU TRƯỞNG <br/>
										TRƯỞNG KHOA <?php echo mb_strtoupper($tenkhoa, 'UTF-8'); ?>
										</b>
									</div>
								</td>
							</tr>
						</table>
					</td>
				</tr>
			</table>
      
        </td>
      </tr>
    </table>
<?php
	}else
		file_put_contents("logs.txt", $sqlstr);
}

if (isset ($db_conn))
	oci_close($db_conn);
?>