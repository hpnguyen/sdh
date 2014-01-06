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
  
<div id = 'quatrinhcongtacdiv'>
	<form id="form_qtct" method="post" action="" >
		<input type="hidden" name="maqtctedit" id="maqtctedit" />
		<div id = 'formthemquatrinhcongtacdiv' title="Quá trình công tác">
			<table width="100%" border="0" align="center" cellpadding="5" cellspacing="2">
			  <tr class="heading">
				<td align="right" ><label for="txtTu_qtct">Từ</label></td>
				<td>
					<input style="width:50px" class="text ui-widget-content ui-corner-all tableData" name="txtTu_qtct" id="txtTu_qtct" type="text" maxlength="4" placeholder=""/>
					<label for="txtDen_qtct">Đến</label>
					<input style="width:50px" class="text ui-widget-content ui-corner-all tableData" name="txtDen_qtct" id="txtDen_qtct" type="text" maxlength="4" placeholder=""/>
				</td>
			  </tr>
			  <tr>
				<td align="right" class="heading"><label for="txtChuyenMon_qtct" id=lblTenLALV_qtct>Chuyên môn</label></td>
				<td>
					<input style="width:470px" class="text ui-widget-content ui-corner-all tableData" name="txtChuyenMon_qtct" id="txtChuyenMon_qtct" type="text" maxlength="200" placeholder="chuyên môn công tác"/>
				</td>
			  </tr>
			  <tr >
				<td align="right" class="heading"><label for="txtChucVu_qtct">Chức vụ</label></td>
				<td  class="heading">
					<table>
					<tr>
					<td>
					<select name="txtChucVu_qtct" id="txtChucVu_qtct" class="text ui-widget-content ui-corner-all tableData" style="height:25px;font-size:15px;width: 150px;">
					   <option value=""></option>
					   <?php $sqlstr="select * from DM_CHUC_VU order by TEN_CHUC_VU"; 
						$stmt = oci_parse($db_conn, $sqlstr);oci_execute($stmt);$n = oci_fetch_all($stmt, $resDM);oci_free_statement($stmt);				
						for ($i = 0; $i < $n; $i++)
						{
							echo "<option value='" .$resDM["MA_CHUC_VU"][$i]."'> " .$resDM["TEN_CHUC_VU"][$i]. " </option> ";
						}
					   ?>
					</select>
					</td>
					<td>	<button id="tao_cv_qtct" style="margin:0 0 0 0; width:22px; height:20px" title="Thêm mới chức vụ nếu danh mục chưa có chức vụ mong muốn." data-placement="right"></button>
					</td>
					</tr>
					</table>
				</td>
			  </tr>
			  
			  <tr >
				<td align="right" class="heading"><label for="txtNoiCongTac_qtct">Nơi Công tác </label></td>
				<td  class="heading">
					<input style="width:470px" class="text ui-widget-content ui-corner-all tableData" name="txtNoiCongTac_qtct" id="txtNoiCongTac_qtct" type="text" maxlength="200" placeholder=""/>
				</td>
			  </tr>
			  
			  <tr >
				<td align="right" class="heading"><label for="txtDiaChi_qtct">Địa chỉ</label></td>
				<td>
					<input style="width:470px" class="text ui-widget-content ui-corner-all tableData" name="txtDiaChi_qtct" id="txtDiaChi_qtct" type="text" maxlength="200" placeholder="địa chỉ nơi công tác"/>
				</td>
			  </tr>
			  
			</table>
			
			<div style="margin-top:10px" align="center" id="tipqtct" class="ui-corner-all validateTips"></div>
				
		</div> <!--end formthemquatrinhcongtacdiv -->
    
	
    
    	<table border="0" width="100%" cellspacing="0" cellpadding="0">
          <tr>
			<td align=left>
				<div style="margin-left:5px;font-weight:bold">Thời gian công tác</div>
			</td>
            <td align="right" >
				<div style="margin-bottom:10px;">
						<a id="taoqtct" name="taoqtct" >&nbsp;Thêm công tác... mới</a>
				&nbsp;&nbsp;
				<a id="btnXoaqtct" name="btnXoaqtct"> &nbsp;Xóa</a>
				</div>
			</td>
          </tr>
        </table>

		<table width="100%" id="tableqtct" align="center" border="0" cellspacing="0" cellpadding="5" class="ui-widget ui-widget-content ui-corner-top tableData" height="20">
        <thead>
          <tr class="ui-widget-header heading" >
            <td class="ui-corner-tl"  align="left" style="width:130px"><em>Thời gian</em></td>
            <td align="left"><em>Nơi công tác</em></td>
            <td align="left"><em>Chức vụ</em></td>
            <td align="left"><em>Chuyên môn</em></td>
            <td align="left"><em>Địa chỉ</em></td>
            <td >&nbsp;</td>
            <td class=" ui-corner-tr" >&nbsp;</td>
          </tr>
          </thead>
          <tbody>
		  </tbody>
        </table>
	</form>
