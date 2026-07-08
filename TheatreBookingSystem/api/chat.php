<?php
require_once __DIR__ . '/../includes/db_config.php';
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/functions.php';

// Set headers for JSON response
header('Content-Type: application/json');

// Check if database connection is successful
if (!$conn) {
    echo json_encode([
        'success' => false,
        'error' => 'Database connection failed: ' . mysqli_connect_error()
    ]);
    exit;
}

// Initialize session if not already started
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Get request type
$action = isset($_POST['action']) ? $_POST['action'] : '';

// Response structure
$response = [
    'success' => false,
    'messages' => [],
    'error' => ''
];

// Handle different chat actions
switch ($action) {
    case 'init':
        // Initialize chat session
        $sessionId = session_id();
        $userId = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : 0;
        $userName = isset($_SESSION['username']) ? $_SESSION['username'] : 'Guest';

        // Check if chat session exists, otherwise create one
        $stmt = $conn->prepare("SELECT id FROM chat_sessions WHERE session_id = ? LIMIT 1");
        $stmt->bind_param("s", $sessionId);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows == 0) {
            // Create new chat session
            $stmt = $conn->prepare("INSERT INTO chat_sessions (session_id, user_id, username, created_at) VALUES (?, ?, ?, NOW())");
            $stmt->bind_param("sis", $sessionId, $userId, $userName);
            $stmt->execute();
            
            // Add welcome message
            $chatSessionId = $conn->insert_id;
            $welcomeMsg = "Welcome to Theatre Support! How can we help you today?";
            $stmt = $conn->prepare("INSERT INTO chat_messages (chat_session_id, message, is_support, created_at) VALUES (?, ?, 1, NOW())");
            $stmt->bind_param("is", $chatSessionId, $welcomeMsg);
            $stmt->execute();
            
            $response['messages'][] = [
                'id' => $conn->insert_id,
                'message' => $welcomeMsg,
                'is_support' => 1,
                'timestamp' => date('Y-m-d H:i:s')
            ];
        } else {
            // Get existing session and load recent messages
            $row = $result->fetch_assoc();
            $chatSessionId = $row['id'];
            
            $stmt = $conn->prepare("SELECT id, message, is_support, created_at FROM chat_messages WHERE chat_session_id = ? ORDER BY created_at DESC LIMIT 10");
            $stmt->bind_param("i", $chatSessionId);
            $stmt->execute();
            $messagesResult = $stmt->get_result();
            
            $messages = [];
            while ($msgRow = $messagesResult->fetch_assoc()) {
                $messages[] = [
                    'id' => $msgRow['id'],
                    'message' => $msgRow['message'],
                    'is_support' => $msgRow['is_support'],
                    'timestamp' => $msgRow['created_at']
                ];
            }
            
            // Reverse to get chronological order
            $response['messages'] = array_reverse($messages);
        }
        
        $_SESSION['chat_session_id'] = $chatSessionId;
        $response['success'] = true;
        break;
    
    case 'send':
        // Send a new message
        if (!isset($_SESSION['chat_session_id'])) {
            $response['error'] = 'No active chat session';
            break;
        }
        
        $chatSessionId = $_SESSION['chat_session_id'];
        $message = isset($_POST['message']) ? trim($_POST['message']) : '';
        
        if (empty($message)) {
            $response['error'] = 'Message cannot be empty';
            break;
        }
        
        // Save user message
        $stmt = $conn->prepare("INSERT INTO chat_messages (chat_session_id, message, is_support, created_at) VALUES (?, ?, 0, NOW())");
        $stmt->bind_param("is", $chatSessionId, $message);
        $stmt->execute();
        
        $messageId = $conn->insert_id;
        $timestamp = date('Y-m-d H:i:s');
        
        // Add user message to response
        $response['messages'][] = [
            'id' => $messageId,
            'message' => $message,
            'is_support' => 0,
            'timestamp' => $timestamp
        ];
        
        // Auto-response (basic implementation)
        $autoResponse = "Thanks for your message. Our team will respond shortly.";
        $stmt = $conn->prepare("INSERT INTO chat_messages (chat_session_id, message, is_support, created_at) VALUES (?, ?, 1, NOW())");
        $stmt->bind_param("is", $chatSessionId, $autoResponse);
        $stmt->execute();
        
        // Add auto-response to the reply
        $response['messages'][] = [
            'id' => $conn->insert_id,
            'message' => $autoResponse,
            'is_support' => 1,
            'timestamp' => date('Y-m-d H:i:s')
        ];
        
        $response['success'] = true;
        break;
    
    case 'check':
        // Check for new messages
        if (!isset($_SESSION['chat_session_id'])) {
            $response['error'] = 'No active chat session';
            break;
        }
        
        $chatSessionId = $_SESSION['chat_session_id'];
        $lastId = isset($_POST['last_id']) ? intval($_POST['last_id']) : 0;
        
        $stmt = $conn->prepare("SELECT id, message, is_support, created_at FROM chat_messages WHERE chat_session_id = ? AND id > ? ORDER BY created_at ASC");
        $stmt->bind_param("ii", $chatSessionId, $lastId);
        $stmt->execute();
        $result = $stmt->get_result();
        
        $messages = [];
        while ($row = $result->fetch_assoc()) {
            $messages[] = [
                'id' => $row['id'],
                'message' => $row['message'],
                'is_support' => $row['is_support'],
                'timestamp' => $row['created_at']
            ];
        }
        
        $response['messages'] = $messages;
        $response['success'] = true;
        break;
    
    case 'close':
        // Close the chat session
        if (isset($_SESSION['chat_session_id'])) {
            $chatSessionId = $_SESSION['chat_session_id'];
            
            $stmt = $conn->prepare("UPDATE chat_sessions SET ended_at = NOW() WHERE id = ?");
            $stmt->bind_param("i", $chatSessionId);
            $stmt->execute();
            
            unset($_SESSION['chat_session_id']);
        }
        
        $response['success'] = true;
        break;
    
    default:
        $response['error'] = 'Invalid action';
        break;
}

// Return JSON response
echo json_encode($response);
?> 