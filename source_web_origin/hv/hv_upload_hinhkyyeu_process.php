<?php
if (isset($_REQUEST["hisid"])){
	session_id($_REQUEST["hisid"]);
	session_start();
}
if (!isset($_SESSION['uidloginhv'])){
	die('Đã hết phiên làm việc'); 
}

include "libs/connect.php";

$mahv = base64_decode($_SESSION["mahv"]);

$strsql="SELECT dot_cap_bang('$mahv') dot_cap_bang FROM dual";
$oci_pa = oci_parse($db_conn,$strsql);oci_execute($oci_pa);$result=oci_fetch_all($oci_pa, $kt);oci_free_statement($oci_pa);
$dotcapbang = $kt["DOT_CAP_BANG"][0];

if ($dotcapbang == '')
{
	$strsql="SELECT value FROM config WHERE name='DOT_CAP_BANG'";
	$oci_pa = oci_parse($db_conn,$strsql);oci_execute($oci_pa);$result=oci_fetch_all($oci_pa, $kt);oci_free_statement($oci_pa);
	$dotcapbang = $kt["VALUE"][0];
}

$type = $_REQUEST['w'];

if ($type=='uploadfile')
{
	$namefile = "$mahv.jpg"; //basename($_FILES['userfile']['name']);
	$uploaddir = "./hinhkyyeu/$dotcapbang/";
	$uploadfile = $uploaddir . $namefile; //basename($_FILES['userfile']['name']);
	
	if (!mkdir($uploaddir, 0, true)) {
		//	die('Failed to create folders...');
	}
	
	//echo $uploadfile;

	//echo '<pre>';
	if (move_uploaded_file($_FILES['userfile']['tmp_name'], $uploadfile)) 
	{
		//echo "File is valid, and was successfully uploaded.\n";
		//echo "<img src='hinhkyyeu/$dotcapbang/$namefile' width=113 height=170 class='ui-widget-content ui-corner-all'/>";
		echo "hinhkyyeu/$dotcapbang/$namefile";
	} 
	else 
	{
		echo "error";
	}

	//echo 'Here is some more debugging info:';
	//print_r($_FILES);
	//print "</pre>";
}

if (isset ($db_conn))
	oci_close($db_conn);
?>