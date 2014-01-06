<?php
if (isset($_REQUEST["hisid"])){
	session_id($_REQUEST["hisid"]);
	session_start();
}
if (!isset($_SESSION['uidloginPortal'])){
	die('Đã hết phiên làm việc'); 
}

include "../libs/connect.php";
include "../libs/pgslibs.php";
if (!allowPermisstion(base64_decode($_SESSION['uidloginPortal']), '105', $db_conn))
{
	die('Truy cập bất hợp pháp'); 
}

$macb = $_SESSION['macb'];
$makhoa = base64_decode($_SESSION['makhoa']);

?>

<div id="gvLichThi" >
	<table  border="0" cellspacing="0" cellpadding="5" align=left>
		<tr>
			<td align=right style="width:60px;font-weight:bold"><label for='khoa_lichthihocky_txtDotHoc'>Chọn HK</label></td>	
			<td align=left style="width:100px;">
				<select name="khoa_lichthihocky_txtDotHoc" id="khoa_lichthihocky_txtDotHoc" style="font-size:15px;">
					   <?php $sqlstr="select (hoc_ky || '/' || nam_hoc_tu || '-' || nam_hoc_den) nam_hoc, dot_hoc
									from dot_hoc_nam_hoc_ky
									where dot_hoc in (	select distinct t.dot_hoc 
														from thoi_khoa_bieu t, mon_hoc mh , bo_mon b, khoa k, lich_thi l
														where t.ma_mh = mh.ma_mh
														and mh.ma_bo_mon = b.ma_bo_mon (+)
														and b.ma_khoa = k.ma_khoa
														and b.ma_khoa = '$makhoa'
														and l.dot_hoc = t.dot_hoc
														and l.ma_mh = t.ma_mh
														and l.lop = t.lop
														and t.tuan_ket_thuc || t.thu =
														(select max(t1.tuan_ket_thuc || t1.thu)
														from thoi_khoa_bieu t1
														where t1.ma_mh = t.ma_mh
														and t1.dot_hoc = t.dot_hoc
														and t1.lop = t.lop
														and t1.khoa = t.khoa)
														and t.lop_tinh is null
													)
									and dot_hoc >= '20-AUG-2012'
									order by nam_hoc_tu desc, dot_hoc desc";
						$stmt = oci_parse($db_conn, $sqlstr);
						oci_execute($stmt);
						$n = oci_fetch_all($stmt, $resDM);
						oci_free_statement($stmt);
						
						for ($i = 0; $i < $n; $i++)
						{
							echo "<option value='".$resDM["DOT_HOC"][$i]."'>" .$resDM["NAM_HOC"][$i]. "</option>";
						}
						
					  ?>
				</select>
			</td>
		</tr>
		<tr>
			<td style="width:50px;"></td><td style="width:300px;"><a id="khoa_lichthihocky_btn_printpreview" style='font-size:80%'>&nbsp;Xem bản In</a> <a id="khoa_lichthihocky_btn_download" style='font-size:80%'>&nbsp;Tải lịch thi hk về</a></td>
		</tr>
	</table>
	
	<div style='clear:both;'></div>
	<div id="khoa_lichthihocky_detail" align=center></div>
</div> <!-- end  -->

<script type="text/javascript">
 function loadLichThiHocKyFile(p_dothoc, p_hocky)
 {
	
	dataString = 't=dothoc-lichthihkfile' 
	+ '&d='+ p_dothoc 
	+ '&h='+ p_hocky 
	+ "&hisid=<?php echo $_REQUEST["hisid"];?>";

	$.post("khoa/khoa_lichthihockyprocess.php", dataString ,
		function(data){
			if (data.url!='') {
				gv_open_msg_box("<p><table><tr><td style='width:200px; font-weight:bold'>Lịch thi học kỳ " + p_hocky + " Khoa <?php echo $_SESSION['tenkhoa']; ?></td><td style='width:80px'><a style='color:blue;font-weight:bold;' target='_blank' href='"+data.url+"'><u><span class='ui-icon ui-icon-disk' style='float:left; margin:0 5px 0 0;'></span> tải về</u></a></td></tr></table></p>", '', 300, 150);
			}
		
	}, "json");
 }
 
$(function(){

 $( "#khoa_lichthihocky_btn_printpreview" ).button({ icons: {primary:'ui-icon ui-icon-print'} });
 $( "#khoa_lichthihocky_btn_download" ).button({ icons: {primary:'ui-icon ui-icon-disk'} });
 
 $("#khoa_lichthihocky_btn_printpreview").click(function(){
	writeConsole("<div style='font-size:13px; font-weight:bold;' align=left>ĐẠI HỌC BÁCH KHOA - TP.HCM<br/>PHÒNG ĐÀO TẠO SAU ĐẠI HỌC<br/> &nbsp;</div>" + $("#khoa_lichthihocky_detail").html() + "<div style='clear:both;'>&nbsp;<br/><a href='JavaScript:window.print();'>In trang này</a></div>", 900,450);
 });
 
 $("#khoa_lichthihocky_btn_download").click(function(){
	loadLichThiHocKyFile( $("#khoa_lichthihocky_txtDotHoc").val() , $("#khoa_lichthihocky_txtDotHoc option:selected").html() );
 });
 
khoa_lichthihocky_loadLichThiHocKy( $("#khoa_lichthihocky_txtDotHoc").val(), $("#khoa_lichthihocky_txtDotHoc option:selected").html() );
 
 $("#khoa_lichthihocky_txtDotHoc").change(function(e) {
	khoa_lichthihocky_loadLichThiHocKy( $("#khoa_lichthihocky_txtDotHoc").val(), $("#khoa_lichthihocky_txtDotHoc option:selected").html() );
 });
 
 function khoa_lichthihocky_loadLichThiHocKy(p_dothoc, p_hocky)
 {
	$("#khoa_lichthihocky_btn_printpreview" ).button( "disable" );
	$("#khoa_lichthihocky_btn_download" ).button( "disable" );
	
	$("#khoa_lichthihocky_detail").html("<img border='0' src='images/ajax-loader.gif'/>");
	
	dataString = 't=dothoc-lichthihk' 
	+ '&d=' + encodeURIComponent(p_dothoc)
	+ '&h=' + encodeURIComponent(p_hocky)
	+ "&hisid=<?php echo $_REQUEST["hisid"];?>";
	
	xreq = $.ajax({
	  type: 'POST', data: dataString, dataType: "html", url: 'khoa/khoa_lichthihockyprocess.php',
	  success: function(data) {
		$( "#khoa_lichthihocky_detail" ).html(data);
		$( "#khoa_lichthihocky_btn_printpreview" ).button( "enable" );
		$( "#khoa_lichthihocky_btn_download" ).button( "enable" );
	  },
	  error: function(xhr, ajaxOptions, thrownError) {
		$( "#khoa_lichthihocky_detail" ).html(thrownError);
		$( "#khoa_lichthihocky_btn_printpreview" ).button( "enable" );
		$( "#khoa_lichthihocky_btn_download" ).button( "enable" );
	  }
	});
	
	if ($( "#khoa_lichthihocky_detail" ).html()!='')
	{
		$( "#khoa_lichthihocky_btn_printpreview" ).button( "enable" );
		$( "#khoa_lichthihocky_btn_download" ).button( "enable" );
	}
	else
	{
		$( "#khoa_lichthihocky_btn_printpreview" ).button( "disable" );
		$( "#khoa_lichthihocky_btn_download" ).button( "disable" );
	}
 }
 

});
</script>



<?php 
if (isset ($db_conn))
	oci_close($db_conn);
?>