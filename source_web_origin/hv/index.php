<?php
if (isset($_REQUEST["hisid"])){
	$sid = $_REQUEST["hisid"];
	session_id($sid);
	session_start();
}

if (!isset($_SESSION['uidloginhv'])){
	header("Location: login.php");
	die('Truy cập bất hợp pháp'); 
}
else
{
	include "libs/connect.php";
	$usr = base64_decode($_SESSION["uidloginhv"]);
	$pass = base64_decode($_SESSION["pidloginhv"]);
	$link = $_REQUEST["l"];
	
	$str="SELECT username, first_login FROM nguoi_dung WHERE username='".($usr)."' AND pass='".($pass)."'";
	
	if(isset($_SESSION['phpCAS'])){
		$str="SELECT username, first_login FROM nguoi_dung WHERE username='".($usr)."'";
	}
	
	$oci_pa = oci_parse($db_conn,$str);oci_execute($oci_pa);$result=oci_fetch_all($oci_pa, $kt);oci_free_statement($oci_pa);
	if ($result==0) {
		die('Truy cập bất hợp pháp');
	}
	
	$first_login = $kt["FIRST_LOGIN"][0];
	
	$str="select HOC_KY, NAM_HOC_TU, NAM_HOC_DEN, to_char(DOT_HOC, 'dd/mm/yyyy') NGAY_BAT_DAU from dot_hoc_nam_hoc_ky where dot_hoc = (select max(dot_hoc) from dot_hoc_nam_hoc_ky)";
	$oci_pa = oci_parse($db_conn,$str);oci_execute($oci_pa);$result=oci_fetch_all($oci_pa, $tmp);oci_free_statement($oci_pa);
	if ($result>0){
		$hk = $tmp["HOC_KY"][0];
		$namtu = $tmp["NAM_HOC_TU"][0];
		$namden= $tmp["NAM_HOC_DEN"][0];
		$ngaybatdau = $tmp["NGAY_BAT_DAU"][0];
	}
	
	// DKMH theo nguyen vong
	$sqlstr="SELECT value , (sysdate - to_date(value,'dd/mm/yyyy')) het_han FROM config WHERE name='DKMH_NV_NGAY_HET_HAN'";
	$stmt = oci_parse($db_conn, $sqlstr);oci_execute($stmt);oci_fetch_all($stmt, $resDM);oci_free_statement($stmt);
	$ngayhethanDKMH_NV = $resDM["VALUE"][0];

	$sqlstr="SELECT value , (sysdate - to_date(value,'dd/mm/yyyy')) bat_dau FROM config WHERE name='DKMH_NV_NGAY_BAT_DAU'";
	$stmt = oci_parse($db_conn, $sqlstr);oci_execute($stmt);$n = oci_fetch_all($stmt, $resDM);oci_free_statement($stmt);
	$ngaybatdauDKMH_NV = $resDM["VALUE"][0];
	// end
	
	// DKMH
	$sqlstr="SELECT value , (to_date('$today','dd/mm/yyyy')-to_date(value,'dd/mm/yyyy')) het_han FROM config WHERE name='DKMH_NGAY_HET_HAN'";
	$stmt = oci_parse($db_conn, $sqlstr);oci_execute($stmt);$n = oci_fetch_all($stmt, $resDM);oci_free_statement($stmt);
	$ngayhethanDKMH = $resDM["VALUE"][0];

	$sqlstr="SELECT value , (to_date('$today','dd/mm/yyyy')-to_date(value,'dd/mm/yyyy')) bat_dau FROM config WHERE name='DKMH_NGAY_BAT_DAU'";
	$stmt = oci_parse($db_conn, $sqlstr);oci_execute($stmt);$n = oci_fetch_all($stmt, $resDM);oci_free_statement($stmt);
	$ngaybatdauDKMH = $resDM["VALUE"][0];
	// end
	
	// Dang ky de cuong
	$sqlstr="SELECT value , floor(sysdate - to_date(value,'dd/mm/yyyy')) het_han FROM config WHERE name='DK_DC_NGAY_KT'";
	$stmt = oci_parse($db_conn, $sqlstr);oci_execute($stmt);oci_fetch_all($stmt, $resDM);oci_free_statement($stmt);
	$ngayhethan_dkdc = $resDM["VALUE"][0];

	$sqlstr="SELECT value , (sysdate - to_date(value,'dd/mm/yyyy')) bat_dau FROM config WHERE name='DK_DC_NGAY_BD'";
	$stmt = oci_parse($db_conn, $sqlstr);oci_execute($stmt);$n = oci_fetch_all($stmt, $resDM);oci_free_statement($stmt);
	$ngaybatdau_dkdc = $resDM["VALUE"][0]; 
	// end
}


?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Phong Dao Tao Sau Dai Hoc</title>

</head>


<link href="../js/ui-1.9.2/css/start/jquery-ui-1.9.2.custom.min.css" rel="stylesheet" type="text/css"/>

