<style type="text/css">
	.<?php echo $formKey ?>phan_bien_title {
		font-size: 15px;
		font-weight: bold;
	}
	.<?php echo $formKey ?>phan_bien_top_title {
		padding: 10px 0 15px 0;
	}
</style>
<div id="<?php echo $formKey ?>_main_content_div" >
	<div align="center" class="<?php echo $formKey ?>phan_bien_top_title">
		<span class="<?php echo $formKey ?>phan_bien_title">Danh sách Đề tài phản biện năm </span>
		<select name="<?php echo $formKey ?>_select_dot_hoc" id="<?php echo $formKey ?>_select_dot_hoc" style="font-size:15px;">
		<?php 
			foreach ($listYear as $item) {
				echo "<option value='".$item["t_year"]."'".($item["selected"] != null ? " selected=\"selected\"": '').">" .($item["t_year"]+1). "</option>";
			}
		?>
		</select>
	</div>
	<div class="<?php echo $formKey ?>list_limit_times">
		<?php
		foreach ($listLimitTimes as $time) {
			if ((int) $time['het_han_phan_bien'] == 0) { ?>
			<div align="center" style="margin:5px 0 5px 0;font-size:12px">
			Đề tài cấp <b><?php echo $time['ten_cap'] ?></b> có thời gian phản biện 
			từ ngày <font color="red"><b><?php echo $time['t_pbdt_ngay_bd']; ?></b></font> 
			đến <font color="red"><b><?php echo $time['t_pbdt_ngay_kt']; ?></b></font>
			</div>
			<?php }else{ ?>
			<div align="center" style="margin:5px 0 5px 0;font-size:12px">
			Đề tài cấp <b><?php echo $time['ten_cap'] ?></b> đã hết hạn phản biện từ 
			<font color="red"><b><?php echo $time['t_pbdt_ngay_kt']; ?></b></font>
			</div>
			<?php
			}
		}
		?>
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