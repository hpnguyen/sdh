<?php
error_reporting(1);
//session_start(); 
//$fk_id=base64_encode(1);
//$dk_search='';
?>

<?php
include('connect.php'); 

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

//if (isset($_POST['txtSoVB']))
	$SoVB = $_POST['txtSoVB'];
	
//if (isset($_POST['txtMaHV']))
	$MaHV = $_POST['txtMaHV'];

//if (isset($_POST['txtHoTen'])){
	$HoTen = $_POST['txtHoTen'];
	$NgaySinh = $_POST['txtNgaySinh'];
//}

// Recapcha
//require_once('recaptchalib.php');                
// Get a key from https://www.google.com/recaptcha/admin/create
$publickey = "6LcDrc8SAAAAAOehS2lVAVXYUefcRz94cIJK6UbZ";
$privatekey = "6LcDrc8SAAAAACyVrTs8yuqB_vHagESgu29bKSoF";

# the response from reCAPTCHA
$resp = null;
# the error code from reCAPTCHA, if any
$error = null;

$ma_hv = 'N/A';

/*
 if ($_POST["recaptcha_response_field"]) {
		$resp = recaptcha_check_answer ($privatekey,
										$_SERVER["REMOTE_ADDR"],
										$_POST["recaptcha_challenge_field"],
										$_POST["recaptcha_response_field"]);

		if ($resp->is_valid) {
			
		} else {
				# set the error code so that we can display it
				$error = $resp->error;
		}
}
// End Recapcha
*/
?>
<META http-equiv=Content-Type content='text/html; charset=utf-8'>
<HTML>
<TITLE>Phòng đào tạo Sau đại học - ÐH Bách khoa TP.HCM</TITLE> 
<link href="includes/main.css" rel="stylesheet" type="text/css">

