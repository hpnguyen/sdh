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

if (!allowPermisstion(base64_decode($_SESSION['uidloginPortal']), '053', $db_conn)){
	die('Truy cập bất hợp pháp');
}

$macb = $_SESSION['macb'];


// Tham so he thong cho ĐKMH
$sqlstr="select to_char(to_date(value), 'dd/mm/yyyy') dot_hoc_f, value dot_hoc_dkmh from config where name='DOT_HOC_DKMH'";
$stmt = oci_parse($db_conn, $sqlstr);oci_execute($stmt);$n = oci_fetch_all($stmt, $resDM);oci_free_statement($stmt);
$dkmh_dot_hoc=$resDM["DOT_HOC_DKMH"][0];

$sqlstr="SELECT (hoc_ky || '/' || nam_hoc_tu || '-' || nam_hoc_den) namhoc
FROM dot_hoc_nam_hoc_ky 
WHERE dot_hoc='$dkmh_dot_hoc'";
$stmt = oci_parse($db_conn, $sqlstr);oci_execute($stmt);$n = oci_fetch_all($stmt, $resDM);oci_free_statement($stmt);
$dkmh_hknamhoc=$resDM["NAMHOC"][0];

$sqlstr="SELECT value FROM config WHERE name='DKMH_NGAY_HET_HAN'";
$stmt = oci_parse($db_conn, $sqlstr);oci_execute($stmt);$n = oci_fetch_all($stmt, $resDM);oci_free_statement($stmt);
$dkmh_ngayhethan = $resDM["VALUE"][0];

$sqlstr="SELECT value FROM config WHERE name='DKMH_NGAY_BAT_DAU'";
$stmt = oci_parse($db_conn, $sqlstr);oci_execute($stmt);$n = oci_fetch_all($stmt, $resDM);oci_free_statement($stmt);
$dkmh_ngaybatdau = $resDM["VALUE"][0];
// END Tham so he thong cho ĐKMH

// Tham so he thong cho ĐKMH theo nguyen vong
$sqlstr="select to_char(to_date(value), 'dd/mm/yyyy') dot_hoc_f, to_date(value) dot_hoc_dkmh_nv from config where name='DOT_HOC_DKMH_NV'";
$stmt = oci_parse($db_conn, $sqlstr);oci_execute($stmt);$n = oci_fetch_all($stmt, $resDM);oci_free_statement($stmt);
$dkmh_nv_dot_hoc=$resDM["DOT_HOC_DKMH_NV"][0];

$sqlstr="SELECT (hoc_ky || '/' || nam_hoc_tu || '-' || nam_hoc_den) namhoc FROM dot_hoc_nam_hoc_ky WHERE dot_hoc='$dkmh_nv_dot_hoc'";
$stmt = oci_parse($db_conn, $sqlstr);oci_execute($stmt);$n = oci_fetch_all($stmt, $resDM);oci_free_statement($stmt);
$dkmh_nv_hknamhoc=$resDM["NAMHOC"][0];

$sqlstr="SELECT value FROM config WHERE name='DKMH_NV_NGAY_HET_HAN'";
$stmt = oci_parse($db_conn, $sqlstr);oci_execute($stmt);$n = oci_fetch_all($stmt, $resDM);oci_free_statement($stmt);
$dkmh_nv_ngayhethan = $resDM["VALUE"][0];

$sqlstr="SELECT value FROM config WHERE name='DKMH_NV_NGAY_BAT_DAU'";
$stmt = oci_parse($db_conn, $sqlstr);oci_execute($stmt);$n = oci_fetch_all($stmt, $resDM);oci_free_statement($stmt);
$dkmh_nv_ngaybatdau = $resDM["VALUE"][0];
// END Tham so he thong cho ĐKMH theo nguyen vong

// Tham so dang ky on tap online
$sqlstr = "SELECT value	FROM config WHERE name='NGAY_HET_HAN_ON_DK' or name='DK_ON_TAP_THONG_BAO' order by name";
$stmt = oci_parse($db_conn, $sqlstr);oci_execute($stmt);$n = oci_fetch_all($stmt, $resDM);oci_free_statement($stmt);
$dkontap_ngayhethan = $resDM["VALUE"][1];
$dkontap_thongbao = $resDM["VALUE"][0];

