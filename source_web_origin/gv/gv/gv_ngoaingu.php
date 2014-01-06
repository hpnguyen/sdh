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
  
<div id = 'ngoaingudiv'>
	
	<form id="form_ngoaingu" method="post" action="" >
	<input type="hidden" name="mangoainguedit" id="mangoainguedit" />
    <div id = 'formthemngoaingudiv' title="Thêm ngoại ngữ">
        <table width="100%" border="0" align="center" cellpadding="5" cellspacing="2">	  
		  <tr>
			<td align="right" class="heading"><label for="txtTenNgoaiNgu_ngoaingu">Tên ngoại ngữ</label></td>
			<td>
				<select name="txtTenNgoaiNgu_ngoaingu" id="txtTenNgoaiNgu_ngoaingu" class="text ui-widget-content ui-corner-all tableData" style="height:23px;font-size:15px">
				   <option value="">chọn ngoại ngữ</option>
				   <?php
					$sqlstr="select MA_NGOAI_NGU,TEN_NGOAI_NGU from DM_NGOAI_NGU order by TEN_NGOAI_NGU"; 
					$stmt = oci_parse($db_conn, $sqlstr);oci_execute($stmt);$n = oci_fetch_all($stmt, $resDM);oci_free_statement($stmt);
					for ($i = 0; $i < $n; $i++){
						echo "<option value='{$resDM["MA_NGOAI_NGU"][$i]}'>{$resDM["TEN_NGOAI_NGU"][$i]}</option>";}
					?>
				</select>
			</td>
		  </tr>
		  
		  <tr class="heading">
            <td align="right" ><label for="txtNghe_ngoaingu">Nghe</label></td>
			<td>
				<select name="txtNghe_ngoaingu" id="txtNghe_ngoaingu" class="text ui-widget-content ui-corner-all tableData" style="height:23px;font-size:15px">
				   <option value="">chọn mức độ</option>
				   <option value="Tốt">Tốt</option>
				   <option value="Khá">Khá</option>
				   <option value="Trung bình">Trung bình</option>
				</select>
			</td>
          </tr>
		  
		  <tr class="heading">
            <td align="right" ><label for="txtNghe_ngoaingu">Nói</label></td>
			<td>
				<select name="txtNoi_ngoaingu" id="txtNoi_ngoaingu" class="text ui-widget-content ui-corner-all tableData" style="height:23px;font-size:15px">
				   <option value="">chọn mức độ</option>
				   <option value="Tốt">Tốt</option>
				   <option value="Khá">Khá</option>
				   <option value="Trung bình">Trung bình</option>
				</select>
			</td>
          </tr>
		  
		  <tr class="heading">
            <td align="right" ><label for="txtViet_ngoaingu">Viết</label></td>
			<td>
				<select name="txtViet_ngoaingu" id="txtViet_ngoaingu" class="text ui-widget-content ui-corner-all tableData" style="height:23px;font-size:15px">
				   <option value="">chọn mức độ</option>
				   <option value="Tốt">Tốt</option>
				   <option value="Khá">Khá</option>
				   <option value="Trung bình">Trung bình</option>
				</select>
			</td>
          </tr>
		  
		  <tr class="heading">
            <td align="right" ><label for="txtDoc_ngoaingu">Đọc hiểu tài liệu</label></td>
			<td>
				<select name="txtDoc_ngoaingu" id="txtDoc_ngoaingu" class="text ui-widget-content ui-corner-all tableData" style="height:23px;font-size:15px">
				   <option value="">chọn mức độ</option>
				   <option value="Tốt">Tốt</option>
				   <option value="Khá">Khá</option>
				   <option value="Trung bình">Trung bình</option>
				</select>
			</td>
          </tr>
		  
		  <tr class="heading">
		    <td align="right" ><label for="txtGhiChu_ngoaingu">Ghi chú</label></td>
			<td>
				<input style="width:200px" class="text ui-widget-content ui-corner-all tableData" name="txtGhiChu_ngoaingu" id="txtGhiChu_ngoaingu" type="text" maxlength="100" placeholder=""/>
			</td>
		  </tr>
        </table>
		
		<div style="margin-top:10px" align="center" id="tipngoaingu" class="ui-corner-all validateTips"></div>
			
    </div> <!--end formthemngoaingudiv -->
    
    	<table border="0" width="100%" cellspacing="0" cellpadding="0">
          <tr>
			<td align=left style="margin-left:5px;font-weight:bold">
				Trình độ ngoại ngữ
			</td>
            <td align="right" >
				<div style="margin-bottom:10px;">
						<a id="taongoaingu" name="taongoaingu" >&nbsp;Thêm ngoại ngữ...</a>
				&nbsp;&nbsp;
				<a id="btnXoangoaingu" name="btnXoangoaingu"> &nbsp;Xóa</a>
				</div>
			</td>
          </tr>
        </table>

		<table width="100%" id="tablengoaingu" align="center" border="0" cellspacing="0" cellpadding="5" class="ui-widget ui-widget-content ui-corner-top tableData" height="20">
		  <thead>
			  <tr class="ui-widget-header heading" >
				<td class="ui-corner-tl"  align="left" style="width:30px"><em>TT</em></td>
				<td align="left"><em>Tên ngoại ngữ</em></td>
				<td align="center" ><em>Nghe</em></td>
				<td align="center" ><em>Nói</em></td>
				<td align="center" ><em>Viết</em></td>
				<td align="center" ><em>Đọc hiểu tài liệu</em></td>
				<td align="left" ><em>Ghi chú</em></td>
				<td style="width:30px">&nbsp;</td>
				<td class=" ui-corner-tr" style="width:20px">&nbsp;</td>
			  </tr>
          </thead>
          <tbody>
		  </tbody>
        </table>
		<input name="ngoaingu_act" id="ngoaingu_act" type="hidden" value="" />
