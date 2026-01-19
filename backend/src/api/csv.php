<?php
session_start();

// Include CORS helper
require_once dirname(__DIR__) . '/cors-helper.php';

include '../db.php';

header('Strict-Transport-Security: max-age=31536000; includeSubDomains');
setCorsHeaders();

// Handle preflight
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

$method = $_SERVER['REQUEST_METHOD'];

function isAuthenticated() {
    return isset($_SESSION['authenticated']) && $_SESSION['authenticated'] === true && isset($_SESSION['user_id']);
}

function getCurrentAdminId() {
    return $_SESSION['user_id'] ?? null;
}

// --- GET Request (Export CSV) ---
if ($method === 'GET') {
    if (!isAuthenticated()) {
        http_response_code(401);
        echo json_encode(['status' => 'error', 'message' => 'Authentication required']);
        exit;
    }
    
    $adminId = getCurrentAdminId();
    
    // Get all students for current admin
    $sql = "SELECT Name, Email, Age, phone, address, status, enrollment_date FROM " . DB_TABLE_STUDENT . " WHERE admin_id = ? ORDER BY Name";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $adminId);
    $stmt->execute();
    $result = $stmt->get_result();
    
    // Set CSV headers
    header('Content-Type: text/csv');
    header('Content-Disposition: attachment; filename="students_export_' . date('Y-m-d_H-i-s') . '.csv"');
    
    // Create CSV output
    $output = fopen('php://output', 'w');
    
    // CSV Header
    fputcsv($output, ['Name', 'Email', 'Age', 'Phone', 'Address', 'Status', 'Enrollment Date']);
    
    // CSV Data
    while ($row = $result->fetch_assoc()) {
        fputcsv($output, [
            $row['Name'],
            $row['Email'],
            $row['Age'],
            $row['phone'] ?? '',
            $row['address'] ?? '',
            $row['status'],
            date('Y-m-d H:i:s', strtotime($row['enrollment_date']))
        ]);
    }
    
    fclose($output);
    exit;
}

// --- POST Request (Import CSV) ---
elseif ($method === 'POST') {
    if (!isAuthenticated()) {
        http_response_code(401);
        echo json_encode(['status' => 'error', 'message' => 'Authentication required']);
        exit;
    }
    
    header('Content-Type: application/json');
    
    if (!isset($_FILES['csv_file'])) {
        http_response_code(400);
        echo json_encode(['status' => 'error', 'message' => 'CSV file required']);
        exit;
    }
    
    $file = $_FILES['csv_file'];
    
    // Validate file
    if ($file['error'] !== UPLOAD_ERR_OK) {
        http_response_code(400);
        echo json_encode(['status' => 'error', 'message' => 'File upload error']);
        exit;
    }
    
    if ($file['size'] > 2097152) { // 2MB limit
        http_response_code(400);
        echo json_encode(['status' => 'error', 'message' => 'File size must be less than 2MB']);
        exit;
    }
    
    $adminId = getCurrentAdminId();
    $imported = 0;
    $errors = [];
    
    // Read CSV file
    if (($handle = fopen($file['tmp_name'], 'r')) !== FALSE) {
        $header = fgetcsv($handle); // Skip header row
        
        while (($data = fgetcsv($handle)) !== FALSE) {
            if (count($data) < 3) continue; // Skip invalid rows
            
            $name = trim($data[0] ?? '');
            $email = trim($data[1] ?? '');
            $age = (int)($data[2] ?? 0);
            $phone = trim($data[3] ?? '');
            $address = trim($data[4] ?? '');
            $status = trim($data[5] ?? 'active');
            
            // Validate required fields
            if (empty($name) || empty($email) || $age < 10 || $age > 100) {
                $errors[] = "Invalid data for: $name ($email)";
                continue;
            }
            
            // Validate status
            if (!in_array($status, ['active', 'inactive', 'graduated'])) {
                $status = 'active';
            }
            
            // Check for duplicate (both email AND phone must match)
            $checkSql = "SELECT ID FROM " . DB_TABLE_STUDENT . " WHERE Email = ? AND phone = ? AND admin_id = ?";
            $checkStmt = $conn->prepare($checkSql);
            $checkStmt->bind_param("ssi", $email, $phone, $adminId);
            $checkStmt->execute();
            $checkResult = $checkStmt->get_result();
            
            if ($checkResult->num_rows > 0) {
                $errors[] = "Duplicate student skipped: $name ($email & $phone)";
                continue;
            }
            
            // Insert student
            $sql = "INSERT INTO " . DB_TABLE_STUDENT . " (admin_id, Name, Email, Age, phone, address, status) VALUES (?, ?, ?, ?, ?, ?, ?)";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("ississs", $adminId, $name, $email, $age, $phone, $address, $status);
            
            if ($stmt->execute()) {
                $imported++;
            } else {
                $errors[] = "Failed to import: $name ($email)";
            }
        }
        fclose($handle);
    }
    
    echo json_encode([
        'status' => 'success',
        'message' => "Import completed: $imported students imported",
        'imported' => $imported,
        'errors' => $errors
    ]);
}

else {
    http_response_code(405);
    echo json_encode(['status' => 'error', 'message' => 'Method not supported']);
}

$conn->close();
?>