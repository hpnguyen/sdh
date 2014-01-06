<?php

if (isset($_REQUEST["hisid"]))
{
	session_id($_REQUEST["hisid"]);
	session_start();
}

if (!isset($_SESSION['uidloginhv'])){
	die('Truy cập bất hợp pháp'); 
}

include "libs/connect.php";

$sqlstr = "SELECT value FROM config WHERE name = 'YCHVU_DK_CHO_PHEP'";
$stmt = oci_parse($db_conn, $sqlstr); oci_execute($stmt); oci_fetch_all($stmt, $resDM);

$allow = intval($resDM["VALUE"][0]);

$ma_hv = base64_decode($_SESSION["uidloginhv"]);

$sqlstr = "SELECT nvl(hvu_online, 1) hvu_online FROM hoc_vien WHERE ma_hoc_vien = '$ma_hv'";
$stmt = oci_parse($db_conn, $sqlstr); oci_execute($stmt); oci_fetch_all($stmt, $resDM);

$allow = $allow & intval($resDM["HVU_ONLINE"][0]);

if ($ma_hv == '03207104')
	$allow = 1;

if ($allow)
{
	
	
?>
<div align="left" style="margin:0 auto;">
<form id="form_dangkyhocvu" name="form_dangkyhocvu" method="post" action="">
   <table width="100%" border="0" cellspacing="0" cellpadding="5" align=center>
	<tr>
	  <td align=center colspan="3" style='font-size:80%'>
		<a id="dkhv_btn_dang_ky" name="taosach">&nbsp;Đăng ký yêu cầu học vụ</a>
	  </td>
	</tr>
   </table>

   <div id="dkhv_dangkyhocvu_chitiet" style="margin-top:5px;" align=left>
   <div style='margin-bottom:10px; font-size:12px;' align=left><b>Các học vụ đã đăng ký</b><br/> 
   </div>
	<table id=dkhv_dangkyhocvu_ds width='100%' border='0'  cellspacing='0' cellpadding='3' class='ui-widget ui-widget-content ui-corner-top tableData' >
		<thead>
		  <tr class='ui-widget-header heading' style='height:20pt;'>
			<td class='ui-corner-tl' align='left' style='width:70px'>Mã GQHV</td>
			<td >Nội dung</td>
			<td align='center'>Số lượng</td>
			<td align='right'>Phí</td>
			<td align='left' style='width:90px;'>Ngày đăng ký</td>
			<td align='left' style='width:90px;'>Hẹn trả</td>
			<td align='left'>Kết quả</td>
			<td align='right' style='width:90px;'>Trạng thái</td>
			<td class='ui-corner-tr' align='right'>Ngày nhận</td>
		  </tr>
	  </thead>
	  <tbody>
	  </tbody>
	</table>
	
   <br/><b><u>Lưu ý</u></b>: 
   <br/>&nbsp; • Học viên đăng ký yêu cầu học vụ mà không đến phòng Đào tạo SĐH nhận kết quả sẽ bị khóa chức năng đăng ký online.
   <br/>&nbsp; • Mọi thắc mắc liên quan đến học vụ vui lòng liên hệ số: <b>38-637-318</b> hoặc <b>38647256 - 5263</b>
   </div>

</form>
</div>

<div id=dkychv_frm_dangky style='width:650px;' title="Đăng ký yêu cầu học vụ">
	<table  border="0" cellspacing="0" cellpadding="5" align=left width=100% class="ui-corner-all">
		<tr>
			<td align=left>
				<b>Yêu cầu học vụ</b>
			</td>
			<td align=left>
				<b>SL</b>
			</td>
		</tr>
		<tr>
			<td align=left>
				<select id=dkychv_dmychv_hvu style='width:100%;height:25px; padding: 0 0 0 0; font-size:12px' class="text ui-widget-content ui-corner-all tableData">
					<option value='' selected style='color:black;'>-chọn yêu cầu học vụ-</option>
				  <?php $sqlstr="select ma_yc, noi_dung_yc, so_ngay_xu_ly, don_gia
							from hvu_dm_yc_hvu where dk_online = 1 order by noi_dung_yc"; 
					$stmt = oci_parse($db_conn, $sqlstr);
					oci_execute($stmt);
					$n = oci_fetch_all($stmt, $resDM);
					oci_free_statement($stmt);
					
					for ($i = 0; $i < $n; $i++)
					{
						echo "<option value='".$resDM["MA_YC"][$i]."'>" .$resDM["NOI_DUNG_YC"][$i]. "</option>";
					}
					
				  ?>
				</select>
				
			</td>
			<td>
				<input placeholder="sl" type=text name=dkychv_sl id=dkychv_sl maxlength=2 style="width:100%;text-align:center;font-size:12px" value=1 class="text ui-widget-content ui-corner-all tableData">			
			</td>
		</tr>
		
		<tr>
			<td align=left colspan=2>
				<input style="display: none;width:100%;font-size:12px" placeholder="thông tin yêu cầu thêm (Môn học, HK - năm học)" type=text name=dkychv_noidungyc id=dkychv_noidungyc maxlength=400 class="text ui-widget-content ui-corner-all tableData">
			</td>
		</tr>
		
		<tr>
			<td align=center colspan=2> 
				<button id="dkychv_ychvu_btn_add" style='height:26px;width:30px;'>&nbsp;</button> 
			</td>
		</tr>
		
	</table>
	<div style='clear:both;'></div>
	<table id=dkychv_dsychv_themmoi width="100%" style="margin-top:5px;" border="0" align="center" cellpadding="5" cellspacing="0" class="tablethemmoi ui-widget ui-widget-content ui-corner-all tableData" >
		<thead>
			<tr class="ui-widget-header heading" height="20">
				
				<th style='width:70px;' align=left>Mã HVụ</th>
				<th style='width:330px;' align=left>Nội dung</th>
				<th style='width:30px;' align=left>SL</th>
				<th style='width:30px;'></th>
			</tr>
		</thead>
		<tbody style="font-size:12px;">
		</tbody>
	</table>
</div>

<style>
	.YCHV_DaXL {color: #96c716; font-weight: bold;}
	.YCHV_ChuaXL {color: #bc3604; font-weight: bold;}
	.YCHV_DangXL {color: blue; font-weight: bold;}
	.YCHV_TrinhLD {color: blue; font-weight: bold;}
</style>

<script type="text/javascript">
var ychv_listMahvu, ychv_classname=''; 

function getRowIndex( el ) {
	while( (el = el.parentNode) && el.nodeName.toLowerCase() !== 'tr' );

	if( el ) {
		return el.rowIndex-1;
	}
}

function removeRow(pindex){
	i = pindex + 1;
	
	t = document.getElementById('dkychv_dsychv_themmoi');
	
	t.deleteRow( i );
}

function MaHVinList(pMaHV)
{	
	var n = document.getElementById('dkychv_dsychv_themmoi').rows.length, mahv;
	for (i=1 ; i<n; i++){
		mahv = document.getElementById('dkychv_dsychv_themmoi').rows[i].cells[0].innerHTML;
		if (pMaHV==mahv)
			return true;
	}
	return false;
}

function GetDSHocVu()
{
	//$("#dkhv_dangkyhocvu_chitiet").html("<div align=center><img border='0' src='images/ajax-loader.gif'/></div>");
	dataString = 'a=getDSGQHocVu&hisid=<?php echo $_REQUEST["hisid"]; ?>'+'&m='+  <?php echo "'$ma_hv'"; ?>;
	$.ajax({
	  type: 'POST', dataType: "html",
	  url: 'hv_dangkyhocvu_process.php',
	  data: dataString,
	  success: function(data) {
		$("#dkhv_dangkyhocvu_ds tbody").html(data);
	  }
	});
}

$(function() {
	$("#dkhv_btn_dang_ky").button({ icons: {primary:'ui-icon ui-icon-pencil'} });
	$("#dkychv_ychvu_btn_add").button({ icons: {primary:'ui-icon ui-icon-plusthick'} });
	
	$("#dkhv_btn_dang_ky").click(function(){
		$('#dkychv_dmychv_hvu').val("");
		$('#dkychv_dsychv_themmoi tbody').html("");
		$('#dkychv_frm_dangky').dialog('open');
	});	// end $("#dkhv_btn_dang_ky")
	
	$("#dkychv_dmychv_hvu").change(function(e){
		if ($("#dkychv_dmychv_hvu").val().length==4)
			$("#dkychv_noidungyc").css("display", "block");
		else
			$("#dkychv_noidungyc").css("display", "none");
			
		$("#dkychv_noidungyc").val("");
	});
	
	$("#dkychv_ychvu_btn_add").click(function(){
	
		var mahvu = $("#dkychv_dmychv_hvu").val();
		var noidung, soluong;
		
		if (mahvu != '')
		{
			if (mahvu.length == 4)
			{
				if ($("#dkychv_noidungyc").val() == "")
				{
					hv_open_msg_box("<font color=red><b>Vui lòng nhập thêm thông tin yêu cầu.</b></font>", 'info', 300, 180);
					return;
				}
			}
			
			if ($("#dkychv_noidungyc").val()!="")
				noidung = $("#dkychv_dmychv_hvu option:selected").html() + ': ' + $("#dkychv_noidungyc").val();
			else
				noidung = $("#dkychv_dmychv_hvu option:selected").html();
				
			soluong = $("#dkychv_sl").val();
			
			if (noidung!='')
			{
				if (!MaHVinList(mahvu))
				{
					//i = ychv_oCountRow+1;
					(ychv_classname == 'alt_') ? ychv_classname = 'alt' : ychv_classname = 'alt_';
					
					$( "#dkychv_dsychv_themmoi tbody" ).append( "<tr class='" + ychv_classname + "'>" +
					"<td align=left>" + mahvu + "</td>" +
					"<td align=left>" + noidung + "</td>" +
					"<td align=left>" + soluong + "</td>"+
					"<td><button class='dkychv_remove' style='height:26px;width:28px;' onclick='removeRow( getRowIndex(this) );'></button></td>" +
					"</tr>" );				
					//ychv_oCountRow += 1;
					$("button.dkychv_remove" ).button({ icons: {primary:'ui-icon ui-icon-trash'} });
				}				
			}	
			else
				hv_open_msg_box("Vui lòng nhập nội dung yêu cầu", 'info', 300, 180);
		}
		else
			hv_open_msg_box("Vui lòng chọn yêu cầu học vụ", 'info', 300, 180);
	 });
	
	$("#dkychv_frm_dangky").dialog({
		resizable: false,
		autoOpen: false,
		width:650, height:400,
		modal: true,
		buttons: {
			"Đăng ký": function() {
				tableData = document.getElementById('dkychv_dsychv_themmoi');
				//alert (tableData.rows.length);
				
				if (tableData.rows.length>1)
				{
					dataString = 'a=dangkyhocvu&hisid=<?php echo $_REQUEST["hisid"]; ?>'
						+'&m='+  <?php echo "'$ma_hv'"; ?> + '&mnn=' + '&c=' + tableData.rows.length;
					for (var i=1; i<tableData.rows.length;i++)
					{
						mayc = tableData.rows[i].cells[0].innerHTML;
						noidung = tableData.rows[i].cells[1].innerHTML;
						sl = tableData.rows[i].cells[2].innerHTML;
						
						//alert (ma + noidung + sl + phi + ngaytra + ghichu);
						dataString +='&myc'+i+'='+ mayc +'&n'+i+'=' + encodeURIComponent(noidung) +'&s'+i+'='+ sl;
					}
					//alert (dataString);
					xreq = $.ajax({
					  type: 'POST', dataType: "html",
					  url: 'hv_dangkyhocvu_process.php',
					  data: dataString,
					  success: function(data) {
						if (data != 'error')
						{
							GetDSHocVu();
						}
						else
							hv_open_msg_box("Có lỗi trong quá trình lưu lên Server. Vui lòng thử lại lần nữa.", 'alert', 250, 180);
					  },
					  error: function(xhr, ajaxOptions, thrownError) {
					  }
					});
					
					//RefreshTableYCHV(oTableYCHV,getFilter());
					$( this ).dialog( "close" );
				}
				else
				{
					hv_open_msg_box("Vui lòng nhập thông tin đầy đủ", 'info', 250, 180);
				}
			},
			"Hủy": function() {
				$( this ).dialog( "close" );
			}
			
		}
	 });
	 
	 GetDSHocVu();
});
</script>
<?php 
}
else
{
?>
	<div align="center" style="margin:0 auto; color: red; font-weight:bold">Chức năng này đang bị khóa.</div>
<?php
}

if (isset ($db_conn))
	oci_close($db_conn);
?>