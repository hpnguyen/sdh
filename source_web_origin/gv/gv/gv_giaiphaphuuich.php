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
  
<div id = 'giaiphaphuuichdiv'>
	<form id="form_giaiphaphuuich" method="post" action="" >
	<input type="hidden" name="magiaiphaphuuichedit" id="magiaiphaphuuichedit" />
    <div id = 'formthemgiaiphaphuuichdiv' title="Giải pháp hữu ích">
        <table width="100%" border="0" align="center" cellpadding="5" cellspacing="2">
		  <tr>
			<td align="right" class="heading"><label for="txtTengiaiphaphuuich_giaiphaphuuich">Tên giải pháp</label></td>
			<td>
				<input style="width:470px" class="text ui-widget-content ui-corner-all tableData" name="txtTengiaiphaphuuich_giaiphaphuuich" id="txtTengiaiphaphuuich_giaiphaphuuich" type="text" maxlength="200" placeholder="giải pháp hữu ích"/>
			</td>
		  </tr>
		  <tr class="heading">
            <td align="right" ><label for="txtDeTai_giaiphaphuuich">Sản phẩm của<br/>đề tài/dự án</label></td>
			<td>
				<input style="width:120px" list="masodetai_list_giaiphaphuuich" class="text ui-widget-content ui-corner-all tableData" name="txtDeTai_giaiphaphuuich" id="txtDeTai_giaiphaphuuich" type="text" maxlength="25" placeholder="chỉ ghi mã số"/>
				<datalist id="masodetai_list_giaiphaphuuich">
				<?php
					$sqlstr="select MA_SO_DE_TAI, NAM_BAT_DAU from DE_TAI_NCKH where MA_CAN_BO = '$macb' order by NAM_BAT_DAU"; 
					$stmt = oci_parse($db_conn, $sqlstr);oci_execute($stmt);$n = oci_fetch_all($stmt, $resDM);oci_free_statement($stmt);
					for ($i = 0; $i < $n; $i++){
						echo "<option value='{$resDM["MA_SO_DE_TAI"][$i]}'>";}
				?>
				</datalist>
				
				<label for="txtSoHieu_giaiphaphuuich">Số hiệu</label>
				<input style="width:120px" class="text ui-widget-content ui-corner-all tableData" name="txtSoHieu_giaiphaphuuich" id="txtSoHieu_giaiphaphuuich" type="text" maxlength="50" placeholder=""/>
				<label for="txtTacGia_giaiphaphuuich">Tác giả</label>
				<select name="txtTacGia_giaiphaphuuich" id="txtTacGia_giaiphaphuuich" class="text ui-widget-content ui-corner-all tableData" style="height:23px;font-size:15px">
				   <option value="">chọn...</option>
				   <option value="1">Tác giả chính</option>
				   <option value="0">Đồng tác giả</option>
				</select>
			</td>
          </tr>
		  <tr class="heading">
            <td align="right" ><label for="txtNamCap_giaiphaphuuich">Năm cấp</label></td>
			<td>
				<input style="width:50px" class="text ui-widget-content ui-corner-all tableData" name="txtNamCap_giaiphaphuuich" id="txtNamCap_giaiphaphuuich" type="text" maxlength="4" placeholder=""/>
				<label for="txtNoiCap_giaiphaphuuich">Nơi cấp</label>
				<input style="width:360px" class="text ui-widget-content ui-corner-all tableData" name="txtNoiCap_giaiphaphuuich" id="txtNoiCap_giaiphaphuuich" type="text" maxlength="200" placeholder="nơi hoặc tổ chức cấp"/>
			</td>
          </tr>
		  <tr class="heading">
            <td align="right" ><label for="txtNuocCap_giaiphaphuuich">Nước cấp</label></td>
			<td>
				<select name="txtNuocCap_giaiphaphuuich" id="txtNuocCap_giaiphaphuuich" class="text ui-widget-content ui-corner-all tableData" style="height:23px;font-size:15px">
				   <option value="">chọn quốc gia</option>
				   <?php  
						$sqlstr="select * from QUOC_GIA order by ten_quoc_gia"; 
						$stmt = oci_parse($db_conn, $sqlstr);oci_execute($stmt);$n = oci_fetch_all($stmt, $resDM);oci_free_statement($stmt);
						for ($i = 0; $i < $n; $i++)
						{
							echo "<option value='" .$resDM["MA_QUOC_GIA"][$i]."'> " .$resDM["TEN_QUOC_GIA"][$i]. " </option> ";
						}
				   ?>
				</select>
			</td>
          </tr>
		  
        </table>
		
		<div style="margin-top:10px" align="center" id="tipgiaiphaphuuich" class="ui-corner-all validateTips"></div>
			
    </div> <!--end formthemgiaiphaphuuichdiv -->
    
	
    
    	<table border="0" width="100%" cellspacing="0" cellpadding="0">
          <tr>
			<td>
			<div style="margin-left:5px;font-weight:bold">
				Bằng giải pháp hữu ích
			</div>
			</td>
            <td align="right" >
				<div style="margin-bottom:10px;">
						<a id="taogiaiphaphuuich" name="taogiaiphaphuuich" >&nbsp;Thêm giải pháp...</a>
				&nbsp;&nbsp;
				<a id="btnXoagiaiphaphuuich" name="btnXoagiaiphaphuuich"> &nbsp;Xóa</a>
				</div>
			</td>
          </tr>
        </table>

		<table width="100%" id="tablegiaiphaphuuich" align="center" border="0" cellspacing="0" cellpadding="5" class="ui-widget ui-widget-content ui-corner-top tableData" height="20">
        <thead>
          <tr class="ui-widget-header heading" >
            <td class="ui-corner-tl"  align="left" style="width:30px"><em>TT</em></td>
            <td align="left"><em>Tên giải pháp</em></td>
            <td align="center" style="width:100px"><em>Sản phẩm của<br/>đề tài/dự án</em></td>
            <td align="left"><em>Số hiệu</em></td>
			<td align="left"><em>Nơi cấp</em></td>
			<td align="left" style="width:120px"><em>Quốc gia cấp</em></td>
            <td align="center" style="width:60px"><em>Năm cấp</em></td>
			<td align="center" style="width:90px"><em>Tác giả/<br/>đồng tác giả</em></td>
            <td style="width:30px">&nbsp;</td>
            <td class=" ui-corner-tr" style="width:20px">&nbsp;</td>
          </tr>
          </thead>
          <tbody>
		  </tbody>
        </table>
		<input name="giaiphaphuuich_act" id="giaiphaphuuich_act" type="hidden" value="" />
