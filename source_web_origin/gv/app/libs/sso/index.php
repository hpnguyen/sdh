<?php

//
// phpCAS simple client
//

// import phpCAS lib
include_once('CAS.php');

 

phpCAS::setDebug();

 

// initialize phpCAS
phpCAS::client(CAS_VERSION_2_0,'sso.hcmut.edu.vn',443,'cas');

 

// no SSL validation for the CAS server
phpCAS::setNoCasServerValidation();

var_dump(phpCAS::isAuthenticated());
if (! phpCAS::isAuthenticated()){
	echo "chua login ";
	phpCAS::forceAuthentication();
	die;
}
// force CAS authentication
//phpCAS::forceAuthentication();

if (isset($_REQUEST['logout'])) {
	//set POST variables
	$url ='http://grad-test.hcmut.edu.vn/sso-test/call_cas_logout.php';
	$url ='https://sso.hcmut.edu.vn/cas/logout?service=http%3A%2F%2Fgrad-test.hcmut.edu.vn%2Fsso-test%2Flogout_success.php';
	$service = 'http://grad-test.hcmut.edu.vn/sso-test/logout_success.php';
	phpCAS::logoutWithRedirectService($service);
	echo "Log out thanh cong";
	die;
}
?>
<html>
  <head>
    <title>phpCAS simple client</title>
  </head>
  <body>
    <h1>Successfull Authentication!</h1>
    <p>the user's login is <b><?php echo phpCAS::getUser(); ?></b>.</p>
    <p>phpCAS version is <b><?php echo phpCAS::getVersion(); ?></b>.</p>
    <p><a href="?logout=">Logout</a></p>
  </body>
</html>
