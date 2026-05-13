<?php
/* =========================================================
   ATWOPAT - SECURE ADMIN LOGOUT
   Description: Destroys admin sessions and redirects to login
   Updated: May 2026
   ========================================================= */

// 1. Initialize the session
session_start();

// 2. Clear all session data
$_SESSION = array();

// 3. Expire the session cookie in the browser
if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(session_name(), '', time() - 42000,
        $params["path"], $params["domain"],
        $params["secure"], $params["httponly"]
    );
}

// 4. Specifically destroy the server-side session
session_destroy();

// 5. Redirect to the landing page or login page
// We go up one level to the main public_html root
header("Location: ../login.html?status=admin_signed_out");
exit;
