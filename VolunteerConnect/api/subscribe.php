<?php
require 'config.php';

$email = trim($_POST['email'] ?? '');
if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    http_response_code(400);
    exit('invalid');
}

$stmt = $pdo->prepare("INSERT IGNORE INTO subscribers(email) VALUES(?)");
$stmt->execute([$email]);
echo 'ok';
