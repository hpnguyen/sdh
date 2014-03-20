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
$sqlstr="	SELECT h.*, (ho || ' ' || ten) ho_ten, email, n.ten_nganh,
				to_char(h.ngay_sinh, 'dd/mm/yyyy') ngay_sinh, decode(h.phai, 'M', 'checked=\"checked\"') phai_nam,
				to_char(h.ngay_vao_doan, 'dd/mm/yyyy') ngay_vao_doan, to_char(h.ngay_vao_dang, 'dd/mm/yyyy') ngay_vao_dang,
				to_char(h.THUC_TAP_KHKT_TU_NGAY, 'dd/mm/yyyy') THUC_TAP_KHKT_TU_NGAY, to_char(h.THUC_TAP_KHKT_DEN_NGAY, 'dd/mm/yyyy') THUC_TAP_KHKT_DEN_NGAY,
				to_char(h.NGAY_BAO_VE_LVTHS, 'dd/mm/yyyy') NGAY_BAO_VE_LVTHS,
				decode(h.phai, 'F', 'checked=\"checked\"') phai_nu,
				thanh_toan_tu_dong, k.ten_kinh_phi_dt, to_char(h.ngay_cap, 'dd/mm/yyyy') ngay_cap,
				decode(ctdt_loai(h.ma_hoc_vien), 1, 'Giảng dạy môn học + khóa luận', 2, 'Giảng dạy môn học + LVThs', 'Nghiên cứu') || ' ' || decode(ctdt_hv_nam(h.ma_hoc_vien), 0, null,'thuộc chương trình: ' || ctdt_hv_nam(h.ma_hoc_vien) || ' năm') ctdt,
				dot_cap_bang('$usr') dot_cap_bang,
				t.BAI_BAO, t.DE_TAI_NCKH, t.THAM_GIA_HOI_NGHI, t.GIAI_THUONG_KHCN, LINK_HINH_KY_YEU
			FROM hoc_vien h, nganh n, dm_kinh_phi_dao_tao k, qt_hoat_dong_khkt t
			WHERE upper(h.ma_hoc_vien) = upper('$usr')
			AND h.ma_nganh = n.ma_nganh
			AND h.fk_kinh_phi_dao_tao = k.ma_kinh_phi_dt AND h.ma_hoc_vien=t.fk_ma_hoc_vien(+)
";

//file_put_contents("logs.txt", "$sqlstr");

$stmt = oci_parse($db_conn, $sqlstr);oci_execute($stmt);$n = oci_fetch_all($stmt, $accinfo);oci_free_statement($stmt);

$linkhinhkyyeu = $accinfo["LINK_HINH_KY_YEU"][0];
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

//$filehinh = "./$hinhkyyeufolder/$dotcapbang/$usr.jpg";

// if ($linkhinhkyyeu==""){
	// $filehinh = "./$hinhkyyeufolder/$dotcapbang/$usr.jpg";
// }else{
	// $filehinh = "./$linkhinhkyyeu";
// }

$filehinh = $linkhinhkyyeu;


//if ($mahvkyyeu==$usr || $usr == '03207104')
//{
	$clickdivhinh = 'onclick="getFile()" style="margin: 0 10px 0px 0; cursor:pointer" ';
	$ndhuongdan = "Hình dùng làm kỷ yếu tốt nghiệp, bạn nên chọn ảnh tự nhiên, lịch sự, không nên chọn hình thẻ.<br/><b>Hướng dẫn tải ảnh lên:</b><br/> Click vào khung hình này hoặc bấm nút &quot;Tải lên&quot; chọn file ảnh chân dung (<b>.jpg, kích thước < 1MB</b>), sau đó bấm nút &quot;Open&quot; để tải ảnh";
