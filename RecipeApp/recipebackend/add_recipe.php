<?php
require_once 'connection.php';  // defines $con

// read POST
$user_id      = $_POST['user_id']      ?? '';
$name         = $_POST['name']         ?? '';
$ingredients  = $_POST['ingredients']  ?? '';
$instructions = $_POST['instructions'] ?? '';
$cuisine      = $_POST['cuisine']      ?? '';
$image_data   = $_POST['image_data']   ?? '';

// validate
if (!$user_id || !$name || !$ingredients || !$instructions || !$cuisine) {
    echo "Missing parameters";
    exit;
}

// if image_data sent, decode & save
$picPath = null;
if ($image_data) {
    $data = base64_decode($image_data);
    if ($data !== false) {
        $uploadDir = __DIR__ . '/uploads/recipe_pics/';
        if (!is_dir($uploadDir)) mkdir($uploadDir,0755,true);
        $filename = $uploadDir . uniqid('rec_') . '.jpg';
        if (file_put_contents($filename, $data)) {
            $picPath = 'uploads/recipe_pics/' . basename($filename);
        }
    }
}

// insert into recipes
$sql = "INSERT INTO recipes (Name,Ingredients,Instructions,Picture,Cuisine,User_id)
        VALUES (?, ?, ?, ?, ?, ?)";
$stmt = $con->prepare($sql);
if (!$stmt) {
    echo 'Prepare failed: '.$con->error;
    exit;
}
$stmt->bind_param(
    'sssssi',
    $name,
    $ingredients,
    $instructions,
    $picPath,
    $cuisine,
    $user_id
);
if ($stmt->execute()) {
    echo "Success";
} else {
    echo "Error: ".$stmt->error;
}
$stmt->close();
$con->close();
