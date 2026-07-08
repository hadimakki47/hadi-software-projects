<?php
require_once 'connection.php';

// Check if POST variables exist
if (isset($_POST['username']) && isset($_POST['Password']) && isset($_POST['Email'])) {

    $username = $_POST['username'];
    $Password = $_POST['Password']; 
    $Email = $_POST['Email']; 

    if (empty($username) || empty($Password) || empty($Email)) {
        echo "Username, password, or email is empty";
        error_log("Empty username, password, or email");
        exit;
    }

    $hashedPassword = password_hash($Password, PASSWORD_DEFAULT);

    $query = "INSERT INTO users (username, Password, Email) VALUES (?, ?, ?)";
    $stmt = $con->prepare($query);

    if (!$stmt) {
        error_log("Prepare failed: " . $con->error);
        echo "Error";
        exit;
    }
    $stmt->bind_param("sss", $username, $hashedPassword, $Email);

    if ($stmt->execute()) {
        echo "Success";
    } else if ($con->errno == 1062) {
        // Duplicate entry (username/email already taken)
        echo "Username or email already exists";
    } else {
        error_log("Execute failed: " . $stmt->error);
        echo "Error";
    }

    $stmt->close();

} else {
    echo "Missing parameters";
    error_log("Missing parameters in POST");
    exit;
}
?>
