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
  
<div id = 'ungdungthuctiendiv'>
	<form id="form_ungdungthuctien" method="post" action="" >
	<input type="hidden" name="maungdungthuctienedit" id="maungdungthuctienedit" />
    <div id = 'formthemungdungthuctiendiv' title="Ứng dụng thực tiễn">
        <table width="100%" border="0" align="center" cellpadding="5" cellspacing="2">
		  <tr>
			<td align="right" class="heading"><label for="txtTenCongNghe_ungdungthuctien">Tên công nghệ</label></td>
			<td>
				<input style="width:470px" class="text ui-widget-content ui-corner-all tableData" name="txtTenCongNghe_ungdungthuctien" id="txtTenCongNghe_ungdungthuctien" type="text" maxlength="500" placeholder="công nghệ/giải pháp hữu ích đã chuyển giao"/>
			</td>
		  </tr>
		  <tr>
			<td align="right" class="heading"><label for="txtHinhThuc_ungdungthuctien">Hình thức</label></td>
			<td>
				<input style="width:470px" class="text ui-widget-content ui-corner-all tableData" name="txtHinhThuc_ungdungthuctien" id="txtHinhThuc_ungdungthuctien" type="text" maxlength="200" placeholder=""/>
			</td>
		  </tr>
		  <tr>
			<td align="right" class="heading"><label for="txtQuyMo_ungdungthuctien">Quy mô</label></td>
			<td>
				<input style="width:470px" class="text ui-widget-content ui-corner-all tableData" name="txtQuyMo_ungdungthuctien" id="txtQuyMo_ungdungthuctien" type="text" maxlength="200" placeholder=""/>
			</td>
		  </tr>
		  <tr>
			<td align="right" class="heading"><label for="txtDiaChi_ungdungthuctien">Địa chỉ áp dụng</label></td>
			<td>
				<input style="width:470px" class="text ui-widget-content ui-corner-all tableData" name="txtDiaChi_ungdungthuctien" id="txtDiaChi_ungdungthuctien" type="text" maxlength="250" placeholder=""/>
			</td>
		  </tr>
		  
		  <tr class="heading">
            <td align="right" ><label for="txtNamBD_ungdungthuctien">Năm bắt đầu</label></td>
			<td>
				<input style="width:50px" class="text ui-widget-content ui-corner-all tableData" name="txtNamBD_ungdungthuctien" id="txtNamBD_ungdungthuctien" type="text" maxlength="4" placeholder=""/>
				<label for="txtNamKT_ungdungthuctien">Năm kết thúc</label>
				<input style="width:50px" class="text ui-widget-content ui-corner-all tableData" name="txtNamKT_ungdungthuctien" id="txtNamKT_ungdungthuctien" type="text" maxlength="4" placeholder=""/>
				<label for="txtNamCG_ungdungthuctien">Năm chuyển giao</label>
				<input style="width:50px" class="text ui-widget-content ui-corner-all tableData" name="txtNamCG_ungdungthuctien" id="txtNamCG_ungdungthuctien" type="text" maxlength="4" placeholder=""/>
			</td>
          </tr>
		  
		  <tr class="heading">
            <td align="right" ><label for="txtDeTai_ungdungthuctien">SP của đề tài/dự án</label></td>
			<td>
				<input style="width:120px" list="masodetai_list_ungdungthuctien" class="text ui-widget-content ui-corner-all tableData" name="txtDeTai_ungdungthuctien" id="txtDeTai_ungdungthuctien" type="text" maxlength="25" placeholder="chỉ ghi mã số"/>
				<datalist id="masodetai_list_ungdungthuctien">
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
		
		<div style="margin-top:10px" align="center" id="tipungdungthuctien" class="ui-corner-all validateTips"></div>
			
    </div> <!--end formthemungdungthuctiendiv -->
    
	
    
    	<table border="0" width="100%" cellspacing="0" cellpadding="0">
          <tr>
			<td>
				<div style="margin-left:5px;font-weight:bold">
					Ứng dụng thực tiễn và thương mại hóa kết quả nghiên cứu
				</div>
			</td>
            <td align="right" >
				<div style="margin-bottom:10px;">
						<a id="taoungdungthuctien" name="taoungdungthuctien" >&nbsp;Thêm ứng dụng...</a>
				&nbsp;&nbsp;
				<a id="btnXoaungdungthuctien" name="btnXoaungdungthuctien"> &nbsp;Xóa</a>
				</div>
			</td>
          </tr>
        </table>

		<table width="100%" id="tableungdungthuctien" align="center" border="0" cellspacing="0" cellpadding="5" class="ui-widget ui-widget-content ui-corner-top tableData" height="20">
        <thead>
          <tr class="ui-widget-header heading" >
            <td class="ui-corner-tl"  align="left" style="width:30px"><em>TT</em></td>
            <td align="left"><em>Tên công nghệ/giải pháp hữu ích đã chuyển giao</em></td>
			<td align="left"><em>Hình thức, quy mô, địa chỉ áp dụng</em></td>
			<td align="center" style="width:120px"><em>Thời gian<br/><span style="font-weight:normal">(bắt đầu - kết thúc)</span></em></td>
			<td align="center" style="width:80px"><em>Năm<br/>chuyển giao</em></td>
            <td align="center" style="width:100px"><em>Sản phẩm của<br/>đề tài/dự án</em></td>
            <td style="width:30px">&nbsp;</td>
            <td class=" ui-corner-tr" style="width:20px">&nbsp;</td>
          </tr>
          </thead>
          <tbody>
		  </tbody>
        </table>
		<input name="ungdungthuctien_act" id="ungdungthuctien_act" type="hidden" value="" />
