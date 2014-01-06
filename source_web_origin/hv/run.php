<?php
include "libs/connect.php";

// Hinh ky yeu
$hinhkyyeufolder = "hinhkyyeu";
$strsql="SELECT ma_hoc_vien FROM xet_luan_van WHERE dot_tmp = '2013-1'";
$oci_pa = oci_parse($db_conn,$strsql);oci_execute($oci_pa);$n=oci_fetch_all($oci_pa, $kt);oci_free_statement($oci_pa);

for ($i = 0; $i < $n; $i++)
{
	$usr = $kt["MA_HOC_VIEN"][$i];
	$filehinh = "./$hinhkyyeufolder/2013-1/$usr.jpg";
	//echo $filehinh;
	if (file_exists($filehinh))
	{
		$strsql="update hoc_vien set hinh_anh='$filehinh' WHERE ma_hoc_vien='$usr'";
		$oci_pa = oci_parse($db_conn,$strsql);
		if (!oci_execute($oci_pa))
			echo "$i . Khong the update $usr $strsql<br/>";
		else
			echo "$i updated $strsql <br/>";
			
		oci_free_statement($oci_pa);
	}
}

/*
ob_start();
echo pack("CCC",0xef,0xbb,0xbf);
echo 'thôi rồi lợm ơi!';
header("Content-Type: application/vnd.ms-excel; charset=utf-8");
header("Content-type: application/x-msexcel; charset=utf-8");
header("Content-Disposition: attachment; filename=Test.xls"); 
header("Expires: 0");
header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
header("Cache-Control: private",false); 


ob_end_flush();
*/
 
if (isset ($db_conn))
	oci_close($db_conn);
?>