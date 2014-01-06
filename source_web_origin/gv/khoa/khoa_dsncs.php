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
if (!allowPermisstion(base64_decode($_SESSION['uidloginPortal']), '102', $db_conn))
{
	die('Truy cập bất hợp pháp'); 
}

$makhoa = base64_decode($_SESSION['makhoa']);
?>
<div align="left" >
	<table  border="0" cellspacing="0" cellpadding="5" align=left>
		<tr>
			<td align=right style="width:50px;font-weight:bold"><label for='khoa_txtKhoaDSNCS'>Khóa</label></td>	
			<td align=left style="width:200px;"><select name="khoa_txtKhoaDSNCS" id="khoa_txtKhoaDSNCS" style="font-size:15px;">
												   <?php $sqlstr="SELECT DISTINCT khoa
																FROM hoc_vien h, nganh n , bo_mon b , khoa k
																WHERE h.ma_bac = 'TS'
																AND h.ma_nganh is not null 
																AND h.ma_nganh  = n.ma_nganh
																AND n.ma_bo_mon = b.ma_bo_mon
																AND b.ma_khoa = k.ma_khoa
																AND b.ma_khoa = '$makhoa'
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
			<td style="width:50px;"></td><td><a id="khoa_dsncs_btn_printpreview" style='font-size:80%'>&nbsp;Xem bản In</a> <a id="khoa_dsncs_btn_downloadpreview" style='font-size:80%' data-placement="bottom" class="tooltips" title='DS này có thêm các thông tin: Tổng chỉ tích lũy, Điểm CĐ 1, 2, 3, Điểm CĐ Tổng quan, KQ Bảo vệ HĐ Khoa (CS), KQ Bảo vệ HĐ Trường (NN)'>&nbsp;Tải về DS HV</a></td>
		</tr>
	</table>
</div>
<div style='clear:both;'></div>
<div id="khoa_dsncs" align=center>	
</div> <!-- end  -->


<script type="text/javascript">
function khoa_dsncs_writeConsole(content) {
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

 function loadDSNCS(pkhoa)
 {
	$("#khoa_dsncs").html("<tr><td colspan='10' align='center'><img border='0' src='images/ajax-loader.gif'/></td></tr>");
	$("#khoa_dsncs_btn_printpreview,#khoa_dsncs_btn_downloadpreview" ).button("disable");
	
	dataString = 'act=dsncs' + '&k='+ pkhoa + "&hisid=<?php echo $_REQUEST["hisid"];?>";
	$.post("khoa/khoa_dsncsprocess.php", dataString ,
		function(data){
			$("#khoa_dsncs").html(data);
			$("#khoa_dsncs_btn_printpreview,#khoa_dsncs_btn_downloadpreview" ).button("enable");
	}, "html");
 }
 
 function loadDSNCSFile()
 {
	dataString = 'act=dsncsfile' + '&k='+ $('#khoa_txtKhoaDSNCS').val() + "&hisid=<?php echo $_REQUEST["hisid"];?>";
	//alert(dataString);
	$.post("khoa/khoa_dsncsprocess.php", dataString ,
		function(data){
			if (data.url!='') {
				gv_open_msg_box("<p><table><tr><td style='width:200px; font-weight:bold'>Danh sách NCS khóa: " + $('#khoa_txtKhoaDSNCS').val() + "</td><td style='width:80px'><a style='color:blue;font-weight:bold;' target='_blank' href='"+data.url+"'><u><span class='ui-icon ui-icon-disk' style='float:left; margin:0 5px 0 0;'></span> tải về</u></a></td></tr></table></p>", '', 300, 150);
			}
		
	}, "json");
 }
 
$(function(){
 $("#khoa_dsncs_btn_downloadpreview").tooltip();
 $( "#khoa_dsncs_btn_printpreview" ).button({ icons: {primary:'ui-icon ui-icon-print'} });
 $( "#khoa_dsncs_btn_downloadpreview" ).button({ icons: {primary:'ui-icon ui-icon-disk'} });
 
 loadDSNCS($("#khoa_txtKhoaDSNCS").val());
 
 $("#khoa_txtKhoaDSNCS").change(function(e) {
	loadDSNCS($("#khoa_txtKhoaDSNCS").val());
 });

$("#khoa_dsncs_btn_printpreview").click(function(){
	khoa_dsncs_writeConsole("<div style='font-size:13px; font-weight:bold;' align=left>ĐẠI HỌC BÁCH KHOA - TP.HCM<br/>PHÒNG ĐÀO TẠO SAU ĐẠI HỌC<br/> &nbsp;</div>" + $("#khoa_dsncs").html() + "<div style='clear:both;'>&nbsp;<br/><a href='JavaScript:window.print();'>In trang này</a></div>");
});

$("#khoa_dsncs_btn_downloadpreview").click(function(){
	loadDSNCSFile();
});

});
</script>



<?php 
if (isset ($db_conn))
	oci_close($db_conn);
?>