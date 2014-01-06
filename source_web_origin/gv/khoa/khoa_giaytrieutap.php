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
$m = str_replace($searchdb,$replacedb,$_POST["txtMaHV_GTT"]);

if ($a == 'form')
{
?>
<div id="dialogGiayGioiThieu">
	<div align=center style="font-size:16px; margin-bottom:10px;"><b>Giấy Triệu Tập</b></div>
	<form id=formGiayTrieuTap_khoa>
	<table width="100%" border="0" align="center" cellpadding="5" cellspacing="2">
	  <tr>
		<td align="right" class="heading" style="width:80px"><label for="txtMaHV_GTT">Mã học viên</label></td>
		<td>
			<input style="width:100px" class="text ui-widget-content ui-corner-all tableData" name="txtMaHV_GTT" id="txtMaHV_GTT" type="text" maxlength="10" placeholder=""/> <span style='font-weight:bold;margin-left:10px'>Họ và tên:</span> <span id=ho_ten_GTT style='font-weight:bold;margin-left:5px'></span>
		</td>
	  </tr>
	  <tr class="heading">
		<td align="right" ><label for="txtKinhGoi_GTT">Kính gửi</label></td>
		<td>
			<input style="width:100%" class="text ui-widget-content ui-corner-all tableData" name="txtKinhGoi_GTT" id="txtKinhGoi_GTT" type="text" maxlength="250" placeholder=""/>
		</td>
	  </tr>
	  <tr class="heading">
		<td align="right" ><label for="txtDotHoc_GTT">Học kỳ</label></td>
		<td>
			<select name="txtDotHoc_GTT" id="txtDotHoc_GTT" style="font-size:15px;" class="ui-widget-content ui-corner-all tableData">
				<option value=""></option>
				   <?php $sqlstr="select (hoc_ky || '/' || nam_hoc_tu || '-' || nam_hoc_den) nam_hoc, dot_hoc
								from dot_hoc_nam_hoc_ky where rownum <7
								order by dot_hoc desc"; 
					$stmt = oci_parse($db_conn, $sqlstr);
					oci_execute($stmt);
					$n = oci_fetch_all($stmt, $resDM);
					oci_free_statement($stmt);
					
					for ($i = 0; $i < $n; $i++)
					{
						echo "<option value='".$resDM["DOT_HOC"][$i]."'>" .$resDM["NAM_HOC"][$i]. "</option>";
					}
					
				  ?>
			</select>
		</td>
	  </tr>
	  <tr >
		<td align="right" class="heading"><label for="txtFrom_GTT">Thời gian từ</label></td>
		<td>
			<input style="width:90px; text-align:center;" class="text ui-widget-content ui-corner-all tableData" name="txtFrom_GTT" id="txtFrom_GTT" type="text" maxlength="10" placeholder=""/>
			&nbsp;<b>đến</b>&nbsp;
			<input style="width:90px; text-align:center;" class="text ui-widget-content ui-corner-all tableData" name="txtTo_GTT" id="txtTo_GTT" type="text" maxlength="10" placeholder=""/> <span style="color: red">(dd/mm/yyyy)</span> 
		</td>
	  </tr>
	  <tr>
		<td></td><td><button id="btn_printpreview_GTT" style='font-size:80%'>&nbsp;Xem bản In</button></td>
	  </tr>
	</table>
	</form>
	<div style="margin-top:5px" align="center" id="tipGTT" class="ui-corner-all validateTips"></div>
</div> <!--end dialogGiayGioiThieu -->

<script type="text/javascript">
var validateMaHV_GTT = 0;
$(function(){
	$("#btn_printpreview_GTT" ).button({ icons: {primary:'ui-icon ui-icon-print'} });
	$("#txtFrom_GTT").mask("99/99/9999");
	$("#txtFrom_GTT").datepicker({
		showButtonPanel: true,
		dateFormat: "dd/mm/yy",
		onClose: function( selectedDate ) {
			$( "#txtTo_GTT" ).datepicker( "option", "minDate", selectedDate );
		}
	});
	$("#txtTo_GTT").mask("99/99/9999");
	$("#txtTo_GTT").datepicker({
		showButtonPanel: true,
		dateFormat: "dd/mm/yy",
		onClose: function( selectedDate ) {
			$( "#txtFrom_GTT" ).datepicker( "option", "maxDate", selectedDate );
		}
	});

	var jtxtMaHV_GTT = $("#txtMaHV_GTT"),
	jtxtKinhGoi_GTT  = $("#txtKinhGoi_GTT"),
	jtxtFrom_GTT = $( "#txtFrom_GTT" ),
	jtxtTo_GTT = $( "#txtTo_GTT" ),
	jtxtDotHoc_GTT = $( "#txtDotHoc_GTT" ),
	allFieldsGTT = $([]).add(jtxtMaHV_GTT).add(jtxtFrom_GTT).add(jtxtTo_GTT).add(jtxtKinhGoi_GTT).add(jtxtDotHoc_GTT),
	tipGTT	= $("#tipGTT");
	function updateTips_GTT( t ) {
		tipGTT
			.text( t )
			.addClass( "ui-state-highlight" );
		setTimeout(function() {
			tipGTT.removeClass( "ui-state-highlight", 1500 );
		}, 1000 );
	}
	function GTT_checkLength( o, n, min, max) {
		if (min==0 && (o.val().length==0))
		{	
			o.addClass( "ui-state-error" );
			o.focus();
			updateTips_GTT( "Thông tin " + n + " không được phép để trống.");
			return false;
		}else if (min==max && o.val().length<min){
			o.addClass( "ui-state-error" );
			o.focus();
			updateTips_GTT("Thông tin " + n + " phải đủ " + min + " ký tự.");
		}else if ( o.val().length > max || o.val().length < min ) {
			o.addClass( "ui-state-error" );
			o.focus();
			updateTips_GTT("Chiều dài của " + n + " từ " + min + " đến " + max + " ký tự.");
			return false;
		} else {
			return true;
		}
	}
	// Check Regexp
	function GTT_checkRegexp( o, regexp, n ) {
		//alert('a');
		if ( o.val()!='' && !( regexp.test( o.val() ) ) ) {
			o.addClass( "ui-state-error" );
			o.focus();
			updateTips_GTT(n);
			return false;
		} else {
			return true;
		}
	}
	
	$("#txtDotHoc_GTT").change(function(e){
		//alert('a');
		if ($("#txtDotHoc_GTT").val()!='')
		{
			dataString = 'a=getdate&hisid=<?php echo $_REQUEST["hisid"]; ?>'+'&d='+$("#txtDotHoc_GTT").val();
			xreq = $.ajax({
			  type: 'POST', dataType: "json",url: 'khoa/khoa_giaytrieutap.php',data: dataString,
			  success: function(data) {
				if (data.error==0)
				{
					$("#txtFrom_GTT").val(data.from);
					$("#txtTo_GTT").val(data.to);
					
				}
			  },
			  error: function(xhr, ajaxOptions, thrownError) {}
			});
		}
	});	
	
	$("#btn_printpreview_GTT").click(function(e){
		var bValid = true;
		
		allFieldsGTT.removeClass( "ui-state-error" );
		
		bValid = bValid && GTT_checkLength( jtxtMaHV_GTT, "\"Mã học viên\"", 0, 10);
		bValid = bValid && GTT_checkLength( jtxtKinhGoi_GTT, "\"Kính gởi\"", 0, 250);
		bValid = bValid && GTT_checkLength( jtxtDotHoc_GTT, "\"Học kỳ\"", 0, 10)
		bValid = bValid && GTT_checkLength( jtxtFrom_GTT, "\"Thời gian từ\"", 0, 10);
		bValid = bValid && GTT_checkLength( jtxtTo_GTT, "\"Thời gian đến\"", 0, 10);
		
		if (bValid && !validateMaHV_GTT)
			gv_open_msg_box("Mã học viên không chính xác. Vui lòng nhập lại", "alert");
			
		if (bValid && validateMaHV_GTT) {					
			gv_processing_diglog("open", "Đang in ...");
			tipGTT.html("");
			dataString = $("#formGiayTrieuTap_khoa").serialize()
			+ '&a=print&hisid=<?php echo $_REQUEST["hisid"];?>';
			$.ajax({
				type: "POST", url: "khoa/khoa_giaytrieutap.php", data: dataString,	dataType: "html",
				success: function(data) {
							gv_processing_diglog("close");
							print_llkh_writeConsole(data, 0, "Giấy triệu tập - SĐH ĐHBK TP.HCM", 'style="font-family:Times New Roman,Arial,Helvetica,sans-serif;"',800,550);
						 }// end function(data)	
			}); // end .ajax
		}
		
		return false;

	});	
	
	$("#txtMaHV_GTT").change(function(e){
		$("#ho_ten_GTT").html("<img border='0' src='../images/ajax-loader.gif'/>");
		dataString = 'a=getname&hisid=<?php echo $_REQUEST["hisid"]; ?>'+'&'+$("#txtMaHV_GTT").serialize();
		xreq = $.ajax({
		  type: 'POST', dataType: "json",url: 'khoa/khoa_giaytrieutap.php',data: dataString,
		  success: function(data) {
			if (data.error==0)
			{
				validateMaHV_GTT = 1;
				$("#ho_ten_GTT").html(data.hoten);
			}
			else
			{
				validateMaHV_GTT = 0;
				$("#ho_ten_GTT").html("<font color=red>Không tìm thấy học viên</font>");
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
	
	file_put_contents("logs.txt", $sqlstr);
	
	$stmt = oci_parse($db_conn, $sqlstr);oci_execute($stmt);$n = oci_fetch_all($stmt, $resDM);oci_free_statement($stmt);
	
	if ($resDM["HO_TEN"][0]!="")
		echo '{"hoten":"'.$resDM["HO_TEN"][0].'", "error":"0"}';
	else
		echo '{"hoten":"", "error":"1"}';
}
else if ($a == 'getdate')
{
	$d = str_replace($searchdb,$replacedb,$_POST["d"]);
	$sqlstr="SELECT to_char(NGAY_BAT_DAU_HK,'dd/mm/yyyy') DATE_FROM, to_char(NGAY_KET_THUC_HK,'dd/mm/yyyy') DATE_TO
			FROM DOT_HOC_NAM_HOC_KY WHERE DOT_HOC = '$d'"; 	
	//file_put_contents("logs.txt", $sqlstr);
	$stmt = oci_parse($db_conn, $sqlstr);oci_execute($stmt);$n = oci_fetch_all($stmt, $resDM);oci_free_statement($stmt);
	if ($n>0)
		echo '{"from":"'.$resDM["DATE_FROM"][0].'", "to":"'.$resDM["DATE_TO"][0].'", "error":"0"}';
	else
		echo '{"from":"", "to":"", "error":"1"}';
}
else if ($a == 'print')
{
	date_default_timezone_set('Asia/Ho_Chi_Minh');
	$ngay =date("d");
	$thang =date("m");
	$nam =date("Y");
	//$date = new DateTime();
	//$date->modify('+1 month');
	
	$kinhgoi = str_replace($searchdb,$replacedb,$_POST["txtKinhGoi_GTT"]);
	$dothoc = str_replace($searchdb,$replacedb,$_POST["txtDotHoc_GTT"]);
	$tu = str_replace($searchdb,$replacedb,$_POST["txtFrom_GTT"]);
	$den = str_replace($searchdb,$replacedb,$_POST["txtTo_GTT"]);
	
	$sqlstr = "SELECT NAM_HOC_TU, NAM_HOC_DEN, HOC_KY FROM DOT_HOC_NAM_HOC_KY WHERE DOT_HOC = '$dothoc'";
	$stmt = oci_parse($db_conn, $sqlstr);oci_execute($stmt);$n = oci_fetch_all($stmt, $hk);oci_free_statement($stmt);
	
	$sqlstr="SELECT upper(ho || ' ' || ten) ho_ten, decode(H.PHAI, 'F','chị' ,'M','anh') titlePhai, to_char(NGAY_SINH, 'dd/mm/yyyy') NGAY_SINH, T.TEN_TINH_TP, h.Khoa, n.TEN_NGANH,
			so_qd_khoa('GTT', H.MA_HOC_VIEN) SO_QD, get_hinh_thuc_dao_tao(H.MA_HOC_VIEN) LOAI_HV
			FROM HOC_VIEN H, DM_TINH_TP T, NGANH N, BO_MON B
			WHERE MA_HOC_VIEN = '$m' AND NOI_SINH = T.MA_TINH_TP(+) AND H.MA_NGANH = N.MA_NGANH AND N.MA_BO_MON = B.MA_BO_MON
			AND B.MA_KHOA = '$makhoa'";
	//file_put_contents("logs.txt", $sqlstr);
	$stmt = oci_parse($db_conn, $sqlstr);
	if (oci_execute($stmt))
	{
		$n = oci_fetch_all($stmt, $hocvien);oci_free_statement($stmt);
		
		$sqlstr="BEGIN insert_so_qd_khoa('GTT', '$m', null, '$kinhgoi', '{$hocvien["SO_QD"][0]}', '$tu', '$den'); END;";
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
				<div style="font-size:140%; font-weight:bold; margin-bottom: 0px;">GIẤY TRIỆU TẬP CAO HỌC</div>
				<div style="font-size:120%; font-weight:bold;">HỌC KỲ <?php echo $hk["HOC_KY"][0];?> NĂM HỌC <?php echo "{$hk["NAM_HOC_TU"][0]}-{$hk["NAM_HOC_DEN"][0]}";?></div>
			</div>
			</td>
		  </tr>
		  <tr>
			<td colspan=2>
		  
				<table width="100%" border="0" align="center" cellpadding="5" cellspacing="0">
					<tr align="left">        
						<td align=left colspan=2 style="">
							<table>
								<tr>
									<td valign=top>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<b>Kính gởi:</b>&nbsp;&nbsp;</td><td valign=top>-&nbsp;&nbsp;<b><?php echo $_POST["txtKinhGoi_GTT"];?></b><br/>-&nbsp;&nbsp;Học viên <b><?php echo "{$hocvien["HO_TEN"][0]} (MSHV: $m)";?></b></td>
								</tr>
							</table>
						</td>
					</tr>
					<tr align="left">        
						<td align=left style="" colspan=2>Trường Đại Học Bách Khoa Tp.HCM kính báo để quý đơn vị, cơ quan và học viên biết:</td>
					</tr>
					<tr align="left">
						<td align=left colspan=2>Trường triệu tập học viên về trường để tham dự học kỳ <b><?php echo $hk["HOC_KY"][0];?></b> năm học <b><?php echo "{$hk["NAM_HOC_TU"][0]}-{$hk["NAM_HOC_DEN"][0]}";?></b></td>
					</tr>
					<tr align="left">
						<td align=left colspan=2>Trong thời gian từ ngày <b><?php echo $tu;?></b> đến ngày <b><?php echo $den;?></b></td>
					</tr>
					<tr align="left">
						<td align=left colspan=2>Đề nghị quý đơn vị, cơ quan cho phép học viên <b><?php echo $hocvien["HO_TEN"][0];?></b> về trường đúng thời hạn để tham dự khóa học.</td>
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
	else 
		file_put_contents("logs.txt", $sqlstr);
}

if (isset ($db_conn))
	oci_close($db_conn);
?>