<link href="css/pgs.css" rel="stylesheet" type="text/css"/>
<link href="../css/bootstrap.min.css" rel="stylesheet" type="text/css"/>
<link href="../datatable/media/css/jquery.dataTables_themeroller.css" rel="stylesheet" type="text/css"/>
 
<script src="../js/jquery-1.8.3.min.js"></script>

<script src="../js/ui-1.9.2/js/jquery-ui-1.9.2.custom.min.js"></script>

<script src="../js/jquery.cookie.js"></script>
<script src="../js/jquery.placeholder-1.1.9.js"></script>
<script src="../js/bootstrap.min.js"></script>  
<script src="../js/pgs.js"></script>
<script src="../datatable/media/js/jquery.dataTables.min.js"></script>
<script src="../js/jquery.maskedinput-1.3.min.js"></script>

<script src="../js/jquery.form.js"></script>

<style>
	#tabs {}
	#tabs li .ui-icon-close { float: left; margin: 0.4em 0.2em 0 0; cursor: pointer; }
</style>

<body style="font-family:Arial, Helvetica, sans-serif">
<div id="container">
<?php include('headerhv.php'); ?>
<div id="gutter"></div>

<div class="banner" >
	<div align=left >
		<img src="../images/banner-sdh.jpg" />
	</div>
</div>

