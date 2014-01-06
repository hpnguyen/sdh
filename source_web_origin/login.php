<?php
//$usr=trim(mysql_real_escape_string($_POST["usrname"]));
//$pass=trim(mysql_real_escape_string($_POST["pass"]));

$usr=trim($_POST["usrname"]);
$pass=trim($_POST["pass"]);

if (is_numeric($usr))
{
	$action = "hv/login.php";
}
else
{
	$action = "gv/login.php";
}
?>
<div align=center style="margin:10px 0 0 0">
	Đang tải chương trình ... vui lòng chờ vài giây.
</div>
<form id="loginform" action="<?php echo $action; ?>" method="POST">
	<input name="usrname"  type="hidden" value="<?php echo $usr; ?>" />
	<input name="pass" type="hidden" value="<?php echo $pass; ?>" />
</form>

<script type="text/javascript">
	document.forms["loginform"].submit();
</script>
