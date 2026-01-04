<?php
require_once 'config/db.php';

// Array of table creation SQL statements
$tables = [
    'users' => "CREATE TABLE IF NOT EXISTS users (
        id INT AUTO_INCREMENT PRIMARY KEY,
        username VARCHAR(50) NOT NULL,
        email VARCHAR(100) NOT NULL UNIQUE,
        password VARCHAR(255) NOT NULL,
        role ENUM('customer', 'admin') DEFAULT 'customer',
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )",
    'categories' => "CREATE TABLE IF NOT EXISTS categories (
        id INT AUTO_INCREMENT PRIMARY KEY,
        name VARCHAR(100) NOT NULL,
        description TEXT,
        image VARCHAR(255)
    )",
    'products' => "CREATE TABLE IF NOT EXISTS products (
        id INT AUTO_INCREMENT PRIMARY KEY,
        name VARCHAR(100) NOT NULL,
        description TEXT,
        price DECIMAL(10, 2) NOT NULL,
        image VARCHAR(255),
        category_id INT,
        stock INT DEFAULT 0,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (category_id) REFERENCES categories(id) ON DELETE SET NULL
    )",
    'orders' => "CREATE TABLE IF NOT EXISTS orders (
        id INT AUTO_INCREMENT PRIMARY KEY,
        user_id INT,
        total_amount DECIMAL(10, 2) NOT NULL,
        status ENUM('pending', 'completed', 'cancelled') DEFAULT 'pending',
        shipping_address TEXT,
        payment_method VARCHAR(50),
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL
    )",
    'order_items' => "CREATE TABLE IF NOT EXISTS order_items (
        id INT AUTO_INCREMENT PRIMARY KEY,
        order_id INT,
        product_id INT,
        quantity INT NOT NULL,
        price DECIMAL(10, 2) NOT NULL,
        FOREIGN KEY (order_id) REFERENCES orders(id) ON DELETE CASCADE,
        FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE SET NULL
    )",
    'wishlist' => "CREATE TABLE IF NOT EXISTS wishlist (
        id INT AUTO_INCREMENT PRIMARY KEY,
        user_id INT,
        product_id INT,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
        FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE
    )",
    'reviews' => "CREATE TABLE IF NOT EXISTS reviews (
        id INT AUTO_INCREMENT PRIMARY KEY,
        user_id INT,
        product_id INT,
        rating INT CHECK (rating >= 1 AND rating <= 5),
        comment TEXT,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
        FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE
    )"
];

echo "<h1>Database Migration Status</h1>";

foreach ($tables as $name => $sql) {
    if ($conn->query($sql) === TRUE) {
        echo "<p style='color: green;'>Table '$name' checked/created successfully.</p>";
    } else {
        echo "<p style='color: red;'>Error creating table '$name': " . $conn->error . "</p>";
    }
}

// Check for admin user
$admin_email = 'admin@donkams.com';
$stmt = $conn->prepare("SELECT id FROM users WHERE email = ?");
$stmt->bind_param("s", $admin_email);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows == 0) {
    // Create admin user
    $username = 'Admin';
    $password = password_hash('admin123', PASSWORD_DEFAULT);
    $role = 'admin';
    
    $insert = $conn->prepare("INSERT INTO users (username, email, password, role) VALUES (?, ?, ?, ?)");
    $insert->bind_param("ssss", $username, $admin_email, $password, $role);
    
    if ($insert->execute()) {
        echo "<p style='color: green;'>Admin user created (Email: $admin_email, Password: admin123)</p>";
    } else {
        echo "<p style='color: red;'>Error creating admin user: " . $insert->error . "</p>";
    }
} else {
    // Reset admin password
    $password = password_hash('admin123', PASSWORD_DEFAULT);
    $update = $conn->prepare("UPDATE users SET password = ? WHERE email = ?");
    $update->bind_param("ss", $password, $admin_email);
    if ($update->execute()) {
        echo "<p style='color: blue;'>Admin password reset to 'admin123'</p>";
    }
}

// Seed a second admin user with new credentials
$new_admin_email = 'admin2@donkams.com';
$check2 = $conn->prepare("SELECT id FROM users WHERE email = ?");
$check2->bind_param("s", $new_admin_email);
$check2->execute();
$res2 = $check2->get_result();
if ($res2->num_rows == 0) {
    $username2 = 'Admin2';
    $password2 = password_hash('Admin@2026!', PASSWORD_DEFAULT);
    $role2 = 'admin';
    $ins2 = $conn->prepare("INSERT INTO users (username, email, password, role) VALUES (?, ?, ?, ?)");
    $ins2->bind_param("ssss", $username2, $new_admin_email, $password2, $role2);
    if ($ins2->execute()) {
        echo "<p style='color: green;'>Second admin created (Email: $new_admin_email, Password: Admin@2026!)</p>";
    } else {
        echo "<p style='color: red;'>Error creating second admin: " . $ins2->error . "</p>";
    }
} else {
    echo "<p style='color: #555;'>Second admin already exists (Email: $new_admin_email)</p>";
}

echo "<p>Migration completed. <a href='index.php'>Go to Home</a></p>";
?>