$sqlstr = "SELECT name, value FROM config WHERE name = 'KHOA_TUYEN_SINH' OR name = 'DOT_TUYEN_SINH'	ORDER BY name";
$stmt = oci_parse($db_conn, $sqlstr);oci_execute($stmt);$n = oci_fetch_all($stmt, $resDM);oci_free_statement($stmt);
$dkontap_dot = $resDM["VALUE"][0]; $dkontap_nam = $resDM["VALUE"][1];
// END Tham so dang ky on tap online

// Tham so tra cuu thong tin thi sinh du thi
$sqlstr = "SELECT value	FROM config WHERE name='TRA_CUU_TT_TSDT' or name='TRA_CUU_TT_TSDT_TB' order by name";
$stmt = oci_parse($db_conn, $sqlstr);oci_execute($stmt);$n = oci_fetch_all($stmt, $resDM);oci_free_statement($stmt);
$tracuu_tsdt_onoff = $resDM["VALUE"][0];
$tracuu_tsdt_tb = $resDM["VALUE"][1];

$sqlstr = "SELECT name, value FROM config WHERE name = 'TRA_CUU_DIEM_TS' OR name = 'TRA_CUU_DIEM_TS_TB'	ORDER BY name";
$stmt = oci_parse($db_conn, $sqlstr);oci_execute($stmt);$n = oci_fetch_all($stmt, $resDM);oci_free_statement($stmt);
$tracuu_dts_onoff = $resDM["VALUE"][0];
$tracuu_dts_tb = $resDM["VALUE"][1];


$sqlstr ="select value from config where name = 'KHOA_TUYEN_SINH_WEB' or name = 'DOT_TUYEN_SINH_WEB' order by name";
$stmt = oci_parse($db_conn, $sqlstr);oci_execute($stmt);$n = oci_fetch_all($stmt, $resDM);oci_free_statement($stmt);
$tracuu_khoa_ts_web = $resDM["VALUE"][1];
$tracuu_dot_ts_web = $resDM["VALUE"][0];
// END Tham so tra cuu thong tin thi sinh du thi

// Dang ky yeu cau hoc vu online
$sqlstr = "SELECT value FROM config WHERE name = 'YCHVU_DK_CHO_PHEP'";
$stmt = oci_parse($db_conn, $sqlstr); oci_execute($stmt); oci_fetch_all($stmt, $resDM);oci_free_statement($stmt);
$chophep_dkychv_online = $resDM["VALUE"][0];
// end

// Đăng ký đề cương
$sqlstr="SELECT value FROM config WHERE name='DK_DC_DOT_HOC'";
$stmt = oci_parse($db_conn, $sqlstr);oci_execute($stmt);$n = oci_fetch_all($stmt, $resDM);oci_free_statement($stmt);
$dothoc_dkdc = $resDM["VALUE"][0];

$sqlstr="select d.hoc_ky||'/'||d.nam_hoc_tu||'-'||d.nam_hoc_den HOC_KY, d.dot_hoc from dot_hoc_nam_hoc_ky d where d.dot_hoc ='$dothoc_dkdc'";
$stmt = oci_parse($db_conn, $sqlstr);oci_execute($stmt);$n = oci_fetch_all($stmt, $resDM);oci_free_statement($stmt);
$hk_dkdc = $resDM["HOC_KY"][0];

$sqlstr="SELECT value , floor(sysdate - to_date(value,'dd/mm/yyyy')) het_han FROM config WHERE name='DK_DC_NGAY_KT'";
$stmt = oci_parse($db_conn, $sqlstr);oci_execute($stmt);oci_fetch_all($stmt, $resDM);oci_free_statement($stmt);
$ngayhethan_dkdc = $resDM["VALUE"][0]; $hethan_dkdc = $resDM["HET_HAN"][0];

$sqlstr="SELECT value , (sysdate - to_date(value,'dd/mm/yyyy')) bat_dau FROM config WHERE name='DK_DC_NGAY_BD'";
$stmt = oci_parse($db_conn, $sqlstr);oci_execute($stmt);$n = oci_fetch_all($stmt, $resDM);oci_free_statement($stmt);
$ngaybatdau_dkdc = $resDM["VALUE"][0]; $batdau_dkdc = $resDM["BAT_DAU"][0];
// END Tham so he thong cho ĐKMH theo nguyen vong
?>

