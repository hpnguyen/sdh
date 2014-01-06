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

<div id="thanhtuukhcndiv">     
	<form id="form_thanhtuukhcn" method="post" action="" >
          <div id="formthemthanhtuukhcndiv" title="Hướng, đề tài nghiên cứu">
                           
                     <table width="100%" border="0" cellspacing="2" cellpadding="5" >
                      <tr align="left" class="heading">
                        <td width="13%"><label for="txtNam_thanhtuukhcn">Năm </label></td>
                        <td ><label for="txtNoidung_thanhtuukhcn">Nội dung thành tựu hoạt động KH&CN </label></td>
                        </tr>
                      <tr align="left" valign="top">
                        <td >
                          <input name="txtNam_thanhtuukhcn" type="text"  id="txtNam_thanhtuukhcn" size="6" maxlength="4" class="text ui-widget-content ui-corner-all tableData"/>
                          <input name="mathanhtuukhcnedit" type="hidden" id="mathanhtuukhcnedit" />
                        </td>
                        <td width="87%"><textarea class="text ui-widget-content ui-corner-all tableData" name="txtNoidung_thanhtuukhcn" id="txtNoidung_thanhtuukhcn" cols="45" rows="10" ></textarea>
          
                        </td>
                        </tr>

                      
                    </table>
					<div align="center" id="tipThanhtuuKHCN" class="ui-corner-all validateTips ui-widget-header"></div>
       
          </div>
          
  
    <table border="0" cellspacing="0" cellpadding="0" width="100%">
		<tr>
			<td align=left>
				<div style="margin-left:5px;font-weight:bold">Thành tựu hoạt động KH&CN và sản xuất kinh doanh khác</div>
			</td>
			<td align="right">
				<div style="margin-bottom:10px;">
					<a name="btnTaoMoiThanhTuuKHCN" id="btnTaoMoiThanhTuuKHCN">&nbsp;Thêm mới...</a>
				  &nbsp;&nbsp;
				  <a id="btnXoaThanhTuuKHCN" name="btnXoaThanhTuuKHCN">&nbsp;Xóa</a>
				</div>
			</td>
        </tr>
    </table>
    
    
    <table id="tablethanhtuukhcn" width="100%" border="0" align="center" cellpadding="5" cellspacing="0" class="ui-widget ui-widget-content ui-corner-top tableData" height="20">
      <thead>
        <tr class="ui-widget-header heading" >
          <td width="30" align="left" valign="middle" class=" ui-corner-tl"><em>Năm</em></td>
          <td width="574" align="left" valign="middle" ><em>Nội dung hoạt động KH&CN và sản xuất kinh doanh khác</em></td>
          <td width="36" valign="middle" >&nbsp;</td>
          <td width="39" align="left" valign="middle" class=" ui-corner-tr">&nbsp;</td>
          </tr>
        </thead>
		<tbody>
        </tbody>
      
      </table>
		
    </form>
	<input name="thanhtuukhcn_act" id="thanhtuukhcn_act" type="hidden" value="" />
  </div>   <!-- end of "thanhtuukhcndiv" -->     

<script type="text/javascript">
//jQuery.ajax
//$(document).ready(function(){

function getThanhtuukhcn(pma, pnoidung, pnam){
	//alert(index);
	$('#thanhtuukhcn_act').val('edit');
	
	$("#txtNam_thanhtuukhcn").val(pnam); 
	$("#txtNoidung_thanhtuukhcn").val(pnoidung);
	$("#mathanhtuukhcnedit").val(pma);
	$( "#formthemthanhtuukhcndiv" ).dialog('option', 'title', 'Cập nhật thành tựu hoạt động KH&CN...');
	$( "#formthemthanhtuukhcndiv" ).dialog('open');
}

