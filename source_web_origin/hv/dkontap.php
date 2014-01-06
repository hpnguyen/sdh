<?php
include("libs\connect.php"); 

function detect_ie()
{
    if (isset($_SERVER['HTTP_USER_AGENT']) && 
    (strpos($_SERVER['HTTP_USER_AGENT'], 'MSIE') !== false))
        return true;
    else
        return false;
}

function vn_str_filter ($str){
        $unicode = array(
            'a'=>'á|à|ả|ã|ạ|ă|ắ|ặ|ằ|ẳ|ẵ|â|ấ|ầ|ẩ|ẫ|ậ',
            'd'=>'đ',
            'e'=>'é|è|ẻ|ẽ|ẹ|ê|ế|ề|ể|ễ|ệ',
            'i'=>'í|ì|ỉ|ĩ|ị',
            'o'=>'ó|ò|ỏ|õ|ọ|ô|ố|ồ|ổ|ỗ|ộ|ơ|ớ|ờ|ở|ỡ|ợ',
            'u'=>'ú|ù|ủ|ũ|ụ|ư|ứ|ừ|ử|ữ|ự',
            'y'=>'ý|ỳ|ỷ|ỹ|ỵ',
			'A'=>'Á|À|Ả|Ã|Ạ|Ă|Ắ|Ặ|Ằ|Ẳ|Ẵ|Â|Ấ|Ầ|Ẩ|Ẫ|Ậ',
            'D'=>'Đ',
            'E'=>'É|È|Ẻ|Ẽ|Ẹ|Ê|Ế|Ề|Ể|Ễ|Ệ',
            'I'=>'Í|Ì|Ỉ|Ĩ|Ị',
            'O'=>'Ó|Ò|Ỏ|Õ|Ọ|Ô|Ố|Ồ|Ổ|Ỗ|Ộ|Ơ|Ớ|Ờ|Ở|Ỡ|Ợ',
            'U'=>'Ú|Ù|Ủ|Ũ|Ụ|Ư|Ứ|Ừ|Ử|Ữ|Ự',
            'Y'=>'Ý|Ỳ|Ỷ|Ỹ|Ỵ',
        );    
       foreach($unicode as $nonUnicode=>$uni){
            $str = preg_replace("/($uni)/i", $nonUnicode, $str);
       }
		return $str;
}

function sendemail($emailaddress, $name, $subject, $content){
	// Tien trinh gui email
	$to  = $emailaddress; // note the comma
	//$to .= 'wez@example.com';
	
	// subject
	$subject = $subject;
	
	// message
	$message = "
	<html>
	<head>
	  <title>Phòng Đào Tạo Sau Đại Học - ĐHBK Tp.HCM: Thông tin Đăng Ký Môn Học Ôn Tập</title>
	</head>
	<body>
		<p><strong>Đại học Bách Khoa TP.HCM <br/>Phòng Đào Tạo Sau Đại Học</strong></p>
		<p>$content</p>
		<p>Email này được gửi từ hệ thống email tự động. <br/>Xin vui lòng không trả lời email này. </p>
		<p>Trân trọng kính chào!</p>
	</body>
	</html>";
	
	// To send HTML mail, the Content-type header must be set
	$headers  = 'MIME-Version: 1.0' . "\r\n";
	$headers .= 'Content-type: text/html; charset=utf-8' . "\r\n";
	
	// Additional headers
	$headers .= "To: $name <$emailaddress>\r\n";
	$headers .= 'From: Phong Dao Tao Sau Dai Hoc <noreply@hcmut.edu.vn>' . "\r\n";
	$headers .= 'Cc: ' . "\r\n";
	$headers .= 'Bcc: ' . "\r\n";
	
	// Mail it
	/*if (mail($to,'=?UTF-8?B?'.base64_encode($subject).'?=', $message, $headers))
		$msgsuccess = " Mật khẩu mới đã được gửi đến địa chỉ email: </br>".$emailaddress;
	else
		$msgsuccess = " Quá trình email có lỗi.";*/
	return mail($to,'=?UTF-8?B?'.base64_encode($subject).'?=', $message, $headers);	
}
date_default_timezone_set('Asia/Ho_Chi_Minh');

$sqlstr = "SELECT name, value
			FROM config
			WHERE name = 'KHOA_TUYEN_SINH' OR name = 'DOT_TUYEN_SINH'
			ORDER BY name";
$stmt = oci_parse($db_conn, $sqlstr);oci_execute($stmt);$n = oci_fetch_all($stmt, $resDM);oci_free_statement($stmt);

