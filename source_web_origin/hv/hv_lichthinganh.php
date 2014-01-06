<?php
if (isset($_REQUEST["hisid"])){
	session_id($_REQUEST["hisid"]);
	session_start();
}
//if (!isset($_SESSION['uidloginhv'])){
//	die('Truy cập bất hợp pháp'); 
//}

include "libs/connect.php";

?>

<div align="left" style="margin:0 auto;">
<form id="form_lichthinganh" name="form_lichthinganh" method="post" action="">
   <table width="100%" border="0" cellspacing="0" cellpadding="5" align=center>
   
	<tr>
	
	  <td align=right style='width:50px'>
		<span class="heading">
		<label for="lichthi_nganh_txtHK">Học kỳ</label>
		</span>
	  </td>
	  <td align=left style="width:50px;">
		<select id=lichthi_nganh_txtHK name=lichthi_nganh_txtHK style="font-size:15px;" onChange="lichthi_nganh_updateKhoa(this.value);">
			<?php
			$sqlstr="	SELECT d.hoc_ky||'/'||d.nam_hoc_tu||'-'||d.nam_hoc_den HOC_KY, d.dot_hoc
						FROM dot_hoc_nam_hoc_ky d
						WHERE d.nam_hoc_tu>=2005
						AND dot_hoc IN (SELECT DISTINCT dot_hoc FROM lich_thi)
						ORDER BY dot_hoc DESC";
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
	  <td align=left style="width:50px;">
		<span class="heading">
		<label for="lichthi_nganh_txtKhoa">Khóa</label>
		</span>
		
	  </td>
	  <td align=left>
		<select id=lichthi_nganh_txtKhoa name=lichthi_nganh_txtKhoa onChange="lichthi_nganh_updateNganh(this.value, $('#lichthi_nganh_txtHK').val());" style="font-size:15px;">
		</select>
		
	  </td>
	</tr>
	
	
	<tr>
	  <td align=right>
		<span class="heading">
			<label for="tkn_nganh_txtNganh">Ngành</label>
		</span>
	  </td>
	  <td align=left colspan="3">
	  
		<select id=tkn_nganh_txtNganh name=tkn_nganh_txtNganh style="font-size:15px;" onChange="lichthi_nganh_updateLichThi($('#lichthi_nganh_txtKhoa').val(),$('#lichthi_nganh_txtHK').val(),this.value);">
			<option value="">Chọn ngành</option>
		</select>
	  </td>
	</tr>
	<tr>
	  <td align=right>
		
	  </td>
	  <td align=left colspan="3" style='font-size:80%'>
		<a id="lichthi_nganh_btn_printpreview" name="taosach">&nbsp;Xem bản In</a>
	  </td>
	</tr>
   </table>

   <div id="lichthi_nganh_chitiet" style="margin-top:5px;" align=center>
   </div>

   
	
</form>
</div>

<script type="text/javascript">
function lichthi_nganh_writeConsole(content) {
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
		
function lichthi_nganh_updateKhoa(p_dothoc)
{
	$("#lichthi_nganh_chitiet").html("<div align=center><img border='0' src='images/ajax-loader.gif'/></div>");
	$( "#lichthi_nganh_btn_printpreview" ).button( "disable" );
	xreq = $.ajax({
	  type: 'POST', dataType: "html",
	  url: 'hv_lichthinganh_process.php?w=hk-khoa'
	  + '&d=' + encodeURIComponent(p_dothoc)
	  + '&hisid=<?php echo $_REQUEST["hisid"]; ?>',
	  success: function(data) {
		$("#lichthi_nganh_txtKhoa").html(data);
		lichthi_nganh_updateNganh($("#lichthi_nganh_txtKhoa").val(), p_dothoc);
	  },
	  error: function(xhr, ajaxOptions, thrownError) {
		$("#lichthi_nganh_chitiet").html(thrownError);
	  }
	});
}

function lichthi_nganh_updateNganh(p_khoa, p_dothoc)
{
	xreq = $.ajax({
	  type: 'POST', dataType: "html",
	  url: 'hv_lichthinganh_process.php?w=khoa_hk-nganh'
	  + '&k=' + p_khoa 
	  + '&d=' + p_dothoc
	  + '&hisid=<?php echo $_REQUEST["hisid"]; ?>',
	  success: function(data) {
		//$('#tkn_nganh_txtNganh').empty();
		$("#tkn_nganh_txtNganh").html(data);
		lichthi_nganh_updateLichThi(p_khoa, p_dothoc, $("#tkn_nganh_txtNganh").val());
	  },
	  error: function(xhr, ajaxOptions, thrownError) {
		$("#lichthi_nganh_chitiet").html(thrownError);
	  }
	});
}

function lichthi_nganh_updateLichThi(p_khoa, p_dothoc, p_nganh)
{
	if (p_nganh!=null)
	{
		$("#lichthi_nganh_chitiet").html("<div align=center><img border='0' src='images/ajax-loader.gif'/></div>");
		$( "#lichthi_nganh_btn_printpreview" ).button( "disable" );
		xreq = $.ajax({
		  type: 'POST', dataType: "html",
		  url: 'hv_lichthinganh_process.php?w=khoa_hk_nganh-lichthi_nganh'
		  + '&k=' + p_khoa 
		  + '&d=' + p_dothoc
		  + '&n=' + p_nganh
		  + '&h=' + encodeURIComponent($("#lichthi_nganh_txtHK option:selected").html())
		  + '&hisid=<?php echo $_REQUEST["hisid"]; ?>',
		  success: function(data) {
			//$('#tkn_nganh_txtNganh').empty();
			$("#lichthi_nganh_chitiet").html(data);
			$( "#lichthi_nganh_btn_printpreview" ).button( "enable" );
		  },
		  error: function(xhr, ajaxOptions, thrownError) {
			$("#lichthi_nganh_chitiet").html(thrownError);
		  }
		});
	}
	else
	{
		$("#lichthi_nganh_chitiet").html('Học kỳ ' + $("#lichthi_nganh_txtHK option:selected").html() + ' chưa có lịch thi');
		$( "#lichthi_nganh_btn_printpreview" ).button( "disable" );
	}
}

$(function() {
	$( "#lichthi_nganh_btn_printpreview" ).button({ icons: {primary:'ui-icon ui-icon-print'} });
	lichthi_nganh_updateKhoa($('#lichthi_nganh_txtHK').val());
	
	$("#lichthi_nganh_btn_printpreview").click(function(){
		lichthi_nganh_writeConsole("<div style='font-size:13px; font-weight:bold;' align=left>ĐẠI HỌC BÁCH KHOA - TP.HCM<br/>PHÒNG ĐÀO TẠO SAU ĐẠI HỌC<br/> &nbsp;</div>" + $("#lichthi_nganh_chitiet").html() + "<div style='clear:both;'>&nbsp;<br/><a href='JavaScript:window.print();'>In trang này</a></div>");
	});	// end $("#lichthi_nganh_btn_printpreview")
});
</script>

<?php 
if (isset ($db_conn))
	oci_close($db_conn);
?>