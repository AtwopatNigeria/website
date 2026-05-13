<?php
/* =========================================================
   ATWOPAT - GLOBAL ADMIN FOOTER
   Description: Closing tags and administrative scripts
   Updated: May 2026
   ========================================================= */
?>

    <!-- Global Footer Bar -->
    <footer style="
        margin-top: 50px; 
        padding: 30px; 
        text-align: center; 
        border-top: 1px solid rgba(255,255,255,0.05);
        color: rgba(255,255,255,0.4);
        font-size: 13px;">
        
        <div style="margin-bottom: 10px;">
            <img src="../images/logo.png" alt="ATWOPAT" style="height: 25px; opacity: 0.3; filter: grayscale(1);">
        </div>
        
        <p>&copy; <?php echo date('Y'); ?> <b>ATWOPAT</b> | Administrative Control Panel</p>
        <p style="font-size: 11px; margin-top: 5px;">Secure Session: <?php echo session_id(); ?></p>
    </footer>

    <!-- Global Scripts -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
    
    <!-- Dashboard Charts (Only load on dashboard page) -->
    <?php if (basename($_SERVER['PHP_SELF']) == 'dashboard.php'): ?>
        <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <?php endif; ?>

    <script>
        // Auto-hide alerts after 5 seconds
        document.addEventListener('DOMContentLoaded', function() {
            const alerts = document.querySelectorAll('.alert');
            alerts.forEach(alert => {
                setTimeout(() => {
                    alert.style.opacity = '0';
                    alert.style.transition = 'opacity 0.5s ease';
                    setTimeout(() => alert.remove(), 500);
                }, 5000);
            });
        });

        // Quick Search Shortcut (Alt + S)
        document.addEventListener('keydown', function(e) {
            if (e.altKey && e.key === 's') {
                window.location.href = 'verification.php';
            }
        });
    </script>

</body>
</html>
