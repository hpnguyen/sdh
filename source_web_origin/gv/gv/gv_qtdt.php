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

//echo allowPermisstion(base64_decode($_SESSION['uidloginPortal']), '003');

$macb = $_SESSION['macb'];
?>
  
<div id = 'quatrinhdaotaodiv'>
	<form id="form_qtdt" method="post" action="" >
	<input type="hidden" name="maqtdtedit" id="maqtdtedit" />
	<input type="hidden" name="maNganhqtdtedit" id="maNganhqtdtedit" />
    <div id = 'formthemquatrinhdaotaodiv' title="Quá trình đào tạo">
        <table width="100%" border="0" align="center" cellpadding="5" cellspacing="2">
          <tr class="heading">
            <td align="right" ><label for="txtBacDT_QTDT">Bậc đào tạo</label></td>
			<td>
				<select style="height:25px;font-size:15px;" class="text ui-widget-content ui-corner-all tableData" name="txtBacDT_QTDT" id="txtBacDT_QTDT" onChange="qtdt_updateNganh(this.value);">
					<option value=""></option>
					<option value="TS"> Tiến sĩ </option>
					<option value="TH"> Thạc sĩ </option>
					<option value="DH"> Đại học </option>
					<option value="NH"> Ngắn hạn </option>
				</select>
				<label for="txtNganh_QTDT">Ngành</label>
				<select name="txtNganh_QTDT" id="txtNganh_QTDT" onChange="" style="height:25px;font-size:15px;width:345px" class="text ui-widget-content ui-corner-all tableData">
				  
				</select>
			</td>
          </tr>
		  <tr >
			<td align="right" valign="top" class="heading"><label for="txtNganhKhac_QTDT">Ngành khác</label></td>
			<td>
				<input style="width:485px" class="text ui-widget-content ui-corner-all tableData" name="txtNganhKhac_QTDT" id="txtNganhKhac_QTDT" type="text" maxlength="100" placeholder="tên ngành khác ghi theo bằng cấp"/>
				<br/><span >(chỉ nhập vào ô này khi mục Ngành chọn là "Khác" ở cuối danh sách Ngành)</span>
			</td>
		  </tr>
		  
		  <tr class="heading">
			<td align="right" ><label for="txtHeDT_QTDT">Hệ đào tạo</label></td>
			<td>
				<select style="height:25px;font-size:15px;" class="text ui-widget-content ui-corner-all tableData" name="txtHeDT_QTDT" id="txtHeDT_QTDT">
					<option value=""></option>
						<?  $sqlstr="select ma_he_dao_tao, ten_he_dao_tao from dm_he_dao_tao 
									order by ten_he_dao_tao";
							$stmt = oci_parse($db_conn, $sqlstr);
							oci_execute($stmt);
							$n = oci_fetch_all($stmt, $resDM);
							oci_free_statement($stmt);
							for ($i = 0; $i < $n; $i++)
								echo "<option value=\"".$resDM["MA_HE_DAO_TAO"][$i]."\"> "
								.$resDM["TEN_HE_DAO_TAO"][$i]." </option>";			
						?>
				</select>
				
				<label for="txtNamBD_QTDT">Năm bắt đầu </label>
				<input align=center style="width:50px" class="text ui-widget-content ui-corner-all tableData" name="txtNamBD_QTDT" id="txtNamBD_QTDT" type="text" maxlength="4"/>
				<label for="txtNamTN_QTDT">Năm tốt nghiệp</label>
				<input align=center style="width:50px" class="text ui-widget-content ui-corner-all tableData" name="txtNamTN_QTDT" id="txtNamTN_QTDT" type="text" maxlength="4"/>
			</td>
		  </tr>
		  
		  
		  
		  <tr class="heading">
			<td align="right" ><label for="txtNoiDT_QTDT">Nơi đào tạo</label></td>
			<td>
				<input style="width:235px" class="text ui-widget-content ui-corner-all tableData" name="txtNoiDT_QTDT" id="txtNoiDT_QTDT" type="text" maxlength="100" placeholder="tên trường đào tạo"/>
				<label for="txtQuocGiaDT_QTDT">Quốc gia</label>
				<select style="height:25px;font-size:15px;" class="text ui-widget-content ui-corner-all tableData" name="txtQuocGiaDT_QTDT" id="txtQuocGiaDT_QTDT" >
					<option value=""></option>
						<?  $sqlstr="select * from QUOC_GIA order by ten_quoc_gia";
							$stmt = oci_parse($db_conn, $sqlstr);oci_execute($stmt);$n = oci_fetch_all($stmt, $resDM);oci_free_statement($stmt);
							for ($i = 0; $i < $n; $i++)
								echo "<option value=\"".$resDM["MA_QUOC_GIA"][$i]."\"> "
								.$resDM["TEN_QUOC_GIA"][$i]." </option>";
						?>
				</select>
			</td>
		  </tr>
		  
		  <tr>
			<td align="right" class="heading"><label for="txtTenLALV_QTDT" id=lblTenLALV_QTDT>Tên LA</label></td>
			<td>
				<input style="width:485px" class="text ui-widget-content ui-corner-all tableData" name="txtTenLALV_QTDT" id="txtTenLALV_QTDT" type="text" maxlength="500" placeholder="tên luận án - luận văn"/>
			</td>
		  </tr>

        </table>
		
		<div style="margin-top:10px" align="center" id="tipQTDT" class="ui-corner-all validateTips"></div>
			
    </div> <!--end formthemquatrinhdaotaodiv -->
    
	
    
    	<table border="0" width="100%" cellspacing="0" cellpadding="0">
          <tr>
			<td align=left>
				<div style="margin-left:5px;font-weight:bold">Quá trình đào tạo</div>
			</td>
            <td align="right" >
				<div style="margin-bottom:10px;">
						<a id="taoQTDT" name="taoQTDT" >&nbsp;Thêm đào tạo... mới</a>
				&nbsp;&nbsp;
				<a id="btnXoaQTDT" name="btnXoaQTDT"> &nbsp;Xóa</a>
				</div>
			</td>
          </tr>
        </table>

		<table width="100%" id="tableQTDT" align="center" border="0" cellspacing="0" cellpadding="5" class="ui-widget ui-widget-content ui-corner-top tableData" height="20">
        <thead>
          <tr class="ui-widget-header heading" >
            <td class=" ui-corner-tl" style="width:80px"><em>Bậc đào tạo</em></td>
            <td align="left"><em>Hệ ĐT</em></td>
			<td align="left"><em>Ngành đào tạo</em></td>
            <td ><em>Nơi đào tạo</em></td>
            <td ><em>Quốc gia</em></td>
            <td align="left"><em>Tên luận án</em></td>
            <td style="width:50px"><em>Năm BĐ</em></td>
            <td style="width:50px"><em>Năm TN</em></td>
            <td >&nbsp;</td>
            <td class=" ui-corner-tr" >&nbsp;</td>
          </tr>
          </thead>
          <tbody>
		  </tbody>
        </table>
