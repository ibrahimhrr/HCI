<?php
$server = 'localhost';
$port = 8888;
$username = 'root';
$password = 'root';
$database = 'mydatabase';


$connection = mysqli_connect($server, $username, $password, $database, $port);
if (!$connection) {
    die('Connection failed: ' . mysqli_connect_error());
}

$select_db = mysqli_select_db($connection, $database);
if(!$select_db)
{
	echo("connection terminated");
}
?> 