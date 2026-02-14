<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');

$response = [
    'status' => 'ok',
    'message' => 'PHP is running'
];

echo json_encode($response);
?>
