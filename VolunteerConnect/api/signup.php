<?php
require 'config.php';
header('Content-Type: application/json');

$fn    = trim($_POST['firstName']  ?? '');
$ln    = trim($_POST['lastName']   ?? '');
$email = trim($_POST['email']      ?? '');
$pass  = $_POST['password']        ?? '';

if (!$fn || !$ln || !$email || !$pass) {
    http_response_code(400);
    echo json_encode(['error' => 'Missing required fields']);
    exit;
}

$password_hash = password_hash($pass, PASSWORD_DEFAULT);

try {
    $stmt = $pdo->prepare(
      "INSERT INTO users 
         (first_name, last_name, email, password_hash)
       VALUES
         (?,?,?,?)"
    );
    $stmt->execute([$fn, $ln, $email, $password_hash]);

    // log the new user in
    $_SESSION['user_id'] = $pdo->lastInsertId();

    echo json_encode(['status' => 'ok']);
} catch (PDOException $e) {
    // MySQL error code 1062 = duplicate entry
    if (isset($e->errorInfo[1]) && $e->errorInfo[1] == 1062) {
        http_response_code(409);
        echo json_encode(['error' => 'Email already exists']);
    } else {
        // for any other DB error, return the real message (helpful for debugging)
        http_response_code(500);
        echo json_encode([
          'error'   => 'Database error',
          'details' => $e->getMessage()
        ]);
    }
}
