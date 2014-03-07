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
if (!allowPermisstion(base64_decode($_SESSION['uidloginPortal']), '002', $db_conn))
{
	die('Truy cập bất hợp pháp'); 
}
$macb = $_POST['m'];
$a = $_POST['a'];

if ($macb == '') 
	$macb = $_SESSION['macb'];

$z = 1;
?>

<?php
if ($a != 'get_llkh')
{
?>
<a id="khcn_print_huongdan_btn_printpreview">&nbsp;In ...</a>

  <div align="center" style="margin-top:10px; font-family:Arial,Helvetica,sans-serif;" id="khcn_chitiethuongdan_in_tmdt">
<?php
}
?>
    <table width="100%"   cellspacing="0" cellpadding="0" class="ui-corner-all shawdow tableData" >

	  <tr>
        <td colspan=2 valign='top'> 
		<div align="center"  style="margin-top:20px; margin-bottom:20px"><b><font style="font-size:160%; font-weight:bold;">HƯỚNG DẪN IN THUYẾT MINH ĐỀ TÀI TỐT NHẤT</font></b><br/><font color=red>(Dùng trình duyệt <b>Mozilla Firefox</b> hoặc copy toàn bộ nội dung biểu mẫu vào World để canh lề và in là tốt nhất)</font></div>
        </td>
      </tr>
      <tr>
        <td colspan=2>
      
			<table width="100%" border="0" align="center" cellpadding="5" cellspacing="0" class="fontcontent" style="font-family:Arial,Helvetica,sans-serif;">
			
				<tr align="left">        
					<td align=left colspan=2><b>Để in biểu mẫu ta làm theo các bước sau:</b> Nếu máy <b>đã có Mozilla Firefox</b> thì <b>không</b> cần thực hiện <b>bước 2</b>
					<br/> <font color=red><u>Lưu ý:</u> <b>bắt buộc phải chọn Date/Time cho ô Footers Center (bước 7)</b></font> 
					</td>
				</tr>
				<tr align="left">        
					<td align=left style="width:20px" valign=top>
						<b><?php echo $z++ . "." ?></b>
					</td>
					<td  style="">Cần sử dụng trình duyệt (browser) hổ trợ in ấn tốt nhất hiện nay là 
					<b>Mozilla Firefox phiên bản mới nhất</b>, quý thầy cô có thể download và cài đặt tại địa chỉ sau 
						<b><a href='http://www.mozilla.org/en-US/firefox/fx/#desktop' target=_blank>http://www.mozilla.org/en-US/firefox/fx/#desktop</a></b>
					</td>
				</tr> 
				<tr align="left">        
					<td align=left style="width:20px" valign=bottom><b><?php echo $z++ . "." ?></b></td>
					<td  style="">Click vào biểu tượng <img src="icons/Firefox-icon32.png" width=24 height=24 style="margin-top:0px;"> để khởi động và đăng nhập lại vào trang cá nhân của quý thầy cô.
					</td>
				</tr>
				<tr align="left">        
					<td align=left style="width:20px" valign=bottom><b><?php echo $z++ . "." ?></b></td>
					<td  style="">Click vào biểu tượng <img src="icons/print-preview-icon24x24.png" style="margin-top:10px;"> tương ứng với từng thuyết minh đề tài cần in, 1 tab mới sẽ được mở ra chứa nội dung là thuyết minh đề tài.<br/></td>
				</tr>
				<tr align="left">        
					<td align=left style="width:20px" valign=top><b><?php echo $z++ . "." ?></b></td>
					<td  style=""> Tiếp theo, Bấm nút In ở góc trái như hình bên dưới<br/>
						<img src="images/huongdan/tmdt/in-tmdt-print.png" style="margin-top:10px;">
					</td>
				</tr>
				<tr align="left">        
					<td align=left style="width:20px" valign=top><b><?php echo $z++ . "." ?></b></td>
					<td  style=""> Sau khi bấm nút In sẽ mở ra 1 cửa sổ browser mới (Nếu không có cửa sổ nào được mở có thể đã bị block pop-up, vui lòng set allow pop-up và thực hiện lại bước trước đó).<br/>
					Tiếp tục bấm vào nút <b>Firefox</b> (nút màu cam ở góc trên bên trái của browser).<br/>Sau đó chọn <b>Print Preview</b> trong menu Print như hình bên dưới.<br/>
					<b><u>Lưu ý:</u></b> Nếu không thấy nút Firefox, menu được sắp xếp ở dạng khác. Khi đó tìm <b>Print Preview</b> trong menu <b>File</b><br/>
						<img src="images/huongdan/tmdt/in-tmdt-preview.png" style="margin-top:10px;">
					</td>
				</tr>
				<tr align="left">        
					<td align=left style="width:20px" valign=top><b><?php echo $z++ . "." ?></b></td>
					<td  style=""> Cửa sổ <b>Print Preview</b> được mở như hình bên dưới, chú ý <b>chọn</b> thông số <b>Scale = Shrink To Fit</b>, <b>Portrait</b> như hình<br/>
						<img src="images/huongdan/tmdt/in-tmdt-preview-s.png" style="margin-top:10px;">
					</td>
				</tr>
				
				<tr align="left">        
					<td align=left style="width:20px" valign=top><b><?php echo $z++ . "." ?></b></td>
					<td  style=""> Để tùy chỉnh <b>header</b>, <b>footer</b>, <b>số trang</b>, <b>canh lề</b> tiếp tục bấm nút <b>Page Setup</b> và chọn tab <b>Margins & Header/Footer</b>,<br /><font color=red><u>Lưu ý:</u> <b>bắt buộc phải chọn Date/Time cho ô Footers Center</b></font><br/>
						<img src="images/huongdan/in-ttkh-page-setup.png" style="margin-top:10px;"> <br/>
						- Top, Right, Left, Bottom: các giá trị lề Trên, Phải, Trái, Dưới đơn vị là mm<br/> 
						- Headers & Footers gồm 6 ô dữ liệu: <br/> &nbsp; &nbsp; &nbsp; + <b>3 ô trên</b> tương ứng 3 vị trí Trái trên, Giữa trên , Phải trên của <b>Header</b><br/> &nbsp; &nbsp; &nbsp; + <b>3 ô dưới</b> tương ứng 3 vị trí Trái dưới, <b>Giữa dưới</b> , Phải dưới của <b>Footer</b><br/>
						- Ý nghĩa các thông số dữ liệu: <br/>
						&nbsp; &nbsp; &nbsp; <i>--blank--</i> : để trống <br/>
						&nbsp; &nbsp; &nbsp; <i>Title</i> : tiêu đề mặc định<br/>
						&nbsp; &nbsp; &nbsp; <i>URL</i> : địa chỉ liên kết<br/>
						&nbsp; &nbsp; &nbsp; <b><i>Date/Time</i> : ngày và giờ in TMĐT</b><br/>
						&nbsp; &nbsp; &nbsp; <i>Page #</i> : số trang dạng Page #<br/>
						&nbsp; &nbsp; &nbsp; <i>Page # of #</i> : số trang dạng Page # of #<br/>
						&nbsp; &nbsp; &nbsp; <i>Custom</i> : nội dung text do người dùng nhập vào.
						<p>
						<b>Thiết lập các thông số, sau đó bấm OK để xem kết quả trước khi In</b>
						<p>
					</td>
				</tr>
				<tr align="left">        
					<td align=left style="width:20px" valign=top><b><?php echo $z++ . "." ?></b></td>
					<td  style=""> Nếu đã hài lòng với kết quả xem trước khi in, Bấm nút <b>Print</b> tiến hành in lý lịch ra giấy.<br/>
						Nếu chưa vừa ý, quay lại bước 6, 7 để điều chỉnh đến khi có kết quả mong muốn.
						<br/> <br/>
						<b><u>Lưu ý:</u></b><br/><br/>
						<b>i.</b> Nếu sử dụng trình duyệt Chrome hay IE, kết quả in Biểu mẫu có khả năng sẽ lỗi sau: <br/>
						<img src="images/huongdan/loiin.png" style="margin-top:10px;"> <br/>
						Vì vậy <b>tốt nhất</b> người dùng nên sử dụng <b>Mozilla Firefox</b>.
						<br/><br/>
						<b>ii.</b> <font color=red><b>Bắt buộc phải chọn Date/Time cho ô Footers Center</b></font>
					
					</td>
				</tr>
				
			</table>
      
        </td>
      </tr>
    </table>
<?php
if ($a != 'get_llkh')
{
?>
  </div>

<script type="text/javascript">
//jQuery.ajax
//$(document).ready(function(){
$(function(){

 $( "#khcn_print_huongdan_btn_printpreview" ).button({ icons: {primary:'ui-icon ui-icon-print'} });
 $( "#khcn_print_huongdan_btn_printpreview" ).click(function(){
	print_llkh_writeConsole($("#khcn_chitiethuongdan_in_tmdt").html(), 0);
 });

});
</script>
<?php 
}
?>
<?php 
if (isset ($db_conn))
	oci_close($db_conn);
?>