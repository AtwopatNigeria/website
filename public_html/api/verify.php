<?php
/* =========================================================
   ATWOPAT - PAYSTACK TRANSACTION VERIFICATION
   ========================================================= */
require_once '../database/connection.php';

// 1. Get the reference from the URL
$reference = isset($_GET['reference']) ? $_GET['reference'] : '';

if (!$reference) {
    die("No reference provided. Transaction cannot be verified.");
}

// 2. Fetch your Secret Key from the database
$query = "SELECT paystack_secret_key FROM system_settings WHERE id = 1";
$result = $conn->query($query);
$settings = $result->fetch_assoc();
$secret_key = $settings['paystack_secret_key'];

// 3. Call Paystack API to verify the transaction
$curl = curl_init();
curl_setopt_array($curl, array(
    CURLOPT_URL => "https://api.paystack.co/transaction/verify/" . rawurlencode($reference),
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_ENCODING => "",
    CURLOPT_MAXREDIRS => 10,
    CURLOPT_TIMEOUT => 30,
    CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
    CURLOPT_CUSTOMREQUEST => "GET",
    CURLOPT_HTTPHEADER => array(
        "Authorization: Bearer " . $secret_key,
        "Cache-Control: no-cache",
    ),
));

$response = curl_exec($curl);
$err = curl_error($curl);
curl_close($curl);

if ($err) {
    die("cURL Error #:" . $err);
}

$tranx = json_decode($response);

if (!$tranx->status) {
    die("API returned error: " . $tranx->message);
}

// 4. If transaction is successful, update the database
if ('success' == $tranx->data->status) {
    $email = $tranx->data->customer->email;
    $amount = $tranx->data->amount / 100; // Paystack sends amount in Kobo
    
    // Update member status and payment reference
    $update_query = "UPDATE members SET 
                     status = 'Active', 
                     payment_ref = ?, 
                     last_dues_payment = NOW() 
                     WHERE email = ?";
    
    $stmt = $conn->prepare($update_query);
    $stmt->bind_param("ss", $reference, $email);
    
    if ($stmt->execute()) {
        // Redirect to a success page
        header("Location: ../member/dashboard.php?payment=success");
    } else {
        echo "Error updating record: " . $conn->error;
    }
} else {
    header("Location: ../member/dashboard.php?payment=failed");
}
?>
