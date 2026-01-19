<?php
// Disable error display in production
error_reporting(E_ALL);
ini_set('display_errors', 0);
ini_set('log_errors', 1);

// Include CORS helper
require_once dirname(__DIR__) . '/cors-helper.php';

// Set headers BEFORE including db.php
header('Content-Type: application/json');
setCorsHeaders();

// Handle preflight
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

// Validate origin for security
if (!validateOrigin() && php_sapi_name() !== 'cli') {
    http_response_code(403);
    echo json_encode(['status' => 'error', 'message' => 'Origin not allowed']);
    exit;
}

session_start();

// Check authentication
if (!isset($_SESSION['authenticated']) || $_SESSION['authenticated'] !== true) {
    http_response_code(401);
    echo json_encode(['status' => 'error', 'message' => 'Authentication required']);
    exit;
}

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['status' => 'error', 'message' => 'Method not allowed']);
    exit;
}

require_once dirname(__DIR__) . '/db.php';

try {
    $student_id = $_POST['student_id'] ?? null;
    
    if (!$student_id || !isset($_FILES['profile_photo'])) {
        http_response_code(400);
        echo json_encode(['status' => 'error', 'message' => 'Student ID and photo file required']);
        exit;
    }
    
    $file = $_FILES['profile_photo'];
    
    // Validate file
    $allowedTypes = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
    if (!in_array($file['type'], $allowedTypes)) {
        http_response_code(400);
        echo json_encode(['status' => 'error', 'message' => 'Invalid file type. Only JPG, PNG, GIF, WEBP allowed']);
        exit;
    }
    
    if ($file['size'] > 5242880) { // 5MB
        http_response_code(400);
        echo json_encode(['status' => 'error', 'message' => 'File size must be less than 5MB']);
        exit;
    }
    
    // Create uploads directory if it doesn't exist
    $uploadDir = '/var/www/html/uploads/';
    if (!is_dir($uploadDir)) {
        mkdir($uploadDir, 0755, true);
    }
    
    // Generate unique filename
    $extension = pathinfo($file['name'], PATHINFO_EXTENSION);
    $filename = 'student_' . $student_id . '_' . uniqid() . '.' . $extension;
    $filepath = $uploadDir . $filename;
    
    // Move uploaded file
    if (move_uploaded_file($file['tmp_name'], $filepath)) {
        // Update database
        $photoPath = 'uploads/' . $filename;
        $sql = "UPDATE " . DB_TABLE_STUDENT . " SET profile_photo = ? WHERE ID = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("si", $photoPath, $student_id);
        
        if ($stmt->execute()) {
            echo json_encode([
                'status' => 'success',
                'message' => 'Photo uploaded successfully',
                'photo_path' => $photoPath
            ]);
        } else {
            // Remove uploaded file if database update fails
            unlink($filepath);
            http_response_code(500);
            echo json_encode(['status' => 'error', 'message' => 'Database update failed']);
        }
    } else {
        http_response_code(500);
        echo json_encode(['status' => 'error', 'message' => 'File upload failed']);
    }
    
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['status' => 'error', 'message' => 'Server error: ' . $e->getMessage()]);
}
?>