<?php

/**
 *
 */
class HelperFunctionsMail {

	function __construct() {

	}
	
	public function sendMail($subject, $message, $to, $cc = null, $from = null, $attach = null, $type = 0, $debug = false){
		$config = Helper::getHelper('functions/util')->getDbFileConfig();
		//Create a new PHPMailer instance
		$mail = new PHPMailer();
		//Tell PHPMailer to use SMTP
		$mail->isSMTP();
			
		if ($debug == true) {
			//Enable SMTP debugging
			// 0 = off (for production use)
			// 1 = client messages
			// 2 = client and server messages
			$mail->SMTPDebug = 2;
			//Ask for HTML-friendly debug output
			$mail->Debugoutput = 'html';
		}
		
		//Set the hostname of the mail server
		$mail->Host = $config['mail_host'];
		//Set the SMTP port number - 587 for authenticated TLS, a.k.a. RFC4409 SMTP submission
		$mail->Port = $config['mail_port'];
		//Set the encryption system to use - ssl (deprecated) or tls
		$mail->SMTPSecure = 'tls';
		//Whether to use SMTP authentication
		$mail->SMTPAuth = true;
		$mail->Username = $config['mail_user'];
		$mail->Password = $config['mail_pass'];
		//Set who the message is to be sent from
		if($from == null) {
			$mail->setFrom($config['mail_from'], $config['mail_name_from']);
		}else{
			$mail->setFrom($from['mail_from'], $from['mail_name_from']);
		}
		
		//Set an alternative reply-to address
		if (isset($config['mail_reply']) && isset($config['mail_name_reply']) && ! empty($config['mail_reply'])){
			$mail->addReplyTo($config['mail_reply'], $config['mail_name_reply']);
		}
		//Set who the message is to be sent to
		foreach ($to as $email) {
			$name = null;
			if (isset($email[1])){
				$name = $email[1];
			}
			$mail->addAddress($email[0], $name);
		}
		//Set who the message is to be cc
		if ($cc != null){
			foreach ($cc as $email) {
				$name = null;
				if (isset($email[1])){
					$name = $email[1];
				}
				$mail->AddCC($email[0], $name);
			}
		}
		
		//Set the subject line
		$mail->Subject = $subject;
		//Read an HTML message body from an external file, convert referenced images to embedded,
		//convert HTML into a basic plain-text alternative body
		if ($type != 0){
			$mail->msgHTML($message, ROOT_DIR.'app/libs/PHPMailer/examples');
		}else{
			$mail->IsHTML(true); // set email format to HTML
			$mail->CharSet = 'utf8'; // set charset to utf8
			$mail->Body = $message; // HTML -> PHP!
		}
		
		//Replace the plain text body with one created manually
		//$mail->AltBody = 'This is a plain-text message body';
		//Attach an image file
		if ($attach != null){
			$mail->addAttachment($attach);
		}
		
		//send the message, check for errors
		if (!$mail->send()) {
			return array('status' => 0, 'message' => "Mailer Error: " . $mail->ErrorInfo);
		} else {
			return array('status' => 1, 'message' => "Message sent!");
		}
	}
}
