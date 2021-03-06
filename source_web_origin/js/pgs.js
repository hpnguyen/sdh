// JavaScript Document
// Check Regexp
// Checklength

//event to check session time variable declaration
var checkSessionTimeEvent;
var sessionLength = 30*60; // 30 minus
//time redirect forced (5 = redirect forced 10 seconds after session ends)    
var forceRedirect = 5;
//time session started
var pageRequestTime = new Date();
//session timeout length
var timeoutLength = sessionLength*1000;
//force redirect to log in page length (session timeout plus 10 seconds)
var forceRedirectLength = timeoutLength + (forceRedirect*1000);

function writeConsole(content, pwidth, pheight) {
		top.consoleRef=window.open('','myconsole',
		'width='+pwidth+',height='+pheight
		+',menubar=0'
		+',toolbar=0'
		+',status=0'
		+',scrollbars=1'
		+',resizable=1')
		top.consoleRef.document.writeln(
		'<html><head><title>Phòng Đào Tạo SĐH - ĐHBK</title></head>'
		+'<body bgcolor=white onLoad="self.focus()">'
		+content
		+'</body></html>'
		)
		top.consoleRef.document.close()
}

// Diable back button 
function changeHashOnLoad() {
     window.location.href += "#";
     setTimeout("changeHashAgain()", "50");
}

function changeHashAgain() {
  window.location.href += "B";
}

/* var storedHash = window.location.hash;
window.setInterval(function () {
    if (window.location.hash != storedHash) {
         window.location.hash = storedHash;
    }
}, 50); */

function detectIE() {
    var ua = window.navigator.userAgent;
    var msie = ua.indexOf('MSIE ');
    var trident = ua.indexOf('Trident/');

    if (msie > 0) {
        // IE 10 or older => return version number
        return parseInt(ua.substring(msie + 5, ua.indexOf('.', msie)), 10);
    }

    if (trident > 0) {
        // IE 11 (or newer) => return version number
        var rv = ua.indexOf('rv:');
        return parseInt(ua.substring(rv + 3, ua.indexOf('.', rv)), 10);
    }

    // other browser
    return false;
}
// ---------------------