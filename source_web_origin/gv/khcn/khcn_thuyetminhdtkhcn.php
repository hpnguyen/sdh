<?php
if (isset($_REQUEST["hisid"])){
	session_id($_REQUEST["hisid"]);
	session_start();
}
if (!isset($_SESSION['uidloginPortal'])){
	die('Đã hết phiên làm việc'); 
}

include "../libs/connectnckhda.php";
include "../libs/connect.php";
include "../libs/pgslibs.php";
if (!allowPermisstion(base64_decode($_SESSION['uidloginPortal']), '051', $db_conn))
{
	die('Truy cập bất hợp pháp'); 
}
$usr = base64_decode($_SESSION['uidloginPortal']);
$makhoa = base64_decode($_SESSION['makhoa']);
$macb = $_SESSION["macb"];

$sqlstr="select id, ho || ' ' || ten ho_ten from nhan_su where upper(username)=upper('$usr')"; 
$stmt = oci_parse($db_conn, $sqlstr);oci_execute($stmt);$n=oci_fetch_all($stmt, $resDM);oci_free_statement($stmt);

$id = $resDM["ID"][0];
$hoten = $resDM["HO_TEN"][0];

$sqlstr="select SHCC from can_bo_giang_day where ma_can_bo='$macb'";
$stmt = oci_parse($db_conn, $sqlstr);oci_execute($stmt);$n=oci_fetch_all($stmt, $resDM);oci_free_statement($stmt);
$shcc = $resDM["SHCC"][0];

// Kiểm tra hạn đăng ký đề tài
$today = date("d/m/Y");

$sqlstr="SELECT value , (sysdate - to_date(value,'HH24:MI dd/mm/yyyy')) het_han FROM config WHERE name='DKDT_NGAY_KT'";
$stmt = oci_parse($db_conn_khcn, $sqlstr);oci_execute($stmt);$n = oci_fetch_all($stmt, $resDM);oci_free_statement($stmt);
$ngayhethanDKDT = $resDM["VALUE"][0]; $hethan = $resDM["HET_HAN"][0];

$sqlstr="SELECT value , (sysdate - to_date(value,'dd/mm/yyyy')) bat_dau FROM config WHERE name='DKDT_NGAY_BD'";
$stmt = oci_parse($db_conn_khcn, $sqlstr);oci_execute($stmt);$n = oci_fetch_all($stmt, $resDM);oci_free_statement($stmt);
$ngaybatdauDKDT = $resDM["VALUE"][0]; $batdau = $resDM["BAT_DAU"][0];

$sqlstr="SELECT value dkdt_nam FROM config WHERE name='DKDT_NAM'";
$stmt = oci_parse($db_conn_khcn, $sqlstr);oci_execute($stmt);$n = oci_fetch_all($stmt, $resDM);oci_free_statement($stmt);
$namDKDT = $resDM["DKDT_NAM"][0];
?>
<div align=center style='font-size:14px; font-weight:bold'>ĐĂNG KÝ ĐỀ TÀI <?php echo $namDKDT; ?></div>
<?php
$sqlstr="SELECT TEN_CAP, (sysdate - DKDT_NGAY_BD) bat_dau, (sysdate-DKDT_NGAY_KT) het_han, 
to_char(DKDT_NGAY_BD, 'dd/mm/yyyy') ngay_bd_f, to_char(DKDT_NGAY_KT, 'HH24:MI dd/mm/yyyy') ngay_kt_f 
FROM CAP_DE_TAI WHERE DKDT_NGAY_KT is not null order by STT";
$stmt = oci_parse($db_conn_khcn, $sqlstr);oci_execute($stmt);$n = oci_fetch_all($stmt, $resDM);oci_free_statement($stmt);

for ($i=0 ; $i < $n; $i++)
{
	$batdau = $resDM["BAT_DAU"][$i];
	$hethan = $resDM["HET_HAN"][$i];
	$tencap = $resDM["TEN_CAP"][$i];
	$ngaybd = $resDM["NGAY_BD_F"][$i];
	$ngaykt = $resDM["NGAY_KT_F"][$i];
	if ($batdau<0){
		echo "<div align=center style='margin:5px 0 5px 0;font-size:12px'>Đề tài cấp <b>$tencap</b> chưa đến ngày đăng ký, theo kế hoạch từ <font color=red><b>$ngaybd</b> - <b>$ngaykt</b></font></div>";
	}else if ($hethan>0){
		echo "<div align=center style='margin:5px 0 5px 0;font-size:12px'>Đề tài cấp <b>$tencap</b> đã hết hạn đăng ký lúc <font color=red><b>$ngaykt</b></font></div>";
	}else{
		echo "<div style='margin:5px 0 5px 0; font-size:12px' align=center>Đề tài cấp <b>$tencap</b> đăng ký từ <font color=red><b>$ngaybd</b></font> đến <font color=red><b>$ngaykt</b></font></div>";
	}
}

?>

<div id="khcn_thuyetminhdtkhcn" style="width:100%;">
	<div style='margin:0 0 10px 0;'>
		<table width="100%" height="20" border="0" align="center" cellpadding="5" cellspacing="0" class="ui-widget ui-widget-content ui-corner-all ">
			<tr>
				<td style="width:50%" align=left><button id='khcn_reg_button' title="Bấm vào đây để đăng ký thuyết minh đề tài mới" class='khcn_tooltips'>Đăng ký Đề tài</button> <button id='khcn_edit_ttchung_button' title="Bấm vào đây để cập nhật phần thông tin chung" class='khcn_tooltips'>A. Cập nhật Thông tin chung</button> <button id='khcn_edit_mota_button' title="Bấm vào đây để cập nhật phần mô tả nghiên cứu" class='khcn_tooltips'>B. Cập nhật Mô tả nghiên cứu</button></td>
				<td style="width:50%" align=right>
					<table cellpadding="3">
						<tr style='cursor:pointer;' >
							<td><img src='icons/idea-icon-24x24.png' border=0 width=24 height=24 /></td><td ><a class="fancybox-effects-d" href="./images/huongdan/tmdt/huongdan_dangkytmdt.png" title="Hướng dẫn đăng ký đề tài" style='font-weight:bold;color:#0195df'>Hướng dẫn đăng ký đề tài</a></td>
							<td><img src='icons/idea-icon-24x24.png' border=0 width=24 height=24 /></td><td ><a class='khcn_tooltips' onclick="$('#khcn_printHuongDanInTMDT').click();" data-toggle='tooltip' title='<b><u>Chú ý:</u> Đọc hướng dẫn này trước khi in thuyết minh đề tài</b>' style='font-weight:bold;color:#0195df'>Hướng dẫn In thuyết minh đề tài</a></td>
							
						</tr>
					</table>
				</td>
			</tr>
		</table>
	</div>
	<div style="display: block; margin: 10px 0 0 0; background: white; border-radius: 10px; padding: 5px;">
		<table id=khcn_ds_thuyetminhdtkhcn width="100%"  border="0" align="center" cellpadding="5" cellspacing="0" class="ui-widget ui-widget-content ui-corner-top tableData display" style='font-size:13px;'>
			<thead>
				<tr class="ui-widget-header heading" >
					<th style="width: 40px" align=left>Mã</th>
					<th align=left>Tên đề tài</th>
					<th style="" align=left>Ngành/Nhóm ngành</th>
					<th style="" align=left>Hướng<br/>đề tài</th>
					<th style="" align=left>Keywords</th>
					<th style="" align=left>Cấp đề tài</th>
					<th style="" align=left>Đơn vị đăng ký</th>
					<th style="" align=left>Loại hình<br/>nghiên cứu</th>
					<th style="" align=right>Thời gian (tháng)</th>
					<th style="" align=right>Kinh phí</th>
					<th style="width: 10px" align=right></th>
					<th align=left>Trạng thái</th>
					<th style="width: 10px" align=left>Xoá</th>
					<th style="width: 20px" align=right>In</th>
				</tr>
			</thead>			
		</table>
	</div>
	
	<div class="clearfloat"></div>
</div> <!-- end  -->

<style>
	.khcn_thuyetminh_error {color: red;}
</style>

<div id=khcn_diag_reg_dtkhcn style='width:650px;' title="Đăng ký Thuyết minh Đề tài KH&CN">
<form id=khcn_frm_reg_dtkhcn name=khcn_frm_reg_dtkhcn >
	<div style='margin: 5px 0 5px 0;' class=heading>Đề tài cấp <span id="khcn_frm_reg_dtkhcn_tencapdetai" name="khcn_frm_reg_dtkhcn_tencapdetai" style="font-weight:bold; color:red"></span></div>
	<input type="hidden" id="khcn_frm_reg_dtkhcn_capdetai" name="khcn_frm_reg_dtkhcn_capdetai">
	<div style='margin: 5px 0 5px 0;' class=heading>Tên đề tài</div>
	<div style='margin: 5px 0 5px 0;'><input type=text id=khcn_frm_reg_dtkhcn_ten_dt_viet name=khcn_frm_reg_dtkhcn_ten_dt_viet placeholder='Tên tiếng Việt' title='Tên tiếng Việt' style='width:97%;height:18px' class='khcn_tooltips'/> <font color=red>*</font></div>
	<div style='margin: 5px 0 5px 0;'><input type=text id=khcn_frm_reg_dtkhcn_ten_dt_anh name=khcn_frm_reg_dtkhcn_ten_dt_anh placeholder='Tên tiếng Anh' title='Tên tiếng Anh' style='width:97%;height:18px' class='khcn_tooltips'/> <font color=red>*</font></div>
	<div style='margin: 10px 0 5px 0;'><input type=text id=khcn_frm_reg_dtkhcn_keywords name=khcn_frm_reg_dtkhcn_keywords placeholder='Keywords (dùng cho tìm kiếm)' title='Keywords (dùng cho tìm kiếm)' style='width:97%;height:18px' class='khcn_tooltips'/> <font color=red>*</font></div>
	<div style='margin: 5px 0 5px 0;'><input type=text id=khcn_frm_reg_dtkhcn_huongdt name=khcn_frm_reg_dtkhcn_huongdt placeholder='Hướng đề tài' title='<b>Hướng đề tài</b>; Hướng nghiên cứu về biến đổi khí hậu; nghiên cứu về nông nghiệp, nông dân và nông thôn; nghiên cứu về công nghệ sinh học (Công nghệ gen, CN tế bào, CN protein và enzime, CN vi sinh, …); nghiên cứu sử dụng hiệu quả năng lượng;…' style='width:97%;height:18px' class='khcn_tooltips'/> <font color=red>*</font></div>
	
	<div style='margin: 5px 0 5px 0;' class=heading>Đăng ký đề tài cho đơn vị</div>
	<div style='margin: 5px 0 5px 0;'>
		<select type=text id=khcn_frm_reg_dtkhcn_dvdk name=khcn_frm_reg_dtkhcn_dvdk style='font-size:13px; width:98%;' class='khcn_tooltips'>
			<option value="">-chọn đơn vị-</option>
				<?php 
					$sqlstr="select MA_BO_MON, TEN_BO_MON from BO_MON order by TEN_BO_MON"; 
					$stmt = oci_parse($db_conn_khcn, $sqlstr);oci_execute($stmt);$n=oci_fetch_all($stmt, $resDM);oci_free_statement($stmt);
					for ($i=0 ; $i < $n; $i++){
						echo "<option value='{$resDM["MA_BO_MON"][$i]}'>{$resDM["TEN_BO_MON"][$i]}</option>";
					}
				?>
		</select>
		<font color=red>*</font>
	</div>
	
	<div style='margin: 5px 0 5px 0;' class=heading><span id=khcn_frm_reg_dtkhcn_lbl_nganh>Thuộc ngành/nhóm ngành</span> <font color=red>*</font></div>
	<div>
		<table id=khcn_frm_reg_table_nganh name=khcn_frm_reg_table_nganh style='line-height:20px; width:100%'>
			<tr>
				<?php 
					$sqlstr="select ma_nhom_nganh, ten_nhom_nganh from nckh_nhom_nganh"; 
					$stmt = oci_parse($db_conn_khcn, $sqlstr);oci_execute($stmt);$numNganh=oci_fetch_all($stmt, $resDM);oci_free_statement($stmt);
					$j = ceil($numNganh/3); 
					$count=0;
					
					for ($i=0 ; $i < $numNganh; $i++)
					{
						if ($count==0)
							echo "<td valign=top>";
						echo "<input type=checkbox id=khcn_frm_reg_nganh$i name=khcn_frm_reg_nganh$i value='{$resDM["MA_NHOM_NGANH"][$i]}' />";
						if ($resDM["MA_NHOM_NGANH"][$i]=='999')
							echo " <input type=text id=khcn_frm_reg_nganhkhac name=khcn_frm_reg_nganhkhac placeholder='Khác...' title='Ngành/nhóm ngành khác' class='khcn_tooltips' style='height:18px; width:85%;'/>";
						else
							echo "<label for=khcn_frm_reg_nganh$i> {$resDM["TEN_NHOM_NGANH"][$i]}</label> <br/>";
						if ($count==($j-1))
						{
							echo "</td>";
							$count = 0;
						}
						else
							$count += 1;
					}
					if ($count*3<$numNganh && $count != 0)
						echo "</td>";
					
				?>
			</tr>
		</table>
	</div>
	
	<div style='margin: 5px 0 0 0;'>
		<table style='line-height:20px; width:100%'>
			<tr>
				<td colspan=4 align=left class=heading>Chuyên ngành hẹp</td>
			</tr>
			<tr>
				<td colspan=4>
					<input type=text id=khcn_frm_reg_dtkhcn_cnganhhep name=khcn_frm_reg_dtkhcn_cnganhhep maxLength=250 placeholder='Chuyên ngành hẹp' title="Chuyên ngành hẹp" style='width:97%;height:18px' class='khcn_tooltips'/> <font color=red>*</font>
				</td>
			</tr>
			<tr>
				<td colspan=2 style='width:50%' align=left class=heading>Loại hình nghiên cứu</td>
				<td colspan=2 style='width:50%' align=left class=heading>Thời gian thực hiện (tháng, kể từ khi được duyệt)</td>						
			</tr>
			<tr>
				<td colspan=2 style='width:50%'>
					<select id=khcn_frm_reg_dtkhcn_loaihinhnc name=khcn_frm_reg_dtkhcn_loaihinhnc style='font-size:13px; width:95%' title='Loại hình nghiên cứu' class='khcn_tooltips'>
						<option value="">-Chọn loại hình nghiên cứu-</option>
						<?php 
							$sqlstr="select MA_LOAI_HINH_NC, TEN_LOAI_HINH_NC from NCKH_LOAI_HINH_NC"; 
							$stmt = oci_parse($db_conn_khcn, $sqlstr);oci_execute($stmt);$n=oci_fetch_all($stmt, $resDM);oci_free_statement($stmt);
							for ($i=0 ; $i < $n; $i++)
							{
								echo "<option value='{$resDM["MA_LOAI_HINH_NC"][$i]}'>{$resDM["TEN_LOAI_HINH_NC"][$i]}</option>";
							}
						?>
					</select> <font color=red>*</font>
				</td>
				<td style='width:50%' colspan=2><input type=text id=khcn_frm_reg_dtkhcn_thoigianthuchien name=khcn_frm_reg_dtkhcn_thoigianthuchien  data-v-min=0 data-v-max=999 maxLength=3 placeholder='Thời gian thực hiện (tháng)' title="Thời gian thực hiện (tháng, kể từ khi được duyệt)" style='width:94%;height:18px' class='khcn_tooltips khcn_autonumbers'/> <font color=red>*</font></td>						
			</tr>
			
		</table>
	</div>
	
</form>
	<div id=khcn_frm_reg_dtkhcn_qd193 style="margin-top: 10px" align=center><input type=checkbox id=khcn_frm_reg_doc_qd193 /> <b>Đã đọc và đồng ý với <a href="./khcn/templ/quyet_dinh_193.pdf" target=_blank style="color: green">Quyết Định 193</a></b></div>
	<div align="center" id="khcn_reg_tips" style="margin-top: 10px" class="validateTips"></div>
</div>

<div id=khcn_diag_nhanlucnghiencuu style='width:100%;' title="Thêm nhân lực nghiên cứu">
	<form id=khcn_frm_reg_nhanlucnghiencuu name=khcn_frm_reg_nhanlucnghiencuu>
		<input type=hidden id='khcn_frm_reg_nhanlucnghiencuu_manl' name='khcn_frm_reg_nhanlucnghiencuu_manl'>
		<input type=hidden id='khcn_frm_reg_nhanlucnghiencuu_index' name='khcn_frm_reg_nhanlucnghiencuu_index'>
		<div id="khcn_div_frm_reg_nhanlucnghiencuu_huongdan" name="khcn_div_frm_reg_nhanlucnghiencuu_huongdan" style='margin:0 0 5px 0;width:100%; color: #115599; font-size: 12px' align="justify">
			Hổ trợ <b>tìm kiếm</b> thành viên chủ chốt <b>chính xác</b> bằng cách <b>nhập</b> họ và tên <b>vào ô</b> "<b>Học hàm, học vị, Họ và tên</b>", sau đó <b>click chọn thành viên được list ra bên dưới</b>.
		</div>
		<div style='margin:0 0 5px 0;width:100%;'>
			<div style='margin:0 0 5px 0;'><b>Nhân lực nghiên cứu</b></div>
			<select id='khcn_frm_reg_nhanlucnghiencuu_loai' name='khcn_frm_reg_nhanlucnghiencuu_loai' style='width:96%;font-size:14px' title='Loại nhân lực tham gia nghiên cứu'>
				<option value=''>-chọn nhân lực nghiên cứu-</option>
				<option value=1>Thành viên chủ chốt</option>
				<option value=2>Nghiên cứu sinh, học viên cao học, sinh viên</option>
			</select> <font color=red>*</font>
		</div>
		
		<div id="khcn_frm_reg_nhanlucnghiencuu_div_masv" style='margin:0 0 5px 0;width:95%;display: none'>
			<div style='margin:0 0 5px 0;'><b>Mã số SV</b></div>
			<input type=text id='khcn_frm_reg_nhanlucnghiencuu_masv' name='khcn_frm_reg_nhanlucnghiencuu_masv' maxlength=10 style='width:150px;' placeholder='Mã số'  title='Mã NCS, Học viên cao học, Sinh viên' class='khcn_tooltips'> <font color=red>*</font>
		</div>
		<div style='margin:0 0 5px 0;width:100%;' id="khcn_div_frm_reg_nhanlucnghiencuu_hh_hv_ho_ten" name="khcn_div_frm_reg_nhanlucnghiencuu_hh_hv_ho_ten">
			<div style='margin:0 0 5px 0;'><b>Họ và tên</b></div>
			<input type=text id='khcn_frm_reg_nhanlucnghiencuu_hh_hv_ho_ten' name='khcn_frm_reg_nhanlucnghiencuu_hh_hv_ho_ten' maxlength=100 style='width:95%;' placeholder='Học hàm, học vị, Họ và tên'  title='Học hàm, học vị, Họ và tên' class='khcn_tooltips'> <font color=red>*</font>
		</div>
		<div id=khcn_frm_reg_nhanlucnghiencuu_div_shcc style='margin:0 0 5px 0;width:100%;display: none'>
			<div style='margin:0 0 5px 0;'><b>SHCC</b></div>
			<input type=text id='khcn_frm_reg_nhanlucnghiencuu_shcc' name='khcn_frm_reg_nhanlucnghiencuu_shcc' maxlength=10 style='width:80px;' placeholder='SHCC'  title='Số hiệu công chức' class='khcn_tooltips'>
			<input type=hidden id='khcn_frm_reg_nhanlucnghiencuu_fk_ma_can_bo' name='khcn_frm_reg_nhanlucnghiencuu_fk_ma_can_bo'>
		</div>
		
		<div style='margin:0 0 5px 0;width:100%;' id="khcn_div_frm_reg_nhanlucnghiencuu_ho_ten_sv" name="khcn_div_frm_reg_nhanlucnghiencuu_ho_ten_sv">
			<div style='margin:0 0 5px 0;'><b>Họ và tên</b></div>
			<input type=text id='khcn_frm_reg_nhanlucnghiencuu_ho_ten_sv' name='khcn_frm_reg_nhanlucnghiencuu_ho_ten_sv' maxlength=100 style='width:95%;' placeholder='Họ và tên NCS, Học viên, Sinh viên'  title='Họ và tên NCS, Học viên, Sinh viên' class='khcn_tooltips'> <font color=red>*</font>
		</div>
		
		<div style='margin:0 0 5px 0;width:100%;'>
			<div style='margin:0 0 5px 0;'><b>Đơn vị công tác</b></div>
			<input type=text id='khcn_frm_reg_nhanlucnghiencuu_don_vi_cong_tac' name='khcn_frm_reg_nhanlucnghiencuu_don_vi_cong_tac' maxlength=200 style='width:95%;' placeholder='Đơn vị công tác'  title='Đơn vị công tác' class='khcn_tooltips'> <font color=red>*</font>
		</div>
		<div style='margin:0 0 5px 0;width:100%;'>
			<div style='margin:0 0 5px 0;'><b>Số tháng quy đổi</b></div>
			<input type=text id='khcn_frm_reg_nhanlucnghiencuu_so_thang_lv_quy_doi' name='khcn_frm_reg_nhanlucnghiencuu_so_thang_lv_quy_doi' data-v-min=0 data-v-max=999  maxlength=5 style='width:95%;' placeholder='Số tháng làm việc quy đổi'  title='Số tháng làm việc quy đổi' class='khcn_tooltips khcn_autonumbers'> <font color=red>*</font>
		</div>
	</form>
	<div style='margin-top:10px' align="center" id="khcn_a9_tips" class="validateTips"></div>
</div>

<div id=khcn_diag_anphamkhoahoc style='width:100%;' title="Thêm ấn phẩm khoa học">
<form id=khcn_frm_reg_anphamkhoahoc name=khcn_frm_reg_anphamkhoahoc>
	<div style='margin:0 0 5px 0;width:100%;'>
		<select id='khcn_frm_reg_anphamkhoahoc_loai' name='khcn_frm_reg_anphamkhoahoc_loai' style='width:100.5%;font-size:12px' title='Loại ấn phẩm khoa học'>
			<option value=''>-chọn ấn phẩm khoa học-</option>
			<?php 
				$sqlstr="select MA_AN_PHAM_KH, TEN_AN_PHAM_KH from NCKH_DM_AN_PHAM_KH"; 
				$stmt = oci_parse($db_conn_khcn, $sqlstr);oci_execute($stmt);$num=oci_fetch_all($stmt, $resDM);oci_free_statement($stmt);
				for ($i=0 ; $i < $num; $i++){
					echo "<option value='{$resDM["MA_AN_PHAM_KH"][$i]}'>{$resDM["TEN_AN_PHAM_KH"][$i]}</option>";
				}
			?>
		</select>
	</div>
	<div style='margin:0 0 5px 0;width:100%;'>
		<input type=text id='khcn_frm_reg_anphamkhoahoc_ten_bb_sach_dk' name='khcn_frm_reg_anphamkhoahoc_ten_bb_sach_dk' maxlength=2000 style='width:100%;' placeholder='Tên sách/bài báo dự kiến'  title='Tên sách/bài báo dự kiến' class='khcn_tooltips'>
	</div>
	<div style='margin:0 0 5px 0;width:100%;'>
		<input type=text id='khcn_frm_reg_anphamkhoahoc_so_luong' name='khcn_frm_reg_anphamkhoahoc_so_luong' data-v-min=0 data-v-max=999  maxlength=5 style='width:150px;' placeholder='Số lượng'  title='Số lượng' class='khcn_tooltips khcn_autonumbers'>
	</div>
	<div style='margin:0 0 5px 0;width:100%;'>
		<input type=text id='khcn_frm_reg_anphamkhoahoc_dk_noi_cong_bo' name='khcn_frm_reg_anphamkhoahoc_dk_noi_cong_bo' maxlength=250 style='width:100%;' placeholder='Nơi công bố dự kiến'  title='Nơi công bố dự kiến' class='khcn_tooltips'>
	</div>
	<div style='margin:0 0 5px 0;width:100%;'>
		<input type=text id='khcn_frm_reg_anphamkhoahoc_ghi_chu' name='khcn_frm_reg_anphamkhoahoc_ghi_chu' maxlength=250 style='width:100%;' placeholder='Ghi chú'  title='Ghi chú' class='khcn_tooltips'>
	</div>
</form>
</div>

<div id=khcn_diag_chuyengia style='width:100%;' title="Thêm chuyên gia nghiên cứu">
<form id=khcn_frm_reg_chuyengia name=khcn_frm_reg_chuyengia>
	<table style="width:100%">
		<tr>
			<td style="width:15%"><input type=text id='khcn_frm_reg_chuyengia_shcc' name='khcn_frm_reg_chuyengia_shcc' maxlength=10 style='width:99%;' placeholder='SHCC'  title='Số hiệu công chức, nhập thông tin này nếu chuyên gia thuộc ĐHBK' class='khcn_tooltips'>
								<input type=hidden id='khcn_frm_reg_chuyengia_fk_ma_can_bo' name='khcn_frm_reg_chuyengia_fk_ma_can_bo'>
			</td>
			<td style="width:100%" colspan=3><input type=text id='khcn_frm_reg_chuyengia_hh_hv_ho_ten' name='khcn_frm_reg_chuyengia_hh_hv_ho_ten' maxlength=100 style='width:100%;' placeholder='Học hàm, học vị, Họ và tên'  title='Học hàm, học vị, Họ và tên' class='khcn_tooltips'></td>
		</tr>
		<tr>
			<td style="width:100%" colspan=4><input type=text id='khcn_frm_reg_chuyengia_huongnccs' name='khcn_frm_reg_chuyengia_huongnccs' maxlength=1000 style='width:100%;' placeholder='Hướng nghiên cứu chuyên sâu'  title='Hướng nghiên cứu chuyên sâu' class='khcn_tooltips'></td>
		</tr>
		<tr>
			<td style="width:100%" colspan=4><input type=text id='khcn_frm_reg_chuyengia_don_vi_cong_tac' name='khcn_frm_reg_chuyengia_don_vi_cong_tac' maxlength=200 style='width:100%;' placeholder='Đơn vị công tác'  title='Đơn vị công tác' class='khcn_tooltips'></td>
		</tr>
		<tr>
			<td style="width:100%" colspan=4><input type=text id='khcn_frm_reg_chuyengia_diachi' name='khcn_frm_reg_chuyengia_diachi' maxlength=200 style='width:100%;' placeholder='Địa chỉ'  title='Địa chỉ' class='khcn_tooltips'></td>
		</tr>
		<tr>
			<td style="width:30%" colspan=2>
				<input type=text id='khcn_frm_reg_chuyengia_dienthoai' name='khcn_frm_reg_chuyengia_dienthoai' maxlength=100 style='width:99%;' placeholder='Điện thoại'  title='Điện thoại' class='khcn_tooltips'>
			</td>
			<td style="width:70%" colspan=2>
				<input type=text id='khcn_frm_reg_chuyengia_email' name='khcn_frm_reg_chuyengia_email' maxlength=100 style='width:100%;' placeholder='Email'  title='Email' class='khcn_tooltips'>
			</td>
		</tr>
	</table>
</form>
</div>

<div id=khcn_diag_sohuutritue style='width:100%;' title="Thêm ấn phẩm khoa học">
<form id=khcn_frm_reg_sohuutritue name=khcn_frm_reg_sohuutritue>
	<div style='margin:0 0 5px 0;width:100%;'>
		<select id='khcn_frm_reg_sohuutritue_hinhthuc' name='khcn_frm_reg_sohuutritue_hinhthuc' style='width:100.5%;font-size:12px' title='Hình thức đăng ký'>
			<option value=''>-chọn hình thức đăng ký-</option>
			<?php 
				$sqlstr="select MA_SO_HUU_TRI_TUE, TEN_SO_HUU_TRI_TUE from NCKH_DM_SO_HUU_TRI_TUE"; 
				$stmt = oci_parse($db_conn_khcn, $sqlstr);oci_execute($stmt);$num=oci_fetch_all($stmt, $resDM);oci_free_statement($stmt);
				for ($i=0 ; $i < $num; $i++){
					echo "<option value='{$resDM["MA_SO_HUU_TRI_TUE"][$i]}'>{$resDM["TEN_SO_HUU_TRI_TUE"][$i]}</option>";
				}
			?>
		</select>
	</div>
	<div style='margin:0 0 5px 0;width:100%;'>
		<input type=text id='khcn_frm_reg_sohuutritue_so_luong' name='khcn_frm_reg_sohuutritue_so_luong' data-v-min=0 data-v-max=999 maxlength=5 style='width:150px;' placeholder='Số lượng'  title='Số lượng' class='khcn_tooltips khcn_autonumbers'>
	</div>
	<div style='margin:0 0 5px 0;width:100%;'>
		<input type=text id='khcn_frm_reg_sohuutritue_noi_dung' name='khcn_frm_reg_sohuutritue_noi_dung' maxlength=500 style='width:100%;' placeholder='Nội dung dự kiến đăng ký'  title='Nội dung dự kiến đăng ký' class='khcn_tooltips'>
	</div>
	<div style='margin:0 0 5px 0;width:100%;'>
		<input type=text id='khcn_frm_reg_sohuutritue_ghi_chu' name='khcn_frm_reg_sohuutritue_ghi_chu' maxlength=100 style='width:100%;' placeholder='Ghi chú'  title='Ghi chú' class='khcn_tooltips'>
	</div>
</form>
</div>

<div id=khcn_diag_sanphammem style='width:100%;' title="Sản phẩm mềm">
<form id=khcn_frm_reg_sanphammem name=khcn_frm_reg_sanphammem>
	<div style='margin:0 0 5px 0;width:100%;'>
		<input type=text id='khcn_frm_reg_sanphammem_tensp' name='khcn_frm_reg_sanphammem_tensp' maxlength=1000 style='width:100%;' placeholder='Tên sản phẩm mềm'  title='Tên sản phẩm mềm' class='khcn_tooltips'>
	</div>
	<div style='margin:0 0 5px 0;width:100%;'>
		<textarea rows='5' id='khcn_frm_reg_sanphammem_ctdanhgia' name='khcn_frm_reg_sanphammem_ctdanhgia' maxlength=1000 style='width:100%;' placeholder='Chỉ tiêu đánh giá'  title='Chỉ tiêu đánh giá' class='khcn_tooltips'></textarea>
	</div>
	<div style='margin:0 0 5px 0;width:100%;'>
		<input type=text id='khcn_frm_reg_sanphammem_ghichu' name='khcn_frm_reg_sanphammem_ghichu' maxlength=250 style='width:100%;' placeholder='Ghi chú'  title='Ghi chú' class='khcn_tooltips'>
	</div>
</form>
</div>

<div id=khcn_diag_sanphamcung style='width:100%;' title="Sản phẩm cứng">
<form id=khcn_frm_reg_sanphamcung name=khcn_frm_reg_sanphamcung>
	<div style='margin:0 0 5px 0;width:100%;'>
		<input type=text id='khcn_frm_reg_sanphamcung_tensp' name='khcn_frm_reg_sanphamcung_tensp' maxlength=2000 style='width:100%;' placeholder='Tên sản phẩm cứng'  title='Tên sản phẩm cúng' class='khcn_tooltips'>
	</div>
	<div style='margin:0 0 5px 0;width:100%;'>
		<input type=text id='khcn_frm_reg_sanphamcung_don_vi_do' name='khcn_frm_reg_sanphamcung_don_vi_do' maxlength=100 style='width:100%;' placeholder='Đơn vị đo'  title='Đơn vị đo' class='khcn_tooltips'>
	</div>
	<div style='margin:0 0 5px 0;width:100%;'>
		<textarea rows='5' id='khcn_frm_reg_sanphamcung_ctdanhgia' name='khcn_frm_reg_sanphamcung_ctdanhgia' maxlength=1000 style='width:100%;' placeholder='Chỉ tiêu đánh giá'  title='Chỉ tiêu đánh giá' class='khcn_tooltips'></textarea>
	</div>
	<div style='margin:0 0 5px 0;width:100%;'>
		<input type=text id='khcn_frm_reg_sanphamcung_mau_tt_trong_nuoc' name='khcn_frm_reg_sanphamcung_mau_tt_trong_nuoc' maxlength=500 style='width:100%;' placeholder='Mẫu tương tự trong nước'  title='Mẫu tương tự trong nước' class='khcn_tooltips'>
	</div>
	<div style='margin:0 0 5px 0;width:100%;'>
		<input type=text id='khcn_frm_reg_sanphamcung_mau_tt_thegioi' name='khcn_frm_reg_sanphamcung_mau_tt_thegioi' maxlength=500 style='width:100%;' placeholder='Mẫu tương tự thế giới'  title='Mẫu tương tự thế giới' class='khcn_tooltips'>
	</div>
	<div style='margin:0 0 5px 0;width:100%;'>
		<input type=text id='khcn_frm_reg_sanphamcung_soluong' name='khcn_frm_reg_sanphamcung_soluong' maxlength=500 style='width:100%;' placeholder='Dự kiến số lượng/quy mô sp tạo ra'  title='Dự kiến số lượng/quy mô sp tạo ra' class='khcn_tooltips'>
	</div>
</form>
</div>

<div id=khcn_diag_ketquadaotao style='width:100%;' title="Kết quả đào tạo">
<form id=khcn_frm_reg_ketquadaotao name=khcn_frm_reg_ketquadaotao>
	<div style='margin:0 0 5px 0;width:100%;'>
		<select id='khcn_frm_reg_ketquadaotao_capdt' name='khcn_frm_reg_ketquadaotao_capdt' style='width:100.5%;font-size:12px' title='Cấp đào tạo'>
			<option value=''>-chọn cấp đào tạo-</option>
			<?php 
				$sqlstr="select MA_BAC, TEN_BAC from BAC_DAO_TAO"; 
				$stmt = oci_parse($db_conn_khcn, $sqlstr);oci_execute($stmt);$num=oci_fetch_all($stmt, $resDM);oci_free_statement($stmt);
				for ($i=0 ; $i < $num; $i++){
					echo "<option value='{$resDM["MA_BAC"][$i]}'>{$resDM["TEN_BAC"][$i]}</option>";
				}
			?>
		</select>
	</div>
	<div style='margin:0 0 5px 0;width:100%;'>
		<input type=text id='khcn_frm_reg_ketquadaotao_so_luong' name='khcn_frm_reg_ketquadaotao_so_luong' data-v-min=0 data-v-max=999 maxlength=5 style='width:150px;' placeholder='Số lượng'  title='Số lượng' class='khcn_tooltips khcn_autonumbers'>
	</div>
	<div style='margin:0 0 5px 0;width:100%;'>
		<input type=text id='khcn_frm_reg_ketquadaotao_nhiem_vu' name='khcn_frm_reg_ketquadaotao_nhiem_vu' maxlength=1000 style='width:100%;' placeholder='Nhiệm vụ được giao trong đề tài'  title='Nhiệm vụ được giao trong đề tài' class='khcn_tooltips'>
	</div>
	<div style='margin:0 0 5px 0;width:100%;'>
		<input type=text id='khcn_frm_reg_ketquadaotao_kinhphi' name='khcn_frm_reg_ketquadaotao_kinhphi' data-v-min=0 data-v-max=99999 maxlength=5 style='width:100%;' placeholder='Dự kiến kinh phí'  title='Dự kiến kinh phí' class='khcn_tooltips khcn_autonumbers'>
	</div>