<style type="text/css" media="screen">
@import url("./top_menu.css");
.style4 {color: #0000FF}

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

<script src="hv/js/jquery.maskedinput.js" type="text/javascript"></script>
<script language="JavaScript1.2" type="text/javascript" src="mm_css_menu.js"></script>
<script type="text/JavaScript">
<!--
function openBox(path) {
para='width=800,height=715,scrollbars=no,toolbar=no,top=200,left=230'
newWindow = open(path,'a',para);
}

function MM_swapImgRestore() { //v3.0
  var i,x,a=document.MM_sr; for(i=0;a&&i<a.length&&(x=a[i])&&x.oSrc;i++) x.src=x.oSrc;
}

function MM_preloadImages() { //v3.0
  var d=document; if(d.images){ if(!d.MM_p) d.MM_p=new Array();
    var i,j=d.MM_p.length,a=MM_preloadImages.arguments; for(i=0; i<a.length; i++)
    if (a[i].indexOf("#")!=0){ d.MM_p[j]=new Image; d.MM_p[j++].src=a[i];}}
}

function MM_findObj(n, d) { //v4.01
  var p,i,x;  if(!d) d=document; if((p=n.indexOf("?"))>0&&parent.frames.length) {
    d=parent.frames[n.substring(p+1)].document; n=n.substring(0,p);}
  if(!(x=d[n])&&d.all) x=d.all[n]; for (i=0;!x&&i<d.forms.length;i++) x=d.forms[i][n];
  for(i=0;!x&&d.layers&&i<d.layers.length;i++) x=MM_findObj(n,d.layers[i].document);
  if(!x && d.getElementById) x=d.getElementById(n); return x;
}

function MM_swapImage() { //v3.0
  var i,j=0,x,a=MM_swapImage.arguments; document.MM_sr=new Array; for(i=0;i<(a.length-2);i+=3)
   if ((x=MM_findObj(a[i]))!=null){document.MM_sr[j++]=x; if(!x.oSrc) x.oSrc=x.src; x.src=a[i+2];}
}
function OpenPopup(c) {
                window.open(c, 'window',
'width=650,height=520,scrollbars=yes,status=yes');
}

window.onload = function() {
	var arrInputs = document.getElementsByTagName("input");
	for (var i = 0; i < arrInputs.length; i++) {
		var curInput = arrInputs[i];
		if (!curInput.type || curInput.type == "" || curInput.type == "text")
			HandlePlaceholder(curInput);
	}
};

function HandlePlaceholder(oTextbox) {
	if (typeof oTextbox.placeholder == "undefined") {
		var curPlaceholder = oTextbox.getAttribute("placeholder");
		if (curPlaceholder && curPlaceholder.length > 0) {
			oTextbox.value = curPlaceholder;
			oTextbox.setAttribute("old_color", oTextbox.style.color);
			oTextbox.style.color = "#c0c0c0";
			oTextbox.onfocus = function() {
				this.style.color = this.getAttribute("old_color");
				if (this.value === curPlaceholder)
					this.value = "";
			};
			oTextbox.onblur = function() {
				if (this.value === "") {
					this.style.color = "#c0c0c0";
					this.value = curPlaceholder;
				}
			};
		}
	}
};
//-->

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



<body background='images/bg_small.gif' onLoad="MM_preloadImages('Untitled-4_r2_c1_f2.gif')">
<TABLE width="800px" align="center" valign="top" border="0" cellpadding="0" cellspacing="0"  class="shawdow" >
<TR>
  <TD colspan="3"><? include('header.php'); ?></TD>
</TR>
<TR>
<TD colspan="3">
<TABLE width="100%" border="0" align="center" cellpadding="0" cellspacing="0" bgcolor="#FFFFFF">
<TR>
<TD width="18%"  valign="top" bgcolor="#c4d9ef"><? include('left_tinh.php'); ?>&nbsp;</TD>
<TD width="514" valign="top" class="body_main">
<?php
if (!isset($_POST['txtSoVB']) && !isset($_POST['txtMaHV']) && !isset($_POST['txtHoTen']) ) 
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

	<div style="width:100%;">
		<div style="width:415px; color:black;margin-top:10px;margin-bottom:15px;font-weight:bold; font-size: 14px;" align=center>
			TRA CỨU VĂN BẰNG
		</div>
		<?php 
		if (!$registerSuccess)
		{
		?>
		<div style="padding-left:10px;padding-right:10px;font-size: 10pt;">
			<form id="form_tracuuvbang" name="form_tracuuvbang" method="post" action="tracuuvbang.php">
			<table style="width:415px">
			<tr>
			<td colspan=2><b>Tra cứu theo:</b></td>
			</tr>
			<tr >
			<td><label for=txtSoVB>Số hiệu bằng</label></td><td align=left><input style="width:150px" type=text name=txtSoVB id=txtSoVB></td>
			</tr>
			<tr>
			<td colspan=2><i>hoặc</i></td>
			</tr>
			<tr>
			<td><label for=txtMaHV>Mã số học viên</label></td><td align=left><input style="width:100px" type=text name=txtMaHV id=txtMaHV></td>
			</tr>
			<tr>
			<td colspan=2><i>hoặc</i></td>
			</tr>
			<tr>
			<td ><label for=txtHoTen>Họ tên</label></td><td align=left><input style="width:200px" type=text name=txtHoTen id=txtHoTen></td>
			</tr>
			
			<tr>
			<td><label for=txtNgaySinh>& Ngày sinh</label></td><td align=left><input style="width:80px;" type=text name=txtNgaySinh id=txtNgaySinh> <span style="font-size:10px; color:blue">dd/mm/yyyy</span></td>
			</tr>
			<tr>
			<td colspan=2><div style="margin-top:5px; width:100%;  color:red;" id="tipOnTap" align=center></div></td>
			</tr>
			<tr>
			<td colspan=2 align=center>
				<script type="text/javascript">
					 var RecaptchaOptions = {
						theme : 'white'
					 };
				 </script>
				<?php
				//if (!$resp->is_valid)
					//echo recaptcha_get_html($publickey, $error);
				?>
				<!-- end recaptcha_div  -->
			</td>
			</tr>
			
			<tr>
			<td colspan=2><div style="margin-top:5px; font-size:60%" align=center><input type=submit id=btnSubmit name=btnSubmit value="Tra cứu"></div></td>
			</tr>
			</table>
			<input type=hidden name="print" id="print" value=''>
			</form>
		</div>
		<?php
		}
		else // $registerSuccess = true
		{
		} 
		?>
	</div>
	<script>
			
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

		function isValidDate(controlName, format){ //format = 'dd/mm/yyyy'
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
		var jHoTen		= $("#txtHoTen"),
		jNgaySinh = $("#txtNgaySinh"),
		jSoVB = $("#txtSoVB"),
		jMaHV = $("#txtMaHV"),
		jRecapcha = $("#recaptcha_response_field"),
		allFieldsLogin = $([]).add(jSoVB).add(jHoTen).add(jNgaySinh).add(jMaHV).add(jRecapcha),
		tipsOnTap	= $("#tipOnTap");
		var dongydangky = false;
		var noidungprint = '';

		$("#form_tracuuvbang").submit(function() {
			//alert(document.getElementsByTagName('label')[5].firstChild.data);
			//alert( $( "#lblmhdk0" ).html() );
			var bValid = true;
			allFieldsLogin.removeClass( "ui-state-error1" );
			
			if (jSoVB.val()=='') {
				if (jMaHV.val()=='') { // Kiem tra ho ten ngay sinh
					if (jHoTen.val()=='' || jNgaySinh.val()=='') {
						
						
						
						bValid = false;
						updateTips('Vui lòng nhập Số văn bằng hoặc Mã học viên hoặc Họ tên & Ngày sinh');
					}else{
						// Co Ho Ten & Ngay Sinh
						// Kiem Tra ngay sinh dung hay sai
						if (bValid && !isValidDate('txtNgaySinh','dd/mm/yy'))
						{
							bValid = false;
							document.getElementById('txtNgaySinh').focus();
							jNgaySinh.addClass( "ui-state-error1" );
							updateTips('Ngày Sinh không chính xác.');
						}
					}
				}else{
					// Co Nhap MA HV
				}
			}else{
				// Co Nhap Ma van bang
			}
			
			
			//alert(isValidDate('txtNgaySinh','dd/mm/yy'));
			//alert (dongydangky);
					
			//bValid = bValid && checkLength( jRecapcha, "\"Recapcha\"", 0, 100);
			
			//alert (bValid);
			if (bValid) {
				
			}
			
			return bValid;
			
		});	// end $("#btnSubmit")
		
		<?php 
			//if ($error != null)
			//	echo 'updateTips("Bạn đã nhập sai các ký tự xác thực.");';
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
			/*document.getElementById('comNganh').value = '<?php echo $_POST["comNganh"]; ?>';
			document.getElementById('txtHo').value = '<?php echo $_POST["txtHo"]; ?>';
			document.getElementById('txtTen').value = '<?php echo $_POST["txtTen"]; ?>';
			document.getElementById('txtNgaySinh').value = '<?php echo $_POST["txtNgaySinh"]; ?>';
			document.getElementById('txtEmail').value = '<?php echo $_POST["txtEmail"]; ?>';
			document.getElementById('comNoiSinh').value = '<?php echo $_POST["comNoiSinh"]; ?>';
			updateMH($("#comNganh").val());*/
		</script>
	<?php
	}
} // end if !$hethan
else
{
	if ($SoVB != null){
		$SoVB = trim(str_replace(array("'","BM:"), array("''",""),$SoVB));
		$q="UPPER(X.SO_HIEU_BANG) = 'BM:".strtoupper($SoVB)."'";
	}elseif ($MaHV != null){
		$MaHV = str_replace("'", "''",$MaHV);
		$q="UPPER(X.MA_HOC_VIEN) = TRIM('".strtoupper($MaHV)."')";
	}elseif ($HoTen != null){
		$HoTen = strtoupper(str_replace("'", "''",vn_str_filter($HoTen)));
		$NgaySinh  = str_replace("'", "''",$NgaySinh);
		$q="TRIM(UPPER(VIET0DAU(H.HO || ' ' || H.TEN))) = TRIM('".strtoupper($HoTen)."')
		AND NGAY_SINH = TO_DATE('$NgaySinh', 'dd/mm/yyyy')";
	}
		//$mh = explode(",", $_REQUEST["lk"]);
		//echo $mh[0];
		//echo $mh[1];
	$MA_NGANH = $_REQUEST["lk"];
	$sqlstr = "	SELECT X.MA_HOC_VIEN, (H.HO || ' ' || H.TEN) ho_ten, to_char(H.NGAY_SINH,'dd/mm/yyyy') ngay_sinh, 
				tp.ten_tinh_tp noi_sinh, h.khoa, n.ten_nganh, 
				x.so_hieu_bang, x.so_dang_ky, x.dot_cap_bang
				FROM XET_LUAN_VAN X, HOC_VIEN H, NGANH N, DM_TINH_TP TP
				WHERE X.MA_HOC_VIEN = H.MA_HOC_VIEN
				AND DOT_CAP_BANG IS NOT NULL
				AND H.MA_NGANH = N.MA_NGANH
				AND H.NOI_SINH = TP.MA_TINH_TP (+)
				AND $q";
	//echo $sqlstr;
	$stmt = ociparse($db_conn, $sqlstr);
	ociexecute($stmt);
	$n = ocifetchstatement($stmt, $resDM);
	ocifreestatement($stmt);
	if ($n>0)
	{
		echo "<div style='margin-top:10px; font-weight:bold;' align=left>
				<table>
				<tr><td style='font-weight:bold;'>
				Thông tin văn bằng
				</td>
				</tr>
				</table>
			</div>";
		for ($i = 0; $i < $n; $i++)
		{
			?>
			<div style="margin-top:10px;">
			<table align=center>
			<tr>
			<td align=right >Mã HV:</td><td align=left style="font-weight:bold;"><?php echo $resDM['MA_HOC_VIEN'][$i]; ?></td>
			</tr>
			<tr>
			<td align=right >Họ tên:</td><td align=left style="font-weight:bold;"><?php echo $resDM['HO_TEN'][$i]; ?></td>
			</tr>
			<tr>
			<td align=right >Ngày sinh:</td><td align=left style="font-weight:bold;"><?php echo $resDM['NGAY_SINH'][$i]; ?></td>
			</tr>
			<tr>
			<td align=right >Nơi sinh:</td><td align=left style="font-weight:bold;"><?php echo $resDM['NOI_SINH'][$i]; ?></td>
			</tr>
			<tr>
			<td align=right >Tên ngành:</td><td align=left style="font-weight:bold;"><?php echo $resDM['TEN_NGANH'][$i]; ?></td>
			</tr>
			<tr>
			<td align=right >Số hiệu bằng:</td><td align=left style="font-weight:bold;"><?php echo $resDM['SO_HIEU_BANG'][$i]; ?></td>
			</tr>
			<tr>
			<td align=right >Đợt cấp bằng:</td><td align=left style="font-weight:bold;"><?php echo $resDM['DOT_CAP_BANG'][$i]; ?></td>
			</tr>
			<tr>
			<td align=right >Số đăng ký:</td><td align=left style="font-weight:bold;"><?php echo $resDM['SO_DANG_KY'][$i]; ?></td>
			</tr>
			</table>
			</div>
			<?php
		}
	}
	else
	{
		?>
			<div style="margin-top:15px;" align=center>
				<table>
				<tr><td style='font-weight:bold;'>
				Không tìm thấy văn bằng
				</td>
				</tr>
				</table>
			</div>
		<?php
	}
}
?>

</TD>
<TD align="right" valign="top" width="22%" bgcolor="#c4d9ef"><? include('right.php'); ?></TD>
</TR>
</TABLE>
</TD>
</TR>
<TR>
  <TD colspan="3"><?php include('footer.php'); ?></TD>
</TR>
</TABLE>
</body>
</html>