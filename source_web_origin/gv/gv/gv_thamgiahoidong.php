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
  
<div id = 'thamgiahoidongdiv'>
	<form id="form_thamgiahoidong" method="post" action="" >
	<input type="hidden" name="mathamgiahoidongedit" id="mathamgiahoidongedit" />
    <div id = 'formthemthamgiahoidongdiv' title="Tham gia chương trình">
        <table width="100%" border="0" align="center" cellpadding="5" cellspacing="2">
		  <tr>
			<td align="right" class="heading"><label for="txtthoigian_thamgiahoidong">Thời gian</label></td>
			<td>
				<input style="width:40px" class="text ui-widget-content ui-corner-all tableData" name="txtthoigian_thamgiahoidong" id="txtthoigian_thamgiahoidong" type="text" maxlength="4" placeholder=""/>
			</td>
		  </tr>
		  <tr class="heading">
            <td align="right" ><label for="txtTenHoiDong_thamgiahoidong">Tên hội đồng</label></td>
			<td>
				<input style="width:470px" class="text ui-widget-content ui-corner-all tableData" name="txtTenHoiDong_thamgiahoidong" id="txtTenHoiDong_thamgiahoidong" type="text" maxlength="200" placeholder="tên hội đồng tham gia trong vòng 3 năm gần nhất"/>
			</td>
          </tr>
		  <tr class="heading">
            <td align="right" ><label for="txtChucDanh_thamgiahoidong">Chức danh</label></td>
			<td>
				<input style="width:250px" list="txtChucDanh_thamgiahoidong" class="text ui-widget-content ui-corner-all tableData" name="txtChucDanh_thamgiahoidong" id="txtChucDanh_thamgiahoidong" type="text" maxlength="100" placeholder=""/>
				<datalist id="chucdang_list_thamgiahoidong">
					<option value='Chủ tịch'>
					<option value='Thư ký'>
					<option value='Ủy viên HĐ'>
				</datalist>
				
				<label for="txtNoiToChuc_thamgiahoidong"> Tổ chức ở</label>
				<select name="txtNoiToChuc_thamgiahoidong" id="txtNoiToChuc_thamgiahoidong" class="text ui-widget-content ui-corner-all tableData" style="height:23px;font-size:15px">
				   <option value="">chọn...</option>
				   <option value="0">trong nước</option>
				   <option value="1">nước ngoài</option>
				</select>
			</td>
          </tr>
		  
        </table>
		
		<div style="margin-top:10px" align="center" id="tipthamgiahoidong" class="ui-corner-all validateTips"></div>
			
    </div> <!--end formthemthamgiahoidongdiv -->
    
	
    
    	<table border="0" width="100%" cellspacing="0" cellpadding="0">
          <tr>
			<td>
			<div style="margin-left:5px;font-weight:bold">
				Tham gia các Hiệp hội Khoa học, Ban biên tập các tạp chí Khoa học, Ban tổ chức các Hội nghị về KH&CN, Phản biện tạp chí khoa học, các hội thảo hội nghị quốc tế và trong nước
			</div>
			</td>
            <td align="right" >
				<div style="margin-bottom:10px;">
						<a id="taothamgiahoidong" name="taothamgiahoidong" >&nbsp;Thêm hội đồng tham gia...</a>
				&nbsp;&nbsp;
				<a id="btnXoathamgiahoidong" name="btnXoathamgiahoidong"> &nbsp;Xóa</a>
				</div>
			</td>
          </tr>
        </table>

		<table width="100%" id="tablethamgiahoidong" align="center" border="0" cellspacing="0" cellpadding="5" class="ui-widget ui-widget-content ui-corner-top tableData" height="20">
        <thead>
          <tr class="ui-widget-header heading" >
            <td class="ui-corner-tl"  align="left" style="width:30px"><em>TT</em></td>
            <td align="left"><em>Thời gian</em></td>
			<td align="left"><em>Tên hội đồng</em></td>
			<td align="left"><em>Nơi tổ chức</em></td>
			<td align="left"><em>Chức danh</em></td>
			<td style="width:30px">&nbsp;</td>
            <td class=" ui-corner-tr" style="width:20px">&nbsp;</td>
          </tr>
          </thead>
          <tbody>
		  </tbody>
        </table>
		<input name="thamgiahoidong_act" id="thamgiahoidong_act" type="hidden" value="" />
