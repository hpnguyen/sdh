<?php
if (isset($_REQUEST["hisid"])){
	session_id($_REQUEST["hisid"]);
	session_start();
}
if (!isset($_SESSION['uidloginPortal'])){
	die('Đã hết phiên làm việc'); 
}

include "../libs/connectnckhda.php";
include "../libs/connect.php";
include "../libs/pgslibs.php";
if (!allowPermisstion(base64_decode($_SESSION['uidloginPortal']), '056', $db_conn))
{
	die('Truy cập bất hợp pháp');
}
$usr = base64_decode($_SESSION['uidloginPortal']);
$makhoa = base64_decode($_SESSION['makhoa']);

$sqlstr="select id, ho || ' ' || ten ho_ten from nhan_su where upper(username)=upper('$usr')"; 
$stmt = oci_parse($db_conn, $sqlstr);oci_execute($stmt);$n=oci_fetch_all($stmt, $resDM);oci_free_statement($stmt);

$id = $resDM["ID"][0];
$hoten = $resDM["HO_TEN"][0];

//echo "Remote ip: ".$_SERVER['REMOTE_ADDR'] . " x: ".$_SERVER['HTTP_X_FORWARDED_FOR'];

//if ($_SERVER['HTTP_X_FORWARDED_FOR'] != '172.28.40.166')
//	die('Truy cập bất hợp pháp');
?>
<div id="khcn_ql_phanbien_thuyetminhdtkhcn" style="width:100%;">
		
	<div style='margin:0 0 10px 0;'>
		<table width="100%" height="20" border="0" align="center" cellpadding="5" cellspacing="0" class="ui-widget ui-widget-content ui-corner-all ">
			<tr>
				<td style="width:70%" align=left><button id=khcn_ql_phanbien_refresh style="margin:0 10px 0 0;">Refresh DS Đề Tài</button> </td>
				<td style="width:30%" align=right></td>
			</tr>
		</table>
	</div>
	
	<!-- Filter -->
	<div style='margin:0 0 10px 0px;'> 
		<table width="100%" border="0" align="center" cellpadding="5"  cellspacing="0" class="ui-widget ui-widget-content ui-corner-all ">
			<tr>
				<td style="width:85px;">
					<select id=khcn_ql_phanbien_filter_tmdt_nam_nhan title="Fillter theo năm nhận hồ sơ" style='width:100%; height:25px; padding: 0 0 0 0;' class="ui-widget-content ui-corner-all tableData khcn_tooltips">
					  <?php 
						$nam =date("Y");
						$sqlstr="select distinct to_char(NGAY_NHAN_HO_SO, 'YYYY') nam from nckh_thuyet_minh_de_tai where NGAY_NHAN_HO_SO is not null"; 
						$stmt = oci_parse($db_conn_khcn, $sqlstr);oci_execute($stmt);$n = oci_fetch_all($stmt, $resDM);oci_free_statement($stmt);
						echo "<option value=''>-năm nhận-</option>";
						for ($i = 0; $i < $n; $i++){
							if ($resDM["NAM"][$i] == $nam)
								$selected = "selected";
							else
								$selected = "";
							echo "<option value='".$resDM["NAM"][$i]."' $selected>" .$resDM["NAM"][$i]. "</option>";
						}
					  ?>
					</select>
				</td>
							
				<td>
					<select id=khcn_ql_phanbien_filter_tmdt_cndt title="Fillter theo chủ nhiệm đề tài" style='width:100%;height:25px; padding: 0 0 0 0;' class=" ui-widget-content ui-corner-all tableData khcn_tooltips" >
					  <?php 
							$sqlstr="select distinct FK_MA_CAN_BO, cb.HO || ' ' || cb.TEN || ' (' || cb.MA_HOC_HAM || ' ' || cb.MA_HOC_VI || ')' HO_TEN
							from NCKH_THUYET_MINH_DE_TAI dt, CAN_BO_GIANG_DAY cb 
							where fk_ma_can_bo is not null and dt.FK_MA_CAN_BO = cb.MA_CAN_BO and dt.THUNG_RAC is null
							order by viet0dau_name(ho_ten)"; 
							$stmt = oci_parse($db_conn_khcn, $sqlstr);oci_execute($stmt);$n = oci_fetch_all($stmt, $resDM);oci_free_statement($stmt);
							echo "<option value=''>-tất cả chủ nhiệm đt-</option>";
							for ($i = 0; $i < $n; $i++){
								echo "<option value='".$resDM["FK_MA_CAN_BO"][$i]."'>" .$resDM["HO_TEN"][$i]. "</option>";
							}
					  ?>
					</select>
				</td>
				
				<td>
					<select id=khcn_ql_phanbien_filter_tmdt_dcndt title="Fillter theo đồng chủ nhiệm đề tài" style='width:100%;height:25px; padding: 0px 0 0 0;' class="text ui-widget-content ui-corner-all tableData khcn_tooltips" >
					  <?php 
							$sqlstr="select distinct DCNDT_HH_HV_HO_TEN from NCKH_THUYET_MINH_DE_TAI where DCNDT_HH_HV_HO_TEN is not null and THUNG_RAC is null order by DCNDT_HH_HV_HO_TEN"; 
							$stmt = oci_parse($db_conn_khcn, $sqlstr);oci_execute($stmt);$n = oci_fetch_all($stmt, $resDM);oci_free_statement($stmt);
							echo "<option value=''>-tất cả đồng chủ nhiệm đt-</option>";
							for ($i = 0; $i < $n; $i++){
								echo "<option value='".$resDM["DCNDT_HH_HV_HO_TEN"][$i]."'>" .$resDM["DCNDT_HH_HV_HO_TEN"][$i]. "</option>";
								//echo "<option value='".$resDM["ID"][$i]."'>" .$resDM["HO_TEN"][$i]. "</option>";
							}
					  ?>
					</select>
				</td>
				
				<td>
					<select id=khcn_ql_phanbien_filter_nguoiphanbien title="Fillter theo người phản biện" style='width:100%;height:25px; padding: 0 0 0 0;' class=" ui-widget-content ui-corner-all tableData khcn_tooltips" >
					  <?php 
							$sqlstr="select distinct FK_MA_CAN_BO, cb.HO || ' ' || cb.TEN || ' (' || cb.MA_HOC_HAM || ' ' || cb.MA_HOC_VI || ')' HO_TEN
							from NCKH_PHAN_CONG_PHAN_BIEN dt, CAN_BO_GIANG_DAY cb 
							where dt.FK_MA_CAN_BO = cb.MA_CAN_BO
							order by viet0dau_name(ho_ten)"; 
							$stmt = oci_parse($db_conn_khcn, $sqlstr);oci_execute($stmt);$n = oci_fetch_all($stmt, $resDM);oci_free_statement($stmt);
							echo "<option value=''>-tất cả người phản biện-</option>";
							for ($i = 0; $i < $n; $i++){
								echo "<option value='".$resDM["FK_MA_CAN_BO"][$i]."'>" .$resDM["HO_TEN"][$i]. "</option>";
							}
					  ?>
					</select>
				</td>
				
				<td>
					<select id=khcn_ql_phanbien_filter_capdt title="Fillter theo cấp đề tài" style='width:100%; height:25px; padding: 0 0 0 0;' class="ui-widget-content ui-corner-all tableData khcn_tooltips" >
					  <?php $sqlstr="select MA_CAP, TEN_CAP from CAP_DE_TAI where MA_CAP_CHA is not null and DK_TMDT=1 order by STT"; 
						$stmt = oci_parse($db_conn_khcn, $sqlstr);oci_execute($stmt);$n = oci_fetch_all($stmt, $resDM);oci_free_statement($stmt);
						echo "<option value=''>-tất cả cấp đề tài-</option>";
						for ($i = 0; $i < $n; $i++){
							echo "<option value='".$resDM["MA_CAP"][$i]."'>" .$resDM["TEN_CAP"][$i]. "</option>";
						}
					  ?>
					</select>
				</td>
				
				<td>
					<select id=khcn_ql_phanbien_filter_donvi title="Fillter theo đơn vị" style='width:100%; height:25px; padding: 0 0 0 0;' class="ui-widget-content ui-corner-all tableData khcn_tooltips" >
					  <?php $sqlstr="select distinct ten_khoa, b.ma_khoa from NCKH_THUYET_MINH_DE_TAI tm, can_bo_giang_day c, bo_mon b, khoa k where tm.fk_ma_can_bo=c.ma_can_bo and b.ma_bo_mon=c.ma_bo_mon and b.ma_khoa=k.ma_khoa and tm.THUNG_RAC is null order by ten_khoa"; 
						$stmt = oci_parse($db_conn_khcn, $sqlstr);oci_execute($stmt);$n = oci_fetch_all($stmt, $resDM);oci_free_statement($stmt);
						echo "<option value=''>-tất cả đơn vị-</option>";
						for ($i = 0; $i < $n; $i++){
							echo "<option value='".$resDM["MA_KHOA"][$i]."'>" .$resDM["TEN_KHOA"][$i]. "</option>";
						}
					  ?>
					</select>
				</td>
				
				<td>
					<select id=khcn_ql_phanbien_filter_trangthai title="Fillter theo trạng thái đề tài" style='width:100%; height:25px; padding: 0 0 0 0;' class="ui-widget-content ui-corner-all tableData khcn_tooltips" >
					  <?php $sqlstr="select MA_TINH_TRANG, TEN_TINH_TRANG, decode(EDIT_ALLOW, '1', '*') EDIT_ALLOW from NCKH_DM_TINH_TRANG order by stt"; 
						$stmt = oci_parse($db_conn_khcn, $sqlstr);oci_execute($stmt);$n = oci_fetch_all($stmt, $resDM);oci_free_statement($stmt);
						echo "<option value=''>-tất cả trạng thái-</option>";
						for ($i = 0; $i < $n; $i++){
							echo "<option value='".$resDM["MA_TINH_TRANG"][$i]."'>" .$resDM["TEN_TINH_TRANG"][$i]." ".$resDM["EDIT_ALLOW"][$i]."</option>";
						}
					  ?>
					</select>
				</td>
			</tr>
		</table>
	</div>
	
	<div style="display: block; margin: 10px 0 0 0; background: white; border-radius: 10px; padding: 5px;">
		<table id=khcn_ql_phanbien_ds_thuyetminhdtkhcn width="100%"  border="0" align="center" cellpadding="5" cellspacing="0" class="ui-widget ui-widget-content ui-corner-top tableData display" style='font-size:13px;'>
			<thead>
				<tr class="ui-widget-header heading" >
					<th style="width: 40px" align=left>Mã</th>
					<th align=left>Tên đề tài</th>
					<th style="" align=left>Chủ nhiệm đề tài</th>
					<th style="" align=left>Đồng chủ nhiệm</th>
					<th style="" align=left>Cán bộ tham gia</th>
					<th style="" align=left>Cấp đề tài</th>
					<th style="" align=left>Đơn vị</th>
					<th style="" align=left>Tổng KP</th>
					<th style="" align=left>Nguồn Trường</th>
					<th style="" align=left>Nguồn khác</th>
					<th style="" align=left>Nhóm ngành</th>
					<th style="" align=left title="Thời gian (Tháng)">Thời<br>gian</th>
					<th align=left>Người phản biện</th>
					<th align=right>In</th>
					<th style="" align=left>A1</th>
					<th style="" align=left>A2</th>
					<th style="" align=left>A3</th>
					<th style="" align=left>A4 (Nhận xét)</th>
					<th style="" align=left>B. Đánh giá</th>
					<th style="" align=left>C. Nhận xét</th>
					<th style="" align=left>Tổng ĐTB</th>
				</tr>
			</thead>			
		</table>
	</div>
	
	<div class="clearfloat"></div>
