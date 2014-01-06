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
if (!allowPermisstion(base64_decode($_SESSION['uidloginPortal']), '103', $db_conn))
{
	die('Truy cập bất hợp pháp'); 
}

$makhoa = base64_decode($_SESSION['makhoa']);
?>
<div align="left" >
	<table  border="0" cellspacing="0" cellpadding="5" align=left>
		<tr>
			<td align=right style="width:50px;font-weight:bold"><label for='khoa_txtDotDSHocVienDKMH'>Học kỳ</label></td>	
			<td align=left style="width:200px;"><select name="khoa_txtDotDSHocVienDKMH" id="khoa_txtDotDSHocVienDKMH" style="font-size:15px;">
												   <?php $sqlstr="select (hoc_ky || '/' || nam_hoc_tu || '-' || nam_hoc_den) nam_hoc, dot_hoc
													from dot_hoc_nam_hoc_ky
													where dot_hoc in (select distinct dot_hoc 
													from dang_ky_mon_hoc d, hoc_vien h, nganh n, bo_mon b, khoa k
													where d.ma_hoc_vien = h.ma_hoc_vien
													and h.ma_nganh = n.ma_nganh
													and n.ma_bo_mon = b.ma_bo_mon
													and b.ma_khoa = k.ma_khoa
													and k.ma_khoa= '".$makhoa."')
													order by nam_hoc_tu desc, dot_hoc desc"; 
													$stmt = oci_parse($db_conn, $sqlstr);
													oci_execute($stmt);
													$n = oci_fetch_all($stmt, $resDM);
													oci_free_statement($stmt);
													
													for ($i = 0; $i < $n; $i++)
													{
														echo "<option value='".$resDM["DOT_HOC"][$i]."'>" .$resDM["NAM_HOC"][$i]. "</option>";
													}
													
												  ?>
												</select>
			</td>
		</tr>
		<tr>
			<td style="width:50px;"></td><td><a id="khoa_dshocvien_dkmh_btn_printpreview" style='font-size:80%'>&nbsp;Xem bản In</a> <a id="khoa_dshocvien_dkmh_btn_downloadpreview" style='font-size:80%'>&nbsp;Tải về DS HV</a></td>
		</tr>

	</table>
</div>
<div style='clear:both;'></div>
<div id="khoa_dshocvien_dkmh" align=center >	
</div> <!-- end  -->


<script type="text/javascript">
function khoa_dshocvien_dkmh_writeConsole(content) {
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

 function loadDSHocVienDKMH(pdot)
 {
	$("#khoa_dshocvien_dkmh").html("<tr><td colspan='10' align='center'><img border='0' src='images/ajax-loader.gif'/></td></tr>");
	$("#khoa_dshocvien_dkmh_btn_printpreview,#khoa_dshocvien_dkmh_btn_downloadpreview" ).button("disable");
	
	dataString = 'a=dshocvien' 
	+ '&d='+ pdot 
	+ '&h=' + encodeURIComponent($("#khoa_txtDotDSHocVienDKMH option:selected").html())
	+ "&hisid=<?php echo $_REQUEST["hisid"];?>";
	$.post("khoa/khoa_dshocvien_dkmh_process.php", dataString ,
		function(data){
			$("#khoa_dshocvien_dkmh").html(data);
			$("#khoa_dshocvien_dkmh_btn_printpreview,#khoa_dshocvien_dkmh_btn_downloadpreview" ).button("enable");
	}, "html");
 }
 
 function loadDSHocVienDKMHFile()
 {
	dataString = 'a=dshocvienfile' 
	+ '&d='+ $('#khoa_txtDotDSHocVienDKMH').val() 
	+ '&h=' + encodeURIComponent($("#khoa_txtDotDSHocVienDKMH option:selected").html())
	+ "&hisid=<?php echo $_REQUEST["hisid"];?>";
	//alert(dataString);
	$.post("khoa/khoa_dshocvien_dkmh_process.php", dataString ,
		function(data){
			if (data.url!='') {
				gv_open_msg_box("<p><table><tr><td style='width:200px; font-weight:bold'>Danh sách học viên ĐKMH HK " + $("#khoa_txtDotDSHocVienDKMH option:selected").html() + "</td><td style='width:80px'><a style='color:blue;font-weight:bold;' target='_blank' href='"+data.url+"'><u><span class='ui-icon ui-icon-disk' style='float:left; margin:0 5px 0 0;'></span> tải về</u></a></td></tr></table></p>", '', 300, 150);
			}
		
	}, "json");
 }
 
$(function(){
 
 $( "#khoa_dshocvien_dkmh_btn_printpreview" ).button({ icons: {primary:'ui-icon ui-icon-print'} });
 $( "#khoa_dshocvien_dkmh_btn_downloadpreview" ).button({ icons: {primary:'ui-icon ui-icon-disk'} });
 
 loadDSHocVienDKMH($("#khoa_txtDotDSHocVienDKMH").val());
 
 $("#khoa_txtDotDSHocVienDKMH").change(function(e) {
	loadDSHocVienDKMH($("#khoa_txtDotDSHocVienDKMH").val());
 });
 

$("#khoa_dshocvien_dkmh_btn_printpreview").click(function(){
	khoa_dshocvien_dkmh_writeConsole("<div style='font-size:13px; font-weight:bold;' align=left>ĐẠI HỌC BÁCH KHOA - TP.HCM<br/>PHÒNG ĐÀO TẠO SAU ĐẠI HỌC<br/> &nbsp;</div>" + $("#khoa_dshocvien_dkmh").html() + "<div style='clear:both;'>&nbsp;<br/><a href='JavaScript:window.print();'>In trang này</a></div>");
});
$("#khoa_dshocvien_dkmh_btn_downloadpreview").click(function(){
	loadDSHocVienDKMHFile();
});

});
</script>



<?php 
if (isset ($db_conn))
	oci_close($db_conn);
?>