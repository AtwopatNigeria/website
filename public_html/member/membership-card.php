<?php
/* =========================================================
   ATWOPAT - DIGITAL MEMBERSHIP CARD
   Features: Glassmorphism Design, QR Code, Print Optimization
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

// 3. Fetch Member Data
$sql = "SELECT * FROM members WHERE member_id = '$member_id' LIMIT 1";
$result = $conn->query($sql);
$user = $result->fetch_assoc();

// Fallback for missing data
$photo = !empty($user['photo']) ? $user['photo'] : 'default-avatar.png';
$status_color = ($user['status'] === 'Active') ? '#22c55e' : '#f59e0b';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Membership Card | ATWOPAT</title>
    <link rel="stylesheet" href="../css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <style>
        .card-container {
            max-width: 1100px;
            margin: 40px auto;
            display: grid;
            grid-template-columns: 300px 1fr;
            gap: 30px;
            padding: 0 20px;
        }

        /* The ID Card Styling */
        .id-card-wrapper {
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            padding: 40px;
            background: rgba(255, 255, 255, 0.05);
            backdrop-filter: blur(10px);
            border-radius: 25px;
            border: 1px solid rgba(255, 255, 255, 0.1);
        }

        .atwopat-card {
            width: 350px;
            height: 500px;
            background: linear-gradient(135deg, #ffffff 0%, #f0f7ff 100%);
            border-radius: 20px;
            box-shadow: 0 20px 40px rgba(0,0,0,0.3);
            position: relative;
            overflow: hidden;
            color: #103d75;
            padding: 20px;
            display: flex;
            flex-direction: column;
            align-items: center;
            border: 1px solid #ddd;
        }

        .card-header { text-align: center; margin-bottom: 20px; }
        .card-header h2 { font-size: 18px; margin: 0; color: #103d75; letter-spacing: 1px; }
        .card-header p { font-size: 10px; margin: 0; text-transform: uppercase; opacity: 0.8; }

        .profile-img-box {
            width: 140px;
            height: 140px;
            border-radius: 15px;
            overflow: hidden;
            border: 4px solid #103d75;
            margin-bottom: 15px;
        }
        .profile-img-box img { width: 100%; height: 100%; object-fit: cover; }

        .member-info { width: 100%; text-align: center; }
        .member-info h1 { font-size: 20px; margin: 0 0 5px; text-transform: uppercase; }
        .member-info p { margin: 2px 0; font-size: 13px; }

        .card-footer {
            margin-top: auto;
            width: 100%;
            display: flex;
            justify-content: space-between;
            align-items: flex-end;
            padding-top: 15px;
            border-top: 1px dashed #ccc;
        }

        .qr-holder img { background: white; padding: 5px; border-radius: 5px; border: 1px solid #ddd; }

        /* Print Button */
        .print-btn {
            margin-top: 30px;
            background: #22c55e;
            color: white;
            padding: 12px 30px;
            border-radius: 50px;
            text-decoration: none;
            font-weight: bold;
            display: inline-flex;
            align-items: center;
            gap: 10px;
            transition: 0.3s;
        }
        .print-btn:hover { transform: scale(1.05); background: #16a34a; }

        /* PRINT LOGIC */
        @media print {
            body * { visibility: hidden; }
            .atwopat-card, .atwopat-card * { visibility: visible; }
            .atwopat-card { 
                position: absolute; 
                left: 50%; 
                top: 50%; 
                transform: translate(-50%, -50%) scale(1.2); 
            }
        }

        @media (max-width: 850px) { .card-container { grid-template-columns: 1fr; } }
    </style>
</head>
<body>

    <div class="card-container animate-fade-in">
        <!-- SIDEBAR -->
        <aside>
            <?php include('sidebar.php'); ?>
        </aside>

        <!-- MAIN CONTENT -->
        <main class="id-card-wrapper">
            <div style="text-align: center; margin-bottom: 20px; color: white;">
                <h2>Your Digital Identity</h2>
                <p>Verify your membership at any official ATWOPAT event.</p>
            </div>

            <!-- PHYSICAL CARD START -->
            <div class="atwopat-card">
                <div class="card-header">
                    <img src="../images/logo.png" style="width: 40px; margin-bottom: 5px;" onerror="this.style.display='none'">
                    <h2>ATWOPAT</h2>
                    <p>Official Membership Card</p>
                </div>

                <div class="profile-img-box">
                    <img src="../images/members/<?php echo $photo; ?>" onerror="this.src='../images/members/default-avatar.png';">
                </div>

                <div class="member-info">
                    <h1><?php echo $user['full_name']; ?></h1>
                    <p style="font-weight: bold; color: #d9534f;"><?php echo $user['role']; ?></p>
                    <p><b>ID No:</b> <?php echo $user['member_id']; ?></p>
                    <p><b>State:</b> <?php echo $user['state']; ?></p>
                    <p><b>Status:</b> <span style="color: <?php echo $status_color; ?>"><?php echo $user['status']; ?></span></p>
                </div>

                <div class="card-footer">
                    <div style="text-align: left; font-size: 9px; opacity: 0.7;">
                        <p>Issued: <?php echo date('Y'); ?></p>
                        <p>Valid Until: 2027-12-31</p>
                    </div>
                    <div class="qr-holder">
                        <img src="https://api.qrserver.com/v1/create-qr-code/?size=70x70&data=https://yourdomain.com/verify.html?id=<?php echo $user['member_id']; ?>" width="70">
                    </div>
                </div>
            </div>
            <!-- PHYSICAL CARD END -->

            <a href="javascript:window.print()" class="print-btn">
                <i class="fa-solid fa-print"></i> Print ID Card
            </a>
        </main>
    </div>

    <footer>
        &copy; 2026 ATWOPAT Membership Portal.
    </footer>

</body>
</html>