</div> <!-- end  -->

<style type="text/css">
	.khcn_ql_phanbien_thuyetminh_error {color: red;}

	#progress, #progress1 { position:relative; width:100%; border: 1px solid #ddd; padding: 1px; border-radius: 3px; }
	#bar, #bar1 { background-color: #B4F5B4; width:0%; height:20px; border-radius: 3px; }
	#percent, #percent1 { position:absolute; display:inline-block; top:3px; left:48%; }
</style>

<script type="text/javascript">
var oTableQlPhanbienThuyetMinhDTKHCN;
var khcn_ql_phanbien_linkdata = "khcn/khcn_ql_phanbien_tmdt_process.php?hisid=<?php echo $_REQUEST["hisid"]; ?>";
var khcn_ql_phanbien_matm_selected = null, bValid=true, khcn_ql_phanbien_nTr_selected = null;
var khcn_ql_phanbien_class = 'alt_';

var khcn_ql_phanbien_tmdt_col_idx = new Array(); 
 khcn_ql_phanbien_tmdt_col_idx['madt'] 			= 0;
 khcn_ql_phanbien_tmdt_col_idx['tendt'] 		= 1;
 khcn_ql_phanbien_tmdt_col_idx['cndt']			= 2;
 khcn_ql_phanbien_tmdt_col_idx['dcn']			= 3;
 khcn_ql_phanbien_tmdt_col_idx['cbthamgia']		= 4;
 khcn_ql_phanbien_tmdt_col_idx['capdetai']		= 5;
 khcn_ql_phanbien_tmdt_col_idx['donvi']			= 6;
 khcn_ql_phanbien_tmdt_col_idx['kinhphi']		= 7;
 khcn_ql_phanbien_tmdt_col_idx['nguontruong']	= 8;
 khcn_ql_phanbien_tmdt_col_idx['nguonkhac']		= 9;
 khcn_ql_phanbien_tmdt_col_idx['nhomnganh']		= 10;
 khcn_ql_phanbien_tmdt_col_idx['thoigian']		= 11;
 khcn_ql_phanbien_tmdt_col_idx['nguoiphanbien']	= 12;
 khcn_ql_phanbien_tmdt_col_idx['in']			= 13;
 khcn_ql_phanbien_tmdt_col_idx['a1']			= 14;
 khcn_ql_phanbien_tmdt_col_idx['a2']	 		= 15;
 khcn_ql_phanbien_tmdt_col_idx['a3']			= 16;
 khcn_ql_phanbien_tmdt_col_idx['a4']			= 17;
 khcn_ql_phanbien_tmdt_col_idx['b']				= 18;
 khcn_ql_phanbien_tmdt_col_idx['c']				= 19;
 khcn_ql_phanbien_tmdt_col_idx['dtb']			= 20;
 
 
