//Load jQuery First
//How frequently to check for session expiration in milliseconds
var sess_pollInterval = 60000;
//How many minutes the session is valid for
var sess_expirationMinutes = 60;
//How many minutes before the warning prompt
var sess_warningMinutes = 50;

var sess_intervalID;
var sess_lastActivity;

function initSessionMonitor() {
    sess_lastActivity = new Date();
    sessSetInterval();
    $(document).bind('keypress.session', function (ed, e) { sessKeyPressed(ed, e); });
}
function sessSetInterval() {
    sess_intervalID = setInterval('sessInterval()', sess_pollInterval);
}
function sessClearInterval() {
    clearInterval(sess_intervalID);
}
function sessKeyPressed(ed, e) {
    sess_lastActivity = new Date();
}
function sessPingServer() {
    //Call an AJAX function to keep-alive your session.
	//$.post("tools/ping.html");
	xreq = $.ajax({
	  type: 'POST', url: 'tools/ping.html', dataType: "html", cache: false
	});
	
}

function sessLogOut() {
    window.location.href = '/gv/login.php?cat=signout';
}

function sessInterval() {
    var now = new Date();
    var diff = now - sess_lastActivity;
    var diffMins = (diff / 1000 / 60);
	
	sessPingServer();
	/*
    if (diffMins >= sess_warningMinutes) {
        //warning before expiring
        //stop the timer
        sessClearInterval();
        //promt for attention
        if (confirm('Phiên làm việc của bạn sẽ kết thúc trong ' + (sess_expirationMinutes - sess_warningMinutes) +
            ' phút nữa (bắt đầu tính từ ' + now.toTimeString() + ').'+
			'\nBấm OK để giữ kết nối hay bấm Cancel để sign out. \nNếu như bạn sign out thì các thay đổi dữ liệu sẽ bị mất.')) 
		{
            now = new Date();
            diff = now - sess_lastActivity;
            diffMins = (diff / 1000 / 60);

            if (diffMins > sess_expirationMinutes) {
                //timed out
                sessLogOut();
            }
            else {
                //reset inactivity timer
                sessPingServer();
                sessSetInterval();
                sess_lastActivity = new Date();
            }
        } else {
            sessLogOut();
        }
    } else {
        sessPingServer();
    }
	*/
}