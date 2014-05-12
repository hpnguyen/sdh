<?php
$pathRoot = "/home/admin/Working/cronjobs";
$config = array(
'host' => '172.28.40.168',
'port' => 1521,
'sid' => 'SDHbk',
'user' => 'csdl',
'pass' => 'LNMINH',
'charset' => 'UTF8',
'mail_user' => 'sdh@hcmut.edu.vn',
'mail_pass' => '12021971ltchon',
'mail_host' => 'smtp.gmail.com',
'mail_port' => 465, //587
'mail_from' => 'sdh@hcmut.edu.vn',
'mail_name_from' => 'Phòng đào tạo sau đại học trường ĐH Bách Khoa TPHCM',
'mail_tkb_title' => 'Thông báo đã có thời khóa biểu giảng dạy',
'mail_tkb_cc' => 'nttvi@hcmut.edu.vn',
'mail_valid_ip_array' => '172.28.40.248,172.28.40.250'
);
$rootRequestUrl = "http://sdh.localhost/gv";
$urlGetData = $rootRequestUrl."/front.php/admin/mail/tkbgetemail";
$urlUpdateData = $rootRequestUrl."/front.php/admin/mail/tkbsendmaildone/id";

date_default_timezone_set('Asia/Ho_Chi_Minh');

echo "[".date("d-m-Y H:i:s")."]\n";
//SMTP needs accurate times, and the PHP time zone MUST be set
//This should be done in your php.ini, but this is how to do it if you don't have access to that
require '/home/hpnguyen/Working/svn_repository_source/gv/app/libs/PHPMailer/PHPMailerAutoload.php';

	//Create a new PHPMailer instance
	$mail = new PHPMailer();
	//Tell PHPMailer to use SMTP
	$mail->isSMTP();
				
	//Enable SMTP debugging
	// 0 = off (for production use)
	// 1 = client messages
	// 2 = client and server messages
	$mail->SMTPDebug = 2;
	//Ask for HTML-friendly debug output
	$mail->Debugoutput = 'html';
	
	//Set the hostname of the mail server
	$mail->Host = $config['mail_host'];
	//Set the SMTP port number - 587 for authenticated TLS, a.k.a. RFC4409 SMTP submission
	$mail->Port = $config['mail_port'];
	//Set the encryption system to use - ssl (deprecated) or tls
	//$mail->SMTPSecure = 'tls';
	$mail->SMTPSecure = 'ssl'; // secure transfer enabled REQUIRED for GMail
	//Whether to use SMTP authentication
	$mail->SMTPAuth = true;
	$mail->Username = $config['mail_user'];
	$mail->Password = $config['mail_pass'];
	//Set who the message is to be sent from
	$mail->setFrom($config['mail_from'], $config['mail_name_from']);
	//Set the subject line
	$mail->Subject = $config['mail_tkb_title'];
	
	//Add cc mail
	$mail->AddCC('hpnguyen@hcmut.edu.vn', null);
	
	//Read an HTML message body from an external file, convert referenced images to embedded,
	//convert HTML into a basic plain-text alternative body
	$mail->IsHTML(true); // set email format to HTML
	$mail->CharSet = 'utf-8'; // set charset to utf8
	$mail->Body = file_get_contents('/home/hpnguyen/Working/svn_repository_source/gv/app/template/view/mail/tkb.php'); // HTML -> PHP!
	
	//Set who the message is to be sent to
	$data = explode(",",$result);
	$rowID = $data[0];
	 
	$mail->addAddress('vitruong@cse.hcmut.edu.vn', null);
	//send the message, check for errors
	if (!$mail->send()) {
		echo "Mailer Error: " . $mail->ErrorInfo;
	} else {
		echo "Message sent!";
	}
echo "\n";