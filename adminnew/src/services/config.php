<?php

// Database configuration
$dbHost = 'localhost';
$dbUsername = 'root'; // Replace with your database username
$dbPassword = 'Job@9198'; // Replace with your database password
$dbName = 'rearthbasednew'; // Replace with your database name

// Create a database connection
$conn = new mysqli($dbHost, $dbUsername, $dbPassword, $dbName);

// Check the connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// timezone
date_default_timezone_set('Asia/kolkata');

// echo "Connected successfully";

// Close the database connection (optional)
// $conn->close();    
?>