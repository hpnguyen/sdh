<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Phong Dao Tao Sau Dai Hoc</title>

</head>

<link href="css/start/jquery-ui-1.8.18.custom.css" rel="stylesheet" type="text/css"/>
<link href="css/pgs.css" rel="stylesheet" type="text/css"/>

<script src="js/jquery-1.7.1.min.js"></script>
<script src="js/jquery-ui-1.8.18.custom.min.js"></script> 

<?php
	
?>

<body style="font-family:Arial, Helvetica, sans-serif">
	<div id=content></div>
</body>
</html>
<script type="text/javascript">
var schedules = new Array();

function addMhTKB(a, b)
{	
	//alert('a[0]=' + a[0] + ', a[1]=' + a[1] + ', b[0]=' + b[0]+ ', b[1]=' + b[1]);
}

addMhTKB([1,2], [3,4]);


$(function() {
// Assign handlers immediately after making the request,
  // and remember the jqxhr object for this request
  var jqxhr = $.get("hv_tracuu_ctdt.php", function(data) {
    //$("#content").html(data);
	alert("first success"); 
  })
  .success(function() { alert("second success"); })
  .error(function() { alert("error"); })
  .complete(function(data) { $("#content").html(data); });
  
});
</script>