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
echo multi_attach_mail('taint@hcmut.edu.vn', $files, 'taint@hcmut.edu.vn');
?>