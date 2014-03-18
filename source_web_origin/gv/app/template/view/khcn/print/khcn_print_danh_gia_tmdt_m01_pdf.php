<?php

//if (!allowPermisstion(base64_decode($_SESSION['uidloginPortal']), '051', $db_conn)) {die('Truy cập bất hợp pháp');}

date_default_timezone_set('Asia/Ho_Chi_Minh');
$ngay =date("d");
$thang =date("m");
$nam =date("Y");
$z = 1;
$y = 1;
?>
<style type="text/css">
	table.table-content1 {
		width: 100%;
		border-color: #000000; 
		border-width: 1px; 
		border-style: dotted; 
		border-collapse:collapse;
	}
	
	table.table-content2 {
		width: 100%;
		border: 1px;
		border-style: solid;
		border-collapse:collapse;
	}
	
	td.table-content-dotted-below {
		border-bottom-color: #000000; 
		border-bottom-width: 1px; 
		border-bottom-style: dotted; 
		border-collapse:collapse;
	}
	td.border-right-dotted, th.border-right-dotted {
		border-right-color: #000000; 
		border-right-width: 1px; 
		border-right-style: dotted; 
		border-collapse:collapse;
	}
	.fontcontent {
		font-size: 13px;
		font-family: Arial, Helvetica, sans-serif;
		color: #000000;
		font-weight: normal;
		line-height: 1.5;
	}
	.bordertable {
		border-color: #000000; 
		border-width: 1px; 
		border-style: solid; 
		border-collapse:collapse;
	}
	tr.borderDOT, thead.borderDOT  {
		border-color: #000000; 
		border-width: 1px; 
		border-style: dotted; 
		border-collapse:collapse;
	}
	table.borderDOT {
		border-color: #000000; 
		border-width: 1px; 
		border-style: dotted; 
		border-collapse:collapse;
	}
	th.borderDOT , td.borderDOT {
		border-color: #000000; 
		border-width: 1px; 
		border-style: dotted; 
		border-collapse:collapse;
	}
	.table-content2-bottom-right{
		border-bottom-color: #000000; 
		border-bottom-width: 1px; 
		border-bottom-style: solid; 
		border-right-color: #000000; 
		border-right-width: 1px; 
		border-right-style: solid; 
		border-collapse:collapse;
	}
	.table-content2-right{
		border-right-color: #000000; 
		border-right-width: 1px; 
		border-right-style: solid; 
		border-collapse:collapse;
	}
</style>
<div style="font-family:Arial, Helvetica, sans-serif">
<table width="100%"   cellspacing="0" cellpadding="0" class="ui-corner-all shawdow tableData fontcontent" >
  <tr>
    <td valign="top"> 
		<div align="left" style="margin-top:10px">
			<img src="<?php echo Helper::getHelper('functions/util')->getGvRootURL() ?>/images/llkh/logodhqg.png" alt="" style="float:left; margin:-5px 5px 0px 5px;" width="72" height="53">
			<div align=left style="margin:10px 0px 0px 10px;" class="fontcontent">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Đại học Quốc gia<br/>Thành phố Hồ Chí Minh</div>
		</div>
    </td>
	<td valign="top" align="right">Mẫu M01</td>
  </tr>
  <tr>
    <td colspan=2 valign="top" align="center"> 
		<font style="font-size:20px;">
			<b>PHIẾU NHẬN XÉT-ĐÁNH GIÁ XÉT DUYỆT</b>
			<br>ĐỀ TÀI NCKH CẤP ĐHQG-HCM LOẠI C
		</font>
    </td>
  </tr>
