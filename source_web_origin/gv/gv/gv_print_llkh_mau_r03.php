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

$macb = "'".str_replace("'", "''", $_POST['m'])."'";
$a = str_replace("'", "''",$_REQUEST['a']); // get_llkh
$b = str_replace("'", "''",$_REQUEST['b']); // export_file
$c = str_replace("'", "''",$_REQUEST['c']); // ma thuyet minh de tai
$d = str_replace("'", "''",$_REQUEST['d']); // ten file
$e = str_replace("'", "''",$_REQUEST['e']); // ma can bo

if ($macb == "''"){
	$macb = "'".$_SESSION['macb']."'";
}

if ($e != ''){
	$macb = "'".$e."'";
}

if ($b == 'export_htm'){
	$sqlstr="select FK_MA_CAN_BO from nckhda.NCKH_NHAN_LUC_TMDT_CBGD  where FK_MA_THUYET_MINH_DT = '$c'";
	$stmt = oci_parse($db_conn, $sqlstr);
	if (!oci_execute($stmt))
	{
		$e = oci_error($stmt);
		$msgerr = $e['message']. " sql: " . $e['sqltext'];
		die ('{"success":"-1", "msgerr":"'.escapeWEB($msgerr).'"}');
	}
	$n = oci_fetch_all($stmt, $dscb);oci_free_statement($stmt);
	
	$macb='';
	for ($i=0; $i<$n; $i++){
		$macb .= "'".$dscb["FK_MA_CAN_BO"][$i]."',";
	}
	if ($n) {$macb=substr($macb,0,-1);}
	
	//die($sqlstr.'abc'.$macb);
}

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
		and cb.ma_can_bo in ($macb)";

$stmt = oci_parse($db_conn, $sqlstr); oci_execute($stmt); $n1 = oci_fetch_all($stmt, $cbgd); oci_free_statement($stmt);

date_default_timezone_set('Asia/Ho_Chi_Minh');
$ngay =date("d");
$thang =date("m");
$nam =date("Y");
$gio =date("H");
$phut =date("i");
$z = 1;

//die($sqlstr);

