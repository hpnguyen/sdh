<?php
if (isset($_REQUEST["hisid"])){
	session_id($_REQUEST["hisid"]);
	session_start();
}
if (!isset($_SESSION['uidloginPortal'])){
	die('Đã hết phiên làm việc'); 
}

include "../libs/connect.php";
include "../libs/pgslibs.php";
if (!allowPermisstion(base64_decode($_SESSION['uidloginPortal']), '001', $db_conn))
{
	die('Truy cập bất hợp pháp'); 
}

$search = array('\\',"'",'"');
$replace = array('\\\\',"\'","&quot;"); 

$searchdb = array("\\\\","\\'",'\\"', "'");
$replacedb = array("\\","'", '"', "''");

if (isset($_SESSION["uidloginPortal"]) && isset($_SESSION["macb"])) 
{
	$macb = $_SESSION["macb"];
	$cat = $_POST['cat'];
	$action = $_POST['act'];
	 
	if ($cat == "thamgiatruongvien") 
	{
		$txtNoidung			= str_replace($searchdb, $replacedb,$_POST['txtNoiDung_thamgiatruongvien']);
		$txtTenTruongVien	= str_replace($searchdb, $replacedb,$_POST['txtTenTruongVien_thamgiatruongvien']);
		//$txtNuocNgoai 		= str_replace($searchdb, $replacedb,$_POST['txtNuocNgoai_thamgiatruongvien']);
		$txtthoigianbd 		= str_replace($searchdb, $replacedb,$_POST['txtthoigianbd_thamgiatruongvien']);
		$txtthoigiankt		= str_replace($searchdb, $replacedb,$_POST['txtthoigiankt_thamgiatruongvien']);
		
		$mathamgiatruongvienedit 	= str_replace($searchdb, $replacedb,$_POST['mathamgiatruongvienedit']);
		
		if ($action=="add" || $action=="edit") // Them de tai hay edit
		{
				
			if ($action=="add") // Add de tai
			{
				$sqlstr = "insert into NCKH_THAM_GIA_TRUONG_VIEN(FK_MA_CAN_BO,MA_TG_TRUONG_VIEN,TEN_TRUONG_VIEN,NOI_DUNG_THAM_GIA,THOI_GIAN_BD,THOI_GIAN_KT) 
				values('$macb',  get_ma_tham_gia_truong_vien('$macb'),'$txtTenTruongVien', '$txtNoidung', '$txtthoigianbd','$txtthoigiankt')";
			}
			else // Edit de tai
			{
				$sqlstr = "update NCKH_THAM_GIA_TRUONG_VIEN set TEN_TRUONG_VIEN='$txtTenTruongVien', 
				NOI_DUNG_THAM_GIA='$txtNoidung',THOI_GIAN_BD='$txtthoigianbd',THOI_GIAN_KT='$txtthoigiankt'
				WHERE FK_MA_CAN_BO ='$macb' AND MA_TG_TRUONG_VIEN='$mathamgiatruongvienedit'";
			}
			
			//file_put_contents("logs.txt", "----------------------------------------------\n
			//		". date("H:i:s d.m.Y")." $sqlstr \n
			//		----------------------------------------------\n", FILE_APPEND);
			
			$stmt = oci_parse($db_conn, $sqlstr);
			
			if (oci_execute($stmt))
				echo '{"status":"1"}';
			else
				echo '{"status":"0"}';
				
			oci_free_statement($stmt);
			
		} // end of ($act=="add")
		else if ($action=="del") // Delete de tai
		{
			$sqlstr = "select count(*) tong from NCKH_THAM_GIA_TRUONG_VIEN where fk_ma_can_bo ='".$macb."'";
			$stmt = oci_parse($db_conn, $sqlstr);oci_execute($stmt);$n = oci_fetch_all($stmt, $danhsachdetai);oci_free_statement($stmt);
			$n=$danhsachdetai["TONG"][0];
			
			for ($i=0; $i<$n; $i++)
			   if ($_POST["thamgiatruongvienchk".$i]=="1")
					$thamgiatruongviendel = $thamgiatruongviendel."'".$_POST["hiddenMathamgiatruongvien".$i]."',";
			$thamgiatruongviendel = substr($thamgiatruongviendel, 0, -1);
			$sqlstr = "delete NCKH_THAM_GIA_TRUONG_VIEN where fk_ma_can_bo ='".$macb."' and MA_TG_TRUONG_VIEN in (" .$thamgiatruongviendel.")";
			
			$stmt = oci_parse($db_conn, $sqlstr);oci_execute($stmt);oci_free_statement($stmt);
			//echo $sqlstr;
		} // end of ($act=="del")

	} // end of ($cat=="detai")
	
	if ($cat == "get_thamgiatruongvien") {
	
		$sqlstr="SELECT FK_MA_CAN_BO,MA_TG_TRUONG_VIEN,TEN_TRUONG_VIEN,NOI_DUNG_THAM_GIA,THOI_GIAN_BD,THOI_GIAN_KT
				FROM NCKH_THAM_GIA_TRUONG_VIEN n
				WHERE FK_MA_CAN_BO='$macb'
				ORDER BY THOI_GIAN_BD desc";
				
		$stmt = oci_parse($db_conn, $sqlstr);
		oci_execute($stmt);
		$n = oci_fetch_all($stmt, $resDM);
		oci_free_statement($stmt);
		$classAlt="alt";
		for ($i = 0; $i < $n; $i++)
		{
			($classAlt=="alt") ? $classAlt="alt_" : $classAlt="alt";
			
			$txtMaTG 			= $resDM["MA_TG_TRUONG_VIEN"][$i];
			$txtThoiGianKT		= str_replace($search,$replace,$resDM["THOI_GIAN_KT"][$i]);
			$txtThoiGianBD	 	= str_replace($search,$replace,$resDM["THOI_GIAN_BD"][$i]);
			//$txtNuocNgoai		= $resDM["NUOC_NGOAI"][$i];
			$txtNuocNgoaiDesc 	= $resDM["NUOC_NGOAI_DESC"][$i];
			$txtTenTruongVien	= str_replace($search,$replace,$resDM["TEN_TRUONG_VIEN"][$i]);
			$txtNoidung			= str_replace($search,$replace,$resDM["NOI_DUNG_THAM_GIA"][$i]);
			
			if ($txtThoiGianKT!="")
				$txtThoiGian = $txtThoiGianBD . "-" . $txtThoiGianKT;
			else
				$txtThoiGian = $txtThoiGianBD;
			
			echo "<tr class='fontcontent ".$classAlt."' align='left' valign='top'><input name='hiddenMathamgiatruongvien".$i."' type='hidden' id='hiddenMathamgiatruongvien".$i."' value=\"$txtMaTG\"/>";
			echo "<td style=''>".($i+1)."</td>";
			echo "<td align=left >$txtThoiGian</td>";
			echo "<td align=left >$txtTenTruongVien</td>";
			echo "<td align=left>$txtNoidung</td>";
						
			echo "<td class='ahref' onclick=\"getthamgiatruongvien_tgtv('$txtMaTG', '$txtTenTruongVien', '$txtNoidung', '$txtThoiGianBD','$txtThoiGianKT');\">Sửa</td>";
			echo "<td ><input type=\"checkbox\" id=\"thamgiatruongvienchk".$i."\" name=\"thamgiatruongvienchk".$i. "\" value=\"1\" /></td>";
			echo "</tr>";
		}
		
	}
	
}
?>


<?php 
if (isset ($db_conn))
	oci_close($db_conn);
?>