<?php
$cookie_name = 'sitePGSauth';
$cookie_time = (3600 * 24 * 30); // 30 days

	include "libs/connect.php";
	//error_reporting(1);
	$sid = $_REQUEST["hisid"];
	$keycode = "qEiuwODpaKdjf";
	$keycode2 = "kWpcNOxeiPZlt";
	$link = $_REQUEST["l"];
	
	
	$search = array("'","\"");
	$replace = array("\\'","&quot;");
			
	//$chkRemberTemp = htmlspecialchars($_COOKIE["pgsRemberMe"]); 
	if(isSet($cookie_name))
		// Check if the cookie exists
		if(isSet($_COOKIE[$cookie_name])){
			//echo "co cookie_name: " .$_COOKIE[$cookie_name] ;
			parse_str($_COOKIE[$cookie_name]);
		}
	
	$usrdecode = trim(base64_decode(str_replace($keycode,"",$usrcookie)));
	$pasdecode = trim(base64_decode(str_replace($keycode2,"",$hashcookie)));
	
	//echo "</br>" . $usrcookie ."</br>". $hashcookie;
	
	if ($sid!=""){
		session_id($sid);
		session_start();
	}
	
	if ($_REQUEST["cat"]=="signout")
	{
		//if(isSet($_COOKIE[$cookie_name]))
			// remove 'site PGS auth' cookie
			//setcookie ($cookie_name, '', time() - $cookie_time);
		
		//session_unset();
		//session_destroy();
		//header("Location: http://www.pgs.hcmut.edu.vn/");
		$url = "front.php/login/cas/index?logout=1";
		header("Location: ".$url);
		die();
	}	
	
	if(!isset($_SESSION['uidloginKhoa']))
	{//mo 1
		$n = 0;
		
		if (isset($_POST["usrname"]) && isset($_POST["pass"]))
		{
			$usr=trim($_POST["usrname"]);
			$pass=trim($_POST["pass"]);
		}
		else
		{
			$usr = base64_decode($_SESSION["uidloginPortal"]);
			$pass = base64_decode($_SESSION["pidloginPortal"]);
		}
		if ($usr=="" && $pass == "") {
			$usr=trim($_REQUEST["usrname"]);
			$pass=trim($_REQUEST["pass"]);
		}
		
		$sqlstr="SELECT n.fk_ma_khoa ma_khoa, n.username, n.fk_ma_can_bo ma_can_bo, k.ten_khoa
		FROM nhan_su n, khoa k 
		WHERE n.fk_ma_khoa = k.ma_khoa(+)
		AND UPPER(n.username)=UPPER('".str_replace("'", "''",$usr)."') and password='".str_replace("'", "''",$pass)."'";
		
		$msg="";	
		$oci_pa = oci_parse($db_conn,$sqlstr); //gan cau query
		oci_execute($oci_pa);
		$n=oci_fetch_all($oci_pa, $kt);//lay du lieu  
		
		if ($n>0) {
			session_start();
			
			$do_login=true;
			$_SESSION["uidloginPortal"]=base64_encode($usr);
			$_SESSION["pidloginPortal"]=base64_encode($pass);
			$_SESSION["makhoa"] = base64_encode($kt["MA_KHOA"][0]);
			$_SESSION["tenkhoa"] = $kt["TEN_KHOA"][0];
			$_SESSION["macb"] = $kt["MA_CAN_BO"][0];
			
			
			date_default_timezone_set('Asia/Ho_Chi_Minh');
			$today =date("d/m/Y");
			$time = date("H:i:s");
	
			$sqlstr="UPDATE nhan_su
			SET last_login = SYSDATE
			WHERE UPPER(username)=UPPER('".str_replace("'", "''",$usr)."')";
			$oci_pa = oci_parse($db_conn,$sqlstr); //gan cau query
			oci_execute($oci_pa);
			
			//file_put_contents("logs.txt", $sqlstr);
			
			//$password_hash = md5($pass); // will result in a 32 characters hash
			//setcookie ($cookie_name, 'usrcookie='.$usr.'&hashcookie='.$password_hash.'RemberMecookie', time()+$cookie_time);
				
			($_REQUEST["chkRemberMe"]==1) ? setcookie ($cookie_name, 'usrcookie='.base64_encode($usr).$keycode.'&hashcookie='.base64_encode($pass).$keycode2.'&RemberMecookie=1', time()+$cookie_time) : setcookie ($cookie_name, 'RemberMecookie=0', time()+$cookie_time);
				
			//$_SESSION["keycode"] = $keycode;
			
			header("Location: index.php?hisid=".session_id()."&l=$link");
			die();
			
			//echo $_SESSION["macb"];
		}
		else
			$msg = "Người Dùng và Mật Khẩu không chính xác"; 
		
		oci_free_statement($oci_pa);
		
	}// end if(session_is_registered('uidloginKhoa')==false)

/* if(!isset($do_login))
// session_is_registered('uidloginKhoa')==false) // Neu chua dang nhap thi xuat form login
{ */
?>
<!doctype html>
<html>
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	
	<title>Cổng thông tin cán bộ Bách Khoa Tp.HCM</title>  
	
	<link href="../js/ui-1.9.2/css/pepper-grinder/jquery-ui-1.9.2.custom.min.css" rel="stylesheet" type="text/css"/>
  
  <link href="css/pgs.css" rel="stylesheet" type="text/css"/>
	  
	  <link href="../css/bootstrap.min.css" rel="stylesheet" type="text/css"/>
	  
	  <script src="../js/jquery-1.8.3.min.js"></script>
	  
	  <script src="../js/ui-1.9.2/js/jquery-ui-1.9.2.custom.min.js"></script>
	  
	  <script src="../js/jquery.placeholder-1.1.9.js"></script>
	  
	  <script src="../js/bootstrap.min.js"></script>
</head>

<body style="font-family:Arial, Helvetica, sans-serif">
	<div id="container">
		<div id="header">
			<div id="header-content">
				<div id=header_col1><a href="http://www.pgs.hcmut.edu.vn/"><h2>Đại Học Bách Khoa Tp.HCM<br/>Cổng Thông Tin Cán Bộ Bách Khoa Tp.HCM</h2></a></div>
				<div id=header_col2></div>
			</div>
		</div> <!-- End header -->
		<div id="header-line"></div>

		<div align="center" style='float:center;margin-top:30px;margin-bottom:30px;font-size:80%'>
			<form id="form_dangnhap" name="username" method="post" action="login.php?l=<?php echo $link; ?>">
				<table align="center" width="294px" border="0" cellpadding="0" cellspacing="0" class="ui-corner-all shawdow ui-widget-content" >
						<tr>
						<td colspan="4" valign="top">
							<div align="center" id="tipLogin" class="ui-corner-tl ui-corner-tr validateTips ">
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
					  <input name="usrname" style="width:200pt;" title="KKnnnn (KK: viết tắt tên khoa)" placeholder="tên đăng nhập" type="text" class="text ui-widget-content ui-corner-all" id="usrname" size="35" value="<?php if ($RemberMecookie){ echo $usrdecode; } ?>" />
				  </td>
				  <td width="45"></td>
				</tr>
				<tr>
				  <td width="29" valign="middle" align="right" class="heading"></td>
				  <td colspan="2" align="center" style="font-family:Arial, Helvetica, sans-serif; font-size:9px; color:blue;">     
					  
				  </td>
				  <td></td>
				</tr>
				
				<tr>
				  <td ></td>
				  <td colspan="2" align="left">
					 <div id="tooltips" style="color:red; font-size:11px;"></div>
				  </td>
				  <td></td>
				</tr>
				
				<tr>
				  <td height="37" class="heading " align="right"><label for="pass" class="ui-icon ui-icon-locked"></label></td>
				  <td colspan="2" align="left" valign="middle">
					  <input name="pass" title="0.nnnn (nnnn: 4 số cuối Mã CB)" style="width:200pt;" type="password" placeholder="mật khẩu" class="text ui-widget-content ui-corner-all" id="pass" size="35" value="<?php if ($RemberMecookie==1){ echo $pasdecode;} ?>" />
				  </td>
				  <td align="left" valign="middle">&nbsp;&nbsp;&nbsp;&nbsp;</td>
				</tr>
				
				
				
				<tr>
				  <td width="29" valign="middle" align="right" class="heading"></td>
				  <td colspan="2" align="center" style="font-family:Arial, Helvetica, sans-serif; font-size:9px; color:blue;">     
					 
				  </td>
				  <td></td>
				</tr>
				
				<tr>
				  <td height="24">&nbsp;</td>
				  <td width="21" align="left"  valign="top"><input name="chkRemberMe" type="checkbox" id="chkRemberMe" value="1" /></td>
				  <td width="199" height="20" align="left" valign="top" style="font-family:Arial, Helvetica, sans-serif; font-size:12px"><label for="chkRemberMe">Ghi nhớ tôi</label><br />
					(Không ghi nhớ nếu là máy dùng chung)</td>
				  <td><?php if (isset($RemberMecookie))
					echo "<script>document.getElementById('chkRemberMe').checked=".$RemberMecookie.";</script>"; 
					?>	
				  </td>
				</tr>
				
				<tr>
				  <td height="24">&nbsp;</td>
				  <td height="24" colspan="2">
					  <table width="100%" border="0" cellspacing="0" cellpadding="8">
						
						<tr>
						  <td align="left">
							<a href="front.php/login/cas/index" style='font-size:15px; color: #96C716'>Đăng nhập bằng email Trường</a>
						  </td>
						</tr>
						
						<tr>
						  <td style='font-size:15px;' align="left"><a href="forgot.php">Quên mật khẩu?</a></td>
						</tr>
						<tr>
						  <td style='font-size:15px;' align="left"><a href="#" id="gv_dskhoa" rel="popover" data-original-title="Danh sách tên Khoa viết tắt" >Danh sách tên Khoa viết tắt</a></td>
						</tr>
					  </table>
				  </td>
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

				<div style="float:right;margin-right:10px; margin-top:0px;font-size: 80%" align=center><p>Dùng tốt nhất với</p>
						<a href='http://www.mozilla.org/en-US/firefox/fx/'><img src="icons/Firefox-icon32.png" border=0 width="32" height="32"/></a> &nbsp; <a href='http://www.google.com/Chrome'><img src="icons/Chrome-icon32.png" border=0 width="32" height="32"/></a>
				</div>
			</div>

		</div>

	</div>
	<script type="text/javascript">
		$(function(){

			$( "button" ).button();
			$( "#btnGo" ).button({ icons: {primary:'ui-icon ui-icon-circle-arrow-e'} });
			
			$( "#usrname" ).tooltip({ placement: 'right' });
			$( "#usrname" ).tooltip('show');
			
			$( "#pass" ).tooltip({ placement: 'right' });
			$( "#pass" ).tooltip('show');
			
			$( "#gv_dskhoa" ).popover({trigger: 'hover', content: "<table  border=0 cellpadding=3 cellspacing=0 style='font-size:15px;width:240px;margin:-10px 0 10px -5px;' align=left>"+
				"<tr><td>Khoa học ứng dụng</td><td>UD</td></tr>" +
				"<tr class=alt><td>Cơ khí</td><td>CK</td></tr>" +
				"<tr><td>Kỹ thuật Hóa học</td><td>HC</td></tr>" +
				"<tr class=alt><td>Kỹ thuật Giao thông</td><td>GT</td></tr>" +
				"<tr><td>Bảo dưỡng công nghiệp</td><td>BD</td></tr>" +
				"<tr class=alt><td>Công nghệ vật liệu</td><td>VL</td></tr>" +
				"<tr><td>Xây dựng</td><td>XD</td></tr>" +
				"<tr class=alt><td>Kỹ thuật Địa chất & Dầu khí</td><td>DC</td></tr>" +
				"<tr><td>Điện-Điện tử</td><td>DD</td></tr>" +
				"<tr class=alt><td>Khoa học và Kỹ thuật máy tính</td><td>MT</td></tr>" +
				"<tr ><td>Môi trường</td><td>MO</td></tr>" +
				"<tr class=alt><td>Quản lý Công nghiệp</td><td>QL</td></tr>" +
				"<tr ><td>Trung tâm Ngoại ngữ</td><td>TN</td></tr>" +
				"</table>"
				});
			//$( "#gv_dskhoa" ).popover('show');
			
			// Check validate fields Login
			var jusrname	= $("#usrname"),
			jpass		= $("#pass"),
			allFieldsLogin = $([]).add(jusrname).add(jpass),
			tipsLogin	= $("#tipLogin");
			
			// UpdateTips session = ttgv, detai, ctkh, sach, login
			function gvlogin_updateTips( t ) {
							tipsLogin
									.text( t )
									.addClass( "ui-state-highlight" );
								setTimeout(function() {
									tipsLogin.removeClass( "ui-state-highlight", 1500 );
								}, 1000 );
			}
			
			// Checklength
			function gvlogin_checkLength( o, n, min, max) {
				if (min==0 && (o.val().length==0))
				{	
					o.addClass( "ui-state-error" );
					o.focus();	
					gvlogin_updateTips( "Thông tin " + n + " không được phép để trống." );
					
					return false;
				}else if (min==max && o.val().length<min){
					o.addClass( "ui-state-error" );
					o.focus();	
					gvlogin_updateTips( "Thông tin " + n + " phải đủ " + min + " ký tự." );
				}else if ( o.val().length > max || o.val().length < min ) {
					o.addClass( "ui-state-error" );
					o.focus();		
					gvlogin_updateTips( "Chiều dài của " + n + " từ " +
								min + " đến " + max + " ký tự.");
					return false;
				} else {
					return true;
				}
			}
			
			// Check Regexp
			function gvlogin_checkRegexp( o, regexp, n ) {
				//alert('a');
				if ( !( regexp.test( o.val() ) ) ) {
					o.addClass( "ui-state-error" );
					o.focus();
					gvlogin_updateTips( n );
					return false;
				} else {
					return true;
				
				}
			}
		// End of check validate
			
			$("#btnGo").click(function(e){
				var bValid = true;
				allFieldsLogin.removeClass( "ui-state-error" );
				bValid = bValid && gvlogin_checkLength( jusrname, "\"Người Dùng\"", 0, 200);
				bValid = bValid && gvlogin_checkLength( jpass, "\"Mật Khẩu\"", 0, 200);
				if ( !bValid )
					e.preventDefault();	
			});	// end $("#btnGo")
			
			$('#pass').keypress(function(e) { 
				var s = String.fromCharCode( e.which );
				if ( s.toUpperCase() === s && s.toLowerCase() !== s && !e.shiftKey ) {
					$("#tooltips").html('Chú ý: Caps Lock đang mở');
					//gvlogin_updateTips('Chú ý: Caps Lock đang mở');
				}
				else
				{
					$("#tooltips").html('');
				}
			});
			
			
			$('input[placeholder],textarea[placeholder]').placeholder();
			
			<?php
			if ($usr!='' && $msg != '')
			{
				echo "gvlogin_updateTips(\"".$msg. "\");";
			}
			?>	

		});
	</script>
</body>
</html>
<?php 
if (isset ($db_conn)){
	oci_close($db_conn);
}
?>