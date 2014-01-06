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
	 
	if ($cat == "thamgiact") 
	{
		$txtChucDanh 		= str_replace($searchdb, $replacedb,$_POST['txtChucDanh_thamgiact']);
		$txtTenChuongTrinh	= str_replace($searchdb, $replacedb,$_POST['txtTenChuongTrinh_thamgiact']);
		$txtNuocNgoai 		= str_replace($searchdb, $replacedb,$_POST['txtNuocNgoai_thamgiact']);
		$txtthoigianbd 		= str_replace($searchdb, $replacedb,$_POST['txtthoigianbd_thamgiact']);
		$txtthoigiankt		= str_replace($searchdb, $replacedb,$_POST['txtthoigiankt_thamgiact']);
		
		$mathamgiactedit 	= str_replace($searchdb, $replacedb,$_POST['mathamgiactedit']);
		
		if ($action=="add" || $action=="edit") // Them de tai hay edit
		{
				
			if ($action=="add") // Add de tai
			{
				$sqlstr = "insert into NCKH_THAM_GIA_CHUONG_TRINH(FK_MA_CAN_BO,MA_TG_CHUONG_TRINH,TEN_CHUONG_TRINH,CHUC_DANH,THOI_GIAN_BD,THOI_GIAN_KT,NUOC_NGOAI) 
				values('$macb',  get_ma_tham_gia_chuong_trinh('$macb'),'$txtTenChuongTrinh', '$txtChucDanh', '$txtthoigianbd','$txtthoigiankt', '$txtNuocNgoai')";
			}
			else // Edit de tai
			{
				$sqlstr = "update NCKH_THAM_GIA_CHUONG_TRINH set TEN_CHUONG_TRINH='$txtTenChuongTrinh', 
				CHUC_DANH='$txtChucDanh',THOI_GIAN_BD='$txtthoigianbd',THOI_GIAN_KT='$txtthoigiankt',NUOC_NGOAI='$txtNuocNgoai'
				WHERE FK_MA_CAN_BO ='$macb' AND MA_TG_CHUONG_TRINH='$mathamgiactedit'";
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
			$sqlstr = "select count(*) tong from NCKH_THAM_GIA_CHUONG_TRINH where fk_ma_can_bo ='".$macb."'";
			$stmt = oci_parse($db_conn, $sqlstr);oci_execute($stmt);$n = oci_fetch_all($stmt, $danhsachdetai);oci_free_statement($stmt);
			$n=$danhsachdetai["TONG"][0];
			
			for ($i=0; $i<$n; $i++)
			   if ($_POST["thamgiactchk".$i]=="1")
					$thamgiactdel = $thamgiactdel."'".$_POST["hiddenMathamgiact".$i]."',";
			$thamgiactdel = substr($thamgiactdel, 0, -1);
			$sqlstr = "delete NCKH_THAM_GIA_CHUONG_TRINH where fk_ma_can_bo ='".$macb."' and MA_TG_CHUONG_TRINH in (" .$thamgiactdel.")";
			
			$stmt = oci_parse($db_conn, $sqlstr);oci_execute($stmt);oci_free_statement($stmt);
			//echo $sqlstr;
		} // end of ($act=="del")

	} // end of ($cat=="detai")
	
	if ($cat == "get_thamgiact") {
	
		$sqlstr="SELECT FK_MA_CAN_BO,MA_TG_CHUONG_TRINH,TEN_CHUONG_TRINH,CHUC_DANH,THOI_GIAN_BD,THOI_GIAN_KT,NUOC_NGOAI, decode(NUOC_NGOAI, '1','ngoài nước', '0','trong nước') nuoc_ngoai_desc
				FROM NCKH_THAM_GIA_CHUONG_TRINH n
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
			
			$txtMaTG 			= $resDM["MA_TG_CHUONG_TRINH"][$i];
			$txtThoiGianKT		= str_replace($search,$replace,$resDM["THOI_GIAN_KT"][$i]);
			$txtThoiGianBD	 	= str_replace($search,$replace,$resDM["THOI_GIAN_BD"][$i]);
			$txtNuocNgoai		= $resDM["NUOC_NGOAI"][$i];
			$txtNuocNgoaiDesc 	= $resDM["NUOC_NGOAI_DESC"][$i];
			$txtTenCT			= str_replace($search,$replace,$resDM["TEN_CHUONG_TRINH"][$i]);
			$txtChucDanh		= str_replace($search,$replace,$resDM["CHUC_DANH"][$i]);
			
			if ($txtThoiGianKT!="")
				$txtThoiGian = $txtThoiGianBD . "-" . $txtThoiGianKT;
			else
				$txtThoiGian = $txtThoiGianBD;
			
			echo "<tr class='fontcontent ".$classAlt."' align='left' valign='top'><input name='hiddenMathamgiact".$i."' type='hidden' id='hiddenMathamgiact".$i."' value=\"$txtMaTG\"/>";
			echo "<td style=''>".($i+1)."</td>";
			echo "<td align=left >$txtThoiGian</td>";
			echo "<td align=left >$txtTenCT</td>";
			echo "<td align=left>$txtNuocNgoaiDesc</td>";
			echo "<td align=left>$txtChucDanh</td>";
						
			echo "<td class='ahref' onclick=\"getthamgiact_tgct('$txtMaTG', '$txtTenCT', '$txtChucDanh', '$txtThoiGianBD','$txtThoiGianKT', '$txtNuocNgoai');\">Sửa</td>";
			echo "<td ><input type=\"checkbox\" id=\"thamgiactchk".$i."\" name=\"thamgiactchk".$i. "\" value=\"1\" /></td>";
			echo "</tr>";
		}
		
	}
	
}
?>


<?php 
if (isset ($db_conn))
	oci_close($db_conn);
?>