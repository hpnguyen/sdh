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
$sqlstr="select cb.*, to_char(cb.NGAY_SINH,'dd-mm-yyyy') NGAY_SINH, to_char(cb.NGAY_CAP,'dd/mm/yyyy') NGAY_CAP, k.ten_khoa, bm.ten_bo_mon, 
		decode(MA_HOC_HAM, 'GS','Giáo Sư' ,'PGS','Phó Giáo Sư' ,'') chuc_danh,
		decode(MA_HOC_VI, 'TSK','Tiến Sĩ Khoa Học', 'TS','Tiến Sĩ', 'TH','Thạc Sĩ', 'CN','Cử Nhân', 'KS','Kỹ Sư', '') hoc_vi,
		get_nam_dat_hv_cao_nhat(cb.ma_can_bo, cb.ma_hoc_vi) nam_dat_hv_cao_nhat
		from can_bo_giang_day cb, bo_mon bm, khoa k
		where cb.ma_bo_mon = bm.ma_bo_mon (+) and bm.ma_khoa = k.ma_khoa (+)
		and cb.ma_can_bo='".$_SESSION['macb']."'";

$stmt = oci_parse($db_conn, $sqlstr);
oci_execute($stmt);
$n = oci_fetch_all($stmt, $cbgd);
oci_free_statement($stmt);

$filehinh  = $cbgd["HINH_ANH"][0];
//if ($cbgd["HINH_ANH"][0]!="")
//	$filehinh  = $cbgd["HINH_ANH"][0];
//else
//	$filehinh  = "images/khunganh4x6.png";

$search = array('\\','"');
$replace = array('\\\\',"&quot;"); 
?>
<style>
	.disableText{
		color:#808080;
	}
	.disableTextBox{
		background: #808080;
	}
</style>

