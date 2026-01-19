<?php
// TIER 2: AUTHENTICATION API

// Configure session before starting
ini_set('session.cookie_httponly', 1);
ini_set('session.cookie_secure', 0); // Set to 1 if using HTTPS
ini_set('session.cookie_samesite', 'Lax');
ini_set('session.use_strict_mode', 1);

// Start session first
session_start();

// Disable error display
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

try {
    // Include database connection
    require_once dirname(__DIR__) . '/db.php';

    $method = $_SERVER['REQUEST_METHOD'];

    $data = [];
    if (in_array($method, ['POST', 'DELETE'])) {
        $input_json = file_get_contents('php://input');
        $data = json_decode($input_json, true);
        
        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new Exception('Invalid JSON input');
        }
    }

    // --- POST Request (Login) ---
    if ($method === 'POST') {
        $username = trim($data['username'] ?? '');
        $password = $data['password'] ?? '';
        
        if (empty($username) || empty($password)) {
            http_response_code(400);
            echo json_encode(['status' => 'error', 'message' => 'Username and password required']);
            exit;
        }
        
        // Check if users table exists
        $tableCheck = $conn->query("SHOW TABLES LIKE 'users'");
        if ($tableCheck->num_rows == 0) {
            http_response_code(401);
            echo json_encode(['status' => 'error', 'message' => 'No admin users exist. Please create one using CLI.']);
            exit;
        }
        
        $sql = "SELECT id, username, password_hash FROM users WHERE username = ?";
        $stmt = $conn->prepare($sql);

        if (!$stmt) {
            throw new Exception('Database prepare failed: ' . $conn->error);
        }

        $stmt->bind_param("s", $username);
        $stmt->execute();
        $result = $stmt->get_result();
        $user = $result->fetch_assoc();

        if (!$user || !password_verify($password, $user['password_hash'])) {
            http_response_code(401);
            echo json_encode(['status' => 'error', 'message' => 'Invalid credentials']);
            exit;
        }

        // Set session variables with correct names
        $_SESSION['admin_id'] = $user['id'];
        $_SESSION['admin_username'] = $user['username'];
        $_SESSION['admin_logged_in'] = true;
        $_SESSION['authenticated'] = true;
        $_SESSION['username'] = $user['username'];
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['last_activity'] = time();

        echo json_encode([
            'status' => 'success',
            'message' => 'Welcome to the Neo-Brutalist Hub!',
            'user' => [
                'id' => $user['id'],
                'username' => $user['username']
            ]
        ]);
        exit;
    }

    // --- GET Request (Check session) ---
    elseif ($method === 'GET') {
        if (isset($_SESSION['authenticated']) && $_SESSION['authenticated'] === true) {
            echo json_encode([
                'status' => 'authenticated',
                'user' => [
                    'id' => $_SESSION['user_id'] ?? null,
                    'username' => $_SESSION['username'] ?? null
                ]
            ]);
        } else {
            http_response_code(401);
            echo json_encode(['status' => 'unauthenticated']);
        }
        exit;
    }

    // --- DELETE Request (Logout) ---
    elseif ($method === 'DELETE') {
        session_unset();
        session_destroy();
        echo json_encode(['status' => 'success', 'message' => 'Logged out successfully']);
        exit;
    }

    else {
        http_response_code(405);
        echo json_encode(['status' => 'error', 'message' => 'Method not supported']);
        exit;
    }

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'status' => 'error',
        'message' => 'Internal server error',
        'detail' => $e->getMessage()
    ]);
    exit;
}
?>