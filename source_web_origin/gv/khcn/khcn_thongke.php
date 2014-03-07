<?php
if (isset($_REQUEST["hisid"])){
	session_id($_REQUEST["hisid"]);
	session_start();
}
if (!isset($_SESSION['uidloginPortal'])){
	die('Đã hết phiên làm việc'); 
}

include "../libs/connect.php";
include "../libs/pgslibs.php";

if (!allowPermisstion(base64_decode($_SESSION['uidloginPortal']), '052', $db_conn)){
	die('Truy cập bất hợp pháp');
}

error_reporting(1);

$macb = $_SESSION['macb'];
?>

<div id="khcn_thongke_accordion">
	<h3>Thống kê số lượng bài báo</h3>
	<div class="tableData">
		<div align="left" style="margin-left: 10px">
			Thống kê Từ năm <input type=text id="khcn_thongke_capnhat_baibao_tu" name="khcn_thongke_capnhat_baibao_tu" style="width:90px; text-align:center" > đến <input type=text id="khcn_thongke_capnhat_baibao_den" name="khcn_thongke_capnhat_baibao_den" style="width:90px; text-align:center">
		</div>
		
		<div style="margin-top:10px; padding:15px;">
			<table id="khcn_thongke_capnhat_baibao_khoa" name="khcn_thongke_capnhat_baibao_khoa" width="100%"  border="0" align="center" cellpadding="5" cellspacing="0" class="ui-widget ui-widget-content ui-corner-top tableData display">
				<thead>
					<tr class="ui-widget-header heading" >
						<th align=left>Khoa</th>
						<th align=left style="width:70px">SL bài báo</th>
						<th align=left style="width:80px">Điểm IF</th>
						<th align=right style="width:15px"></th>
					</tr>
				</thead>				
			</table>
		</div>
	</div>
	
	<h3>Thống kê tình hình cập nhật thông tin Lý Lịch Khoa Học</h3>
	<div class="tableData">
		<div align="left" style="margin-left: 10px">
			Thống kê Từ ngày <input type=text id="khcn_thongke_capnhat_llkh_tu" name="khcn_thongke_capnhat_llkh_tu" style="width:90px; text-align:center" > đến <input type=text id="khcn_thongke_capnhat_llkh_den" name="khcn_thongke_capnhat_llkh_den" style="width:90px; text-align:center">
		</div>
		<div style="width:100%" align=center>
			<div style="margin-top:10px; padding:5px; float: left;">
				<fieldset>
				<legend>Thống kê theo Khoa</legend>
				<table id="khcn_thongke_capnhat_llkh_khoa" name="khcn_thongke_capnhat_llkh_khoa" width="100%"  border="0" align="center" cellpadding="5" cellspacing="0" class="ui-widget ui-widget-content ui-corner-top tableData display">
					<thead>
						<tr class="ui-widget-header heading" >
							<th align=left>Khoa</th>
							<th align=left style="width:60px">SL CBGD cập nhật</th>
							<th align=right style="width:15px"></th>
						</tr>
					</thead>				
				</table>
				</fieldset>
				<div class="clearfloat"></div>
			</div>
			
			<div style="margin-top:10px; padding:15px;">
				<fieldset>
				<legend>Thống kê theo chức danh</legend>
				<table id="khcn_thongke_capnhat_llkh_chucdanh" name="khcn_thongke_capnhat_llkh_chucdanh" width="100%"  border="0" align="center" cellpadding="5" cellspacing="0" class="ui-widget ui-widget-content ui-corner-top tableData display">
					<thead>
						<tr class="ui-widget-header heading" >
							<th align=left>Chức danh</th>
							<th align=left style="width:60px">SL CBGD cập nhật</th>
							<th align=right style="width:15px"></th>
						</tr>
					</thead>				
				</table>
				</fieldset>
				<div class="clearfloat"></div>
			</div>
		</div>
	</div>
</div>

<script type="text/javascript">
var oTable_khcn_thongke_llkh_theo_khoa,oTable_khcn_thongke_llkh_chucdanh, oTable_khcn_thongke_capnhat_baibao_khoa, nTrClicked;
var khcn_thongke_linkdata = "khcn/khcn_thongke_process.php?hisid=<?php echo $_REQUEST["hisid"]; ?>";
 
