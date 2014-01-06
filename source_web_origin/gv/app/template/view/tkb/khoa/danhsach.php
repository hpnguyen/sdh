<?php
$pViewAll = isset($viewAll) ? $viewAll : false;
$help = Helper::getHelper('functions/util');
$gvURL = $help->getGvRootURL();

$postUrl = $gvURL."/front.php/tkb/phanbo/save/dothoc/".$dothoc;
$queryString = "?hisid=".$_GET['hisid'];
if ($pViewAll) {
	$canEdit = false;
}else{
	//Check user can edit or not
	date_default_timezone_set('Asia/Ho_Chi_Minh');
	$timeDotHoc = strtotime($dothoc);
	$timeHienTai = strtotime(date('d-m-Y'));
	$canEdit = $timeDotHoc >= $timeHienTai;	
}

?>
<div align="center">
	<h2>Phân công cán bộ giảng dạy <?php echo $pViewAll ? '': ' - cấp Khoa' ?><br/>Học kỳ <span id="phan-bo-head"><?php echo $hk ?></span></h2>
	<?php if (! $pViewAll) { ?>
	<div>
		Thời gian cập nhật đến hết ngày <?php echo $thoihan ?>
	</div>
	<?php } ?>
</div>
<div style="margin:0 0 10px 5px;" align=left><strong>Ngày bắt đầu HK: <span id='ngaybatdauhk'></span></strong></div>	
<?php
$listArrayData = array();
foreach($listItems as $y => $row)
{
	$listArrayDataString = "";
	
	$classAlt = ($y % 2) ? "alt" : "alt_";
	$i = $row["ma_mh"].'_'.$row["lop"];
	$listArrayDataString .= "'<b class=\"special-color\">" .$row['thu']."</b>',";
	$listArrayDataString .= "'".$row["ma_mh"]."',";
	$listArrayDataString .= "'".$row["ten"]."',";
	if ($canEdit) {
		$listArrayDataString .= "'<img src=\"".$help->gvRootImageURL('add-icon16.png')."\" id=\"add-icon-".$i."\" width=\"16px\" height=\"16px\" style=\"".($row["ma_can_bo"] != '' ? 'display:none; ' : '')."\" title=\"Click vào để chọn giảng viên\"/><img src=\"".$gvURL."/icons/delete-icon.png\" id=\"delete-icon-".$i."\" style=\"".($row["ma_can_bo"] != '' ? '' : 'display:none; ')."float: right\"/>',";
	}
	
	if ($canEdit) {
		$listArrayDataString .= "'<div class=\"list-canbo\" rel=\"".$i."\"><span id=\"view-name-".$i."\" title=\"\">".$row["ten_cb_chinh"]."</span><input style=\"width: 163px\" id=\"project-".$i."\" class=\"project project-input\" title=\"Họ tên, số hiệu công chức, khoa, bộ môn\"/><input type=\"hidden\" id=\"project-id-".$i."\" class=\"project-id\" value=\"".$row["ma_can_bo"]."\"/><input type=\"hidden\" id=\"link-url-".$i."\" value=\"/lop/".$row["lop"]."/mamh/".$row["ma_mh"]."\"/><img src=\"".$gvURL."/icons/loader.gif\" id=\"loading-icon-".$i."\" style=\"display:none; float: right\"/></div>',";
	}else{
		$listArrayDataString .= "'".$row["ten_cb_chinh"]."',";
	}
	
	//Can bo giang day phu
	if ($canEdit) {
		$listArrayDataString .= "'<img src=\"".$help->gvRootImageURL('add-icon16.png')."\" id=\"add-icon-phu-".$i."\" width=\"16px\" height=\"16px\" style=\"".($row["ma_can_bo_phu"] != '' ? 'display:none; ' : '')."\" title=\"Click vào để chọn giảng viên\"/><img src=\"".$gvURL."/icons/delete-icon.png\" id=\"delete-icon-phu-".$i."\" style=\"".($row["ma_can_bo_phu"] != '' ? '' : 'display:none; ')."float: right\"/>',";
	}
	
	if ($canEdit) {
		$listArrayDataString .= "'<div class=\"list-canbo-phu\" rel=\"".$i."\">	<span id=\"view-name-phu-".$i."\" title=\"\">".$row["ten_cb_phu"]."</span>	<input style=\"width: 163px\" id=\"project-phu-".$i."\" class=\"project project-input\" title=\"Họ tên, số hiệu công chức, khoa, bộ môn\"/>	<input type=\"hidden\" id=\"project-id-phu-".$i."\" class=\"project-id\" value=\"".$row["ma_can_bo_phu"]."\"/>	<img src=\"".$gvURL."/icons/loader.gif\" id=\"loading-icon-phu-".$i."\" style=\"display:none; float: right\"/>	</div>',";
	}else{
		$listArrayDataString .= "'".$row["ten_cb_phu"]."',";
	}
	if ($canEdit) {
		$listArrayDataString .= "'<img src=\"".$help->gvRootImageURL('add-icon16.png')."\" id=\"add-icon-ghichu-".$i."\" width=\"16px\" height=\"16px\" title=\"Click vào để thêm ghi chú\"/>',";
	}
	
	if ($canEdit) {
		$listArrayDataString .= "'<div class=\"list-ghichu\"><span id=\"view-ghichu-".$i."\" title=\"\">".$row["ghi_chu"]."</span><input type=\"text\" id=\"ghichu-".$i."\" style=\"width:50px;display:none\" value=\"".$row["ghi_chu"]."\"/><img src=\"".$gvURL."/icons/loader.gif\" id=\"loading-icon-ghichu-".$i."\" style=\"display:none; float: right\"/></div>',";
	}else {
		$listArrayDataString .= "'".$row["ghi_chu"]."',";
	}
	
	$listArrayDataString .= "'<input type=\"checkbox\" title=\"".($row["khoa_xet_duyet"] == "1" ? "Đã duyệt" : "Chưa xét duyệt")."\" id=\"khoa-duyet-".$i."\"".($row["khoa_xet_duyet"] == "1" ? " value=\"1\" checked=\"checked\"": " value=\"0\"").($canEdit ? "": "disabled=\"disabled\"")."/>',";
	$listArrayDataString .= "'<b>".$row["lop"]."</b>',";
	$listArrayDataString .= "'".$row["sl"]."',";
	$listArrayDataString .= "'<b class=\"special-color\">".$row["tiet_bat_dau"].'-'.$row["tiet_ket_thuc"]."</b>',";
	$listArrayDataString .= "'".$row["phong"]."',";
	$listArrayDataString .= "'<b>".$row["tuan_hoc"]."</b>',";
	$listArrayDataString .= "'".$row["ten_bo_mon"]."',";
	$listArrayDataString .= "'".$row["chuyen_nganh"]."'";
	$listArrayData[] = $listArrayDataString;
	
}
?>
<input type="hidden" id="printPageURL" value="<?php echo $gvURL.'/front.php/tkb/phanbo/index/dothoc/'.$dothoc.'/makhoa/'.$makhoa.'?hisid='.$_GET['hisid']; ?>"/>
<div id="dialog" title="Xác Nhận">
  Bạn có muốn xóa hay không?
