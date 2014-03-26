<?php
$help = Helper::getHelper('functions/util');
$gvURL = $help->getGvRootURL();
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
		border-bottom: #453821 solid 0.5px !important;
	}
	td.td-boder-left {
		border-left: #453821 solid 0.5px !important;
	}
	td.td-boder-right {
		border-right: #453821 solid 0.5px !important;
	}
	table.tableData thead tr th {
		border-bottom: #453821 solid 0.5px !important;
		border-top:  #453821 solid 0.5px !important;
	}
	table.tableData thead tr th.th-left{
		border-left:  #453821 solid 0.5px !important;
	}
	table.tableData thead tr th.th-right{
		border-right:  #453821 solid 0.5px !important;
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
		border-style: dotted; 
		border-collapse:collapse;
	}
	.fancyLoadingStatus {
		font-size: 13px;
		display: none;
	}
	.fancybox-outer, .fancybox-inner {
		height: 50px;
	}
</style>
<div id="<?php echo $formKey ?>_listview">
	<div>
		<div style="<?php echo isset($_GET['print']) ? 'display:none' : '' ?>">
		<table width="100%" border="0" align="center" cellpadding="5"  cellspacing="0" class="ui-widget ui-widget-content ui-corner-all">
			<tr>
				<td style="width:315px;">
					Bộ môn 
					<select id="<?php echo $formKey ?>_ma_bo_mon" name="data[ma_bo_mon]" style="width: 227px"<?php echo $viewSelectBoMon == false ? ' disabled="disabled"' : '' ?>>
					<?php 
					$firstRowBoMon = $listItemsBoMon[0];
					$tenBoMon = $firstRowBoMon['ten_bo_mon'];
					$tenKhoa = $firstRowBoMon['ten_khoa'];
					foreach ($listItemsBoMon as $k => $item) { 
						if ($item['selected'] != "" && $firstRowBoMon['ma_bo_mon'] != $item['ma_bo_mon']){
							$tenBoMon = $item['ten_bo_mon'];
							$tenKhoa = $item['ten_khoa'];
						}
					?>
						<option value="<?php echo $item['ma_bo_mon'] ?>"<?php echo $item['selected'] != '' ? ' selected="selected"' :'' ?>><?php echo $item['ten_bo_mon'] ?></option>
					<?php } ?>
					</select>
				</td>
				<td align="left">
					Đợt học 
					<select id="<?php echo $formKey ?>_dot_hoc" name="data[dot_hoc]" style="width:150px; height:25px; padding: 0 0 0 0;" class="ui-widget-content ui-corner-all tableData">
					<?php foreach ($listItemsDotHoc as $k => $itemDotHoc) { ?>
						<option value="<?php echo $itemDotHoc['dot_hoc'] ?>"<?php echo $itemDotHoc['selected'] != '' ? ' selected="selected"' :'' ?>><?php echo $itemDotHoc['nam_hoc'] ?></option>
					<?php } ?>
					</select>
				</td>
				<td align="right">
					<a target="_blank" href="#" id="<?php echo $formKey ?>-btn-print" style="font-size:80%">&nbsp;In</a>
					<a target="_blank" href="#" id="<?php echo $formKey ?>-btn-print-full" style="font-size:80%">&nbsp;In chi tiết</a>
				</td>
			</tr>
		</table>
		</div>
		<div align="center">
			<h2>Khoa <?php echo $tenKhoa ?><br/>
				Bộ môn <?php echo $tenBoMon ?><br/>
				Khối Lượng Giảng Dạy Sau Đại Học<br/>Học kỳ <?php echo $namHoc ?>
			</h2>
		</div>
		<?php if (count($listItems) > 0) {?>
		<div style="margin-bottom:10px; margin-left:0px; font-size:80%" align=left> 
			Đối với thực hành: 20% [Tiết qui đổi] dành cho [THB] và 80% [Tiết qui đổi] dành cho [TH]<br/>
			Đối với tiểu luận: (số tiết tiểu luận / 15) * [(01->30 học viên)*1.5 + (31->60 học viên)*1 + (>60 học viên)*0.2]<br/>
			Giải thích: [HSLĐ] = lớp đông; [HSHH] = học hàm/học vị; [HSMG] = mời giảng; [HSBS] = bổ sung;
		</div>
		<div>
			<table id="<?php echo $formKey ?>TableKLGD" name="tableKLGD" width="100%" border="0" cellspacing="0" cellpadding="0" class="ui-widget ui-widget-content ui-corner-top tableData">
				<thead>
					<tr class="ui-widget-header heading" style="height:20pt;font-weight:bold;">
						<th align="center" class="ui-corner-tl th-left">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</th>
						<th align="center">Loại</th>
						<th style="width:400px;">Môn Học</th>
						<th>Lớp</th>
						<th align="center">Ngoài giờ</th>
						<th align="center" title="Số Học Viên">Số HV</th>
						<th align="center" title="Số Tiết">Tiết</th>
						<th align="center" title="Hệ Số Lớp Đông">HSLĐ</th>
						<th align="center" title="Hệ Số Học Hàm - Học Vị">HSHH</th>
						<th align="center" title="'Hệ Số Mời Giảng">HSMG</th>
						<th align="center" title="Hệ Số Bổ Sung">HSBS</th>
						<th align="right" class="ui-corner-tr th-right">Tiết QĐ&nbsp;</th>
					</tr>
				</thead>
			<tbody>
				<?php
				$booleanCheckShowPrintFull = false;
				foreach ($listItems as $i => $row) {
					$classAlt = ($i % 2) ? "alt" : "alt_";
					?>
					<tr class="fontcontent <?php  echo $classAlt ?> tr-cbgd" style="height:25px;">
						<td class="td-boder-left">&nbsp;</td>
						<td align="left">&nbsp;CBGD:</td>
						<td colspan="10" class="td-boder-right"><b><?php echo $row["hotencb"] ?> (<?php echo $row["shcc"] ?>)</b></td>
					</tr>
					<?php
					$tietQD = 0;
					$rowViewKlgd = $row['view_klgd'];
					foreach ($rowViewKlgd as $key => $resDM) {
						$rowViewKlgd = $resDM['chi_tiet_klgd_lv_ts'];
						$classRowSelectExpand = $rowViewKlgd != null ? ' clickExpand' : '';
						$relDetail = $rowViewKlgd != null ? ' rel="'.$row["shcc"].'_'.$resDM['loai'].'_'.$key.'"' : '';
						$classRowShowExpand = $rowViewKlgd != null ? ' showExpand' : '';
						$relDetailShowExpand = $rowViewKlgd != null ? ' rel="'.$row["shcc"].'_'.$resDM['loai'].'_'.$key.'"' : '';
						if ($resDM['loai'] != null){
						?>
							<tr align="left" valign="middle" class="fontcontent <?php  echo $classAlt ?> tr-detail<?php echo $classRowSelectExpand ?>"<?php echo $relDetail ?> style="height:25px;">
								<td width="16px" class="td-boder-left" valign="center" style="text-align: center">
									<?php if($rowViewKlgd != null) {?>
									<span class="iconExpand iconExpandDown" <?php echo $relDetail ?> title="Click vào để xem chi tiết">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</span>	
									<?php } else { ?>
									&nbsp;
									<?php }?>
								</td>
								<td align="left">&nbsp;<?php echo $resDM['loai'] ?></td>
								<td>&nbsp;<?php echo $resDM["ten_mh"] ?></td>
								<td title="<?php echo $resDM["ten_lop"] ?>">&nbsp;
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
								<td align="center">&nbsp;<?php echo $resDM["ngoai_gio"] ?></td>
								<td align="center">&nbsp;<?php echo $resDM["so_hv"] ?></td>
								<td align="center">&nbsp;<?php echo $resDM["so_tiet"] ?></td>
								<td align="center">&nbsp;<?php echo $resDM["hsld"] != 0 ? number_format($resDM["hsld"], 1, ',', '.') : '' ?></td>
								<td align="center">&nbsp;<?php echo $resDM["hs_hh_hv"] != 0 ? number_format($resDM["hs_hh_hv"], 1, ',', '.') : '' ?></td>
								<td align="center">&nbsp;<?php echo $resDM["hs_moi_giang"] != 0 ? number_format($resDM["hs_moi_giang"], 1, ',', '.') : '' ?></td>
								<td align="center">&nbsp;<?php echo $resDM["hs_bo_sung"] != 0 ? number_format($resDM["hs_bo_sung"], 1, ',', '.') : '' ?></td>
								<td align="right" class="td-boder-right">&nbsp;<?php echo number_format($resDM["tiet_qd"], 1, ',', '.') ?>&nbsp;</td>
							</tr>
						<?php
						}
						//Get view detail for TS/LV
						if($rowViewKlgd != null){
							if ($booleanCheckShowPrintFull == false){
								$booleanCheckShowPrintFull = true;
							}
						?>
							<tr class="showhide <?php echo $classAlt ?><?php echo $classRowShowExpand ?>"<?php echo $relDetailShowExpand ?> style="<?php echo isset($_GET['print']) && $_GET['print'] == '2' ? '': 'display: none' ?>">
								<td class="td-boder-left">&nbsp;</td>
								<td colspan="11" align="right" class="td-boder-right">
									<table cellpadding="3" width="100%" cellspacing="0">
										<thead>
										<tr class="ui-widget-header heading" style="font-color: #aaa">
											<td>Mã</td>
											<td>Họ tên</td>
											<td>Phái</td>
											<td>Ngày sinh</td>
											<td>Đề tài/Luận án</td>
											<td align="center">CTĐT</td>
											<td align="right">Tiết QĐ</td>
										</tr>
										</thead>
										<tbody>
										<?php
										foreach ($rowViewKlgd as $k2 => $resNCS) {
										?>
										<tr>
											<td>&nbsp;<?php echo $resNCS["ma_hoc_vien"] ?></td>
											<td>&nbsp;<?php echo $resNCS["ho_ten"] ?></td>
											<td>&nbsp;<?php echo $resNCS["phaidesc"] ?></td>
											<td>&nbsp;<?php echo $resNCS["ngay_sinh"] ?></td>
											<td>&nbsp;<?php echo $resNCS["detai"] ?></td>
											<td align="center">&nbsp;<?php echo $resNCS["ctdt"] ?></td>
											<td align="right">&nbsp;<?php echo number_format($resNCS["so_tiet_qd"], 1, ',', '.') ?></td>
										</tr>
										<?php
										}
										?>
										</tbody>
									</table>
								</td>
							</tr>
							<tr class="showhide <?php echo $classAlt ?> tr-detail<?php echo $classRowShowExpand ?>"<?php echo $relDetailShowExpand ?> style="display: none">
								<td colspan="12" class="td-boder-left td-boder-right">
									&nbsp;
								</td>
							</tr>
						<?php
						}
						$tietQD += floatval($resDM["tiet_qd"]);
					}
					?>
					<tr class="fontcontent <?php  echo $classAlt ?> tr-tong-so-tiet-qui-doi" style="height:25px;">
						<td class="td-boder-left"></td>
						<td align="left" colspan="10"><b>&nbsp;Tổng số tiết qui đổi</b></td>
						<td class="td-boder-right" align="right"><b><?php echo $tietQD != 0 ?  number_format($tietQD, 1, ',', '.') : '' ?>&nbsp;</b></td>
					</tr>
					<?php
				}
				?>
				<tbody>
			</table>
			<div class="fancyLoadingStatus" align="center">
				<div align="center" class="loadingMessage">
					<img src="<?php echo $gvURL ?>/images/ajax-loader.gif" /><br>
					Đang xử lý ....vui lòng chờ
				</div>
			</div>
		</div>
		<?php } else { ?>
			<div align="center"><font color="red">Không có dữ liệu</font></div>
		<?php } ?>
	</div>
