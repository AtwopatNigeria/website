<?php
/* =========================================================
   ATWOPAT - SYSTEM SETTINGS (ADMIN ONLY)
   Features: API Key Management, Fee Configuration, Security
   Updated: May 2026
   ========================================================= */

// 1. Connection & Session Protection
require_once('../database/connection.php');
session_start();

if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'Admin') {
    header("Location: ../login.html?error=unauthorized");
    exit;
}

$update_msg = "";

// 2. Fetch Current System Settings (Assuming a 'settings' table exists)
// If you don't have a table yet, you can hardcode these or use this logic
$settings_res = $conn->query("SELECT * FROM system_settings WHERE id = 1");
$sys = $settings_res->fetch_assoc();

// 3. Handle Settings Update
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $reg_fee = cleanInput($_POST['reg_fee'], $conn);
    $paystack_pk = cleanInput($_POST['paystack_public'], $conn);
    $paystack_sk = cleanInput($_POST['paystack_secret'], $conn);
    $admin_email = cleanInput($_POST['admin_email'], $conn);

    $update_sql = "UPDATE system_settings SET 
                   registration_fee = '$reg_fee', 
                   paystack_public_key = '$paystack_pk', 
                   paystack_secret_key = '$paystack_sk',
                   admin_notification_email = '$admin_email' 
                   WHERE id = 1";

    if ($conn->query($update_sql)) {
        $update_msg = "<div class='alert success'>System settings updated successfully!</div>";
        // Refresh local data
        $settings_res = $conn->query("SELECT * FROM system_settings WHERE id = 1");
        $sys = $settings_res->fetch_assoc();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>System Settings | ATWOPAT Admin</title>
    <link rel="stylesheet" href="../css/dashboard.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <style>
        .settings-grid { 
            display: grid; 
            grid-template-columns: 300px 1fr; 
            gap: 30px; 
            max-width: 1200px; 
            margin: 40px auto; 
            padding: 0 20px; 
        }
        .config-card { 
            background: rgba(255,255,255,0.05); 
            backdrop-filter: blur(10px); 
            border: 1px solid rgba(255,255,255,0.1); 
            padding: 30px; 
            border-radius: 20px; 
            color: white; 
        }
        .form-section { margin-bottom: 30px; padding-bottom: 20px; border-bottom: 1px solid rgba(255,255,255,0.1); }
        .form-label { display: block; margin-bottom: 10px; font-size: 13px; color: #f59e0b; font-weight: bold; }
        .form-input { 
            width: 100%; 
            background: rgba(0,0,0,0.2); 
            border: 1px solid rgba(255,255,255,0.2); 
            color: white; 
            padding: 12px; 
            border-radius: 8px; 
            margin-bottom: 20px; 
        }
    </style>
</head>
<body style="background: #0f172a;">

    <div class="settings-grid animate-fade-in">
        <!-- SIDEBAR -->
        <aside>
            <?php include('sidebar.php'); ?>
        </aside>

        <!-- MAIN CONFIGURATION -->
        <main>
            <div class="config-card">
                <h2><i class="fa-solid fa-gears"></i> Global System Configuration</h2>
                <p style="opacity: 0.6; margin-bottom: 30px;">Manage API keys, payment amounts, and administrative contacts.</p>

                <?php echo $update_msg; ?>

                <form action="" method="POST">
                    
                    <!-- Payment Configuration -->
                    <div class="form-section">
                        <h3><i class="fa-solid fa-credit-card"></i> Payment Settings</h3>
                        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-top: 15px;">
                            <div>
                                <label class="form-label">Registration Fee (NGN)</label>
                                <input type="number" name="reg_fee" class="form-input" value="<?php echo $sys['registration_fee']; ?>" required>
                            </div>
                            <div>
                                <label class="form-label">Admin Notification Email</label>
                                <input type="email" name="admin_email" class="form-input" value="<?php echo $sys['admin_notification_email']; ?>" required>
                            </div>
                        </div>
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

                    <!-- Security -->
                    <div class="form-section" style="border: none;">
                        <h3><i class="fa-solid fa-shield-halved"></i> Portal Security</h3>
                        <p style="font-size: 13px; opacity: 0.8;">New registrations are set to <strong>Pending</strong> by default for manual verification.</p>
                    </div>

                    <button type="submit" class="btn-primary" style="background: #f59e0b; width: 200px;">
                        Save Configuration
                    </button>

                </form>
            </div>
        </main>
    </div>

</body>
</html>
