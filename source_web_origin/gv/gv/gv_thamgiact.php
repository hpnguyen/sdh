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
  
<div id = 'thamgiactdiv'>
	
	<form id="form_thamgiact" method="post" action="" >
	<input type="hidden" name="mathamgiactedit" id="mathamgiactedit" />
    <div id = 'formthemthamgiactdiv' title="Tham gia chương trình">
        <table width="100%" border="0" align="center" cellpadding="5" cellspacing="2">	  
		  <tr>
			<td align="right" class="heading"><label for="txtthoigianbd_thamgiact">Năm bắt đầu</label></td>
			<td>
				<input style="width:40px" class="text ui-widget-content ui-corner-all tableData" name="txtthoigianbd_thamgiact" id="txtthoigianbd_thamgiact" type="text" maxlength="4" placeholder=""/>
				<label for="txtthoigiankt_thamgiact" class="heading"> Năm kết thúc</label>
				<input style="width:40px" class="text ui-widget-content ui-corner-all tableData" name="txtthoigiankt_thamgiact" id="txtthoigiankt_thamgiact" type="text" maxlength="4" placeholder=""/>
				<label for="txtNuocNgoai_thamgiact" class="heading"> Tổ chức ở</label>
				<select name="txtNuocNgoai_thamgiact" id="txtNuocNgoai_thamgiact" class="text ui-widget-content ui-corner-all tableData" style="height:23px;font-size:15px">
				   <option value="">chọn...</option>
				   <option value="0">trong nước</option>
				   <option value="1">nước ngoài</option>
				</select>
			</td>
		  </tr>
		  
		  <tr class="heading">
            <td align="right" ><label for="txtTenChuongTrinh_thamgiact">Tên chương trình</label></td>
			<td>
				<input style="width:470px" class="text ui-widget-content ui-corner-all tableData" name="txtTenChuongTrinh_thamgiact" id="txtTenChuongTrinh_thamgiact" type="text" maxlength="200" placeholder="tham gia chương trình hoặc thành viên ban chủ nhiệm chương trình"/>
			</td>
          </tr>
		  <tr class="heading">
            <td align="right" ><label for="txtChucDanh_thamgiact">Chức danh</label></td>
			<td>
				<input style="width:250px" class="text ui-widget-content ui-corner-all tableData" name="txtChucDanh_thamgiact" id="txtChucDanh_thamgiact" type="text" maxlength="100" placeholder=""/>
			</td>
          </tr>
		  
        </table>
		
		<div style="margin-top:10px" align="center" id="tipthamgiact" class="ui-corner-all validateTips"></div>
			
    </div> <!--end formthemthamgiactdiv -->
    
	
    
    	<table border="0" width="100%" cellspacing="0" cellpadding="0">
          <tr>
			<td>
			<div style="margin-left:5px;font-weight:bold">
				Tham gia các chương trình trong và ngoài nước, thành viên ban chủ nhiệm các chương trình
			</div>
			</td>
            <td align="right" >
				<div style="margin-bottom:10px;">
						<a id="taothamgiact" name="taothamgiact" >&nbsp;Thêm chương trình tham gia...</a>
				&nbsp;&nbsp;
				<a id="btnXoathamgiact" name="btnXoathamgiact"> &nbsp;Xóa</a>
				</div>
			</td>
          </tr>
        </table>

		<table width="100%" id="tablethamgiact" align="center" border="0" cellspacing="0" cellpadding="5" class="ui-widget ui-widget-content ui-corner-top tableData" height="20">
        <thead>
          <tr class="ui-widget-header heading" >
            <td class="ui-corner-tl"  align="left" style="width:30px"><em>TT</em></td>
            <td align="left"><em>Thời gian</em></td>
            <td align="left"><em>Tên chương trình</em></td>
			<td align="left"><em>Tổ chức tại</em></td>
			<td align="left"><em>Chức danh</em></td>
			<td style="width:30px">&nbsp;</td>
            <td class=" ui-corner-tr" style="width:20px">&nbsp;</td>
          </tr>
          </thead>
          <tbody>
		  </tbody>
        </table>
		<input name="thamgiact_act" id="thamgiact_act" type="hidden" value="" />
