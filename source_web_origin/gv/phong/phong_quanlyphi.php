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
if (!allowPermisstion(base64_decode($_SESSION['uidloginPortal']), '123', $db_conn))
	die('Truy cập bất hợp pháp'); 

$macb = $_SESSION['macb'];
$makhoa = base64_decode($_SESSION["makhoa"]);
?>

<div id="gvQUANLYPHI_Phong" >

	<table  border="0" cellspacing="0" cellpadding="5" align=left>
		<tr>
			<td align=right style="width:60px;font-weight:bold"><label for='phong_txtKhoaQuanLyPhi'>Chọn HK</label></td>	
			<td align=left style="width:100px;">
				<select name="phong_txtKhoaQuanLyPhi" id="phong_txtKhoaQuanLyPhi" style="font-size:15px;">
					   <?php $sqlstr="select (hoc_ky || '/' || nam_hoc_tu || '-' || nam_hoc_den) nam_hoc, dot_hoc
									from dot_hoc_nam_hoc_ky
									where dot_hoc in (select distinct dot_hoc from quan_ly_phi q, bo_mon b, khoa k
													where q.ma_bo_mon = b.ma_bo_mon(+) and b.ma_khoa = k.ma_khoa and k.ma_khoa = '$makhoa')
									order by nam_hoc_tu desc, dot_hoc desc"; 
						$stmt = oci_parse($db_conn, $sqlstr);oci_execute($stmt);$n = oci_fetch_all($stmt, $resDM);oci_free_statement($stmt);
						for ($i = 0; $i < $n; $i++)
						{
							echo "<option value='".$resDM["DOT_HOC"][$i]."'>" .$resDM["NAM_HOC"][$i]. "</option>";
						}
					  ?>
				</select>
			</td>
			<td align=right style="width:40px;font-weight:bold"><label for='phong_txtMaKhoaQuanlyphi'>Khoa</label></td>	
			<td align=left> 
				<select name="phong_txtMaKhoaQuanlyphi" id="phong_txtMaKhoaQuanlyphi" style="font-size:15px">
				   <option value=''>Tất cả Khoa</option>
				   <?php $sqlstr="select distinct k.ma_khoa, k.TEN_KHOA
					from quan_ly_phi q, bo_mon b, khoa k
					where q.ma_bo_mon = b.ma_bo_mon(+) and b.ma_khoa = k.ma_khoa
					order by VIET0DAU_NAME(ten_khoa)"; 
					$stmt = oci_parse($db_conn, $sqlstr);oci_execute($stmt);$n = oci_fetch_all($stmt, $resDM);oci_free_statement($stmt);
					for ($i = 0; $i < $n; $i++){
						if ($i==0)
							$selected = "selected";
						else
							$selected = "";
						echo "<option value='".$resDM["MA_KHOA"][$i]."' $selected>" .$resDM["TEN_KHOA"][$i]. "</option>";
					}
				  ?>
				</select> 
			</td>
		</tr>
		<tr>
			<td style="width:50px;"></td><td colspan=3><a id="gv_quanlyphi_btn_printpreview" style='font-size:80%'>&nbsp;Xem bản In</a></td>
		</tr>
	</table>
	
	<div style='clear:both;'></div>
	<div id="gv_quanlyphi_detail" align=center></div>
	
</div> <!-- end  -->

<script type="text/javascript">
$(function(){
 $("#gv_quanlyphi_btn_printpreview" ).button({ icons: {primary:'ui-icon ui-icon-print'} });
 $("#gv_quanlyphi_btn_printpreview").click(function(){
	//writeConsole("<div style='font-size:13px; font-weight:bold;' align=left>ĐẠI HỌC BÁCH KHOA - TP.HCM<br/>PHÒNG ĐÀO TẠO SAU ĐẠI HỌC<br/> &nbsp;</div>" + $("#gv_quanlyphi_detail").html() + "<div style='clear:both;'>&nbsp;<br/><a href='JavaScript:window.print();'>In trang này</a></div>", 900,450);
	print_llkh_writeConsole($("#gv_quanlyphi_detail").html(), 0, "Quản lý phí - SĐH ĐHBK TP.HCM", 'style="font-family:Times New Roman,Arial,Helvetica,sans-serif;"',800,550);
 });
 
 loadQuanLyPhi();
 
 $("#phong_txtKhoaQuanLyPhi, #phong_txtMaKhoaQuanlyphi").change(function(e) {
	loadQuanLyPhi();
 });
 
 function loadQuanLyPhi()
 {
	$("#gv_quanlyphi_detail").html("<img border='0' src='images/ajax-loader.gif'/>");
	
	dataString = $("#phong_txtKhoaQuanLyPhi").serialize() + '&' + $("#phong_txtMaKhoaQuanlyphi").serialize()
	+ '&h=' + encodeURIComponent($("#phong_txtKhoaQuanLyPhi option:selected").html())
	+ '&tkhoa=' + encodeURIComponent($("#phong_txtMaKhoaQuanlyphi option:selected").html())
	+ "&hisid=<?php echo $_REQUEST["hisid"];?>";
	//alert(dataString);
	
	$.ajax({
		type: "POST",url: "phong/phong_quanlyphi_process.php",data: dataString,dataType: "html",
		success: function(data) {
			$("#gv_quanlyphi_detail").html(data);
		}// end function(data)	
	}); // end .ajax
 }
});
</script>



<?php 
if (isset ($db_conn))
	oci_close($db_conn);
?>