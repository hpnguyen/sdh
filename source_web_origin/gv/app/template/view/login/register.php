<script type="text/javascript" src="<?php echo Helper::getHelper('functions/util')->getGvRootURL() ?>/js/jshash/sha512.js"></script>
<script type="text/javascript" src="<?php echo Helper::getHelper('functions/util')->getGvRootURL() ?>/js/jshash/forms.js"></script>
<script type="text/javascript">
	function formhashadd(form, password) {
		// Create a new element input, this will be our hashed password field.
		var p = document.createElement("input");
	
		// Add the new element to our form.
		form.appendChild(p);
		p.name = "p";
		p.type = "hidden";
		p.value = password.value;
	
		// Make sure the plaintext password doesn't get sent.
		password.value = "";
	
		// Finally submit the form.
		form.submit();
	}
</script>
<!-- Registration form to be output if the POST variables are not
set or if the registration script caused an error. -->
<h1>Register with us</h1>
<?php
if (!empty($error_msg)) {
	echo $error_msg;
}
?>
<ul>
	<li>
		Usernames may contain only digits, upper and lower case letters and underscores
	</li>
	<li>
		Emails must have a valid email format
	</li>
	<li>
		Passwords must be at least 6 characters long
	</li>
	<li>
		Passwords must contain
		<ul>
			<li>
				At least one upper case letter (A..Z)
			</li>
			<li>
				At least one lower case letter (a..z)
			</li>
			<li>
				At least one number (0..9)
			</li>
		</ul>
	</li>
	<li>
		Your password and confirmation must match exactly
	</li>
</ul>
<form action="<?php echo Helper::getHelper('functions/util')->getModuleActionRouteUrl("login/index/register",true); ?>" method="post" name="registration_form">
	Username: <input type='text' name='username' id='username' /> <br>
	Email: <input type="text" name="email" id="email" /> <br>
	Password: <input type="password" name="password" id="password"/> <br>
	Confirm password: <input type="password" name="confirmpwd" id="confirmpwd" /> <br>
	<input type="button" value="Register" onclick="return regformhash(this.form, this.form.username, this.form.email, this.form.password, this.form.confirmpwd);" />
	<input type="hidden" name="bool_register" value="1" id="bool_register" />
</form>
<p>
	Return to the <a href="<?php echo Helper::getHelper('functions/util')->getModuleActionRouteUrl("login/index/index",true); ?>">login page</a>.
</p>