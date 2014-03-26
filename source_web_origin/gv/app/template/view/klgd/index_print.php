<?php
$help = Helper::getHelper('functions/util');

$firstRowBoMon = $listItemsBoMon[0];
$tenBoMon = $firstRowBoMon['ten_bo_mon'];
$tenKhoa = $firstRowBoMon['ten_khoa'];
foreach ($listItemsBoMon as $k => $item) { 
	if ($item['selected'] != "" && $firstRowBoMon['ma_bo_mon'] != $item['ma_bo_mon']){
		$tenBoMon = $item['ten_bo_mon'];
		$tenKhoa = $item['ten_khoa'];
		break;
	}
}
?>
<style type="text/css">
	tr.tr-cbgd td{
		border-bottom: none !important;
	}
	tr.tr-detail td{
		border-bottom: #000000 dashed 0.5px !important;
	}
	tr.alt_ td ,tr.alt td {
		border-top:none !important;
	}
	tr.tr-tong-so-tiet-qui-doi td {
		border-bottom: #000000 solid 0.5px !important;
	}
	td.td-boder-left {
		border-left: #000000 solid 0.5px !important;
	}
	td.td-boder-right {
		border-right: #000000 solid 0.5px !important;
	}
	table.tableData thead tr th {
		border-bottom: #000000 solid 0.5px !important;
		border-top:  #000000 solid 0.5px !important;
	}
	table.tableData thead tr th.th-left{
		border-left:  #000000 solid 0.5px !important;
	}
	table.tableData thead tr th.th-right{
		border-right:  #000000 solid 0.5px !important;
	}
	.iconExpandDown {
		background:transparent url('icons/details_open.png') no-repeat  bottom left;
	}
	.iconExpandUp {
		background:transparent url('icons/details_close.png') no-repeat bottom left;
	}
	#<?php echo $formKey ?>TableKLGD {
		border-color: #453821; 
		border-width: 1px; 
		border-style: solid; 
		border-collapse:collapse;
		font-size: 12px !important;
	}
	#<?php echo $formKey ?>_listview {
		font-size: 12px !important;
	}
