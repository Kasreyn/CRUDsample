<?php
	session_start();
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" >
<head>
	<meta http-equiv="content-type" content="text-html; charset=utf-8"/>
	<link rel="shortcut icon" href="images/favicon.ico">
	<title></title>
	<style type="text/css" title="currentStyle">
		@import "css/dataTables.css";
		@import "css/styles.css";
		@import "css/default/jquery-ui-1.8.8.custom.css";
	</style>
	<script src="scripts/jquery-1.4.4.min.js" type="text/javascript"></script>
	<script src="scripts/jquery-ui-1.8.8.custom.min.js" type="text/javascript"></script>
	<script src="scripts/jquery.themeswitchertool-jj.js" type="text/javascript"></script>
	<script src="scripts/jquery.editinplace.v2.2.0-jj.js" type="text/javascript"></script>
	<script src="scripts/jquery.dataTables.1.7.4.js" type="text/javascript"></script>
	<script src="scripts/jquery.UI.combobox.js" type="text/javascript"></script>
	<script src="scripts/shortcut.js" type="text/javascript"></script>
	<script src="scripts/tooltip.js" type="text/javascript"></script>
	<script src="scripts/optician_crud.js" type="text/javascript"></script> 
</head>

<body>
	<?php
		require "server_settings.php";

		$MySQL = new MySQL();
		$link = $MySQL->Connect();
		require "content.php";

		function specialchars($str) {
			return str_replace('\n',"&#x0A;",htmlspecialchars($str));
		}
	?>
</body>
</html>
