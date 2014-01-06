<?php
if (isset($_REQUEST["hisid"])){
	session_id($_REQUEST["hisid"]);
	session_start();
}
if (!isset($_SESSION['uidloginhv'])){
	die('Truy cập bất hợp pháp'); 
}

include "libs/connect.php";

$mahv = base64_decode($_SESSION["mahv"]);


?>

<div align="left" style="margin:0 auto;">
<form id="form_lichthinganh" name="form_lichthinganh" method="post" action="">
   	<div align=left style='margin: 10px 0 10px 0;'>
		<a id="dcuonglvan_btn_printpreview" name="taosach" style='font-size:80%'>&nbsp;Xem bản In</a>
	</div>
	
	<div id="dcuonglvan_chitiet" style="margin-top:20px;" align=center>
	   <div align=left>
		<?php
			$sqlstr = "	SELECT ho || ' ' || ten ho_ten, khoa, (select hoc_ky || '_' || nam_hoc_tu || '_' || nam_hoc_den from dot_hoc_nam_hoc_ky where dot_hoc = (select value from config where name='DOT_HOC_DKMH')) hk_nam
				FROM 	hoc_vien
				WHERE ma_hoc_vien = '$mahv'";
			$stmt = oci_parse($db_conn, $sqlstr);oci_execute($stmt);$n = oci_fetch_all($stmt, $resDM);oci_free_statement($stmt);
			if ($n > 0 ) 
			{
				$hknam = $resDM["HK_NAM"][0];
				echo "<b><span style='margin-left:0px'>Học viên: {$resDM["HO_TEN"][0]} (Mã số: $mahv)</span> - Khóa: {$resDM["KHOA"][0]}</b>";
			}
		?>
		</div>
		
