<?php  
require_once 'connection.php';

ini_set('display_errors', 1);
error_reporting(E_ALL);

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
        echo "Error preparing statement: " . $con->error;
        error_log("Prepare failed: " . $con->error);
        exit;
    }
    $stmt->bind_param("sss", $username, $hashedPassword, $Email);

    if ($stmt->execute()) {
        echo "Success";
    } else {
        echo "Error: " . $stmt->error;
        error_log("Execute failed: " . $stmt->error);
    }

    $stmt->close();

} else {
    echo "Missing parameters";
    error_log("Missing parameters in POST");
    exit;
}
?>
