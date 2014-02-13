<?php
//ini_set('session.gc_maxlifetime', 1000);
if (isset($_REQUEST["hisid"]))
{
	$sid = $_REQUEST["hisid"];
	session_id($sid);
	session_start();
}

//echo ini_get('session.gc_maxlifetime');

if (!isset($_SESSION['uidloginPortal'])){
	header("Location: login.php");
	die('Truy cập bất hợp pháp'); 
}
else
{
	include "libs/connect.php";
	$usr = base64_decode($_SESSION["uidloginPortal"]);
	$pass = base64_decode($_SESSION["pidloginPortal"]);
	$link = $_REQUEST["l"];
	
	$_SESSION['IsAuthorized'] = true;
	
	$sqlstr="SELECT username, first_login, email
	FROM nhan_su 
	WHERE upper(username)=upper('".str_replace("'","''",$usr)."') 
	AND password='".str_replace("'","''",$pass)."'";
	//echo '<script>alert("'.$sqlstr.'");</script>';
	$oci_pa = oci_parse($db_conn,$sqlstr); //gan cau query
	oci_execute($oci_pa);
	$result=oci_fetch_all($oci_pa, $kt);//lay du lieu  
	oci_free_statement($oci_pa);
	
	if ($result==0) {
		die('Truy cập bất hợp pháp ');
	}
	
	$first_login = $kt["FIRST_LOGIN"][0];
	$email = $kt["EMAIL"][0];
}

// Cập nhật chức năng theo username
$sqlstr="SELECT DISTINCT f.fk_ma_chuc_nang CHUC_NANG
FROM nhan_su n, ct_nhom_nhan_su ct, ct_nhom_nguoi_dung_portal f
WHERE upper(n.username)=upper('$usr')
AND n.id=ct.fk_id_ns
AND ct.fk_ma_nhom = f.fk_ma_nhom";

$oci_pa = oci_parse($db_conn,$sqlstr); oci_execute($oci_pa); $n=oci_fetch_all($oci_pa, $result); oci_free_statement($oci_pa);

for ($i = 0; $i < $n; $i++)
{
	$a .= 'F'.$result["CHUC_NANG"][$i].'=1&';
}
//$a = "F012=1&F011=1";
parse_str($a);

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Phòng Đào Tạo Sau Đại Học</title>

</head>

<link href="../js/ui-1.9.2/css/pepper-grinder/jquery-ui-1.9.2.custom.min.css" rel="stylesheet" type="text/css"/>

<link href="css/pgs.css" rel="stylesheet" type="text/css"/>

<link href="../datatable/media/css/jquery.dataTables_themeroller.css" rel="stylesheet" type="text/css"/>
<link href="../datatable/media/css/jquery.dataTables.css" rel="stylesheet" type="text/css"/>

<script src="../js/jquery-1.8.3.min.js"></script>

<script src="../js/jshashtable-2.1.js"></script>
<script src="../js/jquery.numberformatter-1.2.2.min.js"></script>

<script type="text/javascript" src="../js/autoNumeric-master/autoNumeric-2.0-BETA.js"></script>

<script src="../js/ui-1.9.2/js/jquery-ui-1.9.2.custom.min.js"></script>

<script src="../js/jquery.cookie.js"></script>
<script src="../js/jquery.placeholder-1.1.9.js"></script>
<script src="../js/jquery.maskedinput-1.3.min.js"></script>
<script src="../datatable/media/js/jquery.dataTables.min.js"></script>

<script src="../ckeditor/ckeditor.js"></script>
<script src="../ckeditor/adapters/jquery.js"></script>
  
<script src="../js/session.warning.js"></script>

<script src="../js/jquery.form.js"></script>
<script src="../js/jQuery.download.js"></script>
<script src="../js/pgs.js"></script>

<!-- fancybox -->
	<!-- Add mousewheel plugin (this is optional) -->
	<script type="text/javascript" src="../fancybox/lib/jquery.mousewheel-3.0.6.pack.js"></script>

	<!-- Add fancyBox main JS and CSS files -->
	<script type="text/javascript" src="../fancybox/source/jquery.fancybox.js?v=2.1.5"></script>
	<link rel="stylesheet" type="text/css" href="../fancybox/source/jquery.fancybox.css?v=2.1.5" media="screen" />

	<!-- Add Button helper (this is optional) -->
	<link rel="stylesheet" type="text/css" href="../fancybox/source/helpers/jquery.fancybox-buttons.css?v=1.0.5" />
	<script type="text/javascript" src="../fancybox/source/helpers/jquery.fancybox-buttons.js?v=1.0.5"></script>

	<!-- Add Thumbnail helper (this is optional) -->
	<link rel="stylesheet" type="text/css" href="../fancybox/source/helpers/jquery.fancybox-thumbs.css?v=1.0.7" />
	<script type="text/javascript" src="../fancybox/source/helpers/jquery.fancybox-thumbs.js?v=1.0.7"></script>

	<!-- Add Media helper (this is optional) -->
	<script type="text/javascript" src="../fancybox/source/helpers/jquery.fancybox-media.js?v=1.0.6"></script>
<!-- end fancybox -->
<style>
	
	#tabs {}
	#tabs li .ui-icon-close { float: left; margin: 0.4em 0.2em 0 0; cursor: pointer; }

</style>

<body style="font-family:Arial, Helvetica, sans-serif" onload="changeHashOnLoad(); initSessionMonitor(); ">
<div id="container">
<?php include('header.php'); ?>
<div id="gutter"></div>

<div class="banner" >
	<div align=left >
		<img src="../images/banner-sdh-gv.jpg" />
	</div>
</div>

<div id="col1">
    <script type="text/javascript">
	var tabkhcnloaded = false;
	
	function loadTabKHCN() {
		var activeTabIdx = $('#tabs').tabs('option','active');
		var activeTabID = $('#tabs > div').eq(activeTabIdx).attr('id');
	
		if (activeTabID=='tabs-3' && !tabkhcnloaded){
			/*
			xreq = $.ajax({
			  type: 'POST', dataType: "html",
			  url: 'khcn/khcn_thuyetminhdtkhcn.php?<?php echo "hisid=$sid" ?>',
			  success: function(data) {
				tabkhcnloaded = true;
				$('#tabs-3').html(data);
			  }
			});
			*/
		}
	}
	
	$(function() {
		var $tab_title_input = $( "#tab_title"),
			$tab_content_input = $( "#tab_content" );
		var tab_counter = 4;
		var tab_current;
		var tabOpened=new Array();
		var tabNameClick = '';
		
		// tabs init with a custom tab template and an "add" callback filling in the content
		var $tabs = $( "#tabs").tabs({
			tabTemplate: "<li><a href='#{href}'>#{label}</a> <span class='ui-icon ui-icon-close'>Remove Tab</span></li>",
			add: function( event, ui ) {
				 tabOpened[tabNameClick] = ui.panel.id;
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
					loadTabKHCN();
				}
			},
			cookie: {
				// store cookie for a day, without, it would be a session cookie
				expires: 30
			},
			cache: true
		});
		
		loadTabKHCN();
		
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
		
		var namespace; 
		namespace = {
			addTab_ns : function(pkey, ptabname, piconname, pcurrenttab, plink) {
				tabNameClick = pkey;
				addTab(tabNameClick,"<img border='0' width='12' height='12' src='icons/'"+piconname+" /> "+ptabname, plink);
				tab_current = pcurrenttab;
			},
			get_tabOpened: function () {
				return tabOpened;
			}
		};
		window.ns = namespace;
		
		<?php
		if (isset($F016))
		{			
		?>
		$("#tracuuKinhPhiTHTNKhoa").click(function() {
			tabNameClick = 'tracuuKinhPhiTHTNKhoa';					
			addTab(tabNameClick,"<img border='0' width='12' height='12' src='icons/kinhphithtnkhoa.png' /> Kinh phí TH-TN Khoa", "khoa/khoa_kinhphithtn.php?hisid=<?php echo $sid;?>");
			tab_current = 0;
		});
		<?php
		}
		
		//Phan bo mon hoc cho bo mon
		if (isset($F113))
		{			
		?>
		$("#PhanBoCBGD_Bomon").click(function() {
			
			tabNameClick = 'PhanBoCBGD_Bomon';					
			addTab(tabNameClick,"<img border='0' width='12' height='12' src='icons/Document-icon.png' /> Phân công CBGD - cấp Bộ môn", "front.php/tkb/phanbo/previewbomon?hisid=<?php echo $sid;?>");
			tab_current = 0;
		});
		<?php
		}
		
		//Phan bo mon hoc cho xem tat ca
		if (isset($F114))
		{			
		?>
		$("#PhanBoCBGD_All").click(function() {
			
			tabNameClick = 'PhanBoCBGD_All';					
			addTab(tabNameClick,"<img border='0' width='12' height='12' src='icons/Document-icon.png' /> Xem phân công CBGD", "front.php/tkb/phanbo/previewall?hisid=<?php echo $sid;?>");
			tab_current = 0;
		});
		<?php
		}
		
		//Xem de cuong
		if (isset($F115))
		{			
		?>
		$("#XemDeCuong_All").click(function() {
			
			tabNameClick = 'XemDeCuong_All';					
			addTab(tabNameClick,"<img border='0' width='12' height='12' src='icons/Document-icon.png' /> Xem đề cương", "front.php/index/index/decuong?hisid=<?php echo $sid;?>");
			tab_current = 0;
		});
		<?php
		}
		
		//Chuc nang reset password
		if (isset($F116))
		{			
		?>
		$("#ResetPassword_All").click(function() {
			
			tabNameClick = 'ListUser_All';					
			addTab(tabNameClick,"<img border='0' width='12' height='12' src='icons/Document-icon.png' />Reset password", "front.php/admin/system/listuser?hisid=<?php echo $sid;?>");
			tab_current = 0;
		});
		<?php
		}
		
		//Chuc nang xem danh sach tinh trang hoc vu
		if (isset($F117))
		{
		?>
		$("#TienTrinhHoSo_All").click(function() {
			tabNameClick = 'TienTrinhHoSo_All';					
			addTab(tabNameClick,"<img border='0' width='12' height='12' src='icons/Service-icon.png' />Theo dõi xử lý hồ sơ", "front.php/phongbankhoa/hoso/tientrinh?hisid=<?php echo $sid;?>");
			tab_current = 0;
		});
		<?php
		}
		
		//Xem thoi khoa bieu
		if (isset($F118))
		{			
		?>
		$("#KhoaXemTKB_All").click(function() {
			
			tabNameClick = 'KhoaXemTKB_All';					
			addTab(tabNameClick,"<img border='0' width='12' height='12' src='icons/Document-icon.png' /> Xem TKB", "front.php/tkb/phanbo/previewall?hisid=<?php echo $sid;?>");
			tab_current = 0;
		});
		<?php
		}
		
		if (isset($F012))
		{			
		?>
		$("#tracuuKinhPhiTHTN").click(function() {
			tabNameClick = 'tracuuKinhPhiTHTN';					
			addTab(tabNameClick,"<img border='0' width='12' height='12' src='icons/kinhphithtn.png' /> Kinh phí TH-TN GV", "gv/gv_kinhphithtn.php?hisid=<?php echo $sid;?>");
			tab_current = 0;
		});
		<?php
		}
		
		if (isset($F008))
		{			
		?>
		$("#tracuuTKB").click(function() {
			tabNameClick = 'tracuuTKB';					
			addTab(tabNameClick,"<img border='0' width='12' height='12' src='icons/schedule-icon.png' /> Thời Khóa Biểu", "gv/gv_tkb.php?hisid=<?php echo $sid;?>");
			tab_current = 0;
		});
		<?php
		}
		
		if (isset($F009))
		{			
		?>
		$("#tracuuLichThi").click(function() {
			tabNameClick = 'tracuuLichThi';					
			addTab(tabNameClick,"<img border='0' width='12' height='12' src='icons/lichthi.png' /> Lịch Thi", "gv/gv_lichthi.php?hisid=<?php echo $sid;?>");
			tab_current = 0;
		});
		<?php
		}
		
		if (isset($F010))
		{			
		?>
		$("#tracuuDSLop").click(function() {
			tabNameClick = 'tracuuDSLop';					
			addTab(tabNameClick,"<img border='0' width='12' height='12' src='icons/dslop.png' /> Danh sách Lớp", "gv/gv_dslop.php?hisid=<?php echo $sid;?>");
			tab_current = 0;
		});
		<?php
		}
		
		if (isset($F011))
		{			
		?>
		$("#tracuuKLGD").click(function() {
			tabNameClick = 'tracuuKLGD';					
			addTab(tabNameClick,"<img border='0' width='12' height='12' src='icons/klgd16.png' /> Khối lượng giảng dạy", "gv/gv_klgd.php?hisid=<?php echo $sid;?>");
			tab_current = 0;
		});
		<?php
		}
		
		if (isset($F001))
		{		
		?>
		$("#ttgv").click(function() {
			tabNameClick = 'ttgv';					
			addTab(tabNameClick,"<img border='0' width='12' height='12' src='icons/gv.png' /> Lý lịch cá nhân", "gv/gv_ttgv.php?hisid=<?php echo $sid;?>");
			tab_current = 2;
		});
		<?php
		}
		
		if (isset($F001))
		{			
		?>
		$("#lvnc").click(function() {
			tabNameClick = 'lvnc';
			addTab(tabNameClick,"<img border='0' width='12' height='12' src='icons/research.png' /> Hướng nghiên cứu", "gv/gv_linhvucnc.php?hisid=<?php echo $sid;?>");	
			tab_current = 1;
		});
		<?php
		}
		
		if (isset($F001))
		{			
		?>
		$("#lvcm").click(function() {
			tabNameClick = 'lvcm';
			addTab(tabNameClick,"<img border='0' width='12' height='12' src='icons/research1.png' /> Lĩnh vực chuyên môn", "gv/gv_linhvucchuyenmon.php?hisid=<?php echo $sid;?>");	
			tab_current = 1;
		});
		<?php
		}
		
		if (isset($F001))
		{			
		?>
		$("#nckh").click(function() {
			tabNameClick = 'nckh';
			addTab(tabNameClick,"<img border='0' width='12' height='12' src='icons/dtdannckh.png' /> Đề tài, Dự án, NCKH", "gv/gv_nckh.php?hisid=<?php echo $sid;?>");
			tab_current = 1;
		});
		<?php
		}
		
		if (isset($F001))
		{			
		?>
		$("#ctkh").click(function() {
			tabNameClick = 'ctkh';
			addTab(tabNameClick,"<img border='0' width='12' height='12' src='icons/baibao.png' /> Bài báo Tạp chí/H.Nghị K.Học", "gv/gv_bbaotchi.php?hisid=<?php echo $sid;?>");
			tab_current = 1;
		});
		<?php
		}
		
		if (isset($F001))
		{			
		?>
		$("#sach").click(function() {
			tabNameClick = 'sach';
			addTab(tabNameClick,"<img border='0' width='12' height='12' src='icons/sach.png' /> Sách, Tài liệu tham khảo", "gv/gv_sach.php?hisid=<?php echo $sid;?>");
			tab_current = 1;
		});
		<?php
		}
		
		if (isset($F001))
		{			
		?>
		$("#hdansv").click(function() {
			tabNameClick = 'hdansv';
			addTab(tabNameClick,"<img border='0' width='12' height='12' src='icons/graduatedTHs.png' /> Hướng dẫn sinh viên, học viên, ncs", "gv/gv_huongdan_sv.php?hisid=<?php echo $sid;?>");
			tab_current = 1;
		});
		<?php
		}
		
		if (isset($F001))
		{			
		?>
		$("#hdanths").click(function() {
			tabNameClick = 'hdanths';
			addTab(tabNameClick,"<img border='0' width='12' height='12' src='icons/graduatedTHs.png' /> DS hướng dẫn luận văn Thạc Sĩ", "gv/gv_hdanths.php?hisid=<?php echo $sid;?>");
			tab_current = 1;
		});
		<?php
		}
		
		if (isset($F001))
		{			
		?>
		$("#hdants").click(function() {
			tabNameClick = 'hdants';
			addTab(tabNameClick,"<img border='0' width='12' height='12' src='icons/graduatedTS.png' /> DS hướng dẫn luận án Tiến Sĩ", "gv/gv_hdants.php?hisid=<?php echo $sid;?>");
			tab_current = 1;
		});
		<?php
		}
		
		if (isset($F001))
		{			
		?>
		$("#knqldg").click(function() {
			tabNameClick = 'knqldg';
			addTab(tabNameClick,"<img border='0' width='12' height='12' src='icons/KN-QL-DG-icon-16.png' /> Kinh nghiệm QL, ĐG KH&CN", "gv/gv_kinhnghiem_ql_dg_khcn.php?hisid=<?php echo $sid;?>");
			tab_current = 1;
		});
		<?php
		}
		
		if (isset($F001))
		{			
		?>
		$("#chuyengiakhac").click(function() {
			tabNameClick = 'chuyengiakhac';
			addTab(tabNameClick,"<img border='0' width='12' height='12' src='icons/chuyen-gia-khac-icon-16.png' /> GT chuyên gia khác", "gv/gv_gioithieu_chuyengiakhac_khcn.php?hisid=<?php echo $sid;?>");
			tab_current = 1;
		});
		<?php
		}
		
		if (isset($F013))
		{			
		?>
		$("#changePass").click(function() {
			tabNameClick = 'changepass';
			addTab(tabNameClick,"<img border='0' width='12' height='12' src='icons/user-password-icon.png' /> Thay đổi mật khẩu", "changepassfrm.php?hisid=<?php echo $sid;?>");
			tab_current = 3;
		});
		<?php
		}
		
		if (isset($F101))
		{		
		?>
		$("#tracuuDSHocVien").click(function() {
			tabNameClick = 'tracuuDSHocVien';					
			addTab(tabNameClick,"<img border='0' width='12' height='12' src='icons/dshocvien.png' /> Danh sách HV Khóa", "khoa/khoa_dshocvien.php?hisid=<?php echo $sid;?>");
			tab_current = 0;
		});
		<?php
		}
		
		if (isset($F103))
		{
		?>
		$("#DSHocVienDKMH").click(function() {
			tabNameClick = 'DSHocVienDKMH';					
			addTab(tabNameClick,"<img border='0' width='12' height='12' src='icons/dshocvien.png' /> Danh sách HV ĐKMH", "khoa/khoa_dshocvien_dkmh.php?hisid=<?php echo $sid;?>");
			tab_current = 0;
		});
		<?php
		}
		
		if (isset($F104))
		{		
		?>
		$("#DSHocVienTNdothoc").click(function() {
			tabNameClick = 'DSHocVienTNdothoc';					
			addTab(tabNameClick,"<img border='0' width='12' height='12' src='icons/dshocvien.png' /> Danh sách HV Tốt Nghiệp", "khoa/khoa_dshocvien_tn_dothoc.php?hisid=<?php echo $sid;?>");
			tab_current = 0;
		});
		<?php
		}
		
		if (isset($F105))
		{		
		?>
		$("#LichThiHocKy").click(function() {
			tabNameClick = 'LichThiHocKy';					
			addTab(tabNameClick,"<img border='0' width='12' height='12' src='icons/lichthi.png' /> Lịch Thi Học Kỳ", "khoa/khoa_lichthihocky.php?hisid=<?php echo $sid;?>");
			tab_current = 0;
		});
		<?php
		}
		
		if (isset($F102))
		{		
		?>
		$("#tracuuDSNCS").click(function() {
			tabNameClick = 'tracuuDSNCS';					
			addTab(tabNameClick,"<img border='0' width='12' height='12' src='icons/dsncs.png' /> Danh sách NCS", "khoa/khoa_dsncs.php?hisid=<?php echo $sid;?>");
			tab_current = 0;
		});
		<?php
		}
		
		if (isset($F014))
		{	
		?>
		$("#accountInfo").click(function() {
			tabNameClick = 'accountInfo';
			addTab(tabNameClick,"<img border='0' width='12' height='12' src='icons/personal-information-icon.png' /> Thông tin tài khoản", "accountinfofrm.php?hisid=<?php echo $sid;?>");
			tab_current = 3;
		});
		<?php
		}
		?>
		
		<?php
		if (isset($F053)){
		?>
		$("#confInfo").click(function() {
			tabNameClick = 'confInfo';					
			addTab(tabNameClick,"<img border='0' width='12' height='12' src='icons/work.png' /> Cấu hình hệ thống", "phong/phong_cauhinhhethong.php?hisid=<?php echo $sid;?>");
			tab_current = 3;
		});
		<?php
		}
		?>
		
		<?php
		if (isset($F015))
		{	
		?>
		$("#tracuuDSHocVienKhoa").click(function() {
			tabNameClick = 'tracuuDSHocVienKhoa';
			addTab(tabNameClick,"<img border='0' width='12' height='12' src='icons/dshocvientheokhoa.png' /> Danh sách HV", "phong/phong_dshocvienkhoa.php?hisid=<?php echo $sid;?>");
			tab_current = 0;
		});
		<?php
		}
		
		if (isset($F018))
		{	
		?>
		$("#phongDSHocVienTN_Khoa").click(function() {
			tabNameClick = 'phongDSHocVienTN_Khoa';
			addTab(tabNameClick,"<img border='0' width='12' height='12' src='icons/dshocvientheokhoa.png' /> Danh sách HV TN", "phong/phong_dshocvien_tn.php?hisid=<?php echo $sid;?>");
			tab_current = 0;
		});
		<?php
		}
		
		if (isset($F019))
		{	
		?>
		$("#phongDSHocVienDKMH_Khoa").click(function() {
			tabNameClick = 'phongDSHocVienDKMH_Khoa';
			addTab(tabNameClick,"<img border='0' width='12' height='12' src='icons/dshocvientheokhoa.png' /> Danh sách HV ĐKMH", "phong/phong_dshocvien_dkmh.php?hisid=<?php echo $sid;?>");
			tab_current = 0;
		});
		<?php
		}

		if (isset($F020))
		{	
		?>
		$("#phongDSNCS_Khoa").click(function() {
			tabNameClick = 'phongDSNCS_Khoa';
			addTab(tabNameClick,"<img border='0' width='12' height='12' src='icons/dshocvientheokhoa.png' /> Danh sách NCS", "phong/phong_dsncs_khoa.php?hisid=<?php echo $sid;?>");
			tab_current = 0;
		});
		<?php
		}
		
		if (isset($F021))
		{	
		?>
		$("#phongNhanYCHVu").click(function() {
			tabNameClick = 'phongNhanYCHVu';
			addTab(tabNameClick,"<img border='0' width='12' height='12' src='icons/ychvu-icon.png' /> Quản lý yêu cầu học vụ", "phong/phong_ychv_tiepnhan.php?hisid=<?php echo $sid;?>");
			tab_current = 0;
		});
		<?php
		}
		
		if (isset($F002))
		{
		?>
		$("#printHuongDanInLLKH").click(function() {
			tabNameClick = 'printHuongDanInLLKH';					
			addTab(tabNameClick,"<img border='0' width='12' height='12' src='icons/print-icon.png' /> Hướng dẫn in biểu mẫu", "gv/gv_print_huongdan.php?hisid=<?php echo $sid;?>");
			tab_current = 1;
		});
		<?php
		}
		
		if (isset($F002))
		{			
		?>
		$("#printTTKH").click(function() {
			tabNameClick = 'printTTKH';					
			addTab(tabNameClick,"<img border='0' width='12' height='12' src='icons/print-icon.png' /> In thông tin khoa học", "gv/gv_print_ttkh.php?hisid=<?php echo $sid;?>");
			tab_current = 1;
		});
		<?php
		}
		
		if (isset($F002))
		{			
		?>
		$("#printTTGV_LLKH_MAU_BO").click(function() {
			tabNameClick = 'printTTGV_LLKH_MAU_BO';					
			addTab(tabNameClick,"<img border='0' width='12' height='12' src='icons/print-icon.png' /> In LLKH mẫu Bộ", "gv/gv_print_llkh_mau_Bo.php?hisid=<?php echo $sid;?>");
			tab_current = 1;
		});
		<?php
		}
		
		if (isset($F001))
		{			
		?>
		$("#bangsangche").click(function() {
			tabNameClick = 'bangsangche';					
			addTab(tabNameClick,"<img border='0' width='12' height='12' src='icons/Patent_icon.png' /> Bằng phát minh, sáng chế", "gv/gv_bangsangche.php?hisid=<?php echo $sid;?>");
			tab_current = 1;
		});
		<?php
		}
		
		if (isset($F002))
		{			
		?>
		$("#printTTGV_LLKH_MAU_R04").click(function() {
			tabNameClick = 'printTTGV_LLKH_MAU_R04';
			addTab(tabNameClick,"<img border='0' width='12' height='12' src='icons/print-icon.png' /> In LLKH mẫu ĐHQG (R04)", "gv/gv_print_llkh_mau_r04.php?hisid=<?php echo $sid;?>");
			tab_current = 1;
		});
		<?php
		}
		
		if (isset($F002))
		{			
		?>
		$("#printTTGV_LLKH_MAU_BK").click(function() {
			tabNameClick = 'printTTGV_LLKH_MAU_BK';
			addTab(tabNameClick,"<img border='0' width='12' height='12' src='icons/print-icon.png' /> In LLKH mẫu Bách Khoa", "gv/gv_print_llkh_mau_truong_bk.php?hisid=<?php echo $sid;?>");
			tab_current = 1;
		});
		<?php
		}
		
		if (isset($F002))
		{			
		?>
		$("#printTTGV_LLKH_MAU_KHCN_CNDT").click(function() {
			tabNameClick = 'printTTGV_LLKH_MAU_KHCN_CNDT';
			addTab(tabNameClick,"<img border='0' width='12' height='12' src='icons/print-icon.png' /> In LLKH mẫu CNĐT", "gv/gv_print_llkh_mau_nckh_cndt.php?hisid=<?php echo $sid;?>");
			tab_current = 1;
		});
		<?php
		}
		
		if (isset($F002))
		{			
		?>
		$("#printTTGV_LLKH_MAU_KHCN_TGDT").click(function() {
			tabNameClick = 'printTTGV_LLKH_MAU_KHCN_TGDT';
			addTab(tabNameClick,"<img border='0' width='12' height='12' src='icons/print-icon.png' /> In LLKH mẫu TGĐT", "gv/gv_print_llkh_mau_nckh_tgdt.php?hisid=<?php echo $sid;?>");
			tab_current = 1;
		});
		<?php
		}
		
		if (isset($F002))
		{			
		?>
		$("#printTTGV_LLKH_MAU_KHCN_BO").click(function() {
			tabNameClick = 'printTTGV_LLKH_MAU_KHCN_BO';
			addTab(tabNameClick,"<img border='0' width='12' height='12' src='icons/print-icon.png' /> In LLKH mẫu Chuyên gia KHCN - Bộ", "gv/gv_print_llkh_mau_cgkhcn_bo.php?hisid=<?php echo $sid;?>");
			tab_current = 1;
		});
		<?php
		}
		
		if (isset($F024))
		{			
		?>
		$("#printTTGV_LLKH_DS").click(function() {
			tabNameClick = 'printTTGV_LLKH_DS';
			addTab(tabNameClick,"<img border='0' width='12' height='12' src='icons/profile-search-icon-16.png' /> Quản lý LLKH", "gv/gv_in_bm_gv.php?hisid=<?php echo $sid;?>");
			tab_current = 1;
		});
		<?php
		}
		
		if (isset($F001))
		{			
		?>
		$("#qtdt").click(function() {
			tabNameClick = 'qtdt';					
			addTab(tabNameClick,"<img border='0' width='12' height='12' src='icons/training-icon.png' /> Quá trình đào tạo", "gv/gv_qtdt.php?hisid=<?php echo $sid;?>");
			tab_current = 2;
		});
		<?php
		}
		
		if (isset($F001))
		{			
		?>
		$("#giaithuong").click(function() {
			tabNameClick = 'giaithuong';					
			addTab(tabNameClick,"<img border='0' width='12' height='12' src='icons/award-icon.png' /> Giải thưởng KHCN", "gv/gv_giaithuong.php?hisid=<?php echo $sid;?>");
			tab_current = 1;
		});
		<?php
		}
		
		if (isset($F001))
		{			
		?>
		$("#giaiphaphuuich").click(function() {
			tabNameClick = 'giaiphaphuuich';					
			addTab(tabNameClick,"<img border='0' width='12' height='12' src='icons/huuich-icon.png' /> Giải pháp hữu ích", "gv/gv_giaiphaphuuich.php?hisid=<?php echo $sid;?>");
			tab_current = 1;
		});
		<?php
		}
		
		if (isset($F001))
		{			
		?>
		$("#thamgiact").click(function() {
			tabNameClick = 'thamgiact';					
			addTab(tabNameClick,"<img border='0' width='12' height='12' src='icons/thamgiact-icon.png' /> Tham gia chương trình", "gv/gv_thamgiact.php?hisid=<?php echo $sid;?>");
			tab_current = 1;
		});
		<?php
		}
		
		if (isset($F001))
		{			
		?>
		$("#ungdungthuctien").click(function() {
			tabNameClick = 'ungdungthuctien';					
			addTab(tabNameClick,"<img border='0' width='12' height='12' src='icons/ungdungthuctien-icon.png' /> Ứng dụng thực tiễn", "gv/gv_ungdungthuctien.php?hisid=<?php echo $sid;?>");
			tab_current = 1;
		});
		<?php
		}
		
		if (isset($F001))
		{			
		?>
		$("#thanhtuukhcn").click(function() {
			tabNameClick = 'thanhtuukhcn';					
			addTab(tabNameClick,"<img border='0' width='12' height='12' src='icons/Archive-icon.png' /> Thành tựu hoạt động KH&CN", "gv/gv_thanhtuukhcn.php?hisid=<?php echo $sid;?>");
			tab_current = 1;
		});
		<?php
		}
		
		if (isset($F001))
		{
		?>
		$("#thamgiahhtchn").click(function() {
			tabNameClick = 'thamgiahhtchn';					
			addTab(tabNameClick,"<img border='0' width='12' height='12' src='icons/thamgiact-icon.png' /> Tham gia hiệp hội", "gv/gv_thamgia_hh_tc_hn.php?hisid=<?php echo $sid;?>");
			tab_current = 1;
		});
		<?php
		}
		
		if (isset($F001))
		{			
		?>
		$("#ngoaingu").click(function() {
			tabNameClick = 'ngoaingu';					
			addTab(tabNameClick,"<img border='0' width='12' height='12' src='icons/thamgiact-icon.png' /> Trình độ ngoại ngữ", "gv/gv_ngoaingu.php?hisid=<?php echo $sid;?>");
			tab_current = 2;
		});
		<?php
		}
		
		if (isset($F001))
		{			
		?>
		$("#qtct").click(function() {
			tabNameClick = 'qtct';
			addTab(tabNameClick,"<img border='0' width='12' height='12' src='icons/work.png' /> Thời gian công tác", "gv/gv_qtct.php?hisid=<?php echo $sid;?>");
			tab_current = 2;
		});
		<?php
		}
		
		if (isset($F001))
		{
		?>
		$("#thamgiatruongvien").click(function() {
			tabNameClick = 'thamgiatruongvien';
			addTab(tabNameClick,"<img border='0' width='12' height='12' src='icons/thamgiact-icon.png' /> Tham gia trường viện", "gv/gv_thamgiatruongvien.php?hisid=<?php echo $sid;?>");
			tab_current = 1;
		});
		<?php
		}		
		
		// Giay chung nhan
		// a = form || print
		if (isset($F112))
		{
		?>
		$("#PhanBoCBGD_Khoa").click(function() {
			tabNameClick = 'PhanBoCBGD_Khoa';
			//addTab(tabNameClick,"<img border='0' width='12' height='12' src='icons/Document-icon.png' /> Phân bổ CBGD", "khoa/khoa_phanbocbgd.php?a=form&hisid=<?php echo $sid;?>");
			addTab(tabNameClick,"<img border='0' width='12' height='12' src='icons/Document-icon.png' /> Phân công CBGD - cấp Khoa", "front.php/tkb/phanbo/preview?hisid=<?php echo $sid;?>");
			tab_current = 0;
		});
		<?php
		}
		if (isset($F107))
		{
		?>
		$("#GiayChungNhan_Khoa").click(function() {
			tabNameClick = 'GiayChungNhan_Khoa';
			addTab(tabNameClick,"<img border='0' width='12' height='12' src='icons/Document-icon.png' /> Giấy chứng nhận", "khoa/khoa_giaychungnhan.php?a=form&hisid=<?php echo $sid;?>");
			tab_current = 0;
		});
		<?php
		}
		if (isset($F108))
		{
		?>
		$("#GiayGioiThieu_Khoa").click(function() {
			tabNameClick = 'GiayGioiThieu_Khoa';
			addTab(tabNameClick,"<img border='0' width='12' height='12' src='icons/Document-icon.png' /> Giấy giới thiệu", "khoa/khoa_giaygioithieu.php?a=form&hisid=<?php echo $sid;?>");
			tab_current = 0;
		});
		<?php
		}
		if (isset($F109))
		{
		?>
		$("#GiayTrieuTap_Khoa").click(function() {
			tabNameClick = 'GiayTrieuTap_Khoa';
			addTab(tabNameClick,"<img border='0' width='12' height='12' src='icons/Document-icon.png' /> Giấy triệu tập", "khoa/khoa_giaytrieutap.php?a=form&hisid=<?php echo $sid;?>");
			tab_current = 0;
		});
		<?php
		}
		if (isset($F110))
		{
		?>
		$("#GiayThoiKhoaBieu_Khoa").click(function() {
			tabNameClick = 'GiayThoiKhoaBieu_Khoa';
			addTab(tabNameClick,"<img border='0' width='12' height='12' src='icons/Document-icon.png' /> Giấy TKB", "khoa/khoa_giaythoikhoabieu.php?a=form&hisid=<?php echo $sid;?>");
			tab_current = 0;
		});
		<?php
		}
		if (isset($F107))
		{
		?>
		$("#printHuongDanIn").click(function() {
			tabNameClick = 'printHuongDanIn';
			addTab(tabNameClick,"<img border='0' width='12' height='12' src='icons/print-icon.png' /> Hướng dẫn in", "khoa/khoa_print_huongdan.php?hisid=<?php echo $sid;?>");
			tab_current = 0;
		});
		<?php
		}
		if (isset($F111))
		{
		?>
		$("#QuanLyPhi_Khoa").click(function() {
			tabNameClick = 'QuanLyPhi_Khoa';
			addTab(tabNameClick,"<img border='0' width='12' height='12' src='icons/kinhphithtn.png' /> Quản lý phí", "khoa/khoa_quanlyphi.php?hisid=<?php echo $sid;?>");
			tab_current = 0;
		});
		<?php
		}
		?>
		
		// KHCN
		<?php
		if (isset($F051))
		{
		?>
		$("#khcn_dangky_tmdt").click(function() {
			tabNameClick = 'khcn_dangky_tmdt';
			addTab(tabNameClick,"<img border='0' width='12' height='12' src='icons/register-icon.png' /> Đăng ký thuyết minh đề tài", "khcn/khcn_thuyetminhdtkhcn.php?hisid=<?php echo $sid;?>");
			tab_current = 1;
		});
		<?php
		}
		if (isset($F050))
		{
		?>
		$("#khcn_quanly_tmdt").click(function() {
			tabNameClick = 'khcn_quanly_tmdt';
			addTab(tabNameClick,"<img border='0' width='12' height='12' src='icons/profile-search-icon.png' /> Quản lý TMĐT", "khcn/khcn_ql_tmdt.php?hisid=<?php echo $sid;?>");
			tab_current = 1;
		});
		<?php
		}
		?>
		
		<?php
		if (isset($F051))
		{
		?>
		$("#khcn_printHuongDanInTMDT").click(function() {
			tabNameClick = 'khcn_printHuongDanInTMDT';					
			addTab(tabNameClick,"<img border='0' width='12' height='12' src='icons/print-icon.png' /> Hướng dẫn in TMĐT", "khcn/khcn_print_huongdan_in_tmdt.php?hisid=<?php echo $sid;?>");
			tab_current = 1;
		});
		<?php
		}
		?>
		
		<?php
		if (isset($F052))
		{
		?>
		$("#khcn_thongke_khcn").click(function() {
			tabNameClick = 'khcn_thongke_khcn';					
			addTab(tabNameClick,"<img border='0' width='12' height='12' src='icons/Document-icon.png' /> Thống kê KHCN", "khcn/khcn_thongke.php?hisid=<?php echo $sid;?>");
			tab_current = 1;
		});
		<?php
		}
		?>
		// END KHCN
		
		
		
		
		$( "input:submit, button").button();
		
		
		<?php 
			switch ($link) {
				case "DangKyThuyetMinhDeTai":
					echo "$('#khcn_dangky_tmdt').click();";
					break;
			}
			
			if ($first_login==1)
			{
				echo "gv_open_msg_box('Lần đầu đăng nhập vào hệ thống hoặc chưa thay đổi mật khẩu mặc định xin quý Thầy/Cô vui lòng sử dụng chức năng <b>Thay Đổi Mật Khẩu</b> trong mục Bảo Mật <b>thay đổi mật khẩu mặc định</b> để đảm bảo an toàn dữ liệu.', 'info');";
			}
			elseif ($email=='')
			{
				echo "gv_open_msg_box('Quý Thầy/Cô vui lòng cập nhật thông tin <b>Email</b> vào <b>Thông tin tài khoản</b> ở mục Bảo mật vì Email này được dùng để khôi phục mật khẩu khi quý Thầy/Cô quên mật khẩu','info');";
			}
		?>
	}); 