$(document).ready(function() {
	$('input[placeholder],textarea[placeholder]').placeholder();	
	
	$("#khcn_ql_phanbien_refresh" ).button({ icons: {primary:'ui-icon ui-icon-refresh'} });
	
	$('.khcn_autonumbers').autoNumeric('init', {'wEmpty': 'zero', aSep: '.', aDec: ','});
	
	$("#khcn_ql_phanbien_filter_tmdt_nam_nhan, #khcn_ql_phanbien_filter_nguoiphanbien, #khcn_ql_phanbien_filter_tmdt_cndt, #khcn_ql_phanbien_filter_tmdt_dcndt, #khcn_ql_phanbien_filter_capdt, #khcn_ql_phanbien_filter_donvi, #khcn_ql_phanbien_filter_trangthai").change(function(e){
		khcn_ql_phanbien_RefreshTableThuyeMinh(oTableQlPhanbienThuyetMinhDTKHCN,khcn_ql_phanbien_qltmdt_getFilter());
	});
	
	$('#khcn_ql_phanbien_refresh').click( function() {
		khcn_ql_phanbien_RefreshTableThuyeMinh(oTableQlPhanbienThuyetMinhDTKHCN,khcn_ql_phanbien_qltmdt_getFilter());
	});
	
	khcn_ql_phanbien_qltm_LoadStateFilter();
	khcn_ql_phanbien_initialTableThuyetMinhDTKHCN(khcn_ql_phanbien_qltmdt_getFilter());
	
	$(".khcn_tooltips").tooltip({ track: true });
	
});
// 'C<"clear">lfrtip'  'T<"clear">lfrtip'
function khcn_ql_phanbien_initialTableThuyetMinhDTKHCN(urldata){
	oTableQlPhanbienThuyetMinhDTKHCN = $('#khcn_ql_phanbien_ds_thuyetminhdtkhcn').dataTable( {
		"bJQueryUI": true,
		"bStateSave": true,
		"bAutoWidth": false, 
		"sPaginationType": "full_numbers",
		"sDom": 'TC<"clear">lfrtip',
		"oTableTools": {
			"sSwfPath": "../datatable/extras/TableTools/media/swf/copy_csv_xls_pdf.swf",
			"aButtons": [
				{
					"sExtends": "copy",
					"sButtonText": "Copy"
				},
				{
					"sExtends": "xls",
					"sButtonText": "Excel"
				},
				{
					"sExtends": "print",
					"sButtonText": "Print"
				}
				 
			]
		},
		"aoColumnDefs": [
			{ "bVisible": false, "aTargets": [ 4,6,7,8,9,14,15,16,17,18,19 ] }
		],
		"oColVis": {
			"buttonText": "Ẩn/hiện cột dữ liệu",
			"bRestore": true,
			"aiExclude": [ 	0,1,2,3,5,10,11,12,13,20  ]
		},
		"oLanguage": {
			"sUrl": "../datatable/media/language/vi_VI.txt"
		},
		"bProcessing": true,
		"sAjaxSource": urldata,
		"fnDrawCallback": function( oSettings ) {
			//$(document).tooltip({ track: true });
			$(".khcn_tooltips").tooltip({ track: true });
			
			$('#khcn_ql_phanbien_ds_thuyetminhdtkhcn').find('tbody').find('tr').each(function(){
				$(this).click(function(){
					oTableQlPhanbienThuyetMinhDTKHCN.$('tr.row_selected').removeClass('row_selected');
					$(this).addClass('row_selected');
					
					khcn_ql_phanbien_nTr_selected = $(this)[0];
				});
			});
		}, 
		"fnRowCallback": function( nRow, aaData, iDisplayIndex ) {
			for (var i=0 ; i<21 ; i++){
				aaData[i] = reverse_escapeJsonString(aaData[i]);
			}
			
			//alert(aaData[14]);
			//$('td:eq('+khcn_ql_phanbien_tmdt_col_idx['madt']+')', nRow).css({'font-weight': 'bold'});
			//$('td:eq('+khcn_ql_phanbien_tmdt_col_idx['capdetai']+')', nRow).css({'font-weight': 'bold'});
			//$('td:eq('+khcn_ql_phanbien_tmdt_col_idx['trangthai']+')', nRow).css({'font-weight': 'bold'});
			
			return nRow;
		},
		"aoColumns": [
            null,			// Ma de tai
			null, 			// Ten de tai
			null,			// Chu nhiem de tai
            null,			// Dong chu nhiem
			null,			// Can bo tham gia
			null, 			// Cap de tai
			null, 			// Don vi
			null,			// Tong KP
			null,			// Nguon truong
			{ "sClass" : "left"},							// Nguon khac
			{ "sClass" : "left"},							// Nhom nganh
			{ "bSortable": true},							// Thoi gian thuc hien
			{ "bSortable": true}, 							//Nguoi phan bien
			{ "sClass" : "center", "bSortable": false},		// In
			{ "sClass" : "left", "bSortable": false},		// A1
			{ "sClass" : "left", "bSortable": false},		// A2
			{ "bSortable": false},							// A3
			{ "bSortable": false},							// A4 nhan xet
			{ "bSortable": true},							// Danh gia
			{ "bSortable": false},							// C.Nhan xet
			{ "bSortable": true}							// Tong diem tb
        ]
	} );
}

