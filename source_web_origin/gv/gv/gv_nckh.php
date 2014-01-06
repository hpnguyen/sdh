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
  
<div id = 'nghiencuukhoahocdiv'>
	<form id="form_nckh" method="post" action="" >
    <div id = 'formthemnghiencuukhoahocdiv' title="Đề tài dự án đã thực hiện thành công">
         <table width="100%" border="0" align="center" cellpadding="5" cellspacing="2">
          <tr class="heading" >
            <td colspan="5" align="left"><label for="txtTenNCKH">Đề tài, dự án, nghiên cứu khoa học</label></td>
            </tr>
          <tr align="left" class="heading" >
            <td colspan="5" ><textarea style="width:100%" name="txtTenNCKH" id="txtTenNCKH" class="text ui-widget-content ui-corner-all tableData" cols="75" rows="3" ></textarea></td>
            </tr>
          <tr align="left" class="heading">
            <td width="18%" ><label for="txtNamNCKHbatdau">Năm bắt đầu</label></td>
            <td width="23%" ><label for="txtNamNCKHketthuc">Năm kết thúc</label></td>
            <td width="25%" align="center"><label for="chkChuNhiemNCKH">Chủ nhiệm</label></td>
            <td colspan=2><label for="txtCapDT">Cấp</label></td>
          </tr>
          <tr align="left" class="heading">
            <td ><input class="text ui-widget-content ui-corner-all tableData" name="txtNamNCKHbatdau" type="text" id="txtNamNCKHbatdau" size="4" maxlength="4" /></td>
            <td ><input class="text ui-widget-content ui-corner-all tableData" name="txtNamNCKHketthuc" type="text" id="txtNamNCKHketthuc" size="4" maxlength="4" /></td>
            <td align="center"><input name="chkChuNhiemNCKH" type="checkbox" id="chkChuNhiemNCKH" value="1" /></td>
            
            <td align="left" colspan=2>
				<select style="height:28px;font-size:15px;width:250px;" class="text ui-widget-content ui-corner-all tableData" name="txtCapDT" id="txtCapDT" >
					<option value=""></option>
						<?  $sqlstr="select * from cap_de_tai order by ten_cap";
							$stmt = oci_parse($db_conn, $sqlstr);
							oci_execute($stmt);
							$n = oci_fetch_all($stmt, $resDM);
							oci_free_statement($stmt);
							for ($i = 0; $i < $n; $i++)
								echo "<option value=\"".$resDM["MA_CAP"][$i]."\"> "
								.$resDM["TEN_CAP"][$i]." </option>";			
						?>
				</select>
			</td>
            
          </tr>
          <tr align="left" class="heading">
			<td ><label id="lblMaSoNCKH" for= "txtMaSoNCKH">Mã số/Số hiệu</label></td>
            <td ><label id="lblChuongTrinhNCKH" for= "txtChuongTrinhNCKH">Thuộc Chương trình</label></td>
            <td  align="center"><label for="txtNghiemThuNCKH">Ngày nghiệm thu</label> </td>
			<td ><label for="txtKinhPhiNCKH">Kinh phí (triệu đồng)</label></td>
            <td ><label for="txtKetQuaNCKH">Kết quả</label></td>
            
          </tr>
          <tr align="left">
			<td ><input class="text ui-widget-content ui-corner-all tableData" name="txtMaSoNCKH" type="text" id="txtMaSoNCKH" size="10" maxlength="25" /></td>
            <td ><input class="text ui-widget-content ui-corner-all tableData" name="txtChuongTrinhNCKH" type="text" id="txtChuongTrinhNCKH" size="15" maxlength="50" /></td>
            <td align="center"><input name="txtNghiemThuNCKH" type="text" id="txtNghiemThuNCKH" value="" maxlength="10" size=8 class="text ui-widget-content ui-corner-all tableData" style='text-align:center' />
              <input type="hidden" name="manckhedit" id="manckhedit" /></td>
            <td>
				<input class="text ui-widget-content ui-corner-all tableData" name="txtKinhPhiNCKH" type="text" id="txtKinhPhiNCKH" size="13" maxlength="25" style='text-align:right'/>
			</td>
			<td ><select name="txtKetQuaNCKH" id="txtKetQuaNCKH" style="height:28px;font-size:15px;" class="text ui-widget-content ui-corner-all tableData">
					  <option value=""></option>
					  <option value="X">Xuất sắc</option>
					  <option value="T">Tốt</option>
					  <option value="K">Khá</option>
					  <option value="B">Trung bình</option>
				</select>
			</td>
            
          </tr>
       
        
            </table>
			<div style="margin-top:10px;" align="center" id="tipNCKH" class="ui-corner-all validateTips"></div>
			
    </div> <!--end formthemnghiencuukhoahocdiv -->
    
	
    
    	<table border="0" width="100%" cellspacing="0" cellpadding="0">
          <tr>
			<td align=left>
				<div style="margin-left:5px;font-weight:bold">Quá trình nghiên cứu</div>
			</td>
            <td align="right" style="width:300px" >
				<div style="margin-bottom:10px;">
						<a id="taoNCKH" name="taoNCKH" >&nbsp;Thêm đề tài... mới</a>
				&nbsp;&nbsp;
				<a id="btnXoaNCKH" name="btnXoaNCKH"> &nbsp;Xóa</a>
				</div>
			</td>
          </tr>
        </table>

		<table width="100%" id="tableNCKH" align="center" border="0" cellspacing="0" cellpadding="5" class="ui-widget ui-widget-content ui-corner-top tableData" height="20">
        <thead>
          <tr class="ui-widget-header heading" >
            <td width="30" class=" ui-corner-tl"></td>
            <td width="246" align=left><em>Các đề tài, dự án, nghiên cứu khoa học</em></td>
			<td width="70" align=center><em>Mã số/<br/>Cấp QL</em></td>
            <td width="70" ><em>Thời gian</em></td>
			<td width="70" align=center ><em>Kinh phí<br/><span style="font-weight:normal">(triệu đồng)</span></em></td>
            <td width="63" ><em>Chủ nhiệm</em></td>
            <td width="86" align=left><em>Chương trình</em></td>
            <td width="70" align=center><em>Ngày nghiệm thu</em></td>
            <td width="59" ><em>Kết quả</em></td>
            <td width="40" >&nbsp;</td>
            <td width="35" class=" ui-corner-tr" >&nbsp;</td>
          </tr>
        </thead>
        <tbody>
		</tbody>
        </table>
