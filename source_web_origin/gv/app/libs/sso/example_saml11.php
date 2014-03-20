<?php

//
// phpCAS SAML1.1 client
//

// import phpCAS lib
include_once('CAS.php');

 

phpCAS::setDebug();

 

// initialize phpCAS
phpCAS::client(SAML_VERSION_1_1,'sso.hcmut.edu.vn',443,'cas');

 

// no SSL validation for the CAS server
phpCAS::setNoCasServerValidation();

 

// force CAS authentication
phpCAS::forceAuthentication();

 

// at this step, the user has been authenticated by the CAS server
// and the user's login name can be read with phpCAS::getUser().

// logout if desired
if (isset($_REQUEST['logout'])) {
 //phpCAS::logout();
 phpCAS::logoutWithRedirectService('http://sso-test.hcmut.edu.vn/sso-test/logout_success.php');
}

// for this test, simply print that the authentication was successful
?>
<html>
  <head>
    <title>phpCAS simple client</title>
  </head>
  <body>
    <h1>Successfull Authentication!</h1>
    <p>the user's login is <strong><?php echo phpCAS::getUser(); ?></strong>.</p>
    <h3>User Attributes</h3>
      <ul>
	<?php
	  foreach (phpCAS::getAttributes() as $key => $value) {
    	    if (is_array($value)) {
              echo '<li>', $key, ':<ol>';
              foreach ($value as $item) {
                echo '<li><strong>', $item, '</strong></li>';
              }
              echo '</ol></li>';
            } 
	    else {
              echo '<li>', $key, ': <strong>', $value, '</strong></li>';
            }
          }
        ?>
    </ul>
    <p>phpCAS version is <b><?php echo phpCAS::getVersion(); ?></b>.</p>
    <p><a href="?logout=">Logout</a></p>
  </body>
</html>
