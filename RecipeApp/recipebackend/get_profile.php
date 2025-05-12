<?php
// get_profile.php
header('Content-Type: application/json; charset=utf-8');
require __DIR__ . '/connection.php';

// Read the posted username
$username = isset($_POST['username']) ? trim($_POST['username']) : '';
if ($username === '') {
    echo json_encode(['error' => 'Missing username']);
    exit;
}

// Fetch the user by username
$stmt = $con->prepare(
    "SELECT Email, Fname, Lname, username, Pnumber 
     FROM users 
     WHERE username = ?"
);
if (! $stmt) {
    echo json_encode(['error' => 'DB error: ' . $con->error]);
    exit;
}
$stmt->bind_param('s', $username);
$stmt->execute();
$result = $stmt->get_result();

// Return JSON matching what your Java code expects
if ($row = $result->fetch_assoc()) {
    echo json_encode([
        'Email'    => $row['Email'],
        'Fname'    => $row['Fname'],
        'Lname'    => $row['Lname'],
        'username' => $row['username'],
        'Pnumber'  => $row['Pnumber']
    ]);
} else {
    echo json_encode(['error' => 'NoData']);
}

$stmt->close();
$con->close();
?>
