<?php
/**
 * Chat Proxy Script
 * This script acts as a proxy to chat_ajax.php, forcing proper headers and settings
 */

// Force AJAX headers
$_SERVER['HTTP_X_REQUESTED_WITH'] = 'xmlhttprequest';

// Always set JSON header
header('Content-Type: application/json');

// Override PHP settings temporarily
ini_set('display_errors', 0);
ini_set('mysqli.default_port', '8889');

// Get POST data
$action = isset($_POST['action']) ? $_POST['action'] : 'init';
$session_id = isset($_POST['session_id']) ? $_POST['session_id'] : '';
$message = isset($_POST['message']) ? $_POST['message'] : '';
$last_timestamp = isset($_POST['last_timestamp']) ? $_POST['last_timestamp'] : 0;

// Start output buffering to ensure clean JSON
ob_start();

try {
    // First, test direct database connection
    $servername = "127.0.0.1";
    $port = 8889;
    $username = "root";
    $password = "root";
    $dbname = "theatre_booking";
    
    $conn = mysqli_connect($servername, $username, $password, $dbname, $port);
    
    if (!$conn) {
        throw new Exception("Database connection failed: " . mysqli_connect_error());
    }
    
    // Now, close the connection and include the main chat_ajax.php
    mysqli_close($conn);
    
    // Set POST variables
    $_POST['action'] = $action;
    if (!empty($session_id)) $_POST['session_id'] = $session_id;
    if (!empty($message)) $_POST['message'] = $message;
    if (!empty($last_timestamp)) $_POST['last_timestamp'] = $last_timestamp;
    
    // Include chat_ajax.php
    require_once __DIR__ . '/includes/chat_ajax.php';
    
} catch (Exception $e) {
    // Clear any output
    ob_clean();
    
    // Return proper JSON error
    echo json_encode([
        'success' => false,
        'message' => 'Proxy error: ' . $e->getMessage()
    ]);
}

// End output buffer if it's still active
if (ob_get_level() > 0) {
    ob_end_flush();
}
?> 