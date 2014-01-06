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
  
<div id = 'kinhnghiemqldiv'>
	
	<form id="form_kinhnghiemql" method="post" action="" >
	<input type="hidden" name="makinhnghiemqledit" id="makinhnghiemqledit" />
    <div id = 'formthemkinhnghiemqldiv' title="Tham gia chương trình">
        <table width="100%" border="0" align="center" cellpadding="5" cellspacing="2">	  
		  <tr>
			<td align="right" class="heading"><label for="txtThoiGian_kinhnghiemql">Năm tham gia</label></td>
			<td>
				<input style="width:40px" class="text ui-widget-content ui-corner-all tableData" name="txtThoiGian_kinhnghiemql" id="txtThoiGian_kinhnghiemql" type="text" maxlength="4" placeholder=""/>
			</td>
		  </tr>
		  
		  <tr class="heading">
            <td align="right" ><label for="txtHinhThucHoiDong_kinhnghiemql">Hình thức hội đồng</label></td>
			<td>
				<input style="width:450px" class="text ui-widget-content ui-corner-all tableData" name="txtHinhThucHoiDong_kinhnghiemql" id="txtHinhThucHoiDong_kinhnghiemql" type="text" maxlength="500" placeholder=""/>
			</td>
          </tr>
		  <tr class="heading">
            <td align="right" ><label for="txtGhiChu_kinhnghiemql">Ghi chú</label></td>
			<td>
				<input style="width:450px" class="text ui-widget-content ui-corner-all tableData" name="txtGhiChu_kinhnghiemql" id="txtGhiChu_kinhnghiemql" type="text" maxlength="100" placeholder=""/>
			</td>
          </tr>
		  
        </table>
		
		<div style="margin-top:10px" align="center" id="tipkinhnghiemql" class="ui-corner-all validateTips"></div>
			
    </div> <!--end formthemkinhnghiemqldiv -->
       
    	<table border="0" width="100%" cellspacing="0" cellpadding="0">
          <tr>
			<td align=left>
			<div style="margin-left:5px;font-weight:bold">
				Số lượng các Hội đồng tư vấn, xét duyệt, nghiệm thu, đánh giá các chương trình, đề tài, dự án KH&CN cấp nhà nước đã tham gia
			</div>
			</td>
            <td align="right" style="width:360px" >
				<div style="margin-bottom:10px;">
						<a id="btn_taomoi_kinhnghiemql" name="btn_taomoi_kinhnghiemql" >&nbsp;Thêm mới...</a>
				&nbsp;&nbsp;
				<a id="btn_Xoa_kinhnghiemql" name="btn_Xoa_kinhnghiemql"> &nbsp;Xóa</a>
				</div>
			</td>
          </tr>
        </table>

		<table width="100%" id="table_kinhnghiemql" align="center" border="0" cellspacing="0" cellpadding="5" class="ui-widget ui-widget-content ui-corner-top tableData" height="20">
        <thead>
          <tr class="ui-widget-header heading" >
            <td class="ui-corner-tl"  align="left" style="width:30px"><em>TT</em></td>
            <td align="left"><em>Hình thức hội đồng</em></td>
			<td align="left"><em>Thời gian</em></td>
			<td align="left"><em>Ghi chú</em></td>
			<td style="width:30px">&nbsp;</td>
            <td class=" ui-corner-tr" style="width:20px">&nbsp;</td>
          </tr>
          </thead>
          <tbody>
		  </tbody>
        </table>
		<input name="kinhnghiemql_act" id="kinhnghiemql_act" type="hidden" value="" />
</form>
</div>		<!-- end of kinhnghiemqldiv -->   

<script type="text/javascript">

function getinfo_kinhnghiemql(pMaKNQL, pHinhThucHD, pGhiChu, pNamTG)
{
	$("#kinhnghiemql_act").val("edit");
	
	$("#makinhnghiemqledit").val(pMaKNQL);
	document.getElementById('txtHinhThucHoiDong_kinhnghiemql').value = pHinhThucHD;
	document.getElementById('txtThoiGian_kinhnghiemql').value = pNamTG;
	document.getElementById('txtGhiChu_kinhnghiemql').value = pGhiChu;
	
	$("#formthemkinhnghiemqldiv").dialog('option', 'title', 'Cập nhật kinh nghiệm quản lý, đánh giá KH&CN...');
	$("#formthemkinhnghiemqldiv").dialog('open');
}

