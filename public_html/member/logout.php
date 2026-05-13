<?php
/* =========================================================
   ATWOPAT - SECURE LOGOUT HANDLER
   Description: Destroys session, clears cookies, & redirects
   Updated: May 2026
   ========================================================= */

// 1. Initialize the session
session_start();

// 2. Unset all session variables
$_SESSION = array();

// 3. Destroy the session cookie in the browser
// This is a critical security step to prevent session fixation/hijacking
if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(session_name(), '', time() - 42000,
        $params["path"], $params["domain"],
        $params["secure"], $params["httponly"]
    );
}

// 4. Destroy the session on the server
session_destroy();

// 5. Redirect to the login page
// We add a status parameter so the login page can show a "Logged out" message
header("Location: ../login.html?status=loggedout");
exit;
