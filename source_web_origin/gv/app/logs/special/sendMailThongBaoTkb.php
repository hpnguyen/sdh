<?php
date_default_timezone_set('Asia/Ho_Chi_Minh');
echo "[".date("d-m-Y H:i:s")."]\n";
//set POST variables
$url ='http://sdh.localhost/gv/front.php/admin/mail/tbtkb';
//open connection
$ch = curl_init();
//set the url, number of POST vars, POST data
curl_setopt($ch, CURLOPT_URL, $url);
curl_setopt($ch,CURLOPT_POST,0);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
//execute post
$result = curl_exec($ch);
echo ($result == "" ? "" : $result."\n");
//close connection
curl_close($ch);
echo "\n";