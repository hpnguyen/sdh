<?php
//ini_set('display_errors', '1');

//  bien nay dung de set folder upload hinh cho ckfinder upload hinh trong ckeditor, chi dung cho tool quan ly tmdt
$_SESSION["khcn_username"]="";

if (isset($_REQUEST["hisid"])){
	session_id($_REQUEST["hisid"]);
	session_start();
}

// success: -1: err; -2: hết phiên làm việc; -3: Truy cập bất hợp pháp
if (!isset($_SESSION['uidloginPortal'])){
	die('{"success":"-2", "msgerr":"Đã hết thời gian phiên làm việc, vui lòng đăng nhập lại."}'); 
}

include "../libs/connectnckhda.php";
include "../libs/connect.php";
include "../libs/pgslibs.php";

if (!allowPermisstion(base64_decode($_SESSION['uidloginPortal']), '001', $db_conn)){
	die('{"success":"-3", "msgerr":"Truy cập bất hợp pháp."}'); 
}

$searchdb = array("\\\\","\\'",'\\"', "'");  //array("'",","), array("''","") 
$replacedb = array("\\","'", '"', "''");

$usr = base64_decode($_SESSION['uidloginPortal']);
//$makhoa = str_replace("'", "''",$_POST['khoa']);
//$khoa = str_replace("'", "''",$_POST['k']);

$a = $_REQUEST["a"];
$macb = $_SESSION['macb'];

date_default_timezone_set('Asia/Ho_Chi_Minh');

$sqlstr="SELECT value , (sysdate - to_date(value,'HH24:MI dd/mm/yyyy')) het_han FROM config WHERE name='DKDT_NGAY_KT'";
$stmt = oci_parse($db_conn_khcn, $sqlstr);oci_execute($stmt);$n = oci_fetch_all($stmt, $resDM);oci_free_statement($stmt);
$ngayhethanDKDT = $resDM["VALUE"][0]; $hethan = $resDM["HET_HAN"][0];

$sqlstr="SELECT value , (sysdate - to_date(value,'dd/mm/yyyy')) bat_dau FROM config WHERE name='DKDT_NGAY_BD'";
$stmt = oci_parse($db_conn_khcn, $sqlstr);oci_execute($stmt);$n = oci_fetch_all($stmt, $resDM);oci_free_statement($stmt);
$ngaybatdauDKDT = $resDM["VALUE"][0]; $batdau = $resDM["BAT_DAU"][0];

if ($a=='checksession'){
	die('{"success":"1"}'); 
}

if ($a=='checkedit'){
	
}

if ($a=='ThungRac'){
	$madt = str_replace("'", "''", $_POST["m"]);
	$today =date("Y-m-d H:i");
	$c = str_replace("'", "''", $_POST["c"]);
	($c == '') ? $c = '1' : $c = 'null';
	
	if ($madt!=''){
		
		$sqlstr = "select tt.EDIT_ALLOW
		from nckh_thuyet_minh_de_tai dt, NCKH_DM_TINH_TRANG tt
		where dt.FK_TINH_TRANG = tt.MA_TINH_TRANG and dt.MA_THUYET_MINH_DT='$madt'";
		$stmt = oci_parse($db_conn_khcn, $sqlstr);
		if (oci_execute($stmt)){
			$n = oci_fetch_all($stmt, $resDM); oci_free_statement($stmt);
			if ($resDM["EDIT_ALLOW"][0]==1){
				$sqlstr="update NCKH_THUYET_MINH_DE_TAI set THUNG_RAC = $c
				where MA_THUYET_MINH_DT = '$madt'";
				$stmt = oci_parse($db_conn_khcn, $sqlstr);
				if (oci_execute($stmt)){
					echo '{"success":"1", "time":"'.$today.'", "ma":"'.$madt.'"}';
				}else{
					echo '{"success":"-1", "ma":"'.$madt.'"}';
				}
			}
		}else{
			echo '{"success":"-1", "ma":"'.$madt.'"}';
		}
	}else{
		echo '{"success":"-1", "ma":"'.$madt.'"}';
	}
	
}

if ($a=='regthuyetminh'){
	
	$tenviet = str_replace("'", "''", $_POST["khcn_frm_reg_dtkhcn_ten_dt_viet"]);
	$tenanh = str_replace("'", "''", $_POST["khcn_frm_reg_dtkhcn_ten_dt_anh"]);
	$nganhkhac = str_replace("'", "''", $_POST["khcn_frm_reg_nganhkhac"]);
	$nganhhep = str_replace("'", "''", $_POST["khcn_frm_reg_dtkhcn_cnganhhep"]);
	$capdetai = str_replace("'", "''", $_POST["khcn_frm_reg_dtkhcn_capdetai"]);
	$loaihinhnc = str_replace("'", "''", $_POST["khcn_frm_reg_dtkhcn_loaihinhnc"]);
	$thoigian = str_replace("'", "''", $_POST["khcn_frm_reg_dtkhcn_thoigianthuchien"]);
	$kinhphi = 0;//str_replace("'", "''", $_POST["khcn_frm_reg_dtkhcn_tongkinhphi"]);
	$keywords = str_replace("'", "''", $_POST["khcn_frm_reg_dtkhcn_keywords"]);
	$huongdt = str_replace("'", "''", $_POST["khcn_frm_reg_dtkhcn_huongdt"]);
	$donvi = str_replace("'", "''", $_POST["khcn_frm_reg_dtkhcn_dvdk"]);
	
	$countnganh = str_replace("'", "''", $_POST["c"]);
	
	$sqlstr="SELECT TEN_CAP, (sysdate - DKDT_NGAY_BD) bat_dau, (sysdate-DKDT_NGAY_KT) het_han, 
	to_char(DKDT_NGAY_BD, 'dd/mm/yyyy') ngay_bd_f, to_char(DKDT_NGAY_KT, 'HH24:MI dd/mm/yyyy') ngay_kt_f 
	FROM CAP_DE_TAI WHERE MA_CAP='$capdetai'";
	$stmt = oci_parse($db_conn_khcn, $sqlstr);oci_execute($stmt);$n = oci_fetch_all($stmt, $resDM);oci_free_statement($stmt);
	$batdau = $resDM["BAT_DAU"][0];
	$hethan = $resDM["HET_HAN"][0];
	
	if ($batdau>0 && $hethan<0)
	{
		$sqlstr="select get_ma_thuyet_minh_dt matm from dual"; 
		$stmt = oci_parse($db_conn_khcn, $sqlstr);
		if (oci_execute($stmt))
		{
			$n = oci_fetch_all($stmt, $resDM); oci_free_statement($stmt);
			$matm = $resDM["MATM"][0];
			
			if ($matm!='')
			{
				$sqlstr="
				insert into NCKH_THUYET_MINH_DE_TAI(MA_THUYET_MINH_DT,FK_MA_CAN_BO,TEN_DE_TAI_VN,TEN_DE_TAI_EN,CHUYEN_NGANH_HEP,FK_CAP_DE_TAI,
				FK_LOAI_HINH_NC,THOI_GIAN_THUC_HIEN,TONG_KINH_PHI, NGAY_DANG_KY, KEYWORDS, HUONG_DE_TAI, FK_TINH_TRANG,FK_BO_MON_DANG_KY)
				values ('$matm','$macb','$tenviet','$tenanh','$nganhhep','$capdetai','$loaihinhnc',$thoigian,$kinhphi, sysdate,'$keywords','$huongdt', '01', '$donvi')"; 
				$stmt = oci_parse($db_conn_khcn, $sqlstr);
				/*
					file_put_contents("logs.txt", "----------------------------------------------\n
						". date("H:i:s d.m.Y")." $sqlstr \n
						----------------------------------------------\n", FILE_APPEND);
				*/
				if (oci_execute($stmt))
				{
					for ($i=0; $i<$countnganh; $i++)
					{
						$nganhnhomnganh = str_replace("'", "''", $_POST["khcn_frm_reg_nganh$i"]);
						if ($nganhnhomnganh!='')
						{
							//file_put_contents("logs.txt", date("H:i:s d.m.Y")." $nganhnhomnganh \n", FILE_APPEND);
							if ($nganhnhomnganh=='999')
								$sqlstr="insert into NCKH_NHOM_NGANH_TMDT(FK_MA_THUYET_MINH_DT,FK_MA_NHOM_NGANH,TEN_NHOM_NGANH_KHAC) values ('$matm','$nganhnhomnganh','$nganhkhac')"; 
							else
								$sqlstr="insert into NCKH_NHOM_NGANH_TMDT(FK_MA_THUYET_MINH_DT,FK_MA_NHOM_NGANH) values ('$matm','$nganhnhomnganh')"; 
								
							$stmt = oci_parse($db_conn_khcn, $sqlstr);
							if (!oci_execute($stmt))
							{
								$e = oci_error($stmt);
								$msgerr = $e['message']. " sql: " . $e['sqltext'];
								die ('{"success":"-1", "msgerr":"'.escapeWEB($msgerr).'"}');
								break 1;
							}
						}
					}
					
					// Cấp ĐHQG
					if ($capdetai == '21' || $capdetai == '22' || $capdetai == '23' || $capdetai == '24'){
						$sqlstr="insert into NCKH_TONG_HOP_KINH_PHI(FK_MA_THUYET_MINH_DT,FK_MA_KHOAN_CHI_PHI) values ('$matm','001')"; 
						$stmt = oci_parse($db_conn_khcn, $sqlstr); oci_execute($stmt);oci_free_statement($stmt);
						$sqlstr="insert into NCKH_TONG_HOP_KINH_PHI(FK_MA_THUYET_MINH_DT,FK_MA_KHOAN_CHI_PHI) values ('$matm','002')"; 
						$stmt = oci_parse($db_conn_khcn, $sqlstr); oci_execute($stmt);oci_free_statement($stmt);
						$sqlstr="insert into NCKH_TONG_HOP_KINH_PHI(FK_MA_THUYET_MINH_DT,FK_MA_KHOAN_CHI_PHI) values ('$matm','003')"; 
						$stmt = oci_parse($db_conn_khcn, $sqlstr); oci_execute($stmt);oci_free_statement($stmt);
						$sqlstr="insert into NCKH_TONG_HOP_KINH_PHI(FK_MA_THUYET_MINH_DT,FK_MA_KHOAN_CHI_PHI) values ('$matm','004')"; 
						$stmt = oci_parse($db_conn_khcn, $sqlstr); oci_execute($stmt);oci_free_statement($stmt);
					// Cấp Trường
					}else if ($capdetai == '31' || $capdetai == '32' || $capdetai == '33' || $capdetai == '34' || $capdetai == '35'){
						$sqlstr="insert into NCKH_TONG_HOP_KINH_PHI(FK_MA_THUYET_MINH_DT,FK_MA_KHOAN_CHI_PHI) values ('$matm','101')"; 
						$stmt = oci_parse($db_conn_khcn, $sqlstr); oci_execute($stmt);oci_free_statement($stmt);
						$sqlstr="insert into NCKH_TONG_HOP_KINH_PHI(FK_MA_THUYET_MINH_DT,FK_MA_KHOAN_CHI_PHI) values ('$matm','102')"; 
						$stmt = oci_parse($db_conn_khcn, $sqlstr); oci_execute($stmt);oci_free_statement($stmt);
						$sqlstr="insert into NCKH_TONG_HOP_KINH_PHI(FK_MA_THUYET_MINH_DT,FK_MA_KHOAN_CHI_PHI) values ('$matm','103')"; 
						$stmt = oci_parse($db_conn_khcn, $sqlstr); oci_execute($stmt);oci_free_statement($stmt);
						$sqlstr="insert into NCKH_TONG_HOP_KINH_PHI(FK_MA_THUYET_MINH_DT,FK_MA_KHOAN_CHI_PHI) values ('$matm','104')"; 
						$stmt = oci_parse($db_conn_khcn, $sqlstr); oci_execute($stmt);oci_free_statement($stmt);
						$sqlstr="insert into NCKH_TONG_HOP_KINH_PHI(FK_MA_THUYET_MINH_DT,FK_MA_KHOAN_CHI_PHI) values ('$matm','105')"; 
						$stmt = oci_parse($db_conn_khcn, $sqlstr); oci_execute($stmt);oci_free_statement($stmt);
					}
					
				}
				else
				{
					$e = oci_error($stmt);
					$msgerr = $e['message']. " sql: " . $e['sqltext'];
					die ('{"success":"-1", "msgerr":"'.escapeWEB($msgerr).'"}');
				}
			}
		}
		else
		{
			$e = oci_error($stmt);
			$msgerr = $e['message']. " sql: " . $e['sqltext'];
			die ('{"success":"-1", "msgerr":"'.escapeWEB($msgerr).'"}');
		}
		
		echo '{"success":"1"}';
	}else{
		die ('{"success":"-1", "msgerr":"Không thể đăng ký đề tài vì đã hết hạn đăng ký"}');
	}
}

if ($a=='editnhanlucnc'){
	$ma_thuyet_minh_dt = str_replace("'", "''", $_POST["m"]);
	$loai = str_replace("'", "''", $_POST["nlnc"]);
	$masv = str_replace("'", "''", $_POST["khcn_frm_reg_nhanlucnghiencuu_masv"]);
	$hoten = str_replace("'", "''", $_POST["khcn_frm_reg_nhanlucnghiencuu_hh_hv_ho_ten"]);
	$hotensv = str_replace("'", "''", $_POST["khcn_frm_reg_nhanlucnghiencuu_ho_ten_sv"]);
	$dvcongtac = str_replace("'", "''", $_POST["khcn_frm_reg_nhanlucnghiencuu_don_vi_cong_tac"]);
	$sothang = str_replace("'", "''", $_POST["khcn_frm_reg_nhanlucnghiencuu_so_thang_lv_quy_doi"]);
	$fk_ma_can_bo = str_replace("'", "''", $_POST["khcn_frm_reg_nhanlucnghiencuu_fk_ma_can_bo"]);
	$shcc = str_replace("'", "''", $_POST["khcn_frm_reg_nhanlucnghiencuu_shcc"]);
	$manl = str_replace("'", "''", $_POST["khcn_frm_reg_nhanlucnghiencuu_manl"]);

	if ($loai=='1'){
		$hoten = str_replace("'", "''", $_POST["hoten"]);
		$shcc = str_replace("'", "''", $_POST["shcc"]);
		
		$sqlstr="update NCKH_NHAN_LUC_TMDT_CBGD set DON_VI_CONG_TAC='$dvcongtac',SO_THANG_LV_QUY_DOI='$sothang'
		where MA_NHAN_LUC_TMDT_CBGD = '$manl' and FK_MA_THUYET_MINH_DT = '$ma_thuyet_minh_dt'"; 
	}
	else if ($loai=='2'){
		$sqlstr="update NCKH_NHAN_LUC_TMDT_SV set FK_MA_HOC_VIEN='$masv',SV_HO_TEN='$hotensv',DON_VI_CONG_TAC='$dvcongtac',SO_THANG_LV_QUY_DOI='$sothang' where MA_NHAN_LUC_TMDT_SV='$manl' and FK_MA_THUYET_MINH_DT='$ma_thuyet_minh_dt'"; 
	}
	$stmt = oci_parse($db_conn_khcn, $sqlstr);
	if (!oci_execute($stmt)){
		$e = oci_error($stmt);
		$msgerr = $e['message']. " sql: " . $e['sqltext'];
		die ('{"success":"-1", "msgerr":"'.escapeJsonString($msgerr).'"}');
	}
	
	echo '{"success":"1", "ho_ten":"'.escapeJsonString($hoten).'", "ho_ten_sv":"'.escapeJsonString($hotensv).'", "don_vi_cong_tac":"'.escapeJsonString($dvcongtac).'", "so_thang":"'.escapeJsonString($sothang).'", "fk_ma_can_bo":"'.escapeJsonString($fk_ma_can_bo).'", "shcc":"'.escapeJsonString($shcc).'", "ma_sv":"'.escapeJsonString($masv).'", "ma_nhan_luc":"'.escapeJsonString($manl).'", "loainhanluc":"'.escapeJsonString($loai).'"}';
}

