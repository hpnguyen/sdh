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

$search = array("'","\"");
$replace = array("\\'","&quot;");
?>
  
 <div id="sachdiv">
 <form id="form_sach" method="post" action="" >
         <div id="formthemsachdiv" title="Sách, tài liệu tham khảo">
         	<table width="100%" border="0" align="center" cellpadding="5" cellspacing="2">
             <tr  class="heading" >
               <td width="68%" align="left" colspan=3><label for="txtTenSach">Tên sách</label></td>
               <td width="32%" align="center"><label for="chkTacGiaChinh">Tác giả chính</label></td>
             </tr>
             <tr>
               <td align="left" colspan=3><input class="text ui-widget-content ui-corner-all tableData" name="txtTenSach" type="text" id="txtTenSach" size="60" maxlength="150" /></td>
               <td align="center"><input name="chkTacGiaChinh" type="checkbox" id="chkTacGiaChinh" value="1" /></td>
             </tr>
             <tr  class="heading" >
               <td align="left" colspan=3><label for="txtNhaXBSach">Nhà xuất bản</label></td>
               <td align="center"><label for="txtNamXBSach">Năm xuất bản</label></td>
             </tr>
             <tr>
               <td align="left" colspan=3><input class="text ui-widget-content ui-corner-all tableData" name="txtNhaXBSach" type="text" id="txtNhaXBSach" size="60" maxlength="150" /></td>
               <td align="center"><input class="text ui-widget-content ui-corner-all tableData" name="txtNamXBSach" type="text" id="txtNamXBSach" size="4" maxlength="4" />
                <input type="hidden" name="masachedit" id="masachedit" /></td>
             </tr>
			 
			 <tr  class="heading" >
				<td><label for="txtButDanhSach">Bút danh</label></td>
				<td align="left" ><label for="txtDeTaiSach">Sản phẩm của đề tài/dự án</label></td>
				<td align="left" ><label for="txtNuocNgoai_sach">Xuất bản tại</label></td>
				<td align="left"></td>
             </tr>
             <tr>
				<td align="left"><input style="width:150px" class="text ui-widget-content ui-corner-all tableData" name="txtButDanhSach" type="text" id="txtButDanhSach" maxlength="100" /></td>
				<td align="left">
					<input style="width:100px" list=masodetai_list_sach class="text ui-widget-content ui-corner-all tableData" name="txtDeTaiSach" type="text" id="txtDeTaiSach" maxlength="20" placeholder="chỉ ghi mã số" />
					<datalist id=masodetai_list_sach>
					<?php
						$sqlstr="select MA_SO_DE_TAI, NAM_BAT_DAU from DE_TAI_NCKH where MA_CAN_BO = '$macb' order by NAM_BAT_DAU"; 
						$stmt = oci_parse($db_conn, $sqlstr);oci_execute($stmt);$n = oci_fetch_all($stmt, $resDM);oci_free_statement($stmt);
						for ($i = 0; $i < $n; $i++){
							echo "<option value='{$resDM["MA_SO_DE_TAI"][$i]}'>";}
					?>
					</datalist>
				</td>
				<td align="left">
					<select name="txtNuocNgoai_sach" id="txtNuocNgoai_sach" class="text ui-widget-content ui-corner-all tableData" style="height:23px;font-size:15px">
					   <option value="">chọn...</option>
					   <option value="0">trong nước</option>
					   <option value="1">nước ngoài</option>
					</select>
				</td>
				<td align="left"></td>
             </tr>
			 
			 <tr>
               <td colspan=4 align="left"></td>
             </tr>
           </table>
		   
			<div align="center" id="tipSACH" class="ui-corner-all validateTips"></div>
           
         </div> <!-- end of formthemsachdiv -->
 
	
    
    	<table width="100%" border="0" cellspacing="0" cellpadding="0">
                  <tr>
					<td align=left>
						<div style="margin-left:5px;font-weight:bold">
						Sách phục vụ đào tạo đại học, sau đại học (chuyên khảo, giáo trình, sách tham khảo)
						</div>
					</td>
                    <td align="right">
						<div style="margin-bottom:10px;">
							<a id="taosach" name="taosach">&nbsp;Thêm sách, tài liệu mới</a>&nbsp;&nbsp;
							<a id="btnXoaSach" name="btnXoaSach" >&nbsp;Xóa</a>
						</div>
					</td>
                  </tr>
            </table>
                
               <table id="tablesach" width="100%" border="0" align="center" cellpadding="5" cellspacing="0" class="ui-widget ui-widget-content ui-corner-top tableData" height="20">
               <thead>
                  <tr class="ui-widget-header heading">
                    <td style="width:20px"class=" ui-corner-tl">&nbsp;</td>
                    <td ><em>Tên sách </em></td>
					<td align=center><em>Sản phẩm của<br/>đề tài/dự án</em></td>
                    <td align=left><em>Nhà XB</em></td>
                    <td style="width:50px" align=center><em>Năm XB</em></td>
					<td align=center><em>Nơi xuất bản</em></td>
                    <td align=center><em>Tác giả /<br/>Đồng tác giả</em></td>
					<td align=center ><em>Bút danh</em></td>
                    <td width="42" align="center" >&nbsp;</td>
                    <td width="34" align="center" class=" ui-corner-tr">&nbsp;</td>
                  </tr>
                 </thead>
                 <tbody>
                 </tbody>
            </table>

