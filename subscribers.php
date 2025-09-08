<?php
// Admin page to view paid subscribers
$dbHost = 'localhost';
$dbUser = 'YOUR_DB_USER';
$dbPass = 'YOUR_DB_PASSWORD';
$dbName = 'YOUR_DB_NAME';
$conn = new mysqli($dbHost, $dbUser, $dbPass, $dbName);
if ($conn->connect_error) die('DB connection failed');
$res = $conn->query('SELECT phone, mpesa_code, amount, paid_at FROM subscribers ORDER BY paid_at DESC');
echo '<h2>Paid Subscribers</h2>';
echo '<table border="1" cellpadding="8"><tr><th>Phone</th><th>Mpesa Code</th><th>Amount</th><th>Date Paid</th></tr>';
while ($row = $res->fetch_assoc()) {
    echo '<tr><td>' . htmlspecialchars($row['phone']) . '</td><td>' . htmlspecialchars($row['mpesa_code']) . '</td><td>' . $row['amount'] . '</td><td>' . $row['paid_at'] . '</td></tr>';
}
echo '</table>';
$conn->close();
?>
