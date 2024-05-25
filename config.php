<?php

if (!defined('BASE_URL')) {
    define('BASE_URL', 'http://localhost/LostLink.github.io/');
}

// Database connection configuration
$host = 'localhost';
$username = 'root';
$password = '';
$database = 'bbcdb';

// Create a connection to the database
$conn = mysqli_connect($host, $username, $password, $database);

if (!$conn) {
    echo "Connection Failed";
    exit(); // Exit if connection fails
}


?>
