<?php
session_start();
//if (isset($_REQUEST["hisid"])){
//	session_id($_REQUEST["hisid"]);
	
//}
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

$search = array('\\',"'",'"');
$replace = array('\\\\',"\'","&quot;"); // \'
?>
<style>
	.disableText{
		color:#808080;
	}
	.disableTextBox{
		background: #808080;
	}
</style>
<div id = 'congtrinhkhoahocdiv'>
<form id='form_ctkh' action='' method='POST'>
    <div id = 'formthemcongtrinhkhoahocdiv' title="Bài báo tạp chí/hội nghị khoa học">
		<input type="hidden" name="mactkhedit" id="mactkhedit" />
         <table width="100%" border="0" align="center" cellpadding="5" cellspacing="2">
          
		  <tr align="left" class="heading" >
            <td ><label for="txtLoaiCongTrinh">Loại bài báo</label></td>
			
			<td colspan=3><label for="txtTenBaiBao">Tên bài báo</label></td>
          </tr>
		  <tr align="left" >
            <td >
				<select style="height:28px;font-size:15px;" name="txtLoaiCongTrinh" id="txtLoaiCongTrinh" class="text ui-widget-content ui-corner-all tableData" onChange="update_loaibaibao(this.value);">
				  <option value="BQ" selected="selected">Tạp chí quốc tế</option>
				  <option value="BT">Tạp chí trong nước</option>
				  <option value="HQ">Hội nghị quốc tế</option>
				  <option value="HT">Hội nghị trong nước</option>
				</select>
			</td>
			<td colspan=3><input style="width:100%" class="text ui-widget-content ui-corner-all tableData" name="txtTenBaiBao" type="text" id="txtTenBaiBao" maxlength="2000" /></td>
          </tr>
		  	  
		  <tr align=left class="heading">
			<td colspan=3><label for="txtTenTacGia">Tác giả</label></td>
			<td ><label for="txtLoaiTacGia">Loại tác giả</label></td>
		  </tr>
		  <tr align=left>		
			<td colspan=3><input style="width:100%" class="text ui-widget-content ui-corner-all tableData" name="txtTenTacGia" type="text" id="txtTenTacGia" maxlength="100" /></td>
			<td >
				<select style="width:100%;height:28px;font-size:15px;" name="txtLoaiTacGia" id="txtLoaiTacGia" class="text ui-widget-content ui-corner-all tableData" >
				  <option value="" selected="selected">chọn loại tác giả</option>
				  <option value="03">Tác giả chính</option>
				  <option value="02">Đồng tác giả</option>
				</select>
			</td>
		  </tr>
		  <tr align=left>		
			<td colspan=2>
				<input style="width:100%" class="tooltips text ui-widget-content ui-corner-all tableData" name="txtTenTacGiaChinh" type="text" id="txtTenTacGiaChinh" maxlength="100" placeholder="tên tác giả chính" data-placement='top' title='Tên tác giả chính'/>
			</td>
			<td colspan=2>
				<input style="width:100%" class="tooltips text ui-widget-content ui-corner-all tableData" name="txtTenTacGiaVNU" type="text" id="txtTenTacGiaVNU" maxlength="500" placeholder="tên tác giả thuộc VNU" data-placement='top' title='Tên tác giả thuộc VNU'/>
			</td>
		  </tr>
		  
		  <tr align=left class="heading">
			<td ><label for="txtMaSoDeTai" class='tooltips' data-placement='right' title='Nếu đề tài/dự án không phải cấp ĐHQG, đề nghị ghi rõ tên đề tài và cấp quản lý vào ô này'>Sản phẩm của đề tài</label></td>
			<td ><label for="txtISBN" id="lbl_ISBN_ISSN">Số ISBN/ISSN</label></td>
			<td ><label for="txtISI" id="lbl_ISI">Thuộc</label></td>
			<td align=left><label for="txtIF" id="lbl_IF">Điểm IF</label></td>
		  </tr>
		  <tr align=left>		
			<td >
				<input style="width:100%" list="masodetai_sach_list" class="tooltips text ui-widget-content ui-corner-all tableData" name="txtMaSoDeTai" type="text" id="txtMaSoDeTai" maxlength="500" placeholder="chỉ ghi mã số" data-placement='right' title='Nếu đề tài/dự án không phải cấp ĐHQG, đề nghị ghi rõ tên đề tài và cấp quản lý vào ô này' />
				<datalist id="masodetai_sach_list">
				<?php
					$sqlstr="select MA_SO_DE_TAI, NAM_BAT_DAU from DE_TAI_NCKH where MA_CAN_BO = '$macb' order by NAM_BAT_DAU"; 
					$stmt = oci_parse($db_conn, $sqlstr);oci_execute($stmt);$n = oci_fetch_all($stmt, $resDM);oci_free_statement($stmt);
					for ($i = 0; $i < $n; $i++){
						echo "<option value='{$resDM["MA_SO_DE_TAI"][$i]}'>";}
				?>
				</datalist>
			</td>
			<td><input style="width:120px" class="text ui-widget-content ui-corner-all tableData" name="txtISBN" type="text" id="txtISBN" maxlength="20" /></td>
			<td>			
				<select style="height:28px;font-size:15px;" name="txtISI" id="txtISI" class="text ui-widget-content ui-corner-all tableData" >
				  <option value="" selected="selected">Ngoài ISI</option>
				  <option value="SCI">SCI</option>
				  <option value="SCIE">SCIE</option>
				  <option value="SSCI">SSCI</option>
				</select>
			</td>
			<td align=left><input style="width:50px" class="text ui-widget-content ui-corner-all tableData" name="txtIF" type="text" id="txtIF" maxlength="6" /> (vd: 10.158) </td>
		  </tr>
		  
		  <tr align=left class="heading">
			<td colspan=2><label for="txtTenTapChi" id=lblTenTapChi>Tên Tạp chí</label></td>
			<td><label for="txtThanhPho" id="lblThanhPho">Thành Phố</label></td>
			<td><label for="txtQuocGia" id="lblQuocGia">Quốc Gia</label></td>
		  </tr>
		  <tr align=left>
			<td colspan=2><input style="width:100%"  class="text ui-widget-content ui-corner-all tableData" name="txtTenTapChi" type="text" id="txtTenTapChi" maxlength="500" /></td>
			<td>		  <input style="width:100%"  class="text ui-widget-content ui-corner-all tableData" name="txtThanhPho" type="text" id="txtThanhPho" maxlength="50" /></td>
			<td>
				<select style="width:100%; height:28px;font-size:15px;" class="text ui-widget-content ui-corner-all tableData" name="txtQuocGia" id="txtQuocGia" >
				  <option value="" selected="selected">&nbsp;</option>
				  <?php
					$sqlstr="select MA_QUOC_GIA, TEN_QUOC_GIA from QUOC_GIA order by TEN_QUOC_GIA"; 
					$stmt = oci_parse($db_conn, $sqlstr);
					oci_execute($stmt);
					$n = oci_fetch_all($stmt, $resDM);
					oci_free_statement($stmt);
					for ($i = 0; $i < $n; $i++)
					{
						echo "<option value='{$resDM["MA_QUOC_GIA"][$i]}'>{$resDM["TEN_QUOC_GIA"][$i]}</option>";
					}
				  ?>
				</select>
			</td>
		  </tr>
		  
		  <tr align=left class="heading">
			<td><label for="txtSoTapChi" id="lblSoTapChi">Số tạp chí</label></td>
			<td><label for="txtTrangDangBaiBao">Trang</label></td>
			<td><label for="txtNamXB">Năm</label></td>
			<td><label for="txtGhiChu_baibao" id="lblGhiChu_baibao">Ghi chú</label></td>
		  </tr>
		  <tr align=left>
			<td><input class="text ui-widget-content ui-corner-all tableData" name="txtSoTapChi" type="text" id="txtSoTapChi" style="width:100%" maxlength="50"  /></td>
			<td><input class="text ui-widget-content ui-corner-all tableData" name="txtTrangDangBaiBao" type="text" id="txtTrangDangBaiBao" style="width:100%" maxlength="50" /></td>
			<td><input class="text ui-widget-content ui-corner-all tableData" name="txtNamXB" type="text" id="txtNamXB" style="width:100%" maxlength="4" align=center/></td>
			<td><input style="width:100%" class="text ui-widget-content ui-corner-all tableData" name="txtGhiChu_baibao" type="text" id="txtGhiChu_baibao" maxlength="500"  /></td>
		  </tr>
		  
		  <tr align=left>		
			<td colspan=2>
				<input style="width:100%" class="tooltips text ui-widget-content ui-corner-all tableData" name="txtThuocLinhVuc" type="text" id="txtThuocLinhVuc" maxlength="200" placeholder="thuộc lĩnh vực" data-placement='top' title='Thuộc lĩnh vực'/>
			</td>
			<td colspan=2>
				<input style="width:100%" class="tooltips text ui-widget-content ui-corner-all tableData" name="txtLinkBaibao" type="text" id="txtLinkBaibao" maxlength="250" placeholder="link bài báo (nếu có)" data-placement='top' title='Link đến bài báo (minh chứng nếu có)'/>
			</td>
		  </tr>
		  
		  <tr align=left class="heading">
			<td></td>
			<td></td>
			<td></td>
			<td></td>
		  </tr>
		  
        </table>
		<div align="center" id="tipCTKH" class="ui-corner-all validateTips"></div>
    </div> <!--end formthemcongtrinhkhoahocdiv -->
    
	
    
    	<table border="0" width="100%" cellspacing="0" cellpadding="0">
          <tr>
			<td>
			<div style="margin-left:5px;font-weight:bold">
			</div>
			</td>
            <td align="right" >
				<div style="margin-bottom:10px;">
						  <a id="taoctkh" name="taoctkh" > &nbsp;Thêm bài báo...</a>
					&nbsp;&nbsp;
					<a id="btnXoaCTKH" name="btnXoaCTKH"> &nbsp;Xóa</a>
				</div>
			</td>
          </tr>
        </table>

		<table width="100%" id="tablectkh" align="center" border="0" cellspacing="0" cellpadding="5" class="ui-widget ui-widget-content ui-corner-top tableData" height="20">
		  <thead>
          <tr class="ui-widget-header heading" >
            <td width="30" class=" ui-corner-tl"></td>
            <td style="width:450px" align="left"><em>Các bài báo tạp chí/hội nghị khoa học đã công bố</em></td>
			<td align="center"></td>
			<td align="center"></td>
			<td align="center"><em></em></td>
			<td align="center"><em></em></td>
            <td width="45" >&nbsp;</td>
            <td width="36" class=" ui-corner-tr" >&nbsp;</td>
          </tr>
          </thead>
          <tbody>
		  
		  </tbody>
        </table>
