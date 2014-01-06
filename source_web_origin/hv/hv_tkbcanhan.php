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
		<label for="tkb_canhan_txtHK">Học kỳ</label>
		</span>
	  </td>
	  <td align=left>
		<select id=tkb_canhan_txtHK name=tkb_canhan_txtHK style="font-size:15px;" onChange="tkb_canhan_updateTKB(this.value);">
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
		<a id="tkb_canhan_btn_printpreview" name="taosach">&nbsp;Xem bản In</a>
	  </td>
	</tr>
   </table>

   <div id="tkb_canhan_chitiet" style="margin-top:5px;" align=center>
   </div>
	
</form>
</div>

<script type="text/javascript">
function tkb_canhan_writeConsole(content) {
	top.consoleRef=window.open('','myconsole',
	'width=800,height=450'
	+',menubar=0'
	+',toolbar=0'
	+',status=0'
	+',scrollbars=1'
	+',resizable=1')
	top.consoleRef.document.writeln(
	'<html><head><title>Phong Dao Tao SDH - DHBK</title></head>'
	+'<body bgcolor=white onLoad="self.focus()">'
	+content
	+'</body></html>'
	)
	top.consoleRef.document.close()
}
		
function tkb_canhan_updateTKB(p_dothoc)
{
	$("#tkb_canhan_chitiet").html("<img border='0' src='images/ajax-loader.gif'/>");
	$( "#tkb_canhan_btn_printpreview" ).button( "disable" );
	
	xreq = $.ajax({
	  type: 'POST', dataType: "html",
	  url: 'hv_tkbcanhan_process.php?w=dothoc-tkb_canhan'
	  + '&d=' + p_dothoc
	  + '&h=' + encodeURIComponent($("#tkb_canhan_txtHK option:selected").html())
	  + '&hisid=<?php echo $_REQUEST["hisid"]; ?>',
	  success: function(data) {
		$("#tkb_canhan_chitiet").html(data);
		$( "#tkb_canhan_btn_printpreview" ).button( "enable" );
	  },
	  error: function(xhr, ajaxOptions, thrownError) {
		$( "#tkb_canhan_btn_printpreview" ).button( "disable" );
		$("#tkb_canhan_chitiet").html(thrownError);
	  }
	});
}

$(function() {
	$( "#tkb_canhan_btn_printpreview" ).button({ icons: {primary:'ui-icon ui-icon-print'} });
	tkb_canhan_updateTKB($("#tkb_canhan_txtHK").val());

	$("#tkb_canhan_btn_printpreview").click(function(){
		writeConsole("<div style='font-size:13px; font-weight:bold;' align=left>ĐẠI HỌC BÁCH KHOA - TP.HCM<br/>PHÒNG ĐÀO TẠO SAU ĐẠI HỌC<br/> &nbsp;</div>" + $("#tkb_canhan_chitiet").html() + "<div style='clear:both;'>&nbsp;<br/><a href='JavaScript:window.print();'>In trang này</a></div>", 800, 450);
	});	// end $("#tkb_canhan_btn_printpreview")
});
</script>

<?php 
if (isset ($db_conn))
	oci_close($db_conn);
?>