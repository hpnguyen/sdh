<?php
$ngay =date("d");
$thang =date("m");
$nam =date("Y");

if ($cbgd["hinh_anh"] !=""){
	$filehinh  = Helper::getHelper('functions/util')->getGvRootURL().str_replace('./', '/', $cbgd["hinh_anh"]);
}else{
	$filehinh  = Helper::getHelper('functions/util')->getGvRootURL()."/images/llkh/khunganh4x6.png";
}
?>
<style type="text/css">
	.fontcontent {
		font-size: 13px;
		font-family: Arial, Helvetica, sans-serif;
		color: #000000;
		font-weight: normal;

	}
	.bordertable {
		border-color: #000000; 
		border-width: 1px; 
		border-style: solid; 
		border-collapse:collapse;
	}
	.header-second-col {
		width: 97%;
	}
	.col-title{
		padding: 5px 0;
	} 
	.content-col {
		width: 100%;
		font-size: 13px;
		font-family: Arial, Helvetica, sans-serif;
		color: #000000;
		font-weight: normal;
		clear:both;
	}
	
	.first-col {
		width: 4%;
		float: left;
		text-align: left;
		display: inline-block;
		vertical-align: top;
	}
	.second-col {
		width: 95%;
		float: left;
		text-align: left;
		display: inline-block;
		vertical-align: top;
	}
	.llkh-signature {
		padding: 20px 0;
	}
	.llkh-signature-left {
		width: 50%; 
		float: left;
	}
	.llkh-signature-right{
		width: 49%; 
		float: right;
	}
	.llkh-first-title {
		font-size:20px; 
		font-weight:bold;
		height: 40px;
	}
	.llkh-first-content {
		margin-top: -120px;
	}
	.llkh-image {
		height: 240px;
		min-height: 240px;
		max-height: 240px;
	}
	table thead tr th {
		text-align: center;
	}
</style>
<div align="center" class="fontcontent" valign="middle">
	<div class="llkh-first-title" align="center">LÝ LỊCH KHOA HỌC</div>
	<div align="right" class="llkh-image">
		<img id="framehinh46_bm_llkh_bk" src="<?php echo $filehinh ?>" width="160px" />
	</div>
</div>
<?php
$z = 1;
$macb = $cbgd["ma_can_bo"];
?>
<div class="content-col llkh-first-content">
	<div class="col-title first-col"><b>I.</b></div>
	<div class="col-title second-col"><b>THÔNG TIN CHUNG</b></div>
</div>
<div class="content-col">
	<div class="col-title first-col"><b><?php echo $z++ . "."; ?></b></div>
	<div class="col-title second-col"><b>Họ và tên:</b> <?php echo $cbgd["ho"]. " " .$cbgd["ten"]; ?></div>
</div>
<div class="content-col">
	<div class="col-title first-col"><b><?php echo $z++ . "."; ?></b></div>
	<div class="col-title second-col"><b>Ngày sinh:</b> <?php echo $cbgd["ngay_sinh"]; ?></div>
</div>
<div class="content-col">
	<div class="col-title first-col"><b><?php echo $z++ . "."; ?></b></div>
	<div class="col-title second-col"><b>Nam/Nữ:</b> <?php echo $cbgd["phai_desc"]; ?></div>
</div>
<div class="content-col">
	<div class="col-title first-col"><b><?php echo $z++ . "."; ?></b></div>
	<div class="col-title second-col"><b>Nơi đang công tác:</b></td>
</div>
<div class="content-col">
	<div class="col-title first-col">&nbsp;</div>
	<div class="col-title second-col">
		<u>Trường/Viện:</u> <?php echo $cbgd["co_quan_cong_tac"]; ?>
	</div>
</div>
<div class="content-col">
	<div class="col-title first-col">&nbsp;</div>
	<div class="col-title second-col">
		<u>Phòng/Khoa:</u> <?php echo $cbgd["ten_khoa"] ?>
	</div>
</div>
<div class="content-col">
	<div class="col-title first-col">&nbsp;</div>
	<div class="col-title second-col">
		<u>Bộ môn:</u> <?php echo $cbgd["ten_bo_mon"] ?>
	</div>
</div>
<div class="content-col">
	<div class="col-title first-col">&nbsp;</div>
	<div class="col-title second-col">
		<u>Chức vụ:</u> 
		<?php echo ($cbgd["ten_chuc_vu"] != "" ? $cbgd["ten_chuc_vu"]. " " .$cbgd["ten_bo_mon_ql"] : '') ?>
	</div>
</div>
<div class="content-col">
	<div class="col-title first-col"><b><?php echo $z++ . "."; ?></b></div>
	<div class="col-title second-col">
		<b>Học vị:</b> <?php echo $cbgd["ten_hv"]?>, <b>năm đạt:</b> <?php echo $cbgd["nam_dat_hv_cao_nhat"]?>
	</div>
</div>
<div class="content-col">
	<div class="col-title first-col">
		<b><?php echo $z++ . "."; ?></b>
	</div>
	<div class="col-title second-col">
		<b>Học hàm:</b>	<?php echo $cbgd["ten_hoc_ham"]; ?>
		<?php if ($cbgd["ma_hoc_ham"] == 'GS' || $cbgd["ma_hoc_ham"] == 'PGS' ){ ?>
		, <b>năm phong:</b> <?php echo $cbgd["nam_phong_hoc_ham"] ?>
		<?php	} ?>
	</div>
