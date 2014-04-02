<?php if($hashpassword == null){?>
<script type="text/javascript" src="<?php echo Helper::getHelper('functions/util')->baseURL() ?>/js/jquery-1.8.0.min.js"></script>
<script type="text/javascript" src="<?php echo Helper::getHelper('functions/util')->getGvRootURL() ?>/js/jshash/sha512.js"></script>
<script type="text/javascript" src="<?php echo Helper::getHelper('functions/util')->getGvRootURL() ?>/js/jshash/forms.js"></script>
<form id="mainForm" action="<?php echo Helper::getHelper('functions/util')->getModuleActionRouteUrl("login/index/gethashpass?u=".$username,true); ?>" method="post">
	<input type="hidden" id="inputH" name="h" value="" />
	<input type="hidden" id="inputU" name="u" value="<?php echo $_GET['u'] ?>" />
</form>
<script type="text/javascript">
$(document).ready(function() {
	var ret;
	ret = hex_sha512('<?php echo $password ?>');
	$('#inputH').val(ret);
	$('#mainForm').submit();
});	
</script>	
<?php } ?>