<div id="col1">
    <script type="text/javascript">	
	
	$(function() {
		var $tab_title_input = $( "#tab_title");
			$tab_content_input = $( "#tab_content" );
		var tab_counter = 3;
		var tab_current;
		//var tab_current_common; // chuyen tab click hien tai vao file php dc goi
		var tabOpened=new Array();
		var tabNameClick = '';		
		// tabs init with a custom tab template and an "add" callback filling in the content
		/*
		var $tabs = $( "#tabs").tabs({
			tabTemplate: "<li><a href='#{href}'>#{label}</a> <span class='ui-icon ui-icon-close'>Remove Tab</span></li>",
			add: function( event, ui ) {
				//var tab_content = ("content tab " + tab_counter) || "Tab " + tab_counter + " content.";
				//$( ui.panel ).append( "<p>" + tab_content + "</p>" );
				 $tabs.tabs('select', '#' + ui.panel.id);
				 tabOpened[tabNameClick] = ui.panel.id;
			},
			remove: function(event, ui) {
				for (index in tabOpened)
					if (tabOpened[index]==ui.panel.id){
						tabOpened[index] = null;
						break;
					}
			},
			cookie: {
				// store cookie for a day, without, it would be a session cookie
				expires: 2
			},
			cache: true
			
		});*/
		
		var $tabs = $( "#tabs").tabs({
			tabTemplate: "<li><a href='#{href}'>#{label}</a> <span class='ui-icon ui-icon-close'>Remove Tab</span></li>",
			add: function( event, ui ) {
				tabOpened[tabNameClick] = ui.panel.id;
				//alert(tabOpened[tabNameClick]);
				$tabs.tabs("option", "active", $('#' + tabOpened[tabNameClick]).index()-1);
			},
			remove: function(event, ui) {
				for (index in tabOpened)
					if (tabOpened[index]==ui.panel.id){
						tabOpened[index] = null;
						break;
					}
			},
			activate: function( event, ui ) {
				if (ui.newPanel.attr('id')=='tabs-3'){
					//loadTabKHCN();
				}
			},
			cookie: {
				// store cookie for a day, without, it would be a session cookie
				expires: 30
			},
			cache: true
		});
		
		
		
		// actual addTab function: adds new tab using the title input from the form above
		function addTab(tabNameFunc, title, url) {
			if (tabOpened[tabNameFunc]!=null){
				$tabs.tabs("option", "active", $('#' + tabOpened[tabNameFunc]).index()-1);
			}else{
				var tab_title = title || "Tab " + tab_counter;
				$tabs.tabs( "add", url, tab_title);
				tab_counter++;
			}
		}
		
		// close icon: removing the tab on click
		// note: closable tabs gonna be an option in the future - see http://dev.jqueryui.com/ticket/3924
		$( "#tabs span.ui-icon-close" ).live( "click", function() {
			var index = $( "li", $tabs ).index( $( this ).parent() );
			$tabs.tabs( "remove", index );
			tab_counter--;
			$tabs.tabs( "select" , tab_current);
		});
		
		
		$("#DangKyYCHV").click(function() {
			tabNameClick = 'DangKyYCHV';
			addTab(tabNameClick,"<img border='0' width='12' height='12' src='icons/schedule-icon.png' /> Đăng ký học vụ", "hv_dangkyhocvu.php?hisid=<?php echo $sid;?>");
			tab_current = 0;
		});
		
		$("#tracuuTKBNganh").click(function() {
			tabNameClick = 'tracuuTKBNganh';
			addTab(tabNameClick,"<img border='0' width='12' height='12' src='icons/schedule-icon.png' /> Thời Khóa Biểu Ngành", "hv_tkbnganh.php?hisid=<?php echo $sid;?>");
			tab_current = 0;
		});
		
		$("#tracuuTKBCN").click(function() {
			tabNameClick = 'tracuuTKBCN';
			addTab(tabNameClick,"<img border='0' width='12' height='12' src='icons/schedule-icon.png' /> Thời Khóa Biểu Cá Nhân", "hv_tkbcanhan.php?hisid=<?php echo $sid;?>");
			tab_current = 0;
		});
		
		$("#tracuuKQHocPhi").click(function() {
			tabNameClick = 'tracuuKQHocPhi';					
			addTab(tabNameClick,"<img border='0' width='12' height='12' src='icons/kinhphithtn.png' /> KQ Học Phí", "hv_kqdonghp.php?hisid=<?php echo $sid;?>");
			tab_current = 0;
		});
		
		$("#tracuuLichThiNganh").click(function() {
			tabNameClick = 'tracuuLichThiNganh';					
			addTab(tabNameClick,"<img border='0' width='12' height='12' src='icons/lichthi.png' /> Lịch Thi Ngành", "hv_lichthinganh.php?hisid=<?php echo $sid;?>");
			tab_current = 0;
		});
		
		$("#tracuuLichThiCN").click(function() {
			tabNameClick = 'tracuuLichThiCN';					
			addTab(tabNameClick,"<img border='0' width='12' height='12' src='icons/lichthicn.png' /> Lịch Thi Cá Nhân", "hv_lichthicanhan.php?hisid=<?php echo $sid;?>");
			tab_current = 0;
		});
		
		$("#tracuuDiem").click(function() {
			tabNameClick = 'tracuuDiem';					
			addTab(tabNameClick,"<img border='0' width='12' height='12' src='icons/tracuudiem.png' /> Bảng Điểm Tích Lũy", "hv_tracuudiem.php?hisid=<?php echo $sid;?>");
			tab_current = 0;
		});
		
		$("#tracuuCTDT").click(function() {
			tabNameClick = 'tracuuCTDT';					
			addTab(tabNameClick,"<img border='0' width='12' height='12' src='icons/ctdt_icon.png' /> Chương Trình Đào Tạo", "hv_tracuu_ctdt.php?hisid=<?php echo $sid;?>");
			tab_current = 0;
		});
		
		<?php
		//if (base64_decode($_SESSION["mahv"])=="03207104")
		//{
		?>
		$("#DangKyMH").click(function() {
			tabNameClick = 'DangKyMH';					
			addTab(tabNameClick,"<img border='0' width='12' height='12' src='icons/klgd16.png' /> Đăng ký MH", "hv_dkmh.php?hisid=<?php echo $sid;?>");
			tab_current = 0;
		});
		
		$("#DangKyMHYeuCau").click(function() {
			tabNameClick = 'DangKyMHYeuCau';					
			addTab(tabNameClick,"<img border='0' width='12' height='12' src='icons/lichthuhocphi16.png' /> ĐKMH theo nguyện vọng", "hv_dkmh_yeu_cau.php?hisid=<?php echo $sid;?>");
			tab_current = 0;
		});
		
		$("#DangKyDeCuong").click(function() {
			tabNameClick = 'DangKyDeCuong';					
			addTab(tabNameClick,"<img border='0' width='12' height='12' src='icons/Hire-me-icon32.png' /> Đăng ký đề cương", "hv_dangkydecuong.php?hisid=<?php echo $sid;?>");
			tab_current = 0;
		});
			
		<?php
		//}
		?>
		
		$("#QDNgoaiNgu").click(function() {
			tabNameClick = 'QDNgoaiNgu';					
			//window.open('http://www.pgs.hcmut.edu.vn/toeic_av_C.php?id=710','_blank');
			tab_current = 0;
		});
		
		$("#DCuongLVan").click(function() {
			tabNameClick = 'DCuongLVan';					
			addTab(tabNameClick,"<span><img border='0' width='12' height='12' src='icons/decuonglvan-icon.png' /></span> Đề Cương-Luận Văn", "hv_dcuonglvan.php?hisid=<?php echo $sid;?>");
			tab_current = 0;
		});
		
		$("#changePass").click(function() {
			tabNameClick = 'changepass';
			addTab(tabNameClick,"<img border='0' width='12' height='12' src='icons/user-password-icon.png' /> Thay đổi mật khẩu", "hv_changepassfrm.php?hisid=<?php echo $sid;?>");
			tab_current = 1;
		});
		
		$("#hv_thongtinhv").click(function() {
			tabNameClick = 'hv_thongtinhv';
			addTab(tabNameClick,"<img border='0' width='12' height='12' src='icons/personal-information-icon.png' /> Thông tin cá nhân", "hv_accountinfo_frm.php?hisid=<?php echo $sid;?>");
			tab_current = 1;
		});
		
		$("#hv_quitrinhcap_lv_dcuong").click(function() {
			tabNameClick = 'hv_quitrinhcap_lv_dcuong';
			addTab(tabNameClick,"<img border='0' width='12' height='12' src='icons/ok-process-icon.png' /> Qui trình giao ĐC-LV Ths", "readfile.php?t=images&l=images/de_cuong_lv.png");
			tab_current = 0;
		});
		$("#hv_bieu_mau_ths").click(function() {
			window.open("http://www.pgs.hcmut.edu.vn/bieu-mau/hv-cao-hoc", '_blank');
		});
		$("#hv_bieu_mau_ts").click(function() {
			window.open("http://www.pgs.hcmut.edu.vn/bieu-mau/ncs", '_blank');
		});
		
		
		$("#hv_diem_ppnckh").click(function() {
			tabNameClick = 'hv_diem_ppnckh';
			addTab(tabNameClick,"<img border='0' width='12' height='12' src='icons/view-list-details-icon.png' /> Điểm PPNCKH", link_diem_ppnckh);
			tab_current = 0;
		});
		
		$("#hv_upload_ky_yeu").click(function() {
			tabNameClick = 'hv_upload_ky_yeu';
			addTab(tabNameClick,"<img border='0' width='12' height='12' src='icons/view-list-details-icon.png' /> Upload hình kỷ yếu", "hv_upload_hinhkyyeu.php?hisid=<?php echo $sid;?>");
			tab_current = 0;
		});
		
		$( "input:submit, button").button();
		
		
		
		//$( "#sortableTTCN, #sortableBAOMAT, #sortableHOCVU" ).disableSelection();
		
		<?php 
			if ($first_login==1)
			{
				echo "hv_open_msg_box('Bạn chưa thay đổi mật khẩu mặc định, vui lòng sử dụng chức năng <b><a href=\"#\" onClick=\"$( \'#hv_index_dialog_msgbox\' ).dialog(\'close\'); $(\'#changePass\').click(); this.close();\">Thay Đổi Mật Khẩu</a></b> trong tab Bảo Mật <b>thay đổi mật khẩu mặc định</b> để đảm bảo an toàn cho tài khoản.', 'info');";
			}
			
			switch ($link) {
				case "TraCuuTKBNganh":
					echo "$('#tracuuTKBNganh').click();";
					break;
				case "TraCuuTKBCaNhan":
					echo "$('#tracuuTKBCN').click();";
					break;
				case "TraCuuKetQuaHocPhi":
					echo "$('#tracuuKQHocPhi').click();";
					break;
				case "TraCuuLichThiNganh":
					echo "$('#tracuuLichThiNganh').click();";
					break;
				case "TraCuuLichThiCaNhan":
					echo "$('#tracuuLichThiCN').click();";
					break;
				case "TraCuuDiem":
					echo "$('#tracuuDiem').click();";
					break;
				case "TraCuuChuongTrinhDaoTao":
					echo "$('#tracuuCTDT').click();";
					break;
				case "DangKyMonHoc":
					echo "$('#DangKyMH').click();";
					break;
				case "DangKyMonHocNguyenVong":
					echo "$('#DangKyMHYeuCau').click();";
					break;
				case "DeCuongLuanVan":
					echo "$('#DCuongLVan').click();";
					break;
				case "ThayDoiMatKhau":
					echo "$('#changePass').click();";
					break;
				case "ThongTinCaNhan":
					echo "$('#hv_thongtinhv').click();";
					break;
				case "QuiTrinhGiaoDCLVThs":
					echo "$('#hv_quitrinhcap_lv_dcuong').click();";
					break;
				
			}
		?>
		
		var namespace; 
		namespace = {
			readPdf : function(plink, ptabname) {
				tabNameClick = ptabname;
				addTab(tabNameClick,"<img border='0' width='12' height='12' src='icons/decuonglvan-icon.png' /> "+ptabname, "readfile.php?w=600px&h=700px&t=pdf&l="+plink);
				tab_current = 1;
			}
			/*,
			bodyInfo : function() {
				alert($('body').attr('id'));
			}*/
		};
		window.ns = namespace;
	});

