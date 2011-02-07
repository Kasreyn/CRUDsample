<?php
	
class MySQL {

	public $IP;
	public $user;
	public $password;
	public $DB;
	public $RegistrationEmail;
	public $sessionpassword;


	function __construct() {
		$this->IP = "localhost";
		$this->user = "web";
		$this->password = "zzVZ4J8aAxJ8dUS";
		$this->DB = "RetCorrLinux";
		$this->sessionpassword = "samsyn3945";
	}
	
	public function CheckPassword() {
		return ($_SESSION['password'] == sha1($this->sessionpassword) || true);
		//$_SERVER['REMOTE_ADDR'] == "11.160.103.138");
	}

	public function Connect() {
		if ($this->CheckPassword()) {
			$link = mysql_connect($this->IP, $this->user, $this->password) or die("Unable to connect to MySQL");
			mysql_select_db($this->DB);
			mysql_set_charset('utf8');
		}
		return $link;
	}

	public function Connect_mysqli() {
		if ($this->CheckPassword()) {
			$link = new mysqli($this->IP, $this->user, $this->password, $this->DB);
			$link->set_charset("utf8");
		}
		return $link;
	}
}

?>