<input name="qtct_act" id="qtct_act" type="hidden" value="" />
</div>		<!-- end of quatrinhcongtacdiv -->   
<div id=form_them_cv_qtct>
	<div style="margin: 5px 0 0 0;">
	<b>Chức vụ</b> <input style="width:220px" class="text ui-widget-content ui-corner-all tableData" name="txtChucVuMoi_qtct" id="txtChucVuMoi_qtct" type="text" maxlength="100" placeholder=""/>
	</div>
</div>

<script type="text/javascript">

function getqtct(pMaQTCT, pNamBD, pNamKT, pNoiCongTac, pChuyenMon, pDiaChi, pChucVu)
{
	//alert(index); '$txtMaQTCT', '$txtNamBD','$txtNamKT','$txtNoiCongTac','$txtChuyenMon','$txtDiaChi'
	$("#qtct_act").val("edit");
	$("#maqtctedit").val(pMaQTCT);
	var table=document.getElementById("tableqtct");
	
	document.getElementById('txtTu_qtct').value = pNamBD;
	document.getElementById('txtDen_qtct').value = pNamKT;
	document.getElementById('txtNoiCongTac_qtct').value = pNoiCongTac;
	document.getElementById('txtChucVu_qtct').value = pChucVu;
	document.getElementById('txtChuyenMon_qtct').value = pChuyenMon;
	document.getElementById('txtDiaChi_qtct').value = pDiaChi;

	
	$("#formthemquatrinhcongtacdiv").dialog('option', 'title', 'Cập nhật quá trình đào tạo...');
	$("#formthemquatrinhcongtacdiv").dialog('open');
}

