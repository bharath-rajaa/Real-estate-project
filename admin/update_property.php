<?php
// Set headers for JSON response
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, GET, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

// Enable error reporting for debugging
// Disable error reporting for production/clean JSON
error_reporting(0);
ini_set('display_errors', 0);

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

// GET request - Get a specific property
if ($method === 'GET') {
    $category = $_GET['category'] ?? '';
    $id = intval($_GET['id'] ?? 0);
    
    if (empty($category) || empty($id)) {
        echo json_encode(['success' => false, 'message' => 'Category and ID are required']);
        exit;
    }
    
    $properties = $data[$category] ?? [];
    
    foreach ($properties as $property) {
        if ($property['id'] === $id) {
            echo json_encode(['success' => true, 'property' => $property]);
            exit;
        }
    }
    
    echo json_encode(['success' => false, 'message' => 'Property not found']);
    exit;
}

// POST request - Update a property
if ($method === 'POST') {
    $category = $_POST['category'] ?? '';
    $id = intval($_POST['id'] ?? 0);
    
    // Validate required fields
    if (empty($category) || empty($id)) {
        echo json_encode(['success' => false, 'message' => 'Category and ID are required']);
        exit;
    }
    
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
    if (empty($name) || empty($location) || empty($description) || empty($sqft) || empty($price) || empty($status)) {
        echo json_encode(['success' => false, 'message' => 'Please fill in all required fields']);
        exit;
    }
    
    // Check if category exists
    if (!isset($data[$category])) {
        echo json_encode(['success' => false, 'message' => 'Category not found']);
        exit;
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
    }
    
    // Find and update the property
    $updated = false;
    foreach ($data[$category] as &$property) {
        if ($property['id'] === $id) {
            $property['name'] = $name;
            $property['location'] = $location;
            $property['latitude'] = $latitude ? floatval($latitude) : null;
            $property['longitude'] = $longitude ? floatval($longitude) : null;
            $property['nearby_landmarks'] = $landmarksArray;
            $property['description'] = $description;
            $property['bhk'] = $bhk;
            $property['sqft'] = intval($sqft);
            $property['price'] = intval($price);
            $property['emi'] = $emi ? intval($emi) : null;
            $property['status'] = $status;
            $property['features'] = $featuresArray;
            if (!empty($imagesArray)) {
                $property['images'] = $imagesArray;
            }
            $updated = true;
            break;
        }
    }
    
    if (!$updated) {
        echo json_encode(['success' => false, 'message' => 'Property not found']);
        exit;
    }
    
    // Encode the updated data back to JSON
    $newJsonContent = json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
    
    // Write the updated data back to the file
    if (file_put_contents($jsonFilePath, $newJsonContent)) {
        echo json_encode([
            'success' => true,
            'message' => 'Property updated successfully!',
            'property' => $data[$category][array_search($id, array_column($data[$category], 'id'))]
        ]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Error saving data to file']);
    }
    exit;
}

echo json_encode(['success' => false, 'message' => 'Invalid request method']);
?>
