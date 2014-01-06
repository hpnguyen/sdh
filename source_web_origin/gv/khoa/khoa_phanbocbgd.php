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
if (!allowPermisstion(base64_decode($_SESSION['uidloginPortal']), '112', $db_conn)){
	die('Truy cập bất hợp pháp'); 
}

$macb = $_SESSION['macb'];
?>

<div id="khoa_phanbocbgd_tkb" >

	<table  border="0" cellspacing="0" cellpadding="5" align=left>
		<tr>
			<td align=right style="width:60px;font-weight:bold"><label for='khoa_phanbocbgd_hk'>Chọn HK</label></td>	
			<td align=left style="width:100px;">
				<select name="khoa_phanbocbgd_hk" id="khoa_phanbocbgd_hk" style="font-size:15px;">
				   <?php $sqlstr="select (hoc_ky || '/' || nam_hoc_tu || '-' || nam_hoc_den) nam_hoc, to_char(dot_hoc,'dd-mm-yyyy') dot_hoc
								from dot_hoc_nam_hoc_ky
								where dot_hoc in (select distinct dot_hoc from thoi_khoa_bieu)
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
			<td style="width:50px;"></td><td><a id="khoa_phanbocbgd_btn_printpreview" style='font-size:80%'>&nbsp;Xem bản In</a></td>
		</tr>
	</table>
	
	<div style='clear:both;'></div>
	<div id="khoa_phanbocbgd_tkb_detail" align=center></div>
	
	<div align='center' style='font-size: 0.8em;'>
		<div style='margin-right:0px; margin-top:20px; float:right;'>
			<div align='center' style='margin-bottom:5px;'><strong>Tối</strong></div>
			<div style='margin-right:10px; float:right;'>
				<font color=red>Tiết 14: 18:15</font> - 19:00<br/>
				Tiết 15: 19:00 - 19:45<br/>
				Tiết 16: 19:55 - 20:40
			</div>
		</div>
		<div style='margin-right:10px; margin-top:20px; float:right;  border-right: 1px solid #96c716;'>
			<div align='center' style='margin-bottom:5px;'><strong>Chiều</strong></div>
			<div style='margin-right:20px; float:left;'>
				<font color=red>Tiết 7: 12:30</font> - 13:15<br/>
				Tiết 8: 13:15 - 14:00<br/>
				Tiết 9: 14:10 - 14:55
			</div>
			<div style='margin-right:10px; float:right;'>
				Tiết 10: 15:05 - 15:50<br/>
				Tiết 11: 16:00 - 16:45<br/>
				Tiết 12: 16:45 - 17:30
			</div>
		</div>
		<div style='margin-right:10px; margin-top:20px; float:right;  border-right: 1px solid #96c716;'>
			<div align='center' style='margin-bottom:5px;'><strong>Sáng</strong></div>
			<div style='margin-right:20px; float:left ;'>
				<font color=red>Tiết 1: 06:30</font> - 07:15<br/>
				Tiết 2: 07:15 - 08:00<br/>
				Tiết 3: 08:10 - 08:55
			</div>
			<div style='margin-right:10px; float:right;'>
				Tiết 4: 09:05 - 09:50<br/>
				Tiết 5: 10:00 - 10:45<br/>
				Tiết 6: 10:45 - 11:30
			</div>
		</div>
	</div>
	
</div> <!-- end  -->

<script type="text/javascript">
$(function(){
 
 $( "#khoa_phanbocbgd_btn_printpreview" ).button({ icons: {primary:'ui-icon ui-icon-print'} });
 
 khoa_loadTKB($("#khoa_phanbocbgd_hk").val());
 
 $('#ngaybatdauhk').text(' ' + $("#khoa_phanbocbgd_hk").val() + ' (tuần 1)');
 
 $("#khoa_phanbocbgd_hk").change(function(e) {
	khoa_loadTKB($("#khoa_phanbocbgd_hk").val());
 });
 
 $("#khoa_phanbocbgd_btn_printpreview").click(function(){
		myURL = $('#printPageURL').val();
		var hk = encodeURIComponent($('#phan-bo-head').text());
		var nbd = encodeURIComponent($('#ngaybatdauhk').text());
		myURL = myURL + '&hk=' + hk + '&nbd=' + nbd;
		window.open(myURL, '_blank', 'location=yes,height=450,width=1024,scrollbars=yes,status=yes');
 });
 
 function khoa_loadTKB(p_dothoc)
 {
	$("#khoa_phanbocbgd_tkb_detail").html("<img border='0' src='images/ajax-loader.gif'/>");
	$( "#khoa_phanbocbgd_btn_printpreview" ).button( "disable" );
	xreq = $.ajax({
	  type: 'POST', dataType: "html",
	  url: 'khoa/khoa_phanbocbgd_process.php?'
	  + '&d=' + p_dothoc
	  + '&h=' + encodeURIComponent($("#khoa_phanbocbgd_hk option:selected").html())
	  + '&hisid=<?php echo $_REQUEST["hisid"]; ?>',
	  success: function(data) {
		$("#khoa_phanbocbgd_tkb_detail").html(data);
		$('#ngaybatdauhk').text(' ' + $("#khoa_phanbocbgd_hk").val() + ' (tuần 1)');
		$( "#khoa_phanbocbgd_btn_printpreview" ).button( "enable" );
	  },
	  error: function(xhr, ajaxOptions, thrownError) {
		$("#khoa_phanbocbgd_tkb_detail").html(thrownError);
		$( "#khoa_phanbocbgd_btn_printpreview" ).button( "disable" );
	  }
	});
 }

});
</script>



<?php 
if (isset ($db_conn))
	oci_close($db_conn);
?>