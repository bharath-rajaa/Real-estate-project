<?php
// Set headers for JSON response
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

// Handle preflight OPTIONS request
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

// Disable error reporting for production/clean JSON
error_reporting(0);
ini_set('display_errors', 0);

// Check if the request method is POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
    exit;
}

// Get form data
$category = $_POST['category'] ?? '';
$name = $_POST['name'] ?? '';
$location = $_POST['location'] ?? '';
$latitude = $_POST['latitude'] ?? null;
$longitude = $_POST['longitude'] ?? null;
$description = $_POST['description'] ?? '';
$bhk = $_POST['bhk'] ?? null;
$sqft = $_POST['sqft'] ?? 0;
$price = $_POST['price'] ?? 0;
$emi = $_POST['emi'] ?? null;
$status = $_POST['status'] ?? '';
$nearby_landmarks = $_POST['nearby_landmarks'] ?? '';
$features = $_POST['features'] ?? '';
$image_url = $_POST['image_url'] ?? '';

// Validate required fields
if (empty($category) || empty($name) || empty($location) || empty($description) || empty($sqft) || empty($price) || empty($status)) {
    echo json_encode(['success' => false, 'message' => 'Please fill in all required fields']);
    exit;
}

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

// Get the next ID for the category
$categoryData = $data[$category] ?? [];
$nextId = 1;
if (!empty($categoryData)) {
    $maxId = max(array_column($categoryData, 'id'));
    $nextId = $maxId + 1;
}

// Parse nearby landmarks
$landmarksArray = [];
if (!empty($nearby_landmarks)) {
    $landmarksArray = array_map('trim', explode(',', $nearby_landmarks));
}

// Parse features
$featuresArray = [];
if (!empty($features)) {
    $featuresArray = array_map('trim', explode(',', $features));
}

// Parse images
$imagesArray = [];
if (!empty($image_url)) {
    $imagesArray = [$image_url];
} else {
    // Generate a random image URL if none provided
    $imagesArray = ["https://picsum.photos/seed/property" . $nextId . "/600/400"];
}

// Create the new property object
$newProperty = [
    'id' => $nextId,
    'category' => ucfirst(str_replace('_', ' ', str_replace('_properties', '', $category))),
    'name' => $name,
    'location' => $location,
    'latitude' => $latitude ? floatval($latitude) : null,
    'longitude' => $longitude ? floatval($longitude) : null,
    'nearby_landmarks' => $landmarksArray,
    'description' => $description,
    'bhk' => $bhk,
    'sqft' => intval($sqft),
    'price' => intval($price),
    'emi' => $emi ? intval($emi) : null,
    'status' => $status,
    'features' => $featuresArray,
    'images' => $imagesArray
];

// Add the new property to the category array
$data[$category][] = $newProperty;

// Encode the updated data back to JSON
$newJsonContent = json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);

// Write the updated data back to the file
if (file_put_contents($jsonFilePath, $newJsonContent)) {
    $response = [
        'success' => true,
        'message' => 'Property added successfully!',
        'property' => $newProperty
    ];
    echo json_encode($response);
} else {
    $response = ['success' => false, 'message' => 'Error saving data to file'];
    echo json_encode($response);
}
?>