?>
<style type="text/css">
#hv_file_ky_yeu_progress { position:relative; width:100%; border: 1px solid #ddd; padding: 1px; border-radius: 3px; }
#hv_file_ky_yeu_bar { background-color: #B4F5B4; width:0%; height:20px; border-radius: 3px; }
#hv_file_ky_yeu_percent { position:absolute; display:inline-block; top:3px; left:48%; }
.fontcontent {
	font-size: 13px;
	font-family: Arial, Helvetica, sans-serif;
	color: #000000;
	font-weight: normal;
	line-height: 1.5;
}
.bordertable {
	border-color: #000000; 
	border-width: 1px; 
	border-style: solid; 
	border-collapse:collapse;
}
.borderDOT  {
	border-color: #000000; 
	border-width: 1px; 
	border-style: dotted; 
	border-collapse:collapse;
}
P.breakhere {page-break-before: always}
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
		<table width="680px" cellspacing="0" cellpadding="0" class="ui-corner-all shawdow">
		    <tr>
			  <td>&nbsp;</td>
			</tr>
        	<tr> 
			<td>
               <table width="100%" border="0" cellspacing="0" cellpadding="5" class="">
					<tr>
					  <td colspan=3 align=center><b><label for="">TÓM TẮT LÝ LỊCH KHOA HỌC</b></label></td>
					</tr>
					<tr>
					  <td colspan=3 align=left><b><label for="">1. Bản thân</b></label></td>
					</tr>
					<tr>
					  <td align=right style='width:140px'>Mã HV</td>
					  <td style='font-weight:bold;' align=left><?php echo $accinfo["MA_HOC_VIEN"][0]; ?></td>
					  <td rowspan=7 align=center>
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
					  <td align=right class="heading">Phái</td>
					  <td style='' align=left><input type="radio" name='hv_info_phai' value='M' <?php echo $accinfo["PHAI_NAM"][0]; ?> />Nam 
								<input type="radio" name='hv_info_phai' value='F' <?php echo $accinfo["PHAI_NU"][0]; ?>/>Nữ </td>
					</tr>
					
					<tr>
						<td align=right class="heading"><label for="hv_info_ngaysinh">Sinh ngày</label></td>
						<td align=left >
							<input style="width:100px;" placeholder="dd/mm/yyyy" id="hv_info_ngaysinh"  name="hv_info_ngaysinh" type="text" class="text" value="<?php echo htmlentities($accinfo["NGAY_SINH"][0], ENT_QUOTES, "UTF-8"); ?>" />
							<font color=red>*</font>&nbsp;&nbsp;
							<b>tại</b> <select id="hv_info_noisinh" name="hv_info_noisinh" class="text" style="height: 25px; font-size:14px" >
									<option>-chọn nơi sinh-</option>
									<?php 
										$sqlstr="select * from dm_tinh_tp order by ten_tinh_tp"; 
										$stmt = oci_parse($db_conn, $sqlstr);oci_execute($stmt);$n=oci_fetch_all($stmt, $resDM);oci_free_statement($stmt);
										for ($i=0 ; $i < $n; $i++){
											($accinfo["NOI_SINH"][0]==$resDM["MA_TINH_TP"][$i]) ? $selected = "selected" : $selected = "";
											echo "<option value='{$resDM["MA_TINH_TP"][$i]}' $selected>{$resDM["TEN_TINH_TP"][$i]}</option>";
										}
									?>
								</select><font color=red>*</font>
						</td>
					</tr>
					
					<tr>
					  <td align=right>Ngành</td>
					  <td style='font-weight:bold;' align=left ><?php echo $accinfo["TEN_NGANH"][0]; ?></td>
					</tr>
					
					<tr>
					  <td align=right>Loại CTĐT</td>
					  <td style='' align=left>
						<b><?php echo $accinfo["CTDT"][0]; ?></b>
						&nbsp; &nbsp; Diện <b><?php echo $accinfo["TEN_KINH_PHI_DT"][0]; ?></b>
					  </td>
					</tr>
					
					<tr>
					  <td align=right ><label for="hv_info_so_cmnd" class="heading">Số CMND</label></td>
					  <td style='font-weight:bold;' align=left>
						<input style="width:120px;" placeholder="số CMND" id="hv_info_so_cmnd"  name="hv_info_so_cmnd" type="text" class="text" value="<?php echo htmlentities($accinfo["SO_CMND"][0], ENT_QUOTES, "UTF-8"); ?>" /><font color=red>*</font>
						&nbsp; &nbsp; <label for="hv_info_ngaycap_cmnd" class="heading">Ngày cấp</label> <input style="width:110px;" placeholder="dd/mm/yyyy" id="hv_info_ngaycap_cmnd"  name="hv_info_ngaycap_cmnd" type="text" class="text" value="<?php echo htmlentities($accinfo["NGAY_CAP"][0], ENT_QUOTES, "UTF-8"); ?>" /><font color=red>*</font>
  					  </td>
					</tr>
					
					<tr>
					  <td align=right><label for="hv_info_noicap_cmnd" class="heading">Nơi cấp</label></td>
					  <td align=left colspan = 2>
						<select id="hv_info_noicap_cmnd" class="text" style="height: 28px; font-size:14px; width:190px" name="hv_info_noicap_cmnd">
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
						<font color=red>*</font>
					  </td>
					</tr>
					
					<tr>
					  <td align=right class="heading"><label for="hv_info_dan_toc">Dân tộc</label></td>
					  <td align=left colspan = 2>
						<select id="hv_info_dan_toc" name="hv_info_dan_toc" class="text" style="height: 25px; font-size:14px" >
							<option>-chọn dân tộc-</option>
							<?php 
								$sqlstr="select MA_DAN_TOC, TEN_DAN_TOC from DM_DAN_TOC order by TEN_DAN_TOC"; 
								$stmt = oci_parse($db_conn, $sqlstr);oci_execute($stmt);$n=oci_fetch_all($stmt, $resDM);oci_free_statement($stmt);
								
								for ($i=0 ; $i < $n; $i++){
									($accinfo["FK_DAN_TOC"][0]==$resDM["MA_DAN_TOC"][$i]) ? $selected = "selected" : $selected = "";
									echo "<option value='{$resDM["MA_DAN_TOC"][$i]}' $selected>{$resDM["TEN_DAN_TOC"][$i]}</option>";
								}
							?>
						</select>
						<font color=red>*</font>
						&nbsp;&nbsp;
						<label for="hv_info_ton_giao" class="heading">Tôn giáo</label>
						<select id="hv_info_ton_giao" name="hv_info_ton_giao" class="text" style="height: 25px; font-size:14px" >
							<option>-chọn tôn giáo-</option>
							<?php 
								$sqlstr="select MA_TON_GIAO, TON_GIAO from DM_TON_GIAO order by TON_GIAO"; 
								$stmt = oci_parse($db_conn, $sqlstr);oci_execute($stmt);$n=oci_fetch_all($stmt, $resDM);oci_free_statement($stmt);
								
								for ($i=0 ; $i < $n; $i++){
									($accinfo["FK_TON_GIAO"][0]==$resDM["MA_TON_GIAO"][$i]) ? $selected = "selected='selected'" : $selected = "";
									echo "<option value='{$resDM["MA_TON_GIAO"][$i]}' $selected>{$resDM["TON_GIAO"][$i]}</option>";
								}
							?>
						</select>
						<font color=red>*</font>
					  </td>
					</tr>
					
					<tr>
					  <td align=right class="heading"><label for="hv_info_dia_chi_thuong_tru">Địa chỉ thường trú</label></td>
					  <td align=left colspan = 2>
						<input style="width:95%;" placeholder="" id="hv_info_dia_chi_thuong_tru"  name="hv_info_dia_chi_thuong_tru" type="text" class="text" value="<?php echo htmlentities($accinfo["DIA_CHI_THUONG_TRU"][0], ENT_QUOTES, "UTF-8"); ?>" />
						<font color=red>*</font>
					  </td>
					</tr> 
					
					<tr>
					  <td align=right class="heading"><label for="hv_info_diachi">Địa chỉ liên lạc</label></td>
					  <td colspan=2 align=left>
						<input style="width:95%;" placeholder="" id="hv_info_diachi"  name="hv_info_diachi" type="text" class="text" value="<?php echo htmlentities($accinfo["DIA_CHI"][0], ENT_QUOTES, "UTF-8"); ?>" />
						<font color=red>*</font>
					  </td>
					</tr>
					
					<tr>
					  <td align=right><label for="hv_info_dienthoai" class="heading">Điện thoại</label></td>
					  <td colspan=2 align=left>
						<input style="width:175px;" placeholder="" id="hv_info_dienthoai"  name="hv_info_dienthoai" type="text" class="text" value="<?php echo htmlentities($accinfo["DIEN_THOAI"][0], ENT_QUOTES, "UTF-8"); ?>" />
						<font color=red>*</font>
						&nbsp;&nbsp;
						<label for="hv_info_email" class="heading">Email</label>
						<input style="width:246px;" placeholder="địa chỉ email" id="hv_info_email"  name="hv_info_email" type="text" class="text" value="<?php echo htmlentities($accinfo["EMAIL"][0], ENT_QUOTES, "UTF-8"); ?>" />
						<font color=red>*</font>
					  </td>
					</tr>
					
					<tr>
					  <td align=right class="heading"><label for="hv_info_nghenghiep">Nghề nghiệp</label></td>
					  <td colspan=2 align=left><input style="width:95%;" placeholder="" id="hv_info_nghenghiep"  name="hv_info_nghenghiep" type="text" class="text" value="<?php echo htmlentities($accinfo["NGHE_NGHIEP"][0], ENT_QUOTES, "UTF-8"); ?>" /></td>
					</tr>
					
					<tr>
					  <td align=right><label for="hv_info_donvicongtac" class="heading">Đơn vị công tác</label></td>
					  <td colspan=2 align=left>
						<input style="width:95%;font-weight:bold" placeholder="" id="hv_info_donvicongtac"  name="hv_info_donvicongtac" type="text" class="text" value="<?php echo htmlentities($accinfo["DON_VI_CONG_TAC"][0], ENT_QUOTES, "UTF-8"); ?>" />
						<font color=red>*</font>
					  </td>
					</tr>
					
					<tr>
					  <td align=right><label for="hv_info_ngayvaodoan" class="heading">Ngày vào Đoàn TNCS-HCM</label></td>
					  <td colspan=2 align=left>
						<input style="width:120px;" placeholder="dd/mm/yyyy" id="hv_info_ngayvaodoan"  name="hv_info_ngayvaodoan" type="text" class="text" value="<?php echo htmlentities($accinfo["NGAY_VAO_DOAN"][0], ENT_QUOTES, "UTF-8"); ?>" />
						&nbsp;&nbsp;
						<label for="hv_info_ngayvaodang" class="heading">Ngày vào Đảng CSVN</label>
						<input style="width:120px;" placeholder="dd/mm/yyyy" id="hv_info_ngayvaodang"  name="hv_info_ngayvaodang" type="text" class="text" value="<?php echo htmlentities($accinfo["NGAY_VAO_DANG"][0], ENT_QUOTES, "UTF-8"); ?>" />
					  </td>
					</tr>
					
					<tr>
					  <td align=right><label for="hv_info_doituonguutien" class="heading">Diện chính sách</label></td>
					  <td colspan=2 align=left>
						<select id="hv_info_doituonguutien" name="hv_info_doituonguutien" class="text" style="height: 25px; font-size:14px" >
							<option value="">-chọn diện chính sách-</option>
							<?php 
								$sqlstr="select MA_UU_TIEN, LY_DO_UU_TIEN from DM_DOI_TUONG_UU_TIEN order by LY_DO_UU_TIEN"; 
								$stmt = oci_parse($db_conn, $sqlstr);oci_execute($stmt);$n=oci_fetch_all($stmt, $resDM);oci_free_statement($stmt);
								
								for ($i=0 ; $i < $n; $i++){
									($accinfo["FK_DOI_TUONG_UU_TIEN"][0]==$resDM["MA_UU_TIEN"][$i]) ? $selected = "selected" : $selected = "";
									echo "<option value='{$resDM["MA_UU_TIEN"][$i]}' $selected>{$resDM["LY_DO_UU_TIEN"][$i]}</option>";
								}
							?>
						</select>
					  </td>
					</tr>
					
					<tr>
					  <td align=right class="heading"><label for="hv_info_so_tk">Số TK NH Đông Á</label></td>
					  <td colspan=2 style='' align=left><input style="width:95%; font-weight:bold" placeholder="" id="hv_info_so_tk"  name="hv_info_so_tk" type="text" class="text" value="<?php echo htmlentities($accinfo["SO_TAI_KHOAN"][0], ENT_QUOTES, "UTF-8"); ?>" /></td>
					</tr>
					
					<tr>
					  <td align=right></td>
					  <td colspan=2 style='' align=left>
						<?php 
							if ($accinfo["THANH_TOAN_TU_DONG"][0]==1){
								echo "Đã kích hoạt thanh toán tự động"; 
							}else{
								echo "<font color=red><b>Chưa kích hoạt thanh toán tự động</b><br/>Vui lòng đến ngân hàng Đông Á <br/>tại cổng 2 ĐHBK-TPHCM để kích hoạt.</font>"; 
							}
						?>
					  </td>
					</tr>
					
					<tr>
					  <td colspan=3 align=left><b><label for="">2. Quá trình đào tạo</b></label></td>
					</tr>
					<tr>
					  <td colspan=3 align=left><b><label for="">a. ĐẠI HỌC</b></label></td>
					</tr>
					
					<tr>
					  <td align=right class="heading"><label for="hv_info_truongdaihoc">Tốt nghiệp Trường/Viện</label></td>
					  <td align=left colspan = 2>
						<input style="width:95%;" placeholder="" id="hv_info_truongdaihoc"  name="hv_info_truongdaihoc" type="text" class="text" value="<?php echo htmlentities($accinfo["TRUONG_DAI_HOC"][0], ENT_QUOTES, "UTF-8"); ?>" />
						<font color=red>*</font>
					  </td>
					</tr> 
					<tr>
					  <td align=right class="heading"><label for="hv_info_nganhdaihoc">Ngành học</label></td>
					  <td align=left colspan = 2>
						<input style="width:95%;" placeholder="" id="hv_info_nganhdaihoc"  name="hv_info_nganhdaihoc" type="text" class="text" value="<?php echo htmlentities($accinfo["FK_NGANH_DAI_HOC"][0], ENT_QUOTES, "UTF-8"); ?>" />
						<font color=red>*</font>
					  </td>
					</tr> 
					
					<tr>
					  <td align=right><label for="hv_info_hedaotao" class="heading">Loại hình đào tạo</label></td>
					  <td colspan=2 align=left>
						<select id="hv_info_hedaotao" name="hv_info_hedaotao" class="text" style="height: 25px; font-size:14px" >
							<option>-chọn loại hình-</option>
							<option value="CQ" <?php if ($accinfo["HE_DAO_TAO_DH"][0]=='CQ') {echo "selected='selected'";} ?> >Chính quy</option>
							<option value="KCQ" <?php if ($accinfo["HE_DAO_TAO_DH"][0]!='CQ') {echo "selected='selected'";} ?>>Không chính quy</option>
						</select>
						<font color=red>*</font>&nbsp;&nbsp;
						<label for="hv_info_nhaphocdaihoc" class=heading>Đào tạo từ năm</label>
						<input style="width:50px;" placeholder="yyyy" id="hv_info_nhaphocdaihoc"  name="hv_info_nhaphocdaihoc" type="text" class="text" value="<?php echo htmlentities($accinfo["THOI_DIEM_NHAP_HOC_DAI_HOC"][0], ENT_QUOTES, "UTF-8"); ?>" />
						<font color=red>*</font>
						<label for="hv_info_totnghiepdaihoc" class=heading>đến năm</label>
						<input style="width:50px;" placeholder="yyyy" id="hv_info_totnghiepdaihoc"  name="hv_info_totnghiepdaihoc" type="text" class="text" value="<?php echo htmlentities($accinfo["THOI_DIEM_TOT_NGHIEP_DAI_HOC"][0], ENT_QUOTES, "UTF-8"); ?>" />
						<font color=red>*</font>
					  </td>
					</tr>
					
					<tr>
					  <td align=right><label for="hv_info_loaitndaihoc" class="heading">Xếp loại tốt nghiệp</label></td>
					  <td colspan=2 align=left>
						<select id="hv_info_loaitndaihoc" name="hv_info_loaitndaihoc" class="text" style="height: 25px; font-size:14px" >
							<option value="">-chọn loại tốt nghiệp-</option>
							<?php 
								$sqlstr="select MA_LOAI_TN_DH, TEN_LOAI_TN_DH from DM_LOAI_TOT_NGHIEP_DAI_HOC"; 
								$stmt = oci_parse($db_conn, $sqlstr);oci_execute($stmt);$n=oci_fetch_all($stmt, $resDM);oci_free_statement($stmt);
								
								for ($i=0 ; $i < $n; $i++){
									($accinfo["FK_LOAI_TOT_NGHIEP_DAI_HOC"][0]==$resDM["MA_LOAI_TN_DH"][$i]) ? $selected = "selected" : $selected = "";
									echo "<option value='{$resDM["MA_LOAI_TN_DH"][$i]}' $selected>{$resDM["TEN_LOAI_TN_DH"][$i]}</option>";
								}
							?>
						</select>
						<font color=red>*</font>
					  </td>
					</tr>
					
					<tr>
					  <td colspan=3 align=left><b><label for="">b. SAU ĐẠI HỌC</b></label></td>
					</tr>
					
					<tr>
					  <td align=right><label for="hv_info_khkt_tu" class="heading">Thực tập khoa học, kỹ thuật từ</label></td>
					  <td colspan=2 align=left>
						<input style="width:120px;" placeholder="" id="hv_info_khkt_tu"  name="hv_info_khkt_tu" type="text" class="text" value="<?php echo htmlentities($accinfo["THUC_TAP_KHKT_TU_NGAY"][0], ENT_QUOTES, "UTF-8"); ?>" />
						&nbsp;&nbsp;
						<label for="hv_info_khkt_den" class="heading">đến</label>
						<input style="width:120px;" placeholder="" id="hv_info_khkt_den"  name="hv_info_khkt_den" type="text" class="text" value="<?php echo htmlentities($accinfo["THUC_TAP_KHKT_DEN_NGAY"][0], ENT_QUOTES, "UTF-8"); ?>" />
					  </td>
					</tr>
					<tr>
					  <td align=right class="heading"><label for="hv_info_khkt_truong">Tại Trường, Viện, Nước </label></td>
					  <td align=left colspan = 2>
						<input style="width:95%;" placeholder="" id="hv_info_khkt_truong"  name="hv_info_khkt_truong" type="text" class="text" value="<?php echo htmlentities($accinfo["THUC_TAP_KHKT_TRUONG"][0], ENT_QUOTES, "UTF-8"); ?>" />
					  </td>
					</tr>
					<tr>
					  <td align=right class="heading"><label for="hv_info_khkt_nd">Nội dung thực tập</label></td>
					  <td align=left colspan = 2>
						<input style="width:95%;" placeholder="" id="hv_info_khkt_nd"  name="hv_info_khkt_nd" type="text" class="text" value="<?php echo htmlentities($accinfo["THUC_TAP_KHKT_NOI_DUNG"][0], ENT_QUOTES, "UTF-8"); ?>" />
					  </td>
					</tr>
					<tr>
					  <td align=right><label for="hv_info_caohoc_tu" class="heading">Học cao học từ năm</label></td>
					  <td colspan=2 align=left>
						<input style="width:50px;" placeholder="" id="hv_info_caohoc_tu"  name="hv_info_caohoc_tu" type="text" class="text" value="<?php echo htmlentities($accinfo["THOI_DIEM_NHAP_HOC_CAO_HOC"][0], ENT_QUOTES, "UTF-8"); ?>" />
						
						<label for="hv_info_caohoc_den" class=heading>đến năm</label>
						<input style="width:50px;" placeholder="" id="hv_info_caohoc_den"  name="hv_info_caohoc_den" type="text" class="text" value="<?php echo htmlentities($accinfo["THOI_DIEM_TOT_NGHIEP_CAO_HOC"][0], ENT_QUOTES, "UTF-8"); ?>" />
						
						<label for="hv_info_truongcaohoc" class=heading>tại</label>
						<input style="width:293px;" placeholder="" id="hv_info_truongcaohoc"  name="hv_info_truongcaohoc" type="text" class="text" value="<?php echo htmlentities($accinfo["TRUONG_CAO_HOC"][0], ENT_QUOTES, "UTF-8"); ?>" />
					  </td>
					</tr>
					<tr>
					  <td align=right class="heading"><label for="hv_info_cn_caohoc">Chuyên ngành</label></td>
					  <td align=left colspan = 2>
						<input style="width:95%;" placeholder="" id="hv_info_cn_caohoc"  name="hv_info_cn_caohoc" type="text" class="text" value="<?php echo htmlentities($accinfo["MA_NGANH_CAO_HOC"][0], ENT_QUOTES, "UTF-8"); ?>" />
					  </td>
					</tr>
					<tr>
					  <td align=right><label for="hv_info_caohoc_ngaybaove" class="heading">Ngày bảo vệ luận văn thạc sĩ</label></td>
					  <td colspan=2 align=left>
						<input style="width:110px;" placeholder="dd/mm/yyyy" id="hv_info_caohoc_ngaybaove"  name="hv_info_caohoc_ngaybaove" type="text" class="text" value="<?php echo htmlentities($accinfo["NGAY_BAO_VE_LVTHS"][0], ENT_QUOTES, "UTF-8"); ?>" />
						<label for="hv_info_caohoc_noibaove" class=heading>nơi bảo vệ</label>
						<input style="width:306px;" placeholder="" id="hv_info_caohoc_noibaove"  name="hv_info_caohoc_noibaove" type="text" class="text" value="<?php echo htmlentities($accinfo["NOI_BAO_VE_LVTHS"][0], ENT_QUOTES, "UTF-8"); ?>" />
					  </td>
					</tr>
					
					<tr>
					  <td colspan=3 align=left><b><label for="">3. Quá trình học tập và làm việc của bản thân (từ khi học đại học đến nay)</b></label></td>
					</tr>
					<tr>
					  <td colspan=3 align=center>
						<div style="margin-bottom: 10px">
							<button id="hv_info_btn_open_dlg_add_ht_lv">Thêm quá trình học tập và làm việc</button>
						</div>
						<table id="hv_tbl_hoctap_lamviec" name="hv_tbl_hoctap_lamviec" style="width:95%; background: white" border="1" cellpadding="5" cellspacing="0"  class="bordertable">
							<thead>	
								<tr class="bordertable">
									<th align="center">Từ Ngày</th><th align="center">Đến Ngày</th><th align="center">Học hoặc làm việc gì</th><th align="center">Ở đâu</th><th align="center">Thành tích học tập</th><th align="center">Xoá</th>
								</tr>
							</thead>
							<tbody></tbody>
						</table>
						
					  </td>
					</tr>
					
					<tr>
					  <td colspan=3 align=left><b><label for="">4. Kết quả hoạt động khoa học, kỹ thuật</b></label></td>
					</tr>
					<tr>
					  <td colspan=3 align=left>&nbsp;&nbsp;<b><label for="hv_info_bbkh">Bài báo khoa học</b></label></td>
					</tr>
					<tr>
					  <td colspan=3 align=left>
						<textarea rows="2" cols="75" class="text " id="hv_info_bbkh" name="hv_info_bbkh" style="width:94%; margin-left: 20px; font-size:15px"><?php echo htmlentities($accinfo["BAI_BAO"][0], ENT_QUOTES, "UTF-8"); ?></textarea>
					  </td>
					</tr>
					
					<tr>
					  <td colspan=3 align=left>&nbsp;&nbsp;<b><label for="hv_info_detai">Đề tài NCKH</b></label></td>
					</tr>
					<tr>
					  <td colspan=3 align=left>
						<textarea rows="2" cols="75" class="text " id="hv_info_detai" name="hv_info_detai" style="width:94%; margin-left: 20px; font-size:15px"><?php echo htmlentities($accinfo["DE_TAI_NCKH"][0], ENT_QUOTES, "UTF-8"); ?></textarea>
					  </td>
					</tr>
					
					<tr>
					  <td colspan=3 align=left>&nbsp;&nbsp;<b><label for="hv_info_giaithuong">Giải thưởng khoa học các cấp</b></label></td>
					</tr>
					<tr>
					  <td colspan=3 align=left>
						<textarea rows="2" cols="75" class="text " id="hv_info_giaithuong" name="hv_info_giaithuong" style="width:94%; margin-left: 20px; font-size:15px"><?php echo htmlentities($accinfo["GIAI_THUONG_KHCN"][0], ENT_QUOTES, "UTF-8"); ?></textarea>
					  </td>
					</tr>
					
					<tr>
					  <td colspan=3 align=left>&nbsp;&nbsp;<b><label for="hv_info_hoinghi">Tham gia các hội nghị khoa học quốc tế</b></label></td>
					</tr>
					<tr>
					  <td colspan=3 align=left>
						<textarea rows="2" cols="75" class="text " id="hv_info_hoinghi" name="hv_info_hoinghi" style="width:94%; margin-left: 20px; font-size:15px"><?php echo htmlentities($accinfo["THAM_GIA_HOI_NGHI"][0], ENT_QUOTES, "UTF-8"); ?></textarea>
					  </td>
					</tr>
					
					<tr>
					  <td colspan=3 align=left><b><label for="hv_info_cmon_nvong">5. Khả năng chuyên môn, nguyện vọng hiện nay về hoạt động khoa học, kỹ thuật</b></label></td>
					</tr>
					<tr>
					  <td colspan=3 align=left>
						<textarea rows="3" cols="75" class="text " id="hv_info_cmon_nvong" name="hv_info_cmon_nvong" style="width:94%; margin-left: 20px; font-size:15px"><?php echo htmlentities($accinfo["KN_CMON_NVONG"][0], ENT_QUOTES, "UTF-8"); ?></textarea>
					  </td>
					</tr>
					<!--
					<tr>
					  <td align=right></td>
					  <td colspan=2 align=left></td>
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
					-->
					<tr> 
						<td colspan=3> <div align="center" id="tipAI" class=" validateTips"></div></td>
					</tr> 
					<tr>
					  <td align="left" colspan=3>
						<table style="width:100%">
							<tr>
								<td align="left" style="width:50%;">
									• <font color="red">Bạn <b>phải upload hình kỷ yếu</b> lên server mới <b>có thể in LLKH</b>.</font>
									<br>
									<br>
									• Sau khi bấm <b>In LLKH</b> mà không thấy cửa sổ in mở ra có thể <b>trình duyệt của bạn đã bị khoá pop-up</b>, bạn vui lòng xem <b>hướng dẫn mở khoá pop-up</b> tại đây <a href="http://www.pgs.hcmut.edu.vn/faq#pgs_8" target="blank"><b>>>></b></a> và bấm In LLKH lại lần nữa.
								</td>
								<td align="right" valign="top" style="width:50%"><a id="hv_btnPrintInfo" target="blank" href="hv_print_llkh.php?hisid=<?php echo $_REQUEST["hisid"]; ?>">In LLKH</a>&nbsp;&nbsp; <button id="hv_btnInfoChange">Lưu thay đổi</button>&nbsp;&nbsp;&nbsp;</td>
							</tr>
						</table>
					  </td>
					</tr>
					<tr>
					  <td align="center" colspan=3></td>
					</tr>
					<tr>
					  <td align="center" colspan=3></td>
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

