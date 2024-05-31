<?php
$servername = "localhost:3306";
$username = "rvfdcfte_uslweb_user";
$password = "wMf!eGx5VTVJ";
$dbname = "rvfdcfte_uslweb";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>
