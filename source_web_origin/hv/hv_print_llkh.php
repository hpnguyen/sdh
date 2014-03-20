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

$sqlstr="	SELECT h.*, (ho || ' ' || ten) ho_ten, email, n.ten_nganh, tp.TEN_TINH_TP,
				to_char(h.ngay_sinh, 'dd/mm/yyyy') ngay_sinh, decode(h.phai, 'M', 'Nam', 'F', 'Nữ') phai_ten,
				to_char(h.ngay_vao_doan, 'dd/mm/yyyy') ngay_vao_doan, to_char(h.ngay_vao_dang, 'dd/mm/yyyy') ngay_vao_dang,
				to_char(h.THUC_TAP_KHKT_TU_NGAY, 'dd/mm/yyyy') THUC_TAP_KHKT_TU_NGAY, to_char(h.THUC_TAP_KHKT_DEN_NGAY, 'dd/mm/yyyy') THUC_TAP_KHKT_DEN_NGAY,
				to_char(h.NGAY_BAO_VE_LVTHS, 'dd/mm/yyyy') NGAY_BAO_VE_LVTHS,
				thanh_toan_tu_dong, k.ten_kinh_phi_dt, to_char(h.ngay_cap, 'dd/mm/yyyy') ngay_cap,
				decode(ctdt_loai(h.ma_hoc_vien), 1, 'Giảng dạy môn học + khóa luận', 2, 'Giảng dạy môn học + LVThs', 'Nghiên cứu') || ' ' || decode(ctdt_hv_nam(h.ma_hoc_vien), 0, null,'thuộc chương trình: ' || ctdt_hv_nam(h.ma_hoc_vien) || ' năm') ctdt,
				dot_cap_bang('$usr') dot_cap_bang, dt.TEN_DAN_TOC, tg.TON_GIAO, ut.LY_DO_UU_TIEN,
				t.BAI_BAO, t.DE_TAI_NCKH, t.THAM_GIA_HOI_NGHI, t.GIAI_THUONG_KHCN,
				decode(h.HE_DAO_TAO_DH, 'CQ', 'Chính quy', 'Không chính quy') LOAI_HINH_DAO_TAO,
				ltn.TEN_LOAI_TN_DH, LINK_HINH_KY_YEU
			FROM hoc_vien h, nganh n, dm_kinh_phi_dao_tao k, qt_hoat_dong_khkt t, dm_tinh_tp tp, DM_DAN_TOC dt, 
			DM_TON_GIAO tg, DM_DOI_TUONG_UU_TIEN ut, DM_LOAI_TOT_NGHIEP_DAI_HOC ltn
			WHERE upper(h.ma_hoc_vien) = upper('$usr') and h.FK_DOI_TUONG_UU_TIEN=ut.MA_UU_TIEN(+)
			AND h.ma_nganh = n.ma_nganh and h.fk_dan_toc = dt.ma_dan_toc (+) and h.fk_ton_giao = tg.ma_ton_giao(+)
			AND h.fk_kinh_phi_dao_tao = k.ma_kinh_phi_dt AND h.ma_hoc_vien=t.fk_ma_hoc_vien(+) and h.NOI_SINH=tp.MA_TINH_TP(+)
			and h.FK_LOAI_TOT_NGHIEP_DAI_HOC = ltn.MA_LOAI_TN_DH(+)
";

//file_put_contents("logs.txt", "$sqlstr");

$stmt = oci_parse($db_conn, $sqlstr);oci_execute($stmt);$n = oci_fetch_all($stmt, $accinfo);oci_free_statement($stmt);

$linkhinhkyyeu = $accinfo["LINK_HINH_KY_YEU"][0];
if ($linkhinhkyyeu==""){
	die('<div style="width:100%;margin-top: 100px" align="center"><font color="red"><h2>Học viên vui lòng upload hình kỷ yếu trước khi in mẫu LLKH này!</h2></font></div>');
}

$dotcapbang = $accinfo["DOT_CAP_BANG"][0];
if ($dotcapbang == '')
{
	$strsql="SELECT value FROM config WHERE name='DOT_CAP_BANG'";
	$oci_pa = oci_parse($db_conn,$strsql);oci_execute($oci_pa);$result=oci_fetch_all($oci_pa, $kt);oci_free_statement($oci_pa);
	$dotcapbang = $kt["VALUE"][0];
}
// Hinh ky yeu
$hinhkyyeufolder = "hinhkyyeu";

