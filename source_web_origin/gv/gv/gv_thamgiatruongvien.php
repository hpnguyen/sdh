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
  
<div id = 'thamgiatruongviendiv'>
	
	<form id="form_thamgiatruongvien" method="post" action="" >
	<input type="hidden" name="mathamgiatruongvienedit" id="mathamgiatruongvienedit" />
    <div id = 'formthemthamgiatruongviendiv' title="Tham gia chương trình">
        <table width="100%" border="0" align="center" cellpadding="5" cellspacing="2">	  
		  <tr>
			<td align="right" class="heading"><label for="txtthoigianbd_thamgiatruongvien">Năm bắt đầu</label></td>
			<td>
				<input style="width:40px" class="text ui-widget-content ui-corner-all tableData" name="txtthoigianbd_thamgiatruongvien" id="txtthoigianbd_thamgiatruongvien" type="text" maxlength="4" placeholder=""/>
				<label for="txtthoigiankt_thamgiatruongvien" class="heading"> Năm kết thúc</label>
				<input style="width:40px" class="text ui-widget-content ui-corner-all tableData" name="txtthoigiankt_thamgiatruongvien" id="txtthoigiankt_thamgiatruongvien" type="text" maxlength="4" placeholder=""/>

			</td>
		  </tr>
		  
		  <tr class="heading">
            <td align="right" ><label for="txtTenTruongVien_thamgiatruongvien">Tên trường viện</label></td>
			<td>
				<input style="width:470px" class="text ui-widget-content ui-corner-all tableData" name="txtTenTruongVien_thamgiatruongvien" id="txtTenTruongVien_thamgiatruongvien" type="text" maxlength="200" placeholder="tham gia chương trình hoặc thành viên ban chủ nhiệm chương trình"/>
			</td>
          </tr>
		  <tr class="heading">
            <td align="right" ><label for="txtNoiDung_thamgiatruongvien">Nội dung tham gia</label></td>
			<td>
				<input style="width:470px" class="text ui-widget-content ui-corner-all tableData" name="txtNoiDung_thamgiatruongvien" id="txtNoiDung_thamgiatruongvien" type="text" maxlength="500" placeholder=""/>
			</td>
          </tr>
		  
        </table>
		
		<div style="margin-top:10px" align="center" id="tipthamgiatruongvien" class="ui-corner-all validateTips"></div>
			
    </div> <!--end formthemthamgiatruongviendiv -->
       
    	<table border="0" width="100%" cellspacing="0" cellpadding="0">
          <tr>
			<td align=left>
			<div style="margin-left:5px;font-weight:bold">
				Tham gia làm việc tại Trường Đại học/Viện/Trung tâm nghiên cứu theo lời mời. Tham gia các hội đồng tư vấn xét duyệt thẩm định đề tài nghiên cứu khoa học cấp nhà nước và trọng điểm
			</div>
			</td>
            <td align="right" style="width:360px" >
				<div style="margin-bottom:10px;">
						<a id="taothamgiatruongvien" name="taothamgiatruongvien" >&nbsp;Thêm tham gia Trường/Viện/Trung tâm...</a>
				&nbsp;&nbsp;
				<a id="btnXoathamgiatruongvien" name="btnXoathamgiatruongvien"> &nbsp;Xóa</a>
				</div>
			</td>
          </tr>
        </table>

		<table width="100%" id="tablethamgiatruongvien" align="center" border="0" cellspacing="0" cellpadding="5" class="ui-widget ui-widget-content ui-corner-top tableData" height="20">
        <thead>
          <tr class="ui-widget-header heading" >
            <td class="ui-corner-tl"  align="left" style="width:30px"><em>TT</em></td>
            <td align="left"><em>Thời gian</em></td>
            <td align="left"><em>Tên Trường Đại học/Viện/Trung tâm nghiên cứu</em></td>
			<td align="left"><em>Nội dung tham gia</em></td>
			<td style="width:30px">&nbsp;</td>
            <td class=" ui-corner-tr" style="width:20px">&nbsp;</td>
          </tr>
          </thead>
          <tbody>
		  </tbody>
        </table>
		<input name="thamgiatruongvien_act" id="thamgiatruongvien_act" type="hidden" value="" />
</form>
</div>		<!-- end of thamgiatruongviendiv -->   

<script type="text/javascript">

