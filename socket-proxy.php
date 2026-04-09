<?php
/**
 * PHP Proxy for Socket.io Polling (v2.0)
 * Bridges request from port 80 to localhost:3001 with long-polling support
 */

// Production optimizations
set_time_limit(0); 
ignore_user_abort(true);
ini_set('output_buffering', 'off');
ini_set('zlib.output_compression', false);

// Handle CORS
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Credentials: true');
header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    exit(0);
}

// Get query string
$queryString = $_SERVER['QUERY_STRING'] ?? '';

// Build target URL 
$targetUrl = "http://localhost:3001/socket.io/?" . $queryString;

$ch = curl_init($targetUrl);

// Forward POST data
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, file_get_contents('php://input'));
}

// CRITICAL: Long-polling needs a large timeout
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
curl_setopt($ch, CURLOPT_TIMEOUT, 60); // 60s for long polling
curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);

// Forward headers
$headers = [];
if (isset($_SERVER['CONTENT_TYPE'])) {
    $headers[] = 'Content-Type: ' . $_SERVER['CONTENT_TYPE'];
}
curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

// Execute
$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

if ($response === false) {
    if ($httpCode == 0) {
        // Likely a timeout which is FINE in long-polling
        http_response_code(200);
        exit();
    }
    http_response_code(502);
    echo "Proxy Error: " . curl_error($ch);
} else {
    http_response_code($httpCode);
    header('Content-Type: text/plain');
    echo $response;
}

curl_close($ch);
