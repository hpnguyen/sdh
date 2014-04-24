<?php
include "libs/connect.php";
//include "libs/pgslibshv.php";

$oci_n = oci_parse($db_conn,"select value nam from config where name = 'KHOA_TUYEN_SINH_WEB'");
oci_execute($oci_n);$row = oci_fetch_all($oci_n,$nam);oci_free_statement($oci_n);
$nam_ts = $nam["NAM"][0];

$oci_n = oci_parse($db_conn,"select value dot_ts from config where name = 'DOT_TUYEN_SINH_WEB'");
oci_execute($oci_n);$row = oci_fetch_all($oci_n,$nam);oci_free_statement($oci_n);
$dot_ts = $nam["DOT_TS"][0];

?>
<div align="left" style="margin:0 auto;">
<?php
$allow = 1;
if ($allow)
{
?>
	
	<form id="form_tracuu_DIEM_TS" name="form_tracuu_DIEM_TS" method="post" action="">
	   <table width="100%" border="0" cellspacing="0" cellpadding="5" align=center>
	   
		<tr><td></td><td colspan=3 style="font-family: arial; font-size:10pt; font-weight: bold">ĐIỂM TUYỂN SINH NĂM <?php echo $nam_ts; ?> ĐỢT <?php echo $dot_ts; ?></td></tr>
		<tr>
		  <td align=right style='width:80px'>
			<span class="heading">
			<label for="diem_ts_txtSBD">Số báo danh</label>
			</span>
		  </td>
		  <td align=left style='width:150px'>
			<input type=textbox id="diem_ts_txtSBD" style="width:100px; height:20px;" class="text ui-widget-content ui-corner-all">
		  </td>
		  <td align=right style='width:100px'>
			<span class="heading">
			<label for="diem_ts_txtHoTen">hoặc Họ & Tên</label>
			</span>
		  </td>
		  <td align=left style='width:150px'>
			<input type=textbox id="diem_ts_txtHoTen" style="width:150px; height:20px;" class="text ui-widget-content ui-corner-all">
		  </td>
		  
		</tr>
		
		
		<tr>
		  <td align=right>
			<span class="heading">
				<label for="diem_cd_txtNgaySinh">và Ngày sinh</label>
			</span>
		  </td>
		  <td colspan=2 align=left>
			<input type=textbox id="diem_cd_txtNgaySinh" style="width:80px; height:20px;" class="text ui-widget-content ui-corner-all"> <font color=red>(dd/mm/yyyy hoặc yyyy)</font>
		  </td>
		  
		  <td align=left>
		  </td>

		</tr>
		
		<tr>
		  <td align=right>
			
		  </td>
		  <td align=left colspan="3" style='font-size:80%'>
			<button id="diem_ts_btn_xem" >&nbsp;Tra điểm</button>&nbsp;<button id="diem_ts_btn_printpreview" >&nbsp;Xem bản In</button>
		  </td>

		</tr>
		
		<tr>
			<td></td>
			<td colspan=3><div id="tip" style='width:100%' class="validateTips" align="center"></div></td>
			
		</tr>
	   </table>

	   <div id="diem_ts_chitiet" style="margin-top:-20px;" align=center></div>
	</form>
<?php
}
else
{
	echo "<div align=center style='font-size:12px; font-weight:bold; color:red'>Kết quả tuyển sinh dự kiên công bố ngày 20/06/2013, vui lòng quay lại sau.</div>";
}
?>
</div>

<script type="text/javascript">
function isNumber(n) {
  return !isNaN(parseFloat(n)) && isFinite(n);
}

function diem_ts_writeConsole(content) {
	a=window.open('','myconsole',
	'width=800,height=450'
	+',menubar=0'
	+',toolbar=0'
	+',status=0'
	+',scrollbars=1'
	+',resizable=1')
	a.document.writeln(
	'<html><head><title>Phòng Đào Tạo SĐH - ĐHBK</title></head>'
	+'<link href="css/pgs.css" rel="stylesheet" type="text/css"/>'
	+'<body bgcolor=white onLoad="self.focus()">'
	+content
	+'</body></html>'
	)
	a.document.close()
}

function diem_ts_updateDiem(p_sbd, p_hoten, p_ngaysinh)
{
	// e=link : nhúng vào trang web khác
	$("#diem_ts_chitiet").html("<img border='0' src='images/ajax-loader.gif'/>");
	$( "#diem_ts_btn_printpreview" ).button( "disable" );
	$( "#diem_ts_btn_xem" ).button( "disable" );
	//document.getElementById('tip').innerHTML('');
	$("#tip").html('');
	
	DataString = 'w=sdb_hoten_ngaysinh-diemts'
	  + '&s=' + p_sbd 
	  + '&h=' + encodeURIComponent(p_hoten) 
	  + '&n=' + encodeURIComponent(p_ngaysinh)
	  + '&hisid=<?php echo $_REQUEST["hisid"]; ?>';
	
	xreq = $.ajax({
	  type: 'POST', data: DataString, dataType: "html", url: 'hv_tracuu_diem_ts_process.php',
	  success: function(data) {
		$("#diem_ts_chitiet").html(data);
		$("#diem_ts_btn_printpreview" ).button( "enable" );
		$( "#diem_ts_btn_xem" ).button( "enable" );
	  },
	  error: function(xhr, ajaxOptions, thrownError) {
		$("#diem_ts_chitiet").html(thrownError);
	  }
	});
	
	if ($("#diem_ts_chitiet").html()!='')
	{
		$( "#diem_ts_btn_printpreview" ).button( "enable" );
		$( "#diem_ts_btn_xem" ).button( "enable" );
	}
	else
	{
		$( "#diem_ts_btn_printpreview" ).button( "disable" );
		$( "#diem_ts_btn_xem" ).button( "disable" );
	}
}