function khcn_ql_phanbien_RefreshTableThuyeMinh(tableId, urlData){
	//$(document).tooltip( "destroy" );
	$(".khcn_tooltips").tooltip( "destroy" );
	
	gv_processing_diglog("open", "Đang xử lý ... vui lòng chờ");
	
	khcn_ql_phanbien_qltm_SaveStateFilter();
	
	table = $(tableId).dataTable();
	oSettings = table.fnSettings();
	$('#khcn_ql_phanbien_ds_thuyetminhdtkhcn_processing').attr('style', 'visibility:visible');
	$.getJSON(urlData, null, function( json )
	{
		table.fnClearTable(this);
		for (var i=0; i<json.aaData.length; i++)
		{
			table.oApi._fnAddData(oSettings, json.aaData[i]);
		}
		oSettings.aiDisplay = oSettings.aiDisplayMaster.slice();
		table.fnDraw();
		$('#khcn_ql_phanbien_ds_thuyetminhdtkhcn_processing').attr('style', 'visibility:hidden');
		
		gv_processing_diglog("close", "...");
	});
}


/* Get the rows which are currently selected */
function khcn_ql_phanbien_fnGetSelected( oTableLocal ){
    return oTableLocal.$('tr.row_selected');
}

function khcn_ql_phanbien_getRowIndex( el ) {
    while( (el = el.parentNode) && el.nodeName.toLowerCase() !== 'tr' );

    if( el ) {
        return el.rowIndex-1;
	}
}

