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

    // --- POST Request (Login or Register) ---
    if ($method === 'POST') {
        $action = $data['action'] ?? 'login';  // Default to login for backwards compatibility
        $username = trim($data['username'] ?? '');
        $password = $data['password'] ?? '';
        
        if (empty($username) || empty($password)) {
            http_response_code(400);
            echo json_encode(['status' => 'error', 'message' => 'Username and password required']);
            exit;
        }
        
        // Check if users table exists
        $tableCheck = $conn->query("SHOW TABLES LIKE 'users'");
        if ($tableCheck->num_rows == 0 && $action === 'login') {
            http_response_code(401);
            echo json_encode(['status' => 'error', 'message' => 'No users exist. Please create an account first.']);
            exit;
        }
        
        if ($action === 'register') {
            // Registration logic
            
            // Validate username length
            if (strlen($username) < 3) {
                http_response_code(400);
                echo json_encode(['status' => 'error', 'message' => 'Username must be at least 3 characters long']);
                exit;
            }
            
            // Validate password strength
            $passwordErrors = [];
            
            if (strlen($password) < 8) {
                $passwordErrors[] = 'Password must be at least 8 characters long';
            }
            if (!preg_match('/[A-Z]/', $password)) {
                $passwordErrors[] = 'Password must contain at least 1 uppercase letter (A-Z)';
            }
            if (!preg_match('/[a-z]/', $password)) {
                $passwordErrors[] = 'Password must contain at least 1 lowercase letter (a-z)';
            }
            if (!preg_match('/[0-9]/', $password)) {
                $passwordErrors[] = 'Password must contain at least 1 number (0-9)';
            }
            if (!preg_match('/[!@#$%^&*()_+\-=\[\]{}|;:,.<>?]/', $password)) {
                $passwordErrors[] = 'Password must contain at least 1 special character (!@#$%^&*()_+-=[]{}|;:,.<>?)';
            }
            
            if (!empty($passwordErrors)) {
                http_response_code(400);
                echo json_encode([
                    'status' => 'error', 
                    'message' => 'Password does not meet requirements: ' . implode('; ', $passwordErrors)
                ]);
                exit;
            }
            
            // Create users table if it doesn't exist
            if ($tableCheck->num_rows == 0) {
                $createTable = "CREATE TABLE users (
                    id INT(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
                    username VARCHAR(50) NOT NULL UNIQUE,
                    password_hash VARCHAR(255) NOT NULL,
                    created_at DATETIME DEFAULT CURRENT_TIMESTAMP
                )";
                if (!$conn->query($createTable)) {
                    throw new Exception('Failed to create users table: ' . $conn->error);
                }
            }
            
            // Check if username already exists
            $checkSql = "SELECT id FROM users WHERE username = ?";
            $checkStmt = $conn->prepare($checkSql);
            
            if (!$checkStmt) {
                throw new Exception('Database prepare failed: ' . $conn->error);
            }
            
            $checkStmt->bind_param("s", $username);
            $checkStmt->execute();
            $checkResult = $checkStmt->get_result();
            
            if ($checkResult->num_rows > 0) {
                http_response_code(400);
                echo json_encode(['status' => 'error', 'message' => 'Username already exists. Please choose a different one.']);
                exit;
            }
            
            // Hash password with ARGON2ID
            $passwordHash = password_hash($password, PASSWORD_ARGON2ID);
            
            // Insert new user
            $insertSql = "INSERT INTO users (username, password_hash) VALUES (?, ?)";
            $insertStmt = $conn->prepare($insertSql);
            
            if (!$insertStmt) {
                throw new Exception('Database prepare failed: ' . $conn->error);
            }
            
            $insertStmt->bind_param("ss", $username, $passwordHash);
            
            if ($insertStmt->execute()) {
                http_response_code(201);
                echo json_encode([
                    'status' => 'success',
                    'message' => 'Account created successfully! You can now login.'
                ]);
                exit;
            } else {
                throw new Exception('Failed to create account: ' . $conn->error);
            }
        } 
        else if ($action === 'login') {
            // Login logic
            
            // Check if users table exists
            if ($tableCheck->num_rows == 0) {
                http_response_code(401);
                echo json_encode(['status' => 'error', 'message' => 'No users exist. Please create an account first.']);
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
        else {
            http_response_code(400);
            echo json_encode(['status' => 'error', 'message' => 'Invalid action']);
            exit;
        }
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