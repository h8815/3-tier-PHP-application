<?php
// Disable error display, log instead
error_reporting(E_ALL);
ini_set('display_errors', 0);
ini_set('log_errors', 1);

// Include CORS helper
require_once dirname(__DIR__) . '/cors-helper.php';

// Config endpoint to provide API URLs to frontend
header('Content-Type: application/json');
setCorsHeaders();

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

// Validate origin for security (disabled for development)
// if (!validateOrigin() && php_sapi_name() !== 'cli') {
//     http_response_code(403);
//     echo json_encode(['status' => 'error', 'message' => 'Origin not allowed']);
//     exit;
// }

try {
    // Get the base URL from environment or detect from request
    $apiBaseUrl = getenv('API_BASE_URL');

    // If not set, try to detect from request
    if (!$apiBaseUrl) {
        $protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
        $host = $_SERVER['HTTP_HOST'] ?? 'localhost:3000';
        $apiBaseUrl = "$protocol://$host";
    }

    echo json_encode([
        'apiBaseUrl' => $apiBaseUrl,
        'studentsUrl' => $apiBaseUrl . '/api/students.php',
        'authUrl' => $apiBaseUrl . '/api/auth.php'
    ]);
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'error' => 'Configuration error',
        'message' => $e->getMessage()
    ]);
}
?>