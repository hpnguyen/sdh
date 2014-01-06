<?php
include('libs/connect.php'); 
include "libs/pgslibshv.php";

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
$w = escape($_POST['w']);

if ($w=='tracuuvbang')
{
//if (isset($_POST['txtSoVB']))
	$SoVB = escape($_POST['s']);
	
//if (isset($_POST['txtMaHV']))
	$MaHV = escape($_POST['m']);

//if (isset($_POST['txtHoTen'])){
	$HoTen = escape($_POST['h']);
	$NgaySinh = escape($_POST['n']);
//}

	
	if ($SoVB != null)
	{
		$SoVB = trim(str_replace(array("'","BM:"), array("''",""),$SoVB));
		$q="UPPER(X.SO_HIEU_BANG) = 'BM:".strtoupper($SoVB)."'";
		
	}elseif ($MaHV != null){
		
		$MaHV = str_replace("'", "''",$MaHV);
		$q="UPPER(X.MA_HOC_VIEN) = TRIM('".strtoupper($MaHV)."')";
		//echo $q;
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
	$stmt = oci_parse($db_conn, $sqlstr);
	oci_execute($stmt);
	$n = oci_fetch_all($stmt, $resDM);
	oci_free_statement($stmt);
	if ($n>0)
	{
		echo "<div style='margin-top:10px; font-size:12px; font-weight:bold;' align=center>
				Thông tin văn bằng
			</div>";
		for ($i = 0; $i < $n; $i++)
		{
			?>
			<div style="margin-top:10px;">
			<table border='0' align=center cellspacing='0' cellpadding='3' class='ui-widget ui-widget-content ui-corner-top tableData'>
			<tr>
			<td align=right class='ui-widget-header' >Mã HV:</td><td align=left style="font-weight:bold;"><?php echo $resDM['MA_HOC_VIEN'][$i]; ?></td>
			</tr>
			<tr>
			<td align=right class='ui-widget-header'>Họ tên:</td><td align=left style="font-weight:bold;"><?php echo $resDM['HO_TEN'][$i]; ?></td>
			</tr>
			<tr>
			<td align=right class='ui-widget-header'>Ngày sinh:</td><td align=left style="font-weight:bold;"><?php echo $resDM['NGAY_SINH'][$i]; ?></td>
			</tr>
			<tr>
			<td align=right class='ui-widget-header'>Nơi sinh:</td><td align=left style="font-weight:bold;"><?php echo $resDM['NOI_SINH'][$i]; ?></td>
			</tr>
			<tr>
			<td align=right class='ui-widget-header'>Tên ngành:</td><td align=left style="font-weight:bold;"><?php echo $resDM['TEN_NGANH'][$i]; ?></td>
			</tr>
			<tr>
			<td align=right class='ui-widget-header'>Số hiệu bằng:</td><td align=left style="font-weight:bold;"><?php echo $resDM['SO_HIEU_BANG'][$i]; ?></td>
			</tr>
			<tr>
			<td align=right class='ui-widget-header'>Đợt cấp bằng:</td><td align=left style="font-weight:bold;"><?php echo $resDM['DOT_CAP_BANG'][$i]; ?></td>
			</tr>
			<tr>
			<td align=right class='ui-widget-header'>Số đăng ký:</td><td align=left style="font-weight:bold;"><?php echo $resDM['SO_DANG_KY'][$i]; ?></td>
			</tr>
			</table>
			</div>
			<?php
		}
	}
	else
	{
		?>
			<div style="margin-top:15px; font-size:12px; font-weight:bold; color:red" align=center>
				
				Không tìm thấy văn bằng
				
			</div>
		<?php
	}
}
?>

<?php 
if (isset ($db_conn))
	oci_close($db_conn);
?>