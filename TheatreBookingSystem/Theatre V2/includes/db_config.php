<?php
// Database configuration for MAMP
$servername = "127.0.0.1"; // Always use IP address for TCP connection
$port = 8889; // MAMP default port
$username = "root";
$password = "root";
$dbname = "theatre_booking";

// Determine if this is an AJAX request
$is_ajax = !empty($_SERVER['HTTP_X_REQUESTED_WITH']) && 
           strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest' ||
           strpos($_SERVER['SCRIPT_NAME'], 'chat_ajax.php') !== false;

// Create connection with error handling
try {
    // Always use 127.0.0.1 (IP address) for TCP connection - never use 'localhost'
    $conn = mysqli_connect($servername, $username, $password, $dbname, $port);
    
    // Check connection
    if (!$conn) {
        throw new Exception("Connection failed: " . mysqli_connect_error());
    }
    
    // Set character set
    mysqli_set_charset($conn, "utf8mb4");
    
    // Set wait_timeout to keep connection alive longer
    mysqli_query($conn, "SET SESSION wait_timeout = 86400");
    
} catch (Exception $e) {
    // Log the real error for debugging
    error_log("Database connection error: " . $e->getMessage());
    
    // For AJAX requests, send JSON response
    if ($is_ajax && !defined('HANDLE_ERROR_SILENTLY')) {
        header('Content-Type: application/json');
        echo json_encode([
            'success' => false,
            'message' => 'Database connection failed. Please try again later.'
        ]);
        exit;
    }
    // For regular page loads, show user-friendly message
    else if (!defined('HANDLE_ERROR_SILENTLY')) {
        echo "<div style='color:red;padding:10px;margin:10px;border:1px solid red;'>
              Database connection error. Please try again later or contact support.
              </div>";
    }
    
    // Keep $conn variable defined but set to false so code can check it without throwing errors
    $conn = false;
}

// Define a function to verify the connection is still alive
function ensure_db_connected() {
    global $conn, $servername, $username, $password, $dbname, $port;
    
    if (!$conn || !mysqli_ping($conn)) {
        try {
            // Only use 127.0.0.1 (IP address) - never use 'localhost'
            $conn = mysqli_connect($servername, $username, $password, $dbname, $port);
            
            // Set character set if reconnected
            if ($conn) {
                mysqli_set_charset($conn, "utf8mb4");
                return $conn;
            } else {
                error_log("Database reconnection failed: " . mysqli_connect_error());
                return false;
            }
        } catch (Exception $e) {
            error_log("Database reconnection error: " . $e->getMessage());
            return false;
        }
    }
    
    return $conn;
}
?> 