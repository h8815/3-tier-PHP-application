<?php
// TIER 2: STUDENTS API WITH IMAGE UPLOAD & ENHANCED FEATURES

session_start();

// Include CORS helper
require_once dirname(__DIR__) . '/cors-helper.php';

include '../db.php';

header('Content-Type: application/json');
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

function handleImageUpload($file) {
    $uploadDir = '/var/www/html/uploads/';
    if (!is_dir($uploadDir)) mkdir($uploadDir, 0755, true);
    
    $allowedTypes = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
    if (!in_array($file['type'], $allowedTypes) || $file['size'] > 5242880) {
        return null;
    }
    
    $extension = pathinfo($file['name'], PATHINFO_EXTENSION);
    $filename = uniqid('student_') . '.' . $extension;
    $filepath = $uploadDir . $filename;
    
    if (move_uploaded_file($file['tmp_name'], $filepath)) {
        return 'uploads/' . $filename;
    }
    return null;
}

// --- GET Request ---
if ($method === 'GET') {
    if (!isAuthenticated()) {
        http_response_code(401);
        echo json_encode(['status' => 'error', 'message' => 'Authentication required']);
        exit;
    }
    
    $id = $_GET['id'] ?? null;
    
    if ($id) {
        $sql = "SELECT * FROM " . DB_TABLE_STUDENT . " WHERE ID = ? AND admin_id = ?";
        $stmt = $conn->prepare($sql);
        $adminId = getCurrentAdminId();
        $stmt->bind_param("ii", $id, $adminId);
        $stmt->execute();
        $result = $stmt->get_result();
        $student = $result->fetch_assoc();
        
        if ($student) {
            echo json_encode(['status' => 'success', 'data' => $student]);
        } else {
            http_response_code(404);
            echo json_encode(['status' => 'error', 'message' => 'Student not found']);
        }
    } else {
        // Pagination & filtering
        $page = max(1, (int)($_GET['page'] ?? 1));
        $limit = min(50, max(1, (int)($_GET['limit'] ?? 12)));
        $offset = ($page - 1) * $limit;
        
        $sortBy = in_array($_GET['sort_by'] ?? '', ['ID', 'Name', 'Email', 'Age', 'enrollment_date']) ? $_GET['sort_by'] : 'ID';
        $order = strtoupper($_GET['order'] ?? 'ASC') === 'DESC' ? 'DESC' : 'ASC';
        
        $search = $_GET['search'] ?? '';
        $statusFilter = $_GET['status'] ?? '';
        
        $whereConditions = ['admin_id = ?'];
        $params = [getCurrentAdminId()];
        $types = 'i';
        
        if (!empty($search)) {
            $whereConditions[] = "(Name LIKE ? OR Email LIKE ?)";
            $searchParam = "%$search%";
            $params[] = $searchParam;
            $params[] = $searchParam;
            $types .= 'ss';
        }
        
        if (!empty($statusFilter)) {
            $whereConditions[] = "status = ?";
            $params[] = $statusFilter;
            $types .= 's';
        }
        
        $whereClause = !empty($whereConditions) ? 'WHERE ' . implode(' AND ', $whereConditions) : '';
        
        // Count total
        $countSql = "SELECT COUNT(*) as total FROM " . DB_TABLE_STUDENT . " $whereClause";
        if (!empty($params)) {
            $countStmt = $conn->prepare($countSql);
            $countStmt->bind_param($types, ...$params);
            $countStmt->execute();
            $totalResult = $countStmt->get_result();
        } else {
            $totalResult = $conn->query($countSql);
        }
        $totalRecords = $totalResult->fetch_assoc()['total'];
        
        // Get data
        $sql = "SELECT * FROM " . DB_TABLE_STUDENT . " $whereClause ORDER BY $sortBy $order LIMIT ? OFFSET ?";
        $params[] = $limit;
        $params[] = $offset;
        $types .= 'ii';
        
        $stmt = $conn->prepare($sql);
        if (!empty($params)) {
            $stmt->bind_param($types, ...$params);
        }
        $stmt->execute();
        $result = $stmt->get_result();
        $students = $result->fetch_all(MYSQLI_ASSOC);
        
        // Status counts
        $statusSql = "SELECT status, COUNT(*) as count FROM " . DB_TABLE_STUDENT . " WHERE admin_id = ? GROUP BY status";
        $statusStmt = $conn->prepare($statusSql);
        $adminId = getCurrentAdminId();
        $statusStmt->bind_param('i', $adminId);
        $statusStmt->execute();
        $statusResult = $statusStmt->get_result();
        $statusCounts = [];
        while ($row = $statusResult->fetch_assoc()) {
            $statusCounts[$row['status']] = (int)$row['count'];
        }
        
        echo json_encode([
            'status' => 'success',
            'data' => $students,
            'pagination' => [
                'page' => $page,
                'limit' => $limit,
                'total' => (int)$totalRecords,
                'pages' => ceil($totalRecords / $limit)
            ],
            'statusCounts' => $statusCounts
        ]);
    }
}

