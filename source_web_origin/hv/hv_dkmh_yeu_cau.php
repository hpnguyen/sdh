<?php
// Chức năng này sử dụng các tham số trong config như sau:
// DOT_HOC_DKMH, DKMH_CHO_PHEP, DKMH_NGAY_BAT_DAU, DKMH_NGAY_HET_HAN

if (isset($_REQUEST["hisid"])){
	session_id($_REQUEST["hisid"]);
	session_start();
}
if (!isset($_SESSION['uidloginhv'])){
	die('Truy cập bất hợp pháp'); 
}

include "libs/connect.php";
include "libs/pgslibshv.php";

date_default_timezone_set('Asia/Ho_Chi_Minh');

$usr = base64_decode($_SESSION["uidloginhv"]);
$pass = base64_decode($_SESSION["pidloginhv"]);
$result=allowUser($usr,$pass,$db_conn);
if ($result==0) {
	die('Truy cập bất hợp pháp');
}

$mahv = base64_decode($_SESSION["mahv"]);
$khoa = base64_decode($_SESSION["khoa"]);

// Kiểm tra được phép đăng ký môn học

// DANG_HOC = 0 : nghi; 1: dang hoc; 2: da ra truong cap bang; 3: da chuyen truong; 4: bo hoc
$sqlstr="SELECT count(*) DANG_HOC
FROM hoc_vien
WHERE dot_cap_bang (ma_hoc_vien) is null
AND ma_hoc_vien ='$mahv'";

$stmt = oci_parse($db_conn, $sqlstr);oci_execute($stmt);$n = oci_fetch_all($stmt, $resDM);oci_free_statement($stmt);
$danghoc = $resDM["DANG_HOC"][0];

if ($danghoc!=1)
{
	if ($mahv!='03207104')
		die("<div align=center>BẠN ĐÃ TỐT NGHIỆP KHÔNG ĐƯỢC PHÉP ĐĂNG KÝ MÔN HỌC</div>");
}

$sqlstr="SELECT value
FROM config
WHERE name='DKMH_CHO_PHEP'";
$stmt = oci_parse($db_conn, $sqlstr);oci_execute($stmt);$n = oci_fetch_all($stmt, $resDM);oci_free_statement($stmt);
$allowDKMH = $resDM["VALUE"][0];

if (!$allowDKMH)
{
	//die("<div align=center>CHƯA ĐƯỢC PHÉP ĐĂNG KÝ MÔN HỌC</div>");
}

// Kiểm tra hạn đăng ký môn học
$today = date("d/m/Y");

$sqlstr="SELECT value , floor(sysdate - to_date(value,'dd/mm/yyyy')) het_han FROM config WHERE name='DKMH_NV_NGAY_HET_HAN'";
//echo $sqlstr;
$stmt = oci_parse($db_conn, $sqlstr);oci_execute($stmt);$n = oci_fetch_all($stmt, $resDM);oci_free_statement($stmt);
$ngayhethanDKMH = $resDM["VALUE"][0];
$hethan = $resDM["HET_HAN"][0];

$sqlstr="SELECT value , (sysdate - to_date(value,'dd/mm/yyyy')) bat_dau FROM config WHERE name='DKMH_NV_NGAY_BAT_DAU'";
//echo $sqlstr;
$stmt = oci_parse($db_conn, $sqlstr);oci_execute($stmt);$n = oci_fetch_all($stmt, $resDM);oci_free_statement($stmt);
$ngaybatdauDKMH = $resDM["VALUE"][0];
$batdau = $resDM["BAT_DAU"][0];

if ($batdau<0)
{
	//die("<div align=center style='color:red'>CHƯA ĐẾN NGÀY ĐĂNG KÝ MÔN HỌC THEO KẾ HOẠCH TỪ <b>$ngaybatdauDKMH</b> - <b>$ngayhethanDKMH</b></div>");
}

if ($hethan>0)
{
	//die("<div align=center style='color:red'>ĐÃ HẾT HẠN ĐĂNG KÝ MÔN HỌC NGÀY <b>$ngayhethanDKMH</b></div>");
}