</form>

<input name="ctkh_act" id="ctkh_act" type="hidden" value="" />

</div>		<!-- end of congtrinhkhoahocdiv -->  

<script type="text/javascript">

function getCTKH(sotapchi, namxb, trangdangbaibao, tentapchi, 
					tenbaibao, tentacgia, loaict, isbn, thanhpho, quocgia, mact, masodetai, isi, diemif, ghichu, ploaitacgia,
					ptacgiachinh, ptacgiavnu, pthuoclinhvuc, plinkbaibao)
{
	$('#ctkh_act').val('edit');
	$('#txtSoTapChi').val(sotapchi);
	$('#txtNamXB').val(namxb);
	$('#txtTrangDangBaiBao').val(trangdangbaibao);
	$('#txtTenTapChi').val(tentapchi);
	$('#txtTenBaiBao').val(tenbaibao);
	$('#txtTenTacGia').val(tentacgia);
	$('#txtLoaiCongTrinh').val(loaict);
	$('#txtISBN').val(isbn);
	$('#txtThanhPho').val(thanhpho);
	$('#txtQuocGia').val(quocgia);
	$('#mactkhedit').val(mact);
	$('#txtMaSoDeTai').val(masodetai);
	$("#txtISI").val(isi);
	$("#txtIF").val(diemif);
	$("#txtGhiChu_baibao").val(ghichu);
	$("#txtLoaiTacGia").val(ploaitacgia);
	
	$("#txtTenTacGiaChinh").val(ptacgiachinh);
	$("#txtTenTacGiaVNU").val(ptacgiavnu);
	$("#txtThuocLinhVuc").val(pthuoclinhvuc);
	$("#txtLinkBaibao").val(plinkbaibao);
	
	update_loaibaibao($('#txtLoaiCongTrinh').val());
	
	$("#formthemcongtrinhkhoahocdiv").dialog('open');
}

