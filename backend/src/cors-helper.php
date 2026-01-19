<?php
// CORS Helper Functions

function getAllowedOrigins() {
    $allowedOrigins = getenv('ALLOWED_ORIGINS') ?: 'http://localhost:3000';
    return array_map('trim', explode(',', $allowedOrigins));
}

function setCorsHeaders() {
    $allowedOrigins = getAllowedOrigins();
    $origin = $_SERVER['HTTP_ORIGIN'] ?? '';
    
    // Check if the origin is in the allowed list
    if (in_array($origin, $allowedOrigins)) {
        header("Access-Control-Allow-Origin: $origin");
    } else if (strpos($origin, 'localhost') !== false || strpos($origin, '127.0.0.1') !== false) {
        // Allow localhost variations for development
        header("Access-Control-Allow-Origin: $origin");
    } else {
        // Default to first allowed origin or wildcard for development
        $defaultOrigin = !empty($allowedOrigins) ? $allowedOrigins[0] : '*';
        header("Access-Control-Allow-Origin: $defaultOrigin");
    }
    
    header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
    header('Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With');
    header('Access-Control-Allow-Credentials: true');
    header('Access-Control-Max-Age: 86400'); // Cache preflight for 24 hours
}

function validateOrigin() {
    // Always allow CLI access
    if (php_sapi_name() === 'cli') {
        return true;
    }
    
    $allowedOrigins = getAllowedOrigins();
    $origin = $_SERVER['HTTP_ORIGIN'] ?? '';
    $referer = $_SERVER['HTTP_REFERER'] ?? '';
    $host = $_SERVER['HTTP_HOST'] ?? '';
    
    // Check origin first
    if ($origin && in_array($origin, $allowedOrigins)) {
        return true;
    }
    
    // Check referer as fallback
    if ($referer) {
        foreach ($allowedOrigins as $allowedOrigin) {
            if (strpos($referer, $allowedOrigin) === 0) {
                return true;
            }
        }
    }
    
    // Allow same-host requests (internal requests)
    if ($host && (strpos($host, 'localhost') !== false || strpos($host, '127.0.0.1') !== false)) {
        return true;
    }
    
    // Allow requests from nginx proxy (internal container communication)
    if (empty($origin) && empty($referer)) {
        return true;
    }
    
    return false;
}
?>