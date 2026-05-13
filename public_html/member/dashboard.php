<?php
/* =========================================================
   ATWOPAT - MEMBER DASHBOARD
   Features: Session Protection, Digital ID Display, & Status
   Updated: May 2026
   ========================================================= */

// 1. Protect the page - Ensure user is logged in
require_once('../api/session.php'); 

// 2. Database Connection to get fresh data
$host = "localhost";
$user = "your_db_user";
$pass = "your_db_password";
$dbname = "atwopat_db";

$conn = new mysqli($host, $user, $pass, $dbname);
$member_id = $_SESSION['user_id'];

// Fetch latest member details
$sql = "SELECT * FROM members WHERE member_id = '$member_id' LIMIT 1";
$result = $conn->query($sql);
$user = $result->fetch_assoc();

// Determine status color
$status_color = ($user['status'] === 'Active') ? '#22c55e' : (($user['status'] === 'Pending') ? '#f59e0b' : '#d9534f');
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard | ATWOPAT Member Portal</title>
    <link rel="stylesheet" href="../css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <style>
        .dashboard-container {
            max-width: 1000px;
            margin: 40px auto;
            padding: 20px;
            display: grid;
            grid-template-columns: 350px 1fr;
            gap: 30px;
        }
        @media (max-width: 850px) {
            .dashboard-container { grid-template-columns: 1fr; }
        }
        .welcome-box {
            text-align: left;
            background: rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(10px);
            padding: 30px;
            border-radius: 25px;
            border: 1px solid rgba(255, 255, 255, 0.2);
        }
        .nav-menu {
            margin-top: 30px;
            display: flex;
            flex-direction: column;
            gap: 15px;
        }
        .nav-item {
            padding: 15px 20px;
            background: rgba(255, 255, 255, 0.05);
            border-radius: 12px;
            text-decoration: none;
            color: #fff;
            transition: 0.3s;
            display: flex;
            align-items: center;
            gap: 12px;
        }
        .nav-item:hover {
            background: rgba(255, 255, 255, 0.2);
            transform: translateX(5px);
        }
        .logout-btn { color: #ff7675; }
    </style>
</head>
<body>

    <div class="dashboard-container animate-fade-in">
        
        <!-- SIDEBAR: Digital ID Card -->
        <aside>
            <div class="card" style="background: rgba(255, 255, 255, 0.9); color: #103d75; margin: 0; width: 100%;">
                <h3 style="margin-bottom: 15px; font-size: 16px;">OFFICIAL DIGITAL ID</h3>
                
                <div style="width: 130px; height: 130px; margin: 0 auto 20px; border-radius: 20px; overflow: hidden; border: 4px solid #fff; box-shadow: 0 8px 20px rgba(0,0,0,0.1);">
                    <img src="../images/members/<?php echo $user['photo']; ?>" style="width: 100%; height: 100%; object-fit: cover;" onerror="this.src='../images/members/default-avatar.png';">
                </div>

                <div style="text-align: left; font-size: 14px; line-height: 2;">
                    <p><b>Name:</b> <?php echo $user['full_name']; ?></p>
                    <p><b>ID:</b> <span style="font-family: monospace; font-weight: bold;"><?php echo $user['member_id']; ?></span></p>
                    <p><b>Role:</b> <?php echo $user['role']; ?></p>
                    <p><b>Status:</b> <span style="color: <?php echo $status_color; ?>; font-weight: bold;"><?php echo $user['status']; ?></span></p>
                </div>

                <div style="margin-top: 20px;">
                    <!-- Use dynamic QR based on Member ID -->
                    <img src="https://api.qrserver.com/v1/create-qr-code/?size=100x100&data=<?php echo $user['member_id']; ?>" style="width: 100px; background: white; padding: 5px; border-radius: 10px;">
                </div>
            </div>
        </aside>

        <!-- MAIN CONTENT -->
        <main>
            <div class="welcome-box">
                <h1 style="font-size: 28px;">Welcome back, <?php echo explode(' ', $user['full_name'])[0]; ?>! 👋</h1>
                <p style="color: #cbd5e1; margin-top: 10px;">This is your member portal. From here you can manage your profile and view announcements.</p>
                
                <div class="nav-menu">
                    <a href="profile.php" class="nav-item">
                        <i class="fa-solid fa-user-gear"></i> Update My Profile
                    </a>
                    <a href="announcements.php" class="nav-item">
                        <i class="fa-solid fa-bullhorn"></i> View Announcements
                    </a>
                    <a href="id-card-print.php" class="nav-item">
                        <i class="fa-solid fa-print"></i> Print ID Card (PDF)
                    </a>
                    <a href="../api/logout.php" class="nav-item logout-btn">
                        <i class="fa-solid fa-right-from-bracket"></i> Logout Account
                    </a>
                </div>
            </div>

            <div class="info" style="margin-top: 30px; width: 100%;">
                <h4 class="info-title">Membership Notice</h4>
                <ul>
                    <li>Your membership is currently <strong><?php echo $user['status']; ?></strong>.</li>
                    <li>If "Pending," please wait 24-48 hours for Admin verification.</li>
                    <li>Ensure your contact details are always up to date for official communications.</li>
                </ul>
            </div>
        </main>

    </div>

    <footer>
        &copy; 2026 ATWOPAT Official Member Portal. All Rights Reserved.
    </footer>

</body>
</html>
