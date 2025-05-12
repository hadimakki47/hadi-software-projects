<?php
require 'config.php';
$rid = intval($_GET['recipe_id'] ?? 0);
$sql = "SELECT r.rating, r.review_text, u.username
        FROM reviews r
        JOIN users u ON u.ID=r.user_id
        WHERE r.recipe_id=?";
$stmt = $mysqli->prepare($sql);
$stmt->bind_param('i',$rid);
$stmt->execute();
$res = $stmt->get_result();
$out = [];
while($row=$res->fetch_assoc()) $out[]=$row;
echo json_encode($out);
