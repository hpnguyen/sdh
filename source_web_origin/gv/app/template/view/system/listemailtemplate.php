<?php
$help = Helper::getHelper('functions/util');
$gvURL = $help->getGvRootURL();
$postUrl = $gvURL."/front.php/admin/mail/change";
$queryString = "?hisid=".$_GET['hisid'];
?>
<style type="text/css">
	table.dataTable tr.row_selected td.sorting { background-color:  #075385; }
	table.dataTable tr.row_selected td.sorting_1 { background-color:  #075385; }
	table.dataTable tr.row_selected td.sorting_2 { background-color:  #075385; }
	table.dataTable tr.row_selected td.sorting_3 { background-color:  #075385; }
	table.dataTable tr.row_selected td.sorting_4 { background-color:  #075385; }
	table.dataTable tr.row_selected td.sorting_5 { background-color:  #075385; }
	table.dataTable tr.row_selected td.sorting_6 { background-color:  #075385; }
	table.dataTable tr.row_selected td.sorting_7 { background-color:  #075385; }
	table.dataTable tr.row_selected td.sorting_8 { background-color:  #075385; }
	.dialogShow, .usernameDialogShow {
		display: none;
	}
	.titleBold, .passwordDetail, .messageAlert{
		font-weight: bold;
	}
	.passwordDetail, .messageAlert{
		color: red;
	}
	.btnResetPassword {
		padding: 10px 0;
	}
	.ajax-loading-bert {
		height: 50px;
	}
	div.tableColumnRow {
		padding: 5px 0px;
	}
	div.tableColumnName {
		width: 30%;
		float: left
	}
	div.tableColumnDetail {
		width: 70%;
		float: left
	}
	div.tableColumnDetail input{
		font-size: 1em;
		width: 315px;
	}
	div.tableColumnGeneral {
		height: 30px;
	}
	div.tableColumnContent {
		height: 60px;
	}
	textarea.emailTemplateContent {
		display: none;
	}
</style>
<div align="center">
	<h2>Danh sách email</h2>
</div>
<div align="left">
	Double click lên từng dòng để cập nhật nội dung
</div>
<?php
$listArrayData = array();

foreach($listItems as $y => $row)
{
	$listArrayDataString = "";
	$i = $row["id"];
	$listArrayDataString .= "'" .$row['id']."',";
	$listArrayDataString .= "'".$row["title"]."',";
	$listArrayDataString .= "'".$row["general_comment"]."',";
	$listArrayDataString .= "'".$row["t_created_at"]."',";
	$listArrayDataString .= "'".$row["t_updated_at"]."'";
	$listArrayData[] = $listArrayDataString;
	?>
	<textarea id="txtarea-<?php echo $row['id'] ?>" class="emailTemplateContent"><?php echo $row["content"];	?></textarea>
	<?php
}
?>
<div id="dialogDetail" title="Chi Tiết">
	<form name="ajaxform" id="ajaxform" action="<?php echo $postUrl ?>" method="POST">
		<div class="tableColumnRow tableColumnGeneral">
			<div class="tableColumnName"><span class="titleBold">ID : </span></div>
			<div class="tableColumnDetail">
				<span class="idDetail"></span>
				<input type="hidden" id="email_template_id" class="idDetail" name="data[id]" value="" />
			</div>
		</div>
		<div class="tableColumnRow tableColumnGeneral">
			<div class="tableColumnName"><span class="titleBold">Tiêu đề email : </span></div>
			<div class="tableColumnDetail">
				<input id="email_template_title" name="data[title]" type="text" class="titleDetail" value=""/>
			</div>
		</div>
		<div class="tableColumnRow tableColumnGeneral">
			<div class="tableColumnName"><span class="titleBold">Chú thích : </span></div>
			<div class="tableColumnDetail">
			<input id="email_template_general_comment" name="data[general_comment]" type="text" class="general_commentDetail" value=""/>
			</div>
		</div>
		<div class="tableColumnRowContent">
			<div class="tableColumnName"><span class="titleBold">Nội dung : </span></div>
			<div class="tableColumnDetail"></div>
		</div>
		<div style="clear: both"></div>
		<div>
			<textarea id="email_template_content" name="data[content]" class="contentDetail"></textarea>
		</div>
	</form>
	<div align="center" class="ajax-loading-bert tableColumnRow">
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
	<br>
	<br>
</div>
<div id="dataGridDanhSachEmailTemplate" style="padding-bottom: 50px">
	<table width="100%" cellpadding="0" cellspacing="0" border="0" class="display" id="dataGridTableDanhSachEmailTemplate" style="padding: 0px">
		<thead>
			<tr class='ui-widget-header heading' style='font-weight:bold; height:20pt;'>
				<td align="center" width="100px">ID</td>
				<td align="center" width="300px">Tiêu đề email</td>
				<td align="center">Chú thích</td>
				<td align="center" width="150px">Ngày tạo</td>
				<td align="center" width="150px">Ngày cập nhật</td>
			</tr>
		</thead>
		<tbody>
		</tbody>
	</table>
</div>
<script>
$(document).ready(function() {
	//hide ajax loading
	$("#squaresWaveG").hide();
	
	//callback handler for form submit
	$("#ajaxform").submit(function(e) {
		var postData = $(this).serializeArray();
		var formURL = $(this).attr("action");
		$.ajax(
		{
			url : formURL,
			type: "POST",
			data : postData,
			beforeSend: function(xhr){
				$(".ui-dialog-buttonpane button:contains('Update')")
				.attr("disabled", true)
				.addClass("ui-state-disabled");
				$("#squaresWaveG").show();
			},
			success:function(data, textStatus, jqXHR)
			{
				//data: return data from server
				if(data.status == '1'){
					$("#dialogDetail").find('#ajaxLoadingBertMessage').html('<div>Cập nhật thành công</div>');
					$('tr.row_selected td').eq(1).html($('#email_template_title').val());
					$('tr.row_selected td').eq(2).html($('#email_template_general_comment').val());
					$('tr.row_selected td').eq(4).html(data.updated_at);
					$('#txtarea-' + $('#email_template_id').val()).val($('#email_template_content').val());
				}else{
					$("#dialogDetail").find('#ajaxLoadingBertMessage').append('<div>Lỗi : ' + data.message + '</div>');
				}
			},
			error: function(jqXHR, textStatus, errorThrown)
			{
				//if fails
			},
			complete: function(xhr,status){
				$("#squaresWaveG").hide();
				$(".ui-dialog-buttonpane button:contains('Update')")
				.attr("disabled", false)
				.removeClass("ui-state-disabled");
			}
		});
		e.preventDefault();
	});
	
	//setting dialog box
	$("#dialogDetail").dialog({
		autoOpen: false,
		modal: true,
		resizable: true,
		width: 580,
		height: 550,
		buttons: { "Update": function() {
			$("#ajaxform").submit(); //Submit  the FORM
		}} 
	});
	
	$('#dataGridTableDanhSachEmailTemplate').dataTable( {
		<?php if (count($listArrayData) > 0) { ?>
		"aaData": [<?php echo "[".implode('],[', $listArrayData)."]"  ?>],
		<?php } ?>
		"aoColumns": [
			{"sClass": "left"},
			{"sClass": "left"},
			{"sClass": "left"},
			{"sClass": "center"},
			{"sClass": "center"}
		],
		"bAutoWidth": false, 
		"sPaginationType": "full_numbers",
		"oLanguage": {"sUrl": "<?php echo $help->baseURL() ?>/datatable/media/language/vi_VI.txt"},
		"fnDrawCallback": function() {
			var oTable = $("#dataGridTableDanhSachEmailTemplate");
			$("#dataGridTableDanhSachEmailTemplate tr").on("click",function(event) {
				$("#dataGridTableDanhSachEmailTemplate tr").each(function (){
					$(this).removeClass('row_selected');
				});
				
				$(this).addClass('row_selected');
			});
			
			$("#dataGridTableDanhSachEmailTemplate tbody tr").on("dblclick",function() {
				//Show popup detail
				var id = $(this).find('td:eq(0)').text();
				var title = $(this).find('td:eq(1)').text();
				var general_comment = $(this).find('td:eq(2)').text();
				var created_at = $(this).find('td:eq(3)').text();
				var updated_at = $(this).find('td:eq( 4 )').text();
				var content = $('#txtarea-'+id).val();
				
				
				$("#dialogDetail").find('span.idDetail').html(id);
				$("#dialogDetail").find('input.idDetail').val(id);
				$("#dialogDetail").find('input.titleDetail').val(title);
				$("#dialogDetail").find('input.general_commentDetail').val(general_comment);
				$("#dialogDetail").find('textarea.contentDetail').val(content);
				$("#dialogDetail").find('#ajaxLoadingBertMessage').html('');
				
				//CKeditor tool to edit content
				var editorTool = $('#email_template_content' ).ckeditor(function( textarea ) {},
					{
						toolbar: [
							{ name: 'document',	items: [ 'Source', 'Preview']},	
							{ name: 'clipboard', groups: [ 'clipboard', 'undo' ], items: ['Paste', 'PasteText', 'PasteFromWord', '-', 'Undo', 'Redo' ] },
							{ name: 'basicstyles', groups: [ 'basicstyles', 'cleanup' ],items: [ 'Bold', 'Italic', 'Underline']},
							{ name: 'links', items: [ 'Link', 'Unlink'] },
							{ name: 'styles', items: [ 'FontSize' ]	},
							{ name: 'colors', items: [ 'TextColor'] }
						],
						entities_latin : false,
						resize_enabled : false
					}
				).val(content);
				
	
				$("#dialogDetail").dialog('open');
				
			});
		}
	});
	
});
</script>