<div id=hv_info_diag_ht_lv style='width:100%;' title="Thêm quá trình học tập và làm việc">
	<form id=hv_info_frm_reg_ht_lv name=hv_info_frm_reg_ht_lv>
		<b><label for="hv_info_frm_reg_ht_lv_tu_ngay">Từ ngày</label>...<label for="hv_info_frm_reg_ht_lv_den_ngay">đến ngày</label></b>
		<div style='margin:5px 0 10px 0;width:100%;'>
			<input type=text id='hv_info_frm_reg_ht_lv_tu_ngay' name='hv_info_frm_reg_ht_lv_tu_ngay' maxlength=10 style='width:100px;' placeholder='từ ngày'  title='Ngày, tháng, năm'>
			&nbsp; &nbsp;
			<input type=text id='hv_info_frm_reg_ht_lv_den_ngay' name='hv_info_frm_reg_ht_lv_den_ngay' maxlength=10 style='width:100px;' placeholder='đến ngày'  title='Ngày, tháng, năm'>
		</div>
		<b><label for="hv_info_frm_reg_ht_lv_hoclam">Học hoặc làm việc gì</label></b> 
		<div style='margin:5px 0 10px 0;width:100%;'>
			<input type=text id='hv_info_frm_reg_ht_lv_hoclam' name='hv_info_frm_reg_ht_lv_hoclam' maxlength=300 style='width:100%;' placeholder=''  title='Học hoặc làm việc gì?'>
		</div>
		<b><label for="hv_info_frm_reg_ht_lv_odau">Ở đâu?</label></b>
		<div style='margin:5px 0 10px 0;width:100%;'>
			<input type=text id='hv_info_frm_reg_ht_lv_odau' name='hv_info_frm_reg_ht_lv_odau' maxlength=300 style='width:100%;' placeholder=''  title='Ở đâu?'/>
		</div>
		<b><label for="hv_info_frm_reg_ht_lv_thanhtich">Thành tích học tập<label></b>
		<div style='margin:5px 0 10px 0;width:100%;'>
			<input type=text id='hv_info_frm_reg_ht_lv_thanhtich' name='hv_info_frm_reg_ht_lv_thanhtich' maxlength=300 style='width:100%;' placeholder=''  title='Thành tích'>
		</div>
		<div id="hv_info_diag_ht_lv_msg" align=center style='margin:10px 0 5px 0;width:100%; color:red;'></div>
	</form>
