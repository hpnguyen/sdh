<?php
/*
if (isset($_REQUEST["hisid"]))
{
	session_id($_REQUEST["hisid"]);
	session_start();
}
*/
//if (!isset($_SESSION['uidloginhv'])){
//	die('Truy cập bất hợp pháp'); 
//}

include "libs/connect.php";

?>

<div align="left" style="margin:0 auto;">
<form id="form_tkbnganh" name="form_tkbnganh" method="post" action="">
   <table width="100%" border="0" cellspacing="0" cellpadding="5" align=center>
   
	<tr>
	
	  <td align=right style='width:50px'>
		<span class="heading">
		<label for="tkb_nganh_txtHK">Học kỳ</label>
		</span>
	  </td>
	  <td align=left style="width:50px;">
		<select id=tkb_nganh_txtHK name=tkb_nganh_txtHK style="font-size:15px;" onChange="tkb_nganh_updateKhoa(this.value);">
			<?php
			$sqlstr="select d.hoc_ky||'/'||d.nam_hoc_tu||'-'||d.nam_hoc_den HOC_KY, d.dot_hoc
							from dot_hoc_nam_hoc_ky d
							where d.nam_hoc_tu>=2005
							and dot_hoc in (select distinct dot_hoc from thoi_khoa_bieu)
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
	  <td align=left style="width:50px;">
		<span class="heading">
		<label for="tkb_nganh_txtKhoa">Khóa</label>
		</span>
		
	  </td>
	  <td align=left >
		<select id=tkb_nganh_txtKhoa name=tkb_nganh_txtKhoa onChange="tkb_nganh_updateNganh(this.value, $('#tkb_nganh_txtHK').val());" style="font-size:15px;">
		</select>
		
	  </td>
	</tr>
	
	
	<tr>
	  <td align=right>
		<span class="heading">
			<label for="tkb_nganh_txtNganh">Ngành</label>
		</span>
	  </td>
	  <td align=left colspan="3">
	  
		<select id=tkb_nganh_txtNganh name=tkb_nganh_txtNganh style="width:400px;font-size:15px;" onChange="tkb_nganh_updateTKB($('#tkb_nganh_txtKhoa').val(),$('#tkb_nganh_txtHK').val(),this.value);">
			<option value="">Chọn ngành</option>
		</select>
	  </td>
	</tr>
	<tr>
	  <td align=right>
		
	  </td>
	  <td align=left colspan="3" style='font-size:80%'>
		<a id="tkb_nganh_btn_printpreview" name="taosach">&nbsp;Xem bản In</a>
	  </td>
	</tr>
   </table>

   <div id="tkb_nganh_chitiet" style="margin-top:5px;" align=center></div>

</form>
</div>

<script type="text/javascript">
function tkb_nganh_writeConsole(content) {
	a=window.open('','myconsole',
	'width=800,height=450'
	+',menubar=0'
	+',toolbar=0'
	+',status=0'
	+',scrollbars=1'
	+',resizable=1')
	a.document.writeln(
	'<html><head><title>Phong Dao Tao SDH - DHBK</title></head>'
	+'<body bgcolor=white onLoad="self.focus()">'
	+content
	+'</body></html>'
	)
	a.document.close()
}
		
function tkb_nganh_updateKhoa(p_hk)
{
	$("#tkb_nganh_chitiet").html("<div align=center><img border='0' src='images/ajax-loader.gif'/></div>");
	$( "#tkb_nganh_btn_printpreview" ).button( "disable" );
	
	xreq = $.ajax({
	  type: 'POST', dataType: "html",
	  url: 'hv_tkbnganh_process.php?w=hk-khoa'
	  + '&d=' + p_hk
	  + '&hisid=<?php echo $_REQUEST["hisid"]; ?>',
	  success: function(data) {
		//$('#tkb_nganh_txtHK').empty();
		$("#tkb_nganh_txtKhoa").html(data);
		tkb_nganh_updateNganh($("#tkb_nganh_txtKhoa").val(), p_hk);
	  },
	  error: function(xhr, ajaxOptions, thrownError) {
		
	  }
	});
}

function tkb_nganh_updateNganh(p_khoa, p_dothoc)
{
	xreq = $.ajax({
	  type: 'POST', dataType: "html",
	  url: 'hv_tkbnganh_process.php?w=khoa_hk-nganh'
	  + '&k=' + p_khoa 
	  + '&d=' + p_dothoc
	  + '&hisid=<?php echo $_REQUEST["hisid"]; ?>',
	  success: function(data) {
		//$('#tkb_nganh_txtNganh').empty();
		$("#tkb_nganh_txtNganh").html(data);
		tkb_nganh_updateTKB(p_khoa, p_dothoc, $("#tkb_nganh_txtNganh").val());
	  },
	  error: function(xhr, ajaxOptions, thrownError) {
		
	  }
	});
}

function tkb_nganh_updateTKB(p_khoa, p_dothoc, p_nganh)
{
	$("#tkb_nganh_chitiet").html("<div align=center><img border='0' src='images/ajax-loader.gif'/></div>");
	$( "#tkb_nganh_btn_printpreview" ).button( "disable" );
	xreq = $.ajax({
	  type: 'POST', dataType: "html",
	  url: 'hv_tkbnganh_process.php?w=khoa_hk_nganh-tkb_nganh'
	  + '&k=' + p_khoa 
	  + '&d=' + p_dothoc
	  + '&n=' + p_nganh
	  + '&h=' + encodeURIComponent($("#tkb_nganh_txtHK option:selected").html())
	  + '&hisid=<?php echo $_REQUEST["hisid"]; ?>',
	  success: function(data) {
		$("#tkb_nganh_chitiet").html(data);
		$( "#tkb_nganh_btn_printpreview" ).button( "enable" );
	  },
	  error: function(xhr, ajaxOptions, thrownError) {
		$("#tkb_nganh_chitiet").html(thrownError);
		$( "#tkb_nganh_btn_printpreview" ).button( "disable" );
	  }
	});
}

$(function() {
	$( "#tkb_nganh_btn_printpreview" ).button({ icons: {primary:'ui-icon ui-icon-print'} });
	tkb_nganh_updateKhoa($('#tkb_nganh_txtHK').val());
	
	
	$("#tkb_nganh_btn_printpreview").click(function(){
		tkb_nganh_writeConsole("<div style='font-size:13px; font-weight:bold;' align=left>ĐẠI HỌC BÁCH KHOA - TP.HCM<br/>PHÒNG ĐÀO TẠO SAU ĐẠI HỌC<br/> &nbsp;</div>" + $("#tkb_nganh_chitiet").html() + "<div style='clear:both;'>&nbsp;<br/><a href='JavaScript:window.print();'>In trang này</a></div>");
	});	// end $("#tkb_nganh_btn_printpreview")
});
</script>

<?php 
if (isset ($db_conn))
	oci_close($db_conn);
?>