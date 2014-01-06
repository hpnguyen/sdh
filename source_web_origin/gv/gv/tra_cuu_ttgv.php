<script type="text/javascript">
if (window==window.top) { /* I'm not in a frame! */
	window.location.href = "http://www.pgs.hcmut.edu.vn";
}
</script>
<?php
	//include "libs/connect.php";
	include "../libs/connect.php";
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title></title>
</head>

<script src="//ajax.googleapis.com/ajax/libs/jquery/1.9.0/jquery.min.js"></script>

<script src="../../js/ui-1.9.2/js/jquery-ui-1.9.2.custom.min.js"></script>

<link href="../../css/start/jquery-ui-1.10.0.custom.min.css" rel="stylesheet" type="text/css"/>
<link href="../css/pgs.css" rel="stylesheet" type="text/css"/>
	
<body style="font-family:Arial, Helvetica, sans-serif; font-size:70%">
	
		<div style="float:left">
			<div style="margin-bottom:5px; font-weight:bold;color:#0083C4; font-size: 12px">
			Khoa - Bộ môn đào tạo
			</div>
			<ul id="menu">
				<?php
				$sqlstr="select viet0dau_name(k.ten_khoa) ten_k_khong_dau, viet0dau_name(b.ten_bo_mon) ten_bm_khong_dau, 
						k.ma_khoa, k.ten_khoa, b.ma_bo_mon, b.ten_bo_mon
						from khoa k, bo_mon b
						where k.loai = 'K' and k.ma_khoa = b.ma_khoa
						and b.ma_bo_mon in 
						(select ma_bo_mon
						from can_bo_giang_day c
						where c.fk_loai_can_bo = '00')
						order by ten_k_khong_dau, ten_bm_khong_dau";
				$stmt = oci_parse($db_conn, $sqlstr);
				oci_execute($stmt);
				$n = oci_fetch_all($stmt, $resDM);
				oci_free_statement($stmt);
				
				$khoa_tmp = '';
				for ($i = 0; $i < $n; $i++)
				{
					if ($khoa_tmp != $resDM["TEN_KHOA"][$i])
					{
						if ($khoa_tmp != '')
						echo "
								</ul>
							</li>";
						
						echo "
							<li>
								<a href='#'>{$resDM["TEN_KHOA"][$i]}</a>
								<ul>
									<li><a href='#' onClick='getDSGV(\"{$resDM["MA_BO_MON"][$i]}\"); $(\"#tenbomon\").html(\"Bộ môn {$resDM["TEN_BO_MON"][$i]}\");'>{$resDM["TEN_BO_MON"][$i]}</a></li>";
					}
					else
					{
						echo "		<li ><a href='#' onClick='getDSGV(\"{$resDM["MA_BO_MON"][$i]}\"); $(\"#tenbomon\").html(\"Bộ môn {$resDM["TEN_BO_MON"][$i]}\");'>{$resDM["TEN_BO_MON"][$i]}</a></li>";
					}
					
					$khoa_tmp = $resDM["TEN_KHOA"][$i];
				}
				echo "
								</ul>
							</li>";
				?>
			</ul>
			
			<div style="margin-top:5px;" class=text id=dsgvleft>
				
			</div>
		</div>
		<div id=dsgiangvien align=left style="float:left;width:350px; margin-left: 5px">
			<div style="margin-bottom:5px;font-weight:bold;color:#0083C4; font-size: 12px" id=tenbomon></div>
			<div style="margin-bottom:5px;font-weight:bold">
			Danh sách Giảng viên
			</div>
			<div class="text" >
				<table width="100%" id="tableDSGV" align="center" border="0" cellspacing="0" cellpadding="5" class="ui-widget ui-widget-content ui-corner-top tableData" height="20">
					<thead>
					  <tr class="ui-widget-header heading" >
						<td class=" ui-corner-tl" style="width:25px" align=left></td>
						
						<td class=" ui-corner-tr" align=left><em>Họ và tên giảng viên</em></td>
					  </tr>
					</thead>
					<tbody>
					</tbody>
				</table>
			</div>
			
		</div>
		<div style="clear:both;"></div>
	<div id=content>
	</div>
	
	<div id="gv_index_dialog_msgbox" title="Phòng Đào Tạo SDH - ĐHBK TP.HCM">
		<span id="gv_index_dialog_msgbox_msg"></span>
	</div>
</body>
</html>

<style>
	.ui-menu { width: 350px; }
	.ahref1 {
		cursor:pointer;
		color:#115599;
		font-size:12px;
	}
</style>
	
<script type="text/javascript">
//if (window!=window.top) { /* I'm in a frame! */ }

function gv_open_msg_box(pmsg, pwidth, pheight)
{
	//ptype = ptype || "info";
	pwidth = pwidth || 280;
	pheight = pheight || 180;

	$("#gv_index_dialog_msgbox").dialog("option", "height", pheight);
	$("#gv_index_dialog_msgbox").dialog("option", "width", pwidth);
	$("#gv_index_dialog_msgbox_msg").html(pmsg);
	$("#gv_index_dialog_msgbox").dialog("open");
}

function tracuuttgv_writeConsole(content) 
{
	a=window.open('','myconsole',
	'width=800,height=450'
	+',menubar=0'
	+',toolbar=0'
	+',status=0'
	+',scrollbars=1'
	+',resizable=1')
	a.document.writeln(
	'<html><head><title>Phong Dao Tao SDH - DHBK</title></head>'
	+'<body bgcolor=white onLoad="self.focus()">'
	+content
	+'</body></html>'
	)
	a.document.close()
}

function getDSGV(p_ma_bo_mon)
{
	$("#dsgvleft").html('');
	$("#tableDSGV tbody").html('<tr><td align=center><img scr="../images/ajax-loader.gif"></td></tr>');
	
	xreq = $.ajax({
	  type: 'POST', dataType: "html",
	  url: 'tra_cuu_ttgv_process.php?w=bm_dsgv_1'+'&m=' + p_ma_bo_mon,
	  success: function(data1) {
		$("#tableDSGV tbody").html(data1);
		
		$.ajax({
		  type: 'POST', dataType: "html",
		  url: 'tra_cuu_ttgv_process.php?w=bm_dsgv_2'+'&m=' + p_ma_bo_mon,
		  success: function(data2) {
			$("#dsgvleft").html(data2);
		  },
		  error: function(xhr, ajaxOptions, thrownError) {
		  },
		  cache: false
		});
		
	  },
	  error: function(xhr, ajaxOptions, thrownError) {
	  },
	  cache: false
	});

	//getDSGV2(p_ma_bo_mon);
}

function getTTGV(p_ma)
{
	xreq = $.ajax({
	  type: 'POST', dataType: "html",
	  url: 'tra_cuu_ttgv_process.php?w=mcb_ttgv'+'&m=' + p_ma,
	  success: function(data) {
		//tracuuttgv_writeConsole(data);
		gv_open_msg_box(data, 730, 500);
	  },
	  error: function(xhr, ajaxOptions, thrownError) { }
	});
}

$(function() {
	$( "#menu" ).menu();
  
	$( "#gv_index_dialog_msgbox" ).dialog({
			resizable: false,
			autoOpen: false,
			width:700, height:500,
			modal: true
	});
	
});
</script>



<?php 
if (isset ($db_conn))
	oci_close($db_conn);
?>