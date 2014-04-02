<script type="text/javascript" src="<?php echo Helper::getHelper('functions/util')->baseURL() ?>/js/jquery-1.8.0.min.js"></script>
<script type="text/javascript" src="<?php echo Helper::getHelper('functions/util')->getGvRootURL() ?>/js/jshash/sha512.js"></script>
<script type="text/javascript" src="<?php echo Helper::getHelper('functions/util')->getGvRootURL() ?>/js/jshash/forms.js"></script>
<form action="<?php echo Helper::getHelper('functions/util')->getModuleActionRouteUrl("login/index/register",true); ?>?gs=1" method="post" name="registration_form">
	<input type='hidden' name='username' id='username' value='<?php echo $username ?>' />
	<input type="hidden" name="email" id="email" value='<?php echo $email ?>' />
	<input type="hidden" name="password" id="password" value='<?php echo $password ?>'/>
	<input type="hidden" name="confirmpwd" id="confirmpwd" value='<?php echo $password ?>' />
	<input style="display: none" id="buttonSubmit" type="button" value="Register" onclick="return regformhashadd(this.form, this.form.username, this.form.email, this.form.password, this.form.confirmpwd);" />
	<input type="hidden" name="bool_register" value="1" id="bool_register" />
</form>
<script type="text/javascript">
$(document).ready(function() {
	$('#buttonSubmit').trigger('click');
});	
</script>