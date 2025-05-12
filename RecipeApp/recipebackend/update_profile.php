<?php
// update_profile.php
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

// Update only the editable columns
$stmt = $con->prepare(
    "UPDATE users 
     SET Fname = ?, Lname = ?, Pnumber = ? 
     WHERE username = ?"
);
if (! $stmt) {
    echo 'Error: ' . $con->error;
    exit;
}
$stmt->bind_param('ssss', $fname, $lname, $pnumber, $username);

if ($stmt->execute()) {
    echo 'Success';
} else {
    echo 'Error: ' . $stmt->error;
}

$stmt->close();
$con->close();
?>
