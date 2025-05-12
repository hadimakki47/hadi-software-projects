<?php
ini_set('display_errors',1);
error_reporting(E_ALL);
require 'config.php';
session_start();

$rid  = intval($_POST['recipe_id']   ?? 0);
$uid  = intval($_POST['user_id']     ?? 0);
$rate = intval($_POST['rating']      ?? 0);
$text = $_POST['review_text']        ?? '';

if(!$rid || !$uid || $text==='') {
  http_response_code(400);
  echo 'Missing parameters';
  exit;
}

$stmt = $mysqli->prepare(
  "INSERT INTO reviews(recipe_id,user_id,rating,review_text)
   VALUES(?,?,?,?)"
);
$stmt->bind_param('iiis',$rid,$uid,$rate,$text);

if($stmt->execute()) echo 'Success';
else                echo 'Error: '.$stmt->error;
