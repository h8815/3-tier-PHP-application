<?php
// Disable error display in production
error_reporting(E_ALL);
ini_set('display_errors', 0);
ini_set('log_errors', 1);

// Include CORS helper
require_once dirname(__DIR__) . '/cors-helper.php';

// Set headers
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
    $input = json_decode(file_get_contents('php://input'), true);
    $student_id = $input['student_id'] ?? null;
    
    if (!$student_id) {
        http_response_code(400);
        echo json_encode(['status' => 'error', 'message' => 'Student ID required']);
        exit;
    }
    
    // Get current photo path and verify admin ownership
    $sql = "SELECT profile_photo FROM " . DB_TABLE_STUDENT . " WHERE ID = ? AND admin_id = ?";
    $stmt = $conn->prepare($sql);
    $adminId = $_SESSION['user_id'] ?? null;
    $stmt->bind_param("ii", $student_id, $adminId);
    $stmt->execute();
    $result = $stmt->get_result();
    $student = $result->fetch_assoc();
    
    if (!$student) {
        http_response_code(404);
        echo json_encode(['status' => 'error', 'message' => 'Student not found or access denied']);
        exit;
    }
    
    // Remove photo from database
    $sql = "UPDATE " . DB_TABLE_STUDENT . " SET profile_photo = NULL WHERE ID = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $student_id);
    
    if ($stmt->execute()) {
        // Try to delete physical file
        if ($student['profile_photo']) {
            $filePath = '/var/www/html/' . $student['profile_photo'];
            if (file_exists($filePath)) {
                unlink($filePath);
            }
        }
        
        echo json_encode([
            'status' => 'success',
            'message' => 'Photo removed successfully'
        ]);
    } else {
        http_response_code(500);
        echo json_encode(['status' => 'error', 'message' => 'Database update failed']);
    }
    
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['status' => 'error', 'message' => 'Server error: ' . $e->getMessage()]);
}
?>