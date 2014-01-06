<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<?php
if (isset($_POST["usrname"]))
{
	$usr=trim($_POST["usrname"]);
	$pass=trim($_POST["pass"]);

	if (is_numeric($usr))
	{
		$action = "hv/login.php";
	}
	else
	{
		$action = "gv/login.php";
	}
?>
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<script src="js/jquery-1.8.0.min.js"></script>
</head>
<body style="font-family:Arial, Helvetica, sans-serif">

	<div align=center style="font-family:arial;font-size:12px;margin:10px 0 0 0">
		Đang tải chương trình ... vui lòng chờ vài giây.
	</div>
	<form id="loginform" action="<?php echo $action; ?>" method="POST">
		<input name="usrname"  type="hidden" value="<?php echo $usr; ?>" />
		<input name="pass" type="hidden" value="<?php echo $pass; ?>" />
	</form>

	<script type="text/javascript">
		document.forms["loginform"].submit();
	</script>
</body>
</html>
<?php
}
else
{
?>
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Đăng Nhập Hệ Thống - Phòng Đào Tạo Sau Đại Học</title>

  <link href="css/pgs_m.css" rel="stylesheet" type="text/css"/>
	<script src="js/jquery-1.8.0.min.js"></script>
  
</head>

<body style="font-family:Arial, Helvetica, sans-serif">

<div id="container">
<div id="header">
<div id="header-content" >
	
	<div id=header_col1 > 
	
	<div align=left class=logo-block >
		<a href="http://www.hcmut.edu.vn/"><img src="images/Logo-BK.png" border=0/>
		</a>
		<a href="http://www.pgs.hcmut.edu.vn/">
		<h1>TRƯỜNG ĐẠI HỌC BÁCH KHOA TP.HCM
		<span>CỔNG THÔNG TIN ĐÀO TẠO SAU ĐẠI HỌC</span>
		</h1>
		</a>
	</div>
	
	</div>
    <div id=header_col2></div>
</div>
</div> <!-- End header -->
<div id="header-line"></div>
<div class="banner" >
	<div align=left >
		<img src="images/banner-sdh.jpg" border=0/>
	</div>
	
</div>

<div align="center" style='display:block; float:center;margin-top:30px;margin-bottom:20px;font-size:100%; font-family:arial'>
<div style="font-size:14px; font-weight:bold">
<p>

</p>
</div>
<div style="padding:0 15px 0 15px">
<div style="float:left; background-color:#5b84b3; width:50%;" align=right>
<a href="hv/login.php"><img src="icons/hocvien-icon.png" border=0></a>
</div>

<div style="margin-left:0px;float:right; background-color:#5b84b3; width:50%" align=left>
<a href="gv/login.php"><img src="icons/giangvien-icon.png" border=0></a>
</div>

<div class="clearfloat"></div>
</div>
</div>

<div id="footer">
<div id='footer_content'>

<div  style="float:left;">
<div align=left>Tiện ích dành cho Sinh Viên</div>
<div style="float:left;font-size: 80%">
				<ul>
					<li >Đăng Ký Môn Học </li>
					<li >Thời Khóa Biểu Ngành</li>
					<li >Thời Khóa Biểu Cá Nhân</li>
				</ul>	
	</div>

	<div style="float:left;font-size: 80%">
				<ul>
					<li >Kết Quả Đóng Học Phí</li>
					<li >Lịch Thi Ngành</li>
					<li >Lịch Thi Cá Nhân</li>
					
				</ul>	
	</div>

	<div style="float:left;font-size: 80%">
				<ul>
					<li >Tra Cứu Điểm</li>
					<li >Tra Cứu CTĐT</li>
					<li >Đề Cương Luận Văn</li>
				</ul>	
	</div>
<div class="clearfloat"></div>
<div align=left>Tiện ích dành cho Giảng Viên</div>
<div style="float:left;font-size: 80%">
			<ul>
				<li >Thời Khóa Biểu </li>
				<li >Lịch Thi </li>
				
				<li >Khối lượng giảng dạy</li>
				<li >Kinh phí TH/TN </li>
				
			</ul>	
</div>

<div style="float:left;font-size: 80%">
			<ul>
				<li >Danh sách lớp</li>
				<li >Thông tin cá nhân </li>
				<li >Bài báo, tạp chí, NCKH</li>
				<li >Lĩnh vực nghiên cứu</li>
			</ul>	
</div>

<div style="float:left;font-size: 80%">
			<ul>
				
				<li >Đề tài, dự án</li>
				<li >Sách, tài liệu tham khảo</li>
				<li >Hướng dẫn Luận văn Thạc Sĩ</li>
				<li >Hướng dẫn Luận án Tiến Sĩ</li>
			</ul>	
</div>
</div>
<div style="float:right;margin-right:10px; margin-top:5px;font-size: 100%">
<p align='center'>
<div style='margin-bottom:10px;'>Dùng tốt nhất với</div>
<div align=center><a href='http://www.mozilla.org/en-US/firefox/fx/'><img src="icons/Firefox-icon32.png" width="32" height="32" border=0/></a> &nbsp; <a href='http://www.google.com/Chrome'><img src="icons/Chrome-icon32.png" width="32" height="32" border=0/></a></div> 
</p>
</div>
</div>

</div>

</div>

</body>
</html>
<style>

</style>
<script type="text/javascript">
//jQuery.ajax
//$(document).ready(function(){
$(function(){

	
// End of check validate
	
	//$('input[placeholder],textarea[placeholder]').placeholder();

});


</script>

<?php 
}
?>