<?php
require 'config.php';
$stmt = $pdo->prepare("INSERT IGNORE INTO subscribers(email) VALUES(?)");
$stmt->execute([$_POST['email']]);
echo 'ok';
