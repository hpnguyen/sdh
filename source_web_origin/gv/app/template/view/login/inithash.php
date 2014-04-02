<script type="text/javascript" src="<?php echo Helper::getHelper('functions/util')->baseURL() ?>/js/jquery-1.8.0.min.js"></script>
<script type="text/javascript" src="<?php echo Helper::getHelper('functions/util')->getGvRootURL() ?>/js/jshash/sha512.js"></script>
<script type="text/javascript" src="<?php echo Helper::getHelper('functions/util')->getGvRootURL() ?>/js/jshash/forms.js"></script>

<form id="mainForm" action="<?php echo $urlRelogin ?>" method="post" name="login_form">
	<input type="hidden" id="pH" value="<?php echo $password ?>"/>
	<input type="hidden" id="loginurl" name="loginurl" value="<?php echo $loginurlEncode ?>"/>
</form>
<script type="text/javascript">
$(document).ready(function() {
	var myURL = '<?php echo $url ?>&p=' + hex_sha512($('#pH').val());
	$('#pH').val('');
	
	$.ajax({
		type: "GET",
		url: myURL,
		success:function(result){
		},
		error: function (xhr,status,error){
		},
		complete: function(xhr,status){
			$('#mainForm').submit();
		}
	});
});	
</script>