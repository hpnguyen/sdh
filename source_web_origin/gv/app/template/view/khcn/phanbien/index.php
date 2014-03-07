<style type="text/css">
	.<?php echo $formKey ?>phan_bien_title {
		font-size: 15px;
		font-weight: bold;
	}
	.<?php echo $formKey ?>phan_bien_top_title {
		padding: 20px 0 25px 0;
	}
</style>
<div id="<?php echo $formKey ?>_main_content_div" >
	<div align="center" class="<?php echo $formKey ?>phan_bien_top_title">
		<span class="<?php echo $formKey ?>phan_bien_title">Danh sách đề tài </span>
		<select name="<?php echo $formKey ?>_select_dot_hoc" id="<?php echo $formKey ?>_select_dot_hoc" style="font-size:15px;">
		<?php 
			foreach ($listYear as $item) {
				echo "<option value='".$item["t_year"]."'".($item["selected"] != null ? " selected=\"selected\"": '').">" .$item["t_year"]. "</option>";
			}
		?>
		</select>
	</div>
	<div style='clear:both;'></div>
	<div id="<?php echo $formKey ?>_detail" align="center"></div>
</div> <!-- end  -->

<script type="text/javascript">
$(document).ready(function() {
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
		  url: 'front.php/khcn/phanbien/list?'
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