<?php
// CLI ADMIN CREATION TOOL
// Usage: docker exec -it <container> php create_admin.php

$_SERVER['REQUEST_METHOD'] = 'CLI';
require_once 'db.php';

echo "🎨 NEO-BRUTALIST STUDENT HUB - ADMIN CREATOR 🎨\n";
echo "===============================================\n\n";

// Get username
echo "Enter admin username: ";
$username = trim(fgets(STDIN));

if (empty($username)) {
    echo "❌ Error: Username cannot be empty.\n";
    exit(1);
}

// Get password
echo "Enter admin password: ";
$password = trim(fgets(STDIN));

if (empty($password)) {
    echo "❌ Error: Password cannot be empty.\n";
    exit(1);
}

// Confirm password
echo "Confirm password: ";
$confirmPassword = trim(fgets(STDIN));

if ($password !== $confirmPassword) {
    echo "❌ Error: Passwords do not match.\n";
    exit(1);
}

try {
    // Check if users table exists, create if not
    $tableCheck = $conn->query("SHOW TABLES LIKE 'users'");
    if ($tableCheck->num_rows == 0) {
        echo "📦 Creating users table...\n";
        $createTable = "CREATE TABLE users (
            id INT(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
            username VARCHAR(50) NOT NULL UNIQUE,
            password_hash VARCHAR(255) NOT NULL,
            created_at DATETIME DEFAULT CURRENT_TIMESTAMP
        )";
        $conn->query($createTable);
    }
    
    // Check if username exists
    $checkSql = "SELECT id FROM users WHERE username = ?";
    $checkStmt = $conn->prepare($checkSql);
    $checkStmt->bind_param("s", $username);
    $checkStmt->execute();
    $result = $checkStmt->get_result();
    
    if ($result->num_rows > 0) {
        echo "❌ Error: Username '$username' already exists.\n";
        exit(1);
    }
    
    // Hash password with ARGON2ID
    $passwordHash = password_hash($password, PASSWORD_ARGON2ID);
    
    // Insert new admin
    $insertSql = "INSERT INTO users (username, password_hash) VALUES (?, ?)";
    $insertStmt = $conn->prepare($insertSql);
    $insertStmt->bind_param("ss", $username, $passwordHash);
    
    if ($insertStmt->execute()) {
        echo "\n✅ SUCCESS: Admin user '$username' created!\n";
        echo "🚀 You can now login to the Neo-Brutalist Hub!\n\n";
    } else {
        echo "❌ Error: Failed to create admin user.\n";
        exit(1);
    }
    
} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
    exit(1);
} finally {
    $conn->close();
}
?>