<?php

	// Thong tin de cuong
	$sqlstr = "	SELECT to_char(c.dot_hoc, 'dd/mm/yyyy') dot_hoc, c.ghi_chu, c.du_dieu_kien, c.ngay_dang_ky, c.ngay_xet, d.hoc_ky||'/'||d.nam_hoc_tu||'-'||d.nam_hoc_den HOC_KY
				FROM dang_ky_de_cuong c, dot_hoc_nam_hoc_ky d
				WHERE c.ma_hoc_vien = '$mahv' AND c.dot_hoc = d.dot_hoc
				AND c.dot_hoc = (SELECT max(dot_hoc) FROM dang_ky_de_cuong WHERE ma_hoc_vien = '$mahv')";
	$stmt = oci_parse($db_conn, $sqlstr);oci_execute($stmt);$n = oci_fetch_all($stmt, $resDM);oci_free_statement($stmt);
	if ($n>0)
	{
?>
		<div class='ui-widget ui-widget-content ui-corner-top tableData' style='margin-top:15px;'>	
			<div align=left style='margin: 10px 0 5px 5px; font-weight:bold;'>THÔNG TIN ĐỀ CƯƠNG</div>
	<?php
			for ($i = 0; $i < $n; $i++)
			{
				echo "
				<div style='margin:10px 0 0 25px; font-weight:bold;' align=left >HK ".$resDM["HOC_KY"][$i]." (".$resDM["DOT_HOC"][$i].")</div>
				<div align=left>
				<ul>
					<li style='margin-bottom:5px;'> Kết quả đăng ký đề cương: 
					";
					if ($resDM["DU_DIEU_KIEN"][$i]=='1')
						echo "<b>Đủ điều kiện thực hiện đề cương</b>. Vui lòng xem Kế hoạch thực hiện Đề Cương chi tiết tại đây <a target=_blank href='http://www.pgs.hcmut.edu.vn/thac-si/hoc-vu/ke-hoach-de-cuong' style='color:blue'> >>> </a>";
					else
						echo "<b>Không đủ điều kiện</b>, lý do: ". $resDM["GHI_CHU"][$i];
					echo "
					</li style='margin-bottom:5px;'>
					<li> Kết quả đánh giá đề cương: Học viên xem KQ tại Khoa quản lý chuyên ngành</li>
				</ul>
				</div>
				";
			}
	?>
		</div>
<?php
	}
	
	$sqlstr = "select count(*) Khoa_luan from dao_tao_khoa_luan where ma_hoc_vien = '$mahv'";
	$stmt = oci_parse($db_conn, $sqlstr);oci_execute($stmt);$n = oci_fetch_all($stmt, $resDM);oci_free_statement($stmt);
	
	$khoaluanlv = $resDM["KHOA_LUAN"][0];
	if ($khoaluanlv == '1')
		$khoaluanlv = 'khóa luận';
	else
		$khoaluanlv = 'luận văn';
		
	//Thong tin luan van
	$sqlstr = "	
				SELECT DISTINCT l.ten_de_tai, d.ma_hoc_vien, q.so_quyet_dinh, h.ho || ' ' || h.ten ho_ten, 
					(TO_CHAR(q.ngay_bat_dau_luan_van, 'DD/MM/YYYY') || '-' || TO_CHAR(q.ngay_nop_luan_van,'DD/MM/YYYY')) NGAY_LUAN_VAN,
					c.ho || ' ' || c.ten ho_ten, c2.ho || ' ' || c2.ten ho_ten2, to_char(l.dot_nhan_lv, 'dd/mm/yyyy') dot_nhan_lv_c, l.dot_nhan_lv,
					(select count(*) from hoc_phi_luan_van hp
					where hp.ma_hoc_vien = '$mahv' 
					and hp.dot_hoc = l.dot_nhan_lv) tinh_trang_hoc_phi, 
					muc_hoc_phi_lvtn(l.dot_nhan_lv, h.ma_hoc_vien) muc_hoc_phi, 
					(select to_char(g.ngay_nop_luan_van,'dd/mm/yyyy')
						from gia_han_luan_van g
						where g.dot_nhan_lv = dot_nhan_lv(g.ma_hoc_vien)
						and g.ma_hoc_vien = d.ma_hoc_vien) ngay_gia_han, 
					(select g.ma_hoc_vien
					from gia_han_luan_van g
					where g.dot_nhan_lv = dot_nhan_lv(g.ma_hoc_vien)
					and g.ma_hoc_vien = d.ma_hoc_vien) duoc_gia_han
				FROM 	hoc_vien h, dang_ky_mon_hoc d, luan_van_thac_sy l,
						mon_hoc m, can_bo_giang_day c, can_bo_giang_day c2, QUYET_DINH_GIAO_DE_TAI q,
						gia_han_luan_van gh
				WHERE d.ma_hoc_vien = h.ma_hoc_vien
					AND l.huong_dan_chinh = c.ma_can_bo
					AND l.huong_dan_phu = c2.ma_can_bo(+)
					AND l.SO_QUYET_DINH_GIAO_DE_TAI = q.so_quyet_dinh(+)
					AND d.dot_hoc = l.dot_nhan_lv
					AND l.ma_hoc_vien = h.ma_hoc_vien 
					AND l.dot_nhan_lv = (select max(dot_nhan_lv) from luan_van_thac_sy where ma_hoc_vien='$mahv')
					AND m.ma_loai = '5' 
					AND h.ma_hoc_vien =  '$mahv' 
					AND d.dot_hoc  =  l.dot_nhan_lv
					AND h.ma_hoc_vien = gh.ma_hoc_vien(+)";
	$stmt = oci_parse($db_conn, $sqlstr);oci_execute($stmt);$n = oci_fetch_all($stmt, $resDM);oci_free_statement($stmt);
	if ($n>0)
	{
?>
	<div class='ui-widget ui-widget-content ui-corner-top tableData' style='margin-top:15px;'>
		<div align=left style='margin: 10px 0 5px 5px; font-weight:bold;text-transform:uppercase;'>thông tin <?php echo $khoaluanlv; ?></div>
		<div align=left>
			<ul>
				<li style='margin-bottom:5px;'>Đợt nhận đề tài: <b><?php echo  $resDM["DOT_NHAN_LV_C"][0]; ?></b></li>
				<li style='margin-bottom:5px;'>Tên đề tài: <b><?php echo  $resDM["TEN_DE_TAI"][0]; ?></b></li>
				<li style='margin-bottom:5px;'>Cán bộ hướng dẫn: <b><?php echo  $resDM["HO_TEN"][0]; if (trim($resDM["HO_TEN2"][0])!='') echo " - ".$resDM["HO_TEN2"][0]; ?></b></li>
				<li style='margin-bottom:5px;'>Thời gian thực hiện: <b><?php echo  $resDM["NGAY_LUAN_VAN"][0]; ?></b></li>
				<li style='margin-bottom:5px;'>Thời gian đóng học phí: xem thông báo lịch thu học phí tại trang chủ</li>
				<li style='margin-bottom:5px;'>Học phí <?php echo $khoaluanlv;?>: <b><?php echo  number_format($resDM["MUC_HOC_PHI"][0]); ?> VNĐ</b></li>
				<?php
					if ($resDM["DUOC_GIA_HAN"][0]!="")
					{
						$ngay_gia_han = "";
						if ($resDM["NGAY_GIA_HAN"][0]!= "")
							$ngay_gia_han = "đến ngày <b>{$resDM["NGAY_GIA_HAN"][0]}</b>";
						echo "
							<li style='margin-bottom:5px;color:blue;'>Gia hạn luận văn: <b>Được gia hạn</b> $ngay_gia_han</li>
						";
					}
				?>
			</ul>
		</div>
	</div>
<?php
	}
	else
	{
		$sqlstr = "	SELECT ghi_chu, to_char(DOT_NHAN_LV,'dd/mm/yyyy') DOT_NHAN_LV FROM tmp_giao_de_tai WHERE ma_hoc_vien =  '$mahv' ";
		$stmt = oci_parse($db_conn, $sqlstr);oci_execute($stmt);$n = oci_fetch_all($stmt, $resDM);oci_free_statement($stmt);
		if ($n>0)
		{
			$lydo = $resDM["GHI_CHU"][0];
			$dotnhanlv = $resDM["DOT_NHAN_LV"][0];
			if ($dotnhanlv!='')
				$dotnhanlv = " đợt $dotnhanlv";
			if ($lydo!='')
				$lydo = "($lydo)";
		}
?>
	<div class='ui-widget ui-widget-content ui-corner-top tableData' style='margin-top:15px;'>
		<div align=left style='margin: 15px 0 0 5px; font-weight:bold;text-transform:uppercase;'>thông tin <?php echo $khoaluanlv; ?></div>
		<div align=left>
			<ul>
				<li style='margin-bottom:5px;color:red'>Chưa được giao đề tài <?php echo "$dotnhanlv $lydo"?></li>
				<li style='margin-bottom:5px;'>Học viên tham khảo thêm <b>Bảng điểm tích lũy và Quy trình giao Đề Cương & LVThs</b></li>
				<li style='margin-bottom:5px;'>Mọi câu hỏi xin vui lòng liên hệ Phòng Đào Tạo Sau Đại Học</li>
			</ul>
		</div>
	</div>
<?php
	}