$dot = $resDM["VALUE"][0];
$nam = $resDM["VALUE"][1];
$today = date("d/m/Y");
$month = date("m");

// 11 12 1 2 3 4 5 => dot 1
// 6 7 8 9 10 => dot 2
// Xac dinh dot dang ky on tap online
/*if ($month > 5 && $month < 11)
	$dot = 2;
else
	$dot = 1;
*/
$sqlstr = "SELECT value , (to_date('$today','dd/mm/yyyy')-to_date(value,'dd/mm/yyyy')) het_han
			FROM config
			WHERE name='NGAY_HET_HAN_ON_DK' ";
$stmt = oci_parse($db_conn, $sqlstr);oci_execute($stmt);$n = oci_fetch_all($stmt, $resDM);oci_free_statement($stmt);
$ngayhethanOnTap = $resDM["VALUE"][0];
$hethan = $resDM["HET_HAN"][0];

// Recapcha
require_once('libs\recaptchalib.php');                
// Get a key from https://www.google.com/recaptcha/admin/create
$publickey = "6LcDrc8SAAAAAOehS2lVAVXYUefcRz94cIJK6UbZ";
$privatekey = "6LcDrc8SAAAAACyVrTs8yuqB_vHagESgu29bKSoF";

# the response from reCAPTCHA
$resp = null;
# the error code from reCAPTCHA, if any
$error = null;

$ma_hv = 'N/A';

 if ($_POST["recaptcha_response_field"]) {
		$resp = recaptcha_check_answer ($privatekey,
										$_SERVER["REMOTE_ADDR"],
										$_POST["recaptcha_challenge_field"],
										$_POST["recaptcha_response_field"]);

		if ($resp->is_valid) {
			$nganh = str_replace("'","''",$_POST["comNganh"]);
			$ho = str_replace("'","''",trim($_POST["txtHo"]));
			$ten = str_replace("'","''",trim($_POST["txtTen"]));
			$noisinh = str_replace("'","''",$_POST["comNoiSinh"]);
			$ngaysinh = str_replace("'","''",$_POST["txtNgaySinh"]);		
			$email = str_replace("'","''",$_POST["txtEmail"]);	
			
			$sqlstr = "select New_Hoc_Vien_On_Tap($nam) MA_HV_MOI from dual";
			$stmt = oci_parse($db_conn, $sqlstr);
			oci_execute($stmt);
			$n = oci_fetch_all($stmt, $resDM);
			oci_free_statement($stmt);
			
			$ma_hv = $resDM["MA_HV_MOI"][0];
			
			$sqlstr = "INSERT INTO hoc_vien_on_tap(ma_hoc_vien, nam, ho, ten, ngay_sinh,noi_sinh,ma_nganh,email,dot) 
			values('$ma_hv',$nam,'$ho','$ten', to_date('$ngaysinh','dd/mm/yyyy'), '$noisinh', '$nganh','$email','$dot')";
			$stmt = oci_parse($db_conn, $sqlstr);

			$huongdandongtien = '';
			
			if (oci_execute($stmt)){
				oci_free_statement($stmt);
				for ($i=0; $i<4; $i++){
					if (isset($_POST["mhdk$i"])){
						$sqlstr = "INSERT INTO dang_ky_mon_hoc_on_tap(ma_hoc_vien, nam, ma_mh, ngay_dang_ky)
						values('$ma_hv',$nam,'{$_POST["mhdk$i"]}', sysdate)";
						$stmt = oci_parse($db_conn, $sqlstr);
						oci_execute($stmt);
						
						//$huongdandongtien .= "$sqlstr ; ";
					}
				}
				$huongdandongtien .= '<div style="border: 2px solid black; padding:10px; margin-bottom:10px;">'
				.'<b>Hướng dẫn đóng học phí:</b><br/>'
				.'<ol>'
				.'<li>Nộp tiền vào TK số: <b>1940201178555</b>, Ngân hàng Nông nghiệp & Phát Triển Nông Thôn - Chi nhánh Hiệp Phước.<br/><i>Chủ TK: </i><b>TRƯỜNG ĐẠI HỌC BÁCH KHOA TP.HCM</b><br/><i>Địa chỉ: </i><b>268 Lý Thường Kiệt, Phường 14, Quận 10, Tp.HCM</b><br/>'
				."<i>Nội dung:</i> <b>Học phí ôn tập; Mã HV: $ma_hv; $ho $ten;</b></li>"
				.'<li>HV phải giữ Giấy Nộp Tiền của NH để nhận <b>Thẻ HV Ôn Tập</b> tại phòng ĐT SĐH ĐHBK TP.HCM trong thời gian sớm nhất.<br/><i><u>Lưu ý:</u> Thẻ HV Ôn Tập chỉ cấp 1 lần duy nhất và miễn hoàn lại học phí.</i></li>'
				.'</ol>'
				.'</div>';
				$noidungprint = '<a href="JavaScript:window.print();">In trang này</a>';
				$noidungprint .= "<div align=center style=\"font-size:12pt;margin-top:10px;\"><b>Phiếu ĐKMH Ôn Tập TS SĐH - ĐHBK TP.HCM $nam Đợt $dot</b></div>";
				$noidungprint .= "<div style=\"border: 2px solid black;margin-bottom:10px;margin-top:10px;padding:10px;\"><b>Mã HV: $ma_hv</b><br/> {$_POST['print']} </div>" .$huongdandongtien;
				$noidungprint .= '<a href="JavaScript:window.print();">In trang này</a>';
				$hoten_ko_dau = vn_str_filter($ho." ".$ten);
				
				//sendemail($email, $hoten_ko_dau , "Phieu dang ky Mon Hoc On Tap", $noidungprint);
				//sendemail("taint@hcmut.edu.vn", $hoten_ko_dau , "Phieu dang ky Mon Hoc On Tap ($hoten_ko_dau)", $noidungprint);
				
			}else
			{
				$noidungprint = "<div align=center>Không thể đăng ký.</div>";
			}
			oci_free_statement($stmt);
			
			$registerSuccess = true;
		} else {
				# set the error code so that we can display it
				$error = $resp->error;
		}
}
// End Recapcha