</table>
<table class="fontcontent table-content1" cellpadding="0" cellspacing="0">
	<tr>
		<td class="table-content-dotted-below">
			<div align=left style="font-weight:bold">Tên đề tài (tiếng Việt):</div>
			<div align=left style=""><?php echo $detailTmdt['ten_de_tai_vn']; ?></div>
			<div align=left style=""><b>Loại hình</b> <?php echo $detailTmdt["ten_loai_hinh_nc"]; ?></div>
		</td>
	</tr>
	<tr>
		<td>
			<div align=left><b>Họ và tên người đánh giá:</b> <?php echo $detailCbgd["hotencb"]; ?></div>
			<div align=left>Cơ quan công tác:  <?php echo $detailCbgd["co_quan_cong_tac"]; ?></div>
			<div align=left>Điện thoại: <?php echo $detailCbgd["dien_thoai_cn"]; ?> Email: <?php echo $detailCbgd["email"]; ?></div>
			<div align=left>Số CMND: <?php echo $detailCbgd["so_cmnd"]; ?> hoặc MST: <?php echo $detailCbgd["ma_so_thue"]; ?></div>
			<div align=left>Số tài khoản: <?php echo $detailCbgd["so_tai_khoan"]; ?> Tại ngân hàng: <?php echo $detailCbgd["ngan_hang_mo_tk"]; ?></div>
		</td>
	</tr>
</table>
<div class="fontcontent">
<b>A. NHẬN XÉT</b>
</div>
<div class="fontcontent">
	<b>A1. Tầm quan trọng của nghiên cứu: (a) Tính cấp thiết, tính mới, tính sáng tạo
	và khả năng ứng dụng của nghiên cứu; (b) Sự phù hợp với định hướng khoa học và công nghệ đã công bố hoặc đặt hàng.</b>
</div>
<div class="fontcontent" style="margin-top:10px">
	<?php echo $detailTmdt["a1_tam_quan_trong"]; ?>
</div>
<pagebreak sheet-size="A4" />

<div class="fontcontent">
	<b>A2. Chất lượng nghiên cứu: (a) Mục tiêu, nội dung, phương pháp nghiên
	cứu phù hợp và mới để đạt được mục tiêu; (b) Đóng góp vào tri thức khoa học, có ảnh hưởng đối với xã hội; (c) Sản phẩm nghiên cứu
	phù hợp tiêu chí các loại đề tài đăng ký.</b>
</div>
<div style="margin-top:10px" class="fontcontent">
	<?php echo $detailTmdt["a2_chat_luong_nc"]; ?>
</div>
			
<div style="margin-top:15px" class="fontcontent">
	<b>A3. Năng lực nghiên cứu của chủ nhiệm và nhóm nghiên cứu; điều kiện cơ sở vật chất - kỹ thuật phục vụ nghiên cứu.</b>
</div>
<div style="margin-top:10px" class="fontcontent">
	<?php echo $detailTmdt["a3_nlnc_csvc"]; ?>
</div>
<div class="fontcontent">
	<b>A4. Kinh phí</b>
