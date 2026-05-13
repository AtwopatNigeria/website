<?php
/* =========================================================
   ATWOPAT - ADMIN VERIFICATION CENTER
   Features: Member Search, Status Management, Data Override
   Updated: May 2026
   ========================================================= */

// 1. Connection & Session Protection
require_once('../database/connection.php');
// Assuming you have an admin session check
session_start();
if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'Admin') {
    header("Location: ../login.html?error=unauthorized");
    exit;
}

$update_msg = "";

// 2. Handle Status Update Request
if (isset($_POST['update_status'])) {
    $target_id = cleanInput($_POST['target_id'], $conn);
    $new_status = cleanInput($_POST['new_status'], $conn);
    
    $update_sql = "UPDATE members SET status = '$new_status' WHERE member_id = '$target_id'";
    if ($conn->query($update_sql)) {
        $update_msg = "<div class='alert success'>Member $target_id updated to $new_status.</div>";
    }
}

// 3. Fetch Member Details if searched
$member = null;
if (isset($_GET['search_id'])) {
    $search_id = cleanInput($_GET['search_id'], $conn);
    $res = $conn->query("SELECT * FROM members WHERE member_id = '$search_id'");
    $member = $res->fetch_assoc();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Admin Verification | ATWOPAT</title>
    <link rel="stylesheet" href="../css/dashboard.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <style>
        .admin-container { max-width: 1000px; margin: 50px auto; padding: 20px; }
        .search-bar { display: flex; gap: 10px; margin-bottom: 30px; }
        .member-profile-card { 
            background: white; 
            color: #103d75; 
            border-radius: 20px; 
            padding: 30px; 
            display: grid; 
            grid-template-columns: 200px 1fr; 
            gap: 30px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.2);
        }
        .info-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 15px; }
        .label { font-size: 12px; color: #64748b; font-weight: bold; text-transform: uppercase; }
        .val { font-size: 16px; margin-bottom: 10px; }
        .status-pill { padding: 5px 12px; border-radius: 50px; font-size: 12px; font-weight: bold; }
        .Active { background: #dcfce7; color: #166534; }
        .Pending { background: #fef9c3; color: #854d0e; }
        .Suspended { background: #fee2e2; color: #991b1b; }
    </style>
</head>
<body style="background: #103d75;">

    <div class="admin-container animate-fade-in">
        <h1 style="color: white; margin-bottom: 10px;">Verification Portal</h1>
        <p style="color: #cbd5e1; margin-bottom: 30px;">Search and manage member approval status.</p>

        <?php echo $update_msg; ?>

        <!-- Search Form -->
        <form action="" method="GET" class="search-bar">
            <input type="text" name="search_id" placeholder="Enter Member ID (e.g. APT.LA.001)" 
                   class="btn-primary" style="background: white; color: black; flex: 1; text-align: left;" 
                   value="<?php echo $_GET['search_id'] ?? ''; ?>" required>
            <button type="submit" class="btn-primary">Search Member</button>
        </form>

        <?php if ($member): ?>
            <div class="member-profile-card">
                <div style="text-align: center;">
                    <img src="../images/members/<?php echo $member['photo']; ?>" 
                         style="width: 100%; border-radius: 15px; border: 3px solid #103d75;"
                         onerror="this.src='../images/members/default-avatar.png';">
                    
                    <div style="margin-top: 20px;">
                        <span class="status-pill <?php echo $member['status']; ?>">
                            CURRENT: <?php echo $member['status']; ?>
                        </span>
                    </div>
                </div>

                <div>
                    <div class="info-grid">
                        <div>
                            <p class="label">Full Name</p>
                            <p class="val"><?php echo $member['full_name']; ?></p>
                        </div>
                        <div>
                            <p class="label">Member ID</p>
                            <p class="val" style="font-family: monospace;"><?php echo $member['member_id']; ?></p>
                        </div>
                        <div>
                            <p class="label">Email Address</p>
                            <p class="val"><?php echo $member['email']; ?></p>
                        </div>
                        <div>
                            <p class="label">Phone</p>
                            <p class="val"><?php echo $member['phone']; ?></p>
                        </div>
                    </div>

                    <hr style="margin: 20px 0; opacity: 0.1;">

                    <form action="" method="POST" style="display: flex; align-items: center; gap: 15px;">
                        <input type="hidden" name="target_id" value="<?php echo $member['member_id']; ?>">
                        <label class="label">Change Status To:</label>
                        <select name="new_status" class="btn-primary" style="background: #f1f5f9; color: #103d75; padding: 5px 15px;">
                            <option value="Pending" <?php if($member['status']=='Pending') echo 'selected'; ?>>Pending</option>
                            <option value="Active" <?php if($member['status']=='Active') echo 'selected'; ?>>Active</option>
                            <option value="Suspended" <?php if($member['status']=='Suspended') echo 'selected'; ?>>Suspended</option>
                        </select>
                        <button type="submit" name="update_status" class="btn-primary" style="background: #103d75;">Update Now</button>
                    </form>
                </div>
            </div>
        <?php elseif(isset($_GET['search_id'])): ?>
            <p style="color: #f87171; text-align: center;">No member found with that ID.</p>
        <?php endif; ?>

        <div style="margin-top: 40px; text-align: center;">
            <a href="dashboard.php" style="color: white; text-decoration: none; font-size: 14px;">
                <i class="fa-solid fa-arrow-left"></i> Back to Admin Dashboard
            </a>
        </div>
    </div>

</body>
</html>
