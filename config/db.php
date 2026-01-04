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

$conn->query("CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL,
    email VARCHAR(100) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    role ENUM('customer','admin') DEFAULT 'customer',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
)");

$conn->query("CREATE TABLE IF NOT EXISTS wishlist (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT,
    product_id INT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
)");

function ensure_admin_user($conn) {
    $email = 'admin@donkams.com';
    $stmt = $conn->prepare("SELECT id FROM users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $res = $stmt->get_result();
    if ($res->num_rows === 0) {
        $username = 'Admin';
        $password = password_hash('admin123', PASSWORD_DEFAULT);
        $role = 'admin';
        $ins = $conn->prepare("INSERT INTO users (username, email, password, role) VALUES (?, ?, ?, ?)");
        $ins->bind_param("ssss", $username, $email, $password, $role);
        $ins->execute();
    }

    $email2 = 'admin2@donkams.com';
    $stmt2 = $conn->prepare("SELECT id FROM users WHERE email = ?");
    $stmt2->bind_param("s", $email2);
    $stmt2->execute();
    $res2 = $stmt2->get_result();
    if ($res2->num_rows === 0) {
        $username2 = 'Admin2';
        $password2 = password_hash('Admin@2026!', PASSWORD_DEFAULT);
        $role2 = 'admin';
        $ins2 = $conn->prepare("INSERT INTO users (username, email, password, role) VALUES (?, ?, ?, ?)");
        $ins2->bind_param("ssss", $username2, $email2, $password2, $role2);
        $ins2->execute();
    }
}
ensure_admin_user($conn);

// Session Handler
require_once __DIR__ . '/../includes/session_handler.php';
$handler = new DBSessionHandler($conn);
session_set_save_handler($handler, true);