function getthamgiatruongvien_tgtv(pMaTG, pTenCT, pNoiDung, pThoigianBD, pThoigianKT)
{
	$("#thamgiatruongvien_act").val("edit");
	
	$("#mathamgiatruongvienedit").val(pMaTG);
	document.getElementById('txtTenTruongVien_thamgiatruongvien').value = pTenCT;
	document.getElementById('txtthoigianbd_thamgiatruongvien').value = pThoigianBD;
	document.getElementById('txtthoigiankt_thamgiatruongvien').value = pThoigianKT;
	document.getElementById('txtNoiDung_thamgiatruongvien').value = pNoiDung;
	
	$("#formthemthamgiatruongviendiv").dialog('option', 'title', 'Cập nhật tham gia Trường/Viện/Trung tâm...');
	$("#formthemthamgiatruongviendiv").dialog('open');
}

//jQuery.ajax
//$(document).ready(function(){
$(function(){

  // delete btn
 $( "#btnXoathamgiatruongvien" ).button({ icons: {primary:'ui-icon ui-icon-trash'} });
 
 // create new
 $( "#taothamgiatruongvien" ).button({ icons: {primary:'ui-icon ui-icon-document'} });
 
 // Lay du lieu moi
 thamgiatruongvien_RefreshData();
 
// Check validate fields Nghien cuu khoa hoc
var jtxtNoiDung_thamgiatruongvien			= $("#txtNoiDung_thamgiatruongvien"),
	jtxtTenTruongVien_thamgiatruongvien 	= $("#txtTenTruongVien_thamgiatruongvien"),
	jtxtthoigiankt_thamgiatruongvien		= $("#txtthoigiankt_thamgiatruongvien"),
	jtxtthoigianbd_thamgiatruongvien		= $("#txtthoigianbd_thamgiatruongvien"),
	jmathamgiatruongvienedit				= $("#mathamgiatruongvienedit"),
	allFieldsthamgiatruongvien = $([]).add(jtxtNoiDung_thamgiatruongvien).add(jtxtTenTruongVien_thamgiatruongvien).add(jtxtthoigianbd_thamgiatruongvien).add(jtxtthoigiankt_thamgiatruongvien),
	tipsthamgiatruongvien					= $("#tipthamgiatruongvien");
	
	function thamgiatruongvien_updateTips( t ) {
		tipsthamgiatruongvien
					.text( t )
					.addClass( "ui-state-highlight" );
		setTimeout(function() {
			tipsthamgiatruongvien.removeClass( "ui-state-highlight", 1500 );
		}, 1000 );
	}
	
	// thamgiatruongvien_checkLength
	function thamgiatruongvien_checkLength( o, n, min, max) {
		if (min==0 && (o.val().length==0))
		{	
			o.addClass( "ui-state-error" );
			o.focus();	
			thamgiatruongvien_updateTips( "Thông tin " + n + " không được phép để trống.");
			
			return false;
		}else if (min==max && o.val().length<min){
			o.addClass( "ui-state-error" );
			o.focus();	
			thamgiatruongvien_updateTips( "Thông tin " + n + " phải đủ " + min + " ký tự.");
		}else if ( o.val().length > max || o.val().length < min ) {
			o.addClass( "ui-state-error" );
			o.focus();		
			thamgiatruongvien_updateTips( "Chiều dài của " + n + " từ " +
						min + " đến " + max + " ký tự.");
			return false;
		} else {
			return true;
		}
	}
	
	// Check Regexp
	function thamgiatruongvien_checkRegexp( o, regexp, n ) {
		//alert('a');
		if ( o.val()!='' && !( regexp.test( o.val() ) ) ) {
			o.addClass( "ui-state-error" );
			o.focus();
			thamgiatruongvien_updateTips( n );
			return false;
		} else {
			return true;
		
		}
	}
// End of check validate
	
	// Post du lieu cho nghien cuu khoa hoc
	$("#taothamgiatruongvien").click(function(){
		 $('#thamgiatruongvien_act').val('add');
		 $("#formthemthamgiatruongviendiv").dialog('option', 'title', 'Thêm tham gia Trường/Viện/Trung tâm...');
		 $("#formthemthamgiatruongviendiv").dialog('open');
	});
	
	$( "#formthemthamgiatruongviendiv" ).dialog({
			autoOpen: false,
			height: 250,
			width: 630,
			modal: true,
			buttons: {
				"Ok": function() {
					var bValid = true;
					
					allFieldsthamgiatruongvien.removeClass( "ui-state-error" );

					bValid = bValid && thamgiatruongvien_checkLength( jtxtthoigianbd_thamgiatruongvien, "\"Năm bắt đầu tham gia\"", 4, 4);
					bValid = bValid && thamgiatruongvien_checkRegexp( jtxtthoigianbd_thamgiatruongvien,/^[0-9]{4,4}$/i, "Thông tin \"Năm bắt đầu tham gia\" phải là Số");
					
					if (jtxtthoigiankt_thamgiatruongvien.val()!="")
					{
						//alert('1');
						bValid = bValid && thamgiatruongvien_checkLength( jtxtthoigiankt_thamgiatruongvien, "\"Năm kết thúc tham gia\"", 4, 4);
						bValid = bValid && thamgiatruongvien_checkRegexp( jtxtthoigiankt_thamgiatruongvien,/^[0-9]{4,4}$/i, "Thông tin \"Năm kết thúc tham gia\" phải là Số");
						if (jtxtthoigiankt_thamgiatruongvien.val()<jtxtthoigianbd_thamgiatruongvien.val()){
							bValid = false;
							jtxtthoigiankt_thamgiatruongvien.focus();
							jtxtthoigiankt_thamgiatruongvien.addClass( "ui-state-error" );
							thamgiatruongvien_updateTips("Năm kết thúc phải lớn hơn năm bắt đầu");
						}
					}
					
					bValid = bValid && thamgiatruongvien_checkLength( jtxtTenTruongVien_thamgiatruongvien, "\"Tên chương trình\"", 0, 200);
					bValid = bValid && thamgiatruongvien_checkLength( jtxtNoiDung_thamgiatruongvien, "\"Nội dung tham gia\"", 0, 100);
					
										
					if (bValid) {
						
						$("#tipthamgiatruongvien").html("<table><tr><td ><img src='../images/ajax-loader.gif'></td><td valign=top style='color: black'> Đang xử lý thông tin ...</td></tr></table>");
						
						dataString = $("#form_thamgiatruongvien").serialize()
						+ "&cat=thamgiatruongvien&act=" + $("#thamgiatruongvien_act").val() + "&"
						+ allFieldsthamgiatruongvien.serialize();
						dataString +='&hisid=<?php echo $_REQUEST["hisid"];?>';
						
						//alert(dataString);
						$.ajax({
							type: "POST",
							url: "gv/gv_thamgiatruongvien_process.php",
							data: dataString,
							dataType: "json",
							success: function(data) {
										if (data.status==1)
										{
											thamgiatruongvien_RefreshData();	
											$("#tipthamgiatruongvien").html("");
										}
										else
										{
											$("#tipthamgiatruongvien").html("");
											if ($('#thamgiatruongvien_act').val()=='add')
												gv_open_msg_box("Không thể thêm \"tham gia trường viện\", bạn vui lòng thử lại.","alert",250,150);
											else if ($('#thamgiatruongvien_act').val()=='edit')
												gv_open_msg_box("Không thể cập nhật \"tham gia trường viện\", bạn vui lòng thử lại.","alert",250,150);
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
				allFieldsthamgiatruongvien.val( "" ).removeClass( "ui-state-error" );
			}
		});
	
    $("#btnXoathamgiatruongvien").click(function(){
		$( "#btnXoathamgiatruongvien" ).button({ disabled: true });
		dataString = $("#form_thamgiatruongvien").serialize() + '&cat=thamgiatruongvien&act=del';
		dataString +='&hisid=<?php echo $_REQUEST["hisid"];?>';
		
		$.ajax({type: "POST",url: "gv/gv_thamgiatruongvien_process.php",data: dataString,dataType: "html",
			success: function(data) {
						thamgiatruongvien_RefreshData();
						$( "#btnXoathamgiatruongvien" ).button({ disabled: false });
					 }// end function(data)	
		}); // end .ajax
	});	// end $("#btnXoathamgiatruongvien").click(function()
	
	function thamgiatruongvien_RefreshData() {
		dataString = "cat=get_thamgiatruongvien&hisid=<?php echo $_REQUEST["hisid"];?>";
		$.ajax({
			type: "POST",
			url: "gv/gv_thamgiatruongvien_process.php",
			data: dataString,
			dataType: "html",
			success: function(data) {
						$("#tablethamgiatruongvien tbody").html(data);
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