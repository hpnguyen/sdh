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
if (!allowPermisstion(base64_decode($_SESSION['uidloginPortal']), '014', $db_conn))
{
	die('Truy cập bất hợp pháp'); 
}

//file_put_contents("logs.txt", allowPermisstion1('MT1743', '014', $db_conn));


//$macb = $_SESSION['macb'];
$usr = base64_decode($_SESSION['uidloginPortal']);
$sqlstr="SELECT (ho || ' ' || ten) ho_ten, email, k.ten_khoa, dien_thoai
FROM nhan_su n, khoa k
WHERE upper(n.username) = upper('$usr')
AND n.fk_ma_khoa = k.ma_khoa";

$stmt = oci_parse($db_conn, $sqlstr);
oci_execute($stmt);
$n = oci_fetch_all($stmt, $accinfo);
oci_free_statement($stmt);

?>

<div align="center">
<form id="form_accountinfo" name="form_accountinfo" method="post" action="">
		<table width="320" cellspacing="0" cellpadding="0" class="ui-corner-all shawdow">
 			<tr> <td> <div align="center" id="tipAI" class="ui-corner-tl ui-corner-tr validateTips">
                  </div> </td></tr>       
        	<tr> 
			<td>
               <table width="100%" border="0" cellspacing="0" cellpadding="5" class="">
               
					<tr>
					  <td align=left><span class="heading">
						Họ tên
						</span></td>
					  <td class="fontcontent" align=left><?php echo $accinfo["HO_TEN"][0]; ?></td>
					</tr>
					
					<tr>
					  <td align=left><span class="heading ">
						Khoa
						</span></td>
					  <td class="fontcontent" align=left><?php echo $accinfo["TEN_KHOA"][0]; ?></td>
					</tr>
					<tr>
					  <td align=left><span class="heading ">
						Email
					  </span></td>
					  <td align=left><input style="width:180pt;" placeholder="địa chỉ email" id="acc_email"  name="acc_email" type="text" class="text ui-widget-content ui-corner-all" value="<?php echo $accinfo["EMAIL"][0]; ?>" /></td>
					</tr>
					<tr>
					  <td></td>
					  <td align=left><em>Email này dùng để khôi phục mật khẩu</em></td>
					</tr>
					
					<tr>
					  <td colspan=2 align=left><span class="heading ">
						Xác nhận lại người dùng
					  </span></td>
					</tr>
					
					<tr>
					  <td ></td>
					  <td align="left">
						 <div id="ai_tooltips" style="color:red; font-size:11px;"></div>
					  </td>
					</tr>
					
					<tr>
					  <td ><span class="heading">
						<label for="usrname" class="ui-icon ui-icon-person"></label>
						</span></td>
					  <td><input placeholder="tên đăng nhập" style="width:180pt;" name="acc_usrname" type="text" class="text ui-widget-content ui-corner-all" id="acc_usrname" size="37" value="" /></td>
					</tr>
					
					
					<tr>
					  <td><span class="heading ">
						<label for="pass" class="ui-icon ui-icon-locked"></label>
						</span></td>
					  <td><input placeholder="mật khẩu" style="width:180pt;" name="acc_pass" type="password" class="text ui-widget-content ui-corner-all" id="acc_pass" size="37" /></td>
					</tr>
					
					<tr>
					  <td><span class="heading ">
					
					  </span></td>
					  <td></td>
					</tr>
					<tr>
					  <td>&nbsp;</td>
					  <td align="right"> <button id="btnInfoChange">Thay đổi</button>&nbsp;&nbsp;</td>
					</tr>
					<tr>
					  <td align="center" colspan="2"> </td>
					</tr>
             	
               </table>
             </td> 
			 </tr>
        </table>  
</form>
</div>

