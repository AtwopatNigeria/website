<?php
/* =========================================================
   ATWOPAT - ACCOUNT SETTINGS
   Features: Privacy Settings, Session Info, and Account Status
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

// 3. Fetch latest data
$sql = "SELECT * FROM members WHERE member_id = '$member_id' LIMIT 1";
$result = $conn->query($sql);
$user = $result->fetch_assoc();

$update_msg = "";

// 4. Handle Settings Update (e.g., Notification preferences)
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // This is where you'd handle toggles for emails or SMS alerts
    $update_msg = "<div class='alert success'>Settings updated successfully!</div>";
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Settings | ATWOPAT</title>
    <link rel="stylesheet" href="../css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <style>
        .settings-container {
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
            color: white;
        }

        .settings-section {
            margin-bottom: 40px;
            padding-bottom: 20px;
            border-bottom: 1px solid rgba(255,255,255,0.1);
        }

        .settings-section h3 {
            margin-bottom: 20px;
            display: flex;
            align-items: center;
            gap: 10px;
            font-size: 18px;
        }

        .toggle-row {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 15px 0;
        }

        .status-badge {
            padding: 5px 15px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: bold;
            text-transform: uppercase;
        }

        .bg-pending { background: rgba(245, 158, 11, 0.2); color: #f59e0b; border: 1px solid #f59e0b; }
        .bg-active { background: rgba(34, 197, 94, 0.2); color: #22c55e; border: 1px solid #22c55e; }

        .btn-danger {
            background: rgba(239, 68, 68, 0.1);
            color: #ef4444;
            border: 1px solid #ef4444;
            padding: 10px 20px;
            border-radius: 10px;
            cursor: pointer;
            transition: 0.3s;
        }

        .btn-danger:hover { background: #ef4444; color: white; }

        @media (max-width: 850px) {
            .settings-container { grid-template-columns: 1fr; }
        }
    </style>
</head>
<body>

    <div class="settings-container animate-fade-in">
        <!-- SIDEBAR -->
        <aside>
            <?php include('sidebar.php'); ?>
        </aside>

        <!-- MAIN SETTINGS -->
        <main class="content-card">
            <h2>Account Settings</h2>
            <p style="margin-bottom: 30px; opacity: 0.7;">Manage your account security and preferences.</p>

            <?php echo $update_msg; ?>

            <!-- Account Status -->
            <div class="settings-section">
                <h3><i class="fa-solid fa-shield-check"></i> Account Verification</h3>
                <div class="toggle-row">
                    <div>
                        <p style="font-weight: bold;">Membership Status</p>
                        <p style="font-size: 13px; opacity: 0.6;">Your current standing within the association.</p>
                    </div>
                    <span class="status-badge bg-<?php echo strtolower($user['status']); ?>">
                        <?php echo $user['status']; ?>
                    </span>
                </div>
            </div>

            <!-- Security Section -->
            <div class="settings-section">
                <h3><i class="fa-solid fa-lock"></i> Security</h3>
                <div class="toggle-row">
                    <div>
                        <p style="font-weight: bold;">Login Credentials</p>
                        <p style="font-size: 13px; opacity: 0.6;">Your ID and Email are used for authentication.</p>
                    </div>
                    <p style="font-family: monospace;"><?php echo $user['member_id']; ?></p>
                </div>
                <div class="toggle-row">
                    <div>
                        <p style="font-weight: bold;">Two-Factor Authentication</p>
                        <p style="font-size: 13px; opacity: 0.6;">Secure your account with a mobile code (Coming Soon).</p>
                    </div>
                    <i class="fa-solid fa-toggle-off" style="font-size: 24px; opacity: 0.3;"></i>
                </div>
            </div>

            <!-- Preferences -->
            <div class="settings-section">
                <h3><i class="fa-solid fa-bell"></i> Notifications</h3>
                <form method="POST">
                    <div class="toggle-row">
                        <div>
                            <p style="font-weight: bold;">Email Notifications</p>
                            <p style="font-size: 13px; opacity: 0.6;">Receive association updates via email.</p>
                        </div>
                        <i class="fa-solid fa-toggle-on" style="font-size: 24px; color: #22c55e;"></i>
                    </div>
                    <button type="submit" class="btn-update" style="margin-top: 10px; background: #103d75; color: white; border:none; padding: 10px 20px; border-radius: 8px;">Save Preferences</button>
                </form>
            </div>

            <!-- Danger Zone -->
            <div class="settings-section" style="border-bottom: none;">
                <h3 style="color: #ef4444;"><i class="fa-solid fa-circle-exclamation"></i> Danger Zone</h3>
                <div class="toggle-row">
                    <div>
                        <p style="font-weight: bold;">Logout Everywhere</p>
                        <p style="font-size: 13px; opacity: 0.6;">Sign out of all active sessions on other devices.</p>
                    </div>
                    <button class="btn-danger" onclick="location.href='../api/logout.php'">Logout</button>
                </div>
            </div>
        </main>
    </div>

    <footer>
        &copy; 2026 ATWOPAT Member Portal.
    </footer>

</body>
</html>
