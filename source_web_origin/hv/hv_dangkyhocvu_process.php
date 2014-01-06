<?php
if (isset($_REQUEST["hisid"])){
	session_id($_REQUEST["hisid"]);
	session_start();
}
//if (!isset($_SESSION['uidloginhv'])){
//	die('Truy cập bất hợp pháp'); 
//}

include "libs/connect.php";

?>

<?php

$type = $_REQUEST['a'];

$m = str_replace("'", "''", $_POST["m"]);

if ($type=='dangkyhocvu')
{
	$error = 0;
	$today =date("d/m/Y H:i:s"); $time = date("H:i:s");
	$nam = date("y"); 
	$count = $_POST["c"];
	
	
	for ($i=1 ; $i < $count; $i++)
	{
		$mayc = str_replace("'", "''",$_POST["myc$i"]); // Ma yeu cau
		$noidung = str_replace("'", "''",$_POST["n$i"]); // Noi dung
		$sl = str_replace("'", "''",$_POST["s$i"]); // So luong

		$sqlstr="select add_working_days(so_ngay_xu_ly, sysdate) ngay_tra, don_gia, nguoi_giai_quyet from hvu_dm_yc_hvu where ma_yc = '$mayc'"; 
		$stmt = oci_parse($db_conn, $sqlstr);oci_execute($stmt);$n = oci_fetch_all($stmt, $resDM);oci_free_statement($stmt);
		$phi = $resDM["DON_GIA"][0] * $sl; // Phí
		$mangq = $resDM["NGUOI_GIAI_QUYET"][0]; // Người giải quyết
		$ngaytra = $resDM["NGAY_TRA"][0]; // Ngay tra kq
		
		$sqlstr="select New_Ma_GQHVU($nam, 'N') MAGQ from dual"; 
		$stmt = oci_parse($db_conn, $sqlstr);oci_execute($stmt);$n = oci_fetch_all($stmt, $resDM);oci_free_statement($stmt);
		$magqhv = $resDM["MAGQ"][0];
		
		if ($magqhv!='')
		{
			$sqlstr="
			insert into hvu_giai_quyet_hvu(MA_GQHVU,FK_MA_HOC_VIEN,FK_MA_YC,NOI_DUNG_YC,NGAY_TIEP_NHAN,
											NGAY_HEN_TRA_KQ,SO_LUONG,DON_GIA,NGUOI_GIAI_QUYET, TINH_TRANG) 
			values ('$magqhv','$m','$mayc','$noidung', sysdate, '$ngaytra',$sl,$phi,'$mangq', 0)"; 
			$stmt = oci_parse($db_conn, $sqlstr);
			
			/*file_put_contents("logs.txt", "----------------------------------------------\n
					". date("H:i:s d.m.Y")." $sqlstr \n
					----------------------------------------------\n", FILE_APPEND);*/
			if (!oci_execute($stmt))
				$error = 1;
			else
			{
				//if ($mangq != "")
				//{
				oci_free_statement($stmt);
				$sqlstr="insert into HVU_QUA_TRINH_GIAI_QUYET(FK_MA_GQHVU,NGAY,NGUOI_XU_LY, GHI_CHU) values ('$magqhv', sysdate,'$mangq', 'Đăng ký online')"; 
				$stmt = oci_parse($db_conn, $sqlstr);oci_execute($stmt);
				//}
			}
				
			oci_free_statement($stmt);
		}
	}
	
	if (!$error)
		echo '';
	else
		echo 'error';
}
else if ($type=='getDSGQHocVu')
{
	$sqlstr="	select MA_GQHVU, NOI_DUNG_YC, to_char(NGAY_TIEP_NHAN, 'dd-mm-yyyy') NGAY_TIEP_NHAN, to_char(NGAY_HEN_TRA_KQ, 'dd-mm-yyyy') NGAY_HEN_TRA_KQ, 
				(n.ten) TEN_NGUOI_GIAI_QUYET, tt.TEN_TINH_TRANG, hvu.KET_QUA, hvu.SO_LUONG, hvu.DON_GIA, to_char(NGAY_TRA_KQ, 'dd-mm-yyyy hh:mi') NGAY_TRA_KQ
				from hvu_giai_quyet_hvu hvu, nhan_su n, hvu_dm_tinh_trang tt
				where fk_ma_hoc_vien = '$m' and hvu.nguoi_giai_quyet = n.id(+) and hvu.tinh_trang = tt.ma_tinh_trang(+) and thung_rac is null
				order by ma_gqhvu desc
				";
	/*file_put_contents("logs.txt", "----------------------------------------------\n
				". date("H:i:s d.m.Y")." $sqlstr \n
				----------------------------------------------\n", FILE_APPEND);*/
	$stmt = oci_parse($db_conn, $sqlstr);oci_execute($stmt);$n = oci_fetch_all($stmt, $resDM);oci_free_statement($stmt);
	
	$classAlt = 'alt';
	$classXL = '';
	for ($i = 0; $i < $n; $i++)
	{
		($classAlt=='alt') ? $classAlt="alt_" : $classAlt="alt";
		$tinhtrang = $resDM["TEN_TINH_TRANG"][$i];
		
		if ($tinhtrang == "Chưa xử lý")
			$classXL = "color: #bc3604; font-weight: bold;";
		else if ($tinhtrang == "Đang xử lý")
			$classXL = "color: blue; font-weight: bold;";
		else if ($tinhtrang == "Đã xử lý")
			$classXL = "color: #96c716; font-weight: bold;";
		else if ($tinhtrang == "Trình lãnh đạo")
			$classXL = "color: blue; font-weight: bold;";
			
		echo "<tr align='left' valign='top' class=' ".$classAlt."' style='height:20px;'>";
		echo "<td valign=middle align=left style='margin-left:2px;'><b>".$resDM["MA_GQHVU"][$i]."</b></td>";
		echo "<td valign=middle align='left'>".$resDM["NOI_DUNG_YC"][$i] ."</td>";
		echo "<td valign=middle align='center'>". $resDM["SO_LUONG"][$i]."</td>";
		echo "<td valign=middle align='right'>".number_format($resDM["DON_GIA"][$i])."</td>";
		echo "<td valign=middle align='left'>".$resDM["NGAY_TIEP_NHAN"][$i]."</td>";
		echo "<td valign=middle align='left'><b>".$resDM["NGAY_HEN_TRA_KQ"][$i]."</b></td>";
		echo "<td valign=middle align='left' style='color:red'>".$resDM["KET_QUA"][$i]."</td>";
		echo "<td valign=middle align='right' style='$classXL'>$tinhtrang</td>";
		echo "<td valign=middle align='right'><b>".$resDM["NGAY_TRA_KQ"][$i]."</b></td>";

		echo "</tr>";
	}
}

if (isset ($db_conn))
	oci_close($db_conn);
?>