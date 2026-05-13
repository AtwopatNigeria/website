<?php
/* =========================================================
   ATWOPAT - ADMIN ANALYTICS DASHBOARD
   Features: Stats Overview, Revenue Tracking, Status Charts
   Updated: May 2026
   ========================================================= */

// 1. Connection & Session Protection
require_once('../database/connection.php');
session_start();

if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'Admin') {
    header("Location: ../login.html?error=unauthorized");
    exit;
}

$page_title = "Admin Dashboard";
include('header.php');

// 2. Fetch Statistical Data
// Total Members
$total_res = $conn->query("SELECT COUNT(id) as total FROM members");
$total_members = $total_res->fetch_assoc()['total'];

// Pending Approvals
$pending_res = $conn->query("SELECT COUNT(id) as total FROM members WHERE status = 'Pending'");
$pending_members = $pending_res->fetch_assoc()['total'];

// Active Members
$active_res = $conn->query("SELECT COUNT(id) as total FROM members WHERE status = 'Active'");
$active_members = $active_res->fetch_assoc()['total'];

// Revenue Calculation (Assuming fee is stored in system_settings)
$settings_res = $conn->query("SELECT registration_fee FROM system_settings WHERE id = 1");
$fee = $settings_res->fetch_assoc()['registration_fee'] ?? 5000;
$total_revenue = $active_members * $fee;

// 3. Fetch Recent Registrations for the table
$recent_members = $conn->query("SELECT * FROM members ORDER BY created_at DESC LIMIT 5");
?>

<style>
    .dashboard-grid { 
        max-width: 1400px; 
        margin: 30px auto; 
        display: grid; 
        grid-template-columns: 280px 1fr; 
        gap: 30px; 
        padding: 0 20px; 
    }
    .stats-row { 
        display: grid; 
        grid-template-columns: repeat(4, 1fr); 
        gap: 20px; 
        margin-bottom: 30px; 
    }
    .stat-card { 
        background: rgba(255,255,255,0.05); 
        backdrop-filter: blur(10px); 
        border: 1px solid rgba(255,255,255,0.1); 
        padding: 25px; 
        border-radius: 20px; 
        text-align: left; 
    }
    .stat-card i { font-size: 24px; color: var(--admin-accent); margin-bottom: 15px; display: block; }
    .stat-card h3 { font-size: 28px; margin: 5px 0; color: #fff; }
    .stat-card p { font-size: 13px; color: #94a3b8; margin: 0; }

    .chart-container { 
        background: rgba(255,255,255,0.05); 
        border-radius: 20px; 
        padding: 30px; 
        margin-bottom: 30px;
        border: 1px solid rgba(255,255,255,0.1);
    }
</style>

<div class="dashboard-grid animate-fade-in">
    <aside>
        <?php include('sidebar.php'); ?>
    </aside>

    <main>
        <div style="margin-bottom: 30px;">
            <h1>Executive Overview</h1>
            <p style="opacity: 0.6;">Welcome back. Here is the current state of the association.</p>
        </div>

        <!-- Counters -->
        <div class="stats-row">
            <div class="stat-card">
                <i class="fa-solid fa-users"></i>
                <h3><?php echo number_format($total_members); ?></h3>
                <p>Total Registered</p>
            </div>
            <div class="stat-card">
                <i class="fa-solid fa-user-clock" style="color: #f59e0b;"></i>
                <h3><?php echo number_format($pending_members); ?></h3>
                <p>Pending Review</p>
            </div>
            <div class="stat-card">
                <i class="fa-solid fa-user-check" style="color: #22c55e;"></i>
                <h3><?php echo number_format($active_members); ?></h3>
                <p>Verified Members</p>
            </div>
            <div class="stat-card">
                <i class="fa-solid fa-naira-sign" style="color: #38bdf8;"></i>
                <h3>₦<?php echo number_format($total_revenue); ?></h3>
                <p>Estimated Revenue</p>
            </div>
        </div>

        <div style="display: grid; grid-template-columns: 1fr 400px; gap: 30px;">
            <!-- Recent Activity -->
            <section class="chart-container">
                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
                    <h3>Recent Registrations</h3>
                    <a href="members.php" style="color: var(--admin-accent); text-decoration: none; font-size: 13px;">View All</a>
                </div>
                <table style="width: 100%; border-collapse: collapse; color: #cbd5e1;">
                    <thead>
                        <tr style="text-align: left; border-bottom: 1px solid rgba(255,255,255,0.1);">
                            <th style="padding: 10px;">Member</th>
                            <th style="padding: 10px;">ID</th>
                            <th style="padding: 10px;">Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while($m = $recent_members->fetch_assoc()): ?>
                        <tr style="border-bottom: 1px solid rgba(255,255,255,0.05);">
                            <td style="padding: 15px 10px;"><?php echo $m['full_name']; ?></td>
                            <td style="font-family: monospace;"><?php echo $m['member_id']; ?></td>
                            <td>
                                <span style="font-size: 10px; padding: 2px 8px; border-radius: 4px; background: <?php echo ($m['status'] == 'Active') ? '#166534' : '#854d0e'; ?>;">
                                    <?php echo $m['status']; ?>
                                </span>
                            </td>
                        </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </section>

            <!-- Distribution Chart -->
            <section class="chart-container">
                <h3>Member Distribution</h3>
                <canvas id="statusChart" style="margin-top: 20px;"></canvas>
            </section>
        </div>
    </main>
</div>

<!-- Chart.js Logic -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
const ctx = document.getElementById('statusChart').getContext('2d');
new Chart(ctx, {
    type: 'doughnut',
    data: {
        labels: ['Active', 'Pending', 'Suspended'],
        datasets: [{
            data: [<?php echo $active_members; ?>, <?php echo $pending_members; ?>, 0],
            backgroundColor: ['#22c55e', '#f59e0b', '#ef4444'],
            borderWidth: 0,
            hoverOffset: 10
        }]
    },
    options: {
        plugins: {
            legend: { position: 'bottom', labels: { color: '#fff' } }
        }
    }
});
</script>

<?php include('footer.php'); ?>