</script>	
    <div class="demo" style="width:100%;">
		<input type=hidden id=tab_current_common name=tab_current_common>
		
        <div id="tabs" style="border:1;">
            <ul>
                <li><a href="#tabs-1"><strong>Học Vụ</strong></a></li>
                <li><a href="#tabs-2"><strong>Bảo Mật</strong></a></li>
            </ul>
            <div id="tabs-1">
				<div style="height:280px; float:left; border-right: 3px solid #aaa;">
					<table cellpadding="3">
						<tr style='cursor:pointer;' id="hv_lichtiephocvien" rel="popover" data-original-title="Lịch tiếp học viên"><td><img src="icons/lichtiephocvien16.png" border=0 width="16" height="16"/></td><td style='font-weight:bold;color:#0195df'>Lịch tiếp học viên</td></tr>
						<tr style='cursor:pointer;' id="hv_quitrinhcap_lv_dcuong" rel="popover" data-original-title="Qui trình giao Luận văn - Đề Cương"><td><img src="icons/ok-process-icon.png" border=0 width="16" height="16"/></td><td style='font-weight:bold;color:#0195df'>Qui trình giao LV-ĐC</td></tr>
						<tr style='cursor:pointer;' id="hv_bieu_mau_ths" rel="popover" data-original-title="Biểu mẫu cho cao học"><td><img src="icons/documents-icon.png" border=0 width="16" height="16"/></td><td style='font-weight:bold;color:#0195df'>Biểu mẫu cho CH</td></tr>
						<tr style='cursor:pointer;' id="hv_bieu_mau_ts" rel="popover" data-original-title="Biểu mẫu cho NCS"><td><img src="icons/documents-icon.png" border=0 width="16" height="16"/></td><td style='font-weight:bold;color:#0195df'>Biểu mẫu cho NCS</td></tr>
						
						<tr ><td ></td></tr>
						
						<!-- Điểm môn PPNCKH -->
						<?php
							$sqlstr="SELECT ma_hoc_vien, ma_mh
									FROM dang_ky_mon_hoc
									WHERE ma_mh = '340000030'
									AND dot_hoc = '20-AUG-2012' AND lop = 1 AND ma_hoc_vien='$usr'";
									
							$oci_pa = oci_parse($db_conn,$sqlstr);
							oci_execute($oci_pa);
							$nrows=oci_fetch_all($oci_pa, $kt);
							oci_free_statement($oci_pa);
							
							if ($nrows>0)
							{
								$filediem="download/diem/diem_{$kt['MA_MH'][0]}.pdf";
								if (file_exists("./$filediem"))
								{
						?>
									<script type="text/javascript">	
										var link_diem_ppnckh = "readfile.php?t=pdf&l=http://www.grad.hcmut.edu.vn/hv/<?php echo $filediem; ?>";
									</script>
									<tr style='cursor:pointer;' id='hv_diem_ppnckh' rel='popover' data-original-title='Điểm môn PPNCKH'><td ><img src='icons/view-list-details-icon.png' border=0 width='16' height='16'/></td><td style='font-weight:bold;color:#0195df'>Điểm thi môn PPNCKH</td></tr>
						<?php
								}
							}
						?>
						<!-- End of Điểm môn PPNCKH -->
						
						<!-- Hình tốt nghiệp -->
						<?php
							$folder = "./hinhtn/2013_2";
							$filehinh = "$folder/$usr".".jpg";
							$filehinh_1 = "$folder/$usr"."_1.jpg";
							$filehinh_2 = "$folder/$usr"."_2.jpg";
							if (file_exists($filehinh))
							{
						?>
							<tr  id="hv_hinhtn" >
								<td valign=top><img src="icons/Download-icon.png" border=0 width="16" height="16"/></td>
								<td align=left style='font-weight:bold;color:#7eb543'>					
								<div>Hình tốt nghiệp</div>
								<div align=center>
									<a style='color:red;font-weight:bold;' target='_blank' href="<?php echo $filehinh; ?>"><img style='margin:5px 0 0 0; ' src="icons/picture-icon.png" border=0 /></a> 
									<?php
									if (file_exists($filehinh_1)){
									?>
										<a style='color:red;font-weight:bold;' target='_blank' href="<?php echo $filehinh_1; ?>"><img style='margin:5px 0 0px 15px; ' src="icons/picture-icon.png" border=0 /></a>
									<?php
									}
									if (file_exists($filehinh_2)){
									?>
										<a style='color:red;font-weight:bold;' target='_blank' href="<?php echo $filehinh_2; ?>"><img style='margin:5px 0 0px 15px; ' src="icons/picture-icon.png" border=0 /></a>
									<?php
									}
									?>
								</div>
								</td>
							</tr>
						<?php
							}
						?>
						<!-- End of Hình tốt nghiệp -->
					
					</table>
				</div>
				<div id=gutter style="height:280px; width:10px;"></div>
				<div style="width:600px; margin:0 10px 0 20px;" align=left> 
					<ul id="sortableHOCVU">
						<li ><a id='DangKyYCHV' href="#"><img border="0" width="48" height="48" src="icons/register-icon2.png" /><br/>Đăng ký<br/>Yêu cầu học vụ</a></li>
                        <li ><a id='tracuuTKBNganh' href="#"><img border="0" width="48" height="48" src="icons/schedule-icon.png" /><br/>TKB Ngành</a></li>
						<li ><a id='tracuuTKBCN' href="#"><img border="0" width="48" height="48" src="icons/schedule-icon-cn.png" /><br/>TKB Cá Nhân</a></li>
						<li ><a id='tracuuLichThiNganh' href="#"><img border="0" width="48" height="48" src="icons/lichthi.png" /><br/>Lịch Thi Ngành</a></li>
						<li ><a id='tracuuLichThiCN' href="#"><img border="0" width="48" height="48" src="icons/lichthicn.png" /><br/>Lịch Thi<br/>Cá Nhân</a></li>
						<li ><a id='tracuuDiem' href="#"><img border="0" width="48" height="48" src="icons/tracuudiem.png" /><br/>Bảng Điểm<br/>Tích Lũy</a></li>
						<li ><a id='DangKyMH' href="#"><img border="0" width="48" height="48" src="icons/klgd.png" class="tooltips" data-placement="top" title="Thời gian từ <?php echo "<b>$ngaybatdauDKMH</b>-<b>$ngayhethanDKMH</b>"; ?>"/><br/>Đăng Ký<br/>Môn Học</a></li>
						<li ><a id='tracuuKQHocPhi' href="#"><img border="0" width="48" height="48" src="icons/kinhphithtn.png" /><br/>KQ Đóng<br/>Học Phí</a></li>
						<li ><a id='DCuongLVan' href="#"><img border="0" width="48" height="48" src="icons/decuonglvan-icon.png" /><br/>Đề Cương<br/>Luận Văn</a></li>
						<li ><a id='tracuuCTDT' href="#"><img border="0" width="48" height="48" src="icons/ctdt_icon.png" /><br/>Chương Trình<br/>Đào Tạo</a></li>
						<li ><a id='DangKyMHYeuCau' href="#" ><img border="0" width="48" height="48" src="icons/dkmh_yeu_cau.png" class="tooltips" data-placement="top" title="Thời gian từ <?php echo "<b>$ngaybatdauDKMH_NV</b>-<b>$ngayhethanDKMH_NV</b>"; ?>"/><br/>ĐKMH theo<br/>nguyện vọng</a></li>
						<li ><a id='DangKyDeCuong' href="#" ><img border="0" width="48" height="48" src="icons/Hire-me-icon.png" class="tooltips" data-placement="top" title="Thời gian từ <?php echo "<b>$ngaybatdau_dkdc</b>-<b>$ngayhethan_dkdc</b>"; ?>"/><br/>Đăng ký<br/>Đề cương</a></li>
					</ul>
					<!--<p><?php //echo 'user login: '.base64_decode($_SESSION['uidlogin']).' macb='.$_SESSION['macb'] ?></p>-->
				</div>
				<div class=clearfloat></div>
            </div>
            <div id="tabs-2">
            	 <div style="width:450px; margin:auto;"> 
                	<ul id="sortableBAOMAT">
                        <li ><a id='changePass' href="#"><img border="0" width="48" height="48" src="icons/user-password-icon.png" /><br/>Thay đổi<br/>Mật Khẩu</a></li>
                       <li ><a id='hv_thongtinhv' href="#"><img border="0" width="48" height="48" src="icons/personal-information-icon.png" /><br/>Thông tin<br/>cá nhân</a></li>
                    </ul>

                </div>
				<div class=clearfloat></div>
            </div> <!-- End tabs-3 -->
        </div>
        
	</div><!-- End demo -->

