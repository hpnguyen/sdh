<?php
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
	  <title>Phòng Đào Tạo Sau Đại Học - ĐHBK Tp.HCM</title>
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
?>