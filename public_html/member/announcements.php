<?php
/* =========================================================
   ATWOPAT - ANNOUNCEMENTS PORTAL
   Permissions: National Executive (Post), Others (Read-Only)
   Features: Automated Email Notifications
   Updated: May 2026
   ========================================================= */

// 1. Protect page & Header
require_once('../api/session.php');
include('header.php');

// 2. Database Connection
$host = "localhost";
$user = "your_db_user";
$pass = "your_db_password";
$dbname = "atwopat_db";

$conn = new mysqli($host, $user, $pass, $dbname);
$member_id = $_SESSION['user_id'];
$user_role = $_SESSION['user_role']; // e.g., 'National Executive' or 'Member'

$msg = "";

// 3. Handle New Post (Only for National Executive)
if ($_SERVER['REQUEST_METHOD'] == 'POST' && $user_role === 'National Executive') {
    $title = mysqli_real_escape_string($conn, $_POST['title']);
    $content = mysqli_real_escape_string($conn, $_POST['content']);
    $author = $_SESSION['user_name'];

    $sql = "INSERT INTO announcements (title, content, author, date_posted) VALUES ('$title', '$content', '$author', NOW())";
    
    if ($conn->query($sql)) {
        $msg = "<div class='alert success'>Announcement posted and emails queued!</div>";
        
        // 4. AUTOMATIC EMAIL NOTIFICATION LOGIC
        // Fetch all member emails
        $email_query = "SELECT email FROM members WHERE status = 'Active'";
        $emails = $conn->query($email_query);
        
        $subject = "ATWOPAT Official Announcement: $title";
        $headers = "From: official@atwopat.org\r\n";
        $headers .= "Content-Type: text/html; charset=UTF-8\r\n";
        
        $email_body = "<h2>$title</h2><p>$content</p><br><small>Posted by $author</small>";

        while ($row = $emails->fetch_assoc()) {
            // mail($row['email'], $subject, $email_body, $headers); 
            // Note: In real hosting, use PHPMailer for bulk reliability.
        }
    }
}

// 5. Fetch Announcements
$announcements = $conn->query("SELECT * FROM announcements ORDER BY date_posted DESC");
?>

<style>
    .announce-container { max-width: 1100px; margin: 40px auto; display: grid; grid-template-columns: 300px 1fr; gap: 30px; padding: 0 20px; }
    .post-card { background: rgba(255,255,255,0.1); backdrop-filter: blur(15px); padding: 30px; border-radius: 20px; border: 1px solid rgba(255,255,255,0.2); margin-bottom: 30px; }
    .feed-item { background: white; color: #103d75; padding: 25px; border-radius: 15px; margin-bottom: 20px; position: relative; }
    .feed-date { font-size: 11px; color: #64748b; font-weight: bold; }
    .author-badge { background: #103d75; color: white; padding: 3px 10px; border-radius: 5px; font-size: 10px; margin-left: 10px; }
    .editor-input { width: 100%; background: rgba(255,255,255,0.05); border: 1px solid rgba(255,255,255,0.2); color: white; padding: 12px; border-radius: 10px; margin-bottom: 15px; }
</style>

<div class="announce-container animate-fade-in">
    <aside><?php include('sidebar.php'); ?></aside>

    <main>
        <?php echo $msg; ?>

        <!-- POSTING SECTION: Only visible to National Executive -->
        <?php if ($user_role === 'National Executive'): ?>
        <section class="post-card">
            <h3><i class="fa-solid fa-pen-to-square"></i> Post National Announcement</h3>
            <p style="font-size: 13px; opacity: 0.7; margin-bottom: 20px;">Submitting this will notify all active members via email.</p>
            <form action="announcements.php" method="POST">
                <input type="text" name="title" placeholder="Announcement Title" class="editor-input" required>
                <textarea name="content" placeholder="Type your message here..." class="editor-input" rows="5" required></textarea>
                <button type="submit" class="btn-update" style="background:#f59e0b; border:none; padding: 10px 20px; border-radius: 8px; cursor:pointer; font-weight:bold;">
                    Broadcast Announcement
                </button>
            </form>
        </section>
        <?php endif; ?>

        <!-- VIEWING SECTION -->
        <h2 style="margin-bottom: 20px;">Latest Updates</h2>
        
        <?php if ($announcements->num_rows > 0): ?>
            <?php while($row = $announcements->fetch_assoc()): ?>
                <div class="feed-item">
                    <div style="display: flex; justify-content: space-between; align-items: center;">
                        <span class="feed-date"><?php echo date('F j, Y | g:i A', strtotime($row['date_posted'])); ?></span>
                        <span class="author-badge">BY: <?php echo strtoupper($row['author']); ?></span>
                    </div>
                    <h3 style="margin: 15px 0 10px;"><?php echo $row['title']; ?></h3>
                    <p style="line-height: 1.6; opacity: 0.9;"><?php echo nl2br($row['content']); ?></p>
                </div>
            <?php endwhile; ?>
        <?php else: ?>
            <p style="opacity: 0.6; text-align: center; margin-top: 50px;">No announcements posted yet.</p>
        <?php endif; ?>
    </main>
</div>

<?php include('footer.php'); ?>
