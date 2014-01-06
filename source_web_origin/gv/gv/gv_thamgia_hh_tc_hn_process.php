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
	 
	if ($cat == "thamgia_hh_tc_hn") 
	{
		$txtChucDanh 		= str_replace($searchdb, $replacedb,$_POST['txtChucDanh_thamgia_hh_tc_hn']);
		$txtTenChuongTrinh	= str_replace($searchdb, $replacedb,$_POST['txtTenToChuc_thamgia_hh_tc_hn']);
		$txtLoai 			= str_replace($searchdb, $replacedb,$_POST['txtLoai_thamgia_hh_tc_hn']);
		$txtthoigianBD 		= str_replace($searchdb, $replacedb,$_POST['txtthoigian_thamgia_hh_tc_hn']);
		$txtthoigianKT 		= str_replace($searchdb, $replacedb,$_POST['txtthoigian_thamgia_hh_tc_hn_kt']);
		
		$mathamgia_hh_tc_hnedit 	= str_replace($searchdb, $replacedb,$_POST['mathamgia_hh_tc_hnedit']);
		
		if ($action=="add" || $action=="edit") // Them de tai hay edit
		{
				
			if ($action=="add") // Add de tai
			{
				$sqlstr = "insert into NCKH_THAM_GIA_HH_TC_HN(FK_MA_CAN_BO,MA_TG_HH_TC_HN,TEN_HH_TC_HN,CHUC_DANH,THOI_GIAN_BD,THOI_GIAN_KT, LOAI) 
				values('$macb',  get_ma_tham_gia_hh_tc_hn('$macb'),'$txtTenChuongTrinh', '$txtChucDanh', '$txtthoigianBD', '$txtthoigianKT', '$txtLoai')";
			}
			else // Edit de tai
			{
				$sqlstr = "update NCKH_THAM_GIA_HH_TC_HN set TEN_HH_TC_HN='$txtTenChuongTrinh',CHUC_DANH='$txtChucDanh',
				THOI_GIAN_BD='$txtthoigianBD',THOI_GIAN_KT='$txtthoigianKT', LOAI='$txtLoai'
				WHERE FK_MA_CAN_BO ='$macb' AND MA_TG_HH_TC_HN='$mathamgia_hh_tc_hnedit'";
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
			$sqlstr = "select count(*) tong from NCKH_THAM_GIA_HH_TC_HN where fk_ma_can_bo ='".$macb."'";
			$stmt = oci_parse($db_conn, $sqlstr);oci_execute($stmt);$n = oci_fetch_all($stmt, $danhsachdetai);oci_free_statement($stmt);
			$n=$danhsachdetai["TONG"][0];
			
			for ($i=0; $i<$n; $i++)
			   if ($_POST["thamgia_hh_tc_hnchk".$i]=="1")
					$thamgia_hh_tc_hndel = $thamgia_hh_tc_hndel."'".$_POST["hiddenMathamgia_hh_tc_hn".$i]."',";
			$thamgia_hh_tc_hndel = substr($thamgia_hh_tc_hndel, 0, -1);
			$sqlstr = "delete NCKH_THAM_GIA_HH_TC_HN where fk_ma_can_bo ='".$macb."' and MA_TG_HH_TC_HN in (" .$thamgia_hh_tc_hndel.")";
			
			$stmt = oci_parse($db_conn, $sqlstr);oci_execute($stmt);oci_free_statement($stmt);
			//echo $sqlstr;
		} // end of ($act=="del")

	} // end of ($cat=="detai")
	
	if ($cat == "get_thamgia_hh_tc_hn") {
	
		$sqlstr="SELECT FK_MA_CAN_BO,MA_TG_HH_TC_HN,TEN_HH_TC_HN,CHUC_DANH,THOI_GIAN_BD,THOI_GIAN_KT, decode(LOAI, 'H','Hiệp hội khoa học', 'T','Tạp chí khoa học', 'HN','Hội nghị khoa học công nghệ') loai_desc, loai
				FROM NCKH_THAM_GIA_HH_TC_HN n
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
			
			$txtMaTG 			= $resDM["MA_TG_HH_TC_HN"][$i];
			$txtTenHH			= str_replace($search,$replace,$resDM["TEN_HH_TC_HN"][$i]);
			$txtThoiGianKT		= str_replace($search,$replace,$resDM["THOI_GIAN_KT"][$i]);
			$txtThoiGianBD	 	= str_replace($search,$replace,$resDM["THOI_GIAN_BD"][$i]);
			$txtLoai			= str_replace($search,$replace,$resDM["LOAI"][$i]);
			$txtTenLoai			= str_replace($search,$replace,$resDM["LOAI_DESC"][$i]);
			$txtChucDanh		= str_replace($search,$replace,$resDM["CHUC_DANH"][$i]);
			
			if ($txtThoiGianKT!="")
				$txtThoiGian = $txtThoiGianBD . "-" . $txtThoiGianKT;
			else
				$txtThoiGian = $txtThoiGianBD;
			
			echo "<tr class='fontcontent ".$classAlt."' align='left' valign='top'><input name='hiddenMathamgia_hh_tc_hn".$i."' type='hidden' id='hiddenMathamgia_hh_tc_hn".$i."' value=\"$txtMaTG\"/>";
			echo "<td style=''>".($i+1)."</td>";
			echo "<td align=left >$txtThoiGian</td>";
			echo "<td align=left >$txtTenHH</td>";
			echo "<td align=left>$txtTenLoai</td>";
			echo "<td align=left>$txtChucDanh</td>";
						
			echo "<td class='ahref' onclick=\"getthamgiahh_tc_hn_tghhtchn('$txtMaTG', '$txtTenHH', '$txtLoai', '$txtChucDanh', '$txtThoiGianBD', '$txtThoiGianKT');\">Sửa</td>";
			echo "<td ><input type=\"checkbox\" id=\"thamgia_hh_tc_hnchk".$i."\" name=\"thamgia_hh_tc_hnchk".$i. "\" value=\"1\" /></td>";
			echo "</tr>";
		}
		
	}
	
}
?>
<?php 
if (isset ($db_conn))
	oci_close($db_conn);
?>