<div id="phong_cauhinh_accordion">
	<h3>Đăng ký môn học</h3>
	<div class="tableData">
	<p>
		<div align="left" style="margin: 0 0 10px 10px">
			Đăng ký môn học <b>HK <?php echo $dkmh_hknamhoc; ?></b>
		</div>
		<div align="left" style="margin:0 0 5px 10px">
			Đợt học đăng ký môn học<input type=text id="phong_cauhinh_dkmh_dothoc" name="phong_cauhinh_dkmh_dothoc" style="width:95px; text-align:center"> (DD-MON-YYYY)
		</div>
		<div align="left" style="margin-left: 10px">
			Đăng ký từ <input type=text id="phong_cauhinh_dkmh_tu" name="phong_cauhinh_dkmh_tu" style="width:90px; text-align:center"> đến <input type=text id="phong_cauhinh_dkmh_den" name="phong_cauhinh_dkmh_den" style="width:90px; text-align:center">
		</div>
		<div align="left" style="margin: 10px 0 0 10px">
			<button id="phong_cauhinh_btn_dkmh_save">Save</button>
		</div>
	</p>
	</div>
	
	<h3>Đăng ký môn học theo nguyện vọng</h3>
	<div class="tableData">
	<p>
		<div align="left" style="margin: 0 0 10px 10px">
			Đăng ký môn học theo nguyện vọng <b>HK <?php echo $dkmh_hknamhoc; ?></b>
		</div>
		<div align="left" style="margin-left: 10px">
			Đăng ký từ <input type=text id="phong_cauhinh_dkmh_nguyenvong_tu" name="phong_cauhinh_dkmh_nguyenvong_tu" style="width:90px; text-align:center"> đến <input type=text id="phong_cauhinh_dkmh_nguyenvong_den" name="phong_cauhinh_dkmh_nguyenvong_den" style="width:90px; text-align:center">
		</div>
		<div align="left" style="margin: 10px 0 0 10px">
			<button id="phong_cauhinh_btn_dkmh_nguyenvong_save">Save</button>
		</div>
	</p>
	</div>
	
	<h3>Đăng ký ôn tập online</h3>
	<div class="tableData">
	<p>
		<div align="left" style="margin: 0 0 10px 10px">
			Đăng ký Ôn tập tuyển sinh online <b><?php echo "năm $dkontap_nam đợt $dkontap_dot"; ?></b>
		</div>
		<div align="left" style="margin-left: 10px">
			Ngày hết hạn đăng ký ôn tập <input type=text id="phong_cauhinh_dkontaponline_ngayhethan" name="phong_cauhinh_dkontaponline_ngayhethan" style="width:90px; text-align:center"> 
		</div>
		<div align="left" style="margin: 10px 0 0 10px">
			Thông báo khi hết hạn đăng ký ôn tập <input type=text id="phong_cauhinh_dkontaponline_thongbao" name="phong_cauhinh_dkontaponline_thongbao" style="width:600px; text-align:left"> 
		</div>
		<div align="left" style="margin: 10px 0 0 10px">
			<button id="phong_cauhinh_btn_dkontaponline_save">Save</button>
		</div>
	</p>
	</div>
	
	<h3>Đăng ký yêu cầu học vụ online</h3>
	<div class="tableData">
	<p>
		<div align="left" style="margin-left: 10px">
			<table>
				<tr><td>Đăng ký yêu cầu học vụ online</td><td><select id="phong_cauhinh_dangky_ychv_online_onoff"><option value="1">On</option><option value="0">Off</option></select></td><td>Thông báo khi off</td><td><input type=text id="phong_cauhinh_dangky_ychv_online_tb" name="phong_cauhinh_dangky_ychv_online_tb" style="width:500px; text-align:left"></td></tr>
			</table>
		</div>
		<div align="left" style="margin: 10px 0 0 10px">
			<button id="phong_cauhinh_btn_dkychvonline_save">Save</button>
		</div>
	</p>
	</div>
	
	<h3>Đăng ký đề cương</h3>
	<div class="tableData">
	<p>
		<div align="left" style="margin: 0 0 10px 10px">
			Đăng ký đề cương <b>HK <?php echo $hk_dkdc; ?></b>
		</div>
		<div align="left" style="margin:0 0 5px 10px">
			Đợt học đăng ký đề cương <input type=text id="phong_cauhinh_dkdc_dothoc" name="phong_cauhinh_dkdc_dothoc" style="width:95px; text-align:center"> (DD-MON-YYYY)
		</div>
		<div align="left" style="margin-left: 10px">
			Đăng ký từ <input type=text id="phong_cauhinh_dkdc_tu" name="phong_cauhinh_dkdc_tu" style="width:90px; text-align:center"> đến <input type=text id="phong_cauhinh_dkdc_den" name="phong_cauhinh_dkdc_den" style="width:90px; text-align:center">
		</div>
		<div align="left" style="margin: 10px 0 0 10px">
			<button id="phong_cauhinh_btn_dkdc_save">Save</button>
		</div>
	</p>
	</div>
	
	<h3>Tra cứu thông tin</h3>
	<div class="tableData">
	<p>
		<div align="left" style="margin:0 0 10px 10px">
			<b>Khoá TS Web</b> <input type=text id="phong_cauhinh_tracuu_khoa_ts_web" name="phong_cauhinh_tracuu_khoa_ts_web" style="width:40px; text-align:center"> <b>Đợt TS Web</b> <input type=text id="phong_cauhinh_tracuu_dot_ts_web" name="phong_cauhinh_tracuu_dot_ts_web" style="width:20px; text-align:center">
		</div>
		<div align="left" style="margin-left: 10px">
			<table>
				<tr><td>Tra cứu thông tin thí sinh dự thi</td><td><select id="phong_cauhinh_tracuu_tsdt_onoff"><option value="1">On</option><option value="0">Off</option></select></td><td>Thông báo khi off</td><td><input type=text id="phong_cauhinh_tracuu_tsdt_tb" name="phong_cauhinh_tracuu_tsdt_tb" style="width:500px; text-align:left"></td></tr>
				<tr><td>Tra cứu điểm tuyển sinh</td><td><select id="phong_cauhinh_tracuu_dts_onoff"><option value="1">On</option><option value="0">Off</option></select></td><td>Thông báo khi off</td><td><input type=text id="phong_cauhinh_tracuu_diemts_tb" name="phong_cauhinh_tracuu_diemts_tb" style="width:500px; text-align:left"></td></tr>
				<tr><td>Tra cứu điểm phúc tra</td><td><select id="phong_cauhinh_tracuu_dpt_onoff"><option value="1">On</option><option value="0">Off</option></select></td><td>Thông báo khi off</td><td><input type=text id="phong_cauhinh_tracuu_diempt_tb" name="phong_cauhinh_tracuu_diempt_tb" style="width:500px; text-align:left"></td></tr>
				<tr><td>Tra cứu nguyện vọng 2</td><td><select id="phong_cauhinh_tracuu_nv2_onoff"><option value="1">On</option><option value="0">Off</option></select></td><td>Thông báo khi off</td><td><input type=text id="phong_cauhinh_tracuu_nv2_tb" name="phong_cauhinh_tracuu_nv2_tb" style="width:500px; text-align:left"></td></tr>
			</table>
		</div>
	</p>
	</div>
	
