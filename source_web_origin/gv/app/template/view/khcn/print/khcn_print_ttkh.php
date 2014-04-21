<?php
$a = $_POST['a'];

$macb = $_SESSION['macb'];
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
</style>
<div class="content-col llkh-first-content">
	<div class="col-title first-row-col" align="center">
		ĐẠI HỌC QUỐC GIA TP.HCM<br/>
		<b>TRƯỜNG ĐẠI HỌC BÁCH KHOA</b><br/>
		-------------
	</div>
	<div class="col-title second-row-col" align="center">
		<b>CỘNG HÒA XÃ HỘI CHỦ NGHĨA VIỆT NAM<br/>Độc lập - Tự do - Hạnh phúc</b><br/>
		-------------
	</div>
</div>
<div class="content-col llkh-first-content">
	<div class="col-title"><b>THÔNG TIN KHOA HỌC</b></div>
	<div class="col-title">
		(Dành cho cán bộ tham gia đào tạo SĐH tại Trường Đại học Bách Khoa, Đại học Quốc gia Tp.HCM)
	</div>
</div>
<div class="content-col">
	<div class="col-title first-col"><?php echo $z++ . "." ?></div>
	<div class="col-title second-col">Họ và tên: <?php echo $cbgd["ho"]. " " .$cbgd["ten"]; ?></div>
</div>
<div class="content-col">
	<div class="col-title first-col"><?php echo $z++ . "." ?></div>
	<div class="col-title second-col">Ngày tháng năm sinh: <?php echo $cbgd["ngay_sinh"]; ?></div>
</div>
<div class="content-col">
	<div class="col-title first-col"><?php echo $z++ . "." ?></div>
	<div class="col-title second-col">
		Khoa: <?php echo $cbgd["ten_khoa"]; ?>, Bộ môn: <?php echo $cbgd["ten_bo_mon"]; ?>
	</div>
</div>
<div class="content-col">
	<div class="col-title first-col"><?php echo $z++ . "." ?></div>
	<div class="col-title second-col">Điện thoại liên hệ: <?php echo $cbgd["dien_thoai"]." - ".$cbgd["dien_thoai_cn"];  ?></div>
</div>
<div class="content-col">
	<div class="col-title first-col"><?php echo $z++ . "." ?></div>
	<div class="col-title second-col">Email: <?php echo $cbgd["email"]." - ".$cbgd["email_2"]; ?></div>
</div>
<div class="content-col">
	<div class="col-title first-col"><?php echo $z++ . "." ?></div>
	<div class="col-title second-col">
	Học vị: <?php switch ($cbgd["ma_hoc_vi"]) {
					case "TSK": 
						echo "Tiến sĩ khoa học"; 
						break;
					case "TS": 
						echo "Tiến sĩ"; 
						break;
					case "TH": 
						echo "Thạc sĩ"; 
						break;
					case "CN": 
						echo "Cử nhân"; 
						break;
					case "KS": 
						echo "Kỹ sư"; 
						break;
					default: 
						break;
				} ?>
	</div>
</div>
<div class="content-col">
	<div class="col-title first-col"><?php echo $z++ . "." ?></div>
	<div class="col-title second-col">
		Nước tốt nghiệp: <?php echo $cbgd["ten_quoc_gia_hv"];?>
	</div>
</div>
<div class="content-col">
	<div class="col-title first-col"><?php echo $z++ . "." ?></div>
	<div class="col-title second-col">
		Ngành: <?php if ($cbgd["fk_nganh"]== '99999999'){
						echo $cbgd["nganh_khac"];
					} else {
						echo $cbgd["nk_ten_nganh"];
					}
				?>
	</div>
</div>
<div class="content-col">
	<div class="col-title first-col"><?php echo $z++ . "." ?></div>
	<div class="col-title second-col">
		Chuyên ngành: <?php echo $cbgd["chuyen_nganh"];?>
	</div>
</div>
<div class="content-col">
	<div class="col-title first-col"><?php echo $z++ . "." ?></div>
	<div class="col-title second-col">
		Chức danh khoa học: <?php echo $cbgd["ten_hoc_ham"] != '' ? $cbgd["ten_hoc_ham"].", Năm phong:".$cbgd["nam_phong_hoc_ham"] : '';?>
	</div>
</div> 
<div class="content-col">
	<div class="col-title first-col"><?php echo $z++ . "." ?></div>
	<div class="col-title second-col">
		Lĩnh vực chuyên môn hiện tại: <?php echo $cbgd["chuyen_mon"];  ?>
	</div>
</div>
<div class="content-col">
	<div class="col-title first-col"><?php echo $z++ . "." ?></div>
	<div class="col-title second-col">
		Các hướng nghiên cứu chính:
		<?php foreach ($cbgd['dm_huong_de_tai'] as $k => $resDM) {?>
		<br>-&nbsp;&nbsp;<?php echo $resDM['ten_de_tai'] ?>
		<?php } ?>
	</div>
</div>
<div class="content-col">
	<div class="col-title first-col"><?php echo $z++ . "." ?></div>
	<div class="col-title second-col">
		Số LATS đã hướng dẫn thành công/đang hướng dẫn tại trường Đại học Bách khoa Tp.HCM (từ năm 2004):
		<b><?php echo $cbgd["la_thanh_cong"]?></b>/<b><?php echo $cbgd["la_dang_huong_dan"]?></b>
	</div>
