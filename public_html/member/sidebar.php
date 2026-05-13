<?php
/* =========================================================
   ATWOPAT - REUSABLE MEMBER SIDEBAR
   Description: Navigation menu for the member portal
   Updated: May 2026
   ========================================================= */

// Ensure we have the user's data from the session
$current_page = basename($_SERVER['PHP_SELF']);
$member_photo = isset($user['photo']) ? $user['photo'] : 'default-avatar.png';
$member_name = isset($user['full_name']) ? $user['full_name'] : $_SESSION['user_name'];
$member_status = isset($user['status']) ? $user['status'] : 'Pending';

// Status Color Logic
$dot_color = ($member_status === 'Active') ? '#22c55e' : (($member_status === 'Pending') ? '#f59e0b' : '#d9534f');
?>

<div class="sidebar-inner" style="
    background: rgba(255, 255, 255, 0.1); 
    backdrop-filter: blur(15px); 
    border: 1px solid rgba(255, 255, 255, 0.2); 
    border-radius: 25px; 
    padding: 25px;
    height: 100%;
    color: white;
    text-align: center;">

    <!-- Profile Summary -->
    <div class="profile-section" style="margin-bottom: 30px;">
        <div style="position: relative; width: 100px; height: 100px; margin: 0 auto 15px;">
            <img src="../images/members/<?php echo $member_photo; ?>" 
                 style="width: 100%; height: 100%; object-fit: cover; border-radius: 50%; border: 3px solid white; box-shadow: 0 4px 10px rgba(0,0,0,0.2);"
                 onerror="this.src='../images/members/default-avatar.png';">
            <div title="<?php echo $member_status; ?>" style="
                position: absolute; 
                bottom: 5px; 
                right: 5px; 
                width: 18px; 
                height: 18px; 
                background: <?php echo $dot_color; ?>; 
                border: 3px solid #103d75; 
                border-radius: 50%;"></div>
        </div>
        <h4 style="margin: 0; font-size: 16px;"><?php echo $member_name; ?></h4>
        <p style="font-size: 12px; opacity: 0.8; margin-top: 5px;"><?php echo $_SESSION['user_id']; ?></p>
    </div>

    <hr style="border: 0; border-top: 1px solid rgba(255,255,255,0.1); margin: 20px 0;">

    <!-- Navigation Menu -->
    <nav class="nav-menu" style="display: flex; flex-direction: column; gap: 10px; text-align: left;">
        
        <a href="dashboard.php" class="nav-link <?php echo ($current_page == 'dashboard.php') ? 'active' : ''; ?>">
            <i class="fa-solid fa-house"></i> Dashboard
        </a>

        <a href="profile.php" class="nav-link <?php echo ($current_page == 'profile.php') ? 'active' : ''; ?>">
            <i class="fa-solid fa-user-gear"></i> My Profile
        </a>

        <a href="announcements.php" class="nav-link <?php echo ($current_page == 'announcements.php') ? 'active' : ''; ?>">
            <i class="fa-solid fa-bullhorn"></i> Notifications
        </a>

        <a href="verify-status.php" class="nav-link <?php echo ($current_page == 'verify-status.php') ? 'active' : ''; ?>">
            <i class="fa-solid fa-id-card"></i> Digital ID
        </a>

        <hr style="border: 0; border-top: 1px solid rgba(255,255,255,0.1); margin: 15px 0;">

        <a href="../api/logout.php" class="nav-link logout-link" style="color: #ff7675;">
            <i class="fa-solid fa-right-from-bracket"></i> Logout
        </a>
    </nav>
</div>

<style>
    .nav-link {
        display: flex;
        align-items: center;
        gap: 12px;
        padding: 12px 18px;
        border-radius: 12px;
        color: rgba(255, 255, 255, 0.8);
        text-decoration: none;
        font-size: 14px;
        font-weight: 500;
        transition: all 0.3s ease;
    }
    .nav-link i { width: 20px; text-align: center; font-size: 16px; }
    .nav-link:hover {
        background: rgba(255, 255, 255, 0.15);
        color: white;
        transform: translateX(5px);
    }
    .nav-link.active {
        background: white;
        color: #103d75;
        box-shadow: 0 4px 15px rgba(0,0,0,0.1);
    }
    .logout-link:hover {
        background: rgba(255, 118, 117, 0.1) !important;
    }
</style>