function khcn_ql_phanbien_print_tmdt(pindex, pcap){
	var i = pindex + 1;
	var matmdt = document.getElementById('khcn_ql_phanbien_ds_thuyetminhdtkhcn').rows[i].cells[0].innerHTML;
	var fileprint='', tabname='', key = 'print_tmdt_' +i + '_' + pcap;
	var tabOpened = window.ns.get_tabOpened();
	var tabCurrent = $('#' + tabOpened['khcn_quanly_tmdt']).index()-1;
	if (pcap > 20 && pcap < 25) { // Cap DHQG
		fileprint = 'khcn_print_tmdt_r01.php';
		tabname = 'TMĐT - ĐHQG Mẫu R01 - ' + matmdt;
	}else if (pcap > 30 && pcap < 35) { // Cap truong
		fileprint = 'khcn_print_tmdt_t12.php';
		tabname = 'TMĐT - Trường Mẫu 12 - ' + matmdt;
	}
	if (fileprint && tabname){
		window.ns.addTab_ns(key, tabname, 'print-preview-icon24x24.png', tabCurrent, "khcn/"+fileprint+"?a=print_tmdt_fromtab&hisid=<?php echo $_REQUEST["hisid"];?>&m="+matmdt+"&k="+key);
	}
}

function khcn_ql_phanbien_print_phanbien_report(pindex, pcap, pmcb){
	var i = pindex;
	var matmdt = document.getElementById('khcn_ql_phanbien_ds_thuyetminhdtkhcn').rows[i].cells[0].innerHTML;
	//alert (matmdt);
	var fileprint='', tabname='', key = 'print_danh_gia_tmdt_' +i + '_' + pcap;
	var tabOpened = window.ns.get_tabOpened();
	var tabCurrent = $('#' + tabOpened['khcn_quanly_tmdt']).index()-1;
	
	if (pcap == 23) { // Cap DHQG Loai C
		fileprint = 'khcn_print_danh_gia_tmdt_m01.php';
		tabname = 'Đánh giá TMĐT - ĐHQG Mẫu M01 - ' + matmdt;
	}else if (pcap > 30 && pcap < 35) { // Cap truong
		fileprint = 'khcn_print_danh_gia_tmdt_m06.php';
		tabname = 'Đánh giá TMĐT - Trường Mẫu BM06/KHCN-08 - ' + matmdt;
	}
	if (fileprint && tabname){
		window.ns.addTab_ns(key, tabname, 'print-preview-icon24x24.png', tabCurrent, "khcn/"+fileprint+"?a=print_tmdt_fromtab&hisid=<?php echo $_REQUEST["hisid"];?>&mdt="+matmdt+"&k="+key+"&mcb="+pmcb);
	}
}


