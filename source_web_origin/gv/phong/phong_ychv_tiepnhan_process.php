<?php
ini_set('display_errors', '1');

function escapeWEB1($str) // Ap dung dc cho data json
{
	$search = array('\\','"', "\n", "\r", "\t", "\b", "\f");
	$replace = array('\\\\',"&quot;", "" , "", "", "", ""); 
	
	return str_replace($search,$replace,$str);
}

if (isset($_REQUEST["hisid"])){
	session_id($_REQUEST["hisid"]);
	session_start();
}
if (!isset($_SESSION['uidloginPortal'])){
	die('Đã hết phiên làm việc'); 
}

include "../libs/connect.php";
include "../libs/pgslibs.php";

if (!allowPermisstion(base64_decode($_SESSION['uidloginPortal']), '021', $db_conn))
{
	die('{"success":"-2", "msg":"Đã hết thời gian phiên làm việc, vui lòng đăng nhập lại."}'); 
}

$searchdb = array("\\\\","\\'",'\\"', "'");
$replacedb = array("\\","'", '"', "''");

$usr = base64_decode($_SESSION['uidloginPortal']);
//$makhoa = str_replace("'", "''",$_POST['khoa']);
//$khoa = str_replace("'", "''",$_POST['k']);

$a = $_REQUEST["a"];
$m = str_replace("'", "''", $_POST["m"]);

date_default_timezone_set('Asia/Ho_Chi_Minh');

