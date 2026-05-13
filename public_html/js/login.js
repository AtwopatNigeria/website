// script.js
const form = document.getElementById("loginForm");
const messageBox = document.getElementById("messageBox");
const captchaText = document.getElementById("captchaText");
const successModal = document.getElementById("success-modal"); // New modal reference

// TEST CREDENTIALS
const TEST_EMAIL = "support.atwopat@gmail.com";
const TEST_PASS = "Talotolorun@3344";

function generateCaptcha() {
    const chars = "ABCDEFGHJKLMNPQRSTUVWXYZ123456789";
    let captcha = "";
    for (let i = 0; i < 4; i++) {
        captcha += chars[Math.floor(Math.random() * chars.length)];
    }
    captchaText.innerText = captcha;
}

generateCaptcha();

form.addEventListener("submit", function(e) {
    e.preventDefault();

    const email = document.getElementById("email").value.trim();
    const password = document.getElementById("password").value.trim();
    const captchaInput = document.getElementById("captchaInput").value.trim();

    messageBox.innerHTML = "";

    // 1. CAPTCHA CHECK
    if (captchaInput.toUpperCase() !== captchaText.innerText) {
        messageBox.innerHTML = `<div class="error-message">Incorrect Verification Code</div>`;
        generateCaptcha();
        return;
    }

    // 2. DATABASE/CREDENTIAL CHECK
    // This simulates the "check for database" step with your specific test email
    if (email === TEST_EMAIL && password === TEST_PASS) {
        showSuccessAnimation();
    } else {
        messageBox.innerHTML = `<div class="error-message">Invalid Email or Password</div>`;
    }
});

function showSuccessAnimation() {
    // Hide the login form area or just show the overlay
    successModal.style.display = 'flex';

    // Redirect after the animation finishes (approx 2.5 seconds)
    setTimeout(() => {
        window.location.href = "admin/dashboard.php"; 
    }, 2500);
}
