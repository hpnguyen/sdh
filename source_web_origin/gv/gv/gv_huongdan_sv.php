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

// update sv tu bk
// Lay ds thac si, tien si tu db bk
$sqlstr="begin NCKH_UPDATE_NCS_HVCH('$macb'); end;"; 
$stmt = oci_parse($db_conn, $sqlstr);oci_execute($stmt);oci_free_statement($stmt);

?>
  
<div id = 'huongdansvdiv'>
	<form id="form_huongdansv" method="post" action="" >
	<input type="hidden" name="mahuongdansvedit" id="mahuongdansvedit" />
	<input type="hidden" name="mahvhuongdanedit" id="mahvhuongdanedit" />
    <div id = 'formthemhuongdansvdiv' title="Hướng dẫn sinh viên, học viên cao học, nghiên cứu sinh">
        <table width="100%" border="0" align="center" cellpadding="5" cellspacing="2">
          <tr class="heading">
            <td align="right" ><label for="txtBacDT_huongdansv">Bậc đào tạo</label></td>
			<td>
				<select name="txtBacDT_huongdansv" id="txtBacDT_huongdansv" class="text ui-widget-content ui-corner-all tableData" style="height:23px;font-size:15px;">
					<option value="DH"> Đại học </option>
					<option value="TH"> Thạc sĩ </option>
					<option value="TS"> Tiến sĩ </option>
				</select>
			</td>
          </tr>
		  <tr>
			<td align="right" class="heading"><label for="txtHoTen_huongdansv" id=lblHoTen_huongdansv>Họ tên</label></td>
			<td>
				<input style="width:470px" class="text ui-widget-content ui-corner-all tableData" name="txtHoTen_huongdansv" id="txtHoTen_huongdansv" type="text" maxlength="50" placeholder="họ và tên sinh viên, học viên cao học, nghiên cứu sinh"/>
			</td>
		  </tr>
		  <tr >
			<td align="right" class="heading"><label for="txtLuanAn_huongdansv">Tên luận án</label></td>
			<td  class="heading">
				<input style="width:470px" class="text ui-widget-content ui-corner-all tableData" name="txtLuanAn_huongdansv" id="txtLuanAn_huongdansv" type="text" maxlength="500" placeholder="tên luận văn, luận án"/>
			</td>
		  </tr>
		  
		  <tr >
			<td align="right" class="heading"><label for="txtTenTruong_huongdansv">Nơi đào tạo</label></td>
			<td  class="heading">
				<input style="width:470px" class="text ui-widget-content ui-corner-all tableData" name="txtTenTruong_huongdansv" id="txtTenTruong_huongdansv" type="text" maxlength="300" placeholder="tên trường đào tạo"/>
			</td>
		  </tr>
		  
		  <tr >
			<td align="right" class="heading"><label for="txtNamTN_huongdansv">Năm tốt nghiệp</label></td>
			<td  class="heading">
				<input style="width:50px" class="text ui-widget-content ui-corner-all tableData" name="txtNamTN_huongdansv" id="txtNamTN_huongdansv" type="text" maxlength="4" placeholder=""/>
				&nbsp;
				<label for="txtDeTai_huongdansv">Sản phẩm của đề tài/dự án</label>
				&nbsp;
				<input style="width:100px" list=masodetai_list_huongdansv class="text ui-widget-content ui-corner-all tableData" name="txtDeTai_huongdansv" type="text" id="txtDeTai_huongdansv" maxlength="20" placeholder="chỉ ghi mã số" />
					<datalist id=masodetai_list_huongdansv>
					<?php
						$sqlstr="select MA_SO_DE_TAI, NAM_BAT_DAU from DE_TAI_NCKH where MA_CAN_BO = '$macb' order by NAM_BAT_DAU"; 
						$stmt = oci_parse($db_conn, $sqlstr);oci_execute($stmt);$n = oci_fetch_all($stmt, $resDM);oci_free_statement($stmt);
						for ($i = 0; $i < $n; $i++){
							echo "<option value='{$resDM["MA_SO_DE_TAI"][$i]}'>";}
					?>
					</datalist>
			</td>
		  </tr>
		  
        </table>
		
		<div style="margin-top:10px" align="center" id="tiphuongdansv" class="ui-corner-all validateTips"></div>
			
    </div> <!--end formthemhuongdansvdiv -->
    
	
    
    	<table border="0" width="100%" cellspacing="0" cellpadding="0">
          <tr>
			<td align=left>
				<div style="margin:0 0 5px 5px; line-height: 20px">
				<b>Đã và đang hướng dẫn sinh viên, học viên cao học, nghiên cứu sinh</b><br>
				<b><u>Lưu ý</u></b>: Chức năng "<b>Xóa</b>" chỉ xóa được thông tin Học viên và NCS do người dùng thêm vào, các thông tin đã có sẽ không thể xóa.<br>
				<span style="margin-left:35px">"<b>Sửa</b>" chỉ cập nhật được thông tin Học viên và NCS do người dùng thêm vào, các thông tin đã có chỉ cập nhật được duy nhất thông tin <em>Mã Số Đề Tài</em>.</span>
				</div>
			</td>
            <td align="right" valign=bottom>
				<div style="margin-bottom:10px;">
						<a id="taohuongdansv" name="taohuongdansv" >&nbsp;Thêm mới</a>
				&nbsp;&nbsp;
				<a id="btnXoahuongdansv" name="btnXoahuongdansv"> &nbsp;Xóa</a>
				</div>
			</td>
          </tr>
        </table>

		<table width="100%" id="tablehuongdansv" align="center" border="0" cellspacing="0" cellpadding="5" class="ui-widget ui-widget-content ui-corner-top tableData" height="20">
        <thead>
          <tr class="ui-widget-header heading" >
            <td class="ui-corner-tl"  align="left"><em>TT</em></td>
            <td align="left" style="width:180px"><em>Tên SV, HVCH, NCS</em></td>
            <td align="left"><em>Tên luận án</em></td>
            <td align="center"><em>Năm tốt nghiệp</em></td>
            <td align="center" style="width:60px"><em>Bậc<br/>đào tạo</em></td>
			<td align="center" style="width:80px"><em>Nơi đào tạo</em></td>
			<td align="center" style="width:90px"><em>Sản phẩm của<br/>đề tài/dự án</em></td>
			
            <td >&nbsp;</td>
            <td class=" ui-corner-tr" >&nbsp;</td>
          </tr>
          </thead>
          <tbody>
		  </tbody>
        </table>
