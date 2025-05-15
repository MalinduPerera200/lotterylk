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
    header('Location: manage_lottery_types.php');
    exit;
}

// Get lottery ID from form
$lottery_id = isset($_POST['lottery_id']) ? (int)$_POST['lottery_id'] : 0;

// Validate lottery ID
if ($lottery_id <= 0) {
    $_SESSION['flash_message'] = 'වලංගු ලොතරැයි ID එකක් සපයන්න.';
    $_SESSION['flash_message_type'] = 'error';
    header('Location: manage_lottery_types.php');
    exit;
}

// Process deletion
if (isset($pdo) && $db_connected) {
    try {
        // Start transaction
        $pdo->beginTransaction();
        
        // Check if lottery exists
        $check_stmt = $pdo->prepare('SELECT name FROM lotteries WHERE id = :id');
        $check_stmt->bindParam(':id', $lottery_id, PDO::PARAM_INT);
        $check_stmt->execute();
        
        $lottery = $check_stmt->fetch();
        
        if (!$lottery) {
            // Lottery not found
            $pdo->rollBack();
            $_SESSION['flash_message'] = 'ලොතරැයිය හමු නොවීය.';
            $_SESSION['flash_message_type'] = 'error';
        } else {
            // Check if there are any results associated with this lottery
            $check_results_stmt = $pdo->prepare('SELECT COUNT(*) FROM results WHERE lottery_id = :lottery_id');
            $check_results_stmt->bindParam(':lottery_id', $lottery_id, PDO::PARAM_INT);
            $check_results_stmt->execute();
            
            $results_count = $check_results_stmt->fetchColumn();
            
            if ($results_count > 0) {
                // Delete all associated results first
                $delete_results_stmt = $pdo->prepare('DELETE FROM results WHERE lottery_id = :lottery_id');
                $delete_results_stmt->bindParam(':lottery_id', $lottery_id, PDO::PARAM_INT);
                $delete_results_stmt->execute();
            }
            
            // Now delete the lottery
            $delete_stmt = $pdo->prepare('DELETE FROM lotteries WHERE id = :id');
            $delete_stmt->bindParam(':id', $lottery_id, PDO::PARAM_INT);
            
            if ($delete_stmt->execute()) {
                // Commit transaction
                $pdo->commit();
                
                // Success
                $_SESSION['flash_message'] = $lottery['name'] . ' ලොතරැයිය සහ එහි සියලුම ප්‍රතිඵල සාර්ථකව මකා දමන ලදී.';
                $_SESSION['flash_message_type'] = 'success';
            } else {
                // Deletion failed
                $pdo->rollBack();
                $_SESSION['flash_message'] = 'ලොතරැයිය මකා දැමීමට අසමත් විය.';
                $_SESSION['flash_message_type'] = 'error';
            }
        }
    } catch (PDOException $e) {
        // Roll back transaction on error
        $pdo->rollBack();
        
        error_log("Database error in delete_lottery.php: " . $e->getMessage(), 0);
        $_SESSION['flash_message'] = 'ලොතරැයිය මකා දැමීමේදී දත්ත සමුදාය දෝෂයක් ඇති විය.';
        $_SESSION['flash_message_type'] = 'error';
    }
} else {
    // Database connection error
    $_SESSION['flash_message'] = 'දත්ත සමුදාය සම්බන්ධතාවය අසාර්ථක විය.';
    $_SESSION['flash_message_type'] = 'error';
}

// Redirect back to manage lottery types page
header('Location: manage_lottery_types.php');
exit;
?>
