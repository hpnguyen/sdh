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

    <a id="tracuudiem_btn_printpreview" style='font-size:80%'>&nbsp;Xem bản In</a>
   <div id="tracuudiem_chitiet" style="margin-top:5px;" align=center>
   </div>

</div>

<script type="text/javascript">
	
function tracuudiem_updateDiem(p_mahv)
{
	$("#tracuudiem_chitiet").html("<img border='0' src='images/ajax-loader.gif'/>");
	$( "#tracuudiem_btn_printpreview" ).button( "disable" );
	
	xreq = $.ajax({
	  type: 'POST', dataType: "html",
	  url: 'hv_tracuudiem_process.php?w=hv-diem'
	  + '&hisid=<?php echo $_REQUEST["hisid"]; ?>',
	  success: function(data) {
		$("#tracuudiem_chitiet").html(data);
		$( "#tracuudiem_btn_printpreview" ).button( "enable" );
	  },
	  error: function(xhr, ajaxOptions, thrownError) {
		$( "#tracuudiem_btn_printpreview" ).button( "disable" );
		$("#tracuudiem_chitiet").html(thrownError);
	  }
	});
}

$(function() {
	$( "#tracuudiem_btn_printpreview" ).button({ icons: {primary:'ui-icon ui-icon-print'} });
	tracuudiem_updateDiem();

	$("#tracuudiem_btn_printpreview").click(function(){
		writeConsole("<div style='font-size:13px; font-weight:bold;' align=left>ĐẠI HỌC BÁCH KHOA - TP.HCM<br/>PHÒNG ĐÀO TẠO SAU ĐẠI HỌC<br/> &nbsp;</div>" + $("#tracuudiem_chitiet").html() + "<div style='clear:both;'>&nbsp;<br/><a href='JavaScript:window.print();'>In trang này</a></div>", 800, 450);
	});	// end $("#lichthi_canhan_btn_printpreview")
});
</script>

<?php 
if (isset ($db_conn))
	oci_close($db_conn);
?>