if ($a=='checksession'){
	die('{"success":"1"}'); 
}else if ($a=='getname') {
	$sqlstr="	SELECT ho || ' ' || ten ho_ten, to_char(ngay_sinh, 'dd/mm/yyyy') ngay_sinh, khoa, ten_nganh, noi_sinh, ma_bac
				FROM view_hv_sdh
				WHERE ma_hoc_vien = '$m'"; 
	$stmt = oci_parse($db_conn, $sqlstr);
	oci_execute($stmt);
	$n = oci_fetch_all($stmt, $resDM);
	oci_free_statement($stmt);
	
	//echo $sqlstr;
	if ($resDM["HO_TEN"][0]!="")
		echo '{"hoten":"'.escapeWEB($resDM["HO_TEN"][0]).'", "ngaysinh":"'.$resDM["NGAY_SINH"][0].'", "khoa":"'.$resDM["KHOA"][0].'", "tennganh":"'.escapeWEB($resDM["TEN_NGANH"][0]).'", "noisinh":"'.escapeWEB($resDM["NOI_SINH"][0]).'", "error":"0"}';
	else
		echo '{"hoten":"", "error":"1"}';
}
else if ($a=='getychvu'){
	$sqlstr="	SELECT to_char(add_working_days(so_ngay_xu_ly, sysdate), 'dd/mm/yyyy') ngay_tra, nvl(don_gia,0) don_gia, ghi_chu, n.ho || ' ' || n.ten ho_ten, h.nguoi_giai_quyet
				FROM hvu_dm_yc_hvu h, nhan_su n
				WHERE ma_yc = '$m' and h.nguoi_giai_quyet=n.id"; 
	$stmt = oci_parse($db_conn, $sqlstr);
	oci_execute($stmt);
	$n = oci_fetch_all($stmt, $resDM);
	oci_free_statement($stmt);
	
	$result = '{"ngaytra":"'.$resDM["NGAY_TRA"][0].'", "dg":"'.$resDM["DON_GIA"][0].'", "gc":"'.$resDM["GHI_CHU"][0].'", "gq":"'.$resDM["HO_TEN"][0].'", "mngq":"'.$resDM["NGUOI_GIAI_QUYET"][0].'"}';
	
	echo $result;
}
else if ($a=='addychvu'){
	$today =date("d/m/Y H:i:s"); $time = date("H:i:s");
	$nam = date("y");
	$count = $_POST["c"];
	$error = 0;
	$tenhv = $_POST["thv"];	
	$html1 = "<table style='font-size: 14px; width:100%' class='bordertable' border=1>
			<tr><td align=left style='font-weight:bold'>Mã</td> <td align=left style='font-weight:bold'>Nội dung</td><td style='font-weight:bold' align=center>Số lượng</td><td align=right style='font-weight:bold'>Ngày trả KQ</td></tr>";
	for ($i=0 ; $i < $count; $i++)
	{
		if (!$error)
		{
			$mann = $_POST["mnn"]; // Ma nguoi nhan
			$tennn = $_POST["tnn"]; // Ten nguoi nhan
			$mangq = $_POST["mngq$i"]; // Ma nguoi giai quyet
			$mayc = $_POST["myc$i"]; // Ma yeu cau
			$noidung = str_replace($searchdb, $replacedb,$_POST["n$i"]); // Noi dung
			$sl = $_POST["s$i"]; // So luong
			$phi = str_replace(',','',$_POST["p$i"]); 	// Phi
			$ngaytra = $_POST["nt$i"]; // Ngay tra kq
			$ghichu = str_replace($searchdb, $replacedb,$_POST["g$i"]);
			$mhv = str_replace($searchdb, $replacedb,$_POST["mhv$i"]);
			$thv = str_replace($searchdb, $replacedb,$_POST["tenhv$i"]);
			
			$sqlstr="select New_Ma_GQHVU($nam, 'L') MAGQ from dual"; 
			$stmt = oci_parse($db_conn, $sqlstr);
			if (oci_execute($stmt))
			{
				$n = oci_fetch_all($stmt, $resDM);
				$magqhv = $resDM["MAGQ"][0];
				oci_free_statement($stmt);
				
				if ($magqhv!='')
				{
					$sqlstr="
					insert into hvu_giai_quyet_hvu(MA_GQHVU,FK_MA_HOC_VIEN,HO_TEN_HOC_VIEN,FK_MA_YC,NOI_DUNG_YC,NGAY_TIEP_NHAN,
													NGAY_HEN_TRA_KQ,NGUOI_TIEP_NHAN,SO_LUONG,DON_GIA,GHI_CHU,NGUOI_GIAI_QUYET, TINH_TRANG) 
					values ('$magqhv','$mhv','$thv','$mayc','$noidung', sysdate,to_date('$ngaytra','dd/mm/yyyy'),'$mann',$sl,$phi,'$ghichu','$mangq', 0)"; 
					$stmt = oci_parse($db_conn, $sqlstr);
					
						/*file_put_contents("logs.txt", "----------------------------------------------\n
							". date("H:i:s d.m.Y")." $sqlstr \n
							----------------------------------------------\n");*/
					
					if (oci_execute($stmt))
					{
						$html1 .="
						<tr><td align=left style='font-weight:bold'>$magqhv</td> <td align=left>$noidung</td><td align=center>$sl</td><td align=right>$ngaytra</td></tr>
						";
						if ($mangq != "")
						{
							oci_free_statement($stmt);
							$sqlstr="insert into HVU_QUA_TRINH_GIAI_QUYET(FK_MA_GQHVU,NGAY,NGUOI_XU_LY, GHI_CHU) values ('$magqhv', sysdate,'$mangq', '$ghichu')"; 
							$stmt = oci_parse($db_conn, $sqlstr);
							if (!oci_execute($stmt))
							{
								$error = 1;
								$e = oci_error($stmt);
								$msgerr = $e['message']. " sql: " . $e['sqltext'];
								echo '{"success":"-1", "msgerr":"'.escapeWEB($msgerr).'"}';
							}
							else
								oci_free_statement($stmt);
						}
					}
					else
					{
						$error = 1;
						$e = oci_error($stmt);
						$msgerr = $e['message']. " sql: " . $e['sqltext'];
						echo '{"success":"-1", "msgerr":"'.escapeWEB($msgerr).'"}';
					}
						
				}
			}
			else
			{
				$error = 1;
				$e = oci_error($stmt);
				$msgerr = $e['message']. " sql: " . $e['sqltext'];
				echo '{"success":"-1", "msgerr":"'.escapeWEB($msgerr).'"}';
			}
		}
	}
	$html1 .= "</table>"; 
	
	$ngay =date("d");$thang =date("m");$nam =date("Y");
	$html = "
			<style>
			.bordertable {
				border-color: #000000; 
				border-width: 1px; 
				border-style: solid; 
				border-collapse:collapse;
			}
			</style>
			<table style='font-size: 13px; width:100%'>
			<tr>
				<td align=left style='width:50%'>
					<div align=center style='width:300px; margin-top:-10px'>
					TRƯỜNG ĐẠI HỌC BÁCH KHOA<br/>
					<U><B>PHÒNG ĐÀO TẠO SĐH</B></U>
					</div>
				</td>
				<td align=right style='width:50%'> 
					<div align=center style='width:300px; margin-top:-10px'><B>CỘNG HÒA XÃ HỘI CHỦ NGHĨA VIỆT NAM</B><br/>
					<u>ĐỘC LẬP - TỰ DO - HẠNH PHÚC</u>
					</div>
				</td>
			</tr>
			<tr><td align=center colspan=2><div style='font-weight: bold; font-size: 15px; margin: 10px 0 10px 0;' >BIÊN NHẬN HỌC VỤ</div></td></tr>
			<tr><td align=left style='' colspan=2><div style='font-size: 14px'>Phòng ĐT-SĐH có nhận phiếu đề nghị giải quyết học vụ của học viên <b>$tenhv</b> (<b>$m</b>):</div></td></tr>
			<tr><td align=center colspan=2>$html1</td></tr>
			<tr>
				<td align=center style='width:50%'></td>
				<td align=right style='width:50%'> 
					<div align=center style='width:300px; margin-top:10px'>Ngày $ngay tháng $thang năm $nam <br/>
					<b>Người nhận</b> <br/><br/><br/><br/><br/><br/>
					<b>$tennn</b>
					</div>
				</td>
			</tr>
			</table>";
	
	
	if (!$error)
		echo '{"success":"1", "html":"'.escapeJsonString($html).'"}';
	
	oci_free_statement($stmt);
}
else if ($a=='refreshdata'){
	$fttr = str_replace ("'", "''", $_REQUEST["fttr"]); // filter thung rac
	$ftt = str_replace ("'", "''", $_REQUEST["ftt"]); // filter tinh trang
	$fnxl = str_replace ("'", "''", $_REQUEST["fnxl"]); // filter nguoi xu ly
	$fnn = str_replace ("'", "''", $_REQUEST["fnn"]); // filter nguoi nhan
	$fhvnyc = str_replace ("'", "''", $_REQUEST["fhvnyc"]); // filter hoc vien nhan yeu cau hoc vu hay chua
	$fhvnnhan = str_replace ("'", "''", $_REQUEST["fhvnnhan"]); // filter hoc vien nam nhan hoc vu
	$filterstr = "";
	
	if ($fttr != "")
		$filterstr .= " AND hvu.THUNG_RAC is not null";
	else
		$filterstr .= " AND hvu.THUNG_RAC is null";
		
	if ($ftt != "")
	{
		if ($ftt == "!2") // Loai tru Đã xử lý
			$filterstr .= " AND hvu.TINH_TRANG <> ". str_replace('!','',$ftt);
		else
			$filterstr .= " AND hvu.TINH_TRANG = $ftt";
	}
	if ($fnxl != "")
		$filterstr .= " AND hvu.NGUOI_GIAI_QUYET = '$fnxl'";
	if ($fnn != "")
		$filterstr .= " AND hvu.NGUOI_TIEP_NHAN = '$fnn'";
	if ($fhvnyc != "" && $fhvnyc == "0")
		$filterstr .= " AND hvu.NGAY_TRA_KQ is null";
	if ($fhvnyc != "" && $fhvnyc == "1")
		$filterstr .= " AND hvu.NGAY_TRA_KQ is not null";
	if ($fhvnnhan != "")
		$filterstr .= " AND to_char(hvu.NGAY_TIEP_NHAN, 'yyyy') = '$fhvnnhan'";
	
	$sqlstr="	SELECT ma_gqhvu, noi_dung_yc, to_char(ngay_tiep_nhan, 'yyyy-mm-dd') ngay_tiep_nhan, to_char(ngay_hen_tra_kq, 'yyyy-mm-dd') ngay_hen_tra_kq, 
				GET_GHI_CHU_YCHV(ma_gqhvu) ghi_chu, fk_ma_hoc_vien, hv.ho || ' ' || hv.ten ho_ten_hv, HO_TEN_HOC_VIEN ho_ten_hv_1, ma_bac, 
				decode(nvl(trang_thai_in, 1), '1', 'checked', '') tt_in, (n.ten) ten_nguoi_giai_quyet, (n.id) id_nguoi_giai_quyet,
				tt.ten_tat ten_tinh_trang, hvu.ket_qua, (n1.ho || ' ' || n1.ten) ten_nguoi_tiep_nhan, hvu.so_luong,don_gia, 
				decode(ngay_tra_kq, null, 0, 1) tra_hv, (n2.ho || ' ' || n2.ten) ten_nguoi_chuyen, vi_tri_luu, to_char(ngay_tra_kq, 'yyyy-mm-dd hh:mi') ngay_tra_kq,
				get_qua_trinh_gq(ma_gqhvu) qua_trinh_chuyen, ceil(ngay_hen_tra_kq-sysdate) het_han, (n3.ho || ' ' || n3.ten) ten_nguoi_tra
				FROM hvu_giai_quyet_hvu hvu, view_hv_sdh hv, nhan_su n, hvu_dm_tinh_trang tt, nhan_su n1, nhan_su n2, nhan_su n3
				WHERE fk_ma_hoc_vien = hv.ma_hoc_vien(+) and hvu.nguoi_giai_quyet = n.id(+) and hvu.NGUOI_TIEP_NHAN = n1.id(+) and hvu.tinh_trang = tt.ma_tinh_trang(+)
				and hvu.nguoi_chuyen = n2.id(+) and hvu.nguoi_tra_kq = n3.id(+)
				$filterstr";
	/*file_put_contents("logs.txt", "----------------------------------------------\n
				". date("H:i:s d.m.Y")." $sqlstr \n
				----------------------------------------------\n", FILE_APPEND);*/
	$stmt = oci_parse($db_conn, $sqlstr);oci_execute($stmt);$n = oci_fetch_all($stmt, $resDM);oci_free_statement($stmt);
	$data='{
			"aaData":[';
	
	for ($i = 0; $i < $n; $i++)
	{
		if ($resDM["TRA_HV"][$i] == 0)
			$trahv = "<img src='icons/circle-red.png' border=0 class='trahvicon' style='cursor:pointer'>";
		else
			$trahv = "<img src='icons/circle-green.png' border=0 title='Trả ngày: {$resDM["NGAY_TRA_KQ"][$i]}<br/>Người trả: {$resDM["TEN_NGUOI_TRA"][$i]}' class='phuchoitrahvicon tooltips' style='cursor:pointer'>";
		
		if ($resDM["HO_TEN_HV"][$i]!=" "){
			$ho_ten = $resDM["HO_TEN_HV"][$i];
		}else{
			$ho_ten = $resDM["HO_TEN_HV_1"][$i];
		}
		
		$data.= '["<input type=checkbox id=selectYCHV'.$i.' name=selectYCHV'.$i.' value=\''.$resDM["MA_GQHVU"][$i].'\' '.''.'>",
				  "'.$resDM["MA_GQHVU"][$i].'", "'.escapeJsonString($resDM["NOI_DUNG_YC"][$i]).'", "'.$resDM["SO_LUONG"][$i].'",
				  "<img class=\'giaiquyethv tooltips\' src=\'icons/document_save.png\' title=\'Xử lý học vụ\' border=0 style=\'cursor:pointer\' >",
				  "<img class=\'chuyennguoigiaiquyet tooltips\' src=\'icons/arrow_right.png\' title=\'Chuyển giải quyết\' border=0 style=\'cursor:pointer\' >", "'.$resDM["TEN_NGUOI_GIAI_QUYET"][$i].'",
				  "'.$resDM["NGAY_TIEP_NHAN"][$i].'", "'.$resDM["NGAY_HEN_TRA_KQ"][$i].'",
				  "'.$trahv.'", "'.$resDM["FK_MA_HOC_VIEN"][$i].'",
				  "'.escapeJsonString($ho_ten).'",
				  "'.$resDM["TEN_TINH_TRANG"][$i].'", "'.escapeJsonString($resDM["KET_QUA"][$i]).'",
				  "'.number_format($resDM["DON_GIA"][$i]).'", "<img src=\'icons/details_open.png\' border=0 class=detailsicon>",
				  "'.$resDM["TEN_NGUOI_TIEP_NHAN"][$i].'", "'.$resDM["SO_LUONG"][$i].'",
				  "'.$resDM["TEN_NGUOI_CHUYEN"][$i].'",
				  "'.escapeJsonString($resDM["VI_TRI_LUU"][$i]).'", "'.escapeJsonString($resDM["QUA_TRINH_CHUYEN"][$i]).'",
				  "'.escapeJsonString($resDM["GHI_CHU"][$i]).'", "'.$resDM["HET_HAN"][$i].'",
				  "'.escapeJsonString($resDM["NGAY_TRA_KQ"][$i]).'", "'.$resDM["ID_NGUOI_GIAI_QUYET"][$i].'"
				 ],';
		// data[24] là item cuối
	}
	
	if ($n>0) 
		$data=substr($data,0,-1);
	
	$data.='	]
			}';
	
	echo $data;
}
else if ($a=='chuyenxuly'){
	$error = 0;
	$magqhv = "'".str_replace(',', "','", $m)."'";
	$mangq = str_replace("'", "''",$_POST["mnxl"]);
	$manc = str_replace("'", "''",$_POST["mnc"]);
	$ghichu = str_replace("'", "''", $_POST["phong_tnychv_ghichu_ychv_chuyen"]);

	// cap nhat table HVU_GIAI_QUYET_HVU truoc
	$sqlstr="
	update hvu_giai_quyet_hvu set NGUOI_GIAI_QUYET = '$mangq', NGUOI_CHUYEN = '$manc'
	where MA_GQHVU in ($magqhv)"; 
	$stmt = oci_parse($db_conn, $sqlstr);

	if (!oci_execute($stmt))
	{
		$error = 1;
		oci_free_statement($stmt);
	}
	else
	{
		oci_free_statement($stmt);
		$magqhv_arr = explode(",", $magqhv); // tách chuổi $magqhvs thành từng array chứa giá trị '1' hoặc '2' hoặc '3' hoặc '4'
		
		foreach ($magqhv_arr as $fk_ma_gqhv) 
		{
			$sqlstr="insert into HVU_QUA_TRINH_GIAI_QUYET (FK_MA_GQHVU, GHI_CHU, NGAY, NGUOI_CHUYEN, NGUOI_XU_LY) 
					values($fk_ma_gqhv, '$ghichu', sysdate, '$manc', '$mangq')";
					
			/*file_put_contents("logs.txt", "----------------------------------------------\n
				". date("H:i:s d.m.Y")." $sqlstr \n
				----------------------------------------------\n", FILE_APPEND);*/
				
			$stmt = oci_parse($db_conn, $sqlstr);
			if (!oci_execute($stmt))
				$error = 1;
			
			oci_free_statement($stmt);
		}
	}

	if (!$error)
	{
		//oci_commit($db_conn);
		echo '{"success":"1"}';
	}
	else
		echo '{"success":"-1"}';
}
else if ($a=='CapnhatTraHV'){
	$error = 0;
	$magqhv = "'".str_replace(',', "','", $m)."'";
	$mantra = $_POST["mntra"];
	$today =date("Y-m-d H:i");
	$tenntra = $_POST["tenntra"];
	
	// cap nhat table HVU_GIAI_QUYET_HVU ở local
	$sqlstr="update hvu_giai_quyet_hvu set NGAY_TRA_KQ = sysdate, NGUOI_TRA_KQ = '$mantra'
	where MA_GQHVU in ($magqhv)"; 
	$stmt = oci_parse($db_conn, $sqlstr);
	if (!oci_execute($stmt))
		$error = 1;
	else
	{
		// Cap nhat hvu_giai_quyet_hvu ở web
		$sqlstr="update hvu_giai_quyet_hvu@db_link set NGAY_TRA_KQ = sysdate, NGUOI_TRA_KQ = '$mantra'
		where MA_GQHVU in ($magqhv)"; 
		$stmt = oci_parse($db_conn, $sqlstr);oci_execute($stmt);oci_free_statement($stmt);
	}
	
	if (!$error)
		echo '{"success":"1", "time":"'.$today.'", "name":"'.escapeWEB($tenntra).'"}';
	else
		echo '{"success":"-1"}';
}
else if ($a=='ThungRac'){
	$error = 0;
	$magqhv = "'".str_replace(',', "','", $m)."'";
	$today =date("Y-m-d H:i");
	$c = str_replace("'", "''", $_POST["c"]);
	if ($c == '')
		$c = '1';
	else
		$c = 'null';

	// cap nhat table HVU_GIAI_QUYET_HVU truoc
	$sqlstr="
	update hvu_giai_quyet_hvu set THUNG_RAC = $c
	where MA_GQHVU in ($magqhv)"; 
	$stmt = oci_parse($db_conn, $sqlstr);
	if (!oci_execute($stmt))
		$error = 1;
	else
	{
		// Cap nhat hvu_giai_quyet_hvu trên web
		oci_free_statement($stmt);
		$sqlstr="
		update hvu_giai_quyet_hvu@db_link set THUNG_RAC = $c
		where MA_GQHVU in ($magqhv)"; 
		$stmt = oci_parse($db_conn, $sqlstr);oci_execute($stmt);oci_free_statement($stmt);
	}
	
	if (!$error)
		echo '{"success":"1", "time":"'.$today.'", "ma":"'.str_replace(',', ", ", $m).'"}';
	else
		echo '{"success":"-1", "ma":"'.str_replace(",", ", ", $m).'"}';
}
else if ($a=='PhucHoiCapnhatTraHV'){
	$error = 0;
	$magqhv = "'".str_replace(',', "','", $m)."'";
	$mantra = $_POST["mntra"];
	$today =date("Y-m-d H:i");
	
	// cap nhat table HVU_GIAI_QUYET_HVU truoc
	$sqlstr="
	update hvu_giai_quyet_hvu set NGAY_TRA_KQ = NULL, NGUOI_TRA_KQ = '$mantra'
	where MA_GQHVU in ($magqhv)"; 
	$stmt = oci_parse($db_conn, $sqlstr);
	if (!oci_execute($stmt))
		$error = 1;
	else
	{
		// Cap nhat hvu_giai_quyet_hvu trên web
		$sqlstr="update hvu_giai_quyet_hvu@db_link set NGAY_TRA_KQ = NULL, NGUOI_TRA_KQ = '$mantra'
		where MA_GQHVU in ($magqhv)"; 
		$stmt = oci_parse($db_conn, $sqlstr);oci_execute($stmt);oci_free_statement($stmt);
	}
	
	if (!$error)
		echo '{"success":"1", "time":"'.$today.'"}';
	else
		echo '{"success":"-1"}';
}
else if ($a=='xuly'){
	$error = 0;
	$magqhv = "'".str_replace(',', "','", $m)."'";
	$mangq = str_replace("'", "''",$_POST["mnxl"]);
	$vitriluu = str_replace("'", "''",$_POST["phong_tnychv_vitriluu_xuly"]);
	$tinhtrang = str_replace("'", "''", $_POST["phong_tnychv_tinhtrang_xuly"]);
	$ketqua = str_replace("'", "''", $_POST["phong_tnychv_ketqua_xuly"]);
	$hentra = str_replace("'", "''", $_POST["phong_tnychv_hentra_xuly"]);
	$log = "";
	if ($ketqua!="")
		$log = "(KQ: $ketqua)";
	if ($vitriluu!="")
		$log .= "(VITRI: $vitriluu)";
	if ($tinhtrang!="")
		$log .= "(T.TRANG: $tinhtrang)";
				
	// cap nhat table HVU_GIAI_QUYET_HVU truoc
	$sqlstr="update hvu_giai_quyet_hvu set VI_TRI_LUU = '$vitriluu', TINH_TRANG = '$tinhtrang', KET_QUA = '$ketqua', NGAY_HEN_TRA_KQ = to_date('$hentra','dd/mm/yyyy')
	where MA_GQHVU in ($magqhv)";
	$stmt = oci_parse($db_conn, $sqlstr);
		
	/*file_put_contents("logs.txt", "----------------------------------------------\n
				". date("H:i:s d.m.Y")." $sqlstr \n
				----------------------------------------------\n", FILE_APPEND);*/
	
	if (!oci_execute($stmt))
	{
		$error = 1;
		oci_free_statement($stmt);
	}
	else
	{
		oci_free_statement($stmt);
		
		// Cap nhat hvu_giai_quyet_hvu tren web
		$sqlstr="update hvu_giai_quyet_hvu@db_link set VI_TRI_LUU = '$vitriluu', TINH_TRANG = '$tinhtrang', KET_QUA = '$ketqua', NGAY_HEN_TRA_KQ = to_date('$hentra','dd/mm/yyyy')
		where MA_GQHVU in ($magqhv)";
		$stmt = oci_parse($db_conn, $sqlstr);oci_execute($stmt);oci_free_statement($stmt);
		
		
		$magqhv_arr = explode(",", $magqhv); // tách chuổi $magqhvs thành từng array chứa giá trị '1' hoặc '2' hoặc '3' hoặc '4'
		
		foreach ($magqhv_arr as $fk_ma_gqhv) 
		{
			$sqlstr="insert into HVU_QUA_TRINH_GIAI_QUYET (FK_MA_GQHVU, LOG, NGAY, NGUOI_XU_LY)
					values($fk_ma_gqhv, '$log', sysdate, '$mangq')";
			
			/*file_put_contents("logs.txt", "----------------------------------------------\n
				". date("H:i:s d.m.Y")." $sqlstr \n
				----------------------------------------------\n", FILE_APPEND);*/
			
			$stmt = oci_parse($db_conn, $sqlstr);
			if (!oci_execute($stmt))
				$error = 1;
			
			oci_free_statement($stmt);
		}
	}

	if (!$error)
	{
		//oci_commit($db_conn);
		echo '{"success":"1"}';
	}
	else
		echo '{"success":"-1"}';
}
else if ($a=='getnewychv'){	
	$error = 0;
	
	// Download các yêu cầu trên web về local
	$sqlstr="insert into hvu_giai_quyet_hvu select * from hvu_giai_quyet_hvu@db_link where MA_GQHVU not in (select MA_GQHVU from hvu_giai_quyet_hvu)"; 
	$stmt = oci_parse($db_conn, $sqlstr);
	
	if (!oci_execute($stmt))
		$error = 1;
	else
	{	
		oci_free_statement($stmt);
		$sqlstr="insert into hvu_qua_trinh_giai_quyet select * from hvu_qua_trinh_giai_quyet@db_link t1
		where 0 = (select count( * ) from hvu_qua_trinh_giai_quyet t2 where t1.FK_MA_GQHVU = t2.FK_MA_GQHVU and t1.ngay = t2.ngay)"; 
		$stmt = oci_parse($db_conn, $sqlstr);
		
		if (!oci_execute($stmt))
			$error = 1;
	}
	// End download
	
	if (!$error)
		echo '{"success":"1"}';
	else
	{
		$e = oci_error($stmt);
        $msgerr = $e['message'];
		echo '{"success":"-1", "msgerr":"'.escapeWEB($msgerr).'"}';
	}
	oci_free_statement($stmt);
}

if (isset ($db_conn))
	oci_close($db_conn);
?>