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

$sqlstr="SELECT value FROM config WHERE name='DK_DC_DOT_HOC'";
$stmt = oci_parse($db_conn, $sqlstr);oci_execute($stmt);$n = oci_fetch_all($stmt, $resDM);oci_free_statement($stmt);
$dothoc_dkdc = $resDM["VALUE"][0];

$sqlstr="select d.hoc_ky||'/'||d.nam_hoc_tu||'-'||d.nam_hoc_den HOC_KY, d.dot_hoc from dot_hoc_nam_hoc_ky d where d.dot_hoc ='$dothoc_dkdc'";
$stmt = oci_parse($db_conn, $sqlstr);oci_execute($stmt);$n = oci_fetch_all($stmt, $resDM);oci_free_statement($stmt);
$hk_dkdc = $resDM["HOC_KY"][0];

$sqlstr="SELECT value , floor(sysdate - to_date(value,'dd/mm/yyyy')) het_han FROM config WHERE name='DK_DC_NGAY_KT'";
$stmt = oci_parse($db_conn, $sqlstr);oci_execute($stmt);oci_fetch_all($stmt, $resDM);oci_free_statement($stmt);
$ngayhethan_dkdc = $resDM["VALUE"][0]; $hethan_dkdc = $resDM["HET_HAN"][0];

$sqlstr="SELECT value , (sysdate - to_date(value,'dd/mm/yyyy')) bat_dau FROM config WHERE name='DK_DC_NGAY_BD'";
$stmt = oci_parse($db_conn, $sqlstr);oci_execute($stmt);$n = oci_fetch_all($stmt, $resDM);oci_free_statement($stmt);
$ngaybatdau_dkdc = $resDM["VALUE"][0]; $batdau_dkdc = $resDM["BAT_DAU"][0];

$cauxacnhan = "Tôi đồng ý đăng ký đề cương học kỳ này.";

if ($batdau_dkdc<0){
	die("<div align=center style='color:red'>CHƯA ĐẾN NGÀY ĐĂNG KÝ ĐỀ CƯƠNG, THEO KẾ HOẠCH TỪ <b>$ngaybatdau_dkdc</b> - <b>$ngayhethan_dkdc</b></div>");
}
if ($hethan_dkdc>0){
	die("<div align=center style='color:red'>ĐÃ HẾT HẠN ĐĂNG KÝ ĐỀ CƯƠNG NGÀY <b>$ngayhethan_dkdc</b></div>");
}

$sqlstr="SELECT MA_HOC_VIEN, DOT_HOC, HUONG_NGHIEN_CUU, get_thanh_vien(HUONG_DAN_1) hd1, get_thanh_vien(HUONG_DAN_2) hd2, GHI_CHU
from DANG_KY_DE_CUONG where DOT_HOC='$dothoc_dkdc' and MA_HOC_VIEN = '$mahv'";
$stmt = oci_parse($db_conn, $sqlstr);oci_execute($stmt);$n = oci_fetch_all($stmt, $resDM);oci_free_statement($stmt);
$huongnc = $resDM["HUONG_NGHIEN_CUU"][0]; $ghichu = $resDM["GHI_CHU"][0];
$hd1 = $resDM["HD1"][0]; $hd2 = $resDM["HD2"][0];
$mahv_dk = $resDM["MA_HOC_VIEN"][0];

$sqlstr="SELECT ctdt_loai('$mahv') ctdt_loai from dual";
$stmt = oci_parse($db_conn, $sqlstr);oci_execute($stmt);$n = oci_fetch_all($stmt, $resDM);oci_free_statement($stmt);
$ctdt = $resDM["CTDT_LOAI"][0]