</form>
<input name="nckh_act" id="nckh_act" type="hidden" value="" />
</div>		<!-- end of nghiencuukhoahocdiv -->   

<script type="text/javascript">

function getNCKH(index, mnckh, nambd, namkt, cn, ngaynt, kq, capdt, maso, chuongtrinh, kinhphi)
{
	//alert(index);
	$("#nckh_act").val("edit");
	var table=document.getElementById("tableNCKH");
	$("#txtTenNCKH").val(table.rows[index].cells[1].innerHTML); 
	//$("#txtChuongTrinhNCKH").val(table.rows[index].cells[4].innerHTML);
	$("#manckhedit").val(mnckh); 
	$("#txtNamNCKHbatdau").val(nambd); 
	$("#txtNamNCKHketthuc").val(namkt); 
	document.getElementById('chkChuNhiemNCKH').checked = cn;
	document.getElementById('txtNghiemThuNCKH').value = ngaynt;
	document.getElementById('txtKetQuaNCKH').value = kq;
	document.getElementById('txtCapDT').value = capdt;
	document.getElementById('txtMaSoNCKH').value = maso;
	document.getElementById('txtChuongTrinhNCKH').value = chuongtrinh;
	document.getElementById('txtKinhPhiNCKH').value = kinhphi;
	
	$("#formthemnghiencuukhoahocdiv").dialog('open');
}

function refresh_data_nckh()
{
	dataString ="cat=nckh&hisid=<?php echo $_REQUEST["hisid"];?>";
	$.ajax({
		type: "POST",
		url: "gv/processgv.php",
		data: dataString,
		dataType: "html",
		success: function(data) {
					$("#tableNCKH tbody").html(data);
				 }// end function(data)	
	}); // end .ajax
}



