<?php
require 'config.php';
$stmt = $pdo->prepare(
  "INSERT INTO contacts(name,email,subject,message)
   VALUES(?,?,?,?)"
);
$stmt->execute([
  $_POST['name'],
  $_POST['email'],
  $_POST['subject'],
  $_POST['message']
]);
echo 'ok';
