<?php
function multi_attach_mail($to, $files, $sendermail){
    // email fields: to, from, subject, and so on
    $from = "Files attach <".$sendermail.">";
    $subject = "Testing Attachment file";//date("d.M H:i")." F=".count($files);
    $message = date("Y.m.d H:i:s")."\n".count($files)." attachments";
    $headers = "From: $from";
 
    // boundary
    $semi_rand = md5(time());
    $mime_boundary = "==Multipart_Boundary_x{$semi_rand}x";
	//$mime_boundary = "------=MIME_BOUNDARY_MESSAGE_PARTS";
	
    // headers for attachment alternative
    $headers .= "\nMIME-Version: 1.0\r\n" . 
	"Content-Type: multipart/mixed; boundary=\"$mime_boundary\"";
	$headers .= "\r\n\r\n";
	
	//define the body of the message.
	//ob_start(); //Turn on output buffering 
	
    // multipart boundary
    $message = "$mime_boundary ".chr(13).chr(10) . 
	"Content-Type: text/html charset=\"utf-8\" " .chr(13).chr(10).
	"Hello Email "  .chr(13).chr(10);
 
    // preparing attachments
    for($i=0;$i<count($files);$i++)
	{
        if(is_file($files[$i]))
		{
			$message .= "$mime_boundary ".chr(13).chr(10);
            $handle =    fopen($files[$i],"rb");
			$data =    fread($handle,filesize($files[$i]));
            fclose($handle);
            $data = chunk_split(base64_encode($data));
			//$data = chunk_split(base64_encode(file_get_contents($files[$i])));
            $message .= "Content-Type: application/octet-stream; name=\"".basename($files[$i])."\" ".chr(13).chr(10)
            //."Content-Description: ".basename($files[$i]).chr(13).chr(10)
            //."Content-Disposition: attachment;" . " filename=\"".basename($files[$i])."\"; size=".filesize($files[$i]).";".chr(13).chr(10)
            ."Content-Transfer-Encoding: base64 ".chr(13).chr(10).chr(13).chr(10)
			.$data." ".chr(13).chr(10).chr(13).chr(10);
			//echo $data;
        }
    }
    $message .= "$mime_boundary";
	
	file_put_contents("logsemail.txt", $headers.$message);
	//echo $headers.$message;

    $returnpath = "-f" . $sendermail;
    $ok = mail($to, '=?UTF-8?B?'.base64_encode($subject).'?=', $message,  $headers, $returnpath);
	
	//mail($to,'=?UTF-8?B?'.base64_encode($subject).'?=', $message, $headers);	
	
    if($ok)
	{ 
		return 'Ok'; 
	} 
	else 
	{ 
		return 0; 
	}
}
$files = array('D:/www/portal/attach_mailer.zip');
//echo $files[0];

include "libs/connect.php";

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
	  <title>Phòng Đào Tạo Sau Đại Học - ĐHBK Tp.HCM: Thông tin Đăng Ký Môn Học</title>
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
	$headers .= 'From: Phòng Đào Tạo Sau Đại Học <no_reply@hcmut.edu.vn>' . "\r\n";
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

<?php
$sqlstr="
	SELECT id_email, email,subject,content_html,sent 
	FROM OUTBOX
	WHERE sent is NULL
	AND id_email>44
";

$stmt = oci_parse($db_conn, $sqlstr);
oci_execute($stmt);
$n = oci_fetch_all($stmt, $resDM);
oci_free_statement($stmt);

date_default_timezone_set('Asia/Ho_Chi_Minh');
$time =date("d/m/Y H:i:s");
//$time = date("H:i:s"); // DD/MM/YYYY HH:MI:SS
echo "Bat dau gui email luc: $time";
$listemailsent="";
$listemailnotsent="";
$countsent=0;
$countnotsent=0;

set_time_limit(21600); // 6 hours
/*
for ($i = 0; $i < $n; $i++)
{
	if (sendemail($resDM["EMAIL"][$i], $resDM["EMAIL"][$i], $resDM["SUBJECT"][$i], str_replace('"', "'",$resDM["CONTENT_HTML"][$i])))
	{
		$listemailsent.=$resDM["EMAIL"][$i].", ";
		$countsent+=1;
		$stmt = oci_parse($db_conn, "UPDATE outbox SET sent=to_date('$time', 'DD/MM/YYYY HH24:MI:SS') WHERE id_email={$resDM["ID_EMAIL"][$i]}");
		oci_execute($stmt);
		oci_free_statement($stmt);
	}
	else
	{
		$listemailnotsent.=$resDM["EMAIL"][$i].", ";
		$countnotsent+=1;
	}
}
*/
if (sendemail("taint@hcmut.edu.vn", "Ngo Trung Tai", "Testing", "Content Testing"))
	echo "Da gui mail testing";
else
	echo "Khong the gui mail testing";

$time =date("d/m/Y H:i:s");
echo "<br/>Ket thuc qua trinh gui email luc: $time";

echo "<p>Email da gui: $countsent email</p>";
echo "<p><b>Danh sach email khong gui duoc: $countnotsent email</b> <br/>$listemailnotsent</p>";
?>