</form>
</div>

<div id=khcn_diag_tonghopkinhphi style='width:100%;' title="Tổng hợp kinh phí">
<form id=khcn_frm_reg_tonghopkinhphi name=khcn_frm_reg_tonghopkinhphi>
	<div style='margin:0 0 5px 0;width:100%;'>
		<input type=hidden id='khcn_frm_reg_tonghopkinhphi_khoan_chi_phi' name='khcn_frm_reg_tonghopkinhphi_khoan_chi_phi'>
		<span id=khcn_frm_reg_tonghopkinhphi_ten_khoan_chi class=heading></span>
	</div>
	<div style='margin:0 0 5px 0;width:100%;' align=left class=heading>Kinh phí</div>
	<div style='margin:0 0 5px 0;width:100%;'>
		<input type=text id='khcn_frm_reg_tonghopkinhphi_kinh_phi' name='khcn_frm_reg_tonghopkinhphi_kinh_phi' data-v-min=0.00 data-v-max=9999.99 maxlength=15 style='width:100%;text-align:right' placeholder='Kinh phí'  title='Kinh phí' class='khcn_tooltips khcn_autonumbers'>
	</div>
	<div style='margin:0 0 5px 0;width:100%;' align=left class=heading>Trong đó khoán chi</div>
	<div style='margin:0 0 5px 0;width:100%;'>
		<input type=text id='khcn_frm_reg_tonghopkinhphi_khoan_chi' name='khcn_frm_reg_tonghopkinhphi_khoan_chi' data-v-min=0.00 data-v-max=9999.99 maxlength=15 style='width:100%;text-align:right' placeholder='Trong đó khoán chi'  title='Trong đó khoán chi' class='khcn_tooltips khcn_autonumbers'>
	</div>
	
</form>
</div>

<div id=khcn_diag_edit_dtkhcn_thongtinchung style='width:650px;' title="Thông tin chung - Thuyết minh đề tài KH&CN">

	<div id="khcn_tabs_thuyetminh_thongtinchung">
		<ul>
		<li><a href="#tabs-A1-A4" title='<b>Tên đề tài - Ngành - Loại hình nghiên cứu - Thời gian thực hiện</b>' class=khcn_tooltips>A1-A4</a></li>
		<li><a href="#tabs-A5" title='<b>Tổng kinh phí</b>' class=khcn_tooltips>A5</a></li>
		<li><a href="#tabs-A6" title='<b>Chủ nhiệm - Tóm tắt hoạt động NC & ĐT SĐH</b>' class=khcn_tooltips>A6</a></li>
		<li><a href="#tabs-A7-A8" title='<b>Cơ quan chủ trì - Cơ quan phối hợp thực hiện</b>' class=khcn_tooltips>A7-A8</a></li>
		<li><a href="#tabs-A9" title='<b>Nhân lực nghiên cứu</b>' class=khcn_tooltips>A9</a></li>
		</ul>
		<div id="tabs-A1-A4">
			<form id=khcn_frm_edit_dtkhcn_A1_A4 name=khcn_frm_edit_dtkhcn_A1_A4 >
				<div style='margin: 0 0 0 0;' class=heading>Cấp đề tài <span id=khcn_frm_edit_dtkhcn_tencapdetai style="font-color:red"></span><input type=hidden id=khcn_frm_edit_dtkhcn_capdetai></div>
				<div style='margin: 5px 0 5px 0;'></div>
				<div style='margin: 5px 0 5px 0;' class=heading>Tên đề tài</div>
				<div style='margin: 5px 0 5px 0;'><input type=text id=khcn_frm_edit_dtkhcn_ten_dt_viet name=khcn_frm_edit_dtkhcn_ten_dt_viet maxLength=1000 placeholder='Tên tiếng Việt' title='Tên tiếng Việt' style='width:97%;height:18px' class='khcn_tooltips'/> <font color=red>*</font><span id=khcn_error_edit_dtkhcn_ten_dt_viet class=khcn_thuyetminh_error></span></div>
				<div style='margin: 5px 0 5px 0;'><input type=text id=khcn_frm_edit_dtkhcn_ten_dt_anh name=khcn_frm_edit_dtkhcn_ten_dt_anh maxLength=1000 placeholder='Tên tiếng Anh' title='Tên tiếng Anh' style='width:97%;height:18px' class='khcn_tooltips'/> <font color=red>*</font></div>
				<div style='margin: 10px 0 5px 0;'><input type=text id=khcn_frm_edit_dtkhcn_keywords name=khcn_frm_edit_dtkhcn_keywords maxLength=500 placeholder='Keywords (dùng cho tìm kiếm)' title='Keywords (dùng cho tìm kiếm)' style='width:97%;height:18px' class='khcn_tooltips'/> <font color=red>*</font></div>
				<div style='margin: 5px 0 5px 0;'><input type=text id=khcn_frm_edit_dtkhcn_huongdt name=khcn_frm_edit_dtkhcn_huongdt maxLength=1000 placeholder='Hướng đề tài' title='<b>Hướng đề tài</b>; Hướng nghiên cứu về biến đổi khí hậu; nghiên cứu về nông nghiệp, nông dân và nông thôn; nghiên cứu về công nghệ sinh học (Công nghệ gen, CN tế bào, CN protein và enzime, CN vi sinh, …); nghiên cứu sử dụng hiệu quả năng lượng;…' style='width:97%;height:18px' class='khcn_tooltips'/> <font color=red>*</font></div>
				
				<div style='margin: 5px 0 5px 0;'>
					<select type=text id=khcn_frm_edit_dtkhcn_dvdk name=khcn_frm_edit_dtkhcn_dvdk style='font-size:13px; width:98%;' title="Đề tài đăng ký cho đơn vị" class='khcn_tooltips'>
						<option value="">-chọn đơn vị-</option>
							<?php 
								$sqlstr="select MA_BO_MON, TEN_BO_MON from BO_MON order by TEN_BO_MON"; 
								$stmt = oci_parse($db_conn_khcn, $sqlstr);oci_execute($stmt);$n=oci_fetch_all($stmt, $resDM);oci_free_statement($stmt);
								for ($i=0 ; $i < $n; $i++){
									echo "<option value='{$resDM["MA_BO_MON"][$i]}'>{$resDM["TEN_BO_MON"][$i]}</option>";
								}
							?>
					</select>
					<font color=red>*</font>
				</div>
				
				<div style='margin: 5px 0 5px 0;' class=heading><span id=khcn_frm_edit_dtkhcn_nganh>Thuộc ngành/nhóm ngành</span> <font color=red>*</font></div>
				<div>
					<table id=khcn_frm_edit_table_nganh name=khcn_frm_edit_table_nganh style='line-height:20px'>
						<tr>
							<?php 
								$sqlstr="select ma_nhom_nganh, ten_nhom_nganh from nckh_nhom_nganh"; 
								$stmt = oci_parse($db_conn_khcn, $sqlstr);oci_execute($stmt);$numNganh=oci_fetch_all($stmt, $resDM);oci_free_statement($stmt);
								$j = ceil($numNganh/3); 
								$count=0;
								
								for ($i=0 ; $i < $numNganh; $i++)
								{
									if ($count==0)
										echo "<td valign=top>";
									echo "<input type=checkbox id=khcn_frm_edit_nganh$i name=khcn_frm_edit_nganh$i value='{$resDM["MA_NHOM_NGANH"][$i]}' />";
									if ($resDM["MA_NHOM_NGANH"][$i]=='999')
										echo " <input type=text id=khcn_frm_edit_nganhkhac name=khcn_frm_edit_nganhkhac placeholder='Khác...' title='Ngành/nhóm ngành khác' class='khcn_tooltips' style='height:18px'/>";
									else
										echo "<label for=khcn_frm_edit_nganh$i> {$resDM["TEN_NHOM_NGANH"][$i]}</label> <br/>";
									if ($count==($j-1))
									{
										echo "</td>";
										$count = 0;
									}
									else
										$count += 1;
								}
								if ($count*3<$numNganh && $count != 0)
									echo "</td>";
							?>
						</tr>
					</table>
				</div>
				
				<div style='margin: 5px 0 0 0;'>
					<table style='line-height:20px; width:100%'>
						<tr>
							<td colspan=4 align=left class=heading>Chuyên ngành hẹp</td>
						</tr>
						<tr>
							<td colspan=4>
								<input type=text id=khcn_frm_edit_dtkhcn_cnganhhep name=khcn_frm_edit_dtkhcn_cnganhhep maxLength=250 placeholder='Chuyên ngành hẹp' title="Chuyên ngành hẹp" style='width:97%;height:18px' class='khcn_tooltips'/> <font color=red>*</font>
							</td>
						</tr>
						<tr>
							<td colspan=2 style='width:50%' align=left class=heading>Loại hình nghiên cứu</td>
							<td colspan=2 style='width:50%' align=left class=heading>Thời gian thực hiện (tháng, kể từ khi được duyệt)</td>						
						</tr>
						<tr>
							<td colspan=2 style='width:50%'>
								<select id=khcn_frm_edit_dtkhcn_loaihinhnc name=khcn_frm_edit_dtkhcn_loaihinhnc style='font-size:13px; width:95%' title='Loại hình nghiên cứu' class='khcn_tooltips'>
									<option value="">-Chọn loại hình nghiên cứu-</option>
									<?php 
										$sqlstr="select MA_LOAI_HINH_NC, TEN_LOAI_HINH_NC from NCKH_LOAI_HINH_NC"; 
										$stmt = oci_parse($db_conn_khcn, $sqlstr);oci_execute($stmt);$n=oci_fetch_all($stmt, $resDM);oci_free_statement($stmt);
										
										for ($i=0 ; $i < $n; $i++)
										{
											echo "<option value='{$resDM["MA_LOAI_HINH_NC"][$i]}'>{$resDM["TEN_LOAI_HINH_NC"][$i]}</option>";
										}
									?>
								</select>
								<font color=red>*</font>
							</td>
							<td style='width:50%' colspan=2><input type=text id=khcn_frm_edit_dtkhcn_thoigianthuchien name=khcn_frm_edit_dtkhcn_thoigianthuchien data-v-min=0 data-v-max=999 maxLength=3 placeholder='Thời gian thực hiện (tháng)' title="Thời gian thực hiện (tháng, kể từ khi được duyệt)" style='width:95%;height:18px' class='khcn_tooltips khcn_autonumbers'/> <font color=red>*</font></td>						
						</tr>
						<tr>
							<td style='width:25%' ></td>
							<td style='width:25%' ></td>
							<td style='width:25%' ></td>
							<td style='width:25%' ></td>						
						</tr>
						<tr>
							<td style='width:100%' colspan=4></td>
						</tr>
					</table>
				</div>
			</form>
			
			<div align="center" id="khcn_a1a4_tips" class="validateTips"></div>
		</div>
		<div id="tabs-A5">
			<form id=khcn_frm_edit_dtkhcn_A5 name=khcn_frm_edit_dtkhcn_A5>
				<table style='line-height:20px; width:100%'>
					<tr>
						<td align=left class=heading>Tổng kinh phí</td>
						<td style='' colspan=4><input type=text id=khcn_frm_edit_dtkhcn_tongkinhphi name=khcn_frm_edit_dtkhcn_tongkinhphi data-v-min=0.00 data-v-max=999999.99 maxLength=11 placeholder='' title="Tổng kinh phí (triệu đồng)" style='width:70px;height:18px;text-align:right' class='khcn_tooltips khcn_autonumbers'/> (triệu đồng), <b>gồm:</b> </td>
					</tr>
					<tr>
						<td></td>
						<td align=left class=heading>- Kinh phí từ <span id=khcn_frm_edit_dtkhcn_A5_kinhphitu>ĐHQG-HCM</span></td>
						<td style='' colspan=3><input type=text id=khcn_frm_edit_dtkhcn_kinhphi_dhqg name=khcn_frm_edit_dtkhcn_kinhphi_dhqg data-v-min=0.00 data-v-max=9999.99 maxLength=11 placeholder='' title="Kinh phí từ ĐHQG-HCM (triệu đồng)" style='width:70px;height:18px;text-align:right' class='khcn_tooltips khcn_autonumbers'/> triệu đồng</td>
					</tr>
					<tr>
						<td></td>
						<td align=left class=heading>- Kinh phí từ nguồn huy động</td>
						<td style='' colspan=3><input type=text id=khcn_frm_edit_dtkhcn_kinhphi_huydong name=khcn_frm_edit_dtkhcn_kinhphi_huydong data-v-min=0.00 data-v-max=999999.99 maxLength=11 placeholder='' title="Kinh phí từ nguồn huy động (vốn tự có và vốn khác)" style='width:70px;height:18px;text-align:right' class='khcn_tooltips khcn_autonumbers'/> triệu đồng, <b>trong đó:</b></td>
					</tr>
					<tr>
						<td></td>
						<td align=left></td>
						<td align=left class=heading>+ Vốn tự có </td>
						<td style='' ><input type=text id=khcn_frm_edit_dtkhcn_kinhphi_tuco name=khcn_frm_edit_dtkhcn_kinhphi_tuco data-v-min=0.00 data-v-max=9999.99  maxLength=11 placeholder='' title="Vốn tự có" style='width:70px;height:18px;text-align:right' class='khcn_tooltips khcn_autonumbers'/></td>
						<td align=left style='width:200px' > triệu đồng </td>
					</tr>
					<tr>
						<td></td>
						<td align=left></td>
						<td align=left class=heading>+ Vốn khác </td>
						<td style='' ><input type=text id=khcn_frm_edit_dtkhcn_kinhphi_khac name=khcn_frm_edit_dtkhcn_kinhphi_khac data-v-min=0.00 data-v-max=9999.99  maxLength=11 placeholder='' title="Vốn khác (triệu đồng)" style='width:70px;height:18px;text-align:right' class='khcn_tooltips khcn_autonumbers'/></td>						
						<td align=left> triệu đồng <font color=red>(<b>*</b><span id=khc_lbl_a5_kinhphi_vonkhac></span>)</font></td>
					</tr>
					
					<tr>
						<td style='width:100%' colspan=5><b>Tên tổ chức tài trợ</b> (Đã nộp hồ sơ đề nghị tài trợ từ nguồn kinh phí khác? Nếu có, ghi rõ tên tổ chức tài trợ)</td>
					</tr>
					<tr>
						<td style='width:100%' colspan=5><input type=text id=khcn_frm_edit_dtkhcn_tochuctaitro name=khcn_frm_edit_dtkhcn_tochuctaitro maxLength=500 placeholder='Tên tổ chức tài trợ' title="Tên tổ chức tài trợ (Đã nộp hồ sơ đề nghị tài trợ từ nguồn kinh phí khác? Nếu có, ghi rõ tên tổ chức tài trợ)" style='width:99.5%;height:18px' class='khcn_tooltips'/></td>
					</tr>
				</table>
			</form>
			
			<div style='margin-top: 10px;'><font color=red>(<b>*</b>) - Đề tài cấp ĐHQG cần đính kèm <b>mẫu R04</b>, download mẫu R04 tại đây <a href="./khcn/templ/R04_xac_nhan_phoi_hop_thuc_hien.doc" target=_blank><font color=green>>>></font></a><br> 
			&nbsp; &nbsp; &nbsp; - Đề tài cấp trường cần đính kèm <b>minh chứng</b></font></div>
			
			<div style='display: block; margin: 20px auto; background: #eee; border-radius: 10px; padding: 15px;'>
				<div style='margin: 0 0 5px 0;' align=left class=heading>PHỤ LỤC: VỐN KHÁC</div>
				<table style='width:100%'>
					<tr>
						<td align=left style='width:17%'><button id=khcn_frm_edit_dtkhcn_btn_open_file_minhchung title="Kích thước file upload tối đa là <b>1MB</b>" class="khcn_tooltips">Đính file phụ lục</button></td>
						<td align=left style='width:60%'>
							<div id="progress1">
								<div id="bar1"></div>
								<div id="percent1" style='font-weight:bold'>0%</div >
							</div>
						</td>
					</tr>
				</table>
				<div style='margin: 10px 0 5px 0px;' align=left>
					* File phụ lục đính kèm: <b><span id=khcn_file_giai_trinh_vonkhac></span></b>
					
					<form id="khcn_frm_upload_file_vonkhac" action="khcn/khcn_thuyetminhdtkhcn_file_phu_luc_process.php?hisid=<?php echo $_REQUEST["hisid"]; ?>&w=uploadfilevonkhac" method="post" enctype="multipart/form-data">
						<input type="hidden" name="MAX_FILE_SIZE" value="1048576" />
						<input type="hidden" name="khcn_file_vonkhac_ma_tmdt" id="khcn_file_vonkhac_ma_tmdt" value="" />
						<div style='display:none;'><input type="file" size="60" name="khcn_file_vonkhac" id="khcn_file_vonkhac"  onchange="khcn_userfile_vonkhac_change(this)"></div>
					</form>
					 
				</div>
			</div>
			
			<div align="center" id="khcn_a5_tips" class="validateTips"></div>
		</div>
		<div id="tabs-A6">
			<form id=khcn_frm_edit_dtkhcn_A6 name=khcn_frm_edit_dtkhcn_A6 >
				<div style='margin: 5px 0 5px 0;' class=heading>Chủ nhiệm <button id=khcn_frm_edit_dtkhcn_A6_btn_cndt title="Lấy thông tin của chủ nhiệm đề tài">...</button></div>
				<table style='width:100%'>
					<tr>
						<td colspan=2 style='width:75%'>
							<input type=text id=khcn_frm_edit_dtkhcn_cndt_hh_hv_ho_ten name=khcn_frm_edit_dtkhcn_cndt_hh_hv_ho_ten maxLength=50 placeholder='Học hàm, học vị, họ và tên' title='Học hàm, học vị, họ và tên' style='width:95%;height:18px' class='khcn_tooltips'/> <font color=red>*</font>
							<input type=hidden id=khcn_frm_edit_dtkhcn_fk_chu_nhiem_dt name=khcn_frm_edit_dtkhcn_fk_chu_nhiem_dt/>
						</td>
						<td style='width:25%' >
							<input type=text id=khcn_frm_edit_dtkhcn_cndt_ngay_sinh name=khcn_frm_edit_dtkhcn_cndt_ngay_sinh maxLength=10 placeholder='Ngày sinh (dd/mm/yyyy)' title='Ngày sinh (dd/mm/yyyy)' style='width:100px;height:18px' class='khcn_tooltips'/> <font color=red>*</font>
							&nbsp; <b>Phái</b> <input type="radio" name='khcn_frm_edit_dtkhcn_cndt_phai' value='M'/>Nam 
								 <input type="radio" name='khcn_frm_edit_dtkhcn_cndt_phai' value='F'/>Nữ
						</td>
					</tr>
					<tr>
						<td style='width:30%'><input type=text id=khcn_frm_edit_dtkhcn_cndt_so_cmnd name=khcn_frm_edit_dtkhcn_cndt_so_cmnd maxLength=10 placeholder='Số CMND' title='Số CMND' style='width:90%;height:18px' class='khcn_tooltips'/> <font color=red>*</font></td>
						<td style='width:30%'><input type=text id=khcn_frm_edit_dtkhcn_cndt_ngay_cap name=khcn_frm_edit_dtkhcn_cndt_ngay_cap maxLength=10 placeholder='Ngày cấp (dd/mm/yyyy)' title='Ngày cấp CMND (dd/mm/yyyy)' style='width:90%;height:18px' class='khcn_tooltips'/> <font color=red>*</font></td>
						<td style='width:40%'>
							<select id=khcn_frm_edit_dtkhcn_cndt_noi_cap name=khcn_frm_edit_dtkhcn_cndt_noi_cap placeholder='Nơi cấp' title='Nơi cấp CMND' style='width:94%;height:24px' class='khcn_tooltips'>
								<option value=''>-chọn nơi cấp-</option>
								<?php $sqlstr="select ma_tinh_tp, ten_tinh_tp from dm_tinh_tp order by ten_tinh_tp"; 
									$stmt = oci_parse($db_conn_khcn, $sqlstr);oci_execute($stmt);$n = oci_fetch_all($stmt, $resDM);oci_free_statement($stmt);
									for ($i = 0; $i < $n; $i++)
									{
										echo "<option value='".$resDM["MA_TINH_TP"][$i]."'>" .$resDM["TEN_TINH_TP"][$i]. "</option>";
									}
									
								  ?>
							</select>
							 <font color=red>*</font>
						</td>
					</tr>
					<tr><td colspan=3 style='width:100%'><input type=text id=khcn_frm_edit_dtkhcn_cndt_ms_thue name=khcn_frm_edit_dtkhcn_cndt_ms_thue maxLength=10 placeholder='Mã số thuê cá nhân' title='Mã số thuê cá nhân' style='width:97%;height:18px' class='khcn_tooltips'/> <font color=red>*</font></td></tr>
					<tr>
						<td colspan=2 style='width:75%'><input type=text id=khcn_frm_edit_dtkhcn_cndt_so_tai_khoan name=khcn_frm_edit_dtkhcn_cndt_so_tai_khoan maxLength=30 placeholder='Số tài khoản' title='Số tài khoản' style='width:99%;height:18px' class='khcn_tooltips'/></td>
						<td style='width:25%'><input type=text id=khcn_frm_edit_dtkhcn_cndt_ngan_hang name=khcn_frm_edit_dtkhcn_cndt_ngan_hang maxLength=200 placeholder='Tại ngân hàng' title='Tại ngân hàng' style='width:92.5%;height:18px' class='khcn_tooltips'/></td>
					</tr>
					<tr><td colspan=3 style='width:100%'><input type=text id=khcn_frm_edit_dtkhcn_cndt_dia_chi_cq name=khcn_frm_edit_dtkhcn_cndt_dia_chi_cq maxLength=200 placeholder='Địa chỉ cơ quan' title='Địa chỉ cơ quan' style='width:97%;height:18px' class='khcn_tooltips'/> <font color=red>*</font></td></tr>
					<tr>
						<td style='width:25%'><input type=text id=khcn_frm_edit_dtkhcn_cndt_dien_thoai name=khcn_frm_edit_dtkhcn_cndt_dien_thoai maxLength=50 placeholder='Điện thoại cá nhân' title='Điện thoại cá nhân' style='width:90%;height:18px' class='khcn_tooltips'/> <font color=red>*</font></td>
						<td colspan=2 style='width:75%'><input type=text id=khcn_frm_edit_dtkhcn_cndt_email name=khcn_frm_edit_dtkhcn_cndt_email maxLength=100 placeholder='Email' title='Email' style='width:95.6%;height:18px' class='khcn_tooltips'/> <font color=red>*</font></td>
					</tr>
					<tr><td colspan=3 style='width:100%'><textarea rows="4" cols="50" id=khcn_frm_edit_dtkhcn_tom_tat_hd_nc name=khcn_frm_edit_dtkhcn_tom_tat_hd_nc maxLength=2000 placeholder="Tóm tắt hoạt động nghiên cứu và đào tạo SĐH có liên quan đến đề tài của chủ nhiệm (không quá 500 chữ)" title="Tóm tắt hoạt động nghiên cứu và đào tạo SĐH có liên quan đến đề tài của chủ nhiệm (không quá 500 chữ)" style='width:97%;' class='khcn_tooltips'></textarea></td></tr>
				</table>
				
				<div style='margin: 5px 0 5px 0;' class=heading>Đồng chủ nhiệm</div>
				<table style='width:100%'>
					<tr>
						<td colspan=2 style='width:75%'>
							<input type=text id=khcn_frm_edit_dtkhcn_dcndt_hh_hv_ho_ten name=khcn_frm_edit_dtkhcn_dcndt_hh_hv_ho_ten maxLength=50 placeholder='Học hàm, học vị, họ và tên' title='Tìm thông tin Cán bộ giảng dạy bằng cách nhập họ tên vào ô này' style='width:285px;height:18px' class='khcn_tooltips'/> <font color=red>*</font> 
							
							<input type=text id=khcn_frm_edit_dtkhcn_dcndt_shcc name=khcn_frm_edit_dtkhcn_dcndt_shcc maxLength=6 placeholder='SHCC'  style='width:60px;height:18px' class='khcn_tooltips'/>
							
							<input type=hidden id="khcn_frm_edit_dtkhcn_fk_dong_chu_nhiem_dt" name="khcn_frm_edit_dtkhcn_fk_dong_chu_nhiem_dt">
						</td>
						<td style='width:25%' >
							<input type=text id=khcn_frm_edit_dtkhcn_dcndt_ngay_sinh name=khcn_frm_edit_dtkhcn_dcndt_ngay_sinh maxLength=10 placeholder='Ngày sinh' title='Ngày sinh (dd/mm/yyyy)' style='width:100px;height:18px' class='khcn_tooltips'/>
							<font color=red>*</font> &nbsp; <b>Phái</b> <input type="radio" name="khcn_frm_edit_dtkhcn_dcndt_phai" value="M">Nam 
							<input type="radio" name="khcn_frm_edit_dtkhcn_dcndt_phai" value="F">Nữ
						</td>
					</tr>
					<tr>
						<td style='width:30%'><input type=text id=khcn_frm_edit_dtkhcn_dcndt_so_cmnd name=khcn_frm_edit_dtkhcn_dcndt_so_cmnd maxLength=10 placeholder='Số CMND' title='Số CMND' style='width:90%;height:18px' class='khcn_tooltips'/> <font color=red>*</font></td>
						<td style='width:30%'><input type=text id=khcn_frm_edit_dtkhcn_dcndt_ngay_cap name=khcn_frm_edit_dtkhcn_dcndt_ngay_cap maxLength=10 placeholder='Ngày cấp (dd/mm/yyyy)' title='Ngày cấp CMND (dd/mm/yyyy)' style='width:90%;height:18px' class='khcn_tooltips'/> <font color=red>*</font></td>
						<td style='width:40%'>
							<select id=khcn_frm_edit_dtkhcn_dcndt_noi_cap name=khcn_frm_edit_dtkhcn_dcndt_noi_cap placeholder='Nơi cấp' title='Nơi cấp CMND' style='width:94%;height:24px' class='khcn_tooltips'>
								<option value=''>-chọn nơi cấp-</option>
								<?php $sqlstr="select ma_tinh_tp, ten_tinh_tp from dm_tinh_tp order by ten_tinh_tp"; 
									$stmt = oci_parse($db_conn_khcn, $sqlstr);oci_execute($stmt);$n = oci_fetch_all($stmt, $resDM);oci_free_statement($stmt);
									for ($i = 0; $i < $n; $i++)
									{
										echo "<option value='".$resDM["MA_TINH_TP"][$i]."'>" .$resDM["TEN_TINH_TP"][$i]. "</option>";
									}
									
								  ?>
							</select> <font color=red>*</font>
						</td>
					</tr>
					<tr><td colspan=3 style='width:100%'><input type=text id=khcn_frm_edit_dtkhcn_dcndt_ms_thue name=khcn_frm_edit_dtkhcn_dcndt_ms_thue maxLength=10 placeholder='Mã số thuê cá nhân' title='Mã số thuê cá nhân' style='width:97%;height:18px' class='khcn_tooltips'/> <font color=red>*</font></td></tr>
					<tr>
						<td colspan=2 style='width:75%'><input type=text id=khcn_frm_edit_dtkhcn_dcndt_so_tai_khoan name=khcn_frm_edit_dtkhcn_dcndt_so_tai_khoan maxLength=30 placeholder='Số tài khoản' title='Số tài khoản' style='width:97%;height:18px' class='khcn_tooltips'/></td>
						<td style='width:25%'><input type=text id=khcn_frm_edit_dtkhcn_dcndt_ngan_hang name=khcn_frm_edit_dtkhcn_dcndt_ngan_hang maxLength=200 placeholder='Tại ngân hàng' title='Tại ngân hàng' style='width:92.5%;height:18px' class='khcn_tooltips'/></td>
					</tr>
					<tr><td colspan=3 style='width:100%'><input type=text id=khcn_frm_edit_dtkhcn_dcndt_dia_chi_cq name=khcn_frm_edit_dtkhcn_dcndt_dia_chi_cq maxLength=200 placeholder='Địa chỉ cơ quan' title='Địa chỉ cơ quan' style='width:97%;height:18px' class='khcn_tooltips'/> <font color=red>*</font></td></tr>
					<tr>
						<td style='width:25%'><input type=text id=khcn_frm_edit_dtkhcn_dcndt_dien_thoai name=khcn_frm_edit_dtkhcn_dcndt_dien_thoai maxLength=50 placeholder='Điện thoại cá nhân' title='Điện thoại cá nhân' style='width:90%;height:18px' class='khcn_tooltips'/> <font color=red>*</font></td>
						<td colspan=2 style='width:75%'><input type=text id=khcn_frm_edit_dtkhcn_dcndt_email name=khcn_frm_edit_dtkhcn_dcndt_email maxLength=80 placeholder='Email' title='Email' style='width:95.8%;height:18px' class='khcn_tooltips'/> <font color=red>*</font></td>
					</tr>					
				</table>
			</form>
			<div style='margin-top:5px' align="center" id="khcn_a6_tips" class="validateTips"></div>
		</div>
		<div id="tabs-A7-A8">
			<form id=khcn_frm_edit_dtkhcn_A7_A8 name=khcn_frm_edit_dtkhcn_A7_A8 >
				<div style='margin: 5px 0 5px 0;' class=heading>Cơ quan chủ trì</div>
				<table style='width:100%'>
					<tr>
						<td colspan=2 style='width:100%'>
							<input type=text id=khcn_frm_edit_dtkhcn_cqct_ten_co_quan name=khcn_frm_edit_dtkhcn_cqct_ten_co_quan maxLength=500 placeholder='Tên cơ quan' title='Tên cơ quan chủ trì' style='width:100%;height:18px' class='khcn_tooltips'/>
							<input type=hidden id=khcn_frm_edit_dtkhcn_fk_cq_chu_tri name=khcn_frm_edit_dtkhcn_fk_cq_chu_tri/>
						</td>
					</tr>
					<tr>
						<td colspan=2 style='width:100%'><input type=text id=khcn_frm_edit_dtkhcn_cqct_ho_ten_tt name=khcn_frm_edit_dtkhcn_cqct_ho_ten_tt maxLength=50 placeholder='Họ và tên thủ trưởng' title='Họ và tên thủ trưởng cơ quan chủ trì' style='width:100%;height:18px' class='khcn_tooltips'/></td>
					</tr>
					<tr>
						<td style='width:50%'><input type=text id=khcn_frm_edit_dtkhcn_cqct_dien_thoai name=khcn_frm_edit_dtkhcn_cqct_dien_thoai maxLength=50 placeholder='Điện thoại' title='Điện thoại cơ quan chủ trì' style='width:99%;height:18px' class='khcn_tooltips'/></td>
						<td style='width:50%'><input type=text id=khcn_frm_edit_dtkhcn_cqct_fax name=khcn_frm_edit_dtkhcn_cqct_fax maxLength=50 placeholder='Fax' title='Fax cơ quan chủ trì' style='width:100%;height:18px' class='khcn_tooltips'/></td>
					</tr>
					<tr>						
						<td colspan=2 style='width:100%'><input type=text id=khcn_frm_edit_dtkhcn_cqct_email name=khcn_frm_edit_dtkhcn_cqct_email maxLength=100 placeholder='Email' title='Email cơ quan chủ trì' style='width:100%;height:18px' class='khcn_tooltips'/></td>
					</tr>

					<tr>
						<td style='width:50%'><input type=text id=khcn_frm_edit_dtkhcn_cqct_so_tai_khoan name=khcn_frm_edit_dtkhcn_cqct_so_tai_khoan maxLength=30 placeholder='Số tài khoản' title='Số tài khoản cơ quan chủ trì' style='width:99%;height:18px' class='khcn_tooltips'/></td>
						<td style='width:50%'><input type=text id=khcn_frm_edit_dtkhcn_cqct_kho_bac name=khcn_frm_edit_dtkhcn_cqct_kho_bac maxLength=200 placeholder='Tại ngân hàng' title='Tại ngân hàng' style='width:100%;height:18px' class='khcn_tooltips'/></td>
					</tr>
					
				</table>
				
				<div style='margin: 5px 0 5px 0;' ><span class=heading>Cơ quan phối hợp thực hiện</span> <em>(Giấy xác nhận đính kèm theo mẫu quy định)</em></div>
				<div style='margin: 5px 0 5px 3px;' class=heading>Cơ quan 1</div>
				<table style='width:100%'>
					<tr>
						<td colspan=2 style='width:100%'>
							<input type=text id=khcn_frm_edit_dtkhcn_cqph1_ten_co_quan name=khcn_frm_edit_dtkhcn_cqph1_ten_co_quan maxLength=500 placeholder='Tên cơ quan phối hợp' title='Tên cơ quan phối hợp' style='width:100%;height:18px' class='khcn_tooltips'/>
						</td>
					</tr>
					<tr>
						<td colspan=2 style='width:100%'><input type=text id=khcn_frm_edit_dtkhcn_cqph1_ho_ten_tt name=khcn_frm_edit_dtkhcn_cqph1_ho_ten_tt maxLength=50 placeholder='Họ và tên thủ trưởng' title='Họ và tên thủ trưởng cơ quan phối hợp' style='width:100%;height:18px' class='khcn_tooltips'/></td>
					</tr>
					<tr>
						<td style='width:50%'><input type=text id=khcn_frm_edit_dtkhcn_cqph1_dien_thoai name=khcn_frm_edit_dtkhcn_cqph1_dien_thoai maxLength=50 placeholder='Điện thoại' title='Điện thoại cơ quan chủ trì' style='width:99%;height:18px' class='khcn_tooltips'/></td>
						<td style='width:50%'><input type=text id=khcn_frm_edit_dtkhcn_cqph1_fax name=khcn_frm_edit_dtkhcn_cqph1_fax maxLength=50 placeholder='Fax' title='Fax cơ quan chủ trì' style='width:100%;height:18px' class='khcn_tooltips'/></td>
					</tr>
					<tr>						
						<td colspan=2 style='width:100%'><input type=text id=khcn_frm_edit_dtkhcn_cqph1_dia_chi name=khcn_frm_edit_dtkhcn_cqph1_dia_chi maxLength=200 placeholder='Địa chỉ cơ quan phối hợp' title='Địa chỉ cơ quan phối hợp' style='width:100%;height:18px' class='khcn_tooltips'/></td>
					</tr>
				</table>
				<div style='margin: 5px 0 5px 3px;' class=heading>Cơ quan 2</div>
				<table style='width:100%'>
					<tr>
						<td colspan=2 style='width:100%'>
							<input type=text id=khcn_frm_edit_dtkhcn_cqph2_ten_co_quan name=khcn_frm_edit_dtkhcn_cqph2_ten_co_quan maxLength=500 placeholder='Tên cơ quan phối hợp' title='Tên cơ quan phối hợp' style='width:100%;height:18px' class='khcn_tooltips'/>
						</td>
					</tr>
					<tr>
						<td colspan=2 style='width:100%'><input type=text id=khcn_frm_edit_dtkhcn_cqph2_ho_ten_tt name=khcn_frm_edit_dtkhcn_cqph2_ho_ten_tt maxLength=50 placeholder='Họ và tên thủ trưởng' title='Họ và tên thủ trưởng cơ quan phối hợp' style='width:100%;height:18px' class='khcn_tooltips'/></td>
					</tr>
					<tr>
						<td style='width:50%'><input type=text id=khcn_frm_edit_dtkhcn_cqph2_dien_thoai name=khcn_frm_edit_dtkhcn_cqph2_dien_thoai maxLength=50 placeholder='Điện thoại' title='Điện thoại cơ quan chủ trì' style='width:99%;height:18px' class='khcn_tooltips'/></td>
						<td style='width:50%'><input type=text id=khcn_frm_edit_dtkhcn_cqph2_fax name=khcn_frm_edit_dtkhcn_cqph2_fax maxLength=50 placeholder='Fax' title='Fax cơ quan chủ trì' style='width:100%;height:18px' class='khcn_tooltips'/></td>
					</tr>
					<tr>						
						<td colspan=2 style='width:100%'><input type=text id=khcn_frm_edit_dtkhcn_cqph2_dia_chi name=khcn_frm_edit_dtkhcn_cqph2_dia_chi maxLength=200 placeholder='Địa chỉ cơ quan phối hợp' title='Địa chỉ cơ quan phối hợp' style='width:100%;height:18px' class='khcn_tooltips'/></td>
					</tr>
				</table>
			</form>
		</div>
		<div id="tabs-A9">
			<form id=khcn_frm_edit_dtkhcn_A9 name=khcn_frm_edit_dtkhcn_A9 >
				<div style='margin: 5px 0 5px 0;' ><span class=heading>Nhân lực nghiên cứu</span> <em>(Ghi những <span style="font-weight:bold; color:red;">người có đóng góp khoa học và chủ trì thực hiện</span> những
				nội dung chính thuộc cơ quan chủ trì và cơ quan phối hợp tham gia thực hiện - mỗi người có tên trong danh sách này phải khai báo lý lịch khoa học theo
				biểu quy định)</em></div>
				<div style='margin: 5px 0 5px 0;' align=right><button id=khcn_frm_edit_dtkhcn_btn_open_dlg_add_nhanluc>Thêm nhân lực nghiên cứu</button></div>
				<table id=khcn_frm_edit_dtkhcn_A9_table_nhanluc class='ui-widget ui-widget-content ui-corner-top tableData' style='width:100%; border-color: #000000; border-width: 1px; border-style: solid; border-collapse:collapse;'>
					<thead>
						<tr class='ui-widget-header heading' style='height:20px;border-color: #000000; border-width: 1px; border-style: solid; border-collapse:collapse;'>
							<td align=left>TT</td><td align=left>Học hàm, học vị, Họ và tên</td><td align=left>SHCC/Mã SV</td><td align=left>Đơn vị công tác</td><td align=center title="Số tháng làm việc quy đổi">Tháng QĐ</td><td align=right></td>
						</tr>
					</thead>
					<thead>
						<tr style='background:#cccccc;height:20px;'>
							<td align=left colspan=6><b>Thành viên chủ chốt</b></td>
						</tr>
					</thead>
					<tbody>
					</tbody>
					<thead>
						<tr style='background:#cccccc;height:20px;'>
							<td align=left colspan=6><b>Nghiên cứu sinh, học viên cao học, sinh viên</b></td>
						</tr>
					</thead>
					<tbody>
					</tbody>
				</table>
				
				<div style='margin: 5px 0 5px 0;' ><em>Thời gian (tháng) mà mỗi thành viên thực sự làm việc cho đề tài (quy đổi toàn thời gian). 
				Ví dụ: một ngày làm việc cho đề tài 4 tiếng thì 2 ngày tính bằng 1 ngày; một tháng làm việc cho đề tài 10 ngày thì 3 tháng như vậy
				tính bằng 1 tháng. Khi lập dự toán kinh phí đề tài, thời gian thực tế làm việc cho đề tài của mỗi thành viên được tính tối đa là 70%.
				Nếu thời gian thực hiện đề tài là 24 tháng thì thời gian tham gia đề tài của mỗi thành viên khi lập dự toán kinh phí không vượt quá 17 tháng</em></div>
			</form>
		</div>
	</div>