</form>
<input name="sach_act" id="sach_act" type="hidden" value="" /> 
</div> <!-- end of sachdiv -->

<script type="text/javascript">
function getSACH(tensach, masach, nhaxb, namxb, tacgiachinh, detai, butdanh, nuocngoai)
{
	$('#sach_act').val('edit');
		
	$('#txtTenSach').val(tensach);
	$('#masachedit').val(masach); 
	$('#txtNhaXBSach').val(nhaxb);
	$('#txtNamXBSach').val(namxb);
	$("#txtDeTaiSach").val(detai);
	$("#txtButDanhSach").val(butdanh);
	document.getElementById('txtNuocNgoai_sach').value=nuocngoai;
	document.getElementById('chkTacGiaChinh').checked=tacgiachinh;

	$("#formthemsachdiv").dialog('open');
}

//jQuery.ajax
//$(document).ready(function(){
$(function(){

  // delete btn
 $( "#btnXoaSach" ).button({ icons: {primary:'ui-icon ui-icon-trash'} });
 
 // create new
 $( "#taosach" ).button({ icons: {primary:'ui-icon ui-icon-document'} });

 refresh_data_sach();
 
// Check validate fields Sach txtDeTaiSach,txtNuocNgoai_sach,txtNuocNgoai_sach
var jtxtTenSach			= $("#txtTenSach"),
	jtxtNhaXBSach 		= $("#txtNhaXBSach"),
	jtxtNamXBSach		= $("#txtNamXBSach"),
	jtxtDeTaiSach		= $("#txtDeTaiSach"),
	jtxtNuocNgoaiSach	= $("#txtNuocNgoai_sach"),
	jtxtButDanhSach		= $("#txtButDanhSach"),
	//jchkTacGiaChinh	= $("#chkTacGiaChinh"),
	jmasachedit		= $("#masachedit"),
	allFieldsSACH	= $( [] ).add(jtxtTenSach).add(jtxtNhaXBSach).add(jtxtNamXBSach).add(jtxtDeTaiSach).add(jtxtNuocNgoaiSach).add(jtxtButDanhSach).add(jmasachedit),
	tipsSACH		= $("#tipSACH");
		
	// 
	function updateTips( t ) {
		tipsSACH
					.text( t )
					.addClass( "ui-state-highlight" );
		setTimeout(function() {
			tipsSACH.removeClass( "ui-state-highlight", 1500 );
		}, 1000 );
	}
	
	// Checklength
	function checkLength( o, n, min, max) {
		if (min==0 && (o.val().length==0))
		{	
			o.addClass( "ui-state-error" );
			o.focus();	
			updateTips( "Thông tin " + n + " không được phép để trống.");
			
			return false;
		}else if (min==max && o.val().length<min){
			o.addClass( "ui-state-error" );
			o.focus();	
			updateTips( "Thông tin " + n + " phải đủ " + min + " ký tự.");
		}else if ( o.val().length > max || o.val().length < min ) {
			o.addClass( "ui-state-error" );
			o.focus();		
			updateTips( "Chiều dài của " + n + " từ " +
						min + " đến " + max + " ký tự.");
			return false;
		} else {
			return true;
		}
	}
	
	// Check Regexp
	function checkRegexp( o, regexp, n ) {
		//alert('a');
		if ( o.val()!='' && !( regexp.test( o.val() ) ) ) {
			o.addClass( "ui-state-error" );
			o.focus();
			updateTips( n );
			return false;
		} else {
			return true;
		
		}
	}
// End of check validate
	
	// Post du lieu cho Sach
	$("#taosach").click(function(){
		$("#sach_act").val("add");
		$("#formthemsachdiv").dialog('open');
	});	// end $("#taosach")

	$( "#formthemsachdiv" ).dialog({
			autoOpen: false,
			height: 340,
			width: 600,
			modal: true,
			buttons: {
				"Ok": function() {
					var bValid = true;
					var chktacgia = 1;
					allFieldsSACH.removeClass( "ui-state-error" );
					
					bValid = bValid && checkLength( jtxtTenSach, "\"Tên sách\"", 1, 150, "sach");
					bValid = bValid && checkLength( jtxtNhaXBSach, "\"Nhà xuất bản\"", 1, 150, "sach");
					bValid = bValid && checkLength( jtxtNamXBSach, "\"Năm xuất bản sách\"", 4, 4, "sach");
					bValid = bValid && checkRegexp( jtxtNamXBSach,/^[0-9]{4,4}$/i, "sach", "Thông tin \"Năm Xuất Bản\" phải là Số");
					bValid = bValid && checkLength( jtxtNuocNgoaiSach, "\"Xuất bản\"", 0, 150, "sach");
					
					if (bValid){						
						if (!document.getElementById('chkTacGiaChinh').checked)
							chktacgia = 0;
							
						dataString = $("#form_sach").serialize()+"&cat=sach&act="+$('#sach_act').val()+'&'
						+ allFieldsSACH.serialize() + "&chkTacGiaChinh=" + chktacgia;
						dataString +='&hisid=<?php echo $_REQUEST["hisid"];?>';
						
						//alert(document.getElementById('chkTacGiaChinh').checked);
						//alert(dataString);
						$.ajax({
							type: "POST",
							url: "gv/processgv.php",
							data: dataString,
							dataType: "html",
							success: function(data) {
										//alert(data);
										$("#tablesach tbody").html(data);
								
									 }// end function(data)	
						}); // end .ajax
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
				allFieldsSACH.val( "" ).removeClass( "ui-state-error" );
			}
		});
	
	$("#btnXoaSach").click(function(){
		dataString = $("#form_sach").serialize()+'&cat=sach&act=del';
		dataString +='&hisid=<?php echo $_REQUEST["hisid"];?>';
		
		$.ajax({type: "POST",url: "gv/processgv.php",data: dataString,dataType: "html",
			success: function(data) {
						//alert(data);
						$("#tablesach tbody").html(data);
					 }// end function(data)	
		}); // end .ajax
	});	// end $("#btnXoaSach")
	
	function refresh_data_sach()
	{
		dataString ="cat=sach&hisid=<?php echo $_REQUEST["hisid"];?>";
		$.ajax({
			type: "POST",
			url: "gv/processgv.php",
			data: dataString,
			dataType: "html",
			success: function(data) {
						$("#tablesach tbody").html(data);
					 }// end function(data)	
		}); // end .ajax
	}
	
});

</script>



<?php 
if (isset ($db_conn))
	oci_close($db_conn);
?>