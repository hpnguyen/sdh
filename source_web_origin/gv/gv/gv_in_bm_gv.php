<?php
if (isset($_REQUEST["hisid"])){
	$sid = $_REQUEST["hisid"];
	session_id($_REQUEST["hisid"]);
	session_start();
}
if (!isset($_SESSION['uidloginPortal'])){
	die('Đã hết phiên làm việc'); 
}

include "../libs/connect.php";
include "../libs/pgslibs.php";
if (!allowPermisstion(base64_decode($_SESSION['uidloginPortal']), '024', $db_conn))
{
	die('Truy cập bất hợp pháp'); 
}

//echo allowPermisstion(base64_decode($_SESSION['uidloginPortal']), '003');

$macb = $_SESSION['macb'];

$sqlstr="select count(*) tong from TMP_MH";
$stmt = oci_parse($db_conn, $sqlstr);oci_execute($stmt);$n = oci_fetch_all($stmt, $resDM);oci_free_statement($stmt);				
$size = $resDM["TONG"][0];

?>
  
<div id = 'inbieumaugv' style='font-size:110%'>
<form id="form_qtct" method="post" action="" >
<div style="margin-bottom:10px;">
	<div style="margin:0 0 0px 0px">
		<table>
		<tr>
		<td align=left>
		<select name="txtBieuMau_inbmgv" id="txtBieuMau_inbmgv" class="text ui-widget-content ui-corner-all tableData" style="font-size:15px;height:25px">
			<option value="">Vui lòng chọn biểu mẫu ...</option>
			<option value="gv_print_ttkh">Thông tin khoa học</option>
			<option value="gv_print_llkh_mau_Bo">Lý lịch khoa học - Mẫu của Bộ</option>
			<option value="gv_print_llkh_mau_r03">Lý lịch khoa học - Mẫu của ĐHQG R03</option>
			<option value="gv_print_llkh_mau_truong_bk">Lý lịch khoa học - Mẫu của trường ĐHBK</option>
			<option value="gv_print_llkh_mau_nckh_cndt">Lý lịch khoa học - Mẫu Chủ nhiệm đề tài</option>
			<option value="gv_print_llkh_mau_nckh_tgdt">Lý lịch khoa học - Mẫu Tham gia đề tài</option>
			<option value="gv_print_llkh_mau_cgkhcn_bo">Lý lịch khoa học - Mẫu chuyên gia KH&CN </option>
		</select>
		</td>
		<td align=right style='width:100%'>
		<a name="refreshDSCB_llkh" id="refreshDSCB_llkh" style="font-size:80%">&nbsp;Refresh DS</a>
		</td>
		</tr>
		</table>
	</div>
	<table id='ds_cbgd' width="100%" height="20" border="0" align="center" cellpadding="5" cellspacing="0" class="ui-widget ui-widget-content ui-corner-top tableData  display">
		<thead>
		<tr class="ui-widget-header heading" height="20">			
			<th style="width: 30px">Mã</th>
			<th style="width: 30px">SHCC</th>
			<th style="width: 120px">Họ</th>
			<th style="width: 30px">Tên</th>
			<th style="width: 10px">Phái</th>
			<th style="">Khoa</th>
			<th style="">Bộ môn</th>
			<th style="align=center">Duyệt</th>
			<th style="width: 20px">In</th>
		</tr>
		</thead>
		<tfoot>
			<tr class="ui-widget-header heading">
				<th style="width: 30px">Mã</th>
				<th style="width: 30px">SHCC</th>
				<th style="width: 100px">Họ</th>
				<th style="width: 30px">Tên</th>
				<th style="width: 10px">Phái</th>
				<th style="">Khoa</th>
				<th style="">Bộ môn</th>
				<th style="align=center">Duyệt</th>
				<th style="width: 20px">In</th>
			</tr>
		</tfoot>
		<tbody>
			
		</tbody>
	</table>
	<!--
	<div id="accordion_in_llkh">
		<h3><a href="#section1">In Lý lịch khoa học</a></h3>
		<div>
			
		</div>
		<h3><a href="#section2">In Lý lịch khoa học theo danh sách</a></h3>
		<div>
		
			<table border="0" cellspacing="0" cellpadding="5" align=center>
			  <tr class="heading">
				<td  align=center>
					<div style="margin-bottom:5px;"><label for="txtCBGD">Danh sách CHƯA in<label></div>
					<select name="txtCBGD" id="txtCBGD" class="text ui-widget-content ui-corner-all " style="height:360px;font-size:15px;width: 360px;" size="<?php echo $size; ?>">
					   <?php 
					   /*
					   $sqlstr="	select a.ma_can_bo, b.ho || ' ' || b.ten ho_ten, a.ghi_chu
										from nckh_ds_cb_in_llkh a, CAN_BO_GIANG_DAY b 
										where a.ma_can_bo = b.ma_can_bo
										order by a.ghi_chu, ho_ten";
						$stmt = oci_parse($db_conn, $sqlstr);oci_execute($stmt);$n = oci_fetch_all($stmt, $resDM);oci_free_statement($stmt);				
						for ($i = 0; $i < $n; $i++)
						{
							echo "<option value='" .$resDM["MA_CAN_BO"][$i]."'>" .($i+1).". {$resDM["HO_TEN"][$i]} - {$resDM["GHI_CHU"][$i]}</option> ";
						}
						*/
					   ?>
					</select>
				</td>
				<td align="center" style="width:50px">
					<a id="btnIn" name="btnIn"> &nbsp;In</a>
				</td>
				<td   align=center>
					<div style="margin-bottom:5px;"><label for="txtCBGD">Danh sách ĐÃ in<label></div>
					<select name="txtCBGD_Da_in" id="txtCBGD_Da_in" class="text ui-widget-content ui-corner-all " style="height:360px;font-size:15px;width: 360px;" size="<?php echo $size; ?>">
					</select>
				</td>
			  </tr>
			</table>
		</div>
	</div>
	-->
	
