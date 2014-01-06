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
if (!allowPermisstion(base64_decode($_SESSION['uidloginPortal']), '107', $db_conn))
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
$mucdich = str_replace($searchdb,$replacedb,$_POST["txtMucDich_GCN"]);

if ($a == 'form')
{
?>
<div id="dialogGiayChungNhan">
	<div align=center style="font-size:16px; margin-bottom:10px;"><b>Giấy Chứng Nhận Học Viên Cao Học/Nghiên Cứu Sinh/Bồi Dưỡng SĐH</b></div>
	<form id=formGiayChungNhan_khoa>
	<table width="100%" border="0" align="center" cellpadding="5" cellspacing="2">
	  <tr>
		<td align="right" class="heading" style="width:80px"><label for="txtMaHV_GCN">Mã học viên</label></td>
		<td>
			<input style="width:100px" class="text ui-widget-content ui-corner-all tableData" name="txtMaHV_GCN" id="txtMaHV_GCN" type="text" maxlength="10" placeholder=""/> <span style='font-weight:bold;margin-left:10px'>Họ và tên:</span> <span id=ho_ten_GCN style='font-weight:bold;margin-left:5px'></span>
		</td>
	  </tr>
	  <tr class="heading">
		<td align="right" ><label for="txtMucDich_GCN">Lý do</label></td>
		<td>
			<input style="width:100%" class="text ui-widget-content ui-corner-all tableData" name="txtMucDich_GCN" id="txtMucDich_GCN" type="text" maxlength="500" placeholder=""/>
		</td>
	  </tr>
	  <tr>
		<td></td><td><button id="btn_printpreview_gcn" style='font-size:80%'>&nbsp;Xem bản In</button></td>
	  </tr>
	</table>
	</form>
	<div style="margin-top:5px" align="center" id="tipGCN" class="ui-corner-all validateTips"></div>
</div> <!--end dialogGiayChungNhan -->

<script type="text/javascript">
var validateMaHV_GCN = 0;
$(function(){
	$("#btn_printpreview_gcn" ).button({ icons: {primary:'ui-icon ui-icon-print'} });
	
	var jtxtMaHV_GCN = $("#txtMaHV_GCN"),
	jtxtMucDich_GCN  = $("#txtMucDich_GCN"),
	allFieldsGCN = $([]).add(jtxtMaHV_GCN).add(jtxtMucDich_GCN),
	tipGCN	= $("#tipGCN");
	function updateTips_GCN( t ) {
		tipGCN
			.text( t )
			.addClass( "ui-state-highlight" );
		setTimeout(function() {
			tipGCN.removeClass( "ui-state-highlight", 1500 );
		}, 1000 );
	}
	function GCN_checkLength( o, n, min, max) {
		if (min==0 && (o.val().length==0))
		{	
			o.addClass( "ui-state-error" );
			o.focus();
			updateTips_GCN( "Thông tin " + n + " không được phép để trống.");
			return false;
		}else if (min==max && o.val().length<min){
			o.addClass( "ui-state-error" );
			o.focus();
			updateTips_GCN("Thông tin " + n + " phải đủ " + min + " ký tự.");
		}else if ( o.val().length > max || o.val().length < min ) {
			o.addClass( "ui-state-error" );
			o.focus();
			updateTips_GCN("Chiều dài của " + n + " từ " + min + " đến " + max + " ký tự.");
			return false;
		} else {
			return true;
		}
	}
	// Check Regexp
	function GCN_checkRegexp( o, regexp, n ) {
		//alert('a');
		if ( o.val()!='' && !( regexp.test( o.val() ) ) ) {
			o.addClass( "ui-state-error" );
			o.focus();
			updateTips_GCN(n);
			return false;
		} else {
			return true;
		}
	}
	
	$("#btn_printpreview_gcn").click(function(e){
		var bValid = true;
		
		allFieldsGCN.removeClass( "ui-state-error" );
		
		bValid = bValid && GCN_checkLength( jtxtMaHV_GCN, "\"Mã học viên\"", 0, 10);
		bValid = bValid && GCN_checkLength( jtxtMucDich_GCN, "\"Lý do\"", 0, 500);
		
		if (bValid && !validateMaHV_GCN)
			gv_open_msg_box("Mã học viên không chính xác. Vui lòng nhập lại", "alert");
			
		if (bValid && validateMaHV_GCN) {					
			gv_processing_diglog("open", "Đang in ...");
			tipGCN.html("");
			dataString = $("#formGiayChungNhan_khoa").serialize()
			+ "&a=print&m="+$("#txtMaHV_GCN").val()+'&hisid=<?php echo $_REQUEST["hisid"];?>';
			$.ajax({
				type: "POST", url: "khoa/khoa_giaychungnhan.php", data: dataString,	dataType: "html",
				success: function(data) {
							gv_processing_diglog("close");
							print_llkh_writeConsole(data, 0, "Giấy chứng nhận học viên cao học - SĐH ĐHBK TP.HCM", 'style="font-family:Times New Roman,Arial,Helvetica,sans-serif;"');
						 }// end function(data)	
			}); // end .ajax
		}
		
		return false;

	});	
	
	$("#txtMaHV_GCN").change(function(e){
		$("#ho_ten_GCN").html("<img border='0' src='../images/ajax-loader.gif'/>");
		dataString = 'a=getname&hisid=<?php echo $_REQUEST["hisid"]; ?>'+'&m='+$("#txtMaHV_GCN").val();
		xreq = $.ajax({
		  type: 'POST', dataType: "json",url: 'khoa/khoa_giaychungnhan.php',data: dataString,
		  success: function(data) {
			if (data.error==0)
			{
				validateMaHV_GCN = 1;
				$("#ho_ten_GCN").html(data.hoten);
			}
			else
			{
				validateMaHV_GCN = 0;
				$("#ho_ten_GCN").html("<font color=red>Không tìm thấy học viên</font>");
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
	//$k = base64_decode($_POST["k"]);
	$sqlstr="SELECT upper(ho || ' ' || ten) ho_ten, to_char(NGAY_SINH, 'dd/mm/yyyy') NGAY_SINH, T.TEN_TINH_TP, h.Khoa, n.TEN_NGANH,
			so_qd_khoa('GCN', H.MA_HOC_VIEN) SO_QD, get_hinh_thuc_dao_tao(H.MA_HOC_VIEN) LOAI_HV
			FROM HOC_VIEN H, DM_TINH_TP T, NGANH N, BO_MON B
			WHERE MA_HOC_VIEN = '$m' AND NOI_SINH = T.MA_TINH_TP(+) AND H.MA_NGANH = N.MA_NGANH AND N.MA_BO_MON = B.MA_BO_MON
			AND B.MA_KHOA = '$makhoa'";
	//file_put_contents("logs.txt", $sqlstr);
	$stmt = oci_parse($db_conn, $sqlstr);oci_execute($stmt);$n = oci_fetch_all($stmt, $hocvien);oci_free_statement($stmt);
	$sqlstr="BEGIN insert_so_qd_khoa('GCN', '$m', '$mucdich', null, '{$hocvien["SO_QD"][0]}', null, null); END;";
	$stmt = oci_parse($db_conn, $sqlstr);
	if (!oci_execute($stmt))
		file_put_contents("logs.txt", $sqlstr);
	oci_free_statement($stmt);
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
			<div style="font-size:140%; font-weight:bold; margin-bottom: 5px;">GIẤY CHỨNG NHẬN</div>
			<div style="font-size:120%; font-weight:bold;">HIỆU TRƯỞNG TRƯỜNG ĐẠI HỌC BÁCH KHOA - ĐHQG TP.HỒ CHÍ MINH</div>
		</div>
        </td>
      </tr>
      <tr>
        <td colspan=2>
      
			<table width="100%" border="0" align="center" cellpadding="5" cellspacing="0">
			
				<tr align="left">        
					<td align=left colspan=2 style="">Chứng nhận học viên: <b><?php echo $hocvien["HO_TEN"][0];?></b></td>
				</tr>
				<tr align="left">
					<td align=left>Ngày sinh: <b><?php echo $hocvien["NGAY_SINH"][0]; ?></b></td><td>Nơi sinh: <b><?php echo $hocvien["TEN_TINH_TP"][0]; ?></b></td>
				</tr>
				
				<tr align="left">
					<td align=left>Hiện là <?php echo $hocvien["LOAI_HV"][0];?></td><td >Chuyên ngành: <b><?php echo $hocvien["TEN_NGANH"][0];?></b></td>
				</tr>
				
				<tr align="left">
					<td align=left>Mã học viên: <b><?php echo "$m";?></b></td><td >Khóa: <b><?php echo $hocvien["KHOA"][0];?></b></td>
				</tr>
				
				<tr align="left">
					<td align=left colspan=2>Giấy chứng nhận này dùng để <?php echo $mucdich;?></td>
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
}

if (isset ($db_conn))
	oci_close($db_conn);
?>