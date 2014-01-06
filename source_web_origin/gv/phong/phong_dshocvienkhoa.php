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
if (!allowPermisstion(base64_decode($_SESSION['uidloginPortal']), '015', $db_conn))
{
	die('Truy cập bất hợp pháp'); 
}

$makhoa = base64_decode($_SESSION['makhoa']);
?>

<div id="phong_dshocvienkhoa" >
	<table  border="0" cellspacing="0" cellpadding="5" align=left>
		<tr>
			<td align=right style="width:80px;font-weight:bold"><label for='phong_txtKhoaDSHocVienKhoa'>Chọn Khóa</label></td>	
			<td align=left style="width:50px;">
				<select name="phong_txtKhoaDSHocVienKhoa" id="phong_txtKhoaDSHocVienKhoa" style="font-size:15px">
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
			<td align=right style="width:40px;font-weight:bold"><label for='phong_txtMaKhoaDSHocVienKhoa'>Khoa</label></td>	
			<td align=left> 
				<select name="phong_txtMaKhoaDSHocVienKhoa" id="phong_txtMaKhoaDSHocVienKhoa" style="font-size:15px">
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
						echo "<option value='".$resDM["MA_KHOA"][$i]."'>" .$resDM["TEN_KHOA"][$i]. "</option>";
					}
					
				  ?>
				</select> 
			</td>
		</tr>
		<tr>
			<td ></td> <td align=left colspan=3><a id="phong_dshocvienkhoa_btn_printpreview" style='font-size:80%'>&nbsp;Xem bản In</a> 
			<a id="phong_dshocvienkhoa_btn_downloadpreview" style='font-size:80%' data-placement="bottom" class="tooltips" title='DS này có thêm các thông tin: Tổng chỉ tích lũy MH, Trung bình tích lũy MH, TB Toàn khóa, Tổng chỉ toàn khóa, Đợt cấp bằng, Thuộc CTĐT'>&nbsp;Tải về DS HV</a></td>
		</tr>
	</table>

	<div style='clear:both;'></div>
	<div id="phong_dshocvienkhoa_detail" align=center></div>
</div> <!-- end  -->


<script type="text/javascript">

 function phong_loadDSHocVienKhoa(pKhoa, pMaKhoa)
 {
	$("#phong_dshocvienkhoa_detail").html("<img border='0' src='images/ajax-loader.gif'/>");
	$("#phong_dshocvienkhoa_btn_printpreview,#phong_dshocvienkhoa_btn_downloadpreview" ).button("disable");
	
	dataString = 'act=dshocvien' + '&k='+ pKhoa + '&khoa=' + pMaKhoa + "&hisid=<?php echo $_REQUEST["hisid"];?>";
	$.post("phong/phong_dshocvienkhoaprocess.php", dataString ,
		function(data){
			$("#phong_dshocvienkhoa_detail").html(data);
			$("#phong_dshocvienkhoa_btn_printpreview,#phong_dshocvienkhoa_btn_downloadpreview" ).button("enable");
	}, "html");
 }
 
 function phong_loadDSHocVienKhoaFile()
 {
	dataString = 'act=dshocvienfile' + '&k='+ $('#phong_txtKhoaDSHocVienKhoa').val() + '&khoa=' + $("#phong_txtMaKhoaDSHocVienKhoa").val() + "&hisid=<?php echo $_REQUEST["hisid"];?>";
	
	$.post("phong/phong_dshocvienkhoaprocess.php", dataString ,
		function(data){
			if (data.url!='') {
				gv_open_msg_box("<p><table><tr><td style='width:200px; font-weight:bold'>Danh sách học viên khóa: " + $('#phong_txtKhoaDSHocVienKhoa').val() + "<br/>Khoa " + $("#phong_txtMaKhoaDSHocVienKhoa option:selected").html() + "</td><td style='width:80px'><a style='color:blue;font-weight:bold;' target='_blank' href='"+data.url+"'><u><span class='ui-icon ui-icon-disk' style='float:left; margin:0 5px 0 0;'></span> tải về</u></a></td></tr></table></p>", '', 300, 150);
			}
		
	}, "json");
 }
 
$(function(){
 $(".tooltips").tooltip();
 $("#phong_dshocvienkhoa_btn_printpreview" ).button({ icons: {primary:'ui-icon ui-icon-print'} });
 $("#phong_dshocvienkhoa_btn_downloadpreview").button({ icons: {primary:'ui-icon ui-icon-disk'} });
 
 $("#phong_dshocvienkhoa_btn_printpreview").click(function(){
	writeConsole("<div style='font-size:13px; font-weight:bold;' align=left>ĐẠI HỌC BÁCH KHOA - TP.HCM<br/>PHÒNG ĐÀO TẠO SAU ĐẠI HỌC<br/> &nbsp;</div>" + $("#phong_dshocvienkhoa_detail").html() + "<div style='clear:both;'>&nbsp;<br/><a href='JavaScript:window.print();'>In trang này</a></div>", 800,600);
 });
 
 $("#phong_dshocvienkhoa_btn_downloadpreview").click(function(){
	phong_loadDSHocVienKhoaFile();
 });
 
 phong_loadDSHocVienKhoa($("#phong_txtKhoaDSHocVienKhoa").val(), $("#phong_txtMaKhoaDSHocVienKhoa").val());
 
 $("#phong_txtKhoaDSHocVienKhoa, #phong_txtMaKhoaDSHocVienKhoa").change(function(e) {
	phong_loadDSHocVienKhoa($("#phong_txtKhoaDSHocVienKhoa").val(), $("#phong_txtMaKhoaDSHocVienKhoa").val());
 });


});
</script>



<?php 
if (isset ($db_conn))
	oci_close($db_conn);
?>