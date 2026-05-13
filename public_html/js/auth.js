/**
 * ATWOPAT - AUTHENTICATION & REGISTRATION LOGIC
 * Handles: Login, Registration, and Paystack Integration
 * Updated: May 2026
 */

document.addEventListener('DOMContentLoaded', () => {
    // --- 1. LOGIN HANDLER ---
    const loginForm = document.getElementById('loginForm');
    if (loginForm) {
        loginForm.addEventListener('submit', async (e) => {
            e.preventDefault();
            
            const formData = new FormData(loginForm);
            
            try {
                const response = await fetch('api/login.php', {
                    method: 'POST',
                    body: formData
                });

                const result = await response.json();

                if (result.status === 'success') {
                    // Show success message and redirect
                    alert(result.message);
                    window.location.href = 'member/dashboard.php';
                } else {
                    alert(result.message || 'Login failed. Please check your credentials.');
                }
            } catch (error) {
                console.error('Login Error:', error);
                alert('An error occurred. Please try again later.');
            }
        });
    }

    // --- 2. REGISTRATION & PAYMENT HANDLER ---
    const registerForm = document.getElementById('registerForm');
    if (registerForm) {
        registerForm.addEventListener('submit', function(e) {
            e.preventDefault();
            
            // Collect Form Data
            const email = document.getElementById('email').value;
            const fullName = document.getElementById('fullName').value;
            const phone = document.getElementById('phone').value;
            const amount = 5000; // Registration fee in Naira (Example)

            // Step A: Trigger Paystack Payment
            payWithPaystack(email, amount, fullName, phone);
        });
    }
});

/**
 * Paystack Integration Function
 */
function payWithPaystack(email, amount, name, phone) {
    const handler = PaystackPop.setup({
        key: 'pk_live_your_actual_key_here', // Replace with your live public key
        email: email,
        amount: amount * 100, // Amount in kobo
        currency: 'NGN',
        metadata: {
            custom_fields: [
                { display_name: "Full Name", variable_name: "full_name", value: name },
                { display_name: "Phone Number", variable_name: "phone", value: phone }
            ]
        },
        callback: function(response) {
            // Step B: Payment Successful - Now register the member in DB
            completeRegistration(response.reference);
        },
        onClose: function() {
            alert('Window closed. Payment not completed.');
        }
    });
    handler.openIframe();
}

/**
 * Final Registration Step
 * Sends all form data + payment reference to register.php
 */
async function completeRegistration(paymentReference) {
    const form = document.getElementById('registerForm');
    const formData = new FormData(form);
    
    // Add the payment reference to the form data
    formData.append('payment_ref', paymentReference);

    try {
        const response = await fetch('api/register.php', {
            method: 'POST',
            body: formData
        });

        const result = await response.json();

        if (result.status === 'success') {
            // Show the generated ID and redirect to a success page
            alert(`Success! Your Member ID is ${result.member_id}. Redirecting to login...`);
            window.location.href = 'login.html';
        } else {
            alert('Registration failed: ' + result.message);
        }
    } catch (error) {
        console.error('Registration Error:', error);
        alert('Payment was successful, but database registration failed. Please contact admin with your Ref: ' + paymentReference);
    }
}