</div>

<div id=khcn_diag_edit_dtkhcn_motanghiencuu style='width:650px;' title="Mô tả nghiên cứu - Thuyết minh đề tài KH&CN">

	<div id="khcn_tabs_thuyetminh_motanghiencuu">
		<ul>
			<li><a href="#tabs-B1" title='<b>Tổng quan tình hình nghiên cứu trong, ngoài nước</b>' class=khcn_tooltips>B1</a></li>
			<li><a href="#tabs-B2" title='<b>Ý tưởng khoa học, tính cấp thiết và tính mới</b>' class=khcn_tooltips>B2</a></li>
			<li><a href="#tabs-B3" title='<b>Kết quả nghiên cứu sơ khởi</b> (nếu có)' class=khcn_tooltips>B3</a></li>
			<li><a href="#tabs-B4" title='<b>Tài liệu tham khảo</b>' class=khcn_tooltips>B4</a></li>
			<li><a href="#tabs-B5_1" title='Kế hoạch & phương pháp nghiên cứu / <b>Mục tiêu</b>' class=khcn_tooltips>B5.1</a></li>
			<li><a href="#tabs-B5_2" title='Kế hoạch & phương pháp nghiên cứu / <b>Nội dung</b>' class=khcn_tooltips>B5.2</a></li>
			<li><a href="#tabs-B5_3" title='Kế hoạch & phương pháp nghiên cứu / <b>Phương án phối hợp</b>' class=khcn_tooltips>B5.3</a></li>
			<li><a href="#tabs-B6_1" title='Kết quả nghiên cứu / <b>Ấn phẩm khoa học</b>' class=khcn_tooltips>B6.1</a></li>
			<li><a href="#tabs-B6_2" title='Kết quả nghiên cứu / <b>Đăng ký sở hữu trí tuệ</b>' class=khcn_tooltips>B6.2</a></li>
			<li><a href="#tabs-B6_3" title='Kết quả nghiên cứu / <b>Kết quả đào tạo</b>' class=khcn_tooltips>B6.3</a></li>
			<li><a href="#tabs-B7"  title='<b>Khả năng ứng dụng kết quả nghiên cứu</b>' class=khcn_tooltips>B7</a></li>
			<li><a href="#tabs-B8" title='<b>Tổng hợp kinh phí đề nghị ĐHQG-HCM cấp</b>' class=khcn_tooltips>B8</a></li>
		</ul>
		<div id="tabs-B1">
			<form id=khcn_frm_edit_dtkhcn_B1 name=khcn_frm_edit_dtkhcn_B1 >
				<div style='margin: 0px 0 10px 0;' class=heading ><span class='khcn_tooltips' title="Trên cơ sở đánh giá tình hình nghiên cứu trong và ngoài nước, phân tích những công trình nghiên cứu, những kết quả mới nhất có liên quan đến đề tài, đánh giá những khác biệt về trình độ KH&CN trong nước và thế giới, những vấn đề đã được giải quyết, cần nêu rõ những vấn đề còn tồn tại">Tổng quan tình hình nghiên cứu trong, ngoài nước</span></div>
				<div style='width:100%; '>
					<textarea class="ckeditor" id="khcn_frm_edit_dtkhcn_tq_tinh_hinh_nc"  name="khcn_frm_edit_dtkhcn_tq_tinh_hinh_nc" style='width:100%;'></textarea>
				</div>
			</form>
		</div>
		<div id="tabs-B2">
			<form id=khcn_frm_edit_dtkhcn_B2 name=khcn_frm_edit_dtkhcn_B2 >
				<div style='margin: 0px 0 10px 0;' class=heading ><span class='khcn_tooltips' title="Chỉ ra những hạn chế cụ thể trình độ KH&CN trong nước và thế giới, từ đó nêu được hướng giải quyết mới - luận giải mục tiêu đặt ra của đề tài và tính cấp thiết, lợi ích của kết quả nghiên cứu đối với ngành, đối với tổ chức chủ trì, đối với xã hội">Ý tưởng khoa học, tính cấp thiết và tính mới</span></div>
				<div style='width:100%; '>
					<textarea class="ckeditor" id="khcn_frm_edit_dtkhcn_y_tuong_kh"  name="khcn_frm_edit_dtkhcn_y_tuong_kh" style='width:100%;'></textarea>
				</div>
			</form>
		</div>
		<div id="tabs-B3">
			<form id=khcn_frm_edit_dtkhcn_B3 name=khcn_frm_edit_dtkhcn_B3 >
				<div style='margin: 0px 0 10px 0;' class=heading ><span class='khcn_tooltips' title="Trước khi đệ trình đề cương này, nhóm nghiên cứu có thể đã thực hiện những nghiên cứu sơ khởi, nếu có thì trình bày kết quả và kỹ thuật sử dụng">Kết quả nghiên cứu sơ khởi (nếu có)</span></div>
				<div style='width:100%; '>
					<textarea class="ckeditor" id="khcn_frm_edit_dtkhcn_kq_nc_so_khoi"  name="khcn_frm_edit_dtkhcn_kq_nc_so_khoi" style='width:100%;'></textarea>
				</div>
			</form>
		</div>
		<div id="tabs-B4">
			<form id=khcn_frm_edit_dtkhcn_B4 name=khcn_frm_edit_dtkhcn_B4 >
				<div style='margin: 0px 0 10px 0;' class=heading ><span class='khcn_tooltips' title="Tên công trình, tác giả, nơi và năm công bố, chỉ nêu những danh mục đã được trích dẫn trong thuyết minh này">Tài liệu tham khảo</span></div>
				<div style='width:100%; '>
					<textarea class="ckeditor" id="khcn_frm_edit_dtkhcn_tai_lieu_tk"  name="khcn_frm_edit_dtkhcn_tai_lieu_tk" style='width:100%;'></textarea>
				</div>
			</form>
				
			<form id=khcn_frm_edit_dtkhcn_B4_1 name=khcn_frm_edit_dtkhcn_B4_1 >
				<div style='margin: 10px 0 10px 0;'  ><span class=heading title="">Giới thiệu chuyên gia/nhà khoa học am hiểu đề tài này</span> (không bắt buộc)</div>
				<div style='margin: 5px 0 5px 0;' align=left><button id=khcn_frm_edit_dtkhcn_btn_open_dlg_add_chuyengia>Thêm chuyên gia/nhà khoa học</button></div>
				<table id=khcn_frm_edit_dtkhcn_B4_1_table_chuyengia class='ui-widget ui-widget-content ui-corner-top tableData' style='width:100%'>
					<thead>
						<tr class='ui-widget-header heading' style='height:20px;'>
							<td align=left>TT</td><td align=left style='width:130px'>Họ và tên</td><td align=left>Hướng nghiên cứu chuyên sâu</td><td align=left >Cơ quan công tác</td><td>Địa chỉ</td><td align=left >Điện thoại, Email</td><td align=right></td>
						</tr>
					</thead>
					<tbody></tbody>
				</table>
			</form>
			
		</div>
		<div id="tabs-B5_1">
			<form id=khcn_frm_edit_dtkhcn_B5_1 name=khcn_frm_edit_dtkhcn_B5_1 >
				<div style='margin: 0px 0 10px 0;' class=heading ><span class='khcn_tooltips' title="">Kế hoạch và phương pháp nghiên cứu</span></div>
				<div style='margin: 0px 0 10px 0;' class=heading ><span class='khcn_tooltips' title="Nói rõ mục tiêu khoa học/công nghệ mà đề tài hướng tới và mức độ giải quyết - Bám sát và cụ thể hóa định hướng mục tiêu theo đặt hàng - nếu có">Mục tiêu (Tiếng Việt)</span> <font color=red> *</font></div>
				<div style='width:100%; '>
					<textarea rows='10' class="ckeditor" id="khcn_frm_edit_dtkhcn_muc_tieu_nc_vn"  name="khcn_frm_edit_dtkhcn_muc_tieu_nc_vn" style='width:100%;'></textarea>
				</div>
			
				<div style='margin: 10px 0 10px 0;' class=heading ><span class='khcn_tooltips' title="Nói rõ mục tiêu khoa học/công nghệ mà đề tài hướng tới và mức độ giải quyết - Bám sát và cụ thể hóa định hướng mục tiêu theo đặt hàng - nếu có">Mục tiêu (English)</span> <font color=red> *</font></div>
				<div style='width:100%; '>
					<textarea rows='9' class="ckeditor" id="khcn_frm_edit_dtkhcn_muc_tieu_nc_en"  name="khcn_frm_edit_dtkhcn_muc_tieu_nc_en" style='width:100%;'></textarea>
				</div>
				<div style='margin-top:10px' align="center" id="khcn_b5_1_tips" class="validateTips"></div>
			</form>
		</div>
		<div id="tabs-B5_2">
			<form id=khcn_frm_edit_dtkhcn_B5_2 name=khcn_frm_edit_dtkhcn_B5_2 >
				<div style='margin: 0px 0 10px 0;' class=heading ><span class='khcn_tooltips' title="<div align=left>Liệt kê và mô tả chi tiết nội dung nghiên cứu<p><b>Nội dung 1:</b><br><b>Mục tiêu nội dung 1</b> (Bám sát và định hướng theo mục tiêu chung...)<br><b>Chỉ tiêu đánh giá</b> (sản phẩm của nội dung 1: ấn phẩm khoa học, đăng ký sỡ hữu trí tuệ,...)<br><b>Kế hoạch thực hiện</b> (Mô tả các hoạt động, giới hạn đối tượng, ý nghĩa, phân công trách nhiệm từng thành viên, sử dụng các nguồn lực và dự kiến các mốc thời gian...)<br><b>Phương pháp</b> (Điểm mới, giới hạn, dự kiến khó khăn, phương án thay thế, quy trình cụ thể...)<br><b>Phân tích và diễn giải số liệu thu được</b></p><p><b>Nội dung 2: ...</b></p></div>">Nội dung</span></div>
				<div style='width:100%; '>
					<textarea class="ckeditor" id="khcn_frm_edit_dtkhcn_noi_dung_nc"  name="khcn_frm_edit_dtkhcn_noi_dung_nc" style='width:100%;'></textarea>
				</div>
			</form>
		</div>
		<div id="tabs-B5_3">
			<form id=khcn_frm_edit_dtkhcn_B5_3 name=khcn_frm_edit_dtkhcn_B5_3 >
				<div style='margin: 0px 0 10px 0;' class=heading ><span class='khcn_tooltips' title="Tên các tổ chức phối hợp và các tổ chức sử dụng kết quả nghiên cứu: Trung tâm CGCN hoặc PTN hoặc các đơn vị trong và ngoài nước; nội dung thực hiện, khả năng đóng góp về nhân lực, tài chính, cơ sở hạ tầng:<br><div align=left><p><b>Phương án phối hợp với các PTN</b><br><b>Phương án phối hợp với các đơn vị</b><br><b>Phương án phối hợp với trung tâm CGCN</b></p></div>">Phương án phối hợp</span></div>
				<div style='width:100%; '>
					<textarea class="ckeditor" id="khcn_frm_edit_dtkhcn_pa_phoi_hop"  name="khcn_frm_edit_dtkhcn_pa_phoi_hop" style='width:100%;'></textarea>
				</div>
			</form>
		</div>
		<div id="tabs-B6_1">			
			<div style='margin: 10px 0 10px 0;'  ><span class=heading title="">Kết quả nghiên cứu</span></div>
			<div style='margin: 0px 0 10px 0;'  ><span class=heading title="">Ấn phẩm khoa học</span></div>
			<div style='margin: 5px 0 5px 0;' align=left><button id=khcn_frm_edit_dtkhcn_btn_open_dlg_add_an_pham_kh>Thêm ấn phẩm khoa học</button></div>
			<table id=khcn_frm_edit_dtkhcn_B6_1_table_an_pham_kh class='ui-widget ui-widget-content ui-corner-top tableData' style='width:100%'>
				<thead>
					<tr class='ui-widget-header heading' style='height:20px;'>
						<td align=left>&nbsp;</td><td align=left style=''>Tên sách/bài báo dự kiến</td><td align=center>Số lượng</td><td align=left >Dự kiến nơi công bố (tên Tạp chí, Nhà xuất bản)</td><td>Ghi chú</td><td align=right></td>
					</tr>
				</thead>
				<?php 
					$sqlstr="select MA_AN_PHAM_KH, TEN_AN_PHAM_KH from NCKH_DM_AN_PHAM_KH"; 
					$stmt = oci_parse($db_conn_khcn, $sqlstr);oci_execute($stmt);$num=oci_fetch_all($stmt, $resDM);oci_free_statement($stmt);
					for ($i=0 ; $i < $num; $i++){
						echo "
							<thead>
								<tr style='background:#cccccc;height:20px;'>
									<td align=left colspan=6><b>{$resDM["TEN_AN_PHAM_KH"][$i]}</b></td>
								</tr>
							</thead>
							<tbody></tbody>
						";
					}
				?>
			</table>
		</div>
		<div id="tabs-B6_2">
			<div style='margin: 0px 0 10px 0;'  ><span class=heading title="">6.2.1 Đăng ký sở hữu trí tuệ</span></div>
			<div style='margin: 5px 0 5px 0;' align=left><button id=khcn_frm_edit_dtkhcn_btn_open_dlg_add_sohuutritue>Thêm hình thức đăng ký sở hữu trí tuệ</button></div>
			<table id=khcn_frm_edit_dtkhcn_B6_2_table_sohuutritue class='ui-widget ui-widget-content ui-corner-top tableData' style='width:100%'>
				<thead>
					<tr class='ui-widget-header heading' style='height:20px;'>
						<td align=left>TT</td><td align=left style=''>Hình thức đăng ký</td><td align=left>Số lượng</td><td align=left >Nội dung dự kiến đăng ký</td><td>Ghi chú</td><td align=right></td>
					</tr>
				</thead>
				<tbody></tbody>
			</table>
			<div style='margin: 10px 0 10px 0;'  ><span class=heading title="">6.2.2 Mô tả sản phẩm / kết quả nghiên cứu</span></div>
			<div style='margin: 0px 0 10px 0;'  ><span class='heading khcn_tooltips' title="Gồm: lý thuyết mới; thuật toán; phương pháp; nguyên lý ứng dụng; mô hình; tiêu chuẩn; quy phạm; bản vẽ thiết kế; quy trình; sơ đồ, bản đồ; số liệu, cơ sở dữ liệu; báo cáo khoa học; tài liệu dự báo; đề án, qui hoạch; luận chứng kinh tế - kỹ thuật; báo cáo nghiên cứu khả thi; phần mềm máy tính; các loại khác">Dạng I: Các sản phẩm mềm</span></div>
			<div style='margin: 5px 0 5px 0;' align=left><button id=khcn_frm_edit_dtkhcn_btn_open_dlg_add_sanphammem>Thêm sản phẩm mềm</button></div>
			<table id=khcn_frm_edit_dtkhcn_B6_2_table_sanphammem class='ui-widget ui-widget-content ui-corner-top tableData' style='width:100%'>
				<thead>
					<tr class='ui-widget-header heading' style='height:20px;'>
						<td align=left>TT</td><td align=left style=''>Tên sản phẩm</td><td align=left>Chỉ tiêu đánh giá (định lượng)</td><td>Ghi chú</td><td align=right></td>
					</tr>
				</thead>
				<tbody></tbody>
			</table>
			
			<div style='margin: 10px 0 10px 0;'  ><span class='heading khcn_tooltips' title="Gồm: mẫu-prototype; vật liệu; thiết bị, máy móc; dây chuyển công nghệ; giống cây trồng; giống vật nuôi; các loại khác">Dạng II: Các sản phẩm cứng</span></div>
			<div style='margin: 5px 0 5px 0;' align=left><button id=khcn_frm_edit_dtkhcn_btn_open_dlg_add_sanphamcung>Thêm sản phẩm cứng</button></div>
			<table id=khcn_frm_edit_dtkhcn_B6_2_table_sanphamcung class='ui-widget ui-widget-content ui-corner-top tableData' style='width:100%'>
				<thead>
					<tr class='ui-widget-header heading' style='height:20px;'>
						<td align=center rowspan=3>TT</td><td align=center rowspan=3 style=''>Tên sản phẩm cụ thể và chỉ tiêu chất lượng chủ yếu của sản phẩm</td><td rowspan=3 align=center>Đơn vị đo</td><td colspan=3 align=center>Mức chất lượng</td><td rowspan=3 align=center>Dự kiến số lượng/quy mô sản phẩm tạo ra</td><td align=right></td>
					</tr>
					<tr class='ui-widget-header heading' style='height:20px;'>
						<td rowspan=2 align=center>Chỉ tiêu đánh giá (định lượng)</td><td colspan=2 align=center>Mẫu tương tự<br><span style='font-weight: normal'>(theo các tiêu chuẩn mới nhất)</span></td><td align=right></td>
					</tr>
					<tr class='ui-widget-header heading' style='height:20px;'>
						<td align=center>Trong nước</td><td align=center>Thế giới</td><td align=right></td>
					</tr>
				</thead>
				<tbody></tbody>
			</table>
			<form id=khcn_frm_edit_dtkhcn_B6_2 name=khcn_frm_edit_dtkhcn_B6_2 >
				<div style='margin: 10px 0 10px 0;'  ><span class='heading khcn_tooltips'  title="Làm rõ cơ sở khoa học và thực tiễn để xác định các chỉ tiêu về chất lượng cần đạt của các sản phẩm">Mức chất lượng các sản phẩm dạng II so với các sản phẩm tương tự trong nước và thế giới</span></div>
				<div style='margin:0 0 5px 0;width:100%;'>
					<textarea rows='5' id='khcn_frm_reg_sanphamcung_mucchatluong' name='khcn_frm_reg_sanphamcung_mucchatluong' maxlength=2000 style='width:100%;' placeholder='Mức chất lượng các sản phẩm dạng II'  title='Làm rõ cơ sở khoa học và thực tiễn để xác định các chỉ tiêu về chất lượng cần đạt của các sản phẩm' class='khcn_tooltips'></textarea>
				</div>
			</form>
		</div>
		<div id="tabs-B6_3">
			<div style='margin: 0px 0 10px 0;'  ><span class=heading title="">Kết quả đào tạo</span></div>
			<div style='margin: 5px 0 5px 0;' align=left><button id=khcn_frm_edit_dtkhcn_btn_open_dlg_add_ketquadaotao>Thêm kết quả đào tạo</button></div>
			<table id=khcn_frm_edit_dtkhcn_B6_3_table_ketquadaotao class='ui-widget ui-widget-content ui-corner-top tableData' style='width:100%'>
				<thead>
					<tr class='ui-widget-header heading' style='height:20px;'>
						<td align=left>TT</td><td align=left style=''>Cấp đào tạo</td><td align=center>Số lượng</td><td align=left >Nhiệm vụ được giao trong đề tài</td><td align=right>Dự kiến kinh phí<br><span style='font-weight: normal'>(Triệu đồng)</span></td><td align=right></td>
					</tr>
				</thead>
				<tbody></tbody>
			</table>
		</div>
		<div id="tabs-B7">
			<form id=khcn_frm_edit_dtkhcn_B7 name=khcn_frm_edit_dtkhcn_B7 >
				<div style='margin: 0px 0 10px 0;' class=heading ><span class='khcn_tooltips' title="">Khả năng ứng dụng kết quả nghiên cứu</span></div>
				<div style='margin: 0px 0 10px 0;' class=heading >
					<span class='khcn_tooltips' title="Nêu những đóng góp vào lĩnh vực khoa học và công nghệ ở trong nước và quốc tế, đóng góp mới, 
					mở ra hướng nghiên cứu mới thông qua các công trình công bố ở trong và ngoài nước">7.1 Khả năng ứng dụng trong lĩnh vực đào tạo, nghiên cứu khoa học & công nghệ, chính sách, quản lý ...</span></div>
				<div style='width:100%; '>
					<textarea rows='5' maxlength=2000 id="khcn_frm_edit_dtkhcn_ud_kqnc_lv_dao_tao"  name="khcn_frm_edit_dtkhcn_ud_kqnc_lv_dao_tao" style='width:100%;'></textarea>
				</div>
			
				<div style='margin: 10px 0 10px 0;'><span class='khcn_tooltips' title=""><b>7.2 Khả năng về ứng dụng các kết quả nghiên cứu vào sản xuất kinh doanh, về liên doanh liên kết với các doanh nghiệp, về thị trường</b> (chỉ dành cho loại hình nghiên cứu trển khai)</span></div>
				<div style='width:100%; '>
					<textarea rows='5' maxlength=2000 id="khcn_frm_edit_dtkhcn_ud_kqnc_sxkd"  name="khcn_frm_edit_dtkhcn_ud_kqnc_sxkd" style='width:100%;'></textarea>
				</div>
				
				<div style='margin: 10px 0 10px 0;'>
					<span class='khcn_tooltips' title="Chuyển giao công nghệ trọn gói, chuyển giao công nghệ có đào tạo, chuyển giao theo hình thức trả dần
					theo tỉ lệ % của doanh thu; liên kết với doanh nghiệp để sản xuất hoặc góp vốn với đơn vị phối hợp nghiên cứu hoặc với cơ sở áp dụng 
					kết quả nghiên cứu theo tỉ lệ đã thỏa thuận để cùng triển khai sản xuất; tự thành lập doanh nghiệp trên cơ sở kết quả nghiên cứu
					tạo ra, ..."><b>7.3 Phương thức chuyển giao kết quả nghiên cứu</b> (chỉ dành cho loại hình nghiên cứu trển khai)</span>
				</div>
				<div style='width:100%; '>
					<textarea rows='5' maxlength=2000 id="khcn_frm_edit_dtkhcn_ud_kqnc_chuyen_giao"  name="khcn_frm_edit_dtkhcn_ud_kqnc_chuyen_giao" style='width:100%;'></textarea>
				</div>
			</form>
		</div>
		<div id="tabs-B8">
			<div style='margin: 0px 0 10px 0;'  ><span class=heading title="">Tổng hợp kinh phí đề nghị <span class=khcn_b8_kp_de_nghi_noicap>ĐHQG-HCM</span> cấp</span></div>
			
			<table id=khcn_frm_edit_dtkhcn_B8_table_tonghopkinhphi class='ui-widget ui-widget-content ui-corner-top tableData' style='width:100%'>
				<thead>
					<tr class='ui-widget-header heading' style='height:20px;'>
						<td align=left rowspan=2>TT</td><td align=left  rowspan=2 style=''>Các khoản chi phí</td><td align=center colspan=2>Đề nghị <span class=khcn_b8_kp_de_nghi_noicap>ĐHQG-HCM</span> cấp</td><td align=right></td>
					</tr>
					<tr class='ui-widget-header heading' style='height:20px;'>
						<td align=center>Kinh phí</td><td align=center >Trong đó khoán chi (*)</td><td align=right></td>
					</tr>
				</thead>
				<tbody></tbody>
			</table>
			<div style='margin: 5px 0 5px 0;' align=left><i>(*) Theo quy định tại Thông tư số 93/2006/TTLT/BTC-BKHCN của liên Bộ Tài chính - Bộ Khoa học và Công nghệ ban hành ngày 04/10/2006 và Thông tư số 44/2007/TTLT/BTC-BKHCN của liên Bộ Tài chính - Bộ Khoa học và Công nghệ ban hành ngày 07/5/2007.</i></div>
			
			<div style='margin-top: 10px;'>	
				<font color=red>
					Tải <b>Phụ lục giải trình các khoản chi</b> tại đây <a id="khcn_b8_phuluc_khoanchi" href="./khcn/templ/R01_phu_luc_giai_trinh_khoan_chi.docx" target=_blank><font color=green><b>>>></b></font></a><br> 
				</font>
			</div>
			
			<div style='display: block; margin: 10px auto; background: #eee; border-radius: 10px; padding: 15px;'>
				<div style='margin: 0 0 5px 0;' align=left class=heading>PHỤ LỤC: GIẢI TRÌNH CÁC KHOẢN CHI</div>
				<table style='width:100%'>
					<tr>
						<td align=left style='width:17%'><button id=khcn_frm_edit_dtkhcn_btn_open_file_khoanchiphi title="Kích thước file upload tối đa là <b>1MB</b>" class="khcn_tooltips">Đính file phụ lục</button></td>
						<td align=left style='width:60%'>
							<div id="progress">
								<div id="bar"></div>
								<div id="percent" style='font-weight:bold'>0%</div >
							</div>
						</td>
					</tr>
				</table>
				<div style='margin: 10px 0 5px 0px;' align=left>
					* File phụ lục đính kèm: <b><span id=khcn_file_giai_trinh_khoan_chi></span></b>
					
					<form id="khcn_frm_upload_file_khoanchi" action="khcn/khcn_thuyetminhdtkhcn_file_phu_luc_process.php?hisid=<?php echo $_REQUEST["hisid"]; ?>&w=uploadfile" method="post" enctype="multipart/form-data">
						<input type="hidden" name="MAX_FILE_SIZE" value="1048576" />
						<input type="hidden" name="khcn_file_ma_tmdt" id="khcn_file_ma_tmdt" value="" />
						<div style='display:none;'><input type="file" size="60" name="khcn_file" id="khcn_file"  onchange="khcn_userfile_change(this)"></div>
					</form>
					 
				</div>
			</div>
		</div>
	</div>

</div>

<div id=khcn_diag_confirm_hoantat_tmdt style='width:100%;' title="Hoàn tất thuyết minh đề tài">
	<div style='margin:5px 5px 5px 0;width:100%;'>
	Xác nhận bạn muốn <b>hoàn tất đăng ký</b> cho thuyết minh đề tài số <span id=khcn_diag_confirm_hoantat_tmdt_id class=heading></span> ? 
	</div>
	<div style='margin:5px 5px 5px 0;width:100%;'>
		Lưu ý: <b>Một mẫu LLKH</b> của <b>chủ nhiệm đề tài</b> và <b>đồng chủ nhiệm</b> sẽ được gửi kèm và bạn sẽ <b><font color=red>không thể chỉnh sửa đề tài</font></b> sau khi hoàn tất đăng ký.
	</div>
	<input type=hidden id='khcn_diag_confirm_hoantat_tmdt_llkh'>
</div>

<div id=khcn_diag_confirm_delete_tmdt style='width:100%;' title="Xoá thuyết minh đề tài">
	<div style='margin:5px 5px 5px 0;width:100%'>
	Xác nhận bạn muốn <b><font color=red>Xoá</font></b> thuyết minh đề tài số <span id=khcn_diag_confirm_delete_tmdt_id class=heading style="color:red"></span> ? 
	</div>
	<input type=hidden id='khcn_diag_confirm_delete_tmdt_llkh'>
</div>

<div id=khcn_diag_capdetai style='width:100%;' title="Đăng ký đề tài">
		<?php
		$sqlstr="select c.*, (sysdate - dkdt_ngay_bd) bat_dau, (sysdate-dkdt_ngay_kt) het_han, 
		to_char(c.DKDT_NGAY_BD, 'dd/mm/yyyy') ngay_bd, to_char(c.DKDT_NGAY_KT, 'HH24:MI dd/mm/yyyy') ngay_kt
		from cap_de_tai c where (sysdate - dkdt_ngay_bd) > 0 and (sysdate-dkdt_ngay_kt)<0";
		$stmt = oci_parse($db_conn_khcn, $sqlstr);oci_execute($stmt);$n=oci_fetch_all($stmt, $resDM);oci_free_statement($stmt);
		$count=0;
		if ($n>0){
			echo '
			<span style="margin:5px 0 5px 0; font-weight:bold;">Vui lòng chọn cấp đề tài</span>
			<div style="margin:5px 5px 5px 0;width:100%">
			<ul id="sortableTTCN">';
			for ($i=0 ; $i < $n; $i++){
				$macap = $resDM["MA_CAP"][$i];
				$tencapdt = $resDM["TEN_CAP"][$i];
				$ngaybd =  $resDM["NGAY_BD"][$i];
				$ngaykt =  $resDM["NGAY_KT"][$i];
				echo "<li ><a href='#' onclick=\"$('#khcn_frm_reg_dtkhcn_tencapdetai').html('$tencapdt'); $('#khcn_frm_reg_dtkhcn_capdetai').val('$macap');khcn_reset_fields_reg();$('#khcn_diag_reg_dtkhcn').dialog('open'); $('#khcn_diag_capdetai').dialog('close');\"><img border='0' width='48' height='48' src='icons/Hire-me-icon.png' /><br><b>$tencapdt</b><br><i>{$ngaybd} →<font color=red>$ngaykt</font></i></a></li>";
			}
			echo '
			</ul>
			</div>';
		}else{
			echo "<div style='color:red; font-weight:bold;margin:5px 5px 5px 0;width:100%' align='center'>Hiện tại chưa đến đợt đăng ký đề tài. Bạn vui lòng quay lại sau.</div>";
		}
		?>
</div>

<style>

#progress, #progress1 { position:relative; width:100%; border: 1px solid #ddd; padding: 1px; border-radius: 3px; }
#bar, #bar1 { background-color: #B4F5B4; width:0%; height:20px; border-radius: 3px; }
#percent, #percent1 { position:absolute; display:inline-block; top:3px; left:48%; }

</style>

<script type="text/javascript">
var oTableThuyetMinhDTKHCN;
var khcn_linkdata = "khcn/khcn_thuyetminhdtkhcn_process.php?hisid=<?php echo $_REQUEST["hisid"]; ?>";
var khcn_matm_selected = null, bValid=true;
var khcn_numnganh = <?php echo "$numNganh";?>;
var khcn_class = 'alt_';
var khcn_formA1A4_changed = false, khcn_formA5_changed = false, khcn_formA6_changed = false, khcn_formA7A8_changed = false, khcn_formB1_changed = false;
var khcn_formB2_changed = false, khcn_formB3_changed = false, khcn_formB4_changed = false, khcn_formB5_1_changed = false;
var khcn_formB5_2_changed = false, khcn_formB5_3_changed = false, khcn_formB6_1_changed = false, khcn_formB6_2_changed = false, khcn_formB7_changed = false;
var khcn_nTr_selected;
var khcn_tmdt_col_idx = new Array(); 
 khcn_tmdt_col_idx['madt'] 			= 0;
 khcn_tmdt_col_idx['tendt'] 		= 1;
 khcn_tmdt_col_idx['nhomnganh']		= 2;
 khcn_tmdt_col_idx['huongdt']		= 3;
 khcn_tmdt_col_idx['keywords']		= 4;
 khcn_tmdt_col_idx['capdetai']		= 5;
 khcn_tmdt_col_idx['tenbomon']		= 6;
 khcn_tmdt_col_idx['loaihinhnc']	= 7;
 khcn_tmdt_col_idx['thoigian']		= 8;
 khcn_tmdt_col_idx['kinhphi']		= 9;
 khcn_tmdt_col_idx['guitmdt']		= 10;
 khcn_tmdt_col_idx['trangthai']		= 11;
 khcn_tmdt_col_idx['xoatmdt']		= 12;
 khcn_tmdt_col_idx['in']			= 13;
 khcn_tmdt_col_idx['matrangthai'] 	= 14;
 khcn_tmdt_col_idx['editallow'] 	= 15;
 khcn_tmdt_col_idx['dcndt_mcb'] 	= 16;
 khcn_tmdt_col_idx['batdau_dkdt'] 	= 17;
 khcn_tmdt_col_idx['hethan_dkdt'] 	= 18;
 
