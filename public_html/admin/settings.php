<?php
/* =========================================================
   ATWOPAT - SYSTEM SETTINGS (ADMIN ONLY)
   Features: API Key Management, Fee Configuration, Security
   Updated: May 2026
   ========================================================= */

require_once('../database/connection.php');
session_start();

// 1. Security Check
if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'Admin') {
    header("Location: ../login.html?error=unauthorized");
    exit;
}

// Helper function for sanitization
function cleanInput($data, $conn) {
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);
    return mysqli_real_escape_string($conn, $data);
}

$update_msg = "";

// 2. Handle Settings Update
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $reg_fee      = cleanInput($_POST['reg_fee'], $conn);
    $monthly_due  = cleanInput($_POST['monthly_due'], $conn);
    $paystack_pk  = cleanInput($_POST['paystack_public'], $conn);
    $paystack_sk  = cleanInput($_POST['paystack_secret'] ?? '', $conn); // Fallback if missing
    $admin_email  = cleanInput($_POST['admin_email'], $conn);

    // Using Prepared Statements for Security
    $stmt = $conn->prepare("UPDATE system_settings SET 
                            registration_fee = ?, 
                            monthly_dues_amount = ?, 
                            paystack_public_key = ?, 
                            paystack_secret_key = ?, 
                            admin_notification_email = ? 
                            WHERE id = 1");
    
    $stmt->bind_param("ddsss", $reg_fee, $monthly_due, $paystack_pk, $paystack_sk, $admin_email);

    if ($stmt->execute()) {
        $update_msg = "<div class='alert alert-success' style='color: #10b981; background: rgba(16,185,129,0.1); padding: 15px; border-radius: 8px; margin-bottom: 20px;'>
                        <i class='fa-solid fa-circle-check'></i> System settings updated successfully!
                      </div>";
    } else {
        $update_msg = "<div class='alert alert-danger' style='color: #ef4444;'>Error updating settings.</div>";
    }
    $stmt->close();
}

// 3. Fetch Current System Settings
$settings_res = $conn->query("SELECT * FROM system_settings WHERE id = 1");
$sys = $settings_res->fetch_assoc();

include('header.php'); 
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>System Settings | ATWOPAT Admin</title>
    <link rel="stylesheet" href="../css/dashboard.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <style>
        .settings-grid { display: grid; grid-template-columns: 280px 1fr; gap: 30px; max-width: 1300px; margin: 40px auto; padding: 0 20px; }
        .config-card { background: rgba(255,255,255,0.05); backdrop-filter: blur(10px); border: 1px solid rgba(255,255,255,0.1); padding: 30px; border-radius: 20px; color: white; }
        .form-section { margin-bottom: 30px; padding-bottom: 20px; border-bottom: 1px solid rgba(255,255,255,0.1); }
        .form-label { display: block; margin-bottom: 10px; font-size: 13px; color: #f59e0b; font-weight: bold; }
        .form-input { width: 100%; background: rgba(0,0,0,0.2); border: 1px solid rgba(255,255,255,0.2); color: white; padding: 12px; border-radius: 8px; margin-bottom: 15px; box-sizing: border-box; }
        .btn-primary { background: #f59e0b; color: #fff; padding: 12px 25px; border: none; border-radius: 8px; cursor: pointer; font-weight: bold; transition: 0.3s; }
        .btn-primary:hover { background: #d97706; transform: translateY(-2px); }
    </style>
</head>
<body style="background: #0f172a; font-family: 'Inter', sans-serif;">

    <div class="settings-grid">
        <aside>
            <?php include('sidebar.php'); ?>
        </aside>

        <main>
            <div class="config-card">
                <h2><i class="fa-solid fa-gears"></i> Global System Configuration</h2>
                <p style="opacity: 0.6; margin-bottom: 30px;">Manage API keys, payment amounts, and administrative contacts.</p>

                <?php echo $update_msg; ?>

                <form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="POST">
                    
                    <!-- Contact & Financials -->
                    <div class="form-section">
                        <h3><i class="fa-solid fa-credit-card"></i> Financial Settings</h3>
                        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-top: 15px;">
                            <div>
                                <label class="form-label">Registration Fee (₦)</label>
                                <input type="number" name="reg_fee" class="form-input" value="<?php echo $sys['registration_fee']; ?>" required>
                            </div>
                            <div>
                                <label class="form-label">Monthly Dues (₦)</label>
                                <input type="number" name="monthly_due" class="form-input" value="<?php echo $sys['monthly_dues_amount']; ?>" required>
                            </div>
                        </div>
                        <label class="form-label">Super Admin Notification Email</label>
                        <input type="email" name="admin_email" class="form-input" value="<?php echo $sys['admin_notification_email']; ?>" required>
                    </div>

                    <!-- Paystack Integration -->
                    <div class="form-section">
                        <h3><i class="fa-solid fa-key"></i> Paystack API Integration</h3>
                        <p style="font-size: 12px; color: #94a3b8; margin-bottom: 15px;">Use Test Keys for development and Live Keys for production.</p>
                        
                        <label class="form-label">Public Key</label>
                        <input type="text" name="paystack_public" class="form-input" value="<?php echo $sys['paystack_public_key']; ?>" placeholder="pk_live_..." required>

                        <label class="form-label">Secret Key</label>
                        <input type="password" name="paystack_secret" class="form-input" value="<?php echo $sys['paystack_secret_key']; ?>" placeholder="sk_live_..." required>
                    </div>

                    <!-- Security Info -->
                    <div class="form-section" style="border: none;">
                        <h3><i class="fa-solid fa-shield-halved"></i> Portal Security</h3>
                        <p style="font-size: 13px; opacity: 0.8;">Note: All changes take effect immediately across the portal.</p>
                    </div>

                    <button type="submit" class="btn-primary">
                        Update Portal Settings
                    </button>
                </form>
            </div>
        </main>
    </div>

    <?php include('footer.php'); ?>
</body>
</html>
