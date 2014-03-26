<?php
ini_set('display_errors', '1');

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

if (!allowPermisstion(base64_decode($_SESSION['uidloginPortal']), '056', $db_conn)){
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

if ($a=='checksession'){
	die('{"success":"1"}'); 
}

if ($a=='ThungRac'){
	$madt = str_replace("'", "''", $_POST["m"]);
	$today =date("Y-m-d H:i");
	$c = str_replace("'", "''", $_POST["c"]);
	($c == '') ? $c = '1' : $c = 'null';
	
	if ($madt!=''){
		
		$sqlstr="update NCKH_THUYET_MINH_DE_TAI set THUNG_RAC = $c
		where MA_THUYET_MINH_DT = '$madt'";
		$stmt = oci_parse($db_conn_khcn, $sqlstr);
		if (oci_execute($stmt)){
			echo '{"success":"1", "time":"'.$today.'", "ma":"'.$madt.'"}';
		}else{
			echo '{"success":"-1", "ma":"'.$madt.'"}';
		}
	}else{
		echo '{"success":"-1", "ma":"'.$madt.'"}';
	}
	
}


if ($a=='refreshdata'){
	$fttr = str_replace ("'", "''", $_REQUEST["fttr"]); // filter thung rac
	$fcndt = str_replace ("'", "''", $_REQUEST["fcndt"]); // filter chu nhiem de tai
	$fdcndt = str_replace ("'", "''", $_REQUEST["fdcndt"]); // filter dong chu nhiem
	$fcdt = str_replace ("'", "''", $_REQUEST["fcdt"]); // filter cap de tai
	$fdv = str_replace ("'", "''", $_REQUEST["fdv"]); // filter don vi
	$fnnhan = str_replace ("'", "''", $_REQUEST["fnnhan"]); // filter nam nhan dang ky
	$ftrangthai = str_replace ("'", "''", $_REQUEST["ftrangthai"]); // filter trang thai
	$fnpbien = str_replace ("'", "''", $_REQUEST["fnpbien"]); // filter fnpbien
	
	$filterstr = " AND tm.THUNG_RAC is null";
	
	if ($fcndt != ""){
		$filterstr .= " AND tm.FK_MA_CAN_BO = '$fcndt'"; // Nguoi so huu de tai la Chu nhiem de tai
	}
	if ($fdcndt != ""){
		$filterstr .= " AND tm.DCNDT_HH_HV_HO_TEN = '$fdcndt'";
	}
	if ($fcdt != ""){
		$filterstr .= " AND FK_CAP_DE_TAI='$fcdt'";
	}
	if ($fdv != ""){
		$filterstr .= " AND b.ma_khoa='$fdv'";
	}
	if ($fnnhan != ""){
		$filterstr .= " AND to_char(tm.NGAY_DANG_KY, 'yyyy') = '$fnnhan'";
		/* $fnnhan1=($fnnhan-1);
		$filterstr .= " AND (tm.NGAY_DANG_KY between to_date('01/10/$fnnhan1', 'dd/mm/yyyy') and to_date('31/12/$fnnhan', 'dd/mm/yyyy'))"; */
	}
	if ($ftrangthai != ""){
		$filterstr .= " AND tm.FK_TINH_TRANG = '$ftrangthai'";		
	}
	if ($fnpbien  != ""){
		$filterstr .= " AND pb.FK_MA_CAN_BO = '$fnpbien'";		
	}
	

	$sqlstr="	SELECT tm.MA_THUYET_MINH_DT, TEN_DE_TAI_VN, cdt.ten_cap, lhnc.TEN_LOAI_HINH_NC, THOI_GIAN_THUC_HIEN, FK_CAP_DE_TAI,
				keywords, huong_de_tai, CNDT_HH_HV_HO_TEN, DCNDT_HH_HV_HO_TEN,
				c.email, c.dien_thoai, b.ma_khoa, k.ten_khoa don_vi, nvl(tm.FK_TINH_TRANG,'01') FK_TINH_TRANG, tt.TEN_TINH_TRANG,
				c.MA_CAN_BO, c.SHCC, TONG_KINH_PHI, KINH_PHI_TU_DHQG, KINH_PHI_HUY_DONG,
				MUC_TIEU_NC_VN,NOI_DUNG_NC,
				get_can_bo_tg(tm.MA_THUYET_MINH_DT) CB_THAM_GIA,
				get_nganh_nhom_nganh(tm.MA_THUYET_MINH_DT) NGANH_NHOMNGANH,
				get_an_pham_kh(tm.MA_THUYET_MINH_DT) AN_PHAM_KH,
				get_dk_shtt(tm.MA_THUYET_MINH_DT) DK_SHTT,
				get_sp_mem_cung(tm.MA_THUYET_MINH_DT) SP_MEM_CUNG,
				get_gt_chuyen_gia(tm.MA_THUYET_MINH_DT) GT_CHUYEN_GIA,
				get_dao_tao(tm.MA_THUYET_MINH_DT) DAO_TAO, 
				pb.FK_MA_CAN_BO MA_PHAN_BIEN,
				csdl.GET_THANH_VIEN_SHCC(pb.FK_MA_CAN_BO) NGUOI_PHAN_BIEN, pb.A1_TAM_QUAN_TRONG, pb.A2_CHAT_LUONG_NC, pb.A3_NLNC_CSVC, 
				pb.A4_KINH_PHI_NX, pb.C_KET_LUAN, GET_NCKH_PB_DTB_DANH_GIA(tm.ma_thuyet_minh_dt) dtb,
				GET_NCKH_PB_SUM_DANH_GIA_CB_DT(pb.FK_MA_CAN_BO, tm.MA_THUYET_MINH_DT) diem_danh_gia
				FROM NCKH_THUYET_MINH_DE_TAI tm, CAP_DE_TAI cdt, NCKH_LOAI_HINH_NC lhnc, can_bo_giang_day c, bo_mon b, khoa k, 
					 NCKH_DM_TINH_TRANG tt, NCKH_PB_NOI_DUNG pb
				WHERE FK_CAP_DE_TAI = cdt.ma_cap(+) and FK_LOAI_HINH_NC = lhnc.MA_LOAI_HINH_NC(+) 
				and tm.fk_ma_can_bo = c.ma_can_bo and b.ma_bo_mon=c.ma_bo_mon and b.ma_khoa=k.ma_khoa 
				and nvl(tm.FK_TINH_TRANG,'01') = tt.MA_TINH_TRANG (+)
				and tm.MA_THUYET_MINH_DT = pb.MA_THUYET_MINH_DT
				$filterstr
				";
				
	//file_put_contents("logs.txt", " $sqlstr");
	
	$stmt = oci_parse($db_conn_khcn, $sqlstr);oci_execute($stmt);$n = oci_fetch_all($stmt, $resDM);oci_free_statement($stmt);
	$data='{
			"aaData":[';
	
	for ($i = 0; $i < $n; $i++){
		$SendTMDT = '"<img src=\'icons/Send-Document-icon.png\' class=khcn_tooltips title=\'Cập nhật trạng thái TMĐT\' border=0 onClick=\'khcn_ql_trangthai_tmdt( khcn_ql_getRowIndex(this),\"'.$resDM["FK_CAP_DE_TAI"][$i].'\"); \' style=\'cursor: pointer\'>"';
		$data.= '["'.$resDM["MA_THUYET_MINH_DT"][$i].'",
				  "'.escapeJsonString($resDM["TEN_DE_TAI_VN"][$i]).'", 
				  "'.escapeJsonString($resDM["CNDT_HH_HV_HO_TEN"][$i]).'", 
				  "'.escapeJsonString($resDM["DCNDT_HH_HV_HO_TEN"][$i]).'", 
				  "'.escapeJsonString($resDM["CB_THAM_GIA"][$i]).'", 
				  "'.escapeJsonString('<b>'.$resDM["TEN_CAP"][$i].'<b>').'",
				  "'.escapeJsonString($resDM["DON_VI"][$i]).'", 
				  "'.escapeJsonString($resDM["TONG_KINH_PHI"][$i]).'",
				  "'.escapeJsonString($resDM["KINH_PHI_TU_DHQG"][$i]).'",
				  "'.escapeJsonString($resDM["KINH_PHI_HUY_DONG"][$i]).'",
				  "'.escapeJsonString($resDM["NGANH_NHOMNGANH"][$i]).'", 
				  "'.escapeJsonString($resDM["THOI_GIAN_THUC_HIEN"][$i]).'", 
				  "'.escapeJsonString(escapeExcel($resDM["NGUOI_PHAN_BIEN"][$i])).'", 

				  "<img src=\'icons/print-preview-icon24x24.png\' class=khcn_tooltips title=\'Xem bản in TMĐT\' border=0 onClick=\'khcn_ql_phanbien_view_phanbien( \"'.$resDM["MA_THUYET_MINH_DT"][$i].'\", \"'.$resDM["MA_PHAN_BIEN"][$i].'\",\"'.$resDM["FK_CAP_DE_TAI"][$i].'\"); \' style=\'cursor: pointer\'>",
				  
				  "'.escapeJsonString(escapeExcel(htmlspecialchars_decode($resDM["A1_TAM_QUAN_TRONG"][$i]))).'",
				  "'.escapeJsonString(escapeExcel(htmlspecialchars_decode($resDM["A2_CHAT_LUONG_NC"][$i]))).'",
				  "'.escapeJsonString(escapeExcel(htmlspecialchars_decode($resDM["A3_NLNC_CSVC"][$i]))).'",
				  "'.escapeJsonString(escapeExcel(htmlspecialchars_decode($resDM["A4_KINH_PHI_NX"][$i]))).'",
				  "'.escapeJsonString(escapeExcel($resDM["DIEM_DANH_GIA"][$i])).'",
				  "'.escapeJsonString(escapeExcel(htmlspecialchars_decode($resDM["C_KET_LUAN"][$i]))).'",
				  "'.escapeJsonString($resDM["DTB"][$i]).'"
				 ],';
	}
	// data 25 la item cuoi cung
	
	if ($n>0) 
		$data=substr($data,0,-1);
	
	$data.='	]
			}';
	
	echo $data;
}


if (isset ($db_conn))
	oci_close($db_conn);
if (isset ($db_conn_khcn))
	oci_close($db_conn_khcn);
?>