</div>
<div class="content-col">
	<div class="col-title first-col"><?php echo $z++ . "." ?></div>
	<div class="col-title second-col">
		Số LVThS đã hướng dẫn thành công tại trường Đại học Bách khoa Tp.HCM (từ năm 2004): <b><?php echo $cbgd['huong_dan_th'] ?></b>
	</div>
</div>
<div class="content-col">
	<div class="col-title first-col"><?php echo $z++ . "." ?></div>
	<div class="col-title second-col">
		Giảng dạy các môn học Sau đại học:
		<?php foreach ($cbgd['mon_giang_day'] as $k => $resDM) { ?>
		<br>-&nbsp;&nbsp;<?php echo $resDM["ten_mon_hoc"]." ".$resDM["mon_giang_day_mon_nganh"] ?>
		<?php } ?>
	</div>
</div>
<div class="content-col">
	<div class="col-title first-col"><?php echo $z++ . "." ?></div>
	<div class="col-title second-col">
		Các công trình khoa học đã công bố trong 3 năm gần đây:<br>
		<b>Đề tài khoa học</b>
	</div>
</div>
<div class="content-col">
	<div class="col-title first-col">&nbsp;</div>
	<div class="col-title second-col">
		<table autosize="1" width="100%" align="center" cellspacing="0" cellpadding="0" border="1" class="bordertable fontcontent">
			<thead>
				<tr class="heading">
					<th width="15px" align="center"><em>TT</em></th>
					<th align="center"><em>Các đề tài, dự án, nghiên cứu khoa học</em></th>
					<th width="70px" align="center"><em>Thời gian</em></th>
					<th width="63px" align="center"><em>Chủ nhiệm</em></th>
					<th width="86px" align="left"><em>Cấp quản lý</em></th>
					<th width="70px" align="center"><em>Nghiệm thu</em></th>
				</tr>
			</thead>
			<tbody>
			<?php foreach ($cbgd['dm_de_tai_khoa_hoc'] as $i => $resDM) {?>
				<tr align="left" valign="top">
					<td><?php echo ($i+1) ?></td>
					<td><?php echo $resDM["ten_de_tai"] ?></td>
					<td align="center" ><?php echo $resDM["nam_bat_dau"] ?>-<?php echo $resDM["nam_ket_thuc"]?></td>
					<td align="center"><?php echo $resDM["tham_gia"] ?></td>
					<td><?php echo $resDM["ten_cap"] ?></td>
					<td align="center"><b><?php echo $resDM["tt_nghiem_thu"] ?></b></td>
				</tr>
			<?php } ?>
			</tbody>
		</table>
	</div>
</div>
<div class="content-col">
	<div class="col-title first-col">&nbsp;</div>
	<div class="col-title second-col">
		<b>Bài báo tạp chí/hội nghị khoa học đã công bố</b>
	</div>
</div>
<div class="content-col">
	<div class="col-title first-col">&nbsp;</div>
	<div class="col-title second-col">
		<table width="100%" align="center" border="1" cellspacing="0" cellpadding="0" class="bordertable fontcontent">
			<thead>
				<tr>
					<th width="20px">TT</th>
					<th align="left">Các bài báo, tạp chí, hội nghị khoa học</th>
				</tr>
			</thead>
			<tbody>
			<?php
				$loaictT='';
				foreach ($cbgd['dm_bb_nckh_tckh'] as $i => $resDM) {
					if ($resDM["loai_cong_trinh"] == "BQ"){
						$loaitc = "Tạp chí quốc tế";
						$isbn = "-ISBN/ISSN: {$resDM['ISBN'][$i]}";
					}else if ($resDM["loai_cong_trinh"] == "BT"){
						$loaitc = "Tạp chí trong nước";
						$isbn = "-ISBN/ISSN: {$resDM['ISBN'][$i]}";
					}else if ($resDM["loai_cong_trinh"] == "HQ"){
						$loaitc = "Hội nghị quốc tế";
						$isbn = "";
					}else if ($resDM["loai_cong_trinh"] == "HT"){
						$loaitc = "Hội nghị trong nước";
						$isbn = "";
					}
					if ($loaictT!=$loaitc){
						echo "<tr><td colspan=2 align=left><b>".$loaitc."</b></td></tr>";
						$loaictT=$loaitc;
					}
					?>
									
					<tr align="left" valign="top" >
						<td valign="top" width="15px"><?php echo ($i+1) ?></td>
						<td><?php echo $resDM["ten_tac_gia"]?> <?php echo $resDM["ten_bai_bao"]?>
							<i><?php echo $resDM["ten_tap_chi"]?></i> - <?php $resDM["city"]?>
							<b><?php echo $resDM["so_tap_chi"] ?></b>,
							<?php echo $resDM["trang_dang_bai_bao"]?> (<?php echo $resDM["nam_xuat_ban_tap_chi"] ?>) <?php echo $isbn ?>
						</td>					
					</tr>
				<?php } ?>
			</tbody>
		</table>
	</div>
</div>
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
			<span>
				<em>Tp.HCM, ngày <?php echo $ngay ?> tháng <?php echo $thang ?> năm <?php echo $nam ?></em>
			</span>
			<br/>
			<b>Người khai ký tên<br/><br/><br/><br/><br/><br/>
			<?php echo $cbgd["hotencb"]; ?>
			</b>
		</div>
	</div>
</div>