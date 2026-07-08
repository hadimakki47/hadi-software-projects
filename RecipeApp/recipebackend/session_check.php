<?php
session_start();
header('Content-Type: application/json; charset=UTF-8');

if (isset($_SESSION['user_id'])) {
    echo json_encode([
        'logged_in' => true,
        'user_id'   => $_SESSION['user_id'],
        'username'  => $_SESSION['username'] ?? null,
    ]);
} else {
    echo json_encode(['logged_in' => false]);
}