</div>
<div class="content-col">
	<div class="col-title first-col"><b><?php echo $z++ . "."; ?></b></div>
	<div class="col-title second-col"><b>Liên lạc:</b></div>
</div>
<table autosize="1" width="100%" align="center" cellspacing="0" cellpadding="5" border="1" class="fontcontent bordertable">
	<thead>
		<tr>
			<th width="15px"><em>TT</em></th>
			<th align="left"></th>
			<th align="center"><em>Cơ quan</em></th>
			<th align="center" ><em>Cá nhân</em></th>
		</tr>
	</thead>
	<tbody>
		<tr>
			<td>1</td>
			<td><b>Địa chỉ</b></td>
			<td><?php echo $cbgd["dia_chi"]?></td>
			<td><?php echo $cbgd["dia_chi_rieng"]?></td>
		</tr>
		<tr>
			<td>2</td>
			<td><b>Điện thoại/fax</b></td>
			<td><?php echo $cbgd["dien_thoai"]?></td>
			<td><?php echo $cbgd["dien_thoai_cn"]?></td>
		</tr>
		<tr>
			<td>3</td>
			<td><b>Email</b></td>
			<td><?php echo $cbgd["email"]?></td>
			<td><?php echo $cbgd["email_2"]?></td>
		</tr>
	</tbody>
</table>
<div class="content-col">
	<div class="col-title first-col"><b><?php echo $z++ . "."; ?></b></div>
	<div class="col-title second-col"><b>Trình độ ngoại ngữ:</b></div>
</div>
<table width="100%" align="center" cellspacing="0" cellpadding="5" border="1" class="fontcontent bordertable" height="20">
	<thead>
		<tr>
			<th width="15px"><em>TT</em></th>
			<th align="left"><em>Tên ngoại ngữ</em></th>
			<th align="left"><em>Nghe</em></th>
			<th align="left"><em>Nói</em></th>
			<th align="left"><em>Viết</em></th>
			<th align="left"><em>Đọc hiểu tài liệu</em></th>
		</tr>
	</thead>
	<tbody>
		<?php foreach ($cbgd['dm_ngoai_ngu'] as $i => $resDM) {?>
		<tr>
			<td align="left"><?php echo ($i+1) ?></td>
			<td><?php echo $resDM["ten_ngoai_ngu"]?></td>
			<td><?php echo $resDM["ky_nang_nghe"] ?></td>
			<td><?php echo $resDM["ky_nang_noi"] ?></td>
			<td><?php echo $resDM["ky_nang_viet"] ?></td>
			<td><?php echo $resDM["ky_nang_doc"] ?></td></tr>		
		<?php } ?>
	</tbody>
</table>
<div class="content-col">
	<div class="col-title first-col"><b><?php echo $z++ . "."; ?></b></div>
	<div class="col-title second-col"><b>Thời gian công tác:</b></div>
</div>
<table autosize="1" width="100%" align="center" cellspacing="0" cellpadding="5" border="1" class="fontcontent bordertable">
	<thead>
	  <tr style="font-weight:bold;">
		<th align="left" style="width:130px"><em>Thời gian</em></th>
		<th align="left"><em>Nơi công tác</em></th>
		<th align="left"><em>Chức vụ</em></th>
	  </tr>
	</thead>
	<tbody>
		<?php foreach ($cbgd['dm_qua_trinh_cong_tac'] as $i => $resDM) {
				if ($resDM["thoi_gian_kt"] == ''){
					$thoigian = "Từ <b>".$resDM["thoi_gian_bd"]."</b> đến nay";
				}else{
					$thoigian = "Từ <b>".$resDM["thoi_gian_bd"]."</b> đến <b>".$resDM["thoi_gian_kt"]."</b>";
				}
		?>	<tr>
				<td><?php echo $thoigian ?></td>
				<td><?php echo $resDM["noi_cong_tac"] ?></td>
				<td align="left"><?php echo $resDM["ten_chuc_vu"] ?></td>
			</tr>
		<?php } ?>
	</tbody>
</table>
<div class="content-col">
	<div class="col-title first-col"><b><?php echo $z++ . "."; ?></b></div>
	<div class="col-title second-col"><b>Quá trình đào tạo:</b></div>
</div>
<table  autosize="1" width="100%" align="center" cellspacing="0" cellpadding="5" border="1" class="tableData fontcontent bordertable" height="20">
	<thead>
	  <tr style="font-weight:bold;">
		<th align="left" style="width:100px"><em>Bậc đào tạo</em></th>
		<th align="left"><em>Thời gian</em></th>
		<th align="left"><em>Nơi đào tạo</em></th>
		<th align="left"><em>Chuyên ngành</em></th>
		<th align="left"><em>Tên luận án tốt nghiệp</em></th>
	  </tr>
	</thead>
	<tbody>
		<?php foreach ($cbgd['dm_qua_trinh_dao_tao'] as $i => $resDM) {
				if ($resDM["fk_nganh"]=="99999999"){
					$ten_nganh = $resDM["nganh_khac"];
				} else {
					$ten_nganh = $resDM["ten_nganh"];
				}
		?>
				<tr align="left" valign="top">
					<td align="left"><?php echo $resDM["ten_bac"] ?></td>
					<td align="center"><?php echo $resDM["thoi_gian_bd"] ?> - <?php echo $resDM["thoi_gian_tn"] ?></td>
					<td align="left"><?php echo $resDM["noi_dao_tao"] ?>, <?php echo $resDM["ten_quoc_gia"] ?></td>
					<td align="left"><?php echo $ten_nganh ?></td>
					<td align="left"><?php echo $resDM["ten_luan_an"] ?></td>
				</tr>
			<?php }	?>
	</tbody>
