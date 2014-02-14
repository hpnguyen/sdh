<div id="khoa_phanbocbgd_tkb" >

	<table  border="0" cellspacing="0" cellpadding="5" align=left>
		<tr>
			<td align="left">
				<select name="<?php echo $formKey ?>_select_dot_hoc" id="<?php echo $formKey ?>_select_dot_hoc" style="font-size:15px;">
				<?php 
					foreach ($listDotHoc as $item) {
						echo "<option value='".$item["dot_hoc"]."'".($dothoc == $item["dot_hoc"] ? " selected=\"selected\"": '').">" .$item["nam_hoc"]. "</option>";
					}
				?>
				</select>
			</td>
		</tr>
		<tr>
			<td align="left"><a id="<?php echo $formKey ?>_btn_printpreview" style='font-size:80%'>&nbsp;Xem bản In</a></td>
		</tr>
	</table>
	
	<div style='clear:both;'></div>
	<div id="<?php echo $formKey ?>_detail" align="center"></div>
</div> <!-- end  -->

<script type="text/javascript">
$(document).ready(function() {
	$( "#<?php echo $formKey ?>_btn_printpreview" ).button({ icons: {primary:'ui-icon ui-icon-print'} });
	 
	 <?php echo $formKey ?>_loadList($("#<?php echo $formKey ?>_select_dot_hoc").val());
	 
	 $("#<?php echo $formKey ?>_select_dot_hoc").change(function(e) {
		<?php echo $formKey ?>_loadList($("#<?php echo $formKey ?>_select_dot_hoc").val());
	 });
	 
	 $("#<?php echo $formKey ?>_btn_printpreview").click(function(){
			myURL = $('#<?php echo $formKey ?>printPageURL').val();
			var hk = encodeURIComponent($('#phan-bo-head').text());
			var nbd = encodeURIComponent($('#ngaybatdauhk').text());
			var d = encodeURIComponent($("#<?php echo $formKey ?>_select_dot_hoc").val());
			myURL = myURL + '&hk=' + hk + '&nbd=' + nbd + '&d=' + d<?php echo ($nganh == true ? " + '&nganh=1'" : "") ?>;
			
			window.open(myURL, '_blank', 'location=yes,height=450,width=1024,scrollbars=yes,status=yes');
	 });
	 
	 function <?php echo $formKey ?>_loadList(p_dothoc)
	 {
	 	$("#<?php echo $formKey ?>_detail").html("<img border='0' src='images/ajax-loader.gif'/>");
		$( "#<?php echo $formKey ?>_btn_printpreview" ).button( "disable" );
		xreq = $.ajax({
		  type: 'POST', dataType: "html",
		  url: 'front.php/index/index/dsdecuong?'
		  + 'd=' + p_dothoc
		  + '&h=' + encodeURIComponent($("#<?php echo $formKey ?>_select_dot_hoc option:selected").html())
		  + '&hisid=<?php echo $_REQUEST["hisid"]; ?><?php echo ($nganh == true ? "&nganh=1" : "") ?>',
		  success: function(data) {
			$("#<?php echo $formKey ?>_detail").html(data);
			$( "#<?php echo $formKey ?>_btn_printpreview" ).button( "enable" );
		  },
		  error: function(xhr, ajaxOptions, thrownError) {
			$("#<?php echo $formKey ?>_detail").html(thrownError);
			$( "#<?php echo $formKey ?>_btn_printpreview" ).button( "disable" );
		  }
		});
	 }
});

</script>
