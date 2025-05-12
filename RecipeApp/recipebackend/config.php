<?php
// config.php
header('Access-Control-Allow-Origin: *');
header('Content-Type: application/json; charset=UTF-8');

$host   = 'localhost';
$db     = 'recipeapp';
$user   = 'root';
$pass   = '';

$mysqli = new mysqli($host, $user, $pass, $db);
if ($mysqli->connect_errno) {
  http_response_code(500);
  echo json_encode(['error' => 'DB connection failed']);
  exit;
}
