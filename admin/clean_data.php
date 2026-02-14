<?php
$jsonFilePath = __DIR__ . '/../js/data.json';
$jsonContent = file_get_contents($jsonFilePath);
$data = json_decode($jsonContent, true);

// Remove the last two items from bank_properties which are the test data
if (isset($data['bank_properties'])) {
    // Check if the last item is a test item (id 7)
    $lastItem = end($data['bank_properties']);
    if ($lastItem['id'] == 7) {
        array_pop($data['bank_properties']);
    }
    
    // Check if the new last item is a test item (id 6)
    $lastItem = end($data['bank_properties']);
    if ($lastItem['id'] == 6) {
        array_pop($data['bank_properties']);
    }
}

$newJsonContent = json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
file_put_contents($jsonFilePath, $newJsonContent);
echo "Cleaned up test data.";
?>