// Lấy đợt học, nam hoc - hoc ky
/*$sqlstr="SELECT max(dot_hoc) dot_hoc_dkmh, 
to_char(max(dot_hoc),'DD/MM/YYYY') dot_hoc_f 
FROM thoi_khoa_bieu";*/
$sqlstr="select to_char(to_date(value), 'dd/mm/yyyy') dot_hoc_f, to_date(value) dot_hoc_dkmh
from config
where name='DOT_HOC_DKMH_NV'";
$stmt = oci_parse($db_conn, $sqlstr);oci_execute($stmt);$n = oci_fetch_all($stmt, $resDM);oci_free_statement($stmt);

$dot_hoc=$resDM["DOT_HOC_DKMH"][0];
$dot_hoc_f=$resDM["DOT_HOC_F"][0];

$sqlstr="SELECT (hoc_ky || '/' || nam_hoc_tu || '-' || nam_hoc_den) namhoc FROM dot_hoc_nam_hoc_ky WHERE dot_hoc='$dot_hoc'";
$stmt = oci_parse($db_conn, $sqlstr);oci_execute($stmt);$n = oci_fetch_all($stmt, $resDM);oci_free_statement($stmt);

$namhoc=$resDM["NAMHOC"][0];

//Thông tin học viên
$sqlstr="SELECT ho || ' ' || ten ho_ten, viet0dau(ho) || ' ' || viet0dau(ten) ho_ten_khong_dau, 
TEXT_CTDT_KN(ma_hoc_vien) huongdan_so_tin_chi_khoa_nganh FROM hoc_vien WHERE ma_hoc_vien='$mahv'";
$stmt = oci_parse($db_conn, $sqlstr);oci_execute($stmt);$n = oci_fetch_all($stmt, $resDM);oci_free_statement($stmt);

$hoten = $resDM["HO_TEN"][0];
$hotenkhongdau = $resDM["HO_TEN_KHONG_DAU"][0];
$huongdan = $resDM["HUONGDAN_SO_TIN_CHI_KHOA_NGANH"][0];
?>
<script type="text/javascript">
var schedules = new Array();
var thuChu = ["","Chủ Nhật", "Thứ Hai", "Thứ Ba", "Thứ Tư", "Thứ Năm", "Thứ Sáu", "Thứ Bảy"];
var numMHdaDK = 0;
var numTongSoTCdaDK=0;
var numSoTCDangChon = 0;
</script>

