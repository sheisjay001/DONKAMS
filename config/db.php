<?php
// Database configuration
$host = "localhost";
$username = "root";
$password = ""; // Default XAMPP password is empty
$database = "donkams_store";

// Create connection
$conn = new mysqli($host, $username, $password);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Create database if it doesn't exist
$sql = "CREATE DATABASE IF NOT EXISTS $database";
if ($conn->query($sql) === TRUE) {
    $conn->select_db($database);
} else {
    die("Error creating database: " . $conn->error);
}

// Set charset to utf8mb4
$conn->set_charset("utf8mb4");

?>
