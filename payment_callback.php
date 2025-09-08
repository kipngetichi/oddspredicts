<?php
// Callback handler for M-PESA STK Push
// Update DB credentials below
$dbHost = 'localhost';
$dbUser = 'YOUR_DB_USER';
$dbPass = 'YOUR_DB_PASSWORD';
$dbName = 'YOUR_DB_NAME';

// Read JSON from Safaricom
$data = json_decode(file_get_contents('php://input'), true);
if (!$data || !isset($data['Body']['stkCallback'])) exit;
$callback = $data['Body']['stkCallback'];
$resultCode = $callback['ResultCode'];
$resultDesc = $callback['ResultDesc'];
$amount = 225;
$phone = '';
$mpesaCode = '';
if ($resultCode == 0) {
    foreach ($callback['CallbackMetadata']['Item'] as $item) {
        if ($item['Name'] == 'PhoneNumber') $phone = $item['Value'];
        if ($item['Name'] == 'MpesaReceiptNumber') $mpesaCode = $item['Value'];
    }
    // Store in DB
    $conn = new mysqli($dbHost, $dbUser, $dbPass, $dbName);
    if ($conn->connect_error) exit;
    $stmt = $conn->prepare("INSERT INTO subscribers (phone, mpesa_code, amount, paid_at) VALUES (?, ?, ?, NOW())");
    $stmt->bind_param('ssi', $phone, $mpesaCode, $amount);
    $stmt->execute();
    $stmt->close();
    $conn->close();
}
file_put_contents('mpesa_callback_log.txt', json_encode($data) . "\n", FILE_APPEND);
?>