</div> <!-- End col1 -->

<div id="footer">
<div id='footer_content'>
<div style="float:left;margin-right:10px;margin-top:8px;"><img src="images/logoBK.png" width="32" height="32"/></div>
<div style="float:left;margin-right:10px;margin-top:5px; font-size: 90%">
	• Mọi thắc mắc liên quan đến học vụ vui lòng liên hệ số: <b>38-637-318</b> hoặc <b>38647256 - 5263</b><br/>
	• Chi tiết nhân sự phụ trách công việc xem <a href='http://www.pgs.hcmut.edu.vn/gioi-thieu/nhan-su' target="_blank">tại đây</a><br/>
	• Học Viên phát hiện lỗi chương trình xin vui lòng gửi email thông báo cho <a href = 'mailto:taint@hcmut.edu.vn'>taint@hcmut.edu.vn</a>
</div>
<div style="float:right;margin-right:10px; margin-top:0px;font-size: 80%" align=center><p>Dùng tốt nhất với</p>
		<a href='http://www.mozilla.org/en-US/firefox/fx/'><img src="icons/Firefox-icon32.png" border=0 width="32" height="32"/></a> &nbsp; <a href='http://www.google.com/Chrome'><img src="icons/Chrome-icon32.png" border=0 width="32" height="32"/></a>