$(function() {
	$( "#diem_ts_btn_printpreview" ).button({ icons: {primary:'ui-icon ui-icon-print'} });
	$( "#diem_ts_btn_xem" ).button({ icons: {primary:'ui-icon ui-icon-search'} });
	
	//$("#diem_cd_txtNgaySinh").mask("99/99/9999");
    $("#diem_cd_txtNgaySinh").datepicker({
			showOn: "button",
			buttonImageOnly: false,
			dateFormat: "dd/mm/yy"
	});
	
	
	// Check validate fields Login
	var tips	= $("#tip");
	function updateTips( t ) {
					tips
							.text( t )
							.addClass( "ui-state-highlight" );
						setTimeout(function() {
							tips.removeClass( "ui-state-highlight", 1500 );
						}, 1000 );
	}
	
	function checkLength( o, n, min, max) {
		if (min==0 && (o.val().length==0))
		{	
			o.addClass( "ui-state-error" );
			o.focus();	
			updateTips( "Thông tin " + n + " không được phép để trống." );
			
			return false;
		}else if (min==max && o.val().length<min){
			o.addClass( "ui-state-error" );
			o.focus();	
			updateTips( "Thông tin " + n + " phải đủ " + min + " ký tự." );
		}else if ( o.val().length > max || o.val().length < min ) {
			o.addClass( "ui-state-error" );
			o.focus();		
			updateTips( "Chiều dài của " + n + " từ " +
						min + " đến " + max + " ký tự.");
			return false;
		} else {
			return true;
		}
	}
	
	function isValidDate(controlName, format){
		var isValid = true;
		try{
			$.datepicker.parseDate(format, controlName.val(),null);
		}
		catch(error){
			isValid = false;
		}
		return isValid;
	}
	
	
	$("#diem_ts_btn_printpreview").click(function(){
		if ($("#diem_ts_chitiet").html()!='')
			diem_ts_writeConsole("<div style='font-size:13px; font-weight:bold;' align=left>ĐẠI HỌC BÁCH KHOA - TP.HCM<br/>PHÒNG ĐÀO TẠO SAU ĐẠI HỌC<br/> &nbsp;</div>" + $("#diem_ts_chitiet").html() + "<div style='clear:both;'>&nbsp;<br/><a href='JavaScript:window.print();' style='font-size:150%; color:blue;'>In trang này</a></div>");
		return false;
	});	// end $("#diem_ts_btn_printpreview")
	
	$("#diem_ts_btn_xem").click(function(){
		var bValid = true;
		$("#diem_ts_txtSBD").removeClass( "ui-state-error" );
		$("#diem_cd_txtNgaySinh").removeClass( "ui-state-error" );
		
		//alert(document.getElementById("diem_ts_txtSBD").value);
		
		//alert ($("#diem_ts_txtSBD").val()); !isValidDate($("#diem_cd_txtNgaySinh"),'dd/mm/yy') &&
		
		if ($("#diem_ts_txtSBD").val()=='' && $("#diem_ts_txtHoTen").val()=='') 
		{
			bValid = false;
			updateTips('Vui lòng nhập SBD hoặc Họ & tên');
		}
		
		bValid = bValid && checkLength( $("#diem_cd_txtNgaySinh"), "\"Ngày sinh\"", 0, 10);
		
		var ngay = $("#diem_cd_txtNgaySinh").val();
	
		if (bValid && ( (!isNumber(ngay)) && !isValidDate($("#diem_cd_txtNgaySinh"),'dd/mm/yy') ))
		{
			$("#diem_cd_txtNgaySinh").addClass( "ui-state-error" );
			$("#diem_cd_txtNgaySinh").focus();
			updateTips("Ngày " + $("#diem_cd_txtNgaySinh").val() + " không tồn tại" );
			bValid = false;
		}

		
		if (bValid){
			diem_ts_updateDiem( $("#diem_ts_txtSBD").val(), $("#diem_ts_txtHoTen").val() , $("#diem_cd_txtNgaySinh").val());
		}
		
		return false;
	});	// end $("#diem_ts_btn_xem")
});
</script>

<?php 
if (isset ($db_conn))
	oci_close($db_conn);
?>