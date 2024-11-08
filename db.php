<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "ricmar";
$port = 3308;

$con = new mysqli($servername, $username, $password, $dbname, $port);

if ($con->connect_error) {
    die("Connection failed: " . $con->connect_error);
}
?>
