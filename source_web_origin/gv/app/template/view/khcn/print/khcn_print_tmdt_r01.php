<?php
$helper = Helper::getHelper('functions/util'); 
$gvUrl = $helper->getGvRootURL();

date_default_timezone_set('Asia/Ho_Chi_Minh');
$ngay =date("d");
$thang =date("m");
$nam =date("Y");
$z = 1;
$y = 1;
?>
<style type="text/css">
	.fontcontent {
		font-size: 13px;
		font-family: Arial, Helvetica, sans-serif;
		color: #000000;
		font-weight: normal;
		line-height: 1.5;
		text-align: justify;
	}
	.bordertable {
		border-color: #000000; 
		border-width: 1px; 
		border-style: solid; 
		border-collapse:collapse;
	}
	.borderDOT  {
		border-color: #000000; 
		border-width: 1px; 
		border-style: dotted; 
		border-collapse:collapse;
	}
	table {
		font-size: 13px;
		font-family: Arial, Helvetica, sans-serif;
		color: #000000;
		font-weight: normal;
		line-height: 1.5;
		border-collapse:collapse;
	}
	.div-left-bold {
		width: 100%;
		font-weight: bold;
		padding: 5px 0;
	}
	.div-right-content {
		width: 100%;
		padding: 0 0 0 40px;
		margin-top: -25px;
	} 
	.div-right-content-no-margin-top {
		width: 100%;
		padding: 0 0 0 40px;
	}
</style>
<div>
	<div align="left"  style="width: 65%; float: left; vertical-align: top">
		<img src="<?php echo $gvUrl ?>/images/llkh/logodhqg.png" style="float:left; margin:-5px 5px 0px 5px;" width="72" height="53">
		<span style="font-size: 11px"><b>&nbsp;&nbsp;&nbsp;&nbsp;Đại học Quốc gia<br/>Thành phố Hồ Chí Minh</b></span>
	</div>
	<div align="right"  style="width: 30%; float: right; border: ">
		<table width="240px" style="font-size: 11px;" cellpadding="0" cellspacing="0" border="0" class="borderDOT">
			<tr>
				<td class="borderDOT">Ngày nhận hồ sơ</td>
				<td class="borderDOT" style="width: 100px"></td>
			</tr>
			<tr>
				<td class="borderDOT">Mã số đề tài</td>
				<td class="borderDOT"><?php echo $detailTmdt['ma_so_de_tai'] ?></td>
			</tr>
		</table>
	</div>
</div>
<div style="clear: both"></div>
<div align="center" style="font-size: 20px; padding: 10px 0">
	<b>THUYẾT MINH</b><br>ĐỀ TÀI KHOA HỌC VÀ CÔNG NGHỆ
