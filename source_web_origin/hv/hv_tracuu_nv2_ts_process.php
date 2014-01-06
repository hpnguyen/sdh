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
    oci_execute($oci_n);$row = oci_fetch_all($oci_n,$nam);oci_free_statement($oci_n);
	$nam_ts = $nam["NAM"][0];
	
	$oci_n = oci_parse($db_conn,"select value dot_ts from config where name = 'DOT_TUYEN_SINH_WEB'");
	oci_execute($oci_n);$row = oci_fetch_all($oci_n,$nam);oci_free_statement($oci_n);
	$dot_ts = $nam["DOT_TS"][0];
	
	if(strlen($sbd)<>0 or strlen($hoten)<>0)
	{
	 $Strdata = "
		select ts.so_bao_danh, ho ||' '||ten hoten, ten_nn || decode(ts.ma_nn, 'M', ' - ', '') ten_nn, ly_do_uu_tien,
				ten_loai ||' ' || DIEM_GOC ly_do_mien_nn, ten_nganh, ten_tinh_tp noi_sinh,
				NVL(to_char(ngay_sinh,'DD/MM/YYYY'), NGAY_SINH_KHONG_CHUAN) NGAY_SINH, dia_chi_lien_lac, dien_thoai_nr, email, DON_VI_CONG_TAC,
				ts.so_bao_danh, ts.nam, ma_bac, nvl(fk_doi_tuong_uu_tien, '!') uu_tien, DIEM_CHUAN_NGUYEN_VONG_2 diem_chuan, huy.ly_do_huy ly_do_huy_ket_qua_thi
		from thi_sinh_du_thi ts, nganh n, diem_chuan_tuyen_sinh dc, dm_tinh_tp ttp, mon_thi_ngoai_ngu nn,
			 dm_doi_tuong_uu_tien ut, dm_ly_do_mien_nn ld, ngoai_ngu_mien_tuyen_sinh mnn, huy_ket_qua_tuyen_sinh huy, thi_sinh_du_thi_nguyen_vong NV
		where noi_sinh = ma_tinh_tp(+) and fk_doi_tuong_uu_tien = ma_uu_tien(+) and ts.ma_nn = nn.ma_nn(+) and MA_NGANH_NGUYEN_VONG_2 = n.ma_nganh(+)
		and ts.so_bao_danh = nv.so_bao_danh and ts.nam = nv.nam
		and MA_NGANH_NGUYEN_VONG_2 = dc.ma_nganh and ts.nam = dc.nam(+) and ts.dot = dc.dot (+)
		and ts.ma_thi_sinh = mnn.ma_thi_sinh(+) and mnn.loai_mien = ld.ma_loai(+) and ts.ma_thi_sinh = huy.ma_thi_sinh(+)
		and ( (ngay_sinh_khong_chuan = '".$ngaysinh."')
				OR (to_char(ngay_sinh, 'ddmmyyyy') = substr('".$ngaysinh."',1,2)||substr('".$ngaysinh."',4,2)
				||substr('".$ngaysinh."',7,4))) and (ts.so_bao_danh ='$sbd'
				OR upper(ho||' '||ten) = upper('" . $hoten . "')
				OR upper(ho_eng||' '||ten_eng) = upper('" . $hoten . "')) and ts.nam =".$nam_ts . "
		order by ts.so_bao_danh desc
	 ";
	 
	 //echo $Strdata;
	 
	 $oci_pa = oci_parse($db_conn,$Strdata); 
	 oci_execute($oci_pa);
	 if(oci_fetch_all($oci_pa, $hocvien))
	 {
		echo "<div align=center style='margin:0 0 10px 5px; font-size:14px;'><b>KẾT QUẢ XÉT TUYỂN NGUYỆN VỌNG 2<br/>TUYỂN SINH NĂM $nam_ts ĐỢT $dot_ts</b></div>";
		
		echo "<table border='0' align=center cellspacing='0' cellpadding='3' class='ui-widget ui-widget-content ui-corner-top tableData'>";
		echo "<tr><td class='ui-widget-header'>Số báo danh</td> <td class='' style='font-weight:bold'>". $hocvien["SO_BAO_DANH"][0] ."</td></tr>";
		echo "<tr><td class='ui-widget-header'>Họ tên</td><td class=''>". $hocvien["HOTEN"][0] ."</td></tr>";
		echo "<tr><td class='ui-widget-header'>Ngày sinh</td><td class=''>". $hocvien["NGAY_SINH"][0] ."</td></tr>";
		echo "<tr><td class='ui-widget-header'>Nơi sinh</td><td class=''>". $hocvien["NOI_SINH"][0] ."</td></tr>";
		echo "<tr><td class='ui-widget-header'>Ngoại ngữ</td><td class=''><b><i>". $hocvien["TEN_NN"][0] . $hocvien["LY_DO_MIEN_NN"][0] . "</i></b></td></tr>";
		echo "<tr><td class='ui-widget-header'>Đối tượng ưu tiên</td><td class=''><i><b>". $hocvien["LY_DO_UU_TIEN"][0] ."</i></b></td></tr>";
		echo "<tr><td class='ui-widget-header'>Ngành đăng ký nguyện vọng 2</td><td class='' style='font-weight:bold'>". $hocvien["TEN_NGANH"][0] ."</td></tr>";
		echo "<tr><td class='ui-widget-header'>Địa chỉ</td><td class=''>". $hocvien["DIA_CHI_LIEN_LAC"][0] ."</td></tr>";
		echo "<tr><td class='ui-widget-header'>Điện thoại</td><td class=''>". $hocvien["DIEN_THOAI_NR"][0] ."</td></tr>";
		echo "<tr><td class='ui-widget-header'>Email</td><td class=''>". $hocvien["EMAIL"][0] ."</td></tr>";
		echo "<tr><td class='ui-widget-header'>Đơn vị công tác</td><td class=''>". $hocvien["DON_VI_CONG_TAC"][0] ."</td></tr>";
		oci_free_statement($oci_pa);
		
		$Strdiem = "select to_char(diem_cb, '990.99') diem_cb, to_char(diem_cs, '990.99') diem_cs, diem_nn, to_char(tong_diem_cb, '990.99') tong_diem_cb, to_char(tong_diem_nn, 990) tong_diem_nn, decode(tong_CB_CS, 200, ' ', to_char(tong_CB_CS, '990.99')) tong_CB_CS, diem_bvdc from diem_goi_to_thu_ky where so_bao_danh ='" . $hocvien["SO_BAO_DANH"][0] . "' and nam =".$nam_ts;
		$oci_pa_diem = oci_parse($db_conn,$Strdiem);
		oci_execute($oci_pa_diem);
		if(oci_fetch_all($oci_pa_diem, $results))
		{
			echo "<tr><td class='ui-widget-header'>Môn cơ bản</td><td class='' style='font-weight:bold'>"; 
			if ($results["DIEM_CB"][0] < 0)
				if ($results["DIEM_CB"][0] == -100)
					echo "Hủy kết quả thi";
				else
					echo "Vắng thi";
			else if ($results["DIEM_CB"][0]==200) 
				echo "Miễn thi";
			else 
			{
				//Lay diem phuc tra (neu co)
				if ($hocvien["UU_TIEN"][0] != "!")	//La doi tuong uu tien
					echo $results["DIEM_CB"][0] . " + <font color='red'><b>1</b> <i>(điểm ưu tiên)</i></font>";
				else
					echo $results["DIEM_CB"][0];
			}
			echo "</td></tr>";
			
			echo "<tr><td class='ui-widget-header'>Môn cơ sở</td><td class='' style='font-weight:bold'>"; 
			if ($results["DIEM_CS"][0] < 0)
				if ($results["DIEM_CS"][0] == -100)
					echo "Hủy kết quả thi";
				else
					echo "Vắng thi";
			else if ($results["DIEM_CS"][0]==200) 
				echo "Miễn thi";
			else echo $results["DIEM_CS"][0];
			echo "</td></tr>";
			
			echo "<tr><td class='ui-widget-header'>Môn ngoại ngữ</td><td class='' style='font-weight:bold'>";  
			if ($results["DIEM_NN"][0] < 0) 
				if ($results["DIEM_NN"][0] == -100) 
					echo "Hủy kết quả thi";
				else
					echo "Vắng thi";
			else if ($results["DIEM_NN"][0]==200) 
					echo "Miễn thi";
			else 
				if ($hocvien["UU_TIEN"][0] != "!")	//La doi tuong uu tien
					echo $results["DIEM_NN"][0]; /*. " + <font color='red'><b>10</b> <i>(điểm ưu tiên)</i></font>";*/
				else
					echo $results["DIEM_NN"][0];
			echo "</td></tr>";
				
			if ($hocvien["MA_BAC"][0] != 'TH' and $hocvien["MA_BAC"][0] != 'CTS')
			{
				echo "<tr><td class='ui-widget-header'>Điểm xét duyệt hồ sơ: </td><td class=''>";
				if ($results["DIEM_BVDC"][0] < 0)
					echo "Vắng thi</td></tr>";
				else
					echo $results["DIEM_BVDC"][0] . "</td></tr>";
				
				echo "<tr><td class='ui-widget-header'>Điều kiện trúng tuyển</td> <td class=''>- Điểm cơ bản >= 5.0<br>- Điểm cơ sở >= 5.0<br>- Điểm ngoại ngữ >= 50<br>- Điểm xét duyệt hồ sơ >= 5.0<br></td></tr>";
				echo "<tr><td class='ui-widget-header'>Kết quả xét tuyển</td><td class='' style='font-weight:bold'>";  
				
				if ($hocvien["DIEM_CHUAN"][0] != '')
					if ((($results["TONG_DIEM_NN"][0]>=50) and ($results["TONG_DIEM_CB"][0]>=5) and ($results["DIEM_CS"][0]>=5) and ($results["DIEM_BVDC"][0]>=5)
					   and ($results["TONG_CB_CS"][0] >=$hocvien["DIEM_CHUAN"][0])) or ($results["DIEM_CS"][0] == 200 and $results["TONG_DIEM_NN"][0]>=50 and $results["DIEM_BVDC"][0]>=5))
						echo "Trúng tuyển";
					else
					{
						if ($hocvien["LY_DO_HUY_KET_QUA_THI"][0]=="")
							echo "Không trúng tuyển";
						else
							echo "Hủy kết quả - lý do: {$hocvien["LY_DO_HUY_KET_QUA_THI"][0]}";
					}
				else
					echo " Chưa có điểm chuẩn";
					
				echo "</td></tr>";
			}
			else
			{
				if ($results["TONG_DIEM_CB"][0]+$results["DIEM_CS"][0] >= 0)
					echo "<tr><td class='ui-widget-header'>Tổng điểm xét tuyển (CB+CS)</td><td class='' style='font-weight:bold'>" . $results["TONG_CB_CS"][0] . "</td></tr>";
				else
					echo "<tr><td class='ui-widget-header'>Tổng điểm xét tuyển (CB+CS)</td><td class='' style='font-weight:bold'>" . 0 . "</td></tr>";
				
				echo "<tr><td class='ui-widget-header'>Điều kiện trúng tuyển</td>
					<td class=''>- Điểm cơ bản >= 5.0<br>- Điểm cơ sở >= 5.0 <br>- Điểm ngoại ngữ >= 50 <br>
					- Tổng điểm CB+CS >= " . $hocvien["DIEM_CHUAN"][0];  
				echo "</td></tr>";
				echo "<tr><td class='ui-widget-header'>Kết quả xét tuyển</td><td class='' style='font-weight:bold'>";  
				//if ($hocvien["SO_BAO_DANH"][0] < '6000') //Khong xet ket qua nuoc ngoai
					if ($hocvien["DIEM_CHUAN"][0] != '')
						if ((($results["TONG_DIEM_NN"][0]>=50) and ($results["TONG_DIEM_CB"][0]>=5) and ($results["DIEM_CS"][0]>=5) and ($results["TONG_CB_CS"][0] >=$hocvien["DIEM_CHUAN"][0])) or ($results["DIEM_CS"][0] == 200 and $results["TONG_DIEM_NN"][0]>=50))
							echo "Trúng tuyển</td></tr>";
						else
						{
							//echo $hocvien["LY_DO_HUY_KET_QUA_THI"][0];
							if ($hocvien["LY_DO_HUY_KET_QUA_THI"][0]=="")
								echo "Không trúng tuyển</td></tr>";
							else
								echo "Hủy kết quả - lý do: {$hocvien["LY_DO_HUY_KET_QUA_THI"][0]}";
						}
					else
						echo " Chưa có điểm chuẩn</td></tr>";
			}
		}
		echo "</table>";

	}
	else
	{
		echo "<div style='font-size:12px; font-weight:bold;'>Không tìm thấy thí sinh dự thi</div>";
	}
		oci_free_statement($oci_pa_diem);
	}
	
}
?>

<?php 
if (isset ($db_conn))
	oci_close($db_conn);
?>