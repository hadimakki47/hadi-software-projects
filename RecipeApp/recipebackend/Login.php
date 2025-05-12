<?php
session_start();
require_once 'connection.php';

// tell the client that we’re returning JSON
header('Content-Type: application/json; charset=UTF-8');

$response = [
    'status'  => 'Error',
    'message' => 'Missing parameters'
];

if (isset($_POST['username'], $_POST['Password'])) {
    $username = $_POST['username'];
    $Password = $_POST['Password'];

    $sql  = "SELECT * FROM users WHERE username = ?";
    $stmt = $con->prepare($sql);
    if (!$stmt) {
        $response['message'] = 'Database error: '.$con->error;
        echo json_encode($response);
        exit;
    }
    $stmt->bind_param('s', $username);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result && $result->num_rows > 0) {
        $row            = $result->fetch_assoc();
        $hashedPassword = $row['Password'];

        if (password_verify($Password, $hashedPassword)) {
            // Login success
            $_SESSION['username'] = $username;
            $_SESSION['user_id']  = $row['ID'];

            $response = [
                'status'  => 'Success',
                'user_id' => $row['ID']
            ];
        } else {
            $response['message'] = 'Invalid password';
        }
    } else {
        $response['message'] = 'User not found';
    }
    $stmt->close();
}

echo json_encode($response);