</div>
</div>

</div>
</div> <!-- End footer -->
</div>

<div id="hv_index_dialog_msgbox" title="Phòng Đào Tạo SDH - ĐHBK TP.HCM">
<span id='hv_index_dialog_msgbox_icon' class="ui-icon ui-icon-alert" style="float:left; margin:0 7px 40px 0;"></span><span id="hv_index_dialog_msgbox_msg"></span>
</div>

<div id="hv_processing_diglog" title="Phòng Đào Tạo SDH - ĐHBK TP.HCM">
	<div align=center style="margin: 10px 0 15px 0; color: #636363">
	<b><span id="hv_processing_diglog_msg"></span></b>
	</div>
	<div align=center >
		<img src='images/ajax-loader.gif'/>
	</div>
</div>

</body>
</html>

<script type="text/javascript">
//ptype = alert, info
function hv_processing_diglog(paction, ptitle, pmsg, pwidth, pheight, presizable){
	pwidth = pwidth || 250;
	pheight = pheight || 120;
	presizable = presizable || false;
	pmsg = pmsg || "Đang xử lý dữ liệu ...";
	ptitle = ptitle || "Phòng Đào Tạo SDH - ĐHBK TP.HCM";
	
	$("#hv_processing_diglog").dialog('option', 'title', ptitle);
	$("#hv_processing_diglog").dialog("option", "height", pheight);
	$("#hv_processing_diglog").dialog("option", "width", pwidth);
	$("#hv_processing_diglog").dialog("option", "resizable", presizable);
	$("#hv_processing_diglog_msg").html(pmsg);
	$("#hv_processing_diglog").dialog(paction);
}

