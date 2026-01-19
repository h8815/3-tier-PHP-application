<?php
// Database inspection tool
$_SERVER['REQUEST_METHOD'] = 'CLI';
require_once 'db.php';

echo "=== Database Schema Inspector ===\n\n";

// Check if users table exists
$result = $conn->query("SHOW TABLES LIKE 'users'");
if ($result->num_rows > 0) {
    echo "Users table exists. Columns:\n";
    $columns = $conn->query("DESCRIBE users");
    while ($row = $columns->fetch_assoc()) {
        echo "- {$row['Field']} ({$row['Type']})\n";
    }
} else {
    echo "Users table does not exist.\n";
    echo "Available tables:\n";
    $tables = $conn->query("SHOW TABLES");
    while ($row = $tables->fetch_array()) {
        echo "- {$row[0]}\n";
    }
}

$conn->close();
?>