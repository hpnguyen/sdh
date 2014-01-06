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
  
<div id = 'giaithuongdiv'>
	<form id="form_giaithuong" method="post" action="" >
	<input type="hidden" name="magiaithuongedit" id="magiaithuongedit" />
    <div id = 'formthemgiaithuongdiv' title="Quá trình công tác">
        <table width="100%" border="0" align="center" cellpadding="5" cellspacing="2">
		  <tr>
			<td align="right" class="heading"><label for="txtTenGiaiThuong_giaithuong">Giải thưởng</label></td>
			<td>
				<input style="width:470px" class="text ui-widget-content ui-corner-all tableData" name="txtTenGiaiThuong_giaithuong" id="txtTenGiaiThuong_giaithuong" type="text" maxlength="200" placeholder="Tên giải thưởng"/>
			</td>
		  </tr>
		  <tr class="heading">
            <td align="right" ><label for="txtNamCap_giaithuong">Năm cấp</label></td>
			<td>
				<input style="width:50px" class="text ui-widget-content ui-corner-all tableData" name="txtNamCap_giaithuong" id="txtNamCap_giaithuong" type="text" maxlength="4" placeholder=""/>
				<label for="txtNoiCap_giaithuong">Nơi cấp</label>
				<input style="width:360px" class="text ui-widget-content ui-corner-all tableData" name="txtNoiCap_giaithuong" id="txtNoiCap_giaithuong" type="text" maxlength="200" placeholder="Nơi, nước hoặc tổ chức cấp"/>
			</td>
          </tr>
		  <tr class="heading">
            <td align="right" ><label for="txtNuocCap_giaithuong">Nước cấp</label></td>
			<td>
				<select name="txtNuocCap_giaithuong" id="txtNuocCap_giaithuong" class="text ui-widget-content ui-corner-all tableData" style="height:23px;font-size:15px">
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
		  <tr >
			<td align="right" class="heading"><label for="txtNoiDung_giaithuong">Nội dung </label></td>
			<td  class="heading">
				<input style="width:470px" class="text ui-widget-content ui-corner-all tableData" name="txtNoiDung_giaithuong" id="txtNoiDung_giaithuong" type="text" maxlength="200" placeholder="Nội dung giải thưởng"/>
			</td>
		  </tr>
		  
        </table>
		
		<div style="margin-top:10px" align="center" id="tipgiaithuong" class="ui-corner-all validateTips"></div>
			
    </div> <!--end formthemgiaithuongdiv -->
    
	
    
    	<table border="0" width="100%" cellspacing="0" cellpadding="0">
          <tr>
			<td>
			<div style="margin-left:5px;font-weight:bold">
				Các giải thưởng Khoa học và Công nghệ
			</div>
			</td>
            <td align="right" >
				<div style="margin-bottom:10px;">
						<a id="taogiaithuong" name="taogiaithuong" >&nbsp;Thêm giải thưởng...</a>
				&nbsp;&nbsp;
				<a id="btnXoagiaithuong" name="btnXoagiaithuong"> &nbsp;Xóa</a>
				</div>
			</td>
          </tr>
        </table>

		<table width="100%" id="tablegiaithuong" align="center" border="0" cellspacing="0" cellpadding="5" class="ui-widget ui-widget-content ui-corner-top tableData" height="20">
        <thead>
          <tr class="ui-widget-header heading" >
            <td class="ui-corner-tl"  align="left" style="width:30px"><em>TT</em></td>
            <td align="left"><em>Tên giải thưởng</em></td>
            <td align="left"><em>Nội dung giải thưởng</em></td>
            <td align="left"><em>Nơi cấp</em></td>
			<td align="left"><em>Quốc gia cấp</em></td>
            <td align="left" style="width:60px"><em>Năm cấp</em></td>
            <td style="width:30px">&nbsp;</td>
            <td class=" ui-corner-tr" style="width:20px">&nbsp;</td>
          </tr>
          </thead>
          <tbody>
		  </tbody>
        </table>
		<input name="giaithuong_act" id="giaithuong_act" type="hidden" value="" />
