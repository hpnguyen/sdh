<?php
if (isset($_REQUEST["hisid"])){
	session_id($_REQUEST["hisid"]);
	session_start();
}
if (!isset($_SESSION['uidloginhv'])){
	die('Truy cập bất hợp pháp'); 
}

include "libs/connect.php";

$mahv = base64_decode($_SESSION["mahv"]);
?>

<div align="left" style="margin:0 auto;">
<form id="form_tkbnganh" name="form_tkbnganh" method="post" action="">
   <table width="100%" border="0" cellspacing="0" cellpadding="5" align=center>
   
	<tr>
	  <td align=left style="width:50px;">
		<span class="heading">
		<label for="kqdhp_txtHK">Học kỳ</label>
		</span>
	  </td>
	  <td align=left>
		<select id=kqdhp_txtHK name=kqdhp_txtHK style="font-size:15px;" onChange="kqdhp_updateKQDHP(this.value);">
			<?php
			$sqlstr="select d.hoc_ky||'/'||d.nam_hoc_tu||'-'||d.nam_hoc_den HOC_KY, d.dot_hoc
							from dot_hoc_nam_hoc_ky d
							where d.dot_hoc in (SELECT dot_hoc from dang_ky_mon_hoc where ma_hoc_vien='$mahv')
							order by dot_hoc desc";
			$stmt = oci_parse($db_conn, $sqlstr);
			oci_execute($stmt);
			$n = oci_fetch_all($stmt, $resDM);
			oci_free_statement($stmt);

			for ($i = 0; $i < $n; $i++)
			{
				echo "<option value='".$resDM["DOT_HOC"][$i]."'>" .$resDM["HOC_KY"][$i]. "</option>";
			}
			?>
		</select>
	  </td>
	</tr>
	<tr>
	  <td align=right>
	  </td>
	  <td align=left style='font-size:80%'>
		<a id="kqdhp_btn_printpreview">&nbsp;Xem bản In</a>
	  </td>
	</tr>
   </table>

   <div id="kqdhp_chitiet" style="margin-top:5px;" align=center>
   </div>

   
	
</form>
</div>

<script type="text/javascript">
function kqdhp_updateKQDHP(p_dothoc)
{
	$("#kqdhp_chitiet").html("<img border='0' src='images/ajax-loader.gif'/>");
	$( "#kqdhp_btn_printpreview" ).button( "disable" );
	
	xreq = $.ajax({
	  type: 'POST', dataType: "html",
	  url: 'hv_kqdonghp_process.php?w=dothoc-kqdonghp'
	  + '&d=' + p_dothoc
	  + '&h=' + encodeURIComponent($("#kqdhp_txtHK option:selected").html())
	  + '&hisid=<?php echo $_REQUEST["hisid"]; ?>',
	  success: function(data) {
		$("#kqdhp_chitiet").html(data);
		$( "#kqdhp_btn_printpreview" ).button( "enable" );
	  },
	  error: function(xhr, ajaxOptions, thrownError) {
		$( "#kqdhp_btn_printpreview" ).button( "disable" );
		$("#kqdhp_chitiet").html(thrownError);
	  }
	});
}

function formatCurrency(num)
 {
	num = num.toString().replace(/\$|\,/g,'');
	if(isNaN(num))
	num = "0";
	sign = (num == (num = Math.abs(num)));
	num = Math.floor(num*100+0.50000000001);
	num = Math.floor(num/100).toString();
	for (var i = 0; i < Math.floor((num.length-(1+i))/3); i++)
	num = num.substring(0,num.length-(4*i+3))+','+
	num.substring(num.length-(4*i+3));
	return (((sign)?'':'-') + num);
}

$(function() {
	$( "#kqdhp_btn_printpreview" ).button({ icons: {primary:'ui-icon ui-icon-print'} });
	kqdhp_updateKQDHP($("#kqdhp_txtHK").val());

	$("#kqdhp_btn_printpreview").click(function(){
		writeConsole("<div style='font-size:13px; font-weight:bold;' align=left>ĐẠI HỌC BÁCH KHOA - TP.HCM<br/>PHÒNG ĐÀO TẠO SAU ĐẠI HỌC<br/> &nbsp;</div>" + $("#kqdhp_chitiet").html() + "<div style='clear:both;'>&nbsp;<br/><a href='JavaScript:window.print();'>In trang này</a></div>", 800, 450);
	});	// end $("#kqdhp_btn_printpreview")
});
</script>

<?php 
if (isset ($db_conn))
	oci_close($db_conn);
?>