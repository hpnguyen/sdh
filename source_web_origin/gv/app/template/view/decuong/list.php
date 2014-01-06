<?php
$help = Helper::getHelper('functions/util');
$gvURL = $help->getGvRootURL();
?>
<div align="center">
	<h2>Danh sách đủ điều kiện nhận đề cương luận văn<br/>Học kỳ <span id="phan-bo-head"><?php echo $hk ?></span></h2>
	
</div>
<div style="margin:0 0 10px 5px;" align=left><strong>Ngày bắt đầu HK: <span id='ngaybatdauhk'></span></strong></div>	
<?php
$listArrayData = array();
foreach($listItems as $y => $row)
{
	$listArrayDataString = "";
	$listArrayDataString .= "'".$row["ten_nganh"]."',";
	$listArrayDataString .= "'".$row["ma_hoc_vien"]."',";
	$listArrayDataString .= "'".$row["ho"]."',";
	$listArrayDataString .= "'".$row["ten"]."',";
	$listArrayDataString .= "'".$row["tong_chi_tich_luy"]."',";
	$listArrayDataString .= "'".$row["loai_ctdt"]."',";
	$listArrayDataString .= "'".$row["huong_nghien_cuu"]."',";
	$listArrayDataString .= "'".$row["huong_dan_1"]."',";
	$listArrayDataString .= "'".$row["huong_dan_2"]."',";
	$listArrayDataString .= "'".$row["dot_xet"]."',";
	$listArrayDataString .= "'".$row["ghi_chu"]."'";
	$listArrayData[] = $listArrayDataString;
	
}
?>
<div id="<?php echo $formKey ?>dataGrid" style="padding-bottom: 50px">
	<table width="100%" cellpadding="0" cellspacing="0" border="0" class="display" id="<?php echo $formKey ?>dataGridTable" style="padding: 0px">
		<thead>
	  <tr class='ui-widget-header heading' style='font-weight:bold; height:20pt;'>
		<td width="168px" align='center'>Tên ngành</td>
		<td width="48px" align="center">Mã HV</td>
		<td width="150px" align="center">Họ</td>
		<td width="50px" align="center">Tên</td>
		<td width="23px" align="center">TC tích lũy</td>
		<td width="100px" align="center">Loại CTĐT</td>
		<td width="163px" align="center">Hướng nghiên cứu</td>
		<td width="163px" align="center">Hướng Dẫn 1</td>
		<td width="163px" align="center">Hướng Dẫn 2</td>
		<td width="23px" align="center">Đợt xét</td>
		<td align="center" class='ui-corner-tr'>Ghi chú</td>
	  </tr>
	  </thead>
	  <tbody>
	  </tbody>
	</table>
</div>
<input type="hidden" id="<?php echo $formKey ?>printPageURL" value="<?php echo $gvURL.'/front.php/index/index/indecuong/?hisid='.$_GET['hisid']; ?>"/>
<script>
$(document).ready(function() {
	$('#ngaybatdauhk').html(' ' + $("#<?php echo $formKey ?>_select_dot_hoc").val() + ' (tuần 1)');
	$('#<?php echo $formKey ?>dataGridTable').dataTable( {
    	<?php if (count($listArrayData) > 0) { ?>
		"aaData": [<?php echo "[".implode('],[', $listArrayData)."]"  ?>],
		<?php } ?>
		"aoColumns": [
			null,
			null,
			null,
			null,
			{"sClass": "center"},
			{"sClass": "center"},
			null,
			null,
			null,
			null,
			null,
		],
		"aaSorting": [[0,'asc'], [2,'asc'], [3,'asc']],
		"bAutoWidth": false, 
		"sPaginationType": "full_numbers",
		"oLanguage": {"sUrl": "<?php echo $help->baseURL() ?>/datatable/media/language/vi_VI.txt"},
	});
});
</script>