<?
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
	 
	if ($cat == "ungdungthuctien") 
	{	
		$txtTenCongNghe = str_replace($searchdb, $replacedb,$_POST['txtTenCongNghe_ungdungthuctien']);
		$txtNamCG 		= str_replace($searchdb, $replacedb,$_POST['txtNamCG_ungdungthuctien']);
		$txtNamBD 		= str_replace($searchdb, $replacedb,$_POST['txtNamBD_ungdungthuctien']);
		$txtNamKT 		= str_replace($searchdb, $replacedb,$_POST['txtNamKT_ungdungthuctien']);
		$txtHinhThuc 	= str_replace($searchdb, $replacedb,$_POST['txtHinhThuc_ungdungthuctien']);
		$txtQuyMo		= str_replace($searchdb, $replacedb,$_POST['txtQuyMo_ungdungthuctien']);
		$txtDiaChi 		= str_replace($searchdb, $replacedb,$_POST['txtDiaChi_ungdungthuctien']);
		$txtDeTai		= str_replace($searchdb, $replacedb,$_POST['txtDeTai_ungdungthuctien']);
	
		$maungdungthuctienedit 	= str_replace($searchdb, $replacedb,$_POST['maungdungthuctienedit']);
		
		if ($action=="add" || $action=="edit") // Them de tai hay edit
		{
				
			if ($action=="add") // Add de tai
			{
				$sqlstr = "insert into NCKH_UD_THUC_TIEN(FK_MA_CAN_BO, MA_UD_THUC_TIEN, TEN_CONG_NGHE_GP_HU , HINH_THUC, 
				QUY_MO, DIA_CHI_AP_DUNG, THOI_GIAN_CG, SAN_PHAM_MA_DE_TAI, THOI_GIAN_BD, THOI_GIAN_KT) 
				values('$macb', get_ma_ud_thuc_tien('$macb'), '$txtTenCongNghe', '$txtHinhThuc', '$txtQuyMo', '$txtDiaChi', 
				'$txtNamCG', '$txtDeTai', '$txtNamBD', '$txtNamKT')";
			}
			else // Edit de tai
			{
				$sqlstr = "update NCKH_UD_THUC_TIEN set TEN_CONG_NGHE_GP_HU='$txtTenCongNghe', HINH_THUC='$txtHinhThuc',
				QUY_MO='$txtQuyMo',DIA_CHI_AP_DUNG='$txtDiaChi',SAN_PHAM_MA_DE_TAI='$txtDeTai',THOI_GIAN_CG='$txtNamCG',
				THOI_GIAN_BD='$txtNamBD', THOI_GIAN_KT='$txtNamKT'
				WHERE FK_MA_CAN_BO ='$macb' AND MA_UD_THUC_TIEN='$maungdungthuctienedit'";
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
			$sqlstr = "select count(*) tong from NCKH_UD_THUC_TIEN where fk_ma_can_bo ='".$macb."'";
			$stmt = oci_parse($db_conn, $sqlstr);oci_execute($stmt);$n = oci_fetch_all($stmt, $danhsachdetai);oci_free_statement($stmt);
			$n=$danhsachdetai["TONG"][0];
			
			for ($i=0; $i<$n; $i++)
			   if ($_POST["ungdungthuctienchk".$i]=="1")
					$giaithuongdel = $giaithuongdel."'".$_POST["hiddenMaungdungthuctien".$i]."',";
			$giaithuongdel = substr($giaithuongdel, 0, -1);
			$sqlstr = "delete NCKH_UD_THUC_TIEN where fk_ma_can_bo ='".$macb."' and MA_UD_THUC_TIEN in (" .$giaithuongdel.")";
			
			$stmt = oci_parse($db_conn, $sqlstr);oci_execute($stmt);oci_free_statement($stmt);
			//echo $sqlstr;
		} // end of ($act=="del")

	} // end of ($cat=="detai")
	
	if ($cat == "get_ungdungthuctien") {
	
		$sqlstr="SELECT *
				FROM NCKH_UD_THUC_TIEN
				WHERE FK_MA_CAN_BO='$macb'
				";
				
		$stmt = oci_parse($db_conn, $sqlstr);
		oci_execute($stmt);
		$n = oci_fetch_all($stmt, $resDM);
		oci_free_statement($stmt);
		$classAlt="alt";
		for ($i = 0; $i < $n; $i++)
		{
			($classAlt=="alt") ? $classAlt="alt_" : $classAlt="alt";
			
			$txtMaBang 			= $resDM["MA_UD_THUC_TIEN"][$i];
			$txtTenCongNghe		= str_replace($search,$replace,$resDM["TEN_CONG_NGHE_GP_HU"][$i]);
			$txtHinhThuc		= str_replace($search,$replace,$resDM["HINH_THUC"][$i]);
			$txtQuyMo			= str_replace($search,$replace,$resDM["QUY_MO"][$i]);
			$txtDiaChi			= str_replace($search,$replace,$resDM["DIA_CHI_AP_DUNG"][$i]);
			$txtNamCG		= str_replace($search,$replace,$resDM["THOI_GIAN_CG"][$i]);
			$txtNamBD			= str_replace($search,$replace,$resDM["THOI_GIAN_BD"][$i]);
			$txtNamKT			= str_replace($search,$replace,$resDM["THOI_GIAN_KT"][$i]);
			$txtSpMaDeTai		= str_replace($search,$replace,$resDM["SAN_PHAM_MA_DE_TAI"][$i]);
			
			$txthtqmdc = "";
			if ($txtHinhThuc!="")
				$txthtqmdc .= "$txtHinhThuc, ";
			if ($txtQuyMo!="")
				$txthtqmdc .= "$txtQuyMo, ";
			if ($txtDiaChi!="")
				$txthtqmdc .= "$txtDiaChi, ";
			$txthtqmdc=substr($txthtqmdc,0,-2);
			
			if ($txtNamKT != '')
				$txtThoiGian = "$txtNamBD - $txtNamKT";
			else
				$txtThoiGian = "$txtNamBD";
			
			echo "<tr class='fontcontent ".$classAlt."' align='left' valign='top'><input name='hiddenMaungdungthuctien".$i."' type='hidden' id='hiddenMaungdungthuctien".$i."' value=\"$txtMaBang\"/>";
			echo "<td style=''>".($i+1)."</td>";
			echo "<td align=left >$txtTenCongNghe</td>";
			echo "<td align=left >$txthtqmdc</td>";
			echo "<td align=center>$txtThoiGian</td>";
			echo "<td align=center>$txtNamCG</td>";
			echo "<td align=left>$txtSpMaDeTai</td>";
						
			echo "<td class='ahref' onclick=\"getungdungthuctien_udtt('$txtMaBang', '$txtTenCongNghe', '$txtHinhThuc', '$txtQuyMo', '$txtDiaChi', '$txtNamCG' , '$txtSpMaDeTai', '$txtNamBD', '$txtNamKT');\">Sửa</td>";
			echo "<td ><input type=\"checkbox\" id=\"ungdungthuctienchk".$i."\" name=\"ungdungthuctienchk".$i. "\" value=\"1\" /></td>";
			echo "</tr>";
		}
		
	}
}
?>


<?php 
if (isset ($db_conn))
	oci_close($db_conn);
?>