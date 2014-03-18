<?php
$help = Helper::getHelper('functions/util');
$gvURL = $help->getGvRootURL();
?>
<style type="text/css">
	.viewDataFormHide {
		display: none;
	}
	.phanbien-button-font-size {
		font-size : 9px !important;
	}
	/*Don't agree*/
	.type0 {
		color: #ff0000;
		font-weight: bold;
	}
	/*Agree*/
	.type1 {
		color: rgb(0, 128, 0);
		font-weight: bold;
	}
	/*Not answer yet*/
	.type2 {
		color: #0000ff;
		font-weight: bold;
	}
	table.dataTable tr.row_selected td.sorting { background-color:  #075385; }
	table.dataTable tr.row_selected td.sorting_1 { background-color:  #075385; }
	table.dataTable tr.row_selected td.sorting_2 { background-color:  #075385; }
	table.dataTable tr.row_selected td.sorting_3 { background-color:  #075385; }
	table.dataTable tr.row_selected td.sorting_4 { background-color:  #075385; }
	table.dataTable tr.row_selected td.sorting_5 { background-color:  #075385; }
	table.dataTable tr.row_selected td.sorting_6 { background-color:  #075385; }
	table.dataTable tr.row_selected td.sorting_7 { background-color:  #075385; }
	table.dataTable tr.row_selected td.sorting_8 { background-color:  #075385; }
	div.fancyLoadingStatus {
		font-size: 13px;
		display: none
	}
	div.warningMessage {
		color: #FF0000;
		font-weight: bold;
	}
</style>
<?php
$listArrayData = array();
foreach($listItems as $y => $row)
{
	$listArrayDataString = "";
	$listArrayDataString .= "'".$row["ma_thuyet_minh_dt"]."',";
	$listArrayDataString .= "'".$row["ten_de_tai_vn"]."',";
	// $listArrayDataString .= "'".$row["ten_tinh_trang"]."',";
	if ((int) $row["het_han_phan_bien"] == 0 && $row["kq_phan_hoi"] == null){
		$listArrayDataString .= "'<select id=\"".$formKey."selectClickPhanBienPhanHoi_".$row["ma_thuyet_minh_dt"]."\" class=\"".$formKey."selectClickPhanBienPhanHoi\"  rel=\"".$row["ma_thuyet_minh_dt"]."\">";
		$listArrayDataString .= "<option value=\"\" selected=\"selected\">Chưa trả lời</option>";
		$listArrayDataString .= "<option value=\"1\">Chọn đồng ý</option>";
		$listArrayDataString .= "<option value=\"0\">Chọn không đồng ý</option>";
		$listArrayDataString .= "</select>',";
	}else{
		$listArrayDataString .= "'".$row["text_kq_phan_hoi"]."',";
	}
	$listArrayDataString .= "'<div id= \"".$formKey."linkClickViewReportTab_".$row["ma_thuyet_minh_dt"]."\" class=\"".$formKey."linkClickViewReportTab phanbien-button-font-size\" rel_cap_de_tai=\"".$row["fk_cap_de_tai"]."\" rel=\"".$row["ma_thuyet_minh_dt"]."\">&nbsp;Xem</div>',";
	// $listArrayDataString .= "'',";
	$url = $help->getModuleActionRouteUrl('khcn/phanbien/ajaxdialog?hisid='.$_GET['hisid'])."&d=".$dothoc."&madetai=".$row["ma_thuyet_minh_dt"];
	$urlPrintPDF = null;
	if ($row['fk_cap_de_tai'] >= 21 && $row['fk_cap_de_tai'] <= 23){
		$urlPrintPDF = $help->getModuleActionRouteUrl('khcn/phanbien/printpdfbm01?a=print_tmdt_pdf&hisid='.$_GET['hisid'])."&mdt=".$row["ma_thuyet_minh_dt"]."&mcb=".$macb."&k=";
	}else if ($row['fk_cap_de_tai'] >= 31 && $row['fk_cap_de_tai'] <= 32){
		$urlPrintPDF = $help->getModuleActionRouteUrl('khcn/phanbien/printpdfbm06?a=print_tmdt_pdf&hisid='.$_GET['hisid'])."&mdt=".$row["ma_thuyet_minh_dt"]."&mcb=".$macb."&k=";
	}
	
	//Check het_han_phan_bien is '1' will not render button	
	$checkEnable = (int) $row["het_han_phan_bien"] == 0 && $row["kq_phan_hoi"] == '1'; 
	if ($checkEnable){
		$textView = "Phản Biện";
		$valueView = 1;
	}else{
		$textView = "Xem Phản Biện";
		$valueView = 0;
	}
	
	
	$listArrayDataString .= "'<a href=\"".$url."\" id=\"".$formKey."linkClickViewPhanBienTab_".$row["ma_thuyet_minh_dt"]."\" class=\"".$formKey."linkClickViewPhanBienTab phanbien-button-font-size\" rel_kq_phan_hoi=\"".$valueView."\" rel=\"".$row["ma_thuyet_minh_dt"]."\">&nbsp;".$textView."</a>";
	$listArrayDataString .= " <a href=\"javascript: void(0);\" id=\"".$formKey."linkClickViewPrintPhanBienTab_".$row["ma_thuyet_minh_dt"]."\" class=\"".$formKey."linkClickViewPrintPhanBienTab phanbien-button-font-size\" rel_cap_de_tai=\"".$row["fk_cap_de_tai"]."\" rel=\"".$row["ma_thuyet_minh_dt"]."\">&nbsp;In</a>";
	if($urlPrintPDF != null){
		$listArrayDataString .= " <a target=\"_blank\" href=\"".$urlPrintPDF."\" id=\"".$formKey."linkClickViewPrintPdfPhanBienTab_".$row["ma_thuyet_minh_dt"]."\" class=\"".$formKey."linkClickViewPrintPdfPhanBienTab phanbien-button-font-size\" rel_cap_de_tai=\"".$row["fk_cap_de_tai"]."\" rel=\"".$row["ma_thuyet_minh_dt"]."\">&nbsp;PDF</a>'";
	}else{
		$listArrayDataString .= "'";
	}
	
	$listArrayData[] = $listArrayDataString;
}
?>
<div id="<?php echo $formKey ?>dataGrid" style="padding-bottom: 50px">
	<table width="100%" cellpadding="5" cellspacing="0" border="0" class="display" id="<?php echo $formKey ?>dataGridTable" style="font-size: 13px">
		<thead>
	  <tr class='ui-widget-header heading' style='font-weight:bold; height:20pt;'>
		<td width="50px" align='left'>Mã ĐT</td>
		<td align="left">Tên Đề Tài</td>
		<!-- <td width="110px" align="left">Trạng Thái</td> -->
		<td width="200px" align="left">Kết Quả Trả Lời</td>
		<td width="100px" align="center">Nội dung TMĐT</td>
		<!-- <td width="100px" align="center">Link LLKH Người Tham Gia</td> -->
		<td width="200px" align="center"></td>
	  </tr>
	  </thead>
	  <tbody>
	  </tbody>
	</table>
</div>
<div id="<?php echo $formKey ?>DialogTabsViewPhanBien" title="">
	<div id="<?php echo $formKey ?>mainDialogData"></div>
	<div align="center" class="<?php echo $formKey ?>ajax-loading-bert ajax-loading-bert tableColumnRow">
		<div id="ajaxLoadingBertMessage" class="messageAlert"></div>
		<div id="squaresWaveG">
			<div id="squaresWaveG_1" class="squaresWaveG"></div>
			<div id="squaresWaveG_2" class="squaresWaveG"></div>
			<div id="squaresWaveG_3" class="squaresWaveG"></div>
			<div id="squaresWaveG_4" class="squaresWaveG"></div>
			<div id="squaresWaveG_5" class="squaresWaveG"></div>
			<div id="squaresWaveG_6" class="squaresWaveG"></div>
			<div id="squaresWaveG_7" class="squaresWaveG"></div>
			<div id="squaresWaveG_8" class="squaresWaveG"></div>
		</div>
	</div>
</div>
<div class="fancyLoadingStatus" align="center">
	<div align="center">
		<img src="<?php echo $gvURL ?>/images/ajax-loader.gif" />
	</div>
	Đang xử lý ....vui lòng chờ
	<br>
	<br>
	<div class="warningMessage">
	Nếu sau một thời gian dài mà thông báo này không tự đóng, bạn có thể thực hiện lại việc nhập bằng cách REFRESH lại trình duyệt.
	</div>
</div>
<script>
function <?php echo $formKey ?>InitReady(){
	$('.fancyLoadingStatus').fancybox({
		maxWidth    : 300,
		maxHeight   : 120,
		height: 120,
		fitToView   : false,
		autoSize    : false,
		autoDimensions  : false,
		scrolling   : 'no',
		closeClick : false,
		closeBtn: false,
		helpers : { 
			overlay : {closeClick: false} // prevents closing when clicking OUTSIDE fancybox
		}
	});
	
	$( ".<?php echo $formKey ?>linkClickViewPhanBienTab" ).button({ icons: {primary:'ui-icon ui-icon-button ui-icon-newwin'} });
	$( ".<?php echo $formKey ?>linkClickViewReportTab" ).button({ icons: {primary:'ui-icon ui-icon-button ui-icon-newwin'} });
	$( ".<?php echo $formKey ?>linkClickViewPrintPhanBienTab, .<?php echo $formKey ?>linkClickViewPrintPdfPhanBienTab" ).button({ icons: {primary:'ui-icon ui-icon-print'} });
	
	$("#<?php echo $formKey ?>dataGridTable tbody tr").on("click",function(event) {
		$("#<?php echo $formKey ?>dataGridTable tbody tr").each(function (){
			$(this).removeClass('row_selected');
		});
		
		$(this).addClass('row_selected');
	});
	
	// $(".<?php echo $formKey ?>ajax-loading-bert").find("#squaresWaveG").hide();
	
	$("#<?php echo $formKey ?>DialogTabsViewPhanBien").dialog({
		autoOpen: false,
		modal: true,
		resizable: true,
		width: 1024,
		height: 550
	});
	
	$(".<?php echo $formKey ?>linkClickViewPhanBienTab").click(function(){
		var myURL = $(this).attr('href');
		var initialCheckPhanHoi = $(this).attr('rel_kq_phan_hoi');
		var dialogTitle = "Phản biện đề tài : " + $(this).parent().parent().find('td').eq(1).text() ;
		
		if (initialCheckPhanHoi == '1'){
			dialogTitle = dialogTitle + "<br> <i><span style='color: #FF0000'>Lưu ý: Nội dung cập nhật chỉ được lưu sau khi nhấn nút Lưu</span></i>";
			$.ajax({
				url: myURL,
				success: function(data) {
					//setting dialog box
					$("#<?php echo $formKey ?>mainDialogData").html(data);
					$("#<?php echo $formKey ?>DialogTabsViewPhanBien").dialog({
						autoOpen: false,
						modal: true,
						resizable: true,
						width: 1024,
						height: 550,
						title: dialogTitle,
						buttons: { "Lưu": function() {
							$('.fancyLoadingStatus').trigger('click');
							
							$('.<?php echo $formKey ?>tabDialogTextAreaPhanBien').each(function(){
								var parentItem = $(this).parent();
								var currentName = $(this).attr('name');
								var currentHtmlValue = $(this).html();
								if (currentHtmlValue == '<br>'){
									currentHtmlValue = '';
								}
								parentItem.find('textarea[name="' + currentName + '"]').val(currentHtmlValue);
							});
							$.ajax({
								type: "POST",
								url: $("#<?php echo $formKey ?>DialogTabsViewPhanBienMainForm").attr("action"),
								data: $("#<?php echo $formKey ?>DialogTabsViewPhanBienMainForm").serialize(),
								beforeSend: function(xhr){
									$(".<?php echo $formKey ?>ajax-loading-bert").find("#squaresWaveG").show();
									//Disable save button
									$(".ui-dialog-buttonset").find(":button:contains('Lưu')").prop("disabled", true);
									
								},
								success:function(result){
									$.fancybox.close();
									gv_open_msg_box(result.message, 'alert', 345, 185, true);
								},
								error: function (xhr,status,error){
								},
								complete: function(xhr,status){
									$.fancybox.close();
									$(".<?php echo $formKey ?>ajax-loading-bert").find("#squaresWaveG").hide();
									//Enable save button
									$(".ui-dialog-buttonset").find(":button:contains('Lưu')").prop("disabled", false);
								}
							});
							
						}},
						close: function() {
							$(".<?php echo $formKey ?>ajax-loading-bert").find("#squaresWaveG").hide();
						}
					});
					
					$("#<?php echo $formKey ?>DialogTabsViewPhanBien").dialog('open');
					
					if (initialCheckPhanHoi == '1'){
						//Enable save button
						$(".ui-dialog-buttonset").find(":button:contains('Lưu')").prop("disabled", false);
						$(".ui-dialog-buttonset").show();
					}else{
						//Disable save button
						$(".ui-dialog-buttonset").find(":button:contains('Lưu')").prop("disabled", true);
						$(".ui-dialog-buttonset").hide();
					}
				},
				complete: function(xhr,status){
					$(".<?php echo $formKey ?>ajax-loading-bert").find("#squaresWaveG").hide();
				}
			});
		}else{
			$.ajax({
				url: myURL,
				success: function(data) {
					//setting dialog box
					$("#<?php echo $formKey ?>mainDialogData").html(data);
					$("#<?php echo $formKey ?>DialogTabsViewPhanBien").dialog({
						autoOpen: false,
						modal: true,
						resizable: true,
						width: 1024,
						height: 550,
						title: dialogTitle,
						close: function() {
							$(".<?php echo $formKey ?>ajax-loading-bert").find("#squaresWaveG").hide();
						}
					});
					
					$("#<?php echo $formKey ?>DialogTabsViewPhanBien").dialog('open');
					
					if (initialCheckPhanHoi == '1'){
						//Enable save button
						$(".ui-dialog-buttonset").find(":button:contains('Lưu')").prop("disabled", false);
						$(".ui-dialog-buttonset").show();
					}else{
						//Disable save button
						$(".ui-dialog-buttonset").find(":button:contains('Lưu')").prop("disabled", true);
						$(".ui-dialog-buttonset").hide();
					}
				},
				complete: function(xhr,status){
					$(".<?php echo $formKey ?>ajax-loading-bert").find("#squaresWaveG").hide();
				}
			});
		}
			
		
		return false;
	});
	
	$(".<?php echo $formKey ?>linkClickViewReportTab").click(function(){
		// var i = pindex + 1;
		
		var matmdt = $(this).parent().parent().find('td').eq(0).text();
		var tabname = "";
 		var fileprint='';
 		var key = '<?php echo $formKey ?>print_tmdt_' + matmdt;
 		var tabOpened = window.ns.get_tabOpened();
 		var tabCurrent = $('#' + tabOpened['XemPhanBienDeTai_All']).index()-1;
 		var cap_dt = parseInt($(this).attr('rel_cap_de_tai'));
  		
  		if (cap_dt > 20 && cap_dt < 25) { // Cap DHQG
			  fileprint = 'khcn_print_tmdt_r01.php';
			  tabname = 'TMĐT - ĐHQG Mẫu R01 - ' + matmdt;
		 }else if (cap_dt > 30 && cap_dt < 35) { // Cap truong
			  fileprint = 'khcn_print_tmdt_t12.php';
			  tabname = 'TMĐT - Trường Mẫu 12 - ' + matmdt;
		 }
		
		if ((cap_dt > 20 && cap_dt < 25) || (cap_dt > 30 && cap_dt < 35)){
			window.ns.addTab_ns(key, tabname, 'print-preview-icon24x24.png', 
				tabCurrent, 
				"<?php echo $gvURL ?>/khcn/"+fileprint+"?a=print_tmdt_fromtab&hisid=<?php echo $_REQUEST["hisid"];?>&m="+matmdt+"&k="+key);
		}else{
			alert("Không có mẫu để xem");
		}
	});
	
	//Print phan_bien
	$(".<?php echo $formKey ?>linkClickViewPrintPhanBienTab").click(function(){
		var matmdt = $(this).parent().parent().find('td').eq(0).text();
		var tabname = "";
 		var fileprint='';
 		var key = '<?php echo $formKey ?>print_tmdt_' + matmdt;
 		var tabOpened = window.ns.get_tabOpened();
 		var tabCurrent = $('#' + tabOpened['XemPhanBienDeTai_All']).index()-1;
 		var cap_dt = parseInt($(this).attr('rel_cap_de_tai'));
  		
		if (cap_dt == 23) { // Cap DHQG Loai C
			fileprint = 'khcn_print_danh_gia_tmdt_m01.php';
			tabname = 'Đánh giá TMĐT - ĐHQG Mẫu M01 - ' + matmdt;
		}else if (cap_dt > 30 && cap_dt < 35) { // Cap truong
			fileprint = 'khcn_print_danh_gia_tmdt_bm06.php';
			tabname = 'Đánh giá TMĐT - Trường Mẫu BM06/KHCN-08 - ' + matmdt;
		}
		
		if (cap_dt == 23 || (cap_dt > 30 && cap_dt < 35)){
			var links = "<?php echo $gvURL ?>/khcn/"+fileprint+"?a=print_tmdt_pdf&hisid=<?php echo $_REQUEST["hisid"]; ?>&mdt="+matmdt+"&mcb=<?php echo $macb ?>&k=";
			window.open(links,tabname,'width=650,height=800,menubar=1'+',toolbar=0'+',status=0'+',scrollbars=1'+',resizable=1');
		}else{
			alert("Không có mẫu để xem");
		}
	});
	
	$(".<?php echo $formKey ?>selectClickPhanBienPhanHoi").change(function(){
		var that = this;
		
		if($(that).val() != ''){
			var myWarningMessage = "Bạn xác định không đồng ý phản biện đề tài này ?"; 
			var sayYes = $(that).val() == '1';
			var myURL = '<?php echo $help->getModuleActionRouteUrl('khcn/phanbien/no?hisid='.$_GET['hisid'])."&d=".$dothoc; ?>';
	
			if(sayYes){
				myWarningMessage = "Bạn xác định đồng ý phản biện đề tài này ?";
				myURL = '<?php echo $help->getModuleActionRouteUrl('khcn/phanbien/yes?hisid='.$_GET['hisid'])."&d=".$dothoc; ?>';
			}
			
			jConfirm(myWarningMessage, 'Xác nhận', function(r) {
				if(r){
					$.ajax({
						type: "POST",
						url: myURL,
						data: {'ma_thuyet_minh_dt' : $(that).attr('rel')},
						beforeSend: function(xhr){
							gv_processing_diglog("open", "Đang xử lý ... vui lòng chờ");
						},
						success: function(data) {
							gv_processing_diglog("close");
							
							if(data.status == '0') {
								jAlert(data.message, 'Thông báo');
							}else{
								var currentRow = $(that).parent().parent();
								var column2 = currentRow.find('td').eq(2);
								
								if(sayYes){
									//Confirm yes
									var button = currentRow.find('td').eq(4).find('.<?php echo $formKey ?>linkClickViewPhanBienTab');
									button.find('span').html('Phản biện');
									column2.html('Đồng ý phản biện').removeClass('type2').addClass('type1');
								}else{
									column2.html('Không đồng ý').removeClass('type2').addClass('type0');
								}
							}
						},
						complete: function(xhr,status){
							$(".<?php echo $formKey ?>ajax-loading-bert").find("#squaresWaveG").hide();
						}
					});
				}else{
					$(that).val('');
				}
			});
		}
		
			
	});
}


$(document).ready(function() {
	$('#<?php echo $formKey ?>dataGridTable').dataTable( {
		<?php if (count($listArrayData) > 0) { ?>
		"aaData": [<?php echo "[".implode('],[', $listArrayData)."]"  ?>],
		<?php } ?>
		"aoColumns": [
			{"sClass": "center"},
			null,
			// null,
			null,
			{"sClass": "center"},
			// {"sClass": "center"},
			{"sClass": "right"}
		],
		//"aaSorting": [[0,'asc'], [2,'asc'], [3,'asc']],
		"bAutoWidth": false, 
		"sPaginationType": "full_numbers",
		"oLanguage": {"sUrl": "<?php echo $help->baseURL() ?>/datatable/media/language/vi_VI.txt"},
		"fnInitComplete": function(oSettings, json) {
		},
		"fnFooterCallback": function ( nRow, aaData, iStart, iEnd, aiDisplay ) {
		},
		"fnDrawCallback": function() {
			<?php echo $formKey ?>InitReady();
		},
		"fnRowCallback": function( nRow, aaData, iDisplayIndex ) {
			
			if (aaData[2] == "Đồng ý phản biện"){
				$('td:eq(2)', nRow).addClass('type1');
			}else if (aaData[2] == "Không đồng ý"){
				$('td:eq(2)', nRow).addClass('type0');
			}else if (aaData[2] == "Chưa trả lời"){
				$('td:eq(2)', nRow).addClass('type2');
			}
			
			return nRow;
		}
	});
	
	
});
</script>