<?php
error_reporting(0);
$db = "(DESCRIPTION=(ADDRESS_LIST = (ADDRESS = (PROTOCOL = TCP)(HOST = 172.28.40.250)(PORT = 1521)))(CONNECT_DATA=(SID=SDHbk)))";
$db_conn = oci_connect("webweb", "weB210810",$db,"UTF8");
 
$id_title = $_GET["id"];
?>
