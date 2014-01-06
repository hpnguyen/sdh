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
	 
	if ($cat == "huongdansv") 
	{
		$txtBacDT 	= str_replace($searchdb, $replacedb,$_POST['txtBacDT_huongdansv']);
		$txtLuanAn 	= str_replace($searchdb, $replacedb,$_POST['txtLuanAn_huongdansv']);
		$txtHoTen 	= str_replace($searchdb, $replacedb,$_POST['txtHoTen_huongdansv']);
		$txtNamTN 	= str_replace($searchdb, $replacedb,$_POST['txtNamTN_huongdansv']);
		$txtDeTai 	= str_replace($searchdb, $replacedb,$_POST['txtDeTai_huongdansv']);
		$txtTruong 	= str_replace($searchdb, $replacedb,$_POST['txtTenTruong_huongdansv']);

		$mahuongdansvedit 	= str_replace($searchdb, $replacedb,$_POST['mahuongdansvedit']);
		$mahvhuongdanedit 	= str_replace($searchdb, $replacedb,$_POST['mahvhuongdanedit']);
		
		if ($action=="add" || $action=="edit") // Them de tai hay edit
		{
				
			if ($action=="add") // Add de tai
			{
				$sqlstr = "insert into NCKH_HD_LUAN_AN(FK_MA_CAN_BO, MA_HD_LUAN_AN, HO_TEN_SV, BAC_DAO_TAO, NAM_TOT_NGHIEP, SAN_PHAM_MA_DE_TAI, TEN_LUAN_AN, TRUONG) values
						('$macb', get_ma_hd_luan_an('$macb'), '$txtHoTen', '$txtBacDT', '$txtNamTN', '$txtDeTai', '$txtLuanAn', '$txtTruong')";
			}
			else // Edit de tai
			{
				if ($mahvhuongdanedit == '')
					$sqlstr = "update NCKH_HD_LUAN_AN set HO_TEN_SV='$txtHoTen', BAC_DAO_TAO='$txtBacDT',NAM_TOT_NGHIEP='$txtNamTN',
					SAN_PHAM_MA_DE_TAI='$txtDeTai',TEN_LUAN_AN='$txtLuanAn', TRUONG = '$txtTruong'
					WHERE FK_MA_CAN_BO ='$macb' AND MA_HD_LUAN_AN='$mahuongdansvedit' and MA_HOC_VIEN is null";
				else
					$sqlstr = "update NCKH_HD_LUAN_AN set SAN_PHAM_MA_DE_TAI='$txtDeTai'
					WHERE FK_MA_CAN_BO ='$macb' AND MA_HD_LUAN_AN='$mahuongdansvedit' and MA_HOC_VIEN is not null";
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
			$sqlstr = "select count(*) tong from NCKH_HD_LUAN_AN where fk_ma_can_bo ='".$macb."'";
			$stmt = oci_parse($db_conn, $sqlstr);oci_execute($stmt);$n = oci_fetch_all($stmt, $danhsachdetai);
			oci_free_statement($stmt);
			$n=$danhsachdetai["TONG"][0];
			
			for ($i=0; $i<$n; $i++){
				if ($_POST["huongdansvchk".$i]=="1"){
					$huongdansvdel = $huongdansvdel."'".$_POST["hiddenMahuongdansv".$i]."',";
				}
			}
			$huongdansvdel = substr($huongdansvdel, 0, -1);
			$sqlstr = "delete NCKH_HD_LUAN_AN where fk_ma_can_bo ='".$macb."' and MA_HD_LUAN_AN in (" .$huongdansvdel.") and MA_HOC_VIEN is null";
			
			$stmt = oci_parse($db_conn, $sqlstr);oci_execute($stmt);oci_free_statement($stmt);
			//echo $sqlstr;
		} // end of ($act=="del")

	} // end of ($cat=="detai")
	
	if ($cat == "get_huongdansv") 
	{
		// Cap nhat danh sach moi
		// Lay ds thac si Trường Đại Học Bách Khoa Tp.HCM
		$sqlstr="select l.ma_hoc_vien, h.ho || ' ' || h.ten ho_ten, l.ten_de_tai, lpad(dot_cap_bang(l.ma_hoc_vien), 4, '0') nam_tn
		from luan_van_thac_sy l, hoc_vien h
		where (huong_dan_chinh = '$macb' or huong_dan_phu = '$macb')
		and h.ma_hoc_vien = l.ma_hoc_vien 
		and dot_nhan_lv = dot_nhan_lv(h.ma_hoc_vien)
		and h.ma_hoc_vien not in (SELECT nvl(MA_HOC_VIEN,'') FROM NCKH_HD_LUAN_AN WHERE FK_MA_CAN_BO='$macb')"; 
		$stmt = oci_parse($db_conn, $sqlstr);oci_execute($stmt);$n = oci_fetch_all($stmt, $resDM);oci_free_statement($stmt);
		$truongbk = 'ĐHBK-ĐHQG-HCM';
		for ($i = 0; $i < $n; $i++)
		{
			$txtHoTen = $resDM["HO_TEN"][$i];
			$txtNamTN = $resDM["NAM_TN"][$i];
			$txtLuanAn = $resDM["TEN_DE_TAI"][$i];
			$txtMaHV = $resDM["MA_HOC_VIEN"][$i];
			
			$sqlstr = "insert into NCKH_HD_LUAN_AN(FK_MA_CAN_BO, MA_HD_LUAN_AN, MA_HOC_VIEN, HO_TEN_SV, BAC_DAO_TAO, NAM_TOT_NGHIEP, TEN_LUAN_AN, TRUONG) values
			('$macb', get_ma_hd_luan_an('$macb'), '$txtMaHV', '$txtHoTen', 'TH', '$txtNamTN', '$txtLuanAn', '$truongbk')";
			$stmt = oci_parse($db_conn, $sqlstr);oci_execute($stmt);
		}
		
		// Lay ds tien si
		$sqlstr="select l.ma_hoc_vien, h.ho || ' ' || h.ten ho_ten, l.ten_luan_an, h.khoa, lpad(dot_cap_bang(l.ma_hoc_vien), 4, '0') nam_tn 
		from  luan_an_tien_sy l, hoc_vien h
		where (l.huong_dan_1 = '".$macb. "' or l.huong_dan_2 = '" .$macb. "' or l.huong_dan_3 = '" .$macb. "') 
		and h.ma_hoc_vien = l.ma_hoc_vien
		and h.ma_hoc_vien not in (SELECT MA_HOC_VIEN FROM NCKH_HD_LUAN_AN WHERE FK_MA_CAN_BO='$macb')";
		$stmt = oci_parse($db_conn, $sqlstr);oci_execute($stmt);$n = oci_fetch_all($stmt, $resDM);oci_free_statement($stmt);
		for ($i = 0; $i < $n; $i++)
		{
			$txtHoTen = $resDM["HO_TEN"][$i];
			$txtNamTN = $resDM["NAM_TN"][$i];
			$txtLuanAn = $resDM["TEN_LUAN_AN"][$i];
			$txtMaHV = $resDM["MA_HOC_VIEN"][$i];
			
			$sqlstr = "insert into NCKH_HD_LUAN_AN(FK_MA_CAN_BO, MA_HD_LUAN_AN, MA_HOC_VIEN, HO_TEN_SV, BAC_DAO_TAO, NAM_TOT_NGHIEP, TEN_LUAN_AN, TRUONG) values
			('$macb', get_ma_hd_luan_an('$macb'), '$txtMaHV', '$txtHoTen', 'TS', '$txtNamTN', '$txtLuanAn', '$truongbk')";
			$stmt = oci_parse($db_conn, $sqlstr);oci_execute($stmt);
		}
		// end of cap nhat danh sach moi
		
		// Update nam tôt nghiep
		$sqlstr=" update NCKH_HD_LUAN_AN set nam_tot_nghiep = lpad(dot_cap_bang(ma_hoc_vien), 4, '0') 
				  where fk_ma_can_bo = '$macb' and nam_tot_nghiep is null";	
		$stmt = oci_parse($db_conn, $sqlstr);
		oci_execute($stmt);
		// end of update
		
		$sqlstr="SELECT MA_HD_LUAN_AN, lower(HO_TEN_SV) ho_ten_sv, BAC_DAO_TAO, TEN_BAC, MA_HOC_VIEN, NAM_TOT_NGHIEP, SAN_PHAM_MA_DE_TAI, TEN_LUAN_AN, TRUONG
				FROM NCKH_HD_LUAN_AN h, BAC_DAO_TAO b
				WHERE FK_MA_CAN_BO='$macb' AND h.BAC_DAO_TAO = b.MA_BAC
				ORDER BY BAC_DAO_TAO, NAM_TOT_NGHIEP DESC, HO_TEN_SV";	
		$stmt = oci_parse($db_conn, $sqlstr);
		oci_execute($stmt);
		$n = oci_fetch_all($stmt, $resDM);
		oci_free_statement($stmt);
		$classAlt="alt";
		for ($i = 0; $i < $n; $i++)
		{
			($classAlt=="alt") ? $classAlt="alt_" : $classAlt="alt";

			$txtHoTen 			= str_replace($search,$replace,$resDM["HO_TEN_SV"][$i]);
			$txtBacDT 			= $resDM["BAC_DAO_TAO"][$i];
			$txtTenBacDT 		= $resDM["TEN_BAC"][$i];
			$txtMaHV 			= $resDM["MA_HOC_VIEN"][$i];
			$txtNamTN			= str_replace($search,$replace,$resDM["NAM_TOT_NGHIEP"][$i]);
			$txtDetai			= str_replace($search,$replace,$resDM["SAN_PHAM_MA_DE_TAI"][$i]);
			$txtLuanAn	 		= str_replace($search,$replace,$resDM["TEN_LUAN_AN"][$i]);
			$txtTruong	 		= str_replace($search,$replace,$resDM["TRUONG"][$i]);
			$txtMahuongdansv	= $resDM["MA_HD_LUAN_AN"][$i];
			if ($txtMaHV!=''){
				$txtTruong = "ĐHBK-ĐHQG-HCM";
				$inputcheck = "";
			}else{
				$inputcheck = "<input type=\"checkbox\" id=\"huongdansvchk".$i."\" name=\"huongdansvchk".$i. "\" value=\"1\" />";
			}
			//else
				//$bk = "";
			if ($txtBacDT=='DH')
				$color = "color:blue;";
			else if ($txtBacDT=='TH')
				$color = "color:gold;";
			else if ($txtBacDT=='TS')
				$color = "color:red;";
			
			//str_replace($search,$replace,
			
			echo "<tr class='fontcontent ".$classAlt."' align='left' valign='top'><input name=\"hiddenMahuongdansv".$i."\" type='hidden' id='hiddenMahuongdansv".$i."' value=\"$txtMahuongdansv\"/>";
			echo "<td style=''>".($i+1)."</td>";
			echo "<td > <span style='font-weight:bold; text-transform:capitalize;'>$txtHoTen</span></td>";
			echo "<td align=left >$txtLuanAn</td>";
			echo "<td align=center><b>$txtNamTN</b></td>";
			echo "<td align=center style='font-weight:bold; $color'>$txtTenBacDT</td>";
			echo "<td align=center>$txtTruong</td>";
			echo "<td align=center>$txtDetai</td>";
			
			echo "<td class='ahref' onclick=\"gethuongdansv('$txtMahuongdansv', '$txtHoTen','$txtLuanAn','$txtNamTN','$txtBacDT','$txtDetai', '$txtMaHV', '$txtTruong');\">Sửa</td>";
			echo "<td >$inputcheck</td>";
			echo "</tr>";
		}
	}
	
	if ($cat == "getnew_huongdansv") 
	{
		// Lay ds thac si Trường Đại Học Bách Khoa Tp.HCM
		$sqlstr="select l.ma_hoc_vien, h.ho || ' ' || h.ten ho_ten, l.ten_de_tai, lpad(dot_cap_bang(l.ma_hoc_vien), 4, '0') nam_tn
		from luan_van_thac_sy l, hoc_vien h
		where diem_luan_van(l.ma_hoc_vien)>=5 
		and (huong_dan_chinh = '$macb' or huong_dan_phu = '$macb')
		and h.ma_hoc_vien = l.ma_hoc_vien 
		and dot_nhan_lv = dot_nhan_lv(h.ma_hoc_vien)
		and h.ma_hoc_vien not in (SELECT MA_HOC_VIEN FROM NCKH_HD_LUAN_AN WHERE FK_MA_CAN_BO='$macb')"; 
		$stmt = oci_parse($db_conn, $sqlstr);oci_execute($stmt);$n = oci_fetch_all($stmt, $resDM);oci_free_statement($stmt);
		$truongbk = 'ĐHBK-ĐHQG-HCM';
		for ($i = 0; $i < $n; $i++)
		{
			$txtHoTen = $resDM["HO_TEN"][$i];
			$txtNamTN = $resDM["NAM_TN"][$i];
			$txtLuanAn = $resDM["TEN_DE_TAI"][$i];
			$txtMaHV = $resDM["MA_HOC_VIEN"][$i];
			
			$sqlstr = "insert into NCKH_HD_LUAN_AN(FK_MA_CAN_BO, MA_HD_LUAN_AN, MA_HOC_VIEN, HO_TEN_SV, BAC_DAO_TAO, NAM_TOT_NGHIEP, TEN_LUAN_AN, TRUONG) values
			('$macb', get_ma_hd_luan_an('$macb'), '$txtMaHV', '$txtHoTen', 'TH', '$txtNamTN', '$txtLuanAn', '$truongbk')";
			$stmt = oci_parse($db_conn, $sqlstr);oci_execute($stmt);
		}
		
		// Lay ds tien si
		$sqlstr="select l.ma_hoc_vien, h.ho || ' ' || h.ten ho_ten, l.ten_luan_an, h.khoa, lpad(dot_cap_bang(l.ma_hoc_vien), 4, '0') nam_tn 
		from  luan_an_tien_sy l, hoc_vien h
		where (l.huong_dan_1 = '".$macb. "' or l.huong_dan_2 = '" .$macb. "' or l.huong_dan_3 = '" .$macb. "') 
		and h.ma_hoc_vien = l.ma_hoc_vien
		and h.ma_hoc_vien not in (SELECT MA_HOC_VIEN FROM NCKH_HD_LUAN_AN WHERE FK_MA_CAN_BO='$macb')";
		$stmt = oci_parse($db_conn, $sqlstr);oci_execute($stmt);$n = oci_fetch_all($stmt, $resDM);oci_free_statement($stmt);
		for ($i = 0; $i < $n; $i++)
		{
			$txtHoTen = $resDM["HO_TEN"][$i];
			$txtNamTN = $resDM["NAM_TN"][$i];
			$txtLuanAn = $resDM["TEN_LUAN_AN"][$i];
			$txtMaHV = $resDM["MA_HOC_VIEN"][$i];
			
			$sqlstr = "insert into NCKH_HD_LUAN_AN(FK_MA_CAN_BO, MA_HD_LUAN_AN, MA_HOC_VIEN, HO_TEN_SV, BAC_DAO_TAO, NAM_TOT_NGHIEP, TEN_LUAN_AN, TRUONG) values
			('$macb', get_ma_hd_luan_an('$macb'), '$txtMaHV', '$txtHoTen', 'TS', '$txtNamTN', '$txtLuanAn', '$truongbk')";
			$stmt = oci_parse($db_conn, $sqlstr);oci_execute($stmt);
		}

	}
	
}
?>


<?php 
if (isset ($db_conn))
	oci_close($db_conn);
?>