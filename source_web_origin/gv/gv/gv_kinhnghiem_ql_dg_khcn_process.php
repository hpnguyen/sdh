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
	 
	if ($cat == "kinhnghiemql") 
	{
		$txtGhiChu			= str_replace($searchdb, $replacedb,$_POST['txtGhiChu_kinhnghiemql']);
		$txtHinhThuc	= str_replace($searchdb, $replacedb,$_POST['txtHinhThucHoiDong_kinhnghiemql']);
		$txtNam 		= str_replace($searchdb, $replacedb,$_POST['txtThoiGian_kinhnghiemql']);
		$makinhnghiemqledit 	= str_replace($searchdb, $replacedb,$_POST['makinhnghiemqledit']);
		
		if ($action=="add" || $action=="edit") // Them de tai hay edit
		{
			if ($action=="add") // Add de tai
			{
				$sqlstr = "insert into NCKH_KINH_NGHIEM_QLDG(FK_MA_CAN_BO,MA_KINH_NGHIEM_QLDG,HINH_THUC_HOI_DONG,NAM,GHI_CHU) 
				values('$macb',  GET_MA_KINH_QLDG('$macb'),'$txtHinhThuc', '$txtNam', '$txtGhiChu')";
			}
			else // Edit de tai
			{
				$sqlstr = "update NCKH_KINH_NGHIEM_QLDG set HINH_THUC_HOI_DONG='$txtHinhThuc', 
				NAM='$txtNam',GHI_CHU='$txtGhiChu'
				WHERE FK_MA_CAN_BO ='$macb' AND MA_KINH_NGHIEM_QLDG='$makinhnghiemqledit'";
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
			$sqlstr = "select count(*) tong from NCKH_KINH_NGHIEM_QLDG where fk_ma_can_bo ='".$macb."'";
			$stmt = oci_parse($db_conn, $sqlstr);oci_execute($stmt);$n = oci_fetch_all($stmt, $danhsachdetai);oci_free_statement($stmt);
			$n=$danhsachdetai["TONG"][0];
			
			for ($i=0; $i<$n; $i++)
			   if ($_POST["KinhNghiemQLchk".$i]=="1")
					$madel = $madel."'".$_POST["hiddenMaKinhNghiemQL".$i]."',";
			$madel = substr($madel, 0, -1);
			$sqlstr = "delete NCKH_KINH_NGHIEM_QLDG where fk_ma_can_bo ='".$macb."' and MA_KINH_NGHIEM_QLDG in (" .$madel.")";
			
			$stmt = oci_parse($db_conn, $sqlstr);oci_execute($stmt);oci_free_statement($stmt);
			//echo $sqlstr;
		} // end of ($act=="del")

	} // end of ($cat=="detai")
	
	if ($cat == "get_kinhnghiemql") {
	
		$sqlstr="SELECT FK_MA_CAN_BO,MA_KINH_NGHIEM_QLDG,HINH_THUC_HOI_DONG,NAM,GHI_CHU
				FROM NCKH_KINH_NGHIEM_QLDG n
				WHERE FK_MA_CAN_BO='$macb'
				ORDER BY NAM desc";
				
		$stmt = oci_parse($db_conn, $sqlstr);oci_execute($stmt);$n = oci_fetch_all($stmt, $resDM);oci_free_statement($stmt);
		$classAlt="alt";
		for ($i = 0; $i < $n; $i++)
		{
			($classAlt=="alt") ? $classAlt="alt_" : $classAlt="alt";
			
			$txtMaKN 			= $resDM["MA_KINH_NGHIEM_QLDG"][$i];
			$txtHinhThuc		= str_replace($search,$replace,$resDM["HINH_THUC_HOI_DONG"][$i]);
			$txtNam	 			= str_replace($search,$replace,$resDM["NAM"][$i]);
			$txtGhiChu			= str_replace($search,$replace,$resDM["GHI_CHU"][$i]);
			
			echo "<tr class='fontcontent ".$classAlt."' align='left' valign='top'><input name='hiddenMaKinhNghiemQL".$i."' type='hidden' id='hiddenMaKinhNghiemQL".$i."' value=\"$txtMaKN\"/>";
			echo "<td style=''>".($i+1)."</td>";
			echo "<td align=left >$txtHinhThuc</td>";
			echo "<td align=left >$txtNam</td>";
			echo "<td align=left>$txtGhiChu</td>";
						
			echo "<td class='ahref' onclick=\"getinfo_kinhnghiemql('$txtMaKN', '$txtHinhThuc', '$txtGhiChu', '$txtNam');\">Sửa</td>";
			echo "<td ><input type=\"checkbox\" id=\"KinhNghiemQLchk".$i."\" name=\"KinhNghiemQLchk".$i. "\" value=\"1\" /></td>";
			echo "</tr>";
		}
	}
	
}
?>


<?php 
if (isset ($db_conn))
	oci_close($db_conn);
?>