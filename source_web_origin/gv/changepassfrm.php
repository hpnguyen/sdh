<?php
if (isset($_REQUEST["hisid"])){
	session_id($_REQUEST["hisid"]);
	session_start();
}
if (!isset($_SESSION['uidloginPortal'])){
	die('Truy cập bất hợp pháp'); 
}
include "libs/connect.php";
include "libs/pgslibs.php";
if (!allowPermisstion(base64_decode($_SESSION['uidloginPortal']), '013', $db_conn))
{
	die('Truy cập bất hợp pháp'); 
}
?>

<div align="center">
<form id="form_changepass" name="form_changepass" method="post" action="">
		<table width="250" cellspacing="0" cellpadding="0" class="ui-corner-all shawdow">
 			<tr> 
				<td> 
					<div align="center" id="tipChangePass" class="ui-corner-tl ui-corner-tr validateTips "></div> 
				</td>
			</tr>       
        	<tr> <td>
               <table width="100%" border="0" cellspacing="0" cellpadding="5" class="">
                <tr>
				  <td><span class="heading">
                    <label for="usrname" class="ui-icon ui-icon-person"></label>
                    </span></td>
				  
                  <td align=left>
				  <input placeholder="tên người dùng" style="width:180pt;" name="usrname" type="text" class="text ui-widget-content ui-corner-all" id="usrname" size="37" value="" />
				  </td>
                </tr>

				<tr>
					<td><span class="heading ">
					<label for="pass" class="ui-icon ui-icon-locked"></label>
					</span></td>
					
					<td align=left><input placeholder="mật khẩu" style="width:180pt;" name="pass" type="password" class="text ui-widget-content ui-corner-all" id="pass" size="37" /></td>
                </tr>
				
				<tr>
				  <td align="left" colspan=2>
					 <div id="cp_tooltips" style="color:red; font-size:11px;"></div>
				  </td>
				</tr>
				
                <tr>
				
					<td><span class="heading ">
                    <label for="passnew" class="ui-icon ui-icon-document"></label>
                  </span></td>
                    <td align=left><input placeholder="mật khẩu mới" style="width:180pt;" name="passnew" type="password" class="text ui-widget-content ui-corner-all" id="passnew" /></td>
                </tr>
				
                <tr>
					<td><span class="heading ">
						<label for="repassnew" class="ui-icon ui-icon-copy"></label>
					  </span></td>
                  <td align=left><input placeholder="xác nhận lại mật khẩu" style="width:180pt;" name="repassnew" type="password" class="text ui-widget-content ui-corner-all" id="repassnew"/></td>
                </tr>
				
                <tr>
					<td></td>
                  <td align="right"> <button id="btnPassChange">Thay đổi</button></td>
                </tr>
                <tr>
                  <td align="center" colspan="2"> </td>
                </tr>
             	
               </table>
             </td> </tr>
        </table>  
</form>
</div>

<div id="changepass_dialog_info" title="Phòng Đào Tạo SDH - ĐHBK TP.HCM">
<p><span class="ui-icon ui-icon-info" style="float:left; margin:0 7px 50px 0;"></span><span id="changepass_dialog_info_msg">Mật khẩu đã thay đổi, quý Thầy/Cô vui lòng đăng nhập lại.</span></p>
</div>

