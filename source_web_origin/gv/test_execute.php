<script type="text/javascript" src="http://sdh.localhost.com/js/jquery-1.8.0.min.js"></script>
<script type="text/javascript" src="http://sdh.localhost.com/gv/js/jshash/sha512.js"></script>
<script type="text/javascript" src="http://sdh.localhost.com/gv/js/jshash/forms.js"></script>
<input type="hidden" id="p" value="8a407a8"/>
<script type="text/javascript">
$(document).ready(function() {
	var myURL = 'http://sdh.localhost.com/gv/front.php/login/index/gethashpassonlydata?u=hpnguyen&p=' + hex_sha512($('#p').val());
	$('#p').val('');
	$.ajax({
		type: "GET",
		url: myURL,
		success:function(result){
		},
		error: function (xhr,status,error){
		},
		complete: function(xhr,status){
			
		}
	});
});	
</script>