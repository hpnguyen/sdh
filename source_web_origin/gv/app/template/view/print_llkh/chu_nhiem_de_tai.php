<?php
$ngay =date("d");
$thang =date("m");
$nam =date("Y");
$z = 1;
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
	.bordertable-dotted, table.bordertable-dotted, table.bordertable-dotted thead tr th, table.bordertable-dotted tbody tr td {
		border-color: #000000; 
		border-width: 1px; 
		border-style:dotted; 
		border-collapse:collapse;
	}
	
	.the-first-row {
		border-bottom-color: #000000; 
		border-bottom-width: 1px; 
		border-bottom-style: solid; 
		border-top-color: #000000; 
		border-top-width: 1px; 
		border-top-style: solid;
		border-left-color: #000000; 
		border-left-width: 1px; 
		border-left-style: solid;
		border-right-color: #000000; 
		border-right-width: 1px; 
		border-right-style: solid;
		border-collapse:collapse;
		display: inline-block;
		padding: 3px 0;
	}
	.the-next-row {
		border-bottom-color: #000000; 
		border-bottom-width: 1px; 
		border-bottom-style: solid; 
		border-left-color: #000000; 
		border-left-width: 1px; 
		border-left-style: solid;
		border-right-color: #000000; 
		border-right-width: 1px; 
		border-right-style: solid;
		border-collapse:collapse;
		display: inline-block;
		padding: 3px 0;
	}
	.header-second-col {
		width: 97%;
	}
	.col-title{
		padding: 5px 0;
	}
	.col-title-header {
		font-size: 20px;
		padding-bottom: 20px;
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
		width: 3.5%;
		float: left;
		text-align: left;
		display: inline-block;
		vertical-align: top;
		padding-left:2px;
		font-weight: bold; 
	}
	.second-col {
		width: 96%;
		float: left;
		display: inline-block;
		vertical-align: top;
	}
	.first-row-col {
		width: 50%;
		float: left;
		display: inline-block;
		vertical-align: top;
	}
	.second-row-col {
		width: 50%;
		float: left;
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
		text-align: center;
	}
	.llkh-image {
		height: 240px;
		min-height: 240px;
		max-height: 240px;
	}
	table thead tr th {
		text-align: center;
	}
	.row-special-padding {
		padding: 7px;
	}
</style>
<div class="content-col llkh-first-content">
	<div class="col-title col-title-header" align="center">LÝ LỊCH KHOA HỌC CỦA CHỦ NHIỆM ĐỀ TÀI, DỰ ÁN SXTN</div>
</div>
<div class="content-col the-first-row">
	<div class="col-title first-col"><?php echo $z++ . "." ?></div>
	<div class="col-title second-col">
		<b>Họ và tên:</b> <?php echo $cbgd["ho"]?> <?php echo $cbgd["ten"] ?>
	</div>
</div>
<div class="content-col the-next-row">
	<div class="col-title first-col"><?php echo $z++ . "." ?></div>
	<div class="col-title second-col">
		<b>Ngày sinh:</b>
		<?php echo $cbgd["ngay_sinh"] ?> &nbsp; &nbsp; &nbsp; <b>
		<?php echo $z++ . "."; ?> Nam/Nữ:</b> <?php echo $cbgd["phai_desc"]; ?>
	</div>
</div>
<div class="content-col the-next-row">
	<div class="col-title first-col"><?php echo $z++ . "." ?></div>
	<div class="col-title second-col">
		<b>Chức danh khoa học:</b>
		<?php echo $cbgd["ten_hoc_ham"];?>
		<?php if ($cbgd["MA_HOC_HAM"][0]=='GS' || $cbgd["MA_HOC_HAM"][0]=='PGS' ) { ?>
		&nbsp; &nbsp; &nbsp; <b>Năm được phong chức danh KH:</b> <?php echo $cbgd["nam_phong_hoc_ham"] ?>
		<?php } ?>
		<br>
		<b>Học vị:</b>
		<?php echo $cbgd["ten_hv"];?> &nbsp;&nbsp;&nbsp; 
		<b>Năm đạt học vị:</b> <?php echo $cbgd["nam_dat_hoc_vi"];?>
	</div>