</script>	
    <div class="demo" style="width:100%;">
        <div id="tabs" style="border:0;">
            <ul>
                <li><a href="#tabs-1"><strong>Sau đại học</strong></a></li>
                <li><a href="#tabs-2"><strong>Khoa học công nghệ</strong></a></li>
				<li><a href="#tabs-3"><strong>Nhân sự</strong></a></li>
                <li><a href="#tabs-4"><strong>Bảo Mật</strong></a></li>
            </ul>
            <div id="tabs-1">
				<div style="width:1024px; margin:auto;">
					<div style="width:650px; margin:0 15px 0 20px; float:left;  border-right: 3px solid #aaa;"> 
						<ul id="sortableHOCVU">
							<?php
								if (isset($F008)){
									echo "<li ><a id='tracuuTKB' href='#'><img border='0' width='48' height='48' src='icons/schedule-icon.png' /><br/>Tra Cứu TKB</a></li>";
								}
								if (isset($F009)){
									echo "<li ><a id='tracuuLichThi' href='#'><img border='0' width='48' height='48' src='icons/lichthi.png' /><br/>Tra Cứu Lịch Thi</a></li>";
								}
								if (isset($F010)){
									echo "<li ><a id='tracuuDSLop' href='#'><img border='0' width='48' height='48' src='icons/dslop.png' /><br/>Danh Sách Lớp</a></li>";
								}
								if (isset($F011)){
									echo "<li ><a id='tracuuKLGD' href='#'><img border='0' width='48' height='48' src='icons/klgd.png' /><br/>Khối Lượng<br/>Giảng Dạy</a></li>";
								}
								if (isset($F012)){
									echo "<li ><a id='tracuuKinhPhiTHTN' href='#'><img border='0' width='48' height='48' src='icons/kinhphithtn.png' /><br/>Kinh phí<br/>Thực hành - Thí Nghiệm</a></li>";
								}
								if (isset($F016)){
									echo "<li ><a id='tracuuKinhPhiTHTNKhoa' href='#'><img border='0' width='48' height='48' src='icons/kinhphithtnkhoa.png' /><br/>Kinh phí<br/>Thực hành - Thí Nghiệm Khoa</a></li>";
								}
								if (isset($F105)){
									echo "<li ><a id='LichThiHocKy' href='#'><img border='0' width='48' height='48' src='icons/lichthi.png' /><br/>Lịch thi<br/>Học kỳ</a></li>";
								}
								if (isset($F101)){
									echo "<li ><a id='tracuuDSHocVien' href='#'><img border='0' width='48' height='48' src='icons/dshocvien.png' /><br/>Danh Sách<br/>Học Viên<br/>theo Khóa</a></li>";
								}
								if (isset($F104)){
									echo "<li ><a id='DSHocVienTNdothoc' href='#'><img border='0' width='48' height='48' src='icons/dshocvien.png' /><br/>Danh Sách<br/>Học Viên<br/>Tốt nghiệp</a></li>";
								}
								if (isset($F103)){
									echo "<li ><a id='DSHocVienDKMH' href='#'><img border='0' width='48' height='48' src='icons/dshocvien.png' /><br/>Danh Sách<br/>Học Viên<br/>ĐKMH</a></li>";
								}
								if (isset($F102)){
									echo "<li ><a id='tracuuDSNCS' href='#'><img border='0' width='48' height='48' src='icons/dsncs.png' /><br/>Danh Sách<br/>Nghiên Cứu Sinh</a></li>";
								}
								if (isset($F015)){
									echo "<li ><a id='tracuuDSHocVienKhoa' href='#'><img border='0' width='48' height='48' src='icons/dshocvientheokhoa.png' /><br/>Danh Sách<br/>Học Viên</a></li>";
								}
								if (isset($F018)){
									echo "<li ><a id='phongDSHocVienTN_Khoa' href='#'><img border='0' width='48' height='48' src='icons/dshocvientheokhoa.png' /><br/>Danh Sách<br/>Học Viên<br/>Tốt nghiệp</a></li>";
								}
								if (isset($F019)){
									echo "<li ><a id='phongDSHocVienDKMH_Khoa' href='#'><img border='0' width='48' height='48' src='icons/dshocvientheokhoa.png' /><br/>Danh Sách<br/>Học Viên<br/>ĐKMH</a></li>";
								}
								if (isset($F020)){
									echo "<li ><a id='phongDSNCS_Khoa' href='#'><img border='0' width='48' height='48' src='icons/dshocvientheokhoa.png' /><br/>Danh Sách<br/>Nghiên Cứu Sinh</a></li>";
								}
								if (isset($F021)){
									echo "<li ><a id='phongNhanYCHVu' href='#'><img border='0' width='48' height='48' src='icons/ychvu-icon.png' /><br/>Quản lý<br/>Yêu cầu học vụ</a></li>";
								}
								if (isset($F112)){
									echo "<li ><a id='PhanBoCBGD_Khoa' href='#'><img border='0' width='48' height='48' src='icons/Document-icon.png' /><br/>Phân công CBGD - cấp Khoa</a></li>";
								}
								if (isset($F113)){
									echo "<li ><a id='PhanBoCBGD_Bomon' href='#'><img border='0' width='48' height='48' src='icons/Document-icon.png' /><br/>Phân công CBGD - cấp Bộ môn</a></li>";
								}
								if (isset($F114)){
									echo "<li ><a id='PhanBoCBGD_All' href='#'><img border='0' width='48' height='48' src='icons/Document-icon.png' /><br/>Xem phân công CBGD</a></li>";
								}
								if (isset($F115)){
									echo "<li ><a id='XemDeCuong_All' href='#'><img border='0' width='48' height='48' src='icons/Document-icon.png' /><br/>Danh Sách Nhận Đề Cương LV</a></li>";
								}
								if (isset($F116)){
									echo "<li ><a id='ResetPassword_All' href='#'><img border='0' width='48' height='48' src='icons/user-password-icon.png' /><br/>Reset password</a></li>";
								}
								if (isset($F117)){
									echo "<li ><a id='TienTrinhHoSo_All' href='#'><img border='0' width='48' height='48' src='icons/Service-icon.png' /><br/>Theo dõi xử lý hồ sơ</a></li>";
								}
								if (isset($F118)){
									echo "<li ><a id='KhoaXemTKB_All' href='#'><img border='0' width='48' height='48' src='icons/Document-icon.png' /><br/>Xem TKB</a></li>";
								}
								if (isset($F107)){
									echo "<li ><a id='GiayChungNhan_Khoa' href='#'><img border='0' width='48' height='48' src='icons/Document-icon.png' /><br/>Giấy chứng nhận</a></li>";
								}
								if (isset($F108)){
									echo "<li ><a id='GiayGioiThieu_Khoa' href='#'><img border='0' width='48' height='48' src='icons/Document-icon.png' /><br/>Giấy giới thiệu</a></li>";
								}
								if (isset($F109)){
									echo "<li ><a id='GiayTrieuTap_Khoa' href='#'><img border='0' width='48' height='48' src='icons/Document-icon.png' /><br/>Giấy triệu tập</a></li>";
								}
								if (isset($F110)){
									echo "<li ><a id='GiayThoiKhoaBieu_Khoa' href='#'><img border='0' width='48' height='48' src='icons/Document-icon.png' /><br/>Giấy TKB</a></li>";
								}
								if (isset($F111)){
									echo "<li ><a id='QuanLyPhi_Khoa' href='#'><img border='0' width='48' height='48' src='icons/kinhphithtn.png' /><br/>Quản lý phí</a></li>";
								}
							?>
						</ul>
					</div>
					<div style="">
						<table cellpadding="3">
						<?php
							if (isset($F107)){
								echo "<tr style='cursor:pointer;' id='printHuongDanIn' ><td><img src='icons/idea-icon-24x24.png' border=0 width=24 height=24 /></td><td ><a class='tooltips' data-toggle='tooltip' title='<b><u>Chú ý:</u> Ðọc hướng dẫn này trước khi in Giấy chứng nhận, giấy giới thiệu, giấy triệu tập, giấy tkb</b>' style='font-weight:bold;color:#0195df'>Hướng dẫn In ấn</a></td></tr>";
							}
						?>
						</table>
					</div>
					<div class=clearfloat></div>
				</div>
            </div>
            <div id="tabs-2">
				<div style="width:1024px; margin:auto;">
					<div style="width:650px; margin:0 15px 0 20px; float:left;  border-right: 3px solid #aaa;"> 
						<ul id="sortableTTCN">
							<?php
								if (isset($F051))
								{
									echo "<li ><a id='khcn_dangky_tmdt' href='#'><img border='0' width='48' height='48' src='icons/register-icon.png' /><br/>Đăng ký<br>Thuyết minh đề tài</a></li>";
								}
								
								if (isset($F050))
								{
									echo "<li ><a id='khcn_quanly_tmdt' href='#'><img border='0' width='48' height='48' src='icons/profile-search-icon.png' /><br/>Quản lý<br>Thuyết minh đề tài</a></li>";
								}
								
								if (isset($F052))
								{
									echo "<li ><a id='khcn_thongke_khcn' href='#'><img border='0' width='48' height='48' src='icons/Document-icon.png' /><br/>Thống kê</a></li>";
								}
								if (isset($F001)){
									echo "<li ><a id='lvcm' href='#'><img border='0' width='48' height='48' src='icons/research1.png' /><br/>Lĩnh vực<br/>chuyên môn</a></li>";
								}
								if (isset($F001)){
									echo "<li ><a id='lvnc' href='#'><img border='0' width='48' height='48' src='icons/research.png' /><br/>Hướng, đề tài<br/>nghiên cứu</a></li>";
								}
								if (isset($F001)){
									echo "<li ><a id='nckh' href='#'><img border='0' width='48' height='48' src='icons/dtdannckh.png' /><br/>Đề tài, Dự án,<br/>Nghiên cứu khoa học</a></li>";
								}
								if (isset($F001)){
									echo "<li ><a id='sach' href='#'><img border='0' width='48' height='48' src='icons/sach.png' /><br/>Sách, Tài liệu<br/>tham khảo</a></li>";
								}
								if (isset($F001)){
									echo "<li ><a id='ctkh' href='#'><img border='0' width='48' height='48' src='icons/baibao.png' /><br/>Bài báo Tạp chí/H.Nghị Khoa Học</a></li>";
								}
							
								if (isset($F001)){
									echo "<li ><a id='giaithuong' href='#'><img border='0' width='48' height='48' src='icons/award-icon.png' /><br/>Giải thưởng KH&CN</a></li>";
								}
								if (isset($F001)){
									echo "<li ><a id='bangsangche' href='#'><img border='0' width='48' height='48' src='icons/Patent_icon.png' /><br/>Bằng phát minh<br/>sáng chế</a></li>";
								}
								if (isset($F001)){
									echo "<li ><a id='giaiphaphuuich' href='#'><img border='0' width='48' height='48' src='icons/huuich-icon.png' /><br/>Giải pháp hữu ích</a></li>";
								}
								if (isset($F001)){
									echo "<li ><a id='ungdungthuctien' href='#'><img border='0' width='48' height='48' src='icons/ungdung-icon.png' /><br/>Ứng dụng thực tiễn</a></li>";
								}
								if (isset($F001)){
									echo "<li ><a id='thanhtuukhcn' href='#'><img border='0' width='48' height='48' src='icons/Archive-icon.png' /><br/>Thành tựu hoạt động KH&CN</a></li>";
								}
								if (isset($F001)){
									echo "<li ><a id='thamgiact' href='#'><img border='0' width='48' height='48' src='icons/thamgiact-icon.png' /><br/>Tham gia chương trình</a></li>";
								}
								if (isset($F001)){
									echo "<li ><a id='thamgiahhtchn' href='#'><img border='0' width='48' height='48' src='icons/thamgiact-icon.png' /><br/>Tham gia hiệp hội<br/>Tạp chí, hội nghị</a></li>";
								}
								if (isset($F001)){
									echo "<li ><a id='thamgiatruongvien' href='#'><img border='0' width='48' height='48' src='icons/thamgiact-icon.png' /><br/>Tham gia Trường<br/>Viện, Trung tâm NC</a></li>";
								}
								if (isset($F001)){
									echo "<li ><a id='hdansv' href='#'><img border='0' width='48' height='48' src='icons/graduatedSV.png' /><br/>Hướng dẫn<br/>Sinh viên<br/>HV Cao học, NCS</a></li>";
								}
								if (isset($F001)){
									//echo "<li ><a id='hdanths' href='#'><img border='0' width='48' height='48' src='icons/graduatedTHs.png' /><br/>DS hướng dẫn<br/>luận văn Thạc Sĩ<br/>tại Bách Khoa</a></li>";
								}
								if (isset($F001)){
									//echo "<li ><a id='hdants' href='#'><img border='0' width='48' height='48' src='icons/graduatedTS.png' /><br/>DS hướng dẫn<br/>luận án Tiến Sĩ<br/>tại Bách Khoa</a></li>";
								}
								if (isset($F001)){
									echo "<li ><a id='knqldg' href='#'><img border='0' width='48' height='48' src='icons/KN-QL-DG-icon.png' /><br/>Kinh nghiệm<br/>quản lý<br/>đánh giá KH&CN</a></li>";
								}
								if (isset($F001)){
									echo "<li ><a id='chuyengiakhac' href='#'><img border='0' width='48' height='48' src='icons/chuyen-gia-khac-icon.png' /><br/>Giới thiệu<br/>Chuyên gia khác</a></li>";
								}
								
								if (isset($F024)){
									echo "<li ><a id='printTTGV_LLKH_DS' href='#'><img border='0' width='48' height='48' src='icons/profile-search-icon.png' /><br/>Quản lý<br/>Lý Lịch Khoa Học</a></li>";
								}
								
								
							?>
						</ul>

					</div>
					<div style="">
						<table cellpadding="3">
							<tr style='cursor:pointer;' id='khcn_printHuongDanInTMDT' ><td><img src='icons/idea-icon-24x24.png' border=0 width=24 height=24 /></td><td ><a class='tooltips' data-toggle='tooltip' title='<b><u>Chú ý:</u> Đọc hướng dẫn này trước khi in thuyết minh đề tài</b>' style='font-weight:bold;color:#0195df'>Hướng dẫn In thuyết minh đề tài</a></td></tr>
						<?php
							
							
							if (isset($F002)){
								echo "<tr style='cursor:pointer;' id='printHuongDanInLLKH' ><td><img src='icons/idea-icon-24x24.png' border=0 width=24 height=24 /></td><td ><a class='tooltips' data-toggle='tooltip' title='<b><u>Chú ý:</u> Đọc hướng dẫn này trước khi in biểu mẫu (cập nhật mới)</b>' style='font-weight:bold;color:#0195df'>Hướng dẫn In biểu mẫu</a></td></tr>";
							}
							
							if (isset($F002)){
								echo "<tr style='cursor:pointer;' id='printTTKH' ><td><img src='icons/print-preview-icon24x24.png' border=0 width=24 height=24 /></td><td ><a class='tooltips' data-toggle='tooltip' title='Dành cho cán bộ tham gia đào tạo SĐH tại Trường Đại học Bách Khoa, Đại học Quốc gia Tp.HCM <br/><b>Để hoàn thành mẫu này, bạn cần điền các form sau:</b> Thông tin giảng viên, Hướng/đề tài nghiên cứu, Đề tài/dự án NCKH, Bài báo/tạp chí/HNKH' style='font-weight:bold;color:#0195df'>Thông tin khoa học</a></td></tr>";
							}
							if (isset($F002)){
								echo "<tr style='cursor:pointer;' id='printTTGV_LLKH_MAU_BO' rel='popover' data-original-title='Lý lịch khoa học - Mẫu của Bộ'><td><img src='icons/print-preview-icon24x24.png' border=0 width=24 height=24/></td><td style='font-weight:bold;color:#0195df'><a class='tooltips' data-toggle='tooltip' title='<b>Để hoàn thành mẫu này, bạn cần điền các form sau:</b> Thông tin giảng viên, Quá trình đào tạo, Trình độ ngoại ngữ, Đề tài/dự án NCKH, Bài báo/tạp chí/HNKH' style='font-weight:bold;color:#0195df'>LLKH - Mẫu của Bộ</a></td></tr>";
							}
				
							if (isset($F002)){
								echo "<tr style='cursor:pointer;' id='printTTGV_LLKH_MAU_R04' rel='popover' data-original-title='Lý lịch khoa học - Mẫu R04'><td><img src='icons/print-preview-icon24x24.png' border=0 width=24 height=24/></td><td style='font-weight:bold;color:#0195df'><a class='tooltips' data-toggle='tooltip' title='Để hoàn thành mẫu này, bạn cần điền <b>tất cả</b> các form bên trái <b>ngoại trừ form Thành tựu hoạt động KH&CN</b>' style='font-weight:bold;color:#0195df'>LLKH - Mẫu ĐHQG R04</a></td></tr>";
							}
							if (isset($F002)){
								echo "<tr style='cursor:pointer;' id='printTTGV_LLKH_MAU_BK' rel='popover' data-original-title='Lý lịch khoa học - Mẫu Trường BK'><td><img src='icons/print-preview-icon24x24.png' border=0 width=24 height=24/></td><td style='font-weight:bold;color:#0195df'><a class='tooltips' data-toggle='tooltip' title='Để hoàn thành mẫu này, bạn cần điền <b>tất cả</b> các form bên trái <b>ngoại trừ form Thành tựu hoạt động KH&CN</b>' style='font-weight:bold;color:#0195df'>LLKH - Mẫu Trường ĐHBK </a></td></tr>";
							}
							if (isset($F002)){
								echo "<tr style='cursor:pointer;' id='printTTGV_LLKH_MAU_KHCN_CNDT' rel='popover' data-original-title='Lý lịch khoa học - Mẫu Chủ nhiệm đề tài'><td><img src='icons/print-preview-icon24x24.png' border=0 width=24 height=24/></td><td style='font-weight:bold;color:#0195df'><a class='tooltips' data-toggle='tooltip' title='<b>Đề hoàn thành mẫu này<br/> bạn cần điền các form sau:</b><br/>Thông tin giảng viên, Quá trình đào tạo<br/>Thời gian công tác, Bài báo tạp chí<br/>Bằng phát minh, Ứng dụng thực tiễn<br/>Đề tài-dự án-NCKH, Giải thưởng KH&CN<br/>Thành tựu hoạt động KH&CN' style='font-weight:bold;color:#0195df'>LLKH - Mẫu Chủ nhiệm đề tài (Tỉnh - Thành phố)</a></td></tr>";
							}
							if (isset($F002)){
								echo "<tr style='cursor:pointer;' id='printTTGV_LLKH_MAU_KHCN_TGDT' rel='popover' data-original-title='Lý lịch khoa học - Mẫu Tham gia đề tài'><td><img src='icons/print-preview-icon24x24.png' border=0 width=24 height=24/></td><td style='font-weight:bold;color:#0195df'><a class='tooltips' data-toggle='tooltip' title='<b>Đề hoàn thành mẫu này<br/> bạn cần điền các form sau:</b><br/>Thông tin giảng viên, Quá trình đào tạo<br/>Thời gian công tác, Bài báo tạp chí<br/>Bằng phát minh, Ứng dụng thực tiễn<br/>Đề tài-dự án-NCKH, Giải thưởng KH&CN<br/>Thành tựu hoạt động KH&CN' style='font-weight:bold;color:#0195df'>LLKH - Mẫu Tham gia đề tài (Tỉnh - Thành phố)</a></td></tr>";
							}
							if (isset($F002)){
								echo "<tr style='cursor:pointer;' id='printTTGV_LLKH_MAU_KHCN_BO' rel='popover' data-original-title='Lý lịch khoa học - Mẫu Tham gia đề tài'><td><img src='icons/print-preview-icon24x24.png' border=0 width=24 height=24/></td><td style='font-weight:bold;color:#0195df'><a class='tooltips' data-toggle='tooltip' title='<b>Đề hoàn thành mẫu này<br/> bạn cần điền các form sau:</b><br/>Thông tin giảng viên, Trình độ ngoại ngữ <br/>Thời gian công tác, Quá trình đào tạo <br/> Lĩnh vực chuyên môn, Bài báo tạp chí<br/>Bằng phát minh, Ứng dụng thực tiễn<br/>Đề tài-dự án-NCKH, Giải thưởng KH&CN<br/>Kinh nghiệm quản lý đánh giá KH&CN, Giới thiệu chuyên gia khác ' style='font-weight:bold;color:#0195df'>LLKH - Mẫu Chuyên gia KH&CN (Bộ)</a></td></tr>";
							}
						?>
						</table>
					</div>
					<div class=clearfloat></div>
				</div>
            </div>
			<div id="tabs-3">
				<div style="width:1024px; margin:auto;">
					<div style="width:250px; margin:0 15px 0 20px; float:left;  border-right: 3px solid #aaa;">
						<ul id="sortableKHCN">
							<?php
							if (isset($F001)){
								echo "<li ><a id='ttgv' href='#'><img border='0' width='48' height='48' src='icons/gv.png' /><br/>Lý lịch cá nhân</a></li>";
							}
							if (isset($F001)){
								echo "<li ><a id='ngoaingu' href='#'><img border='0' width='48' height='48' src='icons/language-icon.png' /><br/>Trình độ ngoại ngữ</a></li>";
							}
							if (isset($F001)){
								echo "<li ><a id='qtct' href='#'><img border='0' width='48' height='48' src='icons/work.png' /><br/>Thời gian công tác</a></li>";
							}
							if (isset($F001)){
								echo "<li ><a id='qtdt' href='#'><img border='0' width='48' height='48' src='icons/training-icon.png' /><br/>Quá trình đào tạo</a></li>";
							}
							
							?>
						</ul>
					</div>
					<div style="">
						<table cellpadding="3">
							
						</table>
					</div>
					<div class=clearfloat></div>
				</div>
			</div>
            <div id="tabs-4">
            	 <div style="width:450px; margin:auto;"> 
                	<ul id="sortableBAOMAT">
						<?php
						if (isset($F013))
						{
							echo "<li ><a id='changePass' href='#'><img border='0' width='48' height='48' src='icons/user-password-icon.png' /><br/>Thay đổi mật khẩu</a></li>";
						}
						
						if (isset($F014))
						{
							echo "<li ><a id='accountInfo' href='#'><img border='0' width='48' height='48' src='icons/personal-information-icon.png' /><br/>Thông tin tài khoản</a></li>";
						}
						
						if (isset($F053))
						{
							echo "<li ><a id='confInfo' href='#'><img border='0' width='48' height='48' src='icons/work.png' /><br/>Cấu hình hệ thống</a></li>";
						}
						?>
                    </ul>
                </div>
				<div class=clearfloat></div>
            </div> <!-- End tabs-4 -->
        </div>
        
	</div><!-- End demo -->