$(document).ready(function() {
	$('input[placeholder],textarea[placeholder]').placeholder();
    $("#khcn_frm_edit_dtkhcn_cndt_ngay_cap, #khcn_frm_edit_dtkhcn_dcndt_ngay_cap, #khcn_frm_edit_dtkhcn_cndt_ngay_sinh, #khcn_frm_edit_dtkhcn_dcndt_ngay_sinh").mask("99/99/9999");

	$("#khcn_reg_button" ).button({ icons: {primary:'ui-icon ui-icon-pencil'} });
	$("#khcn_frm_edit_dtkhcn_btn_open_dlg_add_nhanluc, #khcn_frm_edit_dtkhcn_btn_open_dlg_add_chuyengia, #khcn_frm_edit_dtkhcn_btn_open_dlg_add_an_pham_kh, #khcn_frm_edit_dtkhcn_btn_open_dlg_add_sohuutritue, #khcn_frm_edit_dtkhcn_btn_open_dlg_add_sanphammem, #khcn_frm_edit_dtkhcn_btn_open_dlg_add_sanphamcung, #khcn_frm_edit_dtkhcn_btn_open_dlg_add_ketquadaotao" ).button({ icons: {primary:'ui-icon ui-icon-plusthick'} });
	$("#khcn_frm_edit_dtkhcn_btn_open_file_khoanchiphi, #khcn_frm_edit_dtkhcn_btn_open_file_minhchung" ).button({ icons: {primary:'ui-icon ui-icon-arrowthick-1-n'} });
	
	$("#khcn_edit_ttchung_button, #khcn_edit_mota_button").button();
	
	//$('#khcn_frm_edit_dtkhcn_kinhphi_tuco, #khcn_frm_edit_dtkhcn_kinhphi_khac').autoNumeric('init');
	//$('.khcn_autonumbers').autoNumeric('init', {'wEmpty': 'zero'}); aSep: '.', aDec: ','
	$('.khcn_autonumbers').autoNumeric('init', {'wEmpty': 'zero', aSep: '.', aDec: ','}); 
	
	$('#khcn_frm_edit_dtkhcn_btn_open_file_khoanchiphi').click( function() {
		$('#khcn_file_ma_tmdt').val(khcn_matm_selected);
		document.getElementById("khcn_file").click();
	});
	
	$('#khcn_frm_edit_dtkhcn_btn_open_file_minhchung').click( function() {
		$('#khcn_file_vonkhac_ma_tmdt').val(khcn_matm_selected);
		document.getElementById("khcn_file_vonkhac").click();
	});
	
	$(".fancybox-effects-d").fancybox({
		padding: 0,

		openEffect : 'elastic',
		openSpeed  : 150,

		closeEffect : 'elastic',
		closeSpeed  : 150,

		closeClick : true,

		helpers : {
			overlay : null
		}
	});
	
	$('#khcn_edit_ttchung_button').button("disable");
	$('#khcn_edit_mota_button').button("disable");
	
	$('#khcn_reg_button').click( function() {
		khcn_checksession().done(function(data){
			if (data.success != 1){
				gv_open_msg_box("<font style='color:red;'>Không thể đăng ký đề tài vì:</font> <br/><div style='margin: 5px 0 0 5px'>" + reverse_escapeJsonString(data.msgerr) +'</div>', 'alert', 250, 180, true);
				return;
			}else{
				/* khcn_reset_fields_reg();
				
				$("#khcn_diag_reg_dtkhcn_btn_reg").button("enable");
				$('#khcn_diag_reg_dtkhcn').dialog('open'); */
				
				$('#khcn_diag_capdetai').dialog('open');
			}
		});
	});	
	
	$('#khcn_edit_ttchung_button').click( function() {
		var nTr = khcn_fnGetSelected(oTableThuyetMinhDTKHCN);
		if (nTr.length !== 0){
			khcn_matm_selected = nTr[0].cells[0].innerHTML;
			var aData = oTableThuyetMinhDTKHCN.fnGetData( nTr[0] );
			
			//alert(parseFloat(aData[khcn_tmdt_col_idx['hethan_dkdt']]));
			if ((aData[khcn_tmdt_col_idx['matrangthai']]=='021' || (parseFloat(aData[khcn_tmdt_col_idx['batdau_dkdt']]) > 0 && parseFloat(aData[khcn_tmdt_col_idx['hethan_dkdt']]) < 0) ) && aData[khcn_tmdt_col_idx['editallow']]=='1'){
				khcn_GetThuyetMinh_ThongTinChung(khcn_matm_selected);
			}
			else{
				$('#khcn_edit_ttchung_button').button("disable");
				$('#khcn_edit_mota_button').button("disable");
				gv_open_msg_box("Thuyết minh đề tài này không thể chỉnh sửa.", 'info', 250, 180);
			}
			
		}
		else
			gv_open_msg_box("Vui lòng chọn thuyết minh đề tài bằng cách click vào thuyết minh đề tài ở dưới.", 'info', 250, 180);
	});
	
	$('#khcn_edit_mota_button').click( function() {
		var nTr = khcn_fnGetSelected(oTableThuyetMinhDTKHCN);		
		if (nTr.length !== 0){
			khcn_reset_fields_edit_mota();
			khcn_matm_selected = nTr[0].cells[0].innerHTML;
			var aData = oTableThuyetMinhDTKHCN.fnGetData( nTr[0] );
			
			if (aData[khcn_tmdt_col_idx['editallow']]==1){
				khcn_GetThuyetMinh_MoTaNghienCuu(khcn_matm_selected);
			}else{
				gv_open_msg_box("Thuyết minh đề tài này không thể chỉnh sửa được nữa vì đã hoàn tất thuyết minh.", 'info', 250, 180);
			}
			
		}
		else
			gv_open_msg_box("Vui lòng chọn thuyết minh đề tài bằng cách click vào thuyết minh đề tài ở dưới.", 'info', 250, 180);
	});
	
	$('#khcn_frm_edit_dtkhcn_btn_open_dlg_add_nhanluc').click( function() {
		// reset form
		$("#khcn_frm_reg_nhanlucnghiencuu").find('input[type=text], input[type=hidden], textarea, select').val('');
		
		khcn_init_dialog_nhanluc();
		
		$('#khcn_diag_nhanlucnghiencuu').dialog('open');

		return false;
	});
	
	$('#khcn_frm_edit_dtkhcn_btn_open_dlg_add_chuyengia').click( function() {
		// reset form
		$("#khcn_frm_reg_chuyengia").find('input[type=text], input[type=hidden], textarea, select').val('');
		
		$('#khcn_diag_chuyengia').dialog('open');
		//alert($('#khcn_frm_edit_dtkhcn_A9_table_nhanluc thead:eq(1)').html());
		return false;
	});
	
	$('#khcn_frm_edit_dtkhcn_btn_open_dlg_add_an_pham_kh').click( function() {
		// reset form
		$("#khcn_frm_reg_anphamkhoahoc").find('input[type=text], input[type=hidden], textarea, select').val('');
		
		$('#khcn_diag_anphamkhoahoc').dialog('open');
		//alert($('#khcn_frm_edit_dtkhcn_A9_table_nhanluc thead:eq(1)').html());
		return false;
	});
	
	$('#khcn_frm_edit_dtkhcn_btn_open_dlg_add_sohuutritue').click( function() {
		// reset form
		$("#khcn_frm_reg_sohuutritue").find('input[type=text], input[type=hidden], textarea, select').val('');
		
		$('#khcn_diag_sohuutritue').dialog('open');
		//alert($('#khcn_frm_edit_dtkhcn_A9_table_nhanluc thead:eq(1)').html());
		return false;
	});
	
	$('#khcn_frm_edit_dtkhcn_btn_open_dlg_add_sanphammem').click( function() {
		// reset form
		$("#khcn_frm_reg_sanphammem").find('input[type=text], input[type=hidden], textarea, select').val('');
		
		$('#khcn_diag_sanphammem').dialog('open');
		
		return false;
	});
	
	$('#khcn_frm_edit_dtkhcn_btn_open_dlg_add_sanphamcung').click( function() {
		// reset form
		$("#khcn_frm_reg_sanphamcung").find('input[type=text], input[type=hidden], textarea, select').val('');
		
		$('#khcn_diag_sanphamcung').dialog('open');
		
		return false;
	});
	
	$('#khcn_frm_edit_dtkhcn_btn_open_dlg_add_ketquadaotao').click( function() {
		// reset form
		$("#khcn_frm_reg_ketquadaotao").find('input[type=text], input[type=hidden], textarea, select').val('');
		$('#khcn_diag_ketquadaotao').dialog('open');
		return false;
	});
	
	$('#khcn_frm_edit_dtkhcn_btn_open_dlg_add_khoanchiphi').click( function() {
		// reset form
		$("#khcn_frm_reg_tonghopkinhphi").find('input[type=text], input[type=hidden], textarea, select').val('');
		$('#khcn_diag_tonghopkinhphi').dialog('open');
		return false;
	});
	
	// Lay thong tin CNDT
	$('#khcn_frm_edit_dtkhcn_A6_btn_cndt').click( function() {
		gv_processing_diglog("open","Khoa học & Công nghệ", "Đang xử lý ...");
		dataString = 'a=getllkh&m=<?php echo $shcc; ?>';
		xreq = $.ajax({
		  type: 'POST', dataType: "json", data: dataString,
		  url: khcn_linkdata,
		  success: function(data) {
			gv_processing_diglog("close");
			if (data.success == 1){
				//alert(data.llkh.ho_ten);
				$("#khcn_frm_edit_dtkhcn_cndt_hh_hv_ho_ten").val(reverse_escapeJsonString(data.llkh.ho_ten));
				$("#khcn_frm_edit_dtkhcn_cndt_ngay_sinh").val(reverse_escapeJsonString(data.llkh.ngay_sinh));
				$('input:radio[name=khcn_frm_edit_dtkhcn_cndt_phai][value='+data.llkh.phai+']').attr('checked', true);
				$("#khcn_frm_edit_dtkhcn_cndt_so_cmnd").val(reverse_escapeJsonString(data.llkh.so_cmnd));
				$("#khcn_frm_edit_dtkhcn_cndt_ngay_cap").val(reverse_escapeJsonString(data.llkh.ngay_cap));
				$("#khcn_frm_edit_dtkhcn_cndt_noi_cap").val(reverse_escapeJsonString(data.llkh.noi_cap));
				$("#khcn_frm_edit_dtkhcn_cndt_ms_thue").val(reverse_escapeJsonString(data.llkh.ma_so_thue));
				$("#khcn_frm_edit_dtkhcn_cndt_so_tai_khoan").val(reverse_escapeJsonString(data.llkh.so_tai_khoan));
				$("#khcn_frm_edit_dtkhcn_cndt_ngan_hang").val(reverse_escapeJsonString(data.llkh.ngan_hang_mo_tk));
				$("#khcn_frm_edit_dtkhcn_cndt_dia_chi_cq").val(reverse_escapeJsonString(data.llkh.dia_chi));
				$("#khcn_frm_edit_dtkhcn_cndt_dien_thoai").val(reverse_escapeJsonString(data.llkh.dien_thoai_cn));
				$("#khcn_frm_edit_dtkhcn_cndt_email").val(reverse_escapeJsonString(data.llkh.email));
				
			}else{
				gv_open_msg_box("Chi tiết lỗi: <br/><div style='margin: 5px 0 0 5px'>" + reverse_escapeJsonString(data.msgerr)+'</div>', 'alert', 250, 180, true);
			}
			
		  }
		});
		
		return false;
	});
	
	$("#khcn_diag_reg_dtkhcn").dialog({
		resizable: false,
		autoOpen: false,
		width:700, height:680,
		modal: true,
		buttons: [
			{
				id: "khcn_diag_reg_dtkhcn_btn_reg",
				text: "Lưu đề tài",
				click: function() {
					bValid = true;
					var bNganhkhac=true,
					khcn_reg_capdt			= $("#khcn_frm_reg_dtkhcn_capdetai"),
					khcn_reg_donvi 			= $("#khcn_frm_reg_dtkhcn_dvdk"),
					khcn_reg_tendt_viet 	= $("#khcn_frm_reg_dtkhcn_ten_dt_viet"),
					khcn_reg_tendt_anh		= $("#khcn_frm_reg_dtkhcn_ten_dt_anh"),
					khcn_reg_keywords		= $("#khcn_frm_reg_dtkhcn_keywords"),
					khcn_reg_huongdt		= $("#khcn_frm_reg_dtkhcn_huongdt"),
					khcn_reg_nganhkhac		= $("#khcn_frm_reg_nganhkhac"),
					khcn_reg_cnganhhep		= $("#khcn_frm_reg_dtkhcn_cnganhhep"),
					khcn_reg_loaihinhnc		= $("#khcn_frm_reg_dtkhcn_loaihinhnc"),
					khcn_reg_thoigianthuchien	= $("#khcn_frm_reg_dtkhcn_thoigianthuchien"),
					khcn_reg_nganh	= $("#khcn_frm_reg_dtkhcn_lbl_nganh"),
					
					khcn_reg_allFields = $([]).add(khcn_reg_tendt_viet).add(khcn_reg_tendt_anh).add(khcn_reg_keywords).add(khcn_reg_huongdt).add(khcn_reg_nganh)
						.add(khcn_reg_donvi).add(khcn_reg_cnganhhep).add(khcn_reg_loaihinhnc).add(khcn_reg_thoigianthuchien).add(khcn_reg_nganhkhac),
					khcn_reg_jtips	= $("#khcn_reg_tips");
					
					khcn_reg_allFields.removeClass( "ui-state-error" );
					
					bValid = bValid && checkLength( khcn_reg_tendt_viet, "\"Tên đề tài tiếng Việt\"", 1, 1000, 0, khcn_reg_jtips);
					bValid = bValid && checkLength( khcn_reg_tendt_anh, "\"Tên đề tài tiếng Anh\"", 1, 1000, 0, khcn_reg_jtips);
					bValid = bValid && checkLength( khcn_reg_keywords, "\"Keywords\"", 1, 500, 0, khcn_reg_jtips);
					bValid = bValid && checkLength( khcn_reg_huongdt, "\"Hướng đề tài\"", 1, 1000, 0, khcn_reg_jtips);
					
					bValid = bValid && checkLength( khcn_reg_donvi, "\"Đơn vị đăng ký\"", 0, 5, 0, khcn_reg_jtips);
					
					if (bValid){
						bValid = false;
						$("#khcn_frm_reg_table_nganh input[type=checkbox]").each(function() {
							if ($(this).attr("checked")=='checked') {
								if ($(this).attr("value")=='999'){
									bValid = checkLength( khcn_reg_nganhkhac, "\"Ngành khác\"", 1, 250, 0, khcn_reg_jtips);
								}else{
									bValid = true;
								}
							}else{
								if ($(this).attr("value")=='999'){
									if (khcn_reg_nganhkhac.val()){
										$(this).attr("checked", "checked");
										bValid = true;
									}
								}
							}
						});
						
						if (!bValid){
							khcn_reg_nganh.addClass( "ui-state-error" );
							updateTips('Vui lòng chọn Ngành - Nhóm ngành',khcn_reg_jtips);
							khcn_reg_nganh.focus();
						}
					}
					
					if (khcn_reg_capdt.val()>20 && khcn_reg_capdt.val()<25){
						bValid = bValid && checkLength( khcn_reg_cnganhhep, "\"Chuyên ngành hẹp\"", 1, 250, 0, khcn_reg_jtips);
					}
					bValid = bValid && checkLength( khcn_reg_loaihinhnc, "\"Loại hình nghiên cứu\"", 0, 10, 0, khcn_reg_jtips);
					bValid = bValid && checkLength( khcn_reg_thoigianthuchien, "\"Thời gian thực hiện\"", 1, 3, 0, khcn_reg_jtips);
					
					if (bValid && khcn_reg_capdt.val()>20 && khcn_reg_capdt.val()<25){
						//bValid = bValid && checkLength( khcn_reg_cnganhhep, "\"Chuyên ngành hẹp\"", 1, 250, 0, khcn_reg_jtips);
						if ($("#khcn_frm_reg_doc_qd193").attr("checked")!='checked'){
							bValid = false;
							gv_open_msg_box("<font color=red><b>Bạn phải đọc và đồng ý với Quyết Định 193</b></font>", 'alert', 250, 180, true);
						}
					}
					
					
					if (bValid){
						$("#khcn_diag_reg_dtkhcn_btn_reg").button("disable");
						gv_processing_diglog("open", "Đang xử lý ... vui lòng chờ");
						
						dataString = $("#khcn_frm_reg_dtkhcn").serialize() + '&a=regthuyetminh&hisid=<?php echo $_REQUEST["hisid"]."&c=$numNganh"; ?>';
						xreq = $.ajax({
						  type: 'POST', dataType: "json",
						  url: 'khcn/khcn_thuyetminhdtkhcn_process.php',
						  data: dataString,
						  success: function(data) {
							gv_processing_diglog("close");
							
							if (data.success == 1)
							{
								gv_open_msg_box('Bạn đã <b>đăng ký thành công</b> các thông tin cơ bản.<br>Vui lòng cập nhật tiếp thông tin phần <b>THÔNG TIN CHUNG</b> và <b>MÔ TẢ NGHIÊN CỨU</b> của thuyết minh đề tài bằng cách click vào 2 nút <b>A. Thông tin chung</b> & <b>B. Mô tả nghiên cứu</b> bên dưới.', 'info', 300, 250, true);
								khcn_RefreshTableThuyeMinh(oTableThuyetMinhDTKHCN,khcn_linkdata);
								$( "#khcn_diag_reg_dtkhcn" ).dialog( "close" );
							}
							else
							{
								gv_open_msg_box("<font color=red>Có lỗi trong quá trình đăng ký, chi tiết lỗi: <br/><div style='margin: 5px 0 0 5px'>" + data.msgerr+'</div></font>', 'alert', 250, 180, true);
							}
						  },
						  error: function(xhr, ajaxOptions, thrownError) {}
						});
						
					}
					else
						return false;
				}
			},
			{
				id: "khcn_diag_reg_dtkhcn_btn_close",
				text: "Đóng",
				click: function() {
					$("#khcn_diag_reg_dtkhcn_btn_reg").button("enable");
					$( this ).dialog( "close" );
				}
			}
		]
	});
	
	$("#khcn_diag_edit_dtkhcn_thongtinchung").dialog({
		resizable: false,
		autoOpen: false,
		width:700, height:700,
		modal: true,
		buttons: [
			{
				id: "khcn_diag_edit_dtkhcn_ttc_btn_ok",
				text: "OK",
				click: function() {
					var activeTabIdx = $('#khcn_tabs_thuyetminh_thongtinchung').tabs('option','active');
					var activePanelID = $('#khcn_tabs_thuyetminh_thongtinchung > div').eq(activeTabIdx).attr('id');
					khcn_update_thongtinchung(activePanelID);
					
					if (bValid){
						$( "#khcn_diag_edit_dtkhcn_thongtinchung" ).dialog( "close" );
					}
				}
			},
			{
				id: "khcn_diag_edit_dtkhcn_ttc_btn_cancel",
				text: "Cancel",
				click: function() {
					khcn_formA1A4_changed = false; khcn_formA6_changed = false; khcn_formA7A8_changed = false;
					$("#khcn_diag_edit_dtkhcn_ttc_btn_ok").button("enable");
					$( this ).dialog( "close" );
				}
			}
		]
	});
	
	$("#khcn_diag_edit_dtkhcn_motanghiencuu").dialog({
		resizable: false,
		autoOpen: false,
		width:700, height:630,
		modal: true,
		buttons: [
			{
				id: "khcn_diag_edit_dtkhcn_mtnc_btn_ok",
				text: "OK",
				click: function() {
					// Get the editor data
					var activeTabIdx = $('#khcn_tabs_thuyetminh_motanghiencuu').tabs('option','active');
					var activePanelID = $('#khcn_tabs_thuyetminh_motanghiencuu > div').eq(activeTabIdx).attr('id');
					bValid=true;
					
					khcn_update_mota(activePanelID);
					
					if (bValid){
						//$('#khcn_edit_ttchung_button').button("disable");
						//$('#khcn_edit_mota_button').button("disable");
						
						$( this ).dialog( "close" );
					}
				}
			},
			{
				id: "khcn_diag_edit_dtkhcn_mtnc_btn_cancel",
				text: "Cancel",
				click: function() {
					
					$( this ).dialog( "close" );
				}
			}
		]
	});
	
	$("#khcn_diag_nhanlucnghiencuu").dialog({
		resizable: false,
		autoOpen: false,
		width:500, height:480,
		modal: true,
		buttons: [
			{
				id: "khcn_diag_nhanlucnghiencuu_btn_ok",
				text: "OK",
				click: function() {
					// Check validate
					bValid = true;
					var khcn_a9_shcc 			= $("#khcn_frm_reg_nhanlucnghiencuu_shcc"),
						khcn_a9_loainc 			= $("#khcn_frm_reg_nhanlucnghiencuu_loai"),
						khcn_a9_masv			= $("#khcn_frm_reg_nhanlucnghiencuu_masv"),
						khcn_a9_hoten			= $("#khcn_frm_reg_nhanlucnghiencuu_hh_hv_ho_ten"),
						khcn_a9_hoten_sv		= $("#khcn_frm_reg_nhanlucnghiencuu_ho_ten_sv"),
						khcn_a9_donvicongtac	= $("#khcn_frm_reg_nhanlucnghiencuu_don_vi_cong_tac"),
						khcn_a9_thangquydoi		= $("#khcn_frm_reg_nhanlucnghiencuu_so_thang_lv_quy_doi"),

						khcn_a9_allFields = $([]).add(khcn_a9_shcc).add(khcn_a9_loainc).add(khcn_a9_masv).add(khcn_a9_hoten).add(khcn_a9_donvicongtac).add(khcn_a9_thangquydoi).add(khcn_a9_hoten_sv),
						khcn_a9_jtips	= $("#khcn_a9_tips");
						
					khcn_a9_allFields.removeClass( "ui-state-error" );
					
					bValid = bValid && checkLength( khcn_a9_loainc, "\"Nhân lực nghiên cứu\"", 0, 5, 0, khcn_a9_jtips);
					if (khcn_a9_loainc.val() == '1'){
						bValid = bValid && checkLength( khcn_a9_hoten, "\"Họ tên thành viên\"", 0, 100, 0, khcn_a9_jtips);
					}else if (khcn_a9_loainc.val() == '2'){
						bValid = bValid && checkLength( khcn_a9_masv, "\"Mã số NCS, học viên, sinh viên\"", 0, 10, 0, khcn_a9_jtips);
						bValid = bValid && checkLength( khcn_a9_hoten_sv, "\"Họ tên NCS, học viên, sinh viên\"", 0, 100, 0, khcn_a9_jtips);
					}
					
					bValid = bValid && checkLength( khcn_a9_donvicongtac, "\"Đơn vị công tác\"", 0, 200, 0, khcn_a9_jtips);
					bValid = bValid && checkLength( khcn_a9_thangquydoi, "\"Đơn vị công tác\"", 0, 200, 0, khcn_a9_jtips);
					
					if (bValid ){
						if ($("#khcn_frm_reg_nhanlucnghiencuu_manl").val()==""){
							gv_processing_diglog("open","Khoa học & Công nghệ", "Đang lưu dữ liệu ...");
							dataString = $("#khcn_frm_reg_nhanlucnghiencuu").serialize() + '&a=addnhanlucnc&m='+khcn_matm_selected;
							xreq = $.ajax({
								type: 'POST', dataType: "json", data: dataString,
								url: khcn_linkdata,
								success: function(data) {
									gv_processing_diglog("close");
									if (data.success == 1){
										if (data.loainhanluc==2){
											ho_ten = data.ho_ten_sv;
											shcc_masv=data.ma_sv;
										}else {
											ho_ten = data.ho_ten;
											shcc_masv=data.shcc;
										}
											
										$( "#khcn_frm_edit_dtkhcn_A9_table_nhanluc tbody:eq("+(data.loainhanluc-1)+")" ).append( "<tr style='font-size:12px; border-color: #000000; border-width: 1px; border-style: solid; border-collapse:collapse;' >" +
											"<td align=left>" + data.ma_nhan_luc + "</td>" +
											"<td align=left>" + ho_ten + "</td>" +
											"<td align=left>" + shcc_masv + "</td>" +
											"<td align=left>" + data.don_vi_cong_tac + "</td>" +
											"<td align=center>" + data.so_thang + "</td>" +
											"<td><button title='Xoá' class='khcn_nhanluc_remove' style='height:25px;width:30px;' onclick='khcn_remove_nhanluc( khcn_getRowIndex(this), "+data.loainhanluc+" ); return false;'></button> <button title='Sửa' class='khcn_nhanluc_edit' style='height:25px;width:30px;' onclick='khcn_edit_nhanluc( khcn_getRowIndex(this), "+data.loainhanluc+" ); return false;'></button></td>" +
											"</tr>" );
											
										$("button.khcn_nhanluc_remove" ).button({ icons: {primary:'ui-icon ui-icon-trash'} });
										$("button.khcn_nhanluc_edit" ).button({ icons: {primary:'ui-icon ui-icon-pencil'} });
										
										$( "#khcn_diag_nhanlucnghiencuu" ).dialog( "close" );
									}else{
										gv_open_msg_box("Có lỗi trong quá trình cập nhật dữ liệu.<p>Chi tiết lỗi: <br/><div style='margin: 5px 0 0 5px'>" + data.msgerr+'</div></p>', 'alert', 250, 180, true);
									}
								}
							});
							//$( this ).dialog( "close" );
						}else{
							gv_processing_diglog("open","Khoa học & Công nghệ", "Đang lưu dữ liệu ...");
							dataString = $("#khcn_frm_reg_nhanlucnghiencuu").serialize() + '&a=editnhanlucnc&m='+khcn_matm_selected+'&nlnc='+$("#khcn_frm_reg_nhanlucnghiencuu_loai").val()+'&shcc='+$("#khcn_frm_reg_nhanlucnghiencuu_shcc").val()+'&hoten='+$("#khcn_frm_reg_nhanlucnghiencuu_hh_hv_ho_ten").val();
							xreq = $.ajax({
								type: 'POST', dataType: "json", data: dataString,
								url: khcn_linkdata,
								success: function(data) {
									gv_processing_diglog("close");
									if (data.success == 1){
										if (data.loainhanluc==2){
											ho_ten = data.ho_ten_sv;
											shcc_masv=data.ma_sv;
										}else {
											ho_ten = data.ho_ten;
											shcc_masv=data.shcc;
										}
										
										i = $("#khcn_frm_reg_nhanlucnghiencuu_index").val();
										tbl = document.getElementById('khcn_frm_edit_dtkhcn_A9_table_nhanluc');
	
										tbl.rows[i].cells[0].innerHTML = data.ma_nhan_luc;
										tbl.rows[i].cells[1].innerHTML = ho_ten;
										tbl.rows[i].cells[2].innerHTML = shcc_masv;
										tbl.rows[i].cells[3].innerHTML = data.don_vi_cong_tac;
										tbl.rows[i].cells[4].innerHTML = data.so_thang;
										
										$( "#khcn_diag_nhanlucnghiencuu" ).dialog( "close" );
									}else{
										gv_open_msg_box("Có lỗi trong quá trình cập nhật dữ liệu.<p>Chi tiết lỗi: <br/><div style='margin: 5px 0 0 5px'>" + data.msgerr+'</div></p>', 'alert', 250, 180, true);
									}
								}
							});
						}
					}
				}
			},
			{
				id: "khcn_diag_nhanlucnghiencuu_btn_cancel",
				text: "Cancel",
				click: function() {
					
					$( this ).dialog( "close" );
				}
			}
		]
	});
	
	$("#khcn_diag_chuyengia").dialog({
		resizable: false,
		autoOpen: false,
		width:450, height:270,
		modal: true,
		buttons: [
			{
				id: "khcn_diag_chuyengianghiencuu_btn_ok",
				text: "OK",
				click: function() {
					// Check validate
					bValid = true;
					
					if ($('#khcn_frm_reg_chuyengia_hh_hv_ho_ten').val()==''){
						gv_open_msg_box('<font color=red>Vui lòng nhập Họ và tên</font>', 'alert', 250, 180);
						return false;
					}
					if ($('#khcn_frm_reg_chuyengia_huongnccs').val()==''){
						gv_open_msg_box('<font color=red>Vui lòng nhập Hướng nghiên cứu chuyên sâu</font>', 'alert', 250, 180);
						return false;
					}
					if ($('#khcn_frm_reg_chuyengia_don_vi_cong_tac').val()==''){
						gv_open_msg_box('<font color=red>Vui lòng nhập Đơn vị công tác</font>', 'alert', 250, 180);
						return false;
					}
					if ($('#khcn_frm_reg_chuyengia_diachi').val()==''){
						gv_open_msg_box('<font color=red>Vui lòng nhập Địa chỉ liên lạc</font>', 'alert', 250, 180);
						return false;
					}
					if ($('#khcn_frm_reg_chuyengia_dienthoai').val()==''){
						gv_open_msg_box('<font color=red>Vui lòng nhập Điện thoại liên lạc</font>', 'alert', 250, 180);
						return false;
					}
					if ($('#khcn_frm_reg_chuyengia_email').val()==''){
						gv_open_msg_box('<font color=red>Vui lòng nhập Email liên lạc</font>', 'alert', 250, 180);
						return false;
					}
					
					gv_processing_diglog("open","Khoa học & Công nghệ", "Đang lưu dữ liệu ...");
					dataString = $("#khcn_frm_reg_chuyengia").serialize() + '&a=addchuyengianc&m='+khcn_matm_selected;
					xreq = $.ajax({
					  type: 'POST', dataType: "json", data: dataString,
					  url: khcn_linkdata,
					  success: function(data) {
						gv_processing_diglog("close");
						if (data.success == 1){
							khcn_add_table_chuyengianc(data);
							
							$("button.khcn_chuyengianc_remove" ).button({ icons: {primary:'ui-icon ui-icon-trash'} });
							
							$( "#khcn_diag_chuyengia" ).dialog( "close" );
						}else{
							gv_open_msg_box("Chi tiết lỗi: <br/><div style='margin: 5px 0 0 5px'>" + data.msgerr+'</div>', 'alert', 250, 180, true);
						}
					  }
					});
					//$( this ).dialog( "close" );
				}
			},
			{
				id: "khcn_diag_chuyengianghiencuu_btn_cancel",
				text: "Cancel",
				click: function() {
					
					$( this ).dialog( "close" );
				}
			}
		]
	});
	
	$("#khcn_diag_anphamkhoahoc").dialog({
		resizable: false,
		autoOpen: false,
		width:450, height:270,
		modal: true,
		buttons: [
			{
				id: "khcn_diag_anphamkhoahoc_btn_ok",
				text: "OK",
				click: function() {
					// Check validate
					bValid = true;
					
					if ($('#khcn_frm_reg_anphamkhoahoc_loai').val()==''){
						gv_open_msg_box('<font color=red>Vui lòng chọn ấn phẩm khoa học</font>', 'alert', 250, 180);
						return false;
					}
					if ($('#khcn_frm_reg_anphamkhoahoc_ten_bb_sach_dk').val()==''){
						gv_open_msg_box('<font color=red>Vui lòng nhập Tên sách/Bài báo dự kiến</font>', 'alert', 250, 180);
						return false;
					}
					if ($('#khcn_frm_reg_anphamkhoahoc_so_luong').val()==''){
						gv_open_msg_box('<font color=red>Vui lòng nhập Số lượng</font>', 'alert', 250, 180);
						return false;
					}else if (!$.isNumeric($('#khcn_frm_reg_anphamkhoahoc_so_luong').val())){
						gv_open_msg_box('<font color=red>Lỗi: <b>Số lượng</b> nhập vào không phải là số</font>', 'alert', 250, 180);
						return false;
					}
					
					if ($('#khcn_frm_reg_anphamkhoahoc_dk_noi_cong_bo').val()==''){
						gv_open_msg_box('<font color=red>Vui lòng nhập Nơi công bố</font>', 'alert', 250, 180);
						return false;
					}
					
					
					gv_processing_diglog("open","Khoa học & Công nghệ", "Đang lưu dữ liệu ...");
					dataString = $("#khcn_frm_reg_anphamkhoahoc").serialize() + '&a=addanphamkhoahoc&m='+khcn_matm_selected;
					xreq = $.ajax({
					  type: 'POST', dataType: "json", data: dataString,
					  url: khcn_linkdata,
					  success: function(data) {
						gv_processing_diglog("close");
						if (data.success == 1){
							khcn_add_table_anphamkhoahoc(data);
							
							$("button.khcn_anphamkhoahoc_remove" ).button({ icons: {primary:'ui-icon ui-icon-trash'} });
							
							$( "#khcn_diag_anphamkhoahoc" ).dialog( "close" );
						}else{
							if (reverse_escapeJsonString(data.msgerr).search( 'ORA-00001' )>-1)
								gv_open_msg_box("<font color=red>Ấn phẩm này đã có trong dữ liệu vui lòng chọn ấn phẩm khác</font>", 'alert', 250, 180, true);
							else
								gv_open_msg_box("Chi tiết lỗi: <br/><div style='margin: 5px 0 0 5px'>" + reverse_escapeJsonString(data.msgerr)+'</div>', 'alert', 250, 180, true);
						}
					  }
					});
					//$( this ).dialog( "close" );
				}
			},
			{
				id: "khcn_diag_anphamkhoahoc_btn_cancel",
				text: "Cancel",
				click: function() {
					
					$( this ).dialog( "close" );
				}
			}
		]
	});
	
	$("#khcn_diag_sohuutritue").dialog({
		resizable: false,
		autoOpen: false,
		width:450, height:270,
		modal: true,
		buttons: [
			{
				id: "khcn_diag_sohuutritue_btn_ok",
				text: "OK",
				click: function() {
					// Check validate
					bValid = true;
					
					if ($('#khcn_frm_reg_sohuutritue_hinhthuc').val()==''){
						gv_open_msg_box('<font color=red>Vui lòng chọn hình thức đăng ký</font>', 'alert', 250, 180);
						return false;
					}
					if ($('#khcn_frm_reg_sohuutritue_so_luong').val()==''){
						gv_open_msg_box('<font color=red>Vui lòng nhập Số lượng</font>', 'alert', 250, 180);
						return false;
					}else if (!$.isNumeric($('#khcn_frm_reg_sohuutritue_so_luong').val())){
						gv_open_msg_box('<font color=red>Lỗi: <b>Số lượng</b> nhập vào không phải là số</font>', 'alert', 250, 180);
						return false;
					}
					
					if ($('#khcn_frm_reg_sohuutritue_noi_dung').val()==''){
						gv_open_msg_box('<font color=red>Vui lòng nhập nội dung dự kiến đăng ký</font>', 'alert', 250, 180);
						return false;
					}
					
					gv_processing_diglog("open","Khoa học & Công nghệ", "Đang lưu dữ liệu ...");
					dataString = $("#khcn_frm_reg_sohuutritue").serialize() + '&a=addsohuutritue&m='+khcn_matm_selected +'&hinhthuc=' +encodeURIComponent($("#khcn_frm_reg_sohuutritue_hinhthuc option:selected").html());
					xreq = $.ajax({
					  type: 'POST', dataType: "json", data: dataString,
					  url: khcn_linkdata,
					  success: function(data) {
						gv_processing_diglog("close");
						if (data.success == 1){
							khcn_add_table_sohuutritue(data);
							
							$("button.khcn_sohuutritue_remove" ).button({ icons: {primary:'ui-icon ui-icon-trash'} });
							
							$( "#khcn_diag_sohuutritue" ).dialog( "close" );
						}else{
							if (reverse_escapeJsonString(data.msgerr).search( 'ORA-00001' )>-1)
								gv_open_msg_box("<font color=red>Hình thức đăng ký này đã có trong dữ liệu vui lòng chọn hình thức khác</font>", 'alert', 250, 180, true);
							else
								gv_open_msg_box("Chi tiết lỗi: <br/><div style='margin: 5px 0 0 5px'>" + reverse_escapeJsonString(data.msgerr)+'</div>', 'alert', 250, 180, true);
						}
					  }
					});
				}
			},
			{
				id: "khcn_diag_sohuutritue_btn_cancel",
				text: "Cancel",
				click: function() {
					
					$( this ).dialog( "close" );
				}
			}
		]
	});
	
	$("#khcn_diag_sanphammem").dialog({
		resizable: false,
		autoOpen: false,
		width:450, height:270,
		modal: true,
		buttons: [
			{
				id: "khcn_diag_sanphammem_btn_ok",
				text: "OK",
				click: function() {
					// Check validate
					bValid = true;
					
					if ($('#khcn_frm_reg_sanphammem_tensp').val()==''){
						gv_open_msg_box('<font color=red>Vui lòng nhập tên sản phẩm</font>', 'alert', 250, 180);
						return false;
					}
					if ($('#khcn_frm_reg_sanphammem_ctdanhgia').val()==''){
						gv_open_msg_box('<font color=red>Vui lòng nhập chỉ tiêu đánh giá</font>', 'alert', 250, 180);
						return false;
					}
					
					gv_processing_diglog("open","Khoa học & Công nghệ", "Đang lưu dữ liệu ...");
					dataString = $("#khcn_frm_reg_sanphammem").serialize() + '&a=addsanphammem&m='+khcn_matm_selected;
					xreq = $.ajax({
					  type: 'POST', dataType: "json", data: dataString,
					  url: khcn_linkdata,
					  success: function(data) {
						gv_processing_diglog("close");
						if (data.success == 1){
							khcn_add_table_sanphammem(data);
							
							$("button.khcn_sanphammem_remove" ).button({ icons: {primary:'ui-icon ui-icon-trash'} });
							
							$( "#khcn_diag_sanphammem" ).dialog( "close" );
						}else{
							gv_open_msg_box("Chi tiết lỗi: <br/><div style='margin: 5px 0 0 5px'>" + reverse_escapeJsonString(data.msgerr)+'</div>', 'alert', 250, 180, true);
						}
					  }
					});
				}
			},
			{
				id: "khcn_diag_sanphammem_btn_cancel",
				text: "Cancel",
				click: function() {
					
					$( this ).dialog( "close" );
				}
			}
		]
	});
	
	$("#khcn_diag_sanphamcung").dialog({
		resizable: false,
		autoOpen: false,
		width:450, height:370,
		modal: true,
		buttons: [
			{
				id: "khcn_diag_sanphamcung_btn_ok",
				text: "OK",
				click: function() {
					// Check validate
					bValid = true;
					
					if ($('#khcn_frm_reg_sanphamcung_tensp').val()==''){
						gv_open_msg_box('<font color=red>Vui lòng nhập tên sản phẩm</font>', 'alert', 250, 180);
						return false;
					}
					if ($('#khcn_frm_reg_sanphamcung_don_vi_do').val()==''){
						gv_open_msg_box('<font color=red>Vui lòng nhập đơn vị đo</font>', 'alert', 250, 180);
						return false;
					}
					
					if ($('#khcn_frm_reg_sanphamcung_ctdanhgia').val()==''){
						gv_open_msg_box('<font color=red>Vui lòng nhập chỉ tiêu đánh giá</font>', 'alert', 250, 180);
						return false;
					}
					
					if ($('#khcn_frm_reg_sanphamcung_mau_tt_trong_nuoc').val()==''){
						gv_open_msg_box('<font color=red>Vui lòng nhập mức chất lượng mẫu tương tự trong nước</font>', 'alert', 250, 180);
						return false;
					}
					
					if ($('#khcn_frm_reg_sanphamcung_mau_tt_thegioi').val()==''){
						gv_open_msg_box('<font color=red>Vui lòng nhập mức chất lượng mẫu tương tự thế giới</font>', 'alert', 250, 180);
						return false;
					}
					
					if ($('#khcn_frm_reg_sanphamcung_soluong').val()==''){
						gv_open_msg_box('<font color=red>Vui lòng nhập dự kiến số lượng / quy mô sản phẩm tạo ra</font>', 'alert', 250, 180);
						return false;
					}
					
					gv_processing_diglog("open","Khoa học & Công nghệ", "Đang lưu dữ liệu ...");
					dataString = $("#khcn_frm_reg_sanphamcung").serialize() + '&a=addsanphamcung&m='+khcn_matm_selected;
					xreq = $.ajax({
					  type: 'POST', dataType: "json", data: dataString,
					  url: khcn_linkdata,
					  success: function(data) {
						gv_processing_diglog("close");
						if (data.success == 1){
							khcn_add_table_sanphamcung(data);
							
							$("button.khcn_sanphamcung_remove" ).button({ icons: {primary:'ui-icon ui-icon-trash'} });
							
							$( "#khcn_diag_sanphamcung" ).dialog( "close" );
						}else{
							gv_open_msg_box("Chi tiết lỗi: <br/><div style='margin: 5px 0 0 5px'>" + reverse_escapeJsonString(data.msgerr)+'</div>', 'alert', 250, 180, true);
						}
					  }
					});
				}
			},
			{
				id: "khcn_diag_sanphamcung_btn_cancel",
				text: "Cancel",
				click: function() {
					
					$( this ).dialog( "close" );
				}
			}
		]
	});
	
	$("#khcn_diag_ketquadaotao").dialog({
		resizable: false,
		autoOpen: false,
		width:450, height:270,
		modal: true,
		buttons: [
			{
				id: "khcn_diag_ketquadaotao_btn_ok", text: "OK",
				click: function() {
					// Check validate
					bValid = true;
					
					if ($('#khcn_frm_reg_ketquadaotao_capdt').val()==''){
						gv_open_msg_box('<font color=red>Vui lòng chọn <b>Cấp đào tạo</b></font>', 'alert', 250, 180);
						return false;
					}
					if ($('#khcn_frm_reg_ketquadaotao_so_luong').val()==''){
						gv_open_msg_box('<font color=red>Vui lòng nhập <b>Số lượng</b></font>', 'alert', 250, 180);
						return false;
					}else if (!$.isNumeric($('#khcn_frm_reg_ketquadaotao_so_luong').val())){
						gv_open_msg_box('<font color=red>Lỗi: <b>Số lượng</b> nhập vào không phải là số</font>', 'alert', 250, 180);
						return false;
					}
					if ($('#khcn_frm_reg_ketquadaotao_nhiem_vu').val()==''){
						gv_open_msg_box('<font color=red>Vui lòng nhập <b>Nhiệm vụ được giao trong đề tài</b></font>', 'alert', 250, 180);
						return false;
					}
					if ($('#khcn_frm_reg_ketquadaotao_kinhphi').val()==''){
						gv_open_msg_box('<font color=red>Vui lòng nhập <b>Kinh phí dự kiến</b></font>', 'alert', 250, 180);
						return false;
					}
					
					gv_processing_diglog("open","Khoa học & Công nghệ", "Đang lưu dữ liệu ...");
					dataString = $("#khcn_frm_reg_ketquadaotao").serialize() + '&a=addketquadaotao&m='+khcn_matm_selected +'&capdt=' +encodeURIComponent($("#khcn_frm_reg_ketquadaotao_capdt option:selected").html());
					xreq = $.ajax({
					  type: 'POST', dataType: "json", data: dataString,
					  url: khcn_linkdata,
					  success: function(data) {
						gv_processing_diglog("close");
						if (data.success == 1){
							khcn_add_table_ketquadaotao(data);
							$("button.khcn_ketquadaotao_remove").button({ icons: {primary:'ui-icon ui-icon-trash'} });
							$( "#khcn_diag_ketquadaotao" ).dialog( "close" );
						}else{
							if (reverse_escapeJsonString(data.msgerr).search( 'ORA-00001' )>-1)
								gv_open_msg_box("<font color=red><b>Cấp đào tạo</b> bạn chọn đã có trong dữ liệu vui lòng chọn cấp đào tạo khác</font>", 'alert', 250, 180, true);
							else
								gv_open_msg_box("Chi tiết lỗi: <br/><div style='margin: 5px 0 0 5px'>" + reverse_escapeJsonString(data.msgerr)+'</div>', 'alert', 250, 180, true);
						}
					  }
					});
				}
			},
			{
				id: "khcn_diag_ketquadaotao_btn_cancel", text: "Cancel", click: function() {	$( this ).dialog( "close" );}
			}
		]
	});
	
	$("#khcn_diag_tonghopkinhphi").dialog({
		resizable: false,
		autoOpen: false,
		width:250, height:250,
		modal: true,
		buttons: [
			{
				id: "khcn_diag_tonghopkinhphi_btn_ok", text: "OK",
				click: function() {
					// Check validate
					bValid = true;
					
					if ($('#khcn_frm_reg_tonghopkinhphi_kinh_phi').val()==''){
						gv_open_msg_box('<font color=red>Vui lòng nhập <b>Kinh phí</b></font>', 'alert', 250, 180);
						return false;
					}
					
					if ($('#khcn_frm_reg_tonghopkinhphi_khoan_chi').val()==''){
						gv_open_msg_box('<font color=red>Vui lòng nhập <b>Khoán chi</b></font>', 'alert', 250, 180);
						return false;
					}
					
					gv_processing_diglog("open","Khoa học & Công nghệ", "Đang lưu dữ liệu ...");
					dataString = 'a=updateB8&m='+khcn_matm_selected
					+ '&khcn_frm_reg_tonghopkinhphi_khoan_chi_phi='+ $('#khcn_frm_reg_tonghopkinhphi_khoan_chi_phi').val()
					+ '&khcn_frm_reg_tonghopkinhphi_kinh_phi='+ $('#khcn_frm_reg_tonghopkinhphi_kinh_phi').autoNumeric('get')
					+ '&khcn_frm_reg_tonghopkinhphi_khoan_chi='+ $('#khcn_frm_reg_tonghopkinhphi_khoan_chi').autoNumeric('get');
					xreq = $.ajax({
					  type: 'POST', dataType: "json", data: dataString,
					  url: khcn_linkdata,
					  success: function(data) {
						gv_processing_diglog("close");
						
						if (data.success == 1){
							$('#khcn_frm_edit_dtkhcn_B8_table_tonghopkinhphi tbody tr').each(function() {
								var nTr = this;
								//alert(nTr.cells[0].innerHTML);
								if (nTr.cells[0].innerHTML==data.fk_ma_khoan_chi_phi){
									nTr.cells[2].innerHTML = parseFloat(data.kinh_phi).formatMoney(2,',','.');
									nTr.cells[3].innerHTML = parseFloat(data.khoan_chi).formatMoney(2,',','.');
								}
							});
							
							$( "#khcn_diag_tonghopkinhphi" ).dialog( "close" );
						}else{
							if (reverse_escapeJsonString(data.msgerr).search( 'ORA-00001' )>-1)
								gv_open_msg_box("<font color=red><b>Khoản chi phí</b> bạn chọn đã có trong dữ liệu vui lòng chọn khoản khác</font>", 'alert', 250, 180, true);
							else
								gv_open_msg_box("Chi tiết lỗi: <br/><div style='margin: 5px 0 0 5px'>" + reverse_escapeJsonString(data.msgerr)+'</div>', 'alert', 250, 180, true);
						}
					  }
					});
				}
			},
			{
				id: "khcn_diag_tonghopkinhphi_btn_cancel", text: "Cancel", click: function() {	$( this ).dialog( "close" );}
			}
		]
	});
	
	$("#khcn_diag_confirm_hoantat_tmdt").dialog({
		resizable: false,
		autoOpen: false,
		width:250, height:200,
		modal: true,
		buttons: [
			{
				id: "khcn_diag_confirm_hoantat_tmdt_btn_ok", text: "OK",
				click: function() {
					gv_processing_diglog("open","Khoa học & Công nghệ", "Đang lưu dữ liệu ...");
					
					// Lấy mẫu LLKH của CNĐT
					/* dataString = 'a=get_llkh&b=export_htm&c='+khcn_matm_selected+'&d=llkh_cndt&hisid=<?php echo $_REQUEST["hisid"]; ?>';
					filename = $("#khcn_diag_confirm_hoantat_tmdt_llkh").val();
					xreq = $.ajax({
					  type: 'POST', dataType: "html", data: dataString,
					  url: "gv/"+filename,
					  success: function(data) {
					  
							// Lấy mẫu LLKH của ĐCNĐT
							var aData = oTableThuyetMinhDTKHCN.fnGetData( khcn_nTr_selected );
							var dcnt_mcb = aData[khcn_tmdt_col_idx['dcndt_mcb']];
							if (dcnt_mcb != "") {
								//alert(dcnt_mcb);
								dataString = 'a=get_llkh&b=export_htm&c='+khcn_matm_selected+'&d=llkh_dcndt&e='+dcnt_mcb+'&hisid=<?php echo $_REQUEST["hisid"]; ?>';
								filename = $("#khcn_diag_confirm_hoantat_tmdt_llkh").val();
								xreq = $.ajax({
								  type: 'POST', dataType: "html", data: dataString,
								  url: "gv/"+filename,
								  success: function(data) {
								  
										gv_processing_diglog("close");
										
										// Cập nhật trạng thái Hoàn tất đăng ký
										dataString = 'a=updateS&m='+khcn_matm_selected;
										xreq = $.ajax({
										  type: 'POST', dataType: "json", data: dataString,
										  url: khcn_linkdata,
										  success: function(data) {
											gv_processing_diglog("close");
											if (data.success == 1){
												var aData = oTableThuyetMinhDTKHCN.fnGetData( khcn_nTr_selected );
												aData[khcn_tmdt_col_idx['trangthai']]=data.tinh_trang;
												aData[khcn_tmdt_col_idx['editallow']]=data.edit_allow;
												khcn_nTr_selected.cells[khcn_tmdt_col_idx['trangthai']].innerHTML=data.tinh_trang;
												khcn_nTr_selected.cells[khcn_tmdt_col_idx['guitmdt']].innerHTML="<img src='icons/circle-green.png' class=khcn_tooltips title='Đã hoàn tất đăng ký TMĐT' border=0 >";
												$( "#khcn_diag_confirm_hoantat_tmdt" ).dialog( "close" );
											}else{
												gv_open_msg_box("Chi tiết lỗi: <br/><div style='margin: 5px 0 0 5px'>" + reverse_escapeJsonString(data.msgerr)+'</div>', 'alert', 250, 180, true);
											}
										  }
										});

								  }
								});
							}else{
								// Cập nhật trạng thái Hoàn tất đăng ký
								dataString = 'a=updateS&m='+khcn_matm_selected;
								xreq = $.ajax({
								  type: 'POST', dataType: "json", data: dataString,
								  url: khcn_linkdata,
								  success: function(data) {
									gv_processing_diglog("close");
									if (data.success == 1){
										var aData = oTableThuyetMinhDTKHCN.fnGetData( khcn_nTr_selected );
										aData[khcn_tmdt_col_idx['trangthai']]=data.tinh_trang;
										aData[khcn_tmdt_col_idx['editallow']]=data.edit_allow;
										khcn_nTr_selected.cells[khcn_tmdt_col_idx['trangthai']].innerHTML=data.tinh_trang;
										khcn_nTr_selected.cells[khcn_tmdt_col_idx['guitmdt']].innerHTML="<img src='icons/circle-green.png' class=khcn_tooltips title='Đã hoàn tất đăng ký TMĐT' border=0 >";
										$( "#khcn_diag_confirm_hoantat_tmdt" ).dialog( "close" );
									}else{
										gv_open_msg_box("Chi tiết lỗi: <br/><div style='margin: 5px 0 0 5px'>" + reverse_escapeJsonString(data.msgerr)+'</div>', 'alert', 250, 180, true);
									}
								  }
								});
							}
					  }
					}); */
					
					// Lấy mẫu LLKH của tham gia de tai
					dataString = 'a=get_llkh&b=export_htm&c='+khcn_matm_selected+'&d=llkh_cndt&hisid=<?php echo $_REQUEST["hisid"]; ?>';
					filename = $("#khcn_diag_confirm_hoantat_tmdt_llkh").val();
					xreq = $.ajax({
					  type: 'POST', dataType: "html", data: dataString,
					  url: "gv/"+filename,
					  success: function(data) {
							
							// Cập nhật trạng thái Hoàn tất đăng ký
							/* dataString = 'a=updateS&m='+khcn_matm_selected;
							xreq = $.ajax({
							  type: 'POST', dataType: "json", data: dataString,
							  url: khcn_linkdata,
							  success: function(data) {
								gv_processing_diglog("close");
								if (data.success == 1){
									var aData = oTableThuyetMinhDTKHCN.fnGetData( khcn_nTr_selected );
									aData[khcn_tmdt_col_idx['trangthai']]=data.tinh_trang;
									aData[khcn_tmdt_col_idx['editallow']]=data.edit_allow;
									khcn_nTr_selected.cells[khcn_tmdt_col_idx['trangthai']].innerHTML=data.tinh_trang;
									khcn_nTr_selected.cells[khcn_tmdt_col_idx['guitmdt']].innerHTML="<img src='icons/circle-green.png' class=khcn_tooltips title='Đã hoàn tất đăng ký TMĐT' border=0 >";
									$( "#khcn_diag_confirm_hoantat_tmdt" ).dialog( "close" );
								}else{
									gv_open_msg_box("Chi tiết lỗi: <br/><div style='margin: 5px 0 0 5px'>" + reverse_escapeJsonString(data.msgerr)+'</div>', 'alert', 250, 180, true);
								}
							  }
							}); */

					  }
					});
					
				}
			},
			{
				id: "khcn_diag_confirm_hoantat_tmdt_btn_cancel", text: "Cancel", click: function() {	$( this ).dialog( "close" );}
			}
		]
	});
	
	$("#khcn_diag_confirm_delete_tmdt").dialog({
		resizable: false,
		autoOpen: false,
		width:250, height:200,
		modal: true,
		buttons: [
			{
				id: "khcn_diag_confirm_delete_tmdt_btn_ok", text: "OK",
				click: function() {
					gv_processing_diglog("open","Khoa học & Công nghệ", "Đang xoá đề tài...");
					
					// Cập nhật trạng thái Hoàn tất đăng ký
					dataString = 'a=ThungRac&m='+khcn_matm_selected;
					xreq = $.ajax({
					  type: 'POST', dataType: "json", data: dataString,
					  url: khcn_linkdata,
					  success: function(data) {
						if (data.success == 1){
							khcn_RefreshTableThuyeMinh(oTableThuyetMinhDTKHCN,khcn_linkdata);
							gv_processing_diglog("close");
							$( "#khcn_diag_confirm_delete_tmdt" ).dialog( "close" );
						}else{
							gv_processing_diglog("close");
							gv_open_msg_box("Không thể xoa đề tài số " + data.ma, 'alert', 250, 180, true);
						}
					  }
					});
				}
			},
			{
				id: "khcn_diag_confirm_delete_tmdt_btn_cancel", text: "Cancel", click: function() {	$( this ).dialog( "close" );}
			}
		]
	});

	$( "#khcn_diag_capdetai" ).dialog({
			resizable: false,
			autoOpen: false,
			width:350, height:300,
			modal: true
			
	});
	
	$( "#khcn_tabs_thuyetminh_thongtinchung" ).tabs({
		beforeActivate: function( event, ui ) {
			return khcn_update_thongtinchung(ui.oldPanel.attr('id'));
		}
	});
	
	$( "#khcn_tabs_thuyetminh_motanghiencuu" ).tabs({
		beforeActivate: function( event, ui ) {
			var oldPanelID = ui.oldPanel.attr('id');
			return khcn_update_mota(oldPanelID);
			
		}
	});
	
	khcn_initialTableThuyetMinhDTKHCN(khcn_linkdata);
	
	$(".khcn_tooltips").tooltip({ track: true });
	
	$( '#khcn_frm_edit_dtkhcn_tq_tinh_hinh_nc, #khcn_frm_edit_dtkhcn_y_tuong_kh, #khcn_frm_edit_dtkhcn_kq_nc_so_khoi, #khcn_frm_edit_dtkhcn_noi_dung_nc, #khcn_frm_edit_dtkhcn_pa_phoi_hop' ).ckeditor({
		enterMode : CKEDITOR.ENTER_BR,
		shiftEnterMode : CKEDITOR.ENTER_P,
		language : 'vi',
		height : 340,
		filebrowserBrowseUrl: '/ckfinder/ckfinder.html',
		filebrowserImageBrowseUrl: '/ckfinder/ckfinder.html?Type=Images',
		filebrowserUploadUrl: '/ckfinder/core/connector/php/connector.php?command=QuickUpload&type=Files',
		filebrowserImageUploadUrl: '/ckfinder/core/connector/php/connector.php?command=QuickUpload&type=Images',

		toolbar : [
			{ name: 'document', groups: [ 'mode', 'document', 'doctools' ], items: [ 'NewPage', 'Preview', 'Print'] },
			{ name: 'clipboard', groups: [ 'clipboard', 'undo' ], items: [ 'Cut', 'Copy', 'Paste', 'PasteText', 'PasteFromWord', '-', 'Undo', 'Redo' ] },
			{ name: 'editing', groups: [ 'find', 'selection', 'spellchecker' ], items: [ 'Find', 'Replace', '-', 'SelectAll', '-', 'Scayt' ] },
			{ name: 'insert', items: [ 'Image', 'Table', 'SpecialChar', '-' ] }, { name: 'tools', items: [ 'Maximize', 'ShowBlocks' ] },
			{ name: 'others', items: [ '-' ] },
			'/',
			{ name: 'styles', items: [ 'FontSize' ] },
			{ name: 'colors', items: [ 'TextColor', 'BGColor' ] },
			{ name: 'basicstyles', groups: [ 'basicstyles', 'cleanup' ], items: [ 'Bold', 'Italic', 'Underline', 'Strike', 'Subscript', 'Superscript', '-', 'RemoveFormat', '-', 'EqnEditor' ] },
			{ name: 'paragraph', groups: [ 'list', 'indent', 'blocks', 'align', 'bidi' ], items: [ 'NumberedList', 'BulletedList', '-', 'JustifyLeft', 'JustifyCenter', 'JustifyRight', 'JustifyBlock'] }
		]
	});
	
	$( '#khcn_frm_edit_dtkhcn_tai_lieu_tk' ).ckeditor({
		enterMode : CKEDITOR.ENTER_BR,
		shiftEnterMode : CKEDITOR.ENTER_P,
		language : 'vi',
		height : 150,
		filebrowserBrowseUrl: '/ckfinder/ckfinder.html',
		filebrowserImageBrowseUrl: '/ckfinder/ckfinder.html?Type=Images',
		filebrowserUploadUrl: '/ckfinder/core/connector/php/connector.php?command=QuickUpload&type=Files',
		filebrowserImageUploadUrl: '/ckfinder/core/connector/php/connector.php?command=QuickUpload&type=Images',
		toolbar : [
			{ name: 'document', groups: [ 'mode', 'document', 'doctools' ], items: [ 'NewPage', 'Preview', 'Print'] },
			{ name: 'clipboard', groups: [ 'clipboard', 'undo' ], items: [ 'Cut', 'Copy', 'Paste', 'PasteText', 'PasteFromWord', '-', 'Undo', 'Redo' ] },
			{ name: 'editing', groups: [ 'find', 'selection', 'spellchecker' ], items: [ 'Find', 'Replace', '-', 'SelectAll', '-', 'Scayt' ] },
			{ name: 'insert', items: [ 'Image', 'Table', 'SpecialChar', '-' ] }, { name: 'tools', items: [ 'Maximize', 'ShowBlocks' ] },
			{ name: 'others', items: [ '-' ] },
			'/',
			{ name: 'styles', items: [ 'FontSize' ] },
			{ name: 'colors', items: [ 'TextColor', 'BGColor' ] },
			{ name: 'basicstyles', groups: [ 'basicstyles', 'cleanup' ], items: [ 'Bold', 'Italic', 'Underline', 'Strike', 'Subscript', 'Superscript', '-', 'RemoveFormat', '-', 'EqnEditor' ] },
			{ name: 'paragraph', groups: [ 'list', 'indent', 'blocks', 'align', 'bidi' ], items: [ 'NumberedList', 'BulletedList', '-', 'JustifyLeft', 'JustifyCenter', 'JustifyRight', 'JustifyBlock'] }
		]
	});
	
	/* $('#khcn_ds_thuyetminhdtkhcn tbody tr').live('click', function() {
		var nTr = $(this)[0];
		var aData = oTableThuyetMinhDTKHCN.fnGetData( nTr );
		//alert(nTr.cells[0].innerHTML);
		//alert(aData[khcn_tmdt_col_idx['matrangthai']]);
		
		// Bo chon
		if ( $(this).hasClass('row_selected') ) {
            $(this).removeClass('row_selected');
			
			$('#khcn_edit_ttchung_button').button("disable");
			$('#khcn_edit_mota_button').button("disable");
        }
        else {
            oTableThuyetMinhDTKHCN.$('tr.row_selected').removeClass('row_selected');
            $(this).addClass('row_selected');
			//alert(parseFloat(aData[khcn_tmdt_col_idx['hethan_dkdt']]));
			if ((aData[khcn_tmdt_col_idx['matrangthai']]=='021' || (parseFloat(aData[khcn_tmdt_col_idx['batdau_dkdt']]) > 0 && parseFloat(aData[khcn_tmdt_col_idx['hethan_dkdt']]) < 0) ) && aData[khcn_tmdt_col_idx['editallow']]=='1'){
				$('#khcn_edit_ttchung_button').button("enable");
				$('#khcn_edit_mota_button').button("enable");
			}
			else{
				$('#khcn_edit_ttchung_button').button("disable");
				$('#khcn_edit_mota_button').button("disable");
			}
        }
		
	}); */
	
	$('#khcn_frm_reg_nhanlucnghiencuu_loai').change(function() {
		khcn_init_dialog_nhanluc($(this).val());
	});
	
	/* $('#khcn_frm_reg_nhanlucnghiencuu_shcc').change(function() {
		khcn_GetLLKH($(this).val()).done(function(data){
				if (data.success == 1){
					$('#khcn_frm_reg_nhanlucnghiencuu_hh_hv_ho_ten').val(data.llkh.ho_ten);
					$('#khcn_frm_reg_nhanlucnghiencuu_don_vi_cong_tac').val(data.llkh.co_quan_cong_tac);
					$('#khcn_frm_reg_nhanlucnghiencuu_fk_ma_can_bo').val(data.llkh.ma_can_bo);
				}else
				{
					$('#khcn_frm_reg_nhanlucnghiencuu_shcc').val('');
					gv_open_msg_box('<font color=red>SHCC không chính xác</font>', 'alert', 250, 180);
				}
		});
	}); */
	
	$('#khcn_frm_reg_chuyengia_shcc').change(function() {
		khcn_GetLLKH($(this).val()).done(function(data){
				if (data.success == 1){
					$('#khcn_frm_reg_chuyengia_hh_hv_ho_ten').val(data.llkh.ho_ten);
					$('#khcn_frm_reg_chuyengia_don_vi_cong_tac').val(data.llkh.co_quan_cong_tac);
					$('#khcn_frm_reg_chuyengia_fk_ma_can_bo').val(data.llkh.ma_can_bo);
					$('#khcn_frm_reg_chuyengia_diachi').val(data.llkh.dia_chi);
					$('#khcn_frm_reg_chuyengia_dienthoai').val(data.llkh.dien_thoai_cn);
					$('#khcn_frm_reg_chuyengia_email').val(data.llkh.email);
				}else
				{
					$('#khcn_frm_reg_chuyengia_shcc').val('');
					//gv_open_msg_box('<font color=red>SHCC không chính xác</font>', 'alert', 250, 180);
				}
		});
	});
	
	// Rang buoc kinh phi 
	$("#khcn_frm_edit_dtkhcn_tongkinhphi, #khcn_frm_edit_dtkhcn_kinhphi_huydong").attr("disabled", "disabled");
	$('#khcn_frm_edit_dtkhcn_kinhphi_dhqg, #khcn_frm_edit_dtkhcn_kinhphi_tuco, #khcn_frm_edit_dtkhcn_kinhphi_khac').change(function() {
		khcn_cal_kinhphi();
	});
	
	// Form change
	$('#khcn_frm_edit_dtkhcn_A1_A4').change(function() {
		khcn_formA1A4_changed = true;
	});
	
	$('#khcn_frm_edit_dtkhcn_A5').change(function() {
		khcn_formA5_changed = true;
	});
	
	$('#khcn_frm_edit_dtkhcn_A6').change(function() {
		khcn_formA6_changed = true;
	});
	
	$('#khcn_frm_edit_dtkhcn_A7_A8').change(function() {
		khcn_formA7A8_changed = true;
	});
	
	$('#khcn_frm_edit_dtkhcn_B5_1').change(function() {
		khcn_formB5_1_changed = true;
	});
	
	$('#khcn_frm_edit_dtkhcn_B6_2').change(function() {
		khcn_formB6_2_changed = true;
	});
	
	$('#khcn_frm_edit_dtkhcn_B7').change(function() {
		khcn_formB7_changed = true;
	});
	
	//$('#khcn_frm_edit_dtkhcn_B1').change(function() {
	CKEDITOR.instances['khcn_frm_edit_dtkhcn_tq_tinh_hinh_nc'].on('change', function() {
		khcn_formB1_changed = true;
	});
	
	//$('#khcn_frm_edit_dtkhcn_B2').change(function() {
	CKEDITOR.instances['khcn_frm_edit_dtkhcn_y_tuong_kh'].on('change', function() {
		khcn_formB2_changed = true;
	});
	
	//$('#khcn_frm_edit_dtkhcn_B3').change(function() {
	CKEDITOR.instances['khcn_frm_edit_dtkhcn_kq_nc_so_khoi'].on('change', function() {
		khcn_formB3_changed = true;
	});
	
	//$('#khcn_frm_edit_dtkhcn_B4').change(function() {
	CKEDITOR.instances['khcn_frm_edit_dtkhcn_tai_lieu_tk'].on('change', function() {
		khcn_formB4_changed = true;
	});
	
	CKEDITOR.instances['khcn_frm_edit_dtkhcn_noi_dung_nc'].on('change', function() {
		khcn_formB5_2_changed = true;
	});
	
	CKEDITOR.instances['khcn_frm_edit_dtkhcn_pa_phoi_hop'].on('change', function() {
		khcn_formB5_3_changed = true;
	});

	/* $('#khcn_frm_edit_dtkhcn_dcndt_shcc').change(function() {
		if ($(this).val()){
			gv_processing_diglog("open","Khoa học & Công nghệ" ,"Đang tìm thông tin của cán bộ giảng dạy ... vui lòng chờ");
			dataString = 'a=getllkh&hisid=<?php echo $_REQUEST["hisid"];?>&'+$('#khcn_frm_edit_dtkhcn_dcndt_shcc').serialize();
			xreq = $.ajax({
			  type: 'POST', dataType: "json",
			  url: 'khcn/khcn_thuyetminhdtkhcn_process.php',
			  data: dataString,
			  success: function(data) {
				gv_processing_diglog("close");
				
				if (data.success == 1)
				{
					// A6-A7
					$("#khcn_frm_edit_dtkhcn_fk_dong_chu_nhiem_dt").val(data.llkh.ma_can_bo);
					$("#khcn_frm_edit_dtkhcn_dcndt_hh_hv_ho_ten").val(data.llkh.ho_ten);
					$("#khcn_frm_edit_dtkhcn_dcndt_ngay_sinh").val(data.llkh.ngay_sinh);
					$('input:radio[name=khcn_frm_edit_dtkhcn_dcndt_phai][value='+data.llkh.phai+']').attr('checked', true);
					$("#khcn_frm_edit_dtkhcn_dcndt_so_cmnd").val(data.llkh.so_cmnd);
					$("#khcn_frm_edit_dtkhcn_dcndt_ngay_cap").val(data.llkh.ngay_cap);
					$("#khcn_frm_edit_dtkhcn_dcndt_noi_cap").val(data.llkh.noi_cap);
					$("#khcn_frm_edit_dtkhcn_dcndt_ms_thue").val(data.llkh.ma_so_thue);
					$("#khcn_frm_edit_dtkhcn_dcndt_so_tai_khoan").val(data.llkh.so_tai_khoan);
					$("#khcn_frm_edit_dtkhcn_dcndt_ngan_hang").val(data.llkh.ngan_hang_mo_tk);
					$("#khcn_frm_edit_dtkhcn_dcndt_dia_chi_cq").val(data.llkh.dia_chi);
					$("#khcn_frm_edit_dtkhcn_dcndt_dien_thoai").val(data.llkh.dien_thoai_cn);
					$("#khcn_frm_edit_dtkhcn_dcndt_email").val(data.llkh.email);
					
					khcn_formA6_changed = true;				
				}
				else
				{
					gv_open_msg_box("Không tìm thấy thông tin của cán bộ giảng dạy", "alert", 250, 180, true);
				}
			  },
			  error: function(xhr, ajaxOptions, thrownError) {}
			});
		}
	}); */
	
	$('#khcn_frm_edit_dtkhcn_fk_cq_phoi_hop_1').change(function() {
		//alert('change');
		var macq = $('#khcn_frm_edit_dtkhcn_fk_cq_phoi_hop_1').val();
		if (macq != ''){
			khcn_GetCoQuanInfo(macq).done(function(data){
				if (data.success == 1){
					$('#khcn_frm_edit_dtkhcn_cqph1_ho_ten_tt').val(data.coquan.ho_ten_tt);
					$('#khcn_frm_edit_dtkhcn_cqph1_dien_thoai').val(data.coquan.dien_thoai);
					$('#khcn_frm_edit_dtkhcn_cqph1_fax').val(data.coquan.fax);
					$('#khcn_frm_edit_dtkhcn_cqph1_dia_chi').val(data.coquan.dia_chi);
					$('#khcn_frm_edit_dtkhcn_cqph1_ten_co_quan').val(data.coquan.ten_co_quan);		
				}
			});
		}
		else{
			$('#khcn_frm_edit_dtkhcn_cqph1_ho_ten_tt, #khcn_frm_edit_dtkhcn_cqph1_dien_thoai, #khcn_frm_edit_dtkhcn_cqph1_fax, #khcn_frm_edit_dtkhcn_cqph1_dia_chi, #khcn_frm_edit_dtkhcn_cqph1_ten_co_quan').val("");
		}
	});
	
	$('#khcn_frm_edit_dtkhcn_fk_cq_phoi_hop_2').change(function() {
		
		var macq = $('#khcn_frm_edit_dtkhcn_fk_cq_phoi_hop_2').val();
		if (macq != ''){
			khcn_GetCoQuanInfo(macq).done(function(data){
				if (data.success == 1){
					$('#khcn_frm_edit_dtkhcn_cqph2_ho_ten_tt').val(data.coquan.ho_ten_tt);
					$('#khcn_frm_edit_dtkhcn_cqph2_dien_thoai').val(data.coquan.dien_thoai);
					$('#khcn_frm_edit_dtkhcn_cqph2_fax').val(data.coquan.fax);
					$('#khcn_frm_edit_dtkhcn_cqph2_dia_chi').val(data.coquan.dia_chi);
					$('#khcn_frm_edit_dtkhcn_cqph2_ten_co_quan').val(data.coquan.ten_co_quan);		
				}
			});
		}
		else{
			$('#khcn_frm_edit_dtkhcn_cqph2_ho_ten_tt, #khcn_frm_edit_dtkhcn_cqph2_dien_thoai, #khcn_frm_edit_dtkhcn_cqph2_fax, #khcn_frm_edit_dtkhcn_cqph2_dia_chi, #khcn_frm_edit_dtkhcn_cqph2_ten_co_quan').val("");
		}
	});
	
	$('#khcn_frm_reg_dtkhcn_capdetai').change(function() {
		khcn_change_capdetai($(this).val());
	});
	
	// File upload
	$("#progress").hide();
	var options = {
		beforeSend: function()
		{
			$("#progress").show();
			//clear everything
			$("#bar").width('0%');
			$("#khcn_file_giai_trinh_khoan_chi").html("");
			$("#percent").html("0%");
		},
		uploadProgress: function(event, position, total, percentComplete)
		{
			$("#bar").width(percentComplete+'%');
			$("#percent").html(percentComplete+'%');
			
		},
		success: function()
		{
			$("#bar").width('100%');
			$("#percent").html('100%');
			
		},
		complete: function(response)
		{
			$("#khcn_file_giai_trinh_khoan_chi").html("<font color='green'>"+response.responseText+"</font>");
		},
		error: function()
		{
			$("#khcn_file_giai_trinh_khoan_chi").html("<font color='red'> ERROR: unable to upload files</font>");
		}
	 
	};
    $("#khcn_frm_upload_file_khoanchi").ajaxForm(options);
	
	// A5 giai trinh von khac
	$("#progress1").hide();
	var options1 = {
		beforeSend: function()
		{
			$("#progress1").show();
			//clear everything
			$("#bar1").width('0%');
			$("#khcn_file_giai_trinh_vonkhac").html("");
			$("#percent1").html("0%");
		},
		uploadProgress: function(event, position, total, percentComplete)
		{
			$("#bar1").width(percentComplete+'%');
			$("#percent1").html(percentComplete+'%');
			
		},
		success: function()
		{
			$("#bar1").width('100%');
			$("#percent1").html('100%');
			
		},
		complete: function(response)
		{
			$("#khcn_file_giai_trinh_vonkhac").html("<font color='green'>"+response.responseText+"</font>");
		},
		error: function()
		{
			$("#khcn_file_giai_trinh_vonkhac").html("<font color='red'> ERROR: unable to upload files</font>");
		}
	 
	};
    $("#khcn_frm_upload_file_vonkhac").ajaxForm(options1);
	// End file upload
	
	// Khoi tao danh sach can bo giang day
	dataString = 'a=ds_cb_thamgia&hisid=<?php echo $_REQUEST["hisid"] ?>';
	xreq = $.ajax({
		type: 'POST', url: 'khcn/khcn_thuyetminhdtkhcn_process.php', data: dataString,dataType: "json", 
		success: function(data) {
			khcn_init_ds_nhanlucnc(data.dscanbo);
			khcn_init_ds_dongchunhiem(data.dscanbo);
		},
		error: function(xhr, ajaxOptions, thrownError) {
			alert (thrownError);
		}
	});
	// end
	
	$("#khcn_frm_reg_nhanlucnghiencuu_hh_hv_ho_ten").focusout(function () {
		//console.log("value " + this.value);
		if(this.value==""){
			console.log("xoa");
			$( '#khcn_frm_reg_nhanlucnghiencuu_fk_ma_can_bo' ).val("");
			$( '#khcn_frm_reg_nhanlucnghiencuu_shcc' ).val("");
			$( '#khcn_frm_reg_nhanlucnghiencuu_don_vi_cong_tac').val("");
			
		}
	});
	
	$("#khcn_frm_edit_dtkhcn_dcndt_hh_hv_ho_ten").focusout(function () {
		//console.log("value " + this.value);
		if(this.value==""){
			$( '#khcn_frm_edit_dtkhcn_fk_dong_chu_nhiem_dt, #khcn_frm_edit_dtkhcn_dcndt_hh_hv_ho_ten, #khcn_frm_edit_dtkhcn_dcndt_shcc, #khcn_frm_edit_dtkhcn_dcndt_ngay_sinh, #khcn_frm_edit_dtkhcn_dcndt_so_cmnd, #khcn_frm_edit_dtkhcn_dcndt_ngay_cap, #khcn_frm_edit_dtkhcn_dcndt_noi_cap, #khcn_frm_edit_dtkhcn_dcndt_ms_thue, #khcn_frm_edit_dtkhcn_dcndt_so_tai_khoan, #khcn_frm_edit_dtkhcn_dcndt_ngan_hang, #khcn_frm_edit_dtkhcn_dcndt_dia_chi_cq, #khcn_frm_edit_dtkhcn_dcndt_dien_thoai, #khcn_frm_edit_dtkhcn_dcndt_email' ).val("");
		}
	});
});

