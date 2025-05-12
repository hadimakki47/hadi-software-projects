<?php
/**
 * Standalone Chat API Handler
 * This script contains all chat functionality without any includes
 */

// Start the session for tracking user ID
session_start();

// Always send JSON response
header('Content-Type: application/json');

// Start output buffering to handle errors
ob_start();

// Catch fatal errors and convert to JSON
register_shutdown_function(function() {
    $error = error_get_last();
    if ($error && in_array($error['type'], [E_ERROR, E_PARSE, E_CORE_ERROR, E_COMPILE_ERROR])) {
        ob_clean();
        echo json_encode([
            'success' => false,
            'message' => 'Fatal error occurred. Please try again later.'
        ]);
    }
});

// Helper function to send JSON response
function sendJsonResponse($success, $message = '', $data = []) {
    $response = ['success' => $success];
    
    if (!empty($message)) {
        $response['message'] = $message;
    }
    
    if (!empty($data)) {
        $response = array_merge($response, $data);
    }
    
    echo json_encode($response);
    exit;
}

// Direct database connection - no includes
try {
    // Log attempt to connect
    error_log("Attempting to connect to database: 127.0.0.1:8889");
    
    // Connect directly to MAMP MySQL
    $servername = "127.0.0.1"; 
    $port = 8889;
    $username = "root";
    $password = "root";
    $dbname = "theatre_booking";
    
    // Enable error reporting for the connection attempt
    mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);
    
    $conn = mysqli_connect($servername, $username, $password, $dbname, $port);
    
    // Log successful connection
    error_log("Database connection successful");
    
    // Set character set
    mysqli_set_charset($conn, "utf8mb4");
    
    // Get action and user ID
    $action = isset($_POST['action']) ? $_POST['action'] : '';
    $userId = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : 1; // Default to admin user
    
    error_log("Processing action: $action for user: $userId");
    
    // Process the action
    switch ($action) {
        case 'init':
            initChat($conn, $userId);
            break;
            
        case 'send':
            sendMessage($conn, $userId);
            break;
            
        case 'getNew':
            getNewMessages($conn);
            break;
            
        case 'close':
            closeChat($conn);
            break;
            
        default:
            error_log("Invalid action: $action");
            sendJsonResponse(false, 'Invalid action');
            break;
    }
    
} catch (Exception $e) {
    // Log the error
    error_log("Standalone chat error: " . $e->getMessage());
    sendJsonResponse(false, "Error: " . $e->getMessage());
}

// Initialize a new chat session
function initChat($conn, $userId) {
    // Generate a unique session ID
    $sessionId = uniqid('chat_', true);
    
    try {
        // Test database connection with simple query
        $testResult = $conn->query("SELECT 1");
        if (!$testResult) {
            throw new Exception("Database connection test failed: " . $conn->error);
        }
        
        // Insert the new session into the database
        $stmt = $conn->prepare("INSERT INTO chat_sessions (session_id, user_id, status, created_at, updated_at) VALUES (?, ?, 'active', NOW(), NOW())");
        
        if (!$stmt) {
            throw new Exception("Database error: " . $conn->error);
        }
        
        $stmt->bind_param('si', $sessionId, $userId);
        
        if (!$stmt->execute()) {
            throw new Exception("Failed to create chat session: " . $stmt->error);
        }
        
        // Add welcome message from admin
        $welcomeMessage = "Hello! Welcome to Theatre Admin Support. How can I help you today?";
        $supportUserId = 1; // Admin user ID
        
        // Add a welcome message using the support user ID
        $msgStmt = $conn->prepare("INSERT INTO chat_messages (user_id, message, is_from_user, is_read, created_at) VALUES (?, ?, 0, 0, NOW())");
        
        if (!$msgStmt) {
            throw new Exception("Database error: " . $conn->error);
        }
        
        $msgStmt->bind_param('is', $supportUserId, $welcomeMessage);
        
        if (!$msgStmt->execute()) {
            throw new Exception("Failed to add welcome message: " . $msgStmt->error);
        }
        
        // Send the response
        sendJsonResponse(true, '', [
            'session_id' => $sessionId,
            'welcome_message' => $welcomeMessage
        ]);
    } catch (Exception $e) {
        sendJsonResponse(false, "Could not initialize chat: " . $e->getMessage());
    }
}