<script>
$(function() {
	$( "#btnInfoChange").button();
	
	var ai_jemail 	= $("#acc_email"),
	ai_juser		= $("#acc_usrname"),
	ai_jpass		= $("#acc_pass"),
	ai_allFields = $([]).add(ai_jemail).add(ai_juser).add(ai_jpass),
	ai_tips	= $("#tipAI");
	 
	function ai_updateTips( t ) {
		ai_tips
					.text( t )
					.addClass( "ui-state-highlight" );
		setTimeout(function() {
			ai_tips.removeClass( "ui-state-highlight", 1500 );
		}, 1000 );
	}
	
	// Checklength
	function ai_checkLength( o, n, min, max) {
		if (min==0 && (o.val().length==0))
		{	
			o.addClass( "ui-state-error" );
			o.focus();	
			ai_updateTips( n + " không được để trống." );
			
			return false;
		}else if (min==max && o.val().length<min){
			o.addClass( "ui-state-error" );
			o.focus();	
			ai_updateTips( "Thông tin " + n + " phải đủ " + min + " ký tự." );
		}else if ( o.val().length > max || o.val().length < min ) {
			o.addClass( "ui-state-error" );
			o.focus();	
			ai_updateTips( "Chiều dài của " + n + " từ " +
						min + " đến " + max + " ký tự." );
			return false;
		} else {
			return true;
		}
	}
	
	function ai_checkRegexp( o, regexp, n ) {
		//alert('a');
		if ( !( regexp.test( o.val() ) ) ) {
			o.addClass( "ui-state-error" );
			o.focus();
			ai_updateTips( n);
			return false;
		} else {
			return true;
		
		}
	}
	
// End of check validate
	$("#btnInfoChange").click(function(e){
	//$("#form_changepass").submit(function(e) {
		var bValid = true;
		ai_allFields.removeClass( "ui-state-error" );
		
		bValid = bValid && ai_checkLength( ai_jemail, "\"Email\"", 0, 100 );
		bValid = bValid && ai_checkRegexp( ai_jemail, /^((([a-z]|\d|[!#\$%&'\*\+\-\/=\?\^_`{\|}~]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])+(\.([a-z]|\d|[!#\$%&'\*\+\-\/=\?\^_`{\|}~]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])+)*)|((\x22)((((\x20|\x09)*(\x0d\x0a))?(\x20|\x09)+)?(([\x01-\x08\x0b\x0c\x0e-\x1f\x7f]|\x21|[\x23-\x5b]|[\x5d-\x7e]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(\\([\x01-\x09\x0b\x0c\x0d-\x7f]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF]))))*(((\x20|\x09)*(\x0d\x0a))?(\x20|\x09)+)?(\x22)))@((([a-z]|\d|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(([a-z]|\d|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])*([a-z]|\d|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])))\.)+(([a-z]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(([a-z]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])*([a-z]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])))\.?$/i,"Nhập không đúng định dạng email, vd: pgs@hcmut.edu.vn" );
		
		bValid = bValid && ai_checkLength( ai_juser, "\"Tên người dùng\"", 0, 100 );
		bValid = bValid && ai_checkLength( ai_jpass, "\"Mật khẩu\"", 0, 100 );
		
		if (bValid){
			dataString = $("#form_accountinfo").serialize();
			dataString += '&hisid=<?php echo $_REQUEST["hisid"];?>';
			
			$.ajax({type: "POST",url: "accountinfoprocess.php",data: dataString, dataType: "json",
				success: function(data) {
							//ai_updateTips(data.msg);
							gv_open_msg_box(data.msg,"info",250,150);
						 }// end function(data)	
			}); // end .ajax
		}
		//if (!bValid){		
		e.preventDefault();
			//$("#form_dangnhap").submit();
		//}
	});	// end frmTraCuuCD
	
	
	$('#acc_pass').keypress(function(e) { 
		var s = String.fromCharCode( e.which );
		if ( s.toUpperCase() === s && s.toLowerCase() !== s && !e.shiftKey ) {
			$("#ai_tooltips").html('Chú ý: Caps Lock đang mở');
		}
		else
		{
			$("#ai_tooltips").html('');
		}
	});
	
	$('input[placeholder],textarea[placeholder]').placeholder();

});
</script>