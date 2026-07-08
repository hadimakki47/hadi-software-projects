<?php
// change_password.php
session_start();
header('Content-Type: text/plain; charset=utf-8');
require __DIR__ . '/connection.php';

// 1) Read inputs
$username    = isset($_POST['username'])     ? trim($_POST['username'])     : '';
$newPassword = isset($_POST['new_password']) ? $_POST['new_password']       : '';

if ($username === '' || $newPassword === '') {
    echo 'Missing parameters';
    exit;
}

// Only a logged-in user may change their own password
if (!isset($_SESSION['username']) || $_SESSION['username'] !== $username) {
    http_response_code(403);
    echo 'Not authorized';
    exit;
}

// 2) Hash the password
$hash = password_hash($newPassword, PASSWORD_DEFAULT);

// 3) Prepare & execute
$stmt = $con->prepare(
    "UPDATE users
     SET Password = ?
     WHERE username = ?"
);
if (! $stmt) {
    error_log('change_password prepare failed: ' . $con->error);
    echo 'Error';
    exit;
}
$stmt->bind_param('ss', $hash, $username);

if ($stmt->execute()) {
    echo 'Success';
} else {
    error_log('change_password execute failed: ' . $stmt->error);
    echo 'Error';
}

$stmt->close();
$con->close();
?>
