<?php
/* =========================================================
   ATWOPAT - PROFILE PHOTO HANDLER
   Features: Auto-resizing, naming by Member ID, and security
   ========================================================= */

header('Content-Type: application/json');

// 1. Configuration
$target_dir = "../images/members/";
$allowed_types = ['jpg', 'jpeg', 'png'];
$max_size = 2 * 1024 * 1024; // 2MB Limit

// Create directory if it doesn't exist
if (!file_exists($target_dir)) {
    mkdir($target_dir, 0777, true);
}

// 2. Check if file and member ID are provided
if (!isset($_FILES['passport']) || !isset($_POST['member_id'])) {
    echo json_encode(["status" => "error", "message" => "Missing photo or Member ID"]);
    exit;
}

$member_id = $_POST['member_id'];
$file = $_FILES['passport'];

// 3. Validate File Extension
$ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
if (!in_array($ext, $allowed_types)) {
    echo json_encode(["status" => "error", "message" => "Invalid file type. Use JPG or PNG."]);
    exit;
}

// 4. Validate File Size
if ($file['size'] > $max_size) {
    echo json_encode(["status" => "error", "message" => "File too large. Max 2MB allowed."]);
    exit;
}

// 5. Generate Safe Filename
// We replace dots in APT.ABJ.1 to underscores for file system compatibility: APT_ABJ_1.jpg
$safe_name = str_replace('.', '_', $member_id) . "." . $ext;
$target_file = $target_dir . $safe_name;

// 6. Execute Upload
if (move_uploaded_file($file['tmp_name'], $target_file)) {
    
    // Optional: Update the database with the new filename
    $host = "localhost";
    $user = "your_db_user";
    $pass = "your_db_password";
    $dbname = "atwopat_db";
    
    $conn = new mysqli($host, $user, $pass, $dbname);
    if (!$conn->connect_error) {
        $stmt = $conn->prepare("UPDATE members SET photo = ? WHERE member_id = ?");
        $stmt->bind_param("ss", $safe_name, $member_id);
        $stmt->execute();
        $stmt->close();
        $conn->close();
    }

    echo json_encode([
        "status" => "success", 
        "message" => "Photo uploaded successfully",
        "file_name" => $safe_name
    ]);
} else {
    echo json_encode(["status" => "error", "message" => "Failed to save file to server."]);
}
?>
