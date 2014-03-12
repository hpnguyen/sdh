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
	 
	if ($cat == "qtct") 
	{
		$txtTu 			= str_replace($searchdb, $replacedb,$_POST['txtTu_qtct']);
		$txtDen 		= str_replace($searchdb, $replacedb,$_POST['txtDen_qtct']);
		$txtChucVu 		= str_replace($searchdb, $replacedb,$_POST['txtChucVu_qtct']);
		$txtNoiCongTac 	= str_replace($searchdb, $replacedb,$_POST['txtNoiCongTac_qtct']);
		$txtDiaChi 		= str_replace($searchdb, $replacedb,$_POST['txtDiaChi_qtct']);
		$txtChuyenMon	= str_replace($searchdb, $replacedb,$_POST['txtChuyenMon_qtct']);

		$maqtctedit 	= str_replace($searchdb, $replacedb,$_POST['maqtctedit']);
		
		if ($action=="add" || $action=="edit") // Them de tai hay edit
		{
				
			if ($action=="add") // Add de tai
			{
				$sqlstr = "insert into NCKH_QUA_TRINH_CONG_TAC(FK_MA_CAN_BO, MA_QT_CONG_TAC, THOI_GIAN_BD, THOI_GIAN_KT, NOI_CONG_TAC, DIA_CHI_CO_QUAN, FK_CHUC_VU, CHUYEN_MON) values
						('$macb', get_ma_qt_cong_tac('$macb'), '$txtTu', '$txtDen', '$txtNoiCongTac', '$txtDiaChi', '$txtChucVu', '$txtChuyenMon')";
			}
			else // Edit de tai
			{
				$sqlstr = "update NCKH_QUA_TRINH_CONG_TAC set THOI_GIAN_BD='$txtTu', THOI_GIAN_KT='$txtDen',NOI_CONG_TAC='$txtNoiCongTac',
				DIA_CHI_CO_QUAN='$txtDiaChi',FK_CHUC_VU='$txtChucVu',CHUYEN_MON='$txtChuyenMon'
				WHERE FK_MA_CAN_BO ='$macb' AND MA_QT_CONG_TAC='$maqtctedit'";
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
			
			$sqlstr = "select count(*) tong from NCKH_QUA_TRINH_CONG_TAC where fk_ma_can_bo ='".$macb."'";
			$stmt = oci_parse($db_conn, $sqlstr);oci_execute($stmt);$n = oci_fetch_all($stmt, $danhsachdetai);
			oci_free_statement($stmt);
			$n=$danhsachdetai["TONG"][0];
			
			for ($i=0; $i<$n; $i++)
			   if ($_POST["qtctchk".$i]=="1")
					$qtctdel = $qtctdel."'".$_POST["hiddenMaqtct".$i]."',";
			$qtctdel = substr($qtctdel, 0, -1);
			$sqlstr = "delete NCKH_QUA_TRINH_CONG_TAC where fk_ma_can_bo ='".$macb."' and ma_qt_cong_tac in (" .$qtctdel.")";
			
			$stmt = oci_parse($db_conn, $sqlstr);
			oci_execute($stmt);
			oci_free_statement($stmt);
			
			//echo $sqlstr;
		} // end of ($act=="del")

	} // end of ($cat=="detai")
	
	if ($cat == "get_qtct") {
	
		$sqlstr="SELECT n.fk_chuc_vu, c.ten_chuc_vu, n.thoi_gian_kt, n.thoi_gian_bd, n.noi_cong_tac, 
				n.chuyen_mon, n.dia_chi_co_quan, n.ma_qt_cong_tac
				FROM nckh_qua_trinh_cong_tac n, dm_chuc_vu c 
				WHERE n.fk_chuc_vu=c.ma_chuc_vu (+)
				AND fk_ma_can_bo='$macb'
				ORDER BY n.thoi_gian_bd desc";
				
		$stmt = oci_parse($db_conn, $sqlstr);
		oci_execute($stmt);
		$n = oci_fetch_all($stmt, $resDM);
		oci_free_statement($stmt);
		$classAlt="alt";
		for ($i = 0; $i < $n; $i++)
		{
			($classAlt=="alt") ? $classAlt="alt_" : $classAlt="alt";
			
			$txtMaChucVu 		= $resDM["FK_CHUC_VU"][$i];
			$txtChucVu 		= $resDM["TEN_CHUC_VU"][$i];
			$txtNamBD 		= $resDM["THOI_GIAN_BD"][$i];
			$txtNamKT 		= $resDM["THOI_GIAN_KT"][$i];
			$txtNoiCongTac	= $resDM["NOI_CONG_TAC"][$i];
			$txtChuyenMon	= $resDM["CHUYEN_MON"][$i];
			$txtDiaChi	 	= $resDM["DIA_CHI_CO_QUAN"][$i];
			$txtMaQTCT		= $resDM["MA_QT_CONG_TAC"][$i];
			
			//str_replace($search,$replace,
			
			if ($txtNamKT=='')
				$thoigian = "Từ <b>$txtNamBD</b> đến nay";
			else
				$thoigian = "Từ <b>$txtNamBD</b> đến <b>$txtNamKT</b>";
			
			echo "<tr class='fontcontent ".$classAlt."' align='left' valign='top'><input name=\"hiddenMaqtct".$i."\" type='hidden' id='hiddenMaqtct".$i."' value=\"$txtMaQTCT\"/>";
			echo "<td style=''>$thoigian</td>";
			echo "<td >$txtNoiCongTac</td>";
			echo "<td align=left >$txtChucVu</td>";
			echo "<td align=left>$txtChuyenMon</td>";
			echo "<td align=left>$txtDiaChi</td>";
			
			$txtNoiCongTac = str_replace($search,$replace,$txtNoiCongTac);
			$txtChuyenMon = str_replace($search,$replace,$txtChuyenMon);
			$txtDiaChi = str_replace($search,$replace,$txtDiaChi);
			$txtChucVu = str_replace($search,$replace,$txtChucVu);
			
			echo "<td class='ahref' onclick=\"getqtct('$txtMaQTCT', '$txtNamBD','$txtNamKT','$txtNoiCongTac','$txtChuyenMon','$txtDiaChi', '$txtMaChucVu');\">Sửa</td>";
			echo "<td ><input type=\"checkbox\" id=\"qtctchk".$i."\" name=\"qtctchk".$i. "\" value=\"1\" /></td>";
			echo "</tr>";
		}
	}
	
	if ($cat == "cv_qtct_add") {
		$user = base64_decode($_SESSION['uidloginPortal']);
		$chucvu = str_replace($searchdb, $replacedb,$_POST['txtChucVuMoi_qtct']);
		$sqlstr="select * from dm_chuc_vu where upper(ten_chuc_vu) = trim(upper('$chucvu'))";
		$stmt = oci_parse($db_conn, $sqlstr);
		oci_execute($stmt);
		$n = oci_fetch_all($stmt, $resDM);
		oci_free_statement($stmt);
		
		if ($n==0)
		{
			$sqlstr="insert into dm_chuc_vu(ma_chuc_vu, ten_chuc_vu, nguoi_tao) values(get_ma_dm_chuc_vu(), '$chucvu', '$user')";
			$stmt = oci_parse($db_conn, $sqlstr);
			
			if (oci_execute($stmt))
			{
				$sqlstr="select * from DM_CHUC_VU order by TEN_CHUC_VU";
				$stmt = oci_parse($db_conn, $sqlstr);
				oci_execute($stmt);
				$n = oci_fetch_all($stmt, $resDM);			
				for ($i = 0; $i < $n; $i++)
				{
					echo "<option value='" .$resDM["MA_CHUC_VU"][$i]."'> " .$resDM["TEN_CHUC_VU"][$i]. " </option> ";
				}
			}
			else
				echo "";
			oci_free_statement($stmt);
		}
		else
			echo "";
	}
}

if (isset ($db_conn))
	oci_close($db_conn);
?>