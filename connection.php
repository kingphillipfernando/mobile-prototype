<?php
class dbObj
{
	var $servername = "localhost";
	var $username = "kinfer0_king";
	var $password = "M1sf1tdem0n";
	var $dbname = "kinfer0_king";
	var $conn;
	
	public function getConnstring()
	{
		$this->conn = mysqli_connect($this->servername, $this->username, $this->password, $this->dbname);
		if(!$this->conn){
		  die("Connection Failed: " . mysqli_connect_error());
		  
		}else{
		return $this->conn;}
	}
}
?>