//jQuery.ajax
//$(document).ready(function(){
$(function(){

  $( "#tao_cv_qtct" ).button({ icons: {primary:'ui-icon ui-icon-plusthick'},text: false});
  $( "#tao_cv_qtct" ).tooltip();
  
  // delete btn
 $( "#btnXoaqtct" ).button({ icons: {primary:'ui-icon ui-icon-trash'} });
 
 // create new
 $( "#taoqtct" ).button({ icons: {primary:'ui-icon ui-icon-document'} });
 
 //$( "#txtNganh_qtct" ).combobox();
 
 // Lay du lieu moi
 qtct_RefreshData();
 
// Check validate fields Nghien cuu khoa hoc
var jtxtTu_qtct			= $("#txtTu_qtct"),
	jtxtDen_qtct 		= $("#txtDen_qtct"),
	jtxtChucVu_qtct		= $("#txtChucVu_qtct"),
	jtxtNoiCongTac_qtct	= $("#txtNoiCongTac_qtct"),
	jtxtDiaChi_qtct		= $("#txtDiaChi_qtct"),
	jtxtChuyenMon_qtct	= $("#txtChuyenMon_qtct"),
	jmaqtctedit			= $("#maqtctedit"),
	allFieldsqtct = $([]).add(jtxtTu_qtct).add(jtxtDen_qtct).add(jtxtChucVu_qtct).add(jtxtNoiCongTac_qtct).add(jtxtDiaChi_qtct).add(jtxtChuyenMon_qtct),
	tipsqtct			= $("#tipqtct");
		
	// 
	function qtct_updateTips( t ) {
		tipsqtct
					.text( t )
					.addClass( "ui-state-highlight" );
		setTimeout(function() {
			tipsqtct.removeClass( "ui-state-highlight", 1500 );
		}, 1000 );
	}
	
	// qtct_checkLength
	function qtct_checkLength( o, n, min, max) {
		if (min==0 && (o.val().length==0))
		{	
			o.addClass( "ui-state-error" );
			o.focus();	
			qtct_updateTips( "Thông tin " + n + " không được phép để trống.");
			
			return false;
		}else if (min==max && o.val().length<min){
			o.addClass( "ui-state-error" );
			o.focus();	
			qtct_updateTips( "Thông tin " + n + " phải đủ " + min + " ký tự.");
		}else if ( o.val().length > max || o.val().length < min ) {
			o.addClass( "ui-state-error" );
			o.focus();		
			qtct_updateTips( "Chiều dài của " + n + " từ " +
						min + " đến " + max + " ký tự.");
			return false;
		} else {
			return true;
		}
	}
	
	// Check Regexp
	function qtct_checkRegexp( o, regexp, n ) {
		//alert('a');
		if ( o.val()!='' && !( regexp.test( o.val() ) ) ) {
			o.addClass( "ui-state-error" );
			o.focus();
			qtct_updateTips( n );
			return false;
		} else {
			return true;
		
		}
	}
// End of check validate
	
	// Post du lieu cho nghien cuu khoa hoc
	$("#taoqtct").click(function(){
		 $('#qtct_act').val('add');
		 $("#formthemquatrinhcongtacdiv").dialog('option', 'title', 'Thêm quá trình công tác...');
		 $("#formthemquatrinhcongtacdiv").dialog('open');
	});
	
	$( "#formthemquatrinhcongtacdiv" ).dialog({
			autoOpen: false,
			height: 340,
			width: 610,
			modal: true,
			buttons: {
				"Ok": function() {
					var bValid = true;
					
					allFieldsqtct.removeClass( "ui-state-error" );
					
					bValid = bValid && qtct_checkLength( jtxtTu_qtct, "\"Năm bắt đầu\"", 4, 4);
					bValid = bValid && qtct_checkRegexp( jtxtTu_qtct,/^[0-9]{4,4}$/i, "Thông tin \"Năm bắt đầu\" phải là Số");
					
					if (bValid && jtxtDen_qtct.val()!="")
					{
						bValid = bValid && qtct_checkLength( jtxtDen_qtct, "\"Năm kết thúc\"", 4, 4);
						bValid = bValid && qtct_checkRegexp( jtxtDen_qtct,/^[0-9]{4,4}$/i, "Thông tin \"Năm bắt đầu\" phải là Số");
						
						if (bValid && jtxtDen_qtct.val() < jtxtTu_qtct.val())
						{
							qtct_updateTips( "Năm \"Đến\" phải lớn hơn năm \"Từ\"");
							bValid = false;
							jtxtDen_qtct.addClass( "ui-state-error" );
							jtxtDen_qtct.focus();
						}
								
					}
					
					bValid = bValid && qtct_checkLength( jtxtChuyenMon_qtct, "\"Chuyên môn\"", 0, 200);					
					//bValid = bValid && qtct_checkLength( jtxtChucVu_qtct, "\"Chức vụ\"", 0, 100);
					bValid = bValid && qtct_checkLength( jtxtNoiCongTac_qtct, "\"Nơi Công tác\"", 0, 200);
					//bValid = bValid && qtct_checkLength( jtxtDiaChi_qtct, "\"Địa chỉ\"", 0, 200);
										
					if (bValid) {
						
						$("#tipqtct").html("<table><tr><td ><img src='../images/ajax-loader.gif'></td><td valign=top style='color: black'> Đang xử lý thông tin ...</td></tr></table>");
						
						dataString = $("#form_qtct").serialize()
						+ "&cat=qtct&act=" + $("#qtct_act").val() + "&"
						+ allFieldsqtct.serialize();
						dataString +='&hisid=<?php echo $_REQUEST["hisid"];?>';
						
						//alert(dataString);
						$.ajax({
							type: "POST",
							url: "gv/gv_qtct_process.php",
							data: dataString,
							dataType: "json",
							success: function(data) {
										
										if (data.status==1)
										{
											qtct_RefreshData();	
											$("#tipqtct").html("");
										}
										else
										{
											$("#tipqtct").html("");
											if ($('#qtct_act').val()=='add')
												gv_open_msg_box("Không thể thêm quá trình công tác mới, bạn vui lòng thử lại.","alert",250,150);
											else if ($('#qtct_act').val()=='edit')
												gv_open_msg_box("Không thể cập nhật quá trình công tác, bạn vui lòng thử lại.","alert",250,150);
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
				allFieldsqtct.val( "" ).removeClass( "ui-state-error" );
			}
		});
		
	$( "#form_them_cv_qtct" ).dialog({
			autoOpen: false, height: 130, width: 300, modal: true, resizable: false,
			buttons: {
				"Ok": function() {
					var bValid = true;
					
					if ($("#txtChucVuMoi_qtct").val() == "")
					{
						bValid = false;
						gv_open_msg_box("<font style='color:red'><b>Vui lòng không để trống thông tin.</b></font>", "alert", null , 110);
					}
					
					if (bValid) {
						gv_processing_diglog("open", "Thêm chức vụ mới");
						dataString = "cat=cv_qtct_add&" + $("#txtChucVuMoi_qtct").serialize();
						dataString +='&hisid=<?php echo $_REQUEST["hisid"];?>';
						
						//alert(dataString);
						$.ajax({
							type: "POST",
							url: "gv/gv_qtct_process.php",
							data: dataString,
							dataType: "html",
							success: function(data) {
								if (data != ""){
									$("#txtChucVu_qtct").html("<option value=''></option>"+data);
								}
								gv_processing_diglog("close");
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
				//allFieldsqtct.val( "" ).removeClass( "ui-state-error" );
			}
		});
	
	$("#tao_cv_qtct").click(function(){
		$("#txtChucVuMoi_qtct").val("");
		$("#form_them_cv_qtct").dialog('option', 'title', 'Thêm chức vụ mới...');
		$("#form_them_cv_qtct").dialog('open');
		return false;
	});
	
	$("#txtChucVuMoi_qtct").change(function(){
		 $("#txtChucVuMoi_qtct").val(ucwords($("#txtChucVuMoi_qtct").val()));
	});
	
    $("#btnXoaqtct").click(function(){
		$( "#btnXoaqtct" ).button({ disabled: true });
		dataString = $("#form_qtct").serialize() + '&cat=qtct&act=del';
		dataString +='&hisid=<?php echo $_REQUEST["hisid"];?>';
		
		$.ajax({type: "POST",url: "gv/gv_qtct_process.php",data: dataString,dataType: "html",
			success: function(data) {
						qtct_RefreshData();
						$( "#btnXoaqtct" ).button({ disabled: false });
					 }// end function(data)	
		}); // end .ajax
	});	// end $("#btnXoaqtct").click(function()
	
	function qtct_RefreshData() {
		dataString = "cat=get_qtct&hisid=<?php echo $_REQUEST["hisid"];?>";
		$.ajax({
			type: "POST",
			url: "gv/gv_qtct_process.php",
			data: dataString,
			dataType: "html",
			success: function(data) {
						$("#tableqtct tbody").html(data);
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