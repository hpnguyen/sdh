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
	 
	if ($cat == "ngoaingu") 
	{
		$txtMaNgoaiNgu = str_replace($searchdb, $replacedb,$_POST['txtTenNgoaiNgu_ngoaingu']);
		$txtNghe		= str_replace($searchdb, $replacedb,$_POST['txtNghe_ngoaingu']);
		$txtNoi 		= str_replace($searchdb, $replacedb,$_POST['txtNoi_ngoaingu']);
		$txtDoc 		= str_replace($searchdb, $replacedb,$_POST['txtDoc_ngoaingu']);
		$txtViet		= str_replace($searchdb, $replacedb,$_POST['txtViet_ngoaingu']);
		$txtGhiChu		= str_replace($searchdb, $replacedb,$_POST['txtGhiChu_ngoaingu']);

		$mangoainguedit 	= str_replace($searchdb, $replacedb,$_POST['mangoainguedit']);
		
		if ($action=="add" || $action=="edit") // Them de tai hay edit
		{
		
				if ($action=="add") // Add de tai
				{
					$sqlstr = "insert into NCKH_QT_NGOAI_NGU(FK_MA_CAN_BO,FK_MA_NGOAI_NGU,KY_NANG_NGHE,KY_NANG_NOI,KY_NANG_DOC,KY_NANG_VIET, GHI_CHU) 
					values('$macb','$txtMaNgoaiNgu','$txtNghe','$txtNoi','$txtDoc','$txtViet','$txtGhiChu')";
				}
				else // Edit de tai
				{
					$sqlstr = "update NCKH_QT_NGOAI_NGU set KY_NANG_NGHE='$txtNghe',GHI_CHU='$txtGhiChu',
					KY_NANG_NOI='$txtNoi',KY_NANG_DOC='$txtDoc',KY_NANG_VIET='$txtViet'
					WHERE FK_MA_CAN_BO ='$macb' AND FK_MA_NGOAI_NGU='$mangoainguedit'";
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
			$sqlstr = "select count(*) tong from NCKH_QT_NGOAI_NGU where fk_ma_can_bo ='".$macb."'";
			$stmt = oci_parse($db_conn, $sqlstr);oci_execute($stmt);$n = oci_fetch_all($stmt, $danhsachdetai);oci_free_statement($stmt);
			$n=$danhsachdetai["TONG"][0];
			
			for ($i=0; $i<$n; $i++)
			   if ($_POST["ngoainguchk".$i]=="1")
					$ngoaingudel = $ngoaingudel."'".$_POST["hiddenMangoaingu".$i]."',";
			$ngoaingudel = substr($ngoaingudel, 0, -1);
			$sqlstr = "delete NCKH_QT_NGOAI_NGU where fk_ma_can_bo ='".$macb."' and FK_MA_NGOAI_NGU in (" .$ngoaingudel.")";
			
			$stmt = oci_parse($db_conn, $sqlstr);oci_execute($stmt);oci_free_statement($stmt);
			//echo $sqlstr;
		} // end of ($act=="del")

	} // end of ($cat=="detai")
	
	if ($cat == "get_ngoaingu") {
	
		$sqlstr="SELECT FK_MA_CAN_BO,FK_MA_NGOAI_NGU,a.TEN_NGOAI_NGU,KY_NANG_NGHE,KY_NANG_NOI,KY_NANG_DOC,KY_NANG_VIET,GHI_CHU
				FROM NCKH_QT_NGOAI_NGU n, DM_NGOAI_NGU a
				WHERE FK_MA_CAN_BO='$macb' and n.FK_MA_NGOAI_NGU = a.MA_NGOAI_NGU
				ORDER BY a.TEN_NGOAI_NGU";
				
		$stmt = oci_parse($db_conn, $sqlstr);
		oci_execute($stmt);
		$n = oci_fetch_all($stmt, $resDM);
		oci_free_statement($stmt);
		$classAlt="alt";
		for ($i = 0; $i < $n; $i++)
		{
			($classAlt=="alt") ? $classAlt="alt_" : $classAlt="alt";
			
			$txtMaNN 		= $resDM["FK_MA_NGOAI_NGU"][$i];
			$txtTenNN		= $resDM["TEN_NGOAI_NGU"][$i];
			$txtNghe		= $resDM["KY_NANG_NGHE"][$i];
			$txtNoi			= $resDM["KY_NANG_NOI"][$i];
			$txtDoc			= $resDM["KY_NANG_DOC"][$i];
			$txtViet		= $resDM["KY_NANG_VIET"][$i];
			$txtGhiChu		= str_replace($search,$replace,$resDM["GHI_CHU"][$i]);
			
			if ($txtThoiGianKT!="")
				$txtThoiGian = $txtThoiGianBD . "-" . $txtThoiGianKT;
			else
				$txtThoiGian = $txtThoiGianBD;
			
			echo "<tr class='fontcontent ".$classAlt."' align='left' valign='top'><input name='hiddenMangoaingu".$i."' type='hidden' id='hiddenMangoaingu".$i."' value=\"$txtMaNN\"/>";
			echo "<td style=''>".($i+1)."</td>";
			echo "<td align=left >$txtTenNN </td>";
			echo "<td align=center >$txtNghe </td>";
			echo "<td align=center>$txtNoi </td>";
			echo "<td align=center>$txtViet </td>";
			echo "<td align=center>$txtDoc </td>";
			echo "<td align=left>$txtGhiChu </td>";
						
			echo "<td class='ahref' onclick=\"getngoaingu_nn('$txtMaNN', '$txtNghe', '$txtNoi', '$txtDoc','$txtViet','$txtGhiChu');\">Sửa</td>";
			echo "<td ><input type=\"checkbox\" id=\"ngoainguchk".$i."\" name=\"ngoainguchk".$i. "\" value=\"1\" /></td>";
			echo "</tr>";
		}
		
	}
	
}
?>


<?php 
if (isset ($db_conn))
	oci_close($db_conn);
?>