<?php
	session_start();
	$_SESSION['password'] = sha1($_POST["password"]);
?>
