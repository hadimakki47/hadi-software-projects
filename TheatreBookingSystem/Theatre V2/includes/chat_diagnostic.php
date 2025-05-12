<?php
// Set full error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<h1>Chat Diagnostic Tool</h1>";

// Check PHP version
echo "<h2>PHP Environment</h2>";
echo "<p>PHP Version: " . phpversion() . "</p>";
echo "<p>Server: " . $_SERVER['SERVER_SOFTWARE'] . "</p>";

// Check database connection
echo "<h2>Database Connection</h2>";
try {
    require_once __DIR__ . '/db_config.php';
    
    if (!isset($conn)) {
        echo "<p style='color:red'>Connection variable not defined</p>";
    } else if ($conn->connect_error) {
        echo "<p style='color:red'>Connection failed: " . $conn->connect_error . "</p>";
    } else {
        echo "<p style='color:green'>Database connection successful!</p>";
        echo "<p>Server info: " . $conn->server_info . "</p>";
        
        // Check if tables exist
        echo "<h3>Tables Check</h3>";
        $tables = ["chat_sessions", "chat_messages"];
        
        foreach ($tables as $table) {
            $result = $conn->query("SHOW TABLES LIKE '$table'");
            if ($result->num_rows > 0) {
                echo "<p>Table '$table' exists ✓</p>";
                
                // Show table structure
                $structure = $conn->query("DESCRIBE $table");
                echo "<details><summary>Table structure</summary><pre>";
                while ($row = $structure->fetch_assoc()) {
                    print_r($row);
                }
                echo "</pre></details>";
            } else {
                echo "<p style='color:red'>Table '$table' does not exist ✗</p>";
                
                // Try to create the table
                echo "<p>Attempting to create table...</p>";
                
                if ($table === "chat_sessions") {
                    $sql = "CREATE TABLE IF NOT EXISTS chat_sessions (
                        id INT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
                        session_id VARCHAR(64) NOT NULL UNIQUE,
                        user_id INT(6) UNSIGNED NULL,
                        status ENUM('active', 'closed') DEFAULT 'active',
                        created_at DATETIME NOT NULL,
                        updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
                    )";
                } else if ($table === "chat_messages") {
                    $sql = "CREATE TABLE IF NOT EXISTS chat_messages (
                        id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
                        session_id VARCHAR(64) NOT NULL,
                        sender ENUM('user', 'support') NOT NULL,
                        sender_id INT(6) UNSIGNED NULL,
                        message TEXT NOT NULL,
                        timestamp INT(11) NOT NULL,
                        is_read BOOLEAN NOT NULL DEFAULT 0
                    )";
                }
                
                if ($conn->query($sql)) {
                    echo "<p style='color:green'>Table '$table' created successfully!</p>";
                } else {
                    echo "<p style='color:red'>Error creating table: " . $conn->error . "</p>";
                }
            }
        }
    }
} catch (Exception $e) {
    echo "<p style='color:red'>Exception: " . $e->getMessage() . "</p>";
}

// Check file permissions
echo "<h2>File Permissions</h2>";
$files = [
    __DIR__ . '/chat_ajax.php',
    __DIR__ . '/db_config.php',
    __DIR__ . '/auth.php',
    __DIR__ . '/functions.php'
];

foreach ($files as $file) {
    if (file_exists($file)) {
        echo "<p>" . basename($file) . " exists ✓ (Permissions: " . substr(sprintf('%o', fileperms($file)), -4) . ")</p>";
    } else {
        echo "<p style='color:red'>" . basename($file) . " does not exist ✗</p>";
    }
}

// Test session
echo "<h2>Session Test</h2>";
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$_SESSION['test'] = 'Session working';
echo "<p>Session ID: " . session_id() . "</p>";
echo "<p>Session test value: " . ($_SESSION['test'] ?? 'Not set') . "</p>";

// Check for any PHP errors
echo "<h2>PHP Error Log</h2>";
$error_log = ini_get('error_log');
echo "<p>Error log path: " . ($error_log ? $error_log : 'Not configured') . "</p>";

// Test JSON functionality
echo "<h2>JSON Test</h2>";
$test_array = ['success' => true, 'message' => 'Test message'];
$json = json_encode($test_array);
echo "<p>JSON encode test: " . ($json ? "Working ✓" : "Failed ✗") . "</p>";
echo "<pre>" . $json . "</pre>";

// Test AJAX endpoint with cURL
echo "<h2>AJAX Endpoint Test</h2>";
if (function_exists('curl_version')) {
    $curl = curl_init();
    $url = 'http://' . $_SERVER['HTTP_HOST'] . '/includes/chat_ajax.php';
    $postData = ['action' => 'init'];
    
    curl_setopt_array($curl, [
        CURLOPT_URL => $url,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_POST => true,
        CURLOPT_POSTFIELDS => $postData,
    ]);
    
    $response = curl_exec($curl);
    $error = curl_error($curl);
    $info = curl_getinfo($curl);
    curl_close($curl);
    
    echo "<p>Request URL: $url</p>";
    echo "<p>HTTP Status: " . $info['http_code'] . "</p>";
    
    if ($error) {
        echo "<p style='color:red'>cURL Error: $error</p>";
    } else {
        echo "<p>Response:</p>";
        echo "<pre>" . htmlspecialchars($response) . "</pre>";
    }
} else {
    echo "<p>cURL not available. Cannot test AJAX endpoint.</p>";
}

// Display phpinfo in expandable section
echo "<h2>PHP Info</h2>";
echo "<details><summary>Click to view PHP info</summary>";
ob_start();
phpinfo();
$phpinfo = ob_get_clean();
echo $phpinfo;
echo "</details>";
?> 