<?php
//session_start();
// store session data
if (isset($_SESSION['id']))
{
	session_id($_SESSION['id']);
	session_start();
}
?>