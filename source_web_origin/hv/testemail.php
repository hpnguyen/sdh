<?php
ini_set('display_errors', '1');

include("libs\connect.php"); 

function detect_ie()
{
    if (isset($_SERVER['HTTP_USER_AGENT']) && 
    (strpos($_SERVER['HTTP_USER_AGENT'], 'MSIE') !== false))
        return true;
    else
        return false;
}

function vn_str_filter ($str){
        $unicode = array(
            'a'=>'á|à|ả|ã|ạ|ă|ắ|ặ|ằ|ẳ|ẵ|â|ấ|ầ|ẩ|ẫ|ậ',
            'd'=>'đ',
            'e'=>'é|è|ẻ|ẽ|ẹ|ê|ế|ề|ể|ễ|ệ',
            'i'=>'í|ì|ỉ|ĩ|ị',
            'o'=>'ó|ò|ỏ|õ|ọ|ô|ố|ồ|ổ|ỗ|ộ|ơ|ớ|ờ|ở|ỡ|ợ',
            'u'=>'ú|ù|ủ|ũ|ụ|ư|ứ|ừ|ử|ữ|ự',
            'y'=>'ý|ỳ|ỷ|ỹ|ỵ',
			'A'=>'Á|À|Ả|Ã|Ạ|Ă|Ắ|Ặ|Ằ|Ẳ|Ẵ|Â|Ấ|Ầ|Ẩ|Ẫ|Ậ',
            'D'=>'Đ',
            'E'=>'É|È|Ẻ|Ẽ|Ẹ|Ê|Ế|Ề|Ể|Ễ|Ệ',
            'I'=>'Í|Ì|Ỉ|Ĩ|Ị',
            'O'=>'Ó|Ò|Ỏ|Õ|Ọ|Ô|Ố|Ồ|Ổ|Ỗ|Ộ|Ơ|Ớ|Ờ|Ở|Ỡ|Ợ',
            'U'=>'Ú|Ù|Ủ|Ũ|Ụ|Ư|Ứ|Ừ|Ử|Ữ|Ự',
            'Y'=>'Ý|Ỳ|Ỷ|Ỹ|Ỵ',
        );    
       foreach($unicode as $nonUnicode=>$uni){
            $str = preg_replace("/($uni)/i", $nonUnicode, $str);
       }
		return $str;
}

function sendemail($emailaddress, $name, $subject, $content){
	// Tien trinh gui email
	$to  = $emailaddress; // note the comma
	//$to .= 'wez@example.com';
	
	// subject
	$subject = $subject;
	
	// message
	$message = "
	<html>
	<head>
	  <title>Phòng Đào Tạo Sau Đại Học - ĐHBK Tp.HCM: Thông tin Đăng Ký Môn Học Ôn Tập</title>
	</head>
	<body>
		<p><strong>Đại học Bách Khoa TP.HCM <br/>Phòng Đào Tạo Sau Đại Học</strong></p>
		<p>$content</p>
		<p>Email này được gửi từ hệ thống email tự động. <br/>Xin vui lòng không trả lời email này. </p>
		<p>Trân trọng kính chào!</p>
	</body>
	</html>";
	
	// To send HTML mail, the Content-type header must be set
	$headers  = 'MIME-Version: 1.0' . "\r\n";
	$headers .= 'Content-type: text/html; charset=utf-8' . "\r\n";
	
	// Additional headers
	$headers .= "To: $name <$emailaddress>\r\n";
	$headers .= 'From: Phòng Đào Tạo Sau Đại Học <noreply@hcmut.edu.vn>' . "\r\n";
	$headers .= 'Cc: ' . "\r\n";
	$headers .= 'Bcc: ' . "\r\n";
	
	// Mail it
	/*if (mail($to,'=?UTF-8?B?'.base64_encode($subject).'?=', $message, $headers))
		$msgsuccess = " Mật khẩu mới đã được gửi đến địa chỉ email: </br>".$emailaddress;
	else
		$msgsuccess = " Quá trình email có lỗi.";*/
	return mail($to,'=?UTF-8?B?'.base64_encode($subject).'?=', $message, $headers);	
}
date_default_timezone_set('Asia/Ho_Chi_Minh');


?>
<META http-equiv=Content-Type content='text/html; charset=utf-8'>
<HTML>
<TITLE>Phòng đào tạo Sau đại học - ÐH Bách khoa TP.HCM</TITLE> 

<link href="../css/start/jquery-ui-1.8.18.custom.css" rel="stylesheet" type="text/css"/>
<link href="css/pgs.css" rel="stylesheet" type="text/css" />

<style type="text/css" media="screen">
</style>

<script src="../js/jquery-1.8.0.min.js"></script>
<script src="../js/jquery-ui-1.8.23.custom.min.js"></script>
<script src="../js/jquery.maskedinput-1.3.min.js"></script>

<script type="text/JavaScript">
</script>



<body style="font-family:Arial,Helvetica,sans-serif; font-size:60%">

<noscript>
  <table style="width:100%; margin-top:15px;">
  <tr>
  <td align=center>
  <span style="color: red; width:100%; font-size:12pt;">Để trang này hoạt động bạn cần phải kích hoạt JavaScript</span>
  </td>
  </tr>
  </table>
  <style>div { display:none; }</style>
</noscript>

<?php
	if (sendemail("trung_tai@yahoo.com", "ngo trung tai" , "Phieu dang ky Mon Hoc On Tap", "Testing email"))
		echo "email thanh cong";
	else
		echo "email that bai";
?>
</body>
</html>