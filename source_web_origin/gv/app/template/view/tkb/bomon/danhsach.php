<?php
$help = Helper::getHelper('functions/util');
$gvURL = $help->getGvRootURL();

$postUrl = $gvURL."/front.php/tkb/phanbo/savebomon/dothoc/".$dothoc;
$queryString = "?hisid=".$_GET['hisid'];

//Check user can edit or not
date_default_timezone_set('Asia/Ho_Chi_Minh');
$timeDotHoc = strtotime($dothoc);
$timeHienTai = strtotime(date('d-m-Y'));
$canEdit = $timeDotHoc >= $timeHienTai ; //Dot hoc chua den
$canEdit = $canEdit || ($timeDotHoc < $timeHienTai && $hetHanCapNhat == false) ; //Dot hoc da mo nhung van cho phep sua doi
?>
<div align="center">
	<h2>Phân công cán bộ giảng dạy - cấp Bộ môn<br/>Học kỳ <span id="phan-bo-head"><?php echo $hk ?></span></h2>
	<div>
		Thời gian cập nhật đến hết ngày <?php echo $thoihan ?>
	</div>
</div>	
<div style="margin:0 0 10px 5px;" align=left><strong>Ngày bắt đầu HK: <span id='ngaybatdauhk'></span></strong></div>
	
