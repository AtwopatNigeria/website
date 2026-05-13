<?php
require_once('../database/connection.php');
include('header.php');

$sys = $conn->query("SELECT monthly_dues_amount FROM system_settings WHERE id = 1")->fetch_assoc();
$dues_amount = $sys['monthly_dues_amount'];
?>

<div class="dashboard-wrapper">
    <aside><?php include('sidebar.php'); ?></aside>
    <main class="content-box">
        <div style="text-align: center; padding: 40px;">
            <i class="fa-solid fa-calendar-check" style="font-size: 50px; color: #f59e0b;"></i>
            <h2 style="margin-top: 20px;">Monthly Dues</h2>
            <p>Your monthly contribution supports the growth of ATWOPAT.</p>
            
            <div style="background: rgba(255,255,255,0.1); padding: 20px; border-radius: 15px; margin: 20px 0;">
                <span style="font-size: 14px; opacity: 0.8;">Amount Due:</span>
                <h1 style="color: #fff;">₦<?php echo number_format($dues_amount); ?></h1>
            </div>

            <button onclick="payDues()" class="btn-primary" style="width: 100%; max-width: 300px;">
                Pay Now with Paystack
            </button>
        </div>
    </main>
</div>

<script src="https://js.paystack.co/v1/inline.js"></script>
<script>
function payDues() {
    let handler = PaystackPop.setup({
        key: '<?php echo $sys_keys['paystack_public_key']; ?>',
        email: '<?php echo $_SESSION['user_email']; ?>',
        amount: <?php echo $dues_amount * 100; ?>,
        onClose: function() { alert('Payment cancelled.'); },
        callback: function(response) {
            // Send to a PHP script to update 'last_dues_payment'
            window.location.href = "../api/process_dues.php?ref=" + response.reference;
        }
    });
    handler.openIframe();
}
</script>