</form>
</div>		<!-- end of ungdungthuctiendiv -->   

<script type="text/javascript">

function getungdungthuctien_udtt(pMabang, pTenCongNghe, pHinhThuc, pQuyMo, pDiaChi, pThoiGian, pMaDeTai, pNamBatDau, pNamKetThuc)
{
	//alert(index); '$txtMaungdungthuctien', '$txtNamBD','$txtNamKT','$txtNoiCongTac','$txtChuyenMon','$txtDiaChi'
	$("#ungdungthuctien_act").val("edit");
	
	//var table=document.getElementById("tableungdungthuctien");
	
	$("#maungdungthuctienedit").val(pMabang);
	document.getElementById('txtTenCongNghe_ungdungthuctien').value = pTenCongNghe;
	document.getElementById('txtHinhThuc_ungdungthuctien').value = pHinhThuc;
	document.getElementById('txtQuyMo_ungdungthuctien').value = pQuyMo;
	document.getElementById('txtDiaChi_ungdungthuctien').value = pDiaChi;
	document.getElementById('txtDeTai_ungdungthuctien').value = pMaDeTai;
	document.getElementById('txtNamCG_ungdungthuctien').value = pThoiGian;
	document.getElementById('txtNamBD_ungdungthuctien').value = pNamBatDau;
	document.getElementById('txtNamKT_ungdungthuctien').value = pNamKetThuc;
	
	$("#formthemungdungthuctiendiv").dialog('option', 'title', 'Cập nhật ứng dụng thực tiễn...');
	$("#formthemungdungthuctiendiv").dialog('open');
}