// Send a message from user to support
function sendMessage($conn, $userId) {
    // Get parameters
    $sessionId = isset($_POST['session_id']) ? $_POST['session_id'] : '';
    $message = isset($_POST['message']) ? $_POST['message'] : '';
    
    if (empty($sessionId) || empty($message)) {
        sendJsonResponse(false, 'Missing required parameters');
    }
    
    // Create a timestamp for the response
    $timestamp = time();
    
    try {
        // First, check if the session exists
        $stmt = $conn->prepare("SELECT id FROM chat_sessions WHERE session_id = ? LIMIT 1");
        if (!$stmt) {
            throw new Exception("Database prepare error: " . $conn->error);
        }
        
        $stmt->bind_param('s', $sessionId);
        $stmt->execute();
        $result = $stmt->get_result();
        
        // If session doesn't exist, create it
        if ($result->num_rows == 0) {
            $stmt = $conn->prepare("INSERT INTO chat_sessions (session_id, user_id, status, created_at, updated_at) VALUES (?, ?, 'active', NOW(), NOW())");
            if (!$stmt) {
                throw new Exception("Database prepare error: " . $conn->error);
            }
            
            $stmt->bind_param('si', $sessionId, $userId);
            $stmt->execute();
        }
        
        // Insert message
        $stmt = $conn->prepare("INSERT INTO chat_messages (user_id, message, is_from_user, is_read, created_at) VALUES (?, ?, 1, 0, NOW())");
        if (!$stmt) {
            throw new Exception("Database prepare error: " . $conn->error);
        }
        
        $stmt->bind_param('is', $userId, $message);
        
        if (!$stmt->execute()) {
            throw new Exception("Failed to send message: " . $stmt->error);
        }
        
        // Update session timestamp
        $stmt = $conn->prepare("UPDATE chat_sessions SET updated_at = NOW() WHERE session_id = ?");
        if ($stmt) {
            $stmt->bind_param('s', $sessionId);
            $stmt->execute();
        }
        
        // Send the response with current timestamp for client-side tracking
        sendJsonResponse(true, '', ['timestamp' => $timestamp]);
        
    } catch (Exception $e) {
        sendJsonResponse(false, "Could not send message: " . $e->getMessage());
    }
}

// Get new messages since last timestamp
function getNewMessages($conn) {
    // Get parameters
    $sessionId = isset($_POST['session_id']) ? $_POST['session_id'] : '';
    $lastTimestamp = isset($_POST['last_timestamp']) ? (int)$_POST['last_timestamp'] : 0;
    
    if (empty($sessionId)) {
        sendJsonResponse(false, 'Missing session ID');
    }
    
    try {
        // Get session information first
        $sessionStmt = $conn->prepare("SELECT id, user_id FROM chat_sessions WHERE session_id = ? LIMIT 1");
        if (!$sessionStmt) {
            throw new Exception("Database error: " . $conn->error);
        }
        
        $sessionStmt->bind_param('s', $sessionId);
        $sessionStmt->execute();
        $sessionResult = $sessionStmt->get_result();
        
        if ($sessionResult->num_rows === 0) {
            throw new Exception("Chat session not found");
        }
        
        $sessionRow = $sessionResult->fetch_assoc();
        $userId = $sessionRow['user_id'];
        
        // Get new messages using UNIX_TIMESTAMP to convert created_at to epoch time for comparison
        $stmt = $conn->prepare("SELECT 
                                id, user_id, message, is_from_user, UNIX_TIMESTAMP(created_at) as timestamp
                            FROM chat_messages 
                            WHERE user_id = ? AND UNIX_TIMESTAMP(created_at) > ? 
                            ORDER BY created_at ASC");
        
        if (!$stmt) {
            throw new Exception("Database error: " . $conn->error);
        }
        
        $stmt->bind_param('ii', $userId, $lastTimestamp);
        $stmt->execute();
        $result = $stmt->get_result();
        
        $messages = [];
        while ($row = $result->fetch_assoc()) {
            $messages[] = [
                'id' => $row['id'],
                'sender' => $row['is_from_user'] ? 'user' : 'admin',
                'message' => $row['message'],
                'timestamp' => (int)$row['timestamp']
            ];
        }
        
        // Send the response
        sendJsonResponse(true, '', ['messages' => $messages]);
        
    } catch (Exception $e) {
        sendJsonResponse(false, "Could not retrieve messages: " . $e->getMessage());
    }
}

// Close a chat session
function closeChat($conn) {
    $sessionId = isset($_POST['session_id']) ? $_POST['session_id'] : '';
    
    if (empty($sessionId)) {
        sendJsonResponse(false, 'Missing session ID');
    }
    
    try {
        // Update session status
        $stmt = $conn->prepare("UPDATE chat_sessions SET status = 'closed' WHERE session_id = ?");
        
        if (!$stmt) {
            throw new Exception("Database error: " . $conn->error);
        }
        
        $stmt->bind_param('s', $sessionId);
        
        if (!$stmt->execute()) {
            throw new Exception("Failed to close chat session: " . $stmt->error);
        }
        
        // Send the response
        sendJsonResponse(true, 'Chat session closed successfully');
        
    } catch (Exception $e) {
        sendJsonResponse(false, "Could not close chat session: " . $e->getMessage());
    }
}

// Cleanup
if (ob_get_level() > 0) {
    ob_end_clean();
}
?> 