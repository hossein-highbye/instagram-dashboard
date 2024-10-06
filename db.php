<?php
require_once 'config.php';

// main conf
$dsn = "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME;
try {
    $pdo = new PDO($dsn, DB_USER, DB_PASS);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    echo "Connection failed: " . $e->getMessage();
}

$users_table = "CREATE TABLE IF NOT EXISTS users(
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(100) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    role ENUM('admin', 'user') DEFAULT 'user',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);";
$pdo->exec($users_table);

$messages_table = "CREATE TABLE IF NOT EXISTS messages (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT,
    sender VARCHAR(100),
    receiver VARCHAR(100),
    message TEXT,
    timestamp DATETIME DEFAULT CURRENT_TIMESTAMP,
    status ENUM('sent', 'received') DEFAULT 'sent',
    FOREIGN KEY (user_id) REFERENCES users(id)
);";
$pdo->exec($messages_table);

// Add this to your database initialization or in a separate migration file
$token_table = "CREATE TABLE IF NOT EXISTS tokens (
    id INT AUTO_INCREMENT PRIMARY KEY,
    access_token TEXT NOT NULL,
    expires_at DATETIME NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);";
$pdo->exec($token_table);

function test_input($data) {
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);
    return $data;
}