</form>
</div>		<!-- end of giaiphaphuuichdiv -->   

<script type="text/javascript">

function getgiaiphaphuuich_gphi(pMabang, pTenbang, pSohieu, pMadetai, pTacGia, pNoiCap, pMaNuocCap, pNamCap)
{
	//alert(index); '$txtMagiaiphaphuuich', '$txtNamBD','$txtNamKT','$txtNoiCongTac','$txtChuyenMon','$txtDiaChi'
	$("#giaiphaphuuich_act").val("edit");
	
	//var table=document.getElementById("tablegiaiphaphuuich");
	
	$("#magiaiphaphuuichedit").val(pMabang);
	document.getElementById('txtTengiaiphaphuuich_giaiphaphuuich').value = pTenbang;
	document.getElementById('txtSoHieu_giaiphaphuuich').value = pSohieu;
	document.getElementById('txtDeTai_giaiphaphuuich').value = pMadetai;
	document.getElementById('txtTacGia_giaiphaphuuich').value = pTacGia;
	document.getElementById('txtNoiCap_giaiphaphuuich').value = pNoiCap;
	document.getElementById('txtNuocCap_giaiphaphuuich').value = pMaNuocCap;
	document.getElementById('txtNamCap_giaiphaphuuich').value = pNamCap;
	
	$("#formthemgiaiphaphuuichdiv").dialog('option', 'title', 'Cập nhật giải pháp hữu ích...');
	$("#formthemgiaiphaphuuichdiv").dialog('open');
}

