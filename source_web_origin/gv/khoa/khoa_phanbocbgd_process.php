<?php
if (isset($_REQUEST["hisid"])){
	session_id($_REQUEST["hisid"]);
	session_start();
}
if (!isset($_SESSION['uidloginPortal'])){
	die('Truy cập bất hợp pháp'); 
}

include "../libs/connect.php";

$macb = $_SESSION['macb'];
$dothoc = $_REQUEST['d'];
$makhoa = base64_decode($_SESSION['makhoa']);
$hk = $_REQUEST['h'];
$thu = array("1"=>"CN", "2"=>"Hai", "3"=>"Ba", "4"=>"Tư", "5"=>"Năm", "6"=>"Sáu", "7"=>"Bảy");


$helper = Helper::getHelper('danhmuc/phanBoCanboGiangDay');
$results = $helper->getMonHoc($db_conn,$dothoc,$makhoa);
$listCanBo = $helper->getCanBo($db_conn);

$template = new Template("PhanBoCanboGiangDay/index.php");
$template->listItems = $results;
$template->listCanBo = $listCanBo;
$template->macb = $macb;
$template->dothoc =  $dothoc;
$template->makhoa = $makhoa;
$template->hk = $hk;
$template->thu = $thu;
$template->render();


if (isset ($db_conn))
	oci_close($db_conn);
?>