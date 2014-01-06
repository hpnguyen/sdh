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
  
<div id = 'bangsangchediv'>
	<form id="form_bangsangche" method="post" action="" >
	<input type="hidden" name="mabangsangcheedit" id="mabangsangcheedit" />
    <div id = 'formthembangsangchediv' title="Quá trình công tác">
        <table width="100%" border="0" align="center" cellpadding="5" cellspacing="2">
		  <tr>
			<td align="right" class="heading"><label for="txtTenbangsangche_bangsangche">Tên bằng</label></td>
			<td>
				<input style="width:470px" class="text ui-widget-content ui-corner-all tableData" name="txtTenbangsangche_bangsangche" id="txtTenbangsangche_bangsangche" type="text" maxlength="200" placeholder="tên bằng sáng chế"/>
			</td>
		  </tr>
		  <tr class="heading">
            <td align="right" ><label for="txtDeTai_bangsangche">Sản phẩm của<br/>đề tài/dự án</label></td>
			<td>
				<input style="width:120px" list="masodetai_list_bangsangche" class="text ui-widget-content ui-corner-all tableData" name="txtDeTai_bangsangche" id="txtDeTai_bangsangche" type="text" maxlength="25" placeholder="chỉ ghi mã số"/>
				<datalist id="masodetai_list_bangsangche">
				<?php
					$sqlstr="select MA_SO_DE_TAI, NAM_BAT_DAU from DE_TAI_NCKH where MA_CAN_BO = '$macb' order by NAM_BAT_DAU"; 
					$stmt = oci_parse($db_conn, $sqlstr);oci_execute($stmt);$n = oci_fetch_all($stmt, $resDM);oci_free_statement($stmt);
					for ($i = 0; $i < $n; $i++){
						echo "<option value='{$resDM["MA_SO_DE_TAI"][$i]}'>";}
				?>
				</datalist>
				
				<label for="txtSoHieu_bangsangche">Số hiệu</label>
				<input style="width:120px" class="text ui-widget-content ui-corner-all tableData" name="txtSoHieu_bangsangche" id="txtSoHieu_bangsangche" type="text" maxlength="50" placeholder=""/>
				<label for="txtTacGia_bangsangche">Tác giả</label>
				<select name="txtTacGia_bangsangche" id="txtTacGia_bangsangche" class="text ui-widget-content ui-corner-all tableData" style="height:23px;font-size:15px">
				   <option value="">chọn...</option>
				   <option value="1">Tác giả chính</option>
				   <option value="0">Đồng tác giả</option>
				</select>
			</td>
          </tr>
		  <tr class="heading">
            <td align="right" ><label for="txtNamCap_bangsangche">Năm cấp</label></td>
			<td>
				<input style="width:50px" class="text ui-widget-content ui-corner-all tableData" name="txtNamCap_bangsangche" id="txtNamCap_bangsangche" type="text" maxlength="4" placeholder=""/>
				<label for="txtNoiCap_bangsangche">Nơi cấp</label>
				<input style="width:360px" class="text ui-widget-content ui-corner-all tableData" name="txtNoiCap_bangsangche" id="txtNoiCap_bangsangche" type="text" maxlength="200" placeholder="nơi hoặc tổ chức cấp"/>
			</td>
          </tr>
		  <tr class="heading">
            <td align="right" ><label for="txtNuocCap_bangsangche">Nước cấp</label></td>
			<td>
				<select name="txtNuocCap_bangsangche" id="txtNuocCap_bangsangche" class="text ui-widget-content ui-corner-all tableData" style="height:23px;font-size:15px">
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
		
		<div style="margin-top:10px" align="center" id="tipbangsangche" class="ui-corner-all validateTips"></div>
			
    </div> <!--end formthembangsangchediv -->
    
	
    
    	<table border="0" width="100%" cellspacing="0" cellpadding="0">
          <tr>
			<td>
			<div style="margin-left:5px;font-weight:bold">
				Bằng phát minh, sáng chế
			</div>
			</td>
            <td align="right" >
				<div style="margin-bottom:10px;">
						<a id="taobangsangche" name="taobangsangche" >&nbsp;Thêm bằng sáng chế...</a>
				&nbsp;&nbsp;
				<a id="btnXoabangsangche" name="btnXoabangsangche"> &nbsp;Xóa</a>
				</div>
			</td>
          </tr>
        </table>

		<table width="100%" id="tablebangsangche" align="center" border="0" cellspacing="0" cellpadding="5" class="ui-widget ui-widget-content ui-corner-top tableData" height="20">
        <thead>
          <tr class="ui-widget-header heading" >
            <td class="ui-corner-tl"  align="left" style="width:30px"><em>TT</em></td>
            <td align="left"><em>Tên bằng</em></td>
            <td align="left" style="width:100px"><em>Sản phẩm của<br/>đề tài/dự án</em></td>
            <td align="left"><em>Số hiệu</em></td>
			<td align="left"><em>Nơi cấp</em></td>
			<td align="left" style="width:120px"><em>Quốc gia cấp</em></td>
            <td align="left" style="width:60px"><em>Năm cấp</em></td>
			<td align="left" style="width:80px"><em>Tác giả/<br/>đồng tác giả</em></td>
            <td style="width:30px">&nbsp;</td>
            <td class=" ui-corner-tr" style="width:20px">&nbsp;</td>
          </tr>
          </thead>
          <tbody>
		  </tbody>
        </table>
		<input name="bangsangche_act" id="bangsangche_act" type="hidden" value="" />
