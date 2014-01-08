<?php
if (isset($_REQUEST["hisid"])){
	session_id($_REQUEST["hisid"]);
	session_start();
}
if (!isset($_SESSION['uidloginhv'])){
	die('Truy cập bất hợp pháp');
}
include "libs/connect.php";

$usr = base64_decode($_SESSION['uidloginhv']);
$sqlstr="	SELECT h.ma_hoc_vien, (ho || ' ' || ten) ho_ten, email, n.ten_nganh, h.dien_thoai, h.so_cmnd, h.so_tai_khoan,
				h.dia_chi, to_char(h.ngay_sinh, 'dd/mm/yyyy') ngay_sinh, decode(h.phai, 'M', 'Nam', 'F', 'Nữ') phai, 
				t.ten_tinh_tp noi_sinh, don_vi_cong_tac, thanh_toan_tu_dong, k.ten_kinh_phi_dt, to_char(h.ngay_cap, 'dd/mm/yyyy') ngay_cap, h.noi_cap,
				decode(ctdt_loai(h.ma_hoc_vien), 1, 'Giảng dạy môn học + khóa luận', 2, 'Giảng dạy môn học + LVThs', 'Nghiên cứu') || ' ' || decode(ctdt_hv_nam(h.ma_hoc_vien), 0, null,'thuộc chương trình: ' || ctdt_hv_nam(h.ma_hoc_vien) || ' năm') ctdt,
				dot_cap_bang('$usr') dot_cap_bang
			FROM hoc_vien h, nganh n, dm_tinh_tp t, dm_kinh_phi_dao_tao k
			WHERE upper(h.ma_hoc_vien) = upper('$usr')
			AND h.ma_nganh = n.ma_nganh
			AND h.noi_sinh = t.ma_tinh_tp
			AND h.fk_kinh_phi_dao_tao = k.ma_kinh_phi_dt
";
$stmt = oci_parse($db_conn, $sqlstr);oci_execute($stmt);$n = oci_fetch_all($stmt, $accinfo);oci_free_statement($stmt);

$dotcapbang = $accinfo["DOT_CAP_BANG"][0];
if ($dotcapbang == '')
{
	$strsql="SELECT value FROM config WHERE name='DOT_CAP_BANG'";
	$oci_pa = oci_parse($db_conn,$strsql);oci_execute($oci_pa);$result=oci_fetch_all($oci_pa, $kt);oci_free_statement($oci_pa);
	$dotcapbang = $kt["VALUE"][0];
}
// Hinh ky yeu
$hinhkyyeufolder = "hinhkyyeu";
$strsql="SELECT ma_hoc_vien FROM hoc_vien WHERE dot_cap_bang('$usr') is null and diem_luan_van('$usr')>=5 and diem_av('$usr')>=5 and ma_hoc_vien = '$usr'";
$oci_pa = oci_parse($db_conn,$strsql);oci_execute($oci_pa);$result=oci_fetch_all($oci_pa, $kt);oci_free_statement($oci_pa);
$mahvkyyeu = $kt["MA_HOC_VIEN"][0];

$filehinh = "./$hinhkyyeufolder/$dotcapbang/$usr.jpg";

//if ($mahvkyyeu==$usr || $usr == '03207104')
//{
	$clickdivhinh = 'onclick="getFile()" style="margin: 0 10px 0px 0; cursor:pointer" ';
	$ndhuongdan = "Hình dùng làm kỷ yếu tốt nghiệp, bạn nên chọn ảnh tự nhiên, lịch sự, không nên chọn hình thẻ.<br/><b>Hướng dẫn tải ảnh lên:</b><br/> Click vào khung hình này hoặc bấm nút &quot;Tải lên&quot; chọn file ảnh chân dung (<b>.jpg, kích thước < 1MB</b>), sau đó bấm nút &quot;Open&quot; để tải ảnh";
