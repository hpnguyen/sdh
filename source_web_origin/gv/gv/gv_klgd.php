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
if (!allowPermisstion(base64_decode($_SESSION['uidloginPortal']), '011', $db_conn))
{
	die('Truy cập bất hợp pháp'); 
}

$macb = $_SESSION['macb'];

$sqlstr="select to_char(to_date(value, 'dd-mon-yyyy'),'dd/mm/yyyy') ngay_het_han from config where name='HAN_PHAN_HOI_KLGD'"; 
$stmt = oci_parse($db_conn, $sqlstr);oci_execute($stmt);$n = oci_fetch_all($stmt, $resDM);oci_free_statement($stmt);
$ngayhethanykienphanhoi = $resDM["NGAY_HET_HAN"][0];
?>

<div id="gvKLGD" >
	
	<table  border="0" cellspacing="0" cellpadding="5" align=left>
		<tr>
			<td align=right style="width:60px;font-weight:bold"><label for='txtKhoaKLGD'>Chọn HK</label></td>	
			<td align=left style="width:100px;">
				<select name="txtKhoaKLGD" id="txtKhoaKLGD" style="font-size:15px;">
				   <?php $sqlstr="select (hoc_ky || '/' || nam_hoc_tu || '-' || nam_hoc_den) nam_hoc, dot_hoc
								from dot_hoc_nam_hoc_ky
								where dot_hoc in (select distinct dot_hoc from view_klgd where ma_can_bo = '".$macb."')
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
			<td style="width:50px;"></td><td><a id="gv_klgd_btn_printpreview" style='font-size:80%; '>&nbsp;Xem bản In</a></td>
		</tr>
	</table>
	
	<div style='clear:both;'></div>
	<div id="gv_klgd_detail" align=center></div>
	<div id="ykien_klgd" align=left style="display:none;">
		<table style="width:100%;">
		<tr>
			<td colspan=2>
				<b>&nbsp;Ngày hết hạn cho ý kiến phản hồi <font color=red> <?php echo $ngayhethanykienphanhoi; ?></font></b>
			</td>
		</tr>
		<tr>
			<td valign=top style="width:50%">
				<select name="txtXacNhanKLGD" id="txtXacNhanKLGD" style="width:100%;height:25px;font-size:15px" class="text ui-widget-content ui-corner-all tableData" onChange="klgd_updateXacNhanKLGD(this.value)">
					<option value='' style='color:#0000ff;'>Ý kiên của bạn về thông tin KLGD trên?</option>
					<option value='1' style='color:#008000;'>Tôi ĐỒNG Ý với thông tin KLGD trên</option>
					<option value='0' style='color:#cf1919;'>Tôi KHÔNG ĐỒNG Ý với thông tin KLGD trên</option>
				</select>
				<div id=ykiendiv name=ykiendiv style='display:none;'>
					<div  id="divYKienPhanHoiKLGD"  name="divYKienPhanHoiKLGD"  class="text ui-widget-content ui-corner-all tableData" style="font-size:15px; height:120px; padding: 10px 5px 5px 10px; cursor:pointer; overflow:auto;" ></div>
					<input type=hidden id="txtYKienPhanHoiKLGD" name="txtYKienPhanHoiKLGD">
				</div>
				<div style='margin-top:5px'><a id="gv_klgd_btn_ykienphanhoi" style='font-size:80%'>&nbsp;Cập nhật ý kiến</a></div>
			</td>
			<td  valign=top style="width:50%;">
				<div id=ykiendiv1 name=ykiendiv1 style='display:none;'>
					<div id=traloiykiendiv style='display:none;margin: 5px 0 0 0 '>
						<div style="font-size:12px; font-weight:bold; margin:0px 0 5px 5px;">
							Trả lời từ Phòng Đào tạo Sau đại học:
						</div>
						<div id="divTraloiYKienPhanHoiKLGD"  name="divTraloiYKienPhanHoiKLGD"  class="text ui-widget-content ui-corner-all tableData" style="font-size:15px; height:120px; padding: 10px 5px 5px 10px; overflow:auto;font-style:italic; background-color: #e7f8ff" ></div>
					</div>
				</div>
			</td>
		</tr>
		<tr>
			<td>
				
			</td>
			<td style="width:100%;">
			</td>
		</tr>
		</table>
		
	</div>
	
</div> <!-- end  -->

<script type="text/javascript">

function klgd_updateXacNhanKLGD(p_xacnhan)
{
// p_xacnhan is input parameter
// = 1 is Agree
// = 0 is not agree
// = null is not yet agree
	if (p_xacnhan == '')
	{
		$( "#ykiendiv" ).hide();
		$( "#ykiendiv1" ).hide();
		$( '#txtXacNhanKLGD' ).css({ 'color': '#0000ff'});
	}
	else if (p_xacnhan == 1)
	{
		$( "#ykiendiv" ).hide();
		$( "#ykiendiv1" ).hide();
		$( '#txtXacNhanKLGD' ).css({ 'color': '#008000'});
	}
	else if (p_xacnhan == 0)
	{
		$( "#ykiendiv" ).show();
		$( "#ykiendiv1" ).show();
		$( '#txtXacNhanKLGD' ).css({ 'color': '#cf1919'});
	}
	
}

