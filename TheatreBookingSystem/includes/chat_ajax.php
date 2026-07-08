<?php
/**
 * Chat AJAX Handler - Simplified Version
 * Handles chat-related AJAX requests for the support chat widget
 */

// Always set JSON header for all AJAX responses
header('Content-Type: application/json');

// Register shutdown function to catch fatal errors
register_shutdown_function(function() {
    $error = error_get_last();
    if ($error && in_array($error['type'], [E_ERROR, E_PARSE, E_CORE_ERROR, E_COMPILE_ERROR])) {
        $message = "Fatal Error: " . $error['message'] . " in " . $error['file'] . " on line " . $error['line'];
        error_log($message);
        echo json_encode([
            'success' => false,
            'message' => 'An unexpected error occurred. Please try again later.',
            'error' => $message
        ]);
    }
});

// Start output buffering to catch any errors
ob_start();

// Debug mode
$debug = true;

// Set error reporting for debug
error_reporting(E_ALL);
ini_set('display_errors', 0); // Don't display errors directly

// Helper function to send JSON response
function sendJsonResponse($success, $message = '', $data = []) {
    header('Content-Type: application/json');
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

// Main execution block
function main() {
    global $debug;
    try {
        // Define error handling flag for db_config
        define('HANDLE_ERROR_SILENTLY', true);
        
        // Include necessary files
        require_once __DIR__ . '/db_config.php';
        
        // Use our connection verification function to ensure we have a working connection
        $conn = ensure_db_connected();
        
        // Check database connection
        if (!$conn) {
            throw new Exception("Database connection failed: Connection could not be established");
        }
        
        // Initialize session if not started
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        
        // Get action and user ID
        $action = isset($_POST['action']) ? $_POST['action'] : '';
        $userId = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : null;
        
        // Process the action
        switch ($action) {
            case 'init':
                // Try to create tables first
                $tables_created = createChatTables($conn);
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
                sendJsonResponse(false, 'Invalid action');
                break;
        }
    } catch (Exception $e) {
        $errorOutput = ob_get_clean();
        error_log("Chat error: " . $e->getMessage() . "\n" . $errorOutput);
        
        // Provide detailed error information in debug mode
        $errorMessage = $debug ? 
            "Error: " . $e->getMessage() : 
            "Failed to connect to chat service. Please refresh the page and try again.";
        
        // Only try to get mysqli_error if $conn exists and is a mysqli object
        $sqlError = '';
        if (isset($GLOBALS['conn']) && $GLOBALS['conn'] instanceof mysqli) {
            $sqlError = mysqli_error($GLOBALS['conn']);
        }
        
        $debugData = $debug ? 
            ['debug' => $errorOutput, 'sql_error' => $sqlError] : 
            [];
        
        sendJsonResponse(false, $errorMessage, $debugData);
    }
}

// Execute main function
main();

// Clean up output buffer
ob_end_clean();

/**
 * Create necessary tables for chat functionality if they don't exist
 */
function createChatTables($conn) {
    try {
        // Check if tables already exist first
        $tables_exist = true;
        $result = $conn->query("SHOW TABLES LIKE 'chat_sessions'");
        $tables_exist = $tables_exist && ($result && $result->num_rows > 0);
        
        $result = $conn->query("SHOW TABLES LIKE 'chat_messages'");
        $tables_exist = $tables_exist && ($result && $result->num_rows > 0);
        
        // If tables already exist, no need to create them
        if ($tables_exist) {
            return true;
        }
        
        // Create chat_sessions table
        $sql = "CREATE TABLE IF NOT EXISTS chat_sessions (
            id INT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
            session_id VARCHAR(64) NOT NULL UNIQUE,
            user_id INT(6) UNSIGNED NULL,
            status ENUM('active', 'closed') DEFAULT 'active',
            created_at DATETIME NOT NULL,
            updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL
        )";
        
        $result1 = $conn->query($sql);
        
        if (!$result1) {
            throw new Exception("Failed to create chat_sessions table: " . $conn->error);
        }
        
        // Create chat_messages table
        $sql = "CREATE TABLE IF NOT EXISTS chat_messages (
            id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
            user_id INT(6) UNSIGNED NOT NULL,
            message TEXT NOT NULL,
            is_from_user BOOLEAN NOT NULL DEFAULT 1,
            is_read BOOLEAN NOT NULL DEFAULT 0,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
        )";
        
        $result2 = $conn->query($sql);
        
        if (!$result2) {
            throw new Exception("Failed to create chat_messages table: " . $conn->error);
        }
        
        return true;
    } catch (Exception $e) {
        // Handle errors but allow the script to continue
        error_log("Error creating chat tables: " . $e->getMessage());
        return false;
    }
}

/**
 * Initialize a new chat session
 */
function initChat($conn, $userId) {
    // Double check connection
    if (!$conn) {
        sendJsonResponse(false, "Database connection is not available. Please refresh and try again.");
        return;
    }
    
    // Generate a unique session ID
    $sessionId = uniqid('chat_', true);
    
    try {
        // If no user ID, set a default (guest or admin)
        if (!$userId) {
            $userId = 1; // Default to admin/support user
        }
        
        // Test database connection first with a simple query
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
        
        // Add welcome message from support
        $welcomeMessage = "Support ... Hello! Welcome to Theatre Admin Support. How can I help you today?";
        $supportUserId = 1; // Admin/support user ID
        
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
        // Log the error and send a user-friendly response
        error_log("Chat initialization error: " . $e->getMessage());
        sendJsonResponse(false, "Could not initialize chat. Please check your database connection and try again.");
    }
}

/**
 * Send a message from user to support
 */
function sendMessage($conn, $userId) {
    // Get parameters
    $sessionId = isset($_POST['session_id']) ? $_POST['session_id'] : '';
    $message = isset($_POST['message']) ? $_POST['message'] : '';
    
    if (empty($sessionId) || empty($message)) {
        sendJsonResponse(false, 'Missing required parameters');
    }
    
    // Ensure we have a user ID (either from session or a default guest ID)
    if (!$userId) {
        $userId = 1; // Default to admin if no user ID (should be changed based on your requirements)
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
        // Log the error and send a user-friendly response
        error_log("Chat message error: " . $e->getMessage());
        sendJsonResponse(false, "Could not send message. Please try again.");
    }
}

/**
 * Get new messages since last timestamp
 */
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
        $sessionId = $sessionRow['id'];
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
                'sender' => $row['is_from_user'] ? 'user' : 'support',
                'message' => $row['message'],
                'timestamp' => (int)$row['timestamp']
            ];
        }
        
        // Send the response
        sendJsonResponse(true, '', ['messages' => $messages]);
        
    } catch (Exception $e) {
        // Log the error and send a user-friendly response
        error_log("Get messages error: " . $e->getMessage());
        sendJsonResponse(false, "Could not retrieve messages. Please try again.");
    }
}

/**
 * Close a chat session
 */
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
        // Log the error and send a user-friendly response
        error_log("Close chat error: " . $e->getMessage());
        sendJsonResponse(false, "Could not close chat session. Please try again.");
    }
}
?> 