?>
<div align="left" style="margin:0 auto;">
	<div style="margin:5px 0 10px 0; font-size:14px;" align=center>
		<b>ĐĂNG KÝ ĐỀ CƯƠNG HK <?php echo $hk_dkdc; ?></b>
		<div style='margin:5px 0 0 0;font-size:13px;'>Hạn đăng ký từ <font color=red><b><?php echo $ngaybatdau_dkdc; ?></b></font> đến <font color=red><b><?php echo $ngayhethan_dkdc; ?></b></font></div>
		<div style='margin:10px 0 10px 0;font-size:13px;'><b>Thông tin đã đăng ký</b></div>
		<div id=hv_dkdc_thongtin_dk style='margin:10px 0 10px 0;font-size:13px;'>Bạn chưa đăng ký đề cương</div>
		<div style="font-size:80%">
		<button id=dkdc_dangky_btn>Đăng ký đề cương</button> 
		<button id=dkdc_huy_btn>Huỷ đăng ký đề cương</button>
		</div>
	</div>
	
</div>

<div id=hv_dkdc_frm_dangky title="Đăng ký đề cương HK <?php echo $hk_dkdc; ?>">
	<form id="form_dangkydecuong" name="form_dangkydecuong" method="post" action="">
		<div style="margin:5px 0 0px 0; font-size:12px;" align=center>
			<div style='margin:5px 0 0 0;font-size:12px;'>Hạn đăng ký từ <font color=red><b><?php echo $ngaybatdau_dkdc; ?></b></font> đến <font color=red><b><?php echo $ngayhethan_dkdc; ?></b></font></div>
		</div>
		<table align=center width="100%" cellspacing="15" cellpadding="0" style="font-size:12px">
			<tr>
			  <td align=center colspan=2 style="font-weight:bold">
				<?php 
					if ($ctdt == 3){
						echo "Học CTĐT theo phương thức nghiên cứu";
					}elseif ($ctdt == 1){
						echo "Học CTĐT theo phương thức giảng dạy môn học + Khóa luận TN";
					}else{
						echo "Học CTĐT theo phương thức giảng dạy môn học + LVThS";
					}
				?>
			  </td>
			</tr>
			<tr>
			  <td align=left valign=top style="width:120px" >Hướng nghiên cứu</td>
			  <td align=left><input type=text id=dkdc_huongnghiencuu name=dkdc_huongnghiencuu style="width:100%" maxlength=500>
							<span id=dkdc_huongnghiencuu_error style="color: red;"></span>
			  </td>
			</tr>
			<tr>
			  <td align=left valign=top>Hướng dẫn 1</td>
			  <td align=left>
				<input type=text id=dkdc_huongdan1 name=dkdc_huongdan1 style="width:100%" title="Nhập tên của các bộ hướng dẫn vào ô này, sẽ có danh sách cb hướng dẫn "><input type=hidden id=dkdc_huongdan1_ma name=dkdc_huongdan1_ma>
				<span id=dkdc_huongdan1_error style="color: red;"></span>
			  </td>
			</tr>
			<tr>
			  <td align=left valign=top>Hướng dẫn 2</td>
			  <td align=left>
				<input type=text id=dkdc_huongdan2 name=dkdc_huongdan2 style="width:100%"><input type=hidden id=dkdc_huongdan2_ma name=dkdc_huongdan2_ma>
				<span id=dkdc_huongdan2_error style="color: red;"></span>
			  </td>
			</tr>
			<tr>
			  <td align=left valign=top>Ghi chú</td>
			  <td align=left><input type=text id=dkdc_ghichu name=dkdc_ghichu style="width:100%" maxlength=250></td>
			</tr>
			<tr>
			  <td align=left ></td>
			  <td align=left><input type=checkbox id=dkdc_dongy name=dkdc_dongy> <?php echo $cauxacnhan; ?>
				<span id=dkdc_dongy_error style="color: red;"></span>
			  </td>
			</tr>
			<tr>
			  <td align=left colspan=2 style='font-size:80%'>
				<u>Lưu ý</u>: Đối với các GV không có trong danh sách hướng dẫn, học viên vui lòng liên hệ khoa/bộ môn cung cấp lý lịch khoa học & bằng cấp liên quan của GVHD và chuyển cho phòng ĐT SĐH.
			  </td>
			</tr>
		</table>
	</form>
</div>

