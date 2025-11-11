<?php
/**
 * Docker Environment Database Connection
 * Automatically uses environment variables from docker-compose.yml
 */

// Get environment variables (set by Docker)
$hostname = getenv('DB_HOST') ?: 'localhost';
$username = getenv('DB_USER') ?: 'root';
$password = getenv('DB_PASSWORD') ?: '';
$database = getenv('DB_NAME') ?: 'onlyplans';

// Create connection
$connection = mysqli_connect($hostname, $username, $password, $database);

// Check connection
if (!$connection) {
    die("Database Connection Failed: " . mysqli_connect_error());
}

// Optional: Set timezone
// mysqli_query($connection, "SET time_zone = '+00:00'");
?>
