<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
</head>

<body style="font-family:Arial, Helvetica, sans-serif">
<?php
	if (isset($_REQUEST['l']))
	{
		$l = $_REQUEST['l'];
		//$w = $_REQUEST['w'];
		//$h = $_REQUEST['h'];
		echo "
		<div>
		<div align=center style='margin:5px 0 15px 0;'><b>Để In và Lưu đề cương vui lòng bấm nút <img src='icons/gview.png' border=0></b></div>
		<div align=center><iframe src='http://docs.google.com/gview?url=$l&embedded=true' style='width:800px; height:700px;' frameborder='0'></iframe></div>
		</div>
		";
	}
?>
</body>

</html>