if ($a=='addnhanlucnc'){
	$ma_thuyet_minh_dt = str_replace("'", "''", $_POST["m"]);
	$loai = str_replace("'", "''", $_POST["khcn_frm_reg_nhanlucnghiencuu_loai"]);
	$masv = str_replace("'", "''", $_POST["khcn_frm_reg_nhanlucnghiencuu_masv"]);
	$hoten = str_replace("'", "''", $_POST["khcn_frm_reg_nhanlucnghiencuu_hh_hv_ho_ten"]);
	$hotensv = str_replace("'", "''", $_POST["khcn_frm_reg_nhanlucnghiencuu_ho_ten_sv"]);
	$dvcongtac = str_replace("'", "''", $_POST["khcn_frm_reg_nhanlucnghiencuu_don_vi_cong_tac"]);
	$sothang = str_replace("'", "''", $_POST["khcn_frm_reg_nhanlucnghiencuu_so_thang_lv_quy_doi"]);
	$fk_ma_can_bo = str_replace("'", "''", $_POST["khcn_frm_reg_nhanlucnghiencuu_fk_ma_can_bo"]);
	$shcc = str_replace("'", "''", $_POST["khcn_frm_reg_nhanlucnghiencuu_shcc"]);
	
	
	if ($loai=='1'){
		$sqlstr="select get_ma_nhan_luc_TMDT_cbgd('$ma_thuyet_minh_dt') manhanluc from dual"; 
		$stmt = oci_parse($db_conn_khcn, $sqlstr);
		if (oci_execute($stmt)){
			oci_fetch_all($stmt, $resDM); oci_free_statement($stmt);
			$manl = $resDM["MANHANLUC"][0];
			
			$sqlstr="insert into NCKH_NHAN_LUC_TMDT_CBGD(MA_NHAN_LUC_TMDT_CBGD,FK_MA_CAN_BO,FK_MA_THUYET_MINH_DT,HH_HV_HO_TEN,DON_VI_CONG_TAC,SO_THANG_LV_QUY_DOI, SHCC) 
			values ('$manl','$fk_ma_can_bo','$ma_thuyet_minh_dt','$hoten','$dvcongtac','$sothang','$shcc')"; 
		}else{
			$e = oci_error($stmt);
			$msgerr = $e['message']. " sql: " . $e['sqltext'];
			die ('{"success":"-1", "msgerr":"'.escapeJsonString($msgerr).'"}');
		}
	}
	else if ($loai=='2'){
		$sqlstr="select get_ma_nhan_luc_TMDT_SV('$ma_thuyet_minh_dt') manhanluc from dual"; 
		$stmt = oci_parse($db_conn_khcn, $sqlstr);
		if (oci_execute($stmt)){
			oci_fetch_all($stmt, $resDM); oci_free_statement($stmt);
			$manl = $resDM["MANHANLUC"][0];
			
			$sqlstr="insert into NCKH_NHAN_LUC_TMDT_SV(MA_NHAN_LUC_TMDT_SV,FK_MA_HOC_VIEN,FK_MA_THUYET_MINH_DT,SV_HO_TEN,DON_VI_CONG_TAC,SO_THANG_LV_QUY_DOI) 
			values ('$manl','$masv','$ma_thuyet_minh_dt','$hotensv','$dvcongtac','$sothang')"; 
		}else{
			$e = oci_error($stmt);
			$msgerr = $e['message']. " sql: " . $e['sqltext'];
			die ('{"success":"-1", "msgerr":"'.escapeJsonString($msgerr).'"}');
		}
	}
	$stmt = oci_parse($db_conn_khcn, $sqlstr);
	if (!oci_execute($stmt)){
		$e = oci_error($stmt);
		$msgerr = $e['message']. " sql: " . $e['sqltext'];
		die ('{"success":"-1", "msgerr":"'.escapeJsonString($msgerr).'"}');
	}

	echo '{"success":"1", "ho_ten":"'.escapeJsonString($hoten).'", "ho_ten_sv":"'.escapeJsonString($hotensv).'", "don_vi_cong_tac":"'.escapeJsonString($dvcongtac).'", "so_thang":"'.escapeJsonString($sothang).'", "fk_ma_can_bo":"'.escapeJsonString($fk_ma_can_bo).'", "shcc":"'.escapeJsonString($shcc).'", "ma_sv":"'.escapeJsonString($masv).'", "ma_nhan_luc":"'.escapeJsonString($manl).'", "loainhanluc":"'.escapeJsonString($loai).'"}';
}

if ($a=='addchuyengianc'){
	$ma_thuyet_minh_dt = str_replace("'", "''", $_POST["m"]);
	$huongnc = str_replace("'", "''", $_POST["khcn_frm_reg_chuyengia_huongnccs"]);
	$diachi = str_replace("'", "''", $_POST["khcn_frm_reg_chuyengia_diachi"]);
	$hoten = str_replace("'", "''", $_POST["khcn_frm_reg_chuyengia_hh_hv_ho_ten"]);
	$dvcongtac = str_replace("'", "''", $_POST["khcn_frm_reg_chuyengia_don_vi_cong_tac"]);
	$dienthoai = str_replace("'", "''", $_POST["khcn_frm_reg_chuyengia_dienthoai"]);
	$email = str_replace("'", "''", $_POST["khcn_frm_reg_chuyengia_email"]);
	$fk_ma_can_bo = str_replace("'", "''", $_POST["khcn_frm_reg_chuyengia_fk_ma_can_bo"]);
	
	if ($ma_thuyet_minh_dt!=''){
		$sqlstr="select get_ma_chuyen_gia_TMDT('$ma_thuyet_minh_dt') machuyengia from dual"; 
		$stmt = oci_parse($db_conn_khcn, $sqlstr);
		if (oci_execute($stmt)){
			oci_fetch_all($stmt, $resDM); oci_free_statement($stmt);
			$machuyengia = $resDM["MACHUYENGIA"][0];
			
			$sqlstr="insert into NCKH_CHUYEN_GIA_TMDT(MA_CHUYEN_GIA_TMDT,FK_MA_CAN_BO,FK_MA_THUYET_MINH_DT,HH_HV_HO_TEN,HUONG_NC_CHUYEN_SAU,CO_QUAN_CONG_TAC,DIA_CHI,DIEN_THOAI,EMAIL) 
			values ('$machuyengia','$fk_ma_can_bo','$ma_thuyet_minh_dt','$hoten','$huongnc','$dvcongtac','$diachi','$dienthoai','$email')"; 
		}else{
			$e = oci_error($stmt);
			$msgerr = $e['message']. " sql: " . $e['sqltext'];
			die ('{"success":"-1", "msgerr":"'.escapeJsonString($msgerr).'"}');
		}
	}
	
	if ($sqlstr)
	{
		$stmt = oci_parse($db_conn_khcn, $sqlstr);
		if (!oci_execute($stmt)){
			$e = oci_error($stmt);
			$msgerr = $e['message']. " sql: " . $e['sqltext'];
			die ('{"success":"-1", "msgerr":"'.escapeJsonString($msgerr).'"}');
		}
		
		echo '{"success":"1", "ma_chuyen_gia":"'.escapeJsonString($machuyengia).'", "ho_ten":"'.escapeJsonString($hoten).'", 
				"co_quan_cong_tac":"'.escapeJsonString($dvcongtac).'", "fk_ma_can_bo":"'.escapeJsonString($fk_ma_can_bo).'", 
				"huong_nc_chuyen_sau":"'.escapeJsonString($huongnc).'", "dia_chi":"'.escapeJsonString($diachi).'", "dien_thoai":"'.escapeJsonString($dienthoai).'", "email":"'.escapeJsonString($email).'"
			}';
	}
}

if ($a=='addanphamkhoahoc'){
	$ma_thuyet_minh_dt = str_replace("'", "''", $_POST["m"]);
	$fk_ma_an_pham_kh = str_replace("'", "''", $_POST["khcn_frm_reg_anphamkhoahoc_loai"]);
	$ten_bb_sach_dk = str_replace("'", "''", $_POST["khcn_frm_reg_anphamkhoahoc_ten_bb_sach_dk"]);
	$so_luong = str_replace("'", "''", $_POST["khcn_frm_reg_anphamkhoahoc_so_luong"]);
	$dk_noi_cong_bo = str_replace("'", "''", $_POST["khcn_frm_reg_anphamkhoahoc_dk_noi_cong_bo"]);
	$ghi_chu = str_replace("'", "''", $_POST["khcn_frm_reg_anphamkhoahoc_ghi_chu"]);
	
	if ($ma_thuyet_minh_dt!=''){
		$sqlstr="insert into NCKH_AN_PHAM_KH_TMDT(FK_MA_THUYET_MINH_DT,FK_MA_AN_PHAM_KH,TEN_BB_SACH_DK,SO_LUONG,DK_NOI_CONG_BO,GHI_CHU) 
		values ('$ma_thuyet_minh_dt','$fk_ma_an_pham_kh','$ten_bb_sach_dk','$so_luong','$dk_noi_cong_bo','$ghi_chu')"; 
		$stmt = oci_parse($db_conn_khcn, $sqlstr);
		if (!oci_execute($stmt)){
			$e = oci_error($stmt);
			$msgerr = $e['message']. " sql: " . $e['sqltext'];
			die ('{"success":"-1", "msgerr":"'.escapeJsonString($msgerr).'"}');
		}
		
		echo '{"success":"1", "fk_ma_an_pham_kh":"'.escapeJsonString($fk_ma_an_pham_kh).'", "ten_bb_sach_dk":"'.escapeJsonString($ten_bb_sach_dk).'", 
				"so_luong":"'.escapeJsonString($so_luong).'", "dk_noi_cong_bo":"'.escapeJsonString($dk_noi_cong_bo).'", 
				"ghi_chu":"'.escapeJsonString($ghi_chu).'"
		}';
	}
}

if ($a=='addsohuutritue'){
	$ma_thuyet_minh_dt = str_replace("'", "''", $_POST["m"]);
	$fk_ma_so_huu_tri_tue = str_replace("'", "''", $_POST["khcn_frm_reg_sohuutritue_hinhthuc"]);
	$ten_hinh_thuc = str_replace("'", "''", $_POST["hinhthuc"]);
	$so_luong = str_replace("'", "''", $_POST["khcn_frm_reg_sohuutritue_so_luong"]);
	$noi_dung_du_kien = str_replace("'", "''", $_POST["khcn_frm_reg_sohuutritue_noi_dung"]);
	$ghi_chu = str_replace("'", "''", $_POST["khcn_frm_reg_sohuutritue_ghi_chu"]);
	
	if ($ma_thuyet_minh_dt!='' && $fk_ma_so_huu_tri_tue!=''){
		$sqlstr="insert into NCKH_SO_HUU_TRI_TUE(FK_MA_THUYET_MINH_DT,FK_MA_SO_HUU_TRI_TUE,SO_LUONG,NOI_DUNG_DU_KIEN,GHI_CHU) 
		values ('$ma_thuyet_minh_dt','$fk_ma_so_huu_tri_tue','$so_luong','$noi_dung_du_kien','$ghi_chu')"; 
		$stmt = oci_parse($db_conn_khcn, $sqlstr);
		if (!oci_execute($stmt)){
			$e = oci_error($stmt);
			$msgerr = $e['message']. " sql: " . $e['sqltext'];
			die ('{"success":"-1", "msgerr":"'.escapeJsonString($msgerr).'"}');
		}
		
		echo '{"success":"1", "fk_ma_so_huu_tri_tue":"'.escapeJsonString($fk_ma_so_huu_tri_tue).'", "ten_hinh_thuc":"'.escapeJsonString($ten_hinh_thuc).'", 
				"so_luong":"'.escapeJsonString($so_luong).'", "noi_dung_du_kien":"'.escapeJsonString($noi_dung_du_kien).'", 
				"ghi_chu":"'.escapeJsonString($ghi_chu).'"
		}';
	}
}

if ($a=='addsanphammem'){
	$ma_thuyet_minh_dt = str_replace("'", "''", $_POST["m"]);
	$ten_san_pham = str_replace("'", "''", $_POST["khcn_frm_reg_sanphammem_tensp"]);
	$chi_tieu_danh_gia = str_replace("'", "''", $_POST["khcn_frm_reg_sanphammem_ctdanhgia"]);
	$ghi_chu = str_replace("'", "''", $_POST["khcn_frm_reg_sanphammem_ghichu"]);
	$ma_san_pham_mem_tmdt = "";
	
	$sqlstr="select get_ma_sp_mem_TMDT('$ma_thuyet_minh_dt') maspmem from dual"; 
	$stmt = oci_parse($db_conn_khcn, $sqlstr);
	if (oci_execute($stmt)){
		oci_fetch_all($stmt, $resDM); oci_free_statement($stmt);
		$ma_san_pham_mem_tmdt = $resDM["MASPMEM"][0];

		if ($ma_thuyet_minh_dt!='' && $ma_san_pham_mem_tmdt!=''){
			$sqlstr="insert into NCKH_SAN_PHAM_MEM(FK_MA_THUYET_MINH_DT,MA_SAN_PHAM_MEM_TMDT,TEN_SAN_PHAM,CHI_TIEU_DANH_GIA,GHI_CHU) 
			values ('$ma_thuyet_minh_dt','$ma_san_pham_mem_tmdt','$ten_san_pham','$chi_tieu_danh_gia','$ghi_chu')"; 
			$stmt = oci_parse($db_conn_khcn, $sqlstr);
			if (!oci_execute($stmt)){
				$e = oci_error($stmt);
				$msgerr = $e['message']. " sql: " . $e['sqltext'];
				die ('{"success":"-1", "msgerr":"'.escapeJsonString($msgerr).'"}');
			}
			
			echo '{"success":"1", "ma_san_pham_mem_tmdt":"'.escapeJsonString($ma_san_pham_mem_tmdt).'", "ten_san_pham":"'.escapeJsonString($ten_san_pham).'", 
					"chi_tieu_danh_gia":"'.escapeJsonString($chi_tieu_danh_gia).'", "ghi_chu":"'.escapeJsonString($ghi_chu).'"
			}';
		}
	}else{
		$e = oci_error($stmt);
		$msgerr = $e['message']. " sql: " . $e['sqltext'];
		die ('{"success":"-1", "msgerr":"'.escapeJsonString($msgerr).'"}');
	}
}