<div align="left" style="margin:0 auto;">
<form id="form_dkmh_nv" name="form_dkmh_nv" method="post" action="">
	<div align=center style='margin:10px 0 20px 0; font-size: 13px'>
		<div ><b>ĐĂNG KÝ MÔN HỌC THEO NGUYÊN VỌNG <span style="color: #119adf">HK <?php echo $namhoc; ?> </span></b></div>
		<?php
		if ($batdau<0 )
		{
			echo "<div align=center style='color:red'>CHƯA ĐẾN NGÀY ĐĂNG KÝ MÔN HỌC THEO KẾ HOẠCH TỪ <b>$ngaybatdauDKMH</b> - <b>$ngayhethanDKMH</b></div>";
		}
		else if ($hethan>0 )
		{
			echo "<div align=center style='color:red'>ĐÃ HẾT HẠN ĐĂNG KÝ MÔN HỌC NGÀY <b>$ngayhethanDKMH</b></div>";
		}
		else
		{
		?>
			<div style='margin:5px 0 0 0'>Hạn đăng ký từ <font color=red><b><?php echo $ngaybatdauDKMH; ?></b></font> đến <font color=red><b><?php echo $ngayhethanDKMH; ?></b></font></div>
		<?php 
		}
		?>
	</div>
	<div align=left style="margin-bottom:5px; font-weight:bold">
		<?php echo $huongdan; ?>
	</div>
	<div id="accordion_dkmh_nv">
		<h3><a href="#section1">Môn học đã đăng ký</a></h3>
		<div>
			<div id=dkmh_nv_mhdadk align=center></div>
			<div style='margin-top:10px; font-size:80%;' align=left>
				<a id=dkmh_nv_btn_delete>&nbsp;Hủy đăng ký</a>
			</div>
		</div>
		<h3><a href="#section2">Đăng ký môn học theo nguyện vọng</a></h3>
		<div>
		<?php
		if ($batdau<0 )
		{
			echo "<div align=center style='color:red'>CHƯA ĐẾN NGÀY ĐĂNG KÝ MÔN HỌC THEO KẾ HOẠCH TỪ <b>$ngaybatdauDKMH</b> - <b>$ngayhethanDKMH</b></div>";
		}
		else if ($hethan>0 )
		{
			echo "<div align=center style='color:red'>ĐÃ HẾT HẠN ĐĂNG KÝ MÔN HỌC NGÀY <b>$ngayhethanDKMH</b></div>";
		}
		else
		{
		?>
			<table width="100%" border="0" cellspacing="0" cellpadding="5" align=center>
			<tr>
			  <td align=right style="width:80px;">
				<span class="heading">
				<label for="dkmh_nv_khoa">Chọn khóa</label>
				</span>
			  </td>
			  <td align=left>
				<select id=dkmh_nv_khoa name=dkmh_nv_khoa style="font-size:15px;" onChange="dkmh_nv_updateNganh(this.value)">
					<?php
					$sqlstr="SELECT DISTINCT khoa 
						FROM tkb_mh_tham_do 
						WHERE dot_hoc = (SELECT max(dot_hoc) FROM tkb_mh_tham_do) 
						ORDER BY khoa DESC";
					$stmt = oci_parse($db_conn, $sqlstr);oci_execute($stmt);$n = oci_fetch_all($stmt, $resDM);oci_free_statement($stmt);
					for ($i = 0; $i < $n; $i++)
					{
						($resDM["KHOA"][$i]==$khoa) ? $selected = "selected style='background-color: #075385; color:white;'" :	$selected = "";
						echo "<option value='".$resDM["KHOA"][$i]."' $selected>" .$resDM["KHOA"][$i]. "</option>";
					}
					?>
				</select>
			  </td>
			</tr>
			
			<tr>
			  <td align=right style="width:80px;">
				<span class="heading">
				<label for="dkmh_nv_khoa">Chọn Ngành</label>
				</span>
			  </td>
			  <td align=left>
				<select id=dkmh_nv_nganh name=dkmh_nv_nganh style="font-size:15px;" onChange="dkmh_nv_updateMonHoc($('#dkmh_nv_khoa').val(),this.value)">
					
				</select>
			  </td>
			</tr>
			
			<tr>
			  <td align=right>
			  </td>
			  <!--
			  <td align=left style='font-size:80%'>
				<a id="dkmh_nv_btn_printpreview">&nbsp;Xem bản In</a>
			  </td>
			  -->
			</tr>
		   </table>
			<input type=hidden id=tongMH name=tongMH>
		    <div id="dkmh_nv_monhoc_chitiet" style="margin-top:5px;" align=center></div>
			<div style='margin-top:10px; font-size:80%;' align=left>
				<a id=dkmh_nv_btn_next>&nbsp;Tiếp tục</a>
			</div>
		<?php 
		}
		?>
		</div>
	</div>
	
	
</form>

	<?php include "hv_tiethoc.php"; ?>

</div>

<div id="hv_dkmh_nv_dialog" title="Xác nhận Đăng ký môn học"></div>
<div id="hv_dkmh_nv_huy_dialog" title="Xác nhận HỦY Đăng ký môn học"></div>

<script type="text/javascript">
function dkmh_nv_writeConsole(content) {
	top.consoleRef=window.open('','myconsole',
	'width=800,height=450'
	+',menubar=0'
	+',toolbar=0'
	+',status=0'
	+',scrollbars=1'
	+',resizable=1')
	top.consoleRef.document.writeln(
	'<html><head><title>Phong Dao Tao SDH - DHBK</title></head>'
	+'<body bgcolor=white onLoad="self.focus()">'
	+ content
	+'</body></html>'
	)
	top.consoleRef.document.close()
}

