<script type="text/javascript" src="<?php echo Helper::getHelper('functions/util')->baseURL() ?>/js/jquery-1.8.0.min.js"></script>
<script type="text/javascript" src="<?php echo Helper::getHelper('functions/util')->getGvRootURL() ?>/js/jshash/sha512.js"></script>
<script type="text/javascript" src="<?php echo Helper::getHelper('functions/util')->getGvRootURL() ?>/js/jshash/forms.js"></script>
<div id="mainForm"></div>
<script type="text/javascript">
$(document).ready(function() {
	$('#mainForm').html(hex_sha512('<?php echo $password ?>'));
});	
</script>