function khcn_ql_phanbien_qltmdt_checksession(){
	dataString = 'a=checksession';
	return xreq = $.ajax({
	  type: 'POST', dataType: "json", data: dataString,
	  url: khcn_ql_phanbien_linkdata,
	  success: function(data) {
		return jQuery.parseJSON(data);
	  }
	});
}

function khcn_ql_phanbien_qltmdt_getFilter(){
	var linkfilter = khcn_ql_phanbien_linkdata+"&a=refreshdata";
	if ($("#khcn_ql_phanbien_filter_tmdt_cndt").val() != "" )
		linkfilter += "&fcndt="+$("#khcn_ql_phanbien_filter_tmdt_cndt").val();
	if ($("#khcn_ql_phanbien_filter_tmdt_dcndt").val() != "" )
		linkfilter += "&fdcndt="+$("#khcn_ql_phanbien_filter_tmdt_dcndt").val();
	if ($("#khcn_ql_phanbien_filter_capdt").val() != "" )
		linkfilter += "&fcdt="+$("#khcn_ql_phanbien_filter_capdt").val();
	if ($("#khcn_ql_phanbien_filter_donvi").val() != "" )
		linkfilter += "&fdv="+$("#khcn_ql_phanbien_filter_donvi").val();
	if ($("#khcn_ql_phanbien_filter_tmdt_nam_nhan").val() != "" )
		linkfilter += "&fnnhan="+$("#khcn_ql_phanbien_filter_tmdt_nam_nhan").val();
	if ($("#khcn_ql_phanbien_filter_trangthai").val() != "" )
		linkfilter += "&ftrangthai="+$("#khcn_ql_phanbien_filter_trangthai").val();
	if ($("#khcn_ql_phanbien_filter_nguoiphanbien").val() != "" )
		linkfilter += "&fnpbien="+$("#khcn_ql_phanbien_filter_nguoiphanbien").val();
	
	
	return linkfilter;
}