for ($k=0; $k<$n1; $k++)
{
$macb = $cbgd["MA_CAN_BO"][$k];

// update sv tu bk
// Lay ds thac si, tien si tu db bk
$sqlstr="begin NCKH_UPDATE_NCS_HVCH('$macb'); end;"; 
$stmt = oci_parse($db_conn, $sqlstr);oci_execute($stmt);oci_free_statement($stmt);

//file_put_contents("logs.txt", date("H:i:s d.m.Y")." $sqlstr\n", FILE_APPEND);
if ($cbgd["HINH_ANH"][$k]!=""){
	$filehinh  = str_replace("./", "http://www.grad.hcmut.edu.vn/gv/", $cbgd["HINH_ANH"][$k]); //./gv/anh46/0_1838.jpg
}else{
	$filehinh  = "http://www.grad.hcmut.edu.vn/gv/images/llkh/khunganh4x6.png";
}
?>

<?php
if ($b == 'export_htm')
{
	ob_start();
?>
  <html>
  <head>
	  <base href="http://www.pgs.hcmut.edu.vn/" />
	  <meta http-equiv="content-type" content="text/html; charset=utf-8" />
	  <title>Lý lịch khoa học - mẫu R03</title>
  </head>
  <body>
<?php
}
?>

<?php
if ($a != 'get_llkh')
{
?>
  <a id="print_ttgv_r004_btn_printpreview">&nbsp;In ...</a>
  <div align="center" style="margin-top:10px; font-family:Arial,Helvetica,sans-serif;" id="chitietttgv_llkh_mau_r004">
<?php
}
?>
	<style>
		.fontcontent {
			font-size: 13px;
			font-family: Arial, Helvetica, sans-serif;
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
	<table width="100%"   cellspacing="0" cellpadding="0" class="ui-corner-all shawdow tableData fontcontent" >
      <tr>
        <td valign='top'> 
			<div align="left" style="margin-top:10px">
				<img src="http://www.grad.hcmut.edu.vn/gv/images/llkh/logodhqg.png" alt="" style="float:left; margin:-5px 5px 0px 5px;" width="72" height="53">
				<div align=left style="margin:10px 0px 0px 10px;" class=fontcontent><b>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Đại học Quốc gia<br/>Thành phố Hồ Chí Minh</b></div>
			</div>
        </td>
		<td valign='top'> 
		<div align="center"  style="margin-top:10px"> </div>
        </td>
      </tr>
	  <tr>
        <td colspan=2 valign='top'> 
			<div align="center"  style="margin-top:40px; margin-bottom:-10px">
				<b><font style="font-size:20px; font-weight:bold;">LÝ LỊCH KHOA HỌC</font></b><br/>(Thông tin trong 5 năm gần nhất và có liên quan trực tiếp đến đề tài/dự án đăng ký)
				<img id=framehinh46_bmr04 src="" alt="" style="float:right; margin:-90px 0px 0px 5px;" width="160px">
			</div>
			
        </td>
      </tr>
      <tr>
        <td colspan=2>
      
			<table width="100%" style="margin-top:-20px" border="0" align="center" cellpadding="5" cellspacing="0" class="fontcontent">
				<tr align="left" style="font-weight:bold">        
					<td align=left style="width:15px">I.</td><td >THÔNG TIN CHUNG</td>
				</tr>
				<tr align="left">        
					<td align=left ><b><?php echo $z++ . "."; ?></b></td><td  style=""><b>Họ và tên:</b> <?php echo $cbgd["HO"][$k]. " " .$cbgd["TEN"][$k]; ?></td>
				</tr>
				<tr align="left">
					<td align=left><b><?php echo $z++ . "."; ?></b></td><td><b>Ngày sinh:</b> <?php echo $cbgd["NGAY_SINH"][$k]; ?></td>
				</tr>
				<tr align="left">
					<td align=left><b><?php echo $z++ . "."; ?></b></td><td><b>Nam/Nữ:</b> <?php echo $cbgd["PHAI_DESC"][$k]; ?></td>
				</tr>
				<tr align="left" style="font-weight:bold">
					<td align=left><?php echo $z++ . "."; ?></td><td>Nơi đang công tác:</td>
				</tr>
				<tr align="left">
					<td align=left></td><td ><u>Trường/Viện:</u> <?php echo $cbgd["CO_QUAN_CONG_TAC"][$k]; ?></td>
				</tr>
				<tr align="left">
					<td align=left></td><td ><u>Phòng/Khoa:</u> <?php echo $cbgd["TEN_KHOA"][$k]; ?></td>
				</tr>
				<tr align="left">
					<td align=left></td><td ><u>Bộ môn:</u> <?php echo $cbgd["TEN_BO_MON"][$k]; ?></td>
				</tr>
				
				<tr align="left">
					<td align=left></td><td><u>Chức vụ:</u> <?php if ($cbgd["TEN_CHUC_VU"][$k]!='') echo $cbgd["TEN_CHUC_VU"][$k]. " " .$cbgd["TEN_BO_MON_QL"][$k];  ?></td>
				</tr>
				
				<tr align="left">
					<td align=left><b><?php echo $z++ . "."; ?></b></td><td ><b>Học vị:</b> <?php echo $cbgd["TEN_HV"][$k];?>, <b>năm đạt:</b> <?php echo $cbgd["NAM_DAT_HV_CAO_NHAT"][$k];?>
					</td>
				</tr>
				<tr align="left">
					<td align=left><b><?php echo $z++ . "."; ?></b></td><td ><b>Học hàm:</b> <?php 	echo $cbgd["TEN_HOC_HAM"][$k]; 
																									if ($cbgd["MA_HOC_HAM"][$k]=='GS' || $cbgd["MA_HOC_HAM"][$k]=='PGS' ) 
																										echo ", <b>năm phong:</b> {$cbgd["NAM_PHONG_HOC_HAM"][$k]}";
																							?>
					</td>
				</tr>
				<tr align="left">
					<td align=left><b><?php echo $z++ . "."; ?></b></td><td><b>Liên lạc:</b></td>
				</tr>
				<tr align="left">
					<td align=left colspan=2>
						<table width="100%" align="center" cellspacing="0" cellpadding="5" border=1 class="tableData fontcontent bordertable" height="20">
							<thead>
							  <tr style="font-weight:bold;">
								<td style="width:15px;"><em>TT</em></td>
								<td align="left"></td>
								<td align=center><em>Cơ quan</em></td>
								<td align=center ><em>Cá nhân</em></td>
								<!--<td width="59" class="ui-corner-tr" ><em>Kết quả</em></td>-->
							  </tr>
							</thead>
							<tbody>
							  <tr>
								<td>1</td><td><b>Địa chỉ</b></td><td><?php echo $cbgd["DIA_CHI"][$k];?></td><td><?php echo $cbgd["DIA_CHI_RIENG"][$k];?></td>
							  </tr>
							  <tr>
								<td>2</td><td><b>Điện thoại/fax</b></td><td><?php echo $cbgd["DIEN_THOAI"][$k];?></td><td><?php echo $cbgd["DIEN_THOAI_CN"][$k];?></td>
							  </tr>
							  <tr>
								<td>3</td><td><b>Email</b></td><td><?php echo $cbgd["EMAIL"][$k];?></td><td><?php echo $cbgd["EMAIL_2"][$k];?></td>
							  </tr>
							</tbody>
						</table>
					</td>
				</tr>
				<tr align="left">
					<td align=left><b><?php echo $z++ . "."; ?></b></td><td><b>Trình độ ngoại ngữ:</b></td>
				</tr>
				<tr align="left">
					<td align=left colspan=2>
						<table width="100%" align="center" cellspacing="0" cellpadding="5" border=1 class="tableData fontcontent bordertable" height="20">
							<thead>
							  <tr style="font-weight:bold;">
								<td style="width:15px;"><em>TT</em></td>
								<td align=left><em>Tên ngoại ngữ</em></td>
								<td align=left><em>Nghe</em></td>
								<td align=left ><em>Nói</em></td>
								<td align=left ><em>Viết</em></td>
								<td align=left ><em>Đọc hiểu tài liệu</em></td>
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
										$tmp.="<tr><td align=left>".($i+1)."</td><td>{$resDM["TEN_NGOAI_NGU"][$i]}</td><td>{$resDM["KY_NANG_NGHE"][$i]}</td><td>{$resDM["KY_NANG_NOI"][$i]}</td><td>{$resDM["KY_NANG_VIET"][$i]}</td><td>{$resDM["KY_NANG_DOC"][$i]}</td></tr> ";
									}
									echo $tmp;
								?>
							</tbody>
						</table>
					</td>
				</tr>
				
				<tr align="left">
					<td align=left><b><?php echo $z++ . "."; ?></b></td><td><b>Thời gian công tác:</b></td>
				</tr>
				<tr align="left">
					<td align=left colspan=2>
						<table width="100%" align="center" cellspacing="0" cellpadding="5" border=1 class="tableData fontcontent bordertable" height="20">
							<thead>
							  <tr style="font-weight:bold;">
								<td align="left" style="width:130px"><em>Thời gian</em></td>
								<td align=left><em>Nơi công tác</em></td>
								<td align=left><em>Chức vụ</em></td>
							  </tr>
							</thead>
							<tbody>
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
										if ($txtNamKT=='')
											$thoigian = "Từ <b>$txtNamBD</b> đến nay";
										else
											$thoigian = "Từ <b>$txtNamBD</b> đến <b>$txtNamKT</b>";
										
										echo "<tr align='left' valign='top'>";
										echo "<td style=''>$thoigian</td>";
										echo "<td >$txtNoiCongTac</td>";
										echo "<td align=left >$txtChucVu</td>";
										echo "</tr>";
									}
								?>
							</tbody>
						</table>
					</td>
				</tr>
				
				<tr align="left">
					<td align=left><b><?php echo $z++ . "."; ?></b></td><td><b>Quá trình đào tạo:</b></td>
				</tr>
				<tr align="left">
					<td align=left colspan=2>
						<table width="100%" align="center" cellspacing="0" cellpadding="5" border=1 class="tableData fontcontent bordertable" height="20">
							<thead>
							  <tr style="font-weight:bold;">
								<td align=left style="width:100px"><em>Bậc đào tạo</em></td>
								<td align=left><em>Thời gian</em></td>
								<td align=left><em>Nơi đào tạo</em></td>
								<td align=left><em>Chuyên ngành</em></td>
								<td align=left><em>Tên luận án tốt nghiệp</em></td>
							  </tr>
							</thead>
							<tbody>
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
										echo "<tr align=left valign='top'>";
										echo "<td align=left>$txtTenBacDT</td>";
										echo "<td align=center >$txtNamBD-$txtNamTN</td>";
										echo "<td align=left>$txtNoiDT, $txtQuocGiaDT</td>";
										echo "<td align=left>$ten_nganh</td>";
										echo "<td align=left>$txtTenLALV</td>";
										echo "</tr>";
									}
								?>
							</tbody>
						</table>
					</td>
				</tr>
				
				<tr align="left">
					<td align=left><b><?php echo $z++ . "."; ?></b></td><td><b>Các lĩnh vực chuyên môn và hướng nghiên cứu:</b></td>
				</tr>
				<tr align="left">
					<td align=left></td><td><em><?php echo ($z-1).".1"; ?> Lĩnh vực chuyên môn:</em></td>
				</tr>
				<tr align="left">
					<td></td>
					<td align=left>
						<table width="100%" align="center" cellspacing="0" cellpadding="5" border=0 class="tableData fontcontent" >
							<tr >
								<td align=left style="width:120px; font-weight:bold;">- Lĩnh vực:</td>
								<td align=left>
									<?php 	$sqlstr="SELECT q.TEN_LVNC, q.MA_LVNC, l.LVNC_KHAC
											FROM NCKH_LVNC_KHCN_CBGD l, NCKH_LVNC_KHCN q
											WHERE l.FK_MA_CAN_BO = '$macb' and l.FK_MA_LVNC = q.MA_LVNC";
										$stmt = oci_parse($db_conn, $sqlstr);oci_execute($stmt);$n = oci_fetch_all($stmt, $resDM);oci_free_statement($stmt);
										$tmp = '';
										for ($i = 0; $i < $n; $i++)
										{
											$txtTenLVNC = $resDM["TEN_LVNC"][$i];
											if ($resDM["LVNC_KHAC"][$i] != '')
												$txtTenLVNC = $txtTenLVNC . ": " . $resDM["LVNC_KHAC"][$i];
											$tmp .= "+ $txtTenLVNC<br/>";
										}
										$tmp = substr($tmp, 0, -5);
										echo $tmp;
									?>
								</td>
							</tr>
							<tr  >
								<td align=left style="font-weight:bold;">- Chuyên ngành:</td>
								<td align=left><?php echo $cbgd["CHUYEN_NGANH"][$k]; ?></td>
							</tr>
							<tr >
								<td align=left style="font-weight:bold;">- Chuyên môn:</td>
								<td align=left><?php echo $cbgd["CHUYEN_MON"][$k];  ?></td>
							</tr>	
						</table>
					</td>
				</tr>
				<tr align="left">
					<td align=left></td><td><em><?php echo ($z-1).".2"; ?> Hướng nghiên cứu:</em></td>
				</tr>
				<tr align="left">
					<td align=left colspan=2>
						<div style="margin-left:40px">
						<table  width="100%" align="center" cellspacing="0" cellpadding="2" border=0 class="tableData fontcontent">
							<tbody>
								<?php 	$sqlstr="select ma_de_tai, ten_de_tai, nam from huong_de_tai where ma_can_bo ='$macb' order by nam desc, ten_de_tai";
									$stmt = oci_parse($db_conn, $sqlstr);oci_execute($stmt);$n = oci_fetch_all($stmt, $resDM);oci_free_statement($stmt);
									for ($i = 0; $i < $n; $i++)
									{	
										$txtTenDT 	= $resDM["TEN_DE_TAI"][$i];
										echo "<tr align=left valign='top'>";
										echo "<td align=left>".($i+1)."</td>";
										echo "<td align=left>$txtTenDT</td>";
										echo "</tr>";
									}
								?>
							</tbody>
						</table>
						</div>
					</td>
				</tr>
				
				<tr align="left">        
					<td ></td><td></td>
				</tr>
				<tr align="left" style="font-weight:bold;">        
					<td align=left style="width:15px">II.</td><td >NGHIÊN CỨU VÀ GIẢNG DẠY</td>
				</tr>
				<tr align="left">
					<td align=left><b><?php $z=1; echo $z++ . "."; ?></b></td><td><b>Đề tài/dự án</b></td>
				</tr>
				<tr align="left">
					<td align=left colspan=2>
						<div style="margin-left:0px">
						<table  width="100%" align="center" cellspacing="0" cellpadding="5" border=1 class="tableData fontcontent bordertable">
							<thead>
							  <tr style="font-weight:bold;">
								<td style="width:15px;" class="ui-corner-tl"><em>TT</em></td>
								<td  align=left><em>Tên đề tài/dự án</em></td>
								<td  align=center style="width:80px"><em>Mã số & <br/>cấp quản lý</em></td>
								<td  align=center ><em>Thời gian<br/>thực hiện</em></td>
								<td  align=center style="width:80px"><em>Kinh phí<br/><span style="font-weight:normal">(triệu đồng)</span></em></td>
								<td  align=center style="width:80px"><em>Chủ nhiệm<br/>/tham gia</em></td>
								<td  align=center style="width:80px"><em>Ngày<br/>nghiệm thu</em></td>
								<td  align=center><em>Kết quả</em></td>
							  </tr>
							</thead>
							<tbody>
								<?php
									$sqlstr="select a.*, DECODE(a.CHU_NHIEM,1,'Chủ nhiệm','Tham gia') THAM_GIA, DECODE(a.NGHIEM_THU,1,'x','') TT_NGHIEM_THU, DECODE(a.KET_QUA,'X','Xuất sắc', 'T', 'Tốt', 'K','Khá','B', 'Trung Bình') TT_KET_QUA, b.ten_cap
									from de_tai_nckh a, cap_de_tai b
									 where a.fk_cap_de_tai = b.ma_cap(+) and 
									 a.ma_can_bo = '$macb' order by a.nam_bat_dau desc"; 
									$stmt = oci_parse($db_conn, $sqlstr);oci_execute($stmt);$n = oci_fetch_all($stmt, $resDM);oci_free_statement($stmt);
									for ($i = 0; $i < $n; $i++)
									{
										if ($resDM["MA_SO_DE_TAI"][$i]!="")
											$masotencap = "{$resDM["MA_SO_DE_TAI"][$i]}/{$resDM["TEN_CAP"][$i]}";
										else 
											$masotencap = $resDM["TEN_CAP"][$i];
										if ($resDM["NAM_KET_THUC"][$i]!="")
											$thoigian = $resDM["NAM_BAT_DAU"][$i]."-".$resDM["NAM_KET_THUC"][$i];
										else
											$thoigian = $resDM["NAM_BAT_DAU"][$i];
										$kinhphi = $resDM["KINH_PHI"][$i]; $ketqua = $resDM["TT_KET_QUA"][$i];
										$thamgia = $resDM["THAM_GIA"][$i]; $tendt = $resDM["TEN_DE_TAI"][$i];
										$ngaynghiemthu = $resDM["NGAY_NGHIEM_THU"][$i];
										echo "<tr align='left' valign='top'>";
										echo "<td >".($i+1)."</td>";
										echo "<td >$tendt</td>";
										echo "<td align=center>$masotencap</td>";
										echo "<td align=center >$thoigian</td>";
										echo "<td align=center>$kinhphi</td>";
										echo "<td align=center>$thamgia</td>";
										echo "<td align=center>$ngaynghiemthu</td>";
										echo "<td align=center>$ketqua</td>";
										echo "</tr>";
									} 
								?>
							</tbody>
						</table>
						</div>
					</td>
				</tr>
				
				<tr align="left">
					<td align=left><b><?php echo $z++ . "."; ?></b></td><td><b>Hướng dẫn sinh viên, học viên cao học, nghiên cứu sinh</b></td>
				</tr>
				<tr align="left">
					<td align=left colspan=2>
						<div style="margin-left:0px">
						<table  width="100%" align="center" cellspacing="0" cellpadding="5" border=1 class="tableData fontcontent bordertable">
							<thead>
							  <tr style="font-weight:bold;">
								<td style="width:15px;" class="ui-corner-tl"><em>TT</em></td>
								<td  align=left style="width:150px;"><em>Tên SV, HVCH, NCS</em></td>
								<td  align=left><em>Tên luận án</em></td>
								<td  align=center style="width:50px;"><em>Năm tốt nghiệp</em></td>
								<td  align=center style="width:50px;"><em>Bậc đào tạo</em></td>
								<td  align=center style="width:100px;"><em>Sản phẩm của đề tài/dự án</em></td>
							  </tr>
							</thead>
							<tbody>
							 <?php
								$sqlstr="SELECT MA_HD_LUAN_AN, lower(HO_TEN_SV) ho_ten_sv, BAC_DAO_TAO, TEN_BAC, MA_HOC_VIEN, NAM_TOT_NGHIEP, SAN_PHAM_MA_DE_TAI, TEN_LUAN_AN
								FROM NCKH_HD_LUAN_AN h, BAC_DAO_TAO b
								WHERE FK_MA_CAN_BO='$macb' AND h.BAC_DAO_TAO = b.MA_BAC
								ORDER BY BAC_DAO_TAO, HO_TEN_SV"; 
								$stmt = oci_parse($db_conn, $sqlstr);oci_execute($stmt);$n = oci_fetch_all($stmt, $resDM);oci_free_statement($stmt);
								for ($i = 0; $i < $n; $i++)
								{	
									echo "<tr align=left >";
									echo "<td valign=top>" .($i+1).".</td>";
									echo "<td valign=top style='text-transform:capitalize;'>{$resDM["HO_TEN_SV"][$i]}</td>";
									echo "<td >{$resDM["TEN_LUAN_AN"][$i]}</td>";
									echo "<td align=center valign=top>{$resDM["NAM_TOT_NGHIEP"][$i]}</td>";
									echo "<td align=center valign=top>{$resDM["TEN_BAC"][$i]}</td>";
									echo "<td align=center valign=top>{$resDM["SAN_PHAM_MA_DE_TAI"][$i]}</td>";
									echo "</tr>";
								}
								
							?>
							
							
							</tbody>
						</table>
						</div>
					</td>
				</tr>
				
				<tr align="left">        
					<td ></td><td></td>
				</tr>
				<tr align="left" style="font-weight:bold">        
					<td align=left style="width:15px">III.</td><td>CÁC CÔNG TRÌNH ĐÃ CÔNG BỐ</td>
				</tr>
				<tr align="left">
					<td align=left><b><?php $z=1; echo $z++ . "."; ?></b></td><td><b>Sách</b></td>
				</tr>
				<tr align="left">
					<td align=left><i><?php echo ($z-1). ".1"; ?></i></td><td><i>Sách xuất bản Quốc tế</i></td>
				</tr>
				<tr align="left">
					<td align=left colspan=2>
						<div style="margin-left:0px">
							<table  width="100%" align="center" cellspacing="0" cellpadding="5" border=1 class="tableData fontcontent bordertable">
							 <thead>
							  <tr style="font-weight:bold">
								<td style="width:20px"><em>TT</em></td>
								<td ><em>Tên sách </em></td>
								<td align=center style="width:90px"><em>Sản phẩm của<br/>đề tài/dự án</em></td>
								<td align=left style="width:150px"><em>Nhà xuất bản</em></td>
								<td style="width:60px" align=center><em>Năm xuất bản</em></td>
								<td align=center style="width:80px"><em>Tác giả/<br/>Đồng tác giả</em></td>
								<td align=center style="width:150px"><em>Bút danh</em></td>
							  </tr>
							 </thead>
							 <tbody>
							 <?php
								 $sqlstr="select MA_SACH, TEN_SACH, NHA_XUAT_BAN, NAM_XUAT_BAN, DECODE(TAC_GIA,1,'tác giả','đồng tác giả') TAC_GIA_DESC,BUT_DANH, SAN_PHAM_MA_DE_TAI
										from sach where NUOC_NGOAI=1 and ma_can_bo = '$macb' order by nam_xuat_ban desc"; 
								$stmt = oci_parse($db_conn, $sqlstr);oci_execute($stmt);$n = oci_fetch_all($stmt, $resDM);oci_free_statement($stmt);
								for ($i = 0; $i < $n; $i++)
								{								
									$tensach = str_replace($search,$replace,$resDM["TEN_SACH"][$i]);
									$nxb = str_replace($search,$replace,$resDM["NHA_XUAT_BAN"][$i]);
									$namxuatban = str_replace($search,$replace,$resDM["NAM_XUAT_BAN"][$i]);
									$tacgiadesc = str_replace($search,$replace,$resDM["TAC_GIA_DESC"][$i]);
									$detaisp = str_replace($search,$replace,$resDM["SAN_PHAM_MA_DE_TAI"][$i]);
									$butdanh = str_replace($search,$replace,$resDM["BUT_DANH"][$i]);
									echo "<tr valign=top >";
									echo "<td align=left>" .($i+1).".</td>";
									echo "<td align=left>$tensach</td>";
									echo "<td align=center>$detaisp</td>";
									echo "<td align=left>$nxb</td>";
									echo "<td align=center>$namxuatban</td>";
									echo "<td align=center>$tacgiadesc</td>";
									echo "<td align=center>$butdanh</td>";
									echo "</tr>";
								}
							 ?>
							 </tbody>
							</table>
						</div>
					</td>
				</tr>
				
				<tr align="left">
					<td align=left><i><?php echo ($z-1). ".2"; ?></i></td><td><i>Sách xuất bản trong nước</i></td>
				</tr>
				<tr align="left">
					<td align=left colspan=2>
						<div style="margin-left:0px">
							<table  width="100%" align="center" cellspacing="0" cellpadding="5" border=1 class="tableData fontcontent bordertable">
							 <thead>
							  <tr style="font-weight:bold">
								<td style="width:20px"><em>TT</em></td>
								<td ><em>Tên sách </em></td>
								<td align=center style="width:90px"><em>Sản phẩm của<br/>đề tài/dự án</em></td>
								<td align=left style="width:150px"><em>Nhà xuất bản</em></td>
								<td style="width:60px" align=center><em>Năm xuất bản</em></td>
								<td align=center style="width:80px"><em>Tác giả/<br/>Đồng tác giả</em></td>
								<td align=center style="width:150px"><em>Bút danh</em></td>
							  </tr>
							 </thead>
							 <tbody>
							 <?php
								 $sqlstr="select MA_SACH, TEN_SACH, NHA_XUAT_BAN, NAM_XUAT_BAN, DECODE(TAC_GIA,1,'tác giả','đồng tác giả') TAC_GIA_DESC,BUT_DANH, SAN_PHAM_MA_DE_TAI
										from SACH where (NUOC_NGOAI=0 or NUOC_NGOAI is null) and MA_CAN_BO = '$macb' order by NAM_XUAT_BAN desc"; 
								$stmt = oci_parse($db_conn, $sqlstr);oci_execute($stmt);$n = oci_fetch_all($stmt, $resDM);oci_free_statement($stmt);
								
								for ($i = 0; $i < $n; $i++)
								{								
									echo "<tr valign=top >";
									echo "<td align=left>" .($i+1).".</td>";
									echo "<td align=left>".str_replace($search,$replace,$resDM["TEN_SACH"][$i])."</td>";
									echo "<td align=center>".str_replace($search,$replace,$resDM["SAN_PHAM_MA_DE_TAI"][$i])."</td>";
									echo "<td align=left>".str_replace($search,$replace,$resDM["NHA_XUAT_BAN"][$i])."</td>";
									echo "<td align=center>".str_replace($search,$replace,$resDM["NAM_XUAT_BAN"][$i])."</td>";
									echo "<td align=center>".str_replace($search,$replace,$resDM["TAC_GIA_DESC"][$i])."</td>";
									echo "<td align=center>".str_replace($search,$replace,$resDM["BUT_DANH"][$i])."</td>";
									echo "</tr>";
								}
							 ?>
							 </tbody>
							</table>
						</div>
					</td>
				</tr>
				
				<tr align="left">
					<td align=left><b><?php echo $z++ . "."; ?></b></td><td><b>Các bài báo</b></td>
				</tr>
				<tr align="left" >
					<td align=left><i><?php echo ($z-1). ".1"; ?></i></td><td><i>Đăng trên tạp chí Quốc tế</i></td>
				</tr>
				<tr align="left">
					<td align=left colspan=2>
						<div style="margin-left:0px">
							<table  width="100%" align="center" cellspacing="0" cellpadding="5" border=1 class="tableData fontcontent bordertable">
							 <thead>
							  <tr style="font-weight:bold">
								<td style="width:20px"><em>TT</em></td>
								<td ><em>Tên tác giả, tên bài viết, tên tạp chí và số của tạp chí, trang đăng bài viết, năm xuất bản</em></td>
								<td align=center style="width:90px"><em>Sản phẩm của<br/>đề tài/dự án</em></td>
								<td align=center style="width:150px"><em>Số hiệu ISSN <br/><span style="font-weight:normal">(ghi rõ thuộc ISI hay không)</span></em></td>
								<td style="width:50px" align=center><em>Điểm IF</em></td>
							  </tr>
							 </thead>
							 <tbody>
							 <?php
								 $sqlstr="select c.*, q.ten_quoc_gia
								from cong_trinh_khoa_hoc c, quoc_gia q
								where ma_can_bo = '$macb' and c.fk_quoc_gia = q.ma_quoc_gia(+) and c.loai_cong_trinh='BQ'
								order by c.loai_cong_trinh, c.nam_xuat_ban_tap_chi desc"; 
								$stmt = oci_parse($db_conn, $sqlstr);oci_execute($stmt);$n = oci_fetch_all($stmt, $resDM);oci_free_statement($stmt);
								
								for ($i = 0; $i < $n; $i++)
								{	
									$tenbaibao=str_replace($search,$replace,$resDM["TEN_BAI_BAO"][$i]);
									$tentacgia = str_replace($search,$replace,$resDM["TEN_TAC_GIA"][$i]);
									$madetai=str_replace($search,$replace,$resDM["SAN_PHAM_MA_DE_TAI"][$i]);
									$issnisbn = str_replace($search,$replace,$resDM["ISBN"][$i]);
									$isi = str_replace($search,$replace,$resDM["ISI"][$i]);
									$diemif = str_replace($search,$replace,$resDM["DIEM_IF"][$i]);
									$tentapchi=str_replace($search,$replace,$resDM["TEN_TAP_CHI"][$i]);
									$sotapchi=str_replace($search,$replace,$resDM["SO_TAP_CHI"][$i]);
									$trang=str_replace($search,$replace,$resDM["TRANG_DANG_BAI_BAO"][$i]);
									$namxb = str_replace($search,$replace,$resDM["NAM_XUAT_BAN_TAP_CHI"][$i]);
										
									$col1="$tentacgia, $tenbaibao, $tentapchi, $sotapchi, $trang, $namxb";
									$col2="$madetai";
									$col3="$issnisbn";
									if ($isi != "")
										$col3.=" thuộc ISI: $isi";
									$col4="$diemif";
									echo "<tr align=left valign=top >";				
									echo "<td  valign=top width=20>" .($i+1)."</td>";
									echo "<td  width=550>$col1</td>";
									echo "<td  align=center valign=top>$col2</td>";
									echo "<td  align=center valign=top>$col3</td>";
									echo "<td  align=center valign=top>$col4</td>";
									echo "</tr>";
								}
							 ?>
							 </tbody>
							</table>
						</div>
					</td>
				</tr>
				
				<tr align="left">
					<td align=left><i><?php echo ($z-1). ".2"; ?></i></td><td><i>Đăng trên tạp chí trong nước</i></td>
				</tr>
				<tr align="left">
					<td align=left colspan=2>
						<div style="margin-left:0px">
							<table  width="100%" align="center" cellspacing="0" cellpadding="5" border=1 class="tableData fontcontent bordertable">
							 <thead>
							  <tr style="font-weight:bold">
								<td style="width:20px"><em>TT</em></td>
								<td ><em>Tên tác giả, tên bài viết, tên tạp chí và số của tạp chí, trang đăng bài viết, năm xuất bản</em></td>
								<td align=center style="width:90px"><em>Sản phẩm của<br/>đề tài/dự án</em></td>
								<td align=center style="width:90px"><em>Số hiệu ISSN </em></td>
								<td style="width:50px" align=center><em>Điểm IF</em></td>
							  </tr>
							 </thead>
							 <tbody>
							 <?php
								 $sqlstr="select c.*, q.ten_quoc_gia
								from cong_trinh_khoa_hoc c, quoc_gia q
								where ma_can_bo = '$macb' and c.fk_quoc_gia = q.ma_quoc_gia(+) and c.loai_cong_trinh='BT'
								order by c.loai_cong_trinh, c.nam_xuat_ban_tap_chi desc"; 
								$stmt = oci_parse($db_conn, $sqlstr);oci_execute($stmt);$n = oci_fetch_all($stmt, $resDM);oci_free_statement($stmt);
								
								for ($i = 0; $i < $n; $i++)
								{	
									$tenbaibao=str_replace($search,$replace,$resDM["TEN_BAI_BAO"][$i]);
									$tentacgia = str_replace($search,$replace,$resDM["TEN_TAC_GIA"][$i]);
									$madetai=str_replace($search,$replace,$resDM["SAN_PHAM_MA_DE_TAI"][$i]);
									$issnisbn = str_replace($search,$replace,$resDM["ISBN"][$i]);
									$ghichu = str_replace($search,$replace,$resDM["GHI_CHU"][$i]);
									$tentapchi=str_replace($search,$replace,$resDM["TEN_TAP_CHI"][$i]);
									$sotapchi=str_replace($search,$replace,$resDM["SO_TAP_CHI"][$i]);
									$trang=str_replace($search,$replace,$resDM["TRANG_DANG_BAI_BAO"][$i]);
									$namxb = str_replace($search,$replace,$resDM["NAM_XUAT_BAN_TAP_CHI"][$i]);
										
									$col1="$tentacgia, $tenbaibao, $tentapchi, $sotapchi, $trang, $namxb";
									$col2="$madetai";$col3="$issnisbn";$col4="$ghichu";
									echo "<tr align=left valign=top >";				
									echo "<td  valign=top>" .($i+1)."</td>";
									echo "<td  width=550>$col1</td>";
									echo "<td  align=center valign=top>$col2</td>";
									echo "<td  align=center valign=top>$col3</td>";
									echo "<td  align=center valign=top>$col4</td>";
									echo "</tr>";
								}
							 ?>
							 </tbody>
							</table>
						</div>
					</td>
				</tr>
				
				<tr align="left">
					<td align=left><i><?php echo ($z-1). ".3"; ?></i></td><td><i>Đăng trên kỷ yếu Hội nghị Quốc tế</i></td>
				</tr>
				<tr align="left">
					<td align=left colspan=2>
						<div style="margin-left:0px">
							<table  width="100%" align="center" cellspacing="0" cellpadding="5" border=1 class="tableData fontcontent bordertable">
							 <thead>
							  <tr style="font-weight:bold">
								<td style="width:20px"><em>TT</em></td>
								<td align='left'><em>Tên tác giả, tên bài viết, tên Hội nghị, thời gian tổ chức, nơi tổ chức</em></td>
								<td align='center' style="width:90px"><em>Sản phẩm của<br/>đề tài/dự án</em></td>
								<td align='center' style="width:90px"><em>Số hiệu ISBN</em></td>
								<td align='center'><em>Ghi chú</em></td>
							  </tr>
							 </thead>
							 <tbody>
							 <?php
								 $sqlstr="select c.*, q.ten_quoc_gia
								from cong_trinh_khoa_hoc c, quoc_gia q
								where ma_can_bo = '$macb' and c.fk_quoc_gia = q.ma_quoc_gia(+) and c.loai_cong_trinh='HQ'
								order by c.loai_cong_trinh, c.nam_xuat_ban_tap_chi desc"; 
								$stmt = oci_parse($db_conn, $sqlstr);oci_execute($stmt);$n = oci_fetch_all($stmt, $resDM);oci_free_statement($stmt);
								
								for ($i = 0; $i < $n; $i++)
								{	
									$tenbaibao=str_replace($search,$replace,$resDM["TEN_BAI_BAO"][$i]);
									$tentacgia = str_replace($search,$replace,$resDM["TEN_TAC_GIA"][$i]);
									$madetai=str_replace($search,$replace,$resDM["SAN_PHAM_MA_DE_TAI"][$i]);
									$issnisbn = str_replace($search,$replace,$resDM["ISBN"][$i]);
									$ghichu = str_replace($search,$replace,$resDM["GHI_CHU"][$i]);
									$tentapchi=str_replace($search,$replace,$resDM["TEN_TAP_CHI"][$i]);
									$namxb = str_replace($search,$replace,$resDM["NAM_XUAT_BAN_TAP_CHI"][$i]);
									$thanhpho=str_replace($search,$replace,$resDM["CITY"][$i]);
									$tenquocgia=$resDM["TEN_QUOC_GIA"][$i];
									$col1="$tentacgia, $tenbaibao, $tentapchi, $namxb, $thanhpho - $tenquocgia";
									$col2="$madetai";$col3="$issnisbn";$col4="$ghichu";
									echo "<tr align=left valign=top >";				
									echo "<td  valign=top>" .($i+1)."</td>";
									echo "<td  width=550>$col1</td>";
									echo "<td  align=center valign=top>$col2</td>";
									echo "<td  align=center valign=top>$col3</td>";
									echo "<td  align=center valign=top>$col4</td>";
									echo "</tr>";
								}
							 ?>
							 </tbody>
							</table>
						</div>
					</td>
				</tr>
				
				<tr align="left">
					<td align=left><i><?php echo ($z-1). ".4"; ?></i></td><td><i>Đăng trên kỷ yếu Hội nghị trong nước</i></td>
				</tr>
				<tr align="left">
					<td align=left colspan=2>
						<div style="margin-left:0px">
							<table  width="100%" align="center" cellspacing="0" cellpadding="5" border=1 class="tableData fontcontent bordertable">
							 <thead>
							  <tr style="font-weight:bold">
								<td style="width:20px"><em>TT</em></td>
								<td align='left'><em>Tên tác giả, tên bài viết, tên Hội nghị, thời gian tổ chức, nơi tổ chức</em></td>
								<td align='center' style="width:90px"><em>Sản phẩm của<br/>đề tài/dự án</em></td>
								<td align='center' style="width:90px"><em>Số hiệu ISBN</em></td>
								<td align='center'><em>Ghi chú</em></td>
							  </tr>
							 </thead>
							 <tbody>
							 <?php
								 $sqlstr="select c.*, q.ten_quoc_gia
								from cong_trinh_khoa_hoc c, quoc_gia q
								where ma_can_bo = '$macb' and c.fk_quoc_gia = q.ma_quoc_gia(+) and c.loai_cong_trinh='HT'
								order by c.loai_cong_trinh, c.nam_xuat_ban_tap_chi desc"; 
								$stmt = oci_parse($db_conn, $sqlstr);oci_execute($stmt);$n = oci_fetch_all($stmt, $resDM);oci_free_statement($stmt);
								
								for ($i = 0; $i < $n; $i++)
								{	
									$tenbaibao=str_replace($search,$replace,$resDM["TEN_BAI_BAO"][$i]);
									$tentacgia = str_replace($search,$replace,$resDM["TEN_TAC_GIA"][$i]);
									$madetai=str_replace($search,$replace,$resDM["SAN_PHAM_MA_DE_TAI"][$i]);
									$issnisbn = str_replace($search,$replace,$resDM["ISBN"][$i]);
									$ghichu = str_replace($search,$replace,$resDM["GHI_CHU"][$i]);
									$tentapchi=str_replace($search,$replace,$resDM["TEN_TAP_CHI"][$i]);
									$namxb = str_replace($search,$replace,$resDM["NAM_XUAT_BAN_TAP_CHI"][$i]);
									$thanhpho=str_replace($search,$replace,$resDM["CITY"][$i]);
									$tenquocgia=$resDM["TEN_QUOC_GIA"][$i];
									$col1="$tentacgia, $tenbaibao, $tentapchi, $namxb, $thanhpho - $tenquocgia";
									$col2="$madetai";$col3="$issnisbn";	$col4="$ghichu";
									echo "<tr align=left valign=top >";				
									echo "<td  valign=top>" .($i+1)."</td>";
									echo "<td  width=550>$col1</td>";
									echo "<td  align=center valign=top>$col2</td>";
									echo "<td  align=center valign=top>$col3</td>";
									echo "<td  align=center valign=top>$col4</td>";
									echo "</tr>";
								}
							 ?>
							 </tbody>
							</table>
						</div>
					</td>
				</tr>
				
				<tr align="left"><td ></td><td></td></tr>
				<tr align="left" style="font-weight:bold;">        
					<td align=left style="width:15px">IV.</td><td >CÁC GIẢI THƯỞNG</td>
				</tr>
				<tr align="left">
					<td align=left><b><?php $z=1; echo $z++ . "."; ?></b></td><td><b>Các giải thưởng Khoa học và Công nghệ</b></td>
				</tr>
				<tr align="left">
					<td align=left colspan=2>
						<div style="margin-left:0px">
							<table  width="100%" align="center" cellspacing="0" cellpadding="5" border=1 class="tableData fontcontent bordertable">
								 <thead>
								  <tr style="font-weight:bold">
									<td align="left" style="width:20px"><em>TT</em></td>
									<td align="left"><em>Tên giải thưởng</em></td>
									<td align="left"><em>Nội dung giải thưởng</em></td>
									<td align="left"><em>Nơi cấp</em></td>
									<td align="left" style="width:60px"><em>Năm cấp</em></td>
								  </tr>
								 </thead>
								 <tbody>
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
										echo "<td style=''>".($i+1)."</td>";
										echo "<td align=left >$txtTenGiaiThuong</td>";
										echo "<td align=left >$txtNoiDungGiaiThuong</td>";
										echo "<td align=left>$txtNoiCap, $txtTenNuocCap</td>";
										echo "<td align=left>$txtNamCap </td>";
										echo "</tr>";
									}
								 ?>
								 </tbody>
							</table>
						</div>
					</td>
				</tr>
				
				<tr align="left">
					<td align=left><b><?php echo $z++ . "."; ?></b></td><td><b>Bằng phát minh, sáng chế (patent)</b></td>
				</tr>
				<tr align="left">
					<td align=left colspan=2>
						<div style="margin-left:0px">
							<table  width="100%" align="center" cellspacing="0" cellpadding="5" border=1 class="tableData fontcontent bordertable">
								 <thead>
								  <tr style="font-weight:bold">
									<td align="left" style="width:20px"><em>TT</em></td>
									<td align="left"><em>Tên bằng</em></td>
									<td align="left" style="width:100px"><em>Sản phẩm của<br/>đề tài/dự án</em></td>
									<td align="left"><em>Số hiệu</em></td>
									<td align="left" style="width:60px"><em>Năm cấp</em></td>									
									<td align="left"><em>Nơi cấp</em></td>
									<td align="left" style="width:80px"><em>Tác giả/<br/>đồng tác giả</em></td>
								  </tr>
								 </thead>
								 <tbody>
								 <?php
									$sqlstr="SELECT n.MA_BANG_SANG_CHE, c.TEN_QUOC_GIA TEN_NUOC_CAP, n.NAM_CAP, n.NOI_CAP, 
											n.NUOC_CAP, n.TEN_BANG, n.FK_MA_CAN_BO, n.SAN_PHAM_MA_DE_TAI, n.SO_HIEU_BANG,
											decode(n.TAC_GIA, '1','tác giả chính', 'đồng tác giả') Tac_gia_chinh
											FROM NCKH_BANG_SANG_CHE n, QUOC_GIA c 
											WHERE n.NUOC_CAP=c.MA_QUOC_GIA (+)
											AND FK_MA_CAN_BO='$macb'
											ORDER BY n.NAM_CAP desc";
									$stmt = oci_parse($db_conn, $sqlstr);oci_execute($stmt);$n = oci_fetch_all($stmt, $resDM);oci_free_statement($stmt);
									for ($i = 0; $i < $n; $i++)
									{
										$txtTenNuocCap 			= $resDM["TEN_NUOC_CAP"][$i];
										$txtNamCap 				= $resDM["NAM_CAP"][$i];
										$txtTacGiaChinh			= $resDM["TAC_GIA_CHINH"][$i];
										$txtSoHieuBang			= str_replace($search,$replace,$resDM["SO_HIEU_BANG"][$i]);
										$txtSpMaDeTai			= str_replace($search,$replace,$resDM["SAN_PHAM_MA_DE_TAI"][$i]);
										$txtTenBang				= str_replace($search,$replace,$resDM["TEN_BANG"][$i]);
										$txtNoiCap				= str_replace($search,$replace,$resDM["NOI_CAP"][$i]);

										echo "<tr align=left valign='top'>";
										echo "<td style=''>".($i+1)."</td>";
										echo "<td align=left >$txtTenBang</td>";
										echo "<td align=left >$txtSpMaDeTai</td>";
										echo "<td align=left>$txtSoHieuBang</td>";
										echo "<td align=left>$txtNamCap </td>";
										echo "<td align=left>$txtNoiCap, $txtTenNuocCap</td>";
										echo "<td align=left>$txtTacGiaChinh </td>";
										echo "</tr>";
									}
								 ?>
								 </tbody>
							</table>
						</div>
					</td>
				</tr>
				
				<tr align="left">
					<td align=left><b><?php echo $z++ . "."; ?></b></td><td><b>Bằng giải pháp hữu ích</b></td>
				</tr>
				<tr align="left">
					<td align=left colspan=2>
						<div style="margin-left:0px">
							<table  width="100%" align="center" cellspacing="0" cellpadding="5" border=1 class="tableData fontcontent bordertable">
								 <thead>
								  <tr style="font-weight:bold">
									<td align="left" style="width:20px"><em>TT</em></td>
									<td align="left"><em>Tên giải pháp</em></td>
									<td align="left" style="width:100px"><em>Sản phẩm của<br/>đề tài/dự án</em></td>
									<td align="left"><em>Số hiệu</em></td>
									<td align="left" style="width:60px"><em>Năm cấp</em></td>									
									<td align="left"><em>Nơi cấp</em></td>
									<td align="left" style="width:80px"><em>Tác giả/<br/>đồng tác giả</em></td>
								  </tr>
								 </thead>
								 <tbody>
								 <?php
									$sqlstr="SELECT n.MA_BANG_GP_HUU_ICH, c.TEN_QUOC_GIA TEN_NUOC_CAP, n.NAM_CAP, n.NOI_CAP, 
											n.NUOC_CAP, n.TEN_BANG, n.FK_MA_CAN_BO, n.SAN_PHAM_MA_DE_TAI, n.SO_HIEU_BANG,
											decode(n.TAC_GIA, '1','tác giả chính', 'đồng tác giả') Tac_gia_chinh, n.TAC_GIA 
											FROM NCKH_BANG_GP_HUU_ICH n, QUOC_GIA c 
											WHERE n.NUOC_CAP=c.MA_QUOC_GIA (+)
											AND FK_MA_CAN_BO='$macb'
											ORDER BY n.NAM_CAP desc";
									$stmt = oci_parse($db_conn, $sqlstr);oci_execute($stmt);$n = oci_fetch_all($stmt, $resDM);oci_free_statement($stmt);
									for ($i = 0; $i < $n; $i++)
									{
										$txtTenNuocCap 			= $resDM["TEN_NUOC_CAP"][$i];
										$txtNamCap 				= $resDM["NAM_CAP"][$i];
										$txtTacGiaChinh			= $resDM["TAC_GIA_CHINH"][$i];
										$txtSoHieuBang			= str_replace($search,$replace,$resDM["SO_HIEU_BANG"][$i]);
										$txtSpMaDeTai			= str_replace($search,$replace,$resDM["SAN_PHAM_MA_DE_TAI"][$i]);
										$txtTenBang				= str_replace($search,$replace,$resDM["TEN_BANG"][$i]);
										$txtNoiCap				= str_replace($search,$replace,$resDM["NOI_CAP"][$i]);

										echo "<tr align=left valign='top'>";
										echo "<td style=''>".($i+1)."</td>";
										echo "<td align=left >$txtTenBang</td>";
										echo "<td align=left >$txtSpMaDeTai</td>";
										echo "<td align=left>$txtSoHieuBang</td>";
										echo "<td align=left>$txtNamCap </td>";
										echo "<td align=left>$txtNoiCap, $txtTenNuocCap</td>";
										echo "<td align=left>$txtTacGiaChinh </td>";
										echo "</tr>";
									}
								 ?>
								 </tbody>
							</table>
						</div>
					</td>
				</tr>
				
				<tr align="left">
					<td align=left><b><?php echo $z++ . "."; ?></b></td><td><b>Ứng dụng thực tiễn và thương mại hóa kết quả nghiên cứu</b></td>
				</tr>
				<tr align="left">
					<td align=left colspan=2>
						<div style="margin-left:0px">
							<table  width="100%" align="center" cellspacing="0" cellpadding="5" border=1 class="tableData fontcontent bordertable">
								 <thead>
								  <tr style="font-weight:bold">
									<td align="left" style="width:20px"><em>TT</em></td>
									<td align="left"><em>Tên công nghệ/giải pháp hữu ích đã chuyển giao</em></td>
									<td align="left"><em>Hình thức, quy mô, địa chỉ áp dụng</em></td>
									<td align="center" style="width:80px"><em>Năm<br/>chuyển giao</em></td>
									<td align="center" style="width:100px"><em>Sản phẩm của<br/>đề tài/dự án</em></td>
								  </tr>
								 </thead>
								 <tbody>
								 <?php
									$sqlstr="SELECT MA_UD_THUC_TIEN, TEN_CONG_NGHE_GP_HU , HINH_THUC, 
											QUY_MO, DIA_CHI_AP_DUNG, FK_MA_CAN_BO, THOI_GIAN_CG, SAN_PHAM_MA_DE_TAI
											FROM NCKH_UD_THUC_TIEN
											WHERE FK_MA_CAN_BO='$macb'
											";
									$stmt = oci_parse($db_conn, $sqlstr);oci_execute($stmt);$n = oci_fetch_all($stmt, $resDM);oci_free_statement($stmt);
									for ($i = 0; $i < $n; $i++)
									{
										$txtTenCongNghe		= str_replace($search,$replace,$resDM["TEN_CONG_NGHE_GP_HU"][$i]);
										$txtHinhThuc		= str_replace($search,$replace,$resDM["HINH_THUC"][$i]);
										$txtQuyMo			= str_replace($search,$replace,$resDM["QUY_MO"][$i]);
										$txtDiaChi			= str_replace($search,$replace,$resDM["DIA_CHI_AP_DUNG"][$i]);
										$txtThoiGian		= str_replace($search,$replace,$resDM["THOI_GIAN_CG"][$i]);
										$txtSpMaDeTai		= str_replace($search,$replace,$resDM["SAN_PHAM_MA_DE_TAI"][$i]);									
										$txthtqmdc = "";
										if ($txtHinhThuc!="")
											$txthtqmdc .= "$txtHinhThuc, ";
										if ($txtQuyMo!="")
											$txthtqmdc .= "$txtQuyMo, ";
										if ($txtDiaChi!="")
											$txthtqmdc .= "$txtDiaChi, ";
										$txthtqmdc=substr($txthtqmdc,0,-2);										
										echo "<tr align=left valign=top>";
										echo "<td style=''>".($i+1)."</td>";
										echo "<td align=left >$txtTenCongNghe</td>";
										echo "<td align=left >$txthtqmdc</td>";
										echo "<td align=center>$txtThoiGian</td>";
										echo "<td align=left>$txtSpMaDeTai</td>";
										echo "</tr>";
									}
								 ?>
								 </tbody>
							</table>
						</div>
					</td>
				</tr>
				
				<tr align="left"><td ></td><td></td></tr>
				<tr align="left" style="font-weight:bold">        
					<td align=left style="width:15px">V.</td><td>THÔNG TIN KHÁC</td>
				</tr>
				<tr align="left">
					<td align=left><b><?php $z=1; echo $z++ . "."; ?></b></td><td><b>Tham gia các chương trình trong và ngoài nước</b></td>
				</tr>
				<tr align="left">
					<td align=left colspan=2>
						<div style="margin-left:0px">
							<table  width="100%" align="center" cellspacing="0" cellpadding="5" border=1 class="tableData fontcontent bordertable">
								 <thead>
								  <tr style="font-weight:bold">
									<td align="left" style="width:20px"><em>TT</em></td>
									<td align="left" style="width:80px"><em>Thời gian</em></td>
									<td align="left"><em>Tên chương trình</em></td>
									<td align="left"><em>Chức danh</em></td>
								  </tr>
								 </thead>
								 <tbody>
								 <?php
									$sqlstr="SELECT FK_MA_CAN_BO,MA_TG_CHUONG_TRINH,TEN_CHUONG_TRINH,CHUC_DANH,THOI_GIAN_BD,THOI_GIAN_KT,NUOC_NGOAI, decode(NUOC_NGOAI, '1','ngoài nước', '0','trong nước') nuoc_ngoai_desc
											FROM NCKH_THAM_GIA_CHUONG_TRINH n
											WHERE FK_MA_CAN_BO='$macb'
											ORDER BY THOI_GIAN_BD desc";
									$stmt = oci_parse($db_conn, $sqlstr);oci_execute($stmt);$n = oci_fetch_all($stmt, $resDM);oci_free_statement($stmt);
									for ($i = 0; $i < $n; $i++)
									{
										$txtMaTG 			= $resDM["MA_TG_CHUONG_TRINH"][$i];
										$txtThoiGianKT		= str_replace($search,$replace,$resDM["THOI_GIAN_KT"][$i]);
										$txtThoiGianBD	 	= str_replace($search,$replace,$resDM["THOI_GIAN_BD"][$i]);
										$txtNuocNgoai		= $resDM["NUOC_NGOAI"][$i];
										$txtNuocNgoaiDesc 	= $resDM["NUOC_NGOAI_DESC"][$i];
										$txtTenCT			= str_replace($search,$replace,$resDM["TEN_CHUONG_TRINH"][$i]);
										$txtChucDanh		= str_replace($search,$replace,$resDM["CHUC_DANH"][$i]);
										if ($txtThoiGianKT!="")
											$txtThoiGian = $txtThoiGianBD . "-" . $txtThoiGianKT;
										else
											$txtThoiGian = $txtThoiGianBD;
										echo "<tr align=left valign=top>";
										echo "<td style=''>".($i+1)."</td>";
										echo "<td align=left >$txtThoiGian</td>";
										echo "<td align=left >$txtTenCT</td>";
										echo "<td align=left>$txtChucDanh</td>";
										echo "</tr>";
									}
								 ?>
								 </tbody>
							</table>
						</div>
					</td>
				</tr>
				
				<tr align="left">
					<td align=left><b><?php echo $z++ . "."; ?></b></td><td><b>Tham gia các Hiệp hội khoa học, Ban biên tập các tạp chí Khoa học, Ban tổ chức các Hội nghị về KH&CN </b></td>
				</tr>
				<tr align="left">
					<td align=left colspan=2>
						<div style="margin-left:0px">
							<table  width="100%" align="center" cellspacing="0" cellpadding="5" border=1 class="tableData fontcontent bordertable">
								 <thead>
								  <tr style="font-weight:bold">
									<td align="left" style="width:20px"><em>TT</em></td>
									<td align="left" style="width:80px"><em>Thời gian</em></td>
									<td align="left"><em>Tên Hiệp hội/Tạp chí/Hội nghị</em></td>
									<td align="left"><em>Chức danh</em></td>
								  </tr>
								 </thead>
								 <tbody>
								 <?php
									$sqlstr="SELECT FK_MA_CAN_BO,MA_TG_HH_TC_HN,TEN_HH_TC_HN,CHUC_DANH,THOI_GIAN_BD,THOI_GIAN_KT, decode(LOAI, 'H','Hiệp hội khoa học', 'T','Tạp chí khoa học', 'HN','Hội nghị khoa học công nghệ') loai_desc, loai
											FROM NCKH_THAM_GIA_HH_TC_HN n
											WHERE FK_MA_CAN_BO='$macb'
											ORDER BY THOI_GIAN_BD desc";
									$stmt = oci_parse($db_conn, $sqlstr);oci_execute($stmt);$n = oci_fetch_all($stmt, $resDM);oci_free_statement($stmt);
									for ($i = 0; $i < $n; $i++)
									{
										$txtTenHH			= str_replace($search,$replace,$resDM["TEN_HH_TC_HN"][$i]);
										$txtThoiGianKT		= str_replace($search,$replace,$resDM["THOI_GIAN_KT"][$i]);
										$txtThoiGianBD	 	= str_replace($search,$replace,$resDM["THOI_GIAN_BD"][$i]);
										$txtLoai			= str_replace($search,$replace,$resDM["LOAI"][$i]);
										$txtTenLoai			= str_replace($search,$replace,$resDM["LOAI_DESC"][$i]);
										$txtChucDanh		= str_replace($search,$replace,$resDM["CHUC_DANH"][$i]);
										if ($txtThoiGianKT!="")
											$txtThoiGian = $txtThoiGianBD . "-" . $txtThoiGianKT;
										else
											$txtThoiGian = $txtThoiGianBD;
										echo "<tr align='left' valign='top'>";
										echo "<td style=''>".($i+1)."</td>";
										echo "<td align=left >$txtThoiGian</td>";
										echo "<td align=left >$txtTenHH ($txtTenLoai)</td>";									
										echo "<td align=left>$txtChucDanh</td>";
										echo "</tr>";
									}
								 ?>
								 </tbody>
							</table>
						</div>
					</td>
				</tr>
				
				<tr align="left">
					<td align=left><b><?php echo $z++ . "."; ?></b></td><td><b>Tham gia làm việc tại Trường Đại học/Viện/Trung tâm nghiên cứu theo lời mời</b></td>
				</tr>
				<tr align="left">
					<td align=left colspan=2>
						<div style="margin-left:0px">
							<table  width="100%" align="center" cellspacing="0" cellpadding="5" border=1 class="tableData fontcontent bordertable">
								 <thead>
								  <tr style="font-weight:bold">
									<td align="left" style="width:20px"><em>TT</em></td>
									<td align="left" style="width:80px"><em>Thời gian</em></td>
									<td align="left"><em>Tên Trường Đại học/Viện/Trung tâm nghiên cứu</em></td>
									<td align="left"><em>Nội dung tham gia</em></td>
								  </tr>
								 </thead>
								 <tbody>
								 <?php
									$sqlstr="SELECT FK_MA_CAN_BO,MA_TG_TRUONG_VIEN,TEN_TRUONG_VIEN,NOI_DUNG_THAM_GIA,THOI_GIAN_BD,THOI_GIAN_KT
											FROM NCKH_THAM_GIA_TRUONG_VIEN n
											WHERE FK_MA_CAN_BO='$macb'
											ORDER BY THOI_GIAN_BD desc";
									$stmt = oci_parse($db_conn, $sqlstr);oci_execute($stmt);$n = oci_fetch_all($stmt, $resDM);oci_free_statement($stmt);
									for ($i = 0; $i < $n; $i++)
									{
										$txtThoiGianKT		= str_replace($search,$replace,$resDM["THOI_GIAN_KT"][$i]);
										$txtThoiGianBD	 	= str_replace($search,$replace,$resDM["THOI_GIAN_BD"][$i]);
										
										$txtTenTruongVien	= str_replace($search,$replace,$resDM["TEN_TRUONG_VIEN"][$i]);
										$txtNoidung			= str_replace($search,$replace,$resDM["NOI_DUNG_THAM_GIA"][$i]);
										if ($txtThoiGianKT!="")
											$txtThoiGian = $txtThoiGianBD . "-" . $txtThoiGianKT;
										else
											$txtThoiGian = $txtThoiGianBD;
										echo "<tr align='left' valign='top'>";
										echo "<td style=''>".($i+1)."</td>";
										echo "<td align=left >$txtThoiGian</td>";
										echo "<td align=left >$txtTenTruongVien</td>";
										echo "<td align=left>$txtNoidung</td>";
										echo "</tr>";
									}
								 ?>
								 </tbody>
							</table>
						</div>
					</td>
				</tr>
				
				<tr>
					<td colspan=2 align=right>
						<table width=100% class=fontcontent>
							<tr>
								<td align=left valign=top width=50% >
									<div style="width:300px; margin-top:20px" align=center>
										
									</div>
								</td>
								<td align=right width=50%>
									<div style="width:400px; margin-top:20px" align=center>
										<span><em>Tp.HCM, ngày <?php echo $ngay ?> tháng <?php echo $thang ?> năm <?php echo $nam ?></em></span><br/>
										<b>Người khai</b><br/>
										<i>(Họ tên và chữ ký)</i>
										<br/><br/><br/><br/><br/><br/>
										<b><?php echo $cbgd["HOTENCB"][$k]; ?></b>
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
	 var day = new Date();
	 var id= day.getTime();
	 document.getElementById("framehinh46_bmr04").src='<?php echo $filehinh; ?>' + '?'+id;
	</script>
<?php
if ($a != 'get_llkh')
{
?>	
</div>
  
<script type="text/javascript">
$(function(){
 
 $( "#print_ttgv_r004_btn_printpreview" ).button({ icons: {primary:'ui-icon ui-icon-print'} });
 $( "#print_ttgv_r004_btn_printpreview" ).click(function(){
	print_llkh_writeConsole($("#chitietttgv_llkh_mau_r004").html(), 0);
 });
 
});
</script>
<?php 
}
?>
<?php
if ($b == 'export_htm')
{
?>
  </body>
  </html>
<?php
	$usr = base64_decode($_SESSION["uidloginPortal"]);
	$uploaddir = "users/$usr/tmdt_llkh";
	if (!mkdir('../khcn/'.$uploaddir, 0, true)) {	
		//echo '../khcn/'.$uploaddir;
	}
	$filename = "../khcn/$uploaddir/$c"."_".$cbgd["MA_CAN_BO"][$k].".htm";

	//save buffer in a file
	$buffer = ob_get_flush();
	file_put_contents($filename, $buffer);
}

} // End for (lap lai theo 1 danh sach ma can bo phan bien)
?>
<?php
if (isset ($db_conn))
	oci_close($db_conn);
?>