</table>
<div class="content-col">
	<div class="col-title first-col"><b>II.</b></div>
	<div class="col-title second-col"><b>NGHIÊN CỨU VÀ GIẢNG DẠY</b></div>
</div>	
<div class="content-col">
	<div class="col-title first-col">
		<b><?php $z = 1; echo $z++ . "."; ?></b>
	</div>
	<div class="col-title second-col"><b>Các lĩnh vực chuyên môn và hướng nghiên cứu:</b></div>
</div>
<div class="content-col">
	<div class="col-title first-col">&nbsp;</div>
	<div class="col-title second-col">
		<?php echo ($z-1).".1"; ?> Lĩnh vực chuyên môn:<br>
		<table autosize="1" width="100%" align="center" cellspacing="0" cellpadding="5" border="0" class="tableData fontcontent" >
			<tr >
				<td align="left" width="123px"><b>- Lĩnh vực:</b></td>
				<td align="left">
					<?php 	
						$tmp = '';
						foreach ($cbgd['dm_linh_vuc_nghien_cuu'] as $i => $resDM) {
							$txtTenLVNC = $resDM["ten_lvnc"];
							if ($resDM["lvnc_khac"] != ''){
								$txtTenLVNC = $txtTenLVNC . ": " . $resDM["lvnc_khac"];
							}
								
							$tmp .= "+ ".$txtTenLVNC."<br/>";
						}
						$tmp = substr($tmp, 0, -5);
						echo $tmp;
					?>
				</td>
			</tr>
			<tr>
				<td align="left"><b>- Chuyên ngành:</b></td>
				<td align="left"><?php echo $cbgd["chuyen_nganh"] ?></td>
			</tr>
			<tr >
				<td align="left" style="font-weight:bold;">- Chuyên môn:</td>
				<td align="left"><?php echo $cbgd["chuyen_mon"]  ?></td>
			</tr>	
		</table>
	</div>
</div>
<div class="content-col">
	<div class="col-title first-col">&nbsp;</div>
	<div class="col-title second-col"><?php echo ($z-1).".2"; ?> Hướng nghiên cứu:<br>
		<table autosize="1" width="100%" align="center" cellspacing="0" cellpadding="2" border="0" class="tableData fontcontent">
			<tbody>
				<?php foreach ($cbgd['dm_huong_de_tai'] as $i => $resDM) { ?>
					<tr align="left" valign="top">
						<td align="left"><?php echo ($i+1) ?></td>
						<td align="left"><?php echo $resDM["ten_de_tai"] ?></td>
					</tr>
				<?php } ?>
			</tbody>
		</table>
	</div>
</div>
<div class="content-col">
	<div class="col-title first-col"><b><?php echo $z++ . "."; ?></b></div>
	<div class="col-title second-col"><b>Quá trình nghiên cứu</b></div>
</div>
<table autosize="1" width="100%" align="center" cellspacing="0" cellpadding="5" border="1" class="tableData fontcontent bordertable">
	<thead>
	  <tr style="font-weight:bold;">
		<th width="15px"><em>TT</em></th>
		<th align="left"><em>Tên đề tài/dự án</em></th>
		<th align="center" style="width:80px"><em>Mã số & <br/>cấp quản lý</em></th>
		<th align="center" ><em>Thời gian<br/>thực hiện</em></th>
		<th align="center" style="width:80px"><em>Kinh phí<br/><span style="font-weight:normal">(triệu đồng)</span></em></th>
		<th align="center" style="width:80px"><em>Chủ nhiệm<br/>/tham gia</em></th>
		<th align="center" style="width:80px"><em>Ngày<br/>nghiệm thu</em></th>
		<th align="center"><em>Kết quả</em></th>
	  </tr>
	</thead>
	<tbody>
		<?php foreach ($cbgd['dm_de_tai_nckh'] as $i => $resDM) { ?>
			<tr align="left" valign="top">
				<td><?php echo ($i+1) ?></td>
				<td><?php echo $resDM["ten_de_tai"] ?></td>
				<td align="center">
				<?php if ($resDM["ma_so_de_tai"] != ""){
					echo "{".$resDM["ma_so_de_tai"]."}/{".$resDM["ten_cap"]."}";
				} else {
					echo $resDM["ten_cap"];
				}?>
				</td>
				<td align="center"><?php echo $resDM["nam_bat_dau"] ?><?php echo $resDM["nam_ket_thuc"] != '' ? "-". $resDM["nam_ket_thuc"] : "" ?></td>
				<td align="center"><?php echo $resDM["kinh_phi"] ?></td>
				<td align="center"><?php echo $resDM["tham_gia"] ?></td>
				<td align="center"><?php echo $resDM["ngay_nghiem_thu"] ?></td>
				<td align="center"><?php echo $resDM["tt_ket_qua"] ?></td>
			</tr>
		<?php } ?> 
	</tbody>