</div>
<div id="dialogWarning" title="Lưu Ý">
  Môn học này vẫn được duyệt trong khi chưa phân công cán bộ giảng dạy chính.
</div>
<div id="dataGridPhanCongCanBo" style="padding-bottom: 50px">
	<table width="100%" cellpadding="0" cellspacing="0" border="0" class="display" id="dataGridTablePhanCongCanBo" style="padding: 0px">
		<thead>
	  <tr class='ui-widget-header heading' style='font-weight:bold; height:20pt;'>
		<td width="23px" align='center' class='ui-corner-tl'>T<br>h<br>ứ</td>
		<td width="48px" align="center">Mã MH</td>
		<td width="15%" align="center">Tên Môn Học</td>
		<?php if ($canEdit) { ?>
		<td width="18px"></td>
		<?php } ?>
		<td width="163px" align="center">Cán bộ giảng dạy chính</td>
		<?php if ($canEdit) { ?>
		<td width="18px"></td>
		<?php } ?>
		<td width="163px" align="center">Cán bộ giảng dạy phụ</td>
		<?php if ($canEdit) { ?>
		<td width="18px"></td>
		<?php } ?>
		<td width="48px" align="center">Ghi<br>chú</td>
		<td width="30px" align="center">Khoa Duyệt</td>
		<td width="23px" align="center">L<br>ớ<br>p</td>
		<td width="18px" align="center">SL Dự kiến</td>
		<td width="43px" align="center">Tiết học</td>
		<td width="43px" align="center">Phòng</td>
		<td width="43px" align="center">Tuần học</td>
		<td width="163px" align="center" >BM Quản lý MH</td>
		<td width="163px" align="center">Chuyên ngành</td>
	  </tr>
	  </thead>
	  <tbody>
	  </tbody>
	</table>
