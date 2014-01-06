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
	 
	if ($cat == "gioithieuchuyengia") 
	{
		$txtDiaChi		= str_replace($searchdb, $replacedb,$_POST['txtDiaChi_gioithieuchuyengia']);
		$txtNoiCongTac	= str_replace($searchdb, $replacedb,$_POST['txtNoiCongTac_gioithieuchuyengia']);
		$txtHoTen 		= str_replace($searchdb, $replacedb,$_POST['txtHoTen_gioithieuchuyengia']);
		$txtDienThoai 	= str_replace($searchdb, $replacedb,$_POST['txtDienThoai_gioithieuchuyengia']);
		$txtEmail 		= str_replace($searchdb, $replacedb,$_POST['txtEmail_gioithieuchuyengia']);
		$magioithieuchuyengiaedit 	= str_replace($searchdb, $replacedb,$_POST['magioithieuchuyengiaedit']);
		
		if ($action=="add" || $action=="edit") // Them de tai hay edit
		{
			if ($action=="add") // Add de tai
			{
				$sqlstr = "insert into NCKH_GT_CHUYEN_GIA(FK_MA_CAN_BO,MA_GT_CHUYEN_GIA,HO_TEN,NOI_CONG_TAC,DIA_CHI_LIEN_LAC,DIEN_THOAI,EMAIL) 
				values('$macb',GET_MA_GT_CHUYEN_GIA('$macb'),'$txtHoTen','$txtNoiCongTac','$txtDiaChi','$txtDienThoai','$txtEmail')";
			}
			else // Edit de tai
			{
				$sqlstr = "update NCKH_GT_CHUYEN_GIA set HO_TEN='$txtHoTen',NOI_CONG_TAC='$txtNoiCongTac',
				DIA_CHI_LIEN_LAC='$txtDiaChi',DIEN_THOAI='$txtDienThoai',EMAIL='$txtEmail'
				WHERE FK_MA_CAN_BO ='$macb' AND MA_GT_CHUYEN_GIA='$magioithieuchuyengiaedit'";
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
			$sqlstr = "select count(*) tong from NCKH_GT_CHUYEN_GIA where fk_ma_can_bo ='".$macb."'";
			$stmt = oci_parse($db_conn, $sqlstr);oci_execute($stmt);$n = oci_fetch_all($stmt, $danhsachdetai);oci_free_statement($stmt);
			$n=$danhsachdetai["TONG"][0];
			
			for ($i=0; $i<$n; $i++)
			   if ($_POST["GioiThieuChuyenGiaKhacchk".$i]=="1")
					$madel = $madel."'".$_POST["hiddenMaChuyenGiaKhac".$i]."',";
			$madel = substr($madel, 0, -1);
			$sqlstr = "delete NCKH_GT_CHUYEN_GIA where fk_ma_can_bo ='".$macb."' and MA_GT_CHUYEN_GIA in (" .$madel.")";
			
			$stmt = oci_parse($db_conn, $sqlstr);oci_execute($stmt);oci_free_statement($stmt);
			//echo $sqlstr;
		} // end of ($act=="del")

	} // end of ($cat=="detai")
	
	if ($cat == "get_gioithieuchuyengia") {
	
		$sqlstr="SELECT FK_MA_CAN_BO,MA_GT_CHUYEN_GIA,HO_TEN,NOI_CONG_TAC,DIA_CHI_LIEN_LAC,DIEN_THOAI,EMAIL
				FROM NCKH_GT_CHUYEN_GIA n
				WHERE FK_MA_CAN_BO='$macb'
				ORDER BY HO_TEN";
				
		$stmt = oci_parse($db_conn, $sqlstr);oci_execute($stmt);$n = oci_fetch_all($stmt, $resDM);oci_free_statement($stmt);
		$classAlt="alt";
		for ($i = 0; $i < $n; $i++)
		{
			($classAlt=="alt") ? $classAlt="alt_" : $classAlt="alt";
			
			$txtMaCG 		= $resDM["MA_GT_CHUYEN_GIA"][$i];
			$txtHoTen		= str_replace($search,$replace,$resDM["HO_TEN"][$i]);
			$txtNoiCongTac	= str_replace($search,$replace,$resDM["NOI_CONG_TAC"][$i]);
			$txtDiaChi		= str_replace($search,$replace,$resDM["DIA_CHI_LIEN_LAC"][$i]);
			$txtDienThoai	= str_replace($search,$replace,$resDM["DIEN_THOAI"][$i]);
			$txtEmail		= str_replace($search,$replace,$resDM["EMAIL"][$i]);
			
			echo "<tr class='fontcontent ".$classAlt."' align='left' valign='top'><input name='hiddenMaChuyenGiaKhac".$i."' type='hidden' id='hiddenMaChuyenGiaKhac".$i."' value=\"$txtMaCG\"/>";
			echo "<td style=''>".($i+1)."</td>";
			echo "<td align=left >$txtHoTen</td>";
			echo "<td align=left >$txtNoiCongTac</td>";
			echo "<td align=left>$txtDiaChi</td>";
			echo "<td align=left>$txtDienThoai</td>";
			echo "<td align=left>$txtEmail</td>";
						
			echo "<td class='ahref' onclick=\"getinfo_gioithieuchuyengia('$txtMaCG', '$txtHoTen', '$txtNoiCongTac', '$txtDiaChi','$txtDienThoai','$txtEmail');\">Sửa</td>";
			echo "<td ><input type=\"checkbox\" id=\"GioiThieuChuyenGiaKhacchk".$i."\" name=\"GioiThieuChuyenGiaKhacchk".$i. "\" value=\"1\" /></td>";
			echo "</tr>";
		}
	}
	
}
?>


<?php 
if (isset ($db_conn))
	oci_close($db_conn);
?>
