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
if (!allowPermisstion(base64_decode($_SESSION['uidloginPortal']), '020', $db_conn))
{
	die('Truy cập bất hợp pháp'); 
}

$makhoa = base64_decode($_SESSION['makhoa']);
?>

<div id="phong_dsNCSkhoa" >
	<table  border="0" cellspacing="0" cellpadding="5" align=left>
		<tr>
			<td align=right style="width:80px;font-weight:bold"><label for='phong_txtKhoaDS_NCS_Khoa'>Chọn Khóa</label></td>	
			<td align=left style="width:50px;">
				<select name="phong_txtKhoaDS_NCS_Khoa" id="phong_txtKhoaDS_NCS_Khoa" style="font-size:15px" onChange="phong_dsncs_updateKhoa(this.value);">
				   <?php $sqlstr="select distinct khoa
								from hoc_vien
								where ma_bac = 'TS'
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
			<td align=right style="width:40px;font-weight:bold"><label for='phong_txtMaKhoaDS_NCS_Khoa'>Khoa</label></td>	
			<td align=left> 
				<select name="phong_txtMaKhoaDS_NCS_Khoa" id="phong_txtMaKhoaDS_NCS_Khoa" style="font-size:15px" onChange="phong_loadDS_NCS_Khoa($('#phong_txtKhoaDS_NCS_Khoa').val(), this.value);"></select> 
			</td>
		</tr>
		<tr>
			<td ></td> <td align=left colspan=3><a id="phong_dsncskhoa_btn_printpreview" style='font-size:80%'>&nbsp;Xem bản In</a> 
			<a id="phong_dsNCSkhoa_btn_downloadpreview" style='font-size:80%' data-placement="bottom" class="tooltips" title='DS này có thêm các thông tin: Tổng chỉ tích lũy, Điểm CĐ 1, 2, 3, Điểm CĐ Tổng quan, KQ Bảo vệ HĐ Khoa (CS), KQ Bảo vệ HĐ Trường (NN)'>&nbsp;Tải về DS NCS</a></td>
		</tr>
	</table>

	<div style='clear:both;'></div>
	<div id="phong_dsNCSkhoa_detail" align=center></div>
</div> <!-- end  -->


<script type="text/javascript">

function phong_dsncs_updateKhoa(p_khoa)
{
	$("#phong_dsNCSkhoa_detail").html("<div align=center><img border='0' src='images/ajax-loader.gif'/></div>");
	$( "#phong_dsncskhoa_btn_printpreview" ).button( "disable" );
	$( "#phong_dsNCSkhoa_btn_downloadpreview" ).button( "disable" );
	
	
	dataString = 'a=khoa-khoa&hisid=<?php echo $_REQUEST["hisid"]; ?>' + '&k=' + p_khoa;
	xreq = $.ajax({
	  type: 'POST', dataType: "html",
	  url: 'phong/phong_dsncs_khoaprocess.php?',
	  data: dataString,
	  success: function(data) {
		$("#phong_txtMaKhoaDS_NCS_Khoa").html(data);
		phong_loadDS_NCS_Khoa(p_khoa, $("#phong_txtMaKhoaDS_NCS_Khoa").val());
	  },
	  error: function(xhr, ajaxOptions, thrownError) {
		
	  }
	});
}

 function phong_loadDS_NCS_Khoa(pKhoa, pMaKhoa)
 {
	$("#phong_dsNCSkhoa_detail").html("<img border='0' src='images/ajax-loader.gif'/>");
	$("#phong_dsncskhoa_btn_printpreview,#phong_dsNCSkhoa_btn_downloadpreview" ).button("disable");
	
	dataString = 'a=dsncs' + '&k='+ pKhoa + '&khoa=' + pMaKhoa + "&hisid=<?php echo $_REQUEST["hisid"];?>";
	$.post("phong/phong_dsncs_khoaprocess.php", dataString ,
		function(data){
			$("#phong_dsNCSkhoa_detail").html(data);
			$("#phong_dsncskhoa_btn_printpreview,#phong_dsNCSkhoa_btn_downloadpreview" ).button("enable");
	}, "html");
 }
 
 function phong_loadDS_NCS_KhoaFile()
 {
	dataString = 'a=dsncsfile' + '&k='+ $('#phong_txtKhoaDS_NCS_Khoa').val() + '&khoa=' + $("#phong_txtMaKhoaDS_NCS_Khoa").val() + "&hisid=<?php echo $_REQUEST["hisid"];?>";
	
	$.post("phong/phong_dsncs_khoaprocess.php", dataString ,
		function(data){
			if (data.url!='') {
				gv_open_msg_box("<p><table><tr><td style='width:200px; font-weight:bold'>Danh sách NCS khóa: " + $('#phong_txtKhoaDS_NCS_Khoa').val() + "<br/>Khoa " + $("#phong_txtMaKhoaDS_NCS_Khoa option:selected").html() + "</td><td style='width:80px'><a style='color:blue;font-weight:bold;' target='_blank' href='"+data.url+"'><u><span class='ui-icon ui-icon-disk' style='float:left; margin:0 5px 0 0;'></span> tải về</u></a></td></tr></table></p>", '', 300, 150);
			}
		
	}, "json");
 }
 
$(function(){
 $(".tooltips").tooltip();
 $("#phong_dsncskhoa_btn_printpreview" ).button({ icons: {primary:'ui-icon ui-icon-print'} });
 $("#phong_dsNCSkhoa_btn_downloadpreview").button({ icons: {primary:'ui-icon ui-icon-disk'} });
 
 $("#phong_dsncskhoa_btn_printpreview").click(function(){
	writeConsole("<div style='font-size:13px; font-weight:bold;' align=left>ĐẠI HỌC BÁCH KHOA - TP.HCM<br/>PHÒNG ĐÀO TẠO SAU ĐẠI HỌC<br/> &nbsp;</div>" + $("#phong_dsNCSkhoa_detail").html() + "<div style='clear:both;'>&nbsp;<br/><a href='JavaScript:window.print();'>In trang này</a></div>", 800,600);
 });
 
 $("#phong_dsNCSkhoa_btn_downloadpreview").click(function(){
	phong_loadDS_NCS_KhoaFile();
 });
 
 phong_dsncs_updateKhoa($("#phong_txtKhoaDS_NCS_Khoa").val());
});
</script>



<?php 
if (isset ($db_conn))
	oci_close($db_conn);
?>