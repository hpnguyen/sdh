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

 

// force CAS authentication
phpCAS::forceAuthentication();

 

// at this step, the user has been authenticated by the CAS server
// and the user's login name can be read with phpCAS::getUser().

// logout if desired
if (isset($_REQUEST['logout'])) {
phpCAS::logoutWithRedirectService('http://sso-test.hcmut.edu.vn/');
// phpCAS::logoutWithUrl('sso.hcmut.edu.vn');
// phpCAS::logoutWithRedirectServiceAndUrl('sso.hcmut.edu.vn','sso-test.hcmut.edu.vn/example.php');
}

// for this test, simply print that the authentication was successful
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
