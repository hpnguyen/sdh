<?php 
	include "../gv/libs/connect.php"; 
	include "libs/pgslibshv.php";
	
	$a = str_replace("'", "''",$_REQUEST["a"]);
	$m = str_replace("'", "''",$_REQUEST["m"]);
	
	if ($a == 'getHuongDeTai')
	{
		$sqlstr="SELECT distinct ten_de_tai, c.ho||' '||c.ten CB, ghi_chu, decode(ma_bac, 'TH', 'Thạc sĩ', 'TS','Tiến Sĩ') bac, nam
				FROM huong_de_tai h, huong_de_tai_nganh hn, can_bo_giang_day c
 				WHERE (h.ma_de_tai = hn.ma_de_tai) and (h.ma_can_bo = c.ma_can_bo)
				";
		if ($m!='')
			$sqlstr.=" AND (hn.ma_nganh = '$m')";
		//$sqlstr.=" ORDER BY nam DESC";
		
		$stmt = oci_parse($db_conn, $sqlstr);oci_execute($stmt);$n = oci_fetch_all($stmt, $resDM);oci_free_statement($stmt);
		$data='{
				"aaData":[';
		
		for ($i = 0; $i < $n; $i++)
		{			
			$data.= '["'.escapeWEB($resDM["TEN_DE_TAI"][$i]).'",
					  "'.escapeWEB($resDM["CB"][$i]).'", 
					  "'.escapeWEB($resDM["GHI_CHU"][$i]).'", 
					  "'.$resDM["BAC"][$i].'", 
					  "'.$resDM["NAM"][$i].'"],';
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
			<label for="txtNganh_TraCuuHuongDT" class=heading>Chọn ngành </label>
			<select id='txtNganh_TraCuuHuongDT' style=''>
				<option value=''>Tất cả ngành</option>
				<?php
				$sqlstr="select ma_nganh, ten_nganh ||' - ('||ma_nganh||')' ten_nganh, viet0dau(ten_nganh) ten_nganh_khong_dau
						from nganh 
						where ma_nganh in (select distinct ma_nganh from huong_de_tai_nganh)
						order by ten_nganh_khong_dau";
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
			<table id='ds_huongdetai' width="100%" height="20" border="0" align="center" cellpadding="5" cellspacing="0" class="ui-widget ui-widget-content ui-corner-top tableData">
				<thead>
				<tr class="ui-widget-header heading" height="20">			
					<th style="" align=left>Tên đề tài</th>
					<th style="width: 150px" align=left >CB Hướng dẫn</th>
					<th style="" align=left>Ghi chú</th>
					<th style="width: 50px">Bậc ĐT</th>
					<th style="width: 30px">Năm</th>
				</tr>
				</thead>
				<tfoot>
					<tr class="ui-widget-header heading">			
						<th style="" align=left>Tên đề tài</th>
						<th style="" align=left>CB Hướng dẫn</th>
						<th style="" align=left>Ghi chú</th>
						<th style="width: 50px">Bậc ĐT</th>
						<th style="width: 30px">Năm</th>
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
 var linkdata = "tra_cuu_huong_de_tai.php?a=getHuongDeTai";
 
 function initialTable(urldata)
 {
	 oTableData = $('#ds_huongdetai').dataTable( {
		"bJQueryUI": true,
		"bAutoWidth": false, 
		"iDisplayLength": 10,
		"sPaginationType": "full_numbers",
		"oLanguage": {
			"sUrl": "../datatable/media/language/vi_VI.txt"
		},
		"bProcessing": true,
		"sAjaxSource": urldata,
		"aoColumns" : [{},{},{},{"sClass" : "center"},{"sClass" : "center"}]
	} );
 }
 
function RefreshTable(tableId, urlData)
{
	table = $(tableId).dataTable();
	oSettings = table.fnSettings();
	//table._fnProcessingDisplay( oSettings, true );
	$('#ds_huongdetai_processing').attr('style', 'visibility:visible');
	$.getJSON(urlData, null, function( json )
	{	
		table.fnClearTable(this);
		for (var i=0; i<json.aaData.length; i++)
		{
			table.oApi._fnAddData(oSettings, json.aaData[i]);
		}
	
		oSettings.aiDisplay = oSettings.aiDisplayMaster.slice();
		table.fnDraw();
		$('#ds_huongdetai_processing').attr('style', 'visibility:hidden');
	});
}

$("#txtNganh_TraCuuHuongDT").change(function(e) {
	RefreshTable(oTableData,linkdata+"&m="+$("#txtNganh_TraCuuHuongDT").val());
});

initialTable(linkdata+"&m="+$("#txtNganh_TraCuuHuongDT").val());

$(function() {

});
</script>