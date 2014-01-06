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
if (!allowPermisstion(base64_decode($_SESSION['uidloginPortal']), '010', $db_conn))
{
	die('Truy cập bất hợp pháp'); 
}

$macb = $_SESSION['macb'];
?>

<div id="gvDSLop" >

	<table  border="0" cellspacing="0" cellpadding="5" align=left>
		<tr>
			<td align=right style="width:60px;font-weight:bold"><label for='txtKhoaDSLop'>Chọn HK</label></td>	
			<td align=left style="width:100px;">
				<select name="txtKhoaDSLop" id="txtKhoaDSLop" style="font-size:15px;">
					   <?php $sqlstr="select (hoc_ky || '/' || nam_hoc_tu || '-' || nam_hoc_den) nam_hoc, to_char(dot_hoc,'dd-mm-yyyy') dot_hoc
									from dot_hoc_nam_hoc_ky
									where dot_hoc in (select distinct dot_hoc from thoi_khoa_bieu where ma_can_bo='".$macb."' or ma_can_bo_phu='".$macb."')
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
			<td style="width:50px;"></td><td><a id="gv_dslop_btn_printpreview" style='font-size:80%'>&nbsp;Xem bản In</a></td>
		</tr>
	</table>
	<div style='clear:both;'></div>
	<div style='margin:20px 0 10px 5px; ' align=left><strong>Ngày bắt đầu HK: <span id='dslopngaybatdauhk'></span></strong></div>
	<div style='margin-bottom:20px;'>
		<table id='tableDSMonHoc' name='tableDSLop' width="100%" border="0"  cellspacing="0" class="ui-widget ui-widget-content ui-corner-top tableData" >
        <thead>
          <tr class="ui-widget-header heading" style='height:20pt;'>
            <td align='center' class="ui-corner-tl">STT</td>
            <td>Tên Môn Học</td>
			<td>Mã MH</td>
            <td  align='center'>Lớp</td>
            <td  align='center' style='width:300px;'>Chuyên ngành</td>
			<td  align='center'>SL</td>
            <td align='center' class="ui-corner-tr">DS Lớp</td>
          </tr>
          </thead>
          <tbody>
		  
          </tbody>
        </table>
		
	</div>
	<div align='center' id='gv_divdslop'></div>
</div> <!-- end  -->

<script type="text/javascript">
 function loadDSLop(dothoc, lop, monhoc)
 {
	$("#gv_divdslop").html("<img border='0' src='images/ajax-loader.gif'/>");
	
	dataString = 'act=dslop' 
	+ '&dothoc='+ dothoc 
	+ '&lop=' + lop 
	+ '&monhoc='+ monhoc 
	+ "&hisid=<?php echo $_REQUEST["hisid"];?>";
	
	$.post("gv/gv_dslopprocess.php", dataString ,
		function(data){
			$("#gv_divdslop").html(data);
			$('#dslopngaybatdauhk').text(' ' + $("#txtKhoaDSLop").val() + ' (tuần 1)');
			$("#gv_dslop_btn_printpreview" ).button("enable");
	}, "html");
 }
 
 function loadDSLopFile(pdothoc, plop, pmonhoc)
 {
	
	dataString = 'act=dslopfile' 
	+ '&dothoc='+ pdothoc 
	+ '&lop=' + plop 
	+ '&monhoc='+ pmonhoc
	+ "&hisid=<?php echo $_REQUEST["hisid"];?>";

	$.post("gv/gv_dslopprocess.php", dataString ,
		function(data){
			if (data.url!='') {
				gv_open_msg_box("<p><table><tr><td style='width:200px; font-weight:bold'>Danh sách lớp: " + plop + ", môn học: " + pmonhoc +", đợt học: " + pdothoc + "</td><td style='width:80px'><a style='color:blue;font-weight:bold;' target='_blank' href='"+data.url+"'><u><span class='ui-icon ui-icon-disk' style='float:left; margin:0 5px 0 0;'></span> tải về</u></a></td></tr></table></p>", '', 300, 150);
			}
		
	}, "json");
 }
 
$(function(){
 
 $("#gv_dslop_btn_printpreview" ).button({ icons: {primary:'ui-icon ui-icon-print'} });
 
 $("#gv_dslop_btn_printpreview").click(function(){
	writeConsole("<div style='font-size:13px; font-weight:bold;' align=left>ĐẠI HỌC BÁCH KHOA - TP.HCM<br/>PHÒNG ĐÀO TẠO SAU ĐẠI HỌC<br/> &nbsp;</div>" + $("#gv_divdslop").html() + "<div style='clear:both;'>&nbsp;<br/><a href='JavaScript:window.print();'>In trang này</a></div>", 900,600);
 });
 
 loadDSMonHoc();
 
 
 $('#dslopngaybatdauhk').text(' ' + $("#txtKhoaDSLop").val() + ' (tuần 1)');
 
 $("#txtKhoaDSLop").change(function(e) {
	loadDSMonHoc();
 });
 
 function loadDSMonHoc()
 {
	$("#tableDSMonHoc tbody").html("<tr><td colspan='8' align='center'><img border='0' src='images/ajax-loader.gif'/></td></tr>");
	
	if ($("#gv_divdslop").html()=='')
		 $("#gv_dslop_btn_printpreview" ).button("disable");
		 
	dataString = $("#txtKhoaDSLop").serialize()+ '&act=dsmonhoc' + "&hisid=<?php echo $_REQUEST["hisid"];?>";
	//alert(dataString);
	$.post("gv/gv_dslopprocess.php", dataString ,
		function(data){
			//alert(data);
			$("#tableDSMonHoc tbody").html(data);
			$('#dslopngaybatdauhk').text(' ' + $("#txtKhoaDSLop").val() + ' (tuần 1)');
	}, "html");
 }
 
});
</script>



<?php 
if (isset ($db_conn))
	oci_close($db_conn);
?>