<!--
<?php
if (isset($_REQUEST["hisid"])){
	session_id($_REQUEST["hisid"]);
	session_start();
}
if (!isset($_SESSION['uidloginPortal'])){
	die('Truy cập bất hợp pháp'); 
}

include "libs/connect.php";
$usr = strtoupper(str_replace("'","''",base64_decode($_SESSION["uidloginPortal"])));
$macb = $_SESSION["macb"];

$sqlstr="select ten_khoa from khoa where ma_khoa='" . base64_decode($_SESSION['makhoa'])."'"; 

$sqlstr="SELECT (n.ho || ' ' || n.ten) HO_TEN, k.ten_khoa, GET_THANH_VIEN('$macb') chuc_danh
FROM nhan_su n, khoa k
WHERE upper(n.username)='$usr'
AND n.fk_ma_khoa = k.ma_khoa";

$stmt = oci_parse($db_conn, $sqlstr);
oci_execute($stmt);
$n = oci_fetch_all($stmt, $result);
oci_free_statement($stmt);

if ($result['CHUC_DANH'][0]!='')
	$hoten = $result['CHUC_DANH'][0];
else
	$hoten = $result['HO_TEN'][0];
?>-->
<div id="header">
<div id="header-content">

	<div id=header_col1><a href="http://www.pgs.hcmut.edu.vn/"><h2>Đại Học Bách Khoa Tp.HCM<br/>Cổng Thông Tin Cán Bộ Bách Khoa</h2></a></div>
    <div id=header_col2></div>
    <div id=header_col3 >
		<div align=right>Xin chào <b><?php echo $hoten; ?></b> | <a href="login.php?hisid=<?php echo $_REQUEST["hisid"];?>&cat=signout">Sign out</a></div>
		<div style="margin-top:10px" align=left>Khoa <b><?php echo $result['TEN_KHOA'][0]; ?></b></div>
	</div>

</div>
</div> <!-- End header -->

<div id="header-line"></div>

<?php 
if (isset ($db_conn))
	oci_close($db_conn);
?>