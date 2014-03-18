<?php
if (isset($_REQUEST["hisid"])){
	session_id($_REQUEST["hisid"]);
	session_start();
}
if (!isset($_SESSION['uidloginhv'])){
	die('Truy cập bất hợp pháp'); 
}

include "libs/connect.php";
include "libs/pgslibshv.php";

$searchdb = array("\\\\","\\'",'\\"', "'");
$replacedb = array("\\","'", '"', "''");

$usrlogin = base64_decode($_SESSION["uidloginhv"]);
$mahv = $usrlogin;

$email=str_replace($searchdb, $replacedb, trim($_POST["hv_info_email"]));
$diachi=str_replace($searchdb, $replacedb, trim($_POST["hv_info_diachi"]));
$dienthoai=str_replace($searchdb, $replacedb, trim($_POST["hv_info_dienthoai"]));
$donvi=str_replace($searchdb, $replacedb, trim($_POST["hv_info_donvicongtac"]));
$usr=str_replace($searchdb, $replacedb, trim($_POST["hv_info_usrname"]));
$pass=str_replace($searchdb, $replacedb, trim($_POST["hv_info_pass"]));

$cmnd=str_replace($searchdb, $replacedb,trim($_POST["hv_info_so_cmnd"]));
$ngaycap=str_replace($searchdb, $replacedb,trim($_POST["hv_info_ngaycap_cmnd"]));
$noicap=str_replace($searchdb, $replacedb,trim($_POST["hv_info_noicap_cmnd"]));
$sotk=str_replace($searchdb, $replacedb,trim($_POST["hv_info_so_tk"]));

$ngaysinh=str_replace($searchdb, $replacedb, trim($_POST["hv_info_ngaysinh"]));
$noisinh=str_replace($searchdb, $replacedb, trim($_POST["hv_info_noisinh"]));
$dan_toc=str_replace($searchdb, $replacedb, trim($_POST["hv_info_dan_toc"]));
$ton_giao=str_replace($searchdb, $replacedb, trim($_POST["hv_info_ton_giao"]));
$dia_chi_thuong_tru=str_replace($searchdb, $replacedb, trim($_POST["hv_info_dia_chi_thuong_tru"]));
$nghenghiep=str_replace($searchdb, $replacedb, trim($_POST["hv_info_nghenghiep"]));
$ngayvaodoan=str_replace($searchdb, $replacedb,trim($_POST["hv_info_ngayvaodoan"]));
$ngayvaodang=str_replace($searchdb, $replacedb,trim($_POST["hv_info_ngayvaodang"]));
$doituonguutien=str_replace($searchdb, $replacedb,trim($_POST["hv_info_doituonguutien"]));
$truongdaihoc=str_replace($searchdb, $replacedb,trim($_POST["hv_info_truongdaihoc"]));
$nganhdaihoc=str_replace($searchdb, $replacedb, trim($_POST["hv_info_nganhdaihoc"]));
$hedaotao=str_replace($searchdb, $replacedb,trim($_POST["hv_info_hedaotao"]));
$nhaphocdaihoc=str_replace($searchdb, $replacedb,trim($_POST["hv_info_nhaphocdaihoc"]));
$totnghiepdaihoc=str_replace($searchdb, $replacedb,trim($_POST["hv_info_totnghiepdaihoc"]));
$loaitndaihoc=str_replace($searchdb, $replacedb,trim($_POST["hv_info_loaitndaihoc"]));

$khkt_tu=str_replace($searchdb, $replacedb, trim($_POST["hv_info_khkt_tu"]));
$khkt_den=str_replace($searchdb, $replacedb,trim($_POST["hv_info_khkt_den"]));
$khkt_truong=str_replace($searchdb, $replacedb,trim($_POST["hv_info_khkt_truong"]));
$khkt_nd=str_replace($searchdb, $replacedb,trim($_POST["hv_info_khkt_nd"]));
$caohoc_tu=str_replace($searchdb, $replacedb,trim($_POST["hv_info_caohoc_tu"]));
$caohoc_den=str_replace($searchdb, $replacedb, trim($_POST["hv_info_caohoc_den"]));
$cn_caohoc=str_replace($searchdb, $replacedb,trim($_POST["hv_info_cn_caohoc"]));
$truongcaohoc=str_replace($searchdb, $replacedb,trim($_POST["hv_info_truongcaohoc"]));
$caohoc_ngaybaove=str_replace($searchdb, $replacedb,trim($_POST["hv_info_caohoc_ngaybaove"]));
$caohoc_noibaove=str_replace($searchdb, $replacedb,trim($_POST["hv_info_caohoc_noibaove"]));

$giaithuong=str_replace($searchdb, $replacedb,trim($_POST["hv_info_giaithuong"]));
$hoinghi=str_replace($searchdb, $replacedb,trim($_POST["hv_info_hoinghi"]));
$detai=str_replace($searchdb, $replacedb,trim($_POST["hv_info_detai"]));
$bbkh=str_replace($searchdb, $replacedb,trim($_POST["hv_info_bbkh"]));
	
$kn_cmon_nvong=str_replace($searchdb, $replacedb,trim($_POST["hv_info_cmon_nvong"]));

