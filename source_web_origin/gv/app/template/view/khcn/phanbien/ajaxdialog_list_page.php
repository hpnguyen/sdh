<?php
$help = Helper::getHelper('functions/util');
$gvURL = $help->getGvRootURL();
$rootURL = $help->baseURL();
$listArrayData = array();
$row = $listItems[0];
$defaultText = '';
?>
<style type="text/css">
	.viewDataFormHide {
		display: none;
	}
	.formTabA4KinhPhiInput, .formTabBDanhGiaInput, .<?php echo $formKey ?>TotalSummaryKinhPhiInput {
		width: 140px;
		text-align: right;
	}
	.TabTableDataView_A4, .TabTableDataView_B {
		padding: 0 0 10px 0;
	}
	table.TabBDanhGiaTableView tr {
		height: 35px;
	}
	.span-bold, .<?php echo $formKey ?>TotalSummaryDanhGia{
		font-weight: bold;
	}
	.<?php echo $formKey ?>DialogViewPhanBien {
		padding: 10px;
		font-size: 13px;
	}
	.<?php echo $formKey ?>TotalSummaryKinhPhi {
		height: 30px;
		width: 140px;
		text-align: right;
		font-weight: bold;
		font-size: 15px;
		padding: 5px 0 0 0;
	}
	div.<?php echo $formKey ?>DialogViewPhanBien .heading {
		font-size: 13px;
	}
	.<?php echo $formKey ?>tabDialogTextAreaPhanBien{
		display: block; 
		margin: 10px 0 0 0; 
		background: white; 
		border-radius: 10px; 
		padding: 20px;
	}
	.dataGridTableTabTableDataView_A4, .TabBDanhGiaTableView {
		padding: 0px; 
		border-collapse:collapse; 
		border: 1px solid #000000; 
		background: #FFFFFF;
	}
