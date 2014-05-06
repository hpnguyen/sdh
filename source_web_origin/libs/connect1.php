<?php
//$db_config_real_ip = "172.28.40.250";
$db_config_real_ip = "127.0.0.1";
$db = "(DESCRIPTION=(ADDRESS_LIST = (ADDRESS = (PROTOCOL = TCP)(HOST = ".$db_config_real_ip.")(PORT = 1521)))(CONNECT_DATA=(SID=SDHbk)))";
$db_conn_hv = OCILogon("webweb", "",$db);
?>