</form>
<input name="huongdansv_act" id="huongdansv_act" type="hidden" value="" />
</div>		<!-- end of huongdansvdiv -->   

<script type="text/javascript">
function gethuongdansv(pMahuongdansv, pHoTen, pLuanAn, pNamTN, pBacDT, pDetai, pMaHV, pTruong)
{
	$("#huongdansv_act").val("edit");
	$("#mahuongdansvedit").val(pMahuongdansv);
	
	if (pMaHV!='')
	{
		$('#txtHoTen_huongdansv, #txtLuanAn_huongdansv, #txtNamTN_huongdansv, #txtBacDT_huongdansv, #txtTenTruong_huongdansv').attr('disabled','disabled');
		$('#txtHoTen_huongdansv, #txtLuanAn_huongdansv, #txtNamTN_huongdansv, #txtBacDT_huongdansv, #txtTenTruong_huongdansv').addClass("disableTextBox");
	}
	else
	{
		$('#txtHoTen_huongdansv, #txtLuanAn_huongdansv, #txtNamTN_huongdansv, #txtBacDT_huongdansv, #txtTenTruong_huongdansv').removeAttr('disabled');
		$('#txtHoTen_huongdansv, #txtLuanAn_huongdansv, #txtNamTN_huongdansv, #txtBacDT_huongdansv, #txtTenTruong_huongdansv').removeClass("disableTextBox");
	}
	
	var table=document.getElementById("tablehuongdansv");
	
	document.getElementById('txtHoTen_huongdansv').value = ucwords(pHoTen);//.toLowerCase().replace(/\b[a-z]/g, function(letter) {return letter.toUpperCase();});
	document.getElementById('txtLuanAn_huongdansv').value = pLuanAn;
	document.getElementById('txtNamTN_huongdansv').value = pNamTN;
	document.getElementById('txtBacDT_huongdansv').value = pBacDT;
	document.getElementById('txtDeTai_huongdansv').value = pDetai;
	document.getElementById('txtTenTruong_huongdansv').value = pTruong;
	document.getElementById('mahvhuongdanedit').value = pMaHV;
	
	$("#formthemhuongdansvdiv").dialog('option', 'title', 'Cập nhật hướng dẫn học viên, ncs...');
	$("#formthemhuongdansvdiv").dialog('open');
}

