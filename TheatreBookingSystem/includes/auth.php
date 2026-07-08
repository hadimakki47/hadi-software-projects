<?php
session_start();

// Function to register user
function registerUser($username, $password, $email) {
    global $conn;
    
    $username = mysqli_real_escape_string($conn, $username);
    $email = mysqli_real_escape_string($conn, $email);
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);
    
    $sql = "INSERT INTO users (username, password, email) VALUES ('$username', '$hashed_password', '$email')";
    
    if (mysqli_query($conn, $sql)) {
        return true;
    } else {
        return false;
    }
}

// Function to login user
function loginUser($username, $password) {
    global $conn;
    
    $username = mysqli_real_escape_string($conn, $username);
    $sql = "SELECT * FROM users WHERE username = '$username'";
    $result = mysqli_query($conn, $sql);
    
    if (mysqli_num_rows($result) == 1) {
        $user = mysqli_fetch_assoc($result);
        if (password_verify($password, $user['password'])) {
            // Password is correct, start a session
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['username'] = $user['username'];
            $_SESSION['role'] = $user['role'];
            return true;
        }
    }
    return false;
}

// Function to check if user is logged in
function isLoggedIn() {
    return isset($_SESSION['user_id']);
}

// Function to check if user is admin
function isAdmin() {
    return isset($_SESSION['role']) && $_SESSION['role'] == 'admin';
}

// Function to check if user is staff
function isStaff() {
    return isset($_SESSION['role']) && $_SESSION['role'] == 'staff';
}

// Function to check if user has access to admin panel (admin or staff)
function hasAdminAccess() {
    return isAdmin() || isStaff();
}

// Function to check if user has specific permission based on staff role
function hasStaffPermission($feature) {
    if (!isStaff()) {
        return false;
    }
    
    // Staff permissions - add more as needed
    $allowedFeatures = [
        'shows', // Can manage shows
        'support', // Can handle customer support
        'dashboard' // Can access admin dashboard
    ];
    
    return in_array($feature, $allowedFeatures);
}

// Function to logout user
function logoutUser() {
    session_unset();
    session_destroy();
}

// Function to get current user details
function getCurrentUser() {
    global $conn;
    
    if (!isLoggedIn()) {
        return null;
    }
    
    $user_id = $_SESSION['user_id'];
    $sql = "SELECT id, username, email, role FROM users WHERE id = $user_id";
    $result = mysqli_query($conn, $sql);
    
    if (mysqli_num_rows($result) == 1) {
        return mysqli_fetch_assoc($result);
    }
    return null;
}
?> 