</form>
</div>		<!-- end of thamgiahoidongdiv -->   

<script type="text/javascript">

function getthamgiahoidong_tghd(pMaTG, pTenHD, pChucDanh, pThoigian, pNuocNgoai)
{
	$("#thamgiahoidong_act").val("edit");
	
	$("#mathamgiahoidongedit").val(pMaTG);
	document.getElementById('txtTenHoiDong_thamgiahoidong').value = pTenHD;
	document.getElementById('txtNoiToChuc_thamgiahoidong').value = pNuocNgoai;
	document.getElementById('txtthoigian_thamgiahoidong').value = pThoigian;
	document.getElementById('txtChucDanh_thamgiahoidong').value = pChucDanh;
	
	$("#formthemthamgiahoidongdiv").dialog('option', 'title', 'Cập nhật tham gia hội đồng...');
	$("#formthemthamgiahoidongdiv").dialog('open');
}

//jQuery.ajax
//$(document).ready(function(){
$(function(){

  // delete btn
 $( "#btnXoathamgiahoidong" ).button({ icons: {primary:'ui-icon ui-icon-trash'} });
 
 // create new
 $( "#taothamgiahoidong" ).button({ icons: {primary:'ui-icon ui-icon-document'} });
 
 // Lay du lieu moi
 thamgiahoidong_RefreshData();
 
// Check validate fields Nghien cuu khoa hoc
var jtxtChucDanh_thamgiahoidong			= $("#txtChucDanh_thamgiahoidong"),
	jtxtTenHoiDong_thamgiahoidong 		= $("#txtTenHoiDong_thamgiahoidong"),
	jtxtNuocNgoai_thamgiahoidong 		= $("#txtNuocNgoai_thamgiahoidong"),
	jtxtthoigian_thamgiahoidong			= $("#txtthoigian_thamgiahoidong"),
	jmathamgiahoidongedit				= $("#mathamgiahoidongedit"),
	allFieldsthamgiahoidong = $([]).add(jtxtChucDanh_thamgiahoidong).add(jtxtTenHoiDong_thamgiahoidong).add(jtxtNuocNgoai_thamgiahoidong).add(jtxtthoigian_thamgiahoidong),
	tipsthamgiahoidong					= $("#tipthamgiahoidong");
	
	function thamgiahoidong_updateTips( t ) {
		tipsthamgiahoidong
					.text( t )
					.addClass( "ui-state-highlight" );
		setTimeout(function() {
			tipsthamgiahoidong.removeClass( "ui-state-highlight", 1500 );
		}, 1000 );
	}
	
	// thamgiahoidong_checkLength
	function thamgiahoidong_checkLength( o, n, min, max) {
		if (min==0 && (o.val().length==0))
		{	
			o.addClass( "ui-state-error" );
			o.focus();	
			thamgiahoidong_updateTips( "Thông tin " + n + " không được phép để trống.");
			
			return false;
		}else if (min==max && o.val().length<min){
			o.addClass( "ui-state-error" );
			o.focus();	
			thamgiahoidong_updateTips( "Thông tin " + n + " phải đủ " + min + " ký tự.");
		}else if ( o.val().length > max || o.val().length < min ) {
			o.addClass( "ui-state-error" );
			o.focus();		
			thamgiahoidong_updateTips( "Chiều dài của " + n + " từ " +
						min + " đến " + max + " ký tự.");
			return false;
		} else {
			return true;
		}
	}
	
	// Check Regexp
	function thamgiahoidong_checkRegexp( o, regexp, n ) {
		//alert('a');
		if ( o.val()!='' && !( regexp.test( o.val() ) ) ) {
			o.addClass( "ui-state-error" );
			o.focus();
			thamgiahoidong_updateTips( n );
			return false;
		} else {
			return true;
		
		}
	}
// End of check validate
	
	// Post du lieu cho nghien cuu khoa hoc
	$("#taothamgiahoidong").click(function(){
		 $('#thamgiahoidong_act').val('add');
		 $("#formthemthamgiahoidongdiv").dialog('option', 'title', 'Thêm chương trình tham gia...');
		 $("#formthemthamgiahoidongdiv").dialog('open');
	});
	
	$( "#formthemthamgiahoidongdiv" ).dialog({
			autoOpen: false,
			height: 250,
			width: 630,
			modal: true,
			buttons: {
				"Ok": function() {
					var bValid = true;
					
					allFieldsthamgiahoidong.removeClass( "ui-state-error" );

					bValid = bValid && thamgiahoidong_checkLength( jtxtthoigian_thamgiahoidong, "\"Năm tham gia\"", 4, 4);
					bValid = bValid && thamgiahoidong_checkRegexp( jtxtthoigian_thamgiahoidong,/^[0-9]{4,4}$/i, "Thông tin \"Năm tham gia\" phải là Số");
					bValid = bValid && thamgiahoidong_checkLength( jtxtNuocNgoai_thamgiahoidong, "\"tổ chức ở\"", 0, 1);
					bValid = bValid && thamgiahoidong_checkLength( jtxtTenHoiDong_thamgiahoidong, "\"Tên chương trình\"", 0, 200);
					//bValid = bValid && thamgiahoidong_checkLength( jtxtChucDanh_thamgiahoidong, "\"Chức danh\"", 0, 100);
					
										
					if (bValid) {
						
						$("#tipthamgiahoidong").html("<table><tr><td ><img src='../images/ajax-loader.gif'></td><td valign=top style='color: black'> Đang xử lý thông tin ...</td></tr></table>");
						
						dataString = $("#form_thamgiahoidong").serialize()
						+ "&cat=thamgiahoidong&act=" + $("#thamgiahoidong_act").val() + "&"
						+ allFieldsthamgiahoidong.serialize();
						dataString +='&hisid=<?php echo $_REQUEST["hisid"];?>';
						
						//alert(dataString);
						$.ajax({
							type: "POST",
							url: "gv/gv_thamgiahoidong_process.php",
							data: dataString,
							dataType: "json",
							success: function(data) {
										if (data.status==1)
										{
											thamgiahoidong_RefreshData();	
											$("#tipthamgiahoidong").html("");
										}
										else
										{
											$("#tipthamgiahoidong").html("");
											if ($('#thamgiahoidong_act').val()=='add')
												gv_open_msg_box("Không thể thêm chương trình tham gia, bạn vui lòng thử lại.","alert",250,150);
											else if ($('#thamgiahoidong_act').val()=='edit')
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
				allFieldsthamgiahoidong.val( "" ).removeClass( "ui-state-error" );
			}
		});
	
    $("#btnXoathamgiahoidong").click(function(){
		$( "#btnXoathamgiahoidong" ).button({ disabled: true });
		dataString = $("#form_thamgiahoidong").serialize() + '&cat=thamgiahoidong&act=del';
		dataString +='&hisid=<?php echo $_REQUEST["hisid"];?>';
		
		$.ajax({type: "POST",url: "gv/gv_thamgiahoidong_process.php",data: dataString,dataType: "html",
			success: function(data) {
						thamgiahoidong_RefreshData();
						$( "#btnXoathamgiahoidong" ).button({ disabled: false });
					 }// end function(data)	
		}); // end .ajax
	});	// end $("#btnXoathamgiahoidong").click(function()
	
	function thamgiahoidong_RefreshData() {
		dataString = "cat=get_thamgiahoidong&hisid=<?php echo $_REQUEST["hisid"];?>";
		$.ajax({
			type: "POST",
			url: "gv/gv_thamgiahoidong_process.php",
			data: dataString,
			dataType: "html",
			success: function(data) {
						$("#tablethamgiahoidong tbody").html(data);
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