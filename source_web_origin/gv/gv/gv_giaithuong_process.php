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
	 
	if ($cat == "giaithuong") 
	{			
		$txtMaNuocCap 			= str_replace($searchdb, $replacedb,$_POST['txtNuocCap_giaithuong']);
		$txtNamCap 		= str_replace($searchdb, $replacedb,$_POST['txtNamCap_giaithuong']);
		$txtNoiCap 		= str_replace($searchdb, $replacedb,$_POST['txtNoiCap_giaithuong']);
		$txtNoiDungGiaiThuong 	= str_replace($searchdb, $replacedb,$_POST['txtNoiDung_giaithuong']);
		$txtTenGiaiThuong 		= str_replace($searchdb, $replacedb,$_POST['txtTenGiaiThuong_giaithuong']);
		
		$magiaithuongedit 	= str_replace($searchdb, $replacedb,$_POST['magiaithuongedit']);
		
		if ($action=="add" || $action=="edit") // Them de tai hay edit
		{
				
			if ($action=="add") // Add de tai
			{
				$sqlstr = "insert into NCKH_GIAI_THUONG_KHCN(FK_MA_CAN_BO, MA_GIAI_THUONG_KHCN, NAM_CAP, NOI_CAP, 
						NOI_DUNG_GIAI_THUONG, NUOC_CAP, TEN_GIAI_THUONG) values	('$macb', get_ma_giai_thuong_khcn('$macb'), '$txtNamCap', '$txtNoiCap', '$txtNoiDungGiaiThuong', '$txtMaNuocCap', '$txtTenGiaiThuong')";
			}
			else // Edit de tai
			{
				$sqlstr = "update NCKH_GIAI_THUONG_KHCN set NAM_CAP='$txtNamCap', NOI_CAP='$txtNoiCap',NOI_DUNG_GIAI_THUONG='$txtNoiDungGiaiThuong',NUOC_CAP='$txtMaNuocCap',TEN_GIAI_THUONG='$txtTenGiaiThuong'
				WHERE FK_MA_CAN_BO ='$macb' AND MA_GIAI_THUONG_KHCN='$magiaithuongedit'";
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
			$sqlstr = "select count(*) tong from NCKH_GIAI_THUONG_KHCN where fk_ma_can_bo ='".$macb."'";
			$stmt = oci_parse($db_conn, $sqlstr);oci_execute($stmt);$n = oci_fetch_all($stmt, $danhsachdetai);
			oci_free_statement($stmt);
			$n=$danhsachdetai["TONG"][0];
			
			for ($i=0; $i<$n; $i++)
			   if ($_POST["giaithuongchk".$i]=="1")
					$giaithuongdel = $giaithuongdel."'".$_POST["hiddenMagiaithuong".$i]."',";
			$giaithuongdel = substr($giaithuongdel, 0, -1);
			$sqlstr = "delete NCKH_GIAI_THUONG_KHCN where fk_ma_can_bo ='".$macb."' and MA_GIAI_THUONG_KHCN in (" .$giaithuongdel.")";
			
			$stmt = oci_parse($db_conn, $sqlstr);
			oci_execute($stmt);
			oci_free_statement($stmt);
			
			//echo $sqlstr;
		} // end of ($act=="del")

	} // end of ($cat=="detai")
	
	if ($cat == "get_giaithuong") {
	
		$sqlstr="SELECT n.MA_GIAI_THUONG_KHCN, c.TEN_QUOC_GIA TEN_NUOC_CAP, n.NAM_CAP, n.NOI_CAP, 
				n.NOI_DUNG_GIAI_THUONG, n.NUOC_CAP, n.TEN_GIAI_THUONG, n.FK_MA_CAN_BO
				FROM NCKH_GIAI_THUONG_KHCN n, QUOC_GIA c 
				WHERE n.NUOC_CAP=c.MA_QUOC_GIA (+)
				AND FK_MA_CAN_BO='$macb'
				ORDER BY n.NAM_CAP desc";
				
		$stmt = oci_parse($db_conn, $sqlstr);
		oci_execute($stmt);
		$n = oci_fetch_all($stmt, $resDM);
		oci_free_statement($stmt);
		$classAlt="alt";
		for ($i = 0; $i < $n; $i++)
		{
			($classAlt=="alt") ? $classAlt="alt_" : $classAlt="alt";
			
			$txtMaGiaiThuong 		= $resDM["MA_GIAI_THUONG_KHCN"][$i];
			$txtMaNuocCap 			= $resDM["NUOC_CAP"][$i];
			$txtTenNuocCap 			= $resDM["TEN_NUOC_CAP"][$i];
			$txtNamCap 				= $resDM["NAM_CAP"][$i];
			$txtNoiCap 				= str_replace($search,$replace,$resDM["NOI_CAP"][$i]);
			$txtNoiDungGiaiThuong	= str_replace($search,$replace,$resDM["NOI_DUNG_GIAI_THUONG"][$i]);
			$txtTenGiaiThuong		= str_replace($search,$replace,$resDM["TEN_GIAI_THUONG"][$i]);
									
			echo "<tr class='fontcontent ".$classAlt."' align='left' valign='top'><input name=\"hiddenMagiaithuong".$i."\" type='hidden' id='hiddenMagiaithuong".$i."' value=\"$txtMaGiaiThuong\"/>";
			echo "<td style=''>".($i+1)."</td>";
			echo "<td align=left >$txtTenGiaiThuong</td>";
			echo "<td align=left >$txtNoiDungGiaiThuong</td>";
			echo "<td align=left>$txtNoiCap</td>";
			echo "<td align=left>$txtTenNuocCap</td>";
			echo "<td align=left>$txtNamCap </td>";
						
			echo "<td class='ahref' onclick=\"getgiaithuong('$txtMaGiaiThuong', '$txtTenGiaiThuong', '$txtNoiDungGiaiThuong', '$txtNoiCap' , '$txtMaNuocCap','$txtNamCap');\">Sửa</td>";
			echo "<td ><input type=\"checkbox\" id=\"giaithuongchk".$i."\" name=\"giaithuongchk".$i. "\" value=\"1\" /></td>";
			echo "</tr>";
		}
		
	}
	
}
?>


<?php 
if (isset ($db_conn))
	oci_close($db_conn);
?>