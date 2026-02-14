<?php
// Test script to verify PHP backend is working
header('Content-Type: text/plain');

echo "PHP Backend Test\n\n";

echo "Testing add_property.php:\n";
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, 'http://localhost:8000/admin/add_property.php');
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, [
    'category' => 'bank_properties',
    'name' => 'Test Property',
    'location' => 'Test Location',
    'description' => 'Test Description',
    'sqft' => 1000,
    'price' => 1000000,
    'status' => 'Available'
]);
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'Content-Type: application/x-www-form-urlencoded'
]);

$result = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

echo "HTTP Code: $httpCode\n";
echo "Response: $result\n\n";

echo "Testing check_connection.php:\n";
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, 'http://localhost:8000/admin/check_connection.php');
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
$result = curl_exec($ch);
curl_close($ch);

echo "Response: $result\n";
?>