$(function(){
	
	$( "#khcn_thongke_accordion" ).accordion({
		heightStyle: "content"
	});
	
	$( "#khcn_thongke_capnhat_llkh_tu" ).datepicker({
		defaultDate: "-6m",
		changeMonth: true,
		changeYear: true,
		dateFormat: "dd/mm/yy"
	});
	
	$( "#khcn_thongke_capnhat_llkh_den" ).datepicker({
		changeMonth: true,
		changeYear: true,
		dateFormat: "dd/mm/yy"
	});
	
	$( "#khcn_thongke_capnhat_llkh_tu, #khcn_thongke_capnhat_llkh_den" ).mask("99/99/9999");
	$( "#khcn_thongke_capnhat_baibao_tu, #khcn_thongke_capnhat_baibao_den" ).mask("9999");
	
	var d = new Date();
	var m = d.getMonth()+1, y = d.getFullYear(), firstDay = new Date(y, m, 1), lastDay = new Date(y, m + 1, 0);
	
	//$( "#khcn_thongke_capnhat_llkh_tu" ).val('01/' + m + '/' + y);
	//$( "#khcn_thongke_capnhat_llkh_den" ).val(lastDay.getDate() + '/' + m + '/' + y);
	$( "#khcn_thongke_capnhat_baibao_tu, #khcn_thongke_capnhat_baibao_den" ).val(y);
	
	$('#khcn_thongke_capnhat_llkh_tu, #khcn_thongke_capnhat_llkh_den').change(function() {
		khcn_RefreshTableThongKeLLKH(oTable_khcn_thongke_llkh_theo_khoa, "#khcn_thongke_capnhat_llkh_khoa", khcn_thongke_linkdata+"&a=thongke_capnhat_llkh_khoa&tu="+$( "#khcn_thongke_capnhat_llkh_tu" ).val() + "&den=" +$( "#khcn_thongke_capnhat_llkh_den" ).val());
	});
	
	$('#khcn_thongke_capnhat_baibao_tu, #khcn_thongke_capnhat_baibao_den').change(function() {
		khcn_RefreshTableThongKe(oTable_khcn_thongke_capnhat_baibao_khoa, "#khcn_thongke_capnhat_baibao_khoa", khcn_thongke_linkdata+"&a=thongke_capnhat_baibao_khoa&tu="+$( "#khcn_thongke_capnhat_baibao_tu" ).val() + "&den=" +$( "#khcn_thongke_capnhat_baibao_den" ).val());
	});
	
	$('#khcn_thongke_capnhat_llkh_khoa tbody td img').live( 'click', function () {
        var nTr = $(this).parents('tr')[0];
		nTrClicked = nTr;
		
		// Click vào icon detailsicon
		if (this.className == 'detailsicon'){
			if ( oTable_khcn_thongke_llkh_theo_khoa.fnIsOpen(nTr) ){
				/* This row is already open - close it */
				this.src = "icons/details_open.png";
				oTable_khcn_thongke_llkh_theo_khoa.fnClose( nTr );
			}else{
				/* Open this row */
				this.src = "icons/details_close.png";
				oTable_khcn_thongke_llkh_theo_khoa.fnOpen( nTr, khcn_thongke_ct_capnhat_llkh_fnFormatDetails(oTable_khcn_thongke_llkh_theo_khoa, nTr), 'details' );
				
				//$(".qttooltips").tooltip();
			}
		}
    });
	
	$('#khcn_thongke_capnhat_llkh_chucdanh tbody td img').live( 'click', function () {
        var nTr = $(this).parents('tr')[0];
		nTrClicked = nTr;
		
		// Click vào icon detailsicon
		if (this.className == 'detailsicon'){
			if ( oTable_khcn_thongke_llkh_chucdanh.fnIsOpen(nTr) ){
				/* This row is already open - close it */
				this.src = "icons/details_open.png";
				oTable_khcn_thongke_llkh_chucdanh.fnClose( nTr );
			}else{
				/* Open this row */
				this.src = "icons/details_close.png";
				oTable_khcn_thongke_llkh_chucdanh.fnOpen( nTr, khcn_thongke_ct_capnhat_llkh_fnFormatDetails(oTable_khcn_thongke_llkh_chucdanh, nTr), 'details' );
				
				//$(".qttooltips").tooltip();
			}
		}
    });
	
	$('#khcn_thongke_capnhat_baibao_khoa tbody td img').live( 'click', function () {
        var nTr = $(this).parents('tr')[0];
		nTrClicked = nTr;
		
		// Click vào icon detailsicon
		if (this.className == 'detailsicon'){
			if ( oTable_khcn_thongke_capnhat_baibao_khoa.fnIsOpen(nTr) ){
				/* This row is already open - close it */
				this.src = "icons/details_open.png";
				oTable_khcn_thongke_capnhat_baibao_khoa.fnClose( nTr );
			}else{
				/* Open this row */
				this.src = "icons/details_close.png";
				oTable_khcn_thongke_capnhat_baibao_khoa.fnOpen( nTr, khcn_thongke_ct_baibao_fnFormatDetails(oTable_khcn_thongke_capnhat_baibao_khoa, nTr), 'details' );
			}
		}
    });
	
	khcn_init_thongke_capnhat_table(khcn_thongke_linkdata+"&a=thongke_capnhat_llkh_khoa&tu="+$( "#khcn_thongke_capnhat_llkh_tu" ).val() + "&den=" +$( "#khcn_thongke_capnhat_llkh_den" ).val());
	khcn_init_thongke_capnhat_table_chucdanh(khcn_thongke_linkdata+"&a=thongke_capnhat_llkh_chucdanh&tu="+$( "#khcn_thongke_capnhat_llkh_tu" ).val() + "&den=" +$( "#khcn_thongke_capnhat_llkh_den" ).val());
	khcn_init_thongke_baibao_table_khoa(khcn_thongke_linkdata+"&a=thongke_capnhat_baibao_khoa&tu="+$( "#khcn_thongke_capnhat_baibao_tu" ).val() + "&den=" +$( "#khcn_thongke_capnhat_baibao_den" ).val());
});

