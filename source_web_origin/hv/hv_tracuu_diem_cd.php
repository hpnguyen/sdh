<div align="left" style="margin:0 auto;">
<form id="form_tracuu_DIEM_CD" name="form_tracuu_DIEM_CD" method="post" action="">
   <table width="100%" border="0" cellspacing="0" cellpadding="5" align=left>
   
	<tr>
	  <td align=right style='width:80px'>
		<span class="heading">
		<label for="diem_cd_txtMaHV">Mã học viên</label>
		</span>
	  </td>
	  <td align=left style='width:150px'>
		<input type=textbox id="diem_cd_txtMahv" style="width:150px; height:20px;" class="text ui-widget-content ui-corner-all">
	  </td>
	</tr>
	
	
	<tr>
	  <td align=right>
		<span class="heading">
			<label for="diem_cd_txtNgaySinh">Ngày sinh</label>
		</span>
	  </td>
	  <td align=left>
		<input type=textbox id="diem_cd_txtNgaySinh" style="width:80px; height:20px;" class="text ui-widget-content ui-corner-all">
	  </td>
	</tr>
	
	<tr>
	  <td align=right>
		
	  </td>
	  <td align=left colspan="3" style='font-size:80%'>
		<a id="diem_cd_btn_xem" >&nbsp;Tra điểm</a>&nbsp;<a id="diem_cd_btn_printpreview" >&nbsp;Xem bản In</a>
	  </td>
	</tr>
	
	<tr>
	<td></td>
	<td ><div id="tip" style='width:300px' class="validateTips" align="left"></div></td>
	</tr>
   </table>

   <div id="diem_cd_chitiet" style="margin-top:5px;" align=center></div>
</form>
</div>
<script type="text/javascript">
function diem_cd_writeConsole(content) {
	top.consoleRef=window.open('','myconsole','width=800,height=450,menubar=0,toolbar=0,status=0,scrollbars=1,resizable=1');
	top.consoleRef.document.writeln('<html><head><title>Phòng Đào Tạo SĐH - ĐHBK</title></head><link href="css/pgs.css" rel="stylesheet" type="text/css"/><body bgcolor=white onLoad="self.focus()">' + content + '</body></html>');
	top.consoleRef.document.close();
}

function diem_cd_updateDiem(p_mahv, p_ngaysinh)
{
	// e=link : nhúng vào trang web khác
	$("#diem_cd_chitiet").html("<img border='0' src='images/ajax-loader.gif'/>");
	$( "#diem_cd_btn_printpreview" ).button( "disable" );
	$( "#diem_cd_btn_xem" ).button( "disable" );
	//document.getElementById('tip').innerHTML('');
	$("#tip").html('');
	
	var DataString = "w=mahv_ngaysinh-diemcd&m=" + p_mahv + "&n=" + encodeURIComponent(p_ngaysinh) + "&hisid=" + "<?php echo isset($_REQUEST["hisid"]) && ! is_null($_REQUEST["hisid"]) ? $_REQUEST["hisid"] : ''; ?>";
	
	xreq = $.ajax({
	  type: 'POST', 
	  data: DataString, 
	  dataType: "html", 
	  url: 'hv_tracuu_diem_cd_process.php',
	  success: function(data) {
		$("#diem_cd_chitiet").html(data);
		$("#diem_cd_btn_printpreview" ).button( "enable" );
		$( "#diem_cd_btn_xem" ).button( "enable" );
	  },
	  error: function(xhr, ajaxOptions, thrownError) {
		$("#diem_cd_chitiet").html(thrownError);
	  }
	});
	
	if ($("#diem_cd_chitiet").html()!='')
	{
		$( "#diem_cd_btn_printpreview" ).button( "enable" );
		$( "#diem_cd_btn_xem" ).button( "enable" );
	}
	else
	{
		$( "#diem_cd_btn_printpreview" ).button( "disable" );
		$( "#diem_cd_btn_xem" ).button( "disable" );
	}
}

$(function() {
	$( "#diem_cd_btn_printpreview" ).button({ icons: {primary:'ui-icon ui-icon-print'} });
	$( "#diem_cd_btn_xem" ).button({ icons: {primary:'ui-icon ui-icon-search'} });
	
	$("#diem_cd_txtNgaySinh").mask("99/99/9999");
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
			updateTips( "Chiều dài của " + n + " từ " + 	min + " đến " + max + " ký tự.");
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
	
	
	$("#diem_cd_btn_printpreview").click(function(){
		diem_cd_writeConsole("<div style='font-size:13px; font-weight:bold;' align=left>ĐẠI HỌC BÁCH KHOA - TP.HCM<br/>PHÒNG ĐÀO TẠO SAU ĐẠI HỌC<br/> &nbsp;</div>" + $("#diem_cd_chitiet").html() + "<div style='clear:both;'>&nbsp;<br/><a href='JavaScript:window.print();' style='font-size:150%; color:blue;'>In trang này</a></div>");
	});	// end $("#diem_cd_btn_printpreview")
	
	$("#diem_cd_btn_xem").click(function(){
		var bValid = true;
		$("#diem_cd_txtMahv").removeClass( "ui-state-error" );
		$("#diem_cd_txtNgaySinh").removeClass( "ui-state-error" );
		
		//alert(document.getElementById("diem_cd_txtMaHV").value);
		
		//alert ($("#diem_cd_txtMaHV").val());
		
		bValid = bValid && checkLength( $("#diem_cd_txtMahv"), "\"Mã học viên\"", 0, 15);
		bValid = bValid && checkLength( $("#diem_cd_txtNgaySinh"), "\"Ngày sinh\"", 0, 10);
		
		if (bValid && !isValidDate($("#diem_cd_txtNgaySinh"),'dd/mm/yy'))
			{
				$("#diem_cd_txtNgaySinh").addClass( "ui-state-error" );
				$("#diem_cd_txtNgaySinh").focus();
				updateTips("Ngày " + $("#diem_cd_txtNgaySinh").val() + " không tồn tại" );
				bValid = false;
			}

		
		if (bValid){
			diem_cd_updateDiem( $("#diem_cd_txtMahv").val() , $("#diem_cd_txtNgaySinh").val());
		}
	});	// end $("#diem_cd_btn_xem")
});
</script>