<?php
$listArrayData = array();
foreach($listItems as $y => $row)
{
	$listArrayDataString = "";
	
	$classAlt = ($y % 2) ? "alt" : "alt_";
	$i = $row["ma_mh"].'_'.$row["lop"].'_'.$row["thu"].'_'.$row["dot_hoc"].'_'.$row["tiet_bat_dau"].'_'.$row["tuan_bat_dau"];
	$theSameId = $row["ma_mh"].'_'.$row["lop"];
	$listArrayDataString .= "'<b class=\"special-color\">" .$row['thu']."</b>',";
	$listArrayDataString .= "'".$row["ma_mh"]."',";
	$listArrayDataString .= "'".$row["ten"]."',";
	
	$checkEnable = $canEdit && $row['khoa_duoc_pc_cbgd'] == '1';
	
	//Can bo giang day chinh
	if ($checkEnable) {
		$listArrayDataString .= "'<img src=\"".$help->gvRootImageURL('add-icon16.png')."\" order=\"".$y."\" id=\"add-icon-".$i."\" width=\"16px\" height=\"16px\" style=\"".($row["ma_can_bo"] != '' ? 'display:none; ' : '')."\" title=\"Click vào để chọn giảng viên\"/>		<img src=\"".$gvURL."/icons/delete-icon.png\" id=\"delete-icon-".$i."\" style=\"".($row["ma_can_bo"] != '' ? '' : 'display:none; ')."float: right\"/>',";
	}else{
		$listArrayDataString .= "'',";
	}
	
	if ($checkEnable) {
		$listArrayDataString .= "'<div class=\"list-canbo check-the-same-".$theSameId."-canbo\" order=\"".$y."\" relsame=\"check-the-same-".$theSameId."\" rel=\"".$i."\"><span id=\"view-name-".$i."\" title=\"\">".$row["ten_cb_chinh"]."</span>	<input style=\"width: 163px\" id=\"project-".$i."\" class=\"project project-input\" title=\"Họ tên, số hiệu công chức, khoa, bộ môn\"/>	<input type=\"hidden\" id=\"project-id-".$i."\" class=\"project-id\" value=\"".$row["ma_can_bo"]."\"/>	<input type=\"hidden\" id=\"link-url-".$i."\" value=\"/lop/".$row["lop"]."/mamh/".$row["ma_mh"]."\"/>	<img src=\"".$gvURL."/icons/loader.gif\" id=\"loading-icon-".$i."\" style=\"display:none; float: right\"/>	</div>',";
	}else{
		$listArrayDataString .= "'".$row["ten_cb_chinh"]."',";
	}
	
	//Can bo giang day phu
	if ($checkEnable) {
		$listArrayDataString .= "'<img src=\"".$help->gvRootImageURL('add-icon16.png')."\" order=\"".$y."\" id=\"add-icon-phu-".$i."\" width=\"16px\" height=\"16px\" style=\"".($row["ma_can_bo_phu"] != '' ? 'display:none; ' : '')."\" title=\"Click vào để chọn giảng viên\"/>		<img src=\"".$gvURL."/icons/delete-icon.png\" id=\"delete-icon-phu-".$i."\" style=\"".($row["ma_can_bo_phu"] != '' ? '' : 'display:none; ')."float: right\"/>',";
	}else{
		$listArrayDataString .= "'',";
	}
	
	if ($checkEnable) {
		$listArrayDataString .= "'<div class=\"list-canbo-phu check-the-same-".$theSameId."-canbo-phu\" order=\"".$y."\" relsame=\"check-the-same-".$theSameId."\" rel=\"".$i."\">	<span id=\"view-name-phu-".$i."\" title=\"\">".$row["ten_cb_phu"]."</span>	<input style=\"width: 163px\" id=\"project-phu-".$i."\" class=\"project project-input\" title=\"Họ tên, số hiệu công chức, khoa, bộ môn\"/>	<input type=\"hidden\" id=\"project-id-phu-".$i."\" class=\"project-id\" value=\"".$row["ma_can_bo_phu"]."\"/>	<img src=\"".$gvURL."/icons/loader.gif\" id=\"loading-icon-phu-".$i."\" style=\"display:none; float: right\"/>	</div>',";
	}else{
		$listArrayDataString .= "'".$row["ten_cb_phu"]."',";
	}
	
	if ($checkEnable) {
		$listArrayDataString .= "'<img src=\"".$help->gvRootImageURL('add-icon16.png')."\" order=\"".$y."\" id=\"add-icon-ghichu-".$i."\" width=\"16px\" height=\"16px\" title=\"Click vào để thêm ghi chú\"/>',";
	}else{
		$listArrayDataString .= "'',";
	}
	
	$text_ghi_chu = str_replace(array("\r", "\n"), '<br>', $row["ghi_chu"]);
	if ($checkEnable) {
		$listArrayDataString .= "'<div class=\"list-ghichu check-the-same-".$theSameId."-ghichu\" order=\"".$y."\">	<span order=\"".$y."\" id=\"view-ghichu-".$i."\" title=\"\">".$text_ghi_chu."</span>	<input type=\"text\" id=\"ghichu-".$i."\" style=\"width:50px;display:none\" value=\"".$text_ghi_chu."\"/>	<img src=\"".$gvURL."/icons/loader.gif\" id=\"loading-icon-ghichu-".$i."\" style=\"display:none; float: right\"/>	</div>',";
	}else {
		$listArrayDataString .= "'".$text_ghi_chu."',";
	}
	$urlDsLop = $gvURL.'/front.php/tkb/phancong/dslop/dothoc/'.$dothoc.'/mamh/'.$row["ma_mh"].'/lop/'.$row["lop"].'?hisid='.$_GET['hisid'];
	$listArrayDataString .= "'<b>".$row["lop"]."</b> / <a href=\"".$urlDsLop."\" target=\"_blank\" title=\"Danh sách lớp\"><img border=\"0\" width=\"16\" height=\"16\" src=\"icons/save-icon.png\" style=\"margin-bottom:-5px\" /></a>',";
	$listArrayDataString .= "'".$row["sl"]."',";
	$listArrayDataString .= "'<b class=\"special-color\">".$row["tiet_bat_dau"].'-'.$row["tiet_ket_thuc"]."</b>',";
	$listArrayDataString .= "'".$row["phong"]."',";
	$listArrayDataString .= "'<b>".$row["tuan_hoc"]."</b>',";
	$listArrayDataString .= "'".$row["ten_bo_mon"]."',";
	$listArrayDataString .= "'".$row["chuyen_nganh"]."',";
	$listArrayDataString .= "'".$row["khoi_ktbs"]."'";
	$listArrayData[] = $listArrayDataString;
} 
?>
<input type="hidden" id="printPageURLBoMon" value="<?php echo $gvURL.'/front.php/tkb/phanbo/indexbomon/dothoc/'.$dothoc.'/makhoa/'.$makhoa.'?hisid='.$_GET['hisid']; ?>"/>
<div id="dialog" title="Xác Nhận">
  Bạn có muốn xóa hay không?
