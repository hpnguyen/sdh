<?php
//if (!isset($_SESSION['uidloginhv'])){
//	die('Truy cập bất hợp pháp'); 
//}

include "libs/connect.php";
?>

<div align="left" style="margin:0 auto;">
<form id="form_tracuuCTDT" name="form_tracuuCTDT" method="post" action="">
   <table width="100%" border="0" cellspacing="0" cellpadding="5" align=center>
   
	<tr>
	
	  <td align=right style='width:50px'>
		<span class="heading">
		<label for="ctdt_txtKhoa">Khóa</label>
		</span>
	  </td>
	  <td align=left style="width:50px;">
		<select id=ctdt_txtKhoa name=ctdt_txtKhoa style="font-size:15px;" onChange="ctdt_updateNganh(this.value);">
			<?php
			$sqlstr="SELECT DISTINCT khoa 
					FROM ctdt_khoa_nganh 
					WHERE khoa > 2004 
					ORDER BY khoa DESC";
			$stmt = oci_parse($db_conn, $sqlstr);
			oci_execute($stmt);
			$n = oci_fetch_all($stmt, $resDM);
			oci_free_statement($stmt);

			for ($i = 0; $i < $n; $i++)
			{
				echo "<option value='".$resDM["KHOA"][$i]."'>" .$resDM["KHOA"][$i]. "</option>";
			}
			?>
			
		</select>
	  </td>
	</tr>
	
	
	<tr>
	  <td align=right>
		<span class="heading">
			<label for="ctdt_txtNganh">Ngành</label>
		</span>
	  </td>
	  <td align=left>
	  
		<select id=ctdt_txtNganh name=ctdt_txtNganh style="width:400px;font-size:15px;" onChange="ctdt_updateHK($('#ctdt_txtKhoa').val(),this.value);">
			<option value="">Chọn ngành</option>
		</select>
	  </td>
	</tr>
	
	<tr>
	  <td align=right>
		<span class="heading">
		<label for="ctdt_txtHK">Học kỳ</label>
		</span>
	  </td>
	  <td align=left >
		<select id=ctdt_txtHK name=ctdt_txtHK onChange="ctdt_updateCTDT($('#ctdt_txtKhoa').val(), $('#ctdt_txtNganh').val(), this.value);" style="font-size:15px;">
		</select>
	  </td>
	</tr>
	
	<tr>
	  <td align=right>
		
	  </td>
	  <td align=left colspan="3" style='font-size:80%'>
		<a id="ctdt_btn_printpreview" >&nbsp;Xem bản In</a>
	  </td>
	</tr>
   </table>
   
   <div id="ctdt_chitiet" style="margin-top:5px;" align=center>
		<div style='margin-bottom:10px; font-size:14px;' align=center><b>CHƯƠNG TRÌNH ĐÀO TẠO THẠC SĨ KHÓA <span id=text_ctdt_khoa></span> </b><br/>Chuyên ngành: <b><span id=text_ctdt_chuyen_nganh></span></b></div>
		<table id='ds_ctdt_ths' width="100%" height="20" border="0" align="center" cellpadding="5" cellspacing="0" class="ui-widget ui-widget-content ui-corner-top display">
			<thead>
			<tr class="ui-widget-header heading" height="20">
				<th style="" align=left >Mã MH</th>
				<th style="" align=left >Môn học</th>
				<th style="" align=center>Số T.Chỉ</th>
				<th style="" align=center>Khối KTBS</th>
				<th style="" align=right>ST LT</th>
				<th style="" align=right>ST TH</th>
				<th style="" align=right>ST BT</th>
				<th style="" align=right>ST TL</th>
				<th style="" align=center>Tự chọn</th>
				<th style="" align=center>Đề cương</th>
			</tr>
			</thead>
			<tfoot>
			<tr class="ui-widget-header heading">			
				<th style="" align=left >Mã MH</th>
				<th style="" align=left >Môn học</th>
				<th style="" align=center>Số T.Chỉ</th>
				<th style="" align=center>Khối KTBS</th>
				<th style="" align=right>ST LT</th>
				<th style="" align=right>ST TH</th>
				<th style="" align=right>ST BT</th>
				<th style="" align=right>ST TL</th>
				<th style="" align=center>Tự chọn</th>
				<th style="" align=center>Đề cương</th>
			</tr>
			</tfoot>
			<tbody>
			</tbody>
		</table>
   </div>
</form>
</div>

<script type="text/javascript">
function ctdt_writeConsole(content) {
	top.consoleRef=window.open('','myconsole',
	'width=800,height=450'
	+',menubar=0'
	+',toolbar=0'
	+',status=0'
	+',scrollbars=1'
	+',resizable=1')
	top.consoleRef.document.writeln(
	'<html><head><title>Phong Dao Tao SDH - DHBK</title></head>'
	+'<link href="css/pgs.css" rel="stylesheet" type="text/css"/>'
	+'<body bgcolor=white onLoad="self.focus()">'
	+content
	+'</body></html>'
	)
	top.consoleRef.document.close()
}
		
