<div id="khoa_phanbocbgd_tkb" >

	<table  border="0" cellspacing="0" cellpadding="5" align=left>
		<tr>
			<td align="left">
				<select name="khoa_phanbocbgd_hk" id="khoa_phanbocbgd_hk" style="font-size:15px;">
				<?php 
					foreach ($listMonHoc as $item) {
						echo "<option value='".$item["dot_hoc"]."'".($dothoc == $item["dot_hoc"] ? " selected=\"selected\"": '').">" .$item["nam_hoc"]. "</option>";
					}
				?>
				</select>
			</td>
		</tr>
		<tr>
			<td align="left"><a id="khoa_phanbocbgd_btn_printpreview" style='font-size:80%'>&nbsp;Xem bản In</a></td>
		</tr>
	</table>
	
	<div style='clear:both;'></div>
	<div id="khoa_phanbocbgd_tkb_detail" align="center"></div>
	
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
	  url: 'front.php/tkb/phanbo/list?'
	  + 'd=' + p_dothoc
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