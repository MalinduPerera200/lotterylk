<?php

/**
 * Database Connection Script
 * This file establishes a secure connection to the MySQL database using PDO
 */

// Include configuration file
require_once 'config.php';

// Database configuration - retrieve from config file instead of hardcoding
$host = $config['db_host'] ?? 'localhost';
$db = $config['db_name'] ?? 'lotterylk_db';
$user = $config['db_user'] ?? 'root';   // Should be changed in production
$pass = $config['db_pass'] ?? '';       // Should be changed in production
$charset = 'utf8mb4';

// Set DSN (Data Source Name)
$dsn = "mysql:host=$host;dbname=$db;charset=$charset";

// Set PDO options for better error handling and security
$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,    // Throw exceptions on errors
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,          // Return results as associative array
    PDO::ATTR_EMULATE_PREPARES   => false,                     // Use real prepared statements
];

// Try to establish connection
try {
    $pdo = new PDO($dsn, $user, $pass, $options);

    // Optional: Set a global variable to indicate successful connection
    $db_connected = true;
} catch (PDOException $e) {
    // Handle connection failure
    // For production (safer, doesn't expose details):
    $error_message = "දත්ත සමුදාය සම්බන්ධතාවය අසාර්ථක විය. කරුණාකර පසුව නැවත උත්සාහ කරන්න.";
    $db_connected = false;

    // Log error to file instead of displaying (for production)
    error_log("Database connection failed: " . $e->getMessage(), 0);
}
