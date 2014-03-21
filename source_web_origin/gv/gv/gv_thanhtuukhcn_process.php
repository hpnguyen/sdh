<?php
$sid = $_REQUEST["hisid"];

if ($sid!=""){
	session_id($sid);
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
	 
	if ($cat == "thanhtuukhcn") 
	{
		$txtNam 			= str_replace($searchdb, $replacedb,$_POST['txtNam_thanhtuukhcn']);
		$txtNoidung 		= str_replace($searchdb, $replacedb,$_POST['txtNoidung_thanhtuukhcn']);
		$mathanhtuukhcnedit 	= str_replace($searchdb, $replacedb,$_POST['mathanhtuukhcnedit']);
		
		if ($action=="add" || $action=="edit") // Them de tai hay edit
		{
				
			if ($action=="add") // Add de tai
			{
				$sqlstr = "insert into NCKH_THANH_TUU_KHCN(FK_MA_CAN_BO, MA_THANH_TUU_KHCN, THANH_TUU_KHCN, NAM) values
						('$macb', get_ma_thanh_tuu_khcn('$macb'), '$txtNoidung', '$txtNam')";
			}
			else // Edit de tai
			{
				$sqlstr = "update NCKH_THANH_TUU_KHCN set THANH_TUU_KHCN='$txtNoidung', NAM='$txtNam'
				WHERE FK_MA_CAN_BO ='$macb' AND MA_THANH_TUU_KHCN='$mathanhtuukhcnedit'";
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
			
			$sqlstr = "select count(*) tong from NCKH_THANH_TUU_KHCN where fk_ma_can_bo ='$macb'";
			$stmt = oci_parse($db_conn, $sqlstr);oci_execute($stmt);$n = oci_fetch_all($stmt, $danhsachdetai);
			oci_free_statement($stmt);
			$n=$danhsachdetai["TONG"][0];
			
			for ($i=0; $i<$n; $i++)
			   if ($_POST["thanhtuukhcnchk".$i]=="1")
					$thanhtuukhcndel = $thanhtuukhcndel."'".$_POST["hiddenMathanhtuukhcn".$i]."',";
			$thanhtuukhcndel = substr($thanhtuukhcndel, 0, -1);
			$sqlstr = "delete NCKH_THANH_TUU_KHCN where fk_ma_can_bo ='".$macb."' and MA_THANH_TUU_KHCN in (" .$thanhtuukhcndel.")";
			
			$stmt = oci_parse($db_conn, $sqlstr);
			
			if (oci_execute($stmt))
				echo '{"status":"1"}';
			else
				echo '{"status":"0"}';
				
			oci_free_statement($stmt);
			//echo $sqlstr;
		} // end of ($act=="del")

	} // end of ($cat=="detai")
	
	if ($cat == "get_thanhtuukhcn") {
	
		$sqlstr="SELECT FK_MA_CAN_BO, MA_THANH_TUU_KHCN, THANH_TUU_KHCN, NAM
				FROM NCKH_THANH_TUU_KHCN
				WHERE fk_ma_can_bo='$macb'
				ORDER BY NAM desc";
				
		$stmt = oci_parse($db_conn, $sqlstr);oci_execute($stmt);$n = oci_fetch_all($stmt, $resDM);oci_free_statement($stmt);
		$classAlt="alt";
		for ($i = 0; $i < $n; $i++)
		{
			($classAlt=="alt") ? $classAlt="alt_" : $classAlt="alt";
			
			$txtNam 		= str_replace($search,$replace,$resDM["NAM"][$i]);
			$txtNoidung 	= str_replace($search,$replace,$resDM["THANH_TUU_KHCN"][$i]);
			$txtMaThanhTuu	= $resDM["MA_THANH_TUU_KHCN"][$i];
			
			echo "<tr class='fontcontent ".$classAlt."' align='left' valign='top'><input name=\"hiddenMathanhtuukhcn".$i."\" type='hidden' id='hiddenMathanhtuukhcn".$i."' value=\"$txtMaThanhTuu\"/>";
			echo "<td style=''>$txtNam</td>";
			echo "<td >$txtNoidung</td>";

			echo "<td class='ahref' onclick=\"getThanhtuukhcn('$txtMaThanhTuu', '$txtNoidung','$txtNam');\">Sửa</td>";
			echo "<td ><input type=\"checkbox\" id=\"thanhtuukhcnchk".$i."\" name=\"thanhtuukhcnchk".$i. "\" value=\"1\" /></td>";
			echo "</tr>";
		}
		
	}
	
}
?>


<?php 
if (isset ($db_conn))
	oci_close($db_conn);
?>