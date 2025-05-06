<?php
/**
 * Database Connection
 * 
 * Establishes connection to MySQL database for Reshina
 */

// Database configuration
$db_host = 'localhost';
$db_name = 'Reshina';
$db_user = 'root';
$db_pass = '';

// Create connection
try {
    $conn = new PDO("mysql:host=$db_host;dbname=$db_name", $db_user, $db_pass);
    // Set the PDO error mode to exception
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    // Set default fetch mode to associative array
    $conn->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
    // Set character set to utf8mb4
    $conn->exec("SET NAMES utf8mb4");
} catch(PDOException $e) {
    // Log error and display user-friendly message
    error_log("Connection failed: " . $e->getMessage());
    die("Maaf, terjadi kesalahan pada koneksi database. Silakan coba lagi nanti.");
}
?>
