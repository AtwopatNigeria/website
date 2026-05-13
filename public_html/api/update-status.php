<?php
/* =========================================================
   ATWOPAT - MEMBER STATUS MANAGER
   Description: Updates Active/Inactive/Suspended status
   ========================================================= */

header('Content-Type: application/json');

// 1. Database Connection
$host = "localhost";
$user = "your_db_user";
$pass = "your_db_password";
$dbname = "atwopat_db";

$conn = new mysqli($host, $user, $pass, $dbname);

if ($conn->connect_error) {
    echo json_encode(["status" => "error", "message" => "Database connection failed"]);
    exit;
}

// 2. Validate Inputs
// Expecting member_id (e.g., APT.ABJ.1) and new_status
if (!isset($_POST['member_id']) || !isset($_POST['new_status'])) {
    echo json_encode(["status" => "error", "message" => "Missing required parameters"]);
    exit;
}

$member_id = mysqli_real_escape_string($conn, $_POST['member_id']);
$new_status = mysqli_real_escape_string($conn, $_POST['new_status']);

// Define allowed statuses to prevent database corruption
$allowed_statuses = ['Active', 'pending', 'Suspended', 'Expired'];

if (!in_array($new_status, $allowed_statuses)) {
    echo json_encode(["status" => "error", "message" => "Invalid status type"]);
    exit;
}

// 3. Update the Database
$sql = "UPDATE members SET status = '$new_status' WHERE member_id = '$member_id'";

if ($conn->query($sql) === TRUE) {
    if ($conn->affected_rows > 0) {
        echo json_encode([
            "status" => "success",
            "message" => "Member $member_id is now $new_status",
            "updated_id" => $member_id,
            "current_status" => $new_status
        ]);
    } else {
        echo json_encode([
            "status" => "error",
            "message" => "Member ID not found or no change made."
        ]);
    }
} else {
    echo json_encode([
        "status" => "error",
        "message" => "Query failed: " . $conn->error
    ]);
}

$conn->close();
?>
