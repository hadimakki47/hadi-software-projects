<?php
require_once 'connection.php';  
session_start();

$username   = $_POST['username']   ?? '';
$image_data = $_POST['image_data'] ?? '';

if (!$username || !$image_data) {
    echo "Missing parameters";
    exit;
}

// Only a logged-in user may change their own picture
if (!isset($_SESSION['username']) || $_SESSION['username'] !== $username) {
    http_response_code(403);
    echo "Not authorized";
    exit;
}


$data = base64_decode($image_data);
if ($data === false) {
    echo "Invalid image data";
    exit;
}

$uploadDir = __DIR__ . '/uploads/profile_pics/';
if (!is_dir($uploadDir)) {
    mkdir($uploadDir, 0755, true);
}

$filename = $uploadDir . uniqid('prof_') . '.jpg';
if (file_put_contents($filename, $data) === false) {
    echo "Could not save file";
    exit;
}


$webPath = 'uploads/profile_pics/' . basename($filename);

$stmt = $con->prepare("UPDATE users SET ProfilePic = ? WHERE username = ?");
$stmt->bind_param('ss', $webPath, $username);
if ($stmt->execute()) {
    echo "Success";
} else {
    error_log('upload_profile_pic execute failed: ' . $stmt->error);
    echo "Error";
}
$stmt->close();
$con->close();