</form>
<input name="qtdt_act" id="qtdt_act" type="hidden" value="" />
</div>		<!-- end of quatrinhdaotaodiv -->   

<script type="text/javascript">
function qtdt_updateNganh(p_bacdt, p_nganh_default)
{
	if (p_bacdt!='' )
	{
		dataString = 'hisid=<?php echo $_REQUEST["hisid"];?>';
		dataString += '&cat=bacdt-nganh&b='+p_bacdt+'&n='+p_nganh_default;
		$("#tipQTDT").html("<table><tr><td ><img src='../images/ajax-loader.gif'></td><td valign=top style='color: black'> Đang cập nhật Ngành đào tạo ...</td></tr></table>");
		
		xreq = $.ajax({
		  type: 'POST', url: 'gv/gv_qtdt_process.php', data: dataString, dataType: "html",
		  success: function(data) {
			$("#txtNganh_QTDT").html(data);
			$("#tipQTDT").html("");
		  },
		  error: function(xhr, ajaxOptions, thrownError) {
			//$("#dkmh_monhoc_chitiet").html(thrownError);
		  }
		});
	}
}

function getQTDT(index, mabac, manganh, maquocgia, nambd, namtn, hedt)
{
	//alert(index);
	$("#qtdt_act").val("edit");
	$("#maNganhqtdtedit").val(manganh);
	
	var table=document.getElementById("tableQTDT");
	
	qtdt_updateNganh(mabac, manganh);
	
	document.getElementById('txtBacDT_QTDT').value = mabac;
	document.getElementById('txtHeDT_QTDT').value = hedt;
	document.getElementById('txtQuocGiaDT_QTDT').value = maquocgia;
	document.getElementById('txtNamBD_QTDT').value = nambd;
	document.getElementById('txtNamTN_QTDT').value = namtn;
	
	if (manganh=='99999999')
		$("#txtNganhKhac_QTDT").val(table.rows[index].cells[2].innerHTML);
	
	$("#txtNoiDT_QTDT").val(table.rows[index].cells[3].innerHTML);
	$("#txtTenLALV_QTDT").val(table.rows[index].cells[5].innerHTML);
	
	document.getElementById('txtBacDT_QTDT').disabled=true;
	
	$("#formthemquatrinhdaotaodiv").dialog('option', 'title', 'Cập nhật quá trình đào tạo...');
	$("#formthemquatrinhdaotaodiv").dialog('open');
}

