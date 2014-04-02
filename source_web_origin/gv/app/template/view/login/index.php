<?php
Helper::getHelper('functions/userlogin')->sec_session_start(); // Our custom secure way of starting a PHP session.
if (Helper::getHelper('functions/userlogin')->login_check() == true) {
	$logged = 'in';
} else {
	$logged = 'out';
}
?>
<script type="text/javascript" src="<?php echo Helper::getHelper('functions/util')->getGvRootURL() ?>/js/jshash/sha512.js"></script>
<script type="text/javascript" src="<?php echo Helper::getHelper('functions/util')->getGvRootURL() ?>/js/jshash/forms.js"></script>
<?php
if (isset($_GET['error'])) {
    echo '<p class="error">Error Logging In!</p>';
}
?> 
<form action="<?php echo Helper::getHelper('functions/util')->getModuleActionRouteUrl("login/index/login",true); ?>" method="post" name="login_form">                      
	Username: <input type="text" name="username" />
	Password: <input type="password" name="password" id="password"/>
	<input type="button" value="Login" onclick="formhash(this.form, this.form.password);" /> 
</form>
<p>If you don't have a login, please <a href="<?php echo Helper::getHelper('functions/util')->getModuleActionRouteUrl("login/index/register",true); ?>">register</a></p>
<p>If you are done, please <a href="<?php echo Helper::getHelper('functions/util')->getModuleActionRouteUrl("login/index/logout",true); ?>">log out</a>.</p>
<p>You are currently logged <?php echo $logged ?>.</p>
<iframe src="http://www.w3schools.com"></iframe> 