</table>
<div class="content-col">
	<div class="col-title first-col"><b><?php echo $z++ . "."; ?></b></div>
	<div class="col-title second-col">
		<b>Đã và đang hướng dẫn sinh viên, học viên cao học, nghiên cứu sinh</b>
	</div>
</div>
<table autosize="1" width="100%" align="center" cellspacing="0" cellpadding="5" border="1" class="tableData fontcontent bordertable">
	<thead>
		<tr>
			<th style="width:15px;"><em>TT</em></th>
			<th align="left" style="width:150px;"><em>Tên SV, HVCH, NCS</em></th>
			<th align="left"><em>Tên luận án</em></th>
			<th align="center" style="width:50px;"><em>Năm tốt nghiệp</em></th>
			<th align="center" style="width:50px;"><em>Bậc đào tạo</em></th>
			<th align="center" style="width:100px;"><em>Sản phẩm của đề tài/dự án</em></th>
		</tr>
	</thead>
	<tbody>
	<?php foreach ($cbgd['ds_huong_dan_luan_an'] as $i => $resDM) { ?>
		<tr>
			<td valign="top"><?php echo ($i + 1) ?></td>
			<td valign="top" style="text-transform:capitalize;"><?php echo $resDM["ho_ten_sv"] ?></td>
			<td><?php echo $resDM["ten_luan_an"] ?></td>
			<td align="center" valign="top"><?php echo $resDM["nam_tot_nghiep"] ?></td>
			<td align="center" valign="top"><?php echo $resDM["ten_bac"] ?></td>
			<td align="center" valign="top"><?php echo $resDM["san_pham_ma_de_tai"] ?></td>
		</tr>
	<?php }	?>
	</tbody>
</table>
<div class="content-col">
	<div class="col-title first-col"><b>III.</b></div>
	<div class="col-title second-col"><b>CÁC CÔNG TRÌNH ĐÃ CÔNG BỐ</b></div>
</div>
<div class="content-col">
	<div class="col-title first-col"><b><?php $z=1; echo $z++ . "."; ?></b></div>
	<div class="col-title second-col">
		<b>Sách phục vụ đào tạo đại học, sau đại học</b> <em>(Chuyên khảo, giáo trình, sách tham khảo)</em>
	</div>
</div>
<div class="content-col">
	<div class="col-title first-col"><i><?php echo ($z-1). ".1"; ?></i></div>
	<div class="col-title second-col"><i>Sách xuất bản Quốc tế</i></div>
</div>
<table autosize="1"  width="100%" align="center" cellspacing="0" cellpadding="5" border="1" class="tableData fontcontent bordertable">
	<thead>
		<tr>
			<th style="width:20px"><em>TT</em></th>
			<th><em>Tên sách </em></th>
			<th align="center" style="width:90px"><em>Sản phẩm của<br/>đề tài/dự án</em></th>
			<th align="left" style="width:150px"><em>Nhà xuất bản</em></th>
			<th style="width:60px" align="center"><em>Năm xuất bản</em></th>
			<th align="center" style="width:80px"><em>Tác giả/<br/>Đồng tác giả</em></th>
			<th align="center" style="width:150px"><em>Bút danh</em></th>
		</tr>
	</thead>
 <tbody>
 <?php foreach ($cbgd['dm_sach_xuat_ban_quoc_te'] as $i => $resDM) {?>
		<tr valign="top">
		<td align="left"><?php echo ($i + 1) ?></td>
		<td align="left"><?php echo $resDM["ten_sach"] ?></td>
		<td align="center"><?php echo $resDM["san_pham_ma_de_tai"] ?></td>
		<td align="left"><?php echo $resDM["nha_xuat_ban"] ?></td>
		<td align="center"><?php echo $resDM["nam_xuat_ban"] ?></td>
		<td align="center"><?php echo $resDM["tac_gia_desc"] ?></td>
		<td align="center"><?php echo $resDM["but_danh"] ?></td>
		</tr>
<?php } ?>
 </tbody>
</table>
<div class="content-col">
	<div class="col-title first-col"><i><?php echo ($z-1). ".2"; ?></i></div>
	<div class="col-title second-col"><i>Sách xuất bản trong nước</i></div>
</div>
<table autosize="1" width="100%" align="center" cellspacing="0" cellpadding="5" border="1" class="tableData fontcontent bordertable">
	<thead>
		<tr>
			<th style="width:20px"><em>TT</em></th>
			<th><em>Tên sách </em></th>
			<th align="center" style="width:90px"><em>Sản phẩm của<br/>đề tài/dự án</em></th>
			<th align="left" style="width:150px"><em>Nhà xuất bản</em></th>
			<th style="width:60px" align="center"><em>Năm xuất bản</em></th>
			<th align="center" style="width:80px"><em>Tác giả/<br/>Đồng tác giả</em></th>
			<th align="center" style="width:150px"><em>Bút danh</em></th>
		</tr>
	</thead>
	<tbody>
	<?php foreach ($cbgd['dm_sach_xuat_ban_trong_nuoc'] as $i => $resDM) { ?>
		<tr valign="top">
			<td align="left"><?php echo ($i + 1) ?></td>
			<td align="left"><?php echo $resDM["ten_sach"] ?></td>
			<td align="center"><?php echo $resDM["san_pham_ma_de_tai"] ?></td>
			<td align="left"><?php echo $resDM["nha_xuat_ban"] ?></td>
			<td align="center"><?php echo $resDM["nam_xuat_ban"] ?></td>
			<td align="center"><?php echo $resDM["tac_gia_desc"] ?></td>
			<td align="center"><?php echo $resDM["but_danh"] ?></td>
		</tr>
	<?php } ?>
	</tbody>