$(function(){

  // delete btn
 $( "#btnXoahuongdansv" ).button({ icons: {primary:'ui-icon ui-icon-trash'} });
 
 // create new
 $( "#taohuongdansv" ).button({ icons: {primary:'ui-icon ui-icon-document'} });
 
 //$( "#txtNganh_huongdansv" ).combobox();
 
 // Lay du lieu moi
 huongdansv_RefreshData();
 
// Check validate fields Nghien cuu khoa hoc
var jtxtBacDT_huongdansv	= $("#txtBacDT_huongdansv"),
	jtxtLuanAn_huongdansv 	= $("#txtLuanAn_huongdansv"),
	jtxtHoTen_huongdansv	= $("#txtHoTen_huongdansv"),
	jtxtNamTN_huongdansv	= $("#txtNamTN_huongdansv"),
	jtxtDeTai_huongdansv	= $("#txtDeTai_huongdansv"),
	jtxtTenTruong_huongdansv	= $("#txtTenTruong_huongdansv"),
	jmahuongdansvedit		= $("#mahuongdansvedit"),
	jmahvhuongdanedit		= $("#mahvhuongdanedit"),
	allFieldshuongdansv = $([]).add(jtxtTenTruong_huongdansv).add(jtxtBacDT_huongdansv).add(jtxtLuanAn_huongdansv).add(jtxtHoTen_huongdansv).add(jtxtNamTN_huongdansv).add(txtDeTai_huongdansv).add(jmahvhuongdanedit).add(jmahuongdansvedit),
	tipshuongdansv			= $("#tiphuongdansv");

	// 
	function huongdansv_updateTips( t ) {
		tipshuongdansv
					.text( t )
					.addClass( "ui-state-highlight" );
		setTimeout(function() {
			tipshuongdansv.removeClass( "ui-state-highlight", 1500 );
		}, 1000 );
	}
	
	// huongdansv_checkLength
	function huongdansv_checkLength( o, n, min, max) {
		if (min==0 && (o.val().length==0))
		{	
			o.addClass( "ui-state-error" );
			o.focus();	
			huongdansv_updateTips( "Thông tin " + n + " không được phép để trống.");
			
			return false;
		}else if (min==max && o.val().length<min){
			o.addClass( "ui-state-error" );
			o.focus();	
			huongdansv_updateTips( "Thông tin " + n + " phải đủ " + min + " ký tự.");
		}else if ( o.val().length > max || o.val().length < min ) {
			o.addClass( "ui-state-error" );
			o.focus();		
			huongdansv_updateTips( "Chiều dài của " + n + " từ " +
						min + " đến " + max + " ký tự.");
			return false;
		} else {
			return true;
		}
	}
	
	// Check Regexp
	function huongdansv_checkRegexp( o, regexp, n ) {
		//alert('a');
		if ( o.val()!='' && !( regexp.test( o.val() ) ) ) {
			o.addClass( "ui-state-error" );
			o.focus();
			huongdansv_updateTips( n );
			return false;
		} else {
			return true;
		
		}
	}
// End of check validate
	
	// Post du lieu cho nghien cuu khoa hoc
	$("#taohuongdansv").click(function(){
		$('#txtHoTen_huongdansv, #txtLuanAn_huongdansv, #txtNamTN_huongdansv, #txtBacDT_huongdansv, #txtTenTruong_huongdansv').removeAttr('disabled');
		$('#txtHoTen_huongdansv, #txtLuanAn_huongdansv, #txtNamTN_huongdansv, #txtBacDT_huongdansv, #txtTenTruong_huongdansv').removeClass("disableTextBox");
		
		$('#huongdansv_act').val('add');
		$("#formthemhuongdansvdiv").dialog('option', 'title', 'Thêm hướng dẫn học viên, ncs...');
		$("#formthemhuongdansvdiv").dialog('open');
	});
	
	$( "#formthemhuongdansvdiv" ).dialog({
			autoOpen: false,
			height: 340,
			width: 620,
			modal: true,
			buttons: {
				"Ok": function() {
					var bValid = true;
					
					allFieldshuongdansv.removeClass( "ui-state-error" );
					
					bValid = bValid && huongdansv_checkLength( jtxtHoTen_huongdansv, "\"Họ tên\"", 0, 50);
					bValid = bValid && huongdansv_checkLength( jtxtLuanAn_huongdansv, "\"Tên luận án\"", 0, 1000);
					//bValid = bValid && huongdansv_checkLength( jtxtNamTN_huongdansv, "\"Năm tốt nghiệp\"", 4, 4);
					bValid = bValid && huongdansv_checkRegexp( jtxtNamTN_huongdansv,/^[0-9]{4,4}$/i, "Thông tin \"Năm tốt nghiệp\" phải là Số");
										
					if (bValid) {
						
						$("#tiphuongdansv").html("<table><tr><td ><img src='../images/ajax-loader.gif'></td><td valign=top style='color: black'> Đang xử lý thông tin ...</td></tr></table>");
						
						dataString = $("#form_huongdansv").serialize()
						+ "&cat=huongdansv&act=" + $("#huongdansv_act").val() + "&"
						+ allFieldshuongdansv.serialize();
						dataString +='&hisid=<?php echo $_REQUEST["hisid"];?>';
						
						//alert(dataString);
						$.ajax({
							type: "POST",
							url: "gv/gv_huongdan_sv_process.php",
							data: dataString,
							dataType: "json",
							success: function(data) {
										
										if (data.status==1)
										{
											huongdansv_RefreshData();	
											$("#tiphuongdansv").html("");
										}
										else
										{
											$("#tiphuongdansv").html("");
											if ($('#huongdansv_act').val()=='add')
												gv_open_msg_box("Không thể thêm mới, bạn vui lòng thử lại.","alert",250,150);
											else if ($('#huongdansv_act').val()=='edit')
												gv_open_msg_box("Không thể cập nhật thông tin, bạn vui lòng thử lại.","alert",250,150);
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
				allFieldshuongdansv.val( "" ).removeClass( "ui-state-error" );
			}
		});
	
    $("#btnXoahuongdansv").click(function(){
		$( "#btnXoahuongdansv" ).button({ disabled: true });
		dataString = $("#form_huongdansv").serialize() + '&cat=huongdansv&act=del';
		dataString +='&hisid=<?php echo $_REQUEST["hisid"];?>';
		
		$.ajax({type: "POST",url: "gv/gv_huongdan_sv_process.php",data: dataString,dataType: "html",
			success: function(data) {
						huongdansv_RefreshData();
						$( "#btnXoahuongdansv" ).button({ disabled: false });
					 }// end function(data)	
		}); // end .ajax
	});	// end $("#btnXoahuongdansv").click(function()
	
	function huongdansv_RefreshData() {
		dataString = "cat=get_huongdansv&hisid=<?php echo $_REQUEST["hisid"];?>";
		$.ajax({
			type: "POST",
			url: "gv/gv_huongdan_sv_process.php",
			data: dataString,
			dataType: "html",
			success: function(data) {
						$("#tablehuongdansv tbody").html(data);
					 }// end function(data)	
		}); // end .ajax
	}
	
	function huongdansv_UpdateSVMoi() {
		dataString = "cat=getnew_huongdansv&hisid=<?php echo $_REQUEST["hisid"];?>";
		$.ajax({
			type: "POST",
			url: "gv/gv_huongdan_sv_process.php",
			data: dataString,
			dataType: "html",
			success: function(data) {
						$("#tablehuongdansv tbody").html(data);
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