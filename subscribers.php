
<?php
// Admin page to view paid subscribers from Supabase
$supabaseUrl = 'https://YOUR_SUPABASE_PROJECT.supabase.co'; // Replace with your Supabase project URL
$supabaseKey = 'YOUR_SUPABASE_SERVICE_ROLE_KEY'; // Replace with your Supabase service role key
$ch = curl_init($supabaseUrl . '/rest/v1/subscribers?select=phone,mpesa_code,amount,paid_at&order=paid_at.desc');
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'apikey: ' . $supabaseKey,
    'Authorization: Bearer ' . $supabaseKey,
    'Content-Type: application/json'
]);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
$response = curl_exec($ch);
curl_close($ch);
$rows = json_decode($response, true);
echo '<h2>Paid Subscribers</h2>';
echo '<table border="1" cellpadding="8"><tr><th>Phone</th><th>Mpesa Code</th><th>Amount</th><th>Date Paid</th></tr>';
if ($rows) {
    foreach ($rows as $row) {
        echo '<tr><td>' . htmlspecialchars($row['phone']) . '</td><td>' . htmlspecialchars($row['mpesa_code']) . '</td><td>' . $row['amount'] . '</td><td>' . $row['paid_at'] . '</td></tr>';
    }
}
echo '</table>';
?>

$supabaseUrl = 'https://YOUR_SUPABASE_PROJECT.supabase.co'; // Replace with your Supabase project URL
$supabaseKey = 'YOUR_SUPABASE_SERVICE_ROLE_KEY'; // Replace with your Supabase service role key
$ch = curl_init($supabaseUrl . '/rest/v1/subscribers?select=phone,mpesa_code,amount,paid_at&order=paid_at.desc');
curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'apikey: ' . $supabaseKey,
        'Authorization: Bearer ' . $supabaseKey,
        'Content-Type: application/json'
]);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
$response = curl_exec($ch);
curl_close($ch);
$rows = json_decode($response, true);
echo '<h2>Paid Subscribers</h2>';
echo '<table border="1" cellpadding="8"><tr><th>Phone</th><th>Mpesa Code</th><th>Amount</th><th>Date Paid</th></tr>';
if ($rows) {
    foreach ($rows as $row) {
        echo '<tr><td>' . htmlspecialchars($row['phone']) . '</td><td>' . htmlspecialchars($row['mpesa_code']) . '</td><td>' . $row['amount'] . '</td><td>' . $row['paid_at'] . '</td></tr>';
    }
}
echo '</table>';
?>
