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
  
<div id='huongdantiensidiv'>
		<table width="100%" height="20" border="0" align="center" cellpadding="5" cellspacing="0" class="ui-widget ui-widget-content ui-corner-top tableData">
          <tr class="ui-widget-header heading">
            <td width="1%" class="ui-corner-tl" >&nbsp;</td>
            <td width="22%" ><em>Nghiên cứu sinh </em></td>
            <td width="40%" ><em>Đề tài </em></td>
            <td width="11%" ><em>Thành công</em></td>
            <td width="6%" ><em>Năm</em></td>
            <td width="11%" align="center" ><em>HD
              Chính</em></td>
            <td width="9%" align="center" class="ui-corner-tr"><em>HD
              Phụ</em></td>
          </tr>
		  
		  
		  <?php
		  	$sqlstr="select l.ma_hoc_vien, h.ho || ' ' || h.ten ho_ten, l.ten_luan_an, h.khoa, decode(l.huong_dan_1, '".$macb. "', 'x', ' ') huong_dan_chinh, decode(l.huong_dan_1, '".$macb. "', ' ', 'x') huong_dan_phu, decode(l.dot_cap_bang, '', '', 'x') thanh_cong from  hoc_vien h, luan_an_tien_sy l
where (l.huong_dan_1 = '".$macb. "' or l.huong_dan_2 = '" .$macb. "' or l.huong_dan_3 = '" .$macb. "') and h.ma_hoc_vien = l.ma_hoc_vien
order by khoa_trung_tuyen desc"; 
			
			$stmt = oci_parse($db_conn, $sqlstr);
			oci_execute($stmt);
			$n = oci_fetch_all($stmt, $resDM);
			oci_free_statement($stmt);
			
			$classAlt="alt";
			for ($i = 0; $i < $n; $i++)
  			{			
				($classAlt=="alt") ? $classAlt="alt_" : $classAlt="alt";
				echo "<tr class=\"fontcontent ".$classAlt."\" align=\"left\" valign=\"top\">";
			   echo "<td >" .($i+1).".</td>";
			   echo "<td >" .$resDM["HO_TEN"][$i]. "</td>";
			   echo "<td >" .$resDM["TEN_LUAN_AN"][$i]. "</td>";
			   echo "<td align=\"center\"><strong>" .$resDM["THANH_CONG"][$i]. "</strong></td>";
			   echo "<td align=\"center\" >" .$resDM["KHOA"][$i]. "</td>";
			   echo "<td align=\"center\" ><strong>".$resDM["HUONG_DAN_CHINH"][$i]."</strong></td>";
			   echo "<td align=\"center\" ><strong>".$resDM["HUONG_DAN_PHU"][$i]."</strong></td>";
				echo "</tr>";
			}
		  
		  ?>
        </table>
</div>	

<?php 
if (isset ($db_conn))
	oci_close($db_conn);
?>