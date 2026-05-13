<?php
/* =========================================================
   ATWOPAT - DATABASE CONNECTION MANAGER
   Description: Centralized MySQLi connection handler
   Updated: May 2026
   ========================================================= */

// Database Credentials
// Replace these with your actual hosting details
$db_host = "localhost";
$db_user = "atwopat_db_user";
$db_pass = "atwopat_secure_password";
$db_name = "atwopat_main_db";

// Create Connection
$conn = new mysqli($db_host, $db_user, $db_pass, $db_name);

// Check for Connection Errors
if ($conn->connect_error) {
    // Log the error to a file (optional) and show a user-friendly message
    error_log("Database connection failed: " + $conn->connect_error);
    
    // Stop the script and return a JSON error (for APIs) or a message (for pages)
    header('Content-Type: application/json');
    die(json_encode([
        "status" => "error",
        "message" => "Database connection unavailable. Please try again later."
    ]));
}

// Set Character Set to UTF-8 (important for Nigerian names/addresses)
$conn->set_charset("utf8mb4");

/**
 * Clean data to prevent SQL Injection
 * Global helper function for your scripts
 */
function cleanInput($data, $conn) {
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);
    return mysqli_real_escape_string($conn, $data);
}

// $conn is now globally available for any file that includes this one
?>
