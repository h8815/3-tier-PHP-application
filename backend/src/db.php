<?php
// TIER 3: DATA LAYER CONNECTION - Environment Variable Ready

// Disable error display in production
error_reporting(E_ALL);
ini_set('display_errors', 0);
ini_set('log_errors', 1);

// Session configuration for shared sessions across requests
ini_set('session.save_path', getenv('SESSION_SAVE_PATH') ?: '/tmp/sessions');
ini_set('session.gc_probability', 1);
ini_set('session.gc_divisor', 100);

// Retrieve credentials from environment variables
$servername = getenv('DB_HOST') ?: 'database';
$username = getenv('DB_USER') ?: 'DBuser';
$password = getenv('DB_PASSWORD') ?: 'root';
$dbname = getenv('DB_NAME') ?: 'student_db';

// Validate that all required environment variables are set
if (!$servername || !$username || $password === false || !$dbname) {
    if (php_sapi_name() === 'cli') {
        echo "Error: Missing required environment variables.\n";
        exit(1);
    }
    
    // Only set headers if not already sent
    if (!headers_sent()) {
        header('Content-Type: application/json');
        http_response_code(500);
    }
    
    die(json_encode([
        "error" => "Missing required environment variables",
        "detail" => "Please set DB_HOST, DB_USER, DB_PASSWORD, and DB_NAME"
    ]));
}

define('DB_TABLE_STUDENT', 'student');

// Only set CORS headers if not already sent and not in CLI mode
if (!headers_sent() && php_sapi_name() !== 'cli') {
    header('Access-Control-Allow-Origin: *');
    header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
    header('Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With');
    header('Access-Control-Allow-Credentials: true');

    // Handle browser preflight OPTIONS request
    if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
        http_response_code(200);
        exit;
    }
}

try {
    $conn = new mysqli($servername, $username, $password, $dbname);
    
    if ($conn->connect_error) {
        throw new Exception("Database connection failed: " . $conn->connect_error);
    }

    // Set charset to utf8mb4 for better compatibility
    $conn->set_charset("utf8mb4");
    
} catch (Exception $e) {
    if (php_sapi_name() === 'cli') {
        echo "Database Error: " . $e->getMessage() . "\n";
        exit(1);
    }
    
    if (!headers_sent()) {
        header('Content-Type: application/json');
        http_response_code(500);
    }
    
    die(json_encode([
        "error" => "Database connection failed",
        "detail" => $e->getMessage()
    ]));
}
?>