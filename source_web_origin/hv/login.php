<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<?php
$cookie_name = 'sitePGSauth';
$cookie_time = (3600 * 24 * 30); // 30 days
?>
<!--
<?php
	//error_reporting(1);
	include "libs/connect.php";
	include "libs/pgslibshv.php";
		
	$sid = $_REQUEST["hisid"];
	$keycode = "qEiuwODpaKdjf";
	$keycode2 = "kWpcNOxeiPZlt";
	$link = $_REQUEST["l"];
	
	$search = array("'","\"");
	$replace = array("\\'","&quot;");

	//$chkRemberTemp = htmlspecialchars($_COOKIE["pgsRemberMe"]); 
	if(isSet($cookie_name)){
		// Check if the cookie exists
		if(isSet($_COOKIE[$cookie_name])){
			//echo "co cookie_name: " .$_COOKIE[$cookie_name] ;
			parse_str($_COOKIE[$cookie_name]);
		}
	}
	$usrdecode = trim(base64_decode(str_replace($keycode,"",$usrcookiehv)));
	$pasdecode = trim(base64_decode(str_replace($keycode2,"",$hashcookiehv)));
	
	//echo "</br>" . $usrcookiehv ."</br>". $hashcookiehv;
	
	if ($sid!=""){
		session_id($sid);
	}
	
	session_start();
	
	if ($_REQUEST["cat"]=="signout")
	{
		//if(isSet($_COOKIE[$cookie_name]))
			// remove 'site PGS auth' cookie
			//setcookie ($cookie_name, '', time() - $cookie_time);
		session_unset();
		session_destroy();
	}
	
	// Khi user đang đăng nhập, user khác vào login cùng 1 browser, tự động vào lại index
	if(isset($_SESSION['uidloginhv']) && !isset($_POST["usrname"]))
	{
		header("Location: index.php?hisid=".session_id()."&l=$link");
		exit;
	}
	else
	{
		if (isset($_POST["usrname"]) && isset($_POST["pass"]))
		{
			$usr=trim($_POST["usrname"]);
			$pass=trim($_POST["pass"]);
		}
		else
		{
			$usr = base64_decode($_SESSION["uidloginhv"]);
			$pass = base64_decode($_SESSION["pidloginhv"]);
		}
		if ($usr=="" && $pass == "") 
		{
			$usr=trim($_REQUEST["usrname"]);
			$pass=trim($_REQUEST["pass"]);
		}

		$sqlstr="SELECT username FROM nguoi_dung 
		WHERE username='".escape($usr)."' and pass='".escape($pass)."'";
		//echo $sqlstr;
		$msg="";
		$oci_pa = oci_parse($db_conn,$sqlstr); //gan cau query
		oci_execute($oci_pa);
		$result=oci_fetch_all($oci_pa, $kt);//lay du lieu  
		oci_free_statement($oci_pa);
		
		if ($result>0) 
		{
			session_start();
			
			$do_login=true;
			$_SESSION["uidloginhv"]=base64_encode($usr);
			$_SESSION["pidloginhv"]=base64_encode($pass);
			
			//$password_hash = md5($pass); // will result in a 32 characters hash
			//setcookie ($cookie_name, 'usrcookiehv='.$usr.'&hashcookiehv='.$password_hash.'RemberMecookie', time()+$cookie_time);
				
			($_REQUEST["chkRemberMe"]==1) ? setcookie ($cookie_name, 'usrcookiehv='.base64_encode($usr).$keycode.'&hashcookiehv='.base64_encode($pass).$keycode2.'&RemberMecookie=1', time()+$cookie_time) : setcookie ($cookie_name, 'RemberMecookie=0', time()+$cookie_time);
				
			date_default_timezone_set('Asia/Ho_Chi_Minh');
			$today =date("d/m/Y");
			$time = date("H:i:s");
			
			// TO_DATE('$today $time', 'dd/mm/yyyy hh:mi:ss')
			
			$sqlstr="UPDATE nguoi_dung
			SET last_login =  SYSDATE
			WHERE UPPER(username)=UPPER('".str_replace("'", "''",$usr)."')";
			$oci_pa = oci_parse($db_conn,$sqlstr); //gan cau query
			oci_execute($oci_pa);
					
			if (isset ($db_conn))
				oci_close($db_conn);
				
			header("Location: index.php?hisid=".session_id()."&l=$link");
			exit;
			
			//echo $_SESSION["macb"];
		}
		else
			$msg = "Người Dùng và Mật Khẩu không chính xác"; 
	}
	
?>
-->
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Đăng Nhập Hệ Thống - Phòng Đào Tạo Sau Đại Học</title>

	
  <link href="../css/start/jquery-ui-1.8.18.custom.css" rel="stylesheet" type="text/css"/>
  <link href="css/pgs.css" rel="stylesheet" type="text/css"/>
  <link href="../css/bootstrap.min.css" rel="stylesheet" type="text/css"/>
  <script src="../js/jquery-1.8.0.min.js"></script>
  <script src="../js/jquery-ui-1.8.23.custom.min.js"></script>
  <script src="../js/jquery.placeholder-1.1.9.js"></script>
  <script src="../js/bootstrap.min.js"></script>
	
</head>

<body style="font-family:Arial, Helvetica, sans-serif">

<?php 
if(!isset($do_login))
{
?>

<div id="container">
<div id="header">
<div id="header-content">
	<div id=header_col1><a href="http://www.pgs.hcmut.edu.vn/"><h2>Đại Học Bách Khoa Tp.HCM<br/>Cổng Thông Tin Đào Tạo Sau Đại Học</h2></a></div>
    <div id=header_col2></div>
</div>
</div> <!-- End header -->
<div id="header-line"></div>

<div align="center" style='float:center;margin-top:30px;margin-bottom:30px;font-size:80%'>
<form id="form_dangnhap" name="form_dangnhap" method="post" action="login.php?l=<?php echo $link; ?>">
    <table align="center" border=0 width="294px" border="0" cellpadding="0" cellspacing="0" class="ui-corner-all ui-widget-content shawdow " >
            <tr>
            <td colspan="4" valign="top">
				<div align="center" id="tipLogin" class="ui-corner-tl ui-corner-tr validateTips " style='color:red;'>
					</div>
                
			</td>
			</tr>
	 <tr>
      <td >  </td>
      <td colspan="2" align="left"></td>
      <td ></td>
    </tr>
    <tr>
      <td width="29" height="37" valign="middle" align="right" class="heading"> <label for="usrname" class="ui-icon ui-icon-person"></label> </td>
      <td colspan="2" align="left">     
          <input name="usrname" style="width:200pt;"  placeholder="mã học viên" type="text" class="text ui-widget-content ui-corner-all" id="usrname" size="35" value="<?php if ($RemberMecookie==1)
		  				echo $usrdecode; 
				?>" />
      </td>
      <td width="45"></td>
    </tr>
    <tr>
      <td height="37" class="heading " align="right"><label for="pass" class="ui-icon ui-icon-locked"></label></td>
      <td colspan="2" align="left" valign="middle">
          <input name="pass" title="Chú ý đang mở Caps Lock" data-placement=right style="width:200pt;" placeholder="mật khẩu"  type="password" class="text ui-widget-content ui-corner-all" id="pass" size="35" value="<?php if ($RemberMecookie==1)
									echo $pasdecode;
						?>" />
      </td>
      <td align="left" valign="middle">&nbsp;&nbsp;&nbsp;&nbsp;</td>
    </tr>
    <tr>
      <td height="24">&nbsp;</td>
      <td width="21" align="left"  valign="top"><input name="chkRemberMe" type="checkbox" id="chkRemberMe" value="1" /></td>
      <td width="199" height="20" align="left" valign="top" style="font-family:Arial, Helvetica, sans-serif; font-size:12px"><label for="chkRemberMe">Ghi nhớ tôi</label><br />
        (Không ghi nhớ nếu là máy dùng chung)</td>
      <td>
		<?php 
			if (isset($RemberMecookie))
				echo "<script>document.getElementById('chkRemberMe').checked=".$RemberMecookie.";</script>"; 
		?>
	  </td>
    </tr>
    <tr>
      <td height="24">&nbsp;</td>
      <td height="24" colspan="2"><table width="100%" border="0" cellspacing="0" cellpadding="8">
        <tr>
          <td class="fontcontent" align="left"><a href="hv_forgotpass.php" class="">Quên mật khẩu?</a></td>
        </tr>
      </table></td>
      <td height="24">&nbsp;</td>
    </tr>
    <tr >
      <td >&nbsp;</td>
      <td >&nbsp;</td>
      <td align="right"> <input type=submit id="btnGo" value='&nbsp;Go!'></td>
      <td ></td>
    </tr>
 
    <tr>
      <td  colspan="4" valign="bottom" style='height:10px;'>
        
      	<table width="100%" border="0" height="25" cellspacing="0" cellpadding="5" class=" ui-corner-bl ui-corner-br"  >
          <tr>
            <td valign="bottom" >&nbsp;</td>
          </tr>
        </table>
                
      </td>
      </tr>
      </table>
	  
</form>
</div>

<div id="footer">
	<div id='footer_content'>
	<div style="float:left;margin-right:0px;margin-top:15px;"><img src="images/logoBK.png" width="32" height="32"/></div>
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

	<div style="float:right;margin-right:10px; margin-top:0px;font-size: 80%" align=center><p>Dùng tốt nhất với</p>
		<a href='http://www.mozilla.org/en-US/firefox/fx/'><img src="icons/Firefox-icon32.png" border=0 width="32" height="32"/></a> &nbsp; <a href='http://www.google.com/Chrome'><img src="icons/Chrome-icon32.png" border=0 width="32" height="32"/></a>
	</div>
</div>

</div>
</div> <!-- End container -->

<?php
}
?>

</body>
</html>

<script type="text/javascript">
//jQuery.ajax
//$(document).ready(function(){
$(function(){

	$( "button" ).button();
	$( "#btnGo" ).button({ icons: {primary:'ui-icon ui-icon-circle-arrow-e'} });
	
	//title="Chú ý đang mở Caps Lock" data-placement=right
	
	// Check validate fields Login
	var jusrname	= $("#usrname"),
	jpass		= $("#pass"),
	allFieldsLogin = $([]).add(jusrname).add(jpass),
	tipsLogin	= $("#tipLogin");
	
	// UpdateTips session = ttgv, detai, ctkh, sach, login
	function updateTips( t ) {
					tipsLogin
							.text( t )
							.addClass( "ui-state-highlight" );
						setTimeout(function() {
							tipsLogin.removeClass( "ui-state-highlight", 1500 );
						}, 1000 );
	}
	
	// Checklength
	function checkLength( o, n, min, max) {
		if (min==0 && (o.val().length==0))
		{	
			o.addClass( "ui-state-error" );
			o.focus();	
			updateTips( "Thông tin " + n + " không được phép để trống." );
			
			return false;
		}else if (min==max && o.val().length<min){
			o.addClass( "ui-state-error" );
			o.focus();	
			updateTips( "Thông tin " + n + " phải đủ " + min + " ký tự." );
		}else if ( o.val().length > max || o.val().length < min ) {
			o.addClass( "ui-state-error" );
			o.focus();		
			updateTips( "Chiều dài của " + n + " từ " +
						min + " đến " + max + " ký tự.");
			return false;
		} else {
			return true;
		}
	}
	
	// Check Regexp
	function checkRegexp( o, regexp, n ) {
		//alert('a');
		if ( !( regexp.test( o.val() ) ) ) {
			o.addClass( "ui-state-error" );
			o.focus();
			updateTips( n );
			return false;
		} else {
			return true;
		
		}
	}
// End of check validate
	
	$("#btnGo").click(function(e){
		var bValid = true;
		allFieldsLogin.removeClass( "ui-state-error" );
		bValid = bValid && checkLength( jusrname, "\"Người Dùng\"", 0, 200);
		bValid = bValid && checkLength( jpass, "\"Mật Khẩu\"", 0, 200);
		if ( !bValid )
			e.preventDefault();	
	});	// end $("#btnGo")

	<?php
	if ($usr!='' && $msg != '')
	{
		echo "updateTips(\"".$msg. "\");";
	}
	?>	
	
	$( "#pass" ).tooltip('destroy');
	$('#pass').keypress(function(e) { 
		var s = String.fromCharCode( e.which );
		if ( s.toUpperCase() === s && s.toLowerCase() !== s && !e.shiftKey ) {
			//$("#tooltips").html('Chú ý: Caps Lock đang mở');
			//updateTips('Chú ý: Caps Lock đang mở');
			$( "#pass" ).tooltip('show');
			
		}
		else
		{
			$( "#pass" ).tooltip('destroy');
		}
	});
	
	$('input[placeholder],textarea[placeholder]').placeholder();
});



</script>

<?php 
if (isset ($db_conn))
	oci_close($db_conn);
?>