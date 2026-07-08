<?php
require 'config.php';
session_start();

$rid  = intval($_POST['recipe_id']   ?? 0);
$uid  = intval($_POST['user_id']     ?? 0);
$rate = intval($_POST['rating']      ?? 0);
$text = $_POST['review_text']        ?? '';

if(!$rid || !$uid || $text==='' || $rate < 1 || $rate > 5) {
  http_response_code(400);
  echo 'Missing parameters';
  exit;
}

$stmt = $mysqli->prepare(
  "INSERT INTO reviews(recipe_id,user_id,rating,review_text)
   VALUES(?,?,?,?)"
);
$stmt->bind_param('iiis',$rid,$uid,$rate,$text);

if($stmt->execute()) {
  echo 'Success';
} else {
  error_log('add_review execute failed: ' . $stmt->error);
  echo 'Error';
}