if (isset($_REQUEST["lk"])){
	
	//$mh = explode(",", $_REQUEST["lk"]);
	//echo $mh[0];
	//echo $mh[1];
	$MA_NGANH = $_REQUEST["lk"];
	$sqlstr = "SELECT ot.ma_mh, mh.ten_mh, mh.so_tiet
				FROM ctdt_on_tap ot, mon_hoc_chuyen_doi mh
				WHERE ot.nam=(select value from config where name='KHOA_TUYEN_SINH')
				AND ot.ma_nganh = '$MA_NGANH'
				AND ot.ma_mh = mh.ma_mh
				";
	$stmt = oci_parse($db_conn, $sqlstr);
	oci_execute($stmt);
	$n = oci_fetch_all($stmt, $resDM);
	oci_free_statement($stmt);
	for ($i = 0; $i < $n; $i++)
	{	
		// Kiem tra mon anh van 60 va anh van 90
		if ($resDM["MA_MH"][$i]=='ONA2' || $resDM["MA_MH"][$i]=='ONA3' || $resDM["MA_MH"][$i]=='ONA4' || $resDM["MA_MH"][$i]=='ONA5')
		{
			$onlick="onClick='
			if (this.value == \"ONA2\" && this.checked){
				mhdk3.checked=false;
				mhdk4.checked=false;
				mhdk5.checked=false;
			}
			if (this.value == \"ONA3\" && this.checked){
				mhdk2.checked=false;
				mhdk4.checked=false;
				mhdk5.checked=false;
			}
			if (this.value == \"ONA4\" && this.checked){
				mhdk2.checked=false;
				mhdk3.checked=false;
				mhdk5.checked=false;
			}
			if (this.value == \"ONA5\" && this.checked){
				mhdk2.checked=false;
				mhdk3.checked=false;
				mhdk4.checked=false;
			}
			'";
		}
		echo "<input $onlick type='checkbox' id='mhdk$i' name='mhdk$i' value='{$resDM["MA_MH"][$i]}' /> <label id=lblmhdk$i for=mhdk$i>{$resDM["TEN_MH"][$i]}</label><input type=hidden id=stmh$i value='{$resDM["SO_TIET"][$i]}'><br />";
	}
	exit;
}
?>
<META http-equiv=Content-Type content='text/html; charset=utf-8'>
<HTML>
<TITLE>Phòng đào tạo Sau đại học - ÐH Bách khoa TP.HCM</TITLE> 

<link href="../css/start/jquery-ui-1.8.18.custom.css" rel="stylesheet" type="text/css"/>
<link href="css/pgs.css" rel="stylesheet" type="text/css" />

<style type="text/css" media="screen">
a:link {
	color: #000000;
	text-decoration: underline;
}
.shawdow{
	box-shadow: 5px 5px 10px #888888;
}
.shawdow1{
	box-shadow: 3px 3px 10px #888888;
}
.shawdow2{
	box-shadow: 1px 1px 10px #888888;
}

