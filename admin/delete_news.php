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

// Get the news ID
$news_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

// Check if we have a valid ID
if ($news_id <= 0) {
    $_SESSION['flash_message'] = 'වලංගු පුවත් ID එකක් සපයන්න.';
    $_SESSION['flash_message_type'] = 'error';
    header('Location: manage_news.php');
    exit;
}

// Process deletion
if (isset($pdo) && $db_connected) {
    try {
        // First check if the news article exists and get the image path
        $check_stmt = $pdo->prepare('SELECT id, image_path FROM news_articles WHERE id = :id');
        $check_stmt->bindParam(':id', $news_id, PDO::PARAM_INT);
        $check_stmt->execute();
        $news = $check_stmt->fetch();
        
        if (!$news) {
            // News not found
            $_SESSION['flash_message'] = 'පුවත හමු නොවීය.';
            $_SESSION['flash_message_type'] = 'error';
        } else {
            // Delete associated image if exists
            if (!empty($news['image_path'])) {
                $image_path = $_SERVER['DOCUMENT_ROOT'] . $news['image_path'];
                if (file_exists($image_path)) {
                    @unlink($image_path);
                }
            }
            
            // Delete the news article
            $delete_stmt = $pdo->prepare('DELETE FROM news_articles WHERE id = :id');
            $delete_stmt->bindParam(':id', $news_id, PDO::PARAM_INT);
            
            if ($delete_stmt->execute()) {
                // Deletion successful
                $_SESSION['flash_message'] = 'පුවත සාර්ථකව මකා දමන ලදී.';
                $_SESSION['flash_message_type'] = 'success';
            } else {
                // Deletion failed
                $_SESSION['flash_message'] = 'පුවත මකා දැමීමට නොහැකි විය.';
                $_SESSION['flash_message_type'] = 'error';
            }
        }
    } catch (PDOException $e) {
        error_log("Database error in delete_news.php: " . $e->getMessage(), 0);
        $_SESSION['flash_message'] = 'පුවත මකා දැමීමේදී දත්ත සමුදාය දෝෂයක් ඇති විය.';
        $_SESSION['flash_message_type'] = 'error';
    }
} else {
    // Database connection error
    $_SESSION['flash_message'] = 'දත්ත සමුදාය සම්බන්ධතාවය අසාර්ථක විය.';
    $_SESSION['flash_message_type'] = 'error';
}

// Redirect back to the manage news page
header('Location: manage_news.php');
exit;
?>
