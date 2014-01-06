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

if (!allowPermisstion(base64_decode($_SESSION['uidloginPortal']), '002', $db_conn))
{
	$macb = '';
}

if ($macb == '') 
	$macb = $_SESSION['macb'];

//file_put_contents("ds_llkh_da_in.txt", date("H:i:s d.m.Y")." $macb\n", FILE_APPEND);
	
$sqlstr="select cb.*, to_char(cb.NGAY_SINH,'dd-mm-yyyy') NGAY_SINH, decode(PHAI, 'M', 'Nam', 'F', 'Nữ') GIOI_TINH, k.ten_khoa, bm.ten_bo_mon,
		v.ten_chuc_vu, bmql.ten_bo_mon ten_bo_mon_ql, qghv.ten_quoc_gia ten_nuoc_hv, hv.TEN ten_hv, cb.CHUYEN_MON_BC_BO_GDDT, GET_THANH_VIEN(cb.ma_can_bo) HOTENCB
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
?>

<?php
if ($a != 'get_llkh')
{
?>
<a id="print_ttgv_llkh_btn_printpreview">&nbsp;In ...</a>
<div align="center" style="margin-top:10px; font-family:Arial,Helvetica,sans-serif;" id="chitiet_LLKH_Bo">
<?php
}
?>
    <table width="100%"   cellspacing="0" cellpadding="0" class="ui-corner-all shawdow tableData" >
      <tr>
        <td valign='top'> 
		<div align="center" style="margin-top:10px">ĐẠI HỌC QUỐC GIA TP.HCM<br/><b>TRƯỜNG ĐẠI HỌC BÁCH KHOA</b><br/></div>
        </td>
		<td valign='top'> 
		<div align="center"  style="margin-top:10px" ><b>CỘNG HÒA XÃ HỘI CHỦ NGHĨA VIỆT NAM<br/>Độc lập - Tự do - Hạnh phúc</b><br/></div>
        </td>
      </tr>
	  <tr>
        <td colspan=2 valign='top'> 
		<div align="center"  style="margin-top:20px; margin-bottom:20px"><b><font style="font-size:140%; font-weight:bold;">LÝ LỊCH KHOA HỌC</font></b><!--<br/>(Kèm theo Thông tư số: 38/2010/TT-BGDĐT ngày 22 tháng 12 năm 2010 của Bộ trưởng Bộ Giáo dục và Đào tạo)--></div>
		<hr>
        </td>
      </tr>
      <tr>
        <td colspan=2>
      
			<table width="100%" border="0" align="center" cellpadding="5" cellspacing="0" class="fontcontent" style="font-family:Arial,Helvetica,sans-serif;">
				
				<tr align="left">        
					<td  style="width:0px;"></td><td align=left style="font-weight:bold;">I. LÝ LỊCH SƠ LƯỢC</td><td  style=""></td>
				</tr>
				<tr align="left">        
					<td  style=""></td><td align=left style="">Họ và tên: <?php echo $cbgd["HO"][0]. " " .$cbgd["TEN"][0]; ?></td><td  style="">Giới tính: <?php echo $cbgd["GIOI_TINH"][0]; ?></td>
				</tr>
				<tr align="left">
					<td  style=""></td><td align=left>Ngày, tháng, năm sinh: <?php echo $cbgd["NGAY_SINH"][0]; ?></td>
					<td>Nơi sinh: <?php echo $cbgd["NOI_SINH"][0]; ?></td>
				</tr>
				<tr align="left">
					<td  style=""></td><td colspan=2 align=left>Quê quán: <?php echo $cbgd["QUE_QUAN"][0]; ?></td>
					
				</tr>
				
				<tr align="left">
					<td  style=""></td><td align=left>Học vị cao nhất: <?php echo $cbgd["TEN_HV"][0];?>
					</td>
					<td style="width:350px;">Năm, nước nhận học vị: <?php echo "{$cbgd["NAM_DAT_HOC_VI"][0]}, {$cbgd["TEN_NUOC_HV"][0]}"; ?></td>
				</tr>
				
				<tr align="left">
					<td  style=""></td>
					<td align=left colspan=2>Chức danh khoa học cao nhất: <?php  switch ($cbgd["MA_HOC_HAM"][0])
																					{
																						case "GS": echo "Giáo sư"; break;
																						case "PGS": echo "Phó giáo sư"; break;
																						default: echo "";
																					} 
																			?>
																<?php 
							if ($cbgd["MA_HOC_HAM"][0]=='GS' || $cbgd["MA_HOC_HAM"][0]=='PGS' ) 
								echo ", Năm bổ nhiệm: {$cbgd["NAM_PHONG_HOC_HAM"][0]}";
						?>
					</td>
					
				</tr>
				
				<tr align="left">
					<td align=left></td><td colspan=2>Chức vụ: <?php if ($cbgd["TEN_CHUC_VU"][0]!='') echo $cbgd["TEN_CHUC_VU"][0]. " " .$cbgd["TEN_BO_MON_QL"][0];  ?></td>
				</tr>
				
				<tr align="left">
					<td align=left></td><td colspan=2>Đơn vị công tác: <?php echo "{$cbgd["CO_QUAN_CONG_TAC"][0]}";  ?></td>
				</tr>
				
				<tr align="left">
					<td align=left></td><td colspan=2>Địa chỉ riêng: <?php echo "{$cbgd["DIA_CHI_RIENG"][0]}";  ?></td>
				</tr>
				
				<tr align="left">
					<td align=left></td><td colspan=2>Điện thoại liên hệ: <?php echo "{$cbgd["DIEN_THOAI"][0]} - {$cbgd["DIEN_THOAI_CN"][0]}";  ?></td>
				</tr>
				
				<tr align="left">
					<td align=left></td><td colspan=2>Email: <?php echo "{$cbgd["EMAIL"][0]} - {$cbgd["EMAIL_2"][0]}";  ?></td>
				</tr>
				
				<!-- Qua trinh dao tao -->
				<tr align="left">        
					<td  style="width:10px;"></td><td colspan=2 align=left style="font-weight:bold;"><div style="margin-top:10px;">II. QUÁ TRÌNH ĐÀO TẠO</div></td>
				</tr>
				<tr align='left'>        
					<td  style='width:10px;'></td><td align=left style='font-weight:bold;'>1. Đại học:</td><td style=''></td>
				</tr>
				<?php 
					$sqlstr="SELECT q.*, b.TEN_BAC, n.TEN_NGANH, g.TEN_QUOC_GIA, g.TEN_TIENG_VIET, hdt.ten_he_dao_tao
					FROM NCKH_QUA_TRINH_DAO_TAO q, bac_dao_tao b, nckh_nganh_dt n, quoc_gia g, dm_he_dao_tao hdt
					WHERE FK_MA_CAN_BO = '".$macb. "' and q.BAC_DAO_TAO = b.MA_BAC (+) and q.FK_NGANH = n.MA_NGANH (+)
					and q.QG_DAT_HOC_VI = g.MA_QUOC_GIA(+) and q.fk_he_dao_tao = hdt.ma_he_dao_tao (+)
					and q.BAC_DAO_TAO = 'DH'
					ORDER BY THOI_GIAN_TN DESC"; 
					$stmt = oci_parse($db_conn, $sqlstr);oci_execute($stmt);$n = oci_fetch_all($stmt, $resDM);oci_free_statement($stmt);
					$classAlt="alt";

					for ($i = 0; $i < $n; $i++)
					{
						($classAlt=="alt") ? $classAlt="alt_" : $classAlt="alt";
						
						if ($resDM["FK_NGANH"][$i]=="99999999")
							$ten_nganh = $resDM["NGANH_KHAC"][$i];
						else
							$ten_nganh = $resDM["TEN_NGANH"][$i];

						$txtBacDT 		= $resDM["BAC_DAO_TAO"][$i];
						$txtHeDT 		= $resDM["FK_HE_DAO_TAO"][$i];
						$txtTenBacDT 	= $resDM["TEN_BAC"][$i];
						$txtNamBD 		= $resDM["THOI_GIAN_BD"][$i];
						$txtNamTN 		= $resDM["THOI_GIAN_TN"][$i];
						$txtTenHDT 		= $resDM["TEN_HE_DAO_TAO"][$i];
						$txtNganh 		= $resDM["FK_NGANH"][$i];
						$txtNoiDT 		= $resDM["NOI_DAO_TAO"][$i];
						$txtQuocGiaDT 	= $resDM["TEN_TIENG_VIET"][$i];
						$txtMaQuocGiaDT = $resDM["QG_DAT_HOC_VI"][$i];
						$txtTenLALV 	= $resDM["TEN_LUAN_AN"][$i];
						
						echo "
						<tr align='left'>        
							<td  style='width:10px;'></td>
							<td colspan=2 align=left style=''>
							Hệ đào tạo: $txtTenHDT <br/>
							Nơi đào tạo: $txtNoiDT <br/>
							Ngành học: $ten_nganh <br/>
							Nước đào tạo: $txtQuocGiaDT - Năm tốt nghiệp: $txtNamTN
							</td>
						</tr>";
					}
				?>
				<tr align='left'>        
					<td  style='width:10px;'></td><td align=left style='font-weight:bold;'>2. Sau đại học:</td><td style=''></td>
				</tr>
				<?php 
					$sqlstr="SELECT q.*, b.TEN_BAC, n.TEN_NGANH, g.TEN_QUOC_GIA, g.TEN_TIENG_VIET, hdt.ten_he_dao_tao
					FROM NCKH_QUA_TRINH_DAO_TAO q, bac_dao_tao b, nckh_nganh_dt n, quoc_gia g, dm_he_dao_tao hdt
					WHERE FK_MA_CAN_BO = '".$macb. "' and q.BAC_DAO_TAO = b.MA_BAC (+) and q.FK_NGANH = n.MA_NGANH (+)
					and q.QG_DAT_HOC_VI = g.MA_QUOC_GIA(+) and q.fk_he_dao_tao = hdt.ma_he_dao_tao (+)
					and q.BAC_DAO_TAO = 'TH'
					ORDER BY THOI_GIAN_TN DESC"; 
					$stmt = oci_parse($db_conn, $sqlstr);oci_execute($stmt);$n = oci_fetch_all($stmt, $resDM);oci_free_statement($stmt);
					$classAlt="alt";

					for ($i = 0; $i < $n; $i++)
					{
						($classAlt=="alt") ? $classAlt="alt_" : $classAlt="alt";
						
						if ($resDM["FK_NGANH"][$i]=="99999999")
							$ten_nganh = $resDM["NGANH_KHAC"][$i];
						else
							$ten_nganh = $resDM["TEN_NGANH"][$i];

						$txtBacDT 		= $resDM["BAC_DAO_TAO"][$i];
						$txtHeDT 		= $resDM["FK_HE_DAO_TAO"][$i];
						$txtTenBacDT 	= $resDM["TEN_BAC"][$i];
						$txtNamBD 		= $resDM["THOI_GIAN_BD"][$i];
						$txtNamTN 		= $resDM["THOI_GIAN_TN"][$i];
						$txtTenHDT 		= $resDM["TEN_HE_DAO_TAO"][$i];
						$txtNganh 		= $resDM["FK_NGANH"][$i];
						$txtNoiDT 		= $resDM["NOI_DAO_TAO"][$i];
						$txtQuocGiaDT 	= $resDM["TEN_TIENG_VIET"][$i];
						$txtMaQuocGiaDT = $resDM["QG_DAT_HOC_VI"][$i];
						$txtTenLALV 	= $resDM["TEN_LUAN_AN"][$i];
						
						echo "
						<tr align='left'>        
							<td  style='width:10px;'></td>
							<td colspan=2 align=left style=''>
							- <b>Thạc sĩ</b> chuyên ngành: $ten_nganh - Năm cấp bằng: $txtNamTN<br/>
							Nơi đào tạo: $txtNoiDT	- Quốc gia: $txtQuocGiaDT						
							</td>
						</tr>";
					}
				?>
				
				<?php 
					$sqlstr="SELECT q.*, b.TEN_BAC, n.TEN_NGANH, g.TEN_QUOC_GIA, g.TEN_TIENG_VIET, hdt.ten_he_dao_tao
					FROM NCKH_QUA_TRINH_DAO_TAO q, bac_dao_tao b, nckh_nganh_dt n, quoc_gia g, dm_he_dao_tao hdt
					WHERE FK_MA_CAN_BO = '".$macb. "' and q.BAC_DAO_TAO = b.MA_BAC (+) and q.FK_NGANH = n.MA_NGANH (+)
					and q.QG_DAT_HOC_VI = g.MA_QUOC_GIA(+) and q.fk_he_dao_tao = hdt.ma_he_dao_tao (+)
					and q.BAC_DAO_TAO = 'TS'
					ORDER BY THOI_GIAN_TN DESC"; 
					$stmt = oci_parse($db_conn, $sqlstr);oci_execute($stmt);$n = oci_fetch_all($stmt, $resDM);oci_free_statement($stmt);
					$classAlt="alt";
					
					for ($i = 0; $i < $n; $i++)
					{
						($classAlt=="alt") ? $classAlt="alt_" : $classAlt="alt";
						
						if ($resDM["FK_NGANH"][$i]=="99999999")
							$ten_nganh = $resDM["NGANH_KHAC"][$i];
						else
							$ten_nganh = $resDM["TEN_NGANH"][$i];
						
						// Lấy chuyên ngành do bộ yêu cầu
						$ten_nganh = $cbgd["CHUYEN_MON_BC_BO_GDDT"][0];
						
						$txtBacDT 		= $resDM["BAC_DAO_TAO"][$i];
						$txtHeDT 		= $resDM["FK_HE_DAO_TAO"][$i];
						$txtTenBacDT 	= $resDM["TEN_BAC"][$i];
						$txtNamBD 		= $resDM["THOI_GIAN_BD"][$i];
						$txtNamTN 		= $resDM["THOI_GIAN_TN"][$i];
						$txtTenHDT 		= $resDM["TEN_HE_DAO_TAO"][$i];
						$txtNganh 		= $resDM["FK_NGANH"][$i];
						$txtNoiDT 		= $resDM["NOI_DAO_TAO"][$i];
						$txtQuocGiaDT 	= $resDM["TEN_TIENG_VIET"][$i];
						$txtMaQuocGiaDT = $resDM["QG_DAT_HOC_VI"][$i];
						$txtTenLALV 	= $resDM["TEN_LUAN_AN"][$i];
						
						echo "
						<tr align='left'>        
							<td  style='width:10px;'></td>
							<td colspan=2 align=left style=''>
							- <b>Tiến sĩ</b> chuyên ngành: $ten_nganh - Năm cấp bằng: $txtNamTN<br/>
							Nơi đào tạo: $txtNoiDT - Quốc gia: $txtQuocGiaDT <br/>
							- Tên luận án: $txtTenLALV
							</td>
						</tr>";
					}
				?>
				
				<tr align='left'>        
					<td  style='width:10px;'></td><td align=left style='font-weight:bold;'>3. Ngoại ngữ:</td><td style=''></td>
				</tr>
				<?php 
					$sqlstr="SELECT get_info_ngoai_ngu(fk_ma_can_bo, fk_ma_ngoai_ngu) TTNN
					FROM NCKH_QT_NGOAI_NGU
					WHERE FK_MA_CAN_BO = '".$macb. "'"; 
					$stmt = oci_parse($db_conn, $sqlstr);oci_execute($stmt);$n = oci_fetch_all($stmt, $resDM);oci_free_statement($stmt);
					$classAlt="alt";
					
					for ($i = 0; $i < $n; $i++)
					{
						($classAlt=="alt") ? $classAlt="alt_" : $classAlt="alt";
						$ttNN 	= $resDM["TTNN"][$i];
						echo "
						<tr align='left'>
							<td  style='width:10px;'></td>
							<td colspan=2 align=left style=''>- $ttNN</td>
						</tr>";
					}
				?>
				
				<!-- Qua trinh cong tac chuyen mon -->
				<tr align="left">        
					<td  style="width:10px;"></td><td colspan=2 align=left style="font-weight:bold;"><div style="margin-top:10px;">III. QUÁ TRÌNH CÔNG TÁC CHUYÊN MÔN</div></td>
				</tr>
				<tr align="left">        
					<td></td>
					<td align=left colspan=2>
						<table width="100%" id="tablectkh" align="center" border=1 style="border-color: #000000; border-width: 1px; border-style: solid; border-collapse:collapse" cellspacing="0" cellpadding="5" class="tableData" height="20">
							<thead>
							  <tr class="heading" style="font-weight:bold">
								<td align="left" style='width:80px'><em>Thời gian</em></td>
								<td align="left"><em>Nơi công tác</em></td>
								<td align="left"><em>Công việc đảm nhận</em></td>
							  </tr>
							  </thead>
							  <tbody>
							  <?php
								$sqlstr="select thoi_gian_bd, thoi_gian_kt, noi_cong_tac, chuyen_mon, cv.ten_chuc_vu
								from NCKH_QUA_TRINH_CONG_TAC a, dm_chuc_vu cv
								where a.fk_ma_can_bo = '".$macb. "' 
								and a.fk_chuc_vu = cv.ma_chuc_vu (+)
								order by a.thoi_gian_bd desc"; 
								$stmt = oci_parse($db_conn, $sqlstr);oci_execute($stmt);$n = oci_fetch_all($stmt, $resDM);oci_free_statement($stmt);
								
								for ($i = 0; $i < $n; $i++)
								{

									if ($resDM["THOI_GIAN_BD"][$i] != '' && $resDM["THOI_GIAN_KT"][$i]!='')
										$thoigian = $resDM["THOI_GIAN_BD"][$i]."-{$resDM["THOI_GIAN_KT"][$i]}";
									elseif ($resDM["THOI_GIAN_BD"][$i] != '' && $resDM["THOI_GIAN_KT"][$i]=='')
										$thoigian = "{$resDM["THOI_GIAN_BD"][$i]}-nay";
									elseif ($resDM["THOI_GIAN_BD"][$i] == '' && $resDM["THOI_GIAN_KT"][$i]!='')
										$thoigian = $resDM["THOI_GIAN_KT"][$i];
										
									$damnhiem = $resDM["TEN_CHUC_VU"][$i];
									/*if ($damnhiem != '')
										$damnhiem .= " - {$resDM["CHUYEN_MON"][$i]}";
									else
										$damnhiem = $resDM["CHUYEN_MON"][$i];
									*/
									echo "<tr class='' align='left' valign='top'>";
									echo "<td align=center>$thoigian</td>";
									echo "<td align=left >".$resDM["NOI_CONG_TAC"][$i]."</td>";
									echo "<td align=left >$damnhiem</td>";
									echo "</tr>";
								} 
							  ?>
							  </tbody>
						</table>
					</td>
				</tr>
				
				
				<!-- Qua trinh nghien cuu khoa hoc -->
				<tr align="left">        
					<td  style="width:10px;"></td>
					<td colspan=2 align=left style="font-weight:bold;">
					<div style="margin-top:10px;">IV. QUÁ TRÌNH NGHIÊN CỨU KHOA HỌC</div>
					</td>
				</tr>
				
				<tr align="left">
					<td align=left ></td><td colspan=2>
						<div style="margin-top:0px;">
							
							<!-- Đề tài khoa học -->
							<div style="margin-bottom: 5px;">
								1. Các đề tài nghiên cứu khoa học đã và đang tham gia
							</div>
							<table width="100%" id="tableNCKH" align="center" cellspacing="0" cellpadding="5" border=1 style="border-color: #000000; border-width: 1px; border-style: solid; border-collapse:collapse; font-family:Arial,Helvetica,sans-serif;" class="tableData" height="20">
							<thead>
							  <tr class="heading" style="font-weight:bold;">
								<td style="width:15px;" class="ui-corner-tl"><em>TT</em></td>
								<td width="246" align="left"><em>Các đề tài, dự án, nghiên cứu khoa học</em></td>
								<td width="70" align=center><em>Thời gian</em></td>
								<td width="86" align="left"><em>Cấp quản lý</em></td>
								<td width="63" align=center class="ui-corner-tr"><em>Trách nhiệm</em></td>
								
								<!--<td width="59" class="ui-corner-tr" ><em>Kết quả</em></td>-->
							  </tr>
							  </thead>
							  <tbody>
							  <?php
								$sqlstr="select a.*, DECODE(a.CHU_NHIEM,1,'Chủ nhiệm','Tham gia') THAM_GIA, DECODE(a.NGHIEM_THU,1,'x','') TT_NGHIEM_THU, DECODE(a.KET_QUA,'X','Xuất sắc', 'T', 'Tốt', 'K','Khá','B', 'Trung Bình') TT_KET_QUA, b.ten_cap
								from de_tai_nckh a, cap_de_tai b
								 where a.fk_cap_de_tai = b.ma_cap(+) and 
								 a.ma_can_bo = '".$macb. "' order by a.nam_bat_dau desc"; 
								$stmt = oci_parse($db_conn, $sqlstr);
								oci_execute($stmt);
								$n = oci_fetch_all($stmt, $resDM);
								oci_free_statement($stmt);
								$classAlt="alt";
								$classAlt="";
								for ($i = 0; $i < $n; $i++)
								{
									//($classAlt=="alt") ? $classAlt="alt_" : $classAlt="alt";
										
									echo "<tr class=' ".$classAlt."' align='left' valign='top'>";
									echo "<td >".($i+1).".</td>";
									echo "<td >".$resDM["TEN_DE_TAI"][$i]."</td>";
									echo "<td align='center' >".$resDM["NAM_BAT_DAU"][$i]."-".$resDM["NAM_KET_THUC"][$i]."</td>";
									echo "<td >".$resDM["TEN_CAP"][$i]."</td>";
									echo "<td align='center'>".$resDM["THAM_GIA"][$i]."</td>";
									echo "</tr>";
								} 
							  ?>
							  </tbody>
							</table>
							
							<!-- Báo cáo khoa học -->
							<div style="margin-top:15px; margin-bottom:10px;">
								2. Các công trình khoa học đã công bố:
							</div>
							<table width="100%" id="tablectkh" align="center" border=1 style="border-color: #000000; border-width: 1px; border-style: solid; border-collapse:collapse" cellspacing="0" cellpadding="5" class="tableData" height="20">
							<thead>
							  <tr class="heading" style="font-weight:bold">
								<td style="width:20px"><em>TT</em></td>
								<td align="left"><em>Tên công trình</em></td>
								<td align="center" style="width:50px"><em>Năm công bố</em></td>
								<td align="left"><em>Tên tạp chí/Hội nghị</em></td>
							  </tr>
							  </thead>
							  <tbody>
							  <?php
								$sqlstr="select * from cong_trinh_khoa_hoc where ma_can_bo = '".$macb. "' 
								order by loai_cong_trinh, nam_xuat_ban_tap_chi desc"; 
								$stmt = oci_parse($db_conn, $sqlstr);
								oci_execute($stmt);
								$n = oci_fetch_all($stmt, $resDM);
								oci_free_statement($stmt);
								
								$loaictT='';
								$classAlt="";
								for ($i = 0; $i < $n; $i++)
								{
								   if ($resDM["LOAI_CONG_TRINH"][$i]=="BQ")
								   {
									 $loaitc = "Tạp chí quốc tế";
									 $isbn = "-ISBN/ISSN: {$resDM['ISBN'][$i]}";
								   }
								   else if ($resDM["LOAI_CONG_TRINH"][$i]=="BT")
								   {
									 $loaitc = "Tạp chí trong nước";
									 $isbn = "-ISBN/ISSN: {$resDM['ISBN'][$i]}";
								   }
								   else if ($resDM["LOAI_CONG_TRINH"][$i]=="HQ")
								   {
									 $loaitc = "Hội nghị quốc tế";
									 $isbn = "";
									}
								   else if ($resDM["LOAI_CONG_TRINH"][$i]=="HT")
								   {
									 $loaitc = "Hội nghị trong nước";
									 $isbn = "";
									}
									//($i % 2) ? $classAlt="alt" : $classAlt="";
									if ($loaictT!=$loaitc){
										echo "<tr><td colspan=4 align=left><b>$loaitc</b></td></tr>";
										$loaictT=$loaitc;
										//$classAlt="alt";
									}
									
									//($classAlt=="alt") ? $classAlt="alt_" : $classAlt="alt";
									echo "<tr align=\"left\" valign=\"top\" >";				
									echo "<td valign=\"top\" style=\"width:15px\">" .($i+1).".</td>";
									echo "<td>" .$resDM["TEN_BAI_BAO"][$i]."</td>";
									echo "<td align=center>{$resDM["NAM_XUAT_BAN_TAP_CHI"][$i]}</td>";
									echo "<td>{$resDM["TEN_TAP_CHI"][$i]}</td>";
									echo "</tr>";
									
								} 
							  ?>
							  </tbody>
							</table>
							
						</div>					
					</td>
					
				</tr>
				
				<tr>
					<td colspan=3 align=right>
						<table width=100%>
							<tr>
								<td align=left valign=top width=50% >
									<div style="width:300px; margin-top:20px" align=center>
										
										<span><em>Tp.HCM, ngày ...... tháng ...... năm .........</em></span><br/>
										<b>Thủ trưởng Đơn vị</b><br/>
										<i>(Họ tên, đóng dấu)</i>
									
									</div>
								</td>
								<td align=right width=50%>
									<div style="width:400px; margin-top:20px" align=center>
										<span><em>Tp.HCM, ngày <?php echo $ngay ?> tháng <?php echo $thang ?> năm <?php echo $nam ?></em></span><br/>
										<b>Người khai ký tên<br/>
										<br/><br/><br/><br/><br/>
										<?php echo $cbgd["HOTENCB"][0]; ?>
										</b>
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
<?php
if ($a != 'get_llkh')
{
?>	
</div>

<script type="text/javascript">
//jQuery.ajax
//$(document).ready(function(){
$(function(){
 
 $( "#print_ttgv_llkh_btn_printpreview" ).button({ icons: {primary:'ui-icon ui-icon-print'} });
 $( "#print_ttgv_llkh_btn_printpreview" ).click(function(){
	print_llkh_writeConsole($("#chitiet_LLKH_Bo").html(), 0);
 });

});
</script>

<?php 
}
?>
<?php 
if (isset ($db_conn))
	oci_close($db_conn);
?>