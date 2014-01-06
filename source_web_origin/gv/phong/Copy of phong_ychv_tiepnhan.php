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
if (!allowPermisstion(base64_decode($_SESSION['uidloginPortal']), '021', $db_conn))
{
	die('Truy cập bất hợp pháp'); 
}
$usr = base64_decode($_SESSION['uidloginPortal']);
$makhoa = base64_decode($_SESSION['makhoa']);

$sqlstr="select id, ho || ' ' || ten ho_ten from nhan_su where upper(username)=upper('$usr')"; 
$stmt = oci_parse($db_conn, $sqlstr);oci_execute($stmt);$n=oci_fetch_all($stmt, $resDM);oci_free_statement($stmt);

$id = $resDM["ID"][0];
$hoten = $resDM["HO_TEN"][0];

// Download các yêu cầu trên web về local
$sqlstr="insert into hvu_giai_quyet_hvu select * from hvu_giai_quyet_hvu@db_link where MA_GQHVU not in (select MA_GQHVU from hvu_giai_quyet_hvu)"; 
$stmt = oci_parse($db_conn, $sqlstr);oci_execute($stmt);oci_free_statement($stmt);

$sqlstr="insert into hvu_qua_trinh_giai_quyet select * from hvu_qua_trinh_giai_quyet@db_link t1
where 0 = (select count( * ) from hvu_qua_trinh_giai_quyet t2 where t1.FK_MA_GQHVU = t2.FK_MA_GQHVU and t1.ngay = t2.ngay)"; 
$stmt = oci_parse($db_conn, $sqlstr);oci_execute($stmt);oci_free_statement($stmt);
// End download

?>

