<?php
/* =========================================================
   ATWOPAT - SESSION MANAGER
   Description: Protects private pages from unauthorized access
   Updated: May 2026
   ========================================================= */

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

/**
 * Function to check if a user is currently logged in.
 * If not logged in, it redirects to the login page.
 */
function checkSession() {
    // Check if the 'logged_in' flag we set in login.php exists
    if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
        
        // Clear any partial session data
        session_unset();
        session_destroy();
        
        // Redirect to login page
        // Note: Adjust the path if your login file is in a different folder
        header("Location: login.html?error=unauthorized");
        exit;
    }
}

/**
 * Optional: Function to check for Admin-only access
 */
function restrictToAdmin() {
    if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'Admin') {
        header("Location: dashboard.html?error=restricted");
        exit;
    }
}

// Automatically check session when this file is included
checkSession();
?>
