<script type="text/javascript" src="<?php echo Helper::getHelper('functions/util')->baseURL() ?>/js/jquery-1.8.0.min.js"></script>
<script type="text/javascript" src="<?php echo Helper::getHelper('functions/util')->getGvRootURL() ?>/js/jshash/sha512.js"></script>
<script type="text/javascript" src="<?php echo Helper::getHelper('functions/util')->getGvRootURL() ?>/js/jshash/forms.js"></script>
<form action="<?php echo Helper::getHelper('functions/util')->getModuleActionRouteUrl("login/index/login",true); ?>" method="post" name="login_form">                      
	<input type="hidden" name="email" value="<?php $username ?>" />
	<input type="hidden" name="password" id="password" value="<?php $password ?>"/>
	<input style="display: none" id="buttonSubmit" type="button" value="Login" onclick="formhashadd(this.form, this.form.password);" /> 
</form>
<script type="text/javascript">
$(document).ready(function() {
	$('#buttonSubmit').trigger('click');
});	
</script>