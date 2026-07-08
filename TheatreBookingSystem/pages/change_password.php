<?php
require_once __DIR__ . '/../includes/db_config.php';
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/functions.php';

// Redirect if not logged in
if (!isLoggedIn()) {
    header('Location: /pages/login.php');
    exit();
}

// Process form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $user_id = $_SESSION['user_id'];
    $current_password = $_POST['current_password'];
    $new_password = $_POST['new_password'];
    $confirm_password = $_POST['confirm_password'];
    
    // Validate inputs
    $errors = [];
    
    // Get current user data
    $sql = "SELECT password FROM users WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows !== 1) {
        $_SESSION['message'] = "User not found";
        $_SESSION['message_type'] = "danger";
        header('Location: /pages/profile.php');
        exit();
    }
    
    $user = $result->fetch_assoc();
    
    // Verify current password
    if (!password_verify($current_password, $user['password'])) {
        $errors[] = "Current password is incorrect";
    }
    
    // Validate new password
    if (empty($new_password)) {
        $errors[] = "New password is required";
    } elseif (strlen($new_password) < 6) {
        $errors[] = "New password must be at least 6 characters";
    }
    
    // Check if new password and confirmation match
    if ($new_password !== $confirm_password) {
        $errors[] = "New password and confirmation do not match";
    }
    
    // If no errors, update the password
    if (empty($errors)) {
        $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
        
        $sql = "UPDATE users SET password = ? WHERE id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("si", $hashed_password, $user_id);
        
        if ($stmt->execute()) {
            $_SESSION['message'] = "Password changed successfully!";
            $_SESSION['message_type'] = "success";
        } else {
            $_SESSION['message'] = "Error changing password: " . $conn->error;
            $_SESSION['message_type'] = "danger";
        }
    } else {
        $_SESSION['message'] = implode("<br>", $errors);
        $_SESSION['message_type'] = "danger";
    }
    
    // Redirect back to profile page
    header('Location: /pages/profile.php');
    exit();
} else {
    // Not a POST request, redirect to profile page
    header('Location: /pages/profile.php');
    exit();
} 