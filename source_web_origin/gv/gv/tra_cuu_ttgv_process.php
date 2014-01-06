<?php
   // cat = detai, ttgv, ctkh, sach, hdlvts, hdlats
   // act = add, edit, del
error_reporting(0);

$sid = $_REQUEST["hisid"];

if ($sid!=""){
	session_id($sid);
	session_start();
}

$search = array('\\',"'",'"');
$replace = array('\\\\',"\'","&quot;"); 

$searchdb = array("\\\\","\\'",'\\"', "'");
$replacedb = array("\\","'", '"', "''");

include "../libs/connect.php";
//include "libs/connect.php";

$w = $_REQUEST['w'];
$ma_bm = $_REQUEST['m'];
$macb = $_REQUEST['m'];

if ($w == "bm_dsgv_1") 
{
	$sqlstr="select *
			from (select c.ma_can_bo, viet0dau_name(c.ten) ten_khong_dau, viet0dau_name(c.ho) ho_khong_dau, get_thanh_vien(c.ma_can_bo) ho_ten
						from can_bo_giang_day c
						where c.fk_loai_can_bo = '00'	
						and c.ma_bo_mon='$ma_bm' and trang_thai=1 and (c.ma_hoc_vi = 'TH' or c.ma_hoc_vi = 'TS')
						order by c.ma_hoc_vi, ten_khong_dau, ho_khong_dau) ar
			where rownum < 24";
	$stmt = oci_parse($db_conn, $sqlstr);
	oci_execute($stmt);
	$n = oci_fetch_all($stmt, $resDM);
	oci_free_statement($stmt);
	$classAlt="alt";
	for ($i = 0; $i < $n; $i++)
	{
		($classAlt=="alt") ? $classAlt="alt_" : $classAlt="alt";

		$txtHoTen 		= $resDM["HO_TEN"][$i];
		$txtMaCB 		= $resDM["MA_CAN_BO"][$i];
				
		echo "<tr class=' ".$classAlt."' align='left' valign='top'>";
		echo "<td style='' align=left>".($i+1)."</td>";
		echo "<td align=left class='ahref1' onClick=\"getTTGV('$txtMaCB');\">$txtHoTen</td>";
		//echo "<td class='ahref' onclick=\"getQTDT(".($i+1).", '$txtBacDT','$txtNganh','$txtMaQuocGiaDT',$txtNamBD,$txtNamTN);\">Sửa</td>";
		echo "</tr>";
	}
	
}

if ($w == "bm_dsgv_2") 
{
	$sqlstr="select c.ma_can_bo, viet0dau_name(c.ten) ten_khong_dau, viet0dau_name(c.ho) ho_khong_dau, get_thanh_vien(c.ma_can_bo) ho_ten
						from can_bo_giang_day c
						where c.fk_loai_can_bo = '00'	
						and c.ma_bo_mon='$ma_bm' and trang_thai=1 and (c.ma_hoc_vi = 'TH' or c.ma_hoc_vi = 'TS')
						order by c.ma_hoc_vi, ten_khong_dau, ho_khong_dau
			";
	$stmt = oci_parse($db_conn, $sqlstr);
	oci_execute($stmt);
	$n = oci_fetch_all($stmt, $resDM);
	oci_free_statement($stmt);
	$classAlt="alt";
	
	if ($n>=23)
	{
		echo "
		<table width='100%' id='tableDSGV' align='center' border='0' cellspacing='0' cellpadding='5' class='ui-widget ui-widget-content ui-corner-top tableData' height='20'>
					<thead>
					  <tr class='ui-widget-header heading' >
						<td class=' ui-corner-tl' style='width:25px' align=left><em>STT</em></td>
						
						<td class=' ui-corner-tr' align=left><em>Họ và tên giảng viên</em></td>
					  </tr>
					</thead>
					<tbody>
		";
		for ($i = 23; $i < $n; $i++)
		{
			($classAlt=="alt") ? $classAlt="alt_" : $classAlt="alt";

			$txtHoTen 		= $resDM["HO_TEN"][$i];
			$txtMaCB 		= $resDM["MA_CAN_BO"][$i];
					
			echo "<tr class=' ".$classAlt."' align='left' valign='top'>";
			echo "<td style='' align=left>".($i+1)."</td>";
			echo "<td align=left class='ahref1' onClick=\"getTTGV('$txtMaCB');\">$txtHoTen</td>";
			//echo "<td class='ahref' onclick=\"getQTDT(".($i+1).", '$txtBacDT','$txtNganh','$txtMaQuocGiaDT',$txtNamBD,$txtNamTN);\">Sửa</td>";
			echo "</tr>";
		}
		
		echo "
				</tbody>
			</table>
		";
	}
}