if ($a=='addsanphamcung'){
	$ma_thuyet_minh_dt = str_replace("'", "''", $_POST["m"]);
	$ten_san_pham = str_replace("'", "''", $_POST["khcn_frm_reg_sanphamcung_tensp"]);
	$chi_tieu_danh_gia = str_replace("'", "''", $_POST["khcn_frm_reg_sanphamcung_ctdanhgia"]);
	$don_vi_do = str_replace("'", "''", $_POST["khcn_frm_reg_sanphamcung_don_vi_do"]);
	$trong_nuoc = str_replace("'", "''", $_POST["khcn_frm_reg_sanphamcung_mau_tt_trong_nuoc"]);
	$the_gioi = str_replace("'", "''", $_POST["khcn_frm_reg_sanphamcung_mau_tt_thegioi"]);
	$so_luong_quy_mo = str_replace("'", "''", $_POST["khcn_frm_reg_sanphamcung_soluong"]);
	
	$ma_san_pham_cung_tmdt = "";
	
	$sqlstr="select get_ma_sp_cung_TMDT('$ma_thuyet_minh_dt') maspcung from dual"; 
	$stmt = oci_parse($db_conn_khcn, $sqlstr);
	if (oci_execute($stmt)){
		oci_fetch_all($stmt, $resDM); oci_free_statement($stmt);
		$ma_san_pham_cung_tmdt = $resDM["MASPCUNG"][0];

		if ($ma_thuyet_minh_dt!='' && $ma_san_pham_cung_tmdt!=''){
			$sqlstr="insert into NCKH_SAN_PHAM_CUNG(FK_MA_THUYET_MINH_DT,MA_SAN_PHAM_CUNG_TMDT,TEN_SAN_PHAM_CHI_TIEU,DON_VI_DO,CHI_TIEU_DANH_GIA,MAU_TT_TRONG_NUOC,MAU_TT_THE_GIOI,DK_SL_QUY_MO_SP) 
			values ('$ma_thuyet_minh_dt','$ma_san_pham_cung_tmdt','$ten_san_pham','$don_vi_do','$chi_tieu_danh_gia','$trong_nuoc','$the_gioi','$so_luong_quy_mo')"; 
			$stmt = oci_parse($db_conn_khcn, $sqlstr);
			if (!oci_execute($stmt)){
				$e = oci_error($stmt);
				$msgerr = $e['message']. " sql: " . $e['sqltext'];
				die ('{"success":"-1", "msgerr":"'.escapeJsonString($msgerr).'"}');
			}
			
			echo '{"success":"1", "ma_san_pham_cung_tmdt":"'.escapeJsonString($ma_san_pham_cung_tmdt).'", "ten_san_pham":"'.escapeJsonString($ten_san_pham).'", 
					"chi_tieu_danh_gia":"'.escapeJsonString($chi_tieu_danh_gia).'", "don_vi_do":"'.escapeJsonString($don_vi_do).'",
					"trong_nuoc":"'.escapeJsonString($trong_nuoc).'", "the_gioi":"'.escapeJsonString($the_gioi).'",
					"so_luong_quy_mo":"'.escapeJsonString($so_luong_quy_mo).'"
			}';
		}
	}else{
		$e = oci_error($stmt);
		$msgerr = $e['message']. " sql: " . $e['sqltext'];
		die ('{"success":"-1", "msgerr":"'.escapeJsonString($msgerr).'"}');
	}
}

if ($a=='addketquadaotao'){
	$ma_thuyet_minh_dt = str_replace("'", "''", $_POST["m"]);
	$fk_bac_dao_tao = str_replace("'", "''", $_POST["khcn_frm_reg_ketquadaotao_capdt"]);
	$ten_capdt = str_replace("'", "''", $_POST["capdt"]);
	$so_luong = str_replace(array("'",","), array("''",""), $_POST["khcn_frm_reg_ketquadaotao_so_luong"]);
	$nhiem_vu_duoc_giao = str_replace("'", "''", $_POST["khcn_frm_reg_ketquadaotao_nhiem_vu"]);
	$du_kien_kinh_phi = str_replace(array("'",","), array("''",""), $_POST["khcn_frm_reg_ketquadaotao_kinhphi"]);
	
	if ($ma_thuyet_minh_dt!='' && $fk_bac_dao_tao!=''){
		$sqlstr="insert into NCKH_KQ_DAO_TAO_TMDT(FK_MA_THUYET_MINH_DT,FK_BAC_DAO_TAO,SO_LUONG,NHIEM_VU_DUOC_GIAO,DU_KIEN_KINH_PHI) 
		values ('$ma_thuyet_minh_dt','$fk_bac_dao_tao','$so_luong','$nhiem_vu_duoc_giao','$du_kien_kinh_phi')"; 
		$stmt = oci_parse($db_conn_khcn, $sqlstr);
		if (!oci_execute($stmt)){
			$e = oci_error($stmt);
			$msgerr = $e['message']. " sql: " . $e['sqltext'];
			die ('{"success":"-1", "msgerr":"'.escapeJsonString($msgerr).'"}');
		}
		
		echo '{"success":"1", "fk_bac_dao_tao":"'.escapeJsonString($fk_bac_dao_tao).'", "ten_capdt":"'.escapeJsonString($ten_capdt).'", 
				"so_luong":"'.escapeJsonString($so_luong).'", "nhiem_vu_duoc_giao":"'.escapeJsonString($nhiem_vu_duoc_giao).'", 
				"du_kien_kinh_phi":"'.escapeJsonString($du_kien_kinh_phi).'"
		}';
	}
}

if ($a=='addkhoanchiphi'){
	$ma_thuyet_minh_dt = str_replace("'", "''", $_POST["m"]);
	$fk_ma_khoan_chi_phi = str_replace("'", "''", $_POST["khcn_frm_reg_tonghopkinhphi_khoan_chi_phi"]);
	$ten_khoan_chi_phi = str_replace("'", "''", $_POST["tenkhoanchiphi"]);
	
	$kinh_phi = str_replace(array("'", ","), array("''", ""), $_POST["khcn_frm_reg_tonghopkinhphi_kinh_phi"]);
	$khoan_chi = str_replace(array("'", ","), array("''", ""), $_POST["khcn_frm_reg_tonghopkinhphi_khoan_chi"]);
	//$phan_tram = str_replace("'", "''", $_POST["khcn_frm_reg_tonghopkinhphi_phan_tram"]);
	
	if ($ma_thuyet_minh_dt!='' && $fk_ma_khoan_chi_phi!=''){
		$sqlstr="insert into NCKH_TONG_HOP_KINH_PHI(FK_MA_THUYET_MINH_DT,FK_MA_KHOAN_CHI_PHI,KINH_PHI,KHOAN_CHI) 
		values ('$ma_thuyet_minh_dt','$fk_ma_khoan_chi_phi','$kinh_phi','$khoan_chi')"; 
		$stmt = oci_parse($db_conn_khcn, $sqlstr);
		if (!oci_execute($stmt)){
			$e = oci_error($stmt);
			$msgerr = $e['message']. " sql: " . $e['sqltext'];
			die ('{"success":"-1", "msgerr":"'.escapeJsonString($msgerr).'"}');
		}
		
		echo '{"success":"1", "fk_ma_khoan_chi_phi":"'.escapeJsonString($fk_ma_khoan_chi_phi).'", "ten_khoan_chi_phi":"'.escapeJsonString($ten_khoan_chi_phi).'", 
				"kinh_phi":"'.escapeJsonString($kinh_phi).'", "khoan_chi":"'.escapeJsonString($khoan_chi).'", "phan_tram":"'.escapeJsonString($phan_tram).'"
		}';
	}
}

if ($a=='removenhanlucnc'){
	$loai = str_replace("'", "''", $_POST["loai"]);
	$ma_thuyet_minh_dt = str_replace("'", "''", $_POST["m"]);
	$ma_nhan_luc = str_replace("'", "''", $_POST["mnl"]);
	
	if ($loai=='1'){
		$sqlstr="delete NCKH_NHAN_LUC_TMDT_CBGD where MA_NHAN_LUC_TMDT_CBGD='$ma_nhan_luc' and FK_MA_THUYET_MINH_DT='$ma_thuyet_minh_dt'"; 
	}
	else if ($loai=='2'){
		$sqlstr="delete NCKH_NHAN_LUC_TMDT_SV where MA_NHAN_LUC_TMDT_SV='$ma_nhan_luc' and FK_MA_THUYET_MINH_DT='$ma_thuyet_minh_dt'"; 
	}
	$stmt = oci_parse($db_conn_khcn, $sqlstr);
	if (oci_execute($stmt)){
		echo '{"success":"1"}';
	}
	else
	{
		$e = oci_error($stmt);
		$msgerr = $e['message']. " sql: " . $e['sqltext'];
		die ('{"success":"-1", "msgerr":"'.escapeJsonString($msgerr).'"}');
	}
}

if ($a=='removechuyengianc'){
	$ma_thuyet_minh_dt = str_replace("'", "''", $_POST["m"]);
	$ma_chuyen_gia = str_replace("'", "''", $_POST["mcg"]);

	$sqlstr="delete NCKH_CHUYEN_GIA_TMDT where MA_CHUYEN_GIA_TMDT='$ma_chuyen_gia' and FK_MA_THUYET_MINH_DT='$ma_thuyet_minh_dt'"; 
	
	$stmt = oci_parse($db_conn_khcn, $sqlstr);
	if (oci_execute($stmt)){
		echo '{"success":"1"}';
	}
	else
	{
		$e = oci_error($stmt);
		$msgerr = $e['message']. " sql: " . $e['sqltext'];
		die ('{"success":"-1", "msgerr":"'.escapeJsonString($msgerr).'"}');
	}
}

if ($a=='removeanphamkhoahoc'){
	$ma_thuyet_minh_dt = str_replace("'", "''", $_POST["m"]);
	$fk_ma_an_pham_kh = str_replace("'", "''", $_POST["map"]);

	$sqlstr="delete NCKH_AN_PHAM_KH_TMDT where FK_MA_AN_PHAM_KH='$fk_ma_an_pham_kh' and FK_MA_THUYET_MINH_DT='$ma_thuyet_minh_dt'"; 
	
	$stmt = oci_parse($db_conn_khcn, $sqlstr);
	if (oci_execute($stmt)){
		echo '{"success":"1"}';
	}
	else
	{
		$e = oci_error($stmt);
		$msgerr = $e['message']. " sql: " . $e['sqltext'];
		die ('{"success":"-1", "msgerr":"'.escapeJsonString($msgerr).'"}');
	}
}

if ($a=='removesohuutritue'){
	$ma_thuyet_minh_dt = str_replace("'", "''", $_POST["m"]);
	$fk_ma_so_huu_tri_tue = str_replace("'", "''", $_POST["ma"]);
	$sqlstr="delete NCKH_SO_HUU_TRI_TUE where FK_MA_SO_HUU_TRI_TUE='$fk_ma_so_huu_tri_tue' and FK_MA_THUYET_MINH_DT='$ma_thuyet_minh_dt'"; 
	$stmt = oci_parse($db_conn_khcn, $sqlstr);
	if (oci_execute($stmt)){
		echo '{"success":"1"}';
	}
	else{
		$e = oci_error($stmt);
		$msgerr = $e['message']. " sql: " . $e['sqltext'];
		die ('{"success":"-1", "msgerr":"'.escapeJsonString($msgerr).'"}');
	}
}

if ($a=='removesanphammem'){
	$ma_thuyet_minh_dt = str_replace("'", "''", $_POST["m"]);
	$ma_san_pham_mem_tmdt = str_replace("'", "''", $_POST["ma"]);
	$sqlstr="delete NCKH_SAN_PHAM_MEM where MA_SAN_PHAM_MEM_TMDT='$ma_san_pham_mem_tmdt' and FK_MA_THUYET_MINH_DT='$ma_thuyet_minh_dt'"; 
	$stmt = oci_parse($db_conn_khcn, $sqlstr);
	if (oci_execute($stmt)){
		echo '{"success":"1"}';
	}
	else{
		$e = oci_error($stmt);
		$msgerr = $e['message']. " sql: " . $e['sqltext'];
		die ('{"success":"-1", "msgerr":"'.escapeJsonString($msgerr).'"}');
	}
}

if ($a=='removesanphamcung'){
	$ma_thuyet_minh_dt = str_replace("'", "''", $_POST["m"]);
	$ma_san_pham_cung_tmdt = str_replace("'", "''", $_POST["ma"]);
	$sqlstr="delete NCKH_SAN_PHAM_CUNG where MA_SAN_PHAM_CUNG_TMDT='$ma_san_pham_cung_tmdt' and FK_MA_THUYET_MINH_DT='$ma_thuyet_minh_dt'"; 
	$stmt = oci_parse($db_conn_khcn, $sqlstr);
	if (oci_execute($stmt)){
		echo '{"success":"1"}';
	}
	else{
		$e = oci_error($stmt);
		$msgerr = $e['message']. " sql: " . $e['sqltext'];
		die ('{"success":"-1", "msgerr":"'.escapeJsonString($msgerr).'"}');
	}
}

if ($a=='removeketquadaotao'){
	$ma_thuyet_minh_dt = str_replace("'", "''", $_POST["m"]);
	$ma_remove = str_replace("'", "''", $_POST["ma"]);
	$sqlstr="delete NCKH_KQ_DAO_TAO_TMDT where FK_BAC_DAO_TAO='$ma_remove' and FK_MA_THUYET_MINH_DT='$ma_thuyet_minh_dt'"; 
	$stmt = oci_parse($db_conn_khcn, $sqlstr);
	if (oci_execute($stmt)){
		echo '{"success":"1"}';
	}
	else{
		$e = oci_error($stmt);
		$msgerr = $e['message']. " sql: " . $e['sqltext'];
		die ('{"success":"-1", "msgerr":"'.escapeJsonString($msgerr).'"}');
	}
}

if ($a=='removekhoanchiphi'){
	$ma_thuyet_minh_dt = str_replace("'", "''", $_POST["m"]);
	$ma_remove = str_replace("'", "''", $_POST["ma"]);
	$sqlstr="delete NCKH_TONG_HOP_KINH_PHI where FK_MA_KHOAN_CHI_PHI='$ma_remove' and FK_MA_THUYET_MINH_DT='$ma_thuyet_minh_dt'"; 
	$stmt = oci_parse($db_conn_khcn, $sqlstr);
	if (oci_execute($stmt)){
		echo '{"success":"1"}';
	}
	else{
		$e = oci_error($stmt);
		$msgerr = $e['message']. " sql: " . $e['sqltext'];
		die ('{"success":"-1", "msgerr":"'.escapeJsonString($msgerr).'"}');
	}
}

