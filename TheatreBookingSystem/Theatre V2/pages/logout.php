<?php
require_once __DIR__ . '/../includes/db_config.php';
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/functions.php';

// Log out the user
logoutUser();

// Set message for next page
$_SESSION['message'] = "You have been logged out successfully!";
$_SESSION['message_type'] = "success";

// Redirect to the login page
header("Location: /index.php");
exit();
?> 