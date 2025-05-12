<?php
require 'config.php';

// read recipe id
$id = intval($_GET['id'] ?? 0);

$sql = "
  SELECT
    r.ID,
    r.Name,
    r.Ingredients,
    r.Instructions,
    r.Picture,
    r.Cuisine,
    r.User_id,
    u.username      AS authorUsername,
    u.ProfilePic    AS authorProfilePic
  FROM recipes r
  JOIN users u ON u.ID = r.User_id
  WHERE r.ID = ?
";
$stmt = $mysqli->prepare($sql);
$stmt->bind_param('i', $id);
$stmt->execute();
$res = $stmt->get_result()->fetch_assoc() ?: [];
header('Content-Type: application/json');
echo json_encode($res);