if ($a=='getthuyetminhinfo'){
	$m = str_replace("'", "''", $_POST["m"]);

	$sqlstr="SELECT tm.*,to_char(tm.CNDT_NGAY_SINH,'dd/mm/yyyy') CNDT_NGAY_SINH, to_char(tm.CNDT_NGAY_CAP,'dd/mm/yyyy') CNDT_NGAY_CAP, cdt.ten_cap, lhnc.TEN_LOAI_HINH_NC,
	to_char(tm.DCNDT_NGAY_SINH,'dd/mm/yyyy') DCNDT_NGAY_SINH, to_char(tm.DCNDT_NGAY_CAP,'dd/mm/yyyy') DCNDT_NGAY_CAP, tp1.TEN_TINH_TP CNDT_TEN_NOI_CAP, tp2.TEN_TINH_TP DCNDT_TEN_NOI_CAP,
	DECODE(tm.CNDT_PHAI, 'F', 'Nữ', 'M', 'Nam', '') CNDT_TEN_PHAI, DECODE(tm.DCNDT_PHAI, 'F', 'Nữ', 'M', 'Nam', '') DCNDT_TEN_PHAI,
	num2str(tm.TONG_KINH_PHI*1000000) || ' đồng' CHU_TONG_KINH_PHI, num2str(tm.KINH_PHI_TU_DHQG*1000000) || ' đồng' CHU_KINH_PHI_TU_DHQG, 
	num2str(tm.KINH_PHI_HUY_DONG*1000000) || ' đồng' CHU_KINH_PHI_HUY_DONG, num2str(tm.HD_VON_TU_CO*1000000) || ' đồng' CHU_HD_VON_TU_CO, 
	num2str(tm.HD_KHAC*1000000) || ' đồng' CHU_HD_KHAC, n.USERNAME
	FROM NCKH_THUYET_MINH_DE_TAI tm, CAP_DE_TAI cdt, NCKH_LOAI_HINH_NC lhnc, DM_TINH_TP tp1, DM_TINH_TP tp2, nhan_su n
	WHERE MA_THUYET_MINH_DT='$m' and FK_CAP_DE_TAI = cdt.ma_cap(+) and FK_LOAI_HINH_NC = lhnc.MA_LOAI_HINH_NC(+)
	and CNDT_NOI_CAP = tp1.MA_TINH_TP(+) and DCNDT_NOI_CAP = tp2.MA_TINH_TP(+)
	and tm.FK_MA_CAN_BO=n.FK_MA_CAN_BO(+)";

	//file_put_contents("logs.txt", date("H:i:s d.m.Y")." $sqlstr \n", FILE_APPEND);
		
	$stmt = oci_parse($db_conn_khcn, $sqlstr);
	if (!oci_execute($stmt))
	{
		$e = oci_error($stmt);
		$msgerr = $e['message']. " sql: " . $e['sqltext'];
		die ('{"success":"-1", "msgerr":"'.escapeWEB($msgerr).'"}');
	}
	$n = oci_fetch_all($stmt, $resDM);oci_free_statement($stmt);
	
	$data='{';
	if ($n){
		
		if ($resDM["VB_CHUNG_MINH_VON_KHAC_LINK"][0]!=''){
			$path = pathinfo($resDM["VB_CHUNG_MINH_VON_KHAC_LINK"][0]);
			$filename = $path['basename'];
		}else $filename='';
		
		$data.= '
				"info":{
					"masodetai":"'.escapeJsonString($resDM["MA_SO_DE_TAI"][0]).'",
					"mathuyetminh":"'.escapeJsonString($resDM["MA_THUYET_MINH_DT"][0]).'",
					"tendetaivn":"'.escapeJsonString($resDM["TEN_DE_TAI_VN"][0]).'", 
					"tendetaien":"'.escapeJsonString($resDM["TEN_DE_TAI_EN"][0]).'",
					"thoigianthuchien":"'.escapeJsonString($resDM["THOI_GIAN_THUC_HIEN"][0]).'",
					
					"tongkinhphi":"'.escapeJsonString($resDM["TONG_KINH_PHI"][0]).'",
					"kinhphidhqg":"'.escapeJsonString($resDM["KINH_PHI_TU_DHQG"][0]).'",
					"kinhphihuydong":"'.escapeJsonString($resDM["KINH_PHI_HUY_DONG"][0]).'",
					"vontuco":"'.escapeJsonString($resDM["HD_VON_TU_CO"][0]).'",
					"vonkhac":"'.escapeJsonString($resDM["HD_KHAC"][0]).'",
					"chutongkinhphi":"'.escapeJsonString($resDM["CHU_TONG_KINH_PHI"][0]).'",
					"chukinhphidhqg":"'.escapeJsonString($resDM["CHU_KINH_PHI_TU_DHQG"][0]).'",
					"chukinhphihuydong":"'.escapeJsonString($resDM["CHU_KINH_PHI_HUY_DONG"][0]).'",
					"chuvontuco":"'.escapeJsonString($resDM["CHU_HD_VON_TU_CO"][0]).'",
					"chuvonkhac":"'.escapeJsonString($resDM["CHU_HD_KHAC"][0]).'",
					
					"nganhhep":"'.escapeJsonString($resDM["CHUYEN_NGANH_HEP"][0]).'",
					"capdetai":"'.escapeJsonString($resDM["FK_CAP_DE_TAI"][0]).'",
					"tencapdetai":"'.escapeJsonString($resDM["TEN_CAP"][0]).'",
					"loaihinhnc":"'.escapeJsonString($resDM["FK_LOAI_HINH_NC"][0]).'",
					"tenloaihinhnc":"'.escapeJsonString($resDM["TEN_LOAI_HINH_NC"][0]).'",
					"tochuctaitrokhac":"'.escapeJsonString($resDM["TO_CHUC_TAI_TRO_KHAC"][0]).'",
					"keywords":"'.escapeJsonString($resDM["KEYWORDS"][0]).'",
					"huongdt":"'.escapeJsonString($resDM["HUONG_DE_TAI"][0]).'",
					"bomon":"'.escapeJsonString($resDM["FK_BO_MON_DANG_KY"][0]).'", 
					"vb_chung_minh_von_khac_link":"'.escapeJsonString($resDM["VB_CHUNG_MINH_VON_KHAC_LINK"][0]).'",
					"vb_chung_minh_von_khac_name":"'.escapeJsonString($filename).'",
					
					"cndt_hh_hv_ho_ten":"'.escapeJsonString($resDM["CNDT_HH_HV_HO_TEN"][0]).'",
					"cndt_ngay_sinh":"'.escapeJsonString($resDM["CNDT_NGAY_SINH"][0]).'", 
					"cndt_phai":"'.escapeJsonString($resDM["CNDT_PHAI"][0]).'",
					"cndt_ten_phai":"'.escapeJsonString($resDM["CNDT_TEN_PHAI"][0]).'",
					"cndt_so_cmnd":"'.escapeJsonString($resDM["CNDT_SO_CMND"][0]).'",
					"cndt_ngay_cap":"'.escapeJsonString($resDM["CNDT_NGAY_CAP"][0]).'",
					"cndt_noi_cap":"'.escapeJsonString($resDM["CNDT_NOI_CAP"][0]).'",
					"cndt_ten_noi_cap":"'.escapeJsonString($resDM["CNDT_TEN_NOI_CAP"][0]).'",
					"cndt_ms_thue":"'.escapeJsonString($resDM["CNDT_MS_THUE"][0]).'",
					"cndt_so_tai_khoan":"'.escapeJsonString($resDM["CNDT_SO_TAI_KHOAN"][0]).'",
					"cndt_ngan_hang":"'.escapeJsonString($resDM["CNDT_NGAN_HANG"][0]).'",
					"cndt_dia_chi_cq":"'.escapeJsonString($resDM["CNDT_DIA_CHI_CQ"][0]).'",
					"cndt_dien_thoai":"'.escapeJsonString($resDM["CNDT_DIEN_THOAI"][0]).'",
					"cndt_email":"'.escapeJsonString($resDM["CNDT_EMAIL"][0]).'",
					"tom_tat_hd_nc":"'.escapeJsonString($resDM["TOM_TAT_HD_NC"][0]).'",
					
					"dcndt_hh_hv_ho_ten":"'.escapeJsonString($resDM["DCNDT_HH_HV_HO_TEN"][0]).'",
					"dcndt_ngay_sinh":"'.escapeJsonString($resDM["DCNDT_NGAY_SINH"][0]).'", 
					"dcndt_phai":"'.escapeJsonString($resDM["DCNDT_PHAI"][0]).'",
					"dcndt_ten_phai":"'.escapeJsonString($resDM["DCNDT_TEN_PHAI"][0]).'",
					"dcndt_so_cmnd":"'.escapeJsonString($resDM["DCNDT_SO_CMND"][0]).'",
					"dcndt_ngay_cap":"'.escapeJsonString($resDM["DCNDT_NGAY_CAP"][0]).'",
					"dcndt_noi_cap":"'.escapeJsonString($resDM["DCNDT_NOI_CAP"][0]).'",
					"dcndt_ten_noi_cap":"'.escapeJsonString($resDM["DCNDT_TEN_NOI_CAP"][0]).'",
					"dcndt_ms_thue":"'.escapeJsonString($resDM["DCNDT_MS_THUE"][0]).'",
					"dcndt_so_tai_khoan":"'.escapeJsonString($resDM["DCNDT_SO_TAI_KHOAN"][0]).'",
					"dcndt_ngan_hang":"'.escapeJsonString($resDM["DCNDT_NGAN_HANG"][0]).'",
					"dcndt_dia_chi_cq":"'.escapeJsonString($resDM["DCNDT_DIA_CHI_CQ"][0]).'",
					"dcndt_dien_thoai":"'.escapeJsonString($resDM["DCNDT_DIEN_THOAI"][0]).'",
					"dcndt_email":"'.escapeJsonString($resDM["DCNDT_EMAIL"][0]).'",
					
					"cqct_ten_co_quan":"'.escapeJsonString($resDM["CQCT_TEN_CO_QUAN"][0]).'",
					"cqct_ho_ten_tt":"'.escapeJsonString($resDM["CQCT_HO_TEN_TT"][0]).'", 
					"cqct_dien_thoai":"'.escapeJsonString($resDM["CQCT_DIEN_THOAI"][0]).'",
					"cqct_fax":"'.escapeJsonString($resDM["CQCT_FAX"][0]).'",
					"cqct_email":"'.escapeJsonString($resDM["CQCT_EMAIL"][0]).'",
					"cqct_so_tai_khoan":"'.escapeJsonString($resDM["CQCT_SO_TAI_KHOAN"][0]).'",
					"cqct_kho_bac":"'.escapeJsonString($resDM["CQCT_KHO_BAC"][0]).'",
					
					"fk_cq_phoi_hop_1":"'.escapeJsonString($resDM["FK_CQ_PHOI_HOP_1"][0]).'",
					"cqph1_ten_co_quan":"'.escapeJsonString($resDM["CQPH1_TEN_CO_QUAN"][0]).'",
					"cqph1_ho_ten_tt":"'.escapeJsonString($resDM["CQPH1_HO_TEN_TT"][0]).'",
					"cqph1_dien_thoai":"'.escapeJsonString($resDM["CQPH1_DIEN_THOAI"][0]).'",
					"cqph1_fax":"'.escapeJsonString($resDM["CQPH1_FAX"][0]).'",
					"cqph1_dia_chi":"'.escapeJsonString($resDM["CQPH1_DIA_CHI"][0]).'",
					
					"fk_cq_phoi_hop_2":"'.escapeJsonString($resDM["FK_CQ_PHOI_HOP_2"][0]).'",
					"cqph2_ten_co_quan":"'.escapeJsonString($resDM["CQPH2_TEN_CO_QUAN"][0]).'",
					"cqph2_ho_ten_tt":"'.escapeJsonString($resDM["CQPH2_HO_TEN_TT"][0]).'",
					"cqph2_dien_thoai":"'.escapeJsonString($resDM["CQPH2_DIEN_THOAI"][0]).'",
					"cqph2_fax":"'.escapeJsonString($resDM["CQPH2_FAX"][0]).'",
					"cqph2_dia_chi":"'.escapeJsonString($resDM["CQPH2_DIA_CHI"][0]).'"
					
					}, 
				"success": "1"
				';
		
		$sqlstr="SELECT a.FK_MA_NHOM_NGANH, a.TEN_NHOM_NGANH_KHAC, b.TEN_NHOM_NGANH FROM NCKH_NHOM_NGANH_TMDT a, NCKH_NHOM_NGANH b  
		WHERE FK_MA_THUYET_MINH_DT='$m' and a.FK_MA_NHOM_NGANH=b.MA_NHOM_NGANH(+)";
		$stmt = oci_parse($db_conn_khcn, $sqlstr);oci_execute($stmt);$n = oci_fetch_all($stmt, $resDM);oci_free_statement($stmt);
		$data.= '
			,"nhomnganh":[
		';
		for ($i=0; $i<$n; $i++)
		{
			$data.= '{"manganh":"'.escapeJsonString($resDM["FK_MA_NHOM_NGANH"][$i]).'","nganhkhac":"'.escapeJsonString($resDM["TEN_NHOM_NGANH_KHAC"][$i]).'","tennganh":"'.escapeJsonString($resDM["TEN_NHOM_NGANH"][$i]).'"},';
		}
		$data=substr($data,0,-1);
		$data.= ']';
		
		$sqlstr="SELECT ma_can_bo,get_thanh_vien(ma_can_bo) HO_TEN,to_char(NGAY_SINH, 'dd/mm/yyyy') NGAY_SINH,PHAI,EMAIL,SO_CMND,
		to_char(NGAY_CAP,'dd/mm/yyyy') NGAY_CAP,NOI_CAP,DIA_CHI,DIEN_THOAI_CN,SO_TAI_KHOAN,NGAN_HANG_MO_TK,MA_SO_THUE 
		FROM CAN_BO_GIANG_DAY WHERE MA_CAN_BO='$macb'";
		$stmt = oci_parse($db_conn, $sqlstr);oci_execute($stmt);
		if (oci_fetch_all($stmt, $resDM)){
			$data.= '
				,"llkh":{
					"ma_can_bo":"'.escapeJsonString($resDM["MA_CAN_BO"][0]).'",
					"ho_ten":"'.escapeJsonString($resDM["HO_TEN"][0]).'",
					"ngay_sinh":"'.escapeJsonString($resDM["NGAY_SINH"][0]).'", 
					"phai":"'.escapeJsonString($resDM["PHAI"][0]).'",
					"email":"'.escapeJsonString($resDM["EMAIL"][0]).'",
					"so_cmnd":"'.escapeJsonString($resDM["SO_CMND"][0]).'",
					"ngay_cap":"'.escapeJsonString($resDM["NGAY_CAP"][0]).'",
					"noi_cap":"'.escapeJsonString($resDM["NOI_CAP"][0]).'",
					"dia_chi":"'.escapeJsonString($resDM["DIA_CHI"][0]).'",
					"dien_thoai_cn":"'.escapeJsonString($resDM["DIEN_THOAI_CN"][0]).'",
					"so_tai_khoan":"'.escapeJsonString($resDM["SO_TAI_KHOAN"][0]).'",
					"ngan_hang_mo_tk":"'.escapeJsonString($resDM["NGAN_HANG_MO_TK"][0]).'",
					"ma_so_thue":"'.escapeJsonString($resDM["MA_SO_THUE"][0]).'"
					}
				';
			oci_free_statement($stmt);
		}
		
		$sqlstr="SELECT * FROM NCKH_CO_QUAN WHERE MA_CO_QUAN='00001'";
		$stmt = oci_parse($db_conn_khcn, $sqlstr);oci_execute($stmt);
		if (oci_fetch_all($stmt, $resDM)){
			$data.= '
				,"coquanchutri":{
					"ma_co_quan":"'.escapeJsonString($resDM["MA_CO_QUAN"][0]).'",
					"ten_co_quan":"'.escapeJsonString($resDM["TEN_CO_QUAN"][0]).'",
					"ho_ten_tt":"'.escapeJsonString($resDM["HO_TEN_TT"][0]).'", 
					"dien_thoai":"'.escapeJsonString($resDM["DIEN_THOAI"][0]).'",
					"fax":"'.escapeJsonString($resDM["FAX"][0]).'",
					"dia_chi":"'.escapeJsonString($resDM["DIA_CHI"][0]).'",
					"email":"'.escapeJsonString($resDM["EMAIL"][0]).'",
					"so_tai_khoan":"'.escapeJsonString($resDM["SO_TAI_KHOAN"][0]).'",
					"kho_bac":"'.escapeJsonString($resDM["KHO_BAC"][0]).'",
					"ghi_chu":"'.escapeJsonString($resDM["GHI_CHU"][0]).'"
					}
				';
			oci_free_statement($stmt);
		}
		
		// Du lieu A9
		$sqlstr="SELECT * FROM NCKH_NHAN_LUC_TMDT_CBGD WHERE FK_MA_THUYET_MINH_DT='$m' order by MA_NHAN_LUC_TMDT_CBGD";
		$stmt = oci_parse($db_conn_khcn, $sqlstr);oci_execute($stmt);$n = oci_fetch_all($stmt, $resDM);oci_free_statement($stmt);
		$data.= '
			,"nhanluc_cbgd":[
		';
		for ($i=0; $i<$n; $i++){
			$data.= '{"ma_nhan_luc":"'.escapeJsonString($resDM["MA_NHAN_LUC_TMDT_CBGD"][$i]).'",
						"ma_cb":"'.escapeJsonString($resDM["FK_MA_CAN_BO"][$i]).'",
						"ho_ten":"'.escapeJsonString($resDM["HH_HV_HO_TEN"][$i]).'",
						"don_vi_cong_tac":"'.escapeJsonString($resDM["DON_VI_CONG_TAC"][$i]).'",
						"so_thang_lv_quy_doi":"'.escapeJsonString($resDM["SO_THANG_LV_QUY_DOI"][$i]).'",
						"shcc":"'.escapeJsonString($resDM["SHCC"][$i]).'"},';
		}
		$data=substr($data,0,-1);
		$data.= ']';
		
		$sqlstr="SELECT * FROM NCKH_NHAN_LUC_TMDT_SV WHERE FK_MA_THUYET_MINH_DT='$m' order by MA_NHAN_LUC_TMDT_SV";
		$stmt = oci_parse($db_conn_khcn, $sqlstr);oci_execute($stmt);$n = oci_fetch_all($stmt, $resDM);oci_free_statement($stmt);
		$data.= '
			,"nhanluc_sv":[
		';
		for ($i=0; $i<$n; $i++){
			$data.= '{"ma_nhan_luc":"'.escapeJsonString($resDM["MA_NHAN_LUC_TMDT_SV"][$i]).'",
						"ho_ten":"'.escapeJsonString($resDM["SV_HO_TEN"][$i]).'",
						"don_vi_cong_tac":"'.escapeJsonString($resDM["DON_VI_CONG_TAC"][$i]).'",
						"so_thang_lv_quy_doi":"'.escapeJsonString($resDM["SO_THANG_LV_QUY_DOI"][$i]).'",
						"ma_sv":"'.escapeJsonString($resDM["FK_MA_HOC_VIEN"][$i]).'"},';
		}
		$data=substr($data,0,-1);
		$data.= ']';
		
	}
	$data.='}';
	
	echo $data;
}

if ($a=='getmotanghiencuu'){
	$m = str_replace("'", "''", $_POST["m"]);

	$sqlstr="SELECT TQ_TINH_HINH_NC, Y_TUONG_KH, KQ_NC_SO_KHOI, TAI_LIEU_TK, MUC_TIEU_NC_VN, MUC_TIEU_NC_EN, NOI_DUNG_NC, PA_PHOI_HOP, 
	MUC_CL_SP_DANG_II, UD_KQNC_CHUYEN_GIAO, UD_KQNC_LV_DAO_TAO, UD_KQNC_SXKD, PHU_LUC_GIAI_TRINH_LINK, FK_CAP_DE_TAI
	FROM NCKH_THUYET_MINH_DE_TAI tm
	WHERE MA_THUYET_MINH_DT='$m'";
		
	$stmt = oci_parse($db_conn_khcn, $sqlstr);
	if (!oci_execute($stmt))
	{
		$e = oci_error($stmt);
		$msgerr = $e['message']. " sql: " . $e['sqltext'];
		die ('{"success":"-1", "msgerr":"'.escapeJsonString($msgerr).'"}');
	}
	$n = oci_fetch_all($stmt, $resDM);oci_free_statement($stmt);
	$data='{';
	if ($n){
		//file_put_contents("logs.txt", date("H:i:s d.m.Y")." ". escapeJsonString($resDM["TQ_TINH_HINH_NC"][0]));
		if ($resDM["PHU_LUC_GIAI_TRINH_LINK"][0]!=''){
			$path = pathinfo($resDM["PHU_LUC_GIAI_TRINH_LINK"][0]);
			$filename = $path['basename'];
		}else $filename='';
		
		$data.= '
			"mota":{
				"mathuyetminh":"'.escapeJsonString($m).'",
				"tq_tinh_hinh_nc":"'.escapeJsonString($resDM["TQ_TINH_HINH_NC"][0]).'",
				"y_tuong_kh":"'.escapeJsonString($resDM["Y_TUONG_KH"][0]).'",
				"kq_nc_so_khoi":"'.escapeJsonString($resDM["KQ_NC_SO_KHOI"][0]).'",
				"tai_lieu_tk":"'.escapeJsonString($resDM["TAI_LIEU_TK"][0]).'",
				"muc_tieu_nc_vn":"'.escapeJsonString($resDM["MUC_TIEU_NC_VN"][0]).'",
				"muc_tieu_nc_en":"'.escapeJsonString($resDM["MUC_TIEU_NC_EN"][0]).'",
				"noi_dung_nc":"'.escapeJsonString($resDM["NOI_DUNG_NC"][0]).'",
				"pa_phoi_hop":"'.escapeJsonString($resDM["PA_PHOI_HOP"][0]).'",
				"muc_cl_sp_dang_ii":"'.escapeJsonString($resDM["MUC_CL_SP_DANG_II"][0]).'",
				"ud_kqnc_chuyen_giao":"'.escapeJsonString($resDM["UD_KQNC_CHUYEN_GIAO"][0]).'",
				"ud_kqnc_lv_dao_tao":"'.escapeJsonString($resDM["UD_KQNC_LV_DAO_TAO"][0]).'",
				"ud_kqnc_sxkd":"'.escapeJsonString($resDM["UD_KQNC_SXKD"][0]).'",
				"phu_luc_giai_trinh_link":"'.escapeJsonString($resDM["PHU_LUC_GIAI_TRINH_LINK"][0]).'",
				"phu_luc_giai_trinh_name":"'.escapeJsonString($filename).'",
				"fk_cap_de_tai":"'.escapeJsonString($resDM["FK_CAP_DE_TAI"][0]).'"
			}, 
			"success": "1"
			';
	}
	
	// Du lieu B4
	$sqlstr="SELECT * FROM NCKH_CHUYEN_GIA_TMDT WHERE FK_MA_THUYET_MINH_DT='$m'";
	$stmt = oci_parse($db_conn_khcn, $sqlstr);oci_execute($stmt);$n = oci_fetch_all($stmt, $resDM);oci_free_statement($stmt);
	$data.= '
		,"chuyengianc":[
	';
	for ($i=0; $i<$n; $i++){
		$data.= '{"ma_chuyen_gia":"'.escapeJsonString($resDM["MA_CHUYEN_GIA_TMDT"][$i]).'",
					"ho_ten":"'.escapeJsonString($resDM["HH_HV_HO_TEN"][$i]).'",
					"co_quan_cong_tac":"'.escapeJsonString($resDM["CO_QUAN_CONG_TAC"][$i]).'",
					"huong_nc_chuyen_sau":"'.escapeJsonString($resDM["HUONG_NC_CHUYEN_SAU"][$i]).'",
					"dia_chi":"'.escapeJsonString($resDM["DIA_CHI"][$i]).'",
					"dien_thoai":"'.escapeJsonString($resDM["DIEN_THOAI"][$i]).'",
					"email":"'.escapeJsonString($resDM["EMAIL"][$i]).'"
				},';
	}
	if ($n) $data=substr($data,0,-1);
	$data.= ']';
	
	// Du lieu B6.1
	$sqlstr="SELECT a.*,b.TEN_AN_PHAM_KH  FROM NCKH_AN_PHAM_KH_TMDT a, NCKH_DM_AN_PHAM_KH b WHERE a.FK_MA_THUYET_MINH_DT='$m' and a.FK_MA_AN_PHAM_KH = b.MA_AN_PHAM_KH(+)";
	$stmt = oci_parse($db_conn_khcn, $sqlstr);oci_execute($stmt);$n = oci_fetch_all($stmt, $resDM);oci_free_statement($stmt);
	$data.= '
		,"anphamkhoahoc":[
	';
	for ($i=0; $i<$n; $i++){
		$data.= '{"fk_ma_an_pham_kh":"'.escapeJsonString($resDM["FK_MA_AN_PHAM_KH"][$i]).'",
					"ten_bb_sach_dk":"'.escapeJsonString($resDM["TEN_BB_SACH_DK"][$i]).'",
					"so_luong":"'.escapeJsonString($resDM["SO_LUONG"][$i]).'",
					"dk_noi_cong_bo":"'.escapeJsonString($resDM["DK_NOI_CONG_BO"][$i]).'",
					"ghi_chu":"'.escapeJsonString($resDM["GHI_CHU"][$i]).'",
					"ten_an_pham_kh":"'.escapeJsonString($resDM["TEN_AN_PHAM_KH"][$i]).'"
				},';
	}
	if ($n) $data=substr($data,0,-1);
	$data.= ']';
	
	// Du lieu B6.2
	$sqlstr="SELECT a.*, b.TEN_SO_HUU_TRI_TUE FROM NCKH_SO_HUU_TRI_TUE a, NCKH_DM_SO_HUU_TRI_TUE b WHERE FK_MA_THUYET_MINH_DT='$m' and a.FK_MA_SO_HUU_TRI_TUE=b.MA_SO_HUU_TRI_TUE(+)";
	$stmt = oci_parse($db_conn_khcn, $sqlstr);oci_execute($stmt);$n = oci_fetch_all($stmt, $resDM);oci_free_statement($stmt);
	$data.= '
		,"sohuutritue":[
	';
	for ($i=0; $i<$n; $i++){
		$data.= '{"fk_ma_so_huu_tri_tue":"'.escapeJsonString($resDM["FK_MA_SO_HUU_TRI_TUE"][$i]).'",
					"ten_hinh_thuc":"'.escapeJsonString($resDM["TEN_SO_HUU_TRI_TUE"][$i]).'",
					"so_luong":"'.escapeJsonString($resDM["SO_LUONG"][$i]).'",
					"noi_dung_du_kien":"'.escapeJsonString($resDM["NOI_DUNG_DU_KIEN"][$i]).'",
					"ghi_chu":"'.escapeJsonString($resDM["GHI_CHU"][$i]).'"
				},';
	}
	if ($n) $data=substr($data,0,-1);
	$data.= ']';
	
	$sqlstr="SELECT * FROM NCKH_SAN_PHAM_MEM WHERE FK_MA_THUYET_MINH_DT='$m'";
	$stmt = oci_parse($db_conn_khcn, $sqlstr);oci_execute($stmt);$n = oci_fetch_all($stmt, $resDM);oci_free_statement($stmt);
	$data.= '
		,"sanphammem":[
	';
	for ($i=0; $i<$n; $i++){
		$data.= '{"ma_san_pham_mem_tmdt":"'.escapeJsonString($resDM["MA_SAN_PHAM_MEM_TMDT"][$i]).'",
					"ten_san_pham":"'.escapeJsonString($resDM["TEN_SAN_PHAM"][$i]).'",
					"chi_tieu_danh_gia":"'.escapeJsonString($resDM["CHI_TIEU_DANH_GIA"][$i]).'",
					"ghi_chu":"'.escapeJsonString($resDM["GHI_CHU"][$i]).'"
				},';
	}
	if ($n) $data=substr($data,0,-1);
	$data.= ']';
	
	$sqlstr="SELECT * FROM NCKH_SAN_PHAM_CUNG WHERE FK_MA_THUYET_MINH_DT='$m'";
	$stmt = oci_parse($db_conn_khcn, $sqlstr);oci_execute($stmt);$n = oci_fetch_all($stmt, $resDM);oci_free_statement($stmt);
	$data.= '
		,"sanphamcung":[
	';
	for ($i=0; $i<$n; $i++){
		$data.= '{"ma_san_pham_cung_tmdt":"'.escapeJsonString($resDM["MA_SAN_PHAM_CUNG_TMDT"][$i]).'",
					"ten_san_pham":"'.escapeJsonString($resDM["TEN_SAN_PHAM_CHI_TIEU"][$i]).'",
					"chi_tieu_danh_gia":"'.escapeJsonString($resDM["CHI_TIEU_DANH_GIA"][$i]).'",
					"don_vi_do":"'.escapeJsonString($resDM["DON_VI_DO"][$i]).'",
					"trong_nuoc":"'.escapeJsonString($resDM["MAU_TT_TRONG_NUOC"][$i]).'", "the_gioi":"'.escapeJsonString($resDM["MAU_TT_THE_GIOI"][$i]).'",
					"so_luong_quy_mo":"'.escapeJsonString($resDM["DK_SL_QUY_MO_SP"][$i]).'"
				},';
	}
	if ($n) $data=substr($data,0,-1);
	$data.= ']';
	
	// Du lieu B6.3
	$sqlstr="SELECT a.*, b.TEN_BAC FROM NCKH_KQ_DAO_TAO_TMDT a, BAC_DAO_TAO b WHERE FK_MA_THUYET_MINH_DT='$m' and a.FK_BAC_DAO_TAO=b.MA_BAC(+)";
	$stmt = oci_parse($db_conn_khcn, $sqlstr);oci_execute($stmt);$n = oci_fetch_all($stmt, $resDM);oci_free_statement($stmt);
	$data.= '
		,"ketquadaotao":[
	';
	for ($i=0; $i<$n; $i++){
		$data.= '{"fk_bac_dao_tao":"'.escapeJsonString($resDM["FK_BAC_DAO_TAO"][$i]).'",
					"ten_capdt":"'.escapeJsonString($resDM["TEN_BAC"][$i]).'",
					"so_luong":"'.escapeJsonString($resDM["SO_LUONG"][$i]).'",
					"nhiem_vu_duoc_giao":"'.escapeJsonString($resDM["NHIEM_VU_DUOC_GIAO"][$i]).'",
					"du_kien_kinh_phi":"'.escapeJsonString($resDM["DU_KIEN_KINH_PHI"][$i]).'"
				},';
	}
	if ($n) $data=substr($data,0,-1);
	$data.= ']';
	
	// Du lieu B8
	$sqlstr="SELECT a.FK_MA_KHOAN_CHI_PHI, b.TEN_KHOAN_CHI_PHI, NVL(a.KINH_PHI,0) KINH_PHI, NVL(a.KHOAN_CHI,0) KHOAN_CHI, NVL(a.PHAN_TRAM,0) PHAN_TRAM
	FROM NCKH_TONG_HOP_KINH_PHI a, NCKH_DM_KHOAN_CHI_PHI b
	WHERE FK_MA_THUYET_MINH_DT='$m' and a.FK_MA_KHOAN_CHI_PHI=b.MA_KHOAN_CHI_PHI(+) 
	ORDER BY FK_MA_KHOAN_CHI_PHI";
	$stmt = oci_parse($db_conn_khcn, $sqlstr);oci_execute($stmt);$n = oci_fetch_all($stmt, $resDM);oci_free_statement($stmt);
	$data.= '
		,"khoanchiphi":[
	';
	for ($i=0; $i<$n; $i++){
		$data.= '{"fk_ma_khoan_chi_phi":"'.escapeJsonString($resDM["FK_MA_KHOAN_CHI_PHI"][$i]).'",
					"ten_khoan_chi_phi":"'.escapeJsonString($resDM["TEN_KHOAN_CHI_PHI"][$i]).'",
					"kinh_phi":"'.escapeJsonString($resDM["KINH_PHI"][$i]).'",
					"khoan_chi":"'.escapeJsonString($resDM["KHOAN_CHI"][$i]).'",
					"phan_tram":"'.escapeJsonString($resDM["PHAN_TRAM"][$i]).'"
				},';
	}
	if ($n) $data=substr($data,0,-1);
	$data.= ']';
	

	$data.='}';
	echo $data;
	//echo str_replace($searchdb, $replacedb,$resDM["TQ_TINH_HINH_NC"][0]);
}

if ($a=='getllkh'){
	$shcc = str_replace("'", "''", $_POST["khcn_frm_edit_dtkhcn_dcndt_shcc"]);
	if ($shcc==''){
		$shcc = str_replace("'", "''", $_POST["m"]);
	}
	$sqlstr="SELECT ma_can_bo,get_thanh_vien(ma_can_bo) HOTEN,to_char(NGAY_SINH, 'dd/mm/yyyy') NGAY_SINH,PHAI,EMAIL,SO_CMND,
	to_char(NGAY_CAP,'dd/mm/yyyy') NGAY_CAP,NOI_CAP,DIA_CHI,DIEN_THOAI_CN,SO_TAI_KHOAN,NGAN_HANG_MO_TK,MA_SO_THUE, CO_QUAN_CONG_TAC
	FROM CAN_BO_GIANG_DAY WHERE SHCC='$shcc'";
	
	$stmt = oci_parse($db_conn, $sqlstr);oci_execute($stmt);
	if (oci_fetch_all($stmt, $resDM)){
		$data.= '{
				"llkh":{
					"ma_can_bo":"'.escapeWEB($resDM["MA_CAN_BO"][0]).'",
					"ho_ten":"'.escapeWEB($resDM["HOTEN"][0]).'",
					"ngay_sinh":"'.escapeWEB($resDM["NGAY_SINH"][0]).'", 
					"phai":"'.escapeWEB($resDM["PHAI"][0]).'",
					"email":"'.escapeWEB($resDM["EMAIL"][0]).'",
					"so_cmnd":"'.escapeWEB($resDM["SO_CMND"][0]).'",
					"ngay_cap":"'.escapeWEB($resDM["NGAY_CAP"][0]).'",
					"noi_cap":"'.escapeWEB($resDM["NOI_CAP"][0]).'",
					"dia_chi":"'.escapeWEB($resDM["DIA_CHI"][0]).'",
					"dien_thoai_cn":"'.escapeWEB($resDM["DIEN_THOAI_CN"][0]).'",
					"so_tai_khoan":"'.escapeWEB($resDM["SO_TAI_KHOAN"][0]).'",
					"ngan_hang_mo_tk":"'.escapeWEB($resDM["NGAN_HANG_MO_TK"][0]).'",
					"ma_so_thue":"'.escapeWEB($resDM["MA_SO_THUE"][0]).'",
					"co_quan_cong_tac":"'.escapeWEB($resDM["CO_QUAN_CONG_TAC"][0]).'"
					},
				"success":"1"
				}';
		oci_free_statement($stmt);
		echo $data;
	}
	else{
		$e = oci_error($stmt);
		$msgerr = $e['message']. " sql: " . $e['sqltext'];
		die ('{"success":"-1", "msgerr":"'.escapeWEB($msgerr).'"}');
	}
}

