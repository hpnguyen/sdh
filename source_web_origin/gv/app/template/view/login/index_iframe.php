<html>
	<script type="text/javascript" src="<?php echo Helper::getHelper('functions/util')->getGvRootURL() ?>/js/jshash/sha512.js"></script>
	<script type="text/javascript" src="<?php echo Helper::getHelper('functions/util')->getGvRootURL() ?>/js/jshash/forms.js"></script>
	<style type="text/css">
		body{
			<?php echo isset($_GET['w']) ? 'height: '.$_GET['w'].'px;' : '' ?>
			<?php echo isset($_GET['h']) ? 'height: '.$_GET['h'].'px;' : '' ?>
		}
		
		.username {
			background: url("<?php echo Helper::getHelper('functions/util')->getGvRootURL() ?>/icons/icon-username.png") no-repeat scroll 5px 3px rgba(0, 0, 0, 0);
			border: 1px solid #7F9DB9;
			height: 25px;
			padding: 0 0 0 26px;
			width: 160px;
		}
		
		.password {
			background: url("<?php echo Helper::getHelper('functions/util')->getGvRootURL() ?>/icons/icon-password.png") no-repeat scroll 5px 3px rgba(0, 0, 0, 0);
			border: 1px solid #7F9DB9;
			height: 25px;
			padding: 0 0 0 26px;
			width: 160px;
		}
		.field-username, .field-password, .field-submit {
			text-align: right;
			padding: 2px;
		}
		.login-error {
			font-style: italic;
			color: #FF0000;
			font-size: 11px;
		}
	</style>
	<body>
		<div class="login-error">
			<?php echo $error ?>
		</div>
		<form action="<?php echo Helper::getHelper('functions/util')->getModuleActionRouteUrl("login/index/login",true); ?>" method="post" name="login_form">
			<div class="field field-username">
				<input type="text" name="username" class="username" placeholder="tên đăng nhập"/>
			</div>
			<div class="field field-password">
				<input type="password" name="password" id="password" class="password" placeholder="mật khẩu"/>
			</div>
			<div class="field field-submit">
				<input type="button" value="Đăng nhập" onclick="formhash(this.form, this.form.password);" />
			</div>
			
			<input type="hidden" name="loginurl" id="loginurl" value="<?php echo Helper::getHelper('functions/util')->curPageURL(); ?>"/> 
		</form>
	</body>	
</html>