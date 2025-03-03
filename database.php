<?php
// database.php
session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);

$servername = "db5015819388.hosting-data.io";
$username = "dbu3282838";
$password = "myNewWordpress111";
$dbname = "dbs12900011";

// Create connection
$conn = new mysqli($servername, $username, $password);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Create database if not exists
$sql = "CREATE DATABASE IF NOT EXISTS $dbname";
$conn->query($sql);
$conn->select_db($dbname);

// Create products table
$sql = "CREATE TABLE IF NOT EXISTS products (
    id INT AUTO_INCREMENT PRIMARY KEY,
    sku VARCHAR(50) UNIQUE,
    name VARCHAR(255),
    price DECIMAL(10,2),
    sale_price DECIMAL(10,2),
    color VARCHAR(50),
    size VARCHAR(15),
    image VARCHAR(255)
)";
$conn->query($sql);

// Create orders table
$sql = "CREATE TABLE IF NOT EXISTS orders (
    id INT AUTO_INCREMENT PRIMARY KEY,
    product_id INT,
    quantity INT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE
)";
$conn->query($sql);
?>