if ($a=='getcq'){
	$m = str_replace("'", "''", $_POST["mcq"]);
	$sqlstr="SELECT * FROM NCKH_CO_QUAN WHERE MA_CO_QUAN='$m'";
	
	$stmt = oci_parse($db_conn_khcn, $sqlstr);oci_execute($stmt);
	if (oci_fetch_all($stmt, $resDM)){
		$data.= '{
				"coquan":{
					"ma_co_quan":"'.escapeWEB($resDM["MA_CO_QUAN"][0]).'",
					"ten_co_quan":"'.escapeWEB($resDM["TEN_CO_QUAN"][0]).'",
					"ho_ten_tt":"'.escapeWEB($resDM["HO_TEN_TT"][0]).'", 
					"dien_thoai":"'.escapeWEB($resDM["DIEN_THOAI"][0]).'",
					"fax":"'.escapeWEB($resDM["FAX"][0]).'",
					"dia_chi":"'.escapeWEB($resDM["DIA_CHI"][0]).'",
					"email":"'.escapeWEB($resDM["EMAIL"][0]).'",
					"so_tai_khoan":"'.escapeWEB($resDM["SO_TAI_KHOAN"][0]).'",
					"kho_bac":"'.escapeWEB($resDM["KHO_BAC"][0]).'",
					"ghi_chu":"'.escapeWEB($resDM["GHI_CHU"][0]).'"
					},
				"success":"1"
				}';
		oci_free_statement($stmt);
		echo $data;
	}
	else{
		$e = oci_error($stmt);
		$msgerr = $e['message']. " sql: " . $e['sqltext'];
		die ('{"success":"-1", "msgerr":"'.escapeWEB($msgerr).'"}');
	}
}