function khcn_initialTableThuyetMinhDTKHCN(urldata){
	oTableThuyetMinhDTKHCN = $('#khcn_ds_thuyetminhdtkhcn').dataTable( {
		"bJQueryUI": false,
		"bStateSave": true,
		"bAutoWidth": false, 
		"iDisplayLength": 50,
		"sPaginationType": "full_numbers",
		"oLanguage": {
			"sUrl": "../datatable/media/language/vi_VI.txt"
		},
		"bProcessing": true,
		"sAjaxSource": urldata+'&a=refreshdata',
		"fnDrawCallback": function( oSettings ) {
			//$(document).tooltip({ track: true });
			$(".khcn_tooltips").tooltip({ track: true });
			
			$('#khcn_ds_thuyetminhdtkhcn').find('tbody').find('tr').each(function(){
				$(this).click(function(){
					var nTr = $(this)[0];
					var aData = oTableThuyetMinhDTKHCN.fnGetData( nTr );
					
					/* // Bo chon
					if ( $(this).hasClass('row_selected') ) {
						$(this).removeClass('row_selected');
						
						$('#khcn_edit_ttchung_button').button("disable");
						$('#khcn_edit_mota_button').button("disable");
					}
					else { */
						oTableThuyetMinhDTKHCN.$('tr.row_selected').removeClass('row_selected');
						$(this).addClass('row_selected');
						//alert(parseFloat(aData[khcn_tmdt_col_idx['hethan_dkdt']]));
						if ((aData[khcn_tmdt_col_idx['matrangthai']]=='021' || (parseFloat(aData[khcn_tmdt_col_idx['batdau_dkdt']]) > 0 && parseFloat(aData[khcn_tmdt_col_idx['hethan_dkdt']]) < 0) ) && aData[khcn_tmdt_col_idx['editallow']]=='1'){
							$('#khcn_edit_ttchung_button').button("enable");
							$('#khcn_edit_mota_button').button("enable");
						}
						else{
							$('#khcn_edit_ttchung_button').button("disable");
							$('#khcn_edit_mota_button').button("disable");
						}
					//}
				});
			});
		}, 
		"fnRowCallback": function( nRow, aaData, iDisplayIndex ) {
			$('td:eq('+khcn_tmdt_col_idx['trangthai']+')', nRow).css({'font-weight': 'bold'});
			$('td:eq('+khcn_tmdt_col_idx['capdetai']+')', nRow).css({'font-weight': 'bold'});
			
			return nRow;
		},
		"fnFooterCallback": function ( nRow, aaData, iStart, iEnd, aiDisplay ) {		
		},
		"aoColumns": [
            null,null, null,
            null,null,null, null,null,
			{ "sClass" : "right"},
			{ "sClass" : "right"},
			{ "sClass" : "center", "bSortable": false},
			{ "sClass" : "left" },
			{ "sClass" : "left" },
			{ "sClass" : "right", "bSortable": false}
        ]
	} );
}

