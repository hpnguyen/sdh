<?php
if (isset($_REQUEST["hisid"])){
	session_id($_REQUEST["hisid"]);
	session_start();
}
if (!isset($_SESSION['uidloginPortal'])){
	die('Truy cập bất hợp pháp'); 
}

include "../libs/connect.php";
include "../libs/pgslibs.php";
if (!allowPermisstion(base64_decode($_SESSION['uidloginPortal']), '001', $db_conn))
{
	die('Truy cập bất hợp pháp'); 
}

$macb = $_SESSION['macb'];
$sqlstr="select cb.*, to_char(cb.NGAY_SINH,'dd-mm-yyyy') NGAY_SINH, k.ten_khoa, bm.ten_bo_mon
		from can_bo_giang_day cb, bo_mon bm, khoa k
		where cb.ma_bo_mon = bm.ma_bo_mon (+) and bm.ma_khoa = k.ma_khoa (+)
		and cb.ma_can_bo='".$_SESSION['macb']."'";

$stmt = oci_parse($db_conn, $sqlstr);
oci_execute($stmt);
$n = oci_fetch_all($stmt, $cbgd);
oci_free_statement($stmt);

?>

<form id="form_capnhat" name="form_capnhat" method="post" action="" >
  <div align="center">
    <table width="80%"   cellspacing="0" cellpadding="0" class="ui-corner-all shawdow tableData" >
      <tr>
        <td colspan="4" valign='top'> 
		<div align="center" id="tipTTGV" class="ui-corner-tl ui-corner-tr validateTips"></div>
        </td>
      </tr>
      <tr>
        <td colspan="4" >
      
        <table width="100%" border="0" align="center" cellpadding="5" cellspacing="0">
        
			<tr align="left">        
				<td colspan="2" class="heading" style="width:150px" align="right"><label> Họ tên </label></td>
				<td width="644" colspan="2" class="fontcontent"><?php echo $cbgd["HO"][0]. " " .$cbgd["TEN"][0]; ?></td>
			</tr>
			<tr align="left">
				<td colspan="2" class="heading" align="right"><label>Ngày sinh </label></td>
				<td colspan="2" class="fontcontent"><?php echo $cbgd["NGAY_SINH"][0];  ?></td>
			</tr>
			<tr align="left">
				<td colspan="2" class="heading" align="right"><label>Số hiệu công chức</label></td>
				<td colspan="2" class="fontcontent"> <?php echo $cbgd["SHCC"][0];  ?>  <input name="macb" type="hidden" value="<?php echo $macb;  ?>" /> </td>
			</tr>
		  <tr align="left">
			<td colspan="2" class="heading" align="right"><label>Khoa</label></td>
			<td colspan="2" class="fontcontent"><?php echo $cbgd["TEN_KHOA"][0]; ?> <span class="heading"> - Bộ Môn</span>&nbsp;<span class="fontcontent"><?php echo $cbgd["TEN_BO_MON"][0]; ?></span></td>
		  </tr>
		  <tr align="left">
			<td colspan="2" class="heading" align="right"><label for="txtDiaChi">Địa chỉ </label></td>
			<td colspan="2"><input name="txtDiaChi" type="text" id="txtDiaChi" value="<?php echo $cbgd["DIA_CHI"][0];  ?>" size="50" maxlength="200" class="text ui-widget-content ui-corner-all tableData"/></td>
		  </tr>
		  <tr align="left">
			<td colspan="2" class="heading" align="right"><label for="txtDienThoai">Điện thoại CQ</label></td>
			<td colspan="2"><input name="txtDienThoai" type="text" id="txtDienThoai" value="<?php echo $cbgd["DIEN_THOAI"][0];  ?>" size="20" maxlength="40" class="text ui-widget-content ui-corner-all tableData"/>
			<span class="heading">&nbsp;&nbsp;&nbsp; <label for="txtDienThoai2">Di Động </label></span>          <input name="txtDienThoai2" type="text" id="txtDienThoai2" value="<?php echo $cbgd["DIEN_THOAI_CN"][0];  ?>" size="20" maxlength="40" class="text ui-widget-content ui-corner-all tableData" /></td>
		  </tr>
		  <tr align="left">
			<td colspan="2" class="heading" valign=top align="right"><label for="txtEmail"> Email chính </label></td>
			<td colspan="2"><input placeholder="email cơ quan ..." name="txtEmail" type="text" id="txtEmail" value="<?php echo $cbgd["EMAIL"][0];  ?>" size="35" maxlength="100" class="text ui-widget-content ui-corner-all tableData"/> <br/>(<span style="color:red">@hcmut.edu.vn</span>, thông tin về học vụ của phòng SĐH đều được gửi qua email này) <span class="validateTips" id="emailValidate"> <span></td>
		  </tr>
		  <tr align="left">
			<td colspan="2" class="heading" align="right"><label for="txtEmail2">Email phụ </label></td>
			<td colspan="2"><input placeholder="email cá nhân ..." name="txtEmail2" type="text" id="txtEmail2" value="<?php echo $cbgd["EMAIL_2"][0];  ?>" size="35" maxlength="100" class="text ui-widget-content ui-corner-all tableData"/>
			  </td>
		  </tr>
		  <tr align="left">
			<td colspan="2" class="heading" align="right"><label>Học vị cao nhất </label></td>
			<td colspan="2" class="fontcontent">
			<?php switch ($cbgd["MA_HOC_VI"][0])
				{
					case "TSK": echo "Tiến Sĩ Khoa Học"; break;
					case "TS": echo "Tiến Sĩ"; break;
					case "TH": echo "Thạc Sĩ"; break;
					case "CN": echo "Cử Nhân"; break;
					case "KS": echo "Kỹ Sư"; break;
					default: echo "<em> n/a</em>";
				} 
				
			?>		  
			  <span class="heading"> &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;-&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Năm đạt </span> 
			  <input name="txtNamDatHocVi" type="text" id="txtNamDatHocVi" value="<?php echo $cbgd["NAM_DAT_HOC_VI"][0];  ?>" size="4" maxlength="4" class="text ui-widget-content ui-corner-all tableData"/>
			  </td>
		  </tr>
		  <tr align="left">
			<td colspan="2" class="heading" align="right"><label for="txtNoiDatHocVi">Nơi đạt học vị </label></td>
			<td colspan="2" valign=top><input placeholder="tên trường đạt học vị ..." name="txtNoiDatHocVi" type="text" id="txtNoiDatHocVi" value="<?php echo $cbgd["NOI_DAT_HOC_VI"][0];  ?>" size="50" maxlength="200" class="text ui-widget-content ui-corner-all tableData"/>
			  <span class="heading">tại </span>
	  
			  <select name="txtQG" id="txtQG" class="text ui-widget-content ui-corner-all tableData" style="height:23px;font-size:15px">
			   <option value=""></option>
			  <?php $sqlstr="select * from QUOC_GIA order by ten_quoc_gia"; 
				$stmt = oci_parse($db_conn, $sqlstr);
				oci_execute($stmt);
				$n = oci_fetch_all($stmt, $resDM);
				oci_free_statement($stmt);
				
				$txttmp = "";
				
				for ($i = 0; $i < $n; $i++)
				{
					if ($cbgd["QG_DAT_HOC_VI"][0] == $resDM["MA_QUOC_GIA"][$i])
						$txttmp = " selected ";
					else
						$txttmp = "";
					echo "<option value='" .$resDM["MA_QUOC_GIA"][$i]."'" .$txttmp. "> " .$resDM["TEN_QUOC_GIA"][$i]. " </option> ";
				}
				
			  ?>
			  </select></td>
		  </tr>
		  
		  <tr align="left">
			<td colspan="2" class="heading" valign=top align="right"><label>Ngành</label></td>
			<td style="width:300px" valign=top>
			
				<select name="txtNganh" id="txtNganh" class="tableData" onChange="">
				  <option value=''></option>
				  <?php $sqlstr="select ma_nganh, ten_nganh , decode(viet0dau_name(ten_nganh), 'Khac', 'zz', viet0dau_name(ten_nganh)) ten_nganh_orderby
								from nckh_nganh_dt 
								where length(ma_nganh) = 8 and bac_dao_tao = 'TS'
								order by ten_nganh_orderby"; 
					$stmt = oci_parse($db_conn, $sqlstr);
					oci_execute($stmt);
					$n = oci_fetch_all($stmt, $resDM);
					oci_free_statement($stmt);
					$txttmp = "";
					
					for ($i = 0; $i < $n; $i++)
					{
						if ($cbgd["FK_NGANH"][0] == $resDM["MA_NGANH"][$i])
							$txttmp = " selected ";
						else
							$txttmp = "";
						echo "<option value='" .$resDM["MA_NGANH"][$i]."'" .$txttmp. "> " .$resDM["TEN_NGANH"][$i]. " </option> ";
					}
				  ?>
				</select>
			</td>
			<td valign=top>
				<input name="txtNganhKhac" type="text" id="txtNganhKhac" placeholder="tên ngành khác ghi theo bằng cấp" value="<?php echo $cbgd["NGANH_KHAC"][0];  ?>" size="30" maxlength="200" class="text ui-widget-content ui-corner-all tableData"/> <br/><span>(chỉ nhập vào ô này khi mục Ngành chọn là "Khác")</span>
			</td>
		  </tr>
		  
		  <tr align="left">
			<td colspan="2" class="heading" align="right"><label for="txtChuyenNganh">Chuyên ngành </label></td>
			<td colspan="2">
			  <input name="txtChuyenNganh" type="text" id="txtChuyenNganh" value="<?php echo $cbgd["CHUYEN_NGANH"][0];  ?>" size="60" maxlength="200" class="text ui-widget-content ui-corner-all tableData"/>
			</td>
		  </tr>
		  
		  <tr align="left">
			<td colspan="2" class="heading" align="right"><label for="txtChuyenMon">Chuyên môn hiện tại </label></td>
			<td colspan="2">
			  <input name="txtChuyenMon" type="text" id="txtChuyenMon" value="<?php echo $cbgd["CHUYEN_MON"][0];  ?>" size="60" maxlength="200" class="text ui-widget-content ui-corner-all tableData"/>
			</td>
		  </tr>
		  
		  <tr align="left">
			<td colspan="2" class="heading" align="right"><label>Chức danh</label></td>
			<td colspan="2" class="fontcontent">
			  <?php switch ($cbgd["MA_HOC_HAM"][0])
				{
					case "GS": echo "Giáo Sư"; break;
					case "PGS": echo "Phó Giáo Sư"; break;
					case "GVC": echo "Giảng Viên Chính"; break;
					case "GV": echo "Giảng Viên"; break;
					case "TG": echo "Trợ Giảng"; break;
					default: echo "<em> n/a</em>";
				} 
				
			?>
			  
			  <span class="heading">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;-&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Năm công nhận</span> 
			  <input name="txtNamPhongHocHam" type="text" id="txtNamPhongHocHam" value="<?php echo $cbgd["NAM_PHONG_HOC_HAM"][0];  ?>" size="4" maxlength="4" class="text ui-widget-content ui-corner-all tableData"/>
			</td>
		  </tr>
		  
		  <!--
		  Thac si
		  -->
		  <tr align="left">
			<td colspan="2" class="heading" align="right"><label for="txtNoiDaoTaoThs">Nơi đào tạo THs </label></td>
			<td colspan="2" valign=top><input placeholder="tên trường đạt học vị Thạc sĩ..." name="txtNoiDaoTaoThs" type="text" id="txtNoiDaoTaoThs" value="<?php echo $cbgd["TRUONG_TN_TH"][0];  ?>" size="50" maxlength="200" class="text ui-widget-content ui-corner-all tableData"/>
			  <span class="heading">tại </span>
	  
			  <select name="txtQG_TH" id="txtQG_TH" class="text ui-widget-content ui-corner-all tableData" style="height:23px;font-size:15px">
			   <option value=""></option>
			  <?php $sqlstr="select * from QUOC_GIA order by ten_quoc_gia"; 
				$stmt = oci_parse($db_conn, $sqlstr);
				oci_execute($stmt);
				$n = oci_fetch_all($stmt, $resDM);
				oci_free_statement($stmt);
				
				$txttmp = "";
				
				for ($i = 0; $i < $n; $i++)
				{
					if ($cbgd["QG_DAT_HOC_VI_TH"][0] == $resDM["MA_QUOC_GIA"][$i])
						$txttmp = " selected ";
					else
						$txttmp = "";
					echo "<option value='" .$resDM["MA_QUOC_GIA"][$i]."'" .$txttmp. "> " .$resDM["TEN_QUOC_GIA"][$i]. " </option> ";
				}
				
			  ?>
			  </select></td>
		  </tr>
		  <tr align="left">
			<td colspan="2" class="heading" valign=top align="right"><label>Ngành Thạc sĩ</label></td>
			<td style="width:300px" valign=top>
			
				<select name="txtNganhTH" id="txtNganhTH" class="tableData" onChange="">
				  <option value=''></option>
				  <?php $sqlstr="select ma_nganh, ten_nganh , decode(viet0dau_name(ten_nganh), 'Khac', 'zz', viet0dau_name(ten_nganh)) ten_nganh_orderby
								from nganh_th
								where length(ma_nganh) = 8
								order by ten_nganh_orderby"; 
					$stmt = oci_parse($db_conn, $sqlstr);
					oci_execute($stmt);
					$n = oci_fetch_all($stmt, $resDM);
					oci_free_statement($stmt);
					$txttmp = "";
					
					for ($i = 0; $i < $n; $i++)
					{
						if ($cbgd["FK_NGANH"][0] == $resDM["MA_NGANH"][$i])
							$txttmp = " selected ";
						else
							$txttmp = "";
						echo "<option value='" .$resDM["MA_NGANH"][$i]."'" .$txttmp. "> " .$resDM["TEN_NGANH"][$i]. " </option> ";
					}
				  ?>
				</select>
			</td>
			<td valign=top>
				<input name="txtNganhTHKhac" type="text" id="txtNganhTHKhac" placeholder="tên ngành khác ghi theo bằng cấp" value="<?php echo $cbgd["NGANH_KHAC"][0];  ?>" size="30" maxlength="200" class="text ui-widget-content ui-corner-all tableData"/> <br/><span>(chỉ nhập vào ô này khi mục Ngành chọn là "Khác")</span>
			</td>
		  </tr>
		  
		  <tr align="left">
			<td colspan="2" class="heading" align="right"><label for="txtTenLuanAnThs">Tên luận án THs</label></td>
			<td colspan="2" valign=top><input placeholder="tên luận án Thạc sĩ..." name="txtTenLuanAnThs" type="text" id="txtTenLuanAnThs" value="<?php echo $cbgd["TEN_LUAN_AN_TH"][0];  ?>" size="50" maxlength="500" class="text ui-widget-content ui-corner-all tableData"/>
			  <span class="heading">Năm bắt đầu</span> 
				<input type="text" name="txtNamBD_Ths" id="txtNamBD_Ths" style="width:40px" value="<?php echo $cbgd["NAM_BD_TH"][0];  ?>" maxlength="4" class="text ui-widget-content ui-corner-all tableData"/>
			  <span class="heading">Năm TN</span> 
				<input type="text" name="txtNamTN_Ths" id="txtNamTN_Ths" style="width:40px" value="<?php echo $cbgd["NAM_TN_TH"][0];  ?>" maxlength="4" class="text ui-widget-content ui-corner-all tableData"/>
			</td>
		  </tr>
		  <!--end of thac si-->
		  
		  <tr >
			<td colspan="4" align="center" height="30">
			<input name="hisid" type="hidden" id="hisid" value="<?php echo session_id(); ?>" />
				<input id="btnLuuTTGV" type=submit value='Cập nhật'>
			</td>
		  </tr> 
		  
		   <tr >
			<td colspan="4" align="center">
			</td>
		  </tr> 
        </table>
      
        </td>
      </tr>
    </table>
  </div>
  
