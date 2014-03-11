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

if (!allowPermisstion(base64_decode($_SESSION['uidloginPortal']), '019', $db_conn))
{
	die('Truy cập bất hợp pháp'); 
}

$makhoa = base64_decode($_SESSION['makhoa']);
?>

<div id="phong_dshocvienDKMH" >
	<table  border="0" cellspacing="0" cellpadding="5" align=left>
		<tr>
			<td align=right style="width:50px;font-weight:bold"><label for='phong_txtDotDSHocVienDKMH'>Học kỳ</label></td>	
			<td align=left style="width:50px;">
				<select name="phong_txtDotDSHocVienDKMH" id="phong_txtDotDSHocVienDKMH" style="font-size:15px" onChange="phong_loadDSKhoaDKMH(this.value)">
				    <?php $sqlstr="select (hoc_ky || '/' || nam_hoc_tu || '-' || nam_hoc_den) nam_hoc, dot_hoc
					from dot_hoc_nam_hoc_ky
					where dot_hoc in (select distinct dot_hoc 
					from dang_ky_mon_hoc d, hoc_vien h, nganh n, bo_mon b, khoa k
					where d.ma_hoc_vien = h.ma_hoc_vien
					and h.ma_nganh = n.ma_nganh
					and n.ma_bo_mon = b.ma_bo_mon
					and b.ma_khoa = k.ma_khoa)
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
			<td align=right style="width:40px;font-weight:bold"><label for='phong_txtMaKhoaDSHocVienDKMH'>Khoa</label></td>	
			<td align=left> 
				<select name="phong_txtMaKhoaDSHocVienDKMH" id="phong_txtMaKhoaDSHocVienDKMH" style="font-size:15px">
				</select> 
			</td>
		</tr>
		<tr>
			<td ></td> <td align=left colspan=3><a id="phong_dshocvienDKMH_btn_printpreview" style='font-size:80%'>&nbsp;Xem bản In</a> 
			<a id="phong_dshocvienDKMH_btn_downloadpreview" style='font-size:80%'>&nbsp;Tải về DS HV</a></td>
		</tr>
	</table>

	<div style='clear:both;'></div>
	<div id="phong_dshocvienDKMH_detail" align=center></div>
</div> <!-- end  -->


<script type="text/javascript">

 function phong_loadDSKhoaDKMH(pDot)
 {
	$("#phong_dshocvienDKMH_detail").html("<img border='0' src='images/ajax-loader.gif'/>");
	$("#phong_dshocvienDKMH_btn_printpreview,#phong_dshocvienDKMH_btn_downloadpreview" ).button("disable");
	
	dataString = 'a=dot-khoa' 
	+ '&d='+ pDot 
	+ "&hisid=<?php echo $_REQUEST["hisid"];?>";
	$.post("phong/phong_dshocvien_dkmh_process.php", dataString ,
		function(data){
			$("#phong_txtMaKhoaDSHocVienDKMH").html(data);
			phong_loadDSHocVienDKMH(pDot, $("#phong_txtMaKhoaDSHocVienDKMH").val());
			
	}, "html");
 }
 
 function phong_loadDSHocVienDKMH(pDot, pMaKhoa)
 {
	$("#phong_dshocvienDKMH_detail").html("<img border='0' src='images/ajax-loader.gif'/>");
	$("#phong_dshocvienDKMH_btn_printpreview,#phong_dshocvienDKMH_btn_downloadpreview" ).button("disable");
	
	dataString = 'a=dshocvien' 
	+ '&d='+ pDot 
	+ '&khoa=' + pMaKhoa 
	+ '&h=' + encodeURIComponent($("#phong_txtDotDSHocVienDKMH option:selected").html())
	+ "&hisid=<?php echo $_REQUEST["hisid"];?>";
	$.post("phong/phong_dshocvien_dkmh_process.php", dataString ,
		function(data){
			$("#phong_dshocvienDKMH_detail").html(data);
			$("#phong_dshocvienDKMH_btn_printpreview,#phong_dshocvienDKMH_btn_downloadpreview" ).button("enable");
	}, "html");
 }
 
 function phong_loadDSHocVienDKMHFile()
 {
	dataString = 'a=dshocvienfile' 
	+ '&d='+ $('#phong_txtDotDSHocVienDKMH').val()
	+ '&h=' + encodeURIComponent($("#phong_txtDotDSHocVienDKMH option:selected").html())
	+ '&khoa=' + $("#phong_txtMaKhoaDSHocVienDKMH").val() 
	+ "&hisid=<?php echo $_REQUEST["hisid"];?>";
	
	$.post("phong/phong_dshocvien_dkmh_process.php", dataString ,
		function(data){
			if (data.url!='') {
				gv_open_msg_box("<p><table><tr><td style='width:200px; font-weight:bold'>Danh sách học viên ĐKMH HK " + $("#phong_txtDotDSHocVienDKMH option:selected").html() + "<br/>Khoa " + $("#phong_txtMaKhoaDSHocVienDKMH option:selected").html() + "</td><td style='width:80px'><a style='color:blue;font-weight:bold;' target='_blank' href='"+data.url+"'><u><span class='ui-icon ui-icon-disk' style='float:left; margin:0 5px 0 0;'></span> tải về</u></a></td></tr></table></p>", '', 300, 170);
			}
	}, "json");
 }
 
$(function(){
 $("#phong_dshocvienDKMH_btn_printpreview" ).button({ icons: {primary:'ui-icon ui-icon-print'} });
 $("#phong_dshocvienDKMH_btn_downloadpreview").button({ icons: {primary:'ui-icon ui-icon-disk'} });
 
 $("#phong_dshocvienDKMH_btn_printpreview").click(function(){
	writeConsole("<div style='font-size:13px; font-weight:bold;' align=left>ĐẠI HỌC BÁCH KHOA - TP.HCM<br/>PHÒNG ĐÀO TẠO SAU ĐẠI HỌC<br/> &nbsp;</div>" + $("#phong_dshocvienDKMH_detail").html() + "<div style='clear:both;'>&nbsp;<br/><a href='JavaScript:window.print();'>In trang này</a></div>", 800,600);
 });
 
 $("#phong_dshocvienDKMH_btn_downloadpreview").click(function(){
	phong_loadDSHocVienDKMHFile();
 });
 
 //phong_loadDSHocVienDKMH($("#phong_txtDotDSHocVienDKMH").val(), $("#phong_txtMaKhoaDSHocVienDKMH").val());
 phong_loadDSKhoaDKMH($("#phong_txtDotDSHocVienDKMH").val());
 
 $("#phong_txtMaKhoaDSHocVienDKMH").change(function(e) {
	phong_loadDSHocVienDKMH($("#phong_txtDotDSHocVienDKMH").val(), $("#phong_txtMaKhoaDSHocVienDKMH").val());
 });

});
</script>

<?php 
if (isset ($db_conn))
	oci_close($db_conn);
?>