?>
<style>
#hv_file_ky_yeu_progress { position:relative; width:100%; border: 1px solid #ddd; padding: 1px; border-radius: 3px; }
#hv_file_ky_yeu_bar { background-color: #B4F5B4; width:0%; height:20px; border-radius: 3px; }
#hv_file_ky_yeu_percent { position:absolute; display:inline-block; top:3px; left:48%; }
</style>
<form id="hv_frm_upload_file_ky_yeu" action="hv_upload_file_process.php?hisid=<?php echo $_REQUEST["hisid"]; ?>&w=uploadhinhkyyeu" method="post" enctype="multipart/form-data">
	<input type="hidden" name="MAX_FILE_SIZE" value="1048576" />
	<div style='display:none;'><input type="file" size="60" name="hv_file_ky_yeu" id="hv_file_ky_yeu"  onchange="hv_file_ky_yeu_change(this)" accept="image/jpeg"></div>
</form>
<?php
//}
?>
<div align="center">
<!--<form id="form_accountinfo" name="form_accountinfo" method="post" action="">-->
		<table width="500px" cellspacing="0" cellpadding="0" class="ui-corner-all shawdow">
		    <tr>
			  <td>&nbsp;</td>
			</tr>
        	<tr> 
			<td>
               <table width="100%" border="0" cellspacing="0" cellpadding="5" class="">
					<tr>
					  <td align=right>Mã HV</td>
					  <td style='font-weight:bold;width:240px' align=left><?php echo $accinfo["MA_HOC_VIEN"][0]; ?></td>
					  <td rowspan=8 align=center>
						<div id="hinhkyeu_chitiet" <?php echo $clickdivhinh; ?> align=center data-placement="top"  class="tooltips" title='<?php echo $ndhuongdan; ?>'>
							<img id=framehinhkyyeu src='images/khunganh4x6.png' width=113 height=170 class='ui-widget-content ui-corner-all' />
						</div>
						<div align=center style="margin: 5px 0 0 0;">
							<button id="btn_upload_hinhkyyeu" style='font-size:80%;'>&nbsp;Tải lên</button>
						</div>
					  </td>
					</tr>
					
					<tr>
					  <td align=right>Họ tên</td>
					  <td style='font-weight:bold;' align=left><?php echo $accinfo["HO_TEN"][0]; ?></td>
					</tr>
					
					<tr>
					  <td align=right>Phái</td>
					  <td style='font-weight:bold;' align=left><?php echo $accinfo["PHAI"][0]; ?></td>
					</tr>
					
					<tr>
					  <td align=right>Sinh ngày</td>
					  <td align=left ><b><?php echo $accinfo["NGAY_SINH"][0]; ?></b> tại <b><?php echo $accinfo["NOI_SINH"][0];?></td>
					</tr>
					
					<tr>
					  <td align=right>Ngành</td>
					  <td style='font-weight:bold;' align=left ><?php echo $accinfo["TEN_NGANH"][0]; ?></td>
					</tr>
					
					<tr>
					  <td align=right>Loại CTĐT</td>
					  <td style='font-weight:bold;' align=left><?php echo $accinfo["CTDT"][0]; ?></td>
					</tr>
					
					<tr>
					  <td align=right>Diện</td>
					  <td style='font-weight:bold;' align=left><?php echo $accinfo["TEN_KINH_PHI_DT"][0]; ?></td>
					</tr>
					
					<tr>
					  <td align=right><label for="hv_info_so_cmnd">Số CMND</label></td>
					  <td style='font-weight:bold;' align=left><input style="width:120px; font-weight:bold" placeholder="số CMND" id="hv_info_so_cmnd"  name="hv_info_so_cmnd" type="text" class="text" value="<?php echo $accinfo["SO_CMND"][0]; ?>" /></td>
					  
					</tr>
					<tr>
					  <td align=right><label for="hv_info_ngaycap_cmnd">Ngày cấp</label></td>
					  <td align=left colspan = 2><input style="width:120px; font-weight:bold" placeholder="ngày cấp" id="hv_info_ngaycap_cmnd"  name="hv_info_ngaycap_cmnd" type="text" class="text" value="<?php echo $accinfo["NGAY_CAP"][0]; ?>" />
						<font style="color: red">(dd/mm/yyyy)</font>
					  </td>
					</tr> 
					<tr>
					  <td align=right><label >Nơi cấp</label></td>
					  <td align=left colspan = 2>
						<select id="hv_info_noicap_cmnd" class="text" style="height: 28px; font-size:14px" name="hv_info_noicap_cmnd">
							<option>-chọn nơi cấp CMND-</option>
							<?php 
								$sqlstr="select * from dm_tinh_tp order by ten_tinh_tp"; 
								$stmt = oci_parse($db_conn, $sqlstr);oci_execute($stmt);$n=oci_fetch_all($stmt, $resDM);oci_free_statement($stmt);
								
								for ($i=0 ; $i < $n; $i++){
									($accinfo["NOI_CAP"][0]==$resDM["MA_TINH_TP"][$i]) ? $selected = "selected" : $selected = "";
									//($resDM["DK_TMDT"][$i]) ? $ma_cap = $resDM["MA_CAP"][$i] : $ma_cap='';
									echo "<option value='{$resDM["MA_TINH_TP"][$i]}' $selected>{$resDM["TEN_TINH_TP"][$i]}</option>";
								}
							?>
						</select>
					  </td>
					  
					</tr>
					
					<tr>
					  <td align=right>Số TK NH Đông Á</td>
					  <td colspan=2 style='font-weight:bold;' align=left><input style="width:98%; font-weight:bold" placeholder="Số TK Ngân hàng Đông Á" id="hv_info_so_tk"  name="hv_info_so_tk" type="text" class="text" value="<?php echo $accinfo["SO_TAI_KHOAN"][0]; ?>" /></td>
					</tr>
					
					<tr>
					  <td align=right></td>
					  <td colspan=2 style='' align=left>
						<?php 
							if ($accinfo["THANH_TOAN_TU_DONG"][0]==1)
								echo "Đã kích hoạt thanh toán tự động"; 
							else
								echo "<font color=red><b>Chưa kích hoạt thanh toán tự động</b><br/>Vui lòng đến ngân hàng Đông Á <br/>tại cổng 2 ĐHBK-TPHCM để kích hoạt.</font>"; 
						?>
					  </td>
					</tr>
					
					<tr>
					  <td align=right><label for="hv_info_diachi">Địa chỉ</label></td>
					  <td colspan=2 align=left><input style="width:98%; font-weight:bold" placeholder="địa chỉ" id="hv_info_diachi"  name="hv_info_diachi" type="text" class="text" value="<?php echo $accinfo["DIA_CHI"][0]; ?>" /></td>
					</tr>
					<tr>
					  <td align=right><label for="hv_info_dienthoai">Điện thoại</label></td>
					  <td colspan=2 align=left><input style="width:98%;font-weight:bold" placeholder="số điện thoại" id="hv_info_dienthoai"  name="hv_info_dienthoai" type="text" class="text" value="<?php echo $accinfo["DIEN_THOAI"][0]; ?>" /></td>
					</tr>
					<tr>
					  <td align=right><label for="hv_info_donvicongtac">Đơn vị công tác</label></td>
					  <td colspan=2 align=left><input style="width:98%;font-weight:bold" placeholder="đơn vị công tác" id="hv_info_donvicongtac"  name="hv_info_donvicongtac" type="text" class="text" value="<?php echo $accinfo["DON_VI_CONG_TAC"][0]; ?>" /></td>
					</tr>
					<tr>
					  <td align=right><label for="hv_info_email">Email</label></td>
					  <td colspan=2 align=left><input style="width:98%;font-weight:bold" placeholder="địa chỉ email" id="hv_info_email"  name="hv_info_email" type="text" class="text" value="<?php echo $accinfo["EMAIL"][0]; ?>" /></td>
					</tr>
					<tr>
					  <td colspan=3 align=left><b><br/><label for="hv_info_usrname">Xác nhận lại tài khoản khi thay đổi thông tin</label></b></td>
					</tr>
					
					<tr>
					  <td ></td>
					  <td colspan=2 align="left">
						 <div id="ai_tooltips" style="color:red; font-size:11px;"></div>
					  </td>
					</tr>
					
					<tr>
					  <td align=right><label for="hv_info_usrname" class="ui-icon ui-icon-person"></label>
					  </td>
					  <td colspan=2><input placeholder="tên đăng nhập" style="width:370px;" name="hv_info_usrname" type="text" class="text" id="hv_info_usrname" size="37" value="" /></td>
					</tr>
					
					
					<tr>
					  <td align=right><label for="hv_info_pass" class="ui-icon ui-icon-locked"></label>
						</td>
					  <td colspan=2 ><input placeholder="mật khẩu" style="width:370px;" name="hv_info_pass" type="password" class="text" id="hv_info_pass" size="37" /></td>
					</tr>
					
					<tr> 
						<td colspan=3> <div align="center" id="tipAI" class=" validateTips"></div></td>
					</tr> 
					<tr>
					  <td>&nbsp;</td>
					  <td colspan=2 align="right"> <button id="hv_btnInfoChange">Thay đổi</button>&nbsp;</td>
					</tr>
					<tr>
					  <td align="center" colspan=3> </td>
					</tr>
             	
               </table>
             </td> 
			 </tr>
        </table>  
