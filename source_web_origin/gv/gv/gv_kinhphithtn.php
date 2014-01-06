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
if (!allowPermisstion(base64_decode($_SESSION['uidloginPortal']), '012', $db_conn))
{
	die('Truy cập bất hợp pháp'); 
}

$macb = $_SESSION['macb'];
?>

<div id="gvKPTHTN" >

	<table  border="0" cellspacing="0" cellpadding="5" align=left>
		<tr>
			<td align=right style="width:60px;font-weight:bold"><label for='txtKhoaKinhPhiTHTN'>Chọn HK</label></td>	
			<td align=left style="width:100px;">
				<select name="txtKhoaKinhPhiTHTN" id="txtKhoaKinhPhiTHTN" style="font-size:15px;">
					   <?php $sqlstr="select (hoc_ky || '/' || nam_hoc_tu || '-' || nam_hoc_den) nam_hoc, dot_hoc
									from dot_hoc_nam_hoc_ky
									where dot_hoc in (select distinct dot_hoc from kinh_phi_th_tn_cap
													where dot_hoc in (select distinct dot_hoc from thoi_khoa_bieu where ma_can_bo='".$macb."' or ma_can_bo_phu='".$macb."'))
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
			<td style="width:50px;"></td><td><a id="gv_kptntn_btn_printpreview" style='font-size:80%'>&nbsp;Xem bản In</a></td>
		</tr>
	</table>
	
	<div style='clear:both;'></div>
	<div id="gv_kpthtn_detail" align=center></div>
	
</div> <!-- end  -->

<script type="text/javascript">
$(function(){
 $("#gv_kptntn_btn_printpreview" ).button({ icons: {primary:'ui-icon ui-icon-print'} });
 $("#gv_kptntn_btn_printpreview").click(function(){
	writeConsole("<div style='font-size:13px; font-weight:bold;' align=left>ĐẠI HỌC BÁCH KHOA - TP.HCM<br/>PHÒNG ĐÀO TẠO SAU ĐẠI HỌC<br/> &nbsp;</div>" + $("#gv_kpthtn_detail").html() + "<div style='clear:both;'>&nbsp;<br/><a href='JavaScript:window.print();'>In trang này</a></div>", 900,450);
 });
 
 loadKinhPhiTHTN();
 
 $("#txtKhoaKinhPhiTHTN").change(function(e) {
	loadKinhPhiTHTN();
 });
 
 function loadKinhPhiTHTN()
 {
	$("#gv_kpthtn_detail").html("<img border='0' src='images/ajax-loader.gif'/>");
	
	dataString = $("#txtKhoaKinhPhiTHTN").serialize()
	+ '&h=' + encodeURIComponent($("#txtKhoaKinhPhiTHTN option:selected").html())
	+ "&hisid=<?php echo $_REQUEST["hisid"];?>";
	//alert(dataString);
	$.post("gv/gv_kinhphithtnprocess.php", dataString ,
		function(data){
			//alert(data);
			$("#gv_kpthtn_detail").html(data);
	}, "html");
 }
});
</script>



<?php 
if (isset ($db_conn))
	oci_close($db_conn);
?>