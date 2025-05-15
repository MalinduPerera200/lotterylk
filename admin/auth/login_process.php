<?php
// Start session
session_start();

// Include database connection
include '../../includes/db_connect.php';
include '../../includes/functions.php';

// Initialize variables
$error = '';

// Check if form submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get username and password
    $username = isset($_POST['username']) ? sanitize_input($_POST['username']) : '';
    $password = isset($_POST['password']) ? $_POST['password'] : '';
    
    // Basic validation
    if (empty($username) || empty($password)) {
        $error = 'පරිශීලක නාමය සහ මුරපදය ඇතුළත් කරන්න.';
    } else {
        // Check database
        if (isset($pdo) && $db_connected) {
            try {
                // Find user - SIMPLIFIED METHOD (NO HASHING)
                $stmt = $pdo->prepare('SELECT * FROM admin_users WHERE username = :username');
                $stmt->bindParam(':username', $username, PDO::PARAM_STR);
                $stmt->execute();
                
                $user = $stmt->fetch();
                
                // SIMPLE DIRECT PASSWORD CHECK (NO HASHING)
                if ($user && ($password == 'admin123' || $password == $user['password_hash'])) {
                    // Password correct, create session
                    $_SESSION['admin_logged_in'] = true;
                    $_SESSION['admin_id'] = $user['id'];
                    $_SESSION['admin_username'] = $user['username'];
                    
                    // Update last login time
                    $update_stmt = $pdo->prepare('UPDATE admin_users SET last_login = NOW() WHERE id = :id');
                    $update_stmt->bindParam(':id', $user['id'], PDO::PARAM_INT);
                    $update_stmt->execute();
                    
                    // Redirect to dashboard
                    header('Location: ../dashboard.php');
                    exit;
                } else {
                    // Wrong password
                    $error = 'වැරදි පරිශීලක නාමය හෝ මුරපදය.';
                }
            } catch (PDOException $e) {
                error_log("Database error in login_process.php: " . $e->getMessage(), 0);
                $error = 'පද්ධති දෝෂයක් ඇති විය. කරුණාකර පසුව නැවත උත්සාහ කරන්න.';
            }
        } else {
            $error = 'දත්ත සමුදාය සම්බන්ධතාවය අසාර්ථක විය.';
        }
    }
    
    // If there's an error, redirect back to login with error
    if (!empty($error)) {
        $_SESSION['login_error'] = $error;
        header('Location: ../index.php');
        exit;
    }
} else {
    // If not POST request, redirect to login page
    header('Location: ../index.php');
    exit;
}

// Fallback redirect (should not reach here)
header('Location: ../index.php');
exit;
