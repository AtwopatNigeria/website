<?php
/* =========================================================
   ATWOPAT - ADMIN SIDEBAR
   Description: Navigation for administrative oversight
   Updated: May 2026
   ========================================================= */

// Get current file name for active states
$current_page = basename($_SERVER['PHP_SELF']);

// Admin Info (Assumes session data from login)
$admin_name = isset($_SESSION['user_name']) ? $_SESSION['user_name'] : "Administrator";
?>

<div class="sidebar-inner" style="
    background: rgba(16, 61, 117, 0.95); 
    backdrop-filter: blur(15px); 
    border-radius: 25px; 
    padding: 25px;
    height: 100%;
    color: white;
    box-shadow: 0 10px 30px rgba(0,0,0,0.5);">

    <!-- Admin Profile Section -->
    <div class="admin-profile" style="text-align: center; margin-bottom: 30px;">
        <div style="width: 80px; height: 80px; background: #f59e0b; border-radius: 50%; margin: 0 auto 15px; display: flex; align-items: center; justify-content: center; border: 3px solid rgba(255,255,255,0.2);">
            <i class="fa-solid fa-user-shield" style="font-size: 40px; color: #103d75;"></i>
        </div>
        <h4 style="margin: 0; font-size: 16px;"><?php echo $admin_name; ?></h4>
        <p style="font-size: 11px; color: #f59e0b; font-weight: bold; text-transform: uppercase; margin-top: 5px;">National Admin</p>
    </div>

    <hr style="border: 0; border-top: 1px solid rgba(255,255,255,0.1); margin: 20px 0;">

    <!-- Navigation Menu -->
    <nav class="admin-nav" style="display: flex; flex-direction: column; gap: 8px;">
        
        <a href="dashboard.php" class="nav-link <?php echo ($current_page == 'dashboard.php') ? 'active' : ''; ?>">
            <i class="fa-solid fa-chart-line"></i> Overview
        </a>

        <a href="verification.php" class="nav-link <?php echo ($current_page == 'verification.php') ? 'active' : ''; ?>">
            <i class="fa-solid fa-user-check"></i> Pending Approvals
        </a>

        <a href="all-members.php" class="nav-link <?php echo ($current_page == 'all-members.php') ? 'active' : ''; ?>">
            <i class="fa-solid fa-users"></i> Member Database
        </a>

        <a href="../member/announcements.php" class="nav-link">
            <i class="fa-solid fa-bullhorn"></i> Post Announcement
        </a>

        <hr style="border: 0; border-top: 1px solid rgba(255,255,255,0.1); margin: 15px 0;">

        <a href="../api/logout.php" class="nav-link" style="color: #ff7675;">
            <i class="fa-solid fa-right-from-bracket"></i> Exit Admin
        </a>
    </nav>
</div>

<style>
    .nav-link {
        display: flex;
        align-items: center;
        gap: 12px;
        padding: 14px 18px;
        border-radius: 12px;
        color: rgba(255, 255, 255, 0.7);
        text-decoration: none;
        font-size: 14px;
        font-weight: 500;
        transition: all 0.3s ease;
    }
    
    .nav-link i { 
        width: 20px; 
        text-align: center; 
        font-size: 16px; 
    }

    .nav-link:hover {
        background: rgba(255, 255, 255, 0.1);
        color: white;
        transform: translateX(5px);
    }

    .nav-link.active {
        background: #f59e0b;
        color: #103d75;
        font-weight: bold;
        box-shadow: 0 4px 15px rgba(245, 158, 11, 0.3);
    }
</style>