</div> <!-- End col1 -->

<div id="footer">
<div id='footer_content'>
<div style="float:left;margin-right:10px;margin-top:8px;"><img src="images/logoBK.png" width="32" height="32"/></div>
<div style="float:left;margin-right:10px;margin-top:5px; font-size: 80%"><i>Đây là phiên bản thử nghiệm. <br/>
Quý Thầy Cô phát hiện lỗi xin vui lòng gửi email<br/> thông báo cho <a href = 'mailto:taint@hcmut.edu.vn'>taint@hcmut.edu.vn</a></i></div>
<div style="float:right;margin-right:10px; margin-top:0px;font-size: 80%" align=center><p>Dùng tốt nhất với</p>
	<img src="icons/keepalive1.gif" width="1" height="1" id="keepmealive" /><a href='http://www.mozilla.org/en-US/firefox/fx/'><img src="icons/Firefox-icon32.png" border=0 width="32" height="32"/></a> &nbsp; <a href='http://www.google.com/Chrome'><img src="icons/Chrome-icon32.png" border=0 width="32" height="32"/></a>
</div>
</div>

</div>
</div> <!-- End footer -->
</div>

<div id="gv_index_dialog_msgbox" title="Phòng Đào Tạo SDH - ĐHBK TP.HCM">
<span id=gv_index_dialog_msgbox_icon style="float:left; margin:0 7px 60px 0;"></span><span id="gv_index_dialog_msgbox_msg" style="line-height:16px"></span>
</div>

