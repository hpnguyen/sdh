<?php
if (isset($_REQUEST["hisid"])){
	session_id($_REQUEST["hisid"]);
	session_start();
}
if (!isset($_SESSION['uidloginPortal'])){
	die('{"success":"-2", "msg":"Đã hết thời gian phiên làm việc, vui lòng đăng nhập lại."}');
}

include "../libs/connect.php";
include "../libs/pgslibs.php";

if (!allowPermisstion(base64_decode($_SESSION['uidloginPortal']), '052', $db_conn)){
	die('{"success":"-3", "msg":"Không có quyền truy cập chức năng này"}');
}

$macb = $_SESSION['macb'];
$makhoa = base64_decode($_SESSION['makhoa']);
$a = $_REQUEST['a'];

if ($a=='checksession'){
	die('{"success":"1"}'); 
}
else if ($a=='thongke_capnhat_llkh_khoa') {
	$tu = str_replace ("'", "''", $_REQUEST["tu"]); // tu ngay
	$den = str_replace ("'", "''", $_REQUEST["den"]); // den ngay
	
	$sqlstr="select  k.ma_khoa, k.TEN_KHOA, count(*) SO_LUONG, (select count(*) from can_bo_giang_day cb, bo_mon bm where cb.ma_bo_mon = bm.ma_bo_mon and ma_khoa=k.ma_khoa and cb.fk_loai_can_bo in ('00','02')) tong, 
	GET_CT_THONG_KE_CAP_NHAT_LLKH(k.ma_khoa, '$tu', '$den') CHI_TIET 
	from nckh_logs n, bo_mon m, khoa k, can_bo_giang_day c
	where (n.time_strap between to_date('$tu','dd/mm/yyyy') and to_date('$den','dd/mm/yyyy')) and n.log_nckh is not null
	and n.fk_ma_can_bo = c.ma_can_bo and c.ma_bo_mon = m.ma_bo_mon and m.ma_khoa = k.ma_khoa
	and c.fk_loai_can_bo in ('00','02')
	group by  k.ma_khoa,k.ten_khoa
	order by k.ten_khoa";
	$stmt = oci_parse($db_conn, $sqlstr);oci_execute($stmt);$n = oci_fetch_all($stmt, $resDM);oci_free_statement($stmt);
	
	//file_put_contents("logs.txt", $sqlstr);
	
	$data='{
			"aaData":[';
	
	for ($i = 0; $i < $n; $i++){
		$data.= '["'.escapeJsonString($resDM["TEN_KHOA"][$i]).'",
				  "'.$resDM["SO_LUONG"][$i].'/'.$resDM["TONG"][$i].'", 
				  "<img src=\'icons/details_open.png\' border=0 class=detailsicon>",
				  "'.$resDM["CHI_TIET"][$i].'"
				 ],';
	}
	
	if ($n>0){
		$data=substr($data,0,-1);
	}
	$data.='	]
			}';
	
	echo $data;
}
else if ($a=='thongke_capnhat_llkh_chucdanh') {
	$tu = str_replace ("'", "''", $_REQUEST["tu"]); // tu ngay
	$den = str_replace ("'", "''", $_REQUEST["den"]); // den ngay
			
	$sqlstr="select dm.stt stt, c.MA_HOC_HAM, dm.TEN chucdanh, count(*) SO_LUONG, GET_CT_THONGKE_CAPNHAT_LLKH_HH(c.MA_HOC_HAM,'$tu', '$den') CHI_TIET,
				(select count(*) from can_bo_giang_day cb where cb.fk_loai_can_bo in ('00','02') and cb.MA_HOC_HAM = c.MA_HOC_HAM) tong
		  from nckh_logs n, can_bo_giang_day c, dm_hoc_ham dm
          where n.log_nckh is not null and c.MA_HOC_HAM in ('GS','PGS')
          and n.fk_ma_can_bo = c.ma_can_bo and c.MA_HOC_HAM = dm.MA_HOC_HAM
          and c.fk_loai_can_bo in ('00','02')
          and (n.time_strap between to_date('$tu','dd/mm/yyyy') and to_date('$den','dd/mm/yyyy'))
          group by dm.stt,c.MA_HOC_HAM, dm.TEN
      union all
		select dm.stt stt, c.MA_HOC_VI, dm.TEN chucdanh, count(*) SO_LUONG, GET_CT_THONGKE_CAPNHAT_LLKH_HV(c.MA_HOC_VI,'$tu', '$den') CHI_TIET,
			 (select count(*) from can_bo_giang_day cb
				where cb.fk_loai_can_bo in ('00','02')
				and (cb.MA_HOC_HAM not in ('GS','PGS') or cb.MA_HOC_HAM is null) and cb.MA_HOC_VI = c.MA_HOC_VI) tong
		from nckh_logs n, can_bo_giang_day c, dm_hoc_vi dm
        where n.log_nckh is not null and (c.MA_HOC_HAM not in ('GS','PGS') or c.MA_HOC_HAM is null)
        and n.fk_ma_can_bo = c.ma_can_bo and c.MA_HOC_VI = dm.MA_HOC_VI
        and c.fk_loai_can_bo in ('00','02')
        and (n.time_strap between to_date('$tu','dd/mm/yyyy') and to_date('$den','dd/mm/yyyy'))
        group by dm.stt, c.MA_HOC_VI, dm.ten
      order by stt";
	$stmt = oci_parse($db_conn, $sqlstr);oci_execute($stmt);$n = oci_fetch_all($stmt, $resDM);oci_free_statement($stmt);
	
	$data='{
			"aaData":[';
	
	for ($i = 0; $i < $n; $i++){
		$data.= '["'.escapeJsonString($resDM["CHUCDANH"][$i]).'",
				  "'.$resDM["SO_LUONG"][$i].'/'.$resDM["TONG"][$i].'", 
				  "<img src=\'icons/details_open.png\' border=0 class=detailsicon>",
				  "'.$resDM["CHI_TIET"][$i].'"
				 ],';
	}
	
	if ($n>0){
		$data=substr($data,0,-1);
	}
	$data.='	]
			}';
	
	echo $data;
}else if ($a=='thongke_capnhat_baibao_khoa') {
	$tu = str_replace ("'", "''", $_REQUEST["tu"]); // tu ngay
	$den = str_replace ("'", "''", $_REQUEST["den"]); // den ngay
	
	$sqlstr="select distinct k.ma_khoa, k.ten_khoa, sum(ct.diem_if) tong_diem, count(ct.ma_cong_trinh) SO_LUONG, 
	GET_CT_THONGKE_BAIBAO(k.ma_khoa,'$tu','$den') chi_tiet
	from cong_trinh_khoa_hoc ct, can_bo_giang_day c, bo_mon b, khoa k 
	where ct.ma_can_bo = c.ma_can_bo and c.ma_bo_mon = b.ma_bo_mon and b.ma_khoa = k.ma_khoa
	and (ct.nam_xuat_ban_tap_chi BETWEEN '$tu' and '$den') and ct.diem_if is not null 
	group by k.ma_khoa, k.ten_khoa
	order by ten_khoa
	";
	$stmt = oci_parse($db_conn, $sqlstr);oci_execute($stmt);$n = oci_fetch_all($stmt, $resDM);oci_free_statement($stmt);
	
	$data='{
			"aaData":[';
	
	for ($i = 0; $i < $n; $i++){
		$data.= '["'.escapeJsonString($resDM["TEN_KHOA"][$i]).'",
				  "'.$resDM["SO_LUONG"][$i].'", 
				  "'.$resDM["TONG_DIEM"][$i].'", 
				  "<img src=\'icons/details_open.png\' border=0 class=detailsicon>",
				  "'.escapeJsonString($resDM["CHI_TIET"][$i]).'"
				 ],';
	}
	
	if ($n>0){
		$data=substr($data,0,-1);
	}
	$data.='	]
			}';
	
	echo $data;
}

if (isset ($db_conn)){
	oci_close($db_conn);
}
?>