function hv_open_msg_box(pmsg, ptype, pwidth, pheight){
	pwidth = pwidth || 280;
	pheight = pheight || 180;
	
	$("#hv_index_dialog_msgbox_icon").removeClass("ui-icon");
	$("#hv_index_dialog_msgbox_icon").removeClass("ui-icon-alert");
	$("#hv_index_dialog_msgbox_icon").removeClass("ui-icon-info");
	$("#hv_index_dialog_msgbox_icon").removeClass("ui-icon-disk");
	
	if (ptype=='alert')
	{
		$("#hv_index_dialog_msgbox_icon").addClass("ui-icon");
		$("#hv_index_dialog_msgbox_icon").addClass("ui-icon-alert");
	}
	else if (ptype=='info')
	{
		$("#hv_index_dialog_msgbox_icon").addClass("ui-icon");
		$("#hv_index_dialog_msgbox_icon").addClass("ui-icon-info");
	}
	else if (ptype=='disk')
	{
		$("#hv_index_dialog_msgbox_icon").addClass("ui-icon");
		$("#hv_index_dialog_msgbox_icon").addClass("ui-icon-disk");
	}
	
	$("#hv_index_dialog_msgbox").dialog("option", "height", pheight);
	$("#hv_index_dialog_msgbox").dialog("option", "width", pwidth);
	$("#hv_index_dialog_msgbox_msg").html(pmsg);
	$("#hv_index_dialog_msgbox").dialog("open");
}

function reverse_escapeJsonString (str, pBr) {
	var nstr = null;
	if (str!=null){
		nstr = str.replace(/\\\\/g, "\\");
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
	}
	return nstr;
}

function print_hv_writeConsole(pContent, pAutoOpenPrinter, pTitle, pStyle, pWidth, pHeight) {
	var pWidth_ = pWidth || 800;
	var pHeight_ = pHeight || 450;
	var pStyle_ = pStyle || 'style="font-family:Arial,Helvetica,sans-serif;"';
	var abc=window.open('','','width='+pWidth_+',height='+pHeight_+',menubar=1'+',toolbar=0'+',status=0'+',scrollbars=1'+',resizable=1');
	var strprint='';
	if (pAutoOpenPrinter==1){
		strprint = 'window.print();';
	}
	abc.document.writeln(
	'<html><head><title>'+pTitle+'</title></head>'
	+'<body bgcolor=white onLoad="self.focus(); ' + strprint + '" '+ pStyle_ + '>'
	+pContent
	+'</body></html>'
	);
	
//	abc.document.close();
}

// Kiem tra thoi gian cua session
//event to check session time variable declaration
var checkSessionTimeEvent;
var sessionLength = 58*60; // 60 minus
//time redirect forced (5 = redirect forced 10 seconds after session ends)    
var forceRedirect = 5;
//time session started
var pageRequestTime = new Date();
//session timeout length
var timeoutLength = sessionLength*1000;
//force redirect to log in page length (session timeout plus 10 seconds)
var forceRedirectLength = timeoutLength + (forceRedirect*1000);

function checkSessionTime(){
	//get time now
	var timeNow = new Date();
	//event create countdown ticker variable declaration
	//var countdownTickerEvent;  
	 
	//difference between time now and time session started variable declartion
	var timeDifference = 0;
	 
	timeDifference = timeNow - pageRequestTime;
		 
	if (timeDifference > forceRedirectLength)
		{   
		   //clear (stop) checksession event
			clearInterval(checkSessionTimeEvent);
			//force relocation
			window.location="login.php?hisid=<?php echo $sid; ?>&cat=signout";
		}
}

//event to check session time left (times 1000 to convert seconds to milliseconds)
//checkSessionTimeEvent = setInterval("checkSessionTime()",10*1000);

// End Kiem tra thoi gian cua session

