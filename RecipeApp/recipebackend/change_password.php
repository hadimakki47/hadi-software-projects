<?php
// change_password.php
header('Content-Type: text/plain; charset=utf-8');
require __DIR__ . '/connection.php';

// 1) Read inputs
$username    = isset($_POST['username'])     ? trim($_POST['username'])     : '';
$newPassword = isset($_POST['new_password']) ? $_POST['new_password']       : '';

if ($username === '' || $newPassword === '') {
    echo 'Missing parameters';
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
    echo 'Error: ' . $con->error;
    exit;
}
$stmt->bind_param('ss', $hash, $username);

if ($stmt->execute()) {
    echo 'Success';
} else {
    echo 'Error: ' . $stmt->error;
}

$stmt->close();
$con->close();
?>
