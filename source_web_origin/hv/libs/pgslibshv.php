<?php
function allowUser($pusr, $ppass, $conn)
{
	$n=0;
	$sqlstr="SELECT username 
	FROM nguoi_dung
	WHERE username='".escape($pusr)."' and pass='".escape($ppass)."'";
	$stmt = oci_parse($conn, $sqlstr);oci_execute($stmt);$n = oci_fetch_all($stmt, $resDM);oci_free_statement($stmt);
	return $n;
}

function escape($str)
{	
	$search=array("\\","\0","\n","\r","\x1a","'",'"');
	$replace=array("\\\\","\\0","\\n","\\r","\Z","\'",'\"');
	return str_replace($search,$replace,$str);
}

function reverse_escape($str)
{
	$search=array("\\\\","\\0","\\n","\\r","\Z","\'",'\"');
	$replace=array("\\","\0","\n","\r","\x1a","'",'"');
	return str_replace($search,$replace,$str);
}

function escapeWEB($str)
{
	$search = array('\\',"'",'"', "\n", "\r");
	$replace = array('\\\\',"&apos;","&quot;", "<br/>" , ""); 
	
	return str_replace($search,$replace,$str);
}

function escapeJsonString($value) { # list from www.json.org: (\b backspace, \f formfeed)
    $escapers = array("\\", "/", "\"", "\n", "\r", "\t", "\x08", "\x0c");
    $replacements = array("\\\\", "\\/", "\\\"", "\\n", "\\r", "\\t", "\\f", "\\b");
    $result = str_replace($escapers, $replacements, $value);
    return $result;
}
?>