</div>
					
<script>
hv_info_class = "alt";

function getFile(){
   document.getElementById("hv_file_ky_yeu").click();
}

function hv_file_ky_yeu_change(obj){
  var file = obj.value;
  if (file != ''){
	$("#hv_frm_upload_file_ky_yeu").submit();
  }
}
 
$(function() {
	$(".tooltips").tooltip('hide');
	$( "#hv_btnInfoChange").button({ icons: {primary:'ui-icon ui-icon-disk'} });
	$( "#btn_upload_hinhkyyeu" ).button({ icons: {primary:'ui-icon ui-icon-disk'} });
	$( "#hv_btnPrintInfo" ).button({ icons: {primary:'ui-icon ui-icon-print'} });
	
	$( "#hv_info_btn_open_dlg_add_ht_lv" ).button({ icons: {primary:'ui-icon ui-icon-plusthick'} });
	$( "#hv_info_ngaycap_cmnd, #hv_info_ngaysinh, #hv_info_ngayvaodoan, #hv_info_ngayvaodang, #hv_info_khkt_tu, #hv_info_khkt_den, #hv_info_caohoc_ngaybaove" ).mask("99/99/9999");
	$( "#hv_info_nhaphocdaihoc, #hv_info_totnghiepdaihoc, #hv_info_caohoc_tu, #hv_info_caohoc_den" ).mask("9999");
	
	$("#btn_upload_hinhkyyeu").click(function(e){
		document.getElementById("hv_file_ky_yeu").click();
	});	// end 
	
	// $("#hv_btnPrintInfo").click(function(e){
		
		// hv_processing_diglog("open", "Đang xử lý ....");
		// xreq = $.ajax({
		  // type: 'POST', dataType: "html", data: dataString,
		  // url: "hv_print_llkh.php?hisid=<?php echo $_REQUEST["hisid"]; ?>",
		  // success: function(data) {
			// hv_processing_diglog("close");
			// print_hv_writeConsole(data, 1, "Phòng Đào tạo Sau đại học - Trường Đại học Bách khoa Tp.HCM");
		  // }
		// });
		
	// });	// end 
	
	
	$("#hv_info_btn_open_dlg_add_ht_lv").click(function(e){
		// reset form
		$("#hv_info_frm_reg_ht_lv").find('input[type=text], input[type=hidden], textarea, select').val('');
		
		$( "#hv_info_diag_ht_lv" ).dialog( "open" );
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
	
	var ai_jemail 	= $("#hv_info_email"), ai_jdiachi = $("#hv_info_diachi"), 
	ai_jdienthoai	= $("#hv_info_dienthoai"), ai_jdonvicongtac	= $("#hv_info_donvicongtac"),
	ai_juser		= $("#hv_info_usrname"), ai_jpass = $("#hv_info_pass"),	
	ai_jcmnd 		= $("#hv_info_so_cmnd"), ai_jngaycap = $("#hv_info_ngaycap_cmnd"),
	ai_jnoicap		= $("#hv_info_noicap_cmnd"), ai_jsotk = $("#hv_info_so_tk"),
	
	jhv_info_ngaysinh 			= $("#hv_info_ngaysinh"),
	jhv_info_noisinh			= $("#hv_info_noisinh"),
	jhv_info_dan_toc			= $("#hv_info_dan_toc"),
	jhv_info_ton_giao			= $("#hv_info_ton_giao"),
	jhv_info_dia_chi_thuong_tru	= $("#hv_info_dia_chi_thuong_tru"),
	jhv_info_nghenghiep			= $("#hv_info_nghenghiep"),
	jhv_info_ngayvaodoan		= $("#hv_info_ngayvaodoan"), 
	jhv_info_ngayvaodang		= $("#hv_info_ngayvaodang"),
	jhv_info_doituonguutien		= $("#hv_info_doituonguutien"),
	jhv_info_truongdaihoc		= $("#hv_info_truongdaihoc"),
	jhv_info_nganhdaihoc		= $("#hv_info_nganhdaihoc"),
	jhv_info_hedaotao			= $("#hv_info_hedaotao"), 
	jhv_info_nhaphocdaihoc		= $("#hv_info_nhaphocdaihoc"),
	jhv_info_totnghiepdaihoc	= $("#hv_info_totnghiepdaihoc"),
	jhv_info_loaitndaihoc		= $("#hv_info_loaitndaihoc"),
	
	jhv_info_khkt_tu			= $("#hv_info_khkt_tu"),
	jhv_info_khkt_den			= $("#hv_info_khkt_den"),
	jhv_info_khkt_truong		= $("#hv_info_khkt_truong"),
	jhv_info_khkt_nd			= $("#hv_info_khkt_nd"),
	jhv_info_caohoc_tu			= $("#hv_info_caohoc_tu"), 
	jhv_info_caohoc_den			= $("#hv_info_caohoc_den"),
	jhv_info_truongcaohoc		= $("#hv_info_truongcaohoc"),
	jhv_info_cn_caohoc			= $("#hv_info_cn_caohoc"),
	jhv_info_caohoc_ngaybaove	= $("#hv_info_caohoc_ngaybaove"),
	jhv_info_caohoc_noibaove	= $("#hv_info_caohoc_noibaove"),
	
	jhv_info_giaithuong			= $("#hv_info_giaithuong"),
	jhv_info_hoinghi			= $("#hv_info_hoinghi"),
	jhv_info_detai				= $("#hv_info_detai"),
	jhv_info_bbkh				= $("#hv_info_bbkh"),
	
	jhv_info_cmon_nvong	= $("#hv_info_cmon_nvong"),
	
	ai_allFields = $([]).add(ai_jemail).add(ai_juser).add(ai_jpass).add(ai_jdiachi).add(ai_jdienthoai).add(ai_jdonvicongtac).add(ai_jcmnd).add(ai_jngaycap).add(ai_jnoicap).add(ai_jsotk)
		.add(jhv_info_ngaysinh).add(jhv_info_noisinh).add(jhv_info_dan_toc).add(jhv_info_ton_giao).add(jhv_info_dia_chi_thuong_tru)
		.add(jhv_info_nghenghiep).add(jhv_info_ngayvaodoan).add(jhv_info_ngayvaodang).add(jhv_info_doituonguutien).add(jhv_info_truongdaihoc)
		.add(jhv_info_nganhdaihoc).add(jhv_info_hedaotao).add(jhv_info_nhaphocdaihoc).add(jhv_info_totnghiepdaihoc).add(jhv_info_loaitndaihoc)
		.add(jhv_info_khkt_tu).add(jhv_info_khkt_den).add(jhv_info_khkt_truong).add(jhv_info_khkt_nd).add(jhv_info_caohoc_tu)
		.add(jhv_info_caohoc_den).add(jhv_info_truongcaohoc).add(jhv_info_cn_caohoc).add(jhv_info_caohoc_ngaybaove).add(jhv_info_caohoc_noibaove)
		.add(jhv_info_cmon_nvong).add(jhv_info_giaithuong).add(jhv_info_hoinghi).add(jhv_info_detai).add(jhv_info_bbkh),
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
		
		bValid = bValid && ai_checkLength( jhv_info_ngaysinh, "\"Ngày sinh\"", 0, 10);
		bValid = bValid && ai_checkLength( jhv_info_noisinh, "\"Nơi sinh\"", 0, 2);
		
		bValid = bValid && ai_checkLength( ai_jcmnd, "\"Số CMND\"", 0, 10);
		bValid = bValid && ai_checkLength( ai_jngaycap, "\"Ngày cấp CMND\"", 0, 10);
		bValid = bValid && ai_checkLength( ai_jnoicap, "\"Nơi cấp CMND\"", 0, 2);
		bValid = bValid && ai_checkLength( jhv_info_dan_toc, "\"Dân tộc\"", 0, 2);
		bValid = bValid && ai_checkLength( jhv_info_ton_giao, "\"Tôn giáo\"", 0, 2);
		
		bValid = bValid && ai_checkLength( ai_jsotk, "\"Số TK ngân hàng Đông Á\"", 0, 20, 1);
		
		bValid = bValid && ai_checkLength( jhv_info_dia_chi_thuong_tru, "\"Địa chỉ thường trú\"", 0, 300, 0);
		bValid = bValid && ai_checkLength( ai_jdiachi, "\"Địa chỉ liên lạc\"", 0, 300, 0);
		
		bValid = bValid && ai_checkLength( ai_jdienthoai, "\"Điện thoại\"", 0, 50);
		bValid = bValid && ai_checkLength( ai_jemail, "\"Email\"", 0, 100 );
		bValid = bValid && ai_checkRegexp( ai_jemail, /^((([a-z]|\d|[!#\$%&'\*\+\-\/=\?\^_`{\|}~]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])+(\.([a-z]|\d|[!#\$%&'\*\+\-\/=\?\^_`{\|}~]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])+)*)|((\x22)((((\x20|\x09)*(\x0d\x0a))?(\x20|\x09)+)?(([\x01-\x08\x0b\x0c\x0e-\x1f\x7f]|\x21|[\x23-\x5b]|[\x5d-\x7e]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(\\([\x01-\x09\x0b\x0c\x0d-\x7f]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF]))))*(((\x20|\x09)*(\x0d\x0a))?(\x20|\x09)+)?(\x22)))@((([a-z]|\d|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(([a-z]|\d|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])*([a-z]|\d|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])))\.)+(([a-z]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(([a-z]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])*([a-z]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])))\.?$/i,"Nhập không đúng định dạng email, vd: pgs@hcmut.edu.vn" );
		bValid = bValid && ai_checkLength( ai_jdonvicongtac, "\"Đơn vị công tác\"", 0, 350, 0);
		
		bValid = bValid && ai_checkLength( jhv_info_truongdaihoc, "\"Tên trường đại học\"", 0, 300);
		bValid = bValid && ai_checkLength( jhv_info_nganhdaihoc, "\"Ngành đại học\"", 0, 300);
		bValid = bValid && ai_checkLength( jhv_info_hedaotao, "\"Loại hình đào tạo\"", 0, 3);
		bValid = bValid && ai_checkLength( jhv_info_nhaphocdaihoc, "\"Thời gian ĐT ĐH từ\"", 0, 10);
		bValid = bValid && ai_checkLength( jhv_info_totnghiepdaihoc, "\"Thời gian ĐT ĐH đến\"", 0, 10);
		bValid = bValid && ai_checkLength( jhv_info_loaitndaihoc, "\"Loại tốt nghiệp đại học\"", 0, 10);
		
		//bValid = bValid && ai_checkLength( ai_juser, "\"Tên người dùng\"", 0, 100 );
		//bValid = bValid && ai_checkLength( ai_jpass, "\"Mật khẩu\"", 0, 100 );
		
		if (bValid){
			ai_tips.text("");
			dataString = ai_allFields.serialize(); //$("#form_accountinfo").serialize();
			dataString += '&a=savehvinfo&hisid=<?php echo $_REQUEST["hisid"];?>';
			
			hv_processing_diglog("open","Thông tin tài khoản", "Đang lưu dữ liệu ...");
			
			$.ajax({type: "POST",url: "hv_accountinfo_process.php",data: dataString, dataType: "json",
				success: function(data) {
							//ai_updateTips(data.msg);
							hv_processing_diglog("close");
							hv_open_msg_box(data.msg, 'info', 280, 160);
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
	
	$("#hv_info_diag_ht_lv").dialog({
		resizable: false,
		autoOpen: false,
		width:300, height:360,
		modal: true,
		buttons: [
			{
				id: "hv_info_diag_ht_lv_btn_ok",
				text: "OK",
				click: function() {
					// Check validate
					bValid = true;
					if ($('#hv_info_frm_reg_ht_lv_tu_ngay').val()==""){
						$('#hv_info_frm_reg_ht_lv_tu_ngay').focus();
						$("#hv_info_diag_ht_lv_msg").html('Vui lòng nhập ngày bắt đầu');
						return;
					}
					if ($('#hv_info_frm_reg_ht_lv_den_ngay').val()==""){
						$('#hv_info_frm_reg_ht_lv_den_ngay').focus();
						$("#hv_info_diag_ht_lv_msg").html('Vui lòng nhập ngày kết thúc');
						return;
					}
					
					if ($('#hv_info_frm_reg_ht_lv_hoclam').val()==""){
						$('#hv_info_frm_reg_ht_lv_hoclam').focus();
						$("#hv_info_diag_ht_lv_msg").html('Vui lòng nhập công việc đã làm');
						return;
					}
					
					if ($('#hv_info_frm_reg_ht_lv_odau').val()==""){
						$('#hv_info_frm_reg_ht_lv_odau').focus();
						$("#hv_info_diag_ht_lv_msg").html('Vui lòng nhập nơi học hoặc làm việc');
						return;
					}
					
					/* if ($('#hv_info_frm_reg_ht_lv_thanhtich').val()==""){
						$('#hv_info_frm_reg_ht_lv_thanhtich').focus();
						$("#hv_info_diag_ht_lv_msg").html('Vui lòng nhập thành tích');
						return;
					} */
					
					
					hv_processing_diglog("open","Thông tin tài khoản", "Đang lưu dữ liệu ...");
					dataString = $("#hv_info_frm_reg_ht_lv").serialize() + '&a=addquatrinhhoclam';
					xreq = $.ajax({
					  type: 'POST', dataType: "json", data: dataString,
					  url: "hv_accountinfo_process.php?hisid=<?php echo $_REQUEST["hisid"]; ?>",
					  success: function(data) {
						hv_processing_diglog("close");
						if (data.success == 1){
							hv_add_tbl_hoctap_lamviec(data);
							
							$("button.hv_add_hoctap_remove" ).button({ icons: {primary:'ui-icon ui-icon-trash'} });
							
							$( "#hv_info_diag_ht_lv" ).dialog( "close" );
						}else{
							$( "#hv_info_diag_ht_lv" ).dialog( "close" );
							hv_open_msg_box("Chi tiết lỗi: <br/><div style='margin: 5px 0 0 5px'>" + data.msgerr+'</div>', 'alert', 250, 180, true);
						}
					  }
					});
				}
			},
			{
				id: "hv_info_diag_ht_lv_btn_cancel",
				text: "Cancel",
				click: function() {
					
					$( this ).dialog( "close" );
				}
			}
		]
	});
	
	hv_load_tbl_hoctap_lamviec();
});

function hv_add_tbl_hoctap_lamviec(data){
	$( "#hv_tbl_hoctap_lamviec tbody").append( "<tr style='font-size:12px;' class='bordertable' >" +
	"<td align=left valign=top>" + reverse_escapeJsonString(data.tungay) + "</td>" +
	"<td align=left valign=top>" + reverse_escapeJsonString(data.denngay) + "</td>" +
	"<td align=left valign=top>" + reverse_escapeJsonString(data.hoclam) + "</td>" +
	"<td align=left valign=top>" + reverse_escapeJsonString(data.odau) + "</td>" +
	"<td align=left valign=top>" + reverse_escapeJsonString(data.thanhtich) + "</td>" +
	"<td align=right><button class='hv_add_hoctap_remove' style='height:25px;width:30px;' onclick='hv_remove_tbl_hoctap_lamviec( hv_info_getRowIndex(this),\""+reverse_escapeJsonString(data.maqt)+"\"); return false;'></button></td>" +
	"</tr>" );
	(hv_info_class=='alt') ? hv_info_class='alt_' : hv_info_class='alt';
}

function hv_remove_tbl_hoctap_lamviec(pindex, pmaqt){
	hv_processing_diglog("open","Thông tin tài khoản", "Đang xóa dữ liệu ...");
	
	i = pindex + 1;
	t = document.getElementById('hv_tbl_hoctap_lamviec');
	ma = pmaqt;
	//alert(i + ' ' + manhanluc);
	dataString = 'a=removequatrinhhoclam&maqt='+ma;
	xreq = $.ajax({
	  type: 'POST', dataType: "json", data: dataString,
	  url: 'hv_accountinfo_process.php?hisid=<?php echo $_REQUEST["hisid"]; ?>',
	  success: function(data) {
		hv_processing_diglog("close");
		
		if (data.success == 1){
			t.deleteRow( i );
		}else{
			hv_open_msg_box("<font style='color:red;'>Không thể xóa thông tin.</font>", 'alert', 250, 180, true);
			//hv_open_msg_box("<font style='color:red;'>Không thể xóa thông tin.</font> <br/><div style='margin: 5px 0 0 5px'>" + reverse_escapeJsonString(data.msgerr) +'</div>', 'alert', 250, 180, true);
		}
	  }
	});
}

function hv_load_tbl_hoctap_lamviec(){
	hv_processing_diglog("open","Thông tin tài khoản", "Đang tải dữ liệu ...");
	
	$( "#hv_tbl_hoctap_lamviec tbody").html("");
	dataString = 'a=getquatrinhhoclam';
	xreq = $.ajax({
	  type: 'POST', dataType: "json", data: dataString,
	  url: "hv_accountinfo_process.php?hisid=<?php echo $_REQUEST["hisid"]; ?>",
	  success: function(data) {
		hv_processing_diglog("close");
		if (data.success == 1){
			
			for (var i=0; i<data.quatrinhhoclam.length; i++){
				hv_add_tbl_hoctap_lamviec(data.quatrinhhoclam[i]);
			}
			$("button.hv_add_hoctap_remove" ).button({ icons: {primary:'ui-icon ui-icon-trash'} });
			
			$( "#hv_info_diag_ht_lv" ).dialog( "close" );
		}else{
			$( "#hv_info_diag_ht_lv" ).dialog( "close" );
			hv_open_msg_box("Chi tiết lỗi: <br><div style='margin: 5px 0 0 5px'>" + data.msgerr+'</div>', 'alert', 250, 180, true);
		}
	  }
	});
}

function hv_info_getRowIndex( el ) {
    while( (el = el.parentNode) && el.nodeName.toLowerCase() !== 'tr' );

    if( el ) {
        return el.rowIndex-1;
	}
}
</script>

<?php 
if (isset ($db_conn))
	oci_close($db_conn);
?>