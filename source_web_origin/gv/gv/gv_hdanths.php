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
if (!allowPermisstion(base64_decode($_SESSION['uidloginPortal']), '001', $db_conn))
{
	die('Truy cập bất hợp pháp'); 
}

$macb = $_SESSION['macb'];

$search = array("'","\"");
$replace = array("\\'","&quot;");
?>
  
<div id='huongdanthacsidiv'>
		
		<table width="100%" height="20" border="0" align="center" cellpadding="5" cellspacing="0" class="ui-widget ui-widget-content ui-corner-top tableData">
          <tr class="ui-widget-header heading" height="20">
            <td width="3%" class=" ui-corner-tl">&nbsp;</td>
            <td width="21%" ><em>Học viên </em></td>
            <td width="49%" ><em>Đề tài </em></td>
            <td width="6%" ><em>Năm</em></td>
            <td width="12%" align="center" ><em>HD
              Chính</em></td>
            <td width="9%" align="center"  class="ui-corner-tr"><em>HD
Phụ</em></td>
          </tr>
		  
		  <?php
		  	$sqlstr="select l.ma_hoc_vien, h.ho || ' ' || h.ten ho_ten, l.ten_de_tai, to_char(l.dot_nhan_lv, 'yyyy') nam, decode(huong_dan_chinh, '".$macb. "', 'x', ' ') hd_chinh, decode(huong_dan_phu, '".$macb. "', 'x', ' ') hd_phu
from luan_van_thac_sy l, hoc_vien h
where diem_luan_van(l.ma_hoc_vien)>=5 
and (huong_dan_chinh = '$macb' or huong_dan_phu = '$macb')
and h.ma_hoc_vien = l.ma_hoc_vien 
and dot_nhan_lv = dot_nhan_lv(h.ma_hoc_vien) 
order by dot_nhan_lv desc"; 

		  	$stmt = oci_parse($db_conn, $sqlstr);
  			oci_execute($stmt);
  			$n = oci_fetch_all($stmt, $resDM);
			oci_free_statement($stmt);
			
			$classAlt="alt";
			for ($i = 0; $i < $n; $i++)
  			{			
				($classAlt=="alt") ? $classAlt="alt_" : $classAlt="alt";
				echo "<tr class=\"fontcontent ".$classAlt."\" align=\"left\" >";
				   echo "<td valign=\"top\">" .($i+1).".</td>";
				   echo "<td valign=\"top\">" .$resDM["HO_TEN"][$i]. "</td>";
				   echo "<td >" .$resDM["TEN_DE_TAI"][$i]. "</td>";
				   echo "<td align=\"center\" valign=\"top\">" .$resDM["NAM"][$i]. "</td>";
				    echo "<td align=\"center\" valign=\"top\"> <strong>".$resDM["HD_CHINH"][$i]."</strong></td>";
				   echo "<td align=\"center\" valign=\"top\"><strong>".$resDM["HD_PHU"][$i]."</strong></td>";
				echo "</tr>";
			}
		  
		  ?>
		 
        </table>
</div>



<?php 
if (isset ($db_conn))
	oci_close($db_conn);
?>