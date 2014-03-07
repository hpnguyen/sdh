<?php
if (isset($_REQUEST["hisid"])){
	session_id($_REQUEST["hisid"]);
	session_start();
}
if (!isset($_SESSION['uidloginPortal'])){
	die('Đã hết phiên làm việc'); 
}

include "../libs/connect_khcn.php";
include "../libs/connect.php";
include "../libs/pgslibs.php";
if (!allowPermisstion(base64_decode($_SESSION['uidloginPortal']), '051', $db_conn)) {die('Truy cập bất hợp pháp');}

$macb = $_REQUEST['m'];
$a = $_REQUEST['a'];
$key = $_REQUEST["k"];

if ($macb == '') 
	$macb = $_SESSION['macb'];

$sqlstr="select cb.*, to_char(cb.NGAY_SINH,'dd-mm-yyyy') NGAY_SINH, decode(PHAI, 'M', 'Nam', 'F', 'Nữ') phai_desc, k.ten_khoa, bm.ten_bo_mon,
		v.ten_chuc_vu, bmql.ten_bo_mon ten_bo_mon_ql, qghv.ten_quoc_gia ten_nuoc_hv, hv.TEN ten_hv, cb.CHUYEN_MON_BC_BO_GDDT,
		decode(MA_HOC_HAM, 'GS','Giáo sư', 'PGS','Phó giáo sư', '') TEN_HOC_HAM, GET_THANH_VIEN(cb.ma_can_bo) HOTENCB,
		get_nam_dat_hv_cao_nhat(cb.ma_can_bo, cb.ma_hoc_vi) nam_dat_hv_cao_nhat
		from can_bo_giang_day cb, bo_mon bm, khoa k, dm_chuc_vu v, bo_mon bmql, quoc_gia qghv, dm_hoc_vi hv
		where cb.ma_bo_mon = bm.ma_bo_mon (+) and bm.ma_khoa = k.ma_khoa (+)
		and cb.fk_chuc_vu = v.ma_chuc_vu (+)
		and cb.ma_bo_mon_ql = bmql.ma_bo_mon (+)
		and cb.qg_dat_hoc_vi = qghv.ma_quoc_gia (+)
		and cb.ma_hoc_vi = hv.ma_hoc_vi (+)
		and cb.ma_can_bo='$macb'";

$stmt = oci_parse($db_conn, $sqlstr);
oci_execute($stmt);
$n = oci_fetch_all($stmt, $cbgd);
oci_free_statement($stmt);
date_default_timezone_set('Asia/Ho_Chi_Minh');
$ngay =date("d");
$thang =date("m");
$nam =date("Y");
$z = 1;
$y = 1;