</div>
<div style="margin-top:10px">
	<table class="table-content1 fontcontent" cellpadding="0" cellspacing="0">
		<thead>
			<tr>
				<th rowspan="2" align="center" class="borderDOT">TT</th>
				<th rowspan="2" align="center" class="borderDOT">Nội dung đánh giá<br>(Căn cứ phụ lục giải trình các khoản chi)</th>
				<th colspan="3" align="center" class="borderDOT">Nhận xét</th>
			</tr>
			<tr>
				<th align="center" class="borderDOT">Cao</th>
				<th align="center" class="borderDOT">Thấp</th>
				<th align="center" class="borderDOT">Kinh phí đề nghị</th>
			</tr>
		</thead>
		<tbody>
			<?php 
			$rowNckhNoiDungKinhPhi =$detailTmdt["join_tables"]['nckh_pb_noi_dung_kinh_phi'];
			$tmp='';
			$tongkp = 0;
			foreach ($rowNckhNoiDungKinhPhi as $k => $itemNckhNoiDungKinhPhi) {
				$tongkp += $itemNckhNoiDungKinhPhi['kinh_phi_de_nghi'] != null ? floatval($itemNckhNoiDungKinhPhi['kinh_phi_de_nghi']) : 0;
				$valueShow = $itemNckhNoiDungKinhPhi['kinh_phi_de_nghi'] != null ? number_format(floatval($itemNckhNoiDungKinhPhi['kinh_phi_de_nghi']), 2, ',', '.') : '';
				?>
				<tr class="borderDOT">
					<td align="center" class="border-right-dotted"><?php echo $itemNckhNoiDungKinhPhi["stt"] ?></td>
					<td align="left" class="border-right-dotted"><?php echo $itemNckhNoiDungKinhPhi["noi_dung"] ?></td>
					<td align="center" class="border-right-dotted"><?php echo $itemNckhNoiDungKinhPhi["nhan_xet_cao"] ?></td>
					<td align="center" class="border-right-dotted"><?php echo $itemNckhNoiDungKinhPhi["nhan_xet_thap"] ?></td>
					<td align="center" align="right">
						<div style="width:50%"><?php echo $valueShow ?></div>
					</td>
				</tr>
				<?php
			}
			?>
				<tr>
					<td align="center" class="borderDOT" colspan="4"><b>Tổng kinh phí đề nghị</b> (<em>triệu đồng</em>)</td>
					<td align="right" class="borderDOT">
						<b><div style='width:50%'><?php echo $tongkp != 0 ? number_format($tongkp, 2, ',', '.') : '' ?></div></b>
					</td>
				</tr>
		</tbody>
	</table>
</div>
<div style="margin-top:10px" class="fontcontent">
	<?php echo $detailTmdt["a4_kinh_phi_nx"]; ?>
</div>
<pagebreak sheet-size="A4-L" />
<div class="fontcontent">
	<b>B. ĐÁNH GIÁ</b>
