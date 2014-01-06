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
if (!allowPermisstion(base64_decode($_SESSION['uidloginPortal']), '001', $db_conn))
{
	die('Truy cập bất hợp pháp'); 
}

$macb = $_SESSION['macb'];
?>
  
<div id = 'gioithieuchuyengiadiv'>
	
	<form id="form_gioithieuchuyengia" method="post" action="" >
	<input type="hidden" name="magioithieuchuyengiaedit" id="magioithieuchuyengiaedit" />
    <div id = 'formthemgioithieuchuyengiadiv' title="">
        <table width="100%" border="0" align="center" cellpadding="5" cellspacing="2">	  
		  <tr>
			<td align="right" class="heading"><label for="txtHoTen_gioithieuchuyengia">Họ và tên</label></td>
			<td>
				<input style="width:200px" class="text ui-widget-content ui-corner-all tableData" name="txtHoTen_gioithieuchuyengia" id="txtHoTen_gioithieuchuyengia" type="text" maxlength="50" placeholder=""/>
				<label for="txtDienThoai_gioithieuchuyengia"><b>Điện thoại</b></label>
				<input style="width:100px" class="text ui-widget-content ui-corner-all tableData" name="txtDienThoai_gioithieuchuyengia" id="txtDienThoai_gioithieuchuyengia" type="text" maxlength="50" placeholder=""/>
			</td>
		  </tr>
		  <tr class="heading">
            <td align="right" ><label for="txtEmail_gioithieuchuyengia">Email</label></td>
			<td>
				<input style="width:250px" class="text ui-widget-content ui-corner-all tableData" name="txtEmail_gioithieuchuyengia" id="txtEmail_gioithieuchuyengia" type="text" maxlength="50" placeholder=""/>
			</td>
          </tr>
		  <tr class="heading">
            <td align="right" ><label for="txtNoiCongTac_gioithieuchuyengia">Nơi công tác</label></td>
			<td>
				<input style="width:450px" class="text ui-widget-content ui-corner-all tableData" name="txtNoiCongTac_gioithieuchuyengia" id="txtNoiCongTac_gioithieuchuyengia" type="text" maxlength="200" placeholder=""/>
			</td>
          </tr>
		  <tr class="heading">
            <td align="right" ><label for="txtDiaChi_gioithieuchuyengia">Địa chỉ liên lạc</label></td>
			<td>
				<input style="width:450px" class="text ui-widget-content ui-corner-all tableData" name="txtDiaChi_gioithieuchuyengia" id="txtDiaChi_gioithieuchuyengia" type="text" maxlength="200" placeholder=""/>
			</td>
          </tr>
		  
        </table>
		
		<div style="margin-top:10px" align="center" id="tipgioithieuchuyengia" class="ui-corner-all validateTips"></div>
			
    </div> <!--end formthemgioithieuchuyengiadiv -->
       
    	<table border="0" width="100%" cellspacing="0" cellpadding="0">
          <tr>
			<td align=left>
			<div style="margin-left:5px;font-weight:bold">
				
			</div>
			</td>
            <td align="right" style="width:360px" >
				<div style="margin-bottom:10px;">
						<a id="btn_taomoi_gioithieuchuyengia" name="btn_taomoi_gioithieuchuyengia" >&nbsp;Thêm mới...</a>
				&nbsp;&nbsp;
				<a id="btn_Xoa_gioithieuchuyengia" name="btn_Xoa_gioithieuchuyengia"> &nbsp;Xóa</a>
				</div>
			</td>
          </tr>
        </table>

		<table width="100%" id="table_gioithieuchuyengia" align="center" border="0" cellspacing="0" cellpadding="5" class="ui-widget ui-widget-content ui-corner-top tableData" height="20">
        <thead>
          <tr class="ui-widget-header heading" >
            <td class="ui-corner-tl"  align="left" style="width:30px"><em>TT</em></td>
            <td align="left"><em>Họ và tên</em></td>
			<td align="left"><em>Nơi công tác</em></td>
			<td align="left"><em>Địa chỉ liên lạc</em></td>
			<td align="left"><em>Điện thoại</em></td>
			<td align="left"><em>Email</em></td>
			<td style="width:30px">&nbsp;</td>
            <td class=" ui-corner-tr" style="width:20px">&nbsp;</td>
          </tr>
          </thead>
          <tbody>
		  </tbody>
        </table>
		<input name="gioithieuchuyengia_act" id="gioithieuchuyengia_act" type="hidden" value="" />
</form>
</div>		<!-- end of gioithieuchuyengiadiv -->   

<script type="text/javascript">

