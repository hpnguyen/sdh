<!--
<?php
if (isset($_REQUEST["hisid"])){
	session_id($_REQUEST["hisid"]);
	session_start();
}
if (!isset($_SESSION['uidloginhv'])){
	die('Truy cập bất hợp pháp'); 
}

//include "libs/connect.php";

$usr = base64_decode($_SESSION["uidloginhv"]);

$sqlstr="
	SELECT (decode(ma_bac, 'TS', 'NCS ', '') || h.ho || ' ' || h.ten) ho_ten, h.ma_hoc_vien, h.ma_nganh, n.ten_nganh, h.khoa
	FROM hoc_vien h, nganh n 
	WHERE h.ma_hoc_vien='$usr' AND h.ma_nganh=n.ma_nganh
";

$stmt = oci_parse($db_conn, $sqlstr);
oci_execute($stmt);
$n = oci_fetch_all($stmt, $result);
oci_free_statement($stmt);

$_SESSION["mahv"]=base64_encode($result['MA_HOC_VIEN'][0]);
$_SESSION["manganh"]=base64_encode($result['MA_NGANH'][0]);
$_SESSION["khoa"]=base64_encode($result['KHOA'][0]);

?>-->
<div id="header">
<div id="header-content">

	<div id=header_col1><a href="http://www.pgs.hcmut.edu.vn"><h2>Đại Học Bách Khoa Tp.HCM<br/>Cổng Thông Tin Đào Tạo Sau Đại Học</h2></a></div>
    <div id=header_col2></div>
    <div id=header_col3>
		<div align=right>Xin chào <b><?php echo $result['HO_TEN'][0]; ?></b> | <a href="login.php?hisid=<?php echo $_REQUEST["hisid"];?>&cat=signout">Sign out</a></div>
		<div style='margin-top:10px;' align=left>Ngành <b><?php echo $result['TEN_NGANH'][0]; ?></b></div>
	</div>

</div>
</div> <!-- End header -->

<div id="header-line"></div>

<?php 
//if (isset ($db_conn))
	//oci_close($db_conn);
?>