?>
   </div>

   
	
</form>
</div>

<script type="text/javascript">		
function lichthi_canhan_updateLichThi(p_dothoc)
{
	$( "#dcuonglvan_btn_printpreview" ).button( "disable" );
	if (p_dothoc!=null)
	{
		$("#dcuonglvan_chitiet").html("<img border='0' src='images/ajax-loader.gif'/>");
		xreq = $.ajax({
		  type: 'POST', dataType: "html",
		  url: 'hv_lichthicanhan_process.php?w=dothoc-lichthi_canhan'
		  + '&d=' + p_dothoc
		  + '&h=' + encodeURIComponent($("#lichthi_canhan_txtHK option:selected").html())
		  + '&hisid=<?php echo $_REQUEST["hisid"]; ?>',
		  success: function(data) {
			$("#dcuonglvan_chitiet").html(data);
			$( "#dcuonglvan_btn_printpreview" ).button( "enable" );
		  },
		  error: function(xhr, ajaxOptions, thrownError) {
			$( "#dcuonglvan_btn_printpreview" ).button( "disable" );
			$("#dcuonglvan_chitiet").html(thrownError);
		  }
		});
	}
	else
	{
		$("#dcuonglvan_chitiet").html("<div align=center>Chưa có lịch thi cá nhân</div>");
	}
}

$(function() {
	$( "#dcuonglvan_btn_printpreview" ).button({ icons: {primary:'ui-icon ui-icon-print'} });
	//lichthi_canhan_updateLichThi($("#lichthi_canhan_txtHK").val());

	$("#dcuonglvan_btn_printpreview").click(function(){
		writeConsole("<div style='font-size:13px; font-weight:bold;' align=left>ĐẠI HỌC BÁCH KHOA - TP.HCM<br/>PHÒNG ĐÀO TẠO SAU ĐẠI HỌC<br/> &nbsp;</div>" + $("#dcuonglvan_chitiet").html() + "<div style='clear:both;'>&nbsp;<br/><a href='JavaScript:window.print();'>In trang này</a></div>", 800, 450);
	});	// end $("#dcuonglvan_btn_printpreview")
});
</script>

<?php 
if (isset ($db_conn))
	oci_close($db_conn);
?>