</div>
<div class="content-col the-next-row">
	<div class="col-title first-col"><?php echo $z++ . "." ?></div>
	<div class="col-title second-col">
		<b>Chức danh nghiên cứu:</b><br>
		<b>Chức vụ:</b>
		<?php echo $cbgd["chuc_danh_nghien_cuu"]. (($cbgd["ten_chuc_vu"] !='') ? $cbgd["ten_chuc_vu"]. ", " .$cbgd["ten_bo_mon_ql"] : '');?> 
	</div>
</div>
<div class="content-col the-next-row">
	<div class="col-title first-col"><?php echo $z++ . "." ?></div>
	<div class="col-title second-col">
		<b>Địa chỉ nhà riêng:</b> <?php echo $cbgd["dia_chi_rieng"]; ?>
	</div>
</div>
<div class="content-col the-next-row">
	<div class="col-title first-col"><?php echo $z++ . "." ?></div>
	<div class="col-title second-col">
		<b>Điện thoại:</b>
		<br><b>Cơ quan:</b> <?php echo $cbgd["dien_thoai"];?> <b> ; Di động: </b><?php echo $cbgd["dien_thoai_cn"];?>
		<br><b>Fax</b><?php echo $cbgd["fax"];?> <b> ; E-mail: </b><?php echo $cbgd["email"];?>
	</div>
</div>
<div class="content-col the-next-row">
	<div class="col-title first-col"><?php echo $z++ . "." ?></div>
	<div class="col-title second-col">
		<b>Cơ quan - nơi làm việc của cá nhân đăng ký chủ nhiệm đề tài, Dự án:</b>
		<br>Tên người lãnh đạo Cơ quan: <?php echo $cbgd["ten_nguoi_lanh_dao_cq"];?>
		<br>Điện thoại người Lãnh đạo Cơ quan: <?php echo $cbgd["dien_thoai_lanh_dao_cq"];?>
		<br>Địa chỉ Cơ quan: <?php echo $cbgd["dia_chi"];?>
		</div>
	</div>
</div>
<div class="content-col the-next-row">
	<div class="col-title first-col"><?php echo $z++ . "." ?></div>
	<div class="col-title second-col">
		<b>Quá trình đào tạo</b>
	</div>
	<div class="content-col row-special-padding" align="center">
		<table autosize="1" width="98%" align="center" cellspacing="0" cellpadding="5" border="0" class="bordertable-dotted">
			<thead>
				<tr>
					<th align="left" width="100px"><em>Bậc đào tạo</em></th>
					<th align="left"><em>Nơi đào tạo</em></th>
					<th align="left"><em>Chuyên môn</em></th>
					<th align="center"><em>Năm tốt nghiệp</em></th>
				</tr>
			</thead>
			<tbody >
				<?php foreach ($cbgd['dm_qua_trinh_dao_tao'] as $i => $resDM) { ?>
						<tr>
							<td align="left"><?php echo $resDM["ten_bac"]?></td>
							<td align="left"><?php echo $resDM["noi_dao_tao"]?>, <?php echo $resDM["ten_quoc_gia"] ?></td>
							<td align="left">
								<?php  if ($resDM["fk_nganh"] == "99999999"){
									echo  $resDM["nganh_khac"];
								}else{
									echo $resDM["ten_nganh"];
								} ?>
							</td>
							<td align="center"><?php echo $resDM["thoi_gian_tn"] ?></td>
						</tr>
				<?php } ?>
			</tbody>
		</table>
	</div>
</div>
<div class="content-col the-next-row">
	<div class="col-title first-col"><?php echo $z++ . "." ?></div>
	<div class="col-title second-col">
		<b>Quá trình công tác</b>
	</div>
	<div class="content-col row-special-padding" align="center">
		<table autosize="1" width="98%" align="center" cellspacing="0" cellpadding="5" border="0" class="bordertable-dotted">
			<thead>
				<tr>
					<th align="left" width="120px"><em>Thời gian</em></th>
					<th align="left" width="120px"><em>Vị trí công tác</em></th>
					<th align="left"><em>Cơ quan công tác</em></th>
					<th align="center"><em>Địa chỉ Cơ quan</em></th>
				</tr>
			</thead>
			<tbody >
				<?php foreach ($cbgd['dm_qua_trinh_cong_tac'] as $i => $resDM) { ?>
						<tr>
							<td align="left">
							<?php if ($txtNamKT==''){
									echo "Từ <b>".$resDM["thoi_gian_bd"]."</b> đến nay";
								}else {
									echo "Từ <b>".$resDM["thoi_gian_bd"]."</b> đến <b>".$resDM["thoi_gian_kt"]."</b>";
								}?>
							</td>
							<td align="left"><?php echo $resDM["ten_chuc_vu"]?></td>
							<td align="left"><?php echo  $resDM["noi_cong_tac"]?></td>
							<td align="center"><?php echo $resDM["dia_chi_co_quan"] ?></td>
						</tr>
				<?php } ?>
			</tbody>
		</table>
	</div>