(function( $ ) {
	$(".tooltips").tooltip('hide');
	
	$( "#hv_lichtiephocvien" ).popover({trigger: 'hover', content: "<table  border=0 cellpadding=3 cellspacing=0 style='font-size:12px;width:240px;margin:-10px 0 10px -5px;' align=left>"+
		"<tr><td style='color:#0195df;font-weight:bold;'><?php echo "Học kỳ $hk - Năm Học $namtu-$namden"; ?></td></tr>" +
		"<tr><td style='color:#949494;'><em><?php echo "Bắt đầu từ $ngaybatdau";?></em></td></tr>" +
		"<tr><td style='color:#0195df;font-weight:bold;'><span style='float:left;'>Thứ hai</span> <span class='ui-icon ui-icon-arrowthick-1-e' style='float:left;'></span> Thứ sáu</td></tr>" +
		"<tr><td><span class='ui-icon ui-icon-triangle-1-e' style='float:left;'></span> Sáng: 8g00 - 11g00</td></tr>" +
		"<tr><td><span class='ui-icon ui-icon-triangle-1-e' style='float:left;'></span> Chiều: 13g30 - 16g30</td></tr>" +
		"<tr><td><span class='ui-icon ui-icon-triangle-1-e' style='float:left;'></span> Tối: 17g00 - 18g30</td></tr>" +
		"<tr><td style='color:#0195df;margin:0 0px 20px 0;'>ĐT: <b>38-637-318</b> hoặc <b>38647256 - 526</b></td></tr>" +
		"</table>"
		
		
	});
	
	$( "#hv_processing_diglog" ).dialog({
		resizable: false,
		autoOpen: false,
		width:250, height:120,
		modal: true		
	});
	
	$( "#hv_index_dialog_msgbox" ).dialog({
		resizable: false,
		autoOpen: false,
		width:280, height:180,
		modal: true,
		buttons: {
			"Đóng": function() {
				$( this ).dialog( "close" );
			}
		}
	});
	
		$.widget( "ui.combobox", {
			_create: function() {
				var self = this,
					select = this.element.hide(),
					selected = select.children( ":selected" ),
					value = selected.val() ? selected.text() : "";
				var input = this.input = $( "<input>" )
					.insertAfter( select )
					.val( value )
					.autocomplete({
						delay: 0,
						minLength: 0,
						source: function( request, response ) {
							var matcher = new RegExp( $.ui.autocomplete.escapeRegex(request.term), "i" );
							response( select.children( "option" ).map(function() {
								var text = $( this ).text();
								if ( this.value && ( !request.term || matcher.test(text) ) )
									return {
										label: text.replace(
											new RegExp(
												"(?![^&;]+;)(?!<[^<>]*)(" +
												$.ui.autocomplete.escapeRegex(request.term) +
												")(?![^<>]*>)(?![^&;]+;)", "gi"
											), "<strong>$1</strong>" ),
										value: text,
										option: this
									};
							}) );
						},
						select: function( event, ui ) {
							ui.item.option.selected = true;
							self._trigger( "selected", event, {
								item: ui.item.option
							});
						},
						change: function( event, ui ) {
							if ( !ui.item ) {
								var matcher = new RegExp( "^" + $.ui.autocomplete.escapeRegex( $(this).val() ) + "$", "i" ),
									valid = false;
								select.children( "option" ).each(function() {
									if ( $( this ).text().match( matcher ) ) {
										this.selected = valid = true;
										return false;
									}
								});
								if ( !valid ) {
									// remove invalid value, as it didn't match anything
									$( this ).val( "" );
									select.val( "" );
									input.data( "autocomplete" ).term = "";
									return false;
								}
							}
						}
					})
					.addClass( "ui-widget ui-widget-content ui-corner-left" );

				input.data( "autocomplete" )._renderItem = function( ul, item ) {
					return $( "<li></li>" )
						.data( "item.autocomplete", item )
						.append( "<a>" + item.label + "</a>" )
						.appendTo( ul );
				};

				this.button = $( "<button type='button'>&nbsp;</button>" )
					.attr( "tabIndex", -1 )
					.attr( "title", "Show All Items" )
					.insertAfter( input )
					.button({
						icons: {
							primary: "ui-icon-triangle-1-s"
						},
						text: false
					})
					.removeClass( "ui-corner-all" )
					.addClass( "ui-corner-right ui-button-icon" )
					.click(function() {
						// close if already visible
						if ( input.autocomplete( "widget" ).is( ":visible" ) ) {
							input.autocomplete( "close" );
							return;
						}

						// work around a bug (likely same cause as #5265)
						$( this ).blur();

						// pass empty string as value to search for, displaying all results
						input.autocomplete( "search", "" );
						input.focus();
					});
			},

			destroy: function() {
				this.input.remove();
				this.button.remove();
				this.element.show();
				$.Widget.prototype.destroy.call( this );
			}
		});
})( jQuery );
</script>

<?php 
if (isset ($db_conn))
	oci_close($db_conn);
?>