if ($w == "mcb_ttgv") 
{
	$sqlstr="select cb.*, to_char(cb.NGAY_SINH,'dd-mm-yyyy') NGAY_SINH, k.ten_khoa, bm.ten_bo_mon
		from can_bo_giang_day cb, bo_mon bm, khoa k
		where cb.ma_bo_mon = bm.ma_bo_mon (+) and bm.ma_khoa = k.ma_khoa (+)
		and cb.ma_can_bo='$macb'";

	$stmt = oci_parse($db_conn, $sqlstr);
	oci_execute($stmt);
	$n = oci_fetch_all($stmt, $cbgd);
	oci_free_statement($stmt);
	date_default_timezone_set('Asia/Ho_Chi_Minh');
	$ngay =date("d");
	$thang =date("m");
	$nam =date("Y");
	$index=1;
?>
  <div align="center" style="margin-top:0px; font-family:Arial,Helvetica,sans-serif; font-size:80%" id="chitietttgv">
    <table width="100%"   cellspacing="0" cellpadding="0" class="ui-corner-all shawdow tableData" style="font-size:120%;">
      <tr>
        <td valign='top'> 
		<div align="center" style="margin-top:10px">ĐẠI HỌC QUỐC GIA TP.HCM<br/><b>TRƯỜNG ĐẠI HỌC BÁCH KHOA</b><br/>-------------</div>
        </td>
		<td valign='top'> 
		<div align="center"  style="margin-top:10px" ><b>CỘNG HÒA XÃ HỘI CHỦ NGHĨA VIỆT NAM<br/>Độc lập - Tự do - Hạnh phúc</b><br/>-------------</div>
        </td>
      </tr>
	  <tr>
        <td colspan=2 valign='top'> 
		<div align="center"  style="margin-top:20px; margin-bottom:20px"><b><font style="font-size:160%; font-weight:bold;">THÔNG TIN KHOA HỌC</font></b><br/>(Dành cho cán bộ tham gia đào tạo SĐH tại Trường Đại học Bách Khoa, Đại học Quốc gia Tp.HCM)</div>
        </td>
      </tr>
      <tr>
        <td colspan=2>
      
			<table width="100%" border="0" align="center" cellpadding="5" cellspacing="0"  style="font-family:Arial,Helvetica,sans-serif;font-size:100%;">
			
				<tr align="left">        
					<td align=left style="width:20px"><?php echo $index++; ?>.</td><td  style="">Họ và tên: <b><?php echo $cbgd["HO"][0]. " " .$cbgd["TEN"][0]; ?></b></td>
				</tr>
				<tr align="left">
					<td align=left><?php echo $index++; ?>.</td><td>Ngày tháng năm sinh: <?php echo $cbgd["NGAY_SINH"][0]; ?></td>
				</tr>
				
				<tr align="left">
					<td align=left><?php echo $index++; ?>.</td><td >Khoa: <b><?php echo $cbgd["TEN_KHOA"][0]; ?></b>, Bộ môn: <b><?php echo $cbgd["TEN_BO_MON"][0]; ?></b></td>
				</tr>
				
				<tr align="left">
					<td align=left><?php echo $index++; ?>.</td><td >Điện thoại liên hệ: <?php echo "{$cbgd["DIEN_THOAI"][0]} - {$cbgd["DIEN_THOAI_CN"][0]}";  ?></td>
				</tr>
				
				<tr align="left">
					<td align=left><?php echo $index++; ?>.</td><td >Email: <?php echo "{$cbgd["EMAIL"][0]} - {$cbgd["EMAIL_2"][0]}";  ?></td>
				</tr>
				
				<tr align="left">
					<td align=left><?php echo $index++; ?>.</td><td >Học vị: <b><?php switch ($cbgd["MA_HOC_VI"][0])
																		{
																			case "TSK": echo "Tiến sĩ khoa học"; break;
																			case "TS": echo "Tiến sĩ"; break;
																			case "TH": echo "Thạc sĩ"; break;
																			case "CN": echo "Cử nhân"; break;
																			case "KS": echo "Kỹ sư"; break;
																			default: echo "";
																		} 
																	?></b>
					</td>
				</tr>
				
				<tr align="left">
					<td align=left><?php echo $index++; ?>.</td><td >Nước tốt nghiệp: 
						<?php 
							$sqlstr="select * from QUOC_GIA where MA_QUOC_GIA='{$cbgd["QG_DAT_HOC_VI"][0]}'"; 
							$stmt = oci_parse($db_conn, $sqlstr);
							oci_execute($stmt);
							if (oci_fetch_all($stmt, $resDM))
							{
								oci_free_statement($stmt);
								echo $resDM["TEN_QUOC_GIA"][0];
							}
						?>
					</td>
				</tr>
				
				<tr align="left">
					<td align=left><?php echo $index++; ?>.</td><td >Ngành: 
						<?php 
							$sqlstr="select * from nganh_ts where MA_NGANH='{$cbgd["FK_NGANH"][0]}'"; 
							$stmt = oci_parse($db_conn, $sqlstr);
							oci_execute($stmt);
							if (oci_fetch_all($stmt, $resDM))
							{
								oci_free_statement($stmt);
								if ($cbgd["FK_NGANH"][0]== '99999999')
									echo $cbgd["NGANH_KHAC"][0];
								else
									echo $resDM["TEN_NGANH"][0];
							}
						?>
					</td>
				</tr>
				
				<tr align="left">
					<td align=left><?php echo $index++; ?>.</td><td >Chuyên ngành: <?php echo $cbgd["CHUYEN_NGANH"][0];?>	</td>
				</tr>
				
				<tr align="left">
					<td align=left><?php echo $index++; ?>.</td><td >Chức danh khoa học: <b><?php  switch ($cbgd["MA_HOC_HAM"][0])
																					{
																						case "GS": echo "Giáo sư"; break;
																						case "PGS": echo "Phó giáo sư"; break;
																						default: echo "";
																					} 
																			?></b>
						<?php 
								if ($cbgd["MA_HOC_HAM"][0]=='GS' || $cbgd["MA_HOC_HAM"][0]=='PGS' ) 
									echo ", Năm phong: {$cbgd["NAM_PHONG_HOC_HAM"][0]}";
								
						?>
					</td>
				</tr>
				
				<tr align="left">
					<td align=left><?php echo $index++; ?>.</td><td >Lĩnh vực chuyên môn hiện tại: <?php echo "{$cbgd["CHUYEN_MON"][0]}";  ?></td>
				</tr>
				
				<tr align="left">
					<td align=left valign=top><?php echo $index++; ?>.</td><td >Các hướng nghiên cứu chính: <br/>
						<div style="margin-left:5px;margin-top:5px;font-size:100%;">
							<table style="font-size:100%;">
							  <?php $sqlstr="select * from huong_de_tai where ma_can_bo = '$macb' order by nam desc, ten_de_tai"; 
								$stmt = oci_parse($db_conn, $sqlstr);
								oci_execute($stmt);
								$n = oci_fetch_all($stmt, $resDM);
								oci_free_statement($stmt);
								
								$tmp='';
								for ($i = 0; $i < $n; $i++)
								{
									$tmp.="<tr><td valign=top>-</td><td>&nbsp;{$resDM["TEN_DE_TAI"][$i]}</td> </tr> ";
								}
								echo $tmp;
							  ?>
							</table>
						</div>
					</td>
				</tr>
				
				<tr align="left">
					<td align=left><?php echo $index++; ?>.</td><td >Số LATS đã hướng dẫn thành công / đang hướng dẫn tại trường Đại học Bách khoa Tp.HCM (từ năm 2004): 	
																																<?php 
																																	$sqlstr="select 
																																			(select count(l.ma_hoc_vien)
																																			from luan_an_tien_sy l 
																																			where (l.huong_dan_1 = '$macb' or l.huong_dan_2 = '$macb' or l.huong_dan_3 = '$macb') 
																																			and l.dot_cap_bang is null) Dang_Huong_Dan,

																																			(select count(l.ma_hoc_vien)
																																			from luan_an_tien_sy l 
																																			where (l.huong_dan_1 = '$macb' or l.huong_dan_2 = '$macb' or l.huong_dan_3 = '$macb') 
																																			and l.dot_cap_bang is not null) Thanh_Cong

																																			from dual"; 
																																	$stmt = oci_parse($db_conn, $sqlstr);oci_execute($stmt);$n = oci_fetch_all($stmt, $resDM);oci_free_statement($stmt);
																																	echo "<b>{$resDM["THANH_CONG"][0]}</b> / <b>{$resDM["DANG_HUONG_DAN"][0]}</b>";
																																?>
											</td>
				</tr>
				<tr align="left">
					<td align=left><?php echo $index++; ?>.</td><td >Số LVThS đã hướng dẫn thành công tại trường Đại học Bách khoa Tp.HCM (từ năm 2004): <?php 
																																	$sqlstr="select count(l.ma_hoc_vien) huong_dan_th
																																			from luan_van_thac_sy l, hoc_vien h
																																			where diem_luan_van(l.ma_hoc_vien)>=5 
																																			and (huong_dan_chinh = '$macb' or huong_dan_phu = '$macb')
																																			and h.ma_hoc_vien = l.ma_hoc_vien 
																																			and dot_nhan_lv = dot_nhan_lv(h.ma_hoc_vien)"; 
																																	$stmt = oci_parse($db_conn, $sqlstr);oci_execute($stmt);$n = oci_fetch_all($stmt, $resDM);oci_free_statement($stmt);
																																	echo "<b>{$resDM["HUONG_DAN_TH"][0]}</b>";
																																?>
											</td>
				</tr>
				
				<tr align="left">
					<td align=left valign=top><?php echo $index++; ?>.</td><td>Giảng dạy các môn học Sau đại học: <br/> 
												<div style="margin-left:5px;margin-top:5px">
													<table style="font-size:100%;">
													  <?php
														// Lấy tên môn học
														$sqlstr="SELECT DISTINCT titlecase(m.TEN) ten_mon_hoc
																	FROM THOI_KHOA_BIEU t, MON_HOC m
																	WHERE t.MA_MH = m.MA_MH
																	AND (t.ma_can_bo='$macb' OR t.ma_can_bo_phu='$macb')
																	order by ten_mon_hoc"; 
														$stmt = oci_parse($db_conn, $sqlstr);oci_execute($stmt);$n = oci_fetch_all($stmt, $resDM);oci_free_statement($stmt);
														$tmp='';
														for ($i = 0; $i < $n; $i++)
														{
															// Lấy các ngành của môn học
															$nganh = "";
															$sqlstr="select distinct titlecase(ten_nganh) ten_nganh_title
																	from thoi_khoa_bieu t1, nganh n
																	where t1.ma_nganh_in = n.ma_nganh
																	and (t1.ma_can_bo = '$macb' OR t1.ma_can_bo_phu='$macb')
																	and TitleCase(t1.ten_mh) = '{$resDM["TEN_MON_HOC"][$i]}'";
																	
															$stmt = oci_parse($db_conn, $sqlstr);oci_execute($stmt);$m = oci_fetch_all($stmt, $resDMnganh);oci_free_statement($stmt);
															for ($j = 0; $j < $m; $j++)
															{
																$nganh.="{$resDMnganh["TEN_NGANH_TITLE"][$j]}, ";
															}
															if ($nganh!="")
																$nganh = "(".substr($nganh,0,-2).")";
															
															
															$tmp.="<tr><td valign=top>-</td><td>&nbsp;{$resDM["TEN_MON_HOC"][$i]} $nganh</td> </tr> ";
														}
														echo $tmp;
													  ?>
													</table>
												</div>
										</td>
				</tr>
				
				<tr align="left">
					<td align=left valign=top><?php echo $index++; ?>.</td><td >Các công trình khoa học đã công bố trong 3 năm gần đây:
						<div style="margin-top:10px;">
							
							<!-- Đề tài khoa học -->
							<div style="margin-bottom: 5px;">
								<b>Đề tài khoa học</b>
							</div>
							<table width="100%" id="tableNCKH" align="center" cellspacing="0" cellpadding="5" border=1 style="border-color: #000000; border-width: 1px; border-style: solid; border-collapse:collapse; font-family:Arial,Helvetica,sans-serif; font-size:100%;" class="tableData" height="20">
							<thead>
							  <tr class="heading" style="font-weight:bold;">
								<td style="width:15px;" class="ui-corner-tl"><em>TT</em></td>
								<td width="246" align="left"><em>Các đề tài, dự án, nghiên cứu khoa học</em></td>
								<td width="70" align=center><em>Thời gian</em></td>
								<td width="63" align=center ><em>Chủ nhiệm</em></td>
								<td width="86" align="left"><em>Cấp quản lý</em></td>
								<td width="70" align=center class="ui-corner-tr"><em>Nghiệm thu</em></td>
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
										
									echo "<tr class=' ".$classAlt."' align='left' valign='top'><input name=\"hiddenMaNCKH".$i."\" type='hidden' id='hiddenMaNCKH".$i."' value=\"".base64_encode($resDM["MA_DE_TAI"][$i])."\"/>";
									
									echo "<td >".($i+1).".</td>";
									echo "<td >".$resDM["TEN_DE_TAI"][$i]."</td>";
									echo "<td align='center' >".$resDM["NAM_BAT_DAU"][$i]."-".$resDM["NAM_KET_THUC"][$i]."</td>";
									echo "<td align='center'>".$resDM["THAM_GIA"][$i]."</td>";
									echo "<td >".$resDM["TEN_CAP"][$i]."</td>";
									echo "<td align='center' ><b>".$resDM["TT_NGHIEM_THU"][$i]."</b></td>";
									//echo "<td >".$resDM["TT_KET_QUA"][$i]."</td>";
									
									echo "</tr>";
								} 
							  ?>
							  </tbody>
							</table>
							
							<!-- Báo cáo khoa học -->
							<div style="margin-top:15px; margin-bottom:10px;">
								<b>Bài báo tạp chí/hội nghị khoa học đã công bố</b>
							</div>
							<table width="100%" id="tablectkh" align="center" border=1 style="border-color: #000000; border-width: 1px; border-style: solid; border-collapse:collapse;font-size:100%;" cellspacing="0" cellpadding="5" class="tableData" height="20">
							<thead>
							  <tr >
								<td style="width:20px">TT</td>
								<td align="left">Các bài báo, tạp chí, hội nghị khoa học</td>
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
										echo "<tr><td colspan=2 align=left><b>$loaitc</b></td></tr>";
										$loaictT=$loaitc;
										//$classAlt="alt";
									}
									
									//($classAlt=="alt") ? $classAlt="alt_" : $classAlt="alt";
									echo "<tr align=\"left\" valign=\"top\" >";				
									echo "<td valign=\"top\" style=\"width:15px\">" .($i+1).".</td>";
									echo "<td>" .$resDM["TEN_TAC_GIA"][$i].". ".$resDM["TEN_BAI_BAO"][$i].".<i> ".$resDM["TEN_TAP_CHI"][$i]."</i> - {$resDM["CITY"][$i]} <b>".$resDM["SO_TAP_CHI"][$i]."</b>, ".$resDM["TRANG_DANG_BAI_BAO"][$i]." (".$resDM["NAM_XUAT_BAN_TAP_CHI"][$i].") $isbn </td>";
									
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
						<table width=100%>
							<tr>
								<td align=left valign=top width=50% >
									<div style="width:300px; margin-top:20px" align=center>
										
									</div>
								</td>
								<td align=right width=50%>
									<div style="width:400px; margin-top:20px" align=center>
										
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
  </div>
<?php
}
?>


<?php 
if (isset ($db_conn))
	oci_close($db_conn);
?>