</div>
<div class="content-col the-next-row">
	<div class="col-title first-col"><?php echo $z++ . "." ?></div>
	<div class="col-title second-col">
		<b>Các công trình công bố chủ yếu</b>
		<br/> (Liệt kê tối đa 05 công trình tiêu biểu đã công bố liên quan đến đề tài, dự án tuyển chọn trong 5 năm gần nhất)
	</div>
	<div class="content-col row-special-padding" align="center">
		<table autosize="1" width="98%" align="center" cellspacing="0" cellpadding="5" border="0" class="bordertable-dotted">
			<thead>
				<tr>
					<th align="center" valign="top"><em>TT</em></th>
					<th align="left" valign="top"><em>Tên công trình<br/><span style="font-weight:normal">(bài báo, công trình...)</span></em></th>
					<th align="center" valign="top"><em>Tác giả công trình</em></th>
					<th align="left" valign="top"><em>Nơi công bố<br/><span style="font-weight:normal">(tên tạp chí đã đăng công trình)</span></em></th>
					<th align="center" valign="top"><em>Năm công bố</em></th>
				</tr>
			</thead>
			<tbody>
				<?php foreach ($cbgd['dm_qua_trinh_cong_trinh'] as $i => $resDM) {?>
				<tr>
					<td align="center" valign="top"><?php echo ($i+1) ?></td>
					<td><?php echo $resDM["ten_bai_bao"] ?></td>
					<td align="center"><?php echo $resDM["ten_loai_tac_gia"]?></td>
					<td align="left" valign="top"><?php echo $resDM["ten_tap_chi"] ?></td>
					<td align="center" valign="top"><?php echo $resDM["nam_xuat_ban_tap_chi"] ?></td>
				</tr>
				<?php } ?>
			</tbody>
		</table>
	</div>
</div>
<div class="content-col the-next-row">
	<div class="col-title first-col"><?php echo $z++ . "." ?></div>
	<div class="col-title second-col">
		<b>Số lượng văn bằng bảo hộ sở hữu trí tuệ đã được cấp</b>
		<br/> (liên quan đến đề tài, dự án tuyển chọn - nếu có)
	</div>
	<div class="content-col row-special-padding" align="center">
		<table autosize="1" width="98%" align="center" cellspacing="0" cellpadding="5" border="0" class="bordertable-dotted">
			<thead>
				<tr>
					<th align="center" width="20px" valign="top"><em>TT</em></th>
					<th align="left" valign="top"><em>Tên và nội dung văn bằng</em></th>
					<th align="center" valign="top"><em>Năm cấp văn bằng</em></th>
				</tr>
			</thead>
			<tbody>
				<?php foreach ($cbgd['dm_bang_sang_che'] as $i => $resDM) {?>
				<tr>
					<td align="center" valign="top"><?php echo ($i+1) ?></td>
					<td align="left"><?php echo $resDM["ten_bang"] ?></td>
					<td align="center"><?php echo $resDM["nam_cap"]?></td>
				</tr>
				<?php } ?>
			</tbody>
		</table>
	</div>
