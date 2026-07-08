<?php
require_once __DIR__ . '/../includes/db_config.php';
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/functions.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Theatre Booking System</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link href="/css/style.css" rel="stylesheet">
</head>
<body<?php echo isLoggedIn() ? ' data-user-id="' . $_SESSION['user_id'] . '"' : ''; ?>>
    <!-- Header -->
    <header>
        <nav class="navbar navbar-expand-lg navbar-light">
            <div class="container">
                <a class="navbar-brand" href="/index.php">
                    <i class="fas fa-theater-masks me-2"></i>Theatre
                </a>
                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                    <span class="navbar-toggler-icon"></span>
                </button>
                <div class="collapse navbar-collapse" id="navbarNav">
                    <ul class="navbar-nav ms-auto">
                        <li class="nav-item">
                            <a class="nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'index.php' ? 'active' : ''; ?>" href="/index.php">Home</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'shows.php' ? 'active' : ''; ?>" href="/pages/shows.php">Shows</a>
                        </li>
                        <?php if (isLoggedIn()): ?>
                            <li class="nav-item">
                                <a class="nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'bookings.php' ? 'active' : ''; ?>" href="/pages/bookings.php">My Bookings</a>
                            </li>
                            <?php if (hasAdminAccess()): ?>
                                <li class="nav-item dropdown">
                                    <a class="nav-link dropdown-toggle" href="#" id="adminDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                                        Admin
                                    </a>
                                    <ul class="dropdown-menu" aria-labelledby="adminDropdown">
                                        <li><a class="dropdown-item" href="/pages/admin/admin.php">Dashboard</a></li>
                                        <li><a class="dropdown-item" href="/pages/admin/admin_shows.php">Manage Shows</a></li>
                                        <li><a class="dropdown-item" href="/pages/admin/admin_support.php">Support Messages</a></li>
                                        <?php if (isAdmin()): ?>
                                            <li><hr class="dropdown-divider"></li>
                                            <li><a class="dropdown-item" href="/pages/admin/admin_users.php">Manage Users</a></li>
                                            <li><a class="dropdown-item" href="/pages/admin/admin_bookings.php">Manage Bookings</a></li>
                                            <li><a class="dropdown-item" href="/pages/admin/admin_coupons.php">Manage Coupons</a></li>
                                        <?php endif; ?>
                                    </ul>
                                </li>
                            <?php endif; ?>
                            <li class="nav-item">
                                <a class="nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'profile.php' ? 'active' : ''; ?>" href="/pages/profile.php">
                                    <i class="fas fa-user-circle me-1"></i>Profile
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="/pages/logout.php">Logout</a>
                            </li>
                        <?php else: ?>
                            <li class="nav-item">
                                <a class="nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'login.php' ? 'active' : ''; ?>" href="/pages/login.php">Login</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'register.php' ? 'active' : ''; ?>" href="/pages/register.php">Register</a>
                            </li>
                        <?php endif; ?>
                    </ul>
                </div>
            </div>
        </nav>
    </header>

    <?php if (isset($_SESSION['message'])): ?>
    <div class="container mt-3">
        <div class="alert alert-<?php echo $_SESSION['message_type'] == 'error' ? 'danger' : $_SESSION['message_type']; ?> alert-dismissible fade show" role="alert">
            <?php 
            echo $_SESSION['message'];
            unset($_SESSION['message']);
            unset($_SESSION['message_type']);
            ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    </div>
    <?php endif; ?>

    <div class="container my-4">
        <!-- Content will go here -->
    </div>
</body>
</html> 