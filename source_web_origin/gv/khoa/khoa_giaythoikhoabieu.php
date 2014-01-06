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
if (!allowPermisstion(base64_decode($_SESSION['uidloginPortal']), '108', $db_conn))
{
	die('Truy cập bất hợp pháp'); 
}
$search = array('\\',"'",'"');
$replace = array('\\\\',"\'","&quot;"); 

$searchdb = array("\\\\","\\'",'\\"', "'");
$replacedb = array("\\","'", '"', "''");

$macb = $_SESSION['macb'];
$tenkhoa = $_SESSION["tenkhoa"];
$makhoa = base64_decode($_SESSION["makhoa"]);
$a = str_replace($searchdb,$replacedb,$_REQUEST['a']);
$m = str_replace($searchdb,$replacedb,$_POST["txtMaHV_TKB"]);

if ($a == 'form')
{
?>
<div>
	<div align=center style="font-size:16px; margin-bottom:10px;"><b>Giấy Xác Nhận Thời Khóa Biểu</b></div>
	<form id=formGiayTKB_khoa>
	<table width="100%" border="0" align="center" cellpadding="5" cellspacing="2">
	  <tr>
		<td align="right" class="heading" style="width:80px"><label for="txtMaHV_TKB">Mã học viên</label></td>
		<td>
			<input style="width:100px" class="text ui-widget-content ui-corner-all tableData" name="txtMaHV_TKB" id="txtMaHV_TKB" type="text" maxlength="10" placeholder=""/> <span style='font-weight:bold;margin-left:10px'>Họ và tên:</span> <span id=ho_ten_TKB style='font-weight:bold;margin-left:5px'></span>
		</td>
	  </tr>
	  <tr class="heading">
		<td align="right" ><label for="txtKinhGoi_TKB">Kính gửi</label></td>
		<td>
			<input style="width:100%" class="text ui-widget-content ui-corner-all tableData" name="txtKinhGoi_TKB" id="txtKinhGoi_TKB" type="text" maxlength="250" placeholder=""/>
		</td>
	  </tr>
	  <tr class="heading">
		<td align="right" ><label for="txtDotHoc_TKB">Học kỳ</label></td>
		<td>
			<select name="txtDotHoc_TKB" id="txtDotHoc_TKB" style="font-size:15px;" class="ui-widget-content ui-corner-all tableData">
				<option value=""></option>
				   <?php $sqlstr="select (hoc_ky || '/' || nam_hoc_tu || '-' || nam_hoc_den) nam_hoc, dot_hoc
								from dot_hoc_nam_hoc_ky 
								order by dot_hoc desc"; 
					$stmt = oci_parse($db_conn, $sqlstr);
					oci_execute($stmt);
					$n = oci_fetch_all($stmt, $resDM);
					oci_free_statement($stmt);
					
					for ($i = 0; $i < $n; $i++)
					{
						echo "<option value='".$resDM["DOT_HOC"][$i]."'>" .$resDM["NAM_HOC"][$i]. "</option>";
					}
					
				  ?>
			</select>
		</td>
	  </tr>
	  
	  <tr>
		<td></td><td><button id="btn_printpreview_TKB" style='font-size:80%'>&nbsp;Xem bản In</button></td>
	  </tr>
	</table>
	</form>
	<div style="margin-top:5px" align="center" id="tipTKB" class="ui-corner-all validateTips"></div>
</div> <!--end dialogGiayGioiThieu -->

<script type="text/javascript">
var validateMaHV_TKB = 0;
$(function(){
	$("#btn_printpreview_TKB" ).button({ icons: {primary:'ui-icon ui-icon-print'} });

	var jtxtMaHV_TKB = $("#txtMaHV_TKB"),
	jtxtKinhGoi_TKB  = $("#txtKinhGoi_TKB"),
	jtxtDotHoc_TKB = $( "#txtDotHoc_TKB" ),
	allFieldsTKB = $([]).add(jtxtMaHV_TKB).add(jtxtKinhGoi_TKB).add(jtxtDotHoc_TKB),
	tipTKB	= $("#tipTKB");
	function updateTips_TKB( t ) {
		tipTKB
			.text( t )
			.addClass( "ui-state-highlight" );
		setTimeout(function() {
			tipTKB.removeClass( "ui-state-highlight", 1500 );
		}, 1000 );
	}
	function TKB_checkLength( o, n, min, max) {
		if (min==0 && (o.val().length==0))
		{	
			o.addClass( "ui-state-error" );
			o.focus();
			updateTips_TKB( "Thông tin " + n + " không được phép để trống.");
			return false;
		}else if (min==max && o.val().length<min){
			o.addClass( "ui-state-error" );
			o.focus();
			updateTips_TKB("Thông tin " + n + " phải đủ " + min + " ký tự.");
		}else if ( o.val().length > max || o.val().length < min ) {
			o.addClass( "ui-state-error" );
			o.focus();
			updateTips_TKB("Chiều dài của " + n + " từ " + min + " đến " + max + " ký tự.");
			return false;
		} else {
			return true;
		}
	}
	// Check Regexp
	function TKB_checkRegexp( o, regexp, n ) {
		//alert('a');
		if ( o.val()!='' && !( regexp.test( o.val() ) ) ) {
			o.addClass( "ui-state-error" );
			o.focus();
			updateTips_TKB(n);
			return false;
		} else {
			return true;
		}
	}
	
	$("#btn_printpreview_TKB").click(function(e){
		var bValid = true;
		
		allFieldsTKB.removeClass( "ui-state-error" );
		
		bValid = bValid && TKB_checkLength( jtxtMaHV_TKB, "\"Mã học viên\"", 0, 10);
		bValid = bValid && TKB_checkLength( jtxtKinhGoi_TKB, "\"Kính gởi\"", 0, 250);
		bValid = bValid && TKB_checkLength( jtxtDotHoc_TKB, "\"Học kỳ\"", 0, 10);
		
		if (bValid && !validateMaHV_TKB)
			gv_open_msg_box("Mã học viên không chính xác. Vui lòng nhập lại", "alert");
			
		if (bValid && validateMaHV_TKB) {
			gv_processing_diglog("open", "Đang in ...");
			tipTKB.html("");
			dataString = $("#formGiayTKB_khoa").serialize()
			+ '&a=print&hisid=<?php echo $_REQUEST["hisid"];?>';
			$.ajax({
				type: "POST", url: "khoa/khoa_giaythoikhoabieu.php", data: dataString,	dataType: "html",
				success: function(data) {
							gv_processing_diglog("close");
							print_llkh_writeConsole(data, 0, "Giấy xác nhận Thời Khóa Biểu cao học - SĐH ĐHBK TP.HCM", 'style="font-family:Times New Roman,Arial,Helvetica,sans-serif;"',800,550);
						 }// end function(data)	
			}); // end .ajax
		}
		
		return false;

	});	
	
	$("#txtMaHV_TKB").change(function(e){
		$("#ho_ten_TKB").html("<img border='0' src='../images/ajax-loader.gif'/>");
		dataString = 'a=getname&hisid=<?php echo $_REQUEST["hisid"]; ?>'+'&m='+$("#txtMaHV_TKB").val();
		xreq = $.ajax({
		  type: 'POST', dataType: "json",url: 'khoa/khoa_giaythoikhoabieu.php',data: dataString,
		  success: function(data) {
			if (data.error==0)
			{
				validateMaHV_TKB = 1;
				$("#ho_ten_TKB").html(data.hoten);
			}
			else
			{
				validateMaHV_TKB = 0;
				$("#ho_ten_TKB").html("<font color=red>Không tìm thấy học viên</font>");
			}
		  },
		  error: function(xhr, ajaxOptions, thrownError) {}
		});
	 });
	
	$('input[placeholder],textarea[placeholder]').placeholder();
});
</script>
<?php 
}
else if ($a == 'getname')
{
	$m = str_replace($searchdb,$replacedb,$_POST["m"]);
	
	$sqlstr="SELECT ho || ' ' || ten ho_ten, B.MA_KHOA
			FROM HOC_VIEN H, NGANH N, BO_MON B
			WHERE ma_hoc_vien = '$m' 
			AND H.MA_NGANH = N.MA_NGANH
			AND N.MA_BO_MON = B.MA_BO_MON
			AND B.MA_KHOA = '$makhoa'"; 
	
	//file_put_contents("logs.txt", $sqlstr);
	
	$stmt = oci_parse($db_conn, $sqlstr);oci_execute($stmt);$n = oci_fetch_all($stmt, $resDM);oci_free_statement($stmt);
	
	if ($resDM["HO_TEN"][0]!="")
		echo '{"hoten":"'.$resDM["HO_TEN"][0].'", "error":"0"}';
	else
		echo '{"hoten":"", "error":"1"}';
}
else if ($a == 'print')
{
	date_default_timezone_set('Asia/Ho_Chi_Minh');
	$ngay =date("d");
	$thang =date("m");
	$nam =date("Y");
	//$date = new DateTime();
	//$date->modify('+1 month');
	
	$kinhgoi = str_replace($searchdb,$replacedb,$_POST["txtKinhGoi_TKB"]);
	$dothoc = str_replace($searchdb,$replacedb,$_POST["txtDotHoc_TKB"]);
	$tu = str_replace($searchdb,$replacedb,$_POST["txtFrom_TKB"]);
	$den = str_replace($searchdb,$replacedb,$_POST["txtTo_TKB"]);
	
	$sqlstr = "SELECT NAM_HOC_TU, NAM_HOC_DEN, HOC_KY FROM DOT_HOC_NAM_HOC_KY WHERE DOT_HOC = '$dothoc'";
	$stmt = oci_parse($db_conn, $sqlstr);oci_execute($stmt);$n = oci_fetch_all($stmt, $hk);oci_free_statement($stmt);
	
	$sqlstr="SELECT upper(ho || ' ' || ten) ho_ten, decode(H.PHAI, 'F','chị' ,'M','anh') titlePhai, to_char(NGAY_SINH, 'dd/mm/yyyy') NGAY_SINH, T.TEN_TINH_TP, h.Khoa, n.TEN_NGANH,
			so_qd_khoa('TKB', H.MA_HOC_VIEN) SO_QD, get_hinh_thuc_dao_tao(H.MA_HOC_VIEN) LOAI_HV
			FROM HOC_VIEN H, DM_TINH_TP T, NGANH N, BO_MON B
			WHERE MA_HOC_VIEN = '$m' AND NOI_SINH = T.MA_TINH_TP(+) AND H.MA_NGANH = N.MA_NGANH AND N.MA_BO_MON = B.MA_BO_MON
			AND B.MA_KHOA = '$makhoa'";
	//file_put_contents("logs.txt", $sqlstr);
	$stmt = oci_parse($db_conn, $sqlstr);
	if (oci_execute($stmt))
	{
		$n = oci_fetch_all($stmt, $hocvien);oci_free_statement($stmt);
		
		$sqlstr="BEGIN insert_so_qd_khoa('TKB', '$m', null, '$kinhgoi', '{$hocvien["SO_QD"][0]}', '$tu', '$den'); END;";
		$stmt = oci_parse($db_conn, $sqlstr);
		if (oci_execute($stmt))
		{
	?>
		<style>
			.bordertable {
				border-color: #000000; 
				border-width: 1px; 
				border-style: solid; 
				border-collapse:collapse;
			}
		</style>
	
		<table width="100%"   cellspacing="0" cellpadding="0">
		  <tr>
			<td valign='top'> 
				<div align="center" style="margin:10px 0 0 0">ĐẠI HỌC QUỐC GIA TP.HCM<br/><b>TRƯỜNG ĐẠI HỌC BÁCH KHOA</b><br/>-------------</div>
				<div align="center"> <span style="font-size:90%;"><em>Số: <?php echo $hocvien["SO_QD"][0];?></em></span></div>
			</td>
			<td valign='top'> 
				<div align="center"  style="margin:10px 0 0 0" ><b>CỘNG HÒA XÃ HỘI CHỦ NGHĨA VIỆT NAM<br/>Độc lập - Tự do - Hạnh phúc</b><br/>-------------</div>
				<div align="center"> <span><em>Tp. Hồ Chí Minh, ngày <?php echo $ngay ?> tháng <?php echo $thang ?> năm <?php echo $nam ?></em></span></div>
			</td>
		  </tr>
		  
		  <tr>
			<td colspan=2>
		  
				<table width="100%" border="0" align="center" cellpadding="5" cellspacing="0">
					<tr align="left">        
						<td align=left colspan=2 style="">
							<table style="margin:10px 0 10px 0">
								<tr>
									<td valign=top>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<b>Kính gửi:</b>&nbsp;&nbsp;</td><td valign=top>&nbsp;&nbsp;<b><?php echo $_POST["txtKinhGoi_TKB"];?></b></td>
								</tr>
							</table>
						</td>
					</tr>
					<tr align="left">        
						<td align=left style="" colspan=2>Phòng Đào Tạo SĐH Trường Đại Học Bách Khoa - ĐHQG Tp.HCM 
						xác nhận thời khóa biểu cao học học kỳ <b><?php echo $hk["HOC_KY"][0];?></b>
						năm học <b><?php echo "{$hk["NAM_HOC_TU"][0]}-{$hk["NAM_HOC_DEN"][0]}";?></b> của học viên 
						<b><?php echo $hocvien["HO_TEN"][0];?></b> như sau:
						</td>
					</tr>
					<tr align="left">
						<td align=left style="width:100px">Mã học viên:</td><td align=left><b><?php echo $m;?></b></td>
					</tr>
					<tr align="left">
						<td align=left>Họ tên:</td><td align=left><b><?php echo $hocvien["HO_TEN"][0];?></b></b></td>
					</tr>
					<tr align="left">
						<td align=left>Khóa:</td><td align=left><b><?php echo $hocvien["KHOA"][0];?></b></td>
					</tr>
					<tr align="left">
						<td align=left>Ngành:</td><td align=left><b><?php echo $hocvien["TEN_NGANH"][0];?></b></td>
					</tr>
					
					<tr> 
						<td colspan=2>
							<table style="margin: 10px 0 10px 0" width="100%" align="center" cellspacing="0" cellpadding="5" border=1 class="bordertable" height="20">
								<thead>
								  <tr style="font-weight:bold; background-color:#ccc">
									<td style="width:15px;">Thứ</td>
									<td align=left>Mã MH</td>
									<td align=left>Môn học</td>
									<td align=left >CBGD</td>
									<td align=center >Lớp</td>
									<td align=left style="width:55px;">Tiết học</td>
									<td align=left >Phòng</td>
									<td align=left >Tuần</td>
								  </tr>
								</thead>
								<tbody>
									<?php
									$sqlstr="SELECT DISTINCT h.ma_hoc_vien, upper(ho||' '||h.ten) ho_ten, decode(thu, 1, 'CN', 9, null, thu) thu, 
									d.ma_mh, m.ten ten_mh, t.lop, phong, h.khoa, ten_nganh, lpad(tiet_bat_dau,2,'0') tiet_bat_dau, 
									lpad(tiet_ket_thuc,2,'0') tiet_ket_thuc, lpad(tuan_bat_dau,2,'0') tuan_bat_dau, lpad(tuan_ket_thuc,2,'0') tuan_ket_thuc, 
									ho_ten cbgd, fk_kinh_phi_dao_tao, ten_eng, ho_eng, d.dot_hoc
									FROM hoc_vien h, dang_ky_mon_hoc d, thoi_khoa_bieu t, nganh n, mon_hoc m, bo_mon b
									WHERE d.ma_hoc_vien = h.ma_hoc_vien and h.ma_nganh = n.ma_nganh and d.dot_hoc = t.dot_hoc(+) 
									and d.ma_mh = t.ma_mh(+) and d.lop = t.lop(+) and d.ma_mh = m.ma_mh and d.dot_hoc = '$dothoc' and d.ma_hoc_vien = '$m'
									AND B.MA_KHOA = '$makhoa'AND N.MA_BO_MON = B.MA_BO_MON
									ORDER BY ten_eng, ho_eng, tuan_bat_dau, thu, tiet_bat_dau"; 
									//file_put_contents("logs.txt", $sqlstr);
									$stmt = oci_parse($db_conn, $sqlstr);oci_execute($stmt);$n = oci_fetch_all($stmt, $resDM);oci_free_statement($stmt);
									$tmp='';							
									for ($i = 0; $i < $n; $i++)
									{
										$tmp.="<tr><td align=center>{$resDM["THU"][$i]}</td><td align=left>{$resDM["MA_MH"][$i]}</td>
										<td align=left>{$resDM["TEN_MH"][$i]}</td><td align=left>{$resDM["CBGD"][$i]}</td><td align=center>{$resDM["LOP"][$i]}</td>
										<td align=left>{$resDM["TIET_BAT_DAU"][$i]}&rarr;{$resDM["TIET_KET_THUC"][$i]}</td>
										<td align=left>{$resDM["PHONG"][$i]}</td><td align=left>{$resDM["TUAN_BAT_DAU"][$i]}&rarr;{$resDM["TUAN_KET_THUC"][$i]}</td></tr> ";
									}
									echo $tmp;
									?>
								</tbody>
							</table>
							
							<table style="margin: 10px 0 10px 0; font-size:11px; width:100%"  align="center" cellspacing="0" cellpadding="5" border=1 class="bordertable" height="20">
								  <tr>
									<td colspan=2 align=left>Buổi sáng</td>
									<td colspan=2 align=left>Buổi chiều</td>
									<td align=left>Buổi tối</td>
								  </tr>
								  <tr style="">
									<td style="">
										Tiết 1: 06:30 - 07:15 <br/>
										Tiết 2: 07:20 - 08:05 <br/>
										Tiết 3: 08:15 - 09:00 <br/>
									</td>
									<td align=left>
										Tiết 4: 09:05 - 09:50 <br/>
										Tiết 5: 10:00 - 10:45 <br/>
										Tiết 6: 10:50 - 11:35 <br/>
									</td>
									<td align=left>
										Tiết 7: 12:30 - 13:15<br/>
										Tiết 8: 13:20 - 14:05<br/>
										Tiết 9: 14:15 - 15:00<br/>
									</td>
									<td align=left >
										Tiết 10: 15:05 - 15:50<br/>
										Tiết 11: 16:00 - 16:45<br/>
										Tiết 12: 16:50 - 17:35<br/>
									</td>
									<td align=left >
										Tiết 14: 18:15 - 19:00<br/>
										Tiết 15: 19:05 - 19:50<br/>
										Tiết 16: 20:00 - 20:45<br/>
									</td>
								  </tr>
							</table>
						</td>
					</tr>
					<tr>
						<td colspan=2 align=right>
							<table width=100%>
								<tr>
									<td align=left valign=top width=50% >
										<div style="width:200px; margin-top:20px" align=center>
											
										</div>
									</td>
									<td align=right width=50%>
										<div style="width:500px; margin-top:20px" align=center>
											<b>TL.HIỆU TRƯỞNG <br/>
											TRƯỞNG KHOA <?php echo mb_strtoupper($tenkhoa, 'UTF-8'); ?>
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
		}else
			file_put_contents("logs.txt", $sqlstr);
	}
	else 
		file_put_contents("logs.txt", $sqlstr);
}

if (isset ($db_conn))
	oci_close($db_conn);
?>