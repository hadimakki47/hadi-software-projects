<?php
require_once __DIR__ . '/../includes/db_config.php';
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/functions.php';

include __DIR__ . '/../templates/header.php';

// Check if already logged in
if (isLoggedIn()) {
    $_SESSION['message'] = "You are already logged in!";
    $_SESSION['message_type'] = "error";
    header("Location: index.php");
    exit();
}

// Process registration form
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST['username'] ?? '';
    $password = $_POST['password'] ?? '';
    $confirm_password = $_POST['confirm_password'] ?? '';
    $email = $_POST['email'] ?? '';
    
    // Simple validation
    if (empty($username) || empty($password) || empty($confirm_password) || empty($email)) {
        $error = "All fields are required";
    } elseif ($password !== $confirm_password) {
        $error = "Passwords do not match";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = "Invalid email format";
    } else {
        // Check if username already exists
        $query = "SELECT * FROM users WHERE username = ?";
        $stmt = mysqli_prepare($conn, $query);
        mysqli_stmt_bind_param($stmt, "s", $username);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        
        if (mysqli_num_rows($result) > 0) {
            $error = "Username already exists";
        } else {
            // Register the user
            if (registerUser($username, $password, $email)) {
                $_SESSION['message'] = "Registration successful! Please login.";
                $_SESSION['message_type'] = "success";
                header("Location: /pages/login.php");
                exit();
            } else {
                $error = "Registration failed. Username might already exist.";
            }
        }
    }
}
?>

<div class="container">
    <h1>Register</h1>
    
    <?php if (isset($error)): ?>
        <div class="alert alert-danger"><?php echo htmlspecialchars($error); ?></div>
    <?php endif; ?>
    
    <form method="POST" action="">
        <div class="form-group">
            <label for="username">Username:</label>
            <input type="text" id="username" name="username" required class="form-control">
        </div>
        
        <div class="form-group">
            <label for="email">Email:</label>
            <input type="email" id="email" name="email" required class="form-control">
        </div>
        
        <div class="form-group">
            <label for="password">Password:</label>
            <input type="password" id="password" name="password" required class="form-control">
        </div>
        
        <div class="form-group">
            <label for="confirm_password">Confirm Password:</label>
            <input type="password" id="confirm_password" name="confirm_password" required class="form-control">
        </div>
        
        <button type="submit" class="btn btn-primary">Register</button>
    </form>
    
    <p>Already have an account? <a href="/pages/login.php">Login here</a></p>
</div>

<?php include __DIR__ . '/../templates/footer.php'; ?> 