// 
function dkmh_nv_updateNganh(p_khoa)
{
	if (p_khoa!='' )
	{
		dataString = 'hisid=<?php echo $_REQUEST["hisid"];?>';
		dataString += '&w=khoa-nganh';
		dataString += '&k=' + p_khoa;
		dataString += '&d=<?php echo $dot_hoc; ?>';
		
		xreq = $.ajax({
		  type: 'POST', url: 'hv_dkmh_yeu_cau_process.php', data: dataString, dataType: "html",
		  success: function(data) {
			$("#dkmh_nv_nganh").html(data);
			dkmh_nv_updateMonHoc(p_khoa, $("#dkmh_nv_nganh").val());
		  },
		  error: function(xhr, ajaxOptions, thrownError) {
			$("#dkmh_nv_monhoc_chitiet").html(thrownError);
		  }
		});
	}
}
function dkmh_nv_updateMonHoc(p_khoa, p_nganh)
{
	$( "#dkmh_nv_btn_printpreview" ).button( "disable" );
	if (p_khoa!='' && p_nganh!='')
	{
		$("#dkmh_nv_monhoc_chitiet").html("<img border='0' src='images/ajax-loader.gif'/>");

		//dataString = $("#form_dkmh_nv").serialize();
		dataString = 'hisid=<?php echo $_REQUEST["hisid"];?>';
		dataString += '&w=khoa_nganh-monhoc';
		dataString += '&k=' + p_khoa;
		dataString += '&n=' + p_nganh;
		dataString += '&tn=' + encodeURIComponent($("#dkmh_nv_nganh option:selected").html());
		dataString += '&d=<?php echo $dot_hoc;?>';
		dataString += '&df=<?php echo $dot_hoc_f;?>';
		
		xreq = $.ajax({
		  type: 'POST', url: 'hv_dkmh_yeu_cau_process.php', data: dataString,dataType: "html", 
		  success: function(data) {
			
			// Reset lai So TC đang chọn mà chưa commit khi thay đổi môn học
			numSoTCDangChon=0;
			
			$("#dkmh_nv_monhoc_chitiet").html(data);
			$( "#dkmh_nv_btn_printpreview" ).button( "enable" );
		  },
		  error: function(xhr, ajaxOptions, thrownError) {
			$( "#dkmh_nv_btn_printpreview" ).button( "disable" );
			$("#dkmh_nv_monhoc_chitiet").html(thrownError);
		  }
		});
	}
	else
	{
		$("#dkmh_nv_monhoc_chitiet").html("<div align=center>Chưa có môn học</div>");
	}
}

function dkmh_nv_updateMonHocDaDK()
{
	//$( "#dkmh_nv_btn_printpreview" ).button( "disable" );
	
		$("#dkmh_nv_mhdadk").html("<img border='0' src='images/ajax-loader.gif'/>");

		//dataString = $("#form_dkmh_nv").serialize();
		dataString = 'hisid=<?php echo $_REQUEST["hisid"];?>';
		dataString += '&w=monhocdadk';
		dataString += '&d=<?php echo $dot_hoc;?>';
		dataString += '&df=<?php echo $dot_hoc_f;?>';
		
		xreq = $.ajax({
		  type: 'POST', url: 'hv_dkmh_yeu_cau_process.php', data: dataString,dataType: "html", 
		  success: function(data) {
			//alert ('a'+data+'a');
			if (data!='')
			{
				$("#dkmh_nv_mhdadk").html(data);
				$( '#dkmh_nv_btn_delete' ).show();
			}
			else
			{
				$( '#dkmh_nv_btn_delete' ).hide();
				$("#dkmh_nv_mhdadk").html("<div align=center>Chưa có môn học đăng ký</div>");
			//$( "#dkmh_nv_btn_printpreview" ).button( "enable" );
			}
		  },
		  error: function(xhr, ajaxOptions, thrownError) {
			//$( "#dkmh_nv_btn_printpreview" ).button( "disable" );
			$("#dkmh_nv_mhdadk").html(thrownError);
		  }
		});
}

function toggleClassDKMH(ptablename, pindex)
{
	var mytable = document.getElementById(ptablename);
	var myrow = document.getElementById(ptablename).rows[pindex];
	var n = mytable.rows.length;
	
	myrow.classList.toggle('alt_chose');
	for (i=(pindex+1); i<n; i++)
	{
		var mynextrow = mytable.rows[i];
		if (mynextrow.cells[0].innerHTML=='')
			mynextrow.classList.toggle('alt_chose');
		else
			return;
	}
	
	//mytablerow.className += ' alt_chose';
	//alert(mytablerow.className);
}