function khcn_RefreshTableThuyeMinh(tableId, urlData){
	$(".khcn_tooltips").tooltip( "destroy" );
	
	table = $(tableId).dataTable();
	oSettings = table.fnSettings();
	$('#khcn_ds_thuyetminhdtkhcn_processing').attr('style', 'visibility:visible');
	$.getJSON(urlData+'&a=refreshdata', null, function( json )
	{
		table.fnClearTable(this);
		for (var i=0; i<json.aaData.length; i++)
		{
			table.oApi._fnAddData(oSettings, json.aaData[i]);
		}
		oSettings.aiDisplay = oSettings.aiDisplayMaster.slice();
		table.fnDraw();
		$('#khcn_ds_thuyetminhdtkhcn_processing').attr('style', 'visibility:hidden');
	});
}

// Get thong tin
function khcn_GetThuyetMinh_ThongTinChung(pMaThuyetMinh){
	gv_processing_diglog("open","Khoa học & Công nghệ" ,"Đang xử lý ... vui lòng chờ");
	dataString = 'a=getthuyetminhinfo&hisid=<?php echo $_REQUEST["hisid"];?>&m='+pMaThuyetMinh;
	xreq = $.ajax({
	  type: 'POST', dataType: "json",
	  url: 'khcn/khcn_thuyetminhdtkhcn_process.php',
	  data: dataString,
	  success: function(data) {
		gv_processing_diglog("close");
		
		if (data.success == 1)
		{
			khcn_reset_fields_edit();
			// A1-A4
			$("#khcn_frm_edit_dtkhcn_ten_dt_viet").val(reverse_escapeJsonString(data.info.tendetaivn));
			$("#khcn_frm_edit_dtkhcn_ten_dt_anh").val(reverse_escapeJsonString(data.info.tendetaien));
			$("#khcn_frm_edit_dtkhcn_cnganhhep").val(reverse_escapeJsonString(data.info.nganhhep));
			$("#khcn_frm_edit_dtkhcn_tencapdetai").html(reverse_escapeJsonString(data.info.tencapdetai));
			$("#khcn_frm_edit_dtkhcn_capdetai").val(reverse_escapeJsonString(data.info.capdetai));
			khcn_change_capdetai(data.info.capdetai);
			$("#khcn_frm_edit_dtkhcn_loaihinhnc").val(reverse_escapeJsonString(data.info.loaihinhnc));
			$("#khcn_frm_edit_dtkhcn_thoigianthuchien").val(reverse_escapeJsonString(data.info.thoigianthuchien));
			$("#khcn_frm_edit_dtkhcn_keywords").val(reverse_escapeJsonString(data.info.keywords));
			$("#khcn_frm_edit_dtkhcn_huongdt").val(reverse_escapeJsonString(data.info.huongdt));
			$("#khcn_frm_edit_dtkhcn_dvdk").val(reverse_escapeJsonString(data.info.bomon));
			$("#khcn_frm_edit_table_nganh input[type=checkbox]").each(function() {
				var $input = $( this );
				// bo check
				$input.attr('checked', false);
				
				// duyet nhom nganh trong data kiem tra xem có đúng với checkbox hay ko
				for (var i=0; i<data.nhomnganh.length; i++)
				{
					if ($input.attr("value")==data.nhomnganh[i].manganh)
					{
						$input.attr('checked', true);
						if ($input.attr("value")=='999'){
							$("#khcn_frm_edit_nganhkhac").val(reverse_escapeJsonString(data.nhomnganh[i].nganhkhac));
						}
					}
				}
			});
			
			// A5
			if (data.info.vb_chung_minh_von_khac_link) {
				$("#khcn_file_giai_trinh_vonkhac").html("<a target=_blank href='" + reverse_escapeJsonString(data.info.vb_chung_minh_von_khac_link) + "'>"+reverse_escapeJsonString(data.info.vb_chung_minh_von_khac_name)+"</a>");
			}
			//alert(data.info.vontuco);
			$("#khcn_frm_edit_dtkhcn_tongkinhphi").autoNumeric('set',reverse_escapeJsonString(data.info.tongkinhphi));
			$("#khcn_frm_edit_dtkhcn_kinhphi_dhqg").autoNumeric('set',reverse_escapeJsonString(data.info.kinhphidhqg));
			$("#khcn_frm_edit_dtkhcn_kinhphi_huydong").autoNumeric('set',reverse_escapeJsonString(data.info.kinhphihuydong));
			$("#khcn_frm_edit_dtkhcn_kinhphi_tuco").autoNumeric('set',reverse_escapeJsonString(data.info.vontuco));
			$("#khcn_frm_edit_dtkhcn_kinhphi_khac").autoNumeric('set',reverse_escapeJsonString(data.info.vonkhac));
			$("#khcn_frm_edit_dtkhcn_tochuctaitro").val(reverse_escapeJsonString(data.info.tochuctaitrokhac));
			
			if (parseInt(data.info.capdetai) > 30 && parseInt(data.info.capdetai) < 36 ){ // Truong
				$("#khcn_frm_edit_dtkhcn_A5_kinhphitu").html("Trường ĐHBK");
			}else{ // DHQG
				$("#khcn_frm_edit_dtkhcn_A5_kinhphitu").html("ĐHQG-HCM");
			}
			
			// A6
			if (data.info.cndt_hh_hv_ho_ten!=''){
				$("#khcn_frm_edit_dtkhcn_cndt_hh_hv_ho_ten").val(reverse_escapeJsonString(data.info.cndt_hh_hv_ho_ten));
				$("#khcn_frm_edit_dtkhcn_cndt_ngay_sinh").val(reverse_escapeJsonString(data.info.cndt_ngay_sinh));
				$('input:radio[name=khcn_frm_edit_dtkhcn_cndt_phai][value='+data.info.cndt_phai+']').attr('checked', true);
				$("#khcn_frm_edit_dtkhcn_cndt_so_cmnd").val(reverse_escapeJsonString(data.info.cndt_so_cmnd));
				$("#khcn_frm_edit_dtkhcn_cndt_ngay_cap").val(reverse_escapeJsonString(data.info.cndt_ngay_cap));
				$("#khcn_frm_edit_dtkhcn_cndt_noi_cap").val(reverse_escapeJsonString(data.info.cndt_noi_cap));
				$("#khcn_frm_edit_dtkhcn_cndt_ms_thue").val(reverse_escapeJsonString(data.info.cndt_ms_thue));
				$("#khcn_frm_edit_dtkhcn_cndt_so_tai_khoan").val(reverse_escapeJsonString(data.info.cndt_so_tai_khoan));
				$("#khcn_frm_edit_dtkhcn_cndt_ngan_hang").val(reverse_escapeJsonString(data.info.cndt_ngan_hang));
				$("#khcn_frm_edit_dtkhcn_cndt_dia_chi_cq").val(reverse_escapeJsonString(data.info.cndt_dia_chi_cq));
				$("#khcn_frm_edit_dtkhcn_cndt_dien_thoai").val(reverse_escapeJsonString(data.info.cndt_dien_thoai));
				$("#khcn_frm_edit_dtkhcn_cndt_email").val(reverse_escapeJsonString(data.info.cndt_email));
				$("#khcn_frm_edit_dtkhcn_tom_tat_hd_nc").val(reverse_escapeJsonString(data.info.tom_tat_hd_nc));
			}
			else {
				$("#khcn_frm_edit_dtkhcn_fk_chu_nhiem_dt").val(reverse_escapeJsonString(data.llkh.ma_can_bo));
				$("#khcn_frm_edit_dtkhcn_cndt_hh_hv_ho_ten").val(reverse_escapeJsonString(data.llkh.ho_ten));
				$("#khcn_frm_edit_dtkhcn_cndt_ngay_sinh").val(reverse_escapeJsonString(data.llkh.ngay_sinh));
				$('input:radio[name=khcn_frm_edit_dtkhcn_cndt_phai][value='+data.llkh.phai+']').attr('checked', true);
				$("#khcn_frm_edit_dtkhcn_cndt_so_cmnd").val(reverse_escapeJsonString(data.llkh.so_cmnd));
				$("#khcn_frm_edit_dtkhcn_cndt_ngay_cap").val(reverse_escapeJsonString(data.llkh.ngay_cap));
				$("#khcn_frm_edit_dtkhcn_cndt_noi_cap").val(reverse_escapeJsonString(data.llkh.noi_cap));
				$("#khcn_frm_edit_dtkhcn_cndt_ms_thue").val(reverse_escapeJsonString(data.llkh.ma_so_thue));
				$("#khcn_frm_edit_dtkhcn_cndt_so_tai_khoan").val(reverse_escapeJsonString(data.llkh.so_tai_khoan));
				$("#khcn_frm_edit_dtkhcn_cndt_ngan_hang").val(reverse_escapeJsonString(data.llkh.ngan_hang_mo_tk));
				$("#khcn_frm_edit_dtkhcn_cndt_dia_chi_cq").val(reverse_escapeJsonString(data.llkh.dia_chi));
				$("#khcn_frm_edit_dtkhcn_cndt_dien_thoai").val(reverse_escapeJsonString(data.llkh.dien_thoai_cn));
				$("#khcn_frm_edit_dtkhcn_cndt_email").val(reverse_escapeJsonString(data.llkh.email));
				
				khcn_formA6_changed = true;	
			}
			
			if (data.info.dcndt_hh_hv_ho_ten!=''){
				$("#khcn_frm_edit_dtkhcn_dcndt_hh_hv_ho_ten").val(reverse_escapeJsonString(data.info.dcndt_hh_hv_ho_ten));
				$("#khcn_frm_edit_dtkhcn_dcndt_ngay_sinh").val(reverse_escapeJsonString(data.info.dcndt_ngay_sinh));
				$('input:radio[name=khcn_frm_edit_dtkhcn_dcndt_phai][value='+data.info.dcndt_phai+']').attr('checked', true);
				$("#khcn_frm_edit_dtkhcn_dcndt_so_cmnd").val(reverse_escapeJsonString(data.info.dcndt_so_cmnd));
				$("#khcn_frm_edit_dtkhcn_dcndt_ngay_cap").val(reverse_escapeJsonString(data.info.dcndt_ngay_cap));
				$("#khcn_frm_edit_dtkhcn_dcndt_noi_cap").val(reverse_escapeJsonString(data.info.dcndt_noi_cap));
				$("#khcn_frm_edit_dtkhcn_dcndt_ms_thue").val(reverse_escapeJsonString(data.info.dcndt_ms_thue));
				$("#khcn_frm_edit_dtkhcn_dcndt_so_tai_khoan").val(reverse_escapeJsonString(data.info.dcndt_so_tai_khoan));
				$("#khcn_frm_edit_dtkhcn_dcndt_ngan_hang").val(reverse_escapeJsonString(data.info.dcndt_ngan_hang));
				$("#khcn_frm_edit_dtkhcn_dcndt_dia_chi_cq").val(reverse_escapeJsonString(data.info.dcndt_dia_chi_cq));
				$("#khcn_frm_edit_dtkhcn_dcndt_dien_thoai").val(reverse_escapeJsonString(data.info.dcndt_dien_thoai));
				$("#khcn_frm_edit_dtkhcn_dcndt_email").val(reverse_escapeJsonString(data.info.dcndt_email));
			}
			
			// A7 - A8
			if (data.info.cqct_ten_co_quan!=''){
				$("#khcn_frm_edit_dtkhcn_cqct_ten_co_quan").val(reverse_escapeJsonString(data.info.cqct_ten_co_quan));
				$("#khcn_frm_edit_dtkhcn_cqct_ho_ten_tt").val(reverse_escapeJsonString(data.info.cqct_ho_ten_tt));
				$("#khcn_frm_edit_dtkhcn_cqct_dien_thoai").val(reverse_escapeJsonString(data.info.cqct_dien_thoai));
				$("#khcn_frm_edit_dtkhcn_cqct_fax").val(reverse_escapeJsonString(data.info.cqct_fax));
				$("#khcn_frm_edit_dtkhcn_cqct_email").val(reverse_escapeJsonString(data.info.cqct_email));
				$("#khcn_frm_edit_dtkhcn_cqct_so_tai_khoan").val(reverse_escapeJsonString(data.info.cqct_so_tai_khoan));
				$("#khcn_frm_edit_dtkhcn_cqct_kho_bac").val(reverse_escapeJsonString(data.info.cqct_kho_bac));
			}else{
				$("#khcn_frm_edit_dtkhcn_fk_cq_chu_tri").val(reverse_escapeJsonString(data.coquanchutri.ma_co_quan));
				$("#khcn_frm_edit_dtkhcn_cqct_ten_co_quan").val(reverse_escapeJsonString(data.coquanchutri.ten_co_quan));
				$("#khcn_frm_edit_dtkhcn_cqct_ho_ten_tt").val(reverse_escapeJsonString(data.coquanchutri.ho_ten_tt));
				$("#khcn_frm_edit_dtkhcn_cqct_dien_thoai").val(reverse_escapeJsonString(data.coquanchutri.dien_thoai));
				$("#khcn_frm_edit_dtkhcn_cqct_fax").val(reverse_escapeJsonString(data.coquanchutri.fax));
				$("#khcn_frm_edit_dtkhcn_cqct_email").val(reverse_escapeJsonString(data.coquanchutri.email));
				$("#khcn_frm_edit_dtkhcn_cqct_so_tai_khoan").val(reverse_escapeJsonString(data.coquanchutri.so_tai_khoan));
				$("#khcn_frm_edit_dtkhcn_cqct_kho_bac").val(reverse_escapeJsonString(data.coquanchutri.kho_bac));
				khcn_formA7A8_changed = true;	
			}
			
			$("#khcn_frm_edit_dtkhcn_fk_cq_phoi_hop_1").val(reverse_escapeJsonString(data.info.fk_cq_phoi_hop_1));
			$("#khcn_frm_edit_dtkhcn_cqph1_ten_co_quan").val(reverse_escapeJsonString(data.info.cqph1_ten_co_quan));
			$("#khcn_frm_edit_dtkhcn_cqph1_ho_ten_tt").val(reverse_escapeJsonString(data.info.cqph1_ho_ten_tt));
			$("#khcn_frm_edit_dtkhcn_cqph1_dien_thoai").val(reverse_escapeJsonString(data.info.cqph1_dien_thoai));
			$("#khcn_frm_edit_dtkhcn_cqph1_fax").val(reverse_escapeJsonString(data.info.cqph1_fax));
			$("#khcn_frm_edit_dtkhcn_cqph1_dia_chi").val(reverse_escapeJsonString(data.info.cqph1_dia_chi));
			
			$("#khcn_frm_edit_dtkhcn_fk_cq_phoi_hop_2").val(reverse_escapeJsonString(data.info.fk_cq_phoi_hop_2));
			$("#khcn_frm_edit_dtkhcn_cqph2_ten_co_quan").val(reverse_escapeJsonString(data.info.cqph2_ten_co_quan));
			$("#khcn_frm_edit_dtkhcn_cqph2_ho_ten_tt").val(reverse_escapeJsonString(data.info.cqph2_ho_ten_tt));
			$("#khcn_frm_edit_dtkhcn_cqph2_dien_thoai").val(reverse_escapeJsonString(data.info.cqph2_dien_thoai));
			$("#khcn_frm_edit_dtkhcn_cqph2_fax").val(reverse_escapeJsonString(data.info.cqph2_fax));
			$("#khcn_frm_edit_dtkhcn_cqph2_dia_chi").val(reverse_escapeJsonString(data.info.cqph2_dia_chi));
			
			// A9
			for (var i=0; i<data.nhanluc_cbgd.length; i++){
				$( "#khcn_frm_edit_dtkhcn_A9_table_nhanluc tbody:eq(0)" ).append( "<tr style='font-size:12px; border-color: #000000; border-width: 1px; border-style: solid; border-collapse:collapse;'>" +
				"<td align=left>" + data.nhanluc_cbgd[i].ma_nhan_luc + "</td>" +
				"<td align=left>" + reverse_escapeJsonString(data.nhanluc_cbgd[i].ho_ten) + "</td>" +
				"<td align=left>" + reverse_escapeJsonString(data.nhanluc_cbgd[i].shcc) + "</td>" +
				"<td align=left>" + reverse_escapeJsonString(data.nhanluc_cbgd[i].don_vi_cong_tac) + "</td>" +
				"<td align=center>" + reverse_escapeJsonString(data.nhanluc_cbgd[i].so_thang_lv_quy_doi) + "</td>" +
				"<td><button title='Xoá' class='khcn_nhanluc_remove' style='height:25px;width:30px;' onclick='khcn_remove_nhanluc( khcn_getRowIndex(this), 1); return false;'></button>&nbsp;<button title='Sửa' class='khcn_nhanluc_edit' style='height:25px;width:30px;' onclick='khcn_edit_nhanluc( khcn_getRowIndex(this), 1); return false;'></button></td>" +
				"</tr>" );
			}
			for (var i=0; i<data.nhanluc_sv.length; i++){
				$( "#khcn_frm_edit_dtkhcn_A9_table_nhanluc tbody:eq(1)" ).append( "<tr style='font-size:12px; border-color: #000000; border-width: 1px; border-style: solid; border-collapse:collapse;' >" +
				"<td align=left>" + data.nhanluc_sv[i].ma_nhan_luc + "</td>" +
				"<td align=left>" + reverse_escapeJsonString(data.nhanluc_sv[i].ho_ten)+ "</td>" +
				"<td align=left>" + reverse_escapeJsonString(data.nhanluc_sv[i].ma_sv) + "</td>" +
				"<td align=left>" + reverse_escapeJsonString(data.nhanluc_sv[i].don_vi_cong_tac) + "</td>" +
				"<td align=center>" + reverse_escapeJsonString(data.nhanluc_sv[i].so_thang_lv_quy_doi) + "</td>" +
				"<td><button class='khcn_nhanluc_remove' style='height:25px;width:30px;' onclick='khcn_remove_nhanluc( khcn_getRowIndex(this), 2);  return false;'></button>&nbsp;<button class='khcn_nhanluc_edit' style='height:25px;width:30px;' onclick='khcn_edit_nhanluc( khcn_getRowIndex(this), 2); return false;'></button></td>" +
				"</tr>" );
			}
			$("button.khcn_nhanluc_remove" ).button({ icons: {primary:'ui-icon ui-icon-trash'} });
			$("button.khcn_nhanluc_edit" ).button({ icons: {primary:'ui-icon ui-icon-pencil'} });
			
			
			// Open cua so edit
			$('#khcn_diag_edit_dtkhcn_thongtinchung').dialog('open');
		}
		else
		{
			gv_open_msg_box("Không thể truy vấn thông tin: <br/><div style='margin: 5px 0 0 5px'>" + data.msgerr+'</div>', 'alert', 250, 180, true);
		}
	  },
	  error: function(xhr, ajaxOptions, thrownError) {}
	});
}

function khcn_GetThuyetMinh_MoTaNghienCuu(pMaThuyetMinh){
	gv_processing_diglog("open","Khoa học & Công nghệ" ,"Đang xử lý ... vui lòng chờ");
	
	// get B1
	dataString = 'a=getmotanghiencuu&hisid=<?php echo $_REQUEST["hisid"];?>&m='+pMaThuyetMinh;
	xreqB1 = $.ajax({
	  type: 'POST', dataType: "json",
	  url: 'khcn/khcn_thuyetminhdtkhcn_process.php',
	  data: dataString,
	  success: function(data) {
		if (data.success == 1)
		{
			$( '#khcn_frm_edit_dtkhcn_tq_tinh_hinh_nc' ).val( reverse_escapeJsonString(data.mota.tq_tinh_hinh_nc) );
			$( '#khcn_frm_edit_dtkhcn_y_tuong_kh' ).val( reverse_escapeJsonString(data.mota.y_tuong_kh) );
			$( '#khcn_frm_edit_dtkhcn_kq_nc_so_khoi' ).val( reverse_escapeJsonString(data.mota.kq_nc_so_khoi) );
			$( '#khcn_frm_edit_dtkhcn_tai_lieu_tk' ).val( reverse_escapeJsonString(data.mota.tai_lieu_tk) );
			
			$( '#khcn_frm_edit_dtkhcn_muc_tieu_nc_vn' ).val( reverse_escapeJsonString(data.mota.muc_tieu_nc_vn) );
			$( '#khcn_frm_edit_dtkhcn_muc_tieu_nc_en' ).val( reverse_escapeJsonString(data.mota.muc_tieu_nc_en) );
			
			$( '#khcn_frm_edit_dtkhcn_pa_phoi_hop' ).val( reverse_escapeJsonString(data.mota.pa_phoi_hop) );
			
			// B5.2
			if (data.mota.noi_dung_nc==''){ 
				// khoi tao gia tri cho B5.2
				data.mota.noi_dung_nc = "Liệt kê và mô tả chi tiết nội dung nghiên cứu<p><b>Nội dung 1:</b><br><b>Mục tiêu nội dung 1</b> (Bám sát và định hướng theo mục tiêu chung...)<br><b>Chỉ tiêu đánh giá</b> (sản phẩm của nội dung 1: ấn phẩm khoa học, đăng ký sỡ hữu trí tuệ,...)<br><b>Kế hoạch thực hiện</b> (Mô tả các hoạt động, giới hạn đối tượng, ý nghĩa, phân công trách nhiệm từng thành viên, sử dụng các nguồn lực và dự kiến các mốc thời gian...)<br><b>Phương pháp</b> (Điểm mới, giới hạn, dự kiến khó khăn, phương án thay thế, quy trình cụ thể...)<br><b>Phân tích và diễn giải số liệu thu được</b></p><p><b>Nội dung 2: ...</b></p>";
			}
			$( '#khcn_frm_edit_dtkhcn_noi_dung_nc' ).val(reverse_escapeJsonString(data.mota.noi_dung_nc));
				
			// B4
			for (var i=0; i<data.chuyengianc.length; i++){
				khcn_add_table_chuyengianc(data.chuyengianc[i]);
			}
			$("button.khcn_chuyengianc_remove" ).button({ icons: {primary:'ui-icon ui-icon-trash'} });
			
			// B6.1
			for (var i=0; i<data.anphamkhoahoc.length; i++){
				khcn_add_table_anphamkhoahoc(data.anphamkhoahoc[i]);
				khcn_formB6_1_changed = true;
			}
			$("button.khcn_anphamkhoahoc_remove" ).button({ icons: {primary:'ui-icon ui-icon-trash'} });
			
			// B6.2
			for (var i=0; i<data.sohuutritue.length; i++){
				khcn_add_table_sohuutritue(data.sohuutritue[i]);
			}
			$("button.khcn_sohuutritue_remove" ).button({ icons: {primary:'ui-icon ui-icon-trash'} });
			
			// san pham mem
			for (var i=0; i<data.sanphammem.length; i++){
				khcn_add_table_sanphammem(data.sanphammem[i]);
			}
			$("button.khcn_sanphammem_remove" ).button({ icons: {primary:'ui-icon ui-icon-trash'} });
			
			// san pham cung
			for (var i=0; i<data.sanphamcung.length; i++){
				khcn_add_table_sanphamcung(data.sanphamcung[i]);
			}
			$("button.khcn_sanphamcung_remove" ).button({ icons: {primary:'ui-icon ui-icon-trash'} });
			$('#khcn_frm_reg_sanphamcung_mucchatluong').val( reverse_escapeJsonString(data.mota.muc_cl_sp_dang_ii) );
			
			// ket qua dao tao
			for (var i=0; i<data.ketquadaotao.length; i++){
				khcn_add_table_ketquadaotao(data.ketquadaotao[i]);
			}
			$("button.khcn_ketquadaotao_remove" ).button({ icons: {primary:'ui-icon ui-icon-trash'} });
			
			// B7
			$( '#khcn_frm_edit_dtkhcn_ud_kqnc_chuyen_giao' ).val( reverse_escapeJsonString(data.mota.ud_kqnc_chuyen_giao) );
			$( '#khcn_frm_edit_dtkhcn_ud_kqnc_lv_dao_tao' ).val( reverse_escapeJsonString(data.mota.ud_kqnc_lv_dao_tao) );
			$( '#khcn_frm_edit_dtkhcn_ud_kqnc_sxkd' ).val( reverse_escapeJsonString(data.mota.ud_kqnc_sxkd) );
			
			// B8
			for (var i=0; i<data.khoanchiphi.length; i++){
				khcn_add_table_khoanchiphi(data.khoanchiphi[i]);
			}
			if (data.mota.fk_cap_de_tai>20 && data.mota.fk_cap_de_tai<25){ // cap DHQG
				$(".khcn_b8_kp_de_nghi_noicap").html("ĐHQG-HCM");
				$("#khcn_b8_phuluc_khoanchi").attr("href", "./khcn/templ/R01_phu_luc_giai_trinh_khoan_chi.docx");
			}else if (data.mota.fk_cap_de_tai>30 && data.mota.fk_cap_de_tai<36){
				$(".khcn_b8_kp_de_nghi_noicap").html("Trường");
				$("#khcn_b8_phuluc_khoanchi").attr("href", "./khcn/templ/bm03_khcn_08_du_toan.xls");
			}
			$("button.khcn_khoanchiphi_edit" ).button({ icons: {primary:'ui-icon ui-icon-pencil'} });
			$("#khcn_file_giai_trinh_khoan_chi").html("<a target=_blank href='" + reverse_escapeJsonString(data.mota.phu_luc_giai_trinh_link) + "'>"+reverse_escapeJsonString(data.mota.phu_luc_giai_trinh_name)+"</a>");
			
			gv_processing_diglog("close");
			$( '#khcn_diag_edit_dtkhcn_motanghiencuu' ).dialog('open');
		}
		else
		{
			gv_processing_diglog("close");
			gv_open_msg_box("Không thể truy vấn thông tin: <br><div style='margin: 5px 0 0 5px'>" + data.msgerr+'</div>', 'alert', 250, 180, true);
		}
	  }
	});
	
}

function khcn_GetCoQuanInfo(pMaCQ){
	gv_processing_diglog("open","Khoa học & Công nghệ" ,"Đang xử lý ... vui lòng chờ");
	return xreq = $.ajax({
	  type: 'POST', dataType: "json",
	  url: 'khcn/khcn_thuyetminhdtkhcn_process.php',
	  data: 'a=getcq&hisid=<?php echo $_REQUEST["hisid"];?>&mcq='+pMaCQ,
	  success: function(data) {
		gv_processing_diglog("close");
		return jQuery.parseJSON(data);
	  }
	});
}

