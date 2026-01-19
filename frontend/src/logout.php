<?php
session_start();
session_unset();
session_destroy();

// Clear session cookie
if (isset($_COOKIE[session_name()])) {
    setcookie(session_name(), '', time() - 3600, '/');
}

// Handle both GET and POST requests
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    echo json_encode(['status' => 'success']);
} else {
    header('Location: login.php');
}
exit();
?>