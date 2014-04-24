<script type="text/javascript">
if (window==window.top) { /* I'm not in a frame! */
	//throw new Error("Page not found");
	window.location.href = "http://www.pgs.hcmut.edu.vn";
}
</script>
<?php
session_start();
$_SESSION["hv_tra_cuu_toefl"]=1;

include "libs/connect.php";

?>
<div align="left" style="margin:0 auto;">
<?php
$allow = 1;
if ($allow)
{
?>
	
	<form id="form_tracuu_DIEM_TOEFL" name="form_tracuu_DIEM_TOEFL" method="post" action="">
	   <table width="100%" border="0" cellspacing="0" cellpadding="5" align=center>
	   
		<tr><td></td><td colspan=3 style="font-family: arial; font-size:10pt; font-weight: bold">TRA CỨU ĐIỂM THI TOEFL ITP</td></tr>
		<tr>
		  <td align=right style='width:80px'>
			<span class="heading">
			<label for="diem_toefl_txtCMND">Số CMND</label>
			</span>
		  </td>
		  <td align=left style='width:150px'>
			<input type=textbox id="diem_toefl_txtCMND"  style="width:100px; height:20px;" class="text ui-widget-content ui-corner-all">
		  </td>
		  
		  
		</tr>
		
		
		<tr>
		  <td align=right>
			<span class="heading">
				<label for="diem_toefl_txtNgayThi">Ngày thi</label>
			</span>
		  </td>
		  <td align=left>
			<input type=textbox id="diem_toefl_txtNgayThi" style="width:80px; height:20px;" class="text ui-widget-content ui-corner-all"> <b><font color=red>(dd/mm/yyyy)</font></b>
		  </td>

		</tr>
		
		<tr>
		  <td align=right>
			
		  </td>
		  <td align=left style='font-size:80%'>
			<button id="diem_toefl_btn_xem" >&nbsp;Tra điểm</button>&nbsp;<button id="diem_ts_btn_printpreview" >&nbsp;Xem bản In</button>
		  </td>

		</tr>
		
		<tr>
			
			<td colspan=2><div id="tip" style='width:100%' class="validateTips" align="center"></div></td>
			
		</tr>
	   </table>

	   <div id="diem_toefl_chitiet" style="margin-top:-20px;" align=center></div>
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

function diem_toefl_updateDiem(p_cmnd, p_ngaythi)
{
	// e=link : nhúng vào trang web khác
	$("#diem_toefl_chitiet").html("<img border='0' src='images/ajax-loader.gif'/>");
	$( "#diem_ts_btn_printpreview" ).button( "disable" );
	$( "#diem_toefl_btn_xem" ).button( "disable" );
	//document.getElementById('tip').innerHTML('');
	$("#tip").html('');
	
	DataString = 'w=cmnd_ngaythi-diemtoefl'
	  + '&cmnd=' + p_cmnd 
	  + '&n=' + encodeURIComponent(p_ngaythi)
	  + '&hisid=<?php echo session_id(); ?>';
	
	xreq = $.ajax({
	  type: 'POST', data: DataString, dataType: "html", url: 'hv_tracuu_diem_toefl_itp_process.php',
	  success: function(data) {
		$("#diem_toefl_chitiet").html(data);
		$("#diem_ts_btn_printpreview" ).button( "enable" );
		$( "#diem_toefl_btn_xem" ).button( "enable" );
	  },
	  error: function(xhr, ajaxOptions, thrownError) {
		$("#diem_toefl_chitiet").html(thrownError);
	  }
	});
	
	if ($("#diem_toefl_chitiet").html()!='')
	{
		$( "#diem_ts_btn_printpreview" ).button( "enable" );
		$( "#diem_toefl_btn_xem" ).button( "enable" );
	}
	else
	{
		$( "#diem_ts_btn_printpreview" ).button( "disable" );
		$( "#diem_toefl_btn_xem" ).button( "disable" );
	}
}

$(function() {
	$( "#diem_ts_btn_printpreview" ).button({ icons: {primary:'ui-icon ui-icon-print'} });
	$( "#diem_toefl_btn_xem" ).button({ icons: {primary:'ui-icon ui-icon-search'} });
	
	//$("#diem_toefl_txtNgayThi").mask("99/99/9999");
    $("#diem_toefl_txtNgayThi").datepicker({
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
		if ($("#diem_toefl_chitiet").html()!='')
			diem_ts_writeConsole("<div style='font-size:13px; font-weight:bold;' align=left>ĐẠI HỌC BÁCH KHOA - TP.HCM<br/>PHÒNG ĐÀO TẠO SAU ĐẠI HỌC<br/> &nbsp;</div>" + $("#diem_toefl_chitiet").html() + "<div style='clear:both;'>&nbsp;<br/><a href='JavaScript:window.print();' style='font-size:150%; color:blue;'>In trang này</a></div>");
		return false;
	});	// end $("#diem_ts_btn_printpreview")
	
	$("#diem_toefl_btn_xem").click(function(){
		var bValid = true;
		$("#diem_toefl_txtCMND").removeClass( "ui-state-error" );
		$("#diem_toefl_txtNgayThi").removeClass( "ui-state-error" );
		
		if ($("#diem_toefl_txtCMND").val()=='') 
		{
			bValid = false;
			updateTips('Vui lòng nhập số CMND');
		}
		
		bValid = bValid && checkLength( $("#diem_toefl_txtCMND"), "\"Số CMND\"", 0, 12);
		bValid = bValid && checkLength( $("#diem_toefl_txtNgayThi"), "\"Ngày thi\"", 0, 10);
		
		var ngay = $("#diem_toefl_txtNgayThi").val();
	
		if (bValid && ( (!isNumber(ngay)) && !isValidDate($("#diem_toefl_txtNgayThi"),'dd/mm/yy') ))
		{
			$("#diem_toefl_txtNgayThi").addClass( "ui-state-error" );
			$("#diem_toefl_txtNgayThi").focus();
			updateTips("Ngày thi " + $("#diem_toefl_txtNgayThi").val() + " không đúng" );
			bValid = false;
		}

		
		if (bValid){
			diem_toefl_updateDiem( $("#diem_toefl_txtCMND").val(), $("#diem_toefl_txtNgayThi").val());
		}
		
		return false;
	});	// end $("#diem_toefl_btn_xem")
});
</script>

<?php 
if (isset ($db_conn))
	oci_close($db_conn);
?>