<form id="form_ttgv" name="form_ttgv" method="post" action="" >
  <div align="center">
    <table style="width:700px"  cellspacing="0" cellpadding="0" class="ui-corner-all shawdow tableData" >
      <tr>
        <td colspan="4" valign='top'> 
		<div align="center" id="tipTTGV" class="ui-corner-tl ui-corner-tr validateTips"></div>
        </td>
      </tr>
      <tr>
        <td colspan="4" >
      
        <table width="100%" border="0" align="center" cellpadding="5" cellspacing="0">
        
			<tr align="left">        
				<td colspan="2" class="heading" style="width:250px" align="right"><label> Họ tên </label></td>
				<td style="width:400px" class="fontcontent"><?php echo $cbgd["HO"][0]. " " .$cbgd["TEN"][0]; ?></td>
				<td rowspan=8 valign=top>
					<div id="hinhgv_chitiet" onclick="getFileTTGV()" style="margin: 0 10px 5px 0; cursor:pointer" align=center data-placement="top" class="tooltips_ttgv" title='<b>Hướng dẫn tải hình lên:</b> <br/> <b>1.</b> Click vào khung hình 4x6 để chọn ảnh (<b>ảnh tỷ lệ 4x6, kích thước < 1MB</b>) <br/> <b>2.</b> Bấm nút &quot;Tải lên&quot;'><img id=framehinh46_ttgv src='images/khunganh4x6.png' width=113  class='ui-widget-content ui-corner-all' /></div>
					<div align=center>
						<a id="btn_upload_hinh_ttgv" style='font-size:80%;'>&nbsp;Tải lên</a>
					</div>
					<div align=center>
						<a id="btn_upload_hinh_chu_ky_ttgv" style='font-size:80%; margin-top:10px'>&nbsp;Chữ ký</a>
					</div>
				</td>
			</tr>
			<tr align="left">
				<td colspan="2" class="heading" align="right"><label>Ngày sinh </label></td>
				<td class="fontcontent">
					<input type="text" name="txtNgaySinh" id="txtNgaySinh" style="width:90px; text-align:center;" value="<?php echo htmlentities($cbgd["NGAY_SINH"][0], ENT_QUOTES, "UTF-8");?>" maxlength="10" class="text ui-widget-content ui-corner-all tableData"/>
					&nbsp; &nbsp; <label class="heading">Phái</label>  
					<select name="txtPhai" id="txtPhai" class="text ui-widget-content ui-corner-all tableData" style="height:23px;font-size:15px;">
						<option value="" <?php if ($cbgd["PHAI"][0]=="") echo "selected"; ?>></option>
						<option value="M" <?php if ($cbgd["PHAI"][0]=="M") echo "selected"; ?>>Nam</option>
						<option value="F" <?php if ($cbgd["PHAI"][0]=="F") echo "selected"; ?>>Nữ</option>
					</select>
					&nbsp; &nbsp;
					<label title="Số hiệu công chức" class=heading>SHCC</label>
					<?php echo $cbgd["SHCC"][0];  ?>
					<input name="macb" type="hidden" value="<?php echo htmlentities($macb, ENT_QUOTES, "UTF-8");?>" />
				</td>
				
			</tr>
			<tr align="left">
				<td colspan="2" class="heading" align="right"><label title="Số hiệu công chức" class=heading>CMND số</label></td>
				<td class="fontcontent">
					<input type="text" name="txtCMND" id="txtCMND" style="width:90px; text-align:center;" value="<?php echo htmlentities($cbgd["SO_CMND"][0], ENT_QUOTES, "UTF-8");?>" maxlength="15" class="text ui-widget-content ui-corner-all tableData"/>
					&nbsp;
					<label title="Ngày cấp" class=heading>Ngày cấp</label>
					<input type="text" name="txtNgayCap" id="txtNgayCap" style="width:90px; text-align:center;" value="<?php echo htmlentities($cbgd["NGAY_CAP"][0], ENT_QUOTES, "UTF-8");?>" maxlength="10" class="text ui-widget-content ui-corner-all tableData"/>
				</td>
			</tr>
			<tr align="left">
				<td colspan="2" class="heading" align="right"><label title="Nơi cấp" class=heading>Nơi cấp CMND</label></td>
				<td class="fontcontent">
					<select id=txtNoiCapCMND name=txtNoiCapCMND placeholder='Nơi cấp CMND' title='Nơi cấp CMND' style='width:200px;height:24px'>
						<option value=''>-chọn nơi cấp CMND-</option>
						<?php $sqlstr="select ma_tinh_tp, ten_tinh_tp from dm_tinh_tp order by viet0dau_name(ten_tinh_tp)"; 
							$stmt = oci_parse($db_conn, $sqlstr);oci_execute($stmt);$n = oci_fetch_all($stmt, $resDM);oci_free_statement($stmt);
							for ($i = 0; $i < $n; $i++)
							{
								if ($cbgd["NOI_CAP"][0]==$resDM["MA_TINH_TP"][$i]){
									echo "<option value='".$resDM["MA_TINH_TP"][$i]."' selected>" .$resDM["TEN_TINH_TP"][$i]. "</option>";
								}else{
									echo "<option value='".$resDM["MA_TINH_TP"][$i]."'>" .$resDM["TEN_TINH_TP"][$i]. "</option>";
								}
							}
							
						  ?>
					</select>
					
					&nbsp;&nbsp;<label for="txtMST" class=heading title="Mã số thuế">MST</label> <input name="txtMST" id="txtMST" title="Mã số thuế" placeholder="mã số thuế" type="text"  value="<?php echo htmlentities($cbgd["MA_SO_THUE"][0], ENT_QUOTES, "UTF-8");?>" style="width:145px" maxlength="10" class="text ui-widget-content ui-corner-all tableData" />
				</td>
			</tr>
			
			<tr align="left">
				<td colspan="2" class="heading" align="right"><label title="Số tài khoản" for="txtSTK" class=heading>Số tài khoản</label></td>
				<td class="fontcontent">
					<input id="txtSTK" name="txtSTK" placeholder='số tài khoản' title='Số tài khoản' style='width:200px;height:24px' maxlength="30" value="<?php echo htmlentities($cbgd["SO_TAI_KHOAN"][0], ENT_QUOTES, "UTF-8"); ?>" class="text ui-widget-content ui-corner-all tableData"/>					
				</td>
			</tr>
			<tr align="left">
				<td colspan="2" class="heading" align="right"><label for="txtTaiNH" class=heading title="Tại ngân hàng">Tại ngân hàng</label></td>
				<td class="fontcontent">
					<input name="txtTaiNH" id="txtTaiNH" title="Tại ngân hàng" placeholder="tại ngân hàng" type="text"  value="<?php echo htmlentities($cbgd["NGAN_HANG_MO_TK"][0], ENT_QUOTES, "UTF-8");?>" style="width:145px" maxlength="100" class="text ui-widget-content ui-corner-all tableData" />
					&nbsp;&nbsp;<label for="txtChiNhanhNH" class=heading title="Chi nhánh ngân hàng">CN</label> <input name="txtChiNhanhNH" id="txtChiNhanhNH" title="Chi nhánh ngân hàng" placeholder="chi nhánh ngân hàng" type="text"  value="<?php echo htmlentities($cbgd["CHI_NHANH_NGAN_HANG"][0], ENT_QUOTES, "UTF-8");?>" style="width:200px" maxlength="60" class="text ui-widget-content ui-corner-all tableData" />
				</td>
			</tr>
			
		  <tr align="left">
			<td colspan="2" class="heading" align="right"><label>Khoa</label></td>
			<td class="fontcontent"><?php echo $cbgd["TEN_KHOA"][0]; ?> <span class="heading"> - Bộ Môn</span>&nbsp;<span class="fontcontent"><?php echo $cbgd["TEN_BO_MON"][0]; ?></span></td>
		  </tr>
		  <tr align="left">
			<td colspan="2" class="heading" align="right"><label for="txtCoQuan" title="Cơ quan công tác">CQ công tác</label></td>
			<td ><input type="text" name="txtCoQuan" id="txtCoQuan" style="width:98%" value="<?php echo htmlentities($cbgd["CO_QUAN_CONG_TAC"][0], ENT_QUOTES, "UTF-8");?>" maxlength="200" class="text ui-widget-content ui-corner-all tableData"/></td>
		  </tr>
		  <tr align="left">
			<td colspan="2" class="heading" align="right"><label for="txtDiaChi">Địa chỉ CQ</label></td>
			<td ><input type="text" name="txtDiaChi" id="txtDiaChi" style="width:98%" value="<?php echo htmlentities($cbgd["DIA_CHI"][0], ENT_QUOTES, "UTF-8");?>" maxlength="200" class="text ui-widget-content ui-corner-all tableData"/></td>
		  </tr>
		  <tr align="left">
			<td colspan="2" class="heading" align="right"><label for="txtWebsite">Website CQ</label></td>
			<td colspan="2"><input type="text" name="txtWebsite" id="txtWebsite" style="width:250px" value="<?php echo htmlentities($cbgd["WEBSITE_CO_QUAN"][0], ENT_QUOTES, "UTF-8");?>" maxlength="200" class="text ui-widget-content ui-corner-all tableData"/>
			 &nbsp;&nbsp; <label for="txtDienThoai" class="heading">Điện thoại CQ</label> <input name="txtDienThoai" type="text" id="txtDienThoai" value="<?php echo htmlentities($cbgd["DIEN_THOAI"][0], ENT_QUOTES, "UTF-8");?>" style="width:150px" maxlength="40" class="text ui-widget-content ui-corner-all tableData"/>
			</td>
		  </tr>
		  <tr align="left">
			<td colspan="2" class="heading" align="right"><label for="txtTenLanhDaoCQ">Lãnh đạo CQ</label></td>
			<td colspan="2"><input name="txtTenLanhDaoCQ" type="text" id="txtTenLanhDaoCQ" value="<?php echo htmlentities($cbgd["TEN_NGUOI_LANH_DAO_CQ"][0], ENT_QUOTES, "UTF-8");?>" style="width:250px;" maxlength="50" class="text ui-widget-content ui-corner-all tableData" placeholder="họ và tên người lãnh đạo CQ" />
			&nbsp;&nbsp;<label for="txtDTLanhDaoCQ" class=heading title="ĐT Lãnh đạo cơ quan">ĐT Lãnh đạo</label> <input name="txtDTLanhDaoCQ" title="ĐT Lãnh đạo cơ quan" placeholder="ĐT Lãnh đạo CQ" type="text" id="txtDTLanhDaoCQ" value="<?php echo htmlentities($cbgd["DIEN_THOAI_LANH_DAO_CQ"][0], ENT_QUOTES, "UTF-8");?>" style="width:185px" maxlength="25" class="text ui-widget-content ui-corner-all tableData" />
			</td>
		  </tr>
		  
		  
		  <tr align="left">
			<td colspan="2" class="heading" align="right"><label for="txtNamCongTac">Năm BĐ công tác </label></td>
			<td colspan="2"><input name="txtNamCongTac" type="text" id="txtNamCongTac" value="<?php echo htmlentities($cbgd["NAM_BD_CONG_TAC"][0], ENT_QUOTES, "UTF-8");  ?>" maxlength="4" class="text ui-widget-content ui-corner-all tableData" style="width: 40px;"/>
			&nbsp;&nbsp;<label for="txtNamNghiHuu" class="heading">Năm nghỉ hưu </label> <input name="txtNamNghiHuu" type="text" id="txtNamNghiHuu" value="<?php echo htmlentities($cbgd["NAM_NGHI_HUU"][0], ENT_QUOTES, "UTF-8");?>" maxlength="4" class="text ui-widget-content ui-corner-all tableData" style="width: 40px;"/>
			</td>
		  </tr>
		  
		  <tr align="left">
			<td colspan="2" class="heading" align="right"><label for="txtChucVu">Chức vụ hiện tại</label></td>
			<td colspan="2">
			<select name="txtChucVu" id="txtChucVu" class="text ui-widget-content ui-corner-all tableData" style="height:23px;font-size:15px;width: 175px;">
			   <option value="">chọn chức vụ của bạn</option>
			   <?php $sqlstr="select * from DM_CHUC_VU order by TEN_CHUC_VU"; 
				$stmt = oci_parse($db_conn, $sqlstr);
				oci_execute($stmt);
				$n = oci_fetch_all($stmt, $resDM);
				oci_free_statement($stmt);
				
				$txttmp = "";
				
				for ($i = 0; $i < $n; $i++)
				{
					if ($cbgd["FK_CHUC_VU"][0] == $resDM["MA_CHUC_VU"][$i])
						$txttmp = " selected ";
					else
						$txttmp = "";
					echo "<option value='" .$resDM["MA_CHUC_VU"][$i]."'" .$txttmp. "> " .$resDM["TEN_CHUC_VU"][$i]. " </option> ";
				}
				
			   ?>
			</select>
			&nbsp;&nbsp;<label for="txtBoMonQL" class="heading"> </label> 
			
			<select name="txtBoMonQL" id="txtBoMonQL" class="text ui-widget-content ui-corner-all tableData" style="height:23px;font-size:15px;width: 335px;">
			   <option value="">chọn đơn vị quản lý</option>
			   <?php $sqlstr="select * from BO_MON order by TEN_BO_MON"; 
				$stmt = oci_parse($db_conn, $sqlstr);
				oci_execute($stmt);
				$n = oci_fetch_all($stmt, $resDM);
				oci_free_statement($stmt);
				
				$txttmp = "";
				
				for ($i = 0; $i < $n; $i++)
				{
					if ($cbgd["MA_BO_MON_QL"][0] == $resDM["MA_BO_MON"][$i])
						$txttmp = " selected ";
					else
						$txttmp = "";
					echo "<option value='" .$resDM["MA_BO_MON"][$i]."'" .$txttmp. "> " .$resDM["TEN_BO_MON"][$i]. " </option> ";
				}
			   ?>
			</select>

			</td>
		  </tr>
		  
		  <tr align="left">
			<td colspan="2" class="heading" align="right"><label for="txtDiaChiRieng">Địa chỉ riêng</label></td>
			<td colspan="2"><input name="txtDiaChiRieng" type="text" id="txtDiaChiRieng" value="<?php echo htmlentities($cbgd["DIA_CHI_RIENG"][0], ENT_QUOTES, "UTF-8");?>" style="width:98%" maxlength="200" class="text ui-widget-content ui-corner-all tableData"/></td>
		  </tr>
		  
		  <tr align="left">
			<td colspan="2" class="heading" align="right"><label for="txtDienThoai2">Điện thoại cá nhân</label></td>
			<td colspan="2">
				<input name="txtDienThoai2" type="text" id="txtDienThoai2" value="<?php echo htmlentities($cbgd["DIEN_THOAI_CN"][0], ENT_QUOTES, "UTF-8");?>" style="width:150px" maxlength="20" class="text ui-widget-content ui-corner-all tableData" />
				&nbsp;&nbsp; <label for="txtFax" class="heading">Fax </label><input name="txtFax" type="text" id="txtFax" value="<?php echo htmlentities($cbgd["FAX"][0], ENT_QUOTES, "UTF-8");?>" style="width:150px" maxlength="20" class="text ui-widget-content ui-corner-all tableData" />
			</td>
		  </tr>
		  <tr align="left">
			<td colspan="2" class="heading" valign=top align="right"><label for="txtEmail"> Email chính </label></td>
			<td colspan="2"><input placeholder="email cơ quan ..." name="txtEmail" type="text" id="txtEmail" value="<?php echo htmlentities($cbgd["EMAIL"][0], ENT_QUOTES, "UTF-8");?>" size="35" maxlength="100" class="text ui-widget-content ui-corner-all tableData"/> <br/>(<span style="color:red">@hcmut.edu.vn</span>, thông tin về học vụ của phòng SĐH đều được gửi qua email này) <span class="validateTips" id="emailValidate"> <span></td>
		  </tr>
		  <tr align="left">
			<td colspan="2" class="heading" align="right"><label for="txtEmail2">Email phụ </label></td>
			<td colspan="2"><input placeholder="email cá nhân ..." name="txtEmail2" type="text" id="txtEmail2" value="<?php echo htmlentities($cbgd["EMAIL_2"][0], ENT_QUOTES, "UTF-8");?>" size="35" maxlength="100" class="text ui-widget-content ui-corner-all tableData"/>
			  </td>
		  </tr>
		  <tr align="left">
			<td colspan="2" class="heading" align="right"><label>Học vị cao nhất </label></td>
			<td colspan="2" class="fontcontent">
			  <?php echo $cbgd["HOC_VI"][0];?>		  
			  <span class="heading"> &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;-&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Năm đạt </span> 
			  <?php echo $cbgd["NAM_DAT_HV_CAO_NHAT"][0];  ?>
			</td>
		  </tr>
		
		  <tr align="left">
			<td colspan="2" class="heading" align="right"><label for="txtChuyenNganh">Chuyên ngành </label></td>
			<td colspan="2">
			  <input name="txtChuyenNganh" type="text" id="txtChuyenNganh" value="<?php echo htmlentities($cbgd["CHUYEN_NGANH"][0], ENT_QUOTES, "UTF-8");?>" style="width:98%" maxlength="200" class="text ui-widget-content ui-corner-all tableData"/>
			</td>
		  </tr>
		  
		  <tr align="left">
			<td colspan="2" class="heading" align="right"><label for="txtChuyenMon">Chuyên môn hiện tại </label></td>
			<td colspan="2">
			  <input name="txtChuyenMon" type="text" id="txtChuyenMon" value="<?php echo htmlentities($cbgd["CHUYEN_MON"][0], ENT_QUOTES, "UTF-8");?>" style="width:98%" maxlength="200" class="text ui-widget-content ui-corner-all tableData"/>
			</td>
		  </tr>
		  
		  <tr align="left">
			<td colspan="2" class="heading" align="right">Chức danh</td>
			<td colspan="2" class="fontcontent">
			  <?php echo $cbgd["CHUC_DANH"][0];	?>
			  <input name="txtMaHocHam" type="hidden" id="txtMaHocHam" value="<?php echo htmlentities($cbgd["MA_HOC_HAM"][0], ENT_QUOTES, "UTF-8");  ?>" size="4" maxlength="4"/>
			  <span class="heading">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;-&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<label for=txtNamPhongHocHam>Năm công nhận</label></span> 
			  <input name="txtNamPhongHocHam" type="text" id="txtNamPhongHocHam" value="<?php echo htmlentities($cbgd["NAM_PHONG_HOC_HAM"][0], ENT_QUOTES, "UTF-8");  ?>" size="4" maxlength="4" class="text ui-widget-content ui-corner-all tableData"/>
			</td>
		  </tr>
		  
		  <tr align="left">
			<td colspan="2" class="heading" align="right"><label for="txtChucDanhNghienCuu">Chức danh nghiên cứu</label></td>
			<td colspan="2" class="fontcontent">
				<input name="txtChucDanhNghienCuu" type="text" id="txtChucDanhNghienCuu" value="<?php echo htmlentities($cbgd["CHUC_DANH_NGHIEN_CUU"][0], ENT_QUOTES, "UTF-8");  ?>" style="width:202px" maxlength="100" class="text ui-widget-content ui-corner-all tableData"/>
			</td>
		  </tr>
		  
		  <tr >
			<td colspan="4" align="center" height="30">
			<input name="hisid" type="hidden" id="hisid" value="<?php echo session_id(); ?>" />
				<input id="btnLuuTTGV" type=submit value='Cập nhật'>
			</td>
		  </tr> 
		  
		   <tr >
			<td colspan="4" align="center">
			
			</td>
		  </tr> 
        </table>
      
        </td>
      </tr>
    </table>
  </div>
  