</form>
</div>		<!-- end of ngoaingudiv -->   

<script type="text/javascript">

function getngoaingu_nn(pMaNN, pNghe, pNoi, pDoc, pViet, pGhiChu)
{
	$("#ngoaingu_act").val("edit");
	$("#mangoainguedit").val(pMaNN);
	document.getElementById('txtNoi_ngoaingu').value = pNoi;
	document.getElementById('txtTenNgoaiNgu_ngoaingu').value = pMaNN;
	document.getElementById('txtNghe_ngoaingu').value = pNghe;
	document.getElementById('txtDoc_ngoaingu').value = pDoc;
	document.getElementById('txtViet_ngoaingu').value = pViet;
	document.getElementById('txtGhiChu_ngoaingu').value = pGhiChu;
	
	document.getElementById('txtTenNgoaiNgu_ngoaingu').disabled=true;
	
	$("#formthemngoaingudiv").dialog('option', 'title', 'Cập nhật ngoại ngữ...');
	$("#formthemngoaingudiv").dialog('open');
}

//jQuery.ajax
//$(document).ready(function(){
$(function(){

  // delete btn
 $( "#btnXoangoaingu" ).button({ icons: {primary:'ui-icon ui-icon-trash'} });
 
 // create new
 $( "#taongoaingu" ).button({ icons: {primary:'ui-icon ui-icon-document'} });
 
 // Lay du lieu moi
 ngoaingu_RefreshData();
 
// Check validate fields Nghien cuu khoa hoc
var jtxtNoi_ngoaingu			= $("#txtNoi_ngoaingu"),
	jtxtNghe_ngoaingu 			= $("#txtNghe_ngoaingu"),
	jtxtTenNgoaiNgu_ngoaingu 	= $("#txtTenNgoaiNgu_ngoaingu"),
	jtxtDoc_ngoaingu			= $("#txtDoc_ngoaingu"),
	jtxtViet_ngoaingu			= $("#txtViet_ngoaingu"),
	jtxtGhiChu_ngoaingu			= $("#txtGhiChu_ngoaingu"),
	jmangoainguedit				= $("#mangoainguedit"),
	allFieldsngoaingu = $([]).add(jtxtNoi_ngoaingu).add(jtxtNghe_ngoaingu).add(jtxtTenNgoaiNgu_ngoaingu).add(jtxtDoc_ngoaingu).add(jtxtViet_ngoaingu).add(jtxtGhiChu_ngoaingu),
	tipsngoaingu					= $("#tipngoaingu");
	
	function ngoaingu_updateTips( t ) {
		tipsngoaingu
					.text( t )
					.addClass( "ui-state-highlight" );
		setTimeout(function() {
			tipsngoaingu.removeClass( "ui-state-highlight", 1500 );
		}, 1000 );
	}
	
	// ngoaingu_checkLength
	function ngoaingu_checkLength( o, n, min, max) {
		if (min==0 && (o.val().length==0))
		{	
			o.addClass( "ui-state-error" );
			o.focus();	
			ngoaingu_updateTips( "Thông tin " + n + " không được phép để trống.");
			
			return false;
		}else if (min==max && o.val().length<min){
			o.addClass( "ui-state-error" );
			o.focus();	
			ngoaingu_updateTips( "Thông tin " + n + " phải đủ " + min + " ký tự.");
		}else if ( o.val().length > max || o.val().length < min ) {
			o.addClass( "ui-state-error" );
			o.focus();		
			ngoaingu_updateTips( "Chiều dài của " + n + " từ " +
						min + " đến " + max + " ký tự.");
			return false;
		} else {
			return true;
		}
	}
	
	// Check Regexp
	function ngoaingu_checkRegexp( o, regexp, n ) {
		//alert('a');
		if ( o.val()!='' && !( regexp.test( o.val() ) ) ) {
			o.addClass( "ui-state-error" );
			o.focus();
			ngoaingu_updateTips( n );
			return false;
		} else {
			return true;
		
		}
	}
// End of check validate
	
	// Post du lieu cho nghien cuu khoa hoc
	$("#taongoaingu").click(function(){
		 $('#ngoaingu_act').val('add');
		 document.getElementById('txtTenNgoaiNgu_ngoaingu').disabled=false;
		 $("#formthemngoaingudiv").dialog('option', 'title', 'Thêm chương trình tham gia...');
		 $("#formthemngoaingudiv").dialog('open');
	});
	
	$( "#formthemngoaingudiv" ).dialog({
			autoOpen: false,
			height: 350,
			width: 350,
			modal: true,
			buttons: {
				"Ok": function() {
					var bValid = true;
					
					allFieldsngoaingu.removeClass( "ui-state-error" );

					bValid = bValid && ngoaingu_checkLength( jtxtTenNgoaiNgu_ngoaingu, "\"Tên ngoại ngữ\"", 0, 3);
					bValid = bValid && ngoaingu_checkLength( jtxtNghe_ngoaingu, "\"kỹ năng nghe\"", 0, 20);
					bValid = bValid && ngoaingu_checkLength( jtxtNoi_ngoaingu, "\"kỹ năng nói\"", 0, 20);
					bValid = bValid && ngoaingu_checkLength( jtxtViet_ngoaingu, "\"kỹ năng viết\"", 0, 20);
					bValid = bValid && ngoaingu_checkLength( jtxtDoc_ngoaingu, "\"kỹ năng đọc\"", 0, 20);
					
					if (bValid) {
						
						$("#tipngoaingu").html("<table><tr><td ><img src='../images/ajax-loader.gif'></td><td valign=top style='color: black'> Đang xử lý thông tin ...</td></tr></table>");
						
						dataString = $("#form_ngoaingu").serialize()
						+ "&cat=ngoaingu&act=" + $("#ngoaingu_act").val() + "&"
						+ allFieldsngoaingu.serialize();
						dataString +='&hisid=<?php echo $_REQUEST["hisid"];?>';
						
						//alert(dataString);
						$.ajax({
							type: "POST",
							url: "gv/gv_ngoaingu_process.php",
							data: dataString,
							dataType: "json",
							success: function(data) {
								if (data.status==1)
								{
									ngoaingu_RefreshData();	
									$("#tipngoaingu").html("");
								}
								else
								{
									$("#tipngoaingu").html("");
									if ($('#ngoaingu_act').val()=='add')
										gv_open_msg_box("Không thể thêm ngoại ngữ, bạn vui lòng thử lại.","alert",250,150);
									else if ($('#ngoaingu_act').val()=='edit')
										gv_open_msg_box("Không thể cập nhật ngoại ngữ, bạn vui lòng thử lại.","alert",250,150);
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
				allFieldsngoaingu.val( "" ).removeClass( "ui-state-error" );
			}
		});
	
    $("#btnXoangoaingu").click(function(){
		$( "#btnXoangoaingu" ).button({ disabled: true });
		dataString = $("#form_ngoaingu").serialize() + '&cat=ngoaingu&act=del';
		dataString +='&hisid=<?php echo $_REQUEST["hisid"];?>';
		
		$.ajax({type: "POST",url: "gv/gv_ngoaingu_process.php",data: dataString,dataType: "html",
			success: function(data) {
						ngoaingu_RefreshData();
						$( "#btnXoangoaingu" ).button({ disabled: false });
					 }// end function(data)	
		}); // end .ajax
	});	// end $("#btnXoangoaingu").click(function()
	
	function ngoaingu_RefreshData() {
		dataString = "cat=get_ngoaingu&hisid=<?php echo $_REQUEST["hisid"];?>";
		$.ajax({
			type: "POST",
			url: "gv/gv_ngoaingu_process.php",
			data: dataString,
			dataType: "html",
			success: function(data) {
						$("#tablengoaingu tbody").html(data);
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