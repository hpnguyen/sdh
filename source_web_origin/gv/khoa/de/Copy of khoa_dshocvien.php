<?php
if (isset($_REQUEST["hisid"])){
	session_id($_REQUEST["hisid"]);
	session_start();
}
if (!isset($_SESSION['uidloginPortal'])){
	die('Truy cập bất hợp pháp'); 
}

include "../libs/connect.php";
include "../libs/pgslibs.php";
if (!allowPermisstion(base64_decode($_SESSION['uidloginPortal']), '101'))
{
	die('Truy cập bất hợp pháp'); 
}

$makhoa = base64_decode($_SESSION['makhoa']);
?>
<div align=left>
<a id="khoa_dshocvien_btn_printpreview" style='font-size:80%'>&nbsp;Xem bản In</a>
</div>
<div id="khoa_dshocvien" >
	<div align='center'><h2>Danh Sách Học Viên Cao Học</h2></div>
	<div align='center' style='margin-bottom:10px;font-weight:bold;'> Khóa
		<select name="khoa_txtKhoaDSHocVien" id="khoa_txtKhoaDSHocVien" style="font-size:15px;">
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
	</div>
	<div style='margin-bottom:10px;'></div>
	
	<div style='margin-bottom:20px;'>
		<table id='khoa_tableDSHocVien' name='khoa_tableDSHocVien' width="100%" border="0"  cellspacing="0" class="ui-widget ui-widget-content ui-corner-top" >
        <thead>
          <tr class="ui-widget-header" style='height:20pt;'>
            <td align='center' class="ui-corner-tl">STT</td>
            <td>Mã HV</td>
			<td>Họ</td>
            <td  align='left'>Tên</td>
            <td  align='left' style=''>Phái</td>
			<td  align='center'>Ngày Sinh</td>
			<td  align='left'>Nơi Sinh</td>
            <td align='left'>Ngành</td>
			<td align=left>Hướng ĐT</td>
			<td align='center'>Đã TN</td>
			<td align=left class="ui-corner-tr"> <a href="javascript: loadDSHocVienFile()"> <img border='0' width='16' height='16' src='icons/save-icon.png' title='Download DS học viên'/> </a></td>
          </tr>
          </thead>
          <tbody>
		  
          </tbody>
        </table>
		
	</div>
</div> <!-- end  -->


<div id="dialog-download-dshocvien" title="Tải danh sách học viên">
	<p><span class="ui-icon ui-icon-disk" style="float:left; margin:3px 7px 20px 0;"></span><span id="dialog-download-dshocvien-msg">0</span></p>
</div>

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
	'<html><head><title>Phong Dao Tao SDH - DHBK</title></head>'
	+'<body bgcolor=white onLoad="self.focus()">'
	+content
	+'</body></html>'
	)
	top.consoleRef.document.close()
}

 function loadDSHocVien(pkhoa)
 {
	$("#khoa_tableDSHocVien tbody").html("<tr><td colspan='10' align='center'><img border='0' src='images/ajax-loader.gif'/></td></tr>");
	
	dataString = 'act=dshocvien' + '&k='+ pkhoa + "&hisid=<?php echo $_REQUEST["hisid"];?>";
	$.post("khoa/khoa_dshocvienprocess.php", dataString ,
		function(data){
			$("#khoa_tableDSHocVien tbody").html(data);
			//$('#dslopngaybatdauhk').text(' ' + $("#khoa_txtKhoaDSHocVien").val() + ' (tuần 1)');
	}, "html");
 }
 
 function loadDSHocVienFile()
 {
	dataString = 'act=dshocvienfile' + '&k='+ $('#khoa_txtKhoaDSHocVien').val() + "&hisid=<?php echo $_REQUEST["hisid"];?>";
	//alert(dataString);
	$.post("khoa/khoa_dshocvienprocess.php", dataString ,
		function(data){
			if (data.url!='') {
				$("#dialog-download-dshocvien-msg").html("<table><tr><td style='width:200px'>Danh sách học viên khóa: " + $('#khoa_txtKhoaDSHocVien').val() + "</td><td style='width:50px'><a style='color:blue;font-weight:bold;' target='_blank' href='"+data.url+"'><u>tải về</u></a></td></tr></table>");
				$("#dialog-download-dshocvien").dialog("open");
			}
		
	}, "json");
 }
 
$(function(){
 
 $( "#khoa_dshocvien_btn_printpreview" ).button({ icons: {primary:'ui-icon ui-icon-print'} });
 
 loadDSHocVien($("#khoa_txtKhoaDSHocVien").val());
 
 $("#khoa_txtKhoaDSHocVien").change(function(e) {
	loadDSHocVien($("#khoa_txtKhoaDSHocVien").val());
 });
 
 
$( "#dialog-download-dshocvien" ).dialog({
		resizable: false,
		autoOpen: false,
		width:300, height:150,
		modal: true,
		buttons: {
			"Đóng": function() {
				$( this ).dialog( "close" );
			}
		}
});

$("#khoa_dshocvien_btn_printpreview").click(function(){
	khoa_dshocvien_writeConsole("<div style='font-size:13px; font-weight:bold;' align=left>ĐẠI HỌC BÁCH KHOA - TP.HCM<br/>PHÒNG ĐÀO TẠO SAU ĐẠI HỌC<br/> &nbsp;</div>" + $("#khoa_dshocvien").html() + "<div style='clear:both;'>&nbsp;<br/><a href='JavaScript:window.print();'>In trang này</a></div>");
});

});
</script>



<?php 
if (isset ($db_conn))
	oci_close($db_conn);
?>