//jQuery.ajax
//$(document).ready(function(){
$(function(){

  // delete btn
 $( "#btnXoaungdungthuctien" ).button({ icons: {primary:'ui-icon ui-icon-trash'} });
 
 // create new
 $( "#taoungdungthuctien" ).button({ icons: {primary:'ui-icon ui-icon-document'} });
 
 // Lay du lieu moi
 ungdungthuctien_RefreshData();
 
// Check validate fields Nghien cuu khoa hoc
var jtxtNamCG_ungdungthuctien			= $("#txtNamCG_ungdungthuctien"),
	jtxtNamBD_ungdungthuctien			= $("#txtNamBD_ungdungthuctien"),
	jtxtNamKT_ungdungthuctien			= $("#txtNamKT_ungdungthuctien"),
	jtxtTenCongNghe_ungdungthuctien		= $("#txtTenCongNghe_ungdungthuctien"),
	jtxtHinhThuc_ungdungthuctien		= $("#txtHinhThuc_ungdungthuctien"),
	jtxtQuyMo_ungdungthuctien	 		= $("#txtQuyMo_ungdungthuctien"),
	jtxtDiaChi_ungdungthuctien			= $("#txtDiaChi_ungdungthuctien"),
	jtxtDeTai_ungdungthuctien			= $("#txtDeTai_ungdungthuctien"),
	jmaungdungthuctienedit				= $("#maungdungthuctienedit"),
	allFieldsungdungthuctien = $([]).add(jtxtNamCG_ungdungthuctien).add(jtxtNamBD_ungdungthuctien).add(jtxtNamKT_ungdungthuctien).add(jtxtDeTai_ungdungthuctien).add(jtxtHinhThuc_ungdungthuctien).add(jtxtQuyMo_ungdungthuctien).add(jtxtDiaChi_ungdungthuctien).add(jtxtTenCongNghe_ungdungthuctien),
	tipsungdungthuctien					= $("#tipungdungthuctien");
	
	function ungdungthuctien_updateTips( t ) {
		tipsungdungthuctien
					.text( t )
					.addClass( "ui-state-highlight" );
		setTimeout(function() {
			tipsungdungthuctien.removeClass( "ui-state-highlight", 1500 );
		}, 1000 );
	}
	
	// ungdungthuctien_checkLength
	function ungdungthuctien_checkLength( o, n, min, max) {
		if (min==0 && (o.val().length==0))
		{	
			o.addClass( "ui-state-error" );
			o.focus();	
			ungdungthuctien_updateTips( "Thông tin " + n + " không được phép để trống.");
			
			return false;
		}else if (min==max && o.val().length<min){
			o.addClass( "ui-state-error" );
			o.focus();	
			ungdungthuctien_updateTips( "Thông tin " + n + " phải đủ " + min + " ký tự.");
		}else if ( o.val().length > max || o.val().length < min ) {
			o.addClass( "ui-state-error" );
			o.focus();		
			ungdungthuctien_updateTips( "Chiều dài của " + n + " từ " +
						min + " đến " + max + " ký tự.");
			return false;
		} else {
			return true;
		}
	}
	
	// Check Regexp
	function ungdungthuctien_checkRegexp( o, regexp, n ) {
		//alert('a');
		if ( o.val()!='' && !( regexp.test( o.val() ) ) ) {
			o.addClass( "ui-state-error" );
			o.focus();
			ungdungthuctien_updateTips( n );
			return false;
		} else {
			return true;
		
		}
	}
// End of check validate
	
	// Post du lieu cho nghien cuu khoa hoc
	$("#taoungdungthuctien").click(function(){
		 $('#ungdungthuctien_act').val('add');
		 $("#formthemungdungthuctiendiv").dialog('option', 'title', 'Thêm giải ứng dụng thực tiễn và thương mại hóa kết quả nghiên cứu...');
		 $("#formthemungdungthuctiendiv").dialog('open');
	});
	
	$( "#formthemungdungthuctiendiv" ).dialog({
			autoOpen: false,
			height: 380,
			width: 650,
			modal: true,
			buttons: {
				"Ok": function() {
					var bValid = true;
					
					allFieldsungdungthuctien.removeClass( "ui-state-error" );

					bValid = bValid && ungdungthuctien_checkLength( jtxtTenCongNghe_ungdungthuctien, "\"Tên giải pháp\"", 0, 500);
					bValid = bValid && ungdungthuctien_checkLength( jtxtNamBD_ungdungthuctien, "\"Năm bắt đầu\"", 4, 4);
					bValid = bValid && ungdungthuctien_checkRegexp( jtxtNamBD_ungdungthuctien,/^[0-9]{4,4}$/i, "Thông tin \"Năm bắt đầu\" phải là Số");
					bValid = bValid && ungdungthuctien_checkRegexp( jtxtNamKT_ungdungthuctien,/^[0-9]{4,4}$/i, "Thông tin \"Năm kết thúc\" phải là Số");
					bValid = bValid && ungdungthuctien_checkLength( jtxtNamCG_ungdungthuctien, "\"Năm chuyển giao\"", 4, 4);
					bValid = bValid && ungdungthuctien_checkRegexp( jtxtNamCG_ungdungthuctien,/^[0-9]{4,4}$/i, "Thông tin \"Năm chuyển giao\" phải là Số");
										
					if (bValid) {
						
						$("#tipungdungthuctien").html("<table><tr><td ><img src='../images/ajax-loader.gif'></td><td valign=top style='color: black'> Đang xử lý thông tin ...</td></tr></table>");
						
						dataString = $("#form_ungdungthuctien").serialize()
						+ "&cat=ungdungthuctien&act=" + $("#ungdungthuctien_act").val() + "&"
						+ allFieldsungdungthuctien.serialize();
						dataString +='&hisid=<?php echo $_REQUEST["hisid"];?>';
						
						//alert(dataString);
						$.ajax({
							type: "POST",
							url: "gv/gv_ungdungthuctien_process.php",
							data: dataString,
							dataType: "json",
							success: function(data) {
										if (data.status==1)
										{
											ungdungthuctien_RefreshData();	
											$("#tipungdungthuctien").html("");
										}
										else
										{
											$("#tipungdungthuctien").html("");
											if ($('#ungdungthuctien_act').val()=='add')
												gv_open_msg_box("Không thể thêm giải pháp, bạn vui lòng thử lại.","alert",250,150);
											else if ($('#ungdungthuctien_act').val()=='edit')
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
				allFieldsungdungthuctien.val( "" ).removeClass( "ui-state-error" );
			}
		});
	
    $("#btnXoaungdungthuctien").click(function(){
		$( "#btnXoaungdungthuctien" ).button({ disabled: true });
		dataString = $("#form_ungdungthuctien").serialize() + '&cat=ungdungthuctien&act=del';
		dataString +='&hisid=<?php echo $_REQUEST["hisid"];?>';
		
		$.ajax({type: "POST",url: "gv/gv_ungdungthuctien_process.php",data: dataString,dataType: "html",
			success: function(data) {
						ungdungthuctien_RefreshData();
						$( "#btnXoaungdungthuctien" ).button({ disabled: false });
					 }// end function(data)	
		}); // end .ajax
	});	// end $("#btnXoaungdungthuctien").click(function()
	
	function ungdungthuctien_RefreshData() {
		dataString = "cat=get_ungdungthuctien&hisid=<?php echo $_REQUEST["hisid"];?>";
		$.ajax({
			type: "POST",
			url: "gv/gv_ungdungthuctien_process.php",
			data: dataString,
			dataType: "html",
			success: function(data) {
						$("#tableungdungthuctien tbody").html(data);
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