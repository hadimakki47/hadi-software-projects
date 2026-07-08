<?php
require 'config.php';

$q = trim($_GET['q'] ?? '');

if ($q !== '') {
    $stmt = $mysqli->prepare(
        "SELECT ID, Name, Ingredients, Instructions, Picture, Cuisine, User_id
         FROM recipes
         WHERE Name LIKE CONCAT('%', ?, '%') OR Instructions LIKE CONCAT('%', ?, '%')"
    );
    $stmt->bind_param('ss', $q, $q);
    $stmt->execute();
    $res = $stmt->get_result();
} else {
    $res = $mysqli->query(
        "SELECT ID, Name, Ingredients, Instructions, Picture, Cuisine, User_id FROM recipes"
    );
}

$out = [];
while ($r = $res->fetch_assoc()) {
    $out[] = $r;
}
echo json_encode($out);
