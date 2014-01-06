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

<div id="linhvucchuyenmondiv">     
	<form id="form_lvcm" method="post" action="" >
          <div id="formthemlinhvucchuyenmondiv" title="Lĩnh vực chuyên môn">
                
                <table width="100%" border="0"  cellpadding="5" >
					<tr align="left" >
						<td style="width:40px;font-weight:bold">Năm</td>
                        <td >
                            <input style="width:50px" class="text ui-widget-content ui-corner-all tableData" name="txtNam_LVCM" id="txtNam_LVCM" type="text" maxlength="4" />     
                        </td>
                    </tr>
					
                    <tr align="left" >
						<td style="width:40px;font-weight:bold">Cấp 1</td>
                        <td >
                            <select style="height:25px;font-size:15px;width:290px" class="text ui-widget-content ui-corner-all tableData" name="txtLinhVucChuyenMon1_LVCM" id="txtLinhVucChuyenMon1_LVCM" onChange="lvcm_updateLVCM(this.value, '', '#txtLinhVucChuyenMon2_LVCM');">
									<option value="">Chọn chuyên môn</option>
									<?  $sqlstr="select ma_lvnc, ten_lvnc, ma_lvnc_cha, viet0dau_name(ten_lvnc) ten_lvnc_orderby from NCKH_LVNC_KHCN where ma_lvnc_cha is null order by ten_lvnc_orderby";
										$stmt = oci_parse($db_conn, $sqlstr);oci_execute($stmt);$n = oci_fetch_all($stmt, $resDM);oci_free_statement($stmt);
										for ($i = 0; $i < $n; $i++)
										{
											echo "<option value=\"".$resDM["MA_LVNC"][$i]."\">".$resDM["TEN_LVNC"][$i]."</option>";
										}
									?>
							</select>        
                        </td>
                    </tr>
					
					<tr align="left" >
						<td style="width:40px;font-weight:bold">Cấp 2</td>
                        <td >
                            <select style="height:25px;font-size:15px;width:290px" class="text ui-widget-content ui-corner-all tableData" name="txtLinhVucChuyenMon2_LVCM" id="txtLinhVucChuyenMon2_LVCM" onChange="lvcm_updateLVCM(this.value, '', '#txtLinhVucChuyenMon3_LVCM');" >
							</select>        
                        </td>
                    </tr>
					
					<tr align="left" >
						<td style="width:40px;font-weight:bold">Cấp 3</td>
                        <td >
                            <select style="height:25px;font-size:15px;width:290px" class="text ui-widget-content ui-corner-all tableData" name="txtLinhVucChuyenMon3_LVCM" id="txtLinhVucChuyenMon3_LVCM" >
							</select>        
                        </td>
                    </tr>
					
					<tr align="left" >
                        <td colspan=2 align=left>
							 <input style="width:330px" class="text ui-widget-content ui-corner-all tableData" name="txtChuyenMonKhac_LVCM" id="txtChuyenMonKhac_LVCM" type="text" maxlength="100" placeholder="chuyên môn khác"/>
							<br/>(Chỉ nhập vào ô này nếu chuyên môn cấp 2, 3 là chuyên môn <b>khác</b>)
                        </td>
                    </tr>
					
                </table>
				<div style="margin-top:10px" align="center" id="tipLVCM" class="ui-corner-all validateTips"></div>
       
          </div>
          
  
    <table border="0" cellspacing="0" cellpadding="0" width="100%">
		<tr>
			<td align=left>
				<div style="margin-left:5px;font-weight:bold">Lĩnh vực chuyên môn</div>
			</td>
			<td align="right">
				<div style="margin-bottom:10px;">
					<a name="newLinhVucChuyenMon" id="newLinhVucChuyenMon">&nbsp;Thêm lĩnh vực mới</a>
				  &nbsp;&nbsp;
				  <a id="deleteLinhVucChuyenMon" name="deleteLinhVucChuyenMon">&nbsp;Xóa</a>
				</div>
			</td>
        </tr>
    </table>
    
    
    <table id="tableLVCM" width="100%" border="0" align="center" cellpadding="5" cellspacing="0" class="ui-widget ui-widget-content ui-corner-top tableData" height="20">
		<thead>
			<tr class="ui-widget-header heading" >
			  <td width="30" class=" ui-corner-tl"></td>
			  <td align="left" valign="middle" ><em>Lĩnh vực chuyên môn</em></td>
			  <td align="left" valign="middle" ><em>Năm</em></td>
			  <td width="36" valign="middle" >&nbsp;</td>
			  <td width="39" align="left" valign="middle" class=" ui-corner-tr">&nbsp;</td>
			</tr>
        </thead>
		<tbody>
		  
        </tbody>
      
      </table>
	  
		<input name="lvcm_act" id="lvcm_act" type="hidden" value="" />
		<input name="maLVCMedit" id="maLVCMedit" type="hidden" value="" />	
    </form>
	
  </div>   <!-- end of "linhvucchuyenmondiv" -->     

