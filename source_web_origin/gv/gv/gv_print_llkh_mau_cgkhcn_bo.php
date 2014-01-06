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
if (!allowPermisstion(base64_decode($_SESSION['uidloginPortal']), '002', $db_conn))
{
	die('Truy cập bất hợp pháp'); 
}

$macb = $_POST['m'];
$a = $_POST['a'];

if ($macb == '') 
	$macb = $_SESSION['macb'];

$sqlstr="select cb.*, to_char(cb.NGAY_SINH,'dd-mm-yyyy') NGAY_SINH,decode(PHAI, 'M', 'Ông', 'F', 'Bà') title, decode(PHAI, 'M', 'Nam', 'F', 'Nữ') phai_desc, k.ten_khoa, bm.ten_bo_mon,
		v.ten_chuc_vu, bmql.ten_bo_mon ten_bo_mon_ql, qghv.ten_quoc_gia ten_nuoc_hv, hv.TEN ten_hv, cb.CHUYEN_MON_BC_BO_GDDT,
		decode(MA_HOC_HAM, 'GS','Giáo sư', 'PGS','Phó giáo sư', '') TEN_HOC_HAM, GET_THANH_VIEN(cb.ma_can_bo) HOTENCB
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

if ($a != 'get_llkh')
{
?>
  <a id="print_ttgv_mau_nckh_cndt_btn_printpreview">&nbsp;In ...</a>
  <div align="center" style="margin-top:10px; font-family:Arial,Helvetica,sans-serif;" id="chitietllkh_mau_cgkhcn_bo">
<?php
}
?>
	<style>
		.fontcontent1 {
			font-size: 15px;
			font-family: Times New Roman, Arial, Helvetica, sans-serif;
			color: #000000;
			font-weight: normal;

		}
		.bordertable {
			border-color: #000000; 
			border-width: 1px; 
			border-style: solid; 
			border-collapse:collapse;
		}
	</style>
	<table width="100%"   cellspacing="0" cellpadding="0" class="ui-corner-all shawdow tableData fontcontent1" >
      <tr>
        <td valign='top' style='width:100px'> 
			<div align="left" style="margin:0px 5px 0px 50px;" class=fontcontent1>
				<img src="images/llkh/logobokhcn.png" alt="" style="">
			</div>
        </td>
		<td valign='top'> 
			<div align=left>
			<div align="center" class=fontcontent1 style="margin:10px 0 0 100px; color: #083cb3; font-size:18px;width:400px"><b>BỘ KHOA HỌC VÀ CÔNG NGHỆ</b><br/>
			Địa chỉ: Tòa nhà 113 Trần Duy Hưng, Hà Nội <br/>
			-------------------------------------------------
			</div>
			</div>
        </td>
      </tr>
	  <tr>
        <td colspan=2 valign='top'> 
			<hr>
        </td>
      </tr>
	  <tr>
        <td colspan=2 valign='top'> 
			<div align="center"  style="margin-top:20px; margin-bottom:40px">
				<b><font style="font-size:20px; font-weight:bold;">LÝ LỊCH KHOA HỌC<br/>CHUYÊN GIA KHOA HỌC VÀ CÔNG NGHỆ</font></b>
			</div>			
        </td>
      </tr>
      <tr>
        <td colspan=2>
      
			<table width="100%" style="margin-top:0px" align="center" cellpadding="5" cellspacing="0" border=1 class="tableData fontcontent1 bordertable">
				<tr align="left">        
					<td align=left colspan=2><b><?php echo $z++ . "."; ?> Họ và tên:</b> <?php echo $cbgd["HO"][0]. " " .$cbgd["TEN"][0]; ?> </td>
				</tr>
				<tr align="left">
					<td align=left colspan=2><b><?php echo $z++ . "."; ?> Ngày sinh:</b> <?php echo $cbgd["NGAY_SINH"][0]; ?> &nbsp; &nbsp; &nbsp; <b><?php echo $z++ . "."; ?> Nam/Nữ:</b> <?php echo $cbgd["PHAI_DESC"][0]; ?></td>
				</tr>
				<tr align="left">
					<td align=left colspan=2> <b><?php echo $z++ . "."; ?> Học hàm:</b> <?php 	echo $cbgd["TEN_HOC_HAM"][0]; if ($cbgd["MA_HOC_HAM"][0]=='GS' || $cbgd["MA_HOC_HAM"][0]=='PGS' ) echo "&nbsp; &nbsp; &nbsp; <b>Năm được phong:</b> {$cbgd["NAM_PHONG_HOC_HAM"][0]}";	?>
						
						<div style="margin:10px 0 0 15px">
						<b>Học vị:</b> <?php echo $cbgd["TEN_HV"][0];?> &nbsp;&nbsp;&nbsp; <b>Năm đạt học vị:</b> <?php echo $cbgd["NAM_DAT_HOC_VI"][0];?>
						</div>
					</td>
				</tr>
				<tr align="left">
					<td align=left colspan=2> <b><?php echo $z++ . "."; ?> Lĩnh vực nghiên cứu trong 5 năm gần đây: </b> 
								<span style='text-transform:capitalize'>
								<?php 	$sqlstr="SELECT lower(q.TEN_LVNC) TEN_LVNC, q.MA_LVNC, substr(q.MA_LVNC, 1,1) MA_LVNC_C1, lower(q1.ten_lvnc) ten_lvnc_c1, lower(l.LVNC_KHAC) LVNC_KHAC, l.NAM
												FROM NCKH_LVNC_KHCN_CBGD l, NCKH_LVNC_KHCN q, NCKH_LVNC_KHCN q1
												WHERE l.FK_MA_CAN_BO = '$macb' and l.FK_MA_LVNC = q.MA_LVNC (+)
												AND substr(q.MA_LVNC, 1,1) = q1.ma_lvnc (+)
												AND to_number(to_char(sysdate,'yyyy'))-l.nam <= 5";
									$stmt = oci_parse($db_conn, $sqlstr);oci_execute($stmt);$n = oci_fetch_all($stmt, $resDM);oci_free_statement($stmt);
									$lvnc_c1 = ''; $macnkhcn = '';
									for ($i = 0; $i < $n; $i++)
									{
										if ($resDM["LVNC_KHAC"][$i]!='')
											$ten_lvnc_c3 = "{$resDM["TEN_LVNC"][$i]} ({$resDM["LVNC_KHAC"][$i]})";
										else
											$ten_lvnc_c3 = $resDM["TEN_LVNC"][$i];
											
										$lvnc_c1.="; ".$resDM["TEN_LVNC_C1"][$i];
										$macnkhcn.= "; <b>{$resDM["MA_LVNC"][$i]}</b> Tên gọi: $ten_lvnc_c3";
									}								
									echo substr($lvnc_c1, 2);
								?>
								</span>
						<div style="margin:10px 0 0 15px">
							<b>Mã chuyên ngành KH&CN: </b> <span style='text-transform:capitalize'><?php echo substr($macnkhcn, 2); ?></span>
						</div>
					</td>
				</tr>
				<tr align="left">
					<td align=left colspan=2> <b><?php echo $z++ . "."; ?> Chức danh nghiên cứu:</b> <?php 	echo $cbgd["CHUC_DANH_NGHIEN_CUU"][0]; ?>
						<div style="margin:10px 0 0 15px">
						<b>Chức vụ hiện nay:</b> <?php if ($cbgd["TEN_CHUC_VU"][0]!='') echo $cbgd["TEN_CHUC_VU"][0]. ", " .$cbgd["TEN_BO_MON_QL"][0];  ?> 
						</div>
					</td>
				</tr>
				<tr align="left">
					<td align=left colspan=2> <b><?php echo $z++ . "."; ?> Địa chỉ nhà riêng:</b> <?php echo $cbgd["DIA_CHI_RIENG"][0]; ?>
					<div style="margin:10px 0 0 15px">
						Điện thoại cơ quan: <?php echo $cbgd["DIEN_THOAI"][0];?> ; Di động: <?php echo $cbgd["DIEN_THOAI_CN"][0];?>
					</div>
					<div style="margin:10px 0 10px 15px">
						E-mail: <?php echo $cbgd["EMAIL"][0];?>
					</div>
					</td>
				</tr>
				
				<tr align="left">
					<td align=left colspan=2> <b><?php echo $z++ . "."; ?> Cơ quan - nơi làm việc của cá nhân:</b>
						<div style="margin:10px 0 10px 15px">
						Tên cơ quan: <?php echo $cbgd["CO_QUAN_CONG_TAC"][0];?>
						</div>
						<div style="margin:10px 0 10px 15px">
						Tên người đứng đầu: <?php echo $cbgd["TEN_NGUOI_LANH_DAO_CQ"][0];?>
						</div>
						<div style="margin:10px 0 10px 15px">
						Địa chỉ Cơ quan: <?php echo $cbgd["DIA_CHI"][0];?>
						</div>
						<div style="margin:10px 0 10px 15px">
						Điện thoại: <?php echo $cbgd["DIEN_THOAI_LANH_DAO_CQ"][0];?> ; Fax: <?php echo $cbgd["FAX"][0];?>
						</div>
						<div style="margin:10px 0 10px 15px">
						Website: <?php echo $cbgd["WEBSITE_CO_QUAN"][0];?>
						</div>
					</td>
				</tr>
				
				<tr align="left">
					<td align=left colspan=2> <b><?php echo $z++ . "."; ?> Quá trình đào tạo</b>
						<table width="100%" align="center" cellspacing="0" cellpadding="5" style="margin:10px 0 0 0; border-style:dotted" border=1 class="tableData fontcontent1 bordertable" height="20">
							<thead>
							  <tr style="font-weight:bold;">
								<td align=left style="width:100px; border-style:dotted"><em>Bậc đào tạo</em></td>
								<td align=left style='border-style:dotted'><em>Nơi đào tạo</em></td>
								<td align=left style='border-style:dotted'><em>Chuyên môn</em></td>
								<td align=center style='border-style:dotted'><em>Năm tốt nghiệp</em></td>
							  </tr>
							</thead>
							<tbody >
								<?php 	$sqlstr="SELECT q.*, b.TEN_BAC, n.TEN_NGANH, g.TEN_QUOC_GIA, hdt.ten_he_dao_tao
											FROM NCKH_QUA_TRINH_DAO_TAO q, bac_dao_tao b, nckh_nganh_dt n, quoc_gia g, dm_he_dao_tao hdt
											WHERE FK_MA_CAN_BO = '".$macb. "' and q.BAC_DAO_TAO = b.MA_BAC (+) and q.FK_NGANH = n.MA_NGANH (+)
											and q.QG_DAT_HOC_VI = g.MA_QUOC_GIA and q.fk_he_dao_tao = hdt.ma_he_dao_tao (+)
											ORDER BY THOI_GIAN_TN";
									$stmt = oci_parse($db_conn, $sqlstr);oci_execute($stmt);$n = oci_fetch_all($stmt, $resDM);oci_free_statement($stmt);
									for ($i = 0; $i < $n; $i++)
									{	
										if ($resDM["FK_NGANH"][$i]=="99999999")
											$ten_nganh = $resDM["NGANH_KHAC"][$i];
										else
											$ten_nganh = $resDM["TEN_NGANH"][$i];
										$txtTenBacDT 	= $resDM["TEN_BAC"][$i];
										$txtNamBD 		= $resDM["THOI_GIAN_BD"][$i];
										$txtNamTN 		= $resDM["THOI_GIAN_TN"][$i];
										$txtNganh 		= $resDM["FK_NGANH"][$i];
										$txtNoiDT 		= $resDM["NOI_DAO_TAO"][$i];
										$txtQuocGiaDT 	= $resDM["TEN_QUOC_GIA"][$i];
										$txtTenLALV 	= $resDM["TEN_LUAN_AN"][$i];									
										echo "<tr align=left valign='top' >";
										echo "<td align=left style='border-style:dotted'>$txtTenBacDT</td>";
										echo "<td align=left style='border-style:dotted'>$txtNoiDT, $txtQuocGiaDT</td>";
										echo "<td align=left style='border-style:dotted'>$ten_nganh</td>";
										echo "<td align=center style='border-style:dotted'>$txtNamTN</td>";
										echo "</tr>";
									}
								?>
							</tbody>
						</table>
					</td>
				</tr>
				
				<tr align="left">
					<td align=left colspan=2> <b><?php echo $z++ . "."; ?> Trình độ ngoại ngữ</b>
						<table width="100%" align="center" cellspacing="0" cellpadding="5" border=1 style="margin:10px 0 0 0;border-style:dotted" class="tableData fontcontent1 bordertable" height="20">
							<thead>
							  <tr style="font-weight:bold;">
								<td style="width:15px;"><em>TT</em></td>
								<td align=left><em>Tên ngoại ngữ</em></td>
								<td align=left><em>Nghe</em></td>
								<td align=left ><em>Nói</em></td>
								<td align=left ><em>Đọc</em></td>
								<td align=left ><em>Viết</em></td>
							  </tr>
							</thead>
							<tbody>
								<?php 	$sqlstr="SELECT FK_MA_CAN_BO,FK_MA_NGOAI_NGU,a.TEN_NGOAI_NGU,KY_NANG_NGHE,KY_NANG_NOI,KY_NANG_DOC,KY_NANG_VIET,GHI_CHU
											FROM NCKH_QT_NGOAI_NGU n, DM_NGOAI_NGU a
											WHERE FK_MA_CAN_BO='$macb' and n.FK_MA_NGOAI_NGU = a.MA_NGOAI_NGU
											ORDER BY a.TEN_NGOAI_NGU"; 
									$stmt = oci_parse($db_conn, $sqlstr);oci_execute($stmt);$n = oci_fetch_all($stmt, $resDM);oci_free_statement($stmt);
									
									$tmp='';
									for ($i = 0; $i < $n; $i++)
									{
										$tmp.="<tr><td align=left>".($i+1)."</td><td>{$resDM["TEN_NGOAI_NGU"][$i]}</td><td>{$resDM["KY_NANG_NGHE"][$i]}</td><td>{$resDM["KY_NANG_NOI"][$i]}</td><td>{$resDM["KY_NANG_DOC"][$i]}</td><td>{$resDM["KY_NANG_VIET"][$i]}</td></tr> ";
									}
									echo $tmp;
								?>
							</tbody>
						</table>
					</td>
				</tr>
				
				<tr align="left">
					<td align=left colspan=2> <b><?php echo $z++ . "."; ?> Quá trình công tác</b>
						<table width="100%" align="center" cellspacing="0" cellpadding="5" style="margin:10px 0 0 0; border-style:dotted" border=1 class="tableData fontcontent1 bordertable" height="20">
							<thead>
							  <tr style="font-weight:bold;">
								<td align=left style="width:120px; border-style:dotted"><em>Thời gian</em></td>
								<td align=left style='width:120px;border-style:dotted'><em>Vị trí công tác</em></td>
								<td align=left style='border-style:dotted'><em>Lĩnh vực chuyên môn</em></td>
								<td align=left style='border-style:dotted'><em>Cơ quan công tác</em></td>
							  </tr>
							</thead>
							<tbody >
								<?php 	$sqlstr="SELECT n.fk_chuc_vu, c.ten_chuc_vu, n.thoi_gian_kt, n.thoi_gian_bd, n.noi_cong_tac, 
											n.chuyen_mon, n.dia_chi_co_quan, n.ma_qt_cong_tac
											FROM nckh_qua_trinh_cong_tac n, dm_chuc_vu c 
											WHERE n.fk_chuc_vu=c.ma_chuc_vu (+)
											AND fk_ma_can_bo='$macb'
											ORDER BY n.thoi_gian_bd desc"; 
									$stmt = oci_parse($db_conn, $sqlstr);oci_execute($stmt);$n = oci_fetch_all($stmt, $resDM);oci_free_statement($stmt);
									
									for ($i = 0; $i < $n; $i++)
									{
										$txtChucVu 		= $resDM["TEN_CHUC_VU"][$i];
										$txtNamBD 		= $resDM["THOI_GIAN_BD"][$i];
										$txtNamKT 		= $resDM["THOI_GIAN_KT"][$i];
										$txtNoiCongTac	= $resDM["NOI_CONG_TAC"][$i];
										$txtChuyenMon	= $resDM["CHUYEN_MON"][$i];
										if ($txtNamKT=='')
											$thoigian = "Từ <b>$txtNamBD</b> đến nay";
										else
											$thoigian = "Từ <b>$txtNamBD</b> đến <b>$txtNamKT</b>";
										
										echo "<tr align='left' valign='top'>";
										echo "<td style='border-style:dotted'>$thoigian</td>";
										echo "<td align=left style='border-style:dotted'>$txtChucVu</td>";
										echo "<td style='border-style:dotted'>$txtChuyenMon</td>";
										echo "<td style='border-style:dotted'>$txtNoiCongTac</td>";
										echo "</tr>";
									}
								?>
							</tbody>
						</table>
					</td>
				</tr>
				
				<tr align="left">
					<td align=left colspan=2> <b><?php echo $z++ . "."; ?> Các công trình KH&CN chủ yếu được công bố</b>
						<br/> (Liệt kê tối đa 05 công trình tiêu biểu đã công bố liên quan đến đề tài, dự án tuyển chọn trong 5 năm gần nhất)
						<table width="100%" align="center" cellspacing="0" cellpadding="5" style="margin:10px 0 0 0; border-style:dotted" border=1 class="tableData fontcontent1 bordertable" height="20">
							<thead>
							  <tr style="font-weight:bold;">
								<td align=center style="border-style:dotted"><em>TT</em></td>
								<td align=left style='border-style:dotted'><em>Tên công trình<br/><span style="font-weight:normal">(bài báo, công trình...)</span></em></td>
								<td align=center style='border-style:dotted'><em>Tác giả công trình</em></td>
								<td align=left style='border-style:dotted'><em>Nơi công bố<br/><span style="font-weight:normal">(tên tạp chí đã đăng công trình)</span></em></td>
								<td align=center style='border-style:dotted'><em>Năm công bố</em></td>
							  </tr>
							</thead>
							<tbody >
								<?php 
									 $sqlstr="select c.*, q.ten_quoc_gia, l.ten_loai_tac_gia
									from cong_trinh_khoa_hoc c, quoc_gia q, loai_tac_gia l
									where ma_can_bo = '$macb' and c.fk_quoc_gia = q.ma_quoc_gia(+) and c.loai_cong_trinh='BQ'
									and c.fk_ma_loai_tac_gia = l.ma_loai_tac_gia (+)
									order by c.loai_cong_trinh, c.nam_xuat_ban_tap_chi desc"; 
									$stmt = oci_parse($db_conn, $sqlstr);oci_execute($stmt);$n = oci_fetch_all($stmt, $resDM);oci_free_statement($stmt);
									
									for ($i = 0; $i < $n; $i++)
									{	
										$tenbaibao=str_replace($search,$replace,$resDM["TEN_BAI_BAO"][$i]);
										$tentacgia = str_replace($search,$replace,$resDM["TEN_TAC_GIA"][$i]);
										$loaitacgia=str_replace($search,$replace,$resDM["TEN_LOAI_TAC_GIA"][$i]);
										$tentapchi=str_replace($search,$replace,$resDM["TEN_TAP_CHI"][$i]);
										$namxb = str_replace($search,$replace,$resDM["NAM_XUAT_BAN_TAP_CHI"][$i]);
											
										echo "<tr align=left valign=top >";				
										echo "<td align=center valign=top style='border-style:dotted'>" .($i+1)."</td>";
										echo "<td style='border-style:dotted'>$tenbaibao</td>";
										echo "<td align=center valign=top style='border-style:dotted'>$loaitacgia</td>";
										echo "<td align=left valign=top style='border-style:dotted'>$tentapchi</td>";
										echo "<td align=center valign=top style='border-style:dotted'>$namxb</td>";
										echo "</tr>";
									}
								?>
							</tbody>
						</table>
					</td>
				</tr>
				
				<tr align="left">
					<td align=left colspan=2> <b><?php echo $z++ . "."; ?> Số lượng văn bằng bảo hộ sở hữu trí tuệ đã được cấp</b> (nếu có)
						<table width="100%" align="center" cellspacing="0" cellpadding="5" style="margin:10px 0 0 0; border-style:dotted" border=1 class="tableData fontcontent1 bordertable" height="20">
							<thead>
							  <tr style="font-weight:bold;">
								<td align=center style="width:20px; border-style:dotted"><em>TT</em></td>
								<td align=left style='border-style:dotted'><em>Tên và nội dung văn bằng</em></td>
								<td align=center style='border-style:dotted'><em>Năm cấp văn bằng</em></td>
							  </tr>
							</thead>
							<tbody >
								<?php 
									$sqlstr="SELECT n.MA_BANG_SANG_CHE, n.NAM_CAP, n.TEN_BANG, n.FK_MA_CAN_BO
											FROM NCKH_BANG_SANG_CHE n WHERE FK_MA_CAN_BO='$macb' ORDER BY n.NAM_CAP desc";
									$stmt = oci_parse($db_conn, $sqlstr);oci_execute($stmt);$n = oci_fetch_all($stmt, $resDM);oci_free_statement($stmt);
									for ($i = 0; $i < $n; $i++)
									{
										$txtNamCap 				= $resDM["NAM_CAP"][$i];
										$txtTenBang				= str_replace($search,$replace,$resDM["TEN_BANG"][$i]);

										echo "<tr align=left valign='top'>";
										echo "<td align=center style=''>".($i+1)."</td>";
										echo "<td align=left style='border-style:dotted'>$txtTenBang</td>";
										echo "<td align=center style='border-style:dotted'>$txtNamCap </td>";
										echo "</tr>";
									}
								?>
							</tbody>
						</table>
					</td>
				</tr>
				
				<tr align="left">
					<td align=left colspan=2> <b><?php echo $z++ . "."; ?> Số công trình được áp dụng trong thực tiễn</b> (nếu có)
						<table width="100%" align="center" cellspacing="0" cellpadding="5" style="margin:10px 0 0 0; border-style:dotted" border=1 class="tableData fontcontent1 bordertable" height="20">
							<thead>
							  <tr style="font-weight:bold;">
								<td align=left valign=top style="width:20px; border-style:dotted"><em>TT</em></td>
								<td align=left valign=top style='border-style:dotted'><em>Tên công trình</em></td>
								<td align=left valign=top style='border-style:dotted'><em>Hình thức, quy mô, địa chỉ áp dụng</em></td>
								<td align=center valign=top style='border-style:dotted'><em>Thời gian</em></td>
							  </tr>
							</thead>
							<tbody >
								<?php 
									$sqlstr="SELECT *
									FROM NCKH_UD_THUC_TIEN
									WHERE FK_MA_CAN_BO='$macb' ORDER BY thoi_gian_bd desc";
									$stmt = oci_parse($db_conn, $sqlstr);oci_execute($stmt);$n = oci_fetch_all($stmt, $resDM);oci_free_statement($stmt);
									
									$txtMaBang 			= $resDM["MA_UD_THUC_TIEN"][$i];
									$txtTenCongNghe		= str_replace($search,$replace,$resDM["TEN_CONG_NGHE_GP_HU"][$i]);
									$txtHinhThuc		= str_replace($search,$replace,$resDM["HINH_THUC"][$i]);
									$txtQuyMo			= str_replace($search,$replace,$resDM["QUY_MO"][$i]);
									$txtDiaChi			= str_replace($search,$replace,$resDM["DIA_CHI_AP_DUNG"][$i]);
									$txtNamBD			= str_replace($search,$replace,$resDM["THOI_GIAN_BD"][$i]);
									$txtNamKT			= str_replace($search,$replace,$resDM["THOI_GIAN_KT"][$i]);
									$txtSpMaDeTai		= str_replace($search,$replace,$resDM["SAN_PHAM_MA_DE_TAI"][$i]);
									
									if ($txtHinhThuc!="")
										$txthtqmdc .= "$txtHinhThuc, ";
									if ($txtQuyMo!="")
										$txthtqmdc .= "$txtQuyMo, ";
									if ($txtDiaChi!="")
										$txthtqmdc .= "$txtDiaChi, ";
									$txthtqmdc=substr($txthtqmdc,0,-2);
									
									if ($txtNamKT != '')
										$txtThoiGian = "$txtNamBD - $txtNamKT";
									else
										$txtThoiGian = "$txtNamBD";
				
									for ($i = 0; $i < $n; $i++)
									{
										$txtNamCap 				= $resDM["NAM_CAP"][$i];
										$txtTenBang				= str_replace($search,$replace,$resDM["TEN_BANG"][$i]);

										echo "<tr align=left valign='top'>";
										echo "<td style=''>".($i+1)."</td>";
										echo "<td align=left style='border-style:dotted'>$txtTenCongNghe</td>";
										echo "<td align=left style='border-style:dotted'>$txthtqmdc </td>";
										echo "<td align=center style='border-style:dotted'>$txtThoiGian </td>";
										echo "</tr>";
									}
								?>
							</tbody>
						</table>
					</td>
				</tr>
				
				<tr align="left">
					<td align=left colspan=2> <b><?php echo $z++ . "."; ?> Các đề tài, dự án, nhiệm vụ KH&CN đã chủ trì hoặc tham gia trong 5 năm gần đây</b>
						<table width="100%" align="center" cellspacing="0" cellpadding="5" style="margin:10px 0 0 0; border-style:dotted" border=1 class="tableData fontcontent1 bordertable" height="20">
							<thead>
							  <tr style="font-weight:bold;">
								<td align=left  style="border-style:dotted"><em>Tên đề tài, dự án, nhiệm vụ KH&CN đã chủ trì</em></td>
								<td align=center style='width:120px; border-style:dotted'><em>Thời gian</em></td>
								<td align=center style='width:130px; border-style:dotted'><em>Thuộc chương trình</em></td>
								<td align=center style='width:100px;border-style:dotted'><em>Tình trạng đề tài</em></td>
							  </tr>
							</thead>
							<tbody >
								<?php 
									$sqlstr="select a.*, DECODE(a.CHU_NHIEM,1,'CN','TG') THAM_GIA, DECODE(a.NGAY_NGHIEM_THU, null,'chưa nghiệm thu', 'đã nghiệm thu') TT_NGHIEM_THU, b.ten_cap,a.kinh_phi
									from de_tai_nckh a, cap_de_tai b
									where a.fk_cap_de_tai = b.ma_cap(+) and a.ma_can_bo = '$macb' and a.CHU_NHIEM = 1 
									order by a.nam_bat_dau desc";
									$stmt = oci_parse($db_conn, $sqlstr);oci_execute($stmt);$n = oci_fetch_all($stmt, $resDM);oci_free_statement($stmt);
									 
									for ($i = 0; $i < $n; $i++)
									{
										$thuocct = str_replace($search,$replace,$resDM["THUOC_CHUONG_TRINH"][$i]);
										$nghiemthu = str_replace($search,$replace,$resDM["TT_NGHIEM_THU"][$i]);
										$tendetai = str_replace($search,$replace,$resDM["TEN_DE_TAI"][$i]);
										if ($resDM["NAM_KET_THUC"][$i]!="")
											$thoigian = $resDM["NAM_BAT_DAU"][$i]."-".$resDM["NAM_KET_THUC"][$i];
										else
											$thoigian = $resDM["NAM_BAT_DAU"][$i];
										echo "<tr align=left valign='top'>";
										echo "<td align=left style='border-style:dotted'>$tendetai</td>";
										echo "<td align=center style='border-style:dotted'>$thoigian</td>";
										echo "<td align=center style='border-style:dotted'>$thuocct </td>";
										echo "<td align=center style='border-style:dotted'>$nghiemthu </td>";
										echo "</tr>";
									}
								?>
							</tbody>
							<thead>
							  <tr style="font-weight:bold;">
								<td align=left style="border-style:dotted"><em>Tên đề tài, dự án, nhiệm vụ KH&CN đã tham gia</em></td>
								<td align=center style='width:120px; border-style:dotted'><em>Thời gian</em></td>
								<td align=center style='border-style:dotted'><em>Thuộc chương trình</em></td>
								<td align=center style='width:100px;border-style:dotted'><em>Tình trạng đề tài</em></td>
							  </tr>
							</thead>
							<tbody >
								<?php 
									$sqlstr="select a.*, DECODE(a.CHU_NHIEM,1,'CN','TG') THAM_GIA, DECODE(a.NGAY_NGHIEM_THU, null,'chưa nghiệm thu', 'đã nghiệm thu') TT_NGHIEM_THU, b.ten_cap,a.kinh_phi
									from de_tai_nckh a, cap_de_tai b
									where a.fk_cap_de_tai = b.ma_cap(+) and a.ma_can_bo = '$macb' and a.CHU_NHIEM <> 1 
									order by a.nam_bat_dau desc";
									$stmt = oci_parse($db_conn, $sqlstr);oci_execute($stmt);$n = oci_fetch_all($stmt, $resDM);oci_free_statement($stmt);
									 
									for ($i = 0; $i < $n; $i++)
									{
										$thuocct = str_replace($search,$replace,$resDM["THUOC_CHUONG_TRINH"][$i]);
										$nghiemthu = str_replace($search,$replace,$resDM["TT_NGHIEM_THU"][$i]);
										$tendetai = str_replace($search,$replace,$resDM["TEN_DE_TAI"][$i]);
										if ($resDM["NAM_KET_THUC"][$i]!="")
											$thoigian = $resDM["NAM_BAT_DAU"][$i]."-".$resDM["NAM_KET_THUC"][$i];
										else
											$thoigian = $resDM["NAM_BAT_DAU"][$i];
										echo "<tr align=left valign='top'>";
										echo "<td align=left style='border-style:dotted'>$tendetai</td>";
										echo "<td align=center style='border-style:dotted'>$thoigian</td>";
										echo "<td align=center style='border-style:dotted'>$thuocct </td>";
										echo "<td align=center style='border-style:dotted'>$nghiemthu </td>";
										echo "</tr>";
									}
								?>
							</tbody>
						</table>
					</td>
				</tr>
				
				<tr align="left">
					<td align=left colspan=2> <b><?php echo $z++ . "."; ?> Giải thưởng</b>
						<table width="100%" align="center" cellspacing="0" cellpadding="5" style="margin:10px 0 0 0; border-style:dotted" border=1 class="tableData fontcontent1 bordertable" height="20">
							<thead>
							  <tr style="font-weight:bold;">
								<td align=center valign=top style="width:20px; border-style:dotted"><em>TT</em></td>
								<td align=left valign=top style='border-style:dotted'><em>Hình thức và nội dung giải thưởng</em></td>
								<td align=center valign=top style='border-style:dotted'><em>Năm tặng thưởng</em></td>								
							  </tr>
							</thead>
							<tbody >
								<?php 
									$sqlstr="SELECT n.MA_GIAI_THUONG_KHCN, c.TEN_QUOC_GIA TEN_NUOC_CAP, n.NAM_CAP, n.NOI_CAP, 
									n.NOI_DUNG_GIAI_THUONG, n.NUOC_CAP, n.TEN_GIAI_THUONG, n.FK_MA_CAN_BO
									FROM NCKH_GIAI_THUONG_KHCN n, QUOC_GIA c 
									WHERE n.NUOC_CAP=c.MA_QUOC_GIA (+)
									AND FK_MA_CAN_BO='$macb'
									ORDER BY n.NAM_CAP desc";
									$stmt = oci_parse($db_conn, $sqlstr);oci_execute($stmt);$n = oci_fetch_all($stmt, $resDM);oci_free_statement($stmt);
									for ($i = 0; $i < $n; $i++)
									{								
										$txtTenNuocCap 			= $resDM["TEN_NUOC_CAP"][$i];
										$txtNamCap 				= $resDM["NAM_CAP"][$i];
										$txtNoiCap 				= str_replace($search,$replace,$resDM["NOI_CAP"][$i]);
										$txtNoiDungGiaiThuong	= str_replace($search,$replace,$resDM["NOI_DUNG_GIAI_THUONG"][$i]);
										$txtTenGiaiThuong		= str_replace($search,$replace,$resDM["TEN_GIAI_THUONG"][$i]);															
										echo "<tr align=left valign=top>";
										echo "<td align=center style='border-style:dotted'>".($i+1)."</td>";
										echo "<td align=left style='border-style:dotted'>$txtNoiDungGiaiThuong</td>";
										echo "<td align=center style='border-style:dotted'>$txtNamCap </td>";
										echo "</tr>";
									}
								?>
							</tbody>
						</table>
					</td>
				</tr>
				
				<tr align="left">
					<td align=left colspan=2> <b><?php echo $z++ . "."; ?> Kinh nghiệm về quản lý, đánh giá KH&CN </b>
						<br/> (số lượng các Hội đồng tư vấn, xét duyệt, nghiệm thu, đánh giá các chương trình, đề tài, dự án KH&CN cấp nhà nước trong và ngoài nước đã tham gia trong 5 năm gần đây)
						<table width="100%" align="center" cellspacing="0" cellpadding="5" style="margin:10px 0 0 0; border-style:dotted" border=1 class="tableData fontcontent1 bordertable" height="20">
							<thead>
							  <tr style="font-weight:bold;">
								<td align=center valign=top style="width:20px; border-style:dotted"><em>TT</em></td>
								<td align=left valign=top style="border-style:dotted"><em>Hình thức Hội đồng</em></td>
								<td align=center valign=top style="width:40px; border-style:dotted"><em>Năm</em></td>
								<td align=left valign=top style='border-style:dotted'><em>Ghi chú</em></td>
							  </tr>
							</thead>
							<tbody >
								<?php 
									$sqlstr="SELECT FK_MA_CAN_BO,MA_KINH_NGHIEM_QLDG,HINH_THUC_HOI_DONG,NAM,GHI_CHU
									FROM NCKH_KINH_NGHIEM_QLDG n
									WHERE FK_MA_CAN_BO='$macb' AND (TO_NUMBER(TO_CHAR(SYSDATE, 'yyyy'))-NAM) <= 5
									ORDER BY NAM desc";
									$stmt = oci_parse($db_conn, $sqlstr);oci_execute($stmt);$n = oci_fetch_all($stmt, $resDM);oci_free_statement($stmt);
									for ($i = 0; $i < $n; $i++)
									{									
										$txtHinhThuc		= str_replace($search,$replace,$resDM["HINH_THUC_HOI_DONG"][$i]);
										$txtNam	 			= str_replace($search,$replace,$resDM["NAM"][$i]);
										$txtGhiChu			= str_replace($search,$replace,$resDM["GHI_CHU"][$i]);
										echo "<tr align=left valign=top>";
										echo "<td align=center style='border-style:dotted'>".($i+1)."</td>";
										echo "<td align=left style='border-style:dotted'>$txtHinhThuc</td>";
										echo "<td align=center style='border-style:dotted'>$txtNam</td>";
										echo "<td align=left style='border-style:dotted'>$txtGhiChu</td>";
										echo "</tr>";
									}
								?>
							</tbody>
						</table>
					</td>
				</tr>
				
				<tr align="left">
					<td align=left colspan=2> <b><?php echo $z++ . "."; ?> Giới thiệu những chuyên gia khác trong cùng lĩnh vực nghiên cứu KH&CN </b>						
						<table width="100%" align="center" cellspacing="0" cellpadding="5" style="margin:10px 0 0 0; border-style:dotted" border=1 class="tableData fontcontent1 bordertable" height="20">
							<thead>
							  <tr style="font-weight:bold;">
								<td align=center valign=top style="width:20px; border-style:dotted"><em>TT</em></td>
								<td align=left valign=top style="border-style:dotted"><em>Họ và tên</em></td>
								<td align=left valign=top style="border-style:dotted"><em>Nơi công tác</em></td>
								<td align=left valign=top style='border-style:dotted'><em>Địa chỉ liên lạc</em></td>
								<td align=left valign=top style='border-style:dotted'><em>Địa thoại</em></td>
								<td align=left valign=top style='border-style:dotted'><em>Email</em></td>
							  </tr>
							</thead>
							<tbody >
								<?php 
									$sqlstr="SELECT FK_MA_CAN_BO,MA_GT_CHUYEN_GIA,HO_TEN,NOI_CONG_TAC,DIA_CHI_LIEN_LAC,DIEN_THOAI,EMAIL
									FROM NCKH_GT_CHUYEN_GIA n
									WHERE FK_MA_CAN_BO='$macb'
									ORDER BY HO_TEN";
									$stmt = oci_parse($db_conn, $sqlstr);oci_execute($stmt);$n = oci_fetch_all($stmt, $resDM);oci_free_statement($stmt);
									for ($i = 0; $i < $n; $i++)
									{									
										$txtHoTen		= str_replace($search,$replace,$resDM["HO_TEN"][$i]);
										$txtNoiCongTac	= str_replace($search,$replace,$resDM["NOI_CONG_TAC"][$i]);
										$txtDiaChi		= str_replace($search,$replace,$resDM["DIA_CHI_LIEN_LAC"][$i]);
										$txtDienThoai	= str_replace($search,$replace,$resDM["DIEN_THOAI"][$i]);
										$txtEmail		= str_replace($search,$replace,$resDM["EMAIL"][$i]);
										echo "<tr align=left valign=top>";
										echo "<td align=center style='border-style:dotted'>".($i+1)."</td>";
										echo "<td align=left style='border-style:dotted'>$txtHoTen</td>";
										echo "<td align=left style='border-style:dotted'>$txtNoiCongTac</td>";
										echo "<td align=left style='border-style:dotted'>$txtDiaChi</td>";
										echo "<td align=left style='border-style:dotted'>$txtDienThoai</td>";
										echo "<td align=left style='border-style:dotted'>$txtEmail</td>";
										echo "</tr>";
									}
								?>
							</tbody>
						</table>
					</td>
				</tr>
				
				
			</table>
        </td>
      </tr>
    </table>
	<table width="100%" style="margin-top:0px" align="center" cellpadding="5" cellspacing="0" border=0 class="tableData fontcontent1">
		<tr>
			<td colspan=2 align=center>
				<div style='margin-top:10px;'>
				Tôi xác nhận những thông tin được ghi ở trên là hoàn toàn chính xác.
				</div>
			</td>
		</tr>
		<tr>
			<td colspan=2 align=right>
				<table width=100% class=fontcontent1>
					<tr>
						<td align=left valign=top width=50% >
							<div style="width:350px; margin-top:20px" align=center>
								
							</div>
						</td>
						<td align=right width=50%>
							<div style="width:400px; margin-top:20px" align=center>
								<span><em>............., ngày <?php echo $ngay ?> tháng <?php echo $thang ?> năm <?php echo $nam ?></em></span><br/>
								<b>KÝ TÊN</b><br/>
								<br/><br/><br/><br/><br/><br/>
								<b><?php echo $cbgd["HOTENCB"][0]; ?></b>
							</div>
						</td>
					</tr>
				</table>
			</td>
		</tr>
	</table>

<?php
if ($a != 'get_llkh')
{
?>	
</div>
  
<script type="text/javascript">
//jQuery.ajax
//$(document).ready(function(){
$(function(){
 
 $( "#print_ttgv_mau_nckh_cndt_btn_printpreview" ).button({ icons: {primary:'ui-icon ui-icon-print'} });
 $( "#print_ttgv_mau_nckh_cndt_btn_printpreview" ).click(function(){
	print_llkh_writeConsole($("#chitietllkh_mau_cgkhcn_bo").html(), 0);
 });

});
</script>
<?php 
}

if (isset ($db_conn))
	oci_close($db_conn);
?>