</div>
<div class="fontcontent">
	<div class="div-left-bold"><b>A1.</b></div>
	<div class="div-right-content">
		<b>THÔNG TIN CHUNG</b><br>
		<b>Tên đề tài</b><br>
		- Tên tiếng Việt: <?php echo $detailTmdt['ten_de_tai_vn']; ?>
		<br>
		- Tên tiếng Anh: <?php echo $detailTmdt['ten_de_tai_en']; ?>
	</div>
	<div class="div-left-bold"><b>A2.</b></div>
	<div class="div-right-content">
		<b>Thuộc ngành/nhóm ngành</b><br>
		<?php 
		// Nhóm ngành
		$rowNhomNganh = $detailTmdt['nhomnganh'];
		foreach ($rowNhomNganh as $k => $v) {
			if($v['fk_ma_nhom_nganh'] == "999") {
				echo $v['ten_nhom_nganh_khac'].";";
			}else{
				echo  $v['ten_nhom_nganh'].";";
			}
		}
		
		if ($detailTmdt['chuyen_nganh_hep']){
			echo "<br><br>Chuyên ngành hẹp: " .$detailTmdt['chuyen_nganh_hep'];
		}
		?>
	</div>
	<div class="div-left-bold"><b>A3.</b></div>
	<div class="div-right-content">
		<b>Loại hình nghiên cứu: </b><?php echo $detailTmdt['ten_loai_hinh_nc']; ?>
	</div>
	<div class="div-left-bold"><b>A4.</b></div>
	<div class="div-right-content">
		<b>Thời gian thực hiện:</b> <?php echo $detailTmdt['thoi_gian_thuc_hien'] ?> tháng (kể từ khi được duyệt)
	</div>
	<div class="div-left-bold"><b>A5.</b></div>
	<div class="div-right-content">
		<b>Tổng kinh phí</b>
		<br>
		Tổng kinh phí: 
		<?php 
		$text = number_format(floatval($detailTmdt['tong_kinh_phi']) *1000000, 0, ',', '.');
		$text .= ' đồng (Bằng chữ: '.$detailTmdt['chu_tong_kinh_phi'].')';
		echo $text	;
		?>
		, gồm <br>
		- Kinh phí từ ĐHQG-HCM: 
		<?php 
		$text = number_format(floatval($detailTmdt['kinh_phi_tu_dhqg']) *1000000, 0, ',', '.');
		$text .= ' đồng (Bằng chữ: '.$detailTmdt['chu_kinh_phi_tu_dhqg'].')';
		echo $text	;
		$textKphd = number_format(floatval($detailTmdt['kinh_phi_huy_dong']) *1000000, 0, ',', '.');
		$textKphd .= ' đồng (Bằng chữ: '.$detailTmdt['chu_kinh_phi_huy_dong'].')';
		
		$textVonTuCo = number_format(floatval($detailTmdt['hd_von_tu_co']) *1000000, 0, ',', '.');
		$textVonTuCo .= ' đồng (Bằng chữ: '.$detailTmdt['chu_hd_von_tu_co'].')';
		
		$textVonKhac = number_format(floatval($detailTmdt['hd_khac']) *1000000, 0, ',', '.');
		$textVonKhac .= ' đồng (Bằng chữ: '.$detailTmdt['chu_hd_khac'].')';
		?>
		<br>- Kinh phí từ nguồn huy động: <?php echo $textKphd ?>, trong đó:
		<br> &nbsp; &nbsp; &nbsp; Vốn tự có: <?php echo $textVonTuCo ?>
		<br> &nbsp; &nbsp; &nbsp; Vốn khác: <?php echo $textVonKhac ?>
		<?php if ($detailTmdt['to_chuc_tai_tro_khac']){ ?>
			<br> Đã nộp hồ sơ đề nghị tài trợ từ nguồn kinh phí khác, tổ chức tài trợ: <?php echo $detailTmdt['to_chuc_tai_tro_khac'] ?> 
		<?php } ?>
	</div>
	<div class="div-left-bold"><b>A6.</b></div>
	<div class="div-right-content">
		<b>Chủ nhiệm</b>
		<br>
		Học hàm, học vị, họ và tên: <?php echo $detailTmdt['cndt_hh_hv_ho_ten'] ?>
		<br>Ngày, tháng, năm sinh: <?php echo $detailTmdt['cndt_ngay_sinh'] ?>, Phái: <?php echo $detailTmdt['cndt_ten_phai'] ?>
		<br>Số CMND: <?php echo $detailTmdt['cndt_so_cmnd'] ?>, Ngày cấp: <?php echo $detailTmdt['cndt_ngay_cap'] ?>, Nơi cấp: <?php echo $detailTmdt['cndt_ten_noi_cap'] ?>
		<br>Mã số thuế cá nhân: <?php echo $detailTmdt['cndt_ms_thue'] ?>
		<br>Số tài khoản: <?php echo $detailTmdt['cndt_so_tai_khoan'] ?> Tại ngân hàng: <?php  echo $detailTmdt['cndt_ngan_hang'] ?>
		<br>Địa chỉ cơ quan: <?php echo $detailTmdt['cndt_dia_chi_cq'] ?>
		<br>Điện thoại: <?php echo $detailTmdt['cndt_dien_thoai'] ?>, Email: <?php echo $detailTmdt['cndt_email'] ?>
		<p>
		<b>Tóm tắt hoạt động nghiên cứu và đào tạo sau đại học có liên quan đến đề tài của chủ nhiệm: </b>
		</p>
		<?php echo $detailTmdt['tom_tat_hd_nc'] ?>
		<?php if ($detailTmdt['dcndt_hh_hv_ho_ten'] != ""){ ?>
		<br>
		<b>Đồng chủ nhiệm</b>
		<br>
		Học hàm, học vị, họ và tên: <?php echo $detailTmdt['dcndt_hh_hv_ho_ten'] ?>
		<br>Ngày, tháng, năm sinh: <?php echo $detailTmdt['dcndt_ngay_sinh'] ?>, Phái: <?php echo $detailTmdt['dcndt_ten_phai'] ?>
		<br>Số CMND: <?php echo $detailTmdt['dcndt_so_cmnd'] ?>, Ngày cấp: <?php echo $detailTmdt['dcndt_ngay_cap'] ?>, Nơi cấp: <?php echo $detailTmdt['dcndt_ten_noi_cap'] ?>
		<br>Mã số thuế cá nhân: <?php echo $detailTmdt['dcndt_ms_thue'] ?>
		<br>Số tài khoản: <?php echo $detailTmdt['dcndt_so_tai_khoan'] ?> Tại ngân hàng: <?php echo $detailTmdt['dcndt_ngan_hang'] ?>;
		<br>Địa chỉ cơ quan: <?php echo $detailTmdt['dcndt_dia_chi_cq'] ?>
		<br>Điện thoại: <?php echo $detailTmdt['dcndt_dien_thoai'] ?>, Email: <?php echo $detailTmdt['dcndt_email'] ?>
		<?php } ?>
	</div>
	<div class="div-left-bold"><b>A7.</b></div>
	<div class="div-right-content">
		<b>Cơ quan chủ trì</b>
		<br>Tên cơ quan: <?php echo $detailTmdt['cqct_ten_co_quan'] ?>
		<br>Họ và tên thủ trưởng: <?php echo $detailTmdt['cqct_ho_ten_tt'] ?>
		<br>Điện thoại: <?php echo $detailTmdt['cqct_dien_thoai'] ?> Fax: <?php echo $detailTmdt['cqct_fax'] ?>
		<br>Email: <?php echo $detailTmdt['cqct_email'] ?>
		<br>Số tài khoản: <?php echo $detailTmdt['cqct_so_tai_khoan'] ?> Tại kho bạc: <?php echo $detailTmdt['cqct_kho_bac'] ?>
	</div>
	<div class="div-left-bold"><b>A8.</b></div>
	<div class="div-right-content">
		<b>Cơ quan phối hợp thực hiện</b>
		<?php if ($detailTmdt['cqph1_ten_co_quan']){ ?>
			<br><b>Cơ quan 1</b>:
			<br>Tên cơ quan: <?php echo $detailTmdt['cqph1_ten_co_quan'] ?>
			<br>Họ và tên thủ trưởng: <?php echo $detailTmdt['cqph1_ho_ten_tt'] ?>
			<br>Điện thoại: <?php echo $detailTmdt['cqph1_dien_thoai'] ?> Fax: <?php echo $detailTmdt['cqph1_fax'] ?>
			<br>Địa chỉ: <?php echo $detailTmdt['cqph1_dia_chi'] ?>
		<?php } ?>
		<?php if ($detailTmdt['cqph2_ten_co_quan']){ ?>
			<br><b>Cơ quan 2</b>:
			<br>Tên cơ quan: <?php echo $detailTmdt['cqph2_ten_co_quan'] ?>
			<br>Họ và tên thủ trưởng: <?php echo $detailTmdt['cqph2_ho_ten_tt'] ?>
			<br>Điện thoại: <?php echo $detailTmdt['cqph2_dien_thoai'] ?> Fax: <?php echo $detailTmdt['cqph2_fax'] ?>
			<br>Địa chỉ: <?php echo $detailTmdt['cqph2_dia_chi'] ?>
		<?php } ?>
	</div>
	<div class="div-left-bold"><b>A9.</b></div>
	<div class="div-right-content">
		<b>Nhân lực nghiên cứu</b><br>
		<table autosize="1"  width="100%" cellspacing="0" cellpadding="5" border="1" class="fontcontent bordertable">
			<thead>
				<tr style='height:20px;font-weight:bold'>
					<th align="center">TT</th>
					<th align="left">Học hàm, học vị, Họ và tên</th>
					<th align="left">SHCC/MSSV</th>
					<th align="left">Đơn vị công tác</th>
					<th align="center" title="Số tháng làm việc quy đổi">Số tháng làm việc quy đổi</th>
				</tr>
			</thead>
			<tbody>
			<?php 
			$rowNhanLucCbgd = $detailTmdt['nhanluc_cbgd'];
			
			if (count($rowNhanLucCbgd) > 0){
			?>
				<tr style="height:20px;">
					<td align="left" colspan="5">Thành viên chủ chốt</td>
				</tr>
				<?php foreach ($rowNhanLucCbgd as $k => $v) {?>
				<tr>
					<td align="center"><?php echo ($k+1) ?></td>
					<td align="left"><?php echo $v['hh_hv_ho_ten'] ?></td>
					<td align="left"><?php echo $v['shcc'] ?></td>
					<td align="left"><?php echo $v['don_vi_cong_tac'] ?></td>
					<td align="center"><?php echo $v['so_thang_lv_quy_doi'] ?></td>
				</tr>	
				<?php } ?>
				
			<?php 
			}
			
			$rowNhanLucSv = $detailTmdt['nhanluc_sv'];
			if (count($rowNhanLucSv) > 0) {	?>
				<tr style="height:20px;">
					<td align="left" colspan="5">Nghiên cứu sinh, học viên cao học, sinh viên</td>
				</tr>
				<?php foreach ($rowNhanLucSv as $k => $v) {?>
				<tr>
					<td align="center"><?php echo ($k+1) ?></td>
					<td align="left"><?php echo $v["sv_ho_ten"] ?>  (<?php echo $v["fk_ma_hoc_vien"] ?></td>
					<td align="left"><?php echo $v["fk_ma_hoc_vien"] ?></td>
					<td align="left"><?php echo $v["don_vi_cong_tac"] ?></td>
					<td align="center"><?php echo $v["so_thang_lv_quy_doi"] ?></td>
				</tr>
				<?php } ?>
			<?php }	?>
			</tbody>
		</table>
	</div>
	<div class="div-left-bold"><b>B.</b></div>
	<div class="div-right-content"><b>MÔ TẢ NGHIÊN CỨU</b></div>
	<div class="div-left-bold"><b>B1.</b></div>
	<div class="div-right-content">
		<div>
		<b>Tổng quan tình hình nghiên cứu trong, ngoài nước</b>
		</div>
		<div align="left"><?php echo $detailTmdt['tq_tinh_hinh_nc'] ?></div>
	</div>
	<div class="div-left-bold"><b>B2.</b></div>
	<div class="div-right-content">
		<div><b>Ý tưởng khoa học, tính cấp thiết và tính mới</b></div>
		<div align="left"><?php echo $detailTmdt['y_tuong_kh'] ?></div>
	</div>
	<?php if ($detailTmdt['kq_nc_so_khoi']){ ?>
	<div class="div-left-bold"><b>B3.</b></div>
	<div class="div-right-content">
		<div><b>Kết quả nghiên cứu sơ khởi (nếu có)</b></div>
		<div align="left"><?php echo $detailTmdt['kq_nc_so_khoi'] ?></div>
	</div>
	<?php } ?>
	<div class="div-left-bold"><b>B4.</b></div>
	<div class="div-right-content">
		<div><b>Tài liệu tham khảo</b></div>
		<div align="left"><?php echo $detailTmdt['tai_lieu_tk'] ?></div>
	</div>
	<?php
	$rowChuyenGianC = $detailTmdt['chuyengianc'];
	if (count($rowChuyenGianC) > 0){ ?>
	<div class="div-right-content-no-margin-top">
		<b>Giới thiệu chuyên gia/nhà khoa học am hiểu đề tài này</b><br>
		<table autosize="1"  width="100%" cellspacing="0" cellpadding="5" class="fontcontent bordertable" border="1">
			<thead>
				<tr style="height:20px;font-weight:bold">
					<th align="center">TT</th>
					<th align="left">Họ và tên</th>
					<th align="left">Hướng nghiên cứu chuyên sâu</th>
					<th align="left">Cơ quan công tác, địa chỉ</th>
					<th align="center" >Điện thoại, Email</th>
				</tr>
			</thead>
			<tbody>
				<?php foreach ($rowChuyenGianC as $k => $v) { ?>
				<tr>
					<td align="center"><?php echo ($k+1) ?></td>
					<td align="left"><?php $v['hh_hv_ho_ten'] ?></td>
					<td align="left"><?php $v['huong_nc_chuyen_sau'] ?></td>
					<td align="left"><?php $v['co_quan_cong_tac'] ?>, <?php $v['dia_chi'] ?></td>
					<td align="center"><?php $v['dien_thoai']?>, <?php $v['email'] ?></td>
				</tr>
				<?php } ?>
			</tbody>
		</table>
	</div>
	<?php } ?>
	<div class="div-left-bold"><b>B5.</b></div>
	<div class="div-right-content">
		<b>Kế hoạch và phương pháp nghiên cứu</b>
	</div>
	<div class="div-left-bold"><b>B5.1</b></div>
	<div class="div-right-content">
		<b>Mục tiêu</b> <br>
		<?php echo $detailTmdt['muc_tieu_nc_vn'] ?>
	</div>
	<div class="div-left-bold"><b>B5.2</b></div>
	<div class="div-right-content"><b>Nội dung</b></div>
	<div class="div-right-content-no-margin-top">
		<?php echo $detailTmdt['noi_dung_nc'] ?>
	</div>
	<?php if ($detailTmdt['pa_phoi_hop']){ ?>
	<div class="div-left-bold"><b>B5.3</b></div>
	<div class="div-right-content">
		<b>Phương án phối hợp (nếu có)</b><br>
		<?php echo $detailTmdt['pa_phoi_hop'] ?>
	</div>
	<?php }?>
	<div class="div-left-bold"><b>B6</b></div>
	<div class="div-right-content">
		<b>Kết quả nghiên cứu</b>
	</div>
	<div class="div-left-bold"><b>B6.1</b></div>
	<div class="div-right-content">
		<b>Ấn phẩm khoa học</b><br>
		<table cellspacing="0" width="100%" cellpadding="5" border="1" class="fontcontent bordertable">
				<thead>
					<tr style="height:20px;font-weight:bold">
						<th align="center">TT</th>
						<th align="left">Tên sách/bài báo dự kiến</th>
						<th align="center">Số lượng</th>
						<th align="left">Dự kiến nơi công bố<br><font style='font-weight:normal'>(tên Tạp chí, Nhà xuất bản)</font></th>
						<th align="left" >Ghi chú</th>
					</tr>
				</thead>
				<tbody>
					<?php 
					$rowAnPhamKhoaHoc = $detailTmdt['anphamkhoahoc'];
					
					foreach ($rowAnPhamKhoaHoc as $k => $v) { 
					?>
					<tr>
						<td align="left" colspan="5"><?php echo $v['ten_an_pham_kh'] ?></td>
					</tr>
					<tr>
						<td align=center><?php echo ($k+1) ?></td>
						<td align=left><?php echo $v['ten_bb_sach_dk'] ?></td>
						<td align=center><?php echo $v['so_luong'] ?></td>
						<td align=left><?php echo $v['dk_noi_cong_bo'] ?></td>
						<td align=left><?php echo $v['ghi_chu'] ?></td>
					</tr>
					<?php } ?>
				</tbody>
			</table>
	</div>
	<div class="div-left-bold"><b>B6.2</b></div>
	<div class="div-right-content">
		<b>Đăng ký sở hữu trí tuệ</b><br>
		<table width="100%" cellspacing="0" cellpadding="5" border="1" class="fontcontent bordertable">
			<thead>
				<tr style='height:20px;font-weight:bold'>
					<th align="center">TT</th>
					<th align="left">Hình thức đăng ký</th>
					<th align="center">Số lượng</th>
					<th align="left">Nội dung dự kiến đăng ký</th>
					<th align="left" >Ghi chú</th>
				</tr>
			</thead>
			<tbody>
			<?php
			$rowSoHuuTriTue = $detailTmdt['sohuutritue'];
			 
			foreach ($rowSoHuuTriTue as $k => $v) {
			?>
				<tr>
					<td align="center"> <?php echo ($k+1) ?></td>
					<td align="left"><?php echo $v['ten_hinh_thuc'] ?></td>
					<td align="center"><?php echo $v['so_luong'] ?></td>
					<td align="left"><?php echo $v['noi_dung_du_kien'] ?></td>
					<td align="left"><?php echo $v['ghi_chu'] ?></td>
				</tr>
			<?php } ?>
			</tbody>
		</table>
	</div>
	<div class="div-right-content-no-margin-top">
		<b>Mô tả sản phẩm/kết quả nghiên cứu</b><br>
		<b>Dạng I: Các sản phẩm mềm</b><br>
		<table autosize="1" width="100%" cellspacing="0" cellpadding="5" border="1" class="fontcontent bordertable">
			<thead>
				<tr style='height:20px;font-weight:bold'>
					<th align="center">TT</th>
					<th align="left">Tên sản phẩm</th>
					<th align="center">Chỉ tiêu đánh giá (định lượng)</th>
					<th align="left" >Ghi chú</th>
				</tr>
			</thead>
			<tbody>
			<?php 
			$rowSanPhamMem = $detailTmdt['sanphammem'];
			
			foreach ($rowSanPhamMem as $k => $v) { 
			?>
				<tr>
					<td align="center"><?php echo ($k+1) ?></td>
					<td align="left"><?php echo $v['ten_san_pham'] ?></td>
					<td align="center"><?php echo $v['chi_tieu_danh_gia'] ?></td>
					<td align="left"><?php echo $v['ghi_chu'] ?></td>
				</tr>
			<?php } ?>
			</tbody>
		</table>
		<br><b>Dạng II: Các sản phẩm cứng</b><br>
		<table autosize="1" width="100%" cellspacing="0" cellpadding="5" border="1" class="fontcontent bordertable">
				<thead>
					<tr style="height:20px; font-weight:bold;">
						<th align="center" rowspan="3">TT</th>
						<th align="center" rowspan="3">Tên sản phẩm cụ thể và chỉ tiêu chất lượng chủ yếu của sản phẩm</th>
						<th rowspan="3" align="center">Đơn vị đo</th>
						<th colspan="3" align="center">Mức chất lượng</th>
						<th rowspan="3" align="center">Dự kiến số lượng/quy mô sản phẩm tạo ra</th>
					</tr>
					<tr style="height:20px; font-weight:bold;">
						<th rowspan="2" align="center">Chỉ tiêu đánh giá 
							<span style="font-weight: normal">(định lượng)</span>
						</th>
						<th colspan="2" align="center">Mẫu tương tự<br>
							<span style="font-weight: normal">(theo các tiêu chuẩn mới nhất)</span>
						</th>
					</tr>
					<tr style="height:20px; font-weight:bold;">
						<th align="center">Trong nước</th>
						<th align="center">Thế giới</th>
					</tr>
				</thead>
				<tbody>
				<?php 
				$rowSanPhamCung = $detailTmdt['sanphamcung'];
				foreach ($rowSanPhamCung as $k => $v) {
				?>
					<tr>
						<td align="center"><?php echo ($k+1) ?></td>
						<td align="left"><?php echo $v['ten_san_pham'] ?></td>
						<td align="center"><?php echo $v['don_vi_do'] ?></td>
						<td align="center"><?php echo $v['chi_tieu_danh_gia'] ?></td>
						<td align="center"><?php echo $v['trong_nuoc'] ?></td>
						<td align="center"><?php echo $v['the_gioi'] ?></td>
						<td align="left"><?php echo $v['so_luong_quy_mo'] ?></td>
					</tr>
				<?php } ?>
				</tbody>
			</table>
			<br><b>Mức chất lượng các sản phẩm dạng II so với các sản phẩm tương tự trong nước và thế giới</b>
			<br><?php echo $detailTmdt['muc_cl_sp_dang_ii'] ?>
	</div>
	<div class="div-left-bold"><b>B6.3</b></div>
	<div class="div-right-content">
		<b>Kết quả đào tạo</b><br>
		<table autosize="1" width="100%" cellspacing="0" cellpadding="5" border="1" class="fontcontent bordertable">
			<thead>
				<tr style='height:20px;font-weight:bold'>
					<td align="center">TT</td><td align="left">Cấp đào tạo</td><td align="center">Số lượng</td><td align="left">Nhiệm vụ được giao trong đề tài</font></td><td align="center" >Dự kiến kinh phí <span style='font-weight:normal'>(Triệu đồng)</span></td>
				</tr>
			</thead>
			<tbody>
				<?php 
				$rowKetQuaDaoTao = $detailTmdt['ketquadaotao'];
				foreach ($rowKetQuaDaoTao as $k => $v) { ?>
				<tr>
					<td align="center"><?php echo ($k+1) ?></td>
					<td align="left"><?php echo $v['ten_capdt'] ?></td>
					<td align="center"><?php echo $v['so_luong'] ?></td>
					<td align="left"><?php echo $v['nhiem_vu_duoc_giao'] ?></td>
					<td align="center"><?php echo $v['du_kien_kinh_phi'] ?></td>
				</tr>
				<?php } ?>
			</tbody>
		</table>
	</div>
	<div class="div-left-bold"><b>B7.</b></div>
	<div class="div-right-content">
		<b>Khả năng ứng dụng kết quả nghiên cứu</b>
	</div>
	<div class="div-left-bold"><b>B7.1</b></div>
	<div class="div-right-content">
		<b>Khả năng ứng dụng trong lĩnh vực đào tạo, nghiên cứu khoa học & công nghệ, chính sách, quản lý...</b>
		<br>
		<?php echo $detailTmdt['ud_kqnc_lv_dao_tao'] ?>
	</div>
	<div class="div-left-bold"><b>B7.2</b></div>
	<div class="div-right-content">
		<b>Khả năng về ứng dụng các kết quả nghiên cứu vào sản xuất kinh doanh, về liên doanh liên kết với các doanh nghiệp, về thị trường</b>
		<br>
		<?php echo $detailTmdt['ud_kqnc_sxkd'] ?>
	</div>
	<div class="div-left-bold"><b>B7.3</b></div>
	<div class="div-right-content">
		<b>Phương thức chuyển giao kết quả nghiên cứu</b>
		<br>
		<?php echo $detailTmdt['ud_kqnc_chuyen_giao'] ?>
	</div>
	<div class="div-left-bold"><b>B8.</b></div>
	<div class="div-right-content">
		<b>Tổng hợp kinh phí đề nghị ĐHQG-HCM cấp</b>
	</div>
	<div class="div-right-content-no-margin-top">
		<div align="right"><em>Đơn vị tính: triệu đồng</em></div>
		<table autosize="1" width="100%" cellspacing="0" cellpadding="5" class="fontcontent bordertable" border="1">
			<thead>
				<tr style='height:20px; font-weight: bold'>
					<th align="center" rowspan="2">TT</th>
					<th align="left"  rowspan="2">Các khoản chi phí</th>
					<th align="center" colspan="3">Đề nghị ĐHQG-HCM cấp</th>
				</tr>
				<tr style='height:20px;'>
					<th align="right">Kinh phí</th>
					<th align="right">Trong đó khoán chi (*)</th>
					<th align="right">%</th>
				</tr>
			</thead>
			<tbody>
				<?php
				// B8 tong hop kinh phi
				$tongkhoanchi = 0;
				$tongphantram = 0;
				$rowKhoanChiPhi = $detailTmdt['khoanchiphi'];
				
				foreach ($rowKhoanChiPhi as $k => $v) {
					$tongkhoanchi += floatval($v['kinh_phi']);
				}
				
				foreach ($rowKhoanChiPhi as $k => $v) {
				?>
				<tr>
					<td align="center"><?php echo ($k+1) ?></td>
					<td align="left"><?php echo $v['ten_khoan_chi_phi'] ?></td>
					<td align="right"><?php echo number_format(floatval($v['kinh_phi']), 0, ',', '.') ?></td>
					<td align="right"><?php echo number_format(floatval($v['khoan_chi']), 2, ',', '.') ?></td>
					<td align="right">
					<?php 
						$calVal = 0;
						if($tongkhoanchi != 0) {
							$calVal = (floatval($v['kinh_phi'])/$tongkhoanchi)*100;
						}
						echo number_format($calVal, 2, ',', '.').'%';
					?>
					</td>
				</tr>
				<?php
					if ($tongkhoanchi != 0) {
						$tongphantram += (floatval($v['kinh_phi'])/$tongkhoanchi)*100;
					}
				}
				?>
				<tr>
					<td align="center"></td>
					<td align="left"><b>Cộng:</b></td>
					<td align="right"><?php echo number_format($tongkhoanchi,2, ',', '.') ?></td>
					<td align="right"></td>
					<td align="right"><?php echo number_format($tongphantram,0, ',', '.') ?>%</td>
				</tr>
			</tbody>
		</table>
	</div>
	<div class="div-right-content-no-margin-top">
		<table autosize="1" width="100%" class="fontcontent">
			<tr>
				<td valign="top" align="left" style="font-style:italic">
					(*) Theo quy định tại Thông tư số 93/2006/TTLT/BTC-BKHCN của liên Bộ Tài chính - Bộ Khoa học và Công nghệ 
		ban hành ngày 04/10/2006 và Thông tư số 44/2007/TTLT/BTC-BKHCN của liên Bộ Tài chính - 
		Bộ Khoa học và Công nghệ ban hành ngày 07/5/2007.
				</td>
			</tr>
		</table>
	</div>
	<pagebreak />
	<table autosize="1" width="100%" class="fontcontent">
		<tr>
			<td align="left" valign=top width="50%" >
				<div style="width:300px; margin-top:20px" align="center">
					<span><em>Ngày ...... tháng ...... năm .........</em></span><br/>
					<b>Chủ tịch hội đồng thẩm định <sup>i</sup></b>
					<br/><br/><br/><br/><br/><br/>
				</div>
			</td>
			<td align="right" width="50%">
				<div style="width:400px; margin-top:20px" align="center">
					<span><em>Ngày <?php echo $ngay ?> tháng <?php echo $thang ?> năm <?php echo $nam ?></em></span>
					<table autosize="1" width="100%" class="fontcontent">
						<tr>
							<td align="center">
								<b>Chủ nhiệm</b>
								<br><br><br><br>
								<b>
								<span><?php echo $detailTmdt['cndt_hh_hv_ho_ten'] ?></span>
								</b>
							</td>
							<?php if($detailTmdt['dcndt_hh_hv_ho_ten']) { ?>
							<td align="center" id="td_dongchunhiem<?php echo $key; ?>">
								<b>Đồng chủ nhiệm</b>
								<br><br><br><br>
								<b><span><?php echo $detailTmdt['dcndt_hh_hv_ho_ten'] ?></span></b>
							</td>
							<?php } ?>
						</tr>
					</table>
					
				</div>
			</td>
		</tr>
		<tr>
			<td align="left" valign=top width="50%" >
				<div style="width:300px; margin-top:20px" align="center">
					<span><em>Ngày ...... tháng ...... năm .........</em></span><br/>
					<b>Cơ quan chủ trì <sup>ii</sup></b>
					<br/><br/><br/><br/><br/><br/>
					
				</div>
			</td>
			<td align="right" width="50%">
				<div style="width:400px; margin-top:20px" align="center">
					<span><em>Ngày ...... tháng ...... năm .........</em></span><br/>
					<b>Cơ quan chủ quản <sup>iii</sup></b>
					<br/><br/><br/><br/><br/><br/>
					
				</div>
			</td>
		</tr>
	</table>
</div>