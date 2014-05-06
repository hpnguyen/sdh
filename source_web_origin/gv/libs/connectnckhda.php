<?php
//error_reporting(0);
$db_khcn = "(DESCRIPTION=(ADDRESS_LIST = (ADDRESS = (PROTOCOL = TCP)(HOST = 172.28.40.168)(PORT = 1521)))(CONNECT_DATA=(SID=sdhbk)))";
$db_conn_khcn = oci_connect("nckhda", "",$db_khcn,"UTF8");
?>