</div>

<script type="text/javascript">
var phong_cauhinh_linkdata = "phong/phong_cauhinhhethong_process.php?hisid=<?php echo $_REQUEST["hisid"]; ?>";
 
$(function(){
	//$("#phong_cauhinh_btn_dkmh_save" ).button({ icons: {primary:'ui-icon ui-icon-disk'} });
	
	$( "#phong_cauhinh_accordion" ).accordion({
		heightStyle: "content"
	});
	
	$( "#phong_cauhinh_dkmh_tu, #phong_cauhinh_dkmh_den," 
		+ "#phong_cauhinh_dkmh_nguyenvong_tu, #phong_cauhinh_dkmh_nguyenvong_den, "
		+ "#phong_cauhinh_dkdc_tu, #phong_cauhinh_dkdc_den, "
		+ "#phong_cauhinh_dkontaponline_ngayhethan" ).datepicker({
		dateFormat: "dd/mm/yy"
	});
	
	$( "#phong_cauhinh_dkmh_tu, #phong_cauhinh_dkmh_den, #phong_cauhinh_dkmh_nguyenvong_tu, #phong_cauhinh_dkmh_nguyenvong_den, #phong_cauhinh_dkdc_tu, #phong_cauhinh_dkdc_den" ).mask("99/99/9999");
	$( "#phong_cauhinh_dkmh_dothoc, #phong_cauhinh_dkdc_dothoc" ).mask("99-aaa-9999");
	
	// Khoi tao du lieu
	$( "#phong_cauhinh_dkmh_dothoc" ).val('<?php echo $dkmh_dot_hoc;?>');
	$( "#phong_cauhinh_dkmh_tu" ).val('<?php echo $dkmh_ngaybatdau;?>');
	$( "#phong_cauhinh_dkmh_den" ).val('<?php echo $dkmh_ngayhethan;?>');
	
	$( "#phong_cauhinh_dkmh_nguyenvong_tu" ).val('<?php echo $dkmh_nv_ngaybatdau;?>');
	$( "#phong_cauhinh_dkmh_nguyenvong_den" ).val('<?php echo $dkmh_nv_ngayhethan;?>');
	
	$( "#phong_cauhinh_dkontaponline_ngayhethan" ).val('<?php echo $dkontap_ngayhethan;?>');
	$( "#phong_cauhinh_dkontaponline_thongbao" ).val('<?php echo $dkontap_thongbao;//str_replace ("'", "\'", $dkontap_thongbao);?>');
	
	$( "#phong_cauhinh_tracuu_khoa_ts_web" ).val('<?php echo $tracuu_khoa_ts_web;?>');
	$( "#phong_cauhinh_tracuu_dot_ts_web" ).val('<?php echo $tracuu_dot_ts_web;?>');
	
	$( "#phong_cauhinh_dangky_ychv_online_onoff" ).val('<?php echo $chophep_dkychv_online;?>');
	
	$( "#phong_cauhinh_dkdc_dothoc" ).val('<?php echo $dothoc_dkdc;?>');
	$( "#phong_cauhinh_dkdc_tu" ).val('<?php echo $ngaybatdau_dkdc;?>');
	$( "#phong_cauhinh_dkdc_den" ).val('<?php echo $ngayhethan_dkdc;?>');
	// end
	
	$("#phong_cauhinh_btn_dkmh_save").click(function(){
		phong_cauhinh_save_dkmh();
	});
	
	$("#phong_cauhinh_btn_dkmh_nguyenvong_save").click(function(){
		phong_cauhinh_save_dkmh_nv();
	});
	
	$("#phong_cauhinh_btn_dkontaponline_save").click(function(){
		phong_cauhinh_save_dkontaponline();
	});
	
	$("#phong_cauhinh_btn_dkychvonline_save").click(function(){
		phong_cauhinh_save_dkychv();
	});
	
	$("#phong_cauhinh_btn_dkdc_save").click(function(){
		phong_cauhinh_save_dkdc();
	});
	
	
});

