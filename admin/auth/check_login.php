<?php
/**
 * Admin authentication check
 * Include this file at the top of all admin pages to ensure
 * only logged in users can access them
 */

// Start session if not already started
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Check if user is logged in
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    // Store original requested URL for potential redirect after login
    if (!isset($_SESSION['redirect_url']) && isset($_SERVER['REQUEST_URI'])) {
        $_SESSION['redirect_url'] = $_SERVER['REQUEST_URI'];
    }
    
    // Set a message
    $_SESSION['login_error'] = 'මෙම පිටුව බැලීමට කරුණාකර පළමුව පිවිසෙන්න.';
    
    // Redirect to login page
    header('Location: index.php');
    exit;
}

// Check if admin_id exists
if (!isset($_SESSION['admin_id']) || !isset($_SESSION['admin_username'])) {
    // Invalid session data, destroy session and redirect
    session_unset();
    session_destroy();
    
    // Start new session for error message
    session_start();
    $_SESSION['login_error'] = 'ඔබගේ සැසිය අවලංගු වී ඇත. කරුණාකර නැවත පිවිසෙන්න.';
    
    // Redirect to login page
    header('Location: index.php');
    exit;
}

// Check last activity time
if (isset($_SESSION['last_activity']) && (time() - $_SESSION['last_activity'] > 3600)) {
    // If last activity was more than 1 hour ago, destroy session (inactive timeout)
    session_unset();
    session_destroy();
    
    // Start new session for error message
    session_start();
    $_SESSION['login_error'] = 'අක්‍රීය කාලය ඉක්මවා ඇත. කරුණාකර නැවත පිවිසෙන්න.';
    
    // Redirect to login page
    header('Location: index.php');
    exit;
}

// Update last activity time
$_SESSION['last_activity'] = time();