if ($a=='refreshdata'){
	$sqlstr="	SELECT MA_THUYET_MINH_DT, TEN_DE_TAI_VN, cdt.ten_cap, lhnc.TEN_LOAI_HINH_NC, THOI_GIAN_THUC_HIEN, TONG_KINH_PHI, FK_CAP_DE_TAI,
				GET_NGANH_NHOM_NGANH(MA_THUYET_MINH_DT) NGANH_NHOMNGANH, KEYWORDS, HUONG_DE_TAI,
				nvl(tm.FK_TINH_TRANG,'01') FK_TINH_TRANG, tt.TEN_TINH_TRANG, nvl(tt.EDIT_ALLOW, 0) EDIT_ALLOW, FK_DONG_CHU_NHIEM_DT,
				(sysdate - cdt.DKDT_NGAY_BD) BAT_DAU_DKDT, (sysdate-cdt.dkdt_ngay_kt) HET_HAN_DKDT, bm.TEN_BO_MON, nvl(tm.NGAY_DANG_KY-cdt.DKDT_NGAY_BD, 0) edit_allow_2
				FROM NCKH_THUYET_MINH_DE_TAI tm, CAP_DE_TAI cdt, NCKH_LOAI_HINH_NC lhnc, NCKH_DM_TINH_TRANG tt, BO_MON bm
				WHERE FK_CAP_DE_TAI = cdt.ma_cap(+) and FK_LOAI_HINH_NC = lhnc.MA_LOAI_HINH_NC(+)
				and tm.FK_MA_CAN_BO = '$macb' AND nvl(tm.FK_TINH_TRANG,'01') = tt.MA_TINH_TRANG (+) AND tm.THUNG_RAC is null
				and FK_BO_MON_DANG_KY=bm.ma_bo_mon(+)
				";
	/* file_put_contents("logs.txt", "----------------------------------------------\n
				". date("H:i:s d.m.Y")." $sqlstr \n
		----------------------------------------------\n"); */
	
	$stmt = oci_parse($db_conn_khcn, $sqlstr);oci_execute($stmt);$n = oci_fetch_all($stmt, $resDM);oci_free_statement($stmt);
	$data='{
			"aaData":[';
	
	for ($i = 0; $i < $n; $i++){
		// $resDM["HET_HAN_DKDT"][$i]<0 => còn hạn
		if ($resDM["EDIT_ALLOW"][$i]==1 && $resDM["EDIT_ALLOW_2"][$i]>0 && $resDM["HET_HAN_DKDT"][$i]<0){
			$SendTMDT = '"<img src=\'icons/Send-Document-icon.png\' class=khcn_tooltips title=\'Hoàn tất đăng ký TMĐT\' border=0 onClick=\'khcn_hoantat_tmdt( khcn_getRowIndex(this),\"'.$resDM["FK_CAP_DE_TAI"][$i].'\"); \' style=\'cursor: pointer\'>"';
		}else{
			$SendTMDT = '"<img src=\'icons/circle-green.png\' class=khcn_tooltips title=\'\' border=0 >"';
		}
		
		if ($resDM["FK_TINH_TRANG"][$i]=='01'){
			$DeleteTMDT = '"<img src=\'icons/delete-icon.png\' class=khcn_tooltips title=\'Xoá TMĐT\' border=0 onClick=\'khcn_delete_tmdt( khcn_getRowIndex(this) ); \' style=\'cursor: pointer\'>"';
		}else{
			$DeleteTMDT = '""';
		}
		
		$data.= '["'.$resDM["MA_THUYET_MINH_DT"][$i].'",
				  "'.escapeJsonString($resDM["TEN_DE_TAI_VN"][$i]).'", 
				  "'.escapeJsonString($resDM["NGANH_NHOMNGANH"][$i]).'", 
				  "'.escapeJsonString($resDM["HUONG_DE_TAI"][$i]).'", 
				  "'.escapeJsonString($resDM["KEYWORDS"][$i]).'", 
				  "'.escapeJsonString($resDM["TEN_CAP"][$i]).'",
				  "'.escapeJsonString($resDM["TEN_BO_MON"][$i]).'", 
				  "'.escapeJsonString($resDM["TEN_LOAI_HINH_NC"][$i]).'", 
				  "'.escapeJsonString($resDM["THOI_GIAN_THUC_HIEN"][$i]).'",
				  "'.number_format(escapeJsonString($resDM["TONG_KINH_PHI"][$i])).'", 
				  '.$SendTMDT.',
				  "'.escapeJsonString($resDM["TEN_TINH_TRANG"][$i]).'",
				  '.$DeleteTMDT.',
				  "<img src=\'icons/print-preview-icon24x24.png\' class=khcn_tooltips title=\'Chỉ được phép in In thuyết minh đề tài<br>khi đã <b>hoàn tất đăng ký</b>\' border=0 onClick=\'khcn_print_tmdt( khcn_getRowIndex(this),\"'.$resDM["FK_CAP_DE_TAI"][$i].'\"); \' style=\'cursor: pointer\'>",
				  "'.$resDM["FK_TINH_TRANG"][$i].'", 
				  "'.$resDM["EDIT_ALLOW"][$i].'",
				  "'.$resDM["FK_DONG_CHU_NHIEM_DT"][$i].'",
				  "'.$resDM["BAT_DAU_DKDT"][$i].'",
				  "'.$resDM["HET_HAN_DKDT"][$i].'"
				 ],';
	}
	// data[17] là item cuối
	
	if ($n>0) 
		$data=substr($data,0,-1);
	
	$data.='	]
			}';
	
	echo $data;
}

if ($a=='updatea1a4'){

	$matm = str_replace("'", "''", $_POST["m"]);
	$tenviet = str_replace("'", "''", $_POST["khcn_frm_edit_dtkhcn_ten_dt_viet"]);
	$tenanh = str_replace("'", "''", $_POST["khcn_frm_edit_dtkhcn_ten_dt_anh"]);
	$nganhkhac = str_replace("'", "''", $_POST["khcn_frm_edit_nganhkhac"]);
	$nganhhep = str_replace("'", "''", $_POST["khcn_frm_edit_dtkhcn_cnganhhep"]);
	$donvi = str_replace("'", "''", $_POST["khcn_frm_edit_dtkhcn_dvdk"]);
	$loaihinhnc = str_replace("'", "''", $_POST["khcn_frm_edit_dtkhcn_loaihinhnc"]);
	$thoigian = str_replace("'", "''", $_POST["khcn_frm_edit_dtkhcn_thoigianthuchien"]);
	$keywords = str_replace("'", "''", $_POST["khcn_frm_edit_dtkhcn_keywords"]);
	$huongdt = str_replace("'", "''", $_POST["khcn_frm_edit_dtkhcn_huongdt"]);
	
	$sqlstr = "update NCKH_THUYET_MINH_DE_TAI set TEN_DE_TAI_VN='$tenviet', TEN_DE_TAI_EN='$tenanh', CHUYEN_NGANH_HEP='$nganhhep',
	FK_LOAI_HINH_NC='$loaihinhnc',THOI_GIAN_THUC_HIEN='$thoigian',KEYWORDS='$keywords', HUONG_DE_TAI='$huongdt', FK_BO_MON_DANG_KY='$donvi'
	where MA_THUYET_MINH_DT='$matm'";
	
	$stmt = oci_parse($db_conn_khcn, $sqlstr);
	if (!oci_execute($stmt)){
		$e = oci_error($stmt);
		$msgerr = $e['message']. " sql: " . $e['sqltext'];
		die ('{"success":"-1", "msgerr":"'.escapeJsonString($msgerr).'"}');
	}
	oci_free_statement($stmt);
	
	// Update nhom nganh
	$sqlstr="delete NCKH_NHOM_NGANH_TMDT where FK_MA_THUYET_MINH_DT = '$matm'"; 
	$stmt = oci_parse($db_conn_khcn, $sqlstr);
	if (!oci_execute($stmt)){
		$e = oci_error($stmt);
		$msgerr = $e['message']. " sql: " . $e['sqltext'];
		die ('{"success":"-1", "msgerr":"'.escapeJsonString($msgerr).'"}');
	}
	oci_free_statement($stmt);
	
	$countnganh = str_replace("'", "''", $_POST["c"]);
	for ($i=0; $i<$countnganh; $i++){
		if (isset($_POST["khcn_frm_edit_nganh$i"])){
			$nganhnhomnganh = str_replace("'", "''", $_POST["khcn_frm_edit_nganh$i"]);
			if ($nganhnhomnganh=='999')
				$sqlstr="insert into NCKH_NHOM_NGANH_TMDT(FK_MA_THUYET_MINH_DT,FK_MA_NHOM_NGANH,TEN_NHOM_NGANH_KHAC) values ('$matm','$nganhnhomnganh','$nganhkhac')"; 
			else
				$sqlstr="insert into NCKH_NHOM_NGANH_TMDT(FK_MA_THUYET_MINH_DT,FK_MA_NHOM_NGANH) values ('$matm','$nganhnhomnganh')"; 
			
			$stmt = oci_parse($db_conn_khcn, $sqlstr);
			if (!oci_execute($stmt)){
				$e = oci_error($stmt);
				$msgerr = $e['message']. " sql: " . $e['sqltext'];
				die ('{"success":"-1", "msgerr":"'.escapeJsonString($msgerr).'"}');
				break 1;
			}
			oci_free_statement($stmt);
		}
	}
	
	
	$sqlstr="SELECT MA_THUYET_MINH_DT, TEN_DE_TAI_VN, cdt.ten_cap, lhnc.TEN_LOAI_HINH_NC, THOI_GIAN_THUC_HIEN, TONG_KINH_PHI,
	GET_NGANH_NHOM_NGANH(MA_THUYET_MINH_DT) nganh_nhomnganh, HUONG_DE_TAI, KEYWORDS, bm.TEN_BO_MON
	FROM NCKH_THUYET_MINH_DE_TAI tm, CAP_DE_TAI cdt, NCKH_LOAI_HINH_NC lhnc, bo_mon bm
	WHERE MA_THUYET_MINH_DT='$matm' and FK_CAP_DE_TAI = cdt.ma_cap(+) and FK_LOAI_HINH_NC = lhnc.MA_LOAI_HINH_NC(+) and FK_BO_MON_DANG_KY=bm.ma_bo_mon(+)";
	$stmt = oci_parse($db_conn_khcn, $sqlstr);
	if (!oci_execute($stmt)){
		$e = oci_error($stmt);
		$msgerr = $e['message']. " sql: " . $e['sqltext'];
		die ('{"success":"-1", "msgerr":"'.escapeJsonString($msgerr).'"}');
	}//
	$n = oci_fetch_all($stmt, $resDM);oci_free_statement($stmt);
	if ($n){
		echo '{"success":"1", 
		"tendetaivn":"'.escapeJsonString($resDM["TEN_DE_TAI_VN"][0]).'", "nganhnhomnganh":"'.escapeJsonString($resDM["NGANH_NHOMNGANH"][0]).'",
		"capdetai":"'.escapeJsonString($resDM["TEN_CAP"][0]).'", "loaihinhnc":"'.escapeJsonString($resDM["TEN_LOAI_HINH_NC"][0]).'",
		"thoigianthuchien":"'.escapeJsonString($resDM["THOI_GIAN_THUC_HIEN"][0]).'", "tongkinhphi":"'.escapeJsonString($resDM["TONG_KINH_PHI"][0]).'",
		"huongdt":"'.escapeJsonString($resDM["HUONG_DE_TAI"][0]).'", "keywords":"'.escapeJsonString($resDM["KEYWORDS"][0]).'",
		"tenbomon":"'.escapeJsonString($resDM["TEN_BO_MON"][0]).'"
		}';
	}
}

