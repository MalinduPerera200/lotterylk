<?php
// Include database connection
include '../includes/db_connect.php';

// Start session if not already started
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Check if user is logged in
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    // Redirect to login page
    header('Location: index.php');
    exit;
}

// Check if form was submitted via POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    $_SESSION['flash_message'] = 'අවලංගු ඉල්ලීමක්.';
    $_SESSION['flash_message_type'] = 'error';
    header('Location: manage_users.php');
    exit;
}

// Get data from form
$user_id = isset($_POST['user_id']) ? (int)$_POST['user_id'] : 0;
$new_password = isset($_POST['new_password']) ? $_POST['new_password'] : '';

// Validate data
if ($user_id <= 0) {
    $_SESSION['flash_message'] = 'වලංගු පරිශීලක ID එකක් සපයන්න.';
    $_SESSION['flash_message_type'] = 'error';
    header('Location: manage_users.php');
    exit;
}

if (empty($new_password) || strlen($new_password) < 6) {
    $_SESSION['flash_message'] = 'මුරපදය අවම වශයෙන් අක්ෂර 6ක් විය යුතුය.';
    $_SESSION['flash_message_type'] = 'error';
    header('Location: manage_users.php');
    exit;
}

// Process password reset
if (isset($pdo) && $db_connected) {
    try {
        // Check if user exists
        $check_stmt = $pdo->prepare('SELECT username FROM admin_users WHERE id = :id');
        $check_stmt->bindParam(':id', $user_id, PDO::PARAM_INT);
        $check_stmt->execute();
        
        $user = $check_stmt->fetch();
        
        if (!$user) {
            // User not found
            $_SESSION['flash_message'] = 'පරිශීලකයා හමු නොවීය.';
            $_SESSION['flash_message_type'] = 'error';
        } else {
            // Hash the new password
            $password_hash = password_hash($new_password, PASSWORD_DEFAULT);
            
            // Update the password
            $update_stmt = $pdo->prepare('UPDATE admin_users SET password_hash = :password_hash WHERE id = :id');
            $update_stmt->bindParam(':id', $user_id, PDO::PARAM_INT);
            $update_stmt->bindParam(':password_hash', $password_hash, PDO::PARAM_STR);
            
            if ($update_stmt->execute()) {
                // Success
                $_SESSION['flash_message'] = $user['username'] . ' සඳහා මුරපදය සාර්ථකව යළි පිහිටුවන ලදී.';
                $_SESSION['flash_message_type'] = 'success';
            } else {
                // Update failed
                $_SESSION['flash_message'] = 'මුරපදය යළි පිහිටුවීමට අසමත් විය.';
                $_SESSION['flash_message_type'] = 'error';
            }
        }
    } catch (PDOException $e) {
        error_log("Database error in reset_password.php: " . $e->getMessage(), 0);
        $_SESSION['flash_message'] = 'මුරපදය යළි පිහිටුවීමේදී දත්ත සමුදාය දෝෂයක් ඇති විය.';
        $_SESSION['flash_message_type'] = 'error';
    }
} else {
    // Database connection error
    $_SESSION['flash_message'] = 'දත්ත සමුදාය සම්බන්ධතාවය අසාර්ථක විය.';
    $_SESSION['flash_message_type'] = 'error';
}

// Redirect back to manage users page
header('Location: manage_users.php');
exit;
?>
