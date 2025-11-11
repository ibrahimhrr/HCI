<?php
$server = 'localhost';
$port = 8888;
$username = 'root';
$password = 'root';
$database = 'mydatabase';


$connection = mysqli_connect($server, $username, $password, $database, $port);
if (!$connection) {
    // For AJAX requests, return JSON error instead of dying
    if (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') {
        header('Content-Type: application/json');
        echo json_encode(['error' => true, 'message' => 'Database connection failed: ' . mysqli_connect_error()]);
        exit;
    }
    die('Connection failed: ' . mysqli_connect_error());
}

$select_db = mysqli_select_db($connection, $database);
if(!$select_db)
{
    // For AJAX requests, return JSON error
    if (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') {
        header('Content-Type: application/json');
        echo json_encode(['error' => true, 'message' => 'Database selection failed']);
        exit;
    }
    echo("connection terminated");
}
// No closing PHP tag - best practice for include files to avoid whitespace issues