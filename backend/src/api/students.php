<?php
// TIER 2: BUSINESS LOGIC / API LAYER - Handles all CRUD operations

include '../db.php'; 

header('Content-Type: application/json');

$method = $_SERVER['REQUEST_METHOD'];

// Parse request data based on HTTP method
$data = [];
if (in_array($method, ['POST', 'PUT', 'DELETE'])) {
    // Read JSON data for POST, PUT, DELETE requests
    $input_json = file_get_contents('php://input');
    $data = json_decode($input_json, true);
} else {
    // Read query parameters for GET requests
    $data = $_GET;
}

// Simple input validation (Business Logic)
function validate_input($input) {
    if (!isset($input['name']) || empty($input['name']) ||
        !isset($input['email']) || !filter_var($input['email'], FILTER_VALIDATE_EMAIL) ||
        !isset($input['age']) || !is_numeric($input['age']) || $input['age'] < 10 || $input['age'] > 100) {
        return false;
    }
    return true;
}

// --- GET Request (Read) ---
if ($method === 'GET') {
    $id = $data['id'] ?? null;
    
    if ($id) {
        $sql = "SELECT * FROM " . DB_TABLE_STUDENT . " WHERE ID = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $result = $stmt->get_result();
        $student = $result->fetch_assoc();
        
        if ($student) {
            http_response_code(200);
            echo json_encode(['status' => 'success', 'data' => $student]);
        } else {
            http_response_code(404);
            echo json_encode(['status' => 'error', 'message' => 'Student not found']);
        }
    } else {
        $sql = "SELECT * FROM " . DB_TABLE_STUDENT;
        $result = $conn->query($sql);
        $students = $result->fetch_all(MYSQLI_ASSOC);
        
        http_response_code(200);
        echo json_encode(['status' => 'success', 'data' => $students, 'total' => count($students)]);
    }
}

// --- POST Request (Create) ---
elseif ($method === 'POST') {
    if (!validate_input($data)) {
        http_response_code(400); 
        echo json_encode(['status' => 'error', 'message' => 'Invalid or missing fields. Age must be between 10 and 100.']);
        exit;
    }
    
    $sql = "INSERT INTO " . DB_TABLE_STUDENT . " (Name, Email, Age) VALUES (?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssi", $data['name'], $data['email'], $data['age']);
    
    if ($stmt->execute()) {
        http_response_code(201); // 201 Created
        echo json_encode(['status' => 'inserted', 'id' => $conn->insert_id, 'message' => 'Student record successfully added! 🎉']);
    } else {
        http_response_code(500);
        // Important: Return the specific MySQL error for better debugging
        echo json_encode(['status' => 'error', 'message' => 'Database insert error: ' . $stmt->error]);
    }
}

// --- PUT Request (Update) ---
elseif ($method === 'PUT') {
    $id = $data['id'] ?? null;
    if (!$id || !validate_input($data)) {
        http_response_code(400);
        echo json_encode(['status' => 'error', 'message' => 'Invalid ID or missing fields.']);
        exit;
    }
    
    $sql = "UPDATE " . DB_TABLE_STUDENT . " SET Name = ?, Email = ?, Age = ? WHERE ID = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssii", $data['name'], $data['email'], $data['age'], $id); 
    
    if ($stmt->execute()) {
        http_response_code(200);
        echo json_encode(['status' => 'updated', 'id' => $id, 'message' => 'Student record successfully updated! ✏️']);
    } else {
        http_response_code(500);
        echo json_encode(['status' => 'error', 'message' => 'Database update error: ' . $stmt->error]);
    }
}

// --- DELETE Request (Delete) ---
elseif ($method === 'DELETE') {
    $id = $data['id'] ?? null;
    if (!$id) {
        http_response_code(400);
        echo json_encode(['status' => 'error', 'message' => 'Missing ID for deletion.']);
        exit;
    }
    
    $sql = "DELETE FROM " . DB_TABLE_STUDENT . " WHERE ID = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $id);
    
    if ($stmt->execute()) {
        http_response_code(200);
        echo json_encode(['status' => 'deleted', 'id' => $id, 'message' => 'Student record successfully deleted. 🗑️']);
    } else {
        http_response_code(500);
        echo json_encode(['status' => 'error', 'message' => 'Database delete error: ' . $stmt->error]);
    }
}

// --- Fallback ---
else {
    http_response_code(405); // Method Not Allowed
    echo json_encode(['status' => 'error', 'message' => 'Method not supported.']);
}

$conn->close();
?>