﻿<?php 
	include "../gv/libs/connect.php"; 
	include "libs/pgslibshv.php";
	
	$a = str_replace("'", "''",$_REQUEST["a"]);
	$m = str_replace("'", "''",$_REQUEST["m"]);
	
	if ($a == 'getLVTHs')
	{
		$sqlstr="select distinct rownum, dot_nhan_lv, ten_de_tai, h.ho||' '||h.ten HV, 
					 (select ho||' '||ten from can_bo_giang_day where huong_dan_chinh = ma_can_bo) HD_CHINH,
					 (select ho||' '||ten from can_bo_giang_day where huong_dan_phu = ma_can_bo) HD_PHU
			  	from luan_van_thac_sy l, hoc_vien h, can_bo_giang_day c
			   where (l.ma_hoc_vien = h.ma_hoc_vien) and (huong_dan_chinh = ma_can_bo or huong_dan_phu = ma_can_bo)
				";
		if ($m!='')
			$sqlstr.=" AND (ma_nganh = '$m')";
		//$sqlstr.=" ORDER BY nam DESC";
		
		$stmt = oci_parse($db_conn, $sqlstr);oci_execute($stmt);$n = oci_fetch_all($stmt, $resDM);oci_free_statement($stmt);
		$data='{
				"aaData":[';
		
		//echo $dataB;
		for ($i = 0; $i < $n; $i++)
		{			
			$data .= '["'.escapeWEB($resDM["TEN_DE_TAI"][$i]).'",
					  "'.escapeWEB($resDM["HD_CHINH"][$i]).'", 
					  "'.escapeWEB($resDM["HD_PHU"][$i]).'", 
					  "'.escapeWEB($resDM["HV"][$i]).'"],';
			//if ($i == ($n-1))
			//	$data=substr($data,0,-1);
			
			//echo $data;
		}
		
		if ($n>0)
			$data=substr($data,0,-1);
		
		$data.='	]
				}';
		
		echo $data;
		
		exit;
	}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Phòng Đào Tạo Sau Đại Học</title>

</head>

<script src="../js/jquery-1.8.0.min.js"></script>
<script src="../js/jquery-ui-1.8.23.custom.min.js"></script> 
<script src="../js/jquery.maskedinput-1.3.min.js"></script> 
<script src="../datatable/media/js/jquery.dataTables.min.js"></script>

<link href="../css/start/jquery-ui-1.8.18.custom.css" rel="stylesheet" type="text/css"/>
<link href="css/pgs.css" rel="stylesheet" type="text/css"/>
<link href="../datatable/media/css/jquery.dataTables_themeroller.css" rel="stylesheet" type="text/css"/>

<body style="font-family:Arial, Helvetica, sans-serif">
	<div id=content>
		<div>
			<label for="txtNganh_TraCuuLVTHs" class=heading>Chọn ngành </label>
			<select id='txtNganh_TraCuuLVTHs' style=''>
				<option value="">Tất cả ngành</option>
				<?php
				$sqlstr="SELECT ma_nganh, ten_nganh||' - ('||ma_nganh||')' ten_nganh, viet0dau(ten_nganh) ten_nganh_khong_dau
					FROM nganh WHERE ma_nganh IN (SELECT DISTINCT ma_nganh FROM hoc_vien h, luan_van_thac_sy l WHERE h.ma_hoc_vien = l.ma_hoc_vien)
					ORDER BY ten_nganh_khong_dau";
				$stmt = oci_parse($db_conn, $sqlstr);oci_execute($stmt);$n = oci_fetch_all($stmt, $resDM);oci_free_statement($stmt);
				for ($i = 0; $i < $n; $i++)
				{
					if ($i==0)
						$selected = "selected";
					else
						$selected = "";
						
					echo "<option $selected value='".$resDM["MA_NGANH"][$i]."'>" .$resDM["TEN_NGANH"][$i]. "</option>";
				}
				?>
			</select>
		</div>
		<div style="margin: 10px 0 0 0; font-size:11px">
			<table id='ds_lv_ths' width="100%" height="20" border="0" align="center" cellpadding="5" cellspacing="0" class="ui-widget ui-widget-content ui-corner-top tableData">
				<thead>
				<tr class="ui-widget-header heading" height="20">			
					<th style="" align=left>Tên đề tài</th>
					<th style="width: 130px" align=left >Hướng dẫn chính</th>
					<th style="width: 130px" align=left >Hướng dẫn phụ</th>
					<th style="width: 130px" align=left>HV thực hiện</th>
				</tr>
				</thead>
				<tfoot>
					<tr class="ui-widget-header heading">			
						<th style="" align=left>Tên đề tài</th>
						<th align=left >Hướng dẫn chính</th>
						<th align=left >Hướng dẫn phụ</th>
						<th align=left>HV thực hiện</th>
					</tr>
				</tfoot>
				<tbody>
					
				</tbody>
			</table>
		</div>
	</div>
</body>
</html>
<script type="text/javascript">
 var oTableData;
 var linkdata = "tra_cuu_lv_ths.php?a=getLVTHs";
 
 function initialTable(urldata)
 {
	 oTableData = $('#ds_lv_ths').dataTable( {
		"bJQueryUI": true,
		"bAutoWidth": false, 
		"iDisplayLength": 7,
		"bLengthChange": false,
		"sPaginationType": "full_numbers",
		"oLanguage": {
			"sUrl": "../datatable/media/language/vi_VI.txt"
		},
		"bProcessing": true,
		"sAjaxSource": urldata,
		"aoColumns" : [{},{},{},{},{}],
		"aaSorting": [[4, 'desc']]
	} );
 }
 
function RefreshTable(tableId, urlData)
{
	table = $(tableId).dataTable();
	oSettings = table.fnSettings();
	//table._fnProcessingDisplay( oSettings, true );
	$('#ds_lv_ths_processing').attr('style', 'visibility:visible');
	$.getJSON(urlData, null, function( json )
	{	
		table.fnClearTable(this);
		for (var i=0; i<json.aaData.length; i++)
		{
			table.oApi._fnAddData(oSettings, json.aaData[i]);
		}
	
		oSettings.aiDisplay = oSettings.aiDisplayMaster.slice();
		table.fnDraw();
		$('#ds_lv_ths_processing').attr('style', 'visibility:hidden');
	});
}

$("#txtNganh_TraCuuLVTHs").change(function(e) {
	RefreshTable(oTableData,linkdata+"&m="+$("#txtNganh_TraCuuLVTHs").val());
 });

initialTable(linkdata+"&m="+$("#txtNganh_TraCuuLVTHs").val());

$(function() {

});
</script>