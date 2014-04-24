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
if (!allowPermisstion(base64_decode($_SESSION['uidloginPortal']), '101', $db_conn))
{
	die('Truy cập bất hợp pháp'); 
}

$makhoa = base64_decode($_SESSION['makhoa']);
?>
<div align="left" >
	<table  border="0" cellspacing="0" cellpadding="5" align=left>
		<tr>
			<td align=right style="width:50px;font-weight:bold"><label for='khoa_txtKhoaDSHocVien'>Khóa</label></td>	
			<td align=left style="width:200px;"><select name="khoa_txtKhoaDSHocVien" id="khoa_txtKhoaDSHocVien" style="font-size:15px;">
												   <?php $sqlstr="select distinct khoa
																from hoc_vien
																where khoa >= 2005
																order by khoa desc"; 
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
			<td style="width:50px;"></td><td><a id="khoa_dshocvien_btn_printpreview" style='font-size:80%'>&nbsp;Xem bản In</a> 
			<a id="khoa_dshocvien_btn_downloadpreview" style='font-size:80%' data-placement="bottom" class="tooltips" title='DS này có thêm các thông tin: Tổng chỉ tích lũy MH, Trung bình tích lũy MH, TB Toàn khóa, Tổng chỉ toàn khóa, Đợt cấp bằng, Thuộc CTĐT'>&nbsp;Tải về DS HV</a></td>
		</tr>
	</table>
</div>
<div style='clear:both;'></div>
<div id="khoa_dshocvien" align=center >	
</div> <!-- end  -->

<script type="text/javascript">
function khoa_dshocvien_writeConsole(content) {
	top.consoleRef=window.open('','myconsole',
	'width=800,height=450'
	+',menubar=0'
	+',toolbar=0'
	+',status=0'
	+',scrollbars=1'
	+',resizable=1')
	top.consoleRef.document.writeln(
	'<html><head><title>Phòng Đào Tạo SĐH - ĐHBK</title></head>'
	+'<body bgcolor=white onLoad="self.focus()">'
	+content
	+'</body></html>'
	)
	top.consoleRef.document.close()
}

 function loadDSHocVien(pkhoa)
 {
	$("#khoa_dshocvien").html("<tr><td colspan='10' align='center'><img border='0' src='images/ajax-loader.gif'/></td></tr>");
	$("#khoa_dshocvien_btn_printpreview,#khoa_dshocvien_btn_downloadpreview" ).button("disable");
	
	dataString = 'act=dshocvien' + '&k='+ pkhoa + "&hisid=<?php echo $_REQUEST["hisid"];?>";
	$.post("khoa/khoa_dshocvien_process.php", dataString ,
		function(data){
			$("#khoa_dshocvien").html(data);
			$("#khoa_dshocvien_btn_printpreview,#khoa_dshocvien_btn_downloadpreview" ).button("enable");
	}, "html");
 }
 
 function loadDSHocVienFile()
 {
	dataString = 'act=dshocvienfile' + '&k='+ $('#khoa_txtKhoaDSHocVien').val() + "&hisid=<?php echo $_REQUEST["hisid"];?>";
	
	//alert(dataString);
	$.post("khoa/khoa_dshocvien_process.php", dataString ,
		function(data){
			if (data.url!='') {
				gv_open_msg_box("<p><table><tr><td style='width:200px; font-weight:bold'>Danh sách học viên khóa: " + $('#khoa_txtKhoaDSHocVien').val() + "</td><td style='width:80px'><a style='color:blue;font-weight:bold;' target='_blank' href='"+data.url+"'><u><span class='ui-icon ui-icon-disk' style='float:left; margin:0 5px 0 0;'></span> tải về</u></a></td></tr></table></p>", '', 300, 150);
			}
		
	}, "json");
 }
 
$(function(){
 $( "#khoa_dshocvien_btn_downloadpreview" ).tooltip();
 $( "#khoa_dshocvien_btn_printpreview" ).button({ icons: {primary:'ui-icon ui-icon-print'} });
 $( "#khoa_dshocvien_btn_downloadpreview" ).button({ icons: {primary:'ui-icon ui-icon-disk'} });
 
 loadDSHocVien($("#khoa_txtKhoaDSHocVien").val());
 
 $("#khoa_txtKhoaDSHocVien").change(function(e) {
	loadDSHocVien($("#khoa_txtKhoaDSHocVien").val());
 });
 

$("#khoa_dshocvien_btn_printpreview").click(function(){
	khoa_dshocvien_writeConsole("<div style='font-size:13px; font-weight:bold;' align=left>ĐẠI HỌC BÁCH KHOA - TP.HCM<br/>PHÒNG ĐÀO TẠO SAU ĐẠI HỌC<br/> &nbsp;</div>" + $("#khoa_dshocvien").html() + "<div style='clear:both;'>&nbsp;<br/><a href='JavaScript:window.print();'>In trang này</a></div>");
});

$("#khoa_dshocvien_btn_downloadpreview").click(function(){
	loadDSHocVienFile();
});

});
</script>



<?php 
if (isset ($db_conn))
	oci_close($db_conn);
?>