<!--</form>-->
</div>

<div id="hv_processing_upload_div" title="Upload hình ...">
	 <div align=left style="margin: 5px 0 10px 0; color: #636363">
	 <b>Đang tải ảnh lên máy chủ ... </b>
	 </div>
	 <div id="hv_file_ky_yeu_progress">
		<div id="hv_file_ky_yeu_bar"></div>
		<div id="hv_file_ky_yeu_percent" style='font-weight:bold'>0%</div >
	</div>
	<div align=center id=hv_file_ky_yeu_message style="margin-top: 10px; color: red"></div>
</div>
					
<script>
function getFile(){
   document.getElementById("hv_file_ky_yeu").click();
}

function hv_file_ky_yeu_change(obj){
  var file = obj.value;
  if (file != ''){
	$("#hv_frm_upload_file_ky_yeu").submit();
  }
}

function sub(obj){
  var file = obj.value;
  var fileName = file.split("\\");
  document.getElementById("userfilefake").innerHTML  = fileName[fileName.length-1];
}
 
$(function() {
	$(".tooltips").tooltip('hide');
	$( "#hv_btnInfoChange").button();
	$( "#btn_upload_hinhkyyeu" ).button({ icons: {primary:'ui-icon ui-icon-disk'} });
	
	$( "#hv_info_ngaycap_cmnd" ).mask("99/99/9999");
	
	$("#btn_upload_hinhkyyeu").click(function(e){
		document.getElementById("hv_file_ky_yeu").click();
	});	// end 
	
	$('#hv_frm_upload_file_ky_yeu').on('submit', function () {
        //check if the form submission is valid, if so just let it submit
        //otherwise you could call `return false;` to stop the submission
		var input = document.getElementById("hv_file_ky_yeu"), bvalid = true;
		
		if ($("#hv_file_ky_yeu").val()=="")
			bvalid = false;
		else
		{
			var str = $("#hv_file_ky_yeu").val();
			var	ext = str.substr(str.length-4,4);
			
			if (ext.toLowerCase()!=".jpg")
				bvalid = false;
		}
		
		if (!bvalid)
		{
			//$( "#btn_upload_hinhkyyeu" ).button( "disable" );
			hv_open_msg_box("<font color=red>Vui lòng chọn file định dạng .JPG (Kích thước < 1MB) bằng cách <b>click vào khung hình 4x6</b> sau đó <b>nhấn nút &quot;Open&quot;</b>.</font>", 'alert', 280, 150);
			return bvalid;
		}
		
		if (input.files[0].size>1048576)
		{
			bvalid = false;
			hv_open_msg_box("<font color=red>Vui lòng chọn file định dạng .JPG có <b>kích thước < 1MB</b>.</font>", 'alert', 280, 150);
			return bvalid;
		}
		
		$( "#hv_processing_upload_div").dialog( "open" );
    });

	<?php
		// Khoi tao hinh khi load form
		if (file_exists($filehinh)) {
			echo "var day = new Date(), id= day.getTime();
				  $('#framehinhkyyeu').attr('src', '$filehinh' + '?'+id);";	
		}
	?>
	
	var ai_jemail 		= $("#hv_info_email"),
	ai_jdiachi 			= $("#hv_info_diachi"),
	ai_jdienthoai		= $("#hv_info_dienthoai"),
	ai_jdonvicongtac	= $("#hv_info_donvicongtac"),
	ai_juser			= $("#hv_info_usrname"),
	ai_jpass			= $("#hv_info_pass"),
	ai_jcmnd			= $("#hv_info_so_cmnd"), 
	ai_jngaycap			= $("#hv_info_ngaycap_cmnd"),
	ai_jnoicap			= $("#hv_info_noicap_cmnd"),
	ai_jsotk			= $("#hv_info_so_tk"),
	ai_allFields = $([]).add(ai_jemail).add(ai_juser).add(ai_jpass).add(ai_jdiachi).add(ai_jdienthoai).add(ai_jdonvicongtac).add(ai_jcmnd).add(ai_jngaycap).add(ai_jnoicap).add(ai_jsotk),
	ai_tips	= $("#tipAI");
	    
	function ai_updateTips( t ) {
		ai_tips
					.text( t )
					.addClass( "ui-state-highlight" );
		setTimeout(function() {
			ai_tips.removeClass( "ui-state-highlight", 1500 );
		}, 1000 );
	}
	
	// Checklength
	function ai_checkLength( o, n, min, max, allownull) {
		if (allownull && o.val().length==0)
		{
			return true;
		}
		if (min==0 && (o.val().length==0))
		{
			o.addClass( "ui-state-error" );
			o.focus();	
			ai_updateTips( n + " không được để trống." );
			
			return false;
		}else if (min==max && o.val().length<min){
			o.addClass( "ui-state-error" );
			o.focus();	
			ai_updateTips( "Thông tin " + n + " phải đủ " + min + " ký tự." );
		}else if ( o.val().length > max || o.val().length < min ) {
			o.addClass( "ui-state-error" );
			o.focus();	
			ai_updateTips( "Chiều dài của " + n + " từ " +
						min + " đến " + max + " ký tự." );
			return false;
		} else {
			return true;
		}
	}
	
	function ai_checkRegexp( o, regexp, n ) {
		//alert('a');
		if ( !( regexp.test( o.val() ) ) ) {
			o.addClass( "ui-state-error" );
			o.focus();
			ai_updateTips( n);
			return false;
		} else {
			return true;
		
		}
	}
// End of check validate
	$( "#hv_processing_upload_div" ).dialog({
			resizable: false,
			autoOpen: false,
			width:250, height:120,
			modal: true
			
	});
	
	$("#hv_btnInfoChange").click(function(e){
	//$("#form_changepass").submit(function(e) {
		var bValid = true;
		ai_allFields.removeClass( "ui-state-error" );
		
		bValid = bValid && ai_checkLength( ai_jcmnd, "\"Số CMND\"", 0, 10);
		bValid = bValid && ai_checkLength( ai_jngaycap, "\"Ngày cấp CMND\"", 0, 10);
		bValid = bValid && ai_checkLength( ai_jnoicap, "\"Nơi cấp CMND\"", 0, 2);
		bValid = bValid && ai_checkLength( ai_jsotk, "\"Số TK ngân hàng Đông Á\"", 0, 20, 1);
		
		bValid = bValid && ai_checkLength( ai_jdiachi, "\"Địa chỉ\"", 0, 300);
		bValid = bValid && ai_checkLength( ai_jdienthoai, "\"Điện thoại\"", 0, 50);
		bValid = bValid && ai_checkLength( ai_jdonvicongtac, "\"Đơn vị công tác\"", 0, 350, 1);
		
		bValid = bValid && ai_checkLength( ai_jemail, "\"Email\"", 0, 100 );
		bValid = bValid && ai_checkRegexp( ai_jemail, /^((([a-z]|\d|[!#\$%&'\*\+\-\/=\?\^_`{\|}~]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])+(\.([a-z]|\d|[!#\$%&'\*\+\-\/=\?\^_`{\|}~]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])+)*)|((\x22)((((\x20|\x09)*(\x0d\x0a))?(\x20|\x09)+)?(([\x01-\x08\x0b\x0c\x0e-\x1f\x7f]|\x21|[\x23-\x5b]|[\x5d-\x7e]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(\\([\x01-\x09\x0b\x0c\x0d-\x7f]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF]))))*(((\x20|\x09)*(\x0d\x0a))?(\x20|\x09)+)?(\x22)))@((([a-z]|\d|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(([a-z]|\d|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])*([a-z]|\d|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])))\.)+(([a-z]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(([a-z]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])*([a-z]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])))\.?$/i,"Nhập không đúng định dạng email, vd: pgs@hcmut.edu.vn" );
		
		bValid = bValid && ai_checkLength( ai_juser, "\"Tên người dùng\"", 0, 100 );
		bValid = bValid && ai_checkLength( ai_jpass, "\"Mật khẩu\"", 0, 100 );
		
		if (bValid){
			ai_tips.text("");
			dataString = ai_allFields.serialize(); //$("#form_accountinfo").serialize();
			dataString += '&hisid=<?php echo $_REQUEST["hisid"];?>';
			
			$.ajax({type: "POST",url: "hv_accountinfo_process.php",data: dataString, dataType: "json",
				success: function(data) {
							//ai_updateTips(data.msg);
							hv_open_msg_box(data.msg, 'info', 280, 150);
						 }// end function(data)	
			}); // end .ajax
		}
		//if (!bValid){		
		e.preventDefault();
			//$("#form_dangnhap").submit();
		//}
	});	// end frmTraCuuCD
	
	
	$('#hv_info_pass').keypress(function(e) { 
		var s = String.fromCharCode( e.which );
		if ( s.toUpperCase() === s && s.toLowerCase() !== s && !e.shiftKey ) {
			$("#ai_tooltips").html('Chú ý: Caps Lock đang mở');
		}
		else
		{
			$("#ai_tooltips").html('');
		}
	});
	
	$('input[placeholder],textarea[placeholder]').placeholder();
	
	$("#hv_file_ky_yeu_progress").hide();
	var options = {
		beforeSend: function()
		{
			$("#hv_file_ky_yeu_progress").show();
			//clear everything
			$("#hv_file_ky_yeu_bar").width('0%');
			$("#hv_file_ky_yeu_message").html("");
			$("#hv_file_ky_yeu_percent").html("0%");
		},
		uploadProgress: function(event, position, total, percentComplete)
		{
			$("#hv_file_ky_yeu_bar").width(percentComplete+'%');
			$("#hv_file_ky_yeu_percent").html(percentComplete+'%');
			
		},
		success: function()
		{
			$("#hv_file_ky_yeu_bar").width('100%');
			$("#hv_file_ky_yeu_percent").html('100%');
			
		},
		complete: function(response)
		{
			//alert(response.responseText.search("Lỗi: "));
			if (response.responseText.search("Lỗi: ")>-1){
				$("#hv_file_ky_yeu_message").html("<font color=red>"+response.responseText+"</font>");
				hv_open_msg_box("<font color=red>"+response.responseText+"</font>", 'info', 280, 150);
			}else{
				$("#hv_file_ky_yeu_message").html("<font color=green><b>Tải ảnh thành công</b></font>");
				var day = new Date(), id= day.getTime();				
				$("#framehinhkyyeu").attr("src", response.responseText + '?'+id);
			}
		},
		error: function()
		{
			$("#hv_file_ky_yeu_message").html("<font color='red'> ERROR: unable to upload files</font>");
		}
	 
	};
    $("#hv_frm_upload_file_ky_yeu").ajaxForm(options);
});
</script>

<?php 
if (isset ($db_conn))
	oci_close($db_conn);
?>