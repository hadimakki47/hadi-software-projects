<?php
// api/login.php
require 'config.php';

$email    = $_POST['email']    ?? '';
$password = $_POST['password'] ?? '';

$stmt = $pdo->prepare("SELECT id,password_hash FROM users WHERE email = ?");
$stmt->execute([$email]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

if ($user && password_verify($password, $user['password_hash'])) {
    $_SESSION['user_id'] = $user['id'];
    echo 'ok';
} else {
    http_response_code(401);
    exit('invalid');
}
