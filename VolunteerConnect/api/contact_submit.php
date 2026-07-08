<?php
require 'config.php';

$name    = trim($_POST['name']    ?? '');
$email   = trim($_POST['email']   ?? '');
$subject = trim($_POST['subject'] ?? '');
$message = trim($_POST['message'] ?? '');

if ($name === '' || $subject === '' || $message === '' || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
    http_response_code(400);
    exit('invalid');
}

$stmt = $pdo->prepare(
  "INSERT INTO contacts(name,email,subject,message)
   VALUES(?,?,?,?)"
);
$stmt->execute([$name, $email, $subject, $message]);
echo 'ok';