$(function(){

  // delete btn
 $( "#btnXoaThanhTuuKHCN" ).button({ icons: {primary:'ui-icon ui-icon-trash'} });
 
 // create new
 $( "#btnTaoMoiThanhTuuKHCN" ).button({ icons: {primary:'ui-icon ui-icon-document'} });
 
 thanhtuukhcn_RefreshData();
 
var jtxtNam_thanhtuukhcn 		= $("#txtNam_thanhtuukhcn"),
	jtxtNoidung_thanhtuukhcn	= $("#txtNoidung_thanhtuukhcn"),
	jmathanhtuukhcnedit			= $("#mathanhtuukhcnedit"),
	allFieldsDT 	= $( [] ).add(jtxtNam_thanhtuukhcn).add(jtxtNoidung_thanhtuukhcn).add(jmathanhtuukhcnedit),
	detai_tips 		= $("#tipThanhtuuKHCN");
		
	// 
	function thanhtuukhcn_updateTips( t ) {
		detai_tips
					.text( t )
					.addClass( "ui-state-highlight" );
		setTimeout(function() {
			detai_tips.removeClass( "ui-state-highlight", 1500 );
		}, 1000 );
	}
	
	
	function thanhtuukhcn_checkLength( o, n, min, max) {
		if (min==0 && (o.val().length==0))
		{	
			o.addClass( "ui-state-error" );
			o.focus();	
			thanhtuukhcn_updateTips( "Thông tin " + n + " không được phép để trống.");
			
			return false;
		}else if (min==max && o.val().length<min){
			o.addClass( "ui-state-error" );
			o.focus();	
			thanhtuukhcn_updateTips( "Thông tin " + n + " phải đủ " + min + " ký tự.");
		}else if ( o.val().length > max || o.val().length < min ) {
			o.addClass( "ui-state-error" );
			o.focus();		
			thanhtuukhcn_updateTips( "Chiều dài của " + n + " từ " +
						min + " đến " + max + " ký tự.");
			return false;
		} else {
			return true;
		}
	}
	
	// Check Regexp
	function thanhtuukhcn_checkRegexp( o, regexp, n ) {
		//alert('a');
		if ( o.val()!='' && !( regexp.test( o.val() ) ) ) {
			o.addClass( "ui-state-error" );
			o.focus();
			thanhtuukhcn_updateTips( n );
			return false;
		} else {
			return true;
		
		}
	}
// End of check validate
	
	
	
	// Post du lieu cho de tai
	$("#btnTaoMoiThanhTuuKHCN").click(function() {
		$("#thanhtuukhcn_act").val("add");
		$("#formthemthanhtuukhcndiv").dialog('option', 'title', 'Thêm thành tựu hoạt động KH&CN...');
		$("#formthemthanhtuukhcndiv").dialog( "open" );
	});
	
	$("#btnXoaThanhTuuKHCN").click(function(){
		$( "#btnXoaThanhTuuKHCN" ).button({ disabled: true });
		dataString = $("#form_thanhtuukhcn").serialize()+'&cat=thanhtuukhcn&act=del';
		dataString +='&hisid=<?php echo $_REQUEST["hisid"];?>';
		$.post("gv/gv_thanhtuukhcn_process.php", dataString,
		function(data){
			if (data.status==1)
			{
				thanhtuukhcn_RefreshData();	
				$("#tipThanhtuuKHCN").html("");
			}
			else
			{
				$("#tipThanhtuuKHCN").html("");
				gv_open_msg_box("Không thể xóa, vui lòng thử lại.","alert",250,150);
			}
			$( "#btnXoaThanhTuuKHCN" ).button({ disabled: false });
		}, "json");
	});	// end $("#btnXoaThanhTuuKHCN").click(function()
	
	$( "#formthemthanhtuukhcndiv" ).dialog({
			autoOpen: false,
			height: 360,
			width: 370,
			modal: true,
			buttons: {
				"Ok": function() {
					var bValid = true;
					
					allFieldsDT.removeClass( "ui-state-error" );
					bValid = bValid && thanhtuukhcn_checkLength( jtxtNam_thanhtuukhcn, "\"Năm thực hiện\"", 4, 4);
					bValid = bValid && thanhtuukhcn_checkRegexp( jtxtNam_thanhtuukhcn,/^[0-9]{4,4}$/i, "Thông tin năm phải là Số");
					bValid = bValid && thanhtuukhcn_checkLength( jtxtNoidung_thanhtuukhcn, "\"Thành tựu hoạt động KH&CN\"", 0, 1000);
					if (bValid){
						
						datastring = $("#form_thanhtuukhcn").serialize()
						+ '&cat=thanhtuukhcn&act='+$('#thanhtuukhcn_act').val()+'&'
						+ allFieldsDT.serialize();
						datastring +='&hisid=<?php echo $_REQUEST["hisid"];?>';
 						
						//alert(datastring);
						
						$.post("gv/gv_thanhtuukhcn_process.php", datastring ,
						function(data){
							if (data.status==1)
							{
								thanhtuukhcn_RefreshData();	
								$("#tipThanhtuuKHCN").html("");
							}
							else
							{
								$("#tipThanhtuuKHCN").html("");
								if ($('#thanhtuukhcn_act').val()=='add')
									gv_open_msg_box("Không thể thêm mới, bạn vui lòng thử lại.","alert",250,150);
								else if ($('#thanhtuukhcn_act').val()=='edit')
									gv_open_msg_box("Không thể cập nhật, bạn vui lòng thử lại.","alert",250,150);
							}
						}, "json");
					}

					if ( bValid ) {
						$( this ).dialog( "close" );
					}
				},
				Cancel: function() {
					$( this ).dialog( "close" );
				}
			},
			close: function() {
				allFieldsDT.val( "" ).removeClass( "ui-state-error" );
			}
	});
	
	function thanhtuukhcn_RefreshData() {
		dataString = "cat=get_thanhtuukhcn&hisid=<?php echo $_REQUEST["hisid"];?>";
		$.ajax({
			type: "POST",
			url: "gv/gv_thanhtuukhcn_process.php",
			data: dataString,
			dataType: "html",
			success: function(data) {
						$("#tablethanhtuukhcn tbody").html(data);
					 }// end function(data)	
		}); // end .ajax
	}

});

</script>

<?php 

if (isset ($db_conn))
	oci_close($db_conn);
?>