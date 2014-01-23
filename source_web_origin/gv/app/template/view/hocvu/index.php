<?php
$help = Helper::getHelper('functions/util');
$gvURL = $help->getGvRootURL();
?>
<div align="center">
	<h2>Danh sách tiến trình hồ sơ</h2>
</div>
<!-- Filter -->
<div style='margin:0 0 10px 0px;'> 
	<table width="100%" border="0" align="center" cellpadding="5"  cellspacing="0" class="ui-widget ui-widget-content ui-corner-all ">
		<tr>
			<td style="width:170px;">
				Năm nhận <select id="filter_tien_trinh_ho_so_nam_nhan" title="Fillter theo năm nhận hồ sơ" style="width:50px; height:25px; padding: 0 0 0 0;" class="ui-widget-content ui-corner-all tableData">
				<?php 
					$nam =date("Y");
					for ($i = $nam; $i > ($nam-5); $i--)
					{
						if ($i == $namNhan)
							$selected = "selected";
						else
							$selected = "";
							
						echo "<option value='".$i."' $selected>" .$i. "</option>";
					}
				  ?>
				</select>
			</td>
			<td>
				Tình trạng <select id="filter_tien_trinh_ho_so_tinh_trang" title="Fillter theo tình trạng HS" style="width:90%; height:25px; padding: 0 0 0 0;" class="ui-widget-content ui-corner-all tableData" >
				<?php
					echo "<option value=''>-tất cả tình trạng-</option>";
					foreach ($listHvuDmTinhTrang as $resDM) {
						echo "<option value='".$resDM["ma_tinh_trang"]."'>" .$resDM["ten_tat"]. "</option>";
					}
				?>
				<option value='!2'>Loại trừ Đã XL</option>
				</select>
			</td>
		</tr>
	</table>
</div>
<div align="center">
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

<div id="dialogMessage" title="Lưu Ý">
</div>
<div id="listDataTienTrinhHoSo">
	<?php echo $listView ?>
</div>
<script type="text/javascript">
$(document).ready(function() {
	//hide ajax loading
	$("#squaresWaveG").hide();
	$("#dialogMessage").dialog({
		autoOpen: false,
		modal: true,
		resizable: false,
	});
	$('#filter_tien_trinh_ho_so_nam_nhan,#filter_tien_trinh_ho_so_tinh_trang').change(function() {
		var postData = {
			'namnhan' : $("#filter_tien_trinh_ho_so_nam_nhan").val(),
			'tinhtrang' : $("#filter_tien_trinh_ho_so_tinh_trang").val()
		}
		$.ajax({type: "POST",
			url: '<?php echo $gvURL ?>/front.php/phongbankhoa/hoso/tientrinh?hisid=<?php echo $_GET['hisid']?>',
			data: postData,
			beforeSend: function(xhr){
				$("#squaresWaveG").show();
			},
			success:function(result){
				$("#listDataTienTrinhHoSo").html(result);
			},
			error: function (xhr,status,error){
				$("#dialogMessage").html('error');
				$("#dialogMessage").dialog("open");
			},
			complete: function(xhr,status){
				$("#squaresWaveG").hide();
			}
		});
	});
});	
</script>