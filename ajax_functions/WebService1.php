<?php
	session_start();
	require "../server_settings.php";
	$MySQL = new MySQL();
	$mysqli = $MySQL->Connect_mysqli();
	$logFile = '/tmp/WebService1.php.logFile';
	//$postdata = $_POST; 											//this works when the data sent is on form "NewValue='' &ID=''"
	#$postdata = json_decode(stripslashes($_POST['data']),true); 	//this works when the data sent is on form { data: JSONencoded }
	$postdata = json_decode($_POST['data'],true);
	preg_match('/.*\/(.+)$/',$_SERVER['REQUEST_URI'],$groups); 
	$function = $groups[1];
	$info = date("Y-m-d H:i:s ") . $_SERVER['REMOTE_ADDR'] . " " . $_SERVER['HTTP_USER_AGENT'] . ":\n" ;
	$employee_celltitles = array('SiteID', 'Name', 'PhoneNo', 'Comment', 'MailAddress', 'MailAddress2', 'Optician' , 'Scientist'  );
	$site_celltitles = array('Name', 'Address', 'Zip', 'City', 'StreetAddress',	'StreetZip', 'StreetCity', 'PhoneNo', 'FaxNo', 'MailAddress', 'Approved' );
	$promocode_celltitles = array('PromoCode', 'StartDate', 'ExpireDate', 'Global' );
	$promocodesite_celltitles = array('PromoCodeID', 'SiteID' );

	class TaskHandler {
		private $mysqli;

		function __construct(&$mysqli) {
			$this->mysqli = $mysqli;
		}

		public function UpdateComputerSettings($ComputerID,$CustomerID,$EmployeeID,&$ret,&$info) {
			$query1 = "CALL UpdateComputerSettings(@success,@Customer,@Employee,?,?,?);";
			$query2 = "SELECT @success,@Customer,@Employee;";
			$info = $info . " ComputerID=" . $ComputerID . " CustomerID=" . $CustomerID . " EmployeeID=" . $EmployeeID . "\n";
			if($stmt = $this->mysqli->prepare($query1)) {
				$stmt->bind_param("iii", $ComputerID, $CustomerID, $EmployeeID);
				$stmt->execute();
				$info = $info . $query1 . " error:" . $this->mysqli->error  . "\n";
			}
			if($stmt = $this->mysqli->prepare($query2)) {
				$stmt->execute();
				$stmt->bind_result($success, $Customer, $Employee);
				$stmt->fetch();
				if ($this->mysqli->errno == 0) {
					$ret['d'] = "$Customer:$Employee";
				}
				$info = $info . " success=" . $success . "\n";
			}
			$info = $info . $query1 . " error:" . $this->mysqli->error  . "\n";
		}

		public function Update($tablename,&$info,&$postdata,&$ret) {
			preg_match('/^([a-zA-Z0-9]+)_(\d+)?_?([a-zA-Z0-9]+)?_?(\d+)?.*/', $postdata['ID'], $groups);
			$title = $groups[1];
			$id = $groups[2];
			$newval = $postdata['NewValue'];
			$newval_ret = $newval;

			if (empty($newval)) { 
				$newval = null;
				$info = $info . "newval set to null, id:" . $id . "\n";
			}
			else {
				$newval = mysqli_real_escape_string($this->mysqli,$newval);
			}

			if ($tablename == 'Computer' && $title == 'EmployeeID') {
				$this->UpdateComputerSettings($groups[4], null, $newval, $ret, $info);
			}
			else if ($tablename == 'Computer' && $title == 'CustomerID') {
				$this->UpdateComputerSettings($groups[4], $newval, null, $ret, $info);
			}
			else {
				if ($title == 'Approved' || $title == 'Scientist' || $title == 'Optician' || $title == 'Global') {
					($newval == "Yes") ? $newval = "1" : $newval = "0";
				}
				$query = "UPDATE $tablename SET $title=? WHERE ID=?";
				if($stmt = $this->mysqli->prepare($query)) {
					$stmt->bind_param("si", $newval, $id);
					$stmt->execute();
				    if ($this->mysqli->errno == 0) {
			            $ret['d'] = $newval_ret;
		            }
	            }
				if ($title == 'Scientist' || $title == 'Optician') {
					$this->UpdateComputerSettings(null, null, $id, $ret, $info);
				}
				$info = $info . $query . " error:" . $this->mysqli->error  . "\n";
			}
		}
	
		public function Add($tablename,$celltitles,&$info,&$postdata,&$ret) {	
			foreach ($celltitles as $key => $title) {
				$columns = $columns . $title . ",";
				$values = $values . "'" . mysqli_real_escape_string($this->mysqli, $postdata[$title]) . "'" . ",";
			}
			$columns = trim($columns,",");
			$values = trim($values,",");
			$query = "INSERT INTO $tablename($columns) VALUES($values)";
			$result = $this->mysqli->query($query);
			if ($this->mysqli->errno == 0) {
				$ret['d'] = "added" . $this->mysqli->insert_id;
			}
			$info = $info . $query . " error:" . $this->mysqli->error . "  insert_id: " . $this->mysqli->insert_id . "\n";
		}
	
		public function AddEmployee($tablename,$celltitles,&$info,&$postdata,&$ret) {
			//$query = "CALL AddEmployee(@ret1,@ret2,$values); SELECT @ret1,@ret2;";
			$query1 = "CALL AddEmployee(@ret1,@ret2,?,?,?,?,?,?,?,?);";
			$query2 = "SELECT @ret1,@ret2;";
			if($stmt = $this->mysqli->prepare($query1)) {
				$stmt->bind_param("isssssii", $postdata['SiteID'], $postdata['Name'], $postdata['PhoneNo'], $postdata['Comment'], $postdata['MailAddress'], $postdata['MailAddress2'], $postdata['Optician'], $postdata['Scientist'] );
				$stmt->execute();
				$info = $info . $query1 . " error:" . $this->mysqli->error  . "\n";
			}
			if($stmt = $this->mysqli->prepare($query2)) {
				$stmt->execute();
				$stmt->bind_result($EmployeeID,$EmployeeCustomerID);
				$stmt->fetch();
				$info = $info . $query2 . " error:" . $this->mysqli->error  . "\n";
			}
			/*$this->mysqli->multi_query($query);
			$this->mysqli->next_result();
			$result = $this->mysqli->store_result();
			$row = $result->fetch_row();*/
			if ($this->mysqli->errno == 0) {
				$ret['d'] = "added" . $EmployeeID . ":" . $EmployeeCustomerID;
			}
		}

		public function DeleteEmployee($tablename,&$info,&$postdata,&$ret) {
			$id = $postdata['ID'];
			$query = "CALL DeleteEmployee(@ret,$id);";
			$this->mysqli->query($query);
			if ($this->mysqli->errno == 0) {
				$ret['d'] = "deleted";
			}
			$info = $info . $query . " error:" . $this->mysqli->error  . "\n";
		}

		public function Delete($tablename,&$info,&$postdata,&$ret) {
			$id = $postdata['ID'];
			$query = "DELETE FROM $tablename WHERE ID=?;";
			if($stmt = $this->mysqli->prepare($query)) {
				$stmt->bind_param("i", $id);
				$stmt->execute();
				if ($this->mysqli->errno == 0) {
					$ret['d'] = "deleted";
				}
			}
			$info = $info . $query . " error:" . $this->mysqli->error  . "\n";
		}

		public function DeletePromoCodeSite($tablename,&$info,&$postdata,&$ret) {
			list($id1,$id2) = split(':',$postdata['ID']);
			$query = "DELETE FROM PromoCodeSite WHERE SiteID=? AND PromoCodeID=?;";
			if($stmt = $this->mysqli->prepare($query)) {
				$stmt->bind_param("ii", $id2, $id1);
				$stmt->execute();
				if ($this->mysqli->errno == 0) {
		            $ret['d'] = "deleted";
	            }
			}
			$info = $info . $query . " error:" . $this->mysqli->error  . "\n";
		}

		
	}

	$MyTaskHandler = new TaskHandler($mysqli);

	if (isset($mysqli)) {
		switch($function) {
			case 'UpdateEmployee':
				$MyTaskHandler->Update("Employee",$info,$postdata,$ret);
				break;
			case 'AddEmployee':
				$MyTaskHandler->AddEmployee("Employee",$employee_celltitles,$info,$postdata,$ret);
				break;
			case 'DeleteEmployee':
				$MyTaskHandler->DeleteEmployee("Employee",$info,$postdata,$ret);
				break;
			case 'UpdateSite':
				$MyTaskHandler->Update("Site",$info,$postdata,$ret);
				break;
			case 'AddSite':
				$MyTaskHandler->Add("Site",$site_celltitles,$info,$postdata,$ret);
				break;
			case 'DeleteSite':
				$MyTaskHandler->Delete("Site",$info,$postdata,$ret);
				break;
			case 'UpdatePromoCode':
				$MyTaskHandler->Update("PromoCode",$info,$postdata,$ret);
				break;
			case 'AddPromoCode':
				$MyTaskHandler->Add("PromoCode",$promocode_celltitles,$info,$postdata,$ret);
				break;
			case 'DeletePromoCode':
				$MyTaskHandler->Delete("PromoCode",$info,$postdata,$ret);
				break;
			case 'AddPromoCodeSite':
				$MyTaskHandler->Add("PromoCodeSite",$promocodesite_celltitles,$info,$postdata,$ret);
				break;
			case 'DeletePromoCodeSite':
				$MyTaskHandler->DeletePromoCodeSite("PromoCodeSite",$info,$postdata,$ret);
				break;
			case 'UpdateComputer':
				$MyTaskHandler->Update("Computer",$info,$postdata,$ret);
				break;
			default: 
				break;
		}
	}
	else {
		$info = $info . "Access denied, \$mysqli is not set\n"; 
	}

	$info = $info . " print_r(\$_POST): " . print_r($_POST,true) . " \$_SERVER['REQUEST_METHOD']: " . $_SERVER['REQUEST_METHOD'] . " \$function: " . $function . "\n\n";
	error_log($info,3,$logFile);

	echo json_encode($ret);

?>


