<?php
error_reporting(0);
$db = "(DESCRIPTION=(ADDRESS_LIST = (ADDRESS = (PROTOCOL = TCP)(HOST = 172.28.40.168)(PORT = 1521)))(CONNECT_DATA=(SID=SDHbk)))";
$db_conn = oci_connect("csdl", "LNMINH",$db,"UTF8");
$config_name_db_link = "";
//$config_name_db_link = "_249";
//$config_name_db_link = "_248";
?>