<script>
$(function() {
	$( "#btnPassChange").button();
	
	var cp_jusrname 	= $("#usrname"),
	cp_jpass 			= $("#pass"),
	cp_jpassnew		= $("#passnew"),
	cp_jrepassnew		= $("#repassnew"),
	cp_allFieldsChangePass = $([]).add(cp_jusrname).add(cp_jpass).add(cp_jpassnew).add(cp_jrepassnew),
	cp_tipsChangePass	= $("#tipChangePass");
	 
	function cp_updateTips( t ) {
		cp_tipsChangePass
					.text( t )
					.addClass( "ui-state-highlight" );
		setTimeout(function() {
			cp_tipsChangePass.removeClass( "ui-state-highlight", 1500 );
		}, 1000 );
	}
	
	// Checklength
	function cp_checkLength( o, n, min, max) {
		if (min==0 && (o.val().length==0))
		{	
			o.addClass( "ui-state-error" );
			o.focus();	
			cp_updateTips( n + " không được để trống." );
			
			return false;
		}else if (min==max && o.val().length<min){
			o.addClass( "ui-state-error" );
			o.focus();	
			cp_updateTips( "Thông tin " + n + " phải đủ " + min + " ký tự." );
		}else if ( o.val().length > max || o.val().length < min ) {
			o.addClass( "ui-state-error" );
			o.focus();	
			cp_updateTips( "Chiều dài của " + n + " từ " +
						min + " đến " + max + " ký tự." );
			return false;
		} else {
			return true;
		}
	}
	
	function cp_checkRegexp( o, regexp, n ) {
		//alert('a');
		if ( !( regexp.test( o.val() ) ) ) {
			o.addClass( "ui-state-error" );
			o.focus();
			cp_updateTips( n);
			return false;
		} else {
			return true;
		
		}
	}
	
// End of check validate
	$("#btnPassChange").click(function(e){
	//$("#form_changepass").submit(function(e) {
		var bValid = true;
		cp_allFieldsChangePass.removeClass( "ui-state-error" );
		
		bValid = bValid && cp_checkLength( cp_jusrname, "\"Tên người dùng\"", 0, 20);
		
		
		if (bValid)
			bValid = bValid && cp_checkLength( cp_jpass, "\"Mật khẩu\"", 0, 20);

		if (bValid)
			bValid = bValid && cp_checkLength( cp_jpassnew, "\"Mật khẩu mới\"", 6, 20);
	
		if (bValid)
			bValid = bValid && cp_checkRegexp( cp_jpassnew, /^([0-9a-zA-Z])+$/, "Mật khẩu chỉ được phép có các ký tự: a-z 0-9" );
		
		if (bValid){
			bValid = bValid && cp_checkLength( cp_jrepassnew, "\"Xác nhận mật khẩu\"", 6, 20);
			if (cp_jpassnew.val() != cp_jrepassnew.val())
			{
				bValid = false;
				cp_updateTips("Xác nhận mật khẩu không chính xác");
				cp_jrepassnew.addClass( "ui-state-error" );
				cp_jrepassnew.focus();	
			}
		}
		
		if (bValid){
			if (cp_jpassnew.val() == cp_jusrname.val())
			{
				bValid = false;
				cp_updateTips("Mật khẩu trùng Tên người dùng");
				cp_jpassnew.addClass( "ui-state-error" );
				cp_jpassnew.focus();	
			}
		}
		
		if (bValid){
			dataString = $("#form_changepass").serialize();
			dataString += '&hisid=<?php echo $_REQUEST["hisid"];?>';
			
			$.ajax({type: "POST",url: "changepassprocess.php",data: dataString, dataType: "json",
				success: function(data) {
							if (data.msg=='Mật Khẩu đã thay đổi')
								$( '#changepass_dialog_info' ).dialog('open');
							else
								cp_updateTips(data.msg);
						 }// end function(data)	
			}); // end .ajax
		}
		//if (!bValid){		
		e.preventDefault();
			//$("#form_dangnhap").submit();
		//}
	});	// end frmTraCuuCD
	
	
	$('#pass,#passnew,#repassnew').keypress(function(e) { 
		var s = String.fromCharCode( e.which );
		if ( s.toUpperCase() === s && s.toLowerCase() !== s && !e.shiftKey ) {
			$("#cp_tooltips").html('Chú ý: Caps Lock đang mở');
		}
		else
		{
			$("#cp_tooltips").html('');
		}
	});
	
	$( "#changepass_dialog_info" ).dialog({
			resizable: false,
			autoOpen: false,
			closeOnEscape: false,
			open: function(event, ui) { $(".ui-dialog-titlebar-close").hide(); },
			width:280, height:180,
			modal: true,
			buttons: {
				"Đồng ý": function() {
					<?php echo "window.location.href='login.php?hisid={$_REQUEST["hisid"]}&cat=signout';"; ?>
					$( this ).dialog( "close" );
				}
			}
	});
	
	$('input[placeholder],textarea[placeholder]').placeholder();
});

//HandlePlaceholder(document.getElementById('usrname'));
//HandlePlaceholder(document.getElementById('pass'));

</script>