var hethanykien =  1;

$(function(){
 $( "#gv_klgd_btn_printpreview" ).button({ icons: {primary:'ui-icon ui-icon-print'} });
 $( "#gv_klgd_btn_ykienphanhoi" ).button({ icons: {primary:'ui-icon ui-icon-disk'} });
 
 $("#gv_klgd_btn_printpreview").click(function(){
	writeConsole("<div style='font-size:13px; font-weight:bold;' align=left>ĐẠI HỌC BÁCH KHOA - TP.HCM<br/>PHÒNG ĐÀO TẠO SAU ĐẠI HỌC<br/> &nbsp;</div>" + $("#gv_klgd_detail").html() + "<div style='clear:both;'>&nbsp;<br/><a href='JavaScript:window.print();'>In trang này</a></div>", 900,450);
 });
 
 $("#divYKienPhanHoiKLGD").click(function(){
	if (hethanykien) 
		gv_open_msg_box("<b>Lưu ý hết hạn phản hồi ý kiến vào ngày <font color=red><?php echo $ngayhethanykienphanhoi; ?></font> và chỉ được phản hồi ý kiến cho học kỳ hiện tại</b>","alert");
	else
		replaceDiv(this);
	
 });
 
 $("#gv_klgd_btn_ykienphanhoi").click(function(){
	bvalid = true;
	
	close_ckeditor();
	
	$( "#txtYKienPhanHoiKLGD" ).val($( "#divYKienPhanHoiKLGD" ).html());
	
	//alert($( "#txtYKienPhanHoiKLGD" ).serialize());
	
	if ( $( "#txtXacNhanKLGD" ).val() == 2 )
	{
		bvalid = false;
		gv_open_msg_box("<font color=red>Vui lòng xác nhận thông tin khối lượng giảng dạy!</font>","alert");
	}
	else
	{
		if ( $( "#txtXacNhanKLGD" ).val() == 0 && $( "#divYKienPhanHoiKLGD" ).html() == "" )
		{
			bvalid = false;
			gv_open_msg_box("<font color=red>Bạn vui lòng cho ý kiến phản hồi nếu không đồng ý với thông tin khối lượng giảng dạy này</font>","alert");
		}
		
		if ( $( "#txtXacNhanKLGD" ).val() == 1 )
		{
			$( "#divYKienPhanHoiKLGD" ).html("");
			$( "#txtYKienPhanHoiKLGD" ).val($( "#divYKienPhanHoiKLGD" ).html());
		}
	}
	
	if (bvalid)
	{	
		var strykien = $( "#txtYKienPhanHoiKLGD" ).val();
		if (strykien.length >= 1000)
		{
			bvalid = false;
			gv_open_msg_box("<font color=red>Ý kiến của bạn quá dài vui lòng giới hạn lại trong vòng <b>1000</b> ký tự</font>","alert");
		}
	}
	
	if (bvalid)
	{
		//alert('Luu');
		dataString = $("#txtXacNhanKLGD").serialize() + "&" + $("#txtKhoaKLGD").serialize()
		+ "&w=updateYKien&" + $("#txtYKienPhanHoiKLGD").serialize()
		+ "&hisid=<?php echo $_REQUEST["hisid"];?>";
		$.post("gv/gv_klgdprocess.php", dataString,
			function(data){
				//alert(data);
				//$("#gv_klgd_detail").html(data);
				gv_open_msg_box(data, "info");
		}, "html");
	}
	
 });
 
 loadKLGD();
 
 $("#txtKhoaKLGD").change(function(e) {
	loadKLGD();
 });
 
 function loadKLGD()
 {
	$("#gv_klgd_detail").html("<img border='0' src='images/ajax-loader.gif'/>");
	$( "#ykien_klgd" ).hide();
	
	close_ckeditor();
	
	dataString = $("#txtKhoaKLGD").serialize()
	+ '&w=getKLGD&h=' + encodeURIComponent($("#txtKhoaKLGD option:selected").html())
	+ "&hisid=<?php echo $_REQUEST["hisid"];?>";
	//alert(dataString);
	$.post("gv/gv_klgdprocess.php", dataString ,
		function(data){
			//alert(data);
			$( "#gv_klgd_detail" ).html(data);
			$( "#ykien_klgd" ).show();
	}, "html");
 }

});
</script>



<?php 
if (isset ($db_conn))
	oci_close($db_conn);
?>