function khcn_GetLLKH(pSHCC){
	gv_processing_diglog("open","Khoa học & Công nghệ" ,"Đang xử lý ... vui lòng chờ");
	return xreq = $.ajax({
	  type: 'POST', dataType: "json",
	  url: 'khcn/khcn_thuyetminhdtkhcn_process.php',
	  data: 'a=getllkh&hisid=<?php echo $_REQUEST["hisid"];?>&m='+pSHCC,
	  success: function(data) {
		gv_processing_diglog("close");
		return jQuery.parseJSON(data);
	  }
	});
}

// Reset fields
function khcn_reset_fields_edit(){
	// A1-A5
	$("#khcn_frm_edit_dtkhcn_A1_A4").find('input[type=text], textarea, select').val('');
	$("#khcn_frm_reg_table_nganh input[type=checkbox]").each(function() {
		$( this ).attr('checked', false);
	});
	
	// A5
	$("#khcn_frm_edit_dtkhcn_A5").find('input[type=text], textarea, select').val('');
	
	$("#khcn_frm_edit_dtkhcn_tongkinhphi, #khcn_frm_edit_dtkhcn_kinhphi_dhqg, #khcn_frm_edit_dtkhcn_kinhphi_huydong, #khcn_frm_edit_dtkhcn_kinhphi_tuco, #khcn_frm_edit_dtkhcn_kinhphi_khac").autoNumeric('set',0);
	
	
	// A6
	$("#khcn_frm_edit_dtkhcn_A6").find('input[type=text], textarea, select').val('');
	
	// A7-A8
	$("#khcn_frm_edit_dtkhcn_A7_A8").find('input[type=text], textarea, select').val('');
	
	// A9
	$( "#khcn_frm_edit_dtkhcn_A9_table_nhanluc tbody" ).html( "" );
	//$( "#khcn_frm_edit_dtkhcn_A9_table_nhanluc tbody:eq(1)" ).html( "" );
	
}

function khcn_reset_fields_edit_mota(){
	// B1-B4
	$("#khcn_frm_edit_dtkhcn_B1, #khcn_frm_edit_dtkhcn_B2, #khcn_frm_edit_dtkhcn_B3, #khcn_frm_edit_dtkhcn_B4, #khcn_frm_edit_dtkhcn_B6_2, #khcn_frm_edit_dtkhcn_B7").find('input[type=text], textarea, select').val('');
	
	// B4.1
	$( "#khcn_frm_edit_dtkhcn_B4_1_table_chuyengia tbody" ).html( "" );
	
	// B6.1
	$( "#khcn_frm_edit_dtkhcn_B6_1_table_an_pham_kh tbody" ).html( "" );
	khcn_formB6_1_changed = false;
	
	// B6.2
	$( "#khcn_frm_edit_dtkhcn_B6_2_table_sohuutritue tbody" ).html( "" );
	$( "#khcn_frm_edit_dtkhcn_B6_2_table_sanphammem tbody" ).html( "" );
	$( "#khcn_frm_edit_dtkhcn_B6_2_table_sanphamcung tbody" ).html( "" );
	// B6.3
	$( "#khcn_frm_edit_dtkhcn_B6_3_table_ketquadaotao tbody" ).html( "" );
	
	// B8
	$( "#khcn_frm_edit_dtkhcn_B8_table_tonghopkinhphi tbody" ).html( "" );
}

function khcn_reset_fields_reg(){
	$("#khcn_frm_reg_dtkhcn_ten_dt_viet, #khcn_reg_edit_dtkhcn_ten_dt_anh, #khcn_frm_reg_dtkhcn_cnganhhep, #khcn_frm_reg_dtkhcn_keywords, #khcn_frm_reg_dtkhcn_huongdt").val('');
	$("#khcn_frm_reg_dtkhcn_loaihinhnc, #khcn_frm_reg_dtkhcn_tongkinhphi, #khcn_frm_reg_dtkhcn_thoigianthuchien, #khcn_frm_reg_nganhkhac").val('');
	khcn_change_capdetai($("#khcn_frm_reg_dtkhcn_capdetai").val());
	$("#khcn_frm_edit_table_nganh input[type=checkbox]").each(function() {
		$( this ).attr('checked', false);
	});
}

// Update thong tin chung
function khcn_update_mota(pActivePanelID){
	switch (pActivePanelID){
		case 'tabs-B1': 
			return khcn_update_mota_B1();
			break;
		case 'tabs-B2': 
			return khcn_update_mota_B2();
			break;	
		case 'tabs-B3': 
			return khcn_update_mota_B3();
			break;
		case 'tabs-B4': 
			return khcn_update_mota_B4();
			break;
		case 'tabs-B5_1': 
			return khcn_update_mota_B5_1();
			break;
		case 'tabs-B5_2': 
			return khcn_update_mota_B5_2();
			break;
		case 'tabs-B5_3': 
			return khcn_update_mota_B5_3();
			break;
		case 'tabs-B6_1': 
			//alert(1);
			return khcn_update_mota_B6_1();
			break;
		case 'tabs-B6_2': 
			return khcn_update_mota_B6_2();
			break;
		case 'tabs-B7': 
			return khcn_update_mota_B7();
			break;
	}
}

function khcn_update_thongtinchung(pActivePanelID){
	switch (pActivePanelID){
		case 'tabs-A1-A4': 
			return khcn_update_thongtinchung_A1_A4();
			break;
		case 'tabs-A5': 
			return khcn_update_thongtinchung_A5();
			break;
		case 'tabs-A6': 
			return khcn_update_thongtinchung_A6();
			break;	
		case 'tabs-A7-A8': 
			return khcn_update_thongtinchung_A7_A8();
			break;	
	}
}

function khcn_update_thongtinchung_A1_A4(){
	bValid = true;
	var bNganhkhac = true,
		
		khcn_a1a4_capdt 		= $("#khcn_frm_edit_dtkhcn_capdetai"),
		khcn_a1a4_tendt_viet 	= $("#khcn_frm_edit_dtkhcn_ten_dt_viet"),
		khcn_a1a4_tendt_anh		= $("#khcn_frm_edit_dtkhcn_ten_dt_anh"),
		khcn_a1a4_keywords		= $("#khcn_frm_edit_dtkhcn_keywords"),
		khcn_a1a4_huongdt		= $("#khcn_frm_edit_dtkhcn_huongdt"),
		khcn_a1a4_nganhkhac		= $("#khcn_frm_edit_nganhkhac"),
		khcn_a1a4_cnganhhep		= $("#khcn_frm_edit_dtkhcn_cnganhhep"),
		khcn_a1a4_loaihinhnc	= $("#khcn_frm_edit_dtkhcn_loaihinhnc"),
		khcn_a1a4_thoigianthuchien	= $("#khcn_frm_edit_dtkhcn_thoigianthuchien"),
		khcn_a1a4_nganh			= $("#khcn_frm_edit_dtkhcn_nganh"),
		khcn_a1a4_bomon			= $("#khcn_frm_edit_dtkhcn_dvdk"),
		khcn_a1a4_allFields = $([]).add(khcn_a1a4_tendt_viet).add(khcn_a1a4_tendt_anh).add(khcn_a1a4_keywords).add(khcn_a1a4_huongdt)
			.add(khcn_a1a4_nganh).add(khcn_a1a4_cnganhhep).add(khcn_a1a4_loaihinhnc).add(khcn_a1a4_thoigianthuchien).add(khcn_a1a4_nganhkhac),
		khcn_a1a4_jtips	= $("#khcn_a1a4_tips");
		
	khcn_a1a4_allFields.removeClass( "ui-state-error" );
	bValid = bValid && checkLength( khcn_a1a4_tendt_viet, "\"Tên đề tài tiếng Việt\"", 1, 1000, 0, khcn_a1a4_jtips);
	bValid = bValid && checkLength( khcn_a1a4_tendt_anh, "\"Tên đề tài tiếng Anh\"", 1, 1000, 0, khcn_a1a4_jtips);
	bValid = bValid && checkLength( khcn_a1a4_keywords, "\"Keywords\"", 1, 500, 0, khcn_a1a4_jtips);
	bValid = bValid && checkLength( khcn_a1a4_huongdt, "\"Hướng đề tài\"", 1, 1000, 0, khcn_a1a4_jtips);
	bValid = bValid && checkLength( khcn_a1a4_bomon, "\"Đơn vị đăng ký\"", 1, 5, 0, khcn_a1a4_jtips);
	
	if (bValid){
		bValid = false;
		$("#khcn_frm_edit_table_nganh input[type=checkbox]").each(function() {
			if ($(this).attr("checked")=='checked') {
				if ($(this).attr("value")=='999'){
					bValid = checkLength( khcn_a1a4_nganhkhac, "\"Ngành khác\"", 1, 250, 0, khcn_a1a4_jtips);
				}else{
					bValid = true;
				}
			}else{
				if ($(this).attr("value")=='999'){
					if (khcn_a1a4_nganhkhac.val()){
						 $(this).attr("checked", "checked");
						 bValid = true;
					}
				}
			}
		});
		if (!bValid){
			khcn_a1a4_nganh.addClass( "ui-state-error" );
			updateTips('Vui lòng chọn Ngành - Nhóm ngành',khcn_a1a4_jtips);
			khcn_a1a4_nganh.focus();
		}
	}
	if (khcn_a1a4_capdt.val()>20 && khcn_a1a4_capdt.val()<25){
		bValid = bValid && checkLength( khcn_a1a4_cnganhhep, "\"Chuyên ngành hẹp\"", 1, 250, 0, khcn_a1a4_jtips);				
	}
	bValid = bValid && checkLength( khcn_a1a4_loaihinhnc, "\"Loại hình nghiên cứu\"", 0, 10, 0, khcn_a1a4_jtips);
	bValid = bValid && checkLength( khcn_a1a4_thoigianthuchien, "\"Thời gian thực hiện\"", 1, 3, 0, khcn_a1a4_jtips);
	
	if (bValid && khcn_formA1A4_changed) {
		gv_processing_diglog("open","Khoa học & Công nghệ", "Đang lưu dữ liệu A1-A4 ...");
		dataString = $("#khcn_frm_edit_dtkhcn_A1_A4").serialize() + '&a=updatea1a4&m='+khcn_matm_selected+'&c='+khcn_numnganh;
		xreq = $.ajax({
		  type: 'POST', dataType: "json", data: dataString,
		  url: khcn_linkdata,
		  success: function(data) {
			gv_processing_diglog("close");
			if (data.success == 1){
				khcn_formA1A4_changed = false;
				
				var nTr = khcn_fnGetSelected(oTableThuyetMinhDTKHCN);
				nTr[0].cells[khcn_tmdt_col_idx['tendt']].innerHTML = reverse_escapeJsonString(data.tendetaivn);
				nTr[0].cells[khcn_tmdt_col_idx['nhomnganh']].innerHTML = reverse_escapeJsonString(data.nganhnhomnganh);
				nTr[0].cells[khcn_tmdt_col_idx['huongdt']].innerHTML = reverse_escapeJsonString(data.huongdt);
				nTr[0].cells[khcn_tmdt_col_idx['keywords']].innerHTML = reverse_escapeJsonString(data.keywords);
				nTr[0].cells[khcn_tmdt_col_idx['capdetai']].innerHTML = reverse_escapeJsonString(data.capdetai);
				nTr[0].cells[khcn_tmdt_col_idx['tenbomon']].innerHTML = reverse_escapeJsonString(data.tenbomon);
				nTr[0].cells[khcn_tmdt_col_idx['loaihinhnc']].innerHTML = reverse_escapeJsonString(data.loaihinhnc);
				nTr[0].cells[khcn_tmdt_col_idx['thoigian']].innerHTML = reverse_escapeJsonString(data.thoigianthuchien);
				nTr[0].cells[khcn_tmdt_col_idx['kinhphi']].innerHTML = reverse_escapeJsonString(data.tongkinhphi);

				//$( "#khcn_diag_edit_dtkhcn_thongtinchung" ).dialog( "close" );
				return true;
			}else{
				gv_open_msg_box("<font style='color:red;'>Không thể cập nhật thông tin vì:</font> <br/><div style='margin: 5px 0 0 5px'>" + reverse_escapeJsonString(data.msgerr) +'</div>', 'alert', 250, 180, true);
			}
		  }
		});
	}else{
		return bValid;
	}
}

function khcn_update_thongtinchung_A5(){
	bValid = true;
	var khcn_a5_kpdhqg 			= $("#khcn_frm_edit_dtkhcn_kinhphi_dhqg"),
		khcn_a5_kptuco			= $("#khcn_frm_edit_dtkhcn_kinhphi_tuco"),
		khcn_a5_kpkhac			= $("#khcn_frm_edit_dtkhcn_kinhphi_khac"),
		khcn_a5_tochuctaitroi	= $("#khcn_frm_edit_dtkhcn_tochuctaitro"),
		
		khcn_a5_allFields = $([]).add(khcn_a5_kpdhqg).add(khcn_a5_kptuco).add(khcn_a5_kpkhac).add(khcn_a5_tochuctaitroi),
		khcn_a5_jtips	= $("#khcn_a5_tips");
		
	khcn_a5_allFields.removeClass( "ui-state-error" );
	
	if (bValid && khcn_formA5_changed){
	
		//var kpdhqg = parseInt($('#khcn_frm_edit_dtkhcn_kinhphi_dhqg').val().replace(/,/g, ''));
		var kpdhqg = $('#khcn_frm_edit_dtkhcn_kinhphi_dhqg').autoNumeric('get');
		
		//alert (kpdhqg);
		switch ($('#khcn_frm_edit_dtkhcn_capdetai').val()){
			case "21": // loai A tren 1 ty
				if (!(kpdhqg>1000)){
					gv_open_msg_box("<font color=red>Chú ý: Với <b>đề tài loại A</b> thì <b>kinh phí ĐHQG phải trên 1000 triệu (1 tỷ)</b>.</font>", 'alert', 250, 180, true);
					return false;
				}
				break;
			case "22": // loai B
				if (kpdhqg>1000 || kpdhqg<200){
					gv_open_msg_box("<font color=red>Chú ý: Với <b>đề tài loại B</b> thì <b>kinh phí ĐHQG phải từ 200 triệu đến 1000 triệu (1 tỷ)</b>.</font>", 'alert', 250, 180, true);
					return false;
				}
				break;
			case "23": // loai C
				if (kpdhqg>200){
					gv_open_msg_box("<font color=red>Chú ý: Với <b>đề tài loại C</b> thì <b>kinh phí ĐHQG phải từ 200 triệu trở xuống</b>.</font>", 'alert', 250, 180, true);
					return false;
				}
				break;
		}
		
		if (parseInt($('#khcn_frm_edit_dtkhcn_kinhphi_khac').val().replace(/,/g, ''))>0 && $('#khcn_file_giai_trinh_vonkhac').html()==''){
			gv_open_msg_box("<font color=red>Chú ý bạn chưa đính kèm văn bản chứng minh cho nguồn vốn huy động khác.</font>", 'alert', 250, 180, true);
			//return false;
		}
		
		gv_processing_diglog("open","Khoa học & Công nghệ", "Đang lưu dữ liệu A1-A5 ...");
		dataString = 'a=updatea5&m='+khcn_matm_selected
		+ '&khcn_frm_edit_dtkhcn_tochuctaitro='+ encodeURIComponent($('#khcn_frm_edit_dtkhcn_tochuctaitro').val())
		+ '&khcn_frm_edit_dtkhcn_tongkinhphi='+ $('#khcn_frm_edit_dtkhcn_tongkinhphi').autoNumeric('get')
		+ '&khcn_frm_edit_dtkhcn_kinhphi_huydong='+ $('#khcn_frm_edit_dtkhcn_kinhphi_huydong').autoNumeric('get')
		+ '&khcn_frm_edit_dtkhcn_kinhphi_dhqg='+ $('#khcn_frm_edit_dtkhcn_kinhphi_dhqg').autoNumeric('get')
		+ '&khcn_frm_edit_dtkhcn_kinhphi_tuco='+ $('#khcn_frm_edit_dtkhcn_kinhphi_tuco').autoNumeric('get')
		+ '&khcn_frm_edit_dtkhcn_kinhphi_khac='+ $('#khcn_frm_edit_dtkhcn_kinhphi_khac').autoNumeric('get');
		xreq = $.ajax({
		  type: 'POST', dataType: "json", data: dataString,
		  url: khcn_linkdata,
		  success: function(data) {
			gv_processing_diglog("close");
			if (data.success == 1){
				khcn_formA5_changed = false;
				
				var nTr = khcn_fnGetSelected(oTableThuyetMinhDTKHCN);
				nTr[0].cells[8].innerHTML = reverse_escapeJsonString(data.tongkinhphi);
				
				return true;
			}else{
				gv_open_msg_box("<font style='color:red;'>Không thể cập nhật thông tin vì:</font> <br/><div style='margin: 5px 0 0 5px'>" + reverse_escapeJsonString(data.msgerr) +'</div>', 'alert', 250, 180, true);
			}
		  }
		});
	}else{
		return bValid;
	}
}