</style>
<div class="<?php echo $formKey ?>DialogViewPhanBien">
<?php if ((int) $row["het_han_phan_bien"] == 0)	{ ?>
<form id="<?php echo $formKey ?>DialogTabsViewPhanBienMainForm" action="<?php echo $help->getModuleActionRouteUrl('khcn/phanbien/save?hisid='.$_GET['hisid']) ?>" method="post">
	<input type="hidden" value="0" name="tabActiveIndex" id="<?php echo $formKey ?>tabActiveIndex" />
	<input type="hidden" value="<?php echo $macb ?>" name="fk_ma_can_bo" id="<?php echo $formKey ?>fk_ma_can_bo" />
	<input type="hidden" value="<?php echo $madetai ?>" name="ma_thuyet_minh_dt" id="<?php echo $formKey ?>ma_thuyet_minh_dt" />
<?php } ?>
	<p><b>A. NHẬN XÉT</b></p>
	<div id="<?php echo $formKey ?>tabDialogTabsViewPhanBienA1">
		<p>
		<b>A1. Tầm quan trọng của nghiên cứu:
			(a) Tính cấp thiết, tính mới, tính sáng tạo và khả năng ứng dụng của nghiên cứu;
			(b) Sự phù hợp với định hướng khoa học và công nghệ đã công bố hoặc đặt hàng.
		</b>
		</p>
		<div contenteditable="true" class="<?php echo $formKey ?>tabDialogTextAreaPhanBien" name="data_group_1[a1_tam_quan_trong]">
			<?php echo $row["a1_tam_quan_trong"] == '' || $row["a1_tam_quan_trong"] == null ? $defaultText : $row["a1_tam_quan_trong"] ?>
		</div>
		<textarea class="viewDataFormHide" name="data_group_1[a1_tam_quan_trong]"><?php echo $row["a1_tam_quan_trong"] ?></textarea>
	</div>
	<div id="<?php echo $formKey ?>tabDialogTabsViewPhanBienA2">
		<p><b>A2. Chất lượng nghiên cứu:
			(a) Mục tiêu, nội dung, phương phá nghiên cứu phù hợp và mới để đạt được mục tiêu; 
			(b) Đóng góp vào tri thức khoa học, có ảnh hưởng đối với xã hội;
			(c) Sản phẩm nghiên cứu phù hợp tiêu chí các loại đề tài đăng ký.
		</b></p>
		<div contenteditable="true" class="<?php echo $formKey ?>tabDialogTextAreaPhanBien" name="data_group_1[a2_chat_luong_nc]">
			<?php echo $row["a2_chat_luong_nc"] == '' || $row["a2_chat_luong_nc"] == null ? $defaultText : $row["a2_chat_luong_nc"] ?>
		</div>
		<textarea class="viewDataFormHide" name="data_group_1[a2_chat_luong_nc]"><?php echo $$row["a2_chat_luong_nc"] ?></textarea>
	</div>
	<div id="<?php echo $formKey ?>tabDialogTabsViewPhanBienA3">
		<p><b>A3. Năng lực nghiên cứu của chủ nhiệm và nhóm nghiên cứu; điều kiện cơ sở vật chất - kỹ thuật phục vụ nghiên cứu.</b></p>
		<div contenteditable="true" class="<?php echo $formKey ?>tabDialogTextAreaPhanBien" name="data_group_1[a3_nlnc_csvc]">
			<?php echo $row["a3_nlnc_csvc"] == '' || $row["a3_nlnc_csvc"] == null ? $defaultText : $row["a3_nlnc_csvc"] ?>
		</div>
		<textarea class="viewDataFormHide" name="data_group_1[a3_nlnc_csvc]"><?php echo $row["a3_nlnc_csvc"] ?></textarea>
	</div>
		<div id="<?php echo $formKey ?>tabDialogTabsViewPhanBienA4">
			<p><b>A4. Kinh phí</b></p>
			<div id="<?php echo $formKey ?>TabTableDataView_A4" class="TabTableDataView_A4">
			<?php
			//*****************************************************
			//Render data from table nckh_pb_noi_dung_kinh_phi
			//*****************************************************
			if (isset($row["join_tables"])){
				$temp = $row["join_tables"];
				$tableJoinNckhPbDmNoiDung = $temp['nckh_pb_noi_dung_kinh_phi'];
				?>
				<table width="100%"  cellpadding="5" cellspacing="0" border="1" class="dataGridTableTabTableDataView_A4 display <?php echo $formKey ?>dataGridTableTabTableDataView_A4_<?php echo $row["ma_thuyet_minh_dt"] ?>">
					<thead>
					  <tr class='heading' style='font-weight:bold; height:20pt;'>
						<td width="50px" align='center' rowspan="2">TT</td>
						<td align="center" rowspan="2">Nội dung đánh giá<br>(Căn cứ phụ lục giải trình các khoản chi)</td>
						<td align="center" colspan="3">Nhận xét (đánh dấu X vào các mục)</td>
					  </tr>
					  <tr class='heading' style='font-weight:bold; height:20pt;'>
					  	<td align='center'>Cao</td>
						<td align="center">Thấp</td>
						<td align="center">Kinh phí đề nghị</td>
					  </tr>
					  </thead>
					  <tbody>
						<?php
						$sumKinhPhi = 0;
						foreach ($tableJoinNckhPbDmNoiDung as $k => $dm) {
							?>
							<tr>
								<td align="center"><b><?php echo ($k + 1) ?></b></td>
								<td><?php echo $dm['noi_dung'] ?></td>
								<td align="center">
									<?php if ((int) $row["het_han_phan_bien"] == 0)	{?>
										<input type="radio" name="data_group_2[a4_kinh_phi_A4_radio][<?php echo $dm['ma_nd'] ?>]" class="<?php echo $formKey ?>dataGridTableTabTableDataView_A4_RadioHight<?php echo $row["ma_thuyet_minh_dt"] ?>_<?php echo $dm['ma_nd'] ?>" value="0" <?php echo $dm['nhan_xet'] == '0' ? 'checked="checked"' : '' ?> />
									<?php } else {
										echo $dm['nhan_xet'] == '0' ? 'x' : '';
									}?>
									
								</td>
								<td align="center">
									<?php if ((int) $row["het_han_phan_bien"] == 0)	{?>
										<input type="radio" name="data_group_2[a4_kinh_phi_A4_radio][<?php echo $dm['ma_nd'] ?>]" class="<?php echo $formKey ?>dataGridTableTabTableDataView_A4_RadioLow<?php echo $row["ma_thuyet_minh_dt"] ?>_<?php echo $dm['ma_nd'] ?>" value="1" <?php echo $dm['nhan_xet'] == '1' ? 'checked="checked"' : '' ?> />
									<?php } else {
										echo $dm['nhan_xet'] == '1' ? 'x' : '';
									}?>
									
								</td>
								<td align="center">
									<?php if ((int) $row["het_han_phan_bien"] == 0)	{?>
										<input type="text" data-v-min="0.00" data-v-max="9999999.99" name="data_group_2[a4_kinh_phi_A4_input][<?php echo $dm['ma_nd'] ?>]" class="formTabA4KinhPhiInput <?php echo $formKey ?>dataGridTableTabTableDataView_A4_KinhPhi<?php echo $row["ma_thuyet_minh_dt"] ?>_<?php echo $dm['ma_nd'] ?>" value="<?php echo isset($dm['kinh_phi_de_nghi']) ? $dm['kinh_phi_de_nghi'] : '' ?>" />
									<?php 
									} else {
										 echo isset($dm['kinh_phi_de_nghi']) ? $dm['kinh_phi_de_nghi'] : '';
									} ?>
								</td>
							</tr>
							<?php
							$sumKinhPhi += isset($dm['kinh_phi_de_nghi']) && $dm['kinh_phi_de_nghi'] != null ? floatval( $dm['kinh_phi_de_nghi']) : 0; 
						}
						?>
							<tr>
								<td align="center" colspan="4"><b>Tổng kinh phí đề nghị</b> <i>(triệu đồng)</i></td>
								<td align="center">
									<div class="<?php echo $formKey ?>TotalSummaryKinhPhi">
										<?php echo number_format($sumKinhPhi, 2, ',', '.') ?>
									</div>
								</td>
							</tr>
					</tbody>
				</table>
				<?php
			}
			//*****************************************************
			//End render data from table nckh_pb_noi_dung_kinh_phi
			//*****************************************************
			?>
			</div>
			<p><b>Nhận xét kinh phí: </b></p>
			<div contenteditable="true" class="<?php echo $formKey ?>tabDialogTextAreaPhanBien" name="data_group_1[a4_kinh_phi_nx]">
				<?php echo $row["a4_kinh_phi_nx"] == '' || $row["a4_kinh_phi_nx"] == null ? $defaultText : $row["a4_kinh_phi_nx"] ?>
			</div>
			<textarea class="viewDataFormHide" name="data_group_1[a4_kinh_phi_nx]"><?php echo $row["a4_kinh_phi_nx"] ?></textarea>
		</div>
		<div id="<?php echo $formKey ?>tabDialogTabsViewPhanBienB">
			<p><b>B. ĐÁNH GIÁ</b></p>
			<div id="<?php echo $formKey ?>TabTableDataView_B" class="TabTableDataView_B">
			<?php
			//*****************************************************
			//Render data from table nckh_pb_noi_dung_danh_gia
			//*****************************************************
			if (isset($row["join_tables"])){
				$temp = $row["join_tables"];
				$tableJoinNckhPbDmNoiDungDanhGia = $temp['nckh_pb_noi_dung_danh_gia'];
				?>
				<table width="100%" cellpadding="5" cellspacing="0" border="1" class="TabBDanhGiaTableView display <?php echo $formKey ?>dataGridTableTabTableDataView_B_<?php echo $row["ma_thuyet_minh_dt"] ?>">
					<thead>
					  <tr class='heading' style='font-weight:bold; height:20pt;'>
						<td width="50px" align='center' rowspan="2">TT</td>
						<td align="center">Nội dung đánh giá</td>
						<td align="center">Điểm tối đa</td>
						<td align="center">Điểm đánh giá</td>
					  </tr>
					  </thead>
					  <tbody>
						<?php
						$sumDanhGia = 0;
						$sumDanhGiaDiemMax = 0;
						foreach ($tableJoinNckhPbDmNoiDungDanhGia as $k => $dm) {
							?>
							<tr>
								<td align="center"><b><?php echo $dm['stt'] ?></b></td>
								<td><?php echo $dm['noi_dung'] ?></td>
								<td align="center">
									<?php 
									if((int) $dm['allow_edit'] == 1) {
										echo $dm['diem_toi_da'] ;
									}else{
										echo "<b>".$dm['thang_diem_truong']."</b>" ;
									}
									?>
								</td>
								<td align="center">
									<?php if((int) $dm['allow_edit'] == 1) { ?>
										<?php if ((int) $row["het_han_phan_bien"] == 0)	{?>
										<input type="text" maxlength="15" rel_max="<?php echo $dm['diem_toi_da'] ?>" name="data_group_3[b_danh_gia_input][<?php echo $dm['id'] ?>]" class="formTabBDanhGiaInput <?php echo $formKey ?>dataGridTableTabTableDataView_B_DanhGia<?php echo $row["ma_thuyet_minh_dt"] ?>_<?php echo $dm['ma_nd'] ?>" value="<?php echo isset($dm['diem']) ? $dm['diem'] : '' ?>" />
										<?php } else {
											echo isset($dm['diem']) ? $dm['diem'] : '';
										} ?>	
									<?php } ?>
								</td>
							</tr>
							<?php
							$sumDanhGia += isset($dm['diem']) && $dm['diem'] != null ? (int) $dm['diem'] : 0;
							$sumDanhGiaDiemMax += isset($dm['diem_toi_da']) && $dm['diem_toi_da'] != null ? (int) $dm['diem_toi_da'] : 0;
						}
						?>
							<tr>
								<td align="center" colspan="2"><b>Tổng cộng</b></td>
								<td align="center"><b><?php echo $sumDanhGiaDiemMax ?></b></td>
								<td align="center"><div class="<?php echo $formKey ?>TotalSummaryDanhGia"><?php echo $sumDanhGia ?></div></td>
							</tr>
					</tbody>
				</table>
				<?php
			}
			//*****************************************************
			//End render data from table nckh_pb_dm_danh_gia
			//*****************************************************
			?>
			</div>
		</div>
		<div id="<?php echo $formKey ?>tabDialogTabsViewPhanBienC">
			<p><b>C. KẾT LUẬN</b></p>
			<div contenteditable="true" class="<?php echo $formKey ?>tabDialogTextAreaPhanBien" name="data_group_1[c_ket_luan]">
				<?php echo $row["c_ket_luan"] == '' || $row["c_ket_luan"] == null || $row["c_ket_luan"] == '<br>' ? $defaultText : $row["c_ket_luan"] ?>
			</div>
			<br>
			<br>
			<textarea class="viewDataFormHide" name="data_group_1[c_ket_luan]"><?php echo $row["c_ket_luan"] ?></textarea>
		</div>
	</div>