function update_loaibaibao(loaiBaiBao)
{
	if (loaiBaiBao=='BT' || loaiBaiBao=='BQ')
	{
		document.getElementById('lblTenTapChi').innerHTML = "Tên Tạp Chí";
		document.getElementById('lbl_ISBN_ISSN').innerHTML = "Số ISSN";
		
		document.getElementById('txtSoTapChi').disabled=false;
		document.getElementById('txtISBN').disabled=false;
		$("#lblISBN, #lblSoTapChi").removeClass("disableText");
		$('#txtISBN, #txtSoTapChi').removeClass("disableTextBox");
		
		document.getElementById('txtQuocGia').disabled=true;
		document.getElementById('txtThanhPho').disabled=true;
		document.getElementById('txtIF').disabled=true;
		document.getElementById('txtISI').disabled=true;
		$("#txtThanhPho, #txtQuocGia").val("");
		$("#lblThanhPho, #lblQuocGia, #lbl_IF, #lbl_ISI").addClass("disableText");
		$('#txtThanhPho, #txtQuocGia, #txtIF, #txtISI').addClass("disableTextBox");
		
		if (loaiBaiBao=='BQ')
		{
			$("#lbl_IF, #lbl_ISI").removeClass("disableText");
			$('#txtIF, #txtISI').removeClass("disableTextBox");
			document.getElementById('txtIF').disabled=false;
			document.getElementById('txtISI').disabled=false;
		}
	}
	else if (loaiBaiBao=='HT' || loaiBaiBao=='HQ')
	{
		document.getElementById('lblTenTapChi').innerHTML = "Tên Hội Nghị";
		document.getElementById('lbl_ISBN_ISSN').innerHTML = "Số ISBN";
		document.getElementById('txtSoTapChi').disabled=true;
		document.getElementById('txtIF').disabled=true;
		document.getElementById('txtISI').disabled=true;
		
		$('#txtSoTapChi').val("");
		$("#lblSoTapChi, #lbl_IF, #lbl_ISI").addClass("disableText");
		$('#txtSoTapChi, #txtIF, #txtISI').addClass("disableTextBox");
		
		
		document.getElementById('txtQuocGia').disabled=false;
		document.getElementById('txtThanhPho').disabled=false;
		
		$("#lblThanhPho, #lblQuocGia").removeClass("disableText");
		$('#txtThanhPho, #txtQuocGia').removeClass("disableTextBox");
	}
}