if ($a != 'print_tmdt_pdf')
{
?>
  <button id="print_tmdt_r01_btn_printpreview<?php echo $key; ?>">&nbsp;In ...</button> <button id="print_tmdt_r01_btn_printpdf<?php echo $key; ?>">&nbsp;In PDF...</button>
  <div align="center" style="margin-top:10px; font-family:Arial,Helvetica,sans-serif;" id="chitietttgv_tmdt_mau_r01<?php echo $key; ?>">
<?php
}else {
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Phòng Đào Tạo Sau Đại Học</title>
</head>

<script src="http://www.grad.hcmut.edu.vn/js/jquery-1.8.3.min.js"></script>
<body style="font-family:Arial, Helvetica, sans-serif">
<?php 
}
?>
	<style>
		.fontcontent {
			font-size: 15px;
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
	</style>
	
	<table width="100%"   cellspacing="0" cellpadding="0" class="ui-corner-all shawdow tableData fontcontent" >
      <tr>
        <td valign='top'> 
			<div align="left" style="margin-top:10px">
				<img src="./images/llkh/logodhqg.png" alt="" style="float:left; margin:-5px 5px 0px 5px;" width="72" height="53">
				<div align=left style="margin:10px 0px 0px 10px;" class=fontcontent><b>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Đại học Quốc gia<br/>Thành phố Hồ Chí Minh</b></div>
			</div>
        </td>
		<td valign='top'> 
			<div align="right"  style="margin-top:10px">
				<table class='borderDOT' style='font-size: 12px;' border=1>
					<tr><td class='borderDOT' >Ngày nhận hồ sơ</td><td class='borderDOT' style='width: 100px'></td></tr>
					<tr><td class='borderDOT' >Mã số đề tài</td><td class='borderDOT' ><span id=khcn_print_r01_masodt<?php echo $key;?> ></span></td></tr>
				</table>
			</div>
        </td>
      </tr>
	  <tr>
        <td colspan=2 valign='top'> 
			<div align="center"  style="margin-top:40px; margin-bottom:10px">
				<font style="font-size:20px;"><b>THUYẾT MINH</b><br>ĐỀ TÀI KHOA HỌC VÀ CÔNG NGHỆ</font>
			</div>
        </td>
      </tr>
      <tr>
        <td colspan=2>
      
			<table id=khcn_print_r01_table_content<?php echo $key; ?> width="100%" style="margin-top:10px" border="0" align="center" cellpadding="5" cellspacing="0" class="fontcontent">
				<tr align="left" style="font-weight:bold">        
					<td align=left style="width:15px">A.</td><td >THÔNG TIN CHUNG</td>
				</tr>
				<tr align="left">        
					<td align=left ><b><?php echo "A".$z++."."; ?></b></td><td  style=""><b>Tên đề tài</b></td>
				</tr>
				<tr align="left">        
					<td align=left ></td><td  style="">- Tên tiếng Việt: <span id=khcn_print_r01_ten_dt_viet<?php echo $key; ?>></span></td>
				</tr>
				<tr align="left">        
					<td align=left ></td><td  style="">- Tên tiếng Anh: <span id=khcn_print_r01_ten_dt_anh<?php echo $key; ?>></span></td>
				</tr>
				
				<tr align="left">
					<td align=left valign=top><b><?php echo "A".$z++."."; ?></b></td><td valign=top><b>Thuộc ngành/nhóm ngành</b></td>
				</tr>
				<tr align="left">
					<td align=left></td><td><span id=khcn_print_r01_nganh_nhom_nganh<?php echo $key; ?>></span></td>
				</tr>
				
				<tr align="left">
					<td align=left><b><?php echo "A".$z++."."; ?></b></td><td><b>Loại hình nghiên cứu: </b> <span id=khcn_print_r01_loai_hinh_nc<?php echo $key; ?>></span></td>
				</tr>
				
				<tr align="left">
					<td align=left><b><?php echo "A".$z++."."; ?></b></td><td><b>Thời gian thực hiện:</b> <span id=khcn_print_r01_tg_thuc_hien<?php echo $key; ?>></span></td>
				</tr>
				
				<tr align="left">
					<td align=left><b><?php echo "A".$z++."."; ?></b></td><td><b>Tổng kinh phí</b></td>
				</tr>
				<tr align="left">
					<td align=left></td>
					<td>
						Tổng kinh phí: <span id=khcn_print_r01_tong_kinh_phi<?php echo $key; ?> style='line-height: 15pt'></span>, gồm
						<div align=left style='margin: 5px 0 0 0; line-height: 15pt' id=khcn_print_r01_tong_kinh_phi_ct<?php echo $key; ?>></div>
					</td>
				</tr>
				
				<tr align="left">
					<td align=left><b><?php echo "A".$z++."."; ?></b></td><td><b>Chủ nhiệm</b></td>
				</tr>
				<tr align="left">
					<td align=left></td><td><span id=khcn_print_r01_chu_nhiem<?php echo $key; ?> style='line-height: 15pt'></span></td>
				</tr>
				
				<tr align="left">
					<td align=left></td><td align=left><b>Tóm tắt hoạt động nghiên cứu và đào tạo sau đại học có liên quan đến đề tài của chủ nhiệm: </b></td>
				</tr>
				<tr align="left">
					<td align=left></td><td align=left><span id=khcn_print_r01_tom_tat_hoat_dong_nc<?php echo $key; ?>></span></td>
				</tr>
				
				<tr class="tr_dongchunhiem_<?php echo $key; ?>"  align="left">
					<td align=left></td><td><b>Đồng chủ nhiệm</b></td>
				</tr>
				<tr class="tr_dongchunhiem_<?php echo $key; ?>" align="left">
					<td align=left></td><td><span id=khcn_print_r01_dong_chu_nhiem<?php echo $key; ?> style='line-height: 15pt'></span></td>
				</tr>
				
				<tr align="left">
					<td align=left><b><?php echo "A".$z++."."; ?></b></td><td><b>Cơ quan chủ trì</b></td>
				</tr>
				<tr align="left">
					<td align=left></td><td><span id=khcn_print_r01_chu_tri<?php echo $key; ?> style='line-height: 15pt'></span></td>
				</tr>
				
				<tr align="left">
					<td align=left><b><?php echo "A".$z++."."; ?></b></td><td><b>Cơ quan phối hợp thực hiện</b></td>
				</tr>
				<tr align="left">
					<td align=left></td><td><span id=khcn_print_r01_cq_phoi_hop<?php echo $key; ?> style='line-height: 15pt'></span></td>
				</tr>
				
				<tr align="left">
					<td align=left><b><?php echo "A".$z++."."; ?></b></td><td><b>Nhân lực nghiên cứu</b></td>
				</tr>
				<tr align="left">
					<td align=left></td>
					<td align=left>
						<table id=khcn_print_r01_A9_table_nhanluc<?php echo $key; ?> cellspacing="0" cellpadding="5" border=1 class="fontcontent bordertable" border=1 style='width:100%'>
							<thead>
								<tr style='height:20px;font-weight:bold'>
									<td align=center>TT</td><td align=left>Học hàm, học vị, Họ và tên</td><td align=left>SHCC/MSSV</td><td align=left>Đơn vị công tác</td><td align=center title="Số tháng làm việc quy đổi">Số tháng làm việc quy đổi</td>
								</tr>
							</thead>
							<thead>
								<tr style='height:20px;' class=tr_thanh_vien_chu_chot<?php echo $key; ?>>
									<td align=left colspan=5>Thành viên chủ chốt</td>
								</tr>
							</thead>
							<tbody>
							</tbody>
							<thead>
								<tr style='height:20px;' class=tr_ncs_cao_hoc<?php echo $key; ?>>
									<td align=left colspan=5>Nghiên cứu sinh, học viên cao học, sinh viên</td>
								</tr>
							</thead>
							<tbody>
							</tbody>
						</table>
					</td>
				</tr>
				
				<?php $z=1; ?>
				<tr align="left">
					<td align=left><b>B.</b></td><td><b>MÔ TẢ NGHIÊN CỨU</b></td>
				</tr>
				<tr align="left">
					<td align=left><b><?php echo "B".$z++."."; ?></b></td><td><b>Tổng quan tình hình nghiên cứu trong, ngoài nước</b></td>
				</tr>
				<tr align="left">
					<td align=left></td><td align=left><div align=left id=khcn_print_r01_tq_tinh_hinh_nc<?php echo $key; ?>></div></td>
				</tr>
				
				<tr align="left">
					<td align=left><b><?php echo "B".$z++."."; ?></b></td><td><b>Ý tưởng khoa học, tính cấp thiết và tính mới</b></td>
				</tr>
				<tr align="left">
					<td align=left></td><td align=left><div align=left id=khcn_print_r01_y_tuong_kh<?php echo $key; ?>></div></td>
				</tr>
				
				<tr align="left" class=tr_kq_nc_so_khoi<?php echo $key; ?>>
					<td align=left><b><?php echo "B".$z++."."; ?></b></td><td><b>Kết quả nghiên cứu sơ khởi (nếu có)</b></td>
				</tr>
				<tr align="left" class=tr_kq_nc_so_khoi<?php echo $key; ?>>
					<td align=left></td><td align=left><div align=left id=khcn_print_r01_kq_nc_so_khoi<?php echo $key; ?>></div></td>
				</tr>
				
				<tr align="left">
					<td align=left><b><?php echo "B".$z++."."; ?></b></td><td><b>Tài liệu tham khảo</b></td>
				</tr>
				<tr align="left">
					<td align=left></td><td align=left><div align=left id=khcn_print_r01_tai_lieu_tk<?php echo $key; ?>></div></td>
				</tr>
				
				<tr align="left" class=tr_chuyengianc<?php echo $key; ?>>
					<td align=left></td><td><b>Giới thiệu chuyên gia/nhà khoa học am hiểu đề tài này</b></td>
				</tr>
				<tr align="left" class=tr_chuyengianc<?php echo $key; ?>>
					<td align=left></td>
					<td align=left>
						<table id=khcn_print_r01_B4_table_chuyengianc<?php echo $key; ?> cellspacing="0" cellpadding="5" border=1 class="fontcontent bordertable" border=1 style='width:100%'>
							<thead>
								<tr style='height:20px;font-weight:bold'>
									<td align=center>TT</td><td align=left>Họ và tên</td><td align=left>Hướng nghiên cứu chuyên sâu</td><td align=left>Cơ quan công tác, địa chỉ</td><td align=center >Điện thoại, Email</td>
								</tr>
							</thead>
							<tbody></tbody>
						</table>
					</td>
				</tr>
				
				<tr align="left">
					<td align=left><b><?php echo "B".$z++."."; ?></b></td><td><b>Kế hoạch và phương pháp nghiên cứu</b></td>
				</tr>
				
				<tr align="left">
					<td align=left><b><?php echo "B".($z-1).".".$y++; ?></b></td><td><b>Mục tiêu</b></td>
				</tr>
				<tr align="left">
					<td align=left></td><td align=left><div align=left id=khcn_print_r01_muc_tieu_nc_vn<?php echo $key; ?>></div></td>
				</tr>
				
				<tr align="left">
					<td align=left><b><?php echo "B".($z-1).".".$y++; ?></b></td><td><b>Nội dung</b></td>
				</tr>
				<tr align="left">
					<td align=left></td><td align=left><div align=left id=khcn_print_r01_noi_dung_nc<?php echo $key; ?>></div></td>
				</tr>
				
				<tr align="left" class=tr_pa_phoi_hop<?php echo $key; ?>>
					<td align=left><b><?php echo "B".($z-1).".".$y++; ?></b></td><td><b>Phương án phối hợp (nếu có)</b></td>
				</tr>
				<tr align="left" class=tr_pa_phoi_hop<?php echo $key; ?>>
					<td align=left></td><td align=left><div align=left id=khcn_print_r01_pa_phoi_hop<?php echo $key; ?>></div></td>
				</tr>
				
				<?php $y=1; ?>
				<tr align="left">
					<td align=left><b><?php echo "B".$z++."."; ?></b></td><td><b>Kết quả nghiên cứu</b></td>
				</tr>
				<tr align="left">
					<td align=left><b><?php echo "B".($z-1).".".$y++; ?></b></td><td><b>Ấn phẩm khoa học</b></td>
				</tr>
				<tr align="left">
					<td align=left></td>
					<td align=left>
						<table id=khcn_print_r01_B6_table_anphamkh<?php echo $key; ?> cellspacing="0" cellpadding="5" border=1 class="fontcontent bordertable" border=1 style='width:100%'>
							<thead>
								<tr style='height:20px;font-weight:bold'>
									<td align=center>TT</td><td align=left>Tên sách/bài báo dự kiến</td><td align=center>Số lượng</td><td align=left>Dự kiến nơi công bố<br><font style='font-weight:normal'>(tên Tạp chí, Nhà xuất bản)</font></td><td align=left >Ghi chú</td>
								</tr>
							</thead>
							<tbody></tbody>
						</table>
					</td>
				</tr>
				
				<tr align="left">
					<td align=left><b><?php echo "B".($z-1).".".$y++; ?></b></td><td><b>Đăng ký sở hữu trí tuệ</b></td>
				</tr>
				<tr align="left">
					<td align=left></td>
					<td align=left>
						<table id=khcn_print_r01_B6_table_sohuutritue<?php echo $key; ?> cellspacing="0" cellpadding="5" border=1 class="fontcontent bordertable" border=1 style='width:100%'>
							<thead>
								<tr style='height:20px;font-weight:bold'>
									<td align=center>TT</td><td align=left>Hình thức đăng ký</td><td align=center>Số lượng</td><td align=left>Nội dung dự kiến đăng ký</font></td><td align=left >Ghi chú</td>
								</tr>
							</thead>
							<tbody></tbody>
						</table>
					</td>
				</tr>
				
				<tr align="left">
					<td align=left></td><td align=left><b>Mô tả sản phẩm/kết quả nghiên cứu</b></td>
				</tr>
				<tr align="left">
					<td align=left></td><td align=left><b>Dạng I: Các sản phẩm mềm</b></td>
				</tr>
				<tr align="left">
					<td align=left></td>
					<td align=left>
						<table id=khcn_print_r01_B6_table_sanphammem<?php echo $key; ?> cellspacing="0" cellpadding="5" border=1 class="fontcontent bordertable" border=1 style='width:100%'>
							<thead>
								<tr style='height:20px;font-weight:bold'>
									<td align=center>TT</td><td align=left>Tên sản phẩm</td><td align=center>Chỉ tiêu đánh giá (định lượng)</td><td align=left >Ghi chú</td>
								</tr>
							</thead>
							<tbody></tbody>
						</table>
					</td>
				</tr>
				
				<tr align="left">
					<td align=left></td><td align=left><b>Dạng II: Các sản phẩm cứng</b></td>
				</tr>
				<tr align="left">
					<td align=left></td>
					<td align=left>
						<table id=khcn_print_r01_B6_table_sanphamcung<?php echo $key; ?> cellspacing="0" cellpadding="5" border=1 class="fontcontent bordertable" border=1 style='width:100%'>
							<thead>
								<tr style='height:20px; font-weight:bold;'>
									<td align=center rowspan=3>TT</td><td align=center rowspan=3 style=''>Tên sản phẩm cụ thể và chỉ tiêu chất lượng chủ yếu của sản phẩm</td><td rowspan=3 align=center>Đơn vị đo</td><td colspan=3 align=center>Mức chất lượng</td><td rowspan=3 align=center>Dự kiến số lượng/quy mô sản phẩm tạo ra</td>
								</tr>
								<tr style='height:20px; font-weight:bold;'>
									<td rowspan=2 align=center>Chỉ tiêu đánh giá <span style='font-weight: normal'>(định lượng)</span></td><td colspan=2 align=center>Mẫu tương tự<br><span style='font-weight: normal'>(theo các tiêu chuẩn mới nhất)</span></td>
								</tr>
								<tr style='height:20px; font-weight:bold;'>
									<td align=center>Trong nước</td><td align=center>Thế giới</td>
								</tr>
							</thead>
							<tbody></tbody>
						</table>
					</td>
				</tr>
				<tr align="left">
					<td align=left></td><td align=left><b>Mức chất lượng các sản phẩm dạng II so với các sản phẩm tương tự trong nước và thế giới</b></td>
				</tr>
				<tr align="left">
					<td align=left></td><td align=left><div id=khcn_print_r01_B6_muc_cl_sp_dang_ii<?php echo $key; ?>></div></td>
				</tr>
				
				<tr align="left">
					<td align=left><b><?php echo "B".($z-1).".".$y++; ?></b></td><td><b>Kết quả đào tạo</b></td>
				</tr>
				<tr align="left">
					<td align=left></td>
					<td align=left>
						<table id=khcn_print_r01_B6_table_ketquadaotao<?php echo $key; ?> cellspacing="0" cellpadding="5" border=1 class="fontcontent bordertable" border=1 style='width:100%'>
							<thead>
								<tr style='height:20px;font-weight:bold'>
									<td align=center>TT</td><td align=left>Cấp đào tạo</td><td align=center>Số lượng</td><td align=left>Nhiệm vụ được giao trong đề tài</font></td><td align=center >Dự kiến kinh phí <span style='font-weight:normal'>(Triệu đồng)</span></td>
								</tr>
							</thead>
							<tbody></tbody>
						</table>
					</td>
				</tr>
				
				<?php $y=1; ?>
				<tr align="left">
					<td align=left><b><?php echo "B".$z++."."; ?></b></td><td><b>Khả năng ứng dụng kết quả nghiên cứu</b></td>
				</tr>
				<tr align="left">
					<td align=left><b><?php echo "B".($z-1).".".$y++; ?></b></td><td><b>Khả năng ứng dụng trong lĩnh vực đào tạo, nghiên cứu khoa học & công nghệ, chính sách, quản lý...</b></td>
				</tr>
				<tr align="left">
					<td align=left></td><td><div id=khcn_print_r01_B7_ud_kqnc_lv_dao_tao<?php echo $key; ?>></div></td>
				</tr>
				
				<tr align="left">
					<td align=left><b><?php echo "B".($z-1).".".$y++; ?></b></td><td><b>Khả năng về ứng dụng các kết quả nghiên cứu vào sản xuất kinh doanh, về liên doanh liên kết với các doanh nghiệp, về thị trường</b></td>
				</tr>
				<tr align="left">
					<td align=left></td><td><div id=khcn_print_r01_B7_ud_kqnc_sxkd<?php echo $key; ?>></div></td>
				</tr>
				
				<tr align="left">
					<td align=left><b><?php echo "B".($z-1).".".$y++; ?></b></td><td><b>Phương thức chuyển giao kết quả nghiên cứu</b></td>
				</tr>
				<tr align="left">
					<td align=left></td><td><div id=khcn_print_r01_B7_ud_kqnc_chuyen_giao<?php echo $key; ?>></div></td>
				</tr>
				
				<tr align="left">
					<td align=left><b><?php echo "B".$z++."."; ?></b></td><td><b>Tổng hợp kinh phí đề nghị ĐHQG-HCM cấp</b></td>
				</tr>
				<tr align="left">
					<td align=left></td>
					<td align=left>
						<div align=right><em>Đơn vị tính: triệu đồng</em></div>
						<table id=khcn_print_r01_B8_table_khoanchiphi<?php echo $key; ?> cellspacing="0" cellpadding="5" border=1 class="fontcontent bordertable" border=1 style='width:100%'>
							<thead>
								<tr style='height:20px; font-weight: bold'>
									<td align=center rowspan=2>TT</td><td align=left  rowspan=2 style=''>Các khoản chi phí</td><td align=center colspan=3>Đề nghị ĐHQG-HCM cấp</td>
								</tr>
								<tr style='height:20px;'>
									<td align=right>Kinh phí</td><td align=right >Trong đó khoán chi (*)</td><td align=right>%</td>
								</tr>
							</thead>
							<tbody></tbody>
						</table>
						<div style='margin: 5px 0 5px 0;' align=left><i>(*) Theo quy định tại Thông tư số 93/2006/TTLT/BTC-BKHCN của liên Bộ Tài chính - Bộ Khoa học và Công nghệ ban hành ngày 04/10/2006 và Thông tư số 44/2007/TTLT/BTC-BKHCN của liên Bộ Tài chính - Bộ Khoa học và Công nghệ ban hành ngày 07/5/2007.</i></div>
					</td>
				</tr>
				
				
				<tr>
					<td colspan=2 align=right>
						<table width=100% class=fontcontent>
							<tr>
								<td align=left valign=top width=50% >
									<div style="width:300px; margin-top:20px" align=center>
										<span><em>Ngày ...... tháng ...... năm .........</em></span><br/>
										<b>Chủ tịch hội đồng thẩm định <sup>i</sup></b>
										<br/><br/><br/><br/><br/><br/>
										
									</div>
								</td>
								<td align=right width=50%>
									<div style="width:400px; margin-top:20px" align=center>
										<span><em>Ngày <?php echo $ngay ?> tháng <?php echo $thang ?> năm <?php echo $nam ?></em></span>
										<table width=100% class=fontcontent>
											<tr>
												<td align=center>
													<b>Chủ nhiệm</b>
													<br><br><br><br>
													<b><span id=khcn_print_r01_chunhiemkyten<?php echo $key; ?> ></span></b>
												</td>
												<td align=center id="td_dongchunhiem<?php echo $key; ?>">
													<b>Đồng chủ nhiệm</b>
													<br><br><br><br>
													<b><span id=khcn_print_r01_dongchunhiemkyten<?php echo $key; ?>></span></b>
												</td>
											</tr>
										</table>
										
									</div>
								</td>
							</tr>
							<tr>
								<td align=left valign=top width=50% >
									<div style="width:300px; margin-top:20px" align=center>
										<span><em>Ngày ...... tháng ...... năm .........</em></span><br/>
										<b>Cơ quan chủ trì <sup>ii</sup></b>
										<br/><br/><br/><br/><br/><br/>
										
									</div>
								</td>
								<td align=right width=50%>
									<div style="width:400px; margin-top:20px" align=center>
										<span><em>Ngày ...... tháng ...... năm .........</em></span><br/>
										<b>Cơ quan chủ quản <sup>iii</sup></b>
										<br/><br/><br/><br/><br/><br/>
										
									</div>
								</td>
							</tr>
						</table>
					</td>
				</tr>
			</table>
      
        </td>
      </tr>
    </table>
	<script type="text/javascript">
	 
	</script>
<?php
if ($a != 'print_tmdt_pdf'){
?>	
</div>
<?php 
} else {
?>
</body>
</html>

<script type="text/javascript">
	Number.prototype.formatMoney = function(c, d, t){
	//(123456789.12345).formatMoney(2, '.', ',');
		var n = this, 
		c = isNaN(c = Math.abs(c)) ? 2 : c, 
		d = d == undefined ? "." : d, 
		t = t == undefined ? "," : t, 
		s = n < 0 ? "-" : "", 
		i = parseInt(n = Math.abs(+n || 0).toFixed(c)) + "", 
		j = (j = i.length) > 3 ? j % 3 : 0;
	   return s + (j ? i.substr(0, j) + t : "") + i.substr(j).replace(/(\d{3})(?=\d)/g, "$1" + t) + (c ? d + Math.abs(n - i).toFixed(c).slice(2) : "");
	};
	
	function reverse_escapeJsonString (str, pBr) {	
		var nstr = str.replace(/\\\\/g, "\\");
		nstr = nstr.replace(/\\\//g, '/');
		nstr = nstr.replace(/\\"/g, '"');
		nstr = nstr.replace(/\\'/g, "'");
		nstr = nstr.replace(/\\\\n/g, '\n');
		nstr = nstr.replace(/\\\\r/g, '\r');
		nstr = nstr.replace(/\\\\t/g, '\t');
		nstr = nstr.replace(/\\\\f/g, '\x08');
		nstr = nstr.replace(/\\\\b/g, '\x0c');
		if (pBr){
			nstr = nstr.replace(/\n/g, '<br>');
		}
		return nstr;
	}
</script>
<?php 
} 
?>
<script type="text/javascript">
//jQuery.ajax
//$(document).ready(function(){
$(function(){
 var matmdt<?php echo $key; ?>='<?php echo $_REQUEST['m']; ?>', urlgetdata = '';
 
 <?php
 if ($a == 'print_tmdt_fromtab'){
 ?>
 $( "#print_tmdt_r01_btn_printpreview<?php echo $key; ?>, #print_tmdt_r01_btn_printpdf<?php echo $key; ?>" ).button({ icons: {primary:'ui-icon ui-icon-print'} });
 $( "#print_tmdt_r01_btn_printpreview<?php echo $key; ?>" ).click(function(){
	var links = '';
	print_llkh_writeConsole(links + $("#chitietttgv_tmdt_mau_r01<?php echo $key; ?>").html(), 0, 'Thuyết minh đề tài - ĐHQG Mẫu R01 - ' + matmdt<?php echo $key; ?>);
	return false;
 });
 
 $( "#print_tmdt_r01_btn_printpdf<?php echo $key; ?>" ).click(function(){
	var a = encodeURIComponent('http://www.grad.hcmut.edu.vn/gvbeta/khcn/khcn_print_tmdt_r01.php?a=print_tmdt_pdf&hisid=eremqim7im97c46dvp2aijrec1&m=20130001&k=print_tmdt_1_22');
	var content = encodeURIComponent($("#chitietttgv_tmdt_mau_r01<?php echo $key; ?>").html());
	$.download('khcn/khcn_publishpdf.php', 'url_print='+content);
	
	// http://www.grad.hcmut.edu.vn/gvbeta/khcn/khcn_print_tmdt_r01.php?a=print_tmdt_pdf&hisid=eremqim7im97c46dvp2aijrec1&m=20130001&k=print_tmdt_1_22 //?a=print_tmdt_pdf&hisid=<?php echo $_REQUEST["hisid"];?>&m="+matmdt+"&k="+key
	/*
	xreq = $.ajax({
	  type: 'POST', dataType: "html", data: '',
	  url: 'khcn/khcn_publishpdf.php?url_print='+a,
	  success: function(data) {
		alert(data);
	  }
	});*/
	
	return false;
 });
 $("#print_tmdt_r01_btn_printpdf<?php echo $key; ?>").button("disable");
 
 gv_processing_diglog("open","Khoa học & Công nghệ" ,"Đang xử lý ... vui lòng chờ");
 urlgetdata = "khcn/khcn_thuyetminhdtkhcn_process.php";
 
 <?php
 }else {
	echo 'urlgetdata = "http://www.grad.hcmut.edu.vn/gvbeta/khcn/khcn_thuyetminhdtkhcn_process.php";';
 }
 ?>

 // GET THONG TIN CHUNG
 xreq = $.ajax({
   type: 'POST', dataType: "json", data: 'a=getthuyetminhinfo&hisid=<?php echo $_REQUEST["hisid"];?>&m='+matmdt<?php echo $key; ?>,
   url: urlgetdata,
   success: function(data) {
		if (data.success){
			var nhomnganh = '', kinhphi='', chunhiem='', dongchunhiem='', chutri='', strtmp;
			$('#khcn_print_r01_masodt<?php echo $key;?>').html(reverse_escapeJsonString(data.info.masodetai));
			$('#khcn_print_r01_ten_dt_viet<?php echo $key; ?>').html(reverse_escapeJsonString(data.info.tendetaivn));
			$('#khcn_print_r01_ten_dt_anh<?php echo $key; ?>').html(reverse_escapeJsonString(data.info.tendetaien));
			// Nhóm ngành
			for (var i=0; i<data.nhomnganh.length; i++)
			{
				if (data.nhomnganh[i].manganh == "999"){
					nhomnganh += reverse_escapeJsonString(data.nhomnganh[i].nganhkhac) + "; ";
				}else{
					nhomnganh += reverse_escapeJsonString(data.nhomnganh[i].tennganh) + "; ";
				}
			}
			if (data.info.nganhhep){
				nhomnganh += "<br><br>Chuyên ngành hẹp: " + data.info.nganhhep;
			}
			$("#khcn_print_r01_nganh_nhom_nganh<?php echo $key; ?>").html(nhomnganh);
			
			// A3 loaihinhnc
			$("#khcn_print_r01_loai_hinh_nc<?php echo $key; ?>").html(reverse_escapeJsonString(data.info.tenloaihinhnc));
			
			$("#khcn_print_r01_tg_thuc_hien<?php echo $key; ?>").html(reverse_escapeJsonString(data.info.thoigianthuchien) + " tháng (kể từ khi được duyệt)");
			
			// A5 Kinh phí
			$("#khcn_print_r01_tong_kinh_phi<?php echo $key; ?>").html( (parseFloat(reverse_escapeJsonString(data.info.tongkinhphi))*1000000).formatMoney(0,',','.') + ' đồng' + ' (Bằng chữ: '+reverse_escapeJsonString(data.info.chutongkinhphi)+')');
			kinhphi = "- Kinh phí từ ĐHQG-HCM: " + (parseFloat(reverse_escapeJsonString(data.info.kinhphidhqg))*1000000).formatMoney(0,',','.') + ' đồng' + ' (Bằng chữ: '+reverse_escapeJsonString(data.info.chukinhphidhqg)+')';
				
			if (data.info.kinhphihuydong){
				kinhphi += "<br>- Kinh phí từ nguồn huy động: " + (parseFloat(reverse_escapeJsonString(data.info.kinhphihuydong))*1000000).formatMoney(0,',','.') + ' đồng' + ' (Bằng chữ: '+reverse_escapeJsonString(data.info.chukinhphihuydong)+'), trong đó:';
				kinhphi += "<br> &nbsp; &nbsp; &nbsp; Vốn tự có: " + (parseFloat(reverse_escapeJsonString(data.info.vontuco))*1000000).formatMoney(0,',','.') + ' đồng' + ' (Bằng chữ: '+reverse_escapeJsonString(data.info.chuvontuco)+')';
				kinhphi += "<br> &nbsp; &nbsp; &nbsp; Vốn khác: " + (parseFloat(reverse_escapeJsonString(data.info.vonkhac))*1000000).formatMoney(0,',','.') + ' đồng' + ' (Bằng chữ: '+reverse_escapeJsonString(data.info.chuvonkhac)+')';
					
				if (data.info.tochuctaitrokhac){
					kinhphi += "<br> Đã nộp hồ sơ đề nghị tài trợ từ nguồn kinh phí khác, tổ chức tài trợ: " + reverse_escapeJsonString(data.info.tochuctaitrokhac);
				}
			} 
			$("#khcn_print_r01_tong_kinh_phi_ct<?php echo $key; ?>").html(kinhphi);
			
			// A6
			chunhiem = 'Học hàm, học vị, họ và tên: ' + reverse_escapeJsonString(data.info.cndt_hh_hv_ho_ten);
			chunhiem += '<br>Ngày, tháng, năm sinh: ' + reverse_escapeJsonString(data.info.cndt_ngay_sinh) + ', Phái: ' + data.info.cndt_ten_phai;
			chunhiem += '<br>Số CMND: ' + reverse_escapeJsonString(data.info.cndt_so_cmnd) + ', Ngày cấp: ' + reverse_escapeJsonString(data.info.cndt_ngay_cap) + ', Nơi cấp: ' + reverse_escapeJsonString(data.info.cndt_ten_noi_cap);
			chunhiem += '<br>Mã số thuế cá nhân: ' + reverse_escapeJsonString(data.info.cndt_ms_thue);
			chunhiem += '<br>Số tài khoản: ' + reverse_escapeJsonString(data.info.cndt_so_tai_khoan) + ' Tại ngân hàng: ' + reverse_escapeJsonString(data.info.cndt_ngan_hang);
			chunhiem += '<br>Địa chỉ cơ quan: ' + reverse_escapeJsonString(data.info.cndt_dia_chi_cq);
			chunhiem += '<br>Điện thoại: ' + reverse_escapeJsonString(data.info.cndt_dien_thoai) + ', Email: ' + reverse_escapeJsonString(data.info.cndt_email);
			$("#khcn_print_r01_chu_nhiem<?php echo $key; ?>").html(chunhiem);
			
			$("#khcn_print_r01_tom_tat_hoat_dong_nc<?php echo $key; ?>").html(reverse_escapeJsonString(data.info.tom_tat_hd_nc, 1));
			
			if (data.info.dcndt_hh_hv_ho_ten != ""){
				dongchunhiem = 'Học hàm, học vị, họ và tên: ' + reverse_escapeJsonString(data.info.dcndt_hh_hv_ho_ten);
				dongchunhiem += '<br>Ngày, tháng, năm sinh: ' + reverse_escapeJsonString(data.info.dcndt_ngay_sinh) + ', Phái: ' + data.info.dcndt_ten_phai;
				dongchunhiem += '<br>Số CMND: ' + reverse_escapeJsonString(data.info.dcndt_so_cmnd) + ', Ngày cấp: ' + reverse_escapeJsonString(data.info.dcndt_ngay_cap) + ', Nơi cấp: ' + reverse_escapeJsonString(data.info.dcndt_ten_noi_cap);
				dongchunhiem += '<br>Mã số thuế cá nhân: ' + reverse_escapeJsonString(data.info.dcndt_ms_thue);
				dongchunhiem += '<br>Số tài khoản: ' + reverse_escapeJsonString(data.info.dcndt_so_tai_khoan) + ' Tại ngân hàng: ' + reverse_escapeJsonString(data.info.dcndt_ngan_hang);
				dongchunhiem += '<br>Địa chỉ cơ quan: ' + reverse_escapeJsonString(data.info.dcndt_dia_chi_cq);
				dongchunhiem += '<br>Điện thoại: ' + reverse_escapeJsonString(data.info.dcndt_dien_thoai) + ', Email: ' + reverse_escapeJsonString(data.info.dcndt_email);
				$("#khcn_print_r01_dong_chu_nhiem<?php echo $key; ?>").html(dongchunhiem);
			}else{
				$(".tr_dongchunhiem_<?php echo $key; ?>").hide();
			}
			
			// A7
			chutri = 'Tên cơ quan: ' + reverse_escapeJsonString(data.info.cqct_ten_co_quan);
			chutri += '<br>Họ và tên thủ trưởng: ' + reverse_escapeJsonString(data.info.cqct_ho_ten_tt);
			chutri += '<br>Điện thoại: ' + reverse_escapeJsonString(data.info.cqct_dien_thoai) + ' Fax: ' + data.info.cqct_fax;
			chutri += '<br>Email: ' + reverse_escapeJsonString(data.info.cqct_email);
			chutri += '<br>Số tài khoản: ' + reverse_escapeJsonString(data.info.cqct_so_tai_khoan) + ' Tại kho bạc: ' + data.info.cqct_kho_bac;
			$("#khcn_print_r01_chu_tri<?php echo $key; ?>").html(chutri);
			
			// A8
			strtmp = '';
			if (data.info.cqph1_ten_co_quan){
				strtmp = '<b>Cơ quan 1</b>:<br>Tên cơ quan: ' + reverse_escapeJsonString(data.info.cqph1_ten_co_quan);
				strtmp += '<br>Họ và tên thủ trưởng: ' + reverse_escapeJsonString(data.info.cqph1_ho_ten_tt);
				strtmp += '<br>Điện thoại: ' + reverse_escapeJsonString(data.info.cqph1_dien_thoai) + ' Fax: ' + data.info.cqph1_fax;
				strtmp += '<br>Địa chỉ: ' + reverse_escapeJsonString(data.info.cqph1_dia_chi);
			}
			if (data.info.cqph2_ten_co_quan){
				strtmp += '<br><b>Cơ quan 2</b>:<br>Tên cơ quan: ' + reverse_escapeJsonString(data.info.cqph2_ten_co_quan);
				strtmp += '<br>Họ và tên thủ trưởng: ' + reverse_escapeJsonString(data.info.cqph2_ho_ten_tt);
				strtmp += '<br>Điện thoại: ' + reverse_escapeJsonString(data.info.cqph2_dien_thoai) + ' Fax: ' + data.info.cqph2_fax;
				strtmp += '<br>Địa chỉ: ' + reverse_escapeJsonString(data.info.cqph2_dia_chi);
			}
			$("#khcn_print_r01_cq_phoi_hop<?php echo $key; ?>").html(strtmp);
			
			if (data.nhanluc_cbgd.length){
				for (var i=0; i<data.nhanluc_cbgd.length; i++){
					$( "#khcn_print_r01_A9_table_nhanluc<?php echo $key; ?> tbody:eq(0)" ).append( "<tr>" +
					"<td align=center>" + (i+1) + "</td>" +
					"<td align=left>" + reverse_escapeJsonString(data.nhanluc_cbgd[i].ho_ten) + "</td>" +
					"<td align=left>" + reverse_escapeJsonString(data.nhanluc_cbgd[i].shcc) +  "</td>" +
					"<td align=left>" + reverse_escapeJsonString(data.nhanluc_cbgd[i].don_vi_cong_tac) + "</td>" +
					"<td align=center>" + reverse_escapeJsonString(data.nhanluc_cbgd[i].so_thang_lv_quy_doi) + "</td>" +
					"</tr>" );
				}
			}else{
				//$( "#khcn_print_r01_A9_table_nhanluc<?php echo $key; ?> thead:eq(1), #khcn_print_r01_A9_table_nhanluc<?php echo $key; ?> tbody:eq(0)" ).remove();
				$( "#khcn_print_r01_A9_table_nhanluc<?php echo $key; ?> .tr_thanh_vien_chu_chot<?php echo $key; ?>" ).remove();
			}
			if (data.nhanluc_sv.length){
				for (var i=0; i<data.nhanluc_sv.length; i++){
					$( "#khcn_print_r01_A9_table_nhanluc<?php echo $key; ?> tbody:eq(1)" ).append( "<tr>" +
					"<td align=center>" + (i+1) + "</td>" +
					"<td align=left>" + reverse_escapeJsonString(data.nhanluc_sv[i].ho_ten) + " ("+reverse_escapeJsonString(data.nhanluc_sv[i].ma_sv)+")"+ "</td>" +
					"<td align=left>" + reverse_escapeJsonString(data.nhanluc_sv[i].ma_sv) + "</td>" +
					"<td align=left>" + reverse_escapeJsonString(data.nhanluc_sv[i].don_vi_cong_tac) + "</td>" +
					"<td align=center>" + reverse_escapeJsonString(data.nhanluc_sv[i].so_thang_lv_quy_doi) + "</td>" +
					"</tr>" );
				}
			}else{
				//$( "#khcn_print_r01_A9_table_nhanluc<?php echo $key; ?> thead:eq(2) , #khcn_print_r01_A9_table_nhanluc<?php echo $key; ?> tbody:eq(1)" ).remove();
				$( "#khcn_print_r01_A9_table_nhanluc<?php echo $key; ?> .tr_ncs_cao_hoc<?php echo $key; ?>" ).remove();
			}
			
			// Chu ky dong chu nhiem, chu nhiem
			$("#khcn_print_r01_chunhiemkyten<?php echo $key; ?>").html(reverse_escapeJsonString(data.info.cndt_hh_hv_ho_ten));
			if (data.info.dcndt_hh_hv_ho_ten!=''){
				$("#khcn_print_r01_dongchunhiemkyten<?php echo $key; ?>").html(reverse_escapeJsonString(data.info.dcndt_hh_hv_ho_ten));
			}else{
				$("#td_dongchunhiem<?php echo $key; ?>").remove();
			}
			
			
			// Get MO TA NGHIEN CUU
			xreq = $.ajax({
				type: 'POST', dataType: "json", data: 'a=getmotanghiencuu&hisid=<?php echo $_REQUEST["hisid"];?>&m='+matmdt<?php echo $key; ?>,
				url: urlgetdata,
				success: function(data) {
					if (data.success){
						$('#khcn_print_r01_tq_tinh_hinh_nc<?php echo $key; ?>').html(reverse_escapeJsonString(data.mota.tq_tinh_hinh_nc));
						$('#khcn_print_r01_y_tuong_kh<?php echo $key; ?>').html(reverse_escapeJsonString(data.mota.y_tuong_kh));
						if (data.mota.kq_nc_so_khoi){
							$("#khcn_print_r01_kq_nc_so_khoi<?php echo $key; ?>").html(reverse_escapeJsonString(data.mota.kq_nc_so_khoi));
						}else{
							$("#khcn_print_r01_table_content<?php echo $key; ?> .tr_kq_nc_so_khoi<?php echo $key; ?>").remove();
						}
						
						$('#khcn_print_r01_tai_lieu_tk<?php echo $key; ?>').html(reverse_escapeJsonString(data.mota.tai_lieu_tk));
						if (data.chuyengianc.length){
							for (var i=0; i<data.chuyengianc.length; i++){
								$( "#khcn_print_r01_B4_table_chuyengianc<?php echo $key; ?> tbody" ).append( "<tr>" +
								"<td align=center>" + (i+1) + "</td>" +
								"<td align=left>" + reverse_escapeJsonString(data.chuyengianc[i].ho_ten) + "</td>" +
								"<td align=left>" + reverse_escapeJsonString(data.chuyengianc[i].huong_nc_chuyen_sau) + "</td>" +
								"<td align=left>" + reverse_escapeJsonString(data.chuyengianc[i].co_quan_cong_tac) + ", " + reverse_escapeJsonString(data.chuyengianc[i].dia_chi) + "</td>" +
								"<td align=center>" + reverse_escapeJsonString(data.chuyengianc[i].dien_thoai) + ", " + reverse_escapeJsonString(data.chuyengianc[i].email) + "</td>" +
								"</tr>" );
							}
						}else{
							$( "#khcn_print_r01_table_content<?php echo $key; ?> .tr_chuyengianc<?php echo $key; ?>" ).remove();
						}
						
						$('#khcn_print_r01_muc_tieu_nc_vn<?php echo $key; ?>').html(reverse_escapeJsonString(data.mota.muc_tieu_nc_vn));
						$('#khcn_print_r01_noi_dung_nc<?php echo $key; ?>').html(reverse_escapeJsonString(data.mota.noi_dung_nc));
						
						if (reverse_escapeJsonString(data.mota.pa_phoi_hop)){
							$('#khcn_print_r01_pa_phoi_hop<?php echo $key; ?>').html(reverse_escapeJsonString(data.mota.pa_phoi_hop));
						}else{
							$( "#khcn_print_r01_table_content<?php echo $key; ?> .tr_pa_phoi_hop<?php echo $key; ?>" ).remove();
						}
						
						if (data.anphamkhoahoc.length){
							for (var i=0; i<data.anphamkhoahoc.length; i++){
								$( "#khcn_print_r01_B6_table_anphamkh<?php echo $key; ?> tbody" ).append( "<tr>" +
								"<td align=left colspan=5>" + reverse_escapeJsonString(data.anphamkhoahoc[i].ten_an_pham_kh) + "</td>" +
								"</tr>" );
								
								$( "#khcn_print_r01_B6_table_anphamkh<?php echo $key; ?> tbody" ).append( "<tr>" +
								"<td align=center>" + (i+1) + "</td>" +
								"<td align=left>" + reverse_escapeJsonString(data.anphamkhoahoc[i].ten_bb_sach_dk) + "</td>" +
								"<td align=center>" + reverse_escapeJsonString(data.anphamkhoahoc[i].so_luong) + "</td>" +
								"<td align=left>" + reverse_escapeJsonString(data.anphamkhoahoc[i].dk_noi_cong_bo) + "</td>" +
								"<td align=left>" + reverse_escapeJsonString(data.anphamkhoahoc[i].ghi_chu) + "</td>" +
								"</tr>" );
							}
						}
						
						if (data.sohuutritue.length){
							for (var i=0; i<data.sohuutritue.length; i++){
								$( "#khcn_print_r01_B6_table_sohuutritue<?php echo $key; ?> tbody" ).append( "<tr>" +
								"<td align=center>" + (i+1) + "</td>" +
								"<td align=left>" + reverse_escapeJsonString(data.sohuutritue[i].ten_hinh_thuc) + "</td>" +
								"<td align=center>" + reverse_escapeJsonString(data.sohuutritue[i].so_luong) + "</td>" +
								"<td align=left>" + reverse_escapeJsonString(data.sohuutritue[i].noi_dung_du_kien) + "</td>" +
								"<td align=left>" + reverse_escapeJsonString(data.sohuutritue[i].ghi_chu) + "</td>" +
								"</tr>" );
							}
						}
						
						if (data.sanphammem.length){
							for (var i=0; i<data.sanphammem.length; i++){
								$( "#khcn_print_r01_B6_table_sanphammem<?php echo $key; ?> tbody" ).append( "<tr>" +
								"<td align=center>" + (i+1) + "</td>" +
								"<td align=left>" + reverse_escapeJsonString(data.sanphammem[i].ten_san_pham) + "</td>" +
								"<td align=center>" + reverse_escapeJsonString(data.sanphammem[i].chi_tieu_danh_gia) + "</td>" +
								"<td align=left>" + reverse_escapeJsonString(data.sanphammem[i].ghi_chu) + "</td>" +
								"</tr>" );
							}
						}
						
						if (data.sanphamcung.length){
							for (var i=0; i<data.sanphamcung.length; i++){
								$( "#khcn_print_r01_B6_table_sanphamcung<?php echo $key; ?> tbody" ).append( "<tr>" +
								"<td align=center>" + (i+1) + "</td>" +
								"<td align=left>" + reverse_escapeJsonString(data.sanphamcung[i].ten_san_pham) + "</td>" +
								"<td align=center>" + reverse_escapeJsonString(data.sanphamcung[i].don_vi_do) + "</td>" +
								"<td align=center>" + reverse_escapeJsonString(data.sanphamcung[i].chi_tieu_danh_gia) + "</td>" +
								"<td align=center>" + reverse_escapeJsonString(data.sanphamcung[i].trong_nuoc) + "</td>" +
								"<td align=center>" + reverse_escapeJsonString(data.sanphamcung[i].the_gioi) + "</td>" +
								"<td align=left>" + reverse_escapeJsonString(data.sanphamcung[i].so_luong_quy_mo) + "</td>" +
								"</tr>" );
							}
						}
						
						$('#khcn_print_r01_B6_muc_cl_sp_dang_ii<?php echo $key; ?>').html(reverse_escapeJsonString(data.mota.muc_cl_sp_dang_ii));
						
						if (data.ketquadaotao.length){
							for (var i=0; i<data.ketquadaotao.length; i++){
								$( "#khcn_print_r01_B6_table_ketquadaotao<?php echo $key; ?> tbody" ).append( "<tr>" +
								"<td align=center>" + (i+1) + "</td>" +
								"<td align=left>" + reverse_escapeJsonString(data.ketquadaotao[i].ten_capdt) + "</td>" +
								"<td align=center>" + reverse_escapeJsonString(data.ketquadaotao[i].so_luong) + "</td>" +
								"<td align=left>" + reverse_escapeJsonString(data.ketquadaotao[i].nhiem_vu_duoc_giao) + "</td>" +
								"<td align=center>" + reverse_escapeJsonString(data.ketquadaotao[i].du_kien_kinh_phi) + "</td>" +
								"</tr>" );
							}
						}
						$('#khcn_print_r01_B7_ud_kqnc_lv_dao_tao<?php echo $key; ?>').html(reverse_escapeJsonString(data.mota.ud_kqnc_lv_dao_tao));
						$('#khcn_print_r01_B7_ud_kqnc_sxkd<?php echo $key; ?>').html(reverse_escapeJsonString(data.mota.ud_kqnc_sxkd));
						$('#khcn_print_r01_B7_ud_kqnc_chuyen_giao<?php echo $key; ?>').html(reverse_escapeJsonString(data.mota.ud_kqnc_chuyen_giao));
						
						// B8 tong hop kinh phi
						var tongkhoanchi = 0, tongphantram=0;
						for (var i=0; i<data.khoanchiphi.length; i++){ tongkhoanchi += parseFloat(data.khoanchiphi[i].kinh_phi);}
						for (var i=0; i<data.khoanchiphi.length; i++){
							$( "#khcn_print_r01_B8_table_khoanchiphi<?php echo $key; ?> tbody" ).append( "<tr>" +
								"<td align=center>" + (i+1) + "</td>" +
								"<td align=left>" + reverse_escapeJsonString(data.khoanchiphi[i].ten_khoan_chi_phi) + "</td>" +
								"<td align=right>" + parseFloat(reverse_escapeJsonString(data.khoanchiphi[i].kinh_phi)).formatMoney(2,',','.') + "</td>" +
								"<td align=right>" + parseFloat(reverse_escapeJsonString(data.khoanchiphi[i].khoan_chi)).formatMoney(2,',','.') + "</td>" +
								"<td align=right>" + ((parseFloat(data.khoanchiphi[i].kinh_phi)/tongkhoanchi)*100).formatMoney(2,'.',',') + "%</td>" +
								"</tr>" );
							tongphantram += (parseFloat(data.khoanchiphi[i].kinh_phi)/tongkhoanchi)*100;
						}
						
						$( "#khcn_print_r01_B8_table_khoanchiphi<?php echo $key; ?> tbody" ).append( "<tr>" +
								"<td align=center></td>" +
								"<td align=left><b>Cộng:</b></td>" +
								"<td align=right>" + tongkhoanchi.formatMoney(2,',','.') + "</td>" +
								"<td align=right></td>" +
								"<td align=right>"+tongphantram.formatMoney(0,'.',',')+"%</td>" +
								"</tr>" );
						
						<?php
						 if ($a == 'print_tmdt_fromtab'){
							echo 'gv_processing_diglog("close");';
						 }
						?>
					}
				}
			});
			
		}
   }
 });
 
});
</script>

<?php 
if (isset ($db_conn))
	oci_close($db_conn);
if (isset ($db_conn_khcn))
	oci_close($db_conn_khcn);
?>