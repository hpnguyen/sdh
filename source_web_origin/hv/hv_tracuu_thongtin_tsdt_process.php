<?php
//if (!isset($_SESSION['uidloginhv'])){
//	die('Truy cập bất hợp pháp'); 
//}

include "libs/connect.php";
include "libs/pgslibshv.php";

?>

<?php

$type = escape($_POST['w']);
$sbd = escape($_POST['s']);
$hoten = escape($_POST['h']);
$ngaysinh = escape($_POST['n']);

if ($type=='sdb_hoten_ngaysinh-diemts')
{
	
	$oci_n = oci_parse($db_conn,"select value nam from config where name = 'KHOA_TUYEN_SINH_WEB'");
    oci_execute($oci_n); $row = oci_fetch_all($oci_n,$nam);	oci_free_statement($oci_n);
	$nam_ts = $nam["NAM"][0];
	
	$oci_n = oci_parse($db_conn,"select value dot_ts from config where name = 'DOT_TUYEN_SINH_WEB'");
	oci_execute($oci_n);$row = oci_fetch_all($oci_n,$nam);oci_free_statement($oci_n);
	$dot_ts = $nam["DOT_TS"][0];

	if(strlen($sbd)<>0 or strlen($hoten)<>0)
	{
	 $Strdata = "
		select so_bao_danh, ho ||' '||ten hoten, ten_nn || decode(ts.ma_nn, 'M', ' - ', '') ten_nn, ly_do_uu_tien,
				ten_loai ||' ' || DIEM_GOC ly_do_mien_nn, ten_nganh, ten_tinh_tp noi_sinh,
				NVL(to_char(ngay_sinh,'DD/MM/YYYY'), NGAY_SINH_KHONG_CHUAN) NGAY_SINH, dia_chi_lien_lac, dien_thoai_nr, email, DON_VI_CONG_TAC,
				so_bao_danh, ts.nam, ma_bac, nvl(fk_doi_tuong_uu_tien, '!') uu_tien, diem_chuan, huy.ly_do_huy ly_do_huy_ket_qua_thi
		from thi_sinh_du_thi ts, nganh n, diem_chuan_tuyen_sinh dc, dm_tinh_tp ttp, mon_thi_ngoai_ngu nn,
			 dm_doi_tuong_uu_tien ut, dm_ly_do_mien_nn ld, ngoai_ngu_mien_tuyen_sinh mnn, huy_ket_qua_tuyen_sinh huy
		where noi_sinh = ma_tinh_tp(+) and fk_doi_tuong_uu_tien = ma_uu_tien(+) and ts.ma_nn = nn.ma_nn(+) and ts.ma_chuyen_nganh = n.ma_nganh(+)
		and ma_chuyen_nganh = dc.ma_nganh(+) 
		and ts.nam = dc.nam(+) 
		and ts.dot = dc.dot (+)
		and ts.ma_thi_sinh = mnn.ma_thi_sinh(+) and mnn.loai_mien = ld.ma_loai(+) and ts.ma_thi_sinh = huy.ma_thi_sinh(+)
		and ( (ngay_sinh_khong_chuan = '".$ngaysinh."')
				OR (to_char(ngay_sinh, 'ddmmyyyy') = substr('".$ngaysinh."',1,2)||substr('".$ngaysinh."',4,2)
				||substr('".$ngaysinh."',7,4))) and (so_bao_danh ='$sbd'
				OR upper(ho||' '||ten) = upper('" . $hoten . "')
				OR upper(ho_eng||' '||ten_eng) = upper('" . $hoten . "')) and ts.nam =".$nam_ts . "
		order by ts.so_bao_danh desc
	 ";
	 
	 //echo $Strdata;
	 
	 $oci_pa = oci_parse($db_conn,$Strdata); 
	 oci_execute($oci_pa);
	 if(oci_fetch_all($oci_pa, $hocvien))
	 {
		echo "<div align=center style='margin:0 0 10px 5px; font-size:14px;'><b>THÔNG TIN THÍ SINH DỰ THI NĂM $nam_ts ĐỢT $dot_ts</b></div>";
		
		echo "<table border='0' align=center cellspacing='0' cellpadding='3' class='ui-widget ui-widget-content ui-corner-top tableData'>";
		echo "<tr><td class='ui-widget-header'>Số báo danh</td> <td class='' style='font-weight:bold'>". $hocvien["SO_BAO_DANH"][0] ."</td></tr>";
		echo "<tr><td class='ui-widget-header'>Họ tên</td><td class=''>". $hocvien["HOTEN"][0] ."</td></tr>";
		echo "<tr><td class='ui-widget-header'>Ngày sinh</td><td class=''>". $hocvien["NGAY_SINH"][0] ."</td></tr>";
		echo "<tr><td class='ui-widget-header'>Nơi sinh</td><td class=''>". $hocvien["NOI_SINH"][0] ."</td></tr>";
		echo "<tr><td class='ui-widget-header'>Ngoại ngữ</td><td class=''><b><i>". $hocvien["TEN_NN"][0] . $hocvien["LY_DO_MIEN_NN"][0] . "</i></b></td></tr>";
		echo "<tr><td class='ui-widget-header'>Đối tượng ưu tiên</td><td class=''><i><b>". $hocvien["LY_DO_UU_TIEN"][0] ."</i></b></td></tr>";
		echo "<tr><td class='ui-widget-header'>Chuyên ngành đăng ký dự thi</td><td class='' style='font-weight:bold'>". $hocvien["TEN_NGANH"][0] ."</td></tr>";
		echo "<tr><td class='ui-widget-header'>Địa chỉ</td><td class=''>". $hocvien["DIA_CHI_LIEN_LAC"][0] ."</td></tr>";
		echo "<tr><td class='ui-widget-header'>Điện thoại</td><td class=''>". $hocvien["DIEN_THOAI_NR"][0] ."</td></tr>";
		echo "<tr><td class='ui-widget-header'>Email</td><td class=''>". $hocvien["EMAIL"][0] ."</td></tr>";
		echo "<tr><td class='ui-widget-header'>Đơn vị công tác</td><td class=''>". $hocvien["DON_VI_CONG_TAC"][0] ."</td></tr>";
		oci_free_statement($oci_pa);
	
		echo "</table>";
	}
	else
	{
		echo "<div style='font-size:12px; font-weight:bold; color: red;'>Không tìm thấy thí sinh dự thi</div>";
	}
		oci_free_statement($oci_pa_diem);
	}
	
}
?>

<?php 
if (isset ($db_conn))
	oci_close($db_conn);
?>