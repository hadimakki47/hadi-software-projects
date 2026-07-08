<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Credentials come from environment variables so the same code runs
// locally (XAMPP/MAMP) and inside Docker without edits.
$db_host = getenv('DB_HOST') ?: '127.0.0.1';
$db_port = getenv('DB_PORT') ?: '3307';
$db_name = getenv('DB_NAME') ?: 'volunteer_connect';
$db_user = getenv('DB_USER') ?: 'root';
$db_pass = getenv('DB_PASS') ?: '';

try {
    $pdo = new PDO(
        "mysql:host=$db_host;port=$db_port;dbname=$db_name;charset=utf8mb4",
        $db_user,
        $db_pass,
        [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
    );
} catch (Exception $e) {
    error_log('DB connection failed: ' . $e->getMessage());
    http_response_code(500);
    exit('Database connection failed');
}
