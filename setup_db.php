<?php
require_once 'config/db.php';

$sqlFile = 'database.sql';
$sql = file_get_contents($sqlFile);

if ($sql === false) {
    die("Error reading database.sql file");
}

// Split SQL by semicolon to execute multiple queries
$queries = explode(';', $sql);

foreach ($queries as $query) {
    $query = trim($query);
    if (!empty($query)) {
        if ($conn->query($query) === TRUE) {
            echo "Query executed successfully.<br>";
        } else {
            echo "Error executing query: " . $conn->error . "<br>";
        }
    }
}

echo "Database setup completed!";
?>
