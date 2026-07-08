<?php
// Single source of truth for database credentials.
// Values come from environment variables so the same code runs locally
// (XAMPP/MAMP defaults) and inside Docker without edits.
$server   = getenv('DB_HOST') ?: 'localhost';
$user     = getenv('DB_USER') ?: 'root';
$password = getenv('DB_PASS') ?: '';
$db       = getenv('DB_NAME') ?: 'recipeapp';
$port     = (int)(getenv('DB_PORT') ?: 3306);

$con = mysqli_connect($server, $user, $password, $db, $port);
if (mysqli_connect_errno()) {
    error_log('DB connection failed: ' . mysqli_connect_error());
    http_response_code(500);
    die('Connection failed');
}
mysqli_set_charset($con, 'utf8mb4');
