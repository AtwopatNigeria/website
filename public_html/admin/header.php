<?php
/* =========================================================
   ATWOPAT - GLOBAL ADMIN HEADER
   Features: Admin Brand Identity, Session Verification, Meta
   Updated: May 2026
   ========================================================= */

// 1. Session check to ensure only Admins can load this header
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'Admin') {
    header("Location: ../login.html?error=unauthorized");
    exit;
}

$admin_display_name = isset($_SESSION['user_name']) ? $_SESSION['user_name'] : "Administrator";
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo isset($page_title) ? $page_title . " | ATWOPAT Admin" : "ATWOPAT Admin Portal"; ?></title>
    
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;600;700&display=swap" rel="stylesheet">
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    
    <!-- Global Admin CSS -->
    <link rel="stylesheet" href="../css/dashboard.css">
    
    <style>
        :root {
            --admin-blue: #0f172a;
            --admin-accent: #f59e0b; /* Gold for Admin Distinction */
            --glass-border: rgba(255, 255, 255, 0.1);
        }

        body {
            font-family: 'Inter', sans-serif;
            background: #0f172a; /* Solid dark blue for admin feel */
            color: #fff;
            margin: 0;
            padding-top: 70px;
        }

        .admin-top-navbar {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            height: 70px;
            background: rgba(15, 23, 42, 0.9);
            backdrop-filter: blur(10px);
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 0 40px;
            z-index: 1000;
            border-bottom: 1px solid var(--glass-border);
        }

        .admin-logo-area {
            display: flex;
            align-items: center;
            gap: 12px;
            text-decoration: none;
            color: #fff;
        }

        .admin-logo-area span {
            font-weight: 800;
            letter-spacing: 1.5px;
            font-size: 1.1rem;
            color: var(--admin-accent);
        }

        .admin-badge {
            background: var(--admin-accent);
            color: #0f172a;
            font-size: 10px;
            font-weight: 900;
            padding: 2px 8px;
            border-radius: 4px;
            margin-left: 10px;
            vertical-align: middle;
        }

        .admin-tools {
            display: flex;
            align-items: center;
            gap: 25px;
        }

        .search-trigger {
            opacity: 0.6;
            cursor: pointer;
            transition: 0.3s;
        }

        .search-trigger:hover { opacity: 1; color: var(--admin-accent); }

        @media (max-width: 600px) {
            .admin-top-navbar { padding: 0 20px; }
            .admin-logo-area span { display: none; }
        }
    </style>
</head>
<body>

    <header class="admin-top-navbar">
        <a href="dashboard.php" class="admin-logo-area">
            <img src="../images/logo.png" alt="Logo" style="height: 35px;" onerror="this.style.display='none'">
            <span>ATWOPAT <small class="admin-badge">ADMIN</small></span>
        </a>

        <div class="admin-tools">
            <!-- Quick Search Shortcut -->
            <div class="search-trigger" onclick="location.href='verification.php'">
                <i class="fa-solid fa-magnifying-glass"></i>
            </div>

            <div style="display: flex; align-items: center; gap: 12px; border-left: 1px solid rgba(255,255,255,0.1); padding-left: 20px;">
                <div style="text-align: right; line-height: 1;">
                    <p style="font-size: 13px; margin: 0; font-weight: 600;"><?php echo $admin_display_name; ?></p>
                    <small style="font-size: 10px; color: var(--admin-accent); opacity: 0.8;">System Controller</small>
                </div>
                <div style="width: 35px; height: 35px; background: rgba(255,255,255,0.1); border-radius: 50%; display: flex; align-items: center; justify-content: center; border: 1px solid var(--admin-accent);">
                    <i class="fa-solid fa-user-gear" style="color: var(--admin-accent);"></i>
                </div>
            </div>
        </div>
    </header>
