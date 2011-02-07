<?php
	session_start();
	require "../server_settings.php";
	//$PromoCode = $_POST['PromoCode'];

	if ($_GET['TableSource'] == 'PromoCode') {
		$field = 'PromoCode';
		$query = "SELECT $field,ID FROM PromoCode ORDER BY ID DESC";
	}
	else if ($_GET['TableSource'] == 'Site') {
		$field = 'Name';
		$query = "SELECT $field,ID FROM Site ORDER BY ID DESC";
	}
	else if ($_GET['TableSource'] == 'Computer') {
		$field = 'Name';
		$query = "SELECT $field,ID FROM Computer ORDER BY ID DESC";
	}
	else if ($_GET['TableSource'] == 'Employee') {
		$field = 'Name';
		$query = "SELECT CONCAT('(',ID,') ',Name) AS $field,ID FROM Employee ORDER BY ID DESC";
	}

	$MySQL = new MySQL();
	$mysqli = $MySQL->Connect_mysqli();

	if ($result = $mysqli->query($query)) {
		while ($row = $result->fetch_assoc()) {
			$ret = $ret . preg_replace("[:|,]","",$row[$field]) . ":" . $row['ID'] . ",";
		}
		$ret = trim($ret," \n,");
	}
	echo $ret;
	$mysqli->close();
?>

