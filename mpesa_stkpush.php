<?php
// M-PESA STK Push for till 541299
// Update credentials below
$consumerKey = 'YOUR_CONSUMER_KEY';
$consumerSecret = 'YOUR_CONSUMER_SECRET';
$shortCode = '541299';
$passkey = 'YOUR_PASSKEY';
$callbackUrl = 'https://YOUR_DOMAIN/payment_callback.php';
$dbHost = 'localhost';
$dbUser = 'YOUR_DB_USER';
$dbPass = 'YOUR_DB_PASSWORD';
$dbName = 'YOUR_DB_NAME';

header('Content-Type: application/json');
if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !isset($_POST['phone'])) {
    echo json_encode(['success' => false, 'message' => 'Invalid request.']);
    exit;
}
$phone = preg_replace('/\D/', '', $_POST['phone']);
if (strlen($phone) == 10 && substr($phone, 0, 2) == '07') {
    $phone = '254' . substr($phone, 1);
}
if (!preg_match('/^2547\d{8}$/', $phone)) {
    echo json_encode(['success' => false, 'message' => 'Invalid phone number.']);
    exit;
}
// Get access token
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, 'https://api.safaricom.co.ke/oauth/v1/generate?grant_type=client_credentials');
curl_setopt($ch, CURLOPT_HTTPHEADER, ['Authorization: Basic ' . base64_encode($consumerKey . ':' . $consumerSecret)]);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
$token_response = curl_exec($ch);
curl_close($ch);
$token = json_decode($token_response, true)['access_token'] ?? '';
if (!$token) {
    echo json_encode(['success' => false, 'message' => 'Failed to get access token.']);
    exit;
}
// Prepare STK Push
$timestamp = date('YmdHis');
$password = base64_encode($shortCode . $passkey . $timestamp);
$payload = [
    'BusinessShortCode' => $shortCode,
    'Password' => $password,
    'Timestamp' => $timestamp,
    'TransactionType' => 'CustomerPayBillOnline',
    'Amount' => 225,
    'PartyA' => $phone,
    'PartyB' => $shortCode,
    'PhoneNumber' => $phone,
    'CallBackURL' => $callbackUrl,
    'AccountReference' => 'ODDSPREDICTS',
    'TransactionDesc' => 'Subscription'
];
$ch = curl_init('https://api.safaricom.co.ke/mpesa/stkpush/v1/processrequest');
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'Content-Type: application/json',
    'Authorization: Bearer ' . $token
]);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($payload));
$response = curl_exec($ch);
curl_close($ch);
$res = json_decode($response, true);
if (isset($res['ResponseCode']) && $res['ResponseCode'] == '0') {
    echo json_encode(['success' => true, 'message' => 'Payment request sent. Please check your phone to complete payment.']);
} else {
    $msg = $res['errorMessage'] ?? 'Failed to initiate payment.';
    echo json_encode(['success' => false, 'message' => $msg]);
}
?>
