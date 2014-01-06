<?php
//if (!isset($_SESSION['uidloginhv'])){
//	die('Truy cập bất hợp pháp'); 
//}

include "libs/connect.php";

?>

<div align="left" style="margin:0 auto;">
<form id="form_tracuuCTDT" name="form_tracuuCTDT" method="post" action="">
   <table width="100%" border="0" cellspacing="0" cellpadding="5" align=center>
   
	<tr>
	
	  <td align=right style='width:50px'>
		<span class="heading">
		<label for="ctdt_txtDothoc">Đợt học</label>
		</span>
	  </td>
	  <td align=left style="width:50px;">
		<select id=ctdt_txtDothoc name=ctdt_txtDothoc style="font-size:15px;" onChange="ctdt_updateNganh(this.value);">
			<?php
			$sqlstr="select distinct dot_hoc, to_char(dot_hoc, 'dd/mm/yyyy') dot_format, 
							count(distinct ma_nganh) so_nganh
					from ctdt_chuyen_doi
					group by dot_hoc
					order by dot_hoc desc";
			$stmt = oci_parse($db_conn, $sqlstr);
			oci_execute($stmt);
			$n = oci_fetch_all($stmt, $resDM);
			oci_free_statement($stmt);

			for ($i = 0; $i < $n; $i++)
			{
				echo "<option value='".$resDM["DOT_HOC"][$i]."'>" .$resDM["DOT_FORMAT"][$i]. " ({$resDM["SO_NGANH"][$i]} ngành)</option>";
			}
			?>
			
		</select>
	  </td>
	</tr>
	
	
	<tr>
	  <td align=right>
		<span class="heading">
			<label for="ctdt_txtNganh">Ngành</label>
		</span>
	  </td>
	  <td align=left>
	  
		<select id=ctdt_txtNganh name=ctdt_txtNganh style="width:400px;font-size:15px;" onChange="ctdt_updateCTDT($('#ctdt_txtDothoc').val(), this.value);">
			<option value="">Chọn ngành</option>
		</select>
	  </td>
	</tr>
	
	<tr>
	  <td align=right>
		
	  </td>
	  <td align=left colspan="3" style='font-size:80%'>
		<a id="ctdt_btn_printpreview" >&nbsp;Xem bản In</a>
	  </td>
	</tr>
   </table>

   <div id="ctdt_chitiet" style="margin-top:15px;" align=center></div>
</form>
</div>

<script type="text/javascript">
function ctdt_writeConsole(content) {
	top.consoleRef=window.open('','myconsole',
	'width=800,height=450'
	+',menubar=0'
	+',toolbar=0'
	+',status=0'
	+',scrollbars=1'
	+',resizable=1')
	top.consoleRef.document.writeln(
	'<html><head><title>Phong Dao Tao SDH - DHBK</title></head>'
	+'<link href="css/pgs.css" rel="stylesheet" type="text/css"/>'
	+'<body bgcolor=white onLoad="self.focus()">'
	+content
	+'</body></html>'
	)
	top.consoleRef.document.close()
}
		
function ctdt_updateNganh(p_dothoc)
{
	xreq = $.ajax({
	  type: 'POST', dataType: "html",
	  url: 'hv_tracuu_ctdt_cd_process.php?w=dothoc-nganh'
	  + '&d=' + p_dothoc
	  + '&hisid=<?php echo $_REQUEST["hisid"]; ?>',
	  success: function(data) {
		$("#ctdt_txtNganh").html(data);
		//ctdt_updateHK(p_khoa, $("#ctdt_txtNganh").val());
		ctdt_updateCTDT(p_dothoc, $("#ctdt_txtNganh").val());
	  },
	  error: function(xhr, ajaxOptions, thrownError) {
		
	  }
	});
}

function ctdt_updateCTDT(p_dothoc, p_nganh)
{
	// e=link : nhúng vào trang web khác
	$("#ctdt_chitiet").html("<img border='0' src='images/ajax-loader.gif'/>");
	$( "#ctdt_btn_printpreview" ).button( "disable" );
	xreq = $.ajax({
	  type: 'POST', dataType: "html",
	  url: 'hv_tracuu_ctdt_cd_process.php?w=dothoc_nganh-ctdt'
	  + '&d=' + p_dothoc 
	  + '&df=' + encodeURIComponent($("#ctdt_txtDothoc option:selected").html())
	  + '&n=' + p_nganh
	  + '&hisid=<?php echo $_REQUEST["hisid"]; ?>',
	  success: function(data) {
		$("#ctdt_chitiet").html(data);
		$("#ctdt_btn_printpreview" ).button( "enable" );
	  },
	  error: function(xhr, ajaxOptions, thrownError) {
		$("#ctdt_chitiet").html(thrownError);
	  }
	});
	
	if ($("#ctdt_chitiet").html()!='')
		$( "#ctdt_btn_printpreview" ).button( "enable" );
	else
		$( "#ctdt_btn_printpreview" ).button( "disable" );
	
}

$(function() {
	$( "#ctdt_btn_printpreview" ).button({ icons: {primary:'ui-icon ui-icon-print'} });
	ctdt_updateNganh($('#ctdt_txtDothoc').val());
	
	
	$("#ctdt_btn_printpreview").click(function(){
		ctdt_writeConsole("<div style='font-size:13px; font-weight:bold;' align=left>ĐẠI HỌC BÁCH KHOA - TP.HCM<br/>PHÒNG ĐÀO TẠO SAU ĐẠI HỌC<br/> &nbsp;</div>" + $("#ctdt_chitiet").html() + "<div style='clear:both;'>&nbsp;<br/><a href='JavaScript:window.print();' style='font-size:150%; color:blue;'>In trang này</a></div>");
	});	// end $("#ctdt_btn_printpreview")
});
</script>

<?php 
if (isset ($db_conn))
	oci_close($db_conn);
?>