</form>  <!-- end form_capnhat -->

<script type="text/javascript">
//jQuery.ajax
//$(document).ready(function(){
$(function(){

 $( "#txtNganh, #txtNganhTH" ).combobox();
 
 // ok
 $( "#btnLuuTTGV" ).button({ icons: {primary:'ui-icon ui-icon-check'} });
 // cancel
 
	// Check validate fields TTGV
var txtDiaChi 	= $("#txtDiaChi"),
	txtDienThoai	= $("#txtDienThoai"),
	txtDienThoai2	= $("#txtDienThoai2"),
	txtEmail		= $("#txtEmail"),
	txtEmail2		= $("#txtEmail2"),
	txtNoiDatHocVi	= $("#txtNoiDatHocVi"),
	txtNganh		= $("#txtNganh"),
	txtQG			= $("#txtQG"),
	txtChuyenNganh	= $("#txtChuyenNganh"),
	txtNamPhongHocHam = $("#txtNamPhongHocHam"),
	txtNamDatHocVi	= $("#txtNamDatHocVi"),
	formchange 		= false,
	allFieldsTTGV = $( [] ).add( txtDiaChi ).add( txtDienThoai ).add( txtDienThoai2 ).add( txtEmail ).add( txtEmail2 ).add( txtNoiDatHocVi ).add( txtNganh ).add( txtQG ).add( txtChuyenNganh ).add( txtNamPhongHocHam ).add( txtNamDatHocVi ),
	ttgv_tips = $( "#tipTTGV" );
		
	// 
	function ttgv_updateTips( t ) {
		ttgv_tips
					.text( t )
					.addClass( "ui-state-highlight" );
		setTimeout(function() {
			ttgv_tips.removeClass( "ui-state-highlight", 1500 );
		}, 1000 );
	}
	
	// Checklength
	function checkLength( o, n, min, max) {
		if (min==0 && (o.val().length==0))
		{	
			o.addClass( "ui-state-error" );
			o.focus();	
			ttgv_updateTips( "Thông tin " + n + " không được phép để trống.");
			
			return false;
		}else if (min==max && o.val().length<min){
			o.addClass( "ui-state-error" );
			o.focus();	
			ttgv_updateTips( "Thông tin " + n + " phải đủ " + min + " ký tự.");
		}else if ( o.val().length > max || o.val().length < min ) {
			o.addClass( "ui-state-error" );
			o.focus();		
			ttgv_updateTips( "Chiều dài của " + n + " từ " +
						min + " đến " + max + " ký tự.");
			return false;
		} else {
			return true;
		}
	}
	
	// Check Regexp
	function checkRegexp( o, regexp, n ) {
		//alert('a');
		if ( o.val()!='' && !( regexp.test( o.val() ) ) ) {
			o.addClass( "ui-state-error" );
			o.focus();
			ttgv_updateTips( n );
			return false;
		} else {
			return true;
		
		}
	}
// End of check validate

	// Post du lieu cho Thong tin giang vien
	allFieldsTTGV.change(function(e) {
		formchange =  true;
    });
	
	$("#btnLuuTTGV").click(function(e){
	//$("#form_capnhat").submit(function(e){
		//e.preventDefault();
		//alert ($("#form_capnhat").change().val());
		
		var bValid = true;
		
		allFieldsTTGV.removeClass( "ui-state-error" );
		//alert(2);
		bValid = bValid && checkLength( txtDiaChi, "\"Địa chỉ\"", 0, 200);
		bValid = bValid && checkLength( txtDienThoai, "\"Số đt CQ\"", 4, 40 );
		//bValid = bValid && checkLength( txtDienThoai2, "\"Số di động\"", 4, 40 );
		bValid = bValid && checkLength( txtEmail, "\"Email 1\"", 0, 100 );
		//bValid = bValid && checkLength( txtEmail2, "\"Email 2\"", 6, 100 );
		//bValid = bValid && checkLength( txtNamDatHocVi, "\"Năm đạt học vị\"", 4, 4);
		bValid = bValid && checkRegexp( txtNamDatHocVi,/^[0-9]{4,4}$/i, "Năm đạt học vị phải đủ 4 Số");
		
		bValid = bValid && checkLength( txtNoiDatHocVi, "\"Nơi đạt học vị\"", 0, 200);
		
		//bValid = bValid && checkLength( txtNamPhongHocHam, "\"Năm được phong học hàm\"", 4, 4);
		bValid = bValid && checkRegexp( txtNamPhongHocHam,/^[0-9]{4,4}$/i, "Năm phong học hàm phải đủ 4 Số");
		
		bValid = bValid && checkLength( txtNganh, "\"Ngành\"", 0, 8);
		bValid = bValid && checkLength( txtQG, "\"Quốc gia đạt học vị\"", 0, 8);
		bValid = bValid && checkLength( txtChuyenNganh, "\"Chuyên ngành\"", 0, 200);
		
		bValid = bValid && checkRegexp( txtEmail, /^((([a-z]|\d|[!#\$%&'\*\+\-\/=\?\^_`{\|}~]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])+(\.([a-z]|\d|[!#\$%&'\*\+\-\/=\?\^_`{\|}~]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])+)*)|((\x22)((((\x20|\x09)*(\x0d\x0a))?(\x20|\x09)+)?(([\x01-\x08\x0b\x0c\x0e-\x1f\x7f]|\x21|[\x23-\x5b]|[\x5d-\x7e]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(\\([\x01-\x09\x0b\x0c\x0d-\x7f]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF]))))*(((\x20|\x09)*(\x0d\x0a))?(\x20|\x09)+)?(\x22)))@((([a-z]|\d|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(([a-z]|\d|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])*([a-z]|\d|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])))\.)+(([a-z]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(([a-z]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])*([a-z]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])))\.?$/i,"Nhập không đúng định dạng email, vd: pgs@hcmut.edu.vn" );
		bValid = bValid && checkRegexp( txtEmail2, /^((([a-z]|\d|[!#\$%&'\*\+\-\/=\?\^_`{\|}~]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])+(\.([a-z]|\d|[!#\$%&'\*\+\-\/=\?\^_`{\|}~]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])+)*)|((\x22)((((\x20|\x09)*(\x0d\x0a))?(\x20|\x09)+)?(([\x01-\x08\x0b\x0c\x0e-\x1f\x7f]|\x21|[\x23-\x5b]|[\x5d-\x7e]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(\\([\x01-\x09\x0b\x0c\x0d-\x7f]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF]))))*(((\x20|\x09)*(\x0d\x0a))?(\x20|\x09)+)?(\x22)))@((([a-z]|\d|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(([a-z]|\d|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])*([a-z]|\d|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])))\.)+(([a-z]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(([a-z]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])*([a-z]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])))\.?$/i, "Nhập không đúng định dạng email, vd: pgs@hcmut.edu.vn" );
		
		
		
		if ( bValid ) {
			//$('#cat').val('ttgv');
			dataString = $("#form_capnhat").serialize();
			dataString +='&cat=ttgv';
			//alert(dataString);
			$.ajax({type: "POST",url: "gv/processgv.php",data: dataString,	dataType: "json",
				success: function(data) {
							if (data.email_check === "Exist"){
								//alert(data.email_check);
								txtEmail.addClass( "ui-state-error" );
								txtEmail.focus();
								ttgv_updateTips("Email này đã có người đăng ký");
								gv_open_msg_box("Email này đã có người đăng ký","alert",250,150);
							}else
							{
								ttgv_updateTips("Cập nhật thành công");
								gv_open_msg_box("Cập nhật thành công","info",250,150);
								formchange =  false;
							}
						 }// end function(data)	
			}); // end .ajax
		}
		//else
		e.preventDefault();
		
	});	// end $("#btnLuuTTGV")
	
	$('input[placeholder],textarea[placeholder]').placeholder();

});



</script>



<?php 
if (isset ($db_conn))
	oci_close($db_conn);
?>