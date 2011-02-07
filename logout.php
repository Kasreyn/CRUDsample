<?php
	session_start();
	#session_unset();
	#session_destroy();
	unset($_SESSION['password']);
	#header('Location:index.php');
	header('Location:.');
?>