</div>
<div class="content-col the-next-row">
	<div class="col-title first-col"><?php echo $z++ . "." ?></div>
	<div class="col-title second-col">
		<b>Số công trình được áp dụng trong thực tiễn</b>
		<br/> (liên quan đến đề tài, dự án tuyển chọn - nếu có)
	</div>
	<div class="content-col row-special-padding" align="center">
		<table autosize="1" width="98%" align="center" cellspacing="0" cellpadding="5" border="0" class="bordertable-dotted">
			<thead>
				<tr>
					<th align="center" width="20px" valign="top"><em>TT</em></th>
					<th align="left" valign="top"><em>Tên công trình</em></th>
					<th align="center" valign="top"><em>Hình thức, quy mô, địa chỉ áp dụng</em></th>
					<th align="center" width="80px" valign="top">
						<em>Thời gian<br/><span style="font-weight:normal">(bắt đầu - kết thúc)</span></em>
					</th>
				</tr>
			</thead>
			<tbody>
				<?php foreach ($cbgd['dm_ncud'] as $i => $resDM) {?>
				<tr>
					<td align="center" valign="top"><?php echo ($i+1) ?></td>
					<td align="left"><?php echo $resDM["ten_cong_nghe_gp_hu"] ?></td>
					<td align="left">
					<?php
					$txthtqmdc = "";
					if ($resDM["hinh_thuc"] != ""){
						$txthtqmdc .= (($txthtqmdc != '') ? ", " : "" ).$resDM["hinh_thuc"];
					}
					if ($resDM["quy_mo"] != ""){
						$txthtqmdc .= (($txthtqmdc != '') ? ", " : "" ).$resDM["quy_mo"];
					}
					if ($resDM["dia_chi_ap_dung"] != ""){
						$txthtqmdc .= (($txthtqmdc != '') ? ", " : "" ).$resDM["dia_chi_ap_dung"];
					}
					echo $txthtqmdc;
					?>
					</td>
					<td align="center">
					<?php 
					if ($resDM["thoi_gian_kt "] != ''){
						echo $resDM["thoi_gian_bd"]." - ".$resDM["thoi_gian_kt "];
					} else {
						echo $resDM["thoi_gian_bd"];
					}
					?>
					</td>
				</tr>
				<?php } ?>
			</tbody>
		</table>
	</div>
</div>
<div class="content-col the-next-row">
	<div class="col-title first-col"><?php echo $z++ . "." ?></div>
	<div class="col-title second-col">
		<b>Các đề tài, dự án, nhiệm vụ khác đã chủ trì hoặc tham gia</b>
		<br/> (trong 5 năm gần đây thuộc lĩnh vực nghiên cứu của đề tài, dự án tuyển chọn - nếu có)
	</div>
	<div class="content-col row-special-padding" align="center">
		<table autosize="1" width="98%" align="center" cellspacing="0" cellpadding="5" border="0" class="bordertable-dotted">
			<thead>
				<tr>
					<th align="left" valign="top"><em>Tên đề tài, dự án, nhiệm vụ khác đã chủ trì</em></th>
					<th align="center" width="120px" valign="top"><em>Thời gian <br/><b>(bắt đầu - kết thúc)</b></em></th>
					<th align="center" width="130px" valign="top"><em>Thuộc chương trình <br/><b>(nếu có)</b></em></th>
					<th align="center" valign="top"><em>Tình trạng đề tài<br/><b>(đã nghiệm thu, chưa nghiệm thu)</b></em></th>
				</tr>
			</thead>
			<tbody>
				<?php foreach ($cbgd['dm_de_an_de_tai'] as $i => $resDM) {?>
				<tr>
					<td align="left" valign="top"><?php echo $resDM["ten_de_tai"] ?></td>
					<td align="center"> <?php echo $resDM["nam_bat_dau"] ?>
					<?php if ($resDM["nam_ket_thuc"]!="") {
						echo "-".$resDM["nam_ket_thuc"];
					} 
					?>
					</td>
					<td align="center"><?php echo $resDM["thuoc_chuong_trinh"] ?></td>
					<td align="center"><?php echo $resDM["tt_nghiem_thu"] ?></td>
				</tr>
				<?php } ?>
			</tbody>
			<thead>
				<tr>
					<th align="left" valign="top"><em>Tên đề tài, dự án, nhiệm vụ khác đã tham gia</em></th>
					<th align="center" width="120px" valign="top"><em>Thời gian <br/><b>(bắt đầu - kết thúc)</b></em></th>
					<th align="center" width="130px" valign="top"><em>Thuộc chương trình <br/><b>(nếu có)</b></em></th>
					<th align="center" valign="top"><em>Tình trạng đề tài<br/><b>(đã nghiệm thu, chưa nghiệm thu)</b></em></th>
				</tr>
			</thead>
			<tbody>
				<?php foreach ($cbgd['dm_de_an_de_tai2'] as $i => $resDM) {?>
				<tr>
					<td align="left" valign="top"><?php echo $resDM["ten_de_tai"] ?></td>
					<td align="center"> <?php echo $resDM["nam_bat_dau"] ?>
					<?php if ($resDM["nam_ket_thuc"]!="") {
						echo "-".$resDM["nam_ket_thuc"];
					} 
					?>
					</td>
					<td align="center"><?php echo $resDM["thuoc_chuong_trinh"] ?></td>
					<td align="center"><?php echo $resDM["tt_nghiem_thu"] ?></td>
				</tr>
				<?php } ?>
			</tbody>
		</table>
	</div>