</form>  <!-- end form_ttgv -->

<style>
#gv_file_chandung_progress { position:relative; width:100%; border: 1px solid #ddd; padding: 1px; border-radius: 3px; }
#gv_file_chandung_bar { background-color: #B4F5B4; width:0%; height:20px; border-radius: 3px; }
#gv_file_chandung_percent { position:absolute; display:inline-block; top:3px; left:48%; }
</style>
<form id="gv_frm_upload_file_chandung" action="gv/gv_file_upload_process.php?hisid=<?php echo $_REQUEST["hisid"]; ?>&w=uploadfilegv" method="post" enctype="multipart/form-data">
	<input type="hidden" name="MAX_FILE_SIZE" value="1048576" />
	<div style='display:none;'><input type="file" size="60" name="gv_file_chandung" id="gv_file_chandung"  onchange="userfile_ttgv_change(this)" accept="image/jpeg"></div>
</form>

<div id="ttgv_processing_div" title="Upload hình ...">
	 <div align=center style="margin: 0px 0 10px 0; color: #636363">
	 <b>Đang tải ảnh lên máy chủ ... </b>
	 </div>
	 <div id="gv_file_chandung_progress">
		<div id="gv_file_chandung_bar"></div>
		<div id="gv_file_chandung_percent" style='font-weight:bold'>0%</div >
	</div>
	<div align=center id=gv_file_chandung_message style="margin-top: 10px; color: red"></div>