function khcn_update_thongtinchung_A6(){
	bValid = true;
	var khcn_a6_hoten 		= $("#khcn_frm_edit_dtkhcn_cndt_hh_hv_ho_ten"),
		khcn_a6_masothue	= $("#khcn_frm_edit_dtkhcn_cndt_ms_thue"),
		khcn_a6_diachicq	= $("#khcn_frm_edit_dtkhcn_cndt_dia_chi_cq"),
		khcn_a6_dienthoai	= $("#khcn_frm_edit_dtkhcn_cndt_dien_thoai"),
		khcn_a6_email 		= $("#khcn_frm_edit_dtkhcn_cndt_email"),
		khcn_a6_cmnd		= $("#khcn_frm_edit_dtkhcn_cndt_so_cmnd"),
		khcn_a6_ngaycap		= $("#khcn_frm_edit_dtkhcn_cndt_ngay_cap"),
		khcn_a6_noicap		= $("#khcn_frm_edit_dtkhcn_cndt_noi_cap"),
		khcn_a6_ngaysinh 	= $("#khcn_frm_edit_dtkhcn_cndt_ngay_sinh"),
		
		
		khcn_a6_hoten_dcn 		= $("#khcn_frm_edit_dtkhcn_dcndt_hh_hv_ho_ten"),
		khcn_a6_masothue_dcn	= $("#khcn_frm_edit_dtkhcn_dcndt_ms_thue"),
		khcn_a6_diachicq_dcn	= $("#khcn_frm_edit_dtkhcn_dcndt_dia_chi_cq"),
		khcn_a6_dienthoai_dcn	= $("#khcn_frm_edit_dtkhcn_dcndt_dien_thoai"),
		khcn_a6_email_dcn		= $("#khcn_frm_edit_dtkhcn_dcndt_email"),	
		khcn_a6_cmnd_dcn		= $("#khcn_frm_edit_dtkhcn_dcndt_so_cmnd"),
		khcn_a6_ngaycap_dcn		= $("#khcn_frm_edit_dtkhcn_dcndt_ngay_cap"),
		khcn_a6_noicap_dcn		= $("#khcn_frm_edit_dtkhcn_dcndt_noi_cap"),
		khcn_a6_ngaysinh_dcn	= $("#khcn_frm_edit_dtkhcn_dcndt_ngay_sinh"),

		khcn_a6_allFields = $([]).add(khcn_a6_hoten).add(khcn_a6_masothue).add(khcn_a6_diachicq).add(khcn_a6_dienthoai)
		.add(khcn_a6_cmnd).add(khcn_a6_ngaycap).add(khcn_a6_noicap).add(khcn_a6_ngaysinh)
		.add(khcn_a6_email).add(khcn_a6_masothue_dcn).add(khcn_a6_diachicq_dcn).add(khcn_a6_dienthoai_dcn).add(khcn_a6_email_dcn)
		.add(khcn_a6_cmnd_dcn).add(khcn_a6_ngaycap_dcn).add(khcn_a6_noicap_dcn).add(khcn_a6_ngaysinh_dcn),
		
		khcn_a6_jtips	= $("#khcn_a6_tips");
		
	khcn_a6_allFields.removeClass( "ui-state-error" );
	
	// check chu nhiem
	bValid = bValid && checkLength( khcn_a6_hoten, "\"Họ và tên chủ nhiệm\"", 0, 50, 0, khcn_a6_jtips);
	bValid = bValid && checkLength( khcn_a6_ngaysinh, "\"Ngày sinh chủ nhiệm\"", 0, 10, 0, khcn_a6_jtips);
	bValid = bValid && checkDate(khcn_a6_ngaysinh, 'dd/mm/yy', 'Ngày sinh không chính xác', khcn_a6_jtips);
			
	bValid = bValid && checkLength( khcn_a6_cmnd, "\"Số CMND chủ nhiệm\"", 0, 10, 0, khcn_a6_jtips);
	bValid = bValid && checkLength( khcn_a6_ngaycap, "\"Ngày cấp CMND chủ nhiệm\"", 0, 10, 0, khcn_a6_jtips);
	bValid = bValid && checkDate(khcn_a6_ngaycap, 'dd/mm/yy', 'Ngày cấp CMND không chính xác', khcn_a6_jtips);
	
	bValid = bValid && checkLength( khcn_a6_noicap, "\"Nơi cấp CMND chủ nhiệm\"", 0, 10, 0, khcn_a6_jtips);
	bValid = bValid && checkLength( khcn_a6_masothue, "\"Mã số thuế chủ nhiệm\"", 0, 10, 0, khcn_a6_jtips);
	bValid = bValid && checkLength( khcn_a6_diachicq, "\"Địa chỉ cơ quan chủ nhiệm\"", 0, 200, 0, khcn_a6_jtips);
	bValid = bValid && checkLength( khcn_a6_dienthoai, "\"Điện thoại chủ nhiệm\"", 0, 50, 0, khcn_a6_jtips);
	bValid = bValid && checkLength( khcn_a6_email, "\"Email chủ nhiệm\"", 0, 100, 0, khcn_a6_jtips);
	bValid = bValid && checkRegexp( khcn_a6_email, /^((([a-z]|\d|[!#\$%&'\*\+\-\/=\?\^_`{\|}~]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])+(\.([a-z]|\d|[!#\$%&'\*\+\-\/=\?\^_`{\|}~]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])+)*)|((\x22)((((\x20|\x09)*(\x0d\x0a))?(\x20|\x09)+)?(([\x01-\x08\x0b\x0c\x0e-\x1f\x7f]|\x21|[\x23-\x5b]|[\x5d-\x7e]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(\\([\x01-\x09\x0b\x0c\x0d-\x7f]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF]))))*(((\x20|\x09)*(\x0d\x0a))?(\x20|\x09)+)?(\x22)))@((([a-z]|\d|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(([a-z]|\d|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])*([a-z]|\d|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])))\.)+(([a-z]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(([a-z]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])*([a-z]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])))\.?$/i,"Email chủ nhiệm không đúng định dạng email, vd: pgs@hcmut.edu.vn", khcn_a6_jtips);
	
	// Check đồng chủ nhiệm
	if (khcn_a6_hoten_dcn.val()){
		//bValid = bValid && checkLength( khcn_a6_hoten, "\"Họ và tên\"", 0, 50, 0, khcn_a6_jtips);
		bValid = bValid && checkLength( khcn_a6_ngaysinh_dcn, "\"Ngày sinh đồng chủ nhiệm\"", 0, 10, 0, khcn_a6_jtips);
		bValid = bValid && checkDate(khcn_a6_ngaysinh_dcn, 'dd/mm/yy', 'Ngày sinh đồng chủ nhiệm không chính xác', khcn_a6_jtips);
		bValid = bValid && checkLength( khcn_a6_cmnd_dcn, "\"Số CMND đồng chủ nhiệm\"", 0, 10, 0, khcn_a6_jtips);
		bValid = bValid && checkLength( khcn_a6_ngaycap_dcn, "\"Ngày cấp CMND đồng chủ nhiệm\"", 0, 10, 0, khcn_a6_jtips);
		bValid = bValid && checkDate(khcn_a6_ngaycap_dcn, 'dd/mm/yy', 'Ngày cấp CMND đồng chủ nhiệm không chính xác', khcn_a6_jtips);
		
		bValid = bValid && checkLength( khcn_a6_noicap_dcn, "\"Nơi cấp CMND đồng chủ nhiệm\"", 0, 10, 0, khcn_a6_jtips);
		bValid = bValid && checkLength( khcn_a6_masothue_dcn, "\"Mã số thuế đồng chủ nhiệm\"", 0, 10, 0, khcn_a6_jtips);
		bValid = bValid && checkLength( khcn_a6_diachicq_dcn, "\"Địa chỉ cơ quan đồng chủ nhiệm\"", 0, 200, 0, khcn_a6_jtips);
		bValid = bValid && checkLength( khcn_a6_dienthoai_dcn, "\"Điện thoại đồng chủ nhiệm\"", 0, 50, 0, khcn_a6_jtips);
		bValid = bValid && checkLength( khcn_a6_email_dcn, "\"Email đồng chủ nhiệm\"", 0, 100, 0, khcn_a6_jtips);
		bValid = bValid && checkRegexp( khcn_a6_email_dcn, /^((([a-z]|\d|[!#\$%&'\*\+\-\/=\?\^_`{\|}~]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])+(\.([a-z]|\d|[!#\$%&'\*\+\-\/=\?\^_`{\|}~]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])+)*)|((\x22)((((\x20|\x09)*(\x0d\x0a))?(\x20|\x09)+)?(([\x01-\x08\x0b\x0c\x0e-\x1f\x7f]|\x21|[\x23-\x5b]|[\x5d-\x7e]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(\\([\x01-\x09\x0b\x0c\x0d-\x7f]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF]))))*(((\x20|\x09)*(\x0d\x0a))?(\x20|\x09)+)?(\x22)))@((([a-z]|\d|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(([a-z]|\d|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])*([a-z]|\d|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])))\.)+(([a-z]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(([a-z]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])*([a-z]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])))\.?$/i,"Email đồng chủ nhiệm không đúng định dạng email, vd: pgs@hcmut.edu.vn", khcn_a6_jtips);
	}
	
	if (bValid && khcn_formA6_changed){
		gv_processing_diglog("open","Khoa học & Công nghệ", "Đang lưu dữ liệu A6 ...");
		dataString = $("#khcn_frm_edit_dtkhcn_A6").serialize() + '&a=updatea6&m='+khcn_matm_selected;
		xreq = $.ajax({
		  type: 'POST', dataType: "json", data: dataString,
		  url: khcn_linkdata,
		  success: function(data) {
			gv_processing_diglog("close");
			if (data.success == 1){
				khcn_formA6_changed = false;
				return true;
			}else{
				gv_open_msg_box("<font style='color:red;'>Không thể cập nhật thông tin vì:</font> <br/><div style='margin: 5px 0 0 5px'>" + reverse_escapeJsonString(data.msgerr) +'</div>', 'alert', 250, 180, true);
			}
		  }
		});
	}else{
		return bValid;
	}
}

function khcn_update_thongtinchung_A7_A8(){
	if (khcn_formA7A8_changed){
		gv_processing_diglog("open","Khoa học & Công nghệ", "Đang lưu dữ liệu A7-A8 ...");
		dataString = $("#khcn_frm_edit_dtkhcn_A7_A8").serialize() + '&a=updatea7a8&m='+khcn_matm_selected;
		xreq = $.ajax({
		  type: 'POST', dataType: "json", data: dataString,
		  url: khcn_linkdata,
		  success: function(data) {
			gv_processing_diglog("close");
			if (data.success == 1){
				khcn_formA7A8_changed = false;
				return true;
			}else{
				gv_open_msg_box("<font style='color:red;'>Không thể cập nhật thông tin vì:</font> <br/><div style='margin: 5px 0 0 5px'>" + reverse_escapeJsonString(data.msgerr) +'</div>', 'alert', 250, 180, true);
			}
		  }
		});
	}
}

function khcn_update_mota_B1(){
	if (khcn_formB1_changed){
		//alert ($("#khcn_frm_edit_dtkhcn_tq_tinh_hinh_nc").val());
		gv_processing_diglog("open","Khoa học & Công nghệ", "Đang lưu dữ liệu B1 ...");
		dataString = 'tq_tinh_hinh_nc='+encodeURIComponent($("#khcn_frm_edit_dtkhcn_tq_tinh_hinh_nc").val()) + '&a=updateB1&m='+khcn_matm_selected;		
		xreq = $.ajax({
		  type: 'POST', dataType: "json", data: dataString,
		  url: khcn_linkdata,
		  success: function(data) {
			gv_processing_diglog("close");
			if (data.success == 1){
				khcn_formB1_changed = false;
				return true;
			}else{
				gv_open_msg_box("<font style='color:red;'>Không thể cập nhật thông tin vì:</font> <br/><div style='margin: 5px 0 0 5px'>" + reverse_escapeJsonString(data.msgerr)+'</div>', 'alert', 250, 180, true);
			}
		  }
		});
	}
}

function khcn_update_mota_B2(){
	if (khcn_formB2_changed){
		gv_processing_diglog("open","Khoa học & Công nghệ", "Đang lưu dữ liệu...");
		dataString = 'y_tuong_kh='+encodeURIComponent($("#khcn_frm_edit_dtkhcn_y_tuong_kh").val()) + '&a=updateB2&m='+khcn_matm_selected;		
		xreq = $.ajax({
		  type: 'POST', dataType: "json", data: dataString,
		  url: khcn_linkdata,
		  success: function(data) {
			gv_processing_diglog("close");
			if (data.success == 1){
				khcn_formB2_changed = false;
				return true;
			}else{
				gv_open_msg_box("<font style='color:red;'>Không thể cập nhật thông tin vì:</font><br/><div style='margin: 5px 0 0 5px'>" + reverse_escapeJsonString(data.msgerr)+'</div>', 'alert', 250, 180, true);
			}
		  }
		});
	}
}

function khcn_update_mota_B3(){
	if (khcn_formB3_changed){
		gv_processing_diglog("open","Khoa học & Công nghệ", "Đang lưu dữ liệu...");
		dataString = 'kq_nc_so_khoi='+encodeURIComponent($("#khcn_frm_edit_dtkhcn_kq_nc_so_khoi").val()) + '&a=updateB3&m='+khcn_matm_selected;		
		xreq = $.ajax({
		  type: 'POST', dataType: "json", data: dataString,
		  url: khcn_linkdata,
		  success: function(data) {
			gv_processing_diglog("close");
			if (data.success == 1){
				khcn_formB3_changed = false;
				return true;
			}else{
				gv_open_msg_box("<font style='color:red;'>Không thể cập nhật thông tin vì:</font> <br/><div style='margin: 5px 0 0 5px'>" + reverse_escapeJsonString(data.msgerr)+'</div>', 'alert', 250, 180, true);
			}
		  }
		});
	}
}

function khcn_update_mota_B4(){
	if (khcn_formB4_changed){
		gv_processing_diglog("open","Khoa học & Công nghệ", "Đang lưu dữ liệu...");
		dataString = 'tai_lieu_tk='+encodeURIComponent($("#khcn_frm_edit_dtkhcn_tai_lieu_tk").val()) + '&a=updateB4&m='+khcn_matm_selected;		
		xreq = $.ajax({
		  type: 'POST', dataType: "json", data: dataString,
		  url: khcn_linkdata,
		  success: function(data) {
			gv_processing_diglog("close");
			if (data.success == 1){
				khcn_formB4_changed = false;
				return true;
			}else{
				gv_open_msg_box("<font style='color:red;'>Không thể cập nhật thông tin vì:</font> <br/><div style='margin: 5px 0 0 5px'>" + reverse_escapeJsonString(data.msgerr)+'</div>', 'alert', 250, 180, true);
			}
		  }
		});
	}
}

function khcn_update_mota_B5_1(){
	bValid = true;
	var khcn_b5_1_viet 			= $("#khcn_frm_edit_dtkhcn_muc_tieu_nc_vn"),
		khcn_b5_1_anh 			= $("#khcn_frm_edit_dtkhcn_muc_tieu_nc_en"),

		khcn_b5_1_allFields = $([]).add(khcn_b5_1_anh).add(khcn_b5_1_viet),
		khcn_b5_1_jtips	= $("#khcn_b5_1_tips");
		
	khcn_b5_1_allFields.removeClass( "ui-state-error" );
	
	bValid = bValid && checkLength( khcn_b5_1_viet, "\"Mục tiêu nghiên cứu (Việt)\"", 0, 10000, 0, khcn_b5_1_jtips);
	bValid = bValid && checkLength( khcn_b5_1_anh, "\"Mục tiêu nghiên cứu (Anh)\"", 0, 10000, 0, khcn_b5_1_jtips);
	
	if (bValid && khcn_formB5_1_changed){
		gv_processing_diglog("open","Khoa học & Công nghệ", "Đang lưu dữ liệu...");
		dataString = 'muc_tieu_nc_vn='+encodeURIComponent($("#khcn_frm_edit_dtkhcn_muc_tieu_nc_vn").val()) +'&muc_tieu_nc_en='+encodeURIComponent($("#khcn_frm_edit_dtkhcn_muc_tieu_nc_en").val()) + '&a=updateB5_1&m='+khcn_matm_selected;		
		xreq = $.ajax({
		  type: 'POST', dataType: "json", data: dataString,
		  url: khcn_linkdata,
		  success: function(data) {
			gv_processing_diglog("close");
			if (data.success == 1){
				khcn_formB5_1_changed = false;
				return true;
			}else{
				gv_open_msg_box("<font style='color:red;'>Không thể cập nhật thông tin vì:</font> <br/><div style='margin: 5px 0 0 5px'>" + reverse_escapeJsonString(data.msgerr)+'</div>', 'alert', 250, 180, true);
			}
		  }
		});
	}
	else {
		return bValid;
	}
}

function khcn_update_mota_B5_2(){
	if (khcn_formB5_2_changed){
		gv_processing_diglog("open","Khoa học & Công nghệ", "Đang lưu dữ liệu...");
		dataString = 'noi_dung_nc='+encodeURIComponent($("#khcn_frm_edit_dtkhcn_noi_dung_nc").val()) + '&a=updateB5_2&m='+khcn_matm_selected;		
		xreq = $.ajax({
		  type: 'POST', dataType: "json", data: dataString,
		  url: khcn_linkdata,
		  success: function(data) {
			gv_processing_diglog("close");
			if (data.success == 1){
				khcn_formB5_2_changed = false;
				return true;
			}else{
				gv_open_msg_box("<font style='color:red;'>Không thể cập nhật thông tin vì:</font> <br/><div style='margin: 5px 0 0 5px'>" + reverse_escapeJsonString(data.msgerr)+'</div>', 'alert', 250, 180, true);
			}
		  }
		});
	}
}

function khcn_update_mota_B5_3(){
	if (khcn_formB5_3_changed){
		gv_processing_diglog("open","Khoa học & Công nghệ", "Đang lưu dữ liệu...");
		dataString = 'pa_phoi_hop='+encodeURIComponent($("#khcn_frm_edit_dtkhcn_pa_phoi_hop").val()) + '&a=updateB5_3&m='+khcn_matm_selected;		
		xreq = $.ajax({
		  type: 'POST', dataType: "json", data: dataString,
		  url: khcn_linkdata,
		  success: function(data) {
			gv_processing_diglog("close");
			if (data.success == 1){
				khcn_formB5_3_changed = false;
				return true;
			}else{
				gv_open_msg_box("<font style='color:red;'>Không thể cập nhật thông tin vì:</font> <br/><div style='margin: 5px 0 0 5px'>" + reverse_escapeJsonString(data.msgerr)+'</div>', 'alert', 250, 180, true);
			}
		  }
		});
	}
}

function khcn_update_mota_B6_1(){
	if (!khcn_formB6_1_changed){
		gv_open_msg_box("<font color=red>Vui lòng nhập thông tin về ấn phẩm khoa học</font>", 'alert', 250, 180, true);
	}
	return khcn_formB6_1_changed;
}

function khcn_update_mota_B6_2(){
	if (khcn_formB6_2_changed){
		gv_processing_diglog("open","Khoa học & Công nghệ", "Đang lưu dữ liệu...");
		dataString = 'muc_cl_sp_dang_ii='+encodeURIComponent($("#khcn_frm_reg_sanphamcung_mucchatluong").val()) + '&a=updateB6_2&m='+khcn_matm_selected;		
		xreq = $.ajax({
		  type: 'POST', dataType: "json", data: dataString,
		  url: khcn_linkdata,
		  success: function(data) {
			gv_processing_diglog("close");
			if (data.success == 1){
				khcn_formB6_2_changed = false;
				return true;
			}else{
				gv_open_msg_box("<font style='color:red;'>Không thể cập nhật thông tin vì:</font> <br/><div style='margin: 5px 0 0 5px'>" + reverse_escapeJsonString(data.msgerr)+'</div>', 'alert', 250, 180, true);
			}
		  }
		});
	}
}

function khcn_update_mota_B7(){
	if (khcn_formB7_changed){
		gv_processing_diglog("open","Khoa học & Công nghệ", "Đang lưu dữ liệu...");
		dataString = $("#khcn_frm_edit_dtkhcn_B7").serialize() + '&a=updateB7&m='+khcn_matm_selected;		
		xreq = $.ajax({
		  type: 'POST', dataType: "json", data: dataString,
		  url: khcn_linkdata,
		  success: function(data) {
			gv_processing_diglog("close");
			if (data.success == 1){
				khcn_formB7_changed = false;
				return true;
			}else{
				gv_open_msg_box("<font style='color:red;'>Không thể cập nhật thông tin vì:</font><br/><div style='margin: 5px 0 0 5px'>" + reverse_escapeJsonString(data.msgerr)+'</div>', 'alert', 250, 180, true);
			}
		  }
		});
	}
}

function khcn_add_table_chuyengianc(data){
	$( "#khcn_frm_edit_dtkhcn_B4_1_table_chuyengia tbody" ).append( "<tr style='font-size:12px;' class='"+khcn_class+"' >" +
	"<td align=left>" + reverse_escapeJsonString(data.ma_chuyen_gia) + "</td>" +
	"<td align=left>" + reverse_escapeJsonString(data.ho_ten) + "</td>" +
	"<td align=left>" + reverse_escapeJsonString(data.huong_nc_chuyen_sau) + "</td>" +
	"<td align=left>" + reverse_escapeJsonString(data.co_quan_cong_tac) + "</td>" +
	"<td align=left>" + reverse_escapeJsonString(data.dia_chi) + "</td>" +
	"<td align=left>" + reverse_escapeJsonString(data.dien_thoai)+ ", " + reverse_escapeJsonString(data.email) + "</td>" +
	"<td><button class='khcn_chuyengianc_remove' style='height:25px;width:30px;' onclick='khcn_remove_chuyengia( khcn_getRowIndex(this)); return false;'></button></td>" +
	"</tr>" );
	(khcn_class=='alt') ? khcn_class='alt_' : khcn_class='alt';
}

function khcn_add_table_anphamkhoahoc(data){
	$( "#khcn_frm_edit_dtkhcn_B6_1_table_an_pham_kh tbody:eq("+(data.fk_ma_an_pham_kh-1)+")").append( "<tr style='font-size:12px;' class='"+khcn_class+"'>" +
	"<td align=left>" + reverse_escapeJsonString(data.fk_ma_an_pham_kh) + "</td>" +
	"<td align=left>" + reverse_escapeJsonString(data.ten_bb_sach_dk) + "</td>" +
	"<td align=center>" + reverse_escapeJsonString(data.so_luong) + "</td>" +
	"<td align=left>" + reverse_escapeJsonString(data.dk_noi_cong_bo) + "</td>" +
	"<td align=left>" + reverse_escapeJsonString(data.ghi_chu) + "</td>" +
	"<td><button class='khcn_anphamkhoahoc_remove' style='height:25px;width:30px;' onclick='khcn_remove_anphamkhoahoc( khcn_getRowIndex(this)); return false;'></button></td>" +
	"</tr>" );
	(khcn_class=='alt') ? khcn_class='alt_' : khcn_class='alt';
}

function khcn_add_table_sohuutritue(data){
	$( "#khcn_frm_edit_dtkhcn_B6_2_table_sohuutritue tbody").append( "<tr style='font-size:12px;' class='"+khcn_class+"' >" +
	"<td align=left>" + reverse_escapeJsonString(data.fk_ma_so_huu_tri_tue) + "</td>" +
	"<td align=left>" + reverse_escapeJsonString(data.ten_hinh_thuc) + "</td>" +
	"<td align=left>" + reverse_escapeJsonString(data.so_luong) + "</td>" +
	"<td align=left>" + reverse_escapeJsonString(data.noi_dung_du_kien) + "</td>" +
	"<td align=left>" + reverse_escapeJsonString(data.ghi_chu) + "</td>" +
	"<td align=right><button class='khcn_sohuutritue_remove' style='height:25px;width:30px;' onclick='khcn_remove_sohuutritue( khcn_getRowIndex(this)); return false;'></button></td>" +
	"</tr>" );
	(khcn_class=='alt') ? khcn_class='alt_' : khcn_class='alt';
}

function khcn_add_table_sanphammem(data){
	$( "#khcn_frm_edit_dtkhcn_B6_2_table_sanphammem tbody").append( "<tr style='font-size:12px;' class='"+khcn_class+"' >" +
	"<td align=left valign=top>" + reverse_escapeJsonString(data.ma_san_pham_mem_tmdt) + "</td>" +
	"<td align=left valign=top>" + reverse_escapeJsonString(data.ten_san_pham) + "</td>" +
	"<td align=left>" + reverse_escapeJsonString(data.chi_tieu_danh_gia.replace(/\r\n/g, '<br>')) + "</td>" +
	"<td align=left valign=top>" + reverse_escapeJsonString(data.ghi_chu) + "</td>" +
	"<td align=right><button class='khcn_sanphammem_remove' style='height:25px;width:30px;' onclick='khcn_remove_sanphammem( khcn_getRowIndex(this)); return false;'></button></td>" +
	"</tr>" );
	(khcn_class=='alt') ? khcn_class='alt_' : khcn_class='alt';
}

function khcn_add_table_sanphamcung(data){
	$( "#khcn_frm_edit_dtkhcn_B6_2_table_sanphamcung tbody").append( "<tr style='font-size:12px;' class='"+khcn_class+"' >" +
	"<td align=left valign=top>" + reverse_escapeJsonString(data.ma_san_pham_cung_tmdt) + "</td>" +
	"<td align=left valign=top>" + reverse_escapeJsonString(data.ten_san_pham) + "</td>" +
	"<td align=center valign=top>" + reverse_escapeJsonString(data.don_vi_do) + "</td>" +
	"<td align=left valign=top>" + reverse_escapeJsonString(data.chi_tieu_danh_gia.replace(/\r\n/g, '<br>')) + "</td>" +
	"<td align=center valign=top>" + reverse_escapeJsonString(data.trong_nuoc) + "</td>" +
	"<td align=center valign=top>" + reverse_escapeJsonString(data.the_gioi) + "</td>" +
	"<td align=center valign=top>" + reverse_escapeJsonString(data.so_luong_quy_mo) + "</td>" +
	"<td align=right><button class='khcn_sanphamcung_remove' style='height:25px;width:30px;' onclick='khcn_remove_sanphamcung( khcn_getRowIndex(this)); return false;'></button></td>" +
	"</tr>" );
	
	(khcn_class=='alt') ? khcn_class='alt_' : khcn_class='alt';
}

function khcn_add_table_ketquadaotao(data){
	$( "#khcn_frm_edit_dtkhcn_B6_3_table_ketquadaotao tbody").append( "<tr style='font-size:12px;' class='"+khcn_class+"' >" +
	"<td align=left>" + reverse_escapeJsonString(data.fk_bac_dao_tao) + "</td>" +
	"<td align=left>" + reverse_escapeJsonString(data.ten_capdt) + "</td>" +
	"<td align=center>" + reverse_escapeJsonString(data.so_luong) + "</td>" +
	"<td align=left>" + reverse_escapeJsonString(data.nhiem_vu_duoc_giao) + "</td>" +
	"<td align=right>" + parseInt(reverse_escapeJsonString(data.du_kien_kinh_phi)).formatMoney(0,'.',',') + "</td>" +
	"<td align=right><button class='khcn_ketquadaotao_remove' style='height:25px;width:30px;' onclick='khcn_remove_ketquadaotao( khcn_getRowIndex(this)); return false;'></button></td>" +
	"</tr>" );
	(khcn_class=='alt') ? khcn_class='alt_' : khcn_class='alt';
}

function khcn_add_table_khoanchiphi(data){
	$( "#khcn_frm_edit_dtkhcn_B8_table_tonghopkinhphi tbody").append( "<tr style='font-size:12px;' class='"+khcn_class+"' >" +
	"<td align=left>" + reverse_escapeJsonString(data.fk_ma_khoan_chi_phi) + "</td>" +
	"<td align=left>" + reverse_escapeJsonString(data.ten_khoan_chi_phi) + "</td>" +
	"<td align=right>" + parseFloat(reverse_escapeJsonString(data.kinh_phi)).formatMoney(2,',','.') + "</td>" +
	"<td align=right>" + parseFloat(reverse_escapeJsonString(data.khoan_chi)).formatMoney(2,',','.') + "</td>" +
	"<td align=right><button class='khcn_khoanchiphi_edit' style='height:25px;width:30px;' onclick='khcn_edit_khoanchiphi( khcn_getRowIndex(this)); return false;'></button></td>" +
	"</tr>" );
	(khcn_class=='alt') ? khcn_class='alt_' : khcn_class='alt';
}

/* Get the rows which are currently selected */
function khcn_fnGetSelected( oTableLocal ){
    return oTableLocal.$('tr.row_selected');
}

function khcn_init_dialog_nhanluc(ploai, pManl, pShccMaSV, pHoten, pDvct, pThangQD){
	$("#khcn_frm_reg_nhanlucnghiencuu_manl").val(pManl);
	$("#khcn_frm_reg_nhanlucnghiencuu_loai").val(ploai);
	
	if (ploai==1){ // Thanh vien chu chot
		$( "#khcn_div_frm_reg_nhanlucnghiencuu_hh_hv_ho_ten" ).show();
		$( "#khcn_div_frm_reg_nhanlucnghiencuu_ho_ten_sv" ).hide();
		$( "#khcn_div_frm_reg_nhanlucnghiencuu_huongdan" ).show();
		$( "#khcn_frm_reg_nhanlucnghiencuu_div_shcc" ).css( "display", "block" );
		$( "#khcn_frm_reg_nhanlucnghiencuu_div_masv" ).css( "display", "none" );
		
		$("#khcn_frm_reg_nhanlucnghiencuu_shcc").val(pShccMaSV);
		$("#khcn_frm_reg_nhanlucnghiencuu_hh_hv_ho_ten").val(pHoten);
		$("#khcn_frm_reg_nhanlucnghiencuu_don_vi_cong_tac").val(pDvct);
		$("#khcn_frm_reg_nhanlucnghiencuu_so_thang_lv_quy_doi").val(pThangQD);
		
	}else if (ploai==2){ // Sinh vien
		$( "#khcn_div_frm_reg_nhanlucnghiencuu_ho_ten_sv" ).show();
		$( "#khcn_div_frm_reg_nhanlucnghiencuu_hh_hv_ho_ten" ).hide();
		$( "#khcn_div_frm_reg_nhanlucnghiencuu_huongdan" ).hide();
		$( "#khcn_frm_reg_nhanlucnghiencuu_div_shcc" ).css( "display", "none" );
		$( "#khcn_frm_reg_nhanlucnghiencuu_div_masv" ).css( "display", "block" );
		
		$("#khcn_frm_reg_nhanlucnghiencuu_masv").val(pShccMaSV);
		$("#khcn_frm_reg_nhanlucnghiencuu_ho_ten_sv").val(pHoten);
		$("#khcn_frm_reg_nhanlucnghiencuu_don_vi_cong_tac").val(pDvct);
		$("#khcn_frm_reg_nhanlucnghiencuu_so_thang_lv_quy_doi").val(pThangQD);
		
	}else{
		$( "#khcn_frm_reg_nhanlucnghiencuu_div_shcc" ).css( "display", "none" );
		$( "#khcn_frm_reg_nhanlucnghiencuu_div_masv" ).css( "display", "none" );
		$( "#khcn_div_frm_reg_nhanlucnghiencuu_ho_ten_sv" ).hide();
		$( "#khcn_div_frm_reg_nhanlucnghiencuu_hh_hv_ho_ten" ).hide();
		$( "#khcn_div_frm_reg_nhanlucnghiencuu_huongdan" ).hide();
	}
	
	if (pManl){ // edit
		$("#khcn_frm_reg_nhanlucnghiencuu_loai, #khcn_frm_reg_nhanlucnghiencuu_shcc, #khcn_frm_reg_nhanlucnghiencuu_hh_hv_ho_ten").attr("disabled", "disabled");
	}else { // Insert
		$("#khcn_frm_reg_nhanlucnghiencuu_loai, #khcn_frm_reg_nhanlucnghiencuu_shcc, #khcn_frm_reg_nhanlucnghiencuu_hh_hv_ho_ten").removeAttr("disabled");
	}
}

function khcn_edit_nhanluc(pindex, ploai){
	//gv_processing_diglog("open","Khoa học & Công nghệ", "Đang xóa dữ liệu ...");
	$("#khcn_frm_reg_nhanlucnghiencuu").find('input[type=text], input[type=hidden], textarea, select').val('');
	
	i = pindex + 1;
	t = document.getElementById('khcn_frm_edit_dtkhcn_A9_table_nhanluc');
	
	$("#khcn_frm_reg_nhanlucnghiencuu_index").val(i);
	
	manhanluc = t.rows[i].cells[0].innerHTML;
	hoten = t.rows[i].cells[1].innerHTML;
	shcc_masv = t.rows[i].cells[2].innerHTML;
	dvct = t.rows[i].cells[3].innerHTML;
	thangQD = t.rows[i].cells[4].innerHTML;

	khcn_init_dialog_nhanluc(ploai, manhanluc, shcc_masv, hoten, dvct, thangQD);
	
	$('#khcn_diag_nhanlucnghiencuu').dialog('open');
}

function khcn_remove_nhanluc(pindex, ploai){
	gv_processing_diglog("open","Khoa học & Công nghệ", "Đang xóa dữ liệu ...");
	
	i = pindex + 1;
	t = document.getElementById('khcn_frm_edit_dtkhcn_A9_table_nhanluc');
	manhanluc = t.rows[i].cells[0].innerHTML;
	//alert(i + ' ' + manhanluc);
	dataString = 'a=removenhanlucnc&m='+khcn_matm_selected+ '&mnl='+manhanluc+'&loai='+ploai;
	xreq = $.ajax({
	  type: 'POST', dataType: "json", data: dataString,
	  url: khcn_linkdata,
	  success: function(data) {
		gv_processing_diglog("close");
		if (data.success == 1){
			t.deleteRow( i );
		}else{
			gv_open_msg_box("<font style='color:red;'>Không thể xóa thông tin.</font><p>Chi tiết lỗi: <br/><div style='margin: 5px 0 0 5px'>" + reverse_escapeJsonString(data.msgerr)+'</div></p>', 'alert', 250, 180, true);
		}
	  }
	});
}

function khcn_remove_chuyengia(pindex, ploai){
	gv_processing_diglog("open","Khoa học & Công nghệ", "Đang xóa dữ liệu ...");
	
	i = pindex + 1;
	t = document.getElementById('khcn_frm_edit_dtkhcn_B4_1_table_chuyengia');
	machuyengia = t.rows[i].cells[0].innerHTML;
	//alert(i + ' ' + manhanluc);
	dataString = 'a=removechuyengianc&m='+khcn_matm_selected+ '&mcg='+machuyengia;
	xreq = $.ajax({
	  type: 'POST', dataType: "json", data: dataString,
	  url: khcn_linkdata,
	  success: function(data) {
		gv_processing_diglog("close");
		if (data.success == 1){
			t.deleteRow( i );
		}else{
			gv_open_msg_box("<font style='color:red;'>Không thể xóa thông tin.</font> <p>Chi tiết lỗi: <br/><div style='margin: 5px 0 0 5px'>" + reverse_escapeJsonString(data.msgerr)+'</div></p>', 'alert', 250, 180, true);
		}
	  }
	});
}

function khcn_remove_anphamkhoahoc(pindex, ploai){
	gv_processing_diglog("open","Khoa học & Công nghệ", "Đang xóa dữ liệu ...");
	
	i = pindex + 1;
	t = document.getElementById('khcn_frm_edit_dtkhcn_B6_1_table_an_pham_kh');
	maanpham = t.rows[i].cells[0].innerHTML;
	//alert(i + ' ' + manhanluc);
	dataString = 'a=removeanphamkhoahoc&m='+khcn_matm_selected+ '&map='+maanpham;
	xreq = $.ajax({
	  type: 'POST', dataType: "json", data: dataString,
	  url: khcn_linkdata,
	  success: function(data) {
		gv_processing_diglog("close");
		if (data.success == 1){
			t.deleteRow( i );
		}else{
			gv_open_msg_box("<font style='color:red;'>Không thể xóa thông tin.</font> <br/><div style='margin: 5px 0 0 5px'>" + reverse_escapeJsonString(data.msgerr) +'</div>', 'alert', 250, 180, true);
		}
	  }
	});
}

function khcn_remove_sohuutritue(pindex, ploai){
	gv_processing_diglog("open","Khoa học & Công nghệ", "Đang xóa dữ liệu ...");
	
	i = pindex + 1;
	t = document.getElementById('khcn_frm_edit_dtkhcn_B6_2_table_sohuutritue');
	ma = t.rows[i].cells[0].innerHTML;
	//alert(i + ' ' + manhanluc);
	dataString = 'a=removesohuutritue&m='+khcn_matm_selected+ '&ma='+ma;
	xreq = $.ajax({
	  type: 'POST', dataType: "json", data: dataString,
	  url: khcn_linkdata,
	  success: function(data) {
		gv_processing_diglog("close");
		if (data.success == 1){
			t.deleteRow( i );
		}else{
			gv_open_msg_box("<font style='color:red;'>Không thể xóa thông tin.</font> <br/><div style='margin: 5px 0 0 5px'>" + reverse_escapeJsonString(data.msgerr) +'</div>', 'alert', 250, 180, true);
		}
	  }
	});
}

function khcn_remove_sanphammem(pindex, ploai){
	gv_processing_diglog("open","Khoa học & Công nghệ", "Đang xóa dữ liệu ...");
	
	i = pindex + 1;
	t = document.getElementById('khcn_frm_edit_dtkhcn_B6_2_table_sanphammem');
	ma = t.rows[i].cells[0].innerHTML;
	//alert(i + ' ' + manhanluc);
	dataString = 'a=removesanphammem&m='+khcn_matm_selected+ '&ma='+ma;
	xreq = $.ajax({
	  type: 'POST', dataType: "json", data: dataString,
	  url: khcn_linkdata,
	  success: function(data) {
		gv_processing_diglog("close");
		if (data.success == 1){
			t.deleteRow( i );
		}else{
			gv_open_msg_box("<font style='color:red;'>Không thể xóa thông tin.</font> <br/><div style='margin: 5px 0 0 5px'>" + reverse_escapeJsonString(data.msgerr) +'</div>', 'alert', 250, 180, true);
		}
	  }
	});
}

function khcn_remove_sanphamcung(pindex, ploai){
	gv_processing_diglog("open","Khoa học & Công nghệ", "Đang xóa dữ liệu ...");
	
	i = pindex + 1;
	t = document.getElementById('khcn_frm_edit_dtkhcn_B6_2_table_sanphamcung');
	ma = t.rows[i].cells[0].innerHTML;
	//alert(i + ' ' + manhanluc);
	dataString = 'a=removesanphamcung&m='+khcn_matm_selected+ '&ma='+ma;
	xreq = $.ajax({
	  type: 'POST', dataType: "json", data: dataString,
	  url: khcn_linkdata,
	  success: function(data) {
		gv_processing_diglog("close");
		if (data.success == 1){
			t.deleteRow( i );
		}else{
			gv_open_msg_box("<font style='color:red;'>Không thể xóa thông tin.</font> <br/><div style='margin: 5px 0 0 5px'>" + reverse_escapeJsonString(data.msgerr) +'</div>', 'alert', 250, 180, true);
		}
	  }
	});
}

function khcn_remove_ketquadaotao(pindex, ploai){
	gv_processing_diglog("open","Khoa học & Công nghệ", "Đang xóa dữ liệu ...");
	
	i = pindex + 1;
	t = document.getElementById('khcn_frm_edit_dtkhcn_B6_3_table_ketquadaotao');
	ma = t.rows[i].cells[0].innerHTML;
	//alert(i + ' ' + manhanluc);
	dataString = 'a=removeketquadaotao&m='+khcn_matm_selected+ '&ma='+ma;
	xreq = $.ajax({
	  type: 'POST', dataType: "json", data: dataString,
	  url: khcn_linkdata,
	  success: function(data) {
		gv_processing_diglog("close");
		if (data.success == 1){
			t.deleteRow( i );
		}else{
			gv_open_msg_box("<font style='color:red;'>Không thể xóa thông tin.</font> <br/><div style='margin: 5px 0 0 5px'>" + reverse_escapeJsonString(data.msgerr) +'</div>', 'alert', 250, 180, true);
		}
	  }
	});
}

function khcn_edit_khoanchiphi(pindex, ploai){
	i = pindex + 1;
	t = document.getElementById('khcn_frm_edit_dtkhcn_B8_table_tonghopkinhphi');
	ma = t.rows[i].cells[0].innerHTML;
	tenkhoanchi = t.rows[i].cells[1].innerHTML;
	kinhphi = (t.rows[i].cells[2].innerHTML).replace(/\./g, '');
	khoanchi = (t.rows[i].cells[3].innerHTML).replace(/\./g, '');
	
	kinhphi = (kinhphi).replace(/,/g, '.');
	khoanchi = (khoanchi).replace(/,/g, '.');
	
	$('#khcn_frm_reg_tonghopkinhphi_khoan_chi_phi').val(ma);
	$('#khcn_frm_reg_tonghopkinhphi_ten_khoan_chi').html(tenkhoanchi);
	//alert(kinhphi);
	$('#khcn_frm_reg_tonghopkinhphi_kinh_phi').autoNumeric('set', kinhphi);
	$('#khcn_frm_reg_tonghopkinhphi_khoan_chi').autoNumeric('set', khoanchi);	
	
	$('#khcn_diag_tonghopkinhphi').dialog('open');
}

function khcn_getRowIndex( el ) {
    while( (el = el.parentNode) && el.nodeName.toLowerCase() !== 'tr' );

    if( el ) {
        return el.rowIndex-1;
	}
}

function khcn_userfile_change(obj){
  var file = obj.value;
  if (file != ''){
	$("#khcn_frm_upload_file_khoanchi").submit();
  }
}

function khcn_userfile_vonkhac_change(obj){
  var file = obj.value;
  if (file != ''){
	$("#khcn_frm_upload_file_vonkhac").submit();
  }
}

function khcn_print_tmdt(pindex, pcap){
	var i = pindex + 1;
	//var matmdt = document.getElementById('khcn_ds_thuyetminhdtkhcn').rows[i].cells[0].innerHTML;
	khcn_matm_selected = document.getElementById('khcn_ds_thuyetminhdtkhcn').rows[i].cells[0].innerHTML;
	khcn_nTr_selected = document.getElementById('khcn_ds_thuyetminhdtkhcn').rows[i];
	
	var fileprint='', tabname='', key = 'print_tmdt_' +i + '_' + pcap;
	var tabOpened = window.ns.get_tabOpened();
	var tabCurrent = $('#' + tabOpened['khcn_dangky_tmdt']).index()-1;
	
	var aData = oTableThuyetMinhDTKHCN.fnGetData( khcn_nTr_selected );
		
	if (pcap > 20 && pcap < 25) { // Cap DHQG
		fileprint = 'khcn_print_tmdt_r01.php';
		tabname = 'TMĐT - ĐHQG Mẫu R01 - ' + khcn_matm_selected;
	}else if (pcap > 30 && pcap < 36) { // Cap truong
		fileprint = 'khcn_print_tmdt_t12.php';
		tabname = 'TMĐT - Trường Mẫu 12 - ' + khcn_matm_selected;
	}
	//alert(parseInt(aData[khcn_tmdt_col_idx['editallow']]));
	if (parseInt(aData[khcn_tmdt_col_idx['editallow']])){
		gv_open_msg_box("<font color=red>Lưu ý: Bạn chỉ được phép in thuyết minh đề tài sau khi đã <b>hoàn tất đăng ký TMĐT</b>.</font>", 'alert', 250, 180, true);
		return;
	}
	
	if (fileprint && tabname){
		window.ns.addTab_ns(key, tabname, 'print-preview-icon24x24.png', tabCurrent, "khcn/"+fileprint+"?a=print_tmdt_fromtab&hisid=<?php echo $_REQUEST["hisid"];?>&m="+khcn_matm_selected+"&k="+key);
	}
}

function khcn_change_capdetai(pVal){
	if (pVal) {
		// Nếu đề tài thuộc cấp Trường (31->35)
		if (parseInt(pVal) > 30 && parseInt(pVal) < 36 ){
			 $("#khcn_frm_reg_dtkhcn_cnganhhep, #khcn_frm_edit_dtkhcn_cnganhhep").attr("disabled", "disabled");
			 $("#khcn_frm_reg_dtkhcn_qd193").css("display", "none");
			 
		}else if (parseInt(pVal) > 20 && parseInt(pVal) < 24 ){
			$("#khcn_frm_reg_dtkhcn_cnganhhep, #khcn_frm_edit_dtkhcn_cnganhhep").removeAttr("disabled");
			$("#khcn_frm_reg_dtkhcn_qd193").css("display", "block");
		}else{
			
		}
	} else {
		 $("#khcn_frm_reg_dtkhcn_qd193").css("display", "none");
	}
}

function khcn_cal_kinhphi(){
	var kpdhqg = parseFloat($('#khcn_frm_edit_dtkhcn_kinhphi_dhqg').autoNumeric('get')); //.val().replace(/,/g, ""));
	var kphuydong = parseFloat($('#khcn_frm_edit_dtkhcn_kinhphi_tuco').autoNumeric('get')) + parseFloat($('#khcn_frm_edit_dtkhcn_kinhphi_khac').autoNumeric('get'));
	$('#khcn_frm_edit_dtkhcn_kinhphi_huydong').autoNumeric('set', kphuydong); 
	$('#khcn_frm_edit_dtkhcn_tongkinhphi').autoNumeric('set', kpdhqg+kphuydong); 
}

function khcn_checksession(){
	dataString = 'a=checksession';
	return xreq = $.ajax({
	  type: 'POST', dataType: "json", data: dataString,
	  url: khcn_linkdata,
	  success: function(data) {
		return jQuery.parseJSON(data);
	  }
	});
}

function khcn_hoantat_tmdt(pindex, pcap){
	var i = pindex + 1;
	khcn_matm_selected = document.getElementById('khcn_ds_thuyetminhdtkhcn').rows[i].cells[0].innerHTML;
	khcn_nTr_selected = document.getElementById('khcn_ds_thuyetminhdtkhcn').rows[i];
	//alert (aData[0]);
	$("#khcn_diag_confirm_hoantat_tmdt_id").html(khcn_matm_selected);
	
	if (pcap > 20 && pcap < 25) { // Cap DHQG
		$("#khcn_diag_confirm_hoantat_tmdt_llkh").val("gv_print_llkh_mau_r03.php");
	}else if (pcap > 30 && pcap < 36) { // Cap truong
		$("#khcn_diag_confirm_hoantat_tmdt_llkh").val("gv_print_llkh_mau_truong_bk.php");
	}
	
	$('#khcn_diag_confirm_hoantat_tmdt').dialog('open');
}

function khcn_delete_tmdt(pindex){
	var i = pindex + 1;
	khcn_matm_selected = document.getElementById('khcn_ds_thuyetminhdtkhcn').rows[i].cells[0].innerHTML;
	khcn_nTr_selected = document.getElementById('khcn_ds_thuyetminhdtkhcn').rows[i];
	var aData = oTableThuyetMinhDTKHCN.fnGetData( khcn_nTr_selected );
	
	if (!parseInt(aData[khcn_tmdt_col_idx['editallow']])){
		gv_open_msg_box("<font color=red>Lưu ý: Bạn không được quyền xoá TMĐT này.</font>", 'alert', 250, 180, true);
		return;
	}
	$("#khcn_diag_confirm_delete_tmdt_id").html(khcn_matm_selected);
	$('#khcn_diag_confirm_delete_tmdt').dialog('open');
}

function khcn_init_ds_nhanlucnc(pProjects){
	$( "#khcn_frm_reg_nhanlucnghiencuu_hh_hv_ho_ten").autocomplete({
			minLength: 0,
			source: pProjects,
			focus: function( event, ui ) {
				return false;
			},
			select: function( event, ui ) {
				$( '#khcn_frm_reg_nhanlucnghiencuu_fk_ma_can_bo' ).val(reverse_escapeJsonString(ui.item.value));
				$( '#khcn_frm_reg_nhanlucnghiencuu_hh_hv_ho_ten' ).val(reverse_escapeJsonString(ui.item.label));
				$( '#khcn_frm_reg_nhanlucnghiencuu_shcc').val( ui.item.shcc );
				$( '#khcn_frm_reg_nhanlucnghiencuu_don_vi_cong_tac').val( reverse_escapeJsonString(ui.item.cq ));
				
				//console.log('click ' + $( '#khcn_frm_reg_nhanlucnghiencuu_hh_hv_ho_ten' ).val());
				return false;
			},
			change:function( event, ui ) {
				var data=$.data(this);
				if(data.autocomplete.selectedItem==undefined){
					$( '#khcn_frm_reg_nhanlucnghiencuu_fk_ma_can_bo' ).val("");
					$( '#khcn_frm_reg_nhanlucnghiencuu_shcc' ).val("");
					$( '#khcn_frm_reg_nhanlucnghiencuu_don_vi_cong_tac').val("");
				}
				//console.log('change ' + $( '#khcn_frm_reg_nhanlucnghiencuu_hh_hv_ho_ten' ).val());
			}
	})
	.data( "ui-autocomplete" )._renderItem = function( ul, item ) {
		return $( "<li>" ).append( "<a><b>" + item.label + "</b><br>" + reverse_escapeJsonString(item.desc) + "</a><hr>" ).appendTo( ul );
	};
}

function khcn_init_ds_dongchunhiem(pProjects){
	$( "#khcn_frm_edit_dtkhcn_dcndt_hh_hv_ho_ten").autocomplete({
			minLength: 0,
			source: pProjects,
			focus: function( event, ui ) {
				return false;
			},
			select: function( event, ui ) {
				$( '#khcn_frm_edit_dtkhcn_fk_dong_chu_nhiem_dt' ).val(reverse_escapeJsonString(ui.item.value));
				$( '#khcn_frm_edit_dtkhcn_dcndt_hh_hv_ho_ten' ).val(reverse_escapeJsonString(ui.item.label));
				$( '#khcn_frm_edit_dtkhcn_dcndt_shcc').val( ui.item.shcc );
				$( '#khcn_frm_edit_dtkhcn_dcndt_ngay_sinh').val( reverse_escapeJsonString(ui.item.ngaysinh ));
				$( '#khcn_frm_edit_dtkhcn_dcndt_so_cmnd').val( reverse_escapeJsonString(ui.item.cmnd ));
				$( '#khcn_frm_edit_dtkhcn_dcndt_ngay_cap').val( reverse_escapeJsonString(ui.item.ngaycap ));
				$( '#khcn_frm_edit_dtkhcn_dcndt_noi_cap').val( reverse_escapeJsonString(ui.item.noicap ));
				$( '#khcn_frm_edit_dtkhcn_dcndt_ms_thue').val( reverse_escapeJsonString(ui.item.msthue ));
				$( '#khcn_frm_edit_dtkhcn_dcndt_so_tai_khoan').val( reverse_escapeJsonString(ui.item.stk ));
				$( '#khcn_frm_edit_dtkhcn_dcndt_ngan_hang').val( reverse_escapeJsonString(ui.item.nganhang ));
				$( '#khcn_frm_edit_dtkhcn_dcndt_dia_chi_cq').val( reverse_escapeJsonString(ui.item.diachicq ));
				$( '#khcn_frm_edit_dtkhcn_dcndt_dien_thoai').val( reverse_escapeJsonString(ui.item.dienthoai ));
				$( '#khcn_frm_edit_dtkhcn_dcndt_email').val( reverse_escapeJsonString(ui.item.email ));
				$('input:radio[name=khcn_frm_edit_dtkhcn_dcndt_phai][value='+ui.item.phai+']').attr('checked', true);
				
				//console.log('click ' + $( '#khcn_frm_reg_nhanlucnghiencuu_hh_hv_ho_ten' ).val());
				return false;
			},
			change:function( event, ui ) {
				var data=$.data(this);
				if(data.autocomplete.selectedItem==undefined){
					$( '#khcn_frm_edit_dtkhcn_fk_dong_chu_nhiem_dt, #khcn_frm_edit_dtkhcn_dcndt_hh_hv_ho_ten, #khcn_frm_edit_dtkhcn_dcndt_shcc, #khcn_frm_edit_dtkhcn_dcndt_ngay_sinh, #khcn_frm_edit_dtkhcn_dcndt_so_cmnd, #khcn_frm_edit_dtkhcn_dcndt_ngay_cap, #khcn_frm_edit_dtkhcn_dcndt_noi_cap, #khcn_frm_edit_dtkhcn_dcndt_ms_thue, #khcn_frm_edit_dtkhcn_dcndt_so_tai_khoan, #khcn_frm_edit_dtkhcn_dcndt_ngan_hang, #khcn_frm_edit_dtkhcn_dcndt_dia_chi_cq, #khcn_frm_edit_dtkhcn_dcndt_dien_thoai, #khcn_frm_edit_dtkhcn_dcndt_email' ).val("");
				}
				
				//console.log(data);
			}
	})
	.data( "ui-autocomplete" )._renderItem = function( ul, item ) {
		return $( "<li>" ).append( "<a><b>" + item.label + "</b><br>" + reverse_escapeJsonString(item.desc) + "</a><hr>" ).appendTo( ul );
	};
}

function khcn_init_form_edit(pCapDT){
	
}
</script>



<?php
if (isset ($db_conn))
	oci_close($db_conn);
	
if (isset ($db_conn_khcn))
	oci_close($db_conn_khcn);
?>