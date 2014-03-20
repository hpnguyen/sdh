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

if (! phpCAS::isAuthenticated()){
	//phpCAS::forceAuthentication();
	echo "chua login";
}else{
	?>
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
    <?php
}
?>
