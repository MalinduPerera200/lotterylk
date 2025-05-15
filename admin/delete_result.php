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

// Get the result ID
$result_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

// Check if we have a valid ID
if ($result_id <= 0) {
    $_SESSION['flash_message'] = 'වලංගු ප්‍රතිඵල ID එකක් සපයන්න.';
    $_SESSION['flash_message_type'] = 'error';
    header('Location: manage_results.php');
    exit;
}

// Process deletion
if (isset($pdo) && $db_connected) {
    try {
        // First check if the result exists
        $check_stmt = $pdo->prepare('SELECT id FROM results WHERE id = :id');
        $check_stmt->bindParam(':id', $result_id, PDO::PARAM_INT);
        $check_stmt->execute();
        
        if ($check_stmt->rowCount() === 0) {
            // Result not found
            $_SESSION['flash_message'] = 'ප්‍රතිඵලය හමු නොවීය.';
            $_SESSION['flash_message_type'] = 'error';
        } else {
            // Delete the result
            $delete_stmt = $pdo->prepare('DELETE FROM results WHERE id = :id');
            $delete_stmt->bindParam(':id', $result_id, PDO::PARAM_INT);
            
            if ($delete_stmt->execute()) {
                // Deletion successful
                $_SESSION['flash_message'] = 'ප්‍රතිඵලය සාර්ථකව මකා දමන ලදී.';
                $_SESSION['flash_message_type'] = 'success';
            } else {
                // Deletion failed
                $_SESSION['flash_message'] = 'ප්‍රතිඵලය මකා දැමීමට නොහැකි විය.';
                $_SESSION['flash_message_type'] = 'error';
            }
        }
    } catch (PDOException $e) {
        error_log("Database error in delete_result.php: " . $e->getMessage(), 0);
        $_SESSION['flash_message'] = 'ප්‍රතිඵලය මකා දැමීමේදී දත්ත සමුදාය දෝෂයක් ඇති විය.';
        $_SESSION['flash_message_type'] = 'error';
    }
} else {
    // Database connection error
    $_SESSION['flash_message'] = 'දත්ත සමුදාය සම්බන්ධතාවය අසාර්ථක විය.';
    $_SESSION['flash_message_type'] = 'error';
}

// Redirect back to the manage results page
header('Location: manage_results.php');
exit;
?>
