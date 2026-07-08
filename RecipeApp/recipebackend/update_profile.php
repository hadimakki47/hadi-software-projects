<?php
// update_profile.php
session_start();
header('Content-Type: text/plain; charset=utf-8');
require __DIR__ . '/connection.php';

// Read the posted fields
$username = isset($_POST['username']) ? trim($_POST['username']) : '';
$fname    = isset($_POST['Fname'])    ? trim($_POST['Fname'])    : '';
$lname    = isset($_POST['Lname'])    ? trim($_POST['Lname'])    : '';
$pnumber  = isset($_POST['Pnumber'])  ? trim($_POST['Pnumber'])  : '';

if ($username === '' || $fname === '' || $lname === '' || $pnumber === '') {
    echo 'Missing parameters';
    exit;
}

// Only a logged-in user may edit their own profile
if (!isset($_SESSION['username']) || $_SESSION['username'] !== $username) {
    http_response_code(403);
    echo 'Not authorized';
    exit;
}

// Update only the editable columns
$stmt = $con->prepare(
    "UPDATE users 
     SET Fname = ?, Lname = ?, Pnumber = ? 
     WHERE username = ?"
);
if (! $stmt) {
    error_log('update_profile prepare failed: ' . $con->error);
    echo 'Error';
    exit;
}
$stmt->bind_param('ssss', $fname, $lname, $pnumber, $username);

if ($stmt->execute()) {
    echo 'Success';
} else {
    error_log('update_profile execute failed: ' . $stmt->error);
    echo 'Error';
}

$stmt->close();
$con->close();
?>
