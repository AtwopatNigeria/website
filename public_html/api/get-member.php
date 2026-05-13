<?php
/* =========================================================
   ATWOPAT - DATA RETRIEVAL API (get-member.php)
   Description: Fetches member details for verification
   Updated: May 2026 
   ========================================================= */

header('Content-Type: application/json');

// 1. Database Connection
$host = "localhost";
$user = "your_db_user";
$pass = "your_db_password";
$dbname = "atwopat_db";

$conn = new mysqli($host, $user, $pass, $dbname);

// Check connection
if ($conn->connect_error) {
    echo json_encode(["status" => "error", "message" => "Database connection failed"]);
    exit;
}

// 2. Validate ID Input
if (!isset($_GET['id']) || empty($_GET['id'])) {
    echo json_encode(["status" => "error", "message" => "No Member ID provided"]);
    exit;
}

$member_id = mysqli_real_escape_string($conn, $_GET['id']);

// 3. Query the Database
// We fetch everything needed for the frosted glass card
$sql = "SELECT 
            member_id, 
            full_name, 
            state, 
            role, 
            status, 
            photo, 
            expiry_date,
            qr_code_url 
        FROM members 
        WHERE member_id = '$member_id' 
        LIMIT 1";

$result = $conn->query($sql);

if ($result->num_rows > 0) {
    $member = $result->fetch_assoc();
    
    // 4. Return Success Response
    echo json_encode([
        "status" => "success",
        "member" => [
            "member_id" => $member['member_id'],
            "full_name" => $member['full_name'],
            "state"     => $member['state'],
            "position"  => $member['role'],
            "status"    => $member['status'],
            "photo"     => $member['photo'],
            "expiry"    => $member['expiry_date'] ?? '2028-12-31', // Default if null
            "qr"        => $member['qr_code_url'] ?? ''
        ]
    ]);
} else {
    // 5. Member Not Found
    echo json_encode([
        "status" => "NOT_FOUND",
        "message" => "Member with ID $member_id does not exist."
    ]);
}

$conn->close();
?>
