<?php
/* =========================================================
   ATWOPAT - MEMBER LOGIN HANDLER
   Features: Secure ID/Email Verification & Session Start
   ========================================================= */

header('Content-Type: application/json');
session_start();

// 1. Database Connection
$host = "localhost";
$user = "your_db_user";
$pass = "your_db_password";
$dbname = "atwopat_db";

$conn = new mysqli($host, $user, $pass, $dbname);

if ($conn->connect_error) {
    echo json_encode(["status" => "error", "message" => "Connection failed"]);
    exit;
}

// 2. Collect Login Data
// We expect 'member_id' and 'email' from the login form
$member_id = mysqli_real_escape_string($conn, $_POST['memberID']);
$email = mysqli_real_escape_string($conn, $_POST['email']);

if (empty($member_id) || empty($email)) {
    echo json_encode(["status" => "error", "message" => "Please fill all fields"]);
    exit;
}

// 3. Query Database
$sql = "SELECT * FROM members WHERE member_id = '$member_id' AND email = '$email' LIMIT 1";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    $user_data = $result->fetch_assoc();

    // 4. Check Member Status
    if ($user_data['status'] === 'Suspended') {
        echo json_encode(["status" => "error", "message" => "Your account is suspended. Contact Admin."]);
        exit;
    }

    // 5. Success - Set Session Variables
    $_SESSION['logged_in'] = true;
    $_SESSION['user_id'] = $user_data['member_id'];
    $_SESSION['user_name'] = $user_data['full_name'];
    $_SESSION['user_role'] = $user_data['role'];

    echo json_encode([
        "status" => "success",
        "message" => "Login successful! Redirecting to dashboard...",
        "redirect" => "dashboard.html" // or verify.html
    ]);

} else {
    // No match found
    echo json_encode([
        "status" => "error",
        "message" => "Invalid Member ID or Email. Please try again."
    ]);
}

$conn->close();
?>