function khcn_ql_phanbien_qltm_SaveStateFilter(){
	var filterstr = $("#khcn_ql_phanbien_filter_tmdt_cndt").val()+"&"+$("#khcn_ql_phanbien_filter_tmdt_dcndt").val()
	+ "&" + $("#khcn_ql_phanbien_filter_capdt").val()+"&"+$("#khcn_ql_phanbien_filter_donvi").val()+"&"+$("#khcn_ql_phanbien_filter_tmdt_nam_nhan").val()
	+ "&" + $("#khcn_ql_phanbien_filter_trangthai").val()+ "&" + $("#khcn_ql_phanbien_filter_nguoiphanbien").val();
	
	$.cookie("FilterQLPBDTKHCN", filterstr, { expires : 30 });
}

function khcn_ql_phanbien_qltm_LoadStateFilter(){
	if ($.cookie("FilterQLPBDTKHCN") != null) {
		var cookieValueArr = $.cookie("FilterQLPBDTKHCN").split("&");
		
		if ($.cookie("FilterQLPBDTKHCN") != "")
		{
			$("#khcn_ql_phanbien_filter_tmdt_cndt").val(cookieValueArr[0]);
			$("#khcn_ql_phanbien_filter_tmdt_dcndt").val(cookieValueArr[1]);
			$("#khcn_ql_phanbien_filter_capdt").val(cookieValueArr[2]);
			$("#khcn_ql_phanbien_filter_donvi").val(cookieValueArr[3]);
			$("#khcn_ql_phanbien_filter_tmdt_nam_nhan").val(cookieValueArr[4]);
			$("#khcn_ql_phanbien_filter_trangthai").val(cookieValueArr[5]);
			$("#khcn_ql_phanbien_filter_nguoiphanbien").val(cookieValueArr[6]);
			//alert(cookieValueArr[6]);
		}
	}
}

function khcn_ql_phanbien_view_phanbien(pMaTM, pMaCB, pCapDT){
	khcn_ql_phanbien_qltmdt_checksession().done(function(data){
		if (data.success != 1){
			gv_open_msg_box("<font style='color:red;'>Đã hết phiên làm việc vui lòng đăng nhập lại</font>'", 'alert', 250, 180, true);
			return;
		}else{
			if (pCapDT == 23) { // Cap DHQG Loai C
				fileprint = 'khcn_print_danh_gia_tmdt_m01.php';
				tabname = 'Đánh giá TMĐT - ĐHQG Mẫu M01 - ' + pMaTM;
			}else if (pCapDT > 30 && pCapDT < 35) { // Cap truong
				fileprint = 'khcn_print_danh_gia_tmdt_bm06.php';
				tabname = 'Đánh giá TMĐT - Trường Mẫu BM06/KHCN-08 - ' + pMaTM;
			}
			var links = "http://<?php echo str_replace("khcn_ql_phanbien_tmdt.php?hisid=".$_REQUEST["hisid"], "",$_SERVER["SERVER_NAME"].$_SERVER["REQUEST_URI"]); ?>"+fileprint+"?a=print_tmdt_pdf&hisid=<?php echo $_REQUEST["hisid"]; ?>&mdt="+pMaTM+"&mcb="+pMaCB+"&k=";
			window.open(links,tabname,'width=650,height=800,menubar=1'+',toolbar=0'+',status=0'+',scrollbars=1'+',resizable=1');				
		}
	});	
}

 </script>



<?php
if (isset ($db_conn))
	oci_close($db_conn);
	
if (isset ($db_conn_khcn))
	oci_close($db_conn_khcn);
?>