</div>
<script>
<?php if ($canEdit) { ?>
function actionScript(item, projects){
	var name = "";
	var nameList = "";
	if (item == 1){
		name = "phu-";
		nameList ="-phu"; 
	}
	$(".list-canbo" + nameList).each(function(index){
		var myIndex = $(this).attr('rel');
		var url1= '<?php echo $postUrl ?>';
		var url2 = jQuery('#link-url-' + myIndex).val();
		var url3= '/loai/' + (name == '' ? 0 : 1 );
		var url4= '<?php echo $queryString ?>';
		var myUrl = url1 + url2 + url3 + url4;
		
		
		$( "#project-" + name + myIndex ).hide();
		$( "#project-" + name + myIndex ).autocomplete({
			minLength: 0,
			source: projects,
			focus: function( event, ui ) {
				$( "#project-" + name  + myIndex ).val( ui.item.label );
				return false;
			},
			select: function( event, ui ) {
				var curValue = (name != "" ? $( "#project-id-" + myIndex ).val() : $( "#project-id-phu-" + myIndex ).val() );
				
				if(curValue != ui.item.value){
					$.ajax({
						type: "POST",
						url: myUrl,
						data: {cbgd : ui.item.value},
						beforeSend: function(xhr){
							$('#loading-icon-' + name+ myIndex).show();
						},
						success:function(result){
							if(result.status == undefined || result.status == 0){
								alert(result.message);
							}else{
								//fill data
								$( "#view-name-" + name + myIndex ).html( ui.item.label );
								$( "#view-name-" + name + myIndex ).attr("title","Mã cán bộ: " + ui.item.ma_can_bo + ", Bộ môn: " + ui.item.ten_bo_mon + ", Khoa: " + ui.item.ten_khoa);
								$( "#project-" + name + myIndex ).val( ui.item.label );
								$( "#project-id-" + name + myIndex ).val( ui.item.value );
								$('#delete-icon-' + name+ myIndex).show();
								$( "#add-icon-" + name + myIndex ).hide();
							}
						},
						error: function (xhr,status,error){
							alert('error');
						},
						complete: function(xhr,status){
							$('#loading-icon-' + name+ myIndex).hide();
							//hide auto fill input
							$( "#project-" + name + myIndex ).hide();
							//show data
							$( "#view-name-" + name + myIndex ).show();
						}
					});
				}else {
					$('#loading-icon-' + name+ myIndex).hide();
					//hide auto fill input
					$( "#project-" + name + myIndex ).hide();
					//show data
					$( "#view-name-" + name + myIndex ).show();
							
					alert('Cán bộ giảng dạy phụ trùng với cán bộ giảng dạy chính.\nHãy chọn lại.');
				}
				
				return false;
			},
			change:function( event, ui ) {
				var data=$.data(this);
				if(data.autocomplete.selectedItem==undefined){
					$( "#project-" + name + myIndex ).val('');
					$( "#project-" + name + myIndex ).hide();
					$( "#view-name-" + name + myIndex ).show();
				}
			}
		})
		.data( "ui-autocomplete" )._renderItem = function( ul, item ) {
			return $( "<li>" ).append( "<a>" + item.label + "<br><b>" + item.desc + "</b></a><hr>" ).appendTo( ul );
		};
		$( "#add-icon-" + name + myIndex ).click(function(){
			$( "#project-" + name + myIndex ).show();
			$( "#view-name-" + name + myIndex ).hide();
			$( "#project-" + name + myIndex ).focus();
		});
		//Auto complete input lost focus event
		$( "#project-" + name + myIndex ).blur(function(event){
			$( "#project-" + name + myIndex ).hide();
			$( "#view-name-" + name + myIndex ).show();
		});
		
		$("#delete-icon-" + name + myIndex).click(function(){
			$("#dialog").dialog({
				close: function(ev, ui) { 
					//hide auto fill input
					$( "#project-" + name + myIndex ).hide();
					//show data
					$( "#view-name-" + name + myIndex ).show();
				},
				buttons : {
					"Yes" : function() {
						$(this).dialog("close");
						$.ajax({
							type: "POST",
							url: myUrl,
							data: {cbgd : ''},
							beforeSend: function(xhr){
								$('#loading-icon-' + name+ myIndex).show();
							},
							success:function(result){
								if(result.status == undefined || result.status == 0){
									alert(result.message);
								}else{
									//fill data
									$( "#view-name-" + name + myIndex ).html('');
									$( "#view-name-" + name + myIndex ).attr("title","");
									$( "#project-" + name + myIndex ).val('');
									$( "#project-id-" + name + myIndex ).val('');
									$('#delete-icon-' + name+ myIndex).hide();
									$( "#add-icon-" + name + myIndex ).show();
									
									//Uncheck khoa xet duyet
									if ($( "#project-" + myIndex ).val() == '' && $("#khoa-duyet-" + myIndex).prop('checked')){
										$("#khoa-duyet-" + myIndex).prop('checked',false);
									}
								}
							},
							error: function (xhr,status,error){
								$('#loading-icon-' + name+ myIndex).hide();
								//hide auto fill input
								$( "#project-" + name + myIndex ).hide();
								//show data
								$( "#view-name-" + name + myIndex ).show();
								alert('error');
							},
							complete: function(xhr,status){
								$('#loading-icon-' + name+ myIndex).hide();
								//hide auto fill input
								$( "#project-" + name + myIndex ).hide();
								//show data
								$( "#view-name-" + name + myIndex ).show();
								
							}
						});
					},
					"No" : function() {
						$(this).dialog("close");
						//hide auto fill input
						$( "#project-" + name + myIndex ).hide();
						//show data
						$( "#view-name-" + name + myIndex ).show();
					}
				}
			});
			$("#dialog").dialog("open");
			
		});
		
		
		//Add comment
		$('#add-icon-ghichu-' + myIndex).click(function(){
			$('#ghichu-'+ myIndex).show();
			$('#add-icon-ghichu-' + myIndex).hide();
			$('#view-ghichu-' + myIndex).hide();
		});
		
		$('#ghichu-'+ myIndex)
		.focusout(function(){})
		.blur(function() {
			var url1= '<?php echo $postUrl ?>';
			var url2 = jQuery('#link-url-' + myIndex).val() + '/loai/2';
			var url3= '<?php echo $queryString ?>';
			var myUrl = url1 + url2 + url3;
			var myData = {ghichu : $('#ghichu-'+ myIndex).val()};
			$.ajax({
				type: "POST",
				url: myUrl,
				data: myData,
				beforeSend: function(xhr){
					$('#loading-icon-ghichu-' + myIndex).show();
					$('#ghichu-'+ myIndex).hide();
				},
				success:function(result){
					if(result.status == undefined || result.status == 0){
						alert(result.message);
					}else{
						//fill data
						$('#view-ghichu-' + myIndex).html($('#ghichu-'+ myIndex).val());
					}
				},
				error: function (xhr,status,error){
					alert('error');
				},
				complete: function(xhr,status){
					$('#loading-icon-ghichu-' + myIndex).hide();
					//hide auto fill input
					$('#add-icon-ghichu-' + myIndex).show();
					$('#view-ghichu-' + myIndex).show();
				}
			});
		});
		
		//Khoa duyet action
		$('#khoa-duyet-'+ myIndex).click(function(){
			// var allowPost = false;
			if ($(this).prop('checked')){
				if ($( "#project-id-" + myIndex ).val() == ''){
					// $(this).val(0);
					// $(this).prop('checked',false);
					// alert('Vui lòng phân công cán bộ giảng dạy chính trước khi duyệt.');
					$("#dialogWarning").dialog("open");
				}
				$(this).val(1);
				// allowPost = true;
			}else{
				$(this).val(0);
				// allowPost = true;
			}
			
			// if (allowPost){
				var url1= '<?php echo $postUrl ?>';
				var url2 = jQuery('#link-url-' + myIndex).val() + '/loai/3';
				var url3= '<?php echo $queryString ?>';
				var myUrl = url1 + url2 + url3;
				var myData = {duyet : $(this).val()};
				
				if ($(this).val() == '1'){
					$('#khoa-duyet-'+ myIndex).attr('title','Đã duyệt');
				}else{
					$('#khoa-duyet-'+ myIndex).attr('title','Chưa xét duyệt');
				}
				
				$.ajax({
					type: "POST",
					url: myUrl,
					data: myData,
					beforeSend: function(xhr){
						$('#khoa-duyet-'+ myIndex).hide();
					},
					success:function(result){
						if(result.status == undefined || result.status == 0){
							alert(result.message);
						}
					},
					error: function (xhr,status,error){
						alert('error');
					},
					complete: function(xhr,status){
						$('#khoa-duyet-'+ myIndex).show();
					}
				});
			// }
		});
	});
}
<?php } ?>
$(document).ready(function() {
	$("#dialog").dialog({
      autoOpen: false,
      modal: true,
      resizable: false,
    });
	$("#dialogWarning").dialog({
      autoOpen: false,
      modal: true,
      resizable: false,
    });
	
	<?php 
	$autoCompleteArrayData = '';
	if ($canEdit) {
		foreach($listCanBo as $i => $row){
			$autoCompleteArrayData .= '{
			value: "'.$row['ma_can_bo'].'",
			label: "'.$row['cbgd'].'",
			desc: "'.$row['shcc'].', '.$row['ten_bo_mon'].', '.$row['ten_khoa'].'",
			ma_can_bo: "'.$row['shcc'].'",
			ten_bo_mon: "'.$row['ten_bo_mon'].'",
			ten_khoa: "'.$row['ten_khoa'].'",
			},';
		}
	} 
	?>
    $('#dataGridTablePhanCongCanBo').dataTable( {
    	<?php if (count($listArrayData) > 0) { ?>
		"aaData": [<?php echo "[".implode('],[', $listArrayData)."]"  ?>],
		<?php } ?>
        "aoColumns": [
            {"sClass": "center"},
            null,
            null,
            <?php if ($canEdit) { ?>
            { "bSortable": false},
            <?php } ?>
            null,
            <?php if ($canEdit) { ?>
            {"bSortable": false},
            <?php } ?>
            null,
            <?php if ($canEdit) { ?>
            {"bSortable": false},
            <?php } ?>
            null,
            {"sClass": "center"},
            {"sClass": "center"},
            {"sClass": "center"},
            {"sClass": "center"},
            {"sClass": "center"},
            {"sClass": "center"},
            null,
            null,
            // {"bSortable": false}
        ],
        <?php if ($canEdit) { ?>
         "aaSorting": [[16,'asc'], [15,'asc'], [0,'asc'], [12,'asc'], [2,'asc'], [10,'asc']],
         <?php }else{ ?> 
         "aaSorting": [[13,'asc'], [12,'asc'], [0,'asc'], [9,'asc'], [2,'asc'], [7,'asc']],
         <?php } ?>
        //"bStateSave": true,
		"bAutoWidth": false, 
		"sPaginationType": "full_numbers",
		"oLanguage": {"sUrl": "<?php echo $help->baseURL() ?>/datatable/media/language/vi_VI.txt"},
		<?php if ($canEdit) { ?>
		"fnInitComplete": function(oSettings, json) {
			var projects = [
			<?php echo $autoCompleteArrayData  ?>
			];
	    	//Can bo chinh
			actionScript(0, projects);
			//Can bo phu
			actionScript(1, projects);
		},
		"fnDrawCallback": function() {
			var projects = [
			<?php echo $autoCompleteArrayData  ?>
			];
	    	//Can bo chinh
			actionScript(0, projects);
			//Can bo phu
			actionScript(1, projects);
		}
		<?php } ?>
	});
});
</script>