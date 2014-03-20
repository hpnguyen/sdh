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
if (!allowPermisstion(base64_decode($_SESSION['uidloginPortal']), '104', $db_conn))
{
	die('Truy cập bất hợp pháp'); 
}

$makhoa = base64_decode($_SESSION['makhoa']);
?>
<div align="left" >
	<table  border="0" cellspacing="0" cellpadding="5" align=left>
		<tr>
			<td ></td> 
			<td align=left colspan=3>
				<select id="khoa_loaidshocvien" name="khoa_loaidshocvien" style="font-size:15px" onChange="khoa_loaiDSHV_change(this.value)">
					<option value='dshocvientn' selected="selected">Danh sách học viên đã tốt nghiệp</option>
					<option value='dshocviendudktn'>Danh sách học viên đủ điều kiện tốt nghiệp</option>
				</select>
			</td>
		</tr>
		<tr class="khoa_hv_tn_tr_dot_cap_bang">
			<td align=right style="width:80px;font-weight:bold"><label for='khoa_txtDotDSHocVienTN'>Đợt cấp bằng</label></td>	
			<td align=left style="width:200px;"><select name="khoa_txtDotDSHocVienTN" id="khoa_txtDotDSHocVienTN" style="font-size:15px;">
												   <?php $sqlstr="SELECT distinct dot_cap_bang
																FROM xet_luan_van x, hoc_vien h, nganh n, bo_mon b, khoa k
																WHERE x.ma_hoc_vien = h.ma_hoc_vien
																AND h.ma_nganh = n.ma_nganh
																AND n.ma_bo_mon = b.ma_bo_mon
																AND b.ma_khoa = k.ma_khoa
																AND k.ma_khoa = '$makhoa'
																AND dot_cap_bang IS NOT NULL
																ORDER BY dot_cap_bang DESC"; 
													$stmt = oci_parse($db_conn, $sqlstr);
													oci_execute($stmt);
													$n = oci_fetch_all($stmt, $resDM);
													oci_free_statement($stmt);
													
													for ($i = 0; $i < $n; $i++)
													{
														echo "<option value='".$resDM["DOT_CAP_BANG"][$i]."'>" .$resDM["DOT_CAP_BANG"][$i]. "</option>";
													}
													
												  ?>
												</select>
			</td>
		</tr>
		<tr>
			<td style="width:50px;"></td><td><a id="khoa_dshocvientn_dot_btn_printpreview" style='font-size:80%'>&nbsp;Xem bản In</a>
			 <a id="khoa_dshocvien_tn_btn_downloadpreview" style='font-size:80%'>&nbsp;Tải về DS HV</a></td>
		</tr>
	</table>
</div>
<div style='clear:both;'></div>
<div id="khoa_dshocvientn_dot" align=center >	
</div> <!-- end  -->


<script type="text/javascript">
function khoa_dshocvientn_dot_writeConsole(content) {
	top.consoleRef=window.open('','myconsole',
	'width=800,height=450'
	+',menubar=0'
	+',toolbar=0'
	+',status=0'
	+',scrollbars=1'
	+',resizable=1')
	top.consoleRef.document.writeln(
	'<html><head><title>Phong Dao Tao SDH - DHBK</title></head>'
	+'<body bgcolor=white onLoad="self.focus()">'
	+content
	+'</body></html>'
	)
	top.consoleRef.document.close()
}

 function loadDSHocVienTNdot(pdot)
 {
	$("#khoa_dshocvientn_dot").html("<tr><td colspan='10' align='center'><img border='0' src='images/ajax-loader.gif'/></td></tr>");
	$("#khoa_dshocvientn_dot_btn_printpreview,#khoa_dshocvien_tn_btn_downloadpreview" ).button("disable");
	
	dataString = 'a=dshocvien&loaids='+$("#khoa_loaidshocvien").val() + '&d='+ pdot + "&hisid=<?php echo $_REQUEST["hisid"];?>";
	$.post("khoa/khoa_dshocvien_tn_dothocprocess.php", dataString ,
		function(data){
			$("#khoa_dshocvientn_dot").html(data);
			$("#khoa_dshocvientn_dot_btn_printpreview,#khoa_dshocvien_tn_btn_downloadpreview" ).button("enable");
	}, "html");
 }
 
 function loadDSHocVienTNdotFile()
 {
	dataString = 'a=dshocvienfile' + '&d='+ $('#khoa_txtDotDSHocVienTN').val() + "&hisid=<?php echo $_REQUEST["hisid"];?>";
	//alert(dataString);
	$.post("khoa/khoa_dshocvien_tn_dothocprocess.php", dataString ,
		function(data){
			if (data.url!='') {
				gv_open_msg_box("<p><table><tr><td style='width:200px; font-weight:bold'>Danh sách học viên TN đợt: " + $('#khoa_txtDotDSHocVienTN').val() + "</td><td style='width:80px'><a style='color:blue;font-weight:bold;' target='_blank' href='"+data.url+"'><u><span class='ui-icon ui-icon-disk' style='float:left; margin:0 5px 0 0;'></span>tải về</u></a></td></tr></table></p>", '', 300, 150);
			}
		
	}, "json");
 }
 
 function khoa_loaiDSHV_change(pLoai){
	if (pLoai=="dshocvientn"){
		//$("#khoa_txtDotDSHocVienTN").removeAttr('disabled');
		$('.khoa_hv_tn_tr_dot_cap_bang').show();
	}else if (pLoai=="dshocviendudktn"){
		$('.khoa_hv_tn_tr_dot_cap_bang').hide();
		//$("#khoa_txtDotDSHocVienTN").attr('disabled', 'disabled');
	}
 }
 
$(function(){
 
 $( "#khoa_dshocvientn_dot_btn_printpreview" ).button({ icons: {primary:'ui-icon ui-icon-print'} });
 $( "#khoa_dshocvien_tn_btn_downloadpreview" ).button({ icons: {primary:'ui-icon ui-icon-disk'} });
 
 loadDSHocVienTNdot($("#khoa_txtDotDSHocVienTN").val());
 
 $("#khoa_txtDotDSHocVienTN, #khoa_loaidshocvien").change(function(e) {
	loadDSHocVienTNdot($("#khoa_txtDotDSHocVienTN").val());
 });
 

$("#khoa_dshocvientn_dot_btn_printpreview").click(function(){
	khoa_dshocvientn_dot_writeConsole("<div style='font-size:13px; font-weight:bold;' align=left>ĐẠI HỌC BÁCH KHOA - TP.HCM<br/>PHÒNG ĐÀO TẠO SAU ĐẠI HỌC<br/> &nbsp;</div>" + $("#khoa_dshocvientn_dot").html() + "<div style='clear:both;'>&nbsp;<br/><a href='JavaScript:window.print();'>In trang này</a></div>");
});
$("#khoa_dshocvien_tn_btn_downloadpreview").click(function(){
	loadDSHocVienTNdotFile();
});

});
</script>



<?php 
if (isset ($db_conn))
	oci_close($db_conn);
?>