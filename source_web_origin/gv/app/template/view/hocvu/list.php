<?php
$help = Helper::getHelper('functions/util');
$gvURL = $help->getGvRootURL();

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
		},
		"fnRowCallback": function( nRow, aaData, iDisplayIndex ) {
			if (aaData[5]=="Đã xử lý") {
				$('td:eq(5)', nRow).addClass('YCHV_DaXL');
			}else if (aaData[5]=="Chưa xử lý") {
				$('td:eq(5)', nRow).addClass('YCHV_ChuaXL');
			}else if (aaData[5]=="Đang xử lý") {
				$('td:eq(5)', nRow).addClass('YCHV_DangXL');
			}else if (aaData[5]=="Trình lãnh đạo" || aaData[5]=="Lấy dấu") {
				$('td:eq(5)', nRow).addClass('YCHV_TrinhLD');
			}
			
			return nRow;
		}
	});
});
</script>