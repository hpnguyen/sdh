﻿<?php
error_reporting(0);
$db = "(DESCRIPTION=(ADDRESS_LIST = (ADDRESS = (PROTOCOL = TCP)(HOST = 172.28.40.254)(PORT = 1521)))(CONNECT_DATA=(SID=SDHbk)))";
$db_conn = oci_connect("csdl", "lnminh",$db,"UTF8");
?>
