// JavaScript Document
// Check Regexp
// Checklength

function writeConsole(content, pwidth, pheight) {
		top.consoleRef=window.open('','myconsole',
		'width='+pwidth+',height='+pheight
		+',menubar=0'
		+',toolbar=0'
		+',status=0'
		+',scrollbars=1'
		+',resizable=1')
		top.consoleRef.document.writeln(
		'<html><head><title>Phong Dao Tao SDH - DHBK</title></head>'
		+'<body bgcolor=white onLoad="self.focus()">'
		+content
		+'</body></html>'
		)
		top.consoleRef.document.close()
}