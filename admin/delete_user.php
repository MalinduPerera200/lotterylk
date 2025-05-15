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

// Get user ID from form
$user_id = isset($_POST['user_id']) ? (int)$_POST['user_id'] : 0;

// Validate user ID
if ($user_id <= 0) {
    $_SESSION['flash_message'] = 'වලංගු පරිශීලක ID එකක් සපයන්න.';
    $_SESSION['flash_message_type'] = 'error';
    header('Location: manage_users.php');
    exit;
}

// Don't allow deleting own account
if ($user_id === (int)$_SESSION['admin_id']) {
    $_SESSION['flash_message'] = 'ඔබගේම ගිණුම මැකීමට නොහැක.';
    $_SESSION['flash_message_type'] = 'error';
    header('Location: manage_users.php');
    exit;
}

// Process deletion
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
            // Delete the user
            $delete_stmt = $pdo->prepare('DELETE FROM admin_users WHERE id = :id');
            $delete_stmt->bindParam(':id', $user_id, PDO::PARAM_INT);
            
            if ($delete_stmt->execute()) {
                // Success
                $_SESSION['flash_message'] = $user['username'] . ' පරිශීලකයා සාර්ථකව මකා දමන ලදී.';
                $_SESSION['flash_message_type'] = 'success';
            } else {
                // Deletion failed
                $_SESSION['flash_message'] = 'පරිශීලකයා මකා දැමීමට අසමත් විය.';
                $_SESSION['flash_message_type'] = 'error';
            }
        }
    } catch (PDOException $e) {
        error_log("Database error in delete_user.php: " . $e->getMessage(), 0);
        $_SESSION['flash_message'] = 'පරිශීලකයා මකා දැමීමේදී දත්ත සමුදාය දෝෂයක් ඇති විය.';
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
