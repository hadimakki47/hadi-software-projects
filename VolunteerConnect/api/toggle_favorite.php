<?php
require 'config.php';
if (!isset($_SESSION['user_id'])) {
  http_response_code(401);
  exit;
}
$uid = $_SESSION['user_id'];
$opp = (int)($_POST['opp_id']??0);

$exists = $pdo->prepare("SELECT 1 FROM favorites WHERE user_id=? AND opp_id=?");
$exists->execute([$uid,$opp]);

if ($exists->fetch()) {
  $pdo->prepare("DELETE FROM favorites WHERE user_id=? AND opp_id=?")
      ->execute([$uid,$opp]);
} else {
  $pdo->prepare("INSERT INTO favorites(user_id,opp_id) VALUES(?,?)")
      ->execute([$uid,$opp]);
}

echo 'ok';
