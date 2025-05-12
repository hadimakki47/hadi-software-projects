<?php
require 'config.php';
$q = isset($_GET['q']) ? $mysqli->real_escape_string($_GET['q']) : '';
$sql = $q
  ? "SELECT ID, Name, Ingredients, Instructions, Picture, Cuisine, User_id FROM recipes WHERE Name LIKE '%$q%' OR Instructions LIKE '%$q%'"
  : "SELECT ID, Name, Ingredients, Instructions, Picture, Cuisine, User_id FROM recipes";
$res = $mysqli->query($sql);
$out = [];
while ($r = $res->fetch_assoc()) {
  $out[] = $r;
}
echo json_encode($out);