</div>
<div id="dialogMessage" title="Lưu Ý">
</div>
<div id="dataGridPhanCongCanBo" style="padding-bottom: 50px">
	<table width="100%" cellpadding="0" cellspacing="0" border="0" class="display" id="dataGridTablePhanCongCanBo" style="padding: 0px">
		<thead>
	  <tr class='ui-widget-header heading' style='font-weight:bold; height:20pt;'>
		<td width="23px" align='center' class='ui-corner-tl'>T<br>h<br>ứ</td>
		<td width="48px" align="center">Mã MH</td>
		<td width="15%" align="center">Tên Môn Học</td>
		<td width="18px"></td>
		<td width="163px" align="center">Cán bộ giảng dạy chính</td>
		<td width="18px"></td>
		<td width="163px" align="center">Cán bộ giảng dạy phụ</td>
		<td width="18px"></td>
		<td width="48px" align="center">Ghi chú</td>
		<td width="65px" align="center">Lớp/DS Lớp</td>
		<td width="18px" align="center">SL Dự kiến</td>
		<td width="43px" align="center">Tiết học</td>
		<td width="43px" align="center">Phòng</td>
		<td width="43px" align="center">Tuần học</td>
		<td width="163px" align="center" >BM Quản lý MH</td>
		<td width="163px" align="center">Chuyên ngành</td>
		<td width="18px" align="center">KT BS</td>
	  </tr>
	  </thead>
	  <tbody>
	  </tbody>
	</table>
