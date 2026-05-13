<?php
/* =========================================================
   ATWOPAT - MASTER MEMBER DIRECTORY
   Features: Advanced Filtering, Export-ready Table, Status Tracking
   Updated: May 2026
   ========================================================= */

// 1. Connection & Session Protection
require_once('../database/connection.php');
session_start();

if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'Admin') {
    header("Location: ../login.html?error=unauthorized");
    exit;
}

// 2. Handle Search & Filter Logic
$where_clauses = [];
if (!empty($_GET['status'])) {
    $status = cleanInput($_GET['status'], $conn);
    $where_clauses[] = "status = '$status'";
}
if (!empty($_GET['search'])) {
    $search = cleanInput($_GET['search'], $conn);
    $where_clauses[] = "(full_name LIKE '%$search%' OR member_id LIKE '%$search%' OR email LIKE '%$search%')";
}

$query = "SELECT * FROM members";
if (count($where_clauses) > 0) {
    $query .= " WHERE " . implode(' AND ', $where_clauses);
}
$query .= " ORDER BY created_at DESC";

$result = $conn->query($query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Member Directory | ATWOPAT Admin</title>
    <link rel="stylesheet" href="../css/dashboard.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <style>
        .members-container { max-width: 1300px; margin: 40px auto; display: grid; grid-template-columns: 280px 1fr; gap: 30px; padding: 0 20px; }
        .data-card { background: rgba(255,255,255,0.05); backdrop-filter: blur(10px); border: 1px solid rgba(255,255,255,0.1); border-radius: 20px; padding: 25px; color: white; overflow-x: auto; }
        
        table { width: 100%; border-collapse: collapse; margin-top: 20px; font-size: 14px; }
        th { text-align: left; padding: 15px; border-bottom: 2px solid rgba(255,255,255,0.1); color: #f59e0b; }
        td { padding: 15px; border-bottom: 1px solid rgba(255,255,255,0.05); }
        tr:hover { background: rgba(255,255,255,0.02); }

        .status-badge { padding: 4px 10px; border-radius: 6px; font-size: 11px; font-weight: bold; text-transform: uppercase; color: white; }

        .filter-flex { display: flex; gap: 15px; margin-bottom: 20px; align-items: flex-end; }
        .search-input { background: rgba(0,0,0,0.2); border: 1px solid rgba(255,255,255,0.2); color: white; padding: 10px; border-radius: 8px; flex: 1; }
    </style>
</head>
<body style="background: #0f172a;">

    <div class="members-container animate-fade-in">
        <aside>
            <?php include('sidebar.php'); ?>
        </aside>

        <main>
            <div class="data-card">
                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
                    <h2>Member Database</h2>
                    <span style="opacity: 0.6; font-size: 14px;">Total Records: <?php echo $result->num_rows; ?></span>
                </div>

                <!-- Filters -->
                <form action="" method="GET" class="filter-flex">
                    <div style="flex: 2;">
                        <label style="font-size: 12px; display: block; margin-bottom: 5px;">Search Name/ID/Email</label>
                        <input type="text" name="search" class="search-input" value="<?php echo $_GET['search'] ?? ''; ?>" placeholder="Type to filter...">
                    </div>
                    <div style="flex: 1;">
                        <label style="font-size: 12px; display: block; margin-bottom: 5px;">Filter by Profile Status</label>
                        <select name="status" class="search-input" style="width: 100%;">
                            <option value="">All Statuses</option>
                            <option value="Active" <?php if(isset($_GET['status']) && $_GET['status'] == 'Active') echo 'selected'; ?>>Active</option>
                            <option value="Pending" <?php if(isset($_GET['status']) && $_GET['status'] == 'Pending') echo 'selected'; ?>>Pending</option>
                            <option value="Suspended" <?php if(isset($_GET['status']) && $_GET['status'] == 'Suspended') echo 'selected'; ?>>Suspended</option>
                        </select>
                    </div>
                    <button type="submit" class="btn-primary" style="padding: 10px 20px;">Filter</button>
                    <a href="members.php" class="btn-primary" style="background: #64748b; padding: 10px 20px; text-decoration: none; font-size: 13px;">Reset</a>
                </form>

                <!-- Table -->
                <table>
                    <thead>
                        <tr>
                            <th>Member ID</th>
                            <th>Full Name</th>
                            <th>Role</th>
                            <th>State</th>
                            <th>Payment Ref</th>
                            <th>Dues Status</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if ($result->num_rows > 0): ?>
                            <?php while($row = $result->fetch_assoc()): ?>
                                <tr>
                                    <td style="font-family: monospace; font-weight: bold;"><?php echo $row['member_id']; ?></td>
                                    <td><?php echo $row['full_name']; ?></td>
                                    <td><?php echo $row['role']; ?></td>
                                    <td><?php echo $row['state']; ?></td>
                                    <td style="font-size: 11px; opacity: 0.7;"><?php echo $row['payment_ref'] ?? 'N/A'; ?></td>
                                    
                                    <!-- Dynamic Payment Status Logic -->
                                    <td>
                                        <?php 
                                            $last_paid = $row['last_dues_payment'];
                                            $current_month = date('Y-m');
                                            
                                            if (!$last_paid || date('Y-m', strtotime($last_paid)) < $current_month) {
                                                echo '<span class="status-badge" style="background: #ef4444;">OWING</span>';
                                            } else {
                                                echo '<span class="status-badge" style="background: #22c55e;">PAID</span>';
                                            }
                                        ?>
                                    </td>

                                    <td>
                                        <a href="verification.php?search_id=<?php echo $row['member_id']; ?>" style="color: #f59e0b; text-decoration: none; font-size: 18px;" title="View/Edit">
                                            <i class="fa-solid fa-eye"></i>
                                        </a>
                                    </td>
                                </tr>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <tr><td colspan="7" style="text-align: center; padding: 40px; opacity: 0.5;">No members found matching your criteria.</td></tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </main>
    </div>

</body>
</html>