//jQuery.ajax
//$(document).ready(function(){
$(function(){

  // delete btn
 $( "#btnXoaQTDT" ).button({ icons: {primary:'ui-icon ui-icon-trash'} });
 
 // create new
 $( "#taoQTDT" ).button({ icons: {primary:'ui-icon ui-icon-document'} });
 
 //$( "#txtNganh_QTDT" ).combobox();
 
 // Lay du lieu moi
 qtdt_RefreshData();
 
// Check validate fields Nghien cuu khoa hoc
var jtxtBacDT_QTDT		= $("#txtBacDT_QTDT"),
	jtxtHeDT_QTDT		= $("#txtHeDT_QTDT"),
	jtxtNganh_QTDT 		= $("#txtNganh_QTDT"),
	jtxtNganhKhac_QTDT	= $("#txtNganhKhac_QTDT"),
	jtxtNoiDT_QTDT		= $("#txtNoiDT_QTDT"),
	jtxtQuocGiaDT_QTDT	= $("#txtQuocGiaDT_QTDT"),
	jtxtTenLALV_QTDT	= $("#txtTenLALV_QTDT"),
	jtxtNamBD_QTDT		= $("#txtNamBD_QTDT"),
	jtxtNamTN_QTDT		= $("#txtNamTN_QTDT"),
	jmaqtdtedit			= $("#maqtdtedit"),
	allFieldsQTDT = $([]).add(jtxtBacDT_QTDT).add(jtxtHeDT_QTDT).add(jtxtNganh_QTDT).add(jtxtNganhKhac_QTDT).add(jtxtNoiDT_QTDT).add(jtxtQuocGiaDT_QTDT).add(jtxtTenLALV_QTDT).add(jtxtNamBD_QTDT).add(jtxtNamTN_QTDT),
	tipsQTDT			= $("#tipQTDT");
		
	// 
	function qtdt_updateTips( t ) {
		tipsQTDT
					.text( t )
					.addClass( "ui-state-highlight" );
		setTimeout(function() {
			tipsQTDT.removeClass( "ui-state-highlight", 1500 );
		}, 1000 );
	}
	
	// qtdt_checkLength
	function qtdt_checkLength( o, n, min, max) {
		if (min==0 && (o.val().length==0))
		{	
			o.addClass( "ui-state-error" );
			o.focus();	
			qtdt_updateTips( "Thông tin " + n + " không được phép để trống.");
			
			return false;
		}else if (min==max && o.val().length<min){
			o.addClass( "ui-state-error" );
			o.focus();	
			qtdt_updateTips( "Thông tin " + n + " phải đủ " + min + " ký tự.");
		}else if ( o.val().length > max || o.val().length < min ) {
			o.addClass( "ui-state-error" );
			o.focus();		
			qtdt_updateTips( "Chiều dài của " + n + " từ " +
						min + " đến " + max + " ký tự.");
			return false;
		} else {
			return true;
		}
	}
	
	// Check Regexp
	function qtdt_checkRegexp( o, regexp, n ) {
		//alert('a');
		if ( o.val()!='' && !( regexp.test( o.val() ) ) ) {
			o.addClass( "ui-state-error" );
			o.focus();
			qtdt_updateTips( n );
			return false;
		} else {
			return true;
		
		}
	}
// End of check validate
	
	// Post du lieu cho nghien cuu khoa hoc
	$("#taoQTDT").click(function(){
		 $('#qtdt_act').val('add');
		 document.getElementById('txtBacDT_QTDT').disabled=false;
		 $("#formthemquatrinhdaotaodiv").dialog('option', 'title', 'Thêm quá trình đào tạo...');
		 $("#formthemquatrinhdaotaodiv").dialog('open');
	});
	
	$( "#formthemquatrinhdaotaodiv" ).dialog({
			autoOpen: false,
			height: 350,
			width: 635,
			modal: true,
			buttons: {
				"Ok": function() {
					var bValid = true;
					
					allFieldsQTDT.removeClass( "ui-state-error" );
					bValid = bValid && qtdt_checkLength( jtxtBacDT_QTDT, "\"Bậc đào tạo\"", 0, 10);
					bValid = bValid && qtdt_checkLength( jtxtNganh_QTDT, "\"Ngành đào tạo\"", 0, 10);
					if (jtxtNganh_QTDT.val()=='99999999')
						bValid = bValid && qtdt_checkLength( jtxtNganhKhac_QTDT, "\"Ngành khác\"", 0, 100);
					
					//bValid = bValid && qtdt_checkLength( jtxtNamBD_QTDT, "\"Năm bắt đầu\"", 4, 4);
					if (jtxtNamBD_QTDT.val() != '')
						bValid = bValid && qtdt_checkRegexp( jtxtNamBD_QTDT,/^[0-9]{4,4}$/i, "Thông tin \"Năm bắt đầu\" phải là Số");
					
					bValid = bValid && qtdt_checkLength( jtxtNamTN_QTDT, "\"Năm tốt nghiệp\"", 4, 4);
					bValid = bValid && qtdt_checkRegexp( jtxtNamTN_QTDT,/^[0-9]{4,4}$/i, "Thông tin \"Năm tốt nghiệp\" phải là Số");
					
					bValid = bValid && qtdt_checkLength( jtxtNoiDT_QTDT, "\"Nơi đào tạo\"", 0, 100);
					bValid = bValid && qtdt_checkLength( jtxtQuocGiaDT_QTDT, "\"Quốc gia đào tạo\"", 0, 10);
					bValid = bValid && qtdt_checkLength( jtxtTenLALV_QTDT, "\"Tên luận án/luận văn\"", 0, 500);
					
					
					if (bValid)
					{
						if (jtxtNamTN_QTDT.val() < jtxtNamBD_QTDT.val())
						{
							bValid = false;
							jtxtNamTN_QTDT.focus();
							jtxtNamTN_QTDT.addClass( "ui-state-error" );
							qtdt_updateTips("Năm tốt nghiệp phải lớn hơn năm bắt đầu");
						}
					}
					//alert (jtxtBacDT_QTDT.val());
					
					if (bValid) {
						
						$("#tipQTDT").html("<table><tr><td ><img src='../images/ajax-loader.gif'></td><td valign=top style='color: black'> Đang xử lý thông tin ...</td></tr></table>");
						
						document.getElementById('txtBacDT_QTDT').disabled=false;
						
						dataString = $("#form_qtdt").serialize()
						+ "&cat=qtdt&act=" + $("#qtdt_act").val() + "&"
						+ allFieldsQTDT.serialize();
						dataString +='&hisid=<?php echo $_REQUEST["hisid"];?>';
						
						//alert(dataString);
						$.ajax({
							type: "POST",
							url: "gv/gv_qtdt_process.php",
							data: dataString,
							dataType: "json",
							success: function(data) {
										
										if (data.status==1)
										{
											qtdt_RefreshData();	
											$("#tipQTDT").html("");
										}
										else
										{
											$("#tipQTDT").html("");
											if ($('#qtdt_act').val()=='add')
												gv_open_msg_box("Không thể thêm quá trình đạo tạo mới, bạn vui lòng thử lại.","alert",250,150);
											else if ($('#qtdt_act').val()=='edit')
												gv_open_msg_box("Không thể cập nhật quá trình đào tạo, bạn vui lòng thử lại.","alert",250,150);
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
				allFieldsQTDT.val( "" ).removeClass( "ui-state-error" );
			}
		});
	
    $("#btnXoaQTDT").click(function(){
		$( "#btnXoaQTDT" ).button({ disabled: true });
		dataString = $("#form_qtdt").serialize() + '&cat=qtdt&act=del';
		dataString +='&hisid=<?php echo $_REQUEST["hisid"];?>';
		
		$.ajax({type: "POST",url: "gv/gv_qtdt_process.php",data: dataString,dataType: "html",
			success: function(data) {
						qtdt_RefreshData();
						$( "#btnXoaQTDT" ).button({ disabled: false });
					 }// end function(data)	
		}); // end .ajax
	});	// end $("#btnXoaQTDT").click(function()
	
	function qtdt_RefreshData() {
		dataString = "cat=get_qtdt&hisid=<?php echo $_REQUEST["hisid"];?>";
		$.ajax({
			type: "POST",
			url: "gv/gv_qtdt_process.php",
			data: dataString,
			dataType: "html",
			success: function(data) {
						$("#tableQTDT tbody").html(data);
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