<div id="phong_tiepnhanYeuCau">
	<div id=phong_tnyc_frm_addYC style='width:650px;' title="Tiếp nhận yêu cầu học vụ">
		<table  border="0" cellspacing="0" cellpadding="5" align=left width=100% class="ui-corner-all">
			<tr>
				<td align=left >
					<input placeholder="mã" title="- Học viên: mã học viên<br/>- Thí sinh dự thi: Năm + SBD (2013415)" type=text name=tnychv_mahv id=tnychv_mahv maxlength=10 style="width:110px;font-weight:bold;font-size:12px" class="text ui-widget-content ui-corner-all tableData"> <span id=phong_tnychv_ho_ten style='font-weight:bold;margin-left:10px'></span>
				</td>
				
				<td align=left colspan=2> 
					<select id=phong_tnychv_nguoi_nhan title="Chuyên viên nhận HS" style='width:100%; height:25px; font-weight:normal; padding: 0px 0 0 0;' class="text ui-widget-content ui-corner-all tableData">
						<option value='' style='color:black;'>-chọn chuyên viên nhận-</option>
						
					  <? 
					    $sqlstr="select id, ho || ' ' || ten ho_ten from nhan_su where pdtsdh='1' order by ten"; 
						$stmt = oci_parse($db_conn, $sqlstr);oci_execute($stmt);$n=oci_fetch_all($stmt, $resDM);oci_free_statement($stmt);
						for ($i = 0; $i < $n; $i++)
						{
							if ($id==$resDM["ID"][$i])
								echo "<option value='".$resDM["ID"][$i]."' selected style='background: #075385; color: white; font-weight:bold'>" .$resDM["HO_TEN"][$i]. "</option>";
							else
								echo "<option value='".$resDM["ID"][$i]."'>" .$resDM["HO_TEN"][$i]. "</option>";
						}
						
					  ?>
					</select>
				</td>
			</tr>
			<tr>
				<td align=left colspan=3>
					Chuyên ngành: <span id=phong_tnychv_chuyennganh style='font-weight:bold;margin-right:10px'></span> Khóa: <span id=phong_tnychv_khoa style='font-weight:bold;margin-right:10px'></span>
					Ngày sinh: <span id=phong_tnychv_ngaysinh style='font-weight:bold;margin-right:10px'></span> Nơi sinh: <span id=phong_tnychv_noisinh style='font-weight:bold;margin-right:10px'></span>
				</td>
			</tr>
			<tr>
				<td align=left colspan=3>
					<select id=phong_tnychv_hvu style='width:100%;height:25px; padding: 0 0 0 0; font-size:12px' class="text ui-widget-content ui-corner-all tableData">
						<option value='' selected style='color:black;'>-chọn yêu cầu học vụ-</option>
						<option value='000' style='color:black;'>Khác</option>
					  <? $sqlstr="select ma_yc, noi_dung_yc, so_ngay_xu_ly, don_gia, viet0dau_name(noi_dung_yc) noi_dung_yc_0_dau
								from hvu_dm_yc_hvu where ma_yc <> '000' and xoa is null order by noi_dung_yc_0_dau"; 
						$stmt = oci_parse($db_conn, $sqlstr);
						oci_execute($stmt);
						$n = oci_fetch_all($stmt, $resDM);
						oci_free_statement($stmt);
						
						for ($i = 0; $i < $n; $i++)
						{
							echo "<option value='".$resDM["MA_YC"][$i]."'>" .$resDM["NOI_DUNG_YC"][$i]. "</option>";
						}
						
					  ?>
					</select>
					
				</td>
			</tr>
			<tr>
				<td colspan=3>
					SL
					<input placeholder="sl" type=text name=tnychv_sl id=tnychv_sl maxlength=2 style="width:25px;font-weight:bold;text-align:center;font-size:12px" value=1 class="text ui-widget-content ui-corner-all tableData">
				
				 &nbsp;&nbsp;Hẹn trả KQ <input type=text name=tnychv_ngaytrakq id=tnychv_ngaytrakq maxlength=10 style="width:90px;font-weight:bold;text-align:center;font-size:12px" class="text ui-widget-content ui-corner-all tableData">
				
					&nbsp;&nbsp;Thu phí <input type=text name=tnychv_don_gia id=tnychv_don_gia maxlength=10 style="width:55px;font-weight:bold;text-align:right;font-size:12px" class="text ui-widget-content ui-corner-all tableData">
				
				&nbsp;&nbsp;Giải quyết: <span id=phong_tnychv_nguoi_giai_quyet style='font-weight:bold;margin-left:10px;font-size:12px'></span> <input type=hidden id="ma_ngq" value="">
				</td>
			</tr>
			
			<tr>
				<td align=left colspan=3>
					<input list="tnychv_noidungyc_list" placeholder="nội dung yêu cầu" type=text name=tnychv_noidungyc id=tnychv_noidungyc maxlength=400 style="width:100%;font-size:12px" class="text ui-widget-content ui-corner-all tableData">
					<datalist id="tnychv_noidungyc_list">
						<option value="TOEIC ">
						<option value="TOEFL ">
					</datalist>
				</td>
			</tr>
			
			<tr>
				<td align=left colspan=3> 
					<input placeholder="ghi chú" type=text name=tnychv_ghi_chu id=tnychv_ghi_chu maxlength=200 style="width:100%;font-size:12px" class="text ui-widget-content ui-corner-all tableData">
				</td>
			</tr>
			
			<tr>
				<td align=center colspan=3> 
					<button id="phong_tnychvu_btn_add" style='height:26px;width:30px;'>&nbsp;</button> 
				</td>
			</tr>
			
		</table>
		<div style='clear:both;'></div>
		<table id=tnychv_dsychv_themmoi width="100%" style="margin-top:5px;" border="0" align="center" cellpadding="5" cellspacing="0" class="tablethemmoi ui-widget ui-widget-content ui-corner-all tableData" >
			<thead>
				<tr class="ui-widget-header heading" height="20">
					
					<th style='width:70px;' align=left>Mã HVụ</th>
					<th style='width:330px;' align=left>Nội dung</th>
					<th style='width:30px;' align=left>SL</th>
					<th style='width:30px;' align=right>Phí</th>
					<th style='width:70px;' align=right>Ngày trả</th>
					<th style='width:100px;' align=left>Ghi chú</th>
					<th style='width:50px;' align=right>Mã HV</th>
					<th style='width:30px;'></th>
				</tr>
			</thead>
			<tbody style="font-size:12px;">
			</tbody>
			
		</table>
	</div>
	
	<div style='clear:both;'></div>
	<div style='margin:0 0 10px 0;'>
		<table width="100%" height="20" border="0" align="center" cellpadding="5" cellspacing="0" class="ui-widget ui-widget-content ui-corner-all ">
			<tr>
				<td style="width:50%" align=left><button id=phong_tnychv_btn_new>Nhận yêu cầu mới</button> <button id=phong_tnychv_btn_print style="margin:0 0 0 10px;">In biên nhận</button> <button id=phong_tnychv_btn_refresh style="margin:0 0 0 10px;">Làm mới danh sách</button></td>
				<td style="width:50%" align=right><button id=phong_tnychv_btn_trash style="margin:0 0 0 10px;">Thùng rác</button></td>
			</tr>
		</table>
	</div>
	
	<!-- Filter -->
	<div style='margin:0 0 10px 0px;'> 
		<table width="100%" border="0" align="center" cellpadding="5"  cellspacing="0" class="ui-widget ui-widget-content ui-corner-all ">
			<tr>
				<td style="width:85px;">
					<select id=filter_phong_tnychv_nam_nhan title="Fillter theo năm nhận hồ sơ" style='width:100%; height:25px; padding: 0 0 0 0;' class="ui-widget-content ui-corner-all tableData">
					  <? 
						$nam =date("Y");
						echo "<option value=''>-năm nhận-</option>";
						for ($i = $nam; $i > ($nam-5); $i--)
						{
							if ($i == $nam)
								$selected = "selected";
							else
								$selected = "";
								
							echo "<option value='".$i."' $selected>" .$i. "</option>";
						}
					  ?>
					</select>
				</td>
				<td style="width:85px;">
					<select id=filter_phong_tnychv_thung_rac title="Fillter theo HS hiện tại / HS trong thùng rác" style='width:100%;height:25px; padding: 0 0 0 0;' class=" ui-widget-content ui-corner-all tableData" >
						<option value=''>Hiện tại</option>
						<option value='1'>Thùng rác</option>
					</select>
				</td>
							
				<td>
					<select id=filter_phong_tnychv_nguoi_xl title="Fillter theo chuyên viên xử lý HS" style='width:100%;height:25px; padding: 0 0 0 0;' class=" ui-widget-content ui-corner-all tableData" >
					  <? 
						// Kiem tra quyền xem tat ca
						if (allowPermisstion($usr, '045', $db_conn))
						{						
							$sqlstr="select id, ho || ' ' || ten ho_ten from nhan_su where pdtsdh='1' order by ten"; 
							$stmt = oci_parse($db_conn, $sqlstr);oci_execute($stmt);$n = oci_fetch_all($stmt, $resDM);oci_free_statement($stmt);
							echo "<option value=''>-tất cả chuyên viên xử lý-</option>";
							for ($i = 0; $i < $n; $i++)
								if ($id==$resDM["ID"][$i])
									echo "<option value='".$resDM["ID"][$i]."' style='background: #075385; color: white; font-weight:bold'>" .$resDM["HO_TEN"][$i]. "</option>";
								else
									echo "<option value='".$resDM["ID"][$i]."'>" .$resDM["HO_TEN"][$i]. "</option>";
								//echo "<option value='".$resDM["ID"][$i]."'>" .$resDM["HO_TEN"][$i]. "</option>";
						}
						// Kiem tra quyền xem hs do mình xử lý
						else if (allowPermisstion($usr, '046', $db_conn))
						{
							$sqlstr="select id, ho || ' ' || ten ho_ten from nhan_su where username='$usr'"; 
							$stmt = oci_parse($db_conn, $sqlstr);oci_execute($stmt);$n = oci_fetch_all($stmt, $resDM);oci_free_statement($stmt);
							
							for ($i = 0; $i < $n; $i++)
								echo "<option value='".$resDM["ID"][$i]."'>" .$resDM["HO_TEN"][$i]. "</option>";
						}
						
						
					  ?>
					</select>
				</td>
				
				<td>
					<select id=filter_phong_tnychv_nguoi_nhan title="Fillter theo chuyên viên nhận HS" style='width:100%;height:25px; padding: 0px 0 0 0;' class="text ui-widget-content ui-corner-all tableData" >
					  <? $sqlstr="select id, ho || ' ' || ten ho_ten from nhan_su where pdtsdh='1' order by ten"; 
						$stmt = oci_parse($db_conn, $sqlstr);oci_execute($stmt);$n = oci_fetch_all($stmt, $resDM);oci_free_statement($stmt);
						echo "<option value=''>-tất cả chuyên viên nhận-</option>";
						for ($i = 0; $i < $n; $i++)
						{
							if ($id==$resDM["ID"][$i])
								echo "<option value='".$resDM["ID"][$i]."' style='background: #075385; color: white; font-weight:bold'>" .$resDM["HO_TEN"][$i]. "</option>";
							else
								echo "<option value='".$resDM["ID"][$i]."'>" .$resDM["HO_TEN"][$i]. "</option>";
						}
						
					  ?>
					</select>
				</td>
				
				<td>
					<select id=filter_phong_tnychv_hv_nhan_yc title="Fillter theo HS đã/chưa trả cho học viên" style='width:100%;height:25px; padding: 0 0 0 0;' class=" ui-widget-content ui-corner-all tableData">
						<option value=''>-Hồ sơ ĐÃ & CHƯA trả-</option>
						<option value='1'>Hồ sơ ĐÃ trả</option>
						<option value='0'>Hồ sơ CHƯA trả</option>
					</select>
				</td>
				
				<td>
					<select id=filter_phong_tnychv_tinh_trang title="Fillter theo tình trạng HS" style='width:100%; height:25px; padding: 0 0 0 0;' class="ui-widget-content ui-corner-all tableData" >
					  <? $sqlstr="select MA_TINH_TRANG, TEN_TAT	from HVU_DM_TINH_TRANG"; 
						$stmt = oci_parse($db_conn, $sqlstr);oci_execute($stmt);$n = oci_fetch_all($stmt, $resDM);oci_free_statement($stmt);
						echo "<option value=''>-tất cả tình trạng-</option>";
						for ($i = 0; $i < $n; $i++)
						{
							echo "<option value='".$resDM["MA_TINH_TRANG"][$i]."'>" .$resDM["TEN_TAT"][$i]. "</option>";
						}
					  ?>
					  <option value='!2'>Loại trừ Đã XL</option>
					</select>
				</td>
				
				<td style="width:150px;"><b>Cảnh báo sớm</b> <input type=text id=ychv_so_ngay_canh_bao title="Cảnh báo những yêu cầu sắp đến hẹn trả mà chưa xử lý sẽ có màu đỏ" class="text ui-widget-content ui-corner-all tableData" style="width:25px; text-align:center;font-size:12px; height:15pt"> <b>ngày</b></td>
				
				
			</tr>
		</table>
	</div>
	
	<table id=tnychv_dsychv width="100%"  border="0" align="center" cellpadding="5" cellspacing="0" class="ui-widget ui-widget-content ui-corner-top tableData display">
		<thead>
			<tr class="ui-widget-header heading" >
				<th style="width: 20px" align=left><input type="checkbox" onClick="SelectAll(this.checked);"></th>
				<th style="width: 40px" align=left>Mã</th>
				<th align=left>Nội dung</th>
				<th style="width: 20px" align=right>SL</th>
				<th style="width: 15px"></th>
				<th style="width: 15px"></th>
				<th style="width: 40px" align=center>CV xử lý</th>
				<th style="width: 60px">Ngày nhận</th>
				<th style="width: 60px">Hẹn trả</th>
				<th style="width: 20px" align=center>Trả HV</th>
				<th style="width: 60px" align=left>Mã HV</th>
				<th align=left>Tên học viên</th>
				<th style="width: 60px">Tình trạng</th>
				<th align=left>Kết quả</th>
				<th style="width: 50px" align=right>Thu phí</th>
				<th style="width: 20px"></th>
			</tr>
		</thead>
		<tfoot>
			<tr class="ui-widget-header heading">
				<th ></th>
				<th align=left>Mã</th>
				<th align=left>Nội dung</th>
				<th align=right>SL</th>
				<th ></th>
				<th ></th>
				<th align=center>CV xử lý</th>
				<th >Ngày nhận</th>
				<th >Hẹn trả</th>
				<th align=center>Trả HV</th>
				<th align=left>Mã HV</th>
				<th align=left>Tên học viên</th>
				<th >Tình trạng</th>
				<th align=left>Kết quả</th>
				<th align=right>Thu phí</th>
				<th ></th>
			</tr>
		</tfoot>
		
	</table>
	
	<div style='clear:both;'></div>
	<div id="phong_tiepnhanyeucau_detail_new" align=center></div>
	<div id="phong_tnychv_chuyen_xuly" title="Người xử lý yêu cầu hv">
		<div align=left style="margin: 5px 0 5px 0">Chuyển các yêu cầu: <b><span id=phong_tnychv_ma_chuyen_xuly></span></b></div>
		<div align=left style="margin: 5px 0 5px 0">Chọn người xử lý yêu cầu:</div>
		<select id=phong_tnychv_nguoi_xu_ly style='width:100%; height:28px; padding: 3px 0 0 0;' class="text ui-widget-content ui-corner-all tableData">
		  <? 
			$sqlstr="select id, ho || ' ' || ten ho_ten, username from nhan_su where pdtsdh='1' order by ten"; 
			$stmt = oci_parse($db_conn, $sqlstr);oci_execute($stmt);$n = oci_fetch_all($stmt, $resDM);oci_free_statement($stmt);
			for ($i = 0; $i < $n; $i++)
			{
				if ($id==$resDM["ID"][$i])
					echo "<option value='".$resDM["ID"][$i]."' selected>" .$resDM["HO_TEN"][$i]. "</option>";
				else
					echo "<option value='".$resDM["ID"][$i]."'>" .$resDM["HO_TEN"][$i]. "</option>";
			}
		  ?>
		</select>
		<div style="margin: 10px 0 0 0"> 
		<input type=text id="phong_tnychv_ghichu_ychv_chuyen" name="phong_tnychv_ghichu_ychv_chuyen" value="" placeholder = "Ghi chú" style="width:100%; font-size: 12px" class="text ui-widget-content ui-corner-all tableData">
		</div>
		<input type=hidden id="phong_tnychv_ma_ychv_chuyen" value="">
	</div>
	
	<div id="phong_tnychv_xuly" title="Xử lý yêu cầu học vụ">
		<div align=left style="margin: 5px 0 10px 0">Xử lý các yêu cầu: <b><span id=phong_tnychv_label_ma_xuly></span></b></div>
		
		<div align=left style="margin: 5px 0 5px 0">Thông tin cập nhật:</div>
		<div style="margin: 10px 0 0 0"> 
		<input type=text id="phong_tnychv_hentra_xuly" name="phong_tnychv_hentra_xuly" value="" placeholder="Hẹn trả KQ" style="text-align:left; width:70px; font-size: 12px" class="text ui-widget-content ui-corner-all tableData">
		</div>
		<div style="margin: 10px 0 0 0"> 
		<input type=text id="phong_tnychv_vitriluu_xuly" name="phong_tnychv_vitriluu_xuly" value="" title="Vị trí cất giữ yêu cầu học vụ" placeholder = "Vị trí lưu hồ sơ" style="width:100%; font-size: 12px" class="text ui-widget-content ui-corner-all tableData">
		</div>
		<div style="margin: 10px 0 0 0"> 
		<select id=phong_tnychv_tinhtrang_xuly style='width:100%; height:25px; padding: 0 0 0 0;' title="Tình trạng hiện tại của yêu cầu học vụ" class="ui-widget-content ui-corner-all tableData" >
		  <? $sqlstr="select MA_TINH_TRANG, TEN_TAT	from HVU_DM_TINH_TRANG"; 
			$stmt = oci_parse($db_conn, $sqlstr);oci_execute($stmt);$n = oci_fetch_all($stmt, $resDM);oci_free_statement($stmt);
			echo "<option value=''>-chọn tình trạng-</option>";
			for ($i = 0; $i < $n; $i++)
			{
				echo "<option value='".$resDM["MA_TINH_TRANG"][$i]."'>" .$resDM["TEN_TAT"][$i]. "</option>";
			}
		  ?>
		</select>
		</div>
		<div style="margin: 10px 0 0 0"> 
		<input type=text id="phong_tnychv_ketqua_xuly" name="phong_tnychv_ketqua_xuly" value="" title="Kết quả xử lý yêu cầu học vụ" placeholder = "Kết quả xử lý" style="width:100%; font-size: 12px" class="text ui-widget-content ui-corner-all tableData">
		</div>
		<input type=hidden id="phong_tnychv_ma_xuly" value="">
	</div>
	
	<div id="phong_tnychv_phuchoitrahv" title="Thu hồi hồ sơ trả cho HV">
		<div align=left style="margin: 5px 0 5px 0">Bạn muốn phục hồi trạng thái trả yêu cầu học vụ có mã: <b><span id=phong_tnychv_label_ma_phuchoi></span></b> ?</div>
	</div>