</table>
<div class="content-col">
	<div class="col-title first-col"><b><?php echo $z++ . "."; ?></b></div>
	<div class="col-title second-col"><b>Các bài báo</b></div>
</div>
<div class="content-col">
	<div class="col-title first-col"><i><?php echo ($z-1). ".1"; ?></i></div>
	<div class="col-title second-col"><i>Đăng trên tạp chí Quốc tế</i></div>
</div>
<table autosize="1" width="100%" align="center" cellspacing="0" cellpadding="5" border="1" class="tableData fontcontent bordertable">
	 <thead>
	  <tr style="font-weight:bold">
		<th style="width:20px"><em>TT</em></th>
		<th><em>Tên tác giả, tên bài viết, tên tạp chí và số của tạp chí, trang đăng bài viết, năm xuất bản</em></th>
		<th align="center" style="width:90px"><em>Sản phẩm của<br/>đề tài/dự án</em></th>
		<th align="center" style="width:150px"><em>Số hiệu ISSN <br/><span style="font-weight:normal">(ghi rõ thuộc ISI hay không)</span></em></th>
		<th style="width:50px" align="center"><em>Điểm IF</em></th>
	  </tr>
	 </thead>
	 <tbody>
	 <?php foreach ($cbgd['tap_chi_quoc_te'] as $i => $resDM) { ?>
		<tr align="left" valign="top">
			<td valign="top" width="20"><?php echo ($i+1) ?></td>
			<td width="550">
				<?php echo $resDM["ten_tac_gia"] ?>, 
				<?php echo $resDM["ten_bai_bao"]?>, 
				<?php echo $resDM["ten_tap_chi"] ?>, 
				<?php echo $resDM["so_tap_chi"] ?>, 
				<?php echo $resDM["trang_dang_bai_bao"] ?>, 
				<?php echo $resDM["nam_xuat_ban_tap_chi"] ?>
			</td>
			<td align="center" valign="top"><?php echo $resDM["san_pham_ma_de_tai"] ?></td>
			<td align="center" valign="top"><?php echo $resDM["isbn"]?><?php echo $resDM["isi"] != "" ? " thuộc ISI: ".$resDM["isi"] : ""?></td>
			<td align="center" valign="top"><?php echo $resDM["diem_if"] ?></td>
		</tr>
	<?php }?>
	 </tbody>
</table>
<div class="content-col">
	<div class="col-title first-col"><i><?php echo ($z-1). ".2"; ?></i></div>
	<div class="col-title second-col"><i>Đăng trên tạp chí trong nước</i></div>
</div>
<table autosize="1" width="100%" align="center" cellspacing="0" cellpadding="5" border="1" class="tableData fontcontent bordertable">
	<thead>
		<tr style="font-weight:bold">
			<th style="width:20px"><em>TT</em></th>
			<th><em>Tên tác giả, tên bài viết, tên tạp chí và số của tạp chí, trang đăng bài viết, năm xuất bản</em></th>
			<th align="center" style="width:90px"><em>Sản phẩm của<br/>đề tài/dự án</em></th>
			<th align="center" style="width:90px"><em>Số hiệu ISSN </em></th>
			<th style="width:50px" align="center"><em>Điểm IF</em></th>
		</tr>
	</thead>
	<tbody>
	<?php foreach ($cbgd['tap_chi_trong_nuoc'] as $i => $resDM) { ?>
		<tr align="left" valign="top">
			<td valign="top" width="20"><?php echo ($i+1) ?></td>
			<td width="550">
				<?php echo $resDM["ten_tac_gia"] ?>, 
				<?php echo $resDM["ten_bai_bao"]?>, 
				<?php echo $resDM["ten_tap_chi"] ?>, 
				<?php echo $resDM["so_tap_chi"] ?>, 
				<?php echo $resDM["trang_dang_bai_bao"] ?>, 
				<?php echo $resDM["nam_xuat_ban_tap_chi"] ?>
			</td>
			<td align="center" valign="top"><?php echo $resDM["san_pham_ma_de_tai"] ?></td>
			<td align="center" valign="top"><?php echo $resDM["isbn"]?></td>
			<td align="center" valign="top"><?php echo $resDM["ghi_chu"] ?></td>
		</tr>
	<?php }?>
	</tbody>
</table>
<div class="content-col">
	<div class="col-title first-col"><i><?php echo ($z-1). ".3"; ?></i></div>
	<div class="col-title second-col"><i>Đăng trên kỷ yếu Hội nghị Quốc tế</i></div>