</div>
<div class="content-col the-next-row">
	<div class="col-title first-col"><?php echo $z++ . "." ?></div>
	<div class="col-title second-col">
		<b>Giải thưởng</b>
		<br/> (về KH&CN, về chất lượng sản phẩm,... liên quan đến đề tài, dự án tuyển chọn - nếu có)
	</div>
	<div class="content-col row-special-padding" align="center">
		<table autosize="1" width="98%" align="center" cellspacing="0" cellpadding="5" border="0" class="bordertable-dotted">
			<thead>
				<tr>
					<th align="center" width="20px" valign="top"><em>TT</em></th>
					<th align="left" valign="top"><em>Hình thức và nội dung giải thưởng</em></th>
					<th align="center" valign="top"><em>Năm tặng thưởng</em></th>
				</tr>
			</thead>
			<tbody>
				<?php foreach ($cbgd['dm_giai_thuong'] as $i => $resDM) {?>
				<tr>
					<td align="center" valign="top"><?php echo ($i+1) ?></td>
					<td align="left"><?php echo $resDM["noi_dung_giai_thuong"] ?></td>
					<td align="center"><?php echo $resDM["nam_cap"]?></td>
				</tr>
				<?php } ?>
			</tbody>
		</table>
	</div>
</div>
<div class="content-col the-next-row">
	<div class="col-title first-col"><?php echo $z++ . "." ?></div>
	<div class="col-title second-col">
		<b>Thành tựu hoạt động KH&CN và sản xuất kinh doanh khác</b>
		<br/> (liên quan đến đề tài, dự án tuyển chọn - nếu có)
	</div>
	<div class="content-col row-special-padding" align="center">
		<table autosize="1" width="98%" align="center" cellspacing="0" cellpadding="5" border="0" class="bordertable-dotted">
			<thead>
				<tr>
					<th align="center" valign="top"><em>Năm</em></th>
					<th align="left" valign="top"><em>Nội dung</em></th>
				</tr>
			</thead>
			<tbody>
				<?php foreach ($cbgd['dm_thanh_tuu_khcn'] as $i => $resDM) {?>
				<tr>
					<td align="center"><?php echo $resDM["nam"] ?></td>
					<td><?php echo $resDM["thanh_tuu_khcn"]?></td>
				</tr>
				<?php } ?>
			</tbody>
		</table>
	</div>
</div>
<div class="fontcontent llkh-signature">
	<div align="left" class="llkh-signature-left">
		<div style="width:300px;" align="center">
			<b>Tổ chức - nơi làm việc của cá nhân đăng ký chủ nhiệm đề tài, dự án SXTN</b><br/>
			Đơn vị đồng ý và sẽ dành thời gian cần thiết để <?php echo $cbgd["title"]." ".$cbgd["hotencb"]; ?> chủ trì thực hiện đề tài, dự án
			<br/>
			(Xác nhận và đóng dấu)
		</div>
	</div>
	<div align="right" class="llkh-signature-right">
		<div style="width:400px;" align="center">
			<span><em>Tp.HCM, ngày <?php echo $ngay ?> tháng <?php echo $thang ?> năm <?php echo $nam ?></em></span><br/>
			<b>Cá nhân đăng ký chủ nhiệm đề tài, dự án SXTN</b><br/>
			(Họ tên và chữ ký)
			<br/><br/><br/><br/><br/><br/>
			<b><?php echo $cbgd["hotencb"]; ?></b>
		</div>
	</div>
</div>