</div>
<script type="text/javascript">
//jQuery.ajax
//$(document).ready(function(){
$(function(){ 
  $(".tooltips_ttgv").tooltip({ track: true });
  
 // ok
 $( "#btnLuuTTGV" ).button({ icons: {primary:'ui-icon ui-icon-check'} });
 $( "#btnLuuTTGV" ).button( "disable" );
 // cancel
 
 
 $("#txtNgaySinh").datepicker({
	defaultDate: "-30y",
	changeMonth: true,
	changeYear: true,
	showButtonPanel: false,
	dateFormat: "dd/mm/yy"
 });
 $("#txtNgayCap").datepicker({
	changeMonth: true,
	changeYear: true,
	showButtonPanel: false,
	dateFormat: "dd/mm/yy"
 });
 $("#txtNgaySinh, #txtNgayCap").mask("99/99/9999");
 
 //$("#txtSTK").mask("999999999999999999999999");
 
 if ($('#txtMaHocHam').val() != 'GS' && $('#txtMaHocHam').val() != 'PGS')
 {
	document.getElementById('txtNamPhongHocHam').disabled=true;
	$('#txtNamPhongHocHam').addClass("disableTextBox");
 }
 else
 {
	//document.getElementById('txtNamPhongHocHam').disabled=false;
	//$('#txtNamPhongHocHam').removeClass("disableTextBox");
 }
 
 
 
// Check validate fields TTGV
var txtNgaySinh 	= $("#txtNgaySinh"),
	txtPhai			= $("#txtPhai"),
	txtDiaChi 		= $("#txtDiaChi"),
	txtDienThoai	= $("#txtDienThoai"),
	txtDienThoai2	= $("#txtDienThoai2"),
	txtEmail		= $("#txtEmail"),
	txtEmail2		= $("#txtEmail2"),
	txtChuyenNganh	= $("#txtChuyenNganh"),
	txtNamPhongHocHam = $("#txtNamPhongHocHam"),
	txtTenLanhDaoCQ	= $("#txtTenLanhDaoCQ"),
	txtDTLanhDaoCQ = $("#txtDTLanhDaoCQ"),
	txtChucDanhNghienCuu = $("#txtChucDanhNghienCuu"),
	txtFax = $("#txtFax"), 
	txtWebsite = $("#txtWebsite"),
	txtSTK = $("#txtSTK"),
	formchange 		= false,
	allFieldsTTGV = $( [] ).add( txtNgaySinh ).add( txtPhai ).add( txtWebsite ).add( txtFax ).add( txtTenLanhDaoCQ ).add( txtDTLanhDaoCQ ).add( txtChucDanhNghienCuu ).add( txtDiaChi ).add( txtDienThoai ).add( txtDienThoai2 ).add( txtEmail ).add( txtEmail2 ).add(  ).add( txtChuyenNganh ).add( txtNamPhongHocHam ).add(txtSTK),
	ttgv_tips = $( "#tipTTGV" );
		
	// 
	function ttgv_updateTips( t ) {
		ttgv_tips
					.text( t )
					.addClass( "ui-state-highlight" );
		setTimeout(function() {
			ttgv_tips.removeClass( "ui-state-highlight", 1500 );
		}, 1000 );
	}
	
	// ttgv_checkLength
	function ttgv_checkLength( o, n, min, max) {
		if (min==0 && (o.val().length==0))
		{	
			o.addClass( "ui-state-error" );
			o.focus();	
			ttgv_updateTips( "Thông tin " + n + " không được phép để trống.");
			
			return false;
		}else if (min==max && o.val().length<min){
			o.addClass( "ui-state-error" );
			o.focus();	
			ttgv_updateTips( "Thông tin " + n + " phải đủ " + min + " ký tự.");
		}else if ( o.val().length > max || o.val().length < min ) {
			o.addClass( "ui-state-error" );
			o.focus();		
			ttgv_updateTips( "Chiều dài của " + n + " từ " +
						min + " đến " + max + " ký tự.");
			return false;
		} else {
			return true;
		}
	}
	
	// Check Regexp
	function ttgv_checkRegexp( o, regexp, n ) {
		//alert('a');
		if ( o.val()!='' && !( regexp.test( o.val() ) ) ) {
			o.addClass( "ui-state-error" );
			o.focus();
			ttgv_updateTips( n );
			return false;
		} else {
			return true;
		
		}
	}
// End of check validate

	// Post du lieu cho Thong tin giang vien
	allFieldsTTGV.change(function(e) {
		formchange =  true;
    });
	
	$("#btnLuuTTGV").click(function(e){
	
		var bValid = true;
		
		allFieldsTTGV.removeClass( "ui-state-error" );
		
		//alert(2);
		bValid = bValid && ttgv_checkLength( txtNgaySinh, "\"Ngày sinh\"", 0, 10);
		bValid = bValid && ttgv_checkLength( txtPhai, "\"Phái\"", 0, 1);
		
		bValid = bValid && ttgv_checkLength( txtDiaChi, "\"Địa chỉ\"", 0, 200);
		bValid = bValid && ttgv_checkLength( txtDienThoai, "\"Số đt CQ\"", 4, 40 );
		
		//bValid = bValid && ttgv_checkLength( txtDienThoai2, "\"Số di động\"", 4, 40 );
		bValid = bValid && ttgv_checkLength( txtEmail, "\"Email 1\"", 0, 100 );
		//bValid = bValid && ttgv_checkLength( txtEmail2, "\"Email 2\"", 6, 100 );
		
		//bValid = bValid && ttgv_checkLength( txtNamPhongHocHam, "\"Năm được phong học hàm\"", 4, 4);
		bValid = bValid && ttgv_checkRegexp( txtNamPhongHocHam,/^[0-9]{4,4}$/i, "Năm phong học hàm phải đủ 4 Số");
		
		bValid = bValid && ttgv_checkRegexp( txtSTK,/^[0-9A-Za-z]{5,30}$/i, "Số Tài khoản tối đa 30 Số");
		
		//bValid = bValid && ttgv_checkLength( txtNganh, "\"Ngành\"", 0, 8);
		
		bValid = bValid && ttgv_checkLength( txtChuyenNganh, "\"Chuyên ngành\"", 0, 200);
		
		bValid = bValid && ttgv_checkRegexp( txtEmail, /^((([a-z]|\d|[!#\$%&'\*\+\-\/=\?\^_`{\|}~]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])+(\.([a-z]|\d|[!#\$%&'\*\+\-\/=\?\^_`{\|}~]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])+)*)|((\x22)((((\x20|\x09)*(\x0d\x0a))?(\x20|\x09)+)?(([\x01-\x08\x0b\x0c\x0e-\x1f\x7f]|\x21|[\x23-\x5b]|[\x5d-\x7e]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(\\([\x01-\x09\x0b\x0c\x0d-\x7f]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF]))))*(((\x20|\x09)*(\x0d\x0a))?(\x20|\x09)+)?(\x22)))@((([a-z]|\d|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(([a-z]|\d|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])*([a-z]|\d|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])))\.)+(([a-z]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(([a-z]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])*([a-z]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])))\.?$/i,"Nhập không đúng định dạng email, vd: pgs@hcmut.edu.vn" );
		bValid = bValid && ttgv_checkRegexp( txtEmail2, /^((([a-z]|\d|[!#\$%&'\*\+\-\/=\?\^_`{\|}~]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])+(\.([a-z]|\d|[!#\$%&'\*\+\-\/=\?\^_`{\|}~]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])+)*)|((\x22)((((\x20|\x09)*(\x0d\x0a))?(\x20|\x09)+)?(([\x01-\x08\x0b\x0c\x0e-\x1f\x7f]|\x21|[\x23-\x5b]|[\x5d-\x7e]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(\\([\x01-\x09\x0b\x0c\x0d-\x7f]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF]))))*(((\x20|\x09)*(\x0d\x0a))?(\x20|\x09)+)?(\x22)))@((([a-z]|\d|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(([a-z]|\d|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])*([a-z]|\d|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])))\.)+(([a-z]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(([a-z]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])*([a-z]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])))\.?$/i, "Nhập không đúng định dạng email, vd: pgs@hcmut.edu.vn" );
		
		if ( bValid ) {
			//$('#cat').val('ttgv');
			dataString = $("#form_ttgv").serialize();
			dataString +='&cat=ttgv';
			//alert(dataString);
			$.ajax({type: "POST",url: "gv/processgv.php",data: dataString,	dataType: "json",
				success: function(data) {
							if (data.email_check === "Exist"){
								//alert(data.email_check);
								txtEmail.addClass( "ui-state-error" );
								txtEmail.focus();
								ttgv_updateTips("Email này đã có người đăng ký");
								gv_open_msg_box("Email này đã có người đăng ký","alert",250,180);
							}else
							{
								$( "#btnLuuTTGV" ).button( "disable" );
								ttgv_updateTips("Cập nhật thành công");
								gv_open_msg_box("Cập nhật thành công","info",250,180);
								formchange =  false;
							}
						 }// end function(data)	
			}); // end .ajax
		}
		//else
		e.preventDefault();
		
	});	// end $("#btnLuuTTGV")
	
	
	$('#form_ttgv').change(function() {
		//alert('Handler for .change() called.');
		$( "#btnLuuTTGV" ).button( "enable" );
	});
	
	$('input[placeholder],textarea[placeholder]').placeholder();
	
	// Upload hinh
	$( "#btn_upload_hinh_ttgv" ).button({ icons: {primary:'ui-icon ui-icon-disk'} });
	$( "#btn_upload_hinh_chu_ky_ttgv" ).button({ icons: {primary:'ui-icon ui-icon-disk'} });
	$( "#btn_upload_hinh_chu_ky_ttgv" ).hide();
	
	$( "#ttgv_processing_div" ).dialog({
			resizable: false,
			autoOpen: false,
			width:250, height:120,
			modal: true
			
	});
	
	$("#btn_upload_hinh_ttgv").click(function(e){
		getFileTTGV();
	});	// end 
	
	$("#btn_upload_hinh_chu_ky_ttgv").click(function(e){
		getFileChuKyTTGV();
	});	// end 
	
	$('#gv_frm_upload_file_chandung').on('submit', function () {
        //check if the form submission is valid, if so just let it submit
        //otherwise you could call `return false;` to stop the submission
		var input = document.getElementById("gv_file_chandung"), bvalid = true;
		
		if ($("#gv_file_chandung").val()=="")
			bvalid = false;
		else
		{
			var str = $("#gv_file_chandung").val();
			var	ext = str.substr(str.length-4,4);
			
			if (ext.toLowerCase()!=".jpg")
				bvalid = false;
		}
		
		if (!bvalid)
		{
			//$( "#btn_upload_hinhkyyeu" ).button( "disable" );
			gv_open_msg_box("<font color=red>Vui lòng chọn file định dạng .JPG (Kích thước < 1MB) bằng cách <b>click vào khung hình 4x6</b> sau đó <b>nhấn nút &quot;Open&quot;</b>.</font>", 'alert', 300, 180);
			return bvalid;
		}
		
		if (input.files[0].size>1048576)
		{
			bvalid = false;
			gv_open_msg_box("<font color=red>Vui lòng chọn file định dạng .JPG có <b>kích thước < 1MB</b>.</font>", 'alert', 300, 180);
			return bvalid;
		}
		
		$( "#ttgv_processing_div").dialog( "open" );
    });
	
	
	<?php
		// Khoi tao hinh khi load form
	if ($filehinh!=""){
		echo "var day = new Date(), id= day.getTime();
		$('#framehinh46_ttgv').attr('src', '$filehinh'+'?'+id);";	
	}
	?>

	$("#gv_file_chandung_progress").hide();
	var options = {
		beforeSend: function()
		{
			$("#gv_file_chandung_progress").show();
			//clear everything
			$("#gv_file_chandung_bar").width('0%');
			$("#gv_file_chandung_message").html("");
			$("#gv_file_chandung_percent").html("0%");
		},
		uploadProgress: function(event, position, total, percentComplete)
		{
			$("#gv_file_chandung_bar").width(percentComplete+'%');
			$("#gv_file_chandung_percent").html(percentComplete+'%');
			
		},
		success: function()
		{
			$("#gv_file_chandung_bar").width('100%');
			$("#gv_file_chandung_percent").html('100%');
			
		},
		complete: function(response)
		{
			//alert(response.responseText.search("Lỗi: "));
			if (response.responseText.search("Lỗi: ")>-1){
				$("#gv_file_chandung_message").html("<font color=red>"+response.responseText+"</font>");
				gv_open_msg_box("<font color=red>"+response.responseText+"</font>", 'info', 280, 150);
			}else{
				$("#gv_file_chandung_message").html("<font color=green><b>Tải ảnh thành công</b></font>");
				var day = new Date(), id= day.getTime();
				$("#framehinh46_ttgv").attr("src", response.responseText + '?'+id);
			}
		},
		error: function()
		{
			$("#gv_file_chandung_message").html("<font color='red'> ERROR: unable to upload files</font>");
		}
	 
	};
    $("#gv_frm_upload_file_chandung").ajaxForm(options);
});

function getFileTTGV(){
   document.getElementById("gv_file_chandung").click();
}

function userfile_ttgv_change(obj){
  var file = obj.value;
  if (file != ''){
	$("#gv_frm_upload_file_chandung").submit();
  }
}
</script>



<?php 
if (isset ($db_conn))
	oci_close($db_conn);
?>