</div> <!-- end  -->

<style>
	.YCHV_DaXL {color: #96c716; font-weight: bold;}
	.YCHV_ChuaXL {color: #bc3604; font-weight: bold; text-transform: uppercase}
	.YCHV_DangXL {color: blue; font-weight: bold;}
	.YCHV_TrinhLD {color: blue; font-weight: bold;}
</style>

<script type="text/javascript">

 var oTableYCHV, nTrClicked;
 var ychv_oCountRow, ychv_SoNgayCanhBaoHetHan=1, ychv_tongtien=0;
 var ychv_validateMaHV = 0;
 var ychv_listMahvu, ychv_classname=''; 
 var ychv_linkdata = "phong/phong_ychv_tiepnhan_process.php?a=refreshdata&hisid=<?php echo $_REQUEST["hisid"]; ?>";
 
function getRowIndex( el ) {
    while( (el = el.parentNode) && el.nodeName.toLowerCase() !== 'tr' );

    if( el ) {
        return el.rowIndex-1;
	}
}

function MaHVinList(pMaHV)
{	
	var n = document.getElementById('tnychv_dsychv_themmoi').rows.length, mahv;
	for (i=1 ; i<n; i++){
		mahv = document.getElementById('tnychv_dsychv_themmoi').rows[i].cells[0].innerHTML;
		if (pMaHV==mahv)
			return true;
	}
	return false;
}

function initialTableYCHV(urldata)
{
	$("#ychv_so_ngay_canh_bao").val(ychv_SoNgayCanhBaoHetHan);
	
	oTableYCHV = $('#tnychv_dsychv').dataTable( {
		"bJQueryUI": false,
		"bStateSave": true,
		"bAutoWidth": false, 
		"iDisplayLength": 50,
		"sPaginationType": "full_numbers",
		"oLanguage": {
			"sUrl": "../datatable/media/language/vi_VI.txt"
		},
		"bProcessing": true,
		"sAjaxSource": urldata,
		"fnDrawCallback": function( oSettings ) {
			$(document).tooltip({ track: true });
			//$("#ychv_tong_thu_phi").html(ychv_tongtien.formatMoney(0, '.', ',') + ' VNĐ');
			//ychv_tongtien = 0;
		}, 
		"fnRowCallback": function( nRow, aaData, iDisplayIndex ) {
			$('td:eq(1)', nRow).css({'font-weight': 'bold'});
			
			//if (!(parseInt(aaData[22]) > ychv_SoNgayCanhBaoHetHan) && aaData[12]=="Chưa XL" && aaData[23]=="")  //ychv_SoNgayCanhBaoHetHan
			if (!(parseInt(aaData[22]) > ychv_SoNgayCanhBaoHetHan) && aaData[23]=="" && aaData[12]!="Đã XL" && aaData[8]!="")  //ychv_SoNgayCanhBaoHetHan
			{
				$(nRow).addClass( 'toihan' );
			}
			$('td:eq(8)', nRow).css({'font-weight': 'bold'});
			$('td:eq(14)', nRow).css({'font-weight': 'bold'});
			
			// tinh tổng tiền
			
			if (aaData[12]=="Đã XL")
			{
				//ychv_tongtien += parseInt(aaData[14].replace(",", ""));
				$('td:eq(12)', nRow).addClass('YCHV_DaXL'); //css({'color': '#96c716', 'font-weight': 'bold'});
			}
			else if (aaData[12]=="Chưa XL")
				$('td:eq(12)', nRow).addClass('YCHV_ChuaXL'); //css({'color': '#bc3604', 'font-weight': 'bold'});
			else if (aaData[12]=="Đang XL")
				$('td:eq(12)', nRow).addClass('YCHV_DangXL'); //css({'color': 'blue', 'font-weight': 'bold'});
			else if (aaData[12]=="Trình LĐ" || aaData[12]=="Lấy dấu")
				$('td:eq(12)', nRow).addClass('YCHV_TrinhLD'); //css({'color': 'blue', 'font-weight': 'bold'});
			
			if (aaData[21]!=''){
				nRow.setAttribute( 'title', '<b>Chú ý</b>: ' + aaData[21] ); 
				$(nRow).addClass("ghichuTooltip");
			}
			
			return nRow;
		},
		"fnFooterCallback": function ( nRow, aaData, iStart, iEnd, aiDisplay ) {
			//alert(1);
		},
		"aoColumns": [
            { "sClass" : "center", "bSortable": false },
            null,
            null,
			{ "sClass" : "center", "bSortable": false },
			{ "sClass" : "center", "bSortable": false },
            { "sClass" : "center", "bSortable": false },
            null, null, null, 
			{ "sClass": "center", "bSortable": false }, 
			null, null, null, 
			{ "sClass" : "left", "bSortable": false },
			{ "sClass" : "right", "bSortable": false },
			{ "bSortable": false }
        ],
		"aaSorting": [[1, 'asc']]
	} );
 }
 
function RefreshTableYCHV(tableId, urlData)
{
	SaveStateFilter();
	
	$(document).tooltip( "destroy" );
	
	table = $(tableId).dataTable();
	oSettings = table.fnSettings();
	$('#tnychv_dsychv_processing').attr('style', 'visibility:visible');
	$.getJSON(urlData, null, function( json )
	{
		table.fnClearTable(this);
		for (var i=0; i<json.aaData.length; i++)
		{
			table.oApi._fnAddData(oSettings, json.aaData[i]);
		}
		oSettings.aiDisplay = oSettings.aiDisplayMaster.slice();
		table.fnDraw();
		$('#tnychv_dsychv_processing').attr('style', 'visibility:hidden');
	});
 }
 
/* Formating function for row details */
function fnFormatDetails ( nTr )
{
    var aData = oTableYCHV.fnGetData( nTr );
    var sOut = '<table cellpadding="5" cellspacing="0" border="0" style="padding-left:50px;">';    
    sOut += '<tr><td><b>Vị trí lưu:</b></td><td colspan=3 style="color:red">'+aData[19]+'</td></tr>';
	sOut += '<tr><td><b>Ghi chú:</b></td><td colspan=3 style="color:red">'+aData[21]+'</td></tr>';
	sOut += '<tr><td>Người nhận:</td><td style="width:150px">'+aData[16]+'</td><td align=right>Người chuyển:</td><td>'+aData[18]+'</td></tr>';
	sOut += '<tr><td>Quá trình xử lý:</td><td colspan=3>'+aData[20]+'</td></tr>';
    sOut += '</table>';
     
    return sOut;
 }
	
function getFilter()
{
	var linkfilter = ychv_linkdata;
	if ($("#filter_phong_tnychv_thung_rac").val() != "" )
		linkfilter += "&fttr="+$("#filter_phong_tnychv_thung_rac").val();
	if ($("#filter_phong_tnychv_tinh_trang").val() != "" )
		linkfilter += "&ftt="+$("#filter_phong_tnychv_tinh_trang").val();
	if ($("#filter_phong_tnychv_nguoi_xl").val() != "" )
		linkfilter += "&fnxl="+$("#filter_phong_tnychv_nguoi_xl").val();
	if ($("#filter_phong_tnychv_nguoi_nhan").val() != "" )
		linkfilter += "&fnn="+$("#filter_phong_tnychv_nguoi_nhan").val();
	if ($("#filter_phong_tnychv_hv_nhan_yc").val() != "" )
		linkfilter += "&fhvnyc="+$("#filter_phong_tnychv_hv_nhan_yc").val();
	if ($("#filter_phong_tnychv_nam_nhan").val() != "" )
		linkfilter += "&fhvnnhan="+$("#filter_phong_tnychv_nam_nhan").val();
		
	return linkfilter;
 }
 
$(document).ready(function() {

 $("#phong_tnychvu_btn_add" ).button({ icons: {primary:'ui-icon ui-icon-plusthick'} });
 $("#phong_tnychv_btn_new" ).button({ icons: {primary:'ui-icon ui-icon-document'} });
 $("#phong_tnychv_btn_print" ).button({ icons: {primary:'ui-icon ui-icon-print'} });
 $("#phong_tnychv_btn_trash" ).button({ icons: {primary:'ui-icon ui-icon-trash'} });
 $("#phong_tnychv_btn_refresh" ).button({ icons: {primary:'ui-icon ui-icon-refresh'} });
 
 
 $("#phong_dshocvienkhoa_btn_printpreview").click(function(){
	writeConsole("<div style='font-size:13px; font-weight:bold;' align=left>ĐẠI HỌC BÁCH KHOA - TP.HCM<br/>PHÒNG ĐÀO TẠO SAU ĐẠI HỌC<br/> &nbsp;</div>" + $("#phong_dsNCSkhoa_detail").html() + "<div style='clear:both;'>&nbsp;<br/><a href='JavaScript:window.print();'>In trang này</a></div>", 800,600);
 });
 
 $("#phong_tnychvu_btn_add").click(function(){
	
	var mahvu = $("#phong_tnychv_hvu").val();
	var noidung='', strfind='', ngaytra, ghichu, tenyc = $("#phong_tnychv_hvu option:selected").html();
	
	if (ychv_validateMaHV==1 && mahvu !='')
	{
		if ( (mahvu.length == 4 || mahvu=='000') )
		{
			var tenyc_arr = tenyc.split("("), msg = "";
			if (tenyc_arr[1] != null)
			{
				strfind = tenyc_arr[1].substr(0,tenyc_arr[1].length-1);
				msg = ", " + strfind;
			}
			if ($("#tnychv_noidungyc").val() == "")
			{
				gv_open_msg_box("Vui lòng nhập nội dung yêu cầu" + msg  + ".", 'alert', 300, 180);
				return;
			}
		}
		
		if (strfind!='')
			tenyc = $.trim(tenyc.replace("(" + strfind + ")", ""));
		
		if (mahvu=='000')
			noidung = $("#tnychv_noidungyc").val(); 
		else
		{
			if ($("#tnychv_noidungyc").val()!="")
				noidung = tenyc + ': ' + $("#tnychv_noidungyc").val();
			else
				noidung = tenyc;
		}	
		ngaytra = $("#tnychv_ngaytrakq").val();
		ghichu = $("#tnychv_ghi_chu").val();
		soluong = $("#tnychv_sl").val();
		phi = $("#tnychv_don_gia").val();
		mahv = $("#tnychv_mahv").val();
		
		if (noidung!='')
		{
			if (ychv_listMahvu[mahvu+mahv]!=1)
			{
				i = $('#tnychv_dsychv_themmoi tbody tr').length;

				(ychv_classname == 'alt_') ? ychv_classname = 'alt' : ychv_classname = 'alt_';
				if (mahvu!='000')
					ychv_listMahvu[mahvu+mahv]=1;
				$( "#tnychv_dsychv_themmoi tbody" ).append( "<tr class='" + ychv_classname + "'>" +
				"<td align=left>" + mahvu + "</td>" +
				"<td align=left>" + noidung + "</td>" +
				"<td align=left>" + soluong + "</td>" +
				"<td align=right>" + parseInt(soluong*phi.replace(",","")).formatMoney(0,'.',',') + "</td>" +
				"<td align=right>" + ngaytra + "</td>" +
				"<td align=left>" + ghichu + "</td>" + "<input type=hidden id='manguoiqgychv"+i+"' value='"+$("#ma_ngq").val()+"'>" + "<input type=hidden id='mahvqgychv"+i+"' value='"+$("#tnychv_mahv").val()+"'>" +
				"<td align=right>" + mahv + "</td>" +
				"<td><button class='tnychv_remove' style='height:26px;width:28px;' onclick='removeRow( getRowIndex(this) );'></button></td>" +
				"</tr>" );			

				$("button.tnychv_remove" ).button({ icons: {primary:'ui-icon ui-icon-trash'} });
				
				$("#tnychv_ngaytrakq").val('');
				$("#tnychv_ghi_chu").val('');
				$("#tnychv_sl").val('1');
				$("#tnychv_don_gia").val('');
				$("#tnychv_noidungyc").val(''); 
				$("#phong_tnychv_hvu").val('');
			}
			else
				gv_open_msg_box("Không thể thêm yêu cầu học vụ này vì đã có <b>"+tenyc+"</b> của học viên <b>"+ mahv +"-"+ $("#phong_tnychv_ho_ten").html() + "</b> ở danh sách bên dưới. Vui lòng nhập dữ liệu lại.", 'alert', 250, 180);

		}	
		else
			//alert('Vui lòng nhập noi dung yêu cầu');
			gv_open_msg_box("Vui lòng nhập nội dung yêu cầu", 'info', 250, 180);
	}
	else
	{
		if (ychv_validateMaHV==0)
			gv_open_msg_box("Nhập sai mã học viên", 'info', 250, 180);
		else if ($("tnychv_mahv").val()=='')
			gv_open_msg_box("Bạn chưa nhập mã học viên", 'info', 250, 180);
		else if (mahvu=='')
			gv_open_msg_box("Bạn chưa chọn yêu cầu học vụ", 'info', 250, 180);
	}	
	
 });
 
 $("#tnychv_ngaytrakq").mask("99/99/9999");
 
 $("#tnychv_ngaytrakq").datepicker({
	showOn: "button",
	buttonImage: "icons/calendar.gif",
	buttonImageOnly: true,
	showButtonPanel: false,
	dateFormat: "dd/mm/yy"
 });
 
 $("#phong_tnychv_hentra_xuly").datepicker({
	showOn: "button",
	buttonImage: "icons/calendar.gif",
	buttonImageOnly: true,
	showButtonPanel: false,
	dateFormat: "dd/mm/yy"
 });
 $("#phong_tnychv_hentra_xuly").mask("99/99/9999");
 
 $("#ychv_so_ngay_canh_bao").change(function(e){
	ychv_SoNgayCanhBaoHetHan = this.value;
	RefreshTableYCHV(oTableYCHV,getFilter());
 });
 
 $("#tnychv_mahv").change(function(e){
	$("#phong_tnychv_ho_ten").html("<img border='0' src='../images/ajax-loader.gif'/>");
	dataString = 'a=getname&hisid=<?php echo $_REQUEST["hisid"]; ?>'+'&m='+$("#tnychv_mahv").val();
	xreq = $.ajax({
	  type: 'POST', dataType: "json",
	  url: 'phong/phong_ychv_tiepnhan_process.php',
	  data: dataString,
	  success: function(data) {
		//alert (data);
		if (data.error==0)
		{
			ychv_validateMaHV = 1;
			$("#phong_tnychv_ho_ten").html(data.hoten);
			$("#phong_tnychv_chuyennganh").html(data.tennganh);
			$("#phong_tnychv_khoa").html(data.khoa);
			$("#phong_tnychv_ngaysinh").html(data.ngaysinh);
			$("#phong_tnychv_noisinh").html(data.noisinh);
		}
		else
		{
			ychv_validateMaHV = 0;
			$("#phong_tnychv_ho_ten").html("<font color=red>Không có HV</font>");
			$("#phong_tnychv_ho_ten").html("");
			$("#phong_tnychv_chuyennganh").html("");
			$("#phong_tnychv_khoa").html("");
			$("#phong_tnychv_ngaysinh").html("");
			$("#phong_tnychv_noisinh").html("");
		}
	  },
	  error: function(xhr, ajaxOptions, thrownError) {
		
	  }
	});
 });
 
 $("#phong_tnychv_hvu").change(function(e){
	//$("#phong_tnychv_ho_ten").html("<img border='0' src='../images/ajax-loader.gif'/>");
	dataString = 'a=getychvu&hisid=<?php echo $_REQUEST["hisid"]; ?>'+'&m='+$("#phong_tnychv_hvu").val();
	xreq = $.ajax({
	  type: 'POST', dataType: "json",
	  url: 'phong/phong_ychv_tiepnhan_process.php',
	  data: dataString,
	  success: function(data) {
		$("#tnychv_ngaytrakq").val(data.ngaytra);
		$("#tnychv_don_gia").val(parseInt(data.dg).formatMoney(0,'.',','));
		$("#tnychv_ghi_chu").val(data.gc);
		$("#phong_tnychv_nguoi_giai_quyet").html(data.gq);
		$("#ma_ngq").val(data.mngq);
	  },
	  error: function(xhr, ajaxOptions, thrownError) {

	  }
	});
 });
 
 $("#filter_phong_tnychv_nam_nhan, #filter_phong_tnychv_thung_rac, #filter_phong_tnychv_tinh_trang, #filter_phong_tnychv_nguoi_xl, #filter_phong_tnychv_nguoi_nhan, #filter_phong_tnychv_hv_nhan_yc").change(function(e){	
	RefreshTableYCHV(oTableYCHV,getFilter());
	
	if ($("#filter_phong_tnychv_thung_rac").val() == '')
		$( "#phong_tnychv_btn_trash" ).button( "option", "label", "Thùng rác" );
	else
		$( "#phong_tnychv_btn_trash" ).button( "option", "label", "Phục hồi rác" );
 });
 
$("#tnychv_noidungyc, #tnychv_ghi_chu").change(function(e){
	if ($(this).val()!="")
	{
		$(this).val($(this).val().charAt(0).toUpperCase() + $(this).val().slice(1));
	}
});

 // Bo rac
 $('#phong_tnychv_btn_trash').click( function() {
	var s=getYCHVselected(null, false);
	var str = '';
	//alert(maychv);
	if (s != '')
	{
		gv_processing_diglog("open", "Đang xử lý ... vui lòng chờ");
		dataString = 'a=ThungRac&hisid=<?php echo $_REQUEST["hisid"]; ?>'
			+'&m='+ s + '&c=' + $("#filter_phong_tnychv_thung_rac").val();
		xreq = $.ajax({
		  type: 'POST', dataType: "json",
		  url: 'phong/phong_ychv_tiepnhan_process.php',
		  data: dataString,
		  success: function(data) {
			if (data.success==-1)
			{
				gv_processing_diglog("close", "Đang xử lý ... vui lòng chờ");
				
				if ($("#filter_phong_tnychv_thung_rac").val()==1)
					str = 'Không thể khôi phục lại các yêu cầu: ';
				else
					str = 'Không thể xóa các yêu cầu: ';
					
				gv_open_msg_box(str + "<b>" + data.ma + "</b>", 'alert', 250, 180);
			}
			else
			{
				RefreshTableYCHV(oTableYCHV,getFilter());
				gv_processing_diglog("close", "...");
				
				if ($("#filter_phong_tnychv_thung_rac").val()==1)
					str = 'Đã khôi phục lại các yêu cầu: ';
				else
					str = 'Đã xóa các yêu cầu: ';
					
				gv_open_msg_box(str + " <b>" + data.ma + "</b>", 'info', 250, 180);
			}
		  }
		});
	}
 });
 
 // Làm mới danh sách
 $('#phong_tnychv_btn_refresh').click( function() {
	
	gv_processing_diglog("open", "Đang xử lý ... vui lòng chờ");
	
	dataString = 'a=getnewychv&hisid=<?php echo $_REQUEST["hisid"]; ?>';
	xreq = $.ajax({
	  type: 'POST', dataType: "json",
	  url: 'phong/phong_ychv_tiepnhan_process.php',
	  data: dataString,
	  success: function(data) {
		if (data.success==-1)
		{
			gv_processing_diglog("close", "Đang xử lý ... vui lòng chờ");
			
			str = 'Có lỗi trong quá trình làm mới danh sách.';
				
			gv_open_msg_box(str, 'alert', 250, 180);
		}
		else
		{
			RefreshTableYCHV(oTableYCHV,getFilter());
			gv_processing_diglog("close", "...");
			//str = 'Đã tải danh sách yêu cầu học vụ mới nhất.';
			//gv_open_msg_box(str, 'info', 250, 180);
		}
	  }
	});
	
 });
 
 // In bien nhan hoc vu 
 $('#phong_tnychv_btn_print').click( function() {
	
	var i=0, html1, html, noidung, sl, ngaytra, tenhv, mahv;
	var dt = new Date();
	
	// Cập nhật lại tên người xử lý mới vào table mà ko cần load lại list từ db
	tableData = document.getElementById('tnychv_dsychv');
	
	html1 ="<table style='font-size: 14px; width:100%; margin-top:5px' class='bordertable' border=1>"+
			"<tr><td align=left style='font-weight:bold'>Mã</td> <td align=left style='font-weight:bold'>Nội dung</td><td style='font-weight:bold' align=center>Số lượng</td><td align=right style='font-weight:bold'>Ngày trả KQ</td></tr>";
	
	$("#tnychv_dsychv tbody input[type=checkbox]").each(function() {
		var $input = $( this );
		if ($input.attr("checked")=="checked")
		{
			var nTr = $(this).parents('tr')[0];
			var aData = oTableYCHV.fnGetData( nTr );
			if (i==0){
				tenhv = nTr.cells[11].innerHTML;
				mahv = nTr.cells[10].innerHTML;
			}
			if (mahv == nTr.cells[10].innerHTML)
			{
				maychv = nTr.cells[1].innerHTML;
				noidung = nTr.cells[2].innerHTML;
				sl = nTr.cells[3].innerHTML;
				ngaytra = nTr.cells[8].innerHTML;
				
				html1 += "<tr><td align=left style='font-weight:bold'>"+maychv+"</td> <td align=left>"+noidung+"</td><td align=center>"+sl+"</td><td align=right>"+ngaytra+"</td></tr>";
			}						
			// bo check
			$input.attr('checked', false);
			
			i++;
		}
	});
	
	html1 += "</table>";
	
	html = "<style> .bordertable {border-color: #000000; border-width: 1px; border-style: solid; border-collapse:collapse;}</style>"+
			"<table style='font-size: 12px; width:100%'>"+
			"<tr>"+
				"<td align=left style='width:50%'>"+
					"<div align=center style='width:300px; margin-top:-10px'>"+
					"TRƯỜNG ĐẠI HỌC BÁCH KHOA <br/>"+
					"<U><B>PHÒNG ĐÀO TẠO SĐH</B></U>"+
					"</div>"+
				"</td>"+
				"<td align=right style='width:50%'> "+
					"<div align=center style='width:300px; margin-top:-10px'><B>CỘNG HÒA XÃ HỘI CHỦ NGHĨA VIỆT NAM</B><br/>"+
					"<u>ĐỘC LẬP - TỰ DO - HẠNH PHÚC</u>"+
					"</div>"+
				"</td>"+
			"</tr>"+
			"<tr><td align=center colspan=2><div style='font-weight: bold; font-size: 15px; margin: 10px 0 10px 0;' >BIÊN NHẬN HỌC VỤ</div></td></tr>"+
			"<tr><td align=left style='' colspan=2><div style='font-size: 14px'>Phòng ĐT-SĐH có nhận phiếu đề nghị giải quyết học vụ của học viên <b>"+tenhv+"</b> (<b>"+mahv+"</b>):</div></td></tr>"+
			"<tr><td align=center colspan=2>"+html1+"</td></tr>"+
			"<tr>"+
				"<td align=center style='width:50%'></td>"+
				"<td align=right style='width:50%'> "+
					"<div align=center style='width:300px; margin-top:10px'>Ngày "+dt.getDate()+" tháng "+ (dt.getMonth()+1) + " năm "+dt.getFullYear()+" <br/>"+
					"<b>Người nhận</b><br/><br/><br/><br/><br/><br/>"+
					"<b><?php echo $hoten; ?></b>"+
					"</div>"+
				"</td>"+
			"</tr>"+
			"</table>";
	//gv_open_msg_box(html, 'info', 600, 400);
	if (i!=0)
		print_llkh_writeConsole(html, 0, "Biên nhận học vụ", "style='font-family:Times New Roman, Arial,Helvetica,sans-serif;'");
	else
		gv_open_msg_box("Vui lòng chọn yêu cầu học vụ cần in", 'info', 250, 180);
	
 });
 
 $('#phong_tnychv_btn_new').click( function() {
	$("#tnychv_dsychv_themmoi tbody" ).html("");
	$("#tnychv_ngaytrakq").val("");
	$("#tnychv_don_gia").val("");
	$("#tnychv_noidungyc").val("");
	$("#tnychv_ghi_chu").val("");
	$("#phong_tnychv_nguoi_giai_quyet").html("");
	$("#tnychv_mahv").val("");
	$("#phong_tnychv_ho_ten").html("");
	$("#phong_tnychv_hvu").val("");
	//$("#phong_tnychv_nguoi_nhan").val("");
	$("#phong_tnychv_chuyennganh, #phong_tnychv_khoa, #phong_tnychv_ngaysinh, #phong_tnychv_noisinh").html("");
	$("#ychv_save_print").button("enable");
	$("#ychv_save").button("enable");
	ychv_oCountRow = 0;
	ychv_listMahvu = new Array();
	$('#phong_tnyc_frm_addYC').dialog('open');
 });
 
  
 $("#phong_tnyc_frm_addYC").dialog({
	resizable: false,
	autoOpen: false,
	width:700, height:500,
	modal: true,
	buttons: [
		{
			id: "ychv_save_print",
			text: "Lưu & In biên nhận",
			click: function() {
				var ychv_countRow = $('#tnychv_dsychv_themmoi tbody tr').length;
				if (ychv_countRow){
					$("#ychv_save_print").button("disable");
					$("#ychv_save").button("disable");
					SaveHVandPrint(1);
				}
				else
					gv_open_msg_box("Chưa có dữ liệu", 'info', 250, 180);
			}
		},
		{
			id: "ychv_save",
			text: "Lưu",
			click: function() {
				var ychv_countRow = $('#tnychv_dsychv_themmoi tbody tr').length;
				if (ychv_countRow){
					$("#ychv_save_print").button("disable");
					$("#ychv_save").button("disable");
					SaveHVandPrint(0);
				}else
					gv_open_msg_box("Chưa có dữ liệu", 'info', 250, 180);
			}
		},
		{
			id: "ychv_close",
			text: "Đóng",
			click: function() {
				$("#ychv_save_print").button("enable");
				$("#ychv_save").button("enable");
				
				$( this ).dialog( "close" );
			}
		}
	]
 });
 
 $("#phong_tnychv_chuyen_xuly").dialog(
 {
	resizable: false,
	autoOpen: false,
	width:300, height:210,
	modal: true,
	buttons: {
		"Chuyển": function() {
			$( this ).dialog( "close" );
			
			gv_processing_diglog("open", "Chuyển người xử lý học vụ");
			
			dataString = 'a=chuyenxuly&hisid=<?php echo $_REQUEST["hisid"]; ?>'
				+'&m='+  $("#phong_tnychv_ma_ychv_chuyen").val() + '&mnxl=' + $("#phong_tnychv_nguoi_xu_ly").val()+'&mnc=<?php echo $id; ?>&' + $("#phong_tnychv_ghichu_ychv_chuyen").serialize();
			xreq = $.ajax({
			  type: 'POST', dataType: "json",
			  url: 'phong/phong_ychv_tiepnhan_process.php',
			  data: dataString,
			  success: function(data) {
				if (data.success==-1)
				{
					gv_processing_diglog("close", "Chuyển người xử lý học vụ");
					gv_open_msg_box("Không thể chuyển cho người xử lý yêu cầu", 'info', 250, 180);
				}
				else
				{
					// Cập nhật lại tên người xử lý mới vào table mà ko cần load lại list từ db
					tableData = document.getElementById('tnychv_dsychv');
					var hotennxl = $("#phong_tnychv_nguoi_xu_ly option:selected").html();
					var ghichu = $("#phong_tnychv_ghichu_ychv_chuyen").val();
					var tennxl = hotennxl.split(' ');
					
					$("#tnychv_dsychv tbody input[type=checkbox]").each(function() {
						var $input = $( this );
						if ($input.attr("checked")=="checked")
						{
							var nTr = $(this).parents('tr')[0];
							var aData = oTableYCHV.fnGetData( nTr );
							
							nTr.cells[6].innerHTML=tennxl[tennxl.length-1];
							aData[21] = ghichu;
							aData[6] = tennxl[tennxl.length-1];
							
							if ( oTableYCHV.fnIsOpen(nTr) )
							{
								oTableYCHV.fnOpen( nTr, fnFormatDetails(nTr), 'details' );	
								$(".qttooltips").tooltip();
							}
							
							// Cập nhật tooltip của Row
							if (ghichu=='')
								nTr.setAttribute( 'title', ''); 
							else
								nTr.setAttribute( 'title', '<b>Chú ý</b>: ' + ghichu);
							
							// bo check
							$input.attr('checked', false);
							
						}
					});
					
					//RefreshTableYCHV(oTableYCHV,getFilter());
					gv_processing_diglog("close", "Chuyển người xử lý học vụ");
					gv_open_msg_box("Đã chuyển cho người xử lý thành công", 'info', 250, 180);
				}

				
			  },
			  error: function(xhr, ajaxOptions, thrownError) {
				gv_open_msg_box(thrownError, 'info', 250, 180);
			  }
			});
		},
		"Đóng": function() {
			
			//ychv_listSelectedYCHV[ychv_maychv_selected] = false;
			SelectAll(false);
			
			$( this ).dialog( "close" );
		}
		
	}
	
 });

 $( "#phong_tnychv_xuly" ).dialog({
	resizable: false,
	autoOpen: false,
	width:400, height:320,
	modal: true,
	buttons: {
		"Cập nhật": function() { 
			
			
			if ($("#phong_tnychv_tinhtrang_xuly").val() == ""){
				gv_open_msg_box("Vui lòng chọn tình trạng xử lý cho yêu cầu", 'alert', 250, 180);
				return false;
			}
			
			$( this ).dialog( "close" );
			
			gv_processing_diglog("open", "Xử lý học vụ");
			
			dataString = 'a=xuly&hisid=<?php echo $_REQUEST["hisid"]; ?>'
				+'&m='+  $("#phong_tnychv_ma_xuly").val() + '&mnxl=<?php echo $id;?>'
				+'&' + $("#phong_tnychv_vitriluu_xuly").serialize()+'&phong_tnychv_tinhtrang_xuly=' + $("#phong_tnychv_tinhtrang_xuly").val()
				+'&' + $("#phong_tnychv_ketqua_xuly").serialize()+'&'+$("#phong_tnychv_hentra_xuly").serialize();
			xreq = $.ajax({
			  type: 'POST', dataType: "json",
			  url: 'phong/phong_ychv_tiepnhan_process.php',
			  data: dataString,
			  success: function(data) {
				if (data.success==-1)
				{
					gv_processing_diglog("close", "Xử lý học vụ");
					gv_open_msg_box("Không thể cập nhật thông tin xử lý yêu cầu", 'info', 250, 180);
				}
				else
				{
					// Cập nhật lại tên người xử lý mới vào table mà ko cần load lại list từ db
					tableData = document.getElementById('tnychv_dsychv');
					var tinhtrang = $("#phong_tnychv_tinhtrang_xuly option:selected").html();
					var vitriluu = $("#phong_tnychv_vitriluu_xuly").val();
					var ketquaxuly = $("#phong_tnychv_ketqua_xuly").val();
					var hentra_arr = $("#phong_tnychv_hentra_xuly").val().split("/");
					var hentra = hentra_arr[2] + '-' + hentra_arr[1] + '-' + hentra_arr[0];
					
					$("#tnychv_dsychv tbody input[type=checkbox]").each(function() {
						var $input = $( this );
						if ($input.attr("checked")=="checked")
						{
							var nTr = $(this).parents('tr')[0];
							var aData = oTableYCHV.fnGetData( nTr );
							
							// cap nhat tinh trang
							nTr.cells[12].innerHTML=tinhtrang; 
							$('td:eq(12)', nTr).removeClass();
							if (tinhtrang=="Đã XL")
							{
								$(nTr).removeClass( 'toihan' );
								$('td:eq(12)', nTr).addClass('YCHV_DaXL');
							}
							else if (tinhtrang=="Chưa XL")
								$('td:eq(12)', nTr).addClass('YCHV_ChuaXL'); 
							else if (tinhtrang=="Đang XL")
								$('td:eq(12)', nTr).addClass('YCHV_DangXL'); 
							else if (tinhtrang=="Trình LĐ" || tinhtrang=="Lấy dấu")
								$('td:eq(12)', nTr).addClass('YCHV_TrinhLD');
							
							if (!(parseInt(aData[22]) > ychv_SoNgayCanhBaoHetHan) && aData[23]=="" && tinhtrang!="Đã XL")  //ychv_SoNgayCanhBaoHetHan
								$(nTr).addClass( 'toihan' );
															
							////
							
							nTr.cells[13].innerHTML=ketquaxuly; // cap nhat ket qua xu ly
							nTr.cells[8].innerHTML=hentra; // cap nhat ngay hen tra ket qua xu ly
							
							aData[19] = vitriluu; // Cap nhat vi tri luu o muc detail
							aData[8] = hentra;
							if ( oTableYCHV.fnIsOpen(nTr) )
							{
								oTableYCHV.fnOpen( nTr, fnFormatDetails(nTr), 'details' );	
								$(".qttooltips").tooltip();
							}
							
							// bo check
							$input.attr('checked', false);
						}
					});
					
					gv_processing_diglog("close", "Xử lý học vụ");
					gv_open_msg_box("Đã cập nhật thành công", 'info', 250, 180);
				}
				
			  },
			  error: function(xhr, ajaxOptions, thrownError) {
				gv_open_msg_box(thrownError, 'info', 250, 180);
			  }
			});
		},
		"Đóng": function() {
			
			SelectAll(false);
			
			$( this ).dialog( "close" );
		}
		
	}
	
 });
 
 $( "#phong_tnychv_phuchoitrahv" ).dialog({
	resizable: false,
	autoOpen: false,
	width:300, height:180,
	modal: true,
	buttons: {
		"Đồng ý": function() { 
			updateTraHV(nTrClicked, 'PhucHoiCapnhatTraHV');
			$( this ).dialog( "close" );
		},
		"Đóng": function() {
			SelectAll(false);
			$( this ).dialog( "close" );
		}
	}
 });
 
 $('input[placeholder],textarea[placeholder]').placeholder();

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

 $("#tnychv_don_gia").each(function() {
		formatNumberVND($(this));
 });
 
 $("#tnychv_don_gia").keydown(function(e) {
		handleKeyDown(e);
	}).keyup(function(e) {
		handleKeyUp(e);
		if (!ignoreEvent(e)) formatNumberVND($(this));
	});
		
 $('#tnychv_dsychv tbody td img').live( 'click', function () {
        var nTr = $(this).parents('tr')[0];
		nTrClicked = nTr;
		
		// Click vào icon detailsicon
		if (this.className == 'detailsicon'){
			if ( oTableYCHV.fnIsOpen(nTr) )
			{
				/* This row is already open - close it */
				this.src = "icons/details_open.png";
				oTableYCHV.fnClose( nTr );
			}
			else
			{
				/* Open this row */
				this.src = "icons/details_close.png";
				oTableYCHV.fnOpen( nTr, fnFormatDetails(nTr), 'details' );
				
				$(".qttooltips").tooltip();
			}
		}
		// Click vào icon chuyen nguoi giai quyet
		else if (this.className == 'chuyennguoigiaiquyet tooltips'){
			openChuyenXuLy(nTr);
		}
		else if (this.className == 'trahvicon'){
			updateTraHV(nTr, 'CapnhatTraHV');
		}
		else if (this.className == 'phuchoitrahvicon tooltips'){
			//updateTraHV(nTr, 'PhucHoiCapnhatTraHV');
			s = getYCHVselected(nTr, true);
			$("#phong_tnychv_label_ma_phuchoi").html(s.replace(/,/g, ', '));
			$('#phong_tnychv_phuchoitrahv').dialog('open');
		}
		else if (this.className == 'giaiquyethv tooltips'){
			openXuLy(nTr);
		}
		
    });
 
 $('#tnychv_dsychv tbody td input[type=checkbox]').live('click', function() {
	var nTr = $(this).parents('td')[0];
	$(nTr).toggleClass('row_selected');
	
 } );
 
 // Load from cookie
 LoadStateFilter();
 
 initialTableYCHV(getFilter());
});

/* Get the rows which are currently selected */
function fnGetSelected( oTableLocal ){
    return oTableLocal.$('tr.row_selected');
}

function removeRow(pindex){
	i = pindex + 1;
	t = document.getElementById('tnychv_dsychv_themmoi');
	mahvu = t.rows[i].cells[0].innerHTML;
	mahv = t.rows[i].cells[6].innerHTML;
	ychv_listMahvu[mahvu+mahv] = 0;
	t.deleteRow( i );
}
 
function openChuyenXuLy(nTr){
	var aData = oTableYCHV.fnGetData( nTr );
	var s = getYCHVselected(nTr, true);
		
	if (s!="")
	{
		$("#phong_tnychv_ma_chuyen_xuly").html(s.replace(/,/g, ', '));
		$("#phong_tnychv_ma_ychv_chuyen").val(s);
		$("#phong_tnychv_ghichu_ychv_chuyen").val(aData[21]);
		$('#phong_tnychv_chuyen_xuly').dialog('open');	
	}
 }
 
function openXuLy(nTr){
	var aData = oTableYCHV.fnGetData( nTr );
	
	var ychv_hentra = aData[8], ychv_hentra_arr;
	
	var s = getYCHVselected(nTr, true), tinhtrang;
	
	// Kiểm tra đã có người xử lý hay chưa
	if (aData[6]=='' && s.indexOf(",") < 0)
	{
		gv_open_msg_box("Yêu cầu học vụ này chưa chuyển cho chuyên viên xử lý. Vui lòng chuyển cho chuyên viên có trách nhiệm xử lý học vụ này.", 'alert', 250, 200);
		return;
	}
	////
	
	if (s!="")
	{
		$("#phong_tnychv_label_ma_xuly").html(s.replace(/,/g, ', '));
		$("#phong_tnychv_ma_xuly").val(s);
		if (ychv_hentra!='') // yyyy/mm/dd
		{
			ychv_hentra_arr = ychv_hentra.split("-");
			ychv_hentra = ychv_hentra_arr[2] + '/' + ychv_hentra_arr[1] + '/' + ychv_hentra_arr[0];
		} 
		switch (aData[12])
		{
			case "Chưa XL":
				tinhtrang = 0;
				break;
			case "Đang XL":
				tinhtrang = 1;
				break;
			case "Đã XL":
				tinhtrang = 2;
				break;
			case "Trình LĐ":
				tinhtrang = 3;
				break;
			case "Lấy dấu":
				tinhtrang = 4;
				break;
			default:
				tinhtrang = '';
		}
		
		$("#phong_tnychv_tinhtrang_xuly").val(tinhtrang);
		$("#phong_tnychv_hentra_xuly").val(ychv_hentra);
		$("#phong_tnychv_vitriluu_xuly").val(aData[19]);
		$("#phong_tnychv_ketqua_xuly").val(aData[13]);
		
		$('#phong_tnychv_xuly').dialog('open');	
	}
 }
 
function updateTraHV(nTr, pTraHS){
	//ychv_maychv_selected = nTr.cells[1].innerHTML;
	s = getYCHVselected(nTr, true, 1);
	
	//if ()
	//{
		gv_processing_diglog("open", "Cập nhật trả yêu cầu học vụ");
		dataString = 'a='+pTraHS+'&hisid=<?php echo $_REQUEST["hisid"]; ?>'
			+'&m='+ s + '&mntra=<?php echo $id; ?>'+ '&tenntra=<?php echo $hoten; ?>';
		xreq = $.ajax({
			  type: 'POST', dataType: "json",
			  url: 'phong/phong_ychv_tiepnhan_process.php',
			  data: dataString,
			  success: function(data) {
				if (data.success==-1)
				{
					gv_processing_diglog("close", "Cập nhật trả yêu cầu học vụ");
					gv_open_msg_box("Không thể cập nhật trả yêu cầu cho học viên", 'info', 250, 180);
				}
				else
				{
					// Cập nhật lại tên người xử lý mới vào table mà ko cần load lại list từ db
					$("#tnychv_dsychv tbody input[type=checkbox]").each(function() {
						var $input = $( this );
						if ($input.attr("checked")=="checked")
						{
							var nTr = $(this).parents('tr')[0];
							var aData = oTableYCHV.fnGetData( nTr );
							if (pTraHS == 'PhucHoiCapnhatTraHV'){
								//nTr.cells[9].innerHTML="<img src='icons/circle-red.png' border=0 class='trahvicon' style='cursor:pointer'>"; // cap nhat trạng thái trả cho hv
								$("td:eq(9) img",nTr).removeClass();
								$("td:eq(9) img",nTr).attr("src", "icons/circle-red.png");
								$("td:eq(9) img",nTr).attr("title", "");
								$("td:eq(9) img",nTr).addClass("trahvicon");
							}
							else
							{
								//nTr.cells[9].innerHTML="<img class='phuchoitrahvicon tooltips' title='Trả ngày: "+data.time+"' src='icons/circle-green.png' border=0 style='cursor:pointer'>"; // cap nhat trạng thái trả cho hv
								
								$("td:eq(9) img",nTr).attr("src", "icons/circle-green.png");
								$("td:eq(9) img",nTr).attr("title", "Trả ngày: "+data.time+"<br/>Người trả: "+data.name);
								$("td:eq(9) img",nTr).removeClass();
								$("td:eq(9) img",nTr).addClass("phuchoitrahvicon tooltips");
								$(nTr).removeClass( 'toihan' ); // loai vo class toihan khoi yeu cau hoc vu đã đánh dấu trả cho học viên
							}
							
							$input.attr('checked', false);
						}
					});
					
					gv_processing_diglog("close", "Cập nhật trả yêu cầu học vụ");
					gv_open_msg_box("Đã cập nhật trả yêu cầu học vụ cho học viên", 'info', 250, 180);
				}
				
			  },
			  error: function(xhr, ajaxOptions, thrownError) {
				gv_open_msg_box(thrownError, 'info', 250, 180);
			  }
		});
	//}
 }

function getYCHVselected(nTr, AutoSelectFirst, pType)
{
// pType = 1 : TraHV
	var s = '';
	
	if (AutoSelectFirst)
		$("td:eq(0) input[type=checkbox]", nTr).attr("checked", true);
		
	$("#tnychv_dsychv tbody input[type=checkbox]").each(function() 
	{
		var nTrTam = $(this).parents('tr')[0];
		var aData = oTableYCHV.fnGetData( nTrTam );
		var $input = $( this );
		if ($input.attr("checked")=="checked" )
		{
			if (pType==1)
			{
				s += $input.attr('value')+',';
			}
			else
			{
			<?php 
				// Kiểm tra xử lý tất cả
				if (allowPermisstion($usr, '049', $db_conn)) { 
			?>
					s += $input.attr('value')+',';
			<?php
				}
				// Kiểm tra xử lý của chính mình
				else if (allowPermisstion($usr, '048', $db_conn)) {
			?>
					if (aData[24] == '<?php echo $id; ?>')
						s += $input.attr('value')+','; 
					else
						$('td:eq(0) input[type=checkbox]', nTrTam).attr('checked', false);
			<?php
				}
			?>
			}
		}
    });
	if (s!='')
		s = s.substr(0,s.length-1);
	
	return s;
}

function SelectAll(pTrueFalse)
{
	$("#tnychv_dsychv tbody input[type=checkbox]").each(function() {
		$(this).attr("checked", pTrueFalse);
	});
}

function removeRowTableData(pindex){
	oTableYCHV.fnDeleteRow( pindex ); 
}

function SaveHVandPrint(p_print)
{
	tableData = document.getElementById('tnychv_dsychv_themmoi');
	
	if ($("#phong_tnychv_nguoi_nhan").val()=="")
	{
		gv_open_msg_box("Vui lòng chọn chuyên viên nhận hồ sơ", 'info', 250, 180);
		return;
	}
	
	/*
	if ($("#tnychv_mahv").val()=="")
	{
		gv_open_msg_box("Vui lòng nhập mã học viên", 'info', 250, 180);
		return;
	}
	*/
	var ychv_countRow = $('#tnychv_dsychv_themmoi tbody tr').length;
	if (ychv_countRow)
	{
		gv_processing_diglog("open", "Đang xử lý ... vui lòng chờ");
		
		dataString = 'a=addychvu&hisid=<?php echo $_REQUEST["hisid"]; ?>'
			+'&m='+  $("#tnychv_mahv").val() + '&mnn=' + $("#phong_tnychv_nguoi_nhan").val()+ '&tnn=' + encodeURIComponent($("#phong_tnychv_nguoi_nhan option:selected").html()) + '&c='+ychv_countRow + '&thv=' +encodeURIComponent($("#phong_tnychv_ho_ten").html());
		for (var i=0; i<ychv_countRow;i++)
		{
			// [i+1] : bỏ qua dòng đầu là header
			mayc = tableData.rows[i+1].cells[0].innerHTML;
			noidung = tableData.rows[i+1].cells[1].innerHTML;
			sl = tableData.rows[i+1].cells[2].innerHTML;
			phi = tableData.rows[i+1].cells[3].innerHTML; 
			ngaytra = tableData.rows[i+1].cells[4].innerHTML; 
			ghichu = tableData.rows[i+1].cells[5].innerHTML;
			mahv = tableData.rows[i+1].cells[6].innerHTML;
			mangq = $("#manguoiqgychv"+i).val();
			
			//alert (ma + noidung + sl + phi + ngaytra + ghichu);
			dataString +='&mhv'+i+'='+ mahv +'&myc'+i+'='+ mayc +'&n'+i+'=' + encodeURIComponent(noidung) +'&s'+i+'='+ sl + '&p'+i+'='+ phi 
			+'&nt'+i+'='+ encodeURIComponent(ngaytra) +'&g'+i+'='+ encodeURIComponent(ghichu) + '&mngq'+i+'='+mangq;
		}
		//alert (dataString);
		
		xreq = $.ajax({
		  type: 'POST', dataType: "json",
		  url: 'phong/phong_ychv_tiepnhan_process.php',
		  data: dataString,
		  success: function(data) {
			gv_processing_diglog("close", "Xử lý học vụ");
			
			if (data.success == 1)
			{
				if (p_print==1)
				{
					print_llkh_writeConsole(reverse_escapeJsonString(data.html), 0, "Biên nhận học vụ", "style='font-family:Times New Roman, Arial,Helvetica,sans-serif;'");
				}
			}
			else
			{
				gv_open_msg_box("Có lỗi trong quá trình lưu, chi tiết lỗi: <br/><div style='margin: 5px 0 0 5px'>" + data.msgerr+'</div>', 'alert', 250, 180, true);
			}
			
			
		  },
		  error: function(xhr, ajaxOptions, thrownError) {
		  }
		});
		
		//$(document).tooltip("destroy");
		RefreshTableYCHV(oTableYCHV,getFilter());
		$( "#phong_tnyc_frm_addYC" ).dialog( "close" );
		
	}
	else
	{
		gv_open_msg_box("Vui lòng nhập thông tin đầy đủ", 'info', 250, 180);
	}
}

function SaveStateFilter(){
//#filter_phong_tnychv_nam_nhan, #filter_phong_tnychv_thung_rac, #filter_phong_tnychv_tinh_trang, #filter_phong_tnychv_nguoi_xl, #filter_phong_tnychv_nguoi_nhan, #filter_phong_tnychv_hv_nhan_yc, #ychv_so_ngay_canh_bao
	var filterstr = $("#filter_phong_tnychv_nam_nhan").val()+"&"+$("#filter_phong_tnychv_thung_rac").val()+"&"+$("#filter_phong_tnychv_tinh_trang").val()
	+"&" + $("#filter_phong_tnychv_nguoi_xl").val()+"&"+$("#filter_phong_tnychv_nguoi_nhan").val()
	+"&"+$("#filter_phong_tnychv_hv_nhan_yc").val()+"&"+$("#ychv_so_ngay_canh_bao").val();
	
	$.cookie("FilterYCHVPGS", filterstr, { expires : 30 });
}

function LoadStateFilter(){
//#filter_phong_tnychv_nam_nhan, #filter_phong_tnychv_thung_rac, #filter_phong_tnychv_tinh_trang, #filter_phong_tnychv_nguoi_xl, #filter_phong_tnychv_nguoi_nhan, #filter_phong_tnychv_hv_nhan_yc, #ychv_so_ngay_canh_bao
	if ($.cookie("FilterYCHVPGS") != null) {
		var cookieValueArr = $.cookie("FilterYCHVPGS").split("&");
		
		if ($.cookie("FilterYCHVPGS") != "")
		{
			$("#filter_phong_tnychv_nam_nhan").val(cookieValueArr[0]);
			$("#filter_phong_tnychv_thung_rac").val(cookieValueArr[1]);
			$("#filter_phong_tnychv_tinh_trang").val(cookieValueArr[2]);
			$("#filter_phong_tnychv_nguoi_xl").val(cookieValueArr[3]);
			$("#filter_phong_tnychv_nguoi_nhan").val(cookieValueArr[4]);
			$("#filter_phong_tnychv_hv_nhan_yc").val(cookieValueArr[5]);
			ychv_SoNgayCanhBaoHetHan = cookieValueArr[6];
			//$("#ychv_so_ngay_canh_bao").val(cookieValueArr[6]);
			//alert(cookieValueArr[6]);
		}
	}
}

function ShowTooltip()
{
}


</script>



<?php 
if (isset ($db_conn))
	oci_close($db_conn);
?>