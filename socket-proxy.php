<?php
/**
 * PHP Proxy for Socket.io Polling
 * Bridges request from port 80 to localhost:3001
 */

// Allow CORS if needed (though on same domain it should be fine)
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Credentials: true');
header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    exit(0);
}

// Get the actual query string from the browser request
$queryString = $_SERVER['QUERY_STRING'] ?? '';

// Build the target URL for the local Node server
$targetUrl = "http://localhost:3001/socket.io/?" . $queryString;

$ch = curl_init($targetUrl);

// Forward the request method
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, file_get_contents('php://input'));
}

// Set standard options
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
curl_setopt($ch, CURLOPT_TIMEOUT, 10);

// Forward relevant headers
$headers = [];
if (isset($_SERVER['CONTENT_TYPE'])) {
    $headers[] = 'Content-Type: ' . $_SERVER['CONTENT_TYPE'];
}
curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

// Execute the call to localhost:3001
$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

if ($response === false) {
    http_response_code(502);
    echo "Proxy Error: " . curl_error($ch);
} else {
    http_response_code($httpCode);
    // Usually Socket.io responses are text/plain or application/octet-stream
    header('Content-Type: text/plain');
    echo $response;
}

curl_close($ch);
