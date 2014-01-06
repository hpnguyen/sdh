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
?>

<div id="detaidiv">     
	<form id="form_lvnc" method="post" action="" >
          <div id="formthemdetaidiv" title="Hướng, đề tài nghiên cứu">
                           
                     <table width="100%" border="0" cellspacing="2" cellpadding="5" >
                      <tr align="left" class="heading">
                        <td width="13%"><label for="dtNam">Năm </label></td>
                        <td ><label for="dtHuongDeTai">Hướng nghiên cứu, đề tài</label></td>
                        </tr>
                      <tr align="left" valign="top">
                        <td >
                          
                          <input name="dtNam" type="text"  id="dtNam" size="6" maxlength="4" class="text ui-widget-content ui-corner-all tableData"/>
                          
                      
                          <input name="madtedit" type="hidden" id="madtedit" />
                        </td>
                        <td width="87%"><textarea class="text ui-widget-content ui-corner-all tableData" name="dtHuongDeTai" id="dtHuongDeTai" cols="45" rows="10" ></textarea>
          
                        </td>
                        </tr>

                      
                    </table>
					<div align="center" id="tipDT" class="ui-corner-all validateTips ui-widget-header"></div>
       
          </div>
          
  
    <table border="0" cellspacing="0" cellpadding="0" width="100%">
		<tr>
			<td align=left>
				<div style="margin-left:5px;font-weight:bold">Hướng nghiên cứu</div>
			</td>
			<td align="right">
				<div style="margin-bottom:10px;">
					<a name="taodetaimoi" id="taodetaimoi">&nbsp;Thêm hướng đề tài</a>
				  &nbsp;&nbsp;
				  <a id="btnXoaDT" name="btnXoaDT">&nbsp;Xóa</a>
				</div>
			</td>
        </tr>
    </table>
    
    
    <table id="tabledetai" width="100%" border="0" align="center" cellpadding="5" cellspacing="0" class="ui-widget ui-widget-content ui-corner-top tableData" height="20">
      <thead>
        <tr class="ui-widget-header heading" >
          <td width="30" class=" ui-corner-tl"></td>
          <td width="51" align="left" valign="middle" ><em>Năm</em></td>
          <td width="574" align="left" valign="middle" ><em>Hướng nghiên cứu</em></td>
          <td width="36" valign="middle" >&nbsp;</td>
          <td width="39" align="left" valign="middle" class=" ui-corner-tr">&nbsp;</td>
          </tr>
        </thead>
      <tbody>
        <?php $sqlstr="select * from huong_de_tai where ma_can_bo = '".$macb. "' order by nam desc, ten_de_tai"; 
		  	$stmt = oci_parse($db_conn, $sqlstr);
  			oci_execute($stmt);
  			$n = oci_fetch_all($stmt, $resDM);
			oci_free_statement($stmt);
			//str_replace("'", "''",$_POST['dtHuongDeTai']);
			
			
			$classAlt="alt";
			for ($i = 0; $i < $n; $i++)
  			{
				($classAlt=="alt") ? $classAlt="alt_" : $classAlt="alt";
				echo "<tr align=\"left\" valign=\"top\" class=\"".$classAlt."\">"."<input name=\"MaDeTai".$i."\" type=\"hidden\" id=\"MaDeTai".$i."\" value=\"".base64_encode($resDM["MA_DE_TAI"][$i])."\"/>";
				echo "<td  class=\"fontcontent\" valign=\"top\" >" .($i+1).".</td>";
				echo "<td class=\"fontcontent\" valign=\"top\">".$resDM["NAM"][$i]."</td>";
				echo "<td class=\"fontcontent\" >".$resDM["TEN_DE_TAI"][$i]."</td>";
				echo "<td class=\"fontcontent ahref\" onclick=\"getDeTai(". ($i+1) .", '".base64_encode($resDM["MA_DE_TAI"][$i])."');\">Sửa</td>";
				echo "<td><input type=\"checkbox\" id=\"dtchk".$i."\" name=\"dtchk".$i. "\" value=\"1\" /></td>";
				echo "</tr>";
			}
		  ?>
        </tbody>
      
      </table>
		
    </form>
	<input name="detai_act" id="detai_act" type="hidden" value="" />
  </div>   <!-- end of "detaidiv" -->     

<script type="text/javascript">
//jQuery.ajax
//$(document).ready(function(){