refresh_data_nckh();

//jQuery.ajax
//$(document).ready(function(){
$(function(){

  // delete btn
 $( "#btnXoaNCKH" ).button({ icons: {primary:'ui-icon ui-icon-trash'} });
 
 // create new
 $( "#taoNCKH" ).button({ icons: {primary:'ui-icon ui-icon-document'} });

function formatNumberVND(e) {
	e.parseNumber({ format: "#,##0", locale: "us" });
	e.formatNumber({ format: "#,##0", locale: "us" });
}
function formatNumberUSD(e) {
	if (e.val().match(/^.+?\.0?$/)) return;
	e.parseNumber({ format: "#,##0.##", locale: "us" });
	e.formatNumber({ format: "#,##0.##", locale: "us" });
} 

var ctrlDown = false;
function handleKeyDown(e) {
	if (e.which == 17) ctrlDown = true;
}
function handleKeyUp(e) {
	if (e.which == 17) ctrlDown = false;
}
function ignoreEvent(e) {
	if (e.which >= 16 && e.which <= 18) return true;
	if (e.which >= 33 && e.which <= 40) return true;
	if (ctrlDown && (e.which == 65 || e.which == 67)) return true;
	return false;
}

$("#txtKinhPhiNCKH").each(function() {
	formatNumberVND($(this));
});
$("#txtKinhPhiNCKH").keydown(function(e) {
	handleKeyDown(e);
}).keyup(function(e) {
	handleKeyUp(e);
	if (!ignoreEvent(e)) formatNumberVND($(this));
});
 //document.getElementById('txtChuongTrinhNCKH').disabled=true;
 //$('#txtChuongTrinhNCKH').addClass("disableTextBox");
 //$("#lblChuongTrinhNCKH").addClass("disableText");
 
// Check validate fields Nghien cuu khoa hoc
var jtxtTenNCKH 		= $("#txtTenNCKH"),
	jtxtNamNCKHbatdau 	= $("#txtNamNCKHbatdau"),
	jtxtNamNCKHketthuc	= $("#txtNamNCKHketthuc"),
	jtxtChuongTrinhNCKH	= $("#txtChuongTrinhNCKH"),
	jtxtMaSoNCKH		= $("#txtMaSoNCKH"),
	jtxtNghiemThuNCKH	= $("#txtNghiemThuNCKH"),
	jtxtKinhPhiNCKH		= $("#txtKinhPhiNCKH"),
	jtxtKetQuaNCKH		= $("#txtKetQuaNCKH"),
	jtxtCapDT			= $("#txtCapDT"),
	jmanckhedit			= $("#manckhedit"),
	allFieldsNCKH = $([]).add(jtxtMaSoNCKH).add(jtxtTenNCKH).add(jtxtNamNCKHbatdau).add(jtxtNamNCKHketthuc).add(jtxtChuongTrinhNCKH).add(jtxtKetQuaNCKH).add(jtxtCapDT).add(jmanckhedit).add(jtxtKinhPhiNCKH).add(jtxtNghiemThuNCKH),
	tipsNCKH			= $("#tipNCKH");
		
	// 
	function nckh_updateTips( t ) {
		tipsNCKH
					.text( t )
					.addClass( "ui-state-highlight" );
		setTimeout(function() {
			tipsNCKH.removeClass( "ui-state-highlight", 1500 );
		}, 1000 );
	}
	
	// nckh_checkLength
	function nckh_checkLength( o, n, min, max) {
		if (min==0 && (o.val().length==0))
		{	
			o.addClass( "ui-state-error" );
			o.focus();	
			nckh_updateTips( "Thông tin " + n + " không được phép để trống.");
			
			return false;
		}else if (min==max && o.val().length<min){
			o.addClass( "ui-state-error" );
			o.focus();	
			nckh_updateTips( "Thông tin " + n + " phải đủ " + min + " ký tự.");
		}else if ( o.val().length > max || o.val().length < min ) {
			o.addClass( "ui-state-error" );
			o.focus();		
			nckh_updateTips( "Chiều dài của " + n + " từ " +
						min + " đến " + max + " ký tự.");
			return false;
		} else {
			return true;
		}
	}
	
	// Check Regexp
	function nckh_checkRegexp( o, regexp, n ) {
		//alert('a');
		if ( o.val()!='' && !( regexp.test( o.val() ) ) ) {
			o.addClass( "ui-state-error" );
			o.focus();
			nckh_updateTips( n );
			return false;
		} else {
			return true;
		
		}
	}
// End of check validate
	
	// Post du lieu cho nghien cuu khoa hoc
	$("#taoNCKH").click(function(){
		 $('#nckh_act').val('add');
		 $("#formthemnghiencuukhoahocdiv").title="Thêm đề tài, dự án, nghiên cứu khoa học...";
		 $("#formthemnghiencuukhoahocdiv").dialog('open');
	});
	
	$( "#formthemnghiencuukhoahocdiv" ).dialog({
			autoOpen: false,
			height: 380,
			width: 680,
			modal: true,
			buttons: {
				"Ok": function() {
					var bValid = true;
					var cnNCKH = 1;
					var ntNCKH = 1;
					//alert($("#txtKinhPhiNCKH").val());
					allFieldsNCKH.removeClass( "ui-state-error" );
					bValid = bValid && nckh_checkLength( jtxtTenNCKH, "\"Tên đề tài dự án\"", 0, 300);
					bValid = bValid && nckh_checkLength( jtxtNamNCKHbatdau, "\"Năm bắt đầu\"", 4, 4);
					bValid = bValid && nckh_checkRegexp( jtxtNamNCKHbatdau,/^[0-9]{4,4}$/i, "Thông tin \"Năm bắt đầu\" phải là Số");
					
					if (jtxtNamNCKHketthuc.val()!="")
						bValid = bValid && nckh_checkRegexp( jtxtNamNCKHketthuc,/^[0-9]{4,4}$/i, "Thông tin \"Năm kết thúc\" phải là Số");
					//bValid = bValid && nckh_checkLength( jtxtChuongTrinhNCKH, "\"Ghi chú\"", 1, 100, "nckh");
					//bValid = bValid && nckh_checkLength( jtxtKetQuaNCKH, "\"Kết quả\"", 0, 100, "nckh");
					
					if (jtxtKinhPhiNCKH.val()!="")
					{
						//bValid = bValid && nckh_checkRegexp( jtxtKinhPhiNCKH,/^[0-9]{0,4}$/i, "\"Kinh phí\" phải là Số có chiều dài tối đa 4 chữ số");
					}
						
					
					if (bValid) {
						
						if (!document.getElementById('chkChuNhiemNCKH').checked)
							cnNCKH = 0;
						
						dataString = $("#form_nckh").serialize()
						+ "&cat=nckh&act=" + $("#nckh_act").val() + "&"
						+ allFieldsNCKH.serialize( ) 
						+ "&chkChuNhiemNCKH=" + cnNCKH;
						dataString +='&hisid=<?php echo $_REQUEST["hisid"];?>';
						
						//+ $("#dtHuongDeTai").serialize() + "&" + $("#dtNam").serialize();
						//alert(dataString);
						$.ajax({
							type: "POST",
							url: "gv/processgv.php",
							data: dataString,
							dataType: "html",
							success: function(data) {
										//alert (data);
										$("#tableNCKH tbody").html(data);
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
				allFieldsNCKH.val( "" ).removeClass( "ui-state-error" );
			}
		});
	
    $("#btnXoaNCKH").click(function(){
		dataString = $("#form_nckh").serialize() + '&cat=nckh&act=del';
		dataString +='&hisid=<?php echo $_REQUEST["hisid"];?>';
		
		$.ajax({type: "POST",url: "gv/processgv.php",data: dataString,dataType: "html",
			success: function(data) {
						//alert(data);
						$("#tableNCKH tbody").html(data);
					 }// end function(data)	
		}); // end .ajax
	});	// end $("#btnXoaNCKH").click(function()
	
});

</script>



<?php 
if (isset ($db_conn))
	oci_close($db_conn);
?>