<script type="text/javascript">
$(function() {
	
	var dkdc_projects;
	var dkdc_ttdecuong = {
			"huongnc":"<?php echo $huongnc ?>",
			"ghichu":"<?php echo $ghichu ?>",
			"hd1_ten":"<?php echo $hd1 ?>",
			"hd2_ten":"<?php echo $hd2 ?>",
			"dongydangky":"<?php if ($mahv_dk!="") {echo "1";} else {echo "0";} ?>"
		};
	$( "#dkdc_dangky_btn" ).button({ icons: {primary:'ui-icon ui-icon-pencil'} });
	$( "#dkdc_dangky_btn" ).click(function(){
		$('#hv_dkdc_frm_dangky').dialog('open');
	});
	
	$( "#dkdc_huy_btn" ).button({ icons: {primary:'ui-icon ui-icon-trash'} });
	$( "#dkdc_huy_btn" ).click(function(){
		var datastring = 'w=huydangkydecuong&m=<?php echo $mahv_dk?>&d=<?php echo $dothoc_dkdc?>&hisid=<?php echo $_REQUEST["hisid"]?>';
		$.ajax({
			type: "POST", url: 'hv_dangkydecuong_process.php', dataType: "json", data: datastring,
			beforeSend: function(xhr){
				hv_processing_diglog("open");
			},
			success:function(result){
				if (result.success==1){
					hv_dkdc_fill_form(1);
					$( "#dkdc_huy_btn" ).hide();
					$( "#dkdc_dangky_btn" ).show();
				}else{
					hv_open_msg_box("Không thể huỷ đề cương", "alert");
				}
				hv_processing_diglog("close");
				
				//$( this ).dialog( "close" );
				
			},
			error: function (xhr,status,error){
				hv_processing_diglog("close");
				alert(error);
			},
			complete: function(xhr,status){
				hv_processing_diglog("close");
			}
		});
	});
	<?php 
		if ($mahv_dk=="") {
	?>
			hv_dkdc_fill_form(1);
			$( "#dkdc_huy_btn" ).hide();
	<?php
		}else{
	?>
			hv_dkdc_fill_form(0, dkdc_ttdecuong.huongnc, dkdc_ttdecuong.hd1_ten, dkdc_ttdecuong.hd2_ten, dkdc_ttdecuong.ghichu);
			$( "#dkdc_dangky_btn" ).hide();
	<?php 
		}
	?>
	// Khoi tao danh sach can bo giang day
	dataString = 'w=ds_cb_huongdan&hisid=<?php echo $_REQUEST["hisid"] ?>&d=' + encodeURIComponent('<?php echo $dothoc_dkdc; ?>');
	xreq = $.ajax({
		type: 'POST', url: 'hv_dangkydecuong_process.php', data: dataString,dataType: "json", 
		success: function(data) {
			dkdc_projects = data.dscanbo;
			hv_dkdc_init_huongdan(dkdc_projects, 1, 2);
			hv_dkdc_init_huongdan(dkdc_projects, 2, 1);
		},
		error: function(xhr, ajaxOptions, thrownError) {
			alert (thrownError);
		}
	});
	// end
	
	$("#hv_dkdc_frm_dangky").dialog({
		resizable: false,
		autoOpen: false,
		width:550, height:500,
		modal: true,
		buttons: [
			{
				id: "hv_dkdc_save",
				text: "Đăng ký",
				click: function() {
					if ($("#dkdc_huongnghiencuu").val()==""){
						$( '#dkdc_huongnghiencuu_error' ).html("* không được để trống");
						$("#dkdc_huongnghiencuu").focus();
						return false;
					}else{
						$( '#dkdc_huongnghiencuu_error' ).html("");
					}
					
					if ( $("#dkdc_dongy").attr("checked") == "checked"){
						$("#dkdc_dongy_error").html("");
					}else{
						$("#dkdc_dongy_error").html("<br>* bạn phải xác nhận điều này");
						$("#dkdc_dongy").focus();
						return false;
					}
					
					var datastring = $("#form_dangkydecuong").serialize()+'&w=dangkydecuong&d=<?php echo $dothoc_dkdc;?>&hisid=<?php echo $_REQUEST["hisid"]; ?>';
					$.ajax({
						type: "POST", url: 'hv_dangkydecuong_process.php', dataType: "json", data: datastring,
						beforeSend: function(xhr){
							hv_processing_diglog("open");
						},
						success:function(result){
							// Đăng ký thành công đề cương
							if (result.success==1){
								hv_dkdc_fill_form(0,result.huongnc, result.hd1_ten, result.hd2_ten, result.ghichu);
								hv_processing_diglog("close");								
								$( "#hv_dkdc_frm_dangky" ).dialog( "close" );
								$( "#dkdc_dangky_btn" ).hide();
								$( "#dkdc_huy_btn" ).show();
							}else{
								hv_open_msg_box("Không thể đăng ký đề cương", "alert");
							}
							
						},
						error: function (xhr,status,error){
							hv_processing_diglog("close");
							alert(error);
						},
						complete: function(xhr,status){
							hv_processing_diglog("close");
						}
					});
				}
			},
			{
				id: "hv_dkdc_close",
				text: "Đóng",
				click: function() {
					$("#dkdc_dongy_error").html("");
					$( '#dkdc_huongdan1_error' ).html("");
					$( '#dkdc_huongdan2_error' ).html("");
					$( '#dkdc_huongnghiencuu_error' ).html("");
					
					$( "#hv_dkdc_frm_dangky" ).dialog( "close" );
				}
			}
		]
	 });
});