//jQuery.ajax
//$(document).ready(function(){
$(function(){

  // delete btn
 $( "#btn_Xoa_kinhnghiemql" ).button({ icons: {primary:'ui-icon ui-icon-trash'} });
 
 // create new
 $( "#btn_taomoi_kinhnghiemql" ).button({ icons: {primary:'ui-icon ui-icon-document'} });
 
 // Lay du lieu moi
 kinhnghiemql_RefreshData();
 
// Check validate fields Nghien cuu khoa hoc
var jtxtGhiChu_kinhnghiemql			= $("#txtGhiChu_kinhnghiemql"),
	jtxtHinhThucHoiDong_kinhnghiemql= $("#txtHinhThucHoiDong_kinhnghiemql"),
	jtxtThoiGian_kinhnghiemql		= $("#txtThoiGian_kinhnghiemql"),
	jmakinhnghiemqledit				= $("#makinhnghiemqledit"),
	allFieldskinhnghiemql = $([]).add(jtxtGhiChu_kinhnghiemql).add(jtxtHinhThucHoiDong_kinhnghiemql).add(jtxtThoiGian_kinhnghiemql),
	tipskinhnghiemql				= $("#tipkinhnghiemql");
	
	function kinhnghiemql_updateTips( t ) {
		tipskinhnghiemql
					.text( t )
					.addClass( "ui-state-highlight" );
		setTimeout(function() {
			tipskinhnghiemql.removeClass( "ui-state-highlight", 1500 );
		}, 1000 );
	}
	
	// kinhnghiemql_checkLength
	function kinhnghiemql_checkLength( o, n, min, max) {
		if (min==0 && (o.val().length==0))
		{	
			o.addClass( "ui-state-error" );
			o.focus();	
			kinhnghiemql_updateTips( "Thông tin " + n + " không được phép để trống.");
			
			return false;
		}else if (min==max && o.val().length<min){
			o.addClass( "ui-state-error" );
			o.focus();	
			kinhnghiemql_updateTips( "Thông tin " + n + " phải đủ " + min + " ký tự.");
		}else if ( o.val().length > max || o.val().length < min ) {
			o.addClass( "ui-state-error" );
			o.focus();		
			kinhnghiemql_updateTips( "Chiều dài của " + n + " từ " +
						min + " đến " + max + " ký tự.");
			return false;
		} else {
			return true;
		}
	}
	
	// Check Regexp
	function kinhnghiemql_checkRegexp( o, regexp, n ) {
		//alert('a');
		if ( o.val()!='' && !( regexp.test( o.val() ) ) ) {
			o.addClass( "ui-state-error" );
			o.focus();
			kinhnghiemql_updateTips( n );
			return false;
		} else {
			return true;
		
		}
	}
// End of check validate
	
	// Post du lieu cho nghien cuu khoa hoc
	$("#btn_taomoi_kinhnghiemql").click(function(){
		 $('#kinhnghiemql_act').val('add');
		 $("#formthemkinhnghiemqldiv").dialog('option', 'title', 'Tạo mới kinh nghiệm quản lý, đánh giá KH&CN...');
		 $("#formthemkinhnghiemqldiv").dialog('open');
	});
	
	$( "#formthemkinhnghiemqldiv" ).dialog({
			autoOpen: false,
			height: 250,
			width: 630,
			modal: true,
			buttons: {
				"Ok": function() {
					var bValid = true;
					
					allFieldskinhnghiemql.removeClass( "ui-state-error" );

					bValid = bValid && kinhnghiemql_checkLength( jtxtThoiGian_kinhnghiemql, "\"Năm tham gia\"", 4, 4);
					bValid = bValid && kinhnghiemql_checkRegexp( jtxtThoiGian_kinhnghiemql,/^[0-9]{4,4}$/i, "Thông tin \"Năm tham gia\" phải là Số");
									
					bValid = bValid && kinhnghiemql_checkLength( jtxtHinhThucHoiDong_kinhnghiemql, "\"Hình thức hội đồng\"", 0, 500);
					bValid = bValid && kinhnghiemql_checkLength( jtxtGhiChu_kinhnghiemql, "\"Ghi chú\"", 0, 100);
					
										
					if (bValid) {
						
						$("#tipkinhnghiemql").html("<table><tr><td ><img src='../images/ajax-loader.gif'></td><td valign=top style='color: black'> Đang xử lý thông tin ...</td></tr></table>");
						
						dataString = $("#form_kinhnghiemql").serialize()
						+ "&cat=kinhnghiemql&act=" + $("#kinhnghiemql_act").val() + "&"
						+ allFieldskinhnghiemql.serialize();
						dataString +='&hisid=<?php echo $_REQUEST["hisid"];?>';
						
						//alert(dataString);
						$.ajax({
							type: "POST",
							url: "gv/gv_kinhnghiem_ql_dg_khcn_process.php",
							data: dataString,
							dataType: "json",
							success: function(data) {
										if (data.status==1)
										{
											kinhnghiemql_RefreshData();	
											$("#tipkinhnghiemql").html("");
										}
										else
										{
											$("#tipkinhnghiemql").html("");
											if ($('#kinhnghiemql_act').val()=='add')
												gv_open_msg_box("Không thể thêm mới, bạn vui lòng thử lại.","alert",250,150);
											else if ($('#kinhnghiemql_act').val()=='edit')
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
				allFieldskinhnghiemql.val( "" ).removeClass( "ui-state-error" );
			}
		});
	
    $("#btn_Xoa_kinhnghiemql").click(function(){
		$( "#btn_Xoa_kinhnghiemql" ).button({ disabled: true });
		dataString = $("#form_kinhnghiemql").serialize() + '&cat=kinhnghiemql&act=del';
		dataString +='&hisid=<?php echo $_REQUEST["hisid"];?>';
		
		$.ajax({type: "POST",url: "gv/gv_kinhnghiem_ql_dg_khcn_process.php",data: dataString,dataType: "html",
			success: function(data) {
						kinhnghiemql_RefreshData();
						$( "#btn_Xoa_kinhnghiemql" ).button({ disabled: false });
					 }// end function(data)	
		}); // end .ajax
	});	// end $("#btn_Xoa_kinhnghiemql").click(function()
	
	function kinhnghiemql_RefreshData() {
		dataString = "cat=get_kinhnghiemql&hisid=<?php echo $_REQUEST["hisid"];?>";
		$.ajax({
			type: "POST",
			url: "gv/gv_kinhnghiem_ql_dg_khcn_process.php",
			data: dataString,
			dataType: "html",
			success: function(data) {
						$("#table_kinhnghiemql tbody").html(data);
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