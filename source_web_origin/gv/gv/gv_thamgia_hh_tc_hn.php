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
  
<div id = 'thamgia_hh_tc_hndiv'>
	<form id="form_thamgia_hh_tc_hn" method="post" action="" >
	<input type="hidden" name="mathamgia_hh_tc_hnedit" id="mathamgia_hh_tc_hnedit" />
    <div id = 'formthemthamgia_hh_tc_hndiv' title="Tham gia Hiệp hội/Tạp chí/Hội nghị">
        <table width="100%" border="0" align="center" cellpadding="5" cellspacing="2">
		  <tr>
			<td align="right" class="heading"><label for="txtLoai_thamgia_hh_tc_hn">Loại</label></td>
			<td>
				<select name="txtLoai_thamgia_hh_tc_hn" id="txtLoai_thamgia_hh_tc_hn" class="text ui-widget-content ui-corner-all tableData" style="height:23px;font-size:15px">
				   <option value="">chọn...</option>
				   <option value="H">Hiệp hội khoa học</option>
				   <option value="T">Tạp chí Khoa học</option>
				   <option value="HN">Hội nghị nghiên cứu khoa học</option>
				</select>
			</td>
		  </tr>
		  <tr>
			<td align="right" class="heading"><label for="txtthoigian_thamgia_hh_tc_hn">Thời gian BĐ</label></td>
			<td>
				<input style="width:40px" class="text ui-widget-content ui-corner-all tableData" name="txtthoigian_thamgia_hh_tc_hn" id="txtthoigian_thamgia_hh_tc_hn" type="text" maxlength="4" placeholder=""/>
				<label for="txtthoigian_thamgia_hh_tc_hn_kt" class="heading"> Thời gian KT</label>
				<input style="width:40px" class="text ui-widget-content ui-corner-all tableData" name="txtthoigian_thamgia_hh_tc_hn_kt" id="txtthoigian_thamgia_hh_tc_hn_kt" type="text" maxlength="4" placeholder=""/>
			</td>
		  </tr>
		  
		  <tr class="heading">
            <td align="right" ><label for="txtTenToChuc_thamgia_hh_tc_hn">Tên tổ chức</label></td>
			<td>
				<input style="width:470px" class="text ui-widget-content ui-corner-all tableData" name="txtTenToChuc_thamgia_hh_tc_hn" id="txtTenToChuc_thamgia_hh_tc_hn" type="text" maxlength="200" placeholder="tên Hiệp hội/Tạp chí/Hội nghị"/>
			</td>
          </tr>
		  <tr class="heading">
            <td align="right" ><label for="txtChucDanh_thamgia_hh_tc_hn">Chức danh</label></td>
			<td>
				<input style="width:250px" class="text ui-widget-content ui-corner-all tableData" name="txtChucDanh_thamgia_hh_tc_hn" id="txtChucDanh_thamgia_hh_tc_hn" type="text" maxlength="100" placeholder=""/>
			</td>
          </tr>
		  
        </table>
		
		<div style="margin-top:10px" align="center" id="tipthamgia_hh_tc_hn" class="ui-corner-all validateTips"></div>
			
    </div> <!--end formthemthamgia_hh_tc_hndiv -->
    
				
    	<table border="0" width="100%" cellspacing="0" cellpadding="0">
          <tr>
			<td align="left" >
				<div style="margin-left:5px;font-weight:bold">Tham gia các Hiệp hội khoa học, Ban biên tập các tạp chí Khoa học, Ban tổ chức các Hội nghị về KH&CN, Phản biện tạp chí khoa học, các hội thảo hội nghị quốc tế và trong nước
				</div>
			</td>
            <td align="right" style="width:280px;">
				<div style="margin-bottom:10px;">
						<a id="taothamgia_hh_tc_hn" name="taothamgia_hh_tc_hn" >&nbsp;Thêm tổ chức tham gia...</a>
				&nbsp;&nbsp;
				<a id="btnXoathamgia_hh_tc_hn" name="btnXoathamgia_hh_tc_hn"> &nbsp;Xóa</a>
				</div>
			</td>
          </tr>
        </table>

		<table width="100%" id="tablethamgia_hh_tc_hn" align="center" border="0" cellspacing="0" cellpadding="5" class="ui-widget ui-widget-content ui-corner-top tableData" height="20">
        <thead>
          <tr class="ui-widget-header heading" >
            <td class="ui-corner-tl"  align="left" style="width:30px"><em>TT</em></td>
            <td align="left"><em>Thời gian</em></td>
            <td align="left"><em>Tên Hiệp hội/Tạp chí/Hội nghị</em></td>
			<td align="left"><em>Loại</em></td>
			<td align="left"><em>Chức danh</em></td>
			<td style="width:30px">&nbsp;</td>
            <td class=" ui-corner-tr" style="width:20px">&nbsp;</td>
          </tr>
          </thead>
          <tbody>
		  </tbody>
        </table>
		<input name="thamgia_hh_tc_hn_act" id="thamgia_hh_tc_hn_act" type="hidden" value="" />
</form>
</div>		<!-- end of thamgia_hh_tc_hndiv -->   

