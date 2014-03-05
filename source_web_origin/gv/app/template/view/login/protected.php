<?php
Helper::getHelper("functions/userlogin")->sec_session_start();
?>
<?php if (Helper::getHelper("functions/userlogin")->login_check() == true) : ?>
	<p>Welcome <?php echo htmlentities($_SESSION['username']); ?>!</p>
	<p>
		This is an example protected page.  To access this page, users 
		must be logged in.  At some stage, we'll also check the role of 
		the user, so pages will be able to determine the type of user 
		authorised to access the page.
	</p>
	<p>Return to <a href="<?php echo Helper::getHelper('functions/util')->getModuleActionRouteUrl("login/index/index",true); ?>">login page</a></p>
<?php else : ?>
	<p>
		<span class="error">You are not authorized to access this page.</span> 
		Please <a href="<?php echo Helper::getHelper('functions/util')->getModuleActionRouteUrl("login/index/index",true); ?>">login</a>.
	</p>
<?php endif; ?>