</form>
</div>		<!-- end of thamgiactdiv -->   

<script type="text/javascript">

function getthamgiact_tgct(pMaTG, pTenCT, pChucDanh, pThoigianBD, pThoigianKT, pNuocNgoai)
{
	$("#thamgiact_act").val("edit");
	
	$("#mathamgiactedit").val(pMaTG);
	document.getElementById('txtTenChuongTrinh_thamgiact').value = pTenCT;
	document.getElementById('txtNuocNgoai_thamgiact').value = pNuocNgoai;
	document.getElementById('txtthoigianbd_thamgiact').value = pThoigianBD;
	document.getElementById('txtthoigiankt_thamgiact').value = pThoigianKT;
	document.getElementById('txtChucDanh_thamgiact').value = pChucDanh;
	
	$("#formthemthamgiactdiv").dialog('option', 'title', 'Cập nhật chương trình tham gia...');
	$("#formthemthamgiactdiv").dialog('open');
}

//jQuery.ajax
//$(document).ready(function(){
$(function(){

  // delete btn
 $( "#btnXoathamgiact" ).button({ icons: {primary:'ui-icon ui-icon-trash'} });
 
 // create new
 $( "#taothamgiact" ).button({ icons: {primary:'ui-icon ui-icon-document'} });
 
 // Lay du lieu moi
 thamgiact_RefreshData();
 
// Check validate fields Nghien cuu khoa hoc
var jtxtChucDanh_thamgiact			= $("#txtChucDanh_thamgiact"),
	jtxtTenChuongTrinh_thamgiact 	= $("#txtTenChuongTrinh_thamgiact"),
	jtxtNuocNgoai_thamgiact 		= $("#txtNuocNgoai_thamgiact"),
	jtxtthoigiankt_thamgiact		= $("#txtthoigiankt_thamgiact"),
	jtxtthoigianbd_thamgiact		= $("#txtthoigianbd_thamgiact"),
	jmathamgiactedit				= $("#mathamgiactedit"),
	allFieldsthamgiact = $([]).add(jtxtChucDanh_thamgiact).add(jtxtTenChuongTrinh_thamgiact).add(jtxtNuocNgoai_thamgiact).add(jtxtthoigianbd_thamgiact).add(jtxtthoigiankt_thamgiact),
	tipsthamgiact					= $("#tipthamgiact");
	
	function thamgiact_updateTips( t ) {
		tipsthamgiact
					.text( t )
					.addClass( "ui-state-highlight" );
		setTimeout(function() {
			tipsthamgiact.removeClass( "ui-state-highlight", 1500 );
		}, 1000 );
	}
	
	// thamgiact_checkLength
	function thamgiact_checkLength( o, n, min, max) {
		if (min==0 && (o.val().length==0))
		{	
			o.addClass( "ui-state-error" );
			o.focus();	
			thamgiact_updateTips( "Thông tin " + n + " không được phép để trống.");
			
			return false;
		}else if (min==max && o.val().length<min){
			o.addClass( "ui-state-error" );
			o.focus();	
			thamgiact_updateTips( "Thông tin " + n + " phải đủ " + min + " ký tự.");
		}else if ( o.val().length > max || o.val().length < min ) {
			o.addClass( "ui-state-error" );
			o.focus();		
			thamgiact_updateTips( "Chiều dài của " + n + " từ " +
						min + " đến " + max + " ký tự.");
			return false;
		} else {
			return true;
		}
	}
	
	// Check Regexp
	function thamgiact_checkRegexp( o, regexp, n ) {
		//alert('a');
		if ( o.val()!='' && !( regexp.test( o.val() ) ) ) {
			o.addClass( "ui-state-error" );
			o.focus();
			thamgiact_updateTips( n );
			return false;
		} else {
			return true;
		
		}
	}
// End of check validate
	
	// Post du lieu cho nghien cuu khoa hoc
	$("#taothamgiact").click(function(){
		 $('#thamgiact_act').val('add');
		 $("#formthemthamgiactdiv").dialog('option', 'title', 'Thêm chương trình tham gia...');
		 $("#formthemthamgiactdiv").dialog('open');
	});
	
	$( "#formthemthamgiactdiv" ).dialog({
			autoOpen: false,
			height: 250,
			width: 630,
			modal: true,
			buttons: {
				"Ok": function() {
					var bValid = true;
					
					allFieldsthamgiact.removeClass( "ui-state-error" );

					bValid = bValid && thamgiact_checkLength( jtxtthoigianbd_thamgiact, "\"Năm bắt đầu tham gia\"", 4, 4);
					bValid = bValid && thamgiact_checkRegexp( jtxtthoigianbd_thamgiact,/^[0-9]{4,4}$/i, "Thông tin \"Năm bắt đầu tham gia\" phải là Số");
					
					if (jtxtthoigiankt_thamgiact.val()!="")
					{
						//alert('1');
						bValid = bValid && thamgiact_checkLength( jtxtthoigiankt_thamgiact, "\"Năm kết thúc tham gia\"", 4, 4);
						bValid = bValid && thamgiact_checkRegexp( jtxtthoigiankt_thamgiact,/^[0-9]{4,4}$/i, "Thông tin \"Năm kết thúc tham gia\" phải là Số");
						if (jtxtthoigiankt_thamgiact.val()<jtxtthoigianbd_thamgiact.val()){
							bValid = false;
							jtxtthoigiankt_thamgiact.focus();
							jtxtthoigiankt_thamgiact.addClass( "ui-state-error" );
							thamgiact_updateTips("Năm kết thúc phải lớn hơn năm bắt đầu");
						}
					}
					
					bValid = bValid && thamgiact_checkLength( jtxtNuocNgoai_thamgiact, "\"tổ chức ở\"", 0, 1);
					bValid = bValid && thamgiact_checkLength( jtxtTenChuongTrinh_thamgiact, "\"Tên chương trình\"", 0, 200);
					//bValid = bValid && thamgiact_checkLength( jtxtChucDanh_thamgiact, "\"Chức danh\"", 0, 100);
					
										
					if (bValid) {
						
						$("#tipthamgiact").html("<table><tr><td ><img src='../images/ajax-loader.gif'></td><td valign=top style='color: black'> Đang xử lý thông tin ...</td></tr></table>");
						
						dataString = $("#form_thamgiact").serialize()
						+ "&cat=thamgiact&act=" + $("#thamgiact_act").val() + "&"
						+ allFieldsthamgiact.serialize();
						dataString +='&hisid=<?php echo $_REQUEST["hisid"];?>';
						
						//alert(dataString);
						$.ajax({
							type: "POST",
							url: "gv/gv_thamgiact_process.php",
							data: dataString,
							dataType: "json",
							success: function(data) {
										if (data.status==1)
										{
											thamgiact_RefreshData();	
											$("#tipthamgiact").html("");
										}
										else
										{
											$("#tipthamgiact").html("");
											if ($('#thamgiact_act').val()=='add')
												gv_open_msg_box("Không thể thêm chương trình tham gia, bạn vui lòng thử lại.","alert",250,150);
											else if ($('#thamgiact_act').val()=='edit')
												gv_open_msg_box("Không thể cập nhật chương trình tham gia, bạn vui lòng thử lại.","alert",250,150);
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
				allFieldsthamgiact.val( "" ).removeClass( "ui-state-error" );
			}
		});
	
    $("#btnXoathamgiact").click(function(){
		$( "#btnXoathamgiact" ).button({ disabled: true });
		dataString = $("#form_thamgiact").serialize() + '&cat=thamgiact&act=del';
		dataString +='&hisid=<?php echo $_REQUEST["hisid"];?>';
		
		$.ajax({type: "POST",url: "gv/gv_thamgiact_process.php",data: dataString,dataType: "html",
			success: function(data) {
						thamgiact_RefreshData();
						$( "#btnXoathamgiact" ).button({ disabled: false });
					 }// end function(data)	
		}); // end .ajax
	});	// end $("#btnXoathamgiact").click(function()
	
	function thamgiact_RefreshData() {
		dataString = "cat=get_thamgiact&hisid=<?php echo $_REQUEST["hisid"];?>";
		$.ajax({
			type: "POST",
			url: "gv/gv_thamgiact_process.php",
			data: dataString,
			dataType: "html",
			success: function(data) {
						$("#tablethamgiact tbody").html(data);
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