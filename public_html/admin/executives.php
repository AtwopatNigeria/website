<?php
/* =========================================================
   ATWOPAT - EXECUTIVE MANAGEMENT
   Features: Role Assignment, Executive Directory, Profile Management
   Updated: May 2026
   ========================================================= */

// 1. Connection & Session Protection
require_once('../database/connection.php');
session_start();

if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'Admin') {
    header("Location: ../login.html?error=unauthorized");
    exit;
}

$page_title = "Executive Management";
include('header.php');

$update_msg = "";

// 2. Handle Executive Role Update
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['update_exec'])) {
    $target_id = cleanInput($_POST['member_id'], $conn);
    $new_role = cleanInput($_POST['exec_role'], $conn);
    
    // Update role in the database
    $sql = "UPDATE members SET role = '$new_role' WHERE member_id = '$target_id'";
    if ($conn->query($sql)) {
        $update_msg = "<div class='alert alert-success'>Executive role updated for $target_id</div>";
    }
}

// 3. Fetch all members with Executive-level roles
$exec_query = "SELECT * FROM members WHERE role IN ('National President', 'National Secretary', 'National Executive', 'Treasurer') ORDER BY role ASC";
$executives = $conn->query($exec_query);
?>

<style>
    .exec-container { 
        max-width: 1200px; 
        margin: 40px auto; 
        display: grid; 
        grid-template-columns: 280px 1fr; 
        gap: 30px; 
        padding: 0 20px; 
    }
    .exec-card-grid { 
        display: grid; 
        grid-template-columns: repeat(auto-fill, minmax(300px, 1fr)); 
        gap: 20px; 
    }
    .exec-item { 
        background: rgba(255,255,255,0.05); 
        backdrop-filter: blur(10px); 
        border: 1px solid rgba(255,255,255,0.1); 
        border-radius: 20px; 
        padding: 20px; 
        text-align: center;
        transition: transform 0.3s ease;
    }
    .exec-item:hover { transform: translateY(-5px); border-color: var(--admin-accent); }
    .exec-img { width: 100px; height: 100px; border-radius: 50%; border: 3px solid var(--admin-accent); object-fit: cover; margin-bottom: 15px; }
    .role-tag { background: var(--admin-accent); color: #0f172a; font-size: 11px; font-weight: bold; padding: 4px 12px; border-radius: 50px; text-transform: uppercase; }
</style>

<div class="exec-container animate-fade-in">
    <aside>
        <?php include('sidebar.php'); ?>
    </aside>

    <main>
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 30px;">
            <div>
                <h1>National Executives</h1>
                <p style="opacity: 0.6;">Manage officials with broadcast and administrative privileges.</p>
            </div>
            <a href="members.php" class="btn-primary" style="text-decoration: none; font-size: 13px;">
                <i class="fa-solid fa-plus"></i> Add New Executive
            </a>
        </div>

        <?php echo $update_msg; ?>

        <div class="exec-card-grid">
            <?php if ($executives->num_rows > 0): ?>
                <?php while($row = $executives->fetch_assoc()): ?>
                    <div class="exec-item">
                        <img src="../images/members/<?php echo $row['photo']; ?>" class="exec-img" onerror="this.src='../images/members/default-avatar.png';">
                        <div style="margin-bottom: 10px;">
                            <span class="role-tag"><?php echo $row['role']; ?></span>
                        </div>
                        <h3 style="margin: 5px 0;"><?php echo $row['full_name']; ?></h3>
                        <p style="font-size: 13px; opacity: 0.7; margin-bottom: 15px;">ID: <?php echo $row['member_id']; ?></p>
                        
                        <hr style="border: 0; border-top: 1px solid rgba(255,255,255,0.1); margin: 15px 0;">
                        
                        <form action="" method="POST" style="display: flex; gap: 10px; justify-content: center;">
                            <input type="hidden" name="member_id" value="<?php echo $row['member_id']; ?>">
                            <select name="exec_role" class="input-group" style="padding: 5px; font-size: 12px; width: 140px; background: #0f172a;">
                                <option value="National Executive" <?php if($row['role'] == 'National Executive') echo 'selected'; ?>>Exec Member</option>
                                <option value="National President" <?php if($row['role'] == 'National President') echo 'selected'; ?>>President</option>
                                <option value="National Secretary" <?php if($row['role'] == 'National Secretary') echo 'selected'; ?>>Secretary</option>
                                <option value="Member" style="color: red;">Demote to Member</option>
                            </select>
                            <button type="submit" name="update_exec" class="btn-primary" style="padding: 5px 10px; font-size: 11px;">Update</button>
                        </form>
                    </div>
                <?php endwhile; ?>
            <?php else: ?>
                <div style="grid-column: 1/-1; text-align: center; padding: 50px; opacity: 0.5;">
                    <i class="fa-solid fa-user-slash" style="font-size: 40px; margin-bottom: 15px;"></i>
                    <p>No executives found in the database.</p>
                </div>
            <?php endif; ?>
        </div>
    </main>
</div>

<?php include('footer.php'); ?>