//jQuery.ajax
//$(document).ready(function(){
$(function(){

  // delete btn
 $( "#btnXoagiaiphaphuuich" ).button({ icons: {primary:'ui-icon ui-icon-trash'} });
 
 // create new
 $( "#taogiaiphaphuuich" ).button({ icons: {primary:'ui-icon ui-icon-document'} });
 
 // Lay du lieu moi
 giaiphaphuuich_RefreshData();
 
// Check validate fields Nghien cuu khoa hoc
var jtxtNamCap_giaiphaphuuich			= $("#txtNamCap_giaiphaphuuich"),
	jtxtNoiCap_giaiphaphuuich 			= $("#txtNoiCap_giaiphaphuuich"),
	jtxtNuocCap_giaiphaphuuich 		= $("#txtNuocCap_giaiphaphuuich"),
	jtxtTacGia_giaiphaphuuich			= $("#txtTacGia_giaiphaphuuich"),
	jtxtTengiaiphaphuuich_giaiphaphuuich	= $("#txtTengiaiphaphuuich_giaiphaphuuich"),
	jtxtSoHieu_giaiphaphuuich			= $("#txtSoHieu_giaiphaphuuich"),
	jtxtDeTai_giaiphaphuuich			= $("#txtDeTai_giaiphaphuuich"),
	jmagiaiphaphuuichedit				= $("#magiaiphaphuuichedit"),
	allFieldsgiaiphaphuuich = $([]).add(jtxtSoHieu_giaiphaphuuich).add(jtxtDeTai_giaiphaphuuich).add(jtxtNuocCap_giaiphaphuuich).add(jtxtNamCap_giaiphaphuuich).add(jtxtNoiCap_giaiphaphuuich).add(jtxtTacGia_giaiphaphuuich).add(jtxtTengiaiphaphuuich_giaiphaphuuich),
	tipsgiaiphaphuuich					= $("#tipgiaiphaphuuich");
	
	function giaiphaphuuich_updateTips( t ) {
		tipsgiaiphaphuuich
					.text( t )
					.addClass( "ui-state-highlight" );
		setTimeout(function() {
			tipsgiaiphaphuuich.removeClass( "ui-state-highlight", 1500 );
		}, 1000 );
	}
	
	// giaiphaphuuich_checkLength
	function giaiphaphuuich_checkLength( o, n, min, max) {
		if (min==0 && (o.val().length==0))
		{	
			o.addClass( "ui-state-error" );
			o.focus();	
			giaiphaphuuich_updateTips( "Thông tin " + n + " không được phép để trống.");
			
			return false;
		}else if (min==max && o.val().length<min){
			o.addClass( "ui-state-error" );
			o.focus();	
			giaiphaphuuich_updateTips( "Thông tin " + n + " phải đủ " + min + " ký tự.");
		}else if ( o.val().length > max || o.val().length < min ) {
			o.addClass( "ui-state-error" );
			o.focus();		
			giaiphaphuuich_updateTips( "Chiều dài của " + n + " từ " +
						min + " đến " + max + " ký tự.");
			return false;
		} else {
			return true;
		}
	}
	
	// Check Regexp
	function giaiphaphuuich_checkRegexp( o, regexp, n ) {
		//alert('a');
		if ( o.val()!='' && !( regexp.test( o.val() ) ) ) {
			o.addClass( "ui-state-error" );
			o.focus();
			giaiphaphuuich_updateTips( n );
			return false;
		} else {
			return true;
		
		}
	}
// End of check validate
	
	// Post du lieu cho nghien cuu khoa hoc
	$("#taogiaiphaphuuich").click(function(){
		 $('#giaiphaphuuich_act').val('add');
		 $("#formthemgiaiphaphuuichdiv").dialog('option', 'title', 'Thêm giải pháp hữu ích...');
		 $("#formthemgiaiphaphuuichdiv").dialog('open');
	});
	
	$( "#formthemgiaiphaphuuichdiv" ).dialog({
			autoOpen: false,
			height: 290,
			width: 630,
			modal: true,
			buttons: {
				"Ok": function() {
					var bValid = true;
					
					allFieldsgiaiphaphuuich.removeClass( "ui-state-error" );

					bValid = bValid && giaiphaphuuich_checkLength( jtxtTengiaiphaphuuich_giaiphaphuuich, "\"Tên giải pháp\"", 0, 200);
					bValid = bValid && giaiphaphuuich_checkLength( jtxtTacGia_giaiphaphuuich, "\"Nội dung giải pháp\"", 0, 1);
					bValid = bValid && giaiphaphuuich_checkLength( jtxtNamCap_giaiphaphuuich, "\"Năm cấp\"", 4, 4);
					bValid = bValid && giaiphaphuuich_checkRegexp( jtxtNamCap_giaiphaphuuich,/^[0-9]{4,4}$/i, "Thông tin \"Năm cấp\" phải là Số");
					bValid = bValid && giaiphaphuuich_checkLength( jtxtNoiCap_giaiphaphuuich, "\"Nơi cấp\"", 0, 200);
					bValid = bValid && giaiphaphuuich_checkLength( jtxtNuocCap_giaiphaphuuich, "\"Nước cấp\"", 0, 5);
					
										
					if (bValid) {
						
						$("#tipgiaiphaphuuich").html("<table><tr><td ><img src='../images/ajax-loader.gif'></td><td valign=top style='color: black'> Đang xử lý thông tin ...</td></tr></table>");
						
						dataString = $("#form_giaiphaphuuich").serialize()
						+ "&cat=giaiphaphuuich&act=" + $("#giaiphaphuuich_act").val() + "&"
						+ allFieldsgiaiphaphuuich.serialize();
						dataString +='&hisid=<?php echo $_REQUEST["hisid"];?>';
						
						//alert(dataString);
						$.ajax({
							type: "POST",
							url: "gv/gv_giaiphaphuuich_process.php",
							data: dataString,
							dataType: "json",
							success: function(data) {
										if (data.status==1)
										{
											giaiphaphuuich_RefreshData();	
											$("#tipgiaiphaphuuich").html("");
										}
										else
										{
											$("#tipgiaiphaphuuich").html("");
											if ($('#giaiphaphuuich_act').val()=='add')
												gv_open_msg_box("Không thể thêm giải pháp, bạn vui lòng thử lại.","alert",250,150);
											else if ($('#giaiphaphuuich_act').val()=='edit')
												gv_open_msg_box("Không thể cập nhật giải pháp, bạn vui lòng thử lại.","alert",250,150);
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
				allFieldsgiaiphaphuuich.val( "" ).removeClass( "ui-state-error" );
			}
		});
	
    $("#btnXoagiaiphaphuuich").click(function(){
		$( "#btnXoagiaiphaphuuich" ).button({ disabled: true });
		dataString = $("#form_giaiphaphuuich").serialize() + '&cat=giaiphaphuuich&act=del';
		dataString +='&hisid=<?php echo $_REQUEST["hisid"];?>';
		
		$.ajax({type: "POST",url: "gv/gv_giaiphaphuuich_process.php",data: dataString,dataType: "html",
			success: function(data) {
						giaiphaphuuich_RefreshData();
						$( "#btnXoagiaiphaphuuich" ).button({ disabled: false });
					 }// end function(data)	
		}); // end .ajax
	});	// end $("#btnXoagiaiphaphuuich").click(function()
	
	function giaiphaphuuich_RefreshData() {
		dataString = "cat=get_giaiphaphuuich&hisid=<?php echo $_REQUEST["hisid"];?>";
		$.ajax({
			type: "POST",
			url: "gv/gv_giaiphaphuuich_process.php",
			data: dataString,
			dataType: "html",
			success: function(data) {
						$("#tablegiaiphaphuuich tbody").html(data);
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