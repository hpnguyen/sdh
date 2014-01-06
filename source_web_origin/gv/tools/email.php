<?php
	// Cu phap php.exe -f email.php to="email1;email2" file="file1;file2"
	// email bk: bkdb.sdh@gmail.com, bkdb.sdh168@ymail.com, T.n.2..T.n....
	
	date_default_timezone_set('Asia/Ho_Chi_Minh');
	$today =date("d.m.Y"); $time = date("H:i:s");
	
	//var_dump($argv);
	if (!isset($argv[1]) || !isset($argv[2]))
	{
		$content="-Cu phap: php.exe -f email.php to=\"email1;email2\" file=\"file1;file2\"";
		$content.="\n-Chu y: File trong tham so f phai nam trong thu muc D:\Exp";
		$content.="\n-Email bkup: bkdb.sdh@gmail.com, bkdb.sdh168@ymail.com, T.n.2..T.n....";
		die($content);
	}
	
	parse_str($argv[1]); // => $to = email
	parse_str($argv[2]); // => $file = filename
	
	if (isset($to))
	{
		$tos = explode(';', $to);
	}
	if (isset($file))
	{
		$files = explode(';', $file);
	}
	
	//var_dump($to);
	
	$from = "Administrator SDH - DHBK <sdh@hcmut.edu.vn>";
	$subject = "$time $today Backup Database SDH - DHBK TpHCM";

	$separator = md5(time());

	// carriage return type (we use a PHP end of line constant)
	$eol = PHP_EOL;

	// attachment name
	$folder = "D:/Exp/";
	
	// main header
	$headers  = "To: $to".$eol;
	$headers .= "From: ".$from.$eol;
	$headers .= "Cc:" . $eol;
	$headers .= "Bcc: " . $eol;
	$headers .= "MIME-Version: 1.0".$eol; 
	$headers .= "Content-Type: multipart/mixed; boundary=\"".$separator."\"";

	// no more headers after this, we start the body! //

	$body = "--".$separator.$eol;
	$body .= "Content-Transfer-Encoding: 7bit".$eol.$eol;
	$body .= "Backup Database SDH - DHBK TpHCM at $time $today with a file $file attached".$eol;

	// message
	//$body .= "--".$separator.$eol;
	//$body .= "Content-Type: text/html; charset=utf-8".$eol;
	//$body .= "Content-Transfer-Encoding: 8bit".$eol.$eol;
	//$body .= "<b>Thôi rồi lợm ơi!</b>".$eol;
	$filenames = "";
	foreach ($files as $file) 
	{
		// get file attachment
		$attachment = chunk_split(base64_encode(file_get_contents($folder.$file)));
		$filenames.="$file; ";
		// add attachment
		$body .= "--".$separator.$eol;
		$body .= "Content-Type: application/octet-stream; name=\"".$file."\"".$eol; 
		$body .= "Content-Transfer-Encoding: base64".$eol;
		$body .= "Content-Disposition: attachment".$eol.$eol;
		$body .= $attachment.$eol;
	}
	$body .= "--".$separator."--";
	$filenames = substr($filenames, 0, strlen($filenames)-2);
	
	// send message to
	foreach ($tos as $to) 
	{
		if (mail($to, '=?UTF-8?B?'.base64_encode($subject).'?=', $body, $headers)) 
		{
			echo "mail send ($filenames attached) ... to $to OK\n";
		} else {
			echo "mail send ... ERROR\n";
		}
	}
?>