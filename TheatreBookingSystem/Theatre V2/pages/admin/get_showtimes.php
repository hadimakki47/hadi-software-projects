<?php
require_once __DIR__ . '/../../includes/db_config.php';
require_once __DIR__ . '/../../includes/auth.php';
require_once __DIR__ . '/../../includes/functions.php';

// Check if user is admin
if (!isAdmin()) {
    header('HTTP/1.1 403 Forbidden');
    echo json_encode(['error' => 'Access denied']);
    exit();
}

// Get show ID from query parameters
if (!isset($_GET['show_id']) || !is_numeric($_GET['show_id'])) {
    header('HTTP/1.1 400 Bad Request');
    echo json_encode(['error' => 'Invalid show ID']);
    exit();
}

$show_id = (int)$_GET['show_id'];

// Get showtimes for this show with booking count
$sql = "SELECT s.*, 
        (SELECT COUNT(*) FROM bookings b WHERE b.showtime_id = s.id) as booking_count
        FROM showtimes s 
        WHERE s.show_id = $show_id 
        ORDER BY s.date DESC, s.time ASC";

$result = mysqli_query($conn, $sql);

if (!$result) {
    header('HTTP/1.1 500 Internal Server Error');
    echo json_encode(['error' => 'Database error: ' . mysqli_error($conn)]);
    exit();
}

$showtimes = [];
while ($row = mysqli_fetch_assoc($result)) {
    // Convert booking_count to integer
    $row['booking_count'] = (int)$row['booking_count'];
    $showtimes[] = $row;
}

// Set content type to JSON
header('Content-Type: application/json');
echo json_encode($showtimes); 