</div>
<table class="table-content2 fontcontent" cellspacing="0" cellpadding="5">
	<thead>
		<tr>
			<th rowspan="2" align="center" class="table-content2-bottom-right">TT</th>
			<th rowspan="2" align="center" class="table-content2-bottom-right">Nội dung đánh giá</th>
			<th colspan="3" align="center" class="table-content2-bottom-right">Điểm tối đa</th>
			<th rowspan="2" style="width: 110px;"  class="table-content2-bottom-right">Điểm đánh giá</th>
		</tr>
		<tr>
			<th align="center" class="table-content2-bottom-right">NCCB</th>
			<th align="center" class="table-content2-bottom-right">NCƯD</th>
			<th align="center" class="table-content2-bottom-right">NCTK</th>
		</tr>
	</thead>
	<tbody>
		<?php 
		$rowNckhNoiDungDanhGia =$detailTmdt["join_tables"]['nckh_pb_noi_dung_danh_gia'];
		$tmp='';
		$tongdiem = 0;
		$flag = $numrowspan = 0;
		$n = count($rowNckhNoiDungDanhGia);
		$logCheck = array();
		foreach ($rowNckhNoiDungDanhGia as $i => $itemNckhNoiDungDanhGia) {
			
			$td_diem = "";
			$td_stt = "";
			$stt = $itemNckhNoiDungDanhGia["stt"];
			
			//check
			$idCha = $itemNckhNoiDungDanhGia["id_cha"];
			
			$numrowspan = 1;
			if($i <= $n && $idCha != null){
				for($j = $i + 1; $j < $n; $j++){
					$item = $rowNckhNoiDungDanhGia[$j];
					$checkID = $item["id"];
					if($idCha == $item["id_cha"]){
						$numrowspan ++;
						$logCheck[] = $item['id'];
					}
				}
			}
			//var_dump($numrowspan);
			$diem = $itemNckhNoiDungDanhGia["diem_text"];
			if ($numrowspan > 1 ) {
				$td_stt = "<td class=\"table-content2-bottom-right\" align=\"left\" rowspan=\"".$numrowspan."\">".$itemNckhNoiDungDanhGia["stt"]."</td>";
				$td_diem = "<td class=\"table-content2-bottom-right\" align=center rowspan=\"".$numrowspan."\" style=\"background: #ebebeb;\">".$diem."</td>";
			}else if (in_array($itemNckhNoiDungDanhGia['id'], $logCheck)) {
				$td_diem = "";
				$td_stt = "";
			} else{
				$td_stt = "<td class=\"table-content2-bottom-right\" align=\"left\">".$itemNckhNoiDungDanhGia["stt"]."</td>";
				$td_diem = "<td class=\"table-content2-bottom-right\" align=\"center\">".$diem."</td>";
			}
			
			$tmp.="
			<tr>
				".$td_stt."
				<td class=\"table-content2-bottom-right\" align=\"left\">".$itemNckhNoiDungDanhGia["noi_dung"]."</td>
				<td class=\"table-content2-bottom-right\" align=\"center\">".$itemNckhNoiDungDanhGia["thang_diem_c_nccb"]."</td>
				<td class=\"table-content2-bottom-right\" align=\"center\">".$itemNckhNoiDungDanhGia["thang_diem_c_ncud"]."</td>
				<td class=\"table-content2-bottom-right\" align=\"center\">".$itemNckhNoiDungDanhGia["thang_diem_c_nctk"]."</td>".
				$td_diem."
			</tr>";
			if ($numrowspan>0){
				$numrowspan--;
			}
			
			$tongdiem += $itemNckhNoiDungDanhGia["diem"];
		}
			echo $tmp;
		?>
			<tr class="bordertable">
				<td align="center" colspan="2" class="table-content2-right"><b>Tổng cộng</b></td>
				<td align="center" class="table-content2-right"><b>100</b></td>
				<td align="center" class="table-content2-right"><b>100</b></td>
				<td align="center" class="table-content2-right"><b>100</b></td>
				<td align="center" class="table-content2-right"><b><?php echo $tongdiem?>/100</b></td>
			</tr>
	</tbody>
</table>
<br>
<br>
<table class="borderDOT fontcontent" cellpadding="5" cellspacing="0">
	<thead>
		<tr class="borderDOT">
			<th align="center" class="border-right-dotted">Xếp loại</th>
			<th align="center">Tổng số điểm đánh giá</th>
		</tr>
	</thead>
	<tbody>
		<tr class="borderDOT">
			<td align="center" class="border-right-dotted">I</td>
			<td align="center">Từ 86 điểm trở lên</td>
		</tr>
		<tr class="borderDOT">
			<td align="center" class="border-right-dotted">II</td>
			<td align="center">Từ 70 đến 85 điểm</td>
		</tr>
		<tr class="borderDOT">
			<td align="center" class="border-right-dotted">II (Không đạt)</td>
			<td align="center">Dưới 70 điểm</td>
		</tr>
	</tbody>
</table>
	
	<pagebreak sheet-size="A4" />
	<div>
		<b>C. KẾT LUẬN</b>
	</div>
	<div style="margin-top:10px" class='fontcontent' >
		<?php echo $detailTmdt["c_ket_luan"]; ?>
	</div>
	
	<table width="100%" class="fontcontent" style="margin-top: 15px">
		<tr>
			<td align=left valign=top width=50% >
				Cam kết: Người đánh giá cam kết thực hiện đánh giá khách quan, bảo mật thông tin đánh giá.
			</td>
			<td align=right width=50%>
				<div style="width:100%;" align="center">
					<span><em>Ngày <?php echo $ngay ?> tháng <?php echo $thang ?> năm <?php echo $nam ?></em></span><br>
					<b>Người đánh giá</b>
					<br><br><br><br>
					<b><?php echo $detailCbgd["hotencb"]; ?></b>
				</div>
			</td>
		</tr>
	</table>
</div>