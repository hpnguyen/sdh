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

if (!allowPermisstion(base64_decode($_SESSION['uidloginPortal']), '018', $db_conn))
{
	die('Đã hết phiên làm việc'); 
}

$makhoa = base64_decode($_SESSION['makhoa']);
?>

<div id="phong_dshocvienkhoa" >
	<table  border="0" cellspacing="0" cellpadding="5" align=left>
		<tr>
			<td align=right style="width:80px;font-weight:bold"><label for='phong_txtDotDSHocVienTN'>Chọn đợt</label></td>	
			<td align=left style="width:50px;">
				<select name="phong_txtDotDSHocVienTN" id="phong_txtDotDSHocVienTN" style="font-size:15px">
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
			<td align=right style="width:40px;font-weight:bold"><label for='phong_txtMaKhoaDSHocVienTN'>Khoa</label></td>	
			<td align=left> 
				<select name="phong_txtMaKhoaDSHocVienTN" id="phong_txtMaKhoaDSHocVienTN" style="font-size:15px">
				   <option value=''>Tất cả Khoa</option>
				   <?php $sqlstr="select distinct b.ma_khoa, k.ten_khoa
								from hoc_vien h, nganh n , bo_mon b, khoa k
								where h.khoa >= 2005 
								and h.ma_nganh = n.ma_nganh
								and n.ma_bo_mon = b.ma_bo_mon (+)
								and b.ma_khoa = k.ma_khoa
								order by ten_khoa"; 
					$stmt = oci_parse($db_conn, $sqlstr);
					oci_execute($stmt);
					$n = oci_fetch_all($stmt, $resDM);
					oci_free_statement($stmt);
					
					for ($i = 0; $i < $n; $i++)
					{
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
			<td ></td> <td align=left colspan=3><a id="phong_dshocvienTN_btn_printpreview" style='font-size:80%'>&nbsp;Xem bản In</a> 
			<a id="phong_dshocvienTN_btn_downloadpreview" style='font-size:80%'>&nbsp;Tải về DS HV</a></td>
		</tr>
	</table>

	<div style='clear:both;'></div>
	<div id="phong_dshocvienTN_detail" align=center></div>
</div> <!-- end  -->


<script type="text/javascript">

 function phong_loadDSHocVienTN(pDot, pMaKhoa)
 {
	$("#phong_dshocvienTN_detail").html("<img border='0' src='images/ajax-loader.gif'/>");
	$("#phong_dshocvienTN_btn_printpreview,#phong_dshocvienTN_btn_downloadpreview" ).button("disable");
	
	dataString = 'a=dshocvien' 
	+ '&d='+ pDot 
	+ '&khoa=' + pMaKhoa 
	+ "&hisid=<?php echo $_REQUEST["hisid"];?>";
	$.post("phong/phong_dshocvien_tn_process.php", dataString ,
		function(data){
			$("#phong_dshocvienTN_detail").html(data);
			$("#phong_dshocvienTN_btn_printpreview,#phong_dshocvienTN_btn_downloadpreview" ).button("enable");
	}, "html");
 }
 
 function phong_loadDSHocVienTNFile()
 {
	dataString = 'a=dshocvienfile' 
	+ '&d='+ $('#phong_txtDotDSHocVienTN').val() 
	+ '&khoa=' + $("#phong_txtMaKhoaDSHocVienTN").val() 
	+ "&hisid=<?php echo $_REQUEST["hisid"];?>";
	
	$.post("phong/phong_dshocvien_tn_process.php", dataString ,
		function(data){
			if (data.url!='') {
				gv_open_msg_box("<p><table><tr><td style='width:200px; font-weight:bold'>Danh sách học viên Tốt nghiệp đợt: " + $('#phong_txtDotDSHocVienTN').val() + "<br/>Khoa " + $("#phong_txtMaKhoaDSHocVienTN option:selected").html() + "</td><td style='width:80px'><a style='color:blue;font-weight:bold;' target='_blank' href='"+data.url+"'><u><span class='ui-icon ui-icon-disk' style='float:left; margin:0 5px 0 0;'></span> tải về</u></a></td></tr></table></p>", '', 300, 170);
			}
	}, "json");
 }
 
$(function(){
 $("#phong_dshocvienTN_btn_printpreview" ).button({ icons: {primary:'ui-icon ui-icon-print'} });
 $("#phong_dshocvienTN_btn_downloadpreview").button({ icons: {primary:'ui-icon ui-icon-disk'} });
 
 $("#phong_dshocvienTN_btn_printpreview").click(function(){
	writeConsole("<div style='font-size:13px; font-weight:bold;' align=left>ĐẠI HỌC BÁCH KHOA - TP.HCM<br/>PHÒNG ĐÀO TẠO SAU ĐẠI HỌC<br/> &nbsp;</div>" + $("#phong_dshocvienTN_detail").html() + "<div style='clear:both;'>&nbsp;<br/><a href='JavaScript:window.print();'>In trang này</a></div>", 800,600);
 });
 
 $("#phong_dshocvienTN_btn_downloadpreview").click(function(){
	phong_loadDSHocVienTNFile();
 });
 
 phong_loadDSHocVienTN($("#phong_txtDotDSHocVienTN").val(), $("#phong_txtMaKhoaDSHocVienTN").val());
 
 $("#phong_txtDotDSHocVienTN, #phong_txtMaKhoaDSHocVienTN").change(function(e) {
	phong_loadDSHocVienTN($("#phong_txtDotDSHocVienTN").val(), $("#phong_txtMaKhoaDSHocVienTN").val());
 });


});
</script>

<?php 
if (isset ($db_conn))
	oci_close($db_conn);
?>