if ($a=='updatea5'){
	$matm = str_replace("'", "''", $_POST["m"]);
	$kinhphi = str_replace(array("'",","), array("''",""), $_POST["khcn_frm_edit_dtkhcn_tongkinhphi"]);
	$kinhphidhqg = str_replace(array("'",","), array("''",""), $_POST["khcn_frm_edit_dtkhcn_kinhphi_dhqg"]);
	$kinhphihuydong = str_replace(array("'",","), array("''",""), $_POST["khcn_frm_edit_dtkhcn_kinhphi_huydong"]);
	$huydongtuco = str_replace(array("'",","), array("''",""), $_POST["khcn_frm_edit_dtkhcn_kinhphi_tuco"]);
	$huydongkhac = str_replace(array("'",","), array("''",""), $_POST["khcn_frm_edit_dtkhcn_kinhphi_khac"]);
	$tochuctaitro = str_replace("'", "''", $_POST["khcn_frm_edit_dtkhcn_tochuctaitro"]);

	$sqlstr = "update NCKH_THUYET_MINH_DE_TAI set TONG_KINH_PHI='$kinhphi',KINH_PHI_HUY_DONG='$kinhphihuydong',
	KINH_PHI_TU_DHQG='$kinhphidhqg',HD_VON_TU_CO='$huydongtuco',HD_KHAC='$huydongkhac',TO_CHUC_TAI_TRO_KHAC = '$tochuctaitro'
	where MA_THUYET_MINH_DT='$matm'";
	
	$stmt = oci_parse($db_conn_khcn, $sqlstr);
	if (!oci_execute($stmt)){
		$e = oci_error($stmt);
		$msgerr = $e['message']. " sql: " . $e['sqltext'];
		die ('{"success":"-1", "msgerr":"'.escapeJsonString($msgerr).'"}');
	}
	oci_free_statement($stmt);
		
	echo '{"success":"1", "tongkinhphi":"'.escapeJsonString($kinhphi).'"}';
}

if ($a=='updatea6'){

	$matm = str_replace("'", "''", $_POST["m"]);
	$fk_chu_nhiem_dt = str_replace("'", "''", $_POST["khcn_frm_edit_dtkhcn_fk_chu_nhiem_dt"]);
	$tencn = str_replace("'", "''", $_POST["khcn_frm_edit_dtkhcn_cndt_hh_hv_ho_ten"]);
	$ngaysinhcn = str_replace("'", "''", $_POST["khcn_frm_edit_dtkhcn_cndt_ngay_sinh"]);
	$phaicn = str_replace("'", "''", $_POST["khcn_frm_edit_dtkhcn_cndt_phai"]);
	$cmndcn = str_replace("'", "''", $_POST["khcn_frm_edit_dtkhcn_cndt_so_cmnd"]);
	$ngaycapcn = str_replace("'", "''", $_POST["khcn_frm_edit_dtkhcn_cndt_ngay_cap"]);
	$noicapcn = str_replace("'", "''", $_POST["khcn_frm_edit_dtkhcn_cndt_noi_cap"]);
	$msthuecn = str_replace("'", "''", $_POST["khcn_frm_edit_dtkhcn_cndt_ms_thue"]);
	$sotkcn = str_replace("'", "''", $_POST["khcn_frm_edit_dtkhcn_cndt_so_tai_khoan"]);
	$tktainganhang = str_replace("'", "''", $_POST["khcn_frm_edit_dtkhcn_cndt_ngan_hang"]);
	$diachicqcn = str_replace("'", "''", $_POST["khcn_frm_edit_dtkhcn_cndt_dia_chi_cq"]);
	$dienthoaicn = str_replace("'", "''", $_POST["khcn_frm_edit_dtkhcn_cndt_dien_thoai"]);
	$emailcn = str_replace("'", "''", $_POST["khcn_frm_edit_dtkhcn_cndt_email"]);
	$hdnccn = str_replace("'", "''", $_POST["khcn_frm_edit_dtkhcn_tom_tat_hd_nc"]);
	
	$fk_dong_chu_nhiem_dt = str_replace("'", "''", $_POST["khcn_frm_edit_dtkhcn_fk_dong_chu_nhiem_dt"]);
	$tendcn = str_replace("'", "''", $_POST["khcn_frm_edit_dtkhcn_dcndt_hh_hv_ho_ten"]);
	$ngaysinhdcn = str_replace("'", "''", $_POST["khcn_frm_edit_dtkhcn_dcndt_ngay_sinh"]);
	$phaidcn = str_replace("'", "''", $_POST["khcn_frm_edit_dtkhcn_dcndt_phai"]);
	$cmnddcn = str_replace("'", "''", $_POST["khcn_frm_edit_dtkhcn_dcndt_so_cmnd"]);
	$ngaycapdcn = str_replace("'", "''", $_POST["khcn_frm_edit_dtkhcn_dcndt_ngay_cap"]);
	$noicapdcn = str_replace("'", "''", $_POST["khcn_frm_edit_dtkhcn_dcndt_noi_cap"]);
	$msthuedcn = str_replace("'", "''", $_POST["khcn_frm_edit_dtkhcn_dcndt_ms_thue"]);
	$sotkdcn = str_replace("'", "''", $_POST["khcn_frm_edit_dtkhcn_dcndt_so_tai_khoan"]);
	$tktainganhangdcn = str_replace("'", "''", $_POST["khcn_frm_edit_dtkhcn_dcndt_ngan_hang"]);
	$diachicqdcn = str_replace("'", "''", $_POST["khcn_frm_edit_dtkhcn_dcndt_dia_chi_cq"]);
	$dienthoaidcn = str_replace("'", "''", $_POST["khcn_frm_edit_dtkhcn_dcndt_dien_thoai"]);
	$emaildcn = str_replace("'", "''", $_POST["khcn_frm_edit_dtkhcn_dcndt_email"]);
	
	$sqlstr = "update NCKH_THUYET_MINH_DE_TAI set CNDT_HH_HV_HO_TEN='$tencn', CNDT_NGAY_SINH=to_date('$ngaysinhcn','dd/mm/yyyy'), CNDT_PHAI='$phaicn',CNDT_SO_CMND='$cmndcn',
	CNDT_NGAY_CAP=to_date('$ngaycapcn', 'dd/mm/yyyy'),CNDT_NOI_CAP='$noicapcn',CNDT_MS_THUE='$msthuecn',CNDT_SO_TAI_KHOAN='$sotkcn',
	CNDT_NGAN_HANG='$tktainganhang',CNDT_DIA_CHI_CQ='$diachicqcn',CNDT_DIEN_THOAI='$dienthoaicn',CNDT_EMAIL = '$emailcn',TOM_TAT_HD_NC='$hdnccn',
	FK_CHU_NHIEM_DT='$fk_chu_nhiem_dt', FK_DONG_CHU_NHIEM_DT='$fk_dong_chu_nhiem_dt',
	DCNDT_HH_HV_HO_TEN='$tendcn', DCNDT_NGAY_SINH=to_date('$ngaysinhdcn','dd/mm/yyyy'), DCNDT_PHAI='$phaidcn',DCNDT_SO_CMND='$cmnddcn',
	DCNDT_NGAY_CAP=to_date('$ngaycapdcn', 'dd/mm/yyyy'),DCNDT_NOI_CAP='$noicapdcn',DCNDT_MS_THUE='$msthuedcn',DCNDT_SO_TAI_KHOAN='$sotkdcn',
	DCNDT_NGAN_HANG='$tktainganhangdcn',DCNDT_DIA_CHI_CQ='$diachicqdcn',DCNDT_DIEN_THOAI='$dienthoaidcn',DCNDT_EMAIL = '$emaildcn'
	where MA_THUYET_MINH_DT='$matm'";
	
	$stmt = oci_parse($db_conn_khcn, $sqlstr);
	if (!oci_execute($stmt)){
		$e = oci_error($stmt);
		$msgerr = $e['message']. " sql: " . $e['sqltext'];
		die ('{"success":"-1", "msgerr":"'.escapeJsonString($msgerr).'"}');
	}
	oci_free_statement($stmt);
		
	echo '{"success":"1"}';
}

if ($a=='updatea7a8'){

	$matm = str_replace("'", "''", $_POST["m"]);
	
	$cqct_ten_co_quan = str_replace("'", "''", $_POST["khcn_frm_edit_dtkhcn_cqct_ten_co_quan"]);
	$cqct_ho_ten_tt = str_replace("'", "''", $_POST["khcn_frm_edit_dtkhcn_cqct_ho_ten_tt"]);
	$cqct_dien_thoai = str_replace("'", "''", $_POST["khcn_frm_edit_dtkhcn_cqct_dien_thoai"]);
	$cqct_fax = str_replace("'", "''", $_POST["khcn_frm_edit_dtkhcn_cqct_fax"]);
	$cqct_email = str_replace("'", "''", $_POST["khcn_frm_edit_dtkhcn_cqct_email"]);
	$cqct_so_tai_khoan = str_replace("'", "''", $_POST["khcn_frm_edit_dtkhcn_cqct_so_tai_khoan"]);
	$cqct_kho_bac = str_replace("'", "''", $_POST["khcn_frm_edit_dtkhcn_cqct_kho_bac"]);
	
	$fk_cq_phoi_hop_1 = str_replace("'", "''", $_POST["khcn_frm_edit_dtkhcn_fk_cq_phoi_hop_1"]);
	$cqph1_ten_co_quan = str_replace("'", "''", $_POST["khcn_frm_edit_dtkhcn_cqph1_ten_co_quan"]);
	$cqph1_ho_ten_tt = str_replace("'", "''", $_POST["khcn_frm_edit_dtkhcn_cqph1_ho_ten_tt"]);
	$cqph1_dien_thoai = str_replace("'", "''", $_POST["khcn_frm_edit_dtkhcn_cqph1_dien_thoai"]);
	$cqph1_fax = str_replace("'", "''", $_POST["khcn_frm_edit_dtkhcn_cqph1_fax"]);
	$cqph1_dia_chi = str_replace("'", "''", $_POST["khcn_frm_edit_dtkhcn_cqph1_dia_chi"]);
	
	$fk_cq_phoi_hop_2 = str_replace("'", "''", $_POST["khcn_frm_edit_dtkhcn_fk_cq_phoi_hop_2"]);
	$cqph2_ten_co_quan = str_replace("'", "''", $_POST["khcn_frm_edit_dtkhcn_cqph2_ten_co_quan"]);
	$cqph2_ho_ten_tt = str_replace("'", "''", $_POST["khcn_frm_edit_dtkhcn_cqph2_ho_ten_tt"]);
	$cqph2_dien_thoai = str_replace("'", "''", $_POST["khcn_frm_edit_dtkhcn_cqph2_dien_thoai"]);
	$cqph2_fax = str_replace("'", "''", $_POST["khcn_frm_edit_dtkhcn_cqph2_fax"]);
	$cqph2_dia_chi = str_replace("'", "''", $_POST["khcn_frm_edit_dtkhcn_cqph2_dia_chi"]);
	
	
	$sqlstr = "update NCKH_THUYET_MINH_DE_TAI set cqct_ten_co_quan='$cqct_ten_co_quan', cqct_ho_ten_tt='$cqct_ho_ten_tt', cqct_dien_thoai='$cqct_dien_thoai',
	cqct_fax='$cqct_fax',cqct_email='$cqct_email',cqct_so_tai_khoan='$cqct_so_tai_khoan',cqct_kho_bac='$cqct_kho_bac',
	fk_cq_phoi_hop_1='$fk_cq_phoi_hop_1', fk_cq_phoi_hop_2='$fk_cq_phoi_hop_2',
	cqph1_ten_co_quan='$cqph1_ten_co_quan',cqph1_ho_ten_tt='$cqph1_ho_ten_tt',cqph1_dien_thoai='$cqph1_dien_thoai',cqph1_fax='$cqph1_fax',cqph1_dia_chi = '$cqph1_dia_chi',
	cqph2_ten_co_quan='$cqph2_ten_co_quan',cqph2_ho_ten_tt='$cqph2_ho_ten_tt',cqph2_dien_thoai='$cqph2_dien_thoai',cqph2_fax='$cqph2_fax',cqph2_dia_chi = '$cqph2_dia_chi'
	where MA_THUYET_MINH_DT='$matm'";
	
	$stmt = oci_parse($db_conn_khcn, $sqlstr);
	if (!oci_execute($stmt)){
		$e = oci_error($stmt);
		$msgerr = $e['message']. " sql: " . $e['sqltext'];
		die ('{"success":"-1", "msgerr":"'.escapeJsonString($msgerr).'"}');
	}
	oci_free_statement($stmt);
		
	echo '{"success":"1"}';
}

if ($a=='updateB1'){

	$matm = str_replace("'", "''", $_POST["m"]);
	$tq_tinh_hinh_nc = $_POST["tq_tinh_hinh_nc"];
		
	$sqlstr = "update NCKH_THUYET_MINH_DE_TAI set tq_tinh_hinh_nc=EMPTY_CLOB() where MA_THUYET_MINH_DT='$matm' RETURNING tq_tinh_hinh_nc INTO :mylob";
	$stmt = oci_parse($db_conn_khcn, $sqlstr);
	$clob = oci_new_descriptor($db_conn_khcn, OCI_D_LOB);
	oci_bind_by_name($stmt, ":mylob", $clob, -1, OCI_B_CLOB);
	
	if (oci_execute($stmt, OCI_DEFAULT)){
		if (!$clob->save($tq_tinh_hinh_nc)){
			oci_rollback($db_conn_khcn);
			$e = oci_error($stmt);
			$msgerr = $e['message']. " sql: " . $e['sqltext'];
			die ('{"success":"-1", "msgerr":"'.escapeJsonString($msgerr).'"}');
		}else{
			oci_commit($db_conn_khcn);
			echo '{"success":"1"}';
		}
	}else{
		$e = oci_error($stmt);
		$msgerr = $e['message']. " sql: " . $e['sqltext'];
		die ('{"success":"-1", "msgerr":"'.escapeJsonString($msgerr).'"}');
	}
	/*
	$sql = "INSERT INTO mytable (mykey, myclob)
        VALUES (:mykey, EMPTY_CLOB())
        RETURNING myclob INTO :myclob";
	$stid = oci_parse($conn, $sql);
	$clob = oci_new_descriptor($conn, OCI_D_LOB);
	oci_bind_by_name($stid, ":mykey", $mykey, 5);
	oci_bind_by_name($stid, ":myclob", $clob, -1, OCI_B_CLOB);
	oci_execute($stid, OCI_NO_AUTO_COMMIT); // use OCI_DEFAULT for PHP <= 5.3.1
	$clob->save("A very long string");
	*/
	oci_free_statement($stmt);
}

if ($a=='updateB2'){
	$matm = str_replace("'", "''", $_POST["m"]);
	$y_tuong_kh = $_POST["y_tuong_kh"];
		
	$sqlstr = "update NCKH_THUYET_MINH_DE_TAI set Y_TUONG_KH=EMPTY_CLOB() where MA_THUYET_MINH_DT='$matm' RETURNING Y_TUONG_KH INTO :mylob";
	$stmt = oci_parse($db_conn_khcn, $sqlstr);
	$clob = oci_new_descriptor($db_conn_khcn, OCI_D_LOB);
	oci_bind_by_name($stmt, ":mylob", $clob, -1, OCI_B_CLOB);
	
	if (oci_execute($stmt, OCI_DEFAULT)){
		if (!$clob->save($y_tuong_kh)){
			oci_rollback($db_conn_khcn);
			$e = oci_error($stmt);
			$msgerr = $e['message']. " sql: " . $e['sqltext'];
			die ('{"success":"-1", "msgerr":"'.escapeJsonString($msgerr).'"}');
		}else{
			oci_commit($db_conn_khcn);
			echo '{"success":"1"}';
		}
	}else{
		$e = oci_error($stmt);
		$msgerr = $e['message']. " sql: " . $e['sqltext'];
		die ('{"success":"-1", "msgerr":"'.escapeJsonString($msgerr).'"}');
	}
	
	oci_free_statement($stmt);
}

