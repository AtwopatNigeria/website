const registrationForm = document.getElementById("registrationForm");

registrationForm.addEventListener("submit", function(e) {
    e.preventDefault();
    
    const email = document.getElementById("email").value;
    const amount = 1500 * 100; // Paystack takes amount in Kobo

    // 1. Initialize Paystack
    let handler = PaystackPop.setup({
        key: 'pk_test_your_public_key', // REPLACE WITH YOUR KEY
        email: email,
        amount: amount,
        currency: "NGN",
        callback: function(response) {
            // This runs after successful payment
            processRegistration(response.reference);
        },
        onClose: function() {
            alert('Window closed. Please complete payment to register.');
        }
    });

    handler.openIframe();
});

function processRegistration(ref) {
    // 2. Show Success Animation
    const successModal = document.getElementById("success-modal");
    successModal.style.display = "flex";

    // 3. Prepare Form Data to send to your PHP API
    const formData = new FormData(registrationForm);
    formData.append('payment_ref', ref);

    fetch('api/register.php', {
        method: 'POST',
        body: formData
    })
    .then(res => res.json())
    .then(data => {
        setTimeout(() => {
            window.location.href = "verify.html?new_id=" + data.member_id;
        }, 3000);
    })
    .catch(err => console.error("Registration Error:", err));
}