if ($linkhinhkyyeu==""){
	$filehinh = "./$hinhkyyeufolder/$dotcapbang/$usr.jpg";
}else{
	$filehinh = "./$linkhinhkyyeu";
}

date_default_timezone_set('Asia/Ho_Chi_Minh');
$ngay =date("d");
$thang =date("m");
$nam =date("Y");
$tablewith = "700px";
?>
<style type="text/css">
.fontcontent {
	font-size: 12pt;
	font-family: Times New Roman, Arial, Helvetica, sans-serif;
	color: #000000;
	font-weight: normal;
	line-height: 1.2;
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

<div align="center">
   <table width="<?php echo $tablewith;?>" border="0" cellspacing="0" cellpadding="5" class="fontcontent" >
		<tr>
		  <td colspan=2 align=center>
			<div style="float: left">ĐẠI HỌC QUỐC GIA TP.HCM<BR><B>TRƯ<u>ỜNG ĐẠI HỌC BÁCH K</u>HOA</B></div>
			<div ><b>CỘNG HOÀ XÃ HỘI CHỦ NGHĨA VIỆT NAM<BR><u>Độc lập - Tự do - Hạnh phúc</u></b></div>
		  </td>
		</tr>
		<tr>
		  <td colspan=2 align=center>
			<div style="float: left; margin-left:20px;">
				<img id=framehinhkyyeu src="<?php
												// Khoi tao hinh khi load form
												// if ($filehinh!="") {
													// echo $filehinh;	
												// }else{
													// echo "images/khunganh3x4.png";
												// }
												echo "images/khunganh3x4.png";
											?>" border=1 class='ui-widget-content ui-corner-all' width="113px" />
			</div>
			<div style="font-size: 16pt; margin-right:150px;"><br><b>TÓM TẮT LÝ LỊCH KHOA HỌC</b></div>
		  </td>
		</tr>
	
		<tr>
		  <td colspan=2 align=left><b>1. Bản thân</b></td>
		</tr>
		<tr>
		  <td colspan=2 align=left>Họ và tên khai sinh: <?php echo $accinfo["HO_TEN"][0]; ?> &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; Phái: <?php echo $accinfo["PHAI_TEN"][0]; ?></td>
		</tr>
		
		<tr>
			<td align=left>Sinh ngày: <?php echo htmlentities($accinfo["NGAY_SINH"][0], ENT_QUOTES, "UTF-8"); ?></td>
			<td align=left>Nơi sinh:  <?php echo htmlentities($accinfo["TEN_TINH_TP"][0], ENT_QUOTES, "UTF-8"); ?></td>
		</tr>
		<tr>
		  <td align=left>Dân tộc: <?php echo htmlentities($accinfo["TEN_DAN_TOC"][0], ENT_QUOTES, "UTF-8"); ?></td>
		  <td align=left>Tôn giáo: <?php echo htmlentities($accinfo["TON_GIAO"][0], ENT_QUOTES, "UTF-8"); ?></td>
		</tr>
		<tr>
		  <td align=left colspan=2>Địa chỉ thường trú: <?php echo htmlentities($accinfo["DIA_CHI_THUONG_TRU"][0], ENT_QUOTES, "UTF-8"); ?></td>
		</tr> 
		<tr>
		  <td align=left colspan=2>Địa chỉ liên lạc: <?php echo htmlentities($accinfo["DIA_CHI"][0], ENT_QUOTES, "UTF-8"); ?></td>
		</tr>
				
		<tr>
		  <td colspan=2 align=left>
			Điện thoại: <?php echo htmlentities($accinfo["DIEN_THOAI"][0], ENT_QUOTES, "UTF-8"); ?> &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
			Email: <?php echo htmlentities($accinfo["EMAIL"][0], ENT_QUOTES, "UTF-8"); ?>
		  </td>
		</tr>
		
		
		
		<tr>
		  <td align=left colspan=2>Nghề nghiệp, nơi làm việc: <?php echo htmlentities($accinfo["NGHE_NGHIEP"][0], ENT_QUOTES, "UTF-8"); ?>, <?php echo htmlentities($accinfo["DON_VI_CONG_TAC"][0], ENT_QUOTES, "UTF-8"); ?></td>
		</tr>
		
		<tr>
			<td colspan=2 align=left>Ngày vào Đoàn TNCS-HCM: <?php echo htmlentities($accinfo["NGAY_VAO_DOAN"][0], ENT_QUOTES, "UTF-8"); ?></td>
		</tr> 
		<tr>
			<td colspan=2 align=left>Ngày vào Đảng CSVN: <?php echo htmlentities($accinfo["NGAY_VAO_DANG"][0], ENT_QUOTES, "UTF-8"); ?></td>
		</tr>
		<tr>
			<td colspan=2 align=left >Diện chính sách: <?php echo htmlentities($accinfo["LY_DO_UU_TIEN"][0], ENT_QUOTES, "UTF-8"); ?></td>
		</tr>
		
		<tr>
		  <td colspan=2 align=left><b>2. Quá trình đào tạo</b></td>
		</tr>
		<tr>
		  <td colspan=2 align=left><b>a. ĐẠI HỌC</b></td>
		</tr>
		
		<tr>
		  <td colspan=2 align=left>Tốt nghiệp Trường/Viện: <?php echo htmlentities($accinfo["TRUONG_DAI_HOC"][0], ENT_QUOTES, "UTF-8"); ?></td>
		</tr> 
		<tr>
		  <td align=left colspan=2>Ngành học: <?php echo htmlentities($accinfo["FK_NGANH_DAI_HOC"][0], ENT_QUOTES, "UTF-8"); ?></td>
		</tr> 
		<tr>
		  <td colspan=2 align=left>Loại hình đào tạo: <?php echo htmlentities($accinfo["LOAI_HINH_DAO_TAO"][0], ENT_QUOTES, "UTF-8"); ?></td>
		</tr>
		<tr>
		  <td colspan=2 align=left>
			Thời gian đào tạo từ năm: <?php echo htmlentities($accinfo["THOI_DIEM_NHAP_HOC_DAI_HOC"][0], ENT_QUOTES, "UTF-8"); ?>
			đến năm: <?php echo htmlentities($accinfo["THOI_DIEM_TOT_NGHIEP_DAI_HOC"][0], ENT_QUOTES, "UTF-8"); ?>
		  </td>		  
		</tr>
		
		<tr>
		  <td colspan=2 align=left>Xếp loại tốt nghiệp: <?php echo $accinfo["TEN_LOAI_TN_DH"][0]; ?></td>
		</tr>
		
		<tr>
		  <td colspan=2 align=left><b><label for="">b. SAU ĐẠI HỌC</b></label></td>
		</tr>
		
		<tr>
		  <td colspan=2 align=left>Thực tập khoa học, kỹ thuật từ <?php echo htmlentities($accinfo["THUC_TAP_KHKT_TU_NGAY"][0], ENT_QUOTES, "UTF-8"); ?> đến <?php echo htmlentities($accinfo["THUC_TAP_KHKT_DEN_NGAY"][0], ENT_QUOTES, "UTF-8"); ?></td>
		</tr>
		<tr>
		  <td colspan=2 align=left>Tại Trường, Viện, Nước: <?php echo htmlentities($accinfo["THUC_TAP_KHKT_TRUONG"][0], ENT_QUOTES, "UTF-8"); ?></td>
		</tr>
		<tr>
		  <td colspan=2 align=left>Nội dung thực tập: <?php echo htmlentities($accinfo["THUC_TAP_KHKT_NOI_DUNG"][0], ENT_QUOTES, "UTF-8"); ?></td>
		</tr>
		<tr>
		  <td colspan=2 align=left>Học cao học/làm NCS từ năm: <?php echo htmlentities($accinfo["THOI_DIEM_NHAP_HOC_CAO_HOC"][0], ENT_QUOTES, "UTF-8"); ?> 
		  đến năm: <?php echo htmlentities($accinfo["THOI_DIEM_TOT_NGHIEP_CAO_HOC"][0], ENT_QUOTES, "UTF-8"); ?> 
		  tại <?php echo htmlentities($accinfo["TRUONG_CAO_HOC"][0], ENT_QUOTES, "UTF-8"); ?>
		  </td>
		</tr>
		<tr>
		  <td colspan=2 align=left>Chuyên ngành: <?php echo htmlentities($accinfo["MA_NGANH_CAO_HOC"][0], ENT_QUOTES, "UTF-8"); ?></td>
		</tr>
		<tr>
		  <td colspan=2 align=left>Ngày và nơi bảo vệ luận văn thạc sĩ: <?php echo htmlentities($accinfo["NGAY_BAO_VE_LVTHS"][0], ENT_QUOTES, "UTF-8"); ?>, <?php echo htmlentities($accinfo["NOI_BAO_VE_LVTHS"][0], ENT_QUOTES, "UTF-8"); ?></td>
		</tr>
	</table>
	
	<p class="breakhere">
	<table width="<?php echo $tablewith;?>" border="0" cellspacing="0" cellpadding="5" class="fontcontent" >
		<tr>
		  <td colspan=2 align=left><b>3. Quá trình học tập và làm việc của bản thân (từ khi học đại học đến nay):</b></td>
		</tr>
		<tr>
		  <td colspan=2 align=center>
			<table style="width:95%; background: white" border="1" cellpadding="5" cellspacing="0"  class="bordertable">
				<thead>	
					<tr class="bordertable">
						<th align="center">Từ Ngày</th><th align="center">Đến Ngày</th><th align="center">Học hoặc làm việc gì</th><th align="center">Ở đâu</th><th align="center">Thành tích học tập</th>
					</tr>
				</thead>
				<tbody>
					<?php
						$sqlstr="SELECT * FROM QT_HOC_LAM_VIEC_HV WHERE FK_MA_HOC_VIEN='$usr'";
						$stmt = oci_parse($db_conn, $sqlstr);oci_execute($stmt);$n = oci_fetch_all($stmt, $resDM);oci_free_statement($stmt);
						for ($i=0; $i<$n; $i++){
							echo "<tr>
								<td>{$resDM["TU_NGAY"][$i]}</td>
								<td>{$resDM["DEN_NGAY"][$i]}</td>
								<td>{$resDM["HOC_LAM_VIEC"][$i]}</td>
								<td>{$resDM["DIA_DIEM"][$i]}</td>
								<td>{$resDM["THANH_TICH"][$i]}</td>
							</tr>";
						}
					?>
				</tbody>
			</table>
			
		  </td>
		</tr>
		
		<tr>
		  <td colspan=2 align=left><b><label for="">4. Kết quả hoạt động khoa học, kỹ thuật</b></label></td>
		</tr>
		
		<?php if ($accinfo["BAI_BAO"][0]!='') {?>
		<tr>
			<td colspan=2 align=left>
			  <b>Bài báo khoa học</b><br>
			  <?php echo str_replace("\n","<br>",$accinfo["BAI_BAO"][0]); ?>
			</td>
		</tr>
		<?php } ?>
		
		<?php if ($accinfo["DE_TAI_NCKH"][0]!='') {?>
		<tr>
			<td colspan=2 align=left>
			  <b>Đề tài NCKH</b><br>
			  <?php echo str_replace("\n","<br>",$accinfo["DE_TAI_NCKH"][0]); ?>
			</td>
		</tr>
		<?php } ?>
		
		<?php if ($accinfo["THAM_GIA_HOI_NGHI"][0]!='') {?>
		<tr>
			<td colspan=2 align=left>
			  <b>Tham gia các hội nghị khoa học quốc tế</b><br>
			  <?php echo str_replace("\n","<br>",$accinfo["THAM_GIA_HOI_NGHI"][0]); ?>
			</td>
		</tr>
		<?php } ?>
		
		<?php if ($accinfo["GIAI_THUONG_KHCN"][0]!='') {?>
		<tr>
			<td colspan=2 align=left>
			  <b>Giải thưởng khoa học các cấp</b><br>
			  <?php echo str_replace("\n","<br>",$accinfo["GIAI_THUONG_KHCN"][0]); ?>
			 </td>
		</tr>
		<?php } ?>
		
		<tr>
		  <td colspan=2 align=left><b>5. Khả năng chuyên môn, nguyện vọng hiện nay về hoạt động khoa học, kỹ thuật</b></td>
		</tr>
		<tr>
		  <td colspan=2 align=left>
			<?php echo htmlentities($accinfo["KN_CMON_NVONG"][0], ENT_QUOTES, "UTF-8"); ?>
		  </td>
		</tr>
		
		<tr>
		  <td colspan=2 align=left><b>6. Lời cam đoan</b></td>
		</tr>
		<tr>
		  <td colspan=2 align=left>Tôi xin cam đoan những nội dung khai trên là đúng sự thật và xin chịu trách nhiệm trước pháp luật về nội dung lý lịch khoa học của bản thân.</td>
		</tr>
			
		<tr>
		  <td align="left" style="width: 400px" ></td>
		  <td align="center" >
			Ngày <?php echo $ngay; ?> tháng <?php echo $thang; ?> năm <?php echo $nam; ?><br>
			<b>Người khai ký tên</b><br><br><br><br>
			<b><?php echo htmlentities($accinfo["HO_TEN"][0], ENT_QUOTES, "UTF-8"); ?></b>
		  </td>
		</tr>
   </table>
</div>

<script>
	
</script>

<?php 
if (isset ($db_conn))
	oci_close($db_conn);
?>