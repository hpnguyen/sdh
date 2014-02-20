<?php
$help = Helper::getHelper('functions/util');
$gvURL = $help->getGvRootURL();
$postUrl = $gvURL."/front.php/admin/system/change";
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
		height: 15px;
	}
</style>
<div align="center">
	<h2>Danh sách cán bộ</h2>
</div>
<div align="left">
	Double click lên từng dòng để xem password hoặc reset password
</div>
<?php
$listArrayData = array();

foreach($listItems as $y => $row)
{
	$listArrayDataString = "";
	$i = $row["id"];
	$listArrayDataString .= "'" .$row['id']."',";
	$listArrayDataString .= "'".$row["fk_ma_can_bo"]."',";
	$listArrayDataString .= "'".$row["shcc"]."',";
	$listArrayDataString .= "'".$row["ten_can_bo"]."<input id=\"password-".$row["username"]."\" type=\"hidden\" class=\"dialogShow\" value=\"".$row["password"]."\" />',";
	$listArrayDataString .= "'".$row["username"]."',";
	$listArrayDataString .= "'".$row["ten_bo_mon"]."',";
	$listArrayDataString .= "'".$row["khoa"]."</b>'";
	$listArrayData[] = $listArrayDataString;
	
}
?>
<div id="dialogDetail" title="Chi Tiết">
	<div><span class="titleBold">ID : </span><span class="idDetail"></span></div>
	<div><span class="titleBold">Mã CB : </span><span class="macanboDetail"></span></div>
	<div><span class="titleBold">SHCC : </span><span class="shccDetail"></span></div>
	<div><span class="titleBold">Tên cán bộ : </span><span class="tencanboDetail"></span></div>
	<div><span class="titleBold">User : </span><span class="usernameDetail"></span></div>
	<div><span class="titleBold">Password : </span><span class="passwordDetail"></span></div>
	<div><span class="titleBold">Tên bộ môn : </span><span class="tenbomonDetail"></span></div>
	<div><span class="titleBold">Khoa : </span><span class="khoaDetail"></span></div>
	<div align="center" class="ajax-loading-bert">
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
<div id="dataGridDanhSachCanBo" style="padding-bottom: 50px">
	<table width="100%" cellpadding="0" cellspacing="0" border="0" class="display" id="dataGridTableDanhSachCanBo" style="padding: 0px">
		<thead>
			<tr class='ui-widget-header heading' style='font-weight:bold; height:20pt;'>
				<td align="center" width="30px">ID</td>
				<td align="center" width="60px">Mã CB</td>
				<td align="center" width="60px">SHCC</td>
				<td width="163px" align="center">Tên cán bộ</td>
				<td align="center" width="70px">Username</td>
				<td align="center">Tên bộ môn</td>
				<td align="center">Khoa</td>
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
	
	//setting dialog box
	$("#dialogDetail").dialog({
		autoOpen: false,
		modal: true,
		resizable: false,
		height: 260,
		buttons: { "Reset Password": function() {
			var t = $(this).find('span.usernameDetail').text();
			var shcc = $(this).find('span.shccDetail').text();
			
			$.ajax({
				type: "POST",
				url: '<?php echo $postUrl ?>',
				data: {p : t},
				beforeSend: function(xhr){
					$(".ui-dialog-buttonpane button:contains('Reset Password')")
					.attr("disabled", true)
					.addClass("ui-state-disabled");
					$("#squaresWaveG").show();
				},
				success:function(result){
					//console.log(result);
					$("#dialogDetail").find('#ajaxLoadingBertMessage').html(result.message);
					if(result.status == '1'){
						$("#dialogDetail").find('#ajaxLoadingBertMessage').append('<div>Password mặc định sau khi reset là số hiệu công chức theo qui định mới ' + shcc +'</div>');
						$("#password-" + t).val(shcc);
					}
				},
				error: function (xhr,status,error){
				},
				complete: function(xhr,status){
					$("#squaresWaveG").hide();
					$(".ui-dialog-buttonpane button:contains('Reset Password')")
					.attr("disabled", false)
					.removeClass("ui-state-disabled");
				}
			});
		}} 
	});
	
	$('#dataGridTableDanhSachCanBo').dataTable( {
		<?php if (count($listArrayData) > 0) { ?>
		"aaData": [<?php echo "[".implode('],[', $listArrayData)."]"  ?>],
		<?php } ?>
		"aoColumns": [
			{"sClass": "center"},
			{"sClass": "center"},
			{"sClass": "center"},
			{"sClass": "center"},
			{"sClass": "center"},
			{"sClass": "center"},
			{"sClass": "center"}
		],
		"aaSorting": [[7,'asc'], [6,'asc'], [3,'asc']],
		"bAutoWidth": false, 
		"sPaginationType": "full_numbers",
		"oLanguage": {"sUrl": "<?php echo $help->baseURL() ?>/datatable/media/language/vi_VI.txt"},
		"fnDrawCallback": function() {
			var oTable = $("#dataGridTableDanhSachCanBo");
			$("#dataGridTableDanhSachCanBo tr").on("click",function(event) {
				$("#dataGridTableDanhSachCanBo tr").each(function (){
					$(this).removeClass('row_selected');
				});
				
				$(this).addClass('row_selected');
			});
			
			$("#dataGridTableDanhSachCanBo tbody tr").on("dblclick",function() {
				//Show popup detail
				var id = $(this).find('td:eq(0)').text();
				var macanbo = $(this).find('td:eq(1)').text();
				var shcc = $(this).find('td:eq(2)').text();
				var tencanbo = $(this).find('td:eq(3)').text();
				var user = $(this).find('td:eq( 4 )').text();
				var pass = $(this).find('td:eq(3)').find('input.dialogShow').val();
				var tenbomon = $(this).find('td:eq( 5 )').text();
				var khoa = $(this).find('td:eq(6)').text();
				
				$("#dialogDetail").find('span.idDetail').html(id);
				$("#dialogDetail").find('span.macanboDetail').html(macanbo);
				$("#dialogDetail").find('span.shccDetail').html(shcc);
				$("#dialogDetail").find('span.tencanboDetail').html(tencanbo);
				$("#dialogDetail").find('span.usernameDetail').html(user);
				$("#dialogDetail").find('span.passwordDetail').html(pass);
				$("#dialogDetail").find('span.tenbomonDetail').html(tenbomon);
				$("#dialogDetail").find('span.khoaDetail').html(khoa);
				$("#dialogDetail").find('#ajaxLoadingBertMessage').html('');
				$("#dialogDetail").dialog('open');
				
			});
		}
	});
	
});
</script>