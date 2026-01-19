<?php
session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $input = json_decode(file_get_contents('php://input'), true);
    
    if (isset($input['username'])) {
        $_SESSION['authenticated'] = true;
        $_SESSION['username'] = $input['username'];
        $_SESSION['user_id'] = $input['user_id'] ?? null;
        $_SESSION['last_activity'] = time();
        
        echo json_encode(['status' => 'success']);
    } else {
        http_response_code(400);
        echo json_encode(['status' => 'error', 'message' => 'Invalid data']);
    }
} else {
    http_response_code(405);
    echo json_encode(['status' => 'error', 'message' => 'Method not allowed']);
}
?>