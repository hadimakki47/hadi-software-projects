<?php
require_once __DIR__ . '/db_config.php';
require_once __DIR__ . '/auth.php';
require_once __DIR__ . '/functions.php';

// Ensure the user is logged in
if (!isLoggedIn()) {
    header('Content-Type: application/json');
    echo json_encode(['error' => 'User not authenticated']);
    exit;
}

// Get current user info
$userId = $_SESSION['user_id'];
$userName = $_SESSION['user_name'] ?? 'User';

// Create chat messages table if it doesn't exist
$sql = "CREATE TABLE IF NOT EXISTS support_chat (
    id INT(11) AUTO_INCREMENT PRIMARY KEY,
    user_id INT(11) NOT NULL,
    message TEXT NOT NULL,
    is_support TINYINT(1) DEFAULT 0,
    read_status TINYINT(1) DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
)";
$conn->query($sql);

// Handle different actions
$action = $_POST['action'] ?? '';

switch ($action) {
    case 'send_message':
        sendMessage($conn, $userId);
        break;
    case 'get_messages':
        getMessages($conn, $userId);
        break;
    case 'start_chat':
        startChat($conn, $userId, $userName);
        break;
    default:
        header('Content-Type: application/json');
        echo json_encode(['error' => 'Invalid action']);
        break;
}

// Function to send a message
function sendMessage($conn, $userId) {
    $message = trim($_POST['message'] ?? '');
    
    if (empty($message)) {
        header('Content-Type: application/json');
        echo json_encode(['error' => 'Message cannot be empty']);
        return;
    }
    
    $stmt = $conn->prepare("INSERT INTO support_chat (user_id, message, is_support) VALUES (?, ?, 0)");
    $stmt->bind_param("is", $userId, $message);
    
    if ($stmt->execute()) {
        header('Content-Type: application/json');
        echo json_encode([
            'success' => true,
            'message' => $message,
            'time' => date('H:i')
        ]);
    } else {
        header('Content-Type: application/json');
        echo json_encode(['error' => 'Failed to send message']);
    }
    
    $stmt->close();
}

// Function to get messages
function getMessages($conn, $userId) {
    $lastId = intval($_POST['last_id'] ?? 0);
    
    $stmt = $conn->prepare("SELECT id, message, is_support, created_at FROM support_chat 
                            WHERE user_id = ? AND id > ? 
                            ORDER BY created_at ASC");
    $stmt->bind_param("ii", $userId, $lastId);
    $stmt->execute();
    $result = $stmt->get_result();
    
    $messages = [];
    while ($row = $result->fetch_assoc()) {
        $messages[] = [
            'id' => $row['id'],
            'message' => $row['message'],
            'is_support' => (bool)$row['is_support'],
            'time' => date('H:i', strtotime($row['created_at']))
        ];
    }
    
    // Update read status for user messages
    if (!empty($messages)) {
        $stmt = $conn->prepare("UPDATE support_chat SET read_status = 1 
                                WHERE user_id = ? AND is_support = 0");
        $stmt->bind_param("i", $userId);
        $stmt->execute();
    }
    
    header('Content-Type: application/json');
    echo json_encode([
        'success' => true,
        'messages' => $messages
    ]);
    
    $stmt->close();
}

// Function to start a chat with welcome message
function startChat($conn, $userId, $userName) {
    // Check if user has any previous chat
    $stmt = $conn->prepare("SELECT COUNT(*) as count FROM support_chat WHERE user_id = ?");
    $stmt->bind_param("i", $userId);
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();
    
    if ($row['count'] == 0) {
        // Add welcome message from support
        $welcomeMessage = "Hello $userName! Welcome to our support chat. How can we help you today?";
        $stmt = $conn->prepare("INSERT INTO support_chat (user_id, message, is_support) VALUES (?, ?, 1)");
        $stmt->bind_param("is", $userId, $welcomeMessage);
        $stmt->execute();
    }
    
    // Return all chat messages
    getMessages($conn, $userId);
} 