</div>
<table autosize="1" width="100%" align="center" cellspacing="0" cellpadding="5" border="1" class="tableData fontcontent bordertable">
	 <thead>
	  <tr style="font-weight:bold">
		<th style="width:20px"><em>TT</em></th>
		<th align="left"><em>Tên tác giả, tên bài viết, tên Hội nghị, thời gian tổ chức, nơi tổ chức</em></th>
		<th align='center' style="width:90px"><em>Sản phẩm của<br/>đề tài/dự án</em></th>
		<th align='center' style="width:90px"><em>Số hiệu ISBN</em></th>
		<th align='center'><em>Ghi chú</em></th>
	  </tr>
	 </thead>
	 <tbody>
	 <?php foreach ($cbgd['ky_yeu_hnqt'] as $i => $resDM) { ?>
		<tr align="left" valign="top">
			<td valign="top" width="20"><?php echo ($i+1) ?></td>
			<td width="550">
				<?php echo $resDM["ten_tac_gia"] ?>, 
				<?php echo $resDM["ten_bai_bao"]?>, 
				<?php echo $resDM["ten_tap_chi"] ?>,
				<?php echo $resDM["nam_xuat_ban_tap_chi"] ?>, 
				<?php echo $resDM["city"] ?> - <?php echo $resDM["ten_quoc_gia"] ?> 
			</td>
			<td align="center" valign="top"><?php echo $resDM["san_pham_ma_de_tai"] ?></td>
			<td align="center" valign="top"><?php echo $resDM["isbn"]?></td>
			<td align="center" valign="top"><?php echo $resDM["ghi_chu"] ?></td>
		</tr>
	<?php } ?>
	</tbody>
</table>
<div class="content-col">
	<div class="col-title first-col"><i><?php echo ($z-1). ".4"; ?></i></div>
	<div class="col-title second-col"><i>Đăng trên kỷ yếu Hội nghị trong nước</i></div>
</div>
<table autosize="1" width="100%" align="center" cellspacing="0" cellpadding="5" border="1" class="tableData fontcontent bordertable">
	 <thead>
	  <tr style="font-weight:bold">
		<th style="width:20px"><em>TT</em></th>
		<th align="left"><em>Tên tác giả, tên bài viết, tên Hội nghị, thời gian tổ chức, nơi tổ chức</em></th>
		<th align='center' style="width:90px"><em>Sản phẩm của<br/>đề tài/dự án</em></th>
		<th align='center' style="width:90px"><em>Số hiệu ISBN</em></th>
		<th align='center'><em>Ghi chú</em></th>
	  </tr>
	 </thead>
	 <tbody>
	 <?php foreach ($cbgd['ky_yeu_trong_nuoc'] as $i => $resDM) { ?>
		<tr align="left" valign="top">
			<td valign="top" width="20"><?php echo ($i+1) ?></td>
			<td width="550">
				<?php echo $resDM["ten_tac_gia"] ?>, 
				<?php echo $resDM["ten_bai_bao"]?>, 
				<?php echo $resDM["ten_tap_chi"] ?>,
				<?php echo $resDM["nam_xuat_ban_tap_chi"] ?>, 
				<?php echo $resDM["city"] ?> - <?php echo $resDM["ten_quoc_gia"] ?> 
			</td>
			<td align="center" valign="top"><?php echo $resDM["san_pham_ma_de_tai"] ?></td>
			<td align="center" valign="top"><?php echo $resDM["isbn"]?></td>
			<td align="center" valign="top"><?php echo $resDM["ghi_chu"] ?></td>
		</tr>
	<?php } ?>
	 </tbody>
</table>
<div class="content-col">
	<div class="col-title first-col"><b>IV.</b></div>
	<div class="col-title second-col"><b>CÁC GIẢI THƯỞNG</b></div>
</div>
<div class="content-col">
	<div class="col-title first-col"><b><?php $z=1; echo $z++ . "."; ?></b></div>
	<div class="col-title second-col"><b>Các giải thưởng Khoa học và Công nghệ</b></div>
</div>
<table autosize="1" width="100%" align="center" cellspacing="0" cellpadding="5" border="1" class="tableData fontcontent bordertable">
	 <thead>
	  <tr style="font-weight:bold">
		<th align="left" style="width:20px"><em>TT</em></th>
		<th align="left"><em>Tên giải thưởng</em></th>
		<th align="left"><em>Nội dung giải thưởng</em></th>
		<th align="left"><em>Nơi cấp</em></th>
		<th align="left" style="width:60px"><em>Năm cấp</em></th>
	  </tr>
	 </thead>
	 <tbody>
	 <?php foreach ($cbgd['giai_thuong'] as $i => $resDM) { ?>
		<tr align="left" valign="top">";
			<td><?php echo ($i+1) ?></td>
			<td align="left"><?php echo $resDM["ten_giai_thuong"] ?></td>
			<td align="left"><?php echo $resDM["noi_dung_giai_thuong"] ?></td>
			<td align="left"><?php echo $resDM["noi_cap"] ?>, <?php echo $resDM["ten_nuoc_cap"] ?></td>
			<td align="left">$txtNamCap </td>
		</tr>
	<?php } ?>
	 </tbody>
</table>
<div class="content-col">
	<div class="col-title first-col"><b><?php echo $z++ . "."; ?></b></div>
	<div class="col-title second-col"><b>Bằng phát minh, sáng chế (patent)</b></div>