function khcn_thongke_checksession(){
	dataString = 'a=checksession';
	return xreq = $.ajax({
	  type: 'POST', dataType: "json", data: dataString,
	  url: khcn_thongke_linkdata,
	  success: function(data) {
		return jQuery.parseJSON(data);
	  }
	});
}

function khcn_RefreshTableThongKe(tableId, pTableName, urlData){
	table = $(tableId).dataTable();
	oSettings = table.fnSettings();
	$(pTableName + '_processing').attr('style', 'visibility:visible');
	$.getJSON(urlData, null, function( json )
	{
		table.fnClearTable(this);
		for (var i=0; i<json.aaData.length; i++)
		{
			table.oApi._fnAddData(oSettings, json.aaData[i]);
		}
		oSettings.aiDisplay = oSettings.aiDisplayMaster.slice();
		table.fnDraw();
		$(pTableName + '_processing').attr('style', 'visibility:hidden');
	});
}

function khcn_RefreshTableThongKeLLKH(tableId, pTableName, urlData){
	table = $(tableId).dataTable();
	oSettings = table.fnSettings();
	$(pTableName + '_processing').attr('style', 'visibility:visible');
	$.getJSON(urlData, null, function( json )
	{
		table.fnClearTable(this);
		for (var i=0; i<json.aaData.length; i++)
		{
			table.oApi._fnAddData(oSettings, json.aaData[i]);
		}
		oSettings.aiDisplay = oSettings.aiDisplayMaster.slice();
		table.fnDraw();
		$(pTableName + '_processing').attr('style', 'visibility:hidden');
		
		khcn_RefreshTableThongKe(oTable_khcn_thongke_llkh_chucdanh, "#khcn_thongke_capnhat_llkh_chucdanh", khcn_thongke_linkdata+"&a=thongke_capnhat_llkh_chucdanh&tu="+$( "#khcn_thongke_capnhat_llkh_tu" ).val() + "&den=" +$( "#khcn_thongke_capnhat_llkh_den" ).val());
	});
}

function khcn_init_thongke_capnhat_table(pUrldata){
	gv_processing_diglog("open", "Đang xử lý ... vui lòng chờ");
	khcn_thongke_checksession().done(function(data){
		gv_processing_diglog("close", "...");
		if (data.success != 1){
			gv_open_msg_box("<font style='color:red;'>Không thể khởi tạo danh sách thống kê vì:</font> <br/><div style='margin: 5px 0 0 5px'>" + reverse_escapeJsonString(data.msg) +'</div>', 'alert', 250, 180, true);
			return;
		}else{
			oTable_khcn_thongke_llkh_theo_khoa = $("#khcn_thongke_capnhat_llkh_khoa").dataTable( {
				"bJQueryUI": false,
				"bStateSave": true,
				"bAutoWidth": false, 
				"iDisplayLength": 10,
				"sPaginationType": "full_numbers",
				"oLanguage": {
					"sUrl": "../datatable/media/language/vi_VI.txt"
				},
				"bProcessing": true,
				"sAjaxSource": pUrldata,
				"fnDrawCallback": function( oSettings ) {
					$(document).tooltip({ track: true });
				},
				"aoColumns": [
					{ "sClass" : "left", "bSortable": true },
					{ "sClass" : "right", "bSortable": true },
					{ "sClass" : "right", "bSortable": false }
				],
				"aaSorting": [[1, 'asc']]
			} );
		}
	});
}

