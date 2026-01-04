<?php
// Database configuration
$host = getenv('DB_HOST') ?: "localhost";
$username = getenv('DB_USER') ?: "root";
$password = getenv('DB_PASS') ?: ""; // Default XAMPP password is empty
$database = getenv('DB_NAME') ?: "donkams_store";
$port = getenv('DB_PORT') ?: 3306;

// Create connection
$conn = mysqli_init();

// Set SSL options if needed (TiDB Cloud requires SSL)
$flags = 0;
if (getenv('DB_HOST') && strpos(getenv('DB_HOST'), 'tidbcloud.com') !== false) {
    // Basic SSL setup - typically TiDB works with just forcing SSL
    $conn->ssl_set(NULL, NULL, NULL, NULL, NULL);
    $flags = MYSQLI_CLIENT_SSL;
}

// Connect
if (!$conn->real_connect($host, $username, $password, '', $port, NULL, $flags)) {
    die("Connect Error: " . mysqli_connect_error());
}

// Create database if it doesn't exist (Only for local dev usually, remote DBs usually pre-exist)
// We only attempt to create DB if we are on localhost or if explicitly allowed
if ($host === 'localhost' || getenv('ALLOW_DB_CREATE') === 'true') {
    $sql = "CREATE DATABASE IF NOT EXISTS $database";
    if ($conn->query($sql) === TRUE) {
        $conn->select_db($database);
    } else {
        die("Error creating database: " . $conn->error);
    }
} else {
    // For production/remote, just select the DB
    $conn->select_db($database);
}

// Set charset to utf8mb4
$conn->set_charset("utf8mb4");

