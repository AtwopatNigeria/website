<?php
/* =========================================================
   ATWOPAT - GLOBAL MEMBER HEADER
   Features: SEO Meta, CSS Links, and Top Navigation
   Updated: May 2026
   ========================================================= */

// Ensure session is started to access user names in the navbar
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Fallback if full_name isn't in session yet
$display_name = isset($_SESSION['user_name']) ? $_SESSION['user_name'] : "Member";
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo isset($page_title) ? $page_title . " | ATWOPAT" : "ATWOPAT Member Portal"; ?></title>
    
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;600;700&display=swap" rel="stylesheet">
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    
    <!-- Global CSS -->
    <link rel="stylesheet" href="../css/style.css">
    
    <style>
        :root {
            --primary-blue: #103d75;
            --accent-gold: #f59e0b;
            --glass-bg: rgba(255, 255, 255, 0.1);
            --glass-border: rgba(255, 255, 255, 0.2);
        }

        body {
            font-family: 'Inter', sans-serif;
            background: url('../images/bg-main.jpg') no-repeat center center fixed;
            background-size: cover;
            color: #fff;
            margin: 0;
            padding-top: 80px; /* Space for fixed header */
        }

        .top-navbar {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            height: 70px;
            background: rgba(16, 61, 117, 0.85);
            backdrop-filter: blur(10px);
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 0 40px;
            z-index: 1000;
            border-bottom: 1px solid var(--glass-border);
        }

        .logo-area {
            display: flex;
            align-items: center;
            gap: 12px;
            text-decoration: none;
            color: #fff;
        }

        .logo-area img {
            height: 40px;
        }

        .logo-area span {
            font-weight: 700;
            letter-spacing: 1px;
            font-size: 1.2rem;
        }

        .user-nav-tools {
            display: flex;
            align-items: center;
            gap: 20px;
        }

        .notif-bell {
            position: relative;
            cursor: pointer;
            font-size: 1.2rem;
            opacity: 0.8;
            transition: 0.3s;
        }

        .notif-bell:hover { opacity: 1; }

        .notif-badge {
            position: absolute;
            top: -5px;
            right: -5px;
            background: #ef4444;
            color: white;
            font-size: 10px;
            padding: 2px 5px;
            border-radius: 50%;
        }

        @media (max-width: 600px) {
            .top-navbar { padding: 0 20px; }
            .logo-area span { display: none; }
        }
    </style>
</head>
<body>

    <header class="top-navbar">
        <a href="dashboard.php" class="logo-area">
            <img src="../images/logo.png" alt="Logo" onerror="this.style.display='none'">
            <span>ATWOPAT</span>
        </a>

        <div class="user-nav-tools">
            <div class="notif-bell">
                <i class="fa-solid fa-bell"></i>
                <span class="notif-badge">3</span>
            </div>
            
            <div style="display: flex; align-items: center; gap: 10px; border-left: 1px solid rgba(255,255,255,0.2); padding-left: 20px;">
                <span style="font-size: 14px; opacity: 0.9;">Hello, <strong><?php echo explode(' ', $display_name)[0]; ?></strong></span>
                <i class="fa-solid fa-circle-user" style="font-size: 1.5rem;"></i>
            </div>
        </div>
    </header>