$a = $_POST["a"];

if ($a=="getquatrinhhoclam"){
	
	$sqlstr="SELECT * FROM QT_HOC_LAM_VIEC_HV WHERE FK_MA_HOC_VIEN='$mahv'";
	$stmt = oci_parse($db_conn, $sqlstr);oci_execute($stmt);$n = oci_fetch_all($stmt, $resDM);oci_free_statement($stmt);
	$data = '{"success":"1",
			  "quatrinhhoclam":[
	';
	for ($i=0; $i<$n; $i++){
		$data.= '{	"maqt":"'.escapeJsonString($resDM["MA_QT_HOC_LAM_VIEC"][$i]).'",
					"tungay":"'.escapeJsonString($resDM["TU_NGAY"][$i]).'",
					"denngay":"'.escapeJsonString($resDM["DEN_NGAY"][$i]).'",
					"hoclam":"'.escapeJsonString($resDM["HOC_LAM_VIEC"][$i]).'",
					"odau":"'.escapeJsonString($resDM["DIA_DIEM"][$i]).'",
					"thanhtich":"'.escapeJsonString($resDM["THANH_TICH"][$i]).'"
				},';
	}
	if ($n) $data=substr($data,0,-1);
	$data.= ']
			}';
	die($data);
}

if ($a=='addquatrinhhoclam'){
	$tungay = str_replace($searchdb, $replacedb, $_POST["hv_info_frm_reg_ht_lv_tu_ngay"]);
	$denngay = str_replace($searchdb, $replacedb, $_POST["hv_info_frm_reg_ht_lv_den_ngay"]);
	$hoclam = str_replace($searchdb, $replacedb, $_POST["hv_info_frm_reg_ht_lv_hoclam"]);
	$odau = str_replace($searchdb, $replacedb, $_POST["hv_info_frm_reg_ht_lv_odau"]);
	$thanhtich = str_replace($searchdb, $replacedb, $_POST["hv_info_frm_reg_ht_lv_thanhtich"]);
	$maqt = "";
	
	$sqlstr="select get_ma_qt_hoc_lam_viec('$mahv') maqt from dual"; 
	$stmt = oci_parse($db_conn, $sqlstr);
	if (oci_execute($stmt)){
		oci_fetch_all($stmt, $resDM); oci_free_statement($stmt);
		$maqt = $resDM["MAQT"][0];

		if ($mahv!='' && $maqt!=''){
			$sqlstr="insert into QT_HOC_LAM_VIEC_HV(FK_MA_HOC_VIEN,MA_QT_HOC_LAM_VIEC,TU_NGAY,DEN_NGAY,HOC_LAM_VIEC,DIA_DIEM,THANH_TICH) 
			values ('$mahv','$maqt','$tungay','$denngay','$hoclam','$odau','$thanhtich')"; 
			
			//file_put_contents("logs.txt", "$sqlstr");
			
			$stmt = oci_parse($db_conn, $sqlstr);
			if (!oci_execute($stmt)){
				$e = oci_error($stmt);
				$msgerr = $e['message']. " sql: " . $e['sqltext'];
				die ('{"success":"-1", "msgerr":"'.escapeJsonString($msgerr).'"}');
			}
			
			die('{"success":"1", "maqt":"'.escapeJsonString($maqt).'", "tungay":"'.escapeJsonString($tungay).'", 
					"denngay":"'.escapeJsonString($denngay).'", "hoclam":"'.escapeJsonString($hoclam).'",
					"odau":"'.escapeJsonString($odau).'","thanhtich":"'.escapeJsonString($thanhtich).'"}');
		}
	}else{
		$e = oci_error($stmt);
		$msgerr = $e['message']. " sql: " . $e['sqltext'];
		die ('{"success":"-1", "msgerr":"'.escapeJsonString($msgerr).'"}');
	}
}

if ($a=='removequatrinhhoclam'){
	$maqt = str_replace($searchdb, $replacedb, $_POST["maqt"]);
	$sqlstr="delete QT_HOC_LAM_VIEC_HV where FK_MA_HOC_VIEN='$mahv' and MA_QT_HOC_LAM_VIEC='$maqt'"; 
	$stmt = oci_parse($db_conn, $sqlstr);
	if (oci_execute($stmt)){
		die('{"success":"1"}');
	}
	else{
		$e = oci_error($stmt);
		$msgerr = $e['message']. " sql: " . $e['sqltext'];
		die ('{"success":"-1", "msgerr":"'.escapeJsonString($msgerr).'"}');
	}
}