</form>
</div>		<!-- end of bangsangchediv -->   

<script type="text/javascript">

function getbangsangche_bsc(pMabang, pTenbang, pSohieu, pMadetai, pTacGia, pNoiCap, pMaNuocCap, pNamCap)
{
	//alert(index); '$txtMabangsangche', '$txtNamBD','$txtNamKT','$txtNoiCongTac','$txtChuyenMon','$txtDiaChi'
	$("#bangsangche_act").val("edit");
	
	//var table=document.getElementById("tablebangsangche");
	
	$("#mabangsangcheedit").val(pMabang);
	document.getElementById('txtTenbangsangche_bangsangche').value = pTenbang;
	document.getElementById('txtSoHieu_bangsangche').value = pSohieu;
	document.getElementById('txtDeTai_bangsangche').value = pMadetai;
	document.getElementById('txtTacGia_bangsangche').value = pTacGia;
	document.getElementById('txtNoiCap_bangsangche').value = pNoiCap;
	document.getElementById('txtNuocCap_bangsangche').value = pMaNuocCap;
	document.getElementById('txtNamCap_bangsangche').value = pNamCap;
	
	$("#formthembangsangchediv").dialog('option', 'title', 'Cập nhật bằng sáng chế...');
	$("#formthembangsangchediv").dialog('open');
}

