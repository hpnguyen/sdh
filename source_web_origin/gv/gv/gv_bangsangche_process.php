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
	 
	if ($cat == "bangsangche") 
	{
		$txtMaNuocCap 			= str_replace($searchdb, $replacedb,$_POST['txtNuocCap_bangsangche']);
		$txtNamCap 		= str_replace($searchdb, $replacedb,$_POST['txtNamCap_bangsangche']);
		$txtNoiCap 		= str_replace($searchdb, $replacedb,$_POST['txtNoiCap_bangsangche']);
		$txtSoHieu 	= str_replace($searchdb, $replacedb,$_POST['txtSoHieu_bangsangche']);
		$txtTenbangsangche 		= str_replace($searchdb, $replacedb,$_POST['txtTenbangsangche_bangsangche']);
		$txtDeTai		= str_replace($searchdb, $replacedb,$_POST['txtDeTai_bangsangche']);
		$txtTacGia		= str_replace($searchdb, $replacedb,$_POST['txtTacGia_bangsangche']);
		
		$mabangsangcheedit 	= str_replace($searchdb, $replacedb,$_POST['mabangsangcheedit']);
		
		if ($action=="add" || $action=="edit") // Them de tai hay edit
		{
				
			if ($action=="add") // Add de tai
			{
				$sqlstr = "insert into NCKH_BANG_SANG_CHE(FK_MA_CAN_BO,MA_BANG_SANG_CHE,NAM_CAP,NOI_CAP,NUOC_CAP,
				TEN_BANG,SAN_PHAM_MA_DE_TAI,SO_HIEU_BANG,TAC_GIA) values('$macb', get_ma_bang_sang_che('$macb'),
				'$txtNamCap', '$txtNoiCap', '$txtMaNuocCap', '$txtTenbangsangche', '$txtDeTai', '$txtSoHieu', '$txtTacGia')";
			}
			else // Edit de tai
			{
				$sqlstr = "update NCKH_BANG_SANG_CHE set NAM_CAP='$txtNamCap', NOI_CAP='$txtNoiCap',
				NUOC_CAP='$txtMaNuocCap',TEN_BANG='$txtTenbangsangche',SAN_PHAM_MA_DE_TAI='$txtDeTai',
				SO_HIEU_BANG='$txtSoHieu',TAC_GIA='$txtTacGia'
				WHERE FK_MA_CAN_BO ='$macb' AND MA_BANG_SANG_CHE='$mabangsangcheedit'";
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
			$sqlstr = "select count(*) tong from NCKH_BANG_SANG_CHE where fk_ma_can_bo ='".$macb."'";
			$stmt = oci_parse($db_conn, $sqlstr);oci_execute($stmt);$n = oci_fetch_all($stmt, $danhsachdetai);oci_free_statement($stmt);
			$n=$danhsachdetai["TONG"][0];
			
			for ($i=0; $i<$n; $i++)
			   if ($_POST["bangsangchechk".$i]=="1")
					$giaithuongdel = $giaithuongdel."'".$_POST["hiddenMabangsangche".$i]."',";
			$giaithuongdel = substr($giaithuongdel, 0, -1);
			$sqlstr = "delete NCKH_BANG_SANG_CHE where fk_ma_can_bo ='".$macb."' and MA_BANG_SANG_CHE in (" .$giaithuongdel.")";
			
			$stmt = oci_parse($db_conn, $sqlstr);oci_execute($stmt);oci_free_statement($stmt);
			//echo $sqlstr;
		} // end of ($act=="del")

	} // end of ($cat=="detai")
	
	if ($cat == "get_bangsangche") {
	
		$sqlstr="SELECT n.MA_BANG_SANG_CHE, c.TEN_QUOC_GIA TEN_NUOC_CAP, n.NAM_CAP, n.NOI_CAP, 
				n.NUOC_CAP, n.TEN_BANG, n.FK_MA_CAN_BO, n.SAN_PHAM_MA_DE_TAI, n.SO_HIEU_BANG,
				decode(n.TAC_GIA, '1','tác giả chính', 'đồng tác giả') Tac_gia_chinh, n.TAC_GIA 
				FROM NCKH_BANG_SANG_CHE n, QUOC_GIA c 
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
			
			$txtMaBang 				= $resDM["MA_BANG_SANG_CHE"][$i];
			$txtMaNuocCap 			= $resDM["NUOC_CAP"][$i];
			$txtTenNuocCap 			= $resDM["TEN_NUOC_CAP"][$i];
			$txtNamCap 				= $resDM["NAM_CAP"][$i];
			$txtTacGiaChinh			= $resDM["TAC_GIA_CHINH"][$i];
			$txtTacGia				= $resDM["TAC_GIA"][$i];
			$txtSoHieuBang			= str_replace($search,$replace,$resDM["SO_HIEU_BANG"][$i]);
			$txtSpMaDeTai			= str_replace($search,$replace,$resDM["SAN_PHAM_MA_DE_TAI"][$i]);
			$txtTenBang				= str_replace($search,$replace,$resDM["TEN_BANG"][$i]);
			$txtNoiCap				= str_replace($search,$replace,$resDM["NOI_CAP"][$i]);
									
			echo "<tr class='fontcontent ".$classAlt."' align='left' valign='top'><input name=\"hiddenMabangsangche".$i."\" type='hidden' id='hiddenMabangsangche".$i."' value=\"$txtMaBang\"/>";
			echo "<td style=''>".($i+1)."</td>";
			echo "<td align=left >$txtTenBang</td>";
			echo "<td align=left >$txtSpMaDeTai</td>";
			echo "<td align=left>$txtSoHieuBang</td>";
			echo "<td align=left>$txtNoiCap</td>";
			echo "<td align=left>$txtTenNuocCap </td>";
			echo "<td align=left>$txtNamCap </td>";
			echo "<td align=left>$txtTacGiaChinh </td>";
						
			echo "<td class='ahref' onclick=\"getbangsangche_bsc('$txtMaBang', '$txtTenBang', '$txtSoHieuBang', '$txtSpMaDeTai', '$txtTacGia', '$txtNoiCap' , '$txtMaNuocCap','$txtNamCap');\">Sửa</td>";
			echo "<td ><input type=\"checkbox\" id=\"bangsangchechk".$i."\" name=\"bangsangchechk".$i. "\" value=\"1\" /></td>";
			echo "</tr>";
		}
		
	}
	
}
?>


<?php 
if (isset ($db_conn))
	oci_close($db_conn);
?>