<div id="gv_processing_diglog" title="Phòng Đào Tạo SDH - ĐHBK TP.HCM">
	 <div align=center style="margin: 10px 0 15px 0; color: #636363">
	 <b><span id="gv_processing_diglog_msg"></span></b>
	 </div>
	 <div align=center >
		<img src='images/ajax-loader.gif'/>
	 </div>
</div>
<div id=tmpdiv style="display: none;"></div>
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

 // Format tien
 function formatNumberVND(e) {
	e.parseNumber({ format: "#,##0", locale: "us" });
	e.formatNumber({ format: "#,##0", locale: "us" });
 }
 function formatNumberUSD(e) {
		if (e.val().match(/^.+?\.0?$/)) return;
		e.parseNumber({ format: "#,##0.##", locale: "us" });
		e.formatNumber({ format: "#,##0.##", locale: "us" });
	} 

 var ctrlDown = false;
 function handleKeyDown(e) {
		if (e.which == 17) ctrlDown = true;
	}
 function handleKeyUp(e) {
		if (e.which == 17) ctrlDown = false;
	}
 function ignoreEvent(e) {
	if (e.which >= 16 && e.which <= 18) return true;
	if (e.which >= 33 && e.which <= 40) return true;
	if (ctrlDown && (e.which == 65 || e.which == 67)) return true;
	return false;
}
// end format tien
 
