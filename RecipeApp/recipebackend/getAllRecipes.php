<?php
require 'config.php';
$result = $mysqli->query("SELECT ID, Name, Ingredients, Instructions, Picture, Cuisine, User_id FROM recipes");
if (!$result) {
  http_response_code(500);
  echo json_encode(['error' => 'Query failed']);
  exit;
}

$recipes = [];
while ($row = $result->fetch_assoc()) {
  $recipes[] = $row;
}
echo json_encode($recipes);