<script type="text/javascript">
function <?php echo $formKey ?>InitReady(){
	$('.fancyLoadingStatus').fancybox({
		height: 50,
		width: 180,
		fitToView   : false,
		autoSize    : true,
		autoDimensions  : true,
		scrolling   : 'no',
		autoCenter : true,
		closeClick : false,
		// closeBtn: false,
		// helpers : { 
			// overlay : {closeClick: false} // prevents closing when clicking OUTSIDE fancybox
		// }
	});
	
	$( "#<?php echo $formKey ?>-btn-print,#<?php echo $formKey ?>-btn-print-full" ).button({ icons: {primary:'ui-icon ui-icon-print'} });
	<?php if ($booleanCheckShowPrintFull == false){?> 
	$( "#<?php echo $formKey ?>-btn-print-full").hide();
	<?php } ?>
	$( "#<?php echo $formKey ?>-btn-print" ).click(function(){
		var postURL = '<?php echo $help->getModuleActionRouteUrl('phongbankhoa/klgd/index')."?hisid=".$_GET['hisid'] ?>';
		postURL += '&print=1&dh=' + $("#<?php echo $formKey ?>_dot_hoc").val();
		postURL += '&bm=' + $("#<?php echo $formKey ?>_ma_bo_mon").val();
		$(this).attr('href', postURL);
	});
	$( "#<?php echo $formKey ?>-btn-print-full" ).click(function(){
		var postURL = '<?php echo $help->getModuleActionRouteUrl('phongbankhoa/klgd/index')."?hisid=".$_GET['hisid'] ?>';
		postURL += '&print=2&dh=' + $("#<?php echo $formKey ?>_dot_hoc").val();
		postURL += '&bm=' + $("#<?php echo $formKey ?>_ma_bo_mon").val();
		$(this).attr('href', postURL);
	});
	//hide ajax loading
	$("#squaresWaveG").hide();
	<?php if($viewSelectBoMon){ ?>
	var idSelectChange = '#<?php echo $formKey ?>_ma_bo_mon,#<?php echo $formKey ?>_dot_hoc';
	<?php } else {?>
	var idSelectChange = '#<?php echo $formKey ?>_dot_hoc';	 
	<?php }?>
	
	$(idSelectChange).change(function() {
		<?php if($viewSelectBoMon){ ?>
		var postData = {
			'bo_mon' : $("#<?php echo $formKey ?>_ma_bo_mon").val(),
			'dot_hoc' : $("#<?php echo $formKey ?>_dot_hoc").val()
		}
		<?php } else {?>
		var postData = {
			'dot_hoc' : $("#<?php echo $formKey ?>_dot_hoc").val()
		}	 
		<?php }?>
		var postURL = '<?php echo $help->getModuleActionRouteUrl('phongbankhoa/klgd/index')."?hisid=".$_GET['hisid'] ?>';
		$.ajax({type: "POST",
			url: postURL,
			data: postData,
			beforeSend: function(xhr){
				$('.fancyLoadingStatus').trigger('click');
			},
			success:function(result){
				$("#<?php echo $formKey ?>_listview").html(result);
				<?php echo $formKey ?>InitReady();
			},
			error: function (xhr,status,error){
				
			},
			complete: function(xhr,status){
				$.fancybox.close();
				$(".iconExpand").click(function(){
					var that = this;
					var currentRel = $(that).attr('rel');
					if ($(that).hasClass('iconExpandDown')){
						$(that).removeClass('iconExpandDown');
						$(that).addClass('iconExpandUp');
						$(that).attr('title', 'Click vào để đóng phần xem chi tiết' )
					}else{
						$(that).removeClass('iconExpandUp');
						$(that).addClass('iconExpandDown');
						$(that).attr('title', 'Click vào để xem chi tiết');
					}
					$(".showExpand").each(function(index){
						var rowShowExpandCheckRel = $(this).attr('rel');
						if (rowShowExpandCheckRel == currentRel) {
							if ($(this).css('display') == 'none') {
								$(this).show(500);
							}else{
								$(this).hide(500);
								
							}
						}
					});
				});
			}
		});
	});
	<?php if(! isset($_POST['dot_hoc'])) {?>
	$(".iconExpand").click(function(){
		var that = this;
		var currentRel = $(that).attr('rel');
		if ($(that).hasClass('iconExpandDown')){
			$(that).removeClass('iconExpandDown');
			$(that).addClass('iconExpandUp');
			$(that).attr('title', 'Click vào để đóng phần xem chi tiết' )
		}else{
			$(that).removeClass('iconExpandUp');
			$(that).addClass('iconExpandDown');
			$(that).attr('title', 'Click vào để xem chi tiết');
		}
		$(".showExpand").each(function(index){
			var rowShowExpandCheckRel = $(this).attr('rel');
			if (rowShowExpandCheckRel == currentRel) {
				if ($(this).css('display') == 'none') {
					$(this).show(500);
				}else{
					$(this).hide(500);
					
				}
			}
		});
	});
	<?php }?>
}
$(document).ready(function() {
	<?php echo $formKey ?>InitReady();
});	
</script>
</div>
