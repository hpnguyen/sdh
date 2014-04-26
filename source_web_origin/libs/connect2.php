<?php
$db = "(DESCRIPTION=(ADDRESS_LIST = (ADDRESS = (PROTOCOL = TCP)(HOST = 172.28.40.168)(PORT = 1521)))(CONNECT_DATA=(SID=SDHbk)))";
$db_conn_gv = oci_connect("csdl", "lnminh",$db);
?>