function getDeTai(index, mdt){
	//alert(index);
	$('#detai_act').val('edit');
	
	var table=document.getElementById("tabledetai");
	
	//alert(table.rows[index].cells[2].innerHTML);
	
	$("#dtNam").val(table.rows[index].cells[1].innerHTML); 
	
	//document.getElementById("dtHuongDeTai").value = table.rows[index].cells[2].innerHTML;
	$("#dtHuongDeTai").val(table.rows[index].cells[2].innerHTML);
	
	$("#madtedit").val(mdt);
	
	$( "#formthemdetaidiv" ).dialog('open');
}

$(function(){

  // delete btn
 $( "#btnXoaDT" ).button({ icons: {primary:'ui-icon ui-icon-trash'} });
 
 // create new
 $( "#taodetaimoi" ).button({ icons: {primary:'ui-icon ui-icon-document'} });
 
	// Check validate fields TTGV
// Check validate fields Huong De Tai
var jdtNam 			= $("#dtNam"),
	jdtHuongDeTai	= $("#dtHuongDeTai"),
	jmadtedit		= $("#madtedit"),
	allFieldsDT 	= $( [] ).add(jdtNam).add(jdtHuongDeTai).add(jmadtedit),
	detai_tips 		= $("#tipDT");
		
	// 
	function detai_updateTips( t ) {
		detai_tips
					.text( t )
					.addClass( "ui-state-highlight" );
		setTimeout(function() {
			detai_tips.removeClass( "ui-state-highlight", 1500 );
		}, 1000 );
	}
	
	
	function detai_checkLength( o, n, min, max) {
		if (min==0 && (o.val().length==0))
		{	
			o.addClass( "ui-state-error" );
			o.focus();	
			detai_updateTips( "Thông tin " + n + " không được phép để trống.");
			
			return false;
		}else if (min==max && o.val().length<min){
			o.addClass( "ui-state-error" );
			o.focus();	
			detai_updateTips( "Thông tin " + n + " phải đủ " + min + " ký tự.");
		}else if ( o.val().length > max || o.val().length < min ) {
			o.addClass( "ui-state-error" );
			o.focus();		
			detai_updateTips( "Chiều dài của " + n + " từ " +
						min + " đến " + max + " ký tự.");
			return false;
		} else {
			return true;
		}
	}
	
	// Check Regexp
	function detai_checkRegexp( o, regexp, n ) {
		//alert('a');
		if ( o.val()!='' && !( regexp.test( o.val() ) ) ) {
			o.addClass( "ui-state-error" );
			o.focus();
			detai_updateTips( n );
			return false;
		} else {
			return true;
		
		}
	}
// End of check validate
	
	
	
	// Post du lieu cho de tai
	$("#taodetaimoi").click(function() {
		$("#detai_act").val("add");
		$("#formthemdetaidiv").dialog( "open" );
	});
	
	$("#btnXoaDT").click(function(){
		dataString = $("#form_lvnc").serialize()+'&cat=detai&act=del';
		dataString +='&hisid=<?php echo $_REQUEST["hisid"];?>';
		//alert(dataString);
		
		$.post("gv/processgv.php", dataString,
		function(data){
			//alert('aaaaaa');
			$("#tabledetai tbody").html(data);
		}, "html");
	});	// end $("#btnXoaDT").click(function()
	
	$( "#formthemdetaidiv" ).dialog({
			autoOpen: false,
			height: 360,
			width: 370,
			modal: true,
			buttons: {
				"Ok": function() {
					var bValid = true;
					
					allFieldsDT.removeClass( "ui-state-error" );
					bValid = bValid && detai_checkLength( jdtNam, "\"Năm thực hiện\"", 4, 4);
					bValid = bValid && detai_checkRegexp( jdtNam,/^[0-9]{4,4}$/i, "Thông tin năm phải là Số");
					bValid = bValid && detai_checkLength( jdtHuongDeTai, "\"Hướng nghiên cứu\"", 0, 1000);
					if (bValid){
						
						datastring = $("#form_lvnc").serialize()
						+ '&cat=detai&act='+$('#detai_act').val()+'&'
						+ allFieldsDT.serialize();
						datastring +='&hisid=<?php echo $_REQUEST["hisid"];?>';
 						
						//alert(datastring);
						
						$.post("gv/processgv.php", datastring ,
						function(data){
							//alert(data);
							$("#tabledetai tbody").html(data);						
						}, "html");
					}

					if ( bValid ) {
						$( this ).dialog( "close" );
					}
				},
				Cancel: function() {
					$( this ).dialog( "close" );
				}
			},
			close: function() {
				allFieldsDT.val( "" ).removeClass( "ui-state-error" );
			}
		});

});

</script>

<?php 

if (isset ($db_conn))
	oci_close($db_conn);
?>