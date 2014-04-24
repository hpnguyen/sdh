<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Phòng Đào Tạo Sau Đại Học</title>

</head>

<link href="css/start/jquery-ui-1.8.18.custom.css" rel="stylesheet" type="text/css"/>
<link href="css/pgs.css" rel="stylesheet" type="text/css"/>
<link href="css/bootstrap.min.css" rel="stylesheet" type="text/css"/>
  
<script src="js/jquery-1.7.1.min.js"></script>
<script src="js/jquery-ui-1.8.18.custom.min.js"></script> 
<script src="js/jquery.cookie.js"></script>
<script src="js/jquery.placeholder-1.1.9.js"></script>
<script src="js/bootstrap.min.js"></script>
  
<script src="js/pgs.js"></script>
<style>
	
	#tabs {}
	#tabs li .ui-icon-close { float: left; margin: 0.4em 0.2em 0 0; cursor: pointer; }

</style>

<body style="font-family:Arial, Helvetica, sans-serif">

<div align="left" style="margin:0 center;">
<form enctype="multipart/form-data" action="test_upload_process.php?hisid=<?php echo $_REQUEST["hisid"]; ?>" method="POST" target=workFrame>
<table align=center>
	<tr>
		<td>Send this file:</td>
		<td>
			<!-- MAX_FILE_SIZE must precede the file input field -->
			<input type="hidden" name="MAX_FILE_SIZE" value="300000" />
			<!-- Name of input element determines name in $_FILES array -->
			<input id=userfile name=userfile type=file size=30 placeholder="Chọn hình làm kỷ yếu" class="text ui-widget-content ui-corner-all tableData"/> 
	   </td>
	</tr>
	<tr>
		<td></td>
		<td>
			<a id="hinhkyeu_btn_upload" style='font-size:80%'>&nbsp;Tải lên</a>
			<input type="submit" value="Send File" />
		</td>
	</tr>
</table>
</form>
<iframe id=workFrame name=workFrame src="about:blank" style="display:none;"></iframe>
</div>

<div id="hinhkyeu_chitiet" style="margin-top:5px;" align=center>
</div>

</body>
</html>

<script type="text/javascript">

$(function() {
	$( "#hinhkyeu_btn_upload" ).button({ icons: {primary:'ui-icon ui-icon-disk'} });
	//tracuudiem_updateDiem();

	$("#hinhkyeu_btn_upload").click(function(){
		//writeConsole("<div style='font-size:13px; font-weight:bold;' align=left>ĐẠI HỌC BÁCH KHOA - TP.HCM<br/>PHÒNG ĐÀO TẠO SAU ĐẠI HỌC<br/> &nbsp;</div>" + $("#tracuudiem_chitiet").html() + "<div style='clear:both;'>&nbsp;<br/><a href='JavaScript:window.print();'>In trang này</a></div>", 800, 450);
		
	});	// end $("#lichthi_canhan_btn_printpreview")
	
	$('form').on('submit', function () {
        //check if the form submission is valid, if so just let it submit
        //otherwise you could call `return false;` to stop the submission
		//alert('form submit');
    });

    $('#workFrame').on('load', function () {

        //get the response from the server
        var response = $(this).contents().find('body').html();
		$("#hinhkyeu_chitiet").html(response);
        //you can now access the server response in the `response` variable
        //this is the same as the success callback for a jQuery AJAX request
    });
	
});
</script>

<?php 
if (isset ($db_conn))
	oci_close($db_conn);
?>