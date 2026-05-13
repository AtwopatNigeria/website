<?php
/* =========================================================
   ATWOPAT - MEMBER PROFILE MANAGEMENT
   Features: View Details, Update Info, Session Protected
   Updated: May 2026
   ========================================================= */

// 1. Protect the page
require_once('../api/session.php'); 

// 2. Database Connection
$host = "localhost";
$user = "your_db_user";
$pass = "your_db_password";
$dbname = "atwopat_db";

$conn = new mysqli($host, $user, $pass, $dbname);
$member_id = $_SESSION['user_id'];

// 3. Handle Form Update
$update_msg = "";
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $new_phone = mysqli_real_escape_string($conn, $_POST['phone']);
    $new_address = mysqli_real_escape_string($conn, $_POST['address']);
    $new_lga = mysqli_real_escape_string($conn, $_POST['lga']);

    $update_sql = "UPDATE members SET phone='$new_phone', address='$new_address', lga='$new_lga' WHERE member_id='$member_id'";
    
    if ($conn->query($update_sql)) {
        $update_msg = "<div class='alert success'>Profile updated successfully!</div>";
    } else {
        $update_msg = "<div class='alert error'>Update failed. Please try again.</div>";
    }
}

// 4. Fetch latest data to display
$sql = "SELECT * FROM members WHERE member_id = '$member_id' LIMIT 1";
$result = $conn->query($sql);
$user = $result->fetch_assoc();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Profile | ATWOPAT</title>
    <link rel="stylesheet" href="../css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <style>
        .profile-container {
            max-width: 1100px;
            margin: 40px auto;
            display: grid;
            grid-template-columns: 300px 1fr;
            gap: 30px;
            padding: 0 20px;
        }

        .content-card {
            background: rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(15px);
            padding: 40px;
            border-radius: 25px;
            border: 1px solid rgba(255, 255, 255, 0.2);
            text-align: left;
        }

        .form-group { margin-bottom: 20px; }
        .form-group label { display: block; margin-bottom: 8px; font-size: 14px; color: #cbd5e1; }
        
        .form-control {
            width: 100%;
            padding: 12px 15px;
            border-radius: 10px;
            border: 1px solid rgba(255,255,255,0.1);
            background: rgba(255,255,255,0.05);
            color: white;
            font-size: 15px;
        }

        .form-control:disabled {
            background: rgba(0,0,0,0.2);
            color: #94a3b8;
            cursor: not-allowed;
        }

        .alert { padding: 15px; border-radius: 10px; margin-bottom: 20px; font-size: 14px; }
        .success { background: rgba(34, 197, 94, 0.2); border: 1px solid #22c55e; color: #22c55e; }
        .error { background: rgba(239, 68, 68, 0.2); border: 1px solid #ef4444; color: #ef4444; }

        .btn-update {
            background: #103d75;
            color: white;
            padding: 12px 25px;
            border: none;
            border-radius: 10px;
            cursor: pointer;
            font-weight: bold;
            transition: 0.3s;
        }
        .btn-update:hover { background: #1e4b8a; transform: translateY(-2px); }

        @media (max-width: 850px) {
            .profile-container { grid-template-columns: 1fr; }
        }
    </style>
</head>
<body>

    <div class="profile-container animate-fade-in">
        <!-- SIDEBAR -->
        <aside>
            <?php include('sidebar.php'); ?>
        </aside>

        <!-- MAIN FORM -->
        <main class="content-card">
            <h2>Account Settings</h2>
            <p style="margin-bottom: 30px; opacity: 0.7;">Keep your contact information up to date.</p>

            <?php echo $update_msg; ?>

            <form action="profile.php" method="POST">
                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px;">
                    <!-- Read Only Info -->
                    <div class="form-group">
                        <label>Member ID</label>
                        <input type="text" class="form-control" value="<?php echo $user['member_id']; ?>" disabled>
                    </div>
                    <div class="form-group">
                        <label>Registration Email</label>
                        <input type="text" class="form-control" value="<?php echo $user['email']; ?>" disabled>
                    </div>

                    <!-- Editable Info -->
                    <div class="form-group">
                        <label>Full Name</label>
                        <input type="text" class="form-control" value="<?php echo $user['full_name']; ?>" disabled>
                    </div>
                    <div class="form-group">
                        <label>Phone Number</label>
                        <input type="text" name="phone" class="form-control" value="<?php echo $user['phone']; ?>" required>
                    </div>

                    <div class="form-group">
                        <label>State of Residence</label>
                        <input type="text" class="form-control" value="<?php echo $user['state']; ?>" disabled>
                    </div>
                    <div class="form-group">
                        <label>Local Govt (LGA)</label>
                        <input type="text" name="lga" class="form-control" value="<?php echo $user['lga']; ?>" required>
                    </div>
                </div>

                <div class="form-group">
                    <label>Residential / Office Address</label>
                    <input type="text" name="address" class="form-control" value="<?php echo $user['address']; ?>" required>
                </div>

                <div class="form-group" style="margin-top: 10px;">
                    <button type="submit" class="btn-update">
                        <i class="fa-solid fa-floppy-disk"></i> Save Changes
                    </button>
                </div>
            </form>

            <hr style="border: 0; border-top: 1px solid rgba(255,255,255,0.1); margin: 40px 0;">
            
            <h3>Membership Details</h3>
            <div style="display: flex; gap: 40px; margin-top: 20px; opacity: 0.8; font-size: 14px;">
                <p><strong>Role:</strong> <?php echo $user['role']; ?></p>
                <p><strong>Experience:</strong> <?php echo $user['experience']; ?> Years</p>
                <p><strong>Joined:</strong> <?php echo date('M d, Y', strtotime($user['created_at'] ?? '2026-01-01')); ?></p>
            </div>
        </main>
    </div>

    <footer>
        &copy; 2026 ATWOPAT Member Portal.
    </footer>

</body>
</html>