function gv_open_msg_box(pmsg, ptype, pwidth, pheight){
	//ptype = ptype || "info";
	pwidth = pwidth || 280;
	pheight = pheight || 180;
	
	$("#gv_index_dialog_msgbox_icon").removeClass("ui-icon");
	$("#gv_index_dialog_msgbox_icon").removeClass("ui-icon-alert");
	$("#gv_index_dialog_msgbox_icon").removeClass("ui-icon-info");
	$("#gv_index_dialog_msgbox_icon").removeClass("ui-icon-disk");
	
	if (ptype=='alert')
	{
		$("#gv_index_dialog_msgbox_icon").addClass("ui-icon");
		$("#gv_index_dialog_msgbox_icon").addClass("ui-icon-alert");
	}
	else if (ptype=='info')
	{
		$("#gv_index_dialog_msgbox_icon").addClass("ui-icon");
		$("#gv_index_dialog_msgbox_icon").addClass("ui-icon-info");
	}
	else if (ptype=='disk')
	{
		$("#gv_index_dialog_msgbox_icon").addClass("ui-icon");
		$("#gv_index_dialog_msgbox_icon").addClass("ui-icon-disk");
	}
	$("#gv_index_dialog_msgbox").dialog("option", "height", pheight);
	$("#gv_index_dialog_msgbox").dialog("option", "width", pwidth);
	$("#gv_index_dialog_msgbox_msg").html(pmsg);
	$("#gv_index_dialog_msgbox").dialog("open");
}
// paction = open, close
function gv_processing_diglog(paction, ptitle, pmsg, pwidth, pheight, presizable){
	pwidth = pwidth || 250;
	pheight = pheight || 120;
	presizable = presizable || false;
	pmsg = pmsg || "Đang xử lý dữ liệu ...";
	
	$("#gv_processing_diglog").dialog('option', 'title', ptitle);
	$("#gv_processing_diglog").dialog("option", "height", pheight);
	$("#gv_processing_diglog").dialog("option", "width", pwidth);
	$("#gv_processing_diglog").dialog("option", "resizable", presizable);
	$("#gv_processing_diglog_msg").html(pmsg);
	$("#gv_processing_diglog").dialog(paction);
}

