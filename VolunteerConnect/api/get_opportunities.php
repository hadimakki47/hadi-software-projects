<?php
require 'config.php';

$q = "%".($_GET['q']??'')."%";
$stmt = $pdo->prepare(
  "SELECT o.*,
          EXISTS(
            SELECT 1 FROM favorites f
            WHERE f.opp_id=o.id AND f.user_id=:uid
          ) AS favorited
   FROM opportunities o
   WHERE (title LIKE :q OR description LIKE :q)
     AND (:category='' OR category=:category)
     AND (:location='' OR location=:location)"
);

$stmt->execute([
  ':q'=>$q,
  ':category'=>$_GET['category']??'',
  ':location'=>$_GET['location']??'',
  ':uid'=>$_SESSION['user_id']??0
]);

header('Content-Type: application/json');
echo json_encode($stmt->fetchAll(PDO::FETCH_ASSOC));