</div>
<table autosize="1" width="100%" align="center" cellspacing="0" cellpadding="5" border="1" class="tableData fontcontent bordertable">
	<thead>
		<tr style="font-weight:bold">
			<th align="left" style="width:20px"><em>TT</em></th>
			<th align="left"><em>Tên bằng</em></th>
			<th align="left" style="width:100px"><em>Sản phẩm của<br/>đề tài/dự án</em></th>
			<th align="left"><em>Số hiệu</em></th>
			<th align="left" style="width:60px"><em>Năm cấp</em></th>									
			<th align="left"><em>Nơi cấp</em></th>
			<th align="left" style="width:80px"><em>Tác giả/<br/>đồng tác giả</em></th>
		</tr>
	</thead>
	<tbody>
	<?php foreach ($cbgd['phat_minh_sang_che'] as $i => $resDM) { ?>
		<tr align="left" valign="top">";
			<td><?php echo ($i+1) ?></td>
			<td align="left"><?php echo $resDM["ten_bang"] ?></td>
			<td align="left"><?php echo $resDM["san_pham_ma_de_tai"] ?></td>
			<td align="left"><?php echo $resDM["so_hieu_bang"] ?></td>
			<td align="left"><?php echo $resDM["nam_cap"] ?> </td>
			<td align="left"><?php echo $resDM["noi_cap"] ?>, <?php echo $resDM["ten_nuoc_cap"] ?></td>
			<td align="left"><?php echo $resDM["tac_gia_chinh"] ?></td>
		</tr>
	<?php } ?>
	 </tbody>
</table>
<div class="content-col">
	<div class="col-title first-col"><b><?php echo $z++ . "."; ?></b></div>
	<div class="col-title second-col"><b>Bằng giải pháp hữu ích</b></div>
</div>
<table autosize="1" width="100%" align="center" cellspacing="0" cellpadding="5" border="1" class="tableData fontcontent bordertable">
	<thead>
		<tr style="font-weight:bold">
			<th align="left" style="width:20px"><em>TT</em></th>
			<th align="left"><em>Tên giải pháp</em></th>
			<th align="left" style="width:100px"><em>Sản phẩm của<br/>đề tài/dự án</em></th>
			<th align="left"><em>Số hiệu</em></th>
			<th align="left" style="width:60px"><em>Năm cấp</em></th>									
			<th align="left"><em>Nơi cấp</em></th>
			<th align="left" style="width:80px"><em>Tác giả/<br/>đồng tác giả</em></th>
		</tr>
	</thead>
	<tbody>
	 <?php foreach ($cbgd['bang_giai_phap'] as $key => $value) { ?>
		<tr align="left" valign="top">";
			<td><?php echo ($i+1) ?></td>
			<td align="left"><?php echo $resDM["ten_bang"] ?></td>
			<td align="left"><?php echo $resDM["san_pham_ma_de_tai"] ?></td>
			<td align="left"><?php echo $resDM["so_hieu_bang"] ?></td>
			<td align="left"><?php echo $resDM["nam_cap"] ?></td>
			<td align="left"><?php echo $resDM["noi_cap"] ?>, <?php echo $resDM["ten_nuoc_cap"] ?></td>
			<td align="left"><?php echo $resDM["tac_gia_chinh"] ?></td>
		</tr>
	<?php } ?>
	 </tbody>
</table>
<div class="content-col">
	<div class="col-title first-col"><b><?php echo $z++ . "."; ?></b></div>
	<div class="col-title second-col"><b>Ứng dụng thực tiễn và thương mại hóa kết quả nghiên cứu</b></div>
</div>
<table autosize="1" width="100%" align="center" cellspacing="0" cellpadding="5" border="1" class="tableData fontcontent bordertable">
	 <thead>
	  <tr style="font-weight:bold">
		<th align="left" style="width:20px"><em>TT</em></th>
		<th align="left"><em>Tên công nghệ/giải pháp hữu ích đã chuyển giao</em></th>
		<th align="left"><em>Hình thức, quy mô, địa chỉ áp dụng</em></th>
		<th align="center" style="width:80px"><em>Năm<br/>chuyển giao</em></th>
		<th align="center" style="width:100px"><em>Sản phẩm của<br/>đề tài/dự án</em></th>
	  </tr>
	 </thead>
	 <tbody>
	 <?php foreach ($cbgd['ung_dung_thuc_tien'] as $i => $resDM) {?>
		<tr align="left" valign="top">";
			<td><?php echo ($i+1) ?></td>
			<td align="left"><?php echo $resDM["ten_cong_nghe_gp_hu"] ?></td>
			<td align="left">
				<?php echo ($resDM["quy_mo"] != "" ? $resDM["quy_mo"].", " : "") ?>
				<?php echo ($resDM["dia_chi_ap_dung"] != "" ? $resDM["dia_chi_ap_dung"].", " : "") ?>
			</td>
			<td align="center"><?php echo $resDM["thoi_gian_cg"] ?></td>
			<td align="left"><?php echo $resDM["san_pham_ma_de_tai"] ?></td>
		</tr>
	<?php } ?>
	 </tbody>
</table>
<div class="content-col">
	<div class="col-title first-col"><b>V.</b></div>
	<div class="col-title second-col"><b>THÔNG TIN KHÁC</b></div>
</div>
<div class="content-col">
	<div class="col-title first-col"><b><?php $z=1; echo $z++ . "."; ?></b></div>
	<div class="col-title second-col">
		<b>Tham gia các chương trình trong và ngoài nước</b>
	</div>
