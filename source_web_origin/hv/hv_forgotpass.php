<!DOCTYPE HTML>

  <link href="../css/start/jquery-ui-1.8.18.custom.css" rel="stylesheet" type="text/css"/>
  <link href="css/pgs.css" rel="stylesheet" type="text/css"/>
  <script src="../js/jquery-1.8.0.min.js"></script>
  <script src="../js/jquery-ui-1.8.23.custom.min.js"></script>
  <script src="../js/jquery.placeholder-1.1.9.js"></script>  

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Khôi Phục Mật Khẩu - Phòng Đào Tạo Sau Đại Học</title>
</head>

<body style="font-family:Arial, Helvetica, sans-serif; ">
<!--
<?php 
	//error_reporting(1);
	$sid = $_REQUEST["hisid"];
	include "libs/connect.php";
	$search = array("'","\"");
	$replace = array("\\'","&quot;");
// session_is_registered('uidlogin')==false) // Neu chua dang nhap thi xuat form login
?>
-->
<div id="header">
<div id="header-content">

	<div id=header_col1><a href="/hv"><h2>Đại Học Bách Khoa Tp.HCM<br/>Phòng Đào Tạo Sau Đại Học</h2></a></div>
    <div id=header_col2></div>
    <div id=header_col3></div>

</div>
</div> <!-- End header -->

<div id="header-line"></div>

<div align="center">
<form id="form_forgotpass" name="form_forgotpass" method="post" action="hv_forgotpass.php">

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
					theme : 'white'
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
                      <input style="width:235pt;" placeholder="email đẵ đăng ký..." name="email" type="text" class="text ui-widget-content ui-corner-all" id="email" size="48" value="<?php echo $_REQUEST["email"] ?>" />
                      </td>
                    <td width="45"></td>
                    </tr>
                  <tr>
                    <td height="24">&nbsp;</td>
                    <td height="20" colspan="2" align="center"  valign="top">
                      
                      <?php
    
                require_once('libs/recaptchalib.php');
                
                // Get a key from https://www.google.com/recaptcha/admin/create
                $publickey = "6LcDrc8SAAAAAOehS2lVAVXYUefcRz94cIJK6UbZ";
                $privatekey = "6LcDrc8SAAAAACyVrTs8yuqB_vHagESgu29bKSoF";
                
                # the response from reCAPTCHA
                $resp = null;
                # the error code from reCAPTCHA, if any
                $error = null;
                
                # was there a reCAPTCHA response?
                if ($_POST["recaptcha_response_field"]) {
                        $resp = recaptcha_check_answer ($privatekey,
                                                        $_SERVER["REMOTE_ADDR"],
                                                        $_POST["recaptcha_challenge_field"],
                                                        $_POST["recaptcha_response_field"]);
                
                        if ($resp->is_valid) {
							$email = trim($_POST["email"]);//503207104@stu.hcmut.edu.vn
							$emailsplit = explode("@", $email);
							
							$ma_hv = substr($emailsplit[0],1,strlen($emailsplit[0])-1);
							$ma_email_pgs = substr($emailsplit[0],0,1);
							
							//echo $ma_hv . ' C:' . $ma_email_pgs . ' D:'.$emailsplit[1] ;
							if ($ma_email_pgs!='5' or $emailsplit[1]!='stu.hcmut.edu.vn'){
								$msgerror = "Lưu ý: Bạn phải sử dụng email @stu.hcmut.edu.vn trường cấp"; 
							}
							if (!isset($msgerror)){
								$str="select pass, username from nguoi_dung 
								where username='".str_replace("'", "''",$ma_hv)."'";
								$oci_pa = ociparse($db_conn,$str); //gan cau query
								oci_execute($oci_pa);
								$n=ocifetchstatement($oci_pa, $kt);//lay du lieu  
								ocifreestatement($oci_pa);
								
								if ($n>0) {
									
									// Tien trinh gui email
									$to  = $email; // note the comma
									//$to = 'nttvi@hcmut.edu.vn';
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
										<p><strong>Phòng Đào Tạo Sau Đại Học</strong></p>
										<p> Bạn đã sử dụng chức năng khôi phục mật khẩu <br/>
											Đây là mật khẩu của bạn: "'.$kt["PASS"][0].'"<br/>
										</p>
										<p>Xin vui lòng không trả lời email này. <br/>
										Trân trọng kính chào!</p>
									</body>
									</html>
									';
									
									// To send HTML mail, the Content-type header must be set
									$headers  = 'MIME-Version: 1.0' . "\r\n";
									$headers .= 'Content-type: text/html; charset=utf-8' . "\r\n";
									
									// Additional headers
									$headers .= 'To: '.$kt["HO"][0].' '.$kt["TEN"][0].' <'.$email.'>' . "\r\n";
									$headers .= 'From: Phòng Đào Tạo Sau Đại Học <no_reply@hcmut.edu.vn>' . "\r\n";
									$headers .= 'Cc: ' . "\r\n";
									$headers .= 'Bcc: ' . "\r\n";
									
									// Mail it
									
									if (mail($to,'=?UTF-8?B?'.base64_encode($subject).'?=', $message, $headers))
										$msgsuccess = " Mật khẩu mới đã được gửi đến địa chỉ email: </br>".$email;
									else
										$msgsuccess = " Quá trình email có lỗi.";
									// Ket thuc email
									
									//echo $msg;
									echo "<script>$('#recaptcha_div').hide()</script>";
								}
								else
									$msgerror = "Email này chưa đăng ký với hệ thống"; 
							}
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
      <td width="58">&nbsp;</td>
      <td width="146">&nbsp;</td>
      <td width="90">&nbsp;</td>
    </tr>
    
    
	<tr>
      <td colspan="3" align="center">&nbsp;</td>
    </tr>

  </table>
</form>
</div>

<div id="footer">
<div id='footer_content'>
<div style="float:left;margin-right:0px;margin-top:15px;"><a href='logingv_.php'><img src="images/logoBK.png" width="32" height="32"/></a></div>
<div style="float:left;font-size: 80%">
			<ul>
				<li >Đăng Ký Môn Học </li>
				<li >Thời Khóa Biểu Ngành</li>
				<li >Thời Khóa Biểu Cá Nhân</li>
				
				
			</ul>	
</div>

<div style="float:left;font-size: 80%">
			<ul>
				<li >Kết Quả Đóng Học Phí</li>
				<li >Lịch Thi Ngành</li>
				<li >Lịch Thi Cá Nhân</li>
				
			</ul>	
</div>

<div style="float:left;font-size: 80%">
			<ul>
				
				<li >Tra Cứu Điểm</li>
				
			</ul>	
</div>

<div style="float:right;margin-right:10px; margin-top:5px;font-size: 80%">Hệ thống được phát triển<br/>bởi nhóm IT PGS 2012
<p align='center'>Dùng tốt nhất với<br/>
<img src="icons/Firefox-icon32.png" width="16" height="16"/> &nbsp; <img src="icons/Chrome-icon32.png" width="16" height="16"/> </p>
</div>
</div>

</div>


</body>
</html>

<script type="text/javascript">
//jQuery.ajax
//$(document).ready(function(){
$(function(){
	//$("input:button").button();
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
</script>

<?php 
if (isset ($db_conn))
	oci_close($db_conn);
?>