// autoOpenPrinter = 1 : auto open printer when browser popup
function print_llkh_writeConsole(pContent, pAutoOpenPrinter, pTitle, pStyle, pWidth, pHeight) {
	var pWidth_ = pWidth || 800;
	var pHeight_ = pHeight || 450;
	var pStyle_ = pStyle || 'style="font-family:Arial,Helvetica,sans-serif;"';
	var abc=window.open('','','width='+pWidth_+',height='+pHeight_+',menubar=1'+',toolbar=0'+',status=0'+',scrollbars=1'+',resizable=1');
	var strprint='';
	if (pAutoOpenPrinter==1)
		strprint = 'window.print();';
	abc.document.writeln(
	'<html><head><title>'+pTitle+'</title></head>'
	+'<body bgcolor=white onLoad="self.focus(); ' + strprint + '" '+ pStyle_ + '>'
	+pContent
	+'</body></html>'
	);
	
//	abc.document.close();
}

function ucwords (str) {
    return (str + '').replace(/^([a-z,đ])|\s+([a-z,đ])/g, function ($1) {
        return $1.toUpperCase();
    });
}

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

// UpdateTips
function updateTips( t, oTips ) {
	oTips
		.text( t )
		.addClass( "ui-state-highlight" );
	setTimeout(function() {
		oTips.removeClass( "ui-state-highlight", 1500 );
	}, 1000 );
}

