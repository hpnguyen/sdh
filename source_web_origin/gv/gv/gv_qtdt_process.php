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
	 
	if ($cat == "qtdt") 
	{
		$txtBacDT 		= str_replace($searchdb, $replacedb,$_POST['txtBacDT_QTDT']);
		$txtHeDT 		= str_replace($searchdb, $replacedb,$_POST['txtHeDT_QTDT']);
		$txtNamBD 		= str_replace($searchdb, $replacedb,$_POST['txtNamBD_QTDT']);
		$txtNamTN 		= str_replace($searchdb, $replacedb,$_POST['txtNamTN_QTDT']);
		$txtNganhKhac 	= str_replace($searchdb, $replacedb,$_POST['txtNganhKhac_QTDT']);
		$txtNganh 		= str_replace($searchdb, $replacedb,$_POST['txtNganh_QTDT']);
		$txtNoiDT 		= str_replace($searchdb, $replacedb,$_POST['txtNoiDT_QTDT']);
		$txtQuocGiaDT 	= str_replace($searchdb, $replacedb,$_POST['txtQuocGiaDT_QTDT']);
		$txtTenLALV 	= str_replace($searchdb, $replacedb,$_POST['txtTenLALV_QTDT']);
		$maqtdtedit 	= str_replace($searchdb, $replacedb,$_POST['maqtdtedit']);
		$maNganhqtdtedit = str_replace($searchdb, $replacedb,$_POST['maNganhqtdtedit']);

		if ($action=="add" || $action=="edit") // Them de tai hay edit
		{
		
			//$dtHuongDeTai = str_replace($searchdb, $replacedb,$_POST['dtHuongDeTai']);
			//$dtHuongDeTai = htmlspecialchars($_POST['dtHuongDeTai']);
			if ($txtNganh!="99999999")
				$txtNganhKhac = "";
			
			if ($action=="add") // Add de tai
			{
				$sqlstr = "insert into NCKH_QUA_TRINH_DAO_TAO(FK_MA_CAN_BO, BAC_DAO_TAO, FK_HE_DAO_TAO, THOI_GIAN_BD, THOI_GIAN_TN, NOI_DAO_TAO, QG_DAT_HOC_VI, FK_NGANH, NGANH_KHAC, TEN_LUAN_AN) values
						('$macb', '$txtBacDT', '$txtHeDT','$txtNamBD', '$txtNamTN', '$txtNoiDT', '$txtQuocGiaDT', '$txtNganh', '$txtNganhKhac', '$txtTenLALV')";
			}
			else // Edit de tai
			{
				$sqlstr = "update NCKH_QUA_TRINH_DAO_TAO set THOI_GIAN_BD='$txtNamBD', THOI_GIAN_TN='$txtNamTN',NOI_DAO_TAO='$txtNoiDT',
				QG_DAT_HOC_VI='$txtQuocGiaDT',FK_NGANH='$txtNganh',NGANH_KHAC='$txtNganhKhac',TEN_LUAN_AN='$txtTenLALV', FK_HE_DAO_TAO='$txtHeDT'
				WHERE FK_MA_CAN_BO ='$macb' AND BAC_DAO_TAO='$txtBacDT' AND FK_NGANH='$maNganhqtdtedit'";
				
				
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
			
			$sqlstr = "select count(*) tongdt from NCKH_QUA_TRINH_DAO_TAO where fk_ma_can_bo ='".$macb."'";
			$stmt = oci_parse($db_conn, $sqlstr);
			oci_execute($stmt);
			$n = oci_fetch_all($stmt, $danhsachdetai);
			oci_free_statement($stmt);
			$n=$danhsachdetai["TONGDT"][0];
			
			for ($i=0; $i<$n; $i++) 
			{
			   if ($_POST["qtdtchk".$i]=="1")
			   {
					$maBacQTDT = $_POST["hiddenMaqtdt".$i];
					$maNganhQTDT = $_POST["hiddenMaNganh".$i];
									
					$qtdtdel = substr($qtdtdel, 0, -1);
					$sqlstr = "delete NCKH_QUA_TRINH_DAO_TAO where fk_ma_can_bo ='".$macb."' and bac_dao_tao = '$maBacQTDT'
								and fk_nganh='$maNganhQTDT'";
					
					$stmt = oci_parse($db_conn, $sqlstr);
					oci_execute($stmt);
					oci_free_statement($stmt);
				}
			}
			//echo $sqlstr;
		} // end of ($act=="del")

	} // end of ($cat=="detai")
	
	if ($cat == "get_qtdt") {
	
		$sqlstr="SELECT q.*, b.TEN_BAC, n.TEN_NGANH, g.TEN_QUOC_GIA, hdt.ten_he_dao_tao
			FROM NCKH_QUA_TRINH_DAO_TAO q, bac_dao_tao b, nckh_nganh_dt n, quoc_gia g, dm_he_dao_tao hdt
			WHERE FK_MA_CAN_BO = '".$macb. "' and q.BAC_DAO_TAO = b.MA_BAC (+) and q.FK_NGANH = n.MA_NGANH (+)
			and q.QG_DAT_HOC_VI = g.MA_QUOC_GIA and q.fk_he_dao_tao = hdt.ma_he_dao_tao (+)
			ORDER BY THOI_GIAN_TN DESC"; 
		$stmt = oci_parse($db_conn, $sqlstr);
		oci_execute($stmt);
		$n = oci_fetch_all($stmt, $resDM);
		oci_free_statement($stmt);
		$classAlt="alt";
		for ($i = 0; $i < $n; $i++)
		{
			($classAlt=="alt") ? $classAlt="alt_" : $classAlt="alt";
			
			if ($resDM["FK_NGANH"][$i]=="99999999")
				$ten_nganh = $resDM["NGANH_KHAC"][$i];
			else
				$ten_nganh = $resDM["TEN_NGANH"][$i];

			$txtBacDT 		= $resDM["BAC_DAO_TAO"][$i];
			$txtHeDT 		= $resDM["FK_HE_DAO_TAO"][$i];
			$txtTenBacDT 	= $resDM["TEN_BAC"][$i];
			$txtNamBD 		= $resDM["THOI_GIAN_BD"][$i];
			$txtNamTN 		= $resDM["THOI_GIAN_TN"][$i];
			$txtTenHDT 		= $resDM["TEN_HE_DAO_TAO"][$i];
			$txtNganh 		= $resDM["FK_NGANH"][$i];
			$txtNoiDT 		= $resDM["NOI_DAO_TAO"][$i];
			$txtQuocGiaDT 	= $resDM["TEN_QUOC_GIA"][$i];
			$txtMaQuocGiaDT = $resDM["QG_DAT_HOC_VI"][$i];
			$txtTenLALV 	= $resDM["TEN_LUAN_AN"][$i];

			
			echo "<tr class='fontcontent ".$classAlt."' align='left' valign='top'><input name=\"hiddenMaqtdt".$i."\" type='hidden' id='hiddenMaqtdt".$i."' value=\"$txtBacDT\"/>
			<input name=\"hiddenMaNganh".$i."\" type='hidden' id='hiddenMaNganh".$i."' value=\"$txtNganh\"/>";
			echo "<td style='font-weight:bold'>$txtTenBacDT</td>";
			echo "<td >$txtTenHDT</td>";
			echo "<td >$ten_nganh</td>";
			echo "<td align=left>$txtNoiDT</td>";
			echo "<td align=left>$txtQuocGiaDT</td>";
			echo "<td align=left>$txtTenLALV</td>";
			echo "<td align=center>$txtNamBD</td>";
			echo "<td align=center>$txtNamTN</td>";
			echo "<td class='ahref' onclick=\"getQTDT(".($i+1).", '$txtBacDT','$txtNganh','$txtMaQuocGiaDT','$txtNamBD','$txtNamTN', '$txtHeDT');\">Sửa</td>";
			echo "<td ><input type=\"checkbox\" id=\"qtdtchk".$i."\" name=\"qtdtchk".$i. "\" value=\"1\" /></td>";
			echo "</tr>";
		}
		
	}
	
	if ($cat == "bacdt-nganh") 
	{
		$bacdaotao = $_POST['b'];
		$nganhdefault = $_POST['n'];
		$sqlstr="select ma_nganh, ten_nganh , decode(viet0dau_name(ten_nganh), 'Khac', 'zz', viet0dau_name(ten_nganh)) ten_nganh_orderby
				from nckh_nganh_dt 
				where length(ma_nganh) = 8 and bac_dao_tao = '$bacdaotao' and ma_nganh <> '99999999'
				order by ten_nganh_orderby"; 
		$stmt = oci_parse($db_conn, $sqlstr);oci_execute($stmt);$n = oci_fetch_all($stmt, $resDM);oci_free_statement($stmt);
		
		echo "<option value=''></option> ";
		for ($i = 0; $i < $n; $i++)
		{
			$selected = "";
			if ($nganhdefault==$resDM["MA_NGANH"][$i])
				$selected = "selected";
			
			echo "<option value='" .$resDM["MA_NGANH"][$i]."' $selected>" .$resDM["TEN_NGANH"][$i]. "</option> ";
		}
		
		if ($nganhdefault=="99999999")
			$selected = "selected";
			
		echo "<option value='99999999' $selected>Khác</option> ";
	}
}
?>


<?php 
if (isset ($db_conn))
	oci_close($db_conn);
?>