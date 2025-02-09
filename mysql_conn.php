<?php
//Connection Parameters
$servername = 'localhost:3306';
$username = 'root';
$userpwd = '';
$dbname = 'assignment1'; 

// Create connection
$conn = new mysqli($servername, $username, $userpwd, $dbname);
// Check connection
if ($conn->connect_error) {
	die("Connection failed: " . $conn->connect_error);	
}
?>
