<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Phòng Đào Tạo Sau Đại Học</title>

</head>

<script src="../js/jquery-1.8.0.min.js"></script>
<script src="../js/jquery-ui-1.8.23.custom.min.js"></script> 
<script src="../js/jquery.maskedinput-1.3.min.js"></script> 

<body style="font-family:Arial, Helvetica, sans-serif">
<link href="../css/start/jquery-ui-1.8.18.custom.css" rel="stylesheet" type="text/css"/>
<link href="css/pgs.css" rel="stylesheet" type="text/css"/>
	<div id=content></div>
</body>
</html>
<script type="text/javascript">

$(function() {
// Assign handlers immediately after making the request,
  // and remember the jqxhr object for this request
  var jqxhr = $.get("hv_tracuu_diem_cd.php", function(data) {
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
  
});
</script>