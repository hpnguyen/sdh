<?php
include "libs/connect.php";
include "libs/pgslibshv.php";
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<meta http-Equiv="Cache-Control" Content="no-cache">
<meta http-Equiv="Pragma" Content="no-cache">
<meta http-Equiv="Expires" Content="0">
<title>Phòng Đào Tạo Sau Đại Học</title>
</head>
<script src="../js/jquery-1.8.0.min.js"></script>
<script src="../js/jquery-ui-1.8.23.custom.min.js"></script> 
<link href="../css/start/jquery-ui-1.8.18.custom.css" rel="stylesheet" type="text/css"/>
<link href="css/pgs.css" rel="stylesheet" type="text/css"/>
<body style="font-family:Arial, Helvetica, sans-serif">
	<div id=content>
	<?php
		$s = "select distinct l.ma_hoc_vien, ten_luan_an, h.ho||' '||h.ten HV,  ngay_bvnn, FILE_CB_BVNN, FILE_TT_LA_VN, FILE_TT_LA_ENG, FILE_TOM_TAT,
			 (select ho||' '||ten from can_bo_giang_day where huong_dan_1 = ma_can_bo) HD_CHINH,
			 (select ho||' '||ten from can_bo_giang_day where huong_dan_2 = ma_can_bo) HD_PHU 
		from luan_an_tien_sy l, hoc_vien h
	   where (l.ma_hoc_vien = h.ma_hoc_vien)
	   and FILE_CB_BVNN is not null";
	   
		$s = $s . " order by ngay_bvnn desc";
		
		//echo $s ;
		
		$oci = oci_parse($db_conn, $s);
		oci_execute($oci);
		$n = oci_fetch_all($oci, $rs);
		
		if ($n)
		{
			echo "<table border='0' id=table_lats align=center cellspacing='0' cellpadding='3' class='ui-widget ui-widget-content ui-corner-top tableData' width='100%'>";
				echo "<tr class='ui-widget-header'>
						<td align='left' style='width:150px' >Nghiên Cứu Sinh</td>
						<td align='left' >Tên Luận án </td>
						<td align='left' style='width:150px' >Tập thể hướng dẩn</td>
						<td align='center' >Ngày BV</td>
						<td align='center' style='width:130px'>Download </td>
					</tr>";
			$classAlt="alt";
			for ($i=0; $i<$n; $i++)
			{
				($classAlt=='alt') ? $classAlt="alt_" : $classAlt="alt";
				if ($rs["HD_PHU"][$i] != "")
					$s = "- ".$rs["HD_CHINH"][$i]."<br>- ".$rs["HD_PHU"][$i];
				else
					$s = "- ".$rs["HD_CHINH"][$i];
				echo "<tr class=' ".$classAlt."'>";
					echo "<td align='left' style='text-transform:uppercase;'>".$rs["HV"][$i]."</td>";
					echo "<td align='left' class=''>".$rs["TEN_LUAN_AN"][$i]."</td>";
					echo "<td align='left' class=''>".$s."</td>";
					echo "<td align='center' class=''>".$rs["NGAY_BVNN"][$i]."</td>";
					if ($rs["FILE_CB_BVNN"][$i] == '')
					{
						echo "<td class=''></td>";
					}
					else
					{
						echo "
						<td class=''>
								 <table >
									<tr class='".$classAlt."'><td class=''> <a style='color:#7ac6ec' href='download/LATS/" .$rs["MA_HOC_VIEN"][$i]. "/" .$rs["FILE_TOM_TAT"][$i]."' >Tóm tắt LATS</a> </td></tr>
									<tr><td > <a style='color:#7ac6ec' href='download/LATS/".$rs["MA_HOC_VIEN"][$i]. "/" .$rs["FILE_TT_LA_ENG"][$i]."' >Thông tin LATS T.Anh </a></td></tr>
									<tr><td class=''> <a style='color:#7ac6ec' href='download/LATS/" .$rs["MA_HOC_VIEN"][$i]. "/" .$rs["FILE_TT_LA_VN"][$i]."' >Thông tin LATS T.Việt</a></td></tr>
									<tr><td class=''> <a style='color:#7ac6ec' href = mailto:sdh@hcmut.edu.vn> Luận Án Tiến sĩ</a> </td></tr>
								</table>
						</td>";							
					}
				//echo "</tr>";
				echo "</tr>";
			}
			echo "</table>";
		}
		oci_free_statement($oci);
	?>
	</div>
</body>
</html>
<script type="text/javascript">

$(function() {
// Assign handlers immediately after making the request,
  // and remember the jqxhr object for this request
  /*
  var jqxhr = $.get("ncs_diem_phuc_tra_ts.php", function(data) {
    $("#content").html(data);
  })
  .success(function() { 
	//alert("second success"); 
  })
  .error(function() { 
	//alert("error"); 
  })
  .complete(function() { 
	//alert("complete"); 
  });
  */
});
</script>