// Checklength
function checkLength( o, n, min, max, allownull, oTips) {
	if (allownull && o.val().length==0){
		return true;
	}
	if (min==0 && (o.val().length==0))
	{
		o.addClass( "ui-state-error" );
		o.focus();	
		updateTips( n + " không được để trống.", oTips);
		
		return false;
	}else if (min==max && o.val().length<min){
		o.addClass( "ui-state-error" );
		o.focus();	
		updateTips( "Thông tin " + n + " phải đủ " + min + " ký tự.", oTips);
	}else if ( o.val().length > max || o.val().length < min ) {
		o.addClass( "ui-state-error" );
		o.focus();	
		updateTips( "Chiều dài của " + n + " từ " +
					min + " đến " + max + " ký tự.", oTips);
		return false;
	} else {
		return true;
	}
}

function checkRegexp( o, regexp, n, oTips) {
	//alert('a');
	if ( !( regexp.test( o.val() ) ) ) {
		o.addClass( "ui-state-error" );
		o.focus();
		updateTips(n, oTips);
		return false;
	} else {
		return true;
	}
}
function checkDate(o, format, n, oTips){
	if ( !isDate( o, format ) ) {
		o.addClass( "ui-state-error" );
		o.focus();
		updateTips(n, oTips);
		return false;
	} else {
		return true;
	}
}
function isDate(o, format){ //format = 'dd/mm/yy'
	var isValid = true;
	try{
		var date = $.datepicker.parseDate(format, o.val());
		//alert (date);
		//if (year < 1900)
			//return false;
	}catch(error){
		isValid = false;
	}
	if (o.val() == ''){
		isValid = false;
	}
	return isValid;
}