if ($a=="savehvinfo"){

	/* $strsql="SELECT username 
	FROM nguoi_dung 
	WHERE upper(username)=upper('".str_replace($searchdb, $replacedb,$usr)."') and pass='".str_replace($searchdb, $replacedb,$pass)."'";
	
	$oci_pa = oci_parse($db_conn, $strsql); oci_execute($oci_pa); $n=oci_fetch_all($oci_pa, $kt); */
	
	// if ($n>0)
	// {
		$strsql="update hoc_vien set email ='$email',
		dia_chi='$diachi', dien_thoai='$dienthoai',	don_vi_cong_tac='$donvi', so_cmnd = '$cmnd', 
		ngay_cap=to_date('$ngaycap', 'dd/mm/yyyy'), noi_cap='$noicap', so_tai_khoan = '$sotk',
		
		ngay_sinh=to_date('$ngaysinh', 'dd/mm/yyyy'), noi_sinh='$noisinh', fk_dan_toc = '$dan_toc',
		fk_ton_giao='$ton_giao', dia_chi_thuong_tru='$dia_chi_thuong_tru', nghe_nghiep='$nghenghiep',
		ngay_vao_doan=to_date('$ngayvaodoan', 'dd/mm/yyyy'), ngay_vao_dang=to_date('$ngayvaodang', 'dd/mm/yyyy'),
		fk_doi_tuong_uu_tien='$doituonguutien', truong_dai_hoc='$truongdaihoc', fk_nganh_dai_hoc='$nganhdaihoc',
		he_dao_tao_dh='$hedaotao', thoi_diem_nhap_hoc_dai_hoc='$nhaphocdaihoc', thoi_diem_tot_nghiep_dai_hoc='$totnghiepdaihoc',
		fk_loai_tot_nghiep_dai_hoc = '$loaitndaihoc',
		
		thuc_tap_khkt_tu_ngay = to_date('$khkt_tu', 'dd/mm/yyyy'),thuc_tap_khkt_den_ngay = to_date('$khkt_den', 'dd/mm/yyyy'),
		thuc_tap_khkt_truong='$khkt_truong', thuc_tap_khkt_noi_dung='$khkt_nd', 
		thoi_diem_nhap_hoc_cao_hoc='$caohoc_tu', thoi_diem_tot_nghiep_cao_hoc='$caohoc_den',
		truong_cao_hoc='$truongcaohoc', ma_nganh_cao_hoc='$cn_caohoc', 
		ngay_bao_ve_lvths = to_date('$caohoc_ngaybaove', 'dd/mm/yyyy'), noi_bao_ve_lvths='$caohoc_noibaove',
		
		kn_cmon_nvong='$kn_cmon_nvong'
		
		where upper(ma_hoc_vien)=upper('$usrlogin')";
		$oci_pa = oci_parse($db_conn,$strsql);
		
		//file_put_contents("logs.txt", "$strsql");
		
		if (!oci_execute($oci_pa)){
			$e = oci_error($oci_pa);
			$msgerr = $e['message']. " sql: " . $e['sqltext'];
			die ('{"success":"-1", "msg":"'.escapeWEB($msgerr).'"}');
		}else{
		
			$strsql="update nguoi_dung set email ='$email' where upper(USERNAME)=upper('$usrlogin')";
			$oci_pa = oci_parse($db_conn,$strsql);
			
			if (!oci_execute($oci_pa)){
				$e = oci_error($oci_pa);
				$msgerr = $e['message']. " sql: " . $e['sqltext'];
				die ('{"success":"-1", "msg":"'.escapeWEB($msgerr).'"}');
			}
			
			$strsql="select count(*) dem from QT_HOAT_DONG_KHKT where upper(fk_ma_hoc_vien)=upper('$usrlogin')";
			$stmt = oci_parse($db_conn, $strsql); 	
			if (oci_execute($stmt)){
				$n=oci_fetch_all($stmt, $qthoatdong);
				if ($qthoatdong["DEM"][0]==1){
					// Thuc hien update
					$strsql="update QT_HOAT_DONG_KHKT set BAI_BAO='$bbkh', DE_TAI_NCKH='$detai', GIAI_THUONG_KHCN='$giaithuong', 
					THAM_GIA_HOI_NGHI='$hoinghi' where FK_MA_HOC_VIEN = '$usrlogin'";
					$stmt = oci_parse($db_conn, $strsql);
					if (!oci_execute($stmt)){
						$e = oci_error($stmt);
						$msgerr = $e['message']. " sql: " . $e['sqltext'];
						die ('{"success":"-1", "msg":"'.escapeWEB($msgerr).'"}');
					}
				}else{
					// Thuc hien insert
					$strsql="insert into QT_HOAT_DONG_KHKT(FK_MA_HOC_VIEN, BAI_BAO, DE_TAI_NCKH, GIAI_THUONG_KHCN, THAM_GIA_HOI_NGHI) 
					values('$usrlogin', '$bbkh','$detai','$giaithuong','$hoinghi')";
					$stmt = oci_parse($db_conn, $strsql);oci_execute($stmt);
					if (!oci_execute($stmt)){
						$e = oci_error($stmt);
						$msgerr = $e['message']. " sql: " . $e['sqltext'];
						die ('{"success":"-1", "msg":"'.escapeWEB($msgerr).'"}');
					}
				}
			}
			
			oci_free_statement($stmt);
			echo  '{"success":"1", "msg":"Đã thay đổi thông tin thành công"}';
		}
	// }
	// else{
		// echo '{"msg":"Người Dùng và Mật Khẩu không chính xác"}';
	// }
	oci_free_statement($oci_pa);
}
?>

<?php 
if (isset ($db_conn))
	oci_close($db_conn);
?>