<?php if ((int) $row["het_han_phan_bien"] == 0)	{ ?>	
</form>
<?php } ?>
</div>
<?php
/*************************************************************************
 * Add the script function when het_han_phan_bien is '0'
 ************************************************************************/ 
if ((int) $row["het_han_phan_bien"] == 0)	{ 
?>
<script>
function <?php echo $formKey ?>InitReadyAjax(){
	
	<?php echo $formKey ?>configTextEditor('.<?php echo $formKey ?>tabDialogTextAreaPhanBien');
	
	//Set input to numeric format
	$('.formTabA4KinhPhiInput').autoNumeric('init',	{'wEmpty': '', aSep: '.', aDec: ',', vMin: '0.00', vMax: '9999999.99'});
	
	//Set input "danh gia" only number
	$(".formTabBDanhGiaInput").keydown(function (e) {
		// Allow: backspace, delete, tab, escape, enter and .
		//if ($.inArray(e.keyCode, [46, 8, 9, 27, 13, 190]) !== -1 ||
		// Allow: backspace, delete, tab, escape 
		if ($.inArray(e.keyCode, [46, 8, 9, 27]) !== -1 ||
		// Allow: Ctrl+A
		(e.keyCode == 65 && e.ctrlKey === true) ||
		// Allow: home, end, left, right
		(e.keyCode >= 35 && e.keyCode <= 39)) {
			// let it happen, don't do anything
			return;
		}
		// Ensure that it is a number and stop the keypress
		if ((e.shiftKey || (e.keyCode < 48 || e.keyCode > 57)) && (e.keyCode < 96 || e.keyCode > 105)) {
			
			e.preventDefault();
			
		}
	});
	
	$(".formTabA4KinhPhiInput").keyup(function (e){
		//Summary
		var totalSummary = parseFloat($(this).autoNumeric('get'));
		var item = $(this).parent().parent().parent();
		var currentName = $(this).attr('name');
		
		$('.formTabA4KinhPhiInput').each(function(index){
			var realInputValue = $(this).autoNumeric('get');
			if($(this).val() != '' && currentName != $(this).attr('name')){
				if (realInputValue != 0){
					totalSummary = totalSummary +  parseFloat(realInputValue);
				}
			}
		});
		
		var x = ''+totalSummary;
		var parts = x.toString().split(".");
		parts[0] = parts[0].replace(/\B(?=(\d{3})+(?!\d))/g, ".");
		var formatLastNumber = parts.join(",");
		
		$(".<?php echo $formKey ?>TotalSummaryKinhPhi").html(formatLastNumber);
	});
	
	$(".formTabBDanhGiaInput").keyup(function (e){
		var maxValue = parseInt($(this).attr('rel_max'));
		var nameItem = $(this).attr('name');
		var error = false;
		
		if($(this).val() != ''){
			if(parseInt($(this).val()) > maxValue){
				$(this).val('');
				error = true;
				gv_open_msg_box('Điểm đánh giá không được vượt quá ' + maxValue, 'alert', 345, 185, true);
			}
		}
		
		//Summary
		var totalSummary = 0;
		$(".formTabBDanhGiaInput").each(function(index){
			var nameItemCheck = $(this).attr('name');
			
			if($(this).val() != ''){
				if (error == false || (error == true && nameItemCheck != nameItem)){
					totalSummary += parseInt($(this).val());
					$(".<?php echo $formKey ?>TotalSummaryDanhGia").html(totalSummary);
				}
			}
		});
	});
}

function <?php echo $formKey ?>configTextEditor(name){
	//CKeditor tool to edit content
	var editorTool = $(name).ckeditor(function( textarea ) {},
		{
			toolbar : [
				{ name: 'document', groups: [ 'mode', 'document', 'doctools' ], items: ['Source', 'NewPage', 'Preview', 'Print'] },
				{ name: 'clipboard', groups: [ 'clipboard', 'undo' ], items: [ 'Cut', 'Copy', 'Paste', 'PasteText', 'PasteFromWord', '-', 'Undo', 'Redo' ] },
				{ name: 'editing', groups: [ 'find', 'selection', 'spellchecker' ], items: [ 'Find', 'Replace', '-', 'SelectAll', '-', 'Scayt' ] },
				{ name: 'insert', items: [ 'Image', 'Table', 'SpecialChar', '-' ] }, { name: 'tools', items: [ 'Maximize', 'ShowBlocks' ] },
				{ name: 'others', items: [ '-' ] },
				'/',
				{ name: 'styles', items: [ 'FontSize' ] },
				{ name: 'colors', items: [ 'TextColor', 'BGColor' ] },
				{ name: 'basicstyles', groups: [ 'basicstyles', 'cleanup' ], items: [ 'Bold', 'Italic', 'Underline', 'Strike', 'Subscript', 'Superscript', '-', 'RemoveFormat', '-', 'EqnEditor' ] },
				{ name: 'paragraph', groups: [ 'list', 'indent', 'blocks', 'align', 'bidi' ], items: [ 'NumberedList', 'BulletedList', '-', 'JustifyLeft', 'JustifyCenter', 'JustifyRight', 'JustifyBlock'] }
			],
			htmlEncodeOutput: false,
			entities : false,
			entities_latin : false,
			resize_enabled : false,
			enterMode : CKEDITOR.ENTER_BR,
			shiftEnterMode : CKEDITOR.ENTER_P,
			language : 'vi',
			filebrowserBrowseUrl: '<?php echo $rootURL ?>/ckfinder/ckfinder.html',
			filebrowserImageBrowseUrl: '<?php echo $rootURL ?>/ckfinder/ckfinder.html?Type=Images',
			filebrowserUploadUrl: '<?php echo $rootURL ?>/ckfinder/core/connector/php/connector.php?command=QuickUpload&type=Files',
			filebrowserImageUploadUrl: '<?php echo $rootURL ?>/ckfinder/core/connector/php/connector.php?command=QuickUpload&type=Images',
		}
	);
}

$(document).ready(function() {
	<?php echo $formKey ?>InitReadyAjax();
});
</script>
<?php 
}
/*************************************************************************
 * End if
 ************************************************************************/
?>
