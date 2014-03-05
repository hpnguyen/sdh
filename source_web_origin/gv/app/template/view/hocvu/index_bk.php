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
			<td style="width:85px;">
				<select id=filter_tien_trinh_ho_so_nam_nhan title="Fillter theo năm nhận hồ sơ" style='width:100%; height:25px; padding: 0 0 0 0;' class="ui-widget-content ui-corner-all tableData">
				<?php 
					$nam =date("Y");
					echo "<option value=''>-năm nhận-</option>";
					for ($i = $nam; $i > ($nam-5); $i--)
					{
						if ($i == $nam)
							$selected = "selected";
						else
							$selected = "";
							
						echo "<option value='".$i."' $selected>" .$i. "</option>";
					}
				  ?>
				</select>
			</td>
			<td>
				<select id=filter_phong_tnychv_tinh_trang title="Fillter theo tình trạng HS" style='width:100%; height:25px; padding: 0 0 0 0;' class="ui-widget-content ui-corner-all tableData" >
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
<?php
$listArrayData = array();
foreach($listItems as $y => $row)
{
	$listArrayDataString = "";
	$listArrayDataString .= "'<b class=\"special-color\">" .$row['ma_gqhvu']."</b>',";
	$listArrayDataString .= "'".$row["noi_dung_yc"]."',";
	$listArrayDataString .= "'".$row["ngay_tiep_nhan"]."',";
	$listArrayDataString .= "'".$row["ngay_hen_tra_kq"]."',";
	$listArrayDataString .= "'".$row["ten_nguoi_giai_quyet"]."',";
	$listArrayDataString .= "'".$row["ten_tinh_trang"]."',";
	$listArrayDataString .= "'".$row["ket_qua"]."',";
	$listArrayDataString .= "'".$row["so_luong"]."',";
	$listArrayDataString .= "'".$row["ngay_tra_kq"]."'";
	$listArrayData[] = $listArrayDataString;
	
}
?>
<div id="dataGridTinhTrangHocVu" style="padding-bottom: 50px">
	<table width="100%" cellpadding="0" cellspacing="0" border="0" class="ui-widget ui-widget-content ui-corner-all display" id="dataGridTableTinhTrangHocVu" style="padding: 0px">
		<thead>
	  <tr class='ui-widget-header heading' style='font-weight:bold; height:20pt;'>
		<td width="23px" align='center' class='ui-corner-tl'>Mã</td>
		<td align="center">Nội dung yêu cầu</td>
		<td width="100px" align="center">Ngày tiếp Nhận</td>
		<td width="100px" align="center">Ngày hẹn trả KQ</td>
		<td width="23px" align="center">Tên người giải quyết</td>
		<td width="100px" align="center">Tình trạng</td>
		<td width="163px" align="center">Kết quả</td>
		<td width="18px" align="center">SL</td>
		<td width="100px" align="center" >Ngày trả KQ</td>
	  </tr>
	  </thead>
	  <tbody>
	  </tbody>
	</table>
</div>
<script>
$(document).ready(function() {
	$('#dataGridTableTinhTrangHocVu').dataTable( {
		<?php if (count($listArrayData) > 0) { ?>
		"aaData": [<?php echo "[".implode('],[', $listArrayData)."]"  ?>],
		<?php } ?>
		"aoColumns": [
			{"sClass": "center"},
			{"sClass": "left"},
			{"sClass": "center"},
			{"sClass": "center"},
			{"sClass": "center"},
			{"sClass": "center"},
			{"sClass": "left"},
			{"sClass": "center"},
			{"sClass": "center"}
		],
		"aaSorting": [[0,'desc']],
		"bAutoWidth": false, 
		"sPaginationType": "full_numbers",
		"oLanguage": {"sUrl": "<?php echo $help->baseURL() ?>/datatable/media/language/vi_VI.txt"},
		"fnInitComplete": function(oSettings, json) {
		},
		"fnDrawCallback": function() {
		}
	});
});
</script>