function phong_cauhinh_checksession(){
	dataString = 'a=checksession';
	return xreq = $.ajax({
	  type: 'POST', dataType: "json", data: dataString,
	  url: phong_cauhinh_linkdata,
	  success: function(data) {
		return jQuery.parseJSON(data);
	  }
	});
}

function phong_cauhinh_save_dkmh(){
	gv_processing_diglog("open", "Đang xử lý ... vui lòng chờ");
	phong_cauhinh_checksession().done(function(data){
		if (data.success != 1){
			gv_processing_diglog("close", "...");
			gv_open_msg_box("<font style='color:red;'>Không thể save vì:</font> <br/><div style='margin: 5px 0 0 5px'>" + reverse_escapeJsonString(data.msg) +'</div>', 'alert', 250, 180, true);
			return;
		}else{
			dataString = 'a=saveDKMHconf&from='+ $("#phong_cauhinh_dkmh_tu").val() + '&to=' + $("#phong_cauhinh_dkmh_den").val() +'&dothoc='+$("#phong_cauhinh_dkmh_dothoc").val();
			xreq = $.ajax({
			  type: 'POST', dataType: "json",
			  url: phong_cauhinh_linkdata,
			  data: dataString,
			  success: function(data) {
				if (data.success==-1){
					gv_processing_diglog("close");
					gv_open_msg_box("Save uncompleted. Error: " + reverse_escapeJsonString(data.msg), 'info', 250, 180);
				}
				else{
					gv_processing_diglog("close");
					gv_open_msg_box("Save completed", 'info', 250, 180);
				}
			  }
			});
		}
	});
}

function phong_cauhinh_save_dkmh_nv(){
	gv_processing_diglog("open", "Đang xử lý ... vui lòng chờ");
	phong_cauhinh_checksession().done(function(data){
		if (data.success != 1){
			gv_processing_diglog("close", "...");
			gv_open_msg_box("<font style='color:red;'>Không thể save vì:</font> <br/><div style='margin: 5px 0 0 5px'>" + reverse_escapeJsonString(data.msg) +'</div>', 'alert', 250, 180, true);
			return;
		}else{
			dataString = 'a=saveDKMHNVconf&from='+ $("#phong_cauhinh_dkmh_nguyenvong_tu").val() + '&to=' + $("#phong_cauhinh_dkmh_nguyenvong_den").val();
			xreq = $.ajax({
			  type: 'POST', dataType: "json",
			  url: phong_cauhinh_linkdata,
			  data: dataString,
			  success: function(data) {
				if (data.success==-1){
					gv_processing_diglog("close");
					gv_open_msg_box("Save uncompleted. Error: " + reverse_escapeJsonString(data.msg), 'info', 250, 180);
				}
				else{
					gv_processing_diglog("close");
					gv_open_msg_box("Save completed", 'info', 250, 180);
				}
			  }
			});
		}
	});
}