function ctdt_updateNganh(p_khoa)
{
	xreq = $.ajax({
	  type: 'POST', dataType: "html",
	  url: 'hv_tracuu_ctdt_process.php?w=khoa-nganh'
	  + '&k=' + p_khoa
	  + '&hisid=<?php echo $_REQUEST["hisid"]; ?>',
	  success: function(data) {
		$("#ctdt_txtNganh").html(data);
		ctdt_updateHK(p_khoa, $("#ctdt_txtNganh").val());
	  },
	  error: function(xhr, ajaxOptions, thrownError) {
		
	  }
	});
}

function ctdt_updateHK(p_khoa, p_nganh)
{
	xreq = $.ajax({
	  type: 'POST', dataType: "html",
	  url: 'hv_tracuu_ctdt_process.php?w=khoa_nganh-hk'
	  + '&k=' + p_khoa 
	  + '&n=' + p_nganh
	  + '&hisid=<?php echo $_REQUEST["hisid"]; ?>',
	  success: function(data) {
		$("#ctdt_txtHK").html(data);
		ctdt_updateCTDT(p_khoa, p_nganh, $("#ctdt_txtHK").val());
	  },
	  error: function(xhr, ajaxOptions, thrownError) {
		
	  }
	});
}

function ctdt_updateCTDT(p_khoa, p_nganh, p_hk)
{
	// e=link : nhúng vào trang web khác
	//$("#ctdt_chitiet").html("<img border='0' src='images/ajax-loader.gif'/>");
	/*
	$( "#ctdt_btn_printpreview" ).button( "disable" );
	if ($("#ctdt_chitiet").html()!='')
		$( "#ctdt_btn_printpreview" ).button( "enable" );
	else
		$( "#ctdt_btn_printpreview" ).button( "disable" );
	*/
	RefreshTableCTDT(oTableDataCTDT, linkdatactdt+ '&k='+p_khoa+'&h='+p_hk+'&n='+p_nganh+'&e=<?php echo $_REQUEST["e"]; ?>'+'&hisid=<?php echo $_REQUEST["hisid"]; ?>');	
}

var oTableDataCTDT;
var vInitTableCTDT = false;
var linkdatactdt = "hv_tracuu_ctdt_process.php?w=khoa_hk_nganh-ctdt";

function initialTableCTDT(urldata)
 {
	 oTableDataCTDT = $('#ds_ctdt_ths').dataTable( {
		"bJQueryUI": true,
		"bStateSave": true,
		"bAutoWidth": false, 
		"iDisplayLength": 10,
		"sPaginationType": "full_numbers",
		"oLanguage": {
			"sUrl": "../datatable/media/language/vi_VI.txt"
		},
		"bProcessing": true,
		"sAjaxSource": urldata,
		"aoColumns" : [{},{},{"sClass" : "center"},{"sClass" : "center"},{"sClass" : "right"},{"sClass" : "right"},{"sClass" : "right"},{"sClass" : "right"},{"sClass" : "center"},{"sClass" : "center"}]
	} );
 }
 
function RefreshTableCTDT(tableId, urlData)
{
	if (!vInitTableCTDT)
	{
		initialTableCTDT(urlData);
		vInitTableCTDT = true;
	}
	else
	{
		table = $(tableId).dataTable();
		oSettings = table.fnSettings();
		//table._fnProcessingDisplay( oSettings, true );
		$('#ds_ctdt_ths_processing').attr('style', 'visibility:visible');
		$.getJSON(urlData, null, function( json )
		{	
			table.fnClearTable(this);
			for (var i=0; i<json.aaData.length; i++)
			{
				table.oApi._fnAddData(oSettings, json.aaData[i]);
			}
		
			oSettings.aiDisplay = oSettings.aiDisplayMaster.slice();
			table.fnDraw();
			$('#ds_ctdt_ths_processing').attr('style', 'visibility:hidden');
		});
	}
	
	$('#text_ctdt_khoa').html($("#ctdt_txtKhoa option:selected").html() + " HK " + $("#ctdt_txtHK option:selected").html());
	$('#text_ctdt_chuyen_nganh').html($("#ctdt_txtNganh option:selected").html());
}

$(function() {
	//initialTableCTDT(linkdatactdt+ '&k=' + $('#ctdt_txtKhoa').val() + '&h=' + $('#ctdt_txtHK').val() + '&n=' + $('#ctdt_txtNganh').val() + '&e=<?php echo $_REQUEST["e"]; ?>' + '&hisid=<?php echo $_REQUEST["hisid"]; ?>');
	
	$( "#ctdt_btn_printpreview" ).button({ icons: {primary:'ui-icon ui-icon-print'} });
	ctdt_updateNganh($('#ctdt_txtKhoa').val());
	
	$("#ctdt_btn_printpreview").click(function(){
		ctdt_writeConsole("<div style='font-size:13px; font-weight:bold;' align=left>ĐẠI HỌC BÁCH KHOA - TP.HCM<br/>PHÒNG ĐÀO TẠO SAU ĐẠI HỌC<br/> &nbsp;</div>" + $("#ctdt_chitiet").html() + "<div style='clear:both;'>&nbsp;<br/><a href='JavaScript:window.print();' style='font-size:150%; color:blue;'>In trang này</a></div>");
	});	// end $("#ctdt_btn_printpreview")
});
</script>

<?php 
if (isset ($db_conn))
	oci_close($db_conn);
?>