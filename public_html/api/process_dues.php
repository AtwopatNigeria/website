<?php
require_once('../database/connection.php');
session_start();

if (!isset($_SESSION['user_id'])) {
    die("Unauthorized access.");
}

$ref = cleanInput($_GET['ref'], $conn);
$member_id = $_SESSION['member_id'];

// In a live environment, you would verify the $ref with Paystack's API here.
// For now, we update the member's last payment date to today.

$today = date('Y-m-d');
$sql = "UPDATE members SET last_dues_payment = '$today' WHERE member_id = '$member_id'";

if ($conn->query($sql)) {
    // Redirect back to dashboard with success
    header("Location: ../member/dashboard.php?payment=success");
} else {
    header("Location: ../member/dashboard.php?payment=failed");
}
exit;
