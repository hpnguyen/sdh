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
	 
	if ($cat == "giaiphaphuuich") 
	{
		$txtMaNuocCap 			= str_replace($searchdb, $replacedb,$_POST['txtNuocCap_giaiphaphuuich']);
		$txtNamCap 		= str_replace($searchdb, $replacedb,$_POST['txtNamCap_giaiphaphuuich']);
		$txtNoiCap 		= str_replace($searchdb, $replacedb,$_POST['txtNoiCap_giaiphaphuuich']);
		$txtSoHieu 	= str_replace($searchdb, $replacedb,$_POST['txtSoHieu_giaiphaphuuich']);
		$txtTengiaiphaphuuich 		= str_replace($searchdb, $replacedb,$_POST['txtTengiaiphaphuuich_giaiphaphuuich']);
		$txtDeTai		= str_replace($searchdb, $replacedb,$_POST['txtDeTai_giaiphaphuuich']);
		$txtTacGia		= str_replace($searchdb, $replacedb,$_POST['txtTacGia_giaiphaphuuich']);
		
		$magiaiphaphuuichedit 	= str_replace($searchdb, $replacedb,$_POST['magiaiphaphuuichedit']);
		
		if ($action=="add" || $action=="edit") // Them de tai hay edit
		{
				
			if ($action=="add") // Add de tai
			{
				$sqlstr = "insert into NCKH_BANG_GP_HUU_ICH(FK_MA_CAN_BO,MA_BANG_GP_HUU_ICH,NAM_CAP,NOI_CAP,NUOC_CAP,
				TEN_BANG,SAN_PHAM_MA_DE_TAI,SO_HIEU_BANG,TAC_GIA) values('$macb', get_ma_bang_gp_huu_ich('$macb'),
				'$txtNamCap', '$txtNoiCap', '$txtMaNuocCap', '$txtTengiaiphaphuuich', '$txtDeTai', '$txtSoHieu', '$txtTacGia')";
			}
			else // Edit de tai
			{
				$sqlstr = "update NCKH_BANG_GP_HUU_ICH set NAM_CAP='$txtNamCap', NOI_CAP='$txtNoiCap',
				NUOC_CAP='$txtMaNuocCap',TEN_BANG='$txtTengiaiphaphuuich',SAN_PHAM_MA_DE_TAI='$txtDeTai',
				SO_HIEU_BANG='$txtSoHieu',TAC_GIA='$txtTacGia'
				WHERE FK_MA_CAN_BO ='$macb' AND MA_BANG_GP_HUU_ICH='$magiaiphaphuuichedit'";
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
			$sqlstr = "select count(*) tong from NCKH_BANG_GP_HUU_ICH where fk_ma_can_bo ='".$macb."'";
			$stmt = oci_parse($db_conn, $sqlstr);oci_execute($stmt);$n = oci_fetch_all($stmt, $danhsachdetai);oci_free_statement($stmt);
			$n=$danhsachdetai["TONG"][0];
			
			for ($i=0; $i<$n; $i++)
			   if ($_POST["giaiphaphuuichchk".$i]=="1")
					$giaithuongdel = $giaithuongdel."'".$_POST["hiddenMagiaiphaphuuich".$i]."',";
			$giaithuongdel = substr($giaithuongdel, 0, -1);
			$sqlstr = "delete NCKH_BANG_GP_HUU_ICH where fk_ma_can_bo ='".$macb."' and MA_BANG_GP_HUU_ICH in (" .$giaithuongdel.")";
			
			$stmt = oci_parse($db_conn, $sqlstr);oci_execute($stmt);oci_free_statement($stmt);
			//echo $sqlstr;
		} // end of ($act=="del")

	} // end of ($cat=="detai")
	
	if ($cat == "get_giaiphaphuuich") {
	
		$sqlstr="SELECT n.MA_BANG_GP_HUU_ICH, c.TEN_QUOC_GIA TEN_NUOC_CAP, n.NAM_CAP, n.NOI_CAP, 
				n.NUOC_CAP, n.TEN_BANG, n.FK_MA_CAN_BO, n.SAN_PHAM_MA_DE_TAI, n.SO_HIEU_BANG,
				decode(n.TAC_GIA, '1','tác giả chính', 'đồng tác giả') Tac_gia_chinh, n.TAC_GIA 
				FROM NCKH_BANG_GP_HUU_ICH n, QUOC_GIA c 
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
			
			$txtMaBang 				= $resDM["MA_BANG_GP_HUU_ICH"][$i];
			$txtMaNuocCap 			= $resDM["NUOC_CAP"][$i];
			$txtTenNuocCap 			= $resDM["TEN_NUOC_CAP"][$i];
			$txtNamCap 				= $resDM["NAM_CAP"][$i];
			$txtTacGiaChinh			= $resDM["TAC_GIA_CHINH"][$i];
			$txtTacGia				= $resDM["TAC_GIA"][$i];
			$txtSoHieuBang			= str_replace($search,$replace,$resDM["SO_HIEU_BANG"][$i]);
			$txtSpMaDeTai			= str_replace($search,$replace,$resDM["SAN_PHAM_MA_DE_TAI"][$i]);
			$txtTenBang				= str_replace($search,$replace,$resDM["TEN_BANG"][$i]);
			$txtNoiCap				= str_replace($search,$replace,$resDM["NOI_CAP"][$i]);
									
			echo "<tr class='fontcontent ".$classAlt."' align='left' valign='top'><input name='hiddenMagiaiphaphuuich".$i."' type='hidden' id='hiddenMagiaiphaphuuich".$i."' value=\"$txtMaBang\"/>";
			echo "<td style=''>".($i+1)."</td>";
			echo "<td align=left >$txtTenBang</td>";
			echo "<td align=center >$txtSpMaDeTai</td>";
			echo "<td align=left>$txtSoHieuBang</td>";
			echo "<td align=left>$txtNoiCap</td>";
			echo "<td align=left>$txtTenNuocCap </td>";
			echo "<td align=center>$txtNamCap </td>";
			echo "<td align=center>$txtTacGiaChinh </td>";
						
			echo "<td class='ahref' onclick=\"getgiaiphaphuuich_gphi('$txtMaBang', '$txtTenBang', '$txtSoHieuBang', '$txtSpMaDeTai', '$txtTacGia', '$txtNoiCap' , '$txtMaNuocCap','$txtNamCap');\">Sửa</td>";
			echo "<td ><input type=\"checkbox\" id=\"giaiphaphuuichchk".$i."\" name=\"giaiphaphuuichchk".$i. "\" value=\"1\" /></td>";
			echo "</tr>";
		}
		
	}
	
}
?>


<?php 
if (isset ($db_conn))
	oci_close($db_conn);
?>