<script type="text/javascript">
//jQuery.ajax
//$(document).ready(function(){
var title_dialog_new_lvnc = "Lĩnh vực chuyên môn";

function getLVCM(index, p_mlvcm, p_lvnckhac, p_nam){
	//alert(index);
	$( "#formthemlinhvucchuyenmondiv" ).dialog('option', 'title', 'Cập nhật lĩnh vực đào tạo...');
	
	$('#lvcm_act').val('edit');
	$("#maLVCMedit").val(p_mlvcm);
	
	ma_c2 = p_mlvcm.substring(0,3);
	ma_c1 = ma_c2.substring(0,1);

	$("#txtNam_LVCM").val(p_nam);
	
	$("#txtLinhVucChuyenMon1_LVCM").val(ma_c1);
	lvcm_updateLVCM(ma_c1, ma_c2, '#txtLinhVucChuyenMon2_LVCM');
	
	$( "#formthemlinhvucchuyenmondiv" ).dialog('option', 'title', 'Cập nhật lĩnh vực đào tạo...');
	
	lvcm_updateLVCM(ma_c2, p_mlvcm, '#txtLinhVucChuyenMon3_LVCM');
	
	$("#txtChuyenMonKhac_LVCM").val(p_lvnckhac);
	
	$("#tipLVCM").html( "" );
	
	$( "#formthemlinhvucchuyenmondiv" ).dialog('open');
}

function lvcm_RefreshData() 
{
	dataString = "cat=get_lvcm&hisid=<?php echo $_REQUEST["hisid"];?>";
	$.ajax({
		type: "POST",
		url: "gv/gv_linhvucchuyenmon_process.php",
		data: dataString,
		dataType: "html",
		success: function(data) {
					$("#tableLVCM tbody").html(data);
				}// end function(data)	
	}); // end .ajax
}

function lvcm_updateLVCM(p_lvcm_cha, p_lvcm_con_default, p_name_obj)
{
	if (p_lvcm_cha!='' )
	{
		var title = $( "#formthemlinhvucchuyenmondiv" ).dialog( "option", "title" );
		//alert(title);
		
		dataString = 'hisid=<?php echo $_REQUEST["hisid"];?>';
		dataString += '&cat=lvcmCha-lvcmCon&cha='+p_lvcm_cha+'&con='+p_lvcm_con_default;
		$('#formthemlinhvucchuyenmondiv').dialog('option', 'title', "Đang cập nhật LVCM Cấp 2 ...");
		$(p_name_obj).html("");
		
		
		
		if (p_name_obj=="#txtLinhVucChuyenMon2_LVCM")
			$('#txtLinhVucChuyenMon3_LVCM').html("");
		
		xreq = $.ajax({
		  type: 'POST', url: 'gv/gv_linhvucchuyenmon_process.php', data: dataString, dataType: "html",
		  success: function(data) {
			$(p_name_obj).html(data);
			$('#formthemlinhvucchuyenmondiv').dialog('option', 'title', title);
		  },
		  error: function(xhr, ajaxOptions, thrownError) {
			//$("#dkmh_monhoc_chitiet").html(thrownError);
		  }
		});
	}
}