if ($a=='updateB3'){
	$matm = str_replace("'", "''", $_POST["m"]);
	$kq_nc_so_khoi = $_POST["kq_nc_so_khoi"];
		
	$sqlstr = "update NCKH_THUYET_MINH_DE_TAI set kq_nc_so_khoi=EMPTY_CLOB() where MA_THUYET_MINH_DT='$matm' RETURNING kq_nc_so_khoi INTO :mylob";
	$stmt = oci_parse($db_conn_khcn, $sqlstr);
	$clob = oci_new_descriptor($db_conn_khcn, OCI_D_LOB);
	oci_bind_by_name($stmt, ":mylob", $clob, -1, OCI_B_CLOB);
	
	if (oci_execute($stmt, OCI_DEFAULT)){
		if (!$clob->save($kq_nc_so_khoi)){
			oci_rollback($db_conn_khcn);
			$e = oci_error($stmt);
			$msgerr = $e['message']. " sql: " . $e['sqltext'];
			die ('{"success":"-1", "msgerr":"'.escapeJsonString($msgerr).'"}');
		}else{
			oci_commit($db_conn_khcn);
			echo '{"success":"1"}';
		}
	}else{
		$e = oci_error($stmt);
		$msgerr = $e['message']. " sql: " . $e['sqltext'];
		die ('{"success":"-1", "msgerr":"'.escapeJsonString($msgerr).'"}');
	}
	
	oci_free_statement($stmt);
}

if ($a=='updateB4'){
	$matm = str_replace("'", "''", $_POST["m"]);
	$tai_lieu_tk = $_POST["tai_lieu_tk"];
		
	$sqlstr = "update NCKH_THUYET_MINH_DE_TAI set TAI_LIEU_TK=EMPTY_CLOB() where MA_THUYET_MINH_DT='$matm' RETURNING TAI_LIEU_TK INTO :mylob";
	$stmt = oci_parse($db_conn_khcn, $sqlstr);
	$clob = oci_new_descriptor($db_conn_khcn, OCI_D_LOB);
	oci_bind_by_name($stmt, ":mylob", $clob, -1, OCI_B_CLOB);
	
	if (oci_execute($stmt, OCI_DEFAULT)){
		if (!$clob->save($tai_lieu_tk)){
			oci_rollback($db_conn_khcn);
			$e = oci_error($stmt);
			$msgerr = $e['message']. " sql: " . $e['sqltext'];
			die ('{"success":"-1", "msgerr":"'.escapeJsonString($msgerr).'"}');
		}else{
			oci_commit($db_conn_khcn);
			echo '{"success":"1"}';
		}
	}else{
		$e = oci_error($stmt);
		$msgerr = $e['message']. " sql: " . $e['sqltext'];
		die ('{"success":"-1", "msgerr":"'.escapeJsonString($msgerr).'"}');
	}
	
	oci_free_statement($stmt);
}

if ($a=='updateB5_1'){
	$matm = str_replace("'", "''", $_POST["m"]);
	$muc_tieu_nc_en = str_replace("'", "''",$_POST["muc_tieu_nc_en"]);
	$muc_tieu_nc_vn = str_replace("'", "''",$_POST["muc_tieu_nc_vn"]);
		
	$sqlstr = "update NCKH_THUYET_MINH_DE_TAI set muc_tieu_nc_vn='$muc_tieu_nc_vn',muc_tieu_nc_en='$muc_tieu_nc_en' where MA_THUYET_MINH_DT='$matm'";
	$stmt = oci_parse($db_conn_khcn, $sqlstr);

	if (oci_execute($stmt)){
		echo '{"success":"1"}';
	}else{
		$e = oci_error($stmt);
		$msgerr = $e['message']. " sql: " . $e['sqltext'];
		die ('{"success":"-1", "msgerr":"'.escapeJsonString($msgerr).'"}');
	}
	
	oci_free_statement($stmt);
}

if ($a=='updateB5_2'){
	$matm = str_replace("'", "''", $_POST["m"]);
	$noi_dung_nc = $_POST["noi_dung_nc"];
		
	$sqlstr = "update NCKH_THUYET_MINH_DE_TAI set noi_dung_nc=EMPTY_CLOB() where MA_THUYET_MINH_DT='$matm' RETURNING noi_dung_nc INTO :mylob";
	$stmt = oci_parse($db_conn_khcn, $sqlstr);
	$clob = oci_new_descriptor($db_conn_khcn, OCI_D_LOB);
	oci_bind_by_name($stmt, ":mylob", $clob, -1, OCI_B_CLOB);
	
	if (oci_execute($stmt, OCI_DEFAULT)){
		if (!$clob->save($noi_dung_nc)){
			oci_rollback($db_conn_khcn);
			$e = oci_error($stmt);
			$msgerr = $e['message']. " sql: " . $e['sqltext'];
			die ('{"success":"-1", "msgerr":"'.escapeJsonString($msgerr).'"}');
		}else{
			oci_commit($db_conn_khcn);
			echo '{"success":"1"}';
		}
	}else{
		$e = oci_error($stmt);
		$msgerr = $e['message']. " sql: " . $e['sqltext'];
		die ('{"success":"-1", "msgerr":"'.escapeJsonString($msgerr).'"}');
	}
	
	oci_free_statement($stmt);
}

if ($a=='updateB5_3'){
	$matm = str_replace("'", "''", $_POST["m"]);
	$pa_phoi_hop = $_POST["pa_phoi_hop"];
		
	$sqlstr = "update NCKH_THUYET_MINH_DE_TAI set pa_phoi_hop=EMPTY_CLOB() where MA_THUYET_MINH_DT='$matm' RETURNING pa_phoi_hop INTO :mylob";
	$stmt = oci_parse($db_conn_khcn, $sqlstr);
	$clob = oci_new_descriptor($db_conn_khcn, OCI_D_LOB);
	oci_bind_by_name($stmt, ":mylob", $clob, -1, OCI_B_CLOB);
	
	if (oci_execute($stmt, OCI_DEFAULT)){
		if (!$clob->save($pa_phoi_hop)){
			oci_rollback($db_conn_khcn);
			$e = oci_error($stmt);
			$msgerr = $e['message']. " sql: " . $e['sqltext'];
			die ('{"success":"-1", "msgerr":"'.escapeJsonString($msgerr).'"}');
		}else{
			oci_commit($db_conn_khcn);
			echo '{"success":"1"}';
		}
	}else{
		$e = oci_error($stmt);
		$msgerr = $e['message']. " sql: " . $e['sqltext'];
		die ('{"success":"-1", "msgerr":"'.escapeJsonString($msgerr).'"}');
	}
	
	oci_free_statement($stmt);
}

if ($a=='updateB6_2'){
	$matm = str_replace("'", "''", $_POST["m"]);
	$muc_cl_sp_dang_ii = str_replace("'", "''",$_POST["muc_cl_sp_dang_ii"]);
		
	$sqlstr = "update NCKH_THUYET_MINH_DE_TAI set muc_cl_sp_dang_ii='$muc_cl_sp_dang_ii' where MA_THUYET_MINH_DT='$matm'";
	$stmt = oci_parse($db_conn_khcn, $sqlstr);	
	if (oci_execute($stmt)){
		echo '{"success":"1"}';
	}else{
		$e = oci_error($stmt);
		$msgerr = $e['message']. " sql: " . $e['sqltext'];
		die ('{"success":"-1", "msgerr":"'.escapeJsonString($msgerr).'"}');
	}
	
	oci_free_statement($stmt);
}

if ($a=='updateB7'){
	$matm = str_replace("'", "''", $_POST["m"]);
	$ud_kqnc_chuyen_giao = str_replace("'", "''",$_POST["khcn_frm_edit_dtkhcn_ud_kqnc_chuyen_giao"]);
	$ud_kqnc_lv_dao_tao = str_replace("'", "''",$_POST["khcn_frm_edit_dtkhcn_ud_kqnc_lv_dao_tao"]);
	$ud_kqnc_sxkd = str_replace("'", "''",$_POST["khcn_frm_edit_dtkhcn_ud_kqnc_sxkd"]);
		
	$sqlstr = 	"update NCKH_THUYET_MINH_DE_TAI set ud_kqnc_chuyen_giao='$ud_kqnc_chuyen_giao', ud_kqnc_lv_dao_tao='$ud_kqnc_lv_dao_tao', 
				 ud_kqnc_sxkd='$ud_kqnc_sxkd' where MA_THUYET_MINH_DT='$matm'";
	$stmt = oci_parse($db_conn_khcn, $sqlstr);	
	if (oci_execute($stmt)){
		echo '{"success":"1"}';
	}else{
		$e = oci_error($stmt);
		$msgerr = $e['message']. " sql: " . $e['sqltext'];
		die ('{"success":"-1", "msgerr":"'.escapeJsonString($msgerr).'"}');
	}
	
	oci_free_statement($stmt);
}

if ($a=='updateB8'){
	$matm = str_replace("'", "''", $_POST["m"]);
	$fk_ma_khoan_chi_phi = str_replace("'", "''", $_POST["khcn_frm_reg_tonghopkinhphi_khoan_chi_phi"]);	
	$kinh_phi = str_replace(array("'", ","), array("''", ""), $_POST["khcn_frm_reg_tonghopkinhphi_kinh_phi"]);
	$khoan_chi = str_replace(array("'", ","), array("''", ""), $_POST["khcn_frm_reg_tonghopkinhphi_khoan_chi"]);
	
	$sqlstr = 	"update NCKH_TONG_HOP_KINH_PHI set KINH_PHI='$kinh_phi', KHOAN_CHI='$khoan_chi'
				where FK_MA_THUYET_MINH_DT='$matm' and FK_MA_KHOAN_CHI_PHI='$fk_ma_khoan_chi_phi'";
				
	/*file_put_contents("logs.txt", "----------------------------------------------\n
				". date("H:i:s d.m.Y")." $sqlstr \n
		----------------------------------------------\n");*/
		
	$stmt = oci_parse($db_conn_khcn, $sqlstr);	
	if (oci_execute($stmt)){
		echo '{"success":"1", "fk_ma_khoan_chi_phi":"'.$fk_ma_khoan_chi_phi.'" ,"kinh_phi":"'.$kinh_phi.'", "khoan_chi":"'.$khoan_chi.'"}';
	}else{
		$e = oci_error($stmt);
		$msgerr = $e['message']. " sql: " . $e['sqltext'];
		die ('{"success":"-1", "msgerr":"'.escapeJsonString($msgerr).'"}');
	}
	
	oci_free_statement($stmt);
}

if ($a=='updateS'){
	$matm = str_replace("'", "''", $_POST["m"]);
	
	$sqlstr = 	"update NCKH_THUYET_MINH_DE_TAI set FK_TINH_TRANG='02', NGAY_NHAN_HO_SO=sysdate where MA_THUYET_MINH_DT='$matm'";
	$stmt = oci_parse($db_conn_khcn, $sqlstr);	
	if (oci_execute($stmt)){
		echo '{"success":"1", "fk_tinh_trang":"02" ,"tinh_trang":"Hoàn tất đăng ký", "edit_allow":"0"}';
	}else{
		$e = oci_error($stmt);
		$msgerr = $e['message']. " sql: " . $e['sqltext'];
		die ('{"success":"-1", "msgerr":"'.escapeJsonString($msgerr).'"}');
	}
	
	oci_free_statement($stmt);
}

if ($a=='ds_cb_thamgia'){
	# thong tin lam luan van, hoc vien
	$sqlstr = "SELECT ma_can_bo, shcc, ten_bo_mon, ten_khoa, csdl.get_thanh_vien(c.ma_can_bo) cbgd, to_char(c.NGAY_SINH, 'dd/mm/yyyy') NGAY_SINH, c.PHAI, c.EMAIL, c.SO_CMND, to_char(c.NGAY_CAP,'dd/mm/yyyy') NGAY_CAP, c.NOI_CAP, c.DIA_CHI, c.DIEN_THOAI_CN, c.SO_TAI_KHOAN, c.NGAN_HANG_MO_TK, c.MA_SO_THUE, c.CO_QUAN_CONG_TAC
	FROM csdl.can_bo_giang_day c, csdl.bo_mon b, csdl.khoa k	
	WHERE 	c.ma_bo_mon = b.ma_bo_mon
			and b.ma_khoa = k.ma_khoa
			and c.trang_thai=1
	ORDER BY ten_khoa, ten_bo_mon, ten_eng, ho_eng";
	$stmt = oci_parse($db_conn_khcn, $sqlstr);
	
	if (!oci_execute($stmt)){
		$e = oci_error($stmt);
		$msgerr = $e['message']. " sql: " . $e['sqltext'];
		die ('{"success":"-1", "msgerr":"'.escapeJsonString($msgerr).'"}');
	}
	
	$n = oci_fetch_all($stmt, $resDM);oci_free_statement($stmt);
			
	$data='{
			"dscanbo": [';
	
	for ($i = 0; $i < $n; $i++)
	{
		$data .= '{
					"value" : "'.escapeJsonString($resDM["MA_CAN_BO"][$i]).'",
					"label" : "'.escapeJsonString($resDM["CBGD"][$i]).'",
					"desc"  : " SHCC: <b>'.escapeJsonString($resDM["SHCC"][$i]).'</b><br> Bộ môn: <b>'.escapeJsonString($resDM["TEN_BO_MON"][$i]).'</b><br> Khoa: <b>'.escapeJsonString($resDM["TEN_KHOA"][$i]).'</b>",
					"shcc" : "'.escapeJsonString($resDM["SHCC"][$i]).'",
					"cq" : "'.escapeJsonString($resDM["CO_QUAN_CONG_TAC"][$i]).'",
					"ngaysinh" : "'.escapeJsonString($resDM["NGAY_SINH"][$i]).'",
					"cmnd" : "'.escapeJsonString($resDM["SO_CMND"][$i]).'",
					"ngaycap" : "'.escapeJsonString($resDM["NGAY_CAP"][$i]).'",
					"noicap" : "'.escapeJsonString($resDM["NOI_CAP"][$i]).'",
					"msthue" : "'.escapeJsonString($resDM["MA_SO_THUE"][$i]).'",
					"stk" : "'.escapeJsonString($resDM["SO_TAI_KHOAN"][$i]).'",
					"nganhang" : "'.escapeJsonString($resDM["NGAN_HANG_MO_TK"][$i]).'",
					"diachicq" : "'.escapeJsonString($resDM["DIA_CHI"][$i]).'",
					"dienthoai" : "'.escapeJsonString($resDM["DIEN_THOAI_CN"][$i]).'",
					"email" : "'.escapeJsonString($resDM["EMAIL"][$i]).'",
					"phai" : "'.escapeJsonString($resDM["PHAI"][$i]).'"
					
				  },';
				  
	}
	
	if ($n>0) 
		$data=substr($data,0,-1);
		
	$data.=']
			}';
			
	echo $data;
}

if (isset ($db_conn))
	oci_close($db_conn);
if (isset ($db_conn_khcn))
	oci_close($db_conn_khcn);
?>