.ui-combobox {
		position: relative;
		display: inline-block;
}
.ui-combobox-toggle {
	position: absolute;
	top: 0;
	bottom: 0;
	margin-left: -1px;
	padding: 0;
	/* adjust styles for IE 6/7 */
	*height: 1.7em;
	*top: 0.1em;
}
.ui-combobox-input {
	margin: 0;
	padding: 0.3em;
}

.ui-state-error1 {
	border: 1px solid #cd0a0a; 
	color: #000000; }

</style>

<script src="../js/jquery-1.8.0.min.js"></script>
<script src="../js/jquery-ui-1.8.23.custom.min.js"></script>
<script src="../js/jquery.maskedinput-1.3.min.js"></script>

<script type="text/JavaScript">

function getInternetExplorerVersion()
// Returns the version of Windows Internet Explorer or a -1
// (indicating the use of another browser).
{
   var rv = -1; // Return value assumes failure.
   if (navigator.appName == 'Microsoft Internet Explorer')
   {
      var ua = navigator.userAgent;
      var re  = new RegExp("MSIE ([0-9]{1,}[\.0-9]{0,})");
      if (re.exec(ua) != null)
         rv = parseFloat( RegExp.$1 );
   }
   return rv;
}
</script>



<body style="font-family:Arial,Helvetica,sans-serif; font-size:60%">

<?php
//$hethan = -1;
if (!($hethan>0)) 
{
?>

<noscript>
  <table style="width:100%; margin-top:15px;">
  <tr>
  <td align=center>
  <span style="color: red; width:100%; font-size:12pt;">Để trang này hoạt động bạn cần phải kích hoạt JavaScript</span>
  </td>
  </tr>
  </table>
  <style>div { display:none; }</style>
</noscript>

	<div style="width:100%; font-size:80%">
		<table style="width:420px" border='0' align=center cellspacing='0' cellpadding='5' class='ui-widget ui-widget-content ui-corner-top tableData'>
		<tr> 
		<td>
			<div style="width:415px; color:black;margin-top:10px;margin-bottom:10px;font-size: 10pt;">
				<b>ĐĂNG KÝ ÔN TẬP TUYỂN SINH <?php echo "NĂM $nam ĐỢT $dot"; ?></b><br/>Hạn chót đăng ký đến hết ngày <span style="color: red; font-weight: bold;"><?php echo "$ngayhethanOnTap"; ?></span>
				
				<div style="margin: 5px 0 5px 0">Quy trình đăng ký ôn tập</div>
				<div style="color: #007bb9">
					1. Đăng ký ôn tập online <br>
					2. In phiếu đăng ký môn học & hướng dẫn đóng học phí<br>
					3. Thực hiện đóng học phí theo hướng dẫn<br>&nbsp;&nbsp;&nbsp;(Miễn hoàn lại học phí) <br>
					4. Đem biên lai đóng tiền đến phòng ĐT SĐH xếp lớp
				</div>
			</div>
			<?php 
			if (!$registerSuccess)
			{
				//echo $month . " "  . $dot;
			?>
			<div >
				<form id="form_dkontap" name="form_dkontap" method="post" action="dkontap.php">
				<table style="width:415px; font-size: 10pt;">
					<tr >
						<td><label for=comNganh>Ngành</label></td>
						<td colspan=3>
							<select name="comNganh" id="comNganh" class="text ui-widget-content" style="width:310px;" onChange="updateMH(this.value)" >
								<option value="" selected>Chọn ngành...</option>
								<?php $sqlstr="select ma_nganh, titlecase(ten_nganh) ten_nganh
											from nganh
											where ma_nganh in (select distinct ma_nganh
											from ctdt_on_tap
											where nam=$nam and dot = $dot
											)
											order by ten_nganh"; 
								$stmt = oci_parse($db_conn, $sqlstr);
								oci_execute($stmt);
								$n = oci_fetch_all($stmt, $resDM);
								oci_free_statement($stmt);
								for ($i = 0; $i < $n; $i++)
								{
									echo "<option value='".$resDM["MA_NGANH"][$i]."'>" .$resDM["TEN_NGANH"][$i]. "</option>";
								}
								?>
							</select>
						</td>
					</tr>
					<tr>
						<td ><label for=txtHo>Họ</label></td><td ><input style="width:150px; font-size: 10pt;" class="text ui-widget-content" type=text name=txtHo id=txtHo onChange="if (this.value!='') this.value = this.value.toLowerCase().capitalize()"></td><td align=right><label for=txtTen>Tên</label></td><td align=right><input style="width:60px;font-size: 10pt;" class="text ui-widget-content" type=text name=txtTen id=txtTen onChange="if (this.value!='') this.value = this.value.toLowerCase().capitalize()"></td>
					</tr>
					<tr>
						<td style="width:100px;"><label for=txtNgaySinh>Ngày sinh</label></td><td colspan=3><input style="width:90px;font-size: 10pt;" class="text ui-widget-content" type=text name=txtNgaySinh id=txtNgaySinh> <span style="font-size:10px; color:blue">dd/mm/yyyy</span></td>
					</tr>
					<tr>
						<td><label for=comNoiSinh>Nơi sinh</label></td>
						<td colspan=3>
							<select name="comNoiSinh" id="comNoiSinh" class="text ui-widget-content">
								<option value="">Chọn nơi sinh...</option>
								<?php $sqlstr="select ma_tinh_tp, ten_tinh_tp
											from dm_tinh_tp
											where ma_tinh_tp not in ('FR','LS','CA','QC','GM')
											order by ten_tinh_tp";
								$stmt = oci_parse($db_conn, $sqlstr);
								oci_execute($stmt);
								$n = oci_fetch_all($stmt, $resDM);
								oci_free_statement($stmt);
								for ($i = 0; $i < $n; $i++)
								{
									echo "<option value='".$resDM["TEN_TINH_TP"][$i]."'>" .$resDM["TEN_TINH_TP"][$i]. "</option>";
								}
							  ?>
							</select>
						</td>
					</tr>
					<tr>
						<td><label for=txtEmail>Email</label></td><td colspan=3><input style="width:200px; font-size: 10pt;" class="text ui-widget-content" type=text name=txtEmail id=txtEmail></td>
					</tr>
					<tr>
						<td colspan=4><strong>Môn học đăng ký ôn tập:</strong></td>
					</tr>
					<tr>
						<td colspan=4><div id=mhdangky style="margin-left:15px;"></div></td>
					</tr>
					<tr>
						<td colspan=4><div style="margin-top:5px; width:100%;  color:red;" id="tipOnTap" align=center></div></td>
					</tr>
					<tr>
						<td colspan=4 align=center>
							<script type="text/javascript">
								 var RecaptchaOptions = {
									theme : 'white'
								 };
							 </script>
							<?php
							//if (!$resp->is_valid)
								echo recaptcha_get_html($publickey, $error);
							?>
							<!-- end recaptcha_div  -->
						</td>
					</tr>
					
					<tr>
						<td colspan=4><div style="margin-top:5px; font-size:80%" align=center><input type=submit id=btnSubmit name=btnSubmit value="Đăng ký"></div></td>
					</tr>
				</table>
				<input type=hidden name="print" id="print" value=''>
				</form>
			</div>
			<?php
			}
			else // $registerSuccess = true
			{
			?>
			<div style="padding:10px ;font-size: 10pt; color: black;">
				<div style="width:100%;color:blue;" >Bạn đã đăng ký thành công.<br/>Vui lòng In phiếu đăng ký môn học & hướng dẫn đóng học phí.</div>
				<p>
				<b>Mã HV: <?php echo $ma_hv; ?></b><br/>
				<?php echo $_POST["print"]; ?>
				<div style="width:100%;margin: 20px 0 0px 0;" align=center><a id=btnaprint href="#" style="font-size:80%"><b>In phiếu đăng ký môn học & hướng dẫn</b></a></div>
				</p>
			</div>
			<?php 
			} 
			?>
		</td>
		</tr>
		</table>
	</div>
	<script>
	
		var dongydangky = false;
		
		String.prototype.capitalize = function(){
		   return this.replace( /(^|\s)([a-z])/g , function(m,p1,p2){ return p1+p2.toUpperCase(); } );
		};

		function writeConsole(content) {
		 a=window.open('','myconsole',
		  'width=650,height=650'
		   +',menubar=1'
		   +',toolbar=0'
		   +',status=0'
		   +',scrollbars=1'
		   +',resizable=1')
		 a.document.writeln(
		  '<html><head><title>Phieu Dang Ky On Tap</title></head>'
		   +'<body bgcolor=white onLoad="self.focus()">'
		   +content
		   +'</body></html>'
		 )
		 a.document.close()
		}
		
		
		function formatCurrency(num)
		 {
			num = num.toString().replace(/\$|\,/g,'');
			if(isNaN(num))
			num = "0";
			sign = (num == (num = Math.abs(num)));
			num = Math.floor(num*100+0.50000000001);
			num = Math.floor(num/100).toString();
			for (var i = 0; i < Math.floor((num.length-(1+i))/3); i++)
			num = num.substring(0,num.length-(4*i+3))+','+
			num.substring(num.length-(4*i+3));
			return (((sign)?'':'-') + num);
		}
		function FormatNumber(obj) {
			var strvalue;
			if (eval(obj))
				strvalue = eval(obj).value;
			else
				strvalue = obj;
			var num;
				num = strvalue.toString().replace(/\$|\,/g,'');
			 
			if(isNaN(num))
				num = "";
				sign = (num == (num = Math.abs(num)));
				num = Math.floor(num*100+0.50000000001);
				num = Math.floor(num/100).toString();
			for (var i = 0; i < Math.floor((num.length-(1+i))/3); i++)
				num = num.substring(0,num.length-(4*i+3))+','+
				num.substring(num.length-(4*i+3));
				//return (((sign)?'':'-') + num);
				eval(obj).value = (((sign)?'':'-') + num);
		}
		/* 
		decimal_sep: character used as deciaml separtor, it defaults to '.' when omitted
		thousands_sep: char used as thousands separator, it defaults to ',' when omitted
		*/
		Number.prototype.toMoney = function(decimals, decimal_sep, thousands_sep)
		{ 
		   var n = this,
		   c = isNaN(decimals) ? 2 : Math.abs(decimals), //if decimal is zero we must take it, it means user does not want to show any decimal
		   d = decimal_sep || '.', //if no decimal separator is passed we use the dot as default decimal separator (we MUST use a decimal separator)

		   /*
		   according to [http://stackoverflow.com/questions/411352/how-best-to-determine-if-an-argument-is-not-sent-to-the-javascript-function]
		   the fastest way to check for not defined parameter is to use typeof value === 'undefined' 
		   rather than doing value === undefined.
		   */   
		   t = (typeof thousands_sep === 'undefined') ? ',' : thousands_sep, //if you don't want to use a thousands separator you can pass empty string as thousands_sep value

		   sign = (n < 0) ? '-' : '',

		   //extracting the absolute value of the integer part of the number and converting to string
		   i = parseInt(n = Math.abs(n).toFixed(c)) + '', 

		   j = ((j = i.length) > 3) ? j % 3 : 0; 
		   return sign + (j ? i.substr(0, j) + t : '') + i.substr(j).replace(/(\d{3})(?=\d)/g, "$1" + t) + (c ? d + Math.abs(n - i).toFixed(c).slice(2) : ''); 
		}

		
		function updateMH(pNganh) {
			//alert(pNganh);
			$( "#mhdangky" ).html('<div style="width:100%;" align=center><img src="images/ajax-loader.gif"></div>');
			xreq = $.ajax({
			  type: 'POST', dataType: "html",
			  url: 'dkontap.php?lk=' + pNganh,
			  success: function(data) {
				$( "#mhdangky" ).html(data);
			  },
			  error: function(xhr, ajaxOptions, thrownError) {
			  }
			});
		}
	$(function() {
		$( "#btnSubmit, #btnaprint" ).button();
		$("#txtNgaySinh").mask("99/99/9999");
		$("#txtNgaySinh").datepicker({
				showOn: "button",
				showButtonPanel: false,
				dateFormat: "dd/mm/yy",
				yearRange: "1900:2000",
				changeMonth: true,
				changeYear: true,
				defaultDate: '01/01/1980'
		});

		function isValidDate(controlName, format){ //format = 'dd/mm/yy'
			var isValid = true;
			//alert(document.getElementById(controlName).value + ' ' + format);
			try{
				$.datepicker.parseDate(format, document.getElementById(controlName).value,null);
			}
			catch(error){
				isValid = false;
			}
			
			if (document.getElementById(controlName).value == '')
				isValid = false;
			//alert(isValid);
			return isValid;
		}
		
		// UpdateTips session = ttgv, detai, ctkh, sach, login
		function updateTips( t ) {
			tipsOnTap
					.text( t )
					.addClass( "ui-state-highlight" );
				setTimeout(function() {
					tipsOnTap.removeClass( "ui-state-highlight", 1500 );
				}, 1000 );
		}
		// Checklength
		function checkLength( o, n, min, max) {
			if (min==0 && (o.val().length==0))
			{	
				o.addClass( "ui-state-error1" );
				o.focus();	
				updateTips( "Thông tin " + n + " không được phép để trống." );
				
				return false;
			}else if (min==max && o.val().length<min){
				o.addClass( "ui-state-error1" );
				o.focus();	
				updateTips( "Thông tin " + n + " phải đủ " + min + " ký tự." );
			}else if ( o.val().length > max || o.val().length < min ) {
				o.addClass( "ui-state-error1" );
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
				o.addClass( "ui-state-error1" );
				o.focus();
				updateTips( n );
				return false;
			} else {
				return true;
			
			}
		}
		
		// Check validate fields Login
		var jnganh	= $("#comNganh"),
		jHo		= $("#txtHo"),
		jTen		= $("#txtTen"),
		jNgaySinh = $("#txtNgaySinh"),
		jNoiSinh = $("#comNoiSinh"),
		jEmail = $("#txtEmail"),
		jRecapcha = $("#recaptcha_response_field"),
		allFieldsLogin = $([]).add(jnganh).add(jHo).add(jTen).add(jNgaySinh).add(jNoiSinh).add(jEmail).add(jRecapcha),
		tipsOnTap	= $("#tipOnTap");
		//var dongydangky = false;
		var noidungprint = '';
		
		$("#btnaprint").click(function(e){
			writeConsole('<?php echo $noidungprint;?>');
		});

		$("#form_dkontap").submit(function() {
			//alert(document.getElementsByTagName('label')[5].firstChild.data);
			//alert( $( "#lblmhdk0" ).html() );
			var bValid = true;
			allFieldsLogin.removeClass( "ui-state-error1" );
			bValid = bValid && checkLength( jnganh, "\"Ngành\"", 0, 200);
			bValid = bValid && checkLength( jHo, "\"Họ\"", 0, 100);
			bValid = bValid && checkLength( jTen, "\"Tên\"", 0, 20);
			
			//alert(isValidDate('txtNgaySinh','dd/mm/yy'));
			//alert (dongydangky);
			if (bValid && !isValidDate('txtNgaySinh','dd/mm/yy'))
			{
				//alert('a');
				bValid = false;
				document.getElementById('txtNgaySinh').focus();
				jNgaySinh.addClass( "ui-state-error1" );
				updateTips('Ngày Sinh không chính xác.');
			}
					
			bValid = bValid && checkLength( jNoiSinh, "\"Nơi Sinh\"", 0, 100);
			bValid = bValid && checkLength( jEmail, "\"Email\"", 0, 100);
			bValid = bValid && checkRegexp( jEmail, /^((([a-z]|\d|[!#\$%&'\*\+\-\/=\?\^_`{\|}~]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])+(\.([a-z]|\d|[!#\$%&'\*\+\-\/=\?\^_`{\|}~]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])+)*)|((\x22)((((\x20|\x09)*(\x0d\x0a))?(\x20|\x09)+)?(([\x01-\x08\x0b\x0c\x0e-\x1f\x7f]|\x21|[\x23-\x5b]|[\x5d-\x7e]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(\\([\x01-\x09\x0b\x0c\x0d-\x7f]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF]))))*(((\x20|\x09)*(\x0d\x0a))?(\x20|\x09)+)?(\x22)))@((([a-z]|\d|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(([a-z]|\d|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])*([a-z]|\d|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])))\.)+(([a-z]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(([a-z]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])*([a-z]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])))\.?$/i, "Email không chính xác, vd: pgs@hcmut.edu.vn" );
			
			if (bValid && !document.getElementById('mhdk0').checked && !document.getElementById('mhdk1').checked && !document.getElementById('mhdk2').checked) {
				bValid = false;
				updateTips('Vui lòng chọn môn học ôn tập');
			}
			
			bValid = bValid && checkLength( jRecapcha, "\"Recapcha\"", 0, 100);
			
			//alert (bValid);
			if (bValid) {
				if (!dongydangky){
					var tongtien = 0;
					var stmh = 0;
					var giatien = 30000;
					var strmhdk = '<table style=font-size:100% >'
						+ '<tr><td align=left colspan=3><strong>Đã đăng ký các môn học sau: </strong></td></tr>'
						+ '<tr><td><b>Môn Học</b></td><td style=\'width:50px\'><b>Số Tiết</b></td><td align=right style=\'width:150px\'><b>Học Phí</b></td></tr>';
						
					if (document.getElementById('mhdk0') != null && document.getElementById('mhdk0').checked) {
						stmh = document.getElementById('stmh0').value;
						strmhdk += '<tr><td>'+document.getElementsByTagName('label')[6].firstChild.data + '</td><td align=center>' + stmh +'</td><td align=right>'+ formatCurrency(stmh*giatien) +' VNĐ</td>';
						tongtien += stmh*giatien;
					}
					if (document.getElementById('mhdk1') != null && document.getElementById('mhdk1').checked) {
						stmh = document.getElementById('stmh1').value;
						strmhdk += '<tr><td>'+document.getElementsByTagName('label')[7].firstChild.data + '</td><td align=center>' + stmh +'</td><td align=right>'+formatCurrency(stmh*giatien)+' VNĐ</td>';
						tongtien += stmh*giatien;
					}
					if (document.getElementById('mhdk2') != null && document.getElementById('mhdk2').checked) {
						stmh = document.getElementById('stmh2').value;
						strmhdk += '<tr><td>'+document.getElementsByTagName('label')[8].firstChild.data + '</td><td align=center>' + stmh +'</td><td align=right>'+formatCurrency(stmh*giatien) +' VNĐ</td>';
						tongtien += stmh*giatien;
					}
					if (document.getElementById('mhdk3') != null && document.getElementById('mhdk3').checked) {
						stmh = document.getElementById('stmh3').value;
						strmhdk += '<tr><td>'+document.getElementsByTagName('label')[9].firstChild.data + '</td><td align=center>' + stmh +'</td><td align=right>'+formatCurrency(stmh*giatien) +' VNĐ</td>';
						tongtien += stmh*giatien;
					}
					strmhdk += '<tr><td colspan=2 align=right>Tổng cộng:</td><td align=right><b>'+formatCurrency(tongtien) +' VNĐ</b></td>';
					strmhdk += '</table>';
					
					noidungprint = "<strong>Học Viên: " + jHo.val() + " " + jTen.val() + "</strong><br/>"
					+"Sinh ngày: " + jNgaySinh.val() + "<br/>"
					+"Nơi Sinh: " + $("#comNoiSinh option:selected").html() + "<br>"
					+"Email: " + jEmail.val()
					+"<p>" + strmhdk + "</p>";
					
					//alert(noidungprint);
					$( "#msginfo" ).html(noidungprint);
					$( "#dialog-confirm" ).dialog("open");
				}
			}
			
			return dongydangky;
			
		});	// end $("#btnSubmit")
		
		
		$( "#dialog-confirm" ).dialog({
			autoOpen: false,
			height:300,
			width:400, 
			modal: true,
			buttons: {
				"Đồng Ý": function() {
					dongydangky = true;
					$("#print").val(noidungprint);
					//writeConsole(noidungprint);
					$( this ).dialog( "close" );
					$("#form_dkontap").submit();
				},
				"Hủy đăng ký": function() {
					dongydangky = false;
					$( this ).dialog( "close" );
				}
			}
		});
		
		updateMH($("#comNganh").val());
		<?php 
			if ($error != null)
				echo 'updateTips("Bạn đã nhập sai các ký tự xác thực. Vui lòng nhập lại và chọn môn học đăng ký.");';
		?>
		
	});
	</script>

	<?php
	// Update lai form khi nhap sai ky tu recapcha
	if ($error != null) {
		//$search = array('\\',"'",'"');
		//$replace = array('\\\\',"\'","&quot;"); 
	?>
		<script>
			document.getElementById('comNganh').value = '<?php echo $_POST["comNganh"]; ?>';
			document.getElementById('txtHo').value = '<?php echo $_POST["txtHo"]; ?>';
			document.getElementById('txtTen').value = '<?php echo $_POST["txtTen"]; ?>';
			document.getElementById('txtNgaySinh').value = '<?php echo $_POST["txtNgaySinh"]; ?>';
			document.getElementById('txtEmail').value = '<?php echo $_POST["txtEmail"]; ?>';
			document.getElementById('comNoiSinh').value = '<?php echo $_POST["comNoiSinh"]; ?>';
			updateMH($("#comNganh").val());
		</script>
	<?php
	}
	?>
	<div id="dialog-confirm" title="Xác nhận thông tin đăng ký ôn tập" style = "background-color: white">
		<div style="width:370px;">
		<p><span class="ui-icon ui-icon-check" style="float:left; margin:0 5px 100px 0;"></span><span id=msginfo>Học Viên:  <br/></span></p>
		</div>
	</div>
<?php
} // end if !$hethan
else
{
	$sqlstr = "SELECT value	FROM config WHERE name='DK_ON_TAP_THONG_BAO'";
	$stmt = oci_parse($db_conn, $sqlstr);oci_execute($stmt);$n = oci_fetch_all($stmt, $resDM);oci_free_statement($stmt);
	$dkontap_thongbao = $resDM["VALUE"][0];
	
	echo "<div align=center style='font-size: 12px; color:red; margin-top: 15px;'><b>$dkontap_thongbao</b></div>";
}
?>

</body>
</html>

<?php
if (isset ($db_conn)){
	oci_close($db_conn);
}
?>