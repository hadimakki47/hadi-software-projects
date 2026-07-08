<?php
// config.php — JSON API entry point config.
// Reuses the shared connection so credentials live in one place.
header('Access-Control-Allow-Origin: *');
header('Content-Type: application/json; charset=UTF-8');

require_once __DIR__ . '/connection.php';

// Endpoints written against config.php expect $mysqli.
$mysqli = $con;
