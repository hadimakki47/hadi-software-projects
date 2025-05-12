<?php
$server = "localhost";
$user = "root";
$password = "";
$db = "recipeapp";
$con = mysqli_connect($server, $user, $password, $db);
if (mysqli_connect_errno()) {
   die('Connection failed');
}
?>