</div>
<table autosize="1" width="100%" align="center" cellspacing="0" cellpadding="5" border="1" class="tableData fontcontent bordertable">
	 <thead>
	  <tr style="font-weight:bold">
		<th align="left" style="width:20px"><em>TT</em></th>
		<th align="left" style="width:80px"><em>Thời gian</em></th>
		<th align="left"><em>Tên chương trình</em></th>
		<th align="left"><em>Chức danh</em></th>
	  </tr>
	 </thead>
	 <tbody>
	 <?php foreach ($cbgd['nckh_tham_gia_ct'] as $i => $resDM) {?>
		<tr align="left" valign="top">";
			<td><?php echo ($i+1) ?></td>
			<td align="left"><?php echo $resDM["thoi_gian_bd"] ?><?php echo  $resDM["thoi_gian_kt"] != "" ? "-".$resDM["thoi_gian_kt"] : ""?></td>
			<td align="left"><?php echo $resDM["ten_chuong_trinh"] ?></td>
			<td align="left"><?php echo $resDM["chuc_danh"] ?></td>
		</tr>
	<?php } ?>
	 </tbody>
</table>
<div class="content-col">
	<div class="col-title first-col"><b><?php echo $z++ . "."; ?></b></div>
	<div class="col-title second-col">
		<b>Tham gia các Hiệp hội khoa học, Ban biên tập các tạp chí Khoa học, Ban tổ chức các Hội nghị về KH&CN, 
			Phản biện tạp chí khoa học, các hội thảo hội nghị quốc tế và trong nước
		</b>
	</div>
</div>
<table autosize="1" width="100%" align="center" cellspacing="0" cellpadding="5" border="1" class="tableData fontcontent bordertable">
	 <thead>
	  <tr>
		<th align="left" width="20px"><em>TT</em></th>
		<th align="left" width="80px"><em>Thời gian</em></th>
		<th align="left"><em>Tên Hiệp hội/Tạp chí/Hội nghị</em></th>
		<th align="left"><em>Chức danh</em></th>
	  </tr>
	 </thead>
	 <tbody>
	 <?php foreach ($cbgd['tham_gia_hiep_hoi'] as $i => $resDM) {?>
		<tr align="left" valign="top">
			<td><?php echo ($i+1) ?></td>
			<td align="left"><?php echo $resDM["thoi_gian_bd"] ?><?php echo  $resDM["thoi_gian_kt"] != "" ? "-".$resDM["thoi_gian_kt"] : ""?></td>
			<td align="left"><?php echo $resDM["ten_hh_tc_hn"] ?> (<?php echo $resDM["loai_desc"] ?>)</td>
			<td align="left"><?php echo $resDM["chuc_danh"] ?></td>
		</tr>
	<?php } ?>
	 </tbody>
</table>
<div class="content-col">
	<div class="col-title first-col"><b><?php echo $z++ . "."; ?></b></div>
	<div class="col-title second-col">
		<b>Tham gia làm việc tại Trường Đại học/Viện/Trung tâm nghiên cứu theo lời mời.
			 Tham gia các hội đồng tư vấn xét duyệt thẩm định đề tài nghiên cứu khoa học cấp nhà nước và trọng điểm
		</b>
	</div>
</div>
	<table autosize="1" width="100%" align="center" cellspacing="0" cellpadding="5" border="1" class="tableData fontcontent bordertable">
		<thead>
			<tr>
				<th align="left" width="20px"><em>TT</em></th>
				<th align="left" width="80px"><em>Thời gian</em></th>
				<th align="left"><em>Tên Trường Đại học/Viện/Trung tâm nghiên cứu</em></th>
				<th align="left"><em>Nội dung tham gia</em></th>
			</tr>
		</thead>
		<tbody>
		<?php foreach ($cbgd['dm_nckh_truong_vien'] as $i => $resDM) {
				if ($resDM["thoi_gian_kt"] != "") {
					$txtThoiGian = $resDM["thoi_gian_bd"] . "-" . $resDM["thoi_gian_kt"];
				} else {
					$txtThoiGian = $resDM["thoi_gian_bd"];
				}
		?>
			<tr align="left" valign="top">
				<td><?php echo ($i+1) ?></td>
				<td align="left"><?php echo $txtThoiGian ?></td>
				<td align="left"><?php echo $resDM["ten_truong_vien"] ?></td>
				<td align="left"><?php echo $resDM["noi_dung_tham_gia"] ?></td>
			</tr>
		<?php 	} ?>
		 </tbody>
	</table>
</div>
<pagebreak />
<div class="fontcontent llkh-signature">
	<div align="left" class="llkh-signature-left">
		<div style="width:300px;" align="center">
			<span><em>Tp.HCM, ngày ...... tháng ...... năm .........</em></span><br/>
			<b>Thủ trưởng Đơn vị</b><br/>
			<i>(Họ tên, đóng dấu)</i>
		</div>
	</div>
	<div align="right" class="llkh-signature-right">
		<div style="width:400px;" align="center">
			<span><em>Tp.HCM, ngày <?php echo $ngay ?> tháng <?php echo $thang ?> năm <?php echo $nam ?></em></span><br/>
			<b>Người khai</b><br/>
			<i>(Họ tên và chữ ký)</i>
			<br/><br/><br/><br/><br/><br/>
			<b><?php echo $cbgd["hotencb"] ?></b>
		</div>
	</div>
</div>