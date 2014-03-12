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
	 
	if ($cat == "lvcm") 
	{
		$txtMaLVNC 	= str_replace($searchdb, $replacedb,$_POST['txtLinhVucChuyenMon3_LVCM']);
		if ($txtMaLVNC=='')
			$txtMaLVNC 	= str_replace($searchdb, $replacedb,$_POST['txtLinhVucChuyenMon2_LVCM']);
		
		$maLVCMedit 	= str_replace($searchdb, $replacedb,$_POST['maLVCMedit']);
		$nam 	= str_replace($searchdb, $replacedb,$_POST['txtNam_LVCM']);
		$txtChuyenMonKhac 	= str_replace($searchdb, $replacedb,$_POST['txtChuyenMonKhac_LVCM']);
		
		if ($action=="add" || $action=="edit") // Them de tai hay edit
		{
			if (substr($txtMaLVNC, -2)!="99")
				$txtChuyenMonKhac = "";
			
			if ($action=="add") // Add de tai
			{
				$sqlstr = "insert into NCKH_LVNC_KHCN_CBGD(FK_MA_CAN_BO, FK_MA_LVNC, LVNC_KHAC, NAM) values
						('$macb', '$txtMaLVNC', '$txtChuyenMonKhac', '$nam')";
			}
			else // Edit de tai
			{
				$sqlstr = "update NCKH_LVNC_KHCN_CBGD set FK_MA_LVNC='$txtMaLVNC', LVNC_KHAC='$txtChuyenMonKhac', NAM = '$nam'
				WHERE FK_MA_CAN_BO ='$macb' AND FK_MA_LVNC='$maLVCMedit'";
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
			
			$sqlstr = "select count(*) tong from NCKH_LVNC_KHCN_CBGD where fk_ma_can_bo ='".$macb."'";
			$stmt = oci_parse($db_conn, $sqlstr);
			oci_execute($stmt);
			$n = oci_fetch_all($stmt, $kq);
			oci_free_statement($stmt);
			$n=$kq["TONG"][0];
			
			for ($i=0; $i<$n; $i++)
			   if ($_POST["lvcmchk".$i]=="1")
					$lvcmdel = $lvcmdel."'".$_POST["hiddenMalvnc".$i]."',";
			$lvcmdel = substr($lvcmdel, 0, -1);
			$sqlstr = "delete NCKH_LVNC_KHCN_CBGD where fk_ma_can_bo ='".$macb."' and FK_MA_LVNC in (" .$lvcmdel.")";
			
			//file_put_contents("logs.txt", "----------------------------------------------\n
			//		". date("H:i:s d.m.Y")." $sqlstr \n
			//		----------------------------------------------\n", FILE_APPEND);
			
			$stmt = oci_parse($db_conn, $sqlstr);
			oci_execute($stmt);
			oci_free_statement($stmt);
			
			//echo $sqlstr;
		} // end of ($act=="del")

	} // end of ($cat=="detai")
	
	if ($cat == "get_lvcm") {
	
		$sqlstr="SELECT q.TEN_LVNC, q.MA_LVNC, l.LVNC_KHAC, l.NAM
			FROM NCKH_LVNC_KHCN_CBGD l, NCKH_LVNC_KHCN q
			WHERE l.FK_MA_CAN_BO = '".$macb. "' and l.FK_MA_LVNC = q.MA_LVNC
			";
		$stmt = oci_parse($db_conn, $sqlstr);
		oci_execute($stmt);
		$n = oci_fetch_all($stmt, $resDM);
		oci_free_statement($stmt);
		$classAlt="alt";
		for ($i = 0; $i < $n; $i++)
		{
			($classAlt=="alt") ? $classAlt="alt_" : $classAlt="alt";

			$txtTenLVNC 	= $resDM["TEN_LVNC"][$i];
			$txtTenLVNCKhac = str_replace($search,$replace,$resDM["LVNC_KHAC"][$i]);
			
			if ($resDM["LVNC_KHAC"][$i] != '')
				$txtTenLVNC = $txtTenLVNC . ": " . $resDM["LVNC_KHAC"][$i];
			$txtMaLVNC 		= $resDM["MA_LVNC"][$i];
			$nam 			= $resDM["NAM"][$i];
			
			echo "<tr class='fontcontent ".$classAlt."' align='left' valign='top'><input name=\"hiddenMalvnc".$i."\" type='hidden' id='hiddenMalvnc".$i."' value=\"$txtMaLVNC\"/>";
			echo "<td style=''>".($i+1)."</td>";
			echo "<td style=''>$txtTenLVNC</td>";
			echo "<td style=''>$nam</td>";
			echo "<td class='ahref' onclick=\"getLVCM(".($i+1).", '$txtMaLVNC', '$txtTenLVNCKhac', '$nam');\">Sửa</td>";
			echo "<td ><input type=\"checkbox\" id=\"lvcmchk".$i."\" name=\"lvcmchk".$i. "\" value=\"1\" /></td>";
			echo "</tr>";
		}
		
	}
	
	if ($cat == "lvcmCha-lvcmCon") 
	{
		$ma_lvcm_cha = $_POST['cha'];
		$ma_lvcm_con_default = $_POST['con'];
		$sqlstr="select MA_LVNC, MA_LVNC_CHA, TEN_LVNC, viet0dau_name(ten_lvnc) ten_lvnc_orderby
				from NCKH_LVNC_KHCN 
				where MA_LVNC_CHA = '$ma_lvcm_cha' 
				order by MA_LVNC"; 
		$stmt = oci_parse($db_conn, $sqlstr);oci_execute($stmt);$n = oci_fetch_all($stmt, $resDM);oci_free_statement($stmt);
		
		echo "<option value=''>Vui lòng chọn chuyên môn</option> ";
		for ($i = 0; $i < $n; $i++)
		{
			if ($ma_lvcm_con_default==$resDM["MA_LVNC"][$i])
				$selected = "selected";
			else	
				$selected = "";
			
			echo "<option value='" .$resDM["MA_LVNC"][$i]."' $selected>" .$resDM["TEN_LVNC"][$i]. "</option> ";
		}
	}
}
?>


<?php 
if (isset ($db_conn))
	oci_close($db_conn);
?>