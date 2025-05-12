<?php
// Direct test of chat_ajax.php functionality

// Set header for proper display
header('Content-Type: text/plain');

echo "Chat AJAX Direct Test\n";
echo "====================\n\n";

// Show PHP info
echo "PHP Version: " . phpversion() . "\n";
echo "Server Software: " . $_SERVER['SERVER_SOFTWARE'] . "\n\n";

// Set up test parameters for initialization
$_POST['action'] = 'init';
$_SERVER['HTTP_X_REQUESTED_WITH'] = 'xmlhttprequest';

// Start output buffering to capture the JSON response
ob_start();

// Include the chat_ajax.php file directly
try {
    echo "Attempting to include chat_ajax.php...\n";
    
    // Use require instead of include to see fatal errors
    require_once __DIR__ . '/includes/chat_ajax.php';
    
    echo "chat_ajax.php included successfully.\n";
} catch (Exception $e) {
    echo "Exception: " . $e->getMessage() . "\n";
}

// Get the output
$output = ob_get_clean();

echo "\nRaw Output:\n";
echo "----------\n";
echo $output . "\n\n";

// Try to parse JSON
echo "Parsed Output:\n";
echo "-------------\n";
if (!empty($output)) {
    try {
        $json = json_decode($output, true);
        if ($json !== null) {
            echo "Success: " . ($json['success'] ? 'true' : 'false') . "\n";
            if (isset($json['message'])) {
                echo "Message: " . $json['message'] . "\n";
            }
            if (isset($json['session_id'])) {
                echo "Session ID: " . $json['session_id'] . "\n";
            }
            if (isset($json['welcome_message'])) {
                echo "Welcome: " . $json['welcome_message'] . "\n";
            }
            if (isset($json['error'])) {
                echo "Error: " . $json['error'] . "\n";
            }
        } else {
            echo "Failed to parse JSON response.\n";
            echo "JSON error: " . json_last_error_msg() . "\n";
        }
    } catch (Exception $e) {
        echo "Exception parsing JSON: " . $e->getMessage() . "\n";
    }
} else {
    echo "No output received.\n";
}

echo "\nComplete.\n";
?> 