function close_ckeditor()
{
	// ckeditor
	if ( editor )
		editor.destroy();
}
 
var editor;
function replaceDiv( div ) {
	//if ( editor )
	//	editor.destroy();

	editor = CKEDITOR.replace( div , {
		enterMode : CKEDITOR.ENTER_BR,
		shiftEnterMode : CKEDITOR.ENTER_P,
		language : 'vi',
		height : 120,
		toolbarGroups: [
			{ name: 'basicstyles', groups: [ 'basicstyles', 'cleanup' ]}
		]
	});
}

(function( $ ) {
	$(".tooltips").tooltip();
	
	$( "#gv_index_dialog_msgbox" ).dialog({
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
	
	$( "#gv_processing_diglog" ).dialog({
			resizable: false,
			autoOpen: false,
			width:250, height:120,
			modal: true
			
	});

	/*
	$('#tabs').bind('tabsshow', function(event, ui) {
		if (ui.panel.id == "tabs-3") {
			dataString = "khcn/khcn_thuyetminhdtkhcn.php?<?php echo "hisid=$sid" ?>";
			xreq = $.ajax({
			  type: 'POST', dataType: "html", data: dataString,
			  url: 'khcn/khcn_thuyetminhdtkhcn.php',
			  success: function(data) {
				$( "#tabs-3" ).html(data);
			  }
			});
		}
	});*/
})( jQuery );
</script>

<?php 
if (isset ($db_conn))
	oci_close($db_conn);
?>