</style>
<div id="<?php echo $formKey ?>_listview">
	<div>
		<div style="width: 220px; float: left" align="center">
			TRƯỜNG ĐẠI HỌC BÁCH KHOA TP.HCM <br/>
			<b>PHÒNG ĐÀO TẠO SAU ĐẠI HỌC</b>
		</div>
	</div>
	<div align="center" style="padding-top: 5px">
		<div align="center"><b>KHỐI LƯỢNG GIẢNG DẠY CAO HỌC CB MỜI GIẢNG HỌC KỲ <?php echo $namHoc ?></b></div>
		<div align="center"><i>(Tính theo quyết định số 1290/QĐ-ĐHBK, ngày 04/12/2008 của Hiệu Trưởng Trường Đại Học Bách Khoa)</i></div>
	</div>
	<?php if (count($listItems) > 0) {?>
	<div style="padding-top: 5px">
		<div style="width: 60px; float: left" align="left">
			KHOA:
		</div>
		<div align="left"><b><?php echo $tenKhoa ?></b></div>
	</div>
	<div style="padding-bottom: 5px">
		<div style="width: 60px; float: left" align="left">
			BỘ MÔN:
		</div>
		<div align="left"><b><?php echo $tenBoMon ?></b></div>
	</div>
	<div style="padding-bottom: 5px">
		<div style="width: 60px; float: left">&nbsp;</div>
		<div align="left">
			Đối với thực hành: 20% [Tiết qui đổi] dành cho [THB] và 80% [Tiết qui đổi] dành cho [TH]<br/>
			Đối với tiểu luận: (số tiết tiểu luận / 15) * [(01->30 học viên)*1.5 + (31->60 học viên)*1 + (>60 học viên)*0.2]<br/>
			Giải thích: [HSLĐ] = lớp đông; [HSHH] = học hàm/học vị; [HSMG] = mời giảng; [HSBS] = bổ sung;
		</div>
	</div>
	
	<div>
		<table id="<?php echo $formKey ?>TableKLGD" name="tableKLGD" width="100%" border="0" cellspacing="0" cellpadding="0" class="ui-widget ui-widget-content ui-corner-top tableData">
			<thead>
				<tr>
					<th align="center" class="th-left">Loại</th>
					<th style="width:400px;" align="left">Môn Học</th>
					<th>Lớp</th>
					<th align="center" style="width:65px;">Ngoài giờ</th>
					<th align="center" style="width:40px;" title="Số Học Viên">Số HV</th>
					<th align="center" style="width:40px;" title="Số Tiết">Tiết</th>
					<th align="center" style="width:40px;" title="Hệ Số Lớp Đông">HSLĐ</th>
					<th align="center" style="width:40px;" title="Hệ Số Học Hàm - Học Vị">HSHH</th>
					<th align="center" style="width:40px;" title="'Hệ Số Mời Giảng">HSMG</th>
					<th align="center" style="width:40px;" title="Hệ Số Bổ Sung">HSBS</th>
					<th align="right" style="width:65px;" class="ui-corner-tr th-right">Tiết QĐ&nbsp;</th>
				</tr>
			</thead>
		<tbody>
			<?php
			$booleanCheckShowPrintFull = false;
			foreach ($listItems as $i => $row) {
				$classAlt = ($i % 2) ? "alt" : "alt_";
				?>
				<tr class="fontcontent <?php  echo $classAlt ?> tr-cbgd" style="height:25px;">
					<td align="left" class="td-boder-left" valign="top">&nbsp;CBGD:</td>
					<td colspan="10" class="td-boder-right" valign="top"><b><?php echo $row["hotencb"] ?> (<?php echo $row["shcc"] ?>)</b></td>
				</tr>
				<?php
				$tietQD = 0;
				$rowViewKlgd = $row['view_klgd'];
				foreach ($rowViewKlgd as $key => $resDM) {
					$rowViewKlgd = $resDM['chi_tiet_klgd_lv_ts'];
					$classRowSelectExpand = $rowViewKlgd != null ? ' clickExpand' : '';
					$relDetail = $rowViewKlgd != null ? ' rel="'.$key.'"' : '';
					$classRowShowExpand = $rowViewKlgd != null ? ' showExpand' : '';
					$relDetailShowExpand = $rowViewKlgd != null ? ' rel="'.$key.'"' : '';
					if ($resDM['loai'] != null){
					?>
						<tr align="left" valign="middle" class="fontcontent <?php  echo $classAlt ?> tr-detail<?php echo $classRowSelectExpand ?>"<?php echo $relDetail ?> style="height:25px;">
							<td align="left" valign="top" class="td-boder-left">&nbsp;<?php echo $resDM['loai'] ?></td>
							<td valign="top">&nbsp;<?php echo $resDM["ten_mh"] ?></td>
							<td valign="top" title="<?php echo $resDM["ten_lop"] ?>">&nbsp;
								<?php 
									if (! empty($resDM["ten_lop"])){
										$countArray = explode('&', $resDM["ten_lop"]);
										if (count($countArray) > 2){
											echo $countArray[0].' &'.$countArray[1].'...';
										}else{
											echo $resDM["ten_lop"];
										}
									}
								?>
							</td>
							<td valign="top" align="center">&nbsp;<?php echo $resDM["ngoai_gio"] ?></td>
							<td valign="top" align="center">&nbsp;<?php echo $resDM["so_hv"] ?></td>
							<td valign="top" align="center">&nbsp;<?php echo $resDM["so_tiet"] ?></td>
							<td valign="top" align="center">&nbsp;<?php echo $resDM["hsld"] != 0 ? number_format($resDM["hsld"], 1, ',', '.') : '' ?></td>
							<td valign="top" align="center">&nbsp;<?php echo $resDM["hs_hh_hv"] != 0 ? number_format($resDM["hs_hh_hv"], 1, ',', '.') : '' ?></td>
							<td valign="top" align="center">&nbsp;<?php echo $resDM["hs_moi_giang"] != 0 ? number_format($resDM["hs_moi_giang"], 1, ',', '.') : '' ?></td>
							<td valign="top" align="center">&nbsp;<?php echo $resDM["hs_bo_sung"] != 0 ? number_format($resDM["hs_bo_sung"], 1, ',', '.') : '' ?></td>
							<td valign="top" align="right" class="td-boder-right">&nbsp;<?php echo number_format($resDM["tiet_qd"], 1, ',', '.') ?>&nbsp;</td>
						</tr>
					<?php
					}
					//Get view detail for TS/LV
					if($rowViewKlgd != null && $_GET['print'] == '2'){
						if ($booleanCheckShowPrintFull == false){
							$booleanCheckShowPrintFull = true;
						}
					?>
						<tr class="showhide <?php echo $classAlt ?><?php echo $classRowShowExpand ?>"<?php echo $relDetailShowExpand ?> style="<?php echo isset($_GET['print']) && $_GET['print'] == '2' ? '': 'display: none' ?>">
							<td colspan="11" align="right" valign="top" class="td-boder-right td-boder-left">
								<table cellpadding="3" style="width: 1000px" cellspacing="0">
									<thead>
									<tr class="ui-widget-header heading" style="font-color: #aaa;">
										<td><b>Mã</b></td>
										<td><b>Họ tên</b></td>
										<td><b>Phái</b></td>
										<td><b>Ngày sinh</b></td>
										<td><b>Đề tài/Luận án</b></td>
										<td align="center"><b>CTĐT</b></td>
										<td align="right"><b>Tiết QĐ</b></td>
									</tr>
									</thead>
									<tbody>
									<?php
									foreach ($rowViewKlgd as $k2 => $resNCS) {
									?>
									<tr>
										<td valign="top">&nbsp;<?php echo $resNCS["ma_hoc_vien"] ?></td>
										<td valign="top">&nbsp;<?php echo $resNCS["ho_ten"] ?></td>
										<td valign="top">&nbsp;<?php echo $resNCS["phaidesc"] ?></td>
										<td valign="top">&nbsp;<?php echo $resNCS["ngay_sinh"] ?></td>
										<td valign="top">&nbsp;<?php echo $resNCS["detai"] ?></td>
										<td valign="top" align="center">&nbsp;<?php echo $resNCS["ctdt"] ?></td>
										<td valign="top" align="right">&nbsp;<?php echo number_format($resNCS["so_tiet_qd"], 1, ',', '.') ?></td>
									</tr>
									<?php
									}
									?>
									</tbody>
								</table>
							</td>
						</tr>
						<tr class="showhide <?php echo $classAlt ?> tr-detail<?php echo $classRowShowExpand ?>"<?php echo $relDetailShowExpand ?> style="display: none">
							<td colspan="11" class="td-boder-left td-boder-right">
								&nbsp;
							</td>
						</tr>
					<?php
					}
					$tietQD += floatval($resDM["tiet_qd"]);
				}
				?>
				<tr class="fontcontent <?php  echo $classAlt ?> tr-tong-so-tiet-qui-doi" style="height:25px;">
					<td align="left" valign="top" colspan="10" class="td-boder-left"><b>&nbsp;Tổng số tiết qui đổi</b></td>
					<td valign="top" class="td-boder-right" align="right"><b><?php echo $tietQD != 0 ?  number_format($tietQD, 1, ',', '.') : '' ?>&nbsp;</b></td>
				</tr>
				<?php
			}
			?>
			<tbody>
		</table>
	</div>
	<div style="padding: 5px 0">
		<div style="float: left; width: 150px">
			<i>Ngày in: &nbsp;&nbsp;&nbsp;<?php echo date("d-m-Y") ?></i> 
		</div>
		<div style="float: right; width: 200px">
			<b>PHÒNG ĐÀO TẠO SAU ĐẠI HỌC</b>
		</div>
	</div>
	<?php } else { ?>
		<div align="center"><font color="red">Không có dữ liệu</font></div>
	<?php } ?>
</div>