function khcn_init_thongke_capnhat_table_chucdanh(pUrldata){
	gv_processing_diglog("open", "Đang xử lý ... vui lòng chờ");
	khcn_thongke_checksession().done(function(data){
		gv_processing_diglog("close", "...");
		if (data.success != 1){
			gv_open_msg_box("<font style='color:red;'>Không thể khởi tạo danh sách thống kê vì:</font> <br/><div style='margin: 5px 0 0 5px'>" + reverse_escapeJsonString(data.msg) +'</div>', 'alert', 250, 180, true);
			return;
		}else{
			oTable_khcn_thongke_llkh_chucdanh = $("#khcn_thongke_capnhat_llkh_chucdanh").dataTable( {
				"bJQueryUI": false,
				"bStateSave": true,
				"bAutoWidth": false, 
				"iDisplayLength": 10,
				"sPaginationType": "full_numbers",
				"oLanguage": {
					"sUrl": "../datatable/media/language/vi_VI.txt"
				},
				"bProcessing": true,
				"sAjaxSource": pUrldata,
				"fnDrawCallback": function( oSettings ) {
					$(document).tooltip({ track: true });
				}, 
				"fnRowCallback": function( nRow, aaData, iDisplayIndex ) {
					
				},
				"fnFooterCallback": function ( nRow, aaData, iStart, iEnd, aiDisplay ) {
				},
				"aoColumns": [
					{ "sClass" : "left", "bSortable": true },
					{ "sClass" : "right", "bSortable": true },
					{ "sClass" : "right", "bSortable": false }
				],
				"aaSorting": [[1, 'asc']]
			} );
		}
	});
}

function khcn_init_thongke_baibao_table_khoa(pUrldata){
	gv_processing_diglog("open", "Đang xử lý ... vui lòng chờ");
	khcn_thongke_checksession().done(function(data){
		gv_processing_diglog("close", "...");
		if (data.success != 1){
			gv_open_msg_box("<font style='color:red;'>Không thể khởi tạo danh sách thống kê vì:</font> <br/><div style='margin: 5px 0 0 5px'>" + reverse_escapeJsonString(data.msg) +'</div>', 'alert', 250, 180, true);
			return;
		}else{
			oTable_khcn_thongke_capnhat_baibao_khoa = $("#khcn_thongke_capnhat_baibao_khoa").dataTable( {
				"bJQueryUI": false,
				"bStateSave": true,
				"bAutoWidth": false, 
				"iDisplayLength": 10,
				"sPaginationType": "full_numbers",
				"oLanguage": {
					"sUrl": "../datatable/media/language/vi_VI.txt"
				},
				"bProcessing": true,
				"sAjaxSource": pUrldata,
				"fnDrawCallback": function( oSettings ) {
					$(document).tooltip({ track: true });
				}, 
				"fnRowCallback": function( nRow, aaData, iDisplayIndex ) {
					
				},
				"fnFooterCallback": function ( nRow, aaData, iStart, iEnd, aiDisplay ) {
				},
				"aoColumns": [
					{ "sClass" : "left", "bSortable": true },
					{ "sClass" : "right", "bSortable": true },
					{ "sClass" : "right", "bSortable": true },
					{ "sClass" : "right", "bSortable": false }
				],
				"aaSorting": [[1, 'asc']]
			} );
		}
	});
}

/* Formating function for row details */
function khcn_thongke_ct_capnhat_llkh_fnFormatDetails ( oTable, nTr ){
    var aData = oTable.fnGetData( nTr );
    var sOut = aData[3];
     
    return sOut;
}

function khcn_thongke_ct_baibao_fnFormatDetails ( oTable, nTr ){
    var aData = oTable.fnGetData( nTr );
    var sOut = aData[4];
     
    return sOut;
}
</script>



<?php 
if (isset ($db_conn))
	oci_close($db_conn);
?>