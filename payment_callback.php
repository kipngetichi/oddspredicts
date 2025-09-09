<?php
// Callback handler for M-PESA STK Push
// Update DB credentials below

$supabaseUrl = 'https://qjymkjcfuzawkykkxzuu.supabase.co'; // Replace with your Supabase project URL
$supabaseKey = 'eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJpc3MiOiJzdXBhYmFzZSIsInJlZiI6InFqeW1ramNmdXphd2t5a2t4enV1Iiwicm9sZSI6ImFub24iLCJpYXQiOjE3NTA2MDgwODMsImV4cCI6MjA2NjE4NDA4M30.zW56UdCNAMwJ702p5sRupPDcJ5JWISFjNfENXAlBmAo'; // Replace with your Supabase service role key

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
    // Store in Supabase
    $payload = json_encode([
        'phone' => $phone,
        'mpesa_code' => $mpesaCode,
        'amount' => $amount,
        'paid_at' => date('Y-m-d H:i:s')
    ]);
    $ch = curl_init($supabaseUrl . '/rest/v1/subscribers');
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'apikey: ' . $supabaseKey,
        'Authorization: Bearer ' . $supabaseKey,
        'Content-Type: application/json',
        'Prefer: return=minimal'
    ]);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, '[' . $payload . ']');
    curl_exec($ch);
    curl_close($ch);
}
file_put_contents('mpesa_callback_log.txt', json_encode($data) . "\n", FILE_APPEND);
?>
