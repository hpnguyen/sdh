<?php
$help = Helper::getHelper('functions/util');
$gvURL = $help->getGvRootURL();
?>
<style type="text/css">
	.viewDataFormHide {
		display: none;
	}
	.phanbien-button-font-size {
		font-size : 8px !important;
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
</style>
<div align="center">
	<h2>Danh sách đề tài</h2>
</div>
<?php
$listArrayData = array();
foreach($listItems as $y => $row)
{
	$listArrayDataString = "";
	$listArrayDataString .= "'".$row["ma_thuyet_minh_dt"]."',";
	$listArrayDataString .= "'".$row["ten_de_tai_vn"]."',";
	$listArrayDataString .= "'".$row["ten_tinh_trang"]."',";
	$listArrayDataString .= "'".$row["text_kq_phan_hoi"]."',";
	$listArrayDataString .= "'<div id= \"".$formKey."linkClickViewReportTab_".$row["ma_thuyet_minh_dt"]."\" class=\"".$formKey."linkClickViewReportTab phanbien-button-font-size\" rel_cap_de_tai=\"".$row["fk_cap_de_tai"]."\" rel=\"".$row["ma_thuyet_minh_dt"]."\">Xem</div>',";
	$listArrayDataString .= "'',";
	$url = $help->getModuleActionRouteUrl('khcn/phanbien/ajaxdialog?hisid='.$_GET['hisid'])."&d=".$dothoc."&madetai=".$row["ma_thuyet_minh_dt"];
	//Check het_han_phan_bien is '1' will not render button	
	if ((int) $row["het_han_phan_bien"] == 0){
		$textView = "Phản Biện";
	}else{
		$textView = "Xem Phản Biện";
	}
	
	$listArrayDataString .= "'<a href=\"".$url."\" id=\"".$formKey."linkClickViewPhanBienTab_".$row["ma_thuyet_minh_dt"]."\" class=\"".$formKey."linkClickViewPhanBienTab phanbien-button-font-size\" rel=\"".$row["ma_thuyet_minh_dt"]."\">&nbsp;".$textView."</a>'";
	$listArrayData[] = $listArrayDataString;
}
?>
<div id="<?php echo $formKey ?>dataGrid" style="padding-bottom: 50px">
	<table width="100%" cellpadding="0" cellspacing="0" border="0" class="display" id="<?php echo $formKey ?>dataGridTable" style="padding: 0px">
		<thead>
	  <tr class='ui-widget-header heading' style='font-weight:bold; height:20pt;'>
		<td width="50px" align='center'>Mã ĐT</td>
		<td align="center">Tên Đề Tài</td>
		<td width="200px" align="center">Trạng Thái</td>
		<td width="150px" align="center">Kết Quả Trả Lời</td>
		<td width="100px" align="center">Chi tiết Link TMĐT</td>
		<td width="100px" align="center">Link LLKH Người Tham Gia</td>
		<td width="100px" align="center"></td>
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
<script>
function <?php echo $formKey ?>InitReady(){
	$( ".<?php echo $formKey ?>linkClickViewReportTab, .<?php echo $formKey ?>linkClickViewPhanBienTab" ).button({ icons: {primary:'ui-icon ui-icon-button'} });
	
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
		//console.log(myURL);
		var dialogTitle = "Phản biện đề tài : " + $(this).parent().parent().find('td').eq(1).text() ;
		<?php
		//Only show warning message if enable to update data
		if ((int) $row["het_han_phan_bien"] == 0){ 
		?>
		dialogTitle = dialogTitle + "<br> <i><span style='color: #FF0000'>Lưu ý: Nội dung cập nhật chỉ được lưu sau khi nhấn nút Lưu</span></i>";
		<?php } ?>
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
					<?php 
					//Only show button to save data when enable to update data
		 			if ((int) $row["het_han_phan_bien"] == 0){ 
		 			?>
					buttons: { "Lưu": function() {
						$('.<?php echo $formKey ?>tabDialogTextAreaPhanBien').each(function(){
							var parentItem = $(this).parent();
							var currentName = $(this).attr('name');
							var currentHtmlValue = $(this).html();
							if (currentHtmlValue != '................................................................................................'){
								if (currentHtmlValue == '<br>'){
									currentHtmlValue = '';
								}
								parentItem.find('textarea[name="' + currentName + '"]').val(currentHtmlValue);
							}
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
								gv_open_msg_box(result.message, 'alert', 345, 185, true);
							},
							error: function (xhr,status,error){
							},
							complete: function(xhr,status){
								$(".<?php echo $formKey ?>ajax-loading-bert").find("#squaresWaveG").hide();
								//Enable save button
								$(".ui-dialog-buttonset").find(":button:contains('Lưu')").prop("disabled", false);
							}
						});
					}},
					<?php } ?>
					close: function() {
						$(".<?php echo $formKey ?>ajax-loading-bert").find("#squaresWaveG").hide();
					}
				});
				
				$("#<?php echo $formKey ?>DialogTabsViewPhanBien").dialog('open');
			},
			complete: function(xhr,status){
				$(".<?php echo $formKey ?>ajax-loading-bert").find("#squaresWaveG").hide();
			}
		});
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
	
}


$(document).ready(function() {
	$('#<?php echo $formKey ?>dataGridTable').dataTable( {
		<?php if (count($listArrayData) > 0) { ?>
		"aaData": [<?php echo "[".implode('],[', $listArrayData)."]"  ?>],
		<?php } ?>
		"aoColumns": [
			{"sClass": "center"},
			null,
			null,
			null,
			{"sClass": "center"},
			{"sClass": "center"},
			{"sClass": "center"}
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
		}
	});
	
	
});
</script>