function hv_dkdc_init_huongdan(pProjects, p1, p2){
	$( "#dkdc_huongdan" + p1).autocomplete({
			minLength: 0,
			source: pProjects,
			focus: function( event, ui ) {
				return false;
			},
			select: function( event, ui ) {
				$( '#dkdc_huongdan'+p1+'_ma' ).val(ui.item.value); $( '#dkdc_huongdan'+p1 ).val( ui.item.label );
				
				if ( ui.item.value == $( '#dkdc_huongdan'+p2+'_ma' ).val()){
					$( '#dkdc_huongdan'+p1+'_ma' ).val(""); $( '#dkdc_huongdan'+p1 ).val( "" );
					$( '#dkdc_huongdan'+p1+'_error' ).html("* trùng hướng dẫn vui lòng chọn lại hướng dẫn khác");
					$( '#dkdc_huongdan'+p1 ).focus();
				}else{
					$( '#dkdc_huongdan'+p1+'_error' ).html("");
				}

				return false;
			},
			change:function( event, ui ) {
				var data=$.data(this);
				if(data.autocomplete.selectedItem==undefined){
					$( '#dkdc_huongdan'+p1+'_ma' ).val(""); $( '#dkdc_huongdan'+p1 ).val("");
				}
			}
	})
	.data( "ui-autocomplete" )._renderItem = function( ul, item ) {
		return $( "<li>" ).append( "<a>" + item.label + "<br><b>" + item.desc + "</b></a><hr>" ).appendTo( ul );
	};
}

function hv_dkdc_fill_form(pReset, pHuongNC, pHD1, pHD2, pGhiChu){
	if (pReset){
		$("#hv_dkdc_thongtin_dk").html("<table cellspacing='5' cellpadding='0'  class='ui-widget ui-widget-content ui-corner-all tableData'> "
		+"<tr><td colspan=2 align=left><font color=red><i>Bạn chưa đăng ký đề cương</i></font></td></tr>" 
		+ "</table>");
	}else{
		$("#hv_dkdc_thongtin_dk").html("<table class='ui-widget ui-widget-content ui-corner-all tableData'> "
		+"<tr><td align=right>Hướng nghiên cứu: </td><td align=left>"+reverse_escapeJsonString(pHuongNC)+"</td></tr>"
		+"<tr><td align=right>Hướng dẫn 1: </td><td align=left>"+reverse_escapeJsonString(pHD1)+"</td></tr>"
		+"<tr><td align=right>Hướng dẫn 2: </td><td align=left>"+reverse_escapeJsonString(pHD2)+"</td></tr>"
		+"<tr><td align=right>Ghi chú: </td><td align=left>"+reverse_escapeJsonString(pGhiChu)+"</td></tr>"
		+ "</table>"
		);
	}
}
</script>

<?php 
if (isset ($db_conn))
	oci_close($db_conn);
?>