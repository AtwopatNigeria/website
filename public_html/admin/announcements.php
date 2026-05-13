<?php
/* =========================================================
   ATWOPAT - ADMIN ANNOUNCEMENT MANAGEMENT
   Features: Post New, View History, Broadcast Control
   Updated: May 2026
   ========================================================= */

// 1. Connection & Session Protection
require_once('../database/connection.php');
session_start();

if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'Admin') {
    header("Location: ../login.html?error=unauthorized");
    exit;
}

$page_title = "Manage Announcements";
include('header.php');

$msg = "";

// 2. Handle New Post logic
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['post_announcement'])) {
    $title = cleanInput($_POST['title'], $conn);
    $content = cleanInput($_POST['content'], $conn);
    $author = $_SESSION['user_name'];

    $sql = "INSERT INTO announcements (title, content, author, date_posted) VALUES ('$title', '$content', '$author', NOW())";
    
    if ($conn->query($sql)) {
        $msg = "<div class='alert alert-success'>Announcement broadcasted successfully!</div>";
        
        // EMAIL LOGIC: Fetch all active member emails
        $emails = $conn->query("SELECT email FROM members WHERE status = 'Active'");
        $subject = "ATWOPAT Update: $title";
        $headers = "From: admin@atwopat.org\r\nContent-Type: text/html; charset=UTF-8";
        
        while ($row = $emails->fetch_assoc()) {
            // mail($row['email'], $subject, $content, $headers);
        }
    }
}

// 3. Handle Delete logic
if (isset($_GET['delete_id'])) {
    $id = cleanInput($_GET['delete_id'], $conn);
    if ($conn->query("DELETE FROM announcements WHERE id = '$id'")) {
        $msg = "<div class='alert alert-success'>Announcement removed.</div>";
    }
}

// 4. Fetch History
$history = $conn->query("SELECT * FROM announcements ORDER BY date_posted DESC");
?>

<style>
    .announce-admin-grid { 
        max-width: 1300px; 
        margin: 30px auto; 
        display: grid; 
        grid-template-columns: 280px 1fr; 
        gap: 30px; 
        padding: 0 20px; 
    }
    .compose-box { 
        background: rgba(255,255,255,0.05); 
        border: 1px solid rgba(255,255,255,0.1); 
        border-radius: 20px; 
        padding: 30px; 
        margin-bottom: 30px; 
    }
    .history-card { 
        background: white; 
        color: #0f172a; 
        border-radius: 15px; 
        padding: 20px; 
        margin-bottom: 15px; 
        display: flex; 
        justify-content: space-between; 
        align-items: flex-start;
    }
</style>

<div class="announce-admin-grid animate-fade-in">
    <aside>
        <?php include('sidebar.php'); ?>
    </aside>

    <main>
        <div style="margin-bottom: 30px;">
            <h1>Broadcasting Center</h1>
            <p style="opacity: 0.6;">Create official updates that will be sent to all active members.</p>
        </div>

        <?php echo $msg; ?>

        <!-- Compose Form -->
        <section class="compose-box">
            <h3 style="margin-bottom: 20px;"><i class="fa-solid fa-paper-plane"></i> New Broadcast</h3>
            <form action="" method="POST">
                <div class="input-group" style="margin-bottom: 20px;">
                    <label>Announcement Subject</label>
                    <input type="text" name="title" placeholder="e.g. National Convention 2026 Notice" required>
                </div>
                <div class="input-group" style="margin-bottom: 20px;">
                    <label>Message Content</label>
                    <textarea name="content" rows="6" placeholder="Write your official message here..." required></textarea>
                </div>
                <button type="submit" name="post_announcement" class="btn-primary" style="width: 250px;">
                    Send to All Members
                </button>
            </form>
        </section>

        <!-- History -->
        <h2 style="margin-bottom: 20px;">Sent Announcements</h2>
        <?php if ($history->num_rows > 0): ?>
            <?php while($row = $history->fetch_assoc()): ?>
                <div class="history-card">
                    <div style="flex: 1;">
                        <span style="font-size: 11px; font-weight: bold; color: #64748b;">
                            SENT ON: <?php echo date('M d, Y', strtotime($row['date_posted'])); ?>
                        </span>
                        <h3 style="margin: 10px 0;"><?php echo $row['title']; ?></h3>
                        <p style="font-size: 14px; opacity: 0.8; line-height: 1.5;">
                            <?php echo nl2br(substr($row['content'], 0, 200)); ?>...
                        </p>
                    </div>
                    <div style="margin-left: 20px;">
                        <a href="?delete_id=<?php echo $row['id']; ?>" 
                           onclick="return confirm('Permanently delete this announcement?')" 
                           style="color: #ef4444; font-size: 18px;">
                            <i class="fa-solid fa-trash-can"></i>
                        </a>
                    </div>
                </div>
            <?php endwhile; ?>
        <?php else: ?>
            <p style="opacity: 0.5; text-align: center;">No announcement history found.</p>
        <?php endif; ?>
    </main>
</div>

<?php include('footer.php'); ?>