$(function(){

 $( "#deleteLinhVucChuyenMon" ).button({ icons: {primary:'ui-icon ui-icon-trash'} });
 
 // create new
 $( "#newLinhVucChuyenMon" ).button({ icons: {primary:'ui-icon ui-icon-document'} });
 
lvcm_RefreshData();

var jtxtLinhVucChuyenMon1_LVCM	= $("#txtLinhVucChuyenMon1_LVCM"),
	jtxtLinhVucChuyenMon2_LVCM	= $("#txtLinhVucChuyenMon2_LVCM"),
	jtxtLinhVucChuyenMon3_LVCM	= $("#txtLinhVucChuyenMon3_LVCM"),
	jtxtLinhVucChuyenMonKhac_LVCM	= $("#txtChuyenMonKhac_LVCM"),
	jtxtNam_LVCM	= $("#txtNam_LVCM"),
	jmaLVCMedit		= $("#maLVCMedit"),
	allFieldsDT 	= $( [] ).add(jtxtNam_LVCM).add(jmaLVCMedit).add(jtxtLinhVucChuyenMon1_LVCM).add(jtxtLinhVucChuyenMon2_LVCM).add(jtxtLinhVucChuyenMon3_LVCM).add(jtxtLinhVucChuyenMonKhac_LVCM),
	lvcm_tips 		= $("#tipLVCM");
		
	// 
	function lvcm_updateTips( t ) {
		lvcm_tips
					.text( t )
					.addClass( "ui-state-highlight" );
		setTimeout(function() {
			lvcm_tips.removeClass( "ui-state-highlight", 1500 );
		}, 1000 );
	}
	
	
	function lvcm_checkLength( o, n, min, max) {
		if (min==0 && (o.val().length==0))
		{	
			o.addClass( "ui-state-error" );
			o.focus();	
			lvcm_updateTips( "Thông tin " + n + " không được phép để trống.");
			
			return false;
		}else if (min==max && o.val().length<min){
			o.addClass( "ui-state-error" );
			o.focus();	
			lvcm_updateTips( "Thông tin " + n + " phải đủ " + min + " ký tự.");
		}else if ( o.val().length > max || o.val().length < min ) {
			o.addClass( "ui-state-error" );
			o.focus();		
			lvcm_updateTips( "Chiều dài của " + n + " từ " +
						min + " đến " + max + " ký tự.");
			return false;
		} else {
			return true;
		}
	}
	
	// Check Regexp
	function lvcm_checkRegexp( o, regexp, n ) {
		//alert('a');
		if ( o.val()!='' && !( regexp.test( o.val() ) ) ) {
			o.addClass( "ui-state-error" );
			o.focus();
			lvcm_updateTips( n );
			return false;
		} else {
			return true;
		
		}
	}
// End of check validate
	
	
	
	// Post du lieu cho de tai
	$("#newLinhVucChuyenMon").click(function() {
		$("#lvcm_act").val("add");
		$("#formthemlinhvucchuyenmondiv").dialog( "open" );
	});
	
	$("#deleteLinhVucChuyenMon").click(function(){
		$( "#deleteLinhVucChuyenMon" ).button({ disabled: true });
		dataString = $("#form_lvcm").serialize() + '&cat=lvcm&act=del';
		dataString +='&hisid=<?php echo $_REQUEST["hisid"];?>';
		
		$.ajax({type: "POST",url: "gv/gv_linhvucchuyenmon_process.php",data: dataString,dataType: "html",
			success: function(data) {
						lvcm_RefreshData();
						$( "#deleteLinhVucChuyenMon" ).button({ disabled: false });
					 }// end function(data)	
		}); // end .ajax
	});	// end $("#deleteLinhVucChuyenMon").click(function()
	
	$( "#formthemlinhvucchuyenmondiv" ).dialog({
			autoOpen: false,
			height: 350,
			width: 370,
			modal: true,
			buttons: {
				"Ok": function() {
					var bValid = true;
					
					allFieldsDT.removeClass( "ui-state-error" );
					
					bValid = bValid && lvcm_checkLength( jtxtNam_LVCM, "\"Năm nghiên cứu\"", 4, 4);
					bValid = bValid && lvcm_checkLength( jtxtNam_LVCM,/^[0-9]{4,4}$/i, "Thông tin năm phải là Số");
					
					bValid = bValid && lvcm_checkLength( jtxtLinhVucChuyenMon1_LVCM, "\"Chuyên môn Cấp 1\"", 0, 1000);					
					bValid = bValid && lvcm_checkLength( jtxtLinhVucChuyenMon2_LVCM, "\"Chuyên môn Cấp 2\"", 0, 1000);
					
					var strc2 = jtxtLinhVucChuyenMon2_LVCM.val();
					var strc3 = jtxtLinhVucChuyenMon3_LVCM.val();
					if (strc2 != '')
					{
						strc2 = strc2.substring(strc2.length-2,strc2.length);
						
						// Cấp 2 chọn Khác
						if (strc2=='99')
							bValid = bValid && lvcm_checkLength( jtxtLinhVucChuyenMonKhac_LVCM, "\"Chuyên môn khác\"", 0, 1000);
						// Cấp 2 chọn trong danh mục, kiểm tra tới cấp 3
						else
							bValid = bValid && lvcm_checkLength( jtxtLinhVucChuyenMon3_LVCM, "\"Chuyên môn Cấp 3\"", 0, 1000);
					}
					
					if (strc3 != '')
					{
						strc3 = strc3.substring(strc3.length-2,strc3.length);
						if (strc3=='99')
							bValid = bValid && lvcm_checkLength( jtxtLinhVucChuyenMonKhac_LVCM, "\"Chuyên môn khác\"", 0, 1000);
					}
					
					//alert (jmaLVCMedit.val());
					if (bValid){
						
						datastring = $("#form_lvcm").serialize()
						+ '&cat=lvcm&act='+$('#lvcm_act').val()+'&'
						+ allFieldsDT.serialize();
						datastring +='&hisid=<?php echo $_REQUEST["hisid"];?>';
 						
						//alert(datastring);
						
						$.post("gv/gv_linhvucchuyenmon_process.php", datastring ,
						function(data){
							//alert(data);
							if (data.status==1)
							{
								lvcm_RefreshData();
							}
							//$("#tableLVCM tbody").html(data);						
						}, "json");
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
				allFieldsDT.val( "" ).removeClass( "ui-state-error" );
			}
		});

});

</script>

<?php 

if (isset ($db_conn))
	oci_close($db_conn);
?>