</form>
</div>		<!-- end of giaithuongdiv -->   

<script type="text/javascript">

function getgiaithuong(pMaGiaiThuong, pTenGiaiThuong, pNoiDungGiaiThuong, pNoiCap, pMaNuocCap, pNamCap)
{
	//alert(index); '$txtMagiaithuong', '$txtNamBD','$txtNamKT','$txtNoiCongTac','$txtChuyenMon','$txtDiaChi'
	$("#giaithuong_act").val("edit");
	
	var table=document.getElementById("tablegiaithuong");
	
	$("#magiaithuongedit").val(pMaGiaiThuong);
	document.getElementById('txtTenGiaiThuong_giaithuong').value = pTenGiaiThuong;
	document.getElementById('txtNoiDung_giaithuong').value = pNoiDungGiaiThuong;
	document.getElementById('txtNoiCap_giaithuong').value = pNoiCap;
	document.getElementById('txtNuocCap_giaithuong').value = pMaNuocCap;
	document.getElementById('txtNamCap_giaithuong').value = pNamCap;
	
	$("#formthemgiaithuongdiv").dialog('option', 'title', 'Cập nhật giải thưởng...');
	$("#formthemgiaithuongdiv").dialog('open');
}

//jQuery.ajax
//$(document).ready(function(){
$(function(){

  // delete btn
 $( "#btnXoagiaithuong" ).button({ icons: {primary:'ui-icon ui-icon-trash'} });
 
 // create new
 $( "#taogiaithuong" ).button({ icons: {primary:'ui-icon ui-icon-document'} });
 
 //$( "#txtNganh_giaithuong" ).combobox();
 
 // Lay du lieu moi
 giaithuong_RefreshData();
 
// Check validate fields Nghien cuu khoa hoc
var jtxtNamCap_giaithuong			= $("#txtNamCap_giaithuong"),
	jtxtNoiCap_giaithuong 			= $("#txtNoiCap_giaithuong"),
	jtxtNuocCap_giaithuong 			= $("#txtNuocCap_giaithuong"),
	jtxtNoiDung_giaithuong			= $("#txtNoiDung_giaithuong"),
	jtxtTenGiaiThuong_giaithuong	= $("#txtTenGiaiThuong_giaithuong"),
	jmagiaithuongedit				= $("#magiaithuongedit"),
	allFieldsgiaithuong = $([]).add(jtxtNuocCap_giaithuong).add(jtxtNamCap_giaithuong).add(jtxtNoiCap_giaithuong).add(jtxtNoiDung_giaithuong).add(jtxtTenGiaiThuong_giaithuong),
	tipsgiaithuong					= $("#tipgiaithuong");
	
	function giaithuong_updateTips( t ) {
		tipsgiaithuong
					.text( t )
					.addClass( "ui-state-highlight" );
		setTimeout(function() {
			tipsgiaithuong.removeClass( "ui-state-highlight", 1500 );
		}, 1000 );
	}
	
	// giaithuong_checkLength
	function giaithuong_checkLength( o, n, min, max) {
		if (min==0 && (o.val().length==0))
		{	
			o.addClass( "ui-state-error" );
			o.focus();	
			giaithuong_updateTips( "Thông tin " + n + " không được phép để trống.");
			
			return false;
		}else if (min==max && o.val().length<min){
			o.addClass( "ui-state-error" );
			o.focus();	
			giaithuong_updateTips( "Thông tin " + n + " phải đủ " + min + " ký tự.");
		}else if ( o.val().length > max || o.val().length < min ) {
			o.addClass( "ui-state-error" );
			o.focus();		
			giaithuong_updateTips( "Chiều dài của " + n + " từ " +
						min + " đến " + max + " ký tự.");
			return false;
		} else {
			return true;
		}
	}
	
	// Check Regexp
	function giaithuong_checkRegexp( o, regexp, n ) {
		//alert('a');
		if ( o.val()!='' && !( regexp.test( o.val() ) ) ) {
			o.addClass( "ui-state-error" );
			o.focus();
			giaithuong_updateTips( n );
			return false;
		} else {
			return true;
		
		}
	}
// End of check validate
	
	// Post du lieu cho nghien cuu khoa hoc
	$("#taogiaithuong").click(function(){
		 $('#giaithuong_act').val('add');
		 $("#formthemgiaithuongdiv").dialog('option', 'title', 'Thêm giải thưởng...');
		 $("#formthemgiaithuongdiv").dialog('open');
	});
	
	$( "#formthemgiaithuongdiv" ).dialog({
			autoOpen: false,
			height: 290,
			width: 610,
			modal: true,
			buttons: {
				"Ok": function() {
					var bValid = true;
					
					allFieldsgiaithuong.removeClass( "ui-state-error" );

					bValid = bValid && giaithuong_checkLength( jtxtTenGiaiThuong_giaithuong, "\"Tên giải thưởng\"", 0, 200);
					bValid = bValid && giaithuong_checkLength( jtxtNamCap_giaithuong, "\"Năm cấp\"", 4, 4);
					bValid = bValid && giaithuong_checkRegexp( jtxtNamCap_giaithuong,/^[0-9]{4,4}$/i, "Thông tin \"Năm cấp\" phải là Số");
					bValid = bValid && giaithuong_checkLength( jtxtNoiCap_giaithuong, "\"Nơi cấp\"", 0, 200);
					bValid = bValid && giaithuong_checkLength( jtxtNuocCap_giaithuong, "\"Nước cấp\"", 0, 5);
					bValid = bValid && giaithuong_checkLength( jtxtNoiDung_giaithuong, "\"Nội dung giải thưởng\"", 0, 200);
										
					if (bValid) {
						
						$("#tipgiaithuong").html("<table><tr><td ><img src='../images/ajax-loader.gif'></td><td valign=top style='color: black'> Đang xử lý thông tin ...</td></tr></table>");
						
						dataString = $("#form_giaithuong").serialize()
						+ "&cat=giaithuong&act=" + $("#giaithuong_act").val() + "&"
						+ allFieldsgiaithuong.serialize();
						dataString +='&hisid=<?php echo $_REQUEST["hisid"];?>';
						
						//alert(dataString);
						$.ajax({
							type: "POST",
							url: "gv/gv_giaithuong_process.php",
							data: dataString,
							dataType: "json",
							success: function(data) {
										if (data.status==1)
										{
											giaithuong_RefreshData();	
											$("#tipgiaithuong").html("");
										}
										else
										{
											$("#tipgiaithuong").html("");
											if ($('#giaithuong_act').val()=='add')
												gv_open_msg_box("Không thể thêm giải thưởng, bạn vui lòng thử lại.","alert",250,150);
											else if ($('#giaithuong_act').val()=='edit')
												gv_open_msg_box("Không thể cập nhật giải thưởng, bạn vui lòng thử lại.","alert",250,150);
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
				allFieldsgiaithuong.val( "" ).removeClass( "ui-state-error" );
			}
		});
	
    $("#btnXoagiaithuong").click(function(){
		$( "#btnXoagiaithuong" ).button({ disabled: true });
		dataString = $("#form_giaithuong").serialize() + '&cat=giaithuong&act=del';
		dataString +='&hisid=<?php echo $_REQUEST["hisid"];?>';
		
		$.ajax({type: "POST",url: "gv/gv_giaithuong_process.php",data: dataString,dataType: "html",
			success: function(data) {
						giaithuong_RefreshData();
						$( "#btnXoagiaithuong" ).button({ disabled: false });
					 }// end function(data)	
		}); // end .ajax
	});	// end $("#btnXoagiaithuong").click(function()
	
	function giaithuong_RefreshData() {
		dataString = "cat=get_giaithuong&hisid=<?php echo $_REQUEST["hisid"];?>";
		$.ajax({
			type: "POST",
			url: "gv/gv_giaithuong_process.php",
			data: dataString,
			dataType: "html",
			success: function(data) {
						$("#tablegiaithuong tbody").html(data);
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