</div>
</form>
</div>		<!-- end of inbieumaugv -->   

<script type="text/javascript">
//jQuery.ajax
//$(document).ready(function(){
$(function(){

  // delete btn
 $( "#btnIn" ).button({ icons: {primary:'ui-icon ui-icon-print'} });
 $( "#refreshDSCB_llkh" ).button({ icons: {primary:'ui-icon ui-icon-refresh'} });
 
 /*
 $( "#accordion_in_llkh" ).accordion({
	autoHeight: false,
	navigation: true
 });
 */
 
 var oTableData;
 var linkdata = "gv/gv_in_bm_gv_process.php?a=refreshdata&hisid=<?php echo $_REQUEST["hisid"]; ?>";
 
 function initialTable(urldata)
 {
	 oTableData = $('#ds_cbgd').dataTable( {
		"bJQueryUI": true,
		"bAutoWidth": false, 
		"iDisplayLength": 10,
		"sPaginationType": "full_numbers",
		"oLanguage": {
			"sUrl": "../datatable/media/language/vi_VI.txt"
		},
		"bProcessing": true,
		"sAjaxSource": urldata,
		"fnDrawCallback": function( oSettings ) {
			$(".tooltips").tooltip();
		},
		"aoColumns" : [{},{},{},{},{},{},{},{"sClass" : "center"},{}]
	} );
 }
 
	function RefreshTable(tableId, urlData)
	{
		table = $(tableId).dataTable();
		oSettings = table.fnSettings();
		//table._fnProcessingDisplay( oSettings, true );
		$('#ds_cbgd_processing').attr('style', 'visibility:visible');
		$.getJSON(urlData, null, function( json )
		{	
			table.fnClearTable(this);
			for (var i=0; i<json.aaData.length; i++)
			{
				table.oApi._fnAddData(oSettings, json.aaData[i]);
			}
		
			oSettings.aiDisplay = oSettings.aiDisplayMaster.slice();
			table.fnDraw();
			$('#ds_cbgd_processing').attr('style', 'visibility:hidden');
		});
	}
  
	$("#refreshDSCB_llkh").click(function(){
		RefreshTable(oTableData,linkdata);
	});
	
 
// Check validate fields Nghien cuu khoa hoc
	
    $("#btnIn").click(function(){
		//$( "#btnIn" ).button({ disabled: true });
		
		if ($("#txtCBGD option:selected").html() != null )
		{
			//ns.addTab_ns("gv/gv_print_ttgv_mau_llkh.php?print=true&hisid=<?php echo $sid;?>&m="+$("#txtCBGD").val(), "LLKH: " + $("#txtCBGD option:selected").html(), "print-icon.png");
			getLLKH($("#txtCBGD").val());
			$("#txtCBGD_Da_in").append('<option value=\''+$("#txtCBGD").val()+'\'>'+$("#txtCBGD option:selected").html()+'</option>');
			$("#txtCBGD option:selected").remove();
			$("#txtCBGD").val($("#txtCBGD option:first").val());
		}
	});	// end $("#btnIn").click(function()	
		
	
	initialTable(linkdata);
	
	$('input[placeholder],textarea[placeholder]').placeholder();
});


function getRowIndex( el ) {
	while( (el = el.parentNode) && el.nodeName.toLowerCase() !== 'tr' );

	if( el ) {
		//alert (el.rowIndex);
		return el.rowIndex-1;
	}
}
function printLLKH(pindex){
	i = pindex + 1;
	macb = document.getElementById('ds_cbgd').rows[i].cells[0].innerHTML;
	//listMahvu[a] = 0;
	//oTableData.fnDeleteRow( pindex ); 
	//alert(macb);
	
	getLLKH(macb);
}

function getLLKH(pmacb){
	if ($("#txtBieuMau_inbmgv").val()=="")
	{
		gv_open_msg_box('<font size=2 color=red>Vui lòng chọn biểu mẫu để xem trước khi in</font>', 'alert');
		$("#txtBieuMau_inbmgv").focus();
	}
	else
	{
		dataString = "a=get_llkh&hisid=<?php echo $_REQUEST["hisid"];?>&m="+pmacb;
		$.ajax({
			type: "POST",
			url: "gv/"+$("#txtBieuMau_inbmgv").val()+".php",
			data: dataString,
			dataType: "html",
			success: function(data) {
				print_llkh_writeConsole(data);
			}// end function(data)	
		}); // end .ajax
	}
}

function updatePassed(pindex){
	i = pindex + 1;
	macb = document.getElementById('ds_cbgd').rows[i].cells[0].innerHTML;
	
	dataString = "a=set_ngayduyet&hisid=<?php echo $_REQUEST["hisid"];?>&m="+macb;
	$.ajax({
		type: "POST",
		url: "gv/gv_in_bm_gv_process.php",
		data: dataString,
		dataType: "html",
		success: function(data) {
			$(".tooltips").tooltip( "destroy" );
			document.getElementById('ds_cbgd').rows[i].cells[7].innerHTML = data;
			$(".tooltips").tooltip();
		}// end function(data)	
	}); // end .ajax
}
</script>



<?php 
if (isset ($db_conn))
	oci_close($db_conn);
?>