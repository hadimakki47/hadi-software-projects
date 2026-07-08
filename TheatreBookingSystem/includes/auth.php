<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Function to register user
function registerUser($username, $password, $email) {
    global $conn;

    $hashed_password = password_hash($password, PASSWORD_DEFAULT);

    $stmt = $conn->prepare("INSERT INTO users (username, password, email) VALUES (?, ?, ?)");
    if (!$stmt) {
        return false;
    }
    $stmt->bind_param('sss', $username, $hashed_password, $email);
    $ok = $stmt->execute();
    $stmt->close();
    return $ok;
}

// Function to login user
function loginUser($username, $password) {
    global $conn;

    $stmt = $conn->prepare("SELECT * FROM users WHERE username = ?");
    if (!$stmt) {
        return false;
    }
    $stmt->bind_param('s', $username);
    $stmt->execute();
    $user = $stmt->get_result()->fetch_assoc();
    $stmt->close();

    if ($user && password_verify($password, $user['password'])) {
        // Prevent session fixation: new session ID on privilege change
        session_regenerate_id(true);
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['username'] = $user['username'];
        $_SESSION['role'] = $user['role'];
        return true;
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
    
    $stmt = $conn->prepare("SELECT id, username, email, role FROM users WHERE id = ?");
    $user_id = (int)$_SESSION['user_id'];
    $stmt->bind_param('i', $user_id);
    $stmt->execute();
    $user = $stmt->get_result()->fetch_assoc();
    $stmt->close();

    return $user ?: null;
}
