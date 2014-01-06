<?php
//if (!isset($_SESSION['uidloginhv'])){
//	die('Truy cập bất hợp pháp'); 
//}

include "libs/connect.php";
include "libs/pgslibshv.php";

?>

<?php

$type = $_REQUEST['w'];
$khoa = $_REQUEST['k'];
$hk = $_REQUEST['h'];
$nganh = $_REQUEST['n'];
$embed = $_REQUEST['e'];

$qHK = "";
$titleHK = "";
if ($hk!='')
{
	$qHK = "AND c.hoc_ky=$hk";
	$titleHK = "HK $hk";
}
if ($type=='khoa-nganh')
{
	$sqlstr="
		SELECT DISTINCT c.ma_nganh,ten_nganh 
		FROM ctdt_khoa_nganh c, nganh n 
		WHERE c.ma_nganh=n.ma_nganh 
		AND c.khoa = $khoa 
		AND c.ma_bac='TH'
		ORDER BY TEN_NGANH
	";
			
	$stmt = oci_parse($db_conn, $sqlstr);
	oci_execute($stmt);
	$n = oci_fetch_all($stmt, $resDM);
	oci_free_statement($stmt);

	for ($i = 0; $i < $n; $i++)
	{
		echo "<option value='".$resDM["MA_NGANH"][$i]."'>" .$resDM["TEN_NGANH"][$i]." (".$resDM["MA_NGANH"][$i].") </option>";
	}
}

if ($type=='khoa_nganh-hk')
{
	$sqlstr="
		SELECT DISTINCT hoc_ky 
		FROM ctdt_khoa_nganh 
		WHERE khoa = $khoa 
		AND ma_nganh = $nganh
		ORDER BY hoc_ky ";
	$stmt = oci_parse($db_conn, $sqlstr);
	oci_execute($stmt);
	$n = oci_fetch_all($stmt, $resDM);
	oci_free_statement($stmt);

	echo "<option value=''>Tất cả HK</option>";
	for ($i = 0; $i < $n; $i++)
	{
		echo "<option value='".$resDM["HOC_KY"][$i]."'>" .$resDM["HOC_KY"][$i]. "</option>";
	}
}

if ($type=='khoa_hk_nganh-ctdt')
{
	$sqlstr="
		SELECT 	c.stt, c.ma_nganh, c.khoa, ten_nganh, m.ma_mh,m.ten, decode(c.ctdt_2_nam, 1, 'x', '&nbsp') ctdt_2_nam, m.so_tin_chi,
				so_tiet_lt,so_tiet_th,so_tiet_bt,so_tiet_tl, decode(tu_chon, 1, 'x', '&nbsp') tu_chon, link_de_cuong
		FROM ctdt_khoa_nganh c, mon_hoc m, nganh n 
		WHERE c.ma_nganh=n.ma_nganh 
		AND c.ma_mh=m.ma_mh 
		AND c.ma_bac='TH' 
		AND c.ma_nganh='$nganh' 
		AND c.khoa=$khoa 
		$qHK
		ORDER BY STT
	";
	$stmt = oci_parse($db_conn, $sqlstr);
	oci_execute($stmt);
	$n = oci_fetch_all($stmt, $resDM);
	oci_free_statement($stmt);
	
	$data='{
				"aaData":[';
				
	for ($i = 0; $i < $n; $i++)
	{		
		if ($resDM["LINK_DE_CUONG"][$i]!='')
		{
			if ($embed=='link')
			{
				$link = "<a target=_blank href='http://www.grad.hcmut.edu.vn/download/ctdt/".$resDM["KHOA"][$i]. "/" .$resDM["MA_NGANH"][$i] . "/".$resDM["LINK_DE_CUONG"][$i]."' title='Xem đề cương {$resDM["TEN"][$i]}'><img border=0 src='icons/view-list-details-icon.png'></a>";
			}
			else
			{
				$link = "<a href=# onClick='ns.readPdf(\"http://www.grad.hcmut.edu.vn/download/ctdt/".$resDM["KHOA"][$i]. "/" .$resDM["MA_NGANH"][$i] . "/".$resDM["LINK_DE_CUONG"][$i]."\",\"Đề cương {$resDM["TEN"][$i]}\");' title='Xem đề cương {$resDM["TEN"][$i]}'><img border=0 src='icons/view-list-details-icon.png' width=16 height=16></a>";
			}
			$link = "<a target=_blank href='http://www.grad.hcmut.edu.vn/download/ctdt/".$resDM["KHOA"][$i]. "/" .$resDM["MA_NGANH"][$i] . "/".$resDM["LINK_DE_CUONG"][$i]."' title='Xem đề cương {$resDM["TEN"][$i]}'><img border=0 src='icons/view-list-details-icon.png'></a>";
		}
		else
			$link = '';
					
		$data .='["'.escapeWEB($resDM["MA_MH"][$i]).'", 
				  "'.escapeWEB($resDM["TEN"][$i]).'", "'.escapeWEB($resDM["SO_TIN_CHI"][$i]).'", "'.escapeWEB($resDM["CTDT_2_NAM"][$i]).'", 
				  "'.escapeWEB($resDM["SO_TIET_LT"][$i]).'", 
				  "'.escapeWEB($resDM["SO_TIET_TH"][$i]).'", 
				  "'.escapeWEB($resDM["SO_TIET_BT"][$i]).'", 
				  "'.escapeWEB($resDM["SO_TIET_TL"][$i]).'", 
				  "'.escapeWEB($resDM["TU_CHON"][$i]).'", 
				  "'.($link).'"],';
	}
	
	if ($n>0)
			$data=substr($data,0,-1);
		
	$data.='	]
			}';
	
	echo $data;
	
}
?>

<?php 
if (isset ($db_conn))
	oci_close($db_conn);
?>