function refresh_data_bai_bao()
{
	dataString = "cat=ctkh&hisid=<?php echo $_REQUEST["hisid"];?>";
	$.ajax({
		type: "POST",
		url: "gv/processgv.php",
		data: dataString,
		dataType: "html",
		success: function(data) {
					$("#tablectkh tbody").html(data);
				 }// end function(data)	
	}); // end .ajax
}
refresh_data_bai_bao();

$(function(){
 $(".tooltips").tooltip();
 
  // delete btn
 $( "#btnXoaCTKH" ).button({ icons: {primary:'ui-icon ui-icon-trash'} });
 
 // create new
 $( "#taoctkh" ).button({ icons: {primary:'ui-icon ui-icon-document'} });

// Check validate fields Cong Trinh Khoa Hoc
var jtxtTenTapChi 			= $("#txtTenTapChi"),
	jtxtSoTapChi			= $("#txtSoTapChi"),
	jtxtNamXB				= $("#txtNamXB"),
	jtxtMaSoDeTai			= $("#txtMaSoDeTai"),
	jtxtTrangDangBaiBao		= $("#txtTrangDangBaiBao"),
	jtxtTenBaiBao			= $("#txtTenBaiBao"),
	jtxtTenTacGia			= $("#txtTenTacGia"),
	jtxtLoaiCongTrinh		= $("#txtLoaiCongTrinh"),
	jtxtISBN				= $("#txtISBN"),
	jtxtThanhPho			= $("#txtThanhPho"),
	jtxtQuocGia				= $("#txtQuocGia"),
	jtxtISI					= $("#txtISI"),
	jtxtIF					= $("#txtIF"),
	jtxtGhiChu				= $("#txtGhiChu_baibao"),
	jtxtLoaiTacGia			= $("#txtLoaiTacGia"),
	jmactkhedit				= $("#mactkhedit"),
	jtxtTenTacGiaChinh		= $("#txtTenTacGiaChinh"),
	jtxtTenTacGiaVNU		= $("#txtTenTacGiaVNU"),
	jtxtThuocLinhVuc		= $("#txtThuocLinhVuc"),
	jtxtLinkBaibao			= $("#txtLinkBaibao"),
	allFieldsCTKH			= $( [] ).add(jtxtMaSoDeTai).add(jtxtTenTapChi).add(jtxtSoTapChi).add(jtxtNamXB).add(jtxtTrangDangBaiBao)
									.add(jtxtTenBaiBao).add(jtxtTenTacGia).add(jtxtLoaiCongTrinh).add(jtxtISBN)
									.add(jtxtThanhPho).add(jtxtQuocGia).add(jmactkhedit)
									.add(jtxtISI).add(jtxtIF).add(jtxtGhiChu).add(jtxtLoaiTacGia)
									.add(jtxtTenTacGiaChinh).add(jtxtTenTacGiaVNU).add(jtxtThuocLinhVuc).add(jtxtLinkBaibao),
	tipsCTKH				= $("#tipCTKH");
	
	// 
	function bbao_updateTips( t ) {
		tipsCTKH
					.text( t )
					.addClass( "ui-state-highlight" );
		setTimeout(function() {
			tipsCTKH.removeClass( "ui-state-highlight", 1500 );
		}, 1000 );
	}
	
	// bbao_checkLength
	function bbao_checkLength( o, n, min, max) {
		if (min==0 && (o.val().length==0))
		{	
			o.addClass( "ui-state-error" );
			o.focus();	
			bbao_updateTips( "Thông tin " + n + " không được phép để trống.");
			
			return false;
		}else if (min==max && o.val().length<min){
			o.addClass( "ui-state-error" );
			o.focus();	
			bbao_updateTips( "Thông tin " + n + " phải đủ " + min + " ký tự.");
		}else if ( o.val().length > max || o.val().length < min ) {
			o.addClass( "ui-state-error" );
			o.focus();		
			bbao_updateTips( "Chiều dài của " + n + " từ " +
						min + " đến " + max + " ký tự.");
			return false;
		} else {
			return true;
		}
	}
	
	// Check Regexp
	function bbao_checkRegexp( o, regexp, n ) {
		//alert('a');
		if ( o.val()!='' && !( regexp.test( o.val() ) ) ) {
			o.addClass( "ui-state-error" );
			o.focus();
			bbao_updateTips( n );
			return false;
		} else {
			return true;
		
		}
	}
// End of check validate
	
	// Post du lieu cho cong trinh khoa hoc
	$("#taoctkh").click(function(){
		$("#ctkh_act").val("add");
		update_loaibaibao($('#txtLoaiCongTrinh').val());
		$("#formthemcongtrinhkhoahocdiv").dialog( "open" );
	});
	
	$( "#formthemcongtrinhkhoahocdiv" ).dialog({
			autoOpen: false,
			height: 600,
			width: 650,
			modal: true,
			buttons: {
				"Ok": function() {
					var bValid = true;
					allFieldsCTKH.removeClass( "ui-state-error" );
					loaiBaiBao = jtxtLoaiCongTrinh.val();
					
					bValid = bValid && bbao_checkLength( jtxtLoaiCongTrinh, "\"Loại công trình\"", 0, 100);
					bValid = bValid && bbao_checkLength( jtxtTenBaiBao, "\"Tên bài báo\"", 0, 2000);
					bValid = bValid && bbao_checkLength( jtxtTenTacGia, "\"Tác giả bài báo\"", 0, 100);
					bValid = bValid && bbao_checkLength( jtxtLoaiTacGia, "\"Loại tác giả\"", 0, 3);
					bValid = bValid && bbao_checkRegexp( jtxtIF,/^[-+]?[0-9]*\.?[0-9]+$/i, "Thông tin \"Điểm IF\" phải là Số");
					
					if (loaiBaiBao=='BQ' && jtxtISBN.val()!='')
					{
						bValid = bValid && bbao_checkLength( jtxtLoaiCongTrinh, "\"Loại công trình\"", 0, 10);
					}
					
					bValid = bValid && bbao_checkLength( jtxtTenTapChi, "\"Tên tạp chí\"", 0, 500);
					
					if (loaiBaiBao=='BT' || loaiBaiBao=='BQ')
					{
						bValid = bValid && bbao_checkLength( jtxtSoTapChi, "\"Số tạp chí\"", 0, 50);
					}
					else if (loaiBaiBao=='HT' || loaiBaiBao=='HQ')
					{
						bValid = bValid && bbao_checkLength( jtxtThanhPho, "\"Thành Phố\"", 0, 50);
						bValid = bValid && bbao_checkLength( jtxtQuocGia, "\"Quốc Gia\"", 0, 50);
					}
					
					//bValid = bValid && bbao_checkLength( jtxtTrangDangBaiBao, "\"Trang\"", 0, 20);
					bValid = bValid && bbao_checkLength( jtxtNamXB, "\"Năm\"", 4, 4);
					bValid = bValid && bbao_checkRegexp( jtxtNamXB,/^[0-9]{4,4}$/i, "Thông tin \"Năm\" phải là Số");
					
					if (bValid) {

						dataString = $("#form_ctkh").serialize()
						+ "&cat=ctkh&act=" + $('#ctkh_act').val() + '&'
						+ allFieldsCTKH.serialize();
						dataString +='&hisid=<?php echo $_REQUEST["hisid"];?>';
						//alert(dataString);
						$.ajax({
							type: "POST",
							url: "gv/processgv.php",
							data: dataString,
							dataType: "html",
							success: function(data) {
										//alert (data);
										$("#tablectkh tbody").html(data);
									 }// end function(data)	
						}); // end .ajax
					}
					
					if ( bValid ) {
						tipsCTKH.text( "" );
						$( this ).dialog( "close" );
						
					}
				},
				Cancel: function() {
					tipsCTKH.text( "" );
					$( this ).dialog( "close" );
				}
			},
			close: function() {
				allFieldsCTKH.val( "" ).removeClass( "ui-state-error" );
			}
		});
	
    $("#btnXoaCTKH").click(function(){
		dataString = $("#form_ctkh").serialize()+'&cat=ctkh&act=del&';
		dataString +='&hisid=<?php echo $_REQUEST["hisid"];?>';
		
		$.ajax({type: "POST",url: "gv/processgv.php",data: dataString,dataType: "html",
			success: function(data) {
						//alert(data);
						$("#tablectkh tbody").html(data);
					 }// end function(data)	
		}); // end .ajax
	});	// end $("#btnXoaCTKH").click(function()
	
	//update(jtxtLoaiCongTrinh.val());
	
	$('input[placeholder],textarea[placeholder]').placeholder();
});

</script>



<?php 
if (isset ($db_conn))
	oci_close($db_conn);
?>