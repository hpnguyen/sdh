<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Đăng Nhập Hệ Thống - Phòng Đào Tạo Sau Đại Học</title>

  <link href="css/start/jquery-ui-1.8.18.custom.css" rel="stylesheet" type="text/css"/>
  <link href="hv/css/pgs.css" rel="stylesheet" type="text/css"/>
  <script src="js/jquery-1.8.0.min.js"></script>
  <script src="js/jquery-ui-1.8.23.custom.min.js"></script>
  <script src="js/jquery.placeholder-1.1.9.js"></script>
	
</head>

<body style="font-family:Arial, Helvetica, sans-serif">
<!--
<?php 
	ini_set('display_errors', '1');

	$sid = $_REQUEST["hisid"];

	include "libs/connect1.php";
	include "libs/connect2.php";
	
	$search = array("'","\"");
	$replace = array("\\'","&quot;");
?>
-->
<div id="container">

<div align="center" style='float:center;margin-top:30px;margin-bottom:30px;font-size:80%'>
<form id="form_forgotpass" name="form_forgotpass" method="post" action="forgot.php">

  <table width="360" border="0" cellspacing="0" cellpadding="0">
    
    <tr>
      <td height="34" colspan="3">
        <table width="100%" border="0" cellpadding="0" cellspacing="0" class="ui-corner-all ui-widget-content shawdow" >
          <tr>
            <td height="57" colspan="4" valign="middle">
              <table width="100%" border="0" cellspacing="0" cellpadding="10" class="cobg ui-corner-tl ui-corner-tr"  >
                <tr>
                  <td height="57" align="left"><p class="heading2">&nbsp;&nbsp;Quên mật khẩu?</p></td>
                  </tr>
                </table>
  </td>
            </tr>
          <tr>
            <td width="1%" align="right" valign="middle" class="heading">&nbsp;</td>
            <td colspan="2" align="left" ></td>
            <td width="8%"></td>
            </tr>
          
          <tr>
            <td  colspan="4">
              
			   <script type="text/javascript">
				 var RecaptchaOptions = {
					theme : 'white',
					lang : 'vi'
				 };
				 </script>
			  
              <div id="recaptcha_div">
                
                <table width="100%" border="0" cellspacing="0" cellpadding="0">
                  <tr>
                    <td height="37" align="left" valign="middle" class="fontcontent"></td>
                    <td height="37" colspan="2" align="left" valign="middle" class="fontcontent"><p>Vui lòng cung cấp thông tin email</p></td>
                    <td height="37" align="left" valign="middle" class="fontcontent"></td>
                    </tr>
                  <tr>
                    <td width="29" height="37" valign="middle" align="right" class="heading"> <label for="email" class="ui-icon ui-icon-mail-closed"></label> </td>
                    <td colspan="2" align="left">     
                      <input style="width:235pt;" placeholder="email đẵ đăng ký..." name="email" type="text" class="text ui-widget-content ui-corner-all" id="email" size="48" value="<?php if (isset($_REQUEST["email"])) echo $_REQUEST["email"] ?>" />
                      </td>
                    <td width="45"></td>
                    </tr>
                  <tr>
                    <td height="24">&nbsp;</td>
                    <td height="20" colspan="2" align="center"  valign="top">
                      
                      <?php
    
                require_once('hv/libs/recaptchalib.php');
                
                // Get a key from https://www.google.com/recaptcha/admin/create
                $publickey = "6LcDrc8SAAAAAOehS2lVAVXYUefcRz94cIJK6UbZ";
                $privatekey = "6LcDrc8SAAAAACyVrTs8yuqB_vHagESgu29bKSoF";
                
                # the response from reCAPTCHA
                $resp = null;
                # the error code from reCAPTCHA, if any
                $error = null;
                
                # was there a reCAPTCHA response?
                if (isset($_POST["recaptcha_response_field"]) && $_POST["recaptcha_response_field"]) {
                        $resp = recaptcha_check_answer ($privatekey,
                                                        $_SERVER["REMOTE_ADDR"],
                                                        $_POST["recaptcha_challenge_field"],
                                                        $_POST["recaptcha_response_field"]);
                
                        if ($resp->is_valid) {
							$email = $_POST["email"];					
							$str="select password,USERNAME, ho, ten from nhan_su 
							where trim(upper(email))=trim(upper('"
							.str_replace("'", "''",$email)."'))";
							$oci_pa = oci_parse($db_conn_gv,$str);oci_execute($oci_pa);$n=oci_fetch_all($oci_pa, $kt);oci_free_statement($oci_pa);
							
							if ($n==0) {
								$str="select PASS password, USERNAME, ho, ten from nguoi_dung 
								where trim(upper(email))=trim(upper('"
								.str_replace("'", "''",$email)."'))";
								$oci_pa = oci_parse($db_conn_hv,$str);oci_execute($oci_pa);$n=oci_fetch_all($oci_pa, $kt);oci_free_statement($oci_pa);
							}
							
							if ($n>0) {
								// Tien trinh gui email
								$to  = $email; // note the comma
								//$to .= 'wez@example.com';
								
								// subject
								$subject = 'Phòng Đào Tạo Sau Đại Học: Khôi Phục Mật Khẩu';
								
								// message
								$message = '
								<html>
								<head>
								  <title>Phòng Đào Tạo Sau Đại Học: Khôi phục mật khẩu</title>
								</head>
								<body>
									<p><strong>Phòng Đào Tạo Sau Đại Học</strong></p><br/>
								  <p> &nbsp; &nbsp; Đây là thông tin tài khoản của bạn: <br/> + Người dùng: <b>' . $kt["USERNAME"][0] . '</b><br/> + Mật khẩu: <b>'
								  .$kt["PASSWORD"][0].'</b></p><br/>
								  <p>Xin vui lòng không trả lời thư này. </p>
								  <br/><p>Trân trọng kính chào!</p>
								</body>
								</html>
								';
								
								// To send HTML mail, the Content-type header must be set
								$headers  = 'MIME-Version: 1.0' . "\r\n";
								$headers .= 'Content-type: text/html; charset=utf-8' . "\r\n";
								
								// Additional headers
								$headers .= 'To: '.$kt["HO"][0].' '.$kt["TEN"][0].' <'.$email.'>' . "\r\n";
								$headers .= 'From: Phòng Đào Tạo Sau Đại Học <taint@hcmut.edu.vn>' . "\r\n";
								$headers .= 'Cc: ' . "\r\n";
								$headers .= 'Bcc: ' . "\r\n";
								
								/*file_put_contents("logs.txt", "----------------------------------------------\n
					". date("H:i:s d.m.Y")." $headers \n
					----------------------------------------------\n", FILE_APPEND);
					file_put_contents("logs.txt", "----------------------------------------------\n
					". date("H:i:s d.m.Y")." $message \n
					----------------------------------------------\n", FILE_APPEND);
					*/
								
								// Mail it
								if (mail($to,'=?UTF-8?B?'.base64_encode($subject).'?=', $message, $headers))
									$msgsuccess = " Mật khẩu mới đã được gửi đến địa chỉ email: </br>".$email;
								else
									$msgsuccess = "Quá trình email có lỗi.";
								// Ket thuc email
		
								//echo $msg;
								echo "<script>$('#recaptcha_div').hide()</script>";
							}
							else
								$msgerror = "Email này chưa đăng ký với hệ thống"; 
                                
                        } else {
                                # set the error code so that we can display it
                                $error = $resp->error;
                        }
                }
                //if (!$resp->is_valid)
                    echo recaptcha_get_html($publickey, $error);
                ?>
                      
                      
                      </td>
                    <td>&nbsp;</td>
                    </tr>
                  <tr>
                    <td height="24">&nbsp;</td>
                    <td width="21" height="24">&nbsp;</td>
                    <td width="199" height="24" align="right"><br /> <button id="btnGo">&nbsp;Ok</button></td>
                    <td height="24">&nbsp;</td>
                    </tr>
                  </table>
                
                </div> 
              <!-- end recaptcha_div  -->
              
              <?php if(isset($msgsuccess))
	   			echo "<p class='fontcontent' align='center'> &nbsp;&nbsp;".$msgsuccess."</p>"; ?>
              </td>
            </tr>
          
          
          <tr>
            <td  colspan="4" valign="bottom">
              <p id="tipForgot" class="validateTips" align="center"></p>
              <table width="100%" border="0" height="25" cellspacing="0" cellpadding="5" class=" cobg ui-corner-bl ui-corner-br"  >
                <tr>
                  <td valign="bottom" ></td>
                  </tr>
                </table>
              
              </td>
            </tr>
          </table>
        </td>
    </tr>
    
   
	<tr>
      <td colspan="3" align="center">&nbsp;</td>
    </tr>

  </table>
</form>
</div>

</div>

</body>
</html>

<script type="text/javascript">
//jQuery.ajax
//$(document).ready(function(){
$(function(){

$( "button" ).button();
 $( "#btnGo" ).button({ icons: {primary:'ui-icon ui-icon-check'} });
 
// Check validate fields Login
var jemail	= $("#email"),
	jRecapcha = $("#recaptcha_response_field"),
	allFieldsForgot = $([]).add(jemail),
	tipsForgot	= $("#tipForgot");
	
	// UpdateTips session = ttgv, detai, ctkh, sach, login
	function updateTips( t ) {
					tipsForgot
							.text( t )
							.addClass( "ui-state-highlight" );
						setTimeout(function() {
							tipsLogin.removeClass( "ui-state-highlight", 1500 );
						}, 1000 );
	}

	// Checklength
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
	
	// Check Regexp
	function checkRegexp( o, regexp, n ) {
		//alert('a');
		if ( !( regexp.test( o.val() ) ) ) {
			o.addClass( "ui-state-error" );
			o.focus();
			updateTips( n );
			return false;
		} else {
			return true;
		
		}
	}
// End of check validate
	
	$("#btnGo").click(function(e)
	//function validateform()
	{
		var bValid = true;
		
		allFieldsForgot.removeClass( "ui-state-error" );
		//alert(2);
		bValid = bValid && checkLength( jemail, "\"Email\"", 0, 100);
		
		bValid = bValid && checkRegexp( jemail, /^((([a-z]|\d|[!#\$%&'\*\+\-\/=\?\^_`{\|}~]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])+(\.([a-z]|\d|[!#\$%&'\*\+\-\/=\?\^_`{\|}~]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])+)*)|((\x22)((((\x20|\x09)*(\x0d\x0a))?(\x20|\x09)+)?(([\x01-\x08\x0b\x0c\x0e-\x1f\x7f]|\x21|[\x23-\x5b]|[\x5d-\x7e]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(\\([\x01-\x09\x0b\x0c\x0d-\x7f]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF]))))*(((\x20|\x09)*(\x0d\x0a))?(\x20|\x09)+)?(\x22)))@((([a-z]|\d|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(([a-z]|\d|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])*([a-z]|\d|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])))\.)+(([a-z]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(([a-z]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])*([a-z]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])))\.?$/i, "vd: pgs@hcmut.edu.vn" );
		bValid = bValid && checkLength( jRecapcha, "\"Recapcha\"", 0, 100);
		if (!bValid)
			e.preventDefault();
		
	});	// end $("#btnGo")

	
	//alert(Recaptcha.get_response());
 <?
	if (isset($msgerror))
	{
		echo "updateTips(\"".$msgerror. "\");";
	}
?>

});

window.onload = function() {
	var arrInputs = document.getElementsByTagName("input");
	for (var i = 0; i < arrInputs.length; i++) {
		var curInput = arrInputs[i];
		if (!curInput.type || curInput.type == "" || curInput.type == "text")
			HandlePlaceholder(curInput);
	}
};

function HandlePlaceholder(oTextbox) {
	if (typeof oTextbox.placeholder == "undefined") {
		var curPlaceholder = oTextbox.getAttribute("placeholder");
		if (curPlaceholder && curPlaceholder.length > 0) {
			oTextbox.value = curPlaceholder;
			oTextbox.setAttribute("old_color", oTextbox.style.color);
			oTextbox.style.color = "#c0c0c0";
			oTextbox.onfocus = function() {
				this.style.color = this.getAttribute("old_color");
				if (this.value === curPlaceholder)
					this.value = "";
			};
			oTextbox.onblur = function() {
				if (this.value === "") {
					this.style.color = "#c0c0c0";
					this.value = curPlaceholder;
				}
			};
		}
	}
};

$('input[placeholder],textarea[placeholder]').placeholder();

</script>

<?php 
if (isset ($db_conn_gv))
	oci_close($db_conn_gv);
if (isset ($db_conn_hv))
	oci_close($db_conn_hv);
?>