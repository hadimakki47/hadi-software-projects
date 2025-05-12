<?php
require_once __DIR__ . '/../includes/db_config.php';
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/functions.php';

include __DIR__ . '/../templates/header.php';

// Check if already logged in
if (isLoggedIn()) {
    $_SESSION['message'] = "You are already logged in!";
    $_SESSION['message_type'] = "error";
    header("Location: /index.php");
    exit();
}

// Process login form
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'] ?? '';
    $password = $_POST['password'] ?? '';
    
    if (empty($username) || empty($password)) {
        $error = "Username and password are required";
    } else {
        if (loginUser($username, $password)) {
            $_SESSION['message'] = "Login successful!";
            $_SESSION['message_type'] = "success";
            header("Location: /index.php");
            exit();
        } else {
            $error = "Invalid username or password";
        }
    }
}
?>

<div class="container">
    <h1>Login</h1>
    
    <?php if (isset($error)): ?>
        <div class="alert alert-danger"><?php echo htmlspecialchars($error); ?></div>
    <?php endif; ?>
    
    <form method="POST" action="">
        <div class="form-group">
            <label for="username">Username:</label>
            <input type="text" id="username" name="username" required class="form-control">
        </div>
        
        <div class="form-group">
            <label for="password">Password:</label>
            <input type="password" id="password" name="password" required class="form-control">
        </div>
        
        <button type="submit" class="btn btn-primary">Login</button>
    </form>
    
    <p>Don't have an account? <a href="/pages/register.php">Register here</a></p>
</div>

<?php include __DIR__ . '/../templates/footer.php'; ?> 