<?php
function allowPermisstion($pusr, $pf, $conn)
{
	$n=0;
	
	$sqlstr="SELECT DISTINCT f.fk_ma_chuc_nang CHUC_NANG
	FROM nhan_su n, ct_nhom_nhan_su ct, ct_nhom_nguoi_dung_portal f
	WHERE upper(n.username)=upper('$pusr')
	AND f.fk_ma_chuc_nang = '$pf'
	AND n.id=ct.fk_id_ns
	AND ct.fk_ma_nhom = f.fk_ma_nhom"; 
	
	//file_put_contents("logs.txt",$sqlstr );
	
	$stmt = oci_parse($conn, $sqlstr);
	oci_execute($stmt);
	$n = oci_fetch_all($stmt, $resDM);
	oci_free_statement($stmt);
			
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

function escapeJsonString($value) { # list from www.json.org: (\b backspace, \f formfeed)
    $escapers = array("\\", "/", "\"", "\n", "\r", "\t", "\x08", "\x0c");
    $replacements = array("\\\\", "\\/", "\\\"", "\\n", "\\r", "\\t", "\\f", "\\b");
    $result = str_replace($escapers, $replacements, $value);
    return $result;
}

function escapeWEB($str) // Ap dung dc cho data json
{
	$search = array('\\','"', "\n", "\r", "\t", "\b", "\f");
	$replace = array('\\\\',"&quot;", "<br>" , "", "", "", ""); 
	
	return str_replace($search,$replace,$str);
}
?>