function getinfo_gioithieuchuyengia(pMaCG, pHoTen, pNoiCongTac, pDiaChi, pDienThoai, pEmail)
{
	$("#gioithieuchuyengia_act").val("edit");
	
	$("#magioithieuchuyengiaedit").val(pMaCG);
	document.getElementById('txtHoTen_gioithieuchuyengia').value = pHoTen;
	document.getElementById('txtNoiCongTac_gioithieuchuyengia').value = pNoiCongTac;
	document.getElementById('txtDiaChi_gioithieuchuyengia').value = pDiaChi;
	document.getElementById('txtDienThoai_gioithieuchuyengia').value = pDienThoai;
	document.getElementById('txtEmail_gioithieuchuyengia').value = pEmail;
	
	$("#formthemgioithieuchuyengiadiv").dialog('option', 'title', 'Cập nhật chuyên gia khác...');
	$("#formthemgioithieuchuyengiadiv").dialog('open');
}

//jQuery.ajax
//$(document).ready(function(){
$(function(){

  // delete btn
 $( "#btn_Xoa_gioithieuchuyengia" ).button({ icons: {primary:'ui-icon ui-icon-trash'} });
 
 // create new
 $( "#btn_taomoi_gioithieuchuyengia" ).button({ icons: {primary:'ui-icon ui-icon-document'} });
 
 // Lay du lieu moi
 gioithieuchuyengia_RefreshData();
 
// Check validate fields Nghien cuu khoa hoc
var jtxtDiaChi_gioithieuchuyengia		= $("#txtDiaChi_gioithieuchuyengia"),
	jtxtNoiCongTac_gioithieuchuyengia	= $("#txtNoiCongTac_gioithieuchuyengia"),
	jtxtHoTen_gioithieuchuyengia		= $("#txtHoTen_gioithieuchuyengia"),
	jtxtDienThoai_gioithieuchuyengia 	= $("#txtDienThoai_gioithieuchuyengia"),
	jtxtEmail_gioithieuchuyengia 		= $("#txtEmail_gioithieuchuyengia"),
	jmagioithieuchuyengiaedit			= $("#magioithieuchuyengiaedit"),
	allFieldsgioithieuchuyengia = $([]).add(jtxtDiaChi_gioithieuchuyengia).add(jtxtNoiCongTac_gioithieuchuyengia).add(jtxtHoTen_gioithieuchuyengia).add(jtxtDienThoai_gioithieuchuyengia).add(jtxtEmail_gioithieuchuyengia),
	tipsgioithieuchuyengia				= $("#tipgioithieuchuyengia");
	
	function gioithieuchuyengia_updateTips( t ) {
		tipsgioithieuchuyengia
					.text( t )
					.addClass( "ui-state-highlight" );
		setTimeout(function() {
			tipsgioithieuchuyengia.removeClass( "ui-state-highlight", 1500 );
		}, 1000 );
	}
	
	// gioithieuchuyengia_checkLength
	function gioithieuchuyengia_checkLength( o, n, min, max) {
		if (min==0 && (o.val().length==0))
		{	
			o.addClass( "ui-state-error" );
			o.focus();	
			gioithieuchuyengia_updateTips( "Thông tin " + n + " không được phép để trống.");
			
			return false;
		}else if (min==max && o.val().length<min){
			o.addClass( "ui-state-error" );
			o.focus();	
			gioithieuchuyengia_updateTips( "Thông tin " + n + " phải đủ " + min + " ký tự.");
		}else if ( o.val().length > max || o.val().length < min ) {
			o.addClass( "ui-state-error" );
			o.focus();		
			gioithieuchuyengia_updateTips( "Chiều dài của " + n + " từ " +
						min + " đến " + max + " ký tự.");
			return false;
		} else {
			return true;
		}
	}
	
	// Check Regexp
	function gioithieuchuyengia_checkRegexp( o, regexp, n ) {
		//alert('a');
		if ( o.val()!='' && !( regexp.test( o.val() ) ) ) {
			o.addClass( "ui-state-error" );
			o.focus();
			gioithieuchuyengia_updateTips( n );
			return false;
		} else {
			return true;
		
		}
	}
// End of check validate
	
	// Post du lieu cho nghien cuu khoa hoc
	$("#btn_taomoi_gioithieuchuyengia").click(function(){
		 $('#gioithieuchuyengia_act').val('add');
		 $("#formthemgioithieuchuyengiadiv").dialog('option', 'title', 'Tạo mới chuyên gia khác...');
		 $("#formthemgioithieuchuyengiadiv").dialog('open');
	});
	
	$( "#formthemgioithieuchuyengiadiv" ).dialog({
			autoOpen: false,
			height: 290,
			width: 630,
			modal: true,
			buttons: {
				"Ok": function() {
					var bValid = true;
					allFieldsgioithieuchuyengia.removeClass( "ui-state-error" );
					
					bValid = bValid && gioithieuchuyengia_checkLength( jtxtHoTen_gioithieuchuyengia, "\"Họ và tên\"", 0, 50);
					bValid = bValid && gioithieuchuyengia_checkLength( jtxtDienThoai_gioithieuchuyengia, "\"Điện thoại\"", 0, 50);
					bValid = bValid && gioithieuchuyengia_checkRegexp( jtxtEmail_gioithieuchuyengia, /^((([a-z]|\d|[!#\$%&'\*\+\-\/=\?\^_`{\|}~]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])+(\.([a-z]|\d|[!#\$%&'\*\+\-\/=\?\^_`{\|}~]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])+)*)|((\x22)((((\x20|\x09)*(\x0d\x0a))?(\x20|\x09)+)?(([\x01-\x08\x0b\x0c\x0e-\x1f\x7f]|\x21|[\x23-\x5b]|[\x5d-\x7e]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(\\([\x01-\x09\x0b\x0c\x0d-\x7f]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF]))))*(((\x20|\x09)*(\x0d\x0a))?(\x20|\x09)+)?(\x22)))@((([a-z]|\d|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(([a-z]|\d|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])*([a-z]|\d|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])))\.)+(([a-z]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(([a-z]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])*([a-z]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])))\.?$/i,"Nhập không đúng định dạng email, vd: pgs@hcmut.edu.vn" );
					bValid = bValid && gioithieuchuyengia_checkLength( jtxtNoiCongTac_gioithieuchuyengia, "\"Nơi công tác\"", 0, 200);
					bValid = bValid && gioithieuchuyengia_checkLength( jtxtDiaChi_gioithieuchuyengia, "\"Địa chỉ\"", 0, 200);
					
					if (bValid) {	
						$("#tipgioithieuchuyengia").html("<table><tr><td ><img src='../images/ajax-loader.gif'></td><td valign=top style='color: black'> Đang xử lý thông tin ...</td></tr></table>");
						
						dataString = $("#form_gioithieuchuyengia").serialize()
						+ "&cat=gioithieuchuyengia&act=" + $("#gioithieuchuyengia_act").val() + "&"
						+ allFieldsgioithieuchuyengia.serialize();
						dataString +='&hisid=<?php echo $_REQUEST["hisid"];?>';
						
						$.ajax({
							type: "POST",
							url: "gv/gv_gioithieu_chuyengiakhac_khcn_process.php",
							data: dataString,
							dataType: "json",
							success: function(data) {
										if (data.status==1)
										{
											gioithieuchuyengia_RefreshData();	
											$("#tipgioithieuchuyengia").html("");
										}
										else
										{
											$("#tipgioithieuchuyengia").html("");
											if ($('#gioithieuchuyengia_act').val()=='add')
												gv_open_msg_box("Không thể thêm mới, bạn vui lòng thử lại.","alert",250,150);
											else if ($('#gioithieuchuyengia_act').val()=='edit')
												gv_open_msg_box("Không thể cập nhật, bạn vui lòng thử lại.","alert",250,150);
										}
									 }// end function(data)	
						}); // end .ajax
					}
					
					if (bValid) {
						$( this ).dialog( "close" );
					}

				},
				Cancel: function() {
					$( this ).dialog( "close" );
				}
			},
			close: function() {
				allFieldsgioithieuchuyengia.val( "" ).removeClass( "ui-state-error" );
			}
		});
	
    $("#btn_Xoa_gioithieuchuyengia").click(function(){
		$( "#btn_Xoa_gioithieuchuyengia" ).button({ disabled: true });
		dataString = $("#form_gioithieuchuyengia").serialize() + '&cat=gioithieuchuyengia&act=del';
		dataString +='&hisid=<?php echo $_REQUEST["hisid"];?>';
		
		$.ajax({type: "POST",url: "gv/gv_gioithieu_chuyengiakhac_khcn_process.php",data: dataString,dataType: "html",
			success: function(data) {
						gioithieuchuyengia_RefreshData();
						$( "#btn_Xoa_gioithieuchuyengia" ).button({ disabled: false });
					 }// end function(data)	
		}); // end .ajax
	});	// end $("#btn_Xoa_gioithieuchuyengia").click(function()
	
	function gioithieuchuyengia_RefreshData() {
		dataString = "cat=get_gioithieuchuyengia&hisid=<?php echo $_REQUEST["hisid"];?>";
		$.ajax({
			type: "POST",
			url: "gv/gv_gioithieu_chuyengiakhac_khcn_process.php",
			data: dataString,
			dataType: "html",
			success: function(data) {
						$("#table_gioithieuchuyengia tbody").html(data);
					 }// end function(data)	
		}); // end .ajax
	}
	
	
	
	$('input[placeholder],textarea[placeholder]').placeholder();
});

</script>



<?php 
if (isset ($db_conn))
	oci_close($db_conn);
?>