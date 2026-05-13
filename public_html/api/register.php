<?php
/* =========================================================
   ATWOPAT - MEMBER ID GENERATOR & REGISTRATION
   Format: APT.ST.1 (e.g., APT.ABJ.1)
   Updated: May 2026
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

// 2. Collect Form Data
$full_name = mysqli_real_escape_with_string($conn, $_POST['fullName']);
$email     = mysqli_real_escape_with_string($conn, $_POST['email']);
$phone     = mysqli_real_escape_with_string($conn, $_POST['phone']);
$state     = mysqli_real_escape_with_string($conn, $_POST['state']);
$role      = mysqli_real_escape_with_string($conn, $_POST['role']);
$lga       = mysqli_real_escape_with_string($conn, $_POST['lga']);
$address   = mysqli_real_escape_with_string($conn, $_POST['address']);
$exp       = mysqli_real_escape_with_string($conn, $_POST['exp']);
$pay_ref   = mysqli_real_escape_with_string($conn, $_POST['payment_ref']);

// 3. GENERATE UNIQUE MEMBER ID (APT.STATE.COUNT)
// Get 3-letter abbreviation of State (e.g., Abuja -> ABJ)
$state_code = strtoupper(substr($state, 0, 3));

// Count existing members from this state to determine the next number
$count_query = "SELECT COUNT(*) as total FROM members WHERE state LIKE '$state%'";
$result = $conn->query($count_query);
$row = $result->fetch_assoc();
$next_number = $row['total'] + 1;

$new_member_id = "APT." . $state_code . "." . $next_number;

// 4. Handle Passport Upload
$passport_name = "default-avatar.png";
if (isset($_FILES['passport']) && $_FILES['passport']['error'] === 0) {
    $target_dir = "../images/members/";
    $extension = pathinfo($_FILES["passport"]["name"], PATHINFO_EXTENSION);
    // Sanitize ID for filename (remove dots)
    $file_safe_id = str_replace('.', '_', $new_member_id);
    $passport_name = $file_safe_id . "." . $extension; 
    
    if (!move_uploaded_file($_FILES["passport"]["tmp_name"], $target_dir . $passport_name)) {
        $passport_name = "default-avatar.png"; // Fallback on failure
    }
}

// 5. Insert into Database
$sql = "INSERT INTO members (
            member_id, 
            full_name, 
            email, 
            phone, 
            gender, 
            dob, 
            state, 
            lga, 
            address, 
            role, 
            experience, 
            photo, 
            status, 
            payment_ref
        ) VALUES (
            '$new_member_id', 
            '$full_name', 
            '$email', 
            '$phone', 
            '".$_POST['gender']."', 
            '".$_POST['dob']."', 
            '$state', 
            '$lga', 
            '$address', 
            '$role', 
            '$exp', 
            '$passport_name', 
            'Active', 
            '$pay_ref'
        )";

if ($conn->query($sql) === TRUE) {
    echo json_encode([
        "status" => "success",
        "member_id" => $new_member_id,
        "message" => "Registration successful. Welcome to ATWOPAT!"
    ]);
} else {
    echo json_encode([
        "status" => "error", 
        "message" => "Database Error: " . $conn->error
    ]);
}

$conn->close();
?>