// --- POST Request ---
elseif ($method === 'POST') {
    if (!isAuthenticated()) {
        http_response_code(401);
        echo json_encode(['status' => 'error', 'message' => 'Authentication required']);
        exit;
    }
    
    $name = trim($_POST['name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $age = (int)($_POST['age'] ?? 0);
    $phone = trim($_POST['phone'] ?? '');
    $address = trim($_POST['address'] ?? '');
    $status = $_POST['status'] ?? 'active';
    
    if (empty($name) || empty($email) || $age < 10 || $age > 100) {
        http_response_code(400);
        echo json_encode(['status' => 'error', 'message' => 'Invalid input data']);
        exit;
    }
    
    $profilePhoto = null;
    if (isset($_FILES['profile_photo']) && $_FILES['profile_photo']['error'] === UPLOAD_ERR_OK) {
        $profilePhoto = handleImageUpload($_FILES['profile_photo']);
    }
    
    $sql = "INSERT INTO " . DB_TABLE_STUDENT . " (admin_id, Name, Email, Age, phone, address, status, profile_photo) VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $adminId = getCurrentAdminId();
    $stmt->bind_param("ississss", $adminId, $name, $email, $age, $phone, $address, $status, $profilePhoto);
    
    if ($stmt->execute()) {
        http_response_code(201);
        echo json_encode(['status' => 'inserted', 'id' => $conn->insert_id, 'message' => 'Student added successfully']);
    } else {
        http_response_code(500);
        echo json_encode(['status' => 'error', 'message' => 'Database error']);
    }
}

// --- PUT Request ---
elseif ($method === 'PUT') {
    if (!isAuthenticated()) {
        http_response_code(401);
        echo json_encode(['status' => 'error', 'message' => 'Authentication required']);
        exit;
    }
    
    $input = json_decode(file_get_contents('php://input'), true);
    $id = $input['id'] ?? null;
    
    if (!$id) {
        http_response_code(400);
        echo json_encode(['status' => 'error', 'message' => 'ID required']);
        exit;
    }
    
    $sql = "UPDATE " . DB_TABLE_STUDENT . " SET Name = ?, Email = ?, Age = ?, phone = ?, address = ?, status = ? WHERE ID = ? AND admin_id = ?";
    $stmt = $conn->prepare($sql);
    $adminId = getCurrentAdminId();
    $stmt->bind_param("sissssii", $input['name'], $input['email'], $input['age'], $input['phone'], $input['address'], $input['status'], $id, $adminId);
    
    if ($stmt->execute()) {
        echo json_encode(['status' => 'updated', 'id' => $id, 'message' => 'Student updated successfully']);
    } else {
        http_response_code(500);
        echo json_encode(['status' => 'error', 'message' => 'Update failed']);
    }
}

// --- DELETE Request ---
elseif ($method === 'DELETE') {
    if (!isAuthenticated()) {
        http_response_code(401);
        echo json_encode(['status' => 'error', 'message' => 'Authentication required']);
        exit;
    }
    
    $input = json_decode(file_get_contents('php://input'), true);
    $id = $input['id'] ?? null;
    
    if (!$id) {
        http_response_code(400);
        echo json_encode(['status' => 'error', 'message' => 'ID required']);
        exit;
    }
    
    $sql = "DELETE FROM " . DB_TABLE_STUDENT . " WHERE ID = ? AND admin_id = ?";
    $stmt = $conn->prepare($sql);
    $adminId = getCurrentAdminId();
    $stmt->bind_param("ii", $id, $adminId);
    
    if ($stmt->execute()) {
        echo json_encode(['status' => 'deleted', 'id' => $id, 'message' => 'Student deleted successfully']);
    } else {
        http_response_code(500);
        echo json_encode(['status' => 'error', 'message' => 'Delete failed']);
    }
}

else {
    http_response_code(405);
    echo json_encode(['status' => 'error', 'message' => 'Method not supported']);
}

$conn->close();
?>