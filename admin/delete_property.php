<?php
// Set headers for JSON response
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, GET, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Handle preflight OPTIONS request
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

// Get the request method
$method = $_SERVER['REQUEST_METHOD'];

// Path to the JSON file
$jsonFilePath = __DIR__ . '/../js/data.json';

// Check if the JSON file exists
if (!file_exists($jsonFilePath)) {
    echo json_encode(['success' => false, 'message' => 'Data file not found']);
    exit;
}

// Read the existing JSON data
$jsonContent = file_get_contents($jsonFilePath);
$data = json_decode($jsonContent, true);

if ($data === null) {
    echo json_encode(['success' => false, 'message' => 'Error reading data file']);
    exit;
}

// POST request - Delete a property
if ($method === 'POST') {
    $category = $_POST['category'] ?? '';
    $id = intval($_POST['id'] ?? 0);
    
    // Validate required fields
    if (empty($category) || empty($id)) {
        echo json_encode(['success' => false, 'message' => 'Category and ID are required']);
        exit;
    }
    
    // Check if category exists
    if (!isset($data[$category])) {
        echo json_encode(['success' => false, 'message' => 'Category not found']);
        exit;
    }
    
    // Check if category array exists
    if (!is_array($data[$category])) {
        echo json_encode(['success' => false, 'message' => 'Invalid category']);
        exit;
    }
    
    // Find and delete the property
    $initialCount = count($data[$category]);
    $data[$category] = array_values(array_filter($data[$category], function($property) use ($id) {
        return $property['id'] !== $id;
    }));
    $finalCount = count($data[$category]);
    
    if ($initialCount === $finalCount) {
        echo json_encode(['success' => false, 'message' => 'Property not found']);
        exit;
    }
    
    // Encode the updated data back to JSON
    $newJsonContent = json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
    
    // Write the updated data back to the file
    if (file_put_contents($jsonFilePath, $newJsonContent)) {
        echo json_encode([
            'success' => true,
            'message' => 'Property deleted successfully!'
        ]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Error saving data to file']);
    }
    exit;
}

echo json_encode(['success' => false, 'message' => 'Invalid request method']);
?>