</div>
<script>
function actionScript(item, projects){
	var name = "";
	var nameList = "";
	if (item == 1){
		name = "phu-";
		nameList ="-phu"; 
	}
	$(".list-canbo" + nameList).each(function(index){
		var myIndex = $(this).attr('rel');
		var myIndexSecond = $(this).attr('relsame');
		var myIndexOrder = $(this).attr('order');
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
								//alert(result.message);
								$("#dialogMessage").html(result.message);
								$("#dialogMessage").dialog("open");
							}else{
								//fill data
								$( "#view-name-" + name + myIndex ).html( ui.item.label );
								$( "#view-name-" + name + myIndex ).attr("title","Mã cán bộ: " + ui.item.ma_can_bo + ", Bộ môn: " + ui.item.ten_bo_mon + ", Khoa: " + ui.item.ten_khoa);
								$( "#project-" + name + myIndex ).val( ui.item.label );
								$( "#project-id-" + name + myIndex ).val( ui.item.value );
								$('#delete-icon-' + name+ myIndex).show();
								$( "#add-icon-" + name + myIndex ).hide();
								//Check the same class
								//Canbo
								$( "." + myIndexSecond + "-canbo" + nameList  ).each(function(index2){
									$currentItem = $(this);
									if($currentItem.attr('order') != myIndexOrder){
										var myIndexCheckSame = $(this).attr('rel');
										$( "#view-name-" + name + myIndexCheckSame ).html( ui.item.label );
										$( "#view-name-" + name + myIndexCheckSame ).attr("title","Mã cán bộ: " + ui.item.ma_can_bo + ", Bộ môn: " + ui.item.ten_bo_mon + ", Khoa: " + ui.item.ten_khoa);
										$( "#project-" + name + myIndexCheckSame ).val( ui.item.label );
										$( "#project-id-" + name + myIndexCheckSame ).val( ui.item.value );
										$('#delete-icon-' + name+ myIndexCheckSame).show();
										$( "#add-icon-" + name + myIndexCheckSame ).hide();
									}
								});
							}
						},
						error: function (xhr,status,error){
							//alert('error');
							$("#dialogMessage").html('error');
							$("#dialogMessage").dialog("open");
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
					//Check the same class
					//Canbo
					$( "." + myIndexSecond + "-canbo" + nameList  ).each(function(index2){
						$currentItem = $(this);
						if($currentItem.attr('order') != myIndexOrder){
							var myIndexCheckSame = $(this).attr('rel');
							$( "#project-" + name + myIndexCheckSame ).val('');
							$( "#project-" + name + myIndexCheckSame ).hide();
							$( "#view-name-" + name + myIndexCheckSame ).show();
						}
					});
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
									//Canbo
									$( "." + myIndexSecond + "-canbo" + nameList  ).each(function(index2){
										$currentItem = $(this);
										if($currentItem.attr('order') != myIndexOrder){
											var myIndexCheckSame = $(this).attr('rel');
											$( "#view-name-" + name + myIndexCheckSame ).html('');
											$( "#view-name-" + name + myIndexCheckSame ).attr("title","");
											$( "#project-" + name + myIndexCheckSame ).val('');
											$( "#project-id-" + name + myIndexCheckSame ).val('');
											$('#delete-icon-' + name+ myIndexCheckSame).hide();
											$( "#add-icon-" + name + myIndexCheckSame ).show();
										}
									});
								}
							},
							error: function (xhr,status,error){
								$('#loading-icon-' + name+ myIndex).hide();
								//hide auto fill input
								$( "#project-" + name + myIndex ).hide();
								//show data
								$( "#view-name-" + name + myIndex ).show();
								//alert('error');
								$("#dialogMessage").html('error');
								$("#dialogMessage").dialog("open");
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
						// alert(result.message);
						$("#dialogMessage").html(result.message);
						$("#dialogMessage").dialog("open");
					}else{
						//fill data
						$('#view-ghichu-' + myIndex).html($('#ghichu-'+ myIndex).val());
						//Ghichu
						$( "." + myIndexSecond + "-ghichu" ).each(function(index){
							$currentItem = $(this);
							if($currentItem.attr('order') != myIndexOrder){
								$("#" + $currentItem.find('span').attr('id')).html($('#ghichu-'+ myIndex).val());
							}
						});
					}
				},
				error: function (xhr,status,error){
					// alert('error');
					$("#dialogMessage").html('error');
					$("#dialogMessage").dialog("open");
				},
				complete: function(xhr,status){
					$('#loading-icon-ghichu-' + myIndex).hide();
					//hide auto fill input
					$('#add-icon-ghichu-' + myIndex).show();
					$('#view-ghichu-' + myIndex).show();
				}
			});
		});
	});
}


$(document).ready(function() {
	$("#dialog").dialog({
		      autoOpen: false,
		      modal: true,
		      resizable: false,
		    });
	$("#dialogMessage").dialog({
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
            null,
            null,
            null,
            { "bSortable": false},
            null,
            {"bSortable": false},
            null,
            {"bSortable": false},
            {"sClass": "center"},
            {"sClass": "center"},
            {"sClass": "center"},
            {"sClass": "center"},
            {"sClass": "center"},
            {"sClass": "center"},
            null,
            null,
            {"sClass": "center"}
        ],
        //"aaSorting": [[15,'asc'], [14,'asc'], [0,'asc'], [11,'asc'], [2,'asc'], [9,'asc']],
        "aaSorting": [[16,'desc']],
        //"bStateSave": true,
		"bAutoWidth": false, 
		"sPaginationType": "full_numbers",
		"oLanguage": {"sUrl": "<?php echo $help->baseURL() ?>/datatable/media/language/vi_VI.txt"},
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
	});
});
</script>