<?php
require 'config.php';
$data = json_decode(file_get_contents('php://input'), true);
if (empty($data['name']) || empty($data['ingredients']) || empty($data['instructions']) || empty($data['cuisine']) || empty($data['user_id'])) {
  http_response_code(400);
  echo json_encode(['error' => 'Missing parameters']);
  exit;
}

$stmt = $mysqli->prepare(
  "INSERT INTO recipes (Name, Ingredients, Instructions, Cuisine, User_id) VALUES (?, ?, ?, ?, ?)"
);
if (!$stmt) {
  http_response_code(500);
  echo json_encode(['error' => 'Prepare failed']);
  exit;
}
$stmt->bind_param(
  'ssssi',
  $data['name'],
  $data['ingredients'],
  $data['instructions'],
  $data['cuisine'],
  $data['user_id']
);
if ($stmt->execute()) {
  echo json_encode(['success' => true, 'id' => $stmt->insert_id]);
} else {
  http_response_code(400);
  echo json_encode(['error' => 'Insert failed']);
}
$stmt->close();

$mysqli->close();