<script type="text/javascript">

function getthamgiahh_tc_hn_tghhtchn(pMaTG, pTenHH, pLoai, pChucDanh, pThoigianBD, pThoigianKT)
{
	$("#thamgia_hh_tc_hn_act").val("edit");
	
	$("#mathamgia_hh_tc_hnedit").val(pMaTG);
	document.getElementById('txtTenToChuc_thamgia_hh_tc_hn').value = pTenHH;
	document.getElementById('txtLoai_thamgia_hh_tc_hn').value = pLoai;
	document.getElementById('txtthoigian_thamgia_hh_tc_hn').value = pThoigianBD;
	document.getElementById('txtthoigian_thamgia_hh_tc_hn_kt').value = pThoigianKT;
	document.getElementById('txtChucDanh_thamgia_hh_tc_hn').value = pChucDanh;
	
	$("#formthemthamgia_hh_tc_hndiv").dialog('option', 'title', 'Cập nhật Hiệp hội/Tạp chí/Hội nghị...');
	$("#formthemthamgia_hh_tc_hndiv").dialog('open');
}

//jQuery.ajax
//$(document).ready(function(){
$(function(){

  // delete btn
 $( "#btnXoathamgia_hh_tc_hn" ).button({ icons: {primary:'ui-icon ui-icon-trash'} });
 
 // create new
 $( "#taothamgia_hh_tc_hn" ).button({ icons: {primary:'ui-icon ui-icon-document'} });
 
 // Lay du lieu moi
 thamgia_hh_tc_hn_RefreshData();
 
// Check validate fields Nghien cuu khoa hoc
var jtxtChucDanh_thamgia_hh_tc_hn		= $("#txtChucDanh_thamgia_hh_tc_hn"),
	jtxtTenToChuc_thamgia_hh_tc_hn 		= $("#txtTenToChuc_thamgia_hh_tc_hn"),
	jtxtLoai_thamgia_hh_tc_hn 			= $("#txtLoai_thamgia_hh_tc_hn"),
	jtxtthoigian_thamgia_hh_tc_hn		= $("#txtthoigian_thamgia_hh_tc_hn"),
	jtxtthoigian_thamgia_hh_tc_hn_kt	= $("#txtthoigian_thamgia_hh_tc_hn_kt"),
	jmathamgia_hh_tc_hnedit				= $("#mathamgia_hh_tc_hnedit"),
	allFieldsthamgia_hh_tc_hn = $([]).add(jtxtChucDanh_thamgia_hh_tc_hn).add(jtxtTenToChuc_thamgia_hh_tc_hn).add(jtxtLoai_thamgia_hh_tc_hn).add(jtxtthoigian_thamgia_hh_tc_hn).add(jtxtthoigian_thamgia_hh_tc_hn_kt),
	tipsthamgia_hh_tc_hn					= $("#tipthamgia_hh_tc_hn");
	
	function thamgia_hh_tc_hn_updateTips( t ) {
		tipsthamgia_hh_tc_hn
					.text( t )
					.addClass( "ui-state-highlight" );
		setTimeout(function() {
			tipsthamgia_hh_tc_hn.removeClass( "ui-state-highlight", 1500 );
		}, 1000 );
	}
	
	// thamgia_hh_tc_hn_checkLength
	function thamgia_hh_tc_hn_checkLength( o, n, min, max) {
		if (min==0 && (o.val().length==0))
		{	
			o.addClass( "ui-state-error" );
			o.focus();	
			thamgia_hh_tc_hn_updateTips( "Thông tin " + n + " không được phép để trống.");
			
			return false;
		}else if (min==max && o.val().length<min){
			o.addClass( "ui-state-error" );
			o.focus();	
			thamgia_hh_tc_hn_updateTips( "Thông tin " + n + " phải đủ " + min + " ký tự.");
		}else if ( o.val().length > max || o.val().length < min ) {
			o.addClass( "ui-state-error" );
			o.focus();		
			thamgia_hh_tc_hn_updateTips( "Chiều dài của " + n + " từ " +
						min + " đến " + max + " ký tự.");
			return false;
		} else {
			return true;
		}
	}
	
	// Check Regexp
	function thamgia_hh_tc_hn_checkRegexp( o, regexp, n ) {
		//alert('a');
		if ( o.val()!='' && !( regexp.test( o.val() ) ) ) {
			o.addClass( "ui-state-error" );
			o.focus();
			thamgia_hh_tc_hn_updateTips( n );
			return false;
		} else {
			return true;
		
		}
	}
// End of check validate
	
	// Post du lieu cho nghien cuu khoa hoc
	$("#taothamgia_hh_tc_hn").click(function(){
		 $('#thamgia_hh_tc_hn_act').val('add');
		 $("#formthemthamgia_hh_tc_hndiv").dialog('option', 'title', 'Thêm Hiệp hội/Tạp chí/Hội nghị...');
		 $("#formthemthamgia_hh_tc_hndiv").dialog('open');
	});
	
	$( "#formthemthamgia_hh_tc_hndiv" ).dialog({
			autoOpen: false,
			height: 290,
			width: 610,
			modal: true,
			buttons: {
				"Ok": function() {
					var bValid = true;
					
					allFieldsthamgia_hh_tc_hn.removeClass( "ui-state-error" );

					bValid = bValid && thamgia_hh_tc_hn_checkLength( jtxtLoai_thamgia_hh_tc_hn, "\"Loại tổ chức\"", 0, 2);
					bValid = bValid && thamgia_hh_tc_hn_checkLength( jtxtthoigian_thamgia_hh_tc_hn, "\"Năm bắt đầu tham gia\"", 4, 4);
					bValid = bValid && thamgia_hh_tc_hn_checkRegexp( jtxtthoigian_thamgia_hh_tc_hn,/^[0-9]{4,4}$/i, "Thông tin \"Năm bắt đầu tham gia\" phải là Số");
					if (jtxtthoigian_thamgia_hh_tc_hn_kt.val()!="")
					{
						bValid = bValid && thamgia_hh_tc_hn_checkLength( jtxtthoigian_thamgia_hh_tc_hn_kt, "\"Năm kết thúc tham gia\"", 4, 4);
						bValid = bValid && thamgia_hh_tc_hn_checkRegexp( jtxtthoigian_thamgia_hh_tc_hn_kt,/^[0-9]{4,4}$/i, "Thông tin \"Năm kết thúc tham gia\" phải là Số");
						if (jtxtthoigian_thamgia_hh_tc_hn_kt.val()<jtxtthoigian_thamgia_hh_tc_hn.val()){
							bValid = false;
							jtxtthoigian_thamgia_hh_tc_hn_kt.focus();
							jtxtthoigian_thamgia_hh_tc_hn_kt.addClass( "ui-state-error" );
							thamgia_hh_tc_hn_updateTips("Năm kết thúc phải lớn hơn năm bắt đầu");
						}
					}
					
					bValid = bValid && thamgia_hh_tc_hn_checkLength( jtxtTenToChuc_thamgia_hh_tc_hn, "\"Tên tổ chức tham gia\"", 0, 200);
					//bValid = bValid && thamgia_hh_tc_hn_checkLength( jtxtChucDanh_thamgia_hh_tc_hn, "\"Chức danh\"", 0, 100);
					
					if (bValid) {
						
						$("#tipthamgia_hh_tc_hn").html("<table><tr><td ><img src='../images/ajax-loader.gif'></td><td valign=top style='color: black'> Đang xử lý thông tin ...</td></tr></table>");
						
						dataString = $("#form_thamgia_hh_tc_hn").serialize()
						+ "&cat=thamgia_hh_tc_hn&act=" + $("#thamgia_hh_tc_hn_act").val() + "&"
						+ allFieldsthamgia_hh_tc_hn.serialize();
						dataString +='&hisid=<?php echo $_REQUEST["hisid"];?>';
						
						//alert(dataString);
						$.ajax({
							type: "POST",
							url: "gv/gv_thamgia_hh_tc_hn_process.php",
							data: dataString,
							dataType: "json",
							success: function(data) {
										if (data.status==1)
										{
											thamgia_hh_tc_hn_RefreshData();	
											$("#tipthamgia_hh_tc_hn").html("");
										}
										else
										{
											$("#tipthamgia_hh_tc_hn").html("");
											if ($('#thamgia_hh_tc_hn_act').val()=='add')
												gv_open_msg_box("Không thể thêm tổ chức tham gia, bạn vui lòng thử lại.","alert",250,150);
											else if ($('#thamgia_hh_tc_hn_act').val()=='edit')
												gv_open_msg_box("Không thể cập nhật tổ chức tham gia, bạn vui lòng thử lại.","alert",250,150);
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
				allFieldsthamgia_hh_tc_hn.val( "" ).removeClass( "ui-state-error" );
			}
		});
	
    $("#btnXoathamgia_hh_tc_hn").click(function(){
		$( "#btnXoathamgia_hh_tc_hn" ).button({ disabled: true });
		dataString = $("#form_thamgia_hh_tc_hn").serialize() + '&cat=thamgia_hh_tc_hn&act=del';
		dataString +='&hisid=<?php echo $_REQUEST["hisid"];?>';
		
		$.ajax({type: "POST",url: "gv/gv_thamgia_hh_tc_hn_process.php",data: dataString,dataType: "html",
			success: function(data) {
						thamgia_hh_tc_hn_RefreshData();
						$( "#btnXoathamgia_hh_tc_hn" ).button({ disabled: false });
					 }// end function(data)	
		}); // end .ajax
	});	// end $("#btnXoathamgia_hh_tc_hn").click(function()
	
	function thamgia_hh_tc_hn_RefreshData() {
		dataString = "cat=get_thamgia_hh_tc_hn&hisid=<?php echo $_REQUEST["hisid"];?>";
		$.ajax({
			type: "POST",
			url: "gv/gv_thamgia_hh_tc_hn_process.php",
			data: dataString,
			dataType: "html",
			success: function(data) {
						$("#tablethamgia_hh_tc_hn tbody").html(data);
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