//jQuery.ajax
//$(document).ready(function(){
$(function(){

  // delete btn
 $( "#btnXoabangsangche" ).button({ icons: {primary:'ui-icon ui-icon-trash'} });
 
 // create new
 $( "#taobangsangche" ).button({ icons: {primary:'ui-icon ui-icon-document'} });
 
 // Lay du lieu moi
 bangsangche_RefreshData();
 
// Check validate fields Nghien cuu khoa hoc
var jtxtNamCap_bangsangche			= $("#txtNamCap_bangsangche"),
	jtxtNoiCap_bangsangche 			= $("#txtNoiCap_bangsangche"),
	jtxtNuocCap_bangsangche 		= $("#txtNuocCap_bangsangche"),
	jtxtTacGia_bangsangche			= $("#txtTacGia_bangsangche"),
	jtxtTenbangsangche_bangsangche	= $("#txtTenbangsangche_bangsangche"),
	jtxtSoHieu_bangsangche			= $("#txtSoHieu_bangsangche"),
	jtxtDeTai_bangsangche			= $("#txtDeTai_bangsangche"),
	jmabangsangcheedit				= $("#mabangsangcheedit"),
	allFieldsbangsangche = $([]).add(jtxtSoHieu_bangsangche).add(jtxtDeTai_bangsangche).add(jtxtNuocCap_bangsangche).add(jtxtNamCap_bangsangche).add(jtxtNoiCap_bangsangche).add(jtxtTacGia_bangsangche).add(jtxtTenbangsangche_bangsangche),
	tipsbangsangche					= $("#tipbangsangche");
	
	function bangsangche_updateTips( t ) {
		tipsbangsangche
					.text( t )
					.addClass( "ui-state-highlight" );
		setTimeout(function() {
			tipsbangsangche.removeClass( "ui-state-highlight", 1500 );
		}, 1000 );
	}
	
	// bangsangche_checkLength
	function bangsangche_checkLength( o, n, min, max) {
		if (min==0 && (o.val().length==0))
		{	
			o.addClass( "ui-state-error" );
			o.focus();	
			bangsangche_updateTips( "Thông tin " + n + " không được phép để trống.");
			
			return false;
		}else if (min==max && o.val().length<min){
			o.addClass( "ui-state-error" );
			o.focus();	
			bangsangche_updateTips( "Thông tin " + n + " phải đủ " + min + " ký tự.");
		}else if ( o.val().length > max || o.val().length < min ) {
			o.addClass( "ui-state-error" );
			o.focus();		
			bangsangche_updateTips( "Chiều dài của " + n + " từ " +
						min + " đến " + max + " ký tự.");
			return false;
		} else {
			return true;
		}
	}
	
	// Check Regexp
	function bangsangche_checkRegexp( o, regexp, n ) {
		//alert('a');
		if ( o.val()!='' && !( regexp.test( o.val() ) ) ) {
			o.addClass( "ui-state-error" );
			o.focus();
			bangsangche_updateTips( n );
			return false;
		} else {
			return true;
		
		}
	}
// End of check validate
	
	// Post du lieu cho nghien cuu khoa hoc
	$("#taobangsangche").click(function(){
		 $('#bangsangche_act').val('add');
		 $("#formthembangsangchediv").dialog('option', 'title', 'Thêm bằng sáng chế...');
		 $("#formthembangsangchediv").dialog('open');
	});
	
	$( "#formthembangsangchediv" ).dialog({
			autoOpen: false,
			height: 290,
			width: 630,
			modal: true,
			buttons: {
				"Ok": function() {
					var bValid = true;
					
					allFieldsbangsangche.removeClass( "ui-state-error" );

					bValid = bValid && bangsangche_checkLength( jtxtTenbangsangche_bangsangche, "\"Tên bằng sáng chế\"", 0, 200);
					bValid = bValid && bangsangche_checkLength( jtxtTacGia_bangsangche, "\"Nội dung bằng sáng chế\"", 0, 1);
					bValid = bValid && bangsangche_checkLength( jtxtNamCap_bangsangche, "\"Năm cấp\"", 4, 4);
					bValid = bValid && bangsangche_checkRegexp( jtxtNamCap_bangsangche,/^[0-9]{4,4}$/i, "Thông tin \"Năm cấp\" phải là Số");
					bValid = bValid && bangsangche_checkLength( jtxtNoiCap_bangsangche, "\"Nơi cấp\"", 0, 200);
					bValid = bValid && bangsangche_checkLength( jtxtNuocCap_bangsangche, "\"Nước cấp\"", 0, 5);
					
										
					if (bValid) {
						
						$("#tipbangsangche").html("<table><tr><td ><img src='../images/ajax-loader.gif'></td><td valign=top style='color: black'> Đang xử lý thông tin ...</td></tr></table>");
						
						dataString = $("#form_bangsangche").serialize()
						+ "&cat=bangsangche&act=" + $("#bangsangche_act").val() + "&"
						+ allFieldsbangsangche.serialize();
						dataString +='&hisid=<?php echo $_REQUEST["hisid"];?>';
						
						//alert(dataString);
						$.ajax({
							type: "POST",
							url: "gv/gv_bangsangche_process.php",
							data: dataString,
							dataType: "json",
							success: function(data) {
										if (data.status==1)
										{
											bangsangche_RefreshData();	
											$("#tipbangsangche").html("");
										}
										else
										{
											$("#tipbangsangche").html("");
											if ($('#bangsangche_act').val()=='add')
												gv_open_msg_box("Không thể thêm bằng sáng chế, bạn vui lòng thử lại.","alert",250,150);
											else if ($('#bangsangche_act').val()=='edit')
												gv_open_msg_box("Không thể cập nhật bằng sáng chế, bạn vui lòng thử lại.","alert",250,150);
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
				allFieldsbangsangche.val( "" ).removeClass( "ui-state-error" );
			}
		});
	
    $("#btnXoabangsangche").click(function(){
		$( "#btnXoabangsangche" ).button({ disabled: true });
		dataString = $("#form_bangsangche").serialize() + '&cat=bangsangche&act=del';
		dataString +='&hisid=<?php echo $_REQUEST["hisid"];?>';
		
		$.ajax({type: "POST",url: "gv/gv_bangsangche_process.php",data: dataString,dataType: "html",
			success: function(data) {
						bangsangche_RefreshData();
						$( "#btnXoabangsangche" ).button({ disabled: false });
					 }// end function(data)	
		}); // end .ajax
	});	// end $("#btnXoabangsangche").click(function()
	
	function bangsangche_RefreshData() {
		dataString = "cat=get_bangsangche&hisid=<?php echo $_REQUEST["hisid"];?>";
		$.ajax({
			type: "POST",
			url: "gv/gv_bangsangche_process.php",
			data: dataString,
			dataType: "html",
			success: function(data) {
						$("#tablebangsangche tbody").html(data);
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