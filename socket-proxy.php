<?php
/**
 * PHP Proxy for Socket.io Polling (v3.0 - Stable)
 * Bridges request from port 80 to localhost:3001
 */

// Disable all buffering
while (ob_get_level()) ob_end_clean();
set_time_limit(0); 
ignore_user_abort(true);

// CORS Headers
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Credentials: true');
header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    exit(0);
}

// Ensure we have a query string
$queryString = $_SERVER['QUERY_STRING'] ?? '';

// Build target URL 
$targetUrl = "http://localhost:3001/socket.io/?" . $queryString;

$ch = curl_init($targetUrl);

// Forward POST data
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, file_get_contents('php://input'));
}

// Polling stability options
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
curl_setopt($ch, CURLOPT_TIMEOUT, 45); // Limit to 45s to stay under most server timeouts
curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 2);
curl_setopt($ch, CURLOPT_BUFFERSIZE, 1024);

// Forward Headers
$headers = [];
if (isset($_SERVER['CONTENT_TYPE'])) {
    $headers[] = 'Content-Type: ' . $_SERVER['CONTENT_TYPE'];
}
curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

// Execute
$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

if ($response === false) {
    if ($httpCode == 200 || $httpCode == 0) {
        // Safe exit on idle
        exit();
    }
    http_response_code(502);
    echo "Proxy Error: " . curl_error($ch);
} else {
    http_response_code($httpCode ? $httpCode : 200);
    header('Content-Type: text/plain');
    echo $response;
}

curl_close($ch);