function phong_cauhinh_save_dkontaponline(){
	gv_processing_diglog("open", "Đang xử lý ... vui lòng chờ");
	phong_cauhinh_checksession().done(function(data){
		if (data.success != 1){
			gv_processing_diglog("close", "...");
			gv_open_msg_box("<font style='color:red;'>Không thể save vì:</font> <br/><div style='margin: 5px 0 0 5px'>" + reverse_escapeJsonString(data.msg) +'</div>', 'alert', 250, 180, true);
			return;
		}else{
			dataString = 'a=saveDKONTAPconf&to='+ $("#phong_cauhinh_dkontaponline_ngayhethan").val() + '&tb=' + $("#phong_cauhinh_dkontaponline_thongbao").val();
			xreq = $.ajax({
			  type: 'POST', dataType: "json",
			  url: phong_cauhinh_linkdata,
			  data: dataString,
			  success: function(data) {
				if (data.success==-1){
					gv_processing_diglog("close");
					gv_open_msg_box("Save uncompleted. Error: " + reverse_escapeJsonString(data.msg), 'info', 250, 180);
				}
				else{
					gv_processing_diglog("close");
					gv_open_msg_box("Save completed", 'info', 250, 180);
				}
			  }
			});
		}
	});
}

function phong_cauhinh_save_dkychv(){
	gv_processing_diglog("open", "Đang xử lý ... vui lòng chờ");
	phong_cauhinh_checksession().done(function(data){
		if (data.success != 1){
			gv_processing_diglog("close", "...");
			gv_open_msg_box("<font style='color:red;'>Không thể save vì:</font> <br/><div style='margin: 5px 0 0 5px'>" + reverse_escapeJsonString(data.msg) +'</div>', 'alert', 250, 180, true);
			return;
		}else{
			dataString = 'a=saveDKYCHVconf&value1='+ $("#phong_cauhinh_dangky_ychv_online_onoff").val();
			xreq = $.ajax({
			  type: 'POST', dataType: "json",
			  url: phong_cauhinh_linkdata,
			  data: dataString,
			  success: function(data) {
				if (data.success==-1){
					gv_processing_diglog("close");
					gv_open_msg_box("Save uncompleted. Error: " + reverse_escapeJsonString(data.msg), 'info', 250, 180);
				}
				else{
					gv_processing_diglog("close");
					gv_open_msg_box("Save completed", 'info', 250, 180);
				}
			  }
			});
		}
	});
}

function phong_cauhinh_save_dkdc(){
	gv_processing_diglog("open", "Đang xử lý ... vui lòng chờ");
	phong_cauhinh_checksession().done(function(data){
		if (data.success != 1){
			gv_processing_diglog("close", "...");
			gv_open_msg_box("<font style='color:red;'>Không thể save vì:</font> <br/><div style='margin: 5px 0 0 5px'>" + reverse_escapeJsonString(data.msg) +'</div>', 'alert', 250, 180, true);
			return;
		}else{
			dataString = 'a=saveDKDCconf&from='+ $("#phong_cauhinh_dkdc_tu").val() + '&to=' + $("#phong_cauhinh_dkdc_den").val()+'&dothoc='+$("#phong_cauhinh_dkdc_dothoc").val();
			xreq = $.ajax({
			  type: 'POST', dataType: "json",
			  url: phong_cauhinh_linkdata,
			  data: dataString,
			  success: function(data) {
				if (data.success==-1){
					gv_processing_diglog("close");
					gv_open_msg_box("Save uncompleted. Error: " + reverse_escapeJsonString(data.msg), 'info', 250, 180);
				}
				else{
					gv_processing_diglog("close");
					gv_open_msg_box("Save completed", 'info', 250, 180);
				}
			  }
			});
		}
	});
}

</script>



<?php 
if (isset ($db_conn))
	oci_close($db_conn);
?>