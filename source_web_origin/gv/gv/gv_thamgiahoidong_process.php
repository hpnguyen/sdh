<?
   // cat = detai, ttgv, ctkh, sach, hdlvts, hdlats
   // act = add, edit, del
error_reporting(0);

$sid = $_REQUEST["hisid"];

if ($sid!=""){
	session_id($sid);
	session_start();
}

$search = array('\\',"'",'"');
$replace = array('\\\\',"\'","&quot;"); 

$searchdb = array("\\\\","\\'",'\\"', "'");
$replacedb = array("\\","'", '"', "''");

if (isset($_SESSION["uidloginPortal"]) && isset($_SESSION["macb"])) 
{
	$macb = $_SESSION["macb"];
		
	include "../libs/connect.php";
	$cat = $_POST['cat'];
	$action = $_POST['act'];
	 
	if ($cat == "thamgiahoidong") 
	{
		$txtChucDanh 		= str_replace($searchdb, $replacedb,$_POST['txtChucDanh_thamgiahoidong']);
		$txtTenChuongTrinh	= str_replace($searchdb, $replacedb,$_POST['txtTenChuongTrinh_thamgiahoidong']);
		$txtNuocNgoai 		= str_replace($searchdb, $replacedb,$_POST['txtNuocNgoai_thamgiahoidong']);
		$txtthoigian 		= str_replace($searchdb, $replacedb,$_POST['txtthoigian_thamgiahoidong']);
		
		$mathamgiahoidongedit 	= str_replace($searchdb, $replacedb,$_POST['mathamgiahoidongedit']);
		
		if ($action=="add" || $action=="edit") // Them de tai hay edit
		{
				
			if ($action=="add") // Add de tai
			{
				$sqlstr = "insert into NCKH_THAM_GIA_CHUONG_TRINH(FK_MA_CAN_BO,MA_TG_CHUONG_TRINH,TEN_CHUONG_TRINH,CHUC_DANH,THOI_GIAN,NUOC_NGOAI) 
				values('$macb',  get_ma_tham_gia_chuong_trinh('$macb'),'$txtTenChuongTrinh', '$txtChucDanh', '$txtthoigian', '$txtNuocNgoai')";
			}
			else // Edit de tai
			{
				$sqlstr = "update NCKH_THAM_GIA_CHUONG_TRINH set TEN_CHUONG_TRINH='$txtTenChuongTrinh', 
				CHUC_DANH='$txtChucDanh',THOI_GIAN='$txtthoigian',NUOC_NGOAI='$txtNuocNgoai'
				WHERE FK_MA_CAN_BO ='$macb' AND MA_TG_CHUONG_TRINH='$mathamgiahoidongedit'";
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
			$sqlstr = "select count(*) tong from NCKH_THAM_GIA_CHUONG_TRINH where fk_ma_can_bo ='".$macb."'";
			$stmt = oci_parse($db_conn, $sqlstr);oci_execute($stmt);$n = oci_fetch_all($stmt, $danhsachdetai);oci_free_statement($stmt);
			$n=$danhsachdetai["TONG"][0];
			
			for ($i=0; $i<$n; $i++)
			   if ($_POST["thamgiahoidongchk".$i]=="1")
					$thamgiahoidongdel = $thamgiahoidongdel."'".$_POST["hiddenMathamgiahoidong".$i]."',";
			$thamgiahoidongdel = substr($thamgiahoidongdel, 0, -1);
			$sqlstr = "delete NCKH_THAM_GIA_CHUONG_TRINH where fk_ma_can_bo ='".$macb."' and MA_TG_CHUONG_TRINH in (" .$thamgiahoidongdel.")";
			
			$stmt = oci_parse($db_conn, $sqlstr);oci_execute($stmt);oci_free_statement($stmt);
			//echo $sqlstr;
		} // end of ($act=="del")

	} // end of ($cat=="detai")
	
	if ($cat == "get_thamgiahoidong") {
	
		$sqlstr="SELECT FK_MA_CAN_BO,MA_TG_HOI_DONG,TEN_HOI_DONG,CHUC_DANH,THOI_GIAN,NUOC_NGOAI, decode(NUOC_NGOAI, '1','ngoài nước', '0','trong nước') nuoc_ngoai_desc
				FROM NCKH_THAM_GIA_HOI_DONG n
				WHERE FK_MA_CAN_BO='$macb'
				ORDER BY THOI_GIAN desc";
				
		$stmt = oci_parse($db_conn, $sqlstr);
		oci_execute($stmt);
		$n = oci_fetch_all($stmt, $resDM);
		oci_free_statement($stmt);
		$classAlt="alt";
		for ($i = 0; $i < $n; $i++)
		{
			($classAlt=="alt") ? $classAlt="alt_" : $classAlt="alt";
			
			$txtMaTG 			= $resDM["MA_TG_HOI_DONG"][$i];
			$txtThoiGian		= $resDM["THOI_GIAN"][$i];
			$txtNuocNgoai		= $resDM["NUOC_NGOAI"][$i];
			$txtNuocNgoaiDesc 	= $resDM["NUOC_NGOAI_DESC"][$i];
			$txtTenHD			= str_replace($search,$replace,$resDM["TEN_HOI_DONG"][$i]);
			$txtChucDanh		= str_replace($search,$replace,$resDM["CHUC_DANH"][$i]);
									
			echo "<tr class='fontcontent ".$classAlt."' align='left' valign='top'><input name='hiddenMathamgiahoidong".$i."' type='hidden' id='hiddenMathamgiahoidong".$i."' value=\"$txtMaTG\"/>";
			echo "<td style=''>".($i+1)."</td>";
			echo "<td align=left >$txtThoiGian</td>";
			echo "<td align=left >$txtTenHD</td>";
			echo "<td align=left>$txtNuocNgoaiDesc</td>";
			echo "<td align=left>$txtChucDanh</td>";
						
			echo "<td class='ahref' onclick=\"getthamgiahoidong_tghd('$txtMaTG', '$txtTenHD', '$txtChucDanh', '$txtThoiGian', '$txtNuocNgoai');\">Sửa</td>";
			echo "<td ><input type=\"checkbox\" id=\"thamgiahoidongchk".$i."\" name=\"thamgiahoidongchk".$i. "\" value=\"1\" /></td>";
			echo "</tr>";
		}
		
	}
	
}
?>


<?php 
if (isset ($db_conn))
	oci_close($db_conn);
?>