$(function() {
	var numMH=0;
	
	$( "#accordion_dkmh_nv" ).accordion({
		autoHeight: false,
		navigation: true
	});
	//$( "#dkmh_nv_btn_printpreview" ).button({ icons: {primary:'ui-icon ui-icon-print'} });
	
	dkmh_nv_updateMonHocDaDK();
	dkmh_nv_updateNganh($("#dkmh_nv_khoa").val());

	/*$("#dkmh_nv_btn_printpreview").click(function(){
		dkmh_nv_writeConsole("<div style='font-size:13px; font-weight:bold;' align=left>ĐẠI HỌC BÁCH KHOA - TP.HCM<br/>PHÒNG ĐÀO TẠO SAU ĐẠI HỌC<br/> &nbsp;</div>" + $("#dkmh_nv_monhoc_chitiet").html() + "<div style='clear:both;'>&nbsp;<br/><a href='JavaScript:window.print();'>In trang này</a></div>");
	});	// end $("#dkmh_nv_btn_printpreview")*/
	
	// Dang ky mon hoc
	$( '#dkmh_nv_btn_next' ).button({ icons: {primary:'ui-icon ui-icon-arrowthick-1-e'} });	
	$( '#dkmh_nv_btn_next' ).click(function(){
		

		var mytable = document.getElementById("tableDKMH");
		var n = mytable.rows.length;
		var content = "<div style='margin: 10px 0 10px 0;'><b>Bạn chọn ĐĂNG KÝ các môn học sau, vui lòng kiểm tra lại.</b></div>"
				+"<table id=tablePreDKMH name=tablePreDKMH width='100%' border='0'  cellspacing='0' cellpadding='3' class='ui-widget ui-widget-content ui-corner-top tableData' >"
				+"	<thead>"
				+"	  <tr class='ui-widget-header heading' style='height:20pt; font-weight:bold;'>"
				+"		<td align=left class='ui-corner-tl'>Mã MH</td>"
				+"		<td align=left>Môn học</td>"
				+"		<td align=center>Lớp</td>"
				+"		<td align=center class='ui-corner-tr'>Tín chỉ</td>"
				+"	  </tr>"
				+"	  </thead>"
				+"	<tbody>";
		var classAlt = "alt";
		numMH = 0;
		$("#tongMH").val(n);
		for (i=1; i<n; i++)
		{
			//if (document.getElementById("checkMH"+i))
			var checkname = "checkMH"+(i-1);
			//alert($("#" + checkname).val);
			var element = document.getElementById(checkname);
			if (element != null)
			{
				if (element.checked)
				{
					(classAlt=="alt") ? classAlt="alt_" : classAlt="alt";
					content += "<tr class="+classAlt+">"
							+ "<td align=left>" + mytable.rows[i].cells[1].innerHTML + "</td>" 
							+ "<td align=left>" + mytable.rows[i].cells[2].innerHTML + "</td>"
							+ "<td align=center>" + mytable.rows[i].cells[3].innerHTML + "</td>"
							+ "<td align=center>" + mytable.rows[i].cells[4].innerHTML + "</td>"
							+ "</tr>";
					numMH +=1;
				}
			}
		}
		
		content += "</tbody>"
				+"</table>";
		
		if (numMH>0)
		{
			$( "#hv_dkmh_nv_dialog" ).html(content);
			$( "#hv_dkmh_nv_dialog" ).dialog("option", "title", "Xác nhận Đăng ký môn học theo nguyện vọng");
			$( "#hv_dkmh_nv_dialog" ).dialog("option", "height", 160+(numMH*20));
			$( "#hv_dkmh_nv_dialog" ).dialog("option", "width", 650);
			$("#hv_dkmh_nv_button-huy").button("enable");
			$("#hv_dkmh_nv_button-dkmh").show();
			$( "#hv_dkmh_nv_dialog" ).dialog("open");
		} 
		else
		{
			hv_open_msg_box("Vui lòng chọn Môn học để đăng ký", "info");
		}
	});	// end $('#dkmh_nv_btn_next')
	
	
	// Confirm DANG KY mon hoc
	// post: ten nganh, dot hoc, dot hoc format, sid, type=dothoc_mahv-dkmonhoc, hk dang hk1_2001_2002
	$( "#hv_dkmh_nv_dialog" ).dialog({
		resizable: true,
		autoOpen: false,
		width:750, height:250,
		closeOnEscape: false,
		open: function(event, ui) { $(".ui-dialog-titlebar-close").hide(); },
		modal: true,
		buttons: [
			{
				id: "hv_dkmh_nv_button-dkmh",
				text: "Đăng ký",
				click: function() {
					$("#hv_dkmh_nv_button-huy").button("disable");
					$("#hv_dkmh_nv_button-dkmh").hide("slow");
					
					$( "#hv_dkmh_nv_dialog" ).dialog("option", "title", "Đăng ký môn học theo nguyện vọng");
					$( "#hv_dkmh_nv_dialog" ).html("<div align=center><p>Đang tiến hành đăng ký môn học </p><p><img src='images/ajax-loader.gif' border=0></p></div>");
					$( "#hv_dkmh_nv_dialog" ).dialog("option", "height", 200);
					
					// Post dang ky mh
					dataString = $("#form_dkmh_nv").serialize()
								+ '&w=dothoc_mahv-dkmonhoc'
								+ '&tn=' + encodeURIComponent($("#dkmh_nv_nganh option:selected").html())
								+ '&ht=' + encodeURIComponent("<?php echo $hotenkhongdau;?>")
								+ '&d=<?php echo $dot_hoc;?>'
								+ '&df=<?php echo $dot_hoc_f;?>'
								+ '&h=<?php echo str_replace(array("/", "-"),array("_", "_"),$namhoc);?>'
								+ '&hisid=<?php echo $_REQUEST["hisid"];?>';
					
					xreq = $.ajax({
					  type: 'POST', url: 'hv_dkmh_yeu_cau_process.php', data: dataString,dataType: "html", 
					  success: function(data) {
						
						$("#hv_dkmh_nv_dialog").dialog("option", "height", 300+(numMH*30));
						$("#hv_dkmh_nv_dialog").html(data);
						$("#hv_dkmh_nv_button-huy").button("enable");
						
						dkmh_nv_updateMonHocDaDK();
						dkmh_nv_updateMonHoc($('#dkmh_nv_khoa').val(), $("#dkmh_nv_nganh").val());
						
					  },
					  error: function(xhr, ajaxOptions, thrownError) {
						$("#hv_dkmh_nv_dialog").html(thrownError);
						$("#hv_dkmh_nv_button-huy").button("enable");
					  }
					});
					
					
					//$( this ).dialog( "close" );
				}
			},
			{
				id: "hv_dkmh_nv_button-huy",
				text: "Đóng",
				click: function() {
					$( this ).dialog( "close" );
				}	
			}
		]
	});
	
	// Huy Mon hoc
	$( '#dkmh_nv_btn_delete' ).button({ icons: {primary:'ui-icon ui-icon-trash'} });
	$( '#dkmh_nv_btn_delete' ).hide();
	<?php
	if ($batdau<0 || $hethan>0)
	{
	?>
		$( '#dkmh_nv_btn_delete' ).button('disable');
	<?php
	}
	else
	{
	?>
	$( '#dkmh_nv_btn_delete' ).click(function(){
		var mytable = document.getElementById("tableMonHocDaDKTheoNV");
		var n = mytable.rows.length;
		var content = "<div style='margin: 10px 0 10px 0;'><b>Bạn chọn HỦY các môn học sau, vui lòng kiểm tra lại.</b></div>"
				+"<table id=tablePreDKMHhuy name=tablePreDKMHhuy width='100%' border='0'  cellspacing='0' cellpadding='3' class='ui-widget ui-widget-content ui-corner-top tableData' >"
				+"	<thead>"
				+"	  <tr class='ui-widget-header heading' style='height:20pt; font-weight:bold;'>"
				+"		<td align=left class='ui-corner-tl'>Mã MH</td>"
				+"		<td align=left>Môn học</td>"
				+"		<td align=center>Tín chỉ</td>"
				+"		<td align=center class='ui-corner-tr'>Lớp</td>"
				+"	  </tr>"
				+"	  </thead>"
				+"	<tbody>";
		var classAlt = "alt";
		numMH = 0;
		$("#tongMH").val(n);
		for (i=1; i<n; i++)
		{
			var checkname = "checkMHxoa"+(i-1);
			//alert($("#" + checkname).val);
			var element = document.getElementById(checkname);
			if (element != null)
			{
				if (element.checked)
				{
					(classAlt=="alt") ? classAlt="alt_" : classAlt="alt";
					content += "<tr class="+classAlt+">"
							+ "<td align=left>" + mytable.rows[i].cells[2].innerHTML + "</td>" 
							+ "<td align=left>" + mytable.rows[i].cells[3].innerHTML + "</td>"
							+ "<td align=center>" + mytable.rows[i].cells[7].innerHTML + "</td>"
							+ "<td align=center>" + mytable.rows[i].cells[8].innerHTML + "</td>"
							+ "</tr>";
					numMH +=1;
				}
			}
		}
		
		content += "</tbody>"
				+"</table>";
		
		if (numMH>0)
		{
			$( "#hv_dkmh_nv_huy_dialog" ).html(content);
			$( "#hv_dkmh_nv_huy_dialog" ).dialog("option", "title", "Xác nhận HỦY đăng ký môn học theo nguyện vọng");
			$( "#hv_dkmh_nv_huy_dialog" ).dialog("option", "height", 160+(numMH*20));
			$( "#hv_dkmh_nv_huy_dialog" ).dialog("option", "width", 650);
			$("#hv_dkmh_nv_button-dong_huy").button("enable");
			$("#hv_dkmh_nv_button-huy_dkmh").show();
			$( "#hv_dkmh_nv_huy_dialog" ).dialog("open");
		} 
		else
		{
			hv_open_msg_box("Vui lòng chọn Môn học", "info");
		}
	});	// end $('#dkmh_nv_btn_delete')
	<?php
	}
	?>
	
	
	// Confirm Huy dang ky mon hoc
	// post: ten nganh, dot hoc, dot hoc format, sid, type=dothoc_mahv-huy_dkmonhoc
	$( "#hv_dkmh_nv_huy_dialog" ).dialog({
		resizable: true,
		autoOpen: false,
		width:650, height:250,
		closeOnEscape: false,
		open: function(event, ui) { $(".ui-dialog-titlebar-close").hide(); },
		modal: true,
		buttons: [
			{
				id: "hv_dkmh_nv_button-huy_dkmh",
				text: "Hủy đăng ký",
				click: function() {
					$("#hv_dkmh_nv_button-dong_huy").button("disable");
					$("#hv_dkmh_nv_button-huy_dkmh").hide("slow");
					
					$( "#hv_dkmh_nv_huy_dialog" ).dialog("option", "title", "Hủy đăng ký môn học theo nguyện vọng");
					$( "#hv_dkmh_nv_huy_dialog" ).html("<div align=center><p>Đang tiến hành HỦY đăng ký môn học theo nguyện vọng</p><p><img src='images/ajax-loader.gif' border=0></p></div>");
					$( "#hv_dkmh_nv_huy_dialog" ).dialog("option", "height", 200);
					
					// Post HUY dang ky mh
					dataString = $("#form_dkmh_nv").serialize()
								+ '&w=dothoc_mahv-huy_dkmonhoc'
								+ '&tn=' + encodeURIComponent($("#dkmh_nv_nganh option:selected").html())
								+ '&ht=' + encodeURIComponent("<?php echo $hotenkhongdau;?>")
								+ '&d=<?php echo $dot_hoc;?>'
								+ '&df=<?php echo $dot_hoc_f;?>'
								+ '&h=<?php echo str_replace(array("/", "-"),array("_", "_"),$namhoc);?>'
								+ '&hisid=<?php echo $_REQUEST["hisid"];?>';
					
					xreq = $.ajax({
					  type: 'POST', url: 'hv_dkmh_yeu_cau_process.php', data: dataString,dataType: "html", 
					  success: function(data) {
						
						$("#hv_dkmh_nv_huy_dialog").dialog("option", "height", 200+(numMH*20));
						$("#hv_dkmh_nv_huy_dialog").html(data);
						$("#hv_dkmh_nv_button-dong_huy").button("enable");
						
						dkmh_nv_updateMonHocDaDK();
						dkmh_nv_updateMonHoc($('#dkmh_nv_khoa').val(), $("#dkmh_nv_nganh").val());
						
					  },
					  error: function(xhr, ajaxOptions, thrownError) {
						$("#hv_dkmh_nv_huy_dialog").html(thrownError);
						$("#hv_dkmh_nv_button-dong_huy").button("enable");
					  }
					});
					
					
					//$( this ).dialog( "close" );
				}
			},
			{
				id: "hv_dkmh_nv_button-dong_huy",
				text: "Đóng",
				click: function() {
					$( this ).dialog( "close" );
				}	
			}
		]
	});
});



</script>

<?php 
if (isset ($db_conn))
	oci_close($db_conn);
?>