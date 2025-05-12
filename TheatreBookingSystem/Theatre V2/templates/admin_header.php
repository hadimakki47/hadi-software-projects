<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - Theatre Management</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="/css/admin.css" rel="stylesheet">
</head>
<body>
    <div class="wrapper">
        <!-- Sidebar -->
        <nav id="sidebar" class="bg-dark">
            <div class="sidebar-header">
                <h3>Theatre Admin</h3>
            </div>

            <ul class="list-unstyled components">
                <li>
                    <a href="/pages/admin/admin.php">
                        <i class="fas fa-home"></i> Dashboard
                    </a>
                </li>
                <li>
                    <a href="/pages/admin/admin_shows.php">
                        <i class="fas fa-film"></i> Manage Shows
                    </a>
                </li>
                <li>
                    <a href="/pages/admin/admin_users.php">
                        <i class="fas fa-users"></i> Manage Users
                    </a>
                </li>
                <li>
                    <a href="/pages/admin/admin_bookings.php">
                        <i class="fas fa-ticket-alt"></i> Manage Bookings
                    </a>
                </li>
                <li>
                    <a href="/pages/admin/admin_coupons.php">
                        <i class="fas fa-tags"></i> Manage Coupons
                    </a>
                </li>
                <li>
                    <a href="/pages/admin/admin_chat.php">
                        <i class="fas fa-comments"></i> Customer Chat
                        <?php 
                        // Try to get count of unread chat messages
                        try {
                            // Use prepared statement for safety
                            $chatUnreadStmt = $conn->prepare("SELECT COUNT(*) as count FROM chat_messages WHERE is_from_user = 1 AND is_read = 0");
                            if ($chatUnreadStmt) {
                                $chatUnreadStmt->execute();
                                $chatUnreadResult = $chatUnreadStmt->get_result();
                                $chatUnreadRow = $chatUnreadResult->fetch_assoc();
                                $chatUnreadCount = $chatUnreadRow['count'];
                                if ($chatUnreadCount > 0): 
                        ?>
                        <span class="badge bg-danger"><?php echo $chatUnreadCount; ?></span>
                        <?php 
                                endif;
                                $chatUnreadStmt->close();
                            }
                        } catch (Exception $e) {
                            // Silently fail if the table doesn't exist yet
                        }
                        ?>
                    </a>
                </li>
                <li>
                    <a href="/index.php">
                        <i class="fas fa-arrow-left"></i> Back to Site
                    </a>
                </li>
            </ul>
        </nav>

        <!-- Page Content -->
        <div id="content">
            <!-- Top Navbar -->
            <nav class="navbar navbar-expand-lg navbar-light bg-light">
                <div class="container-fluid">
                    <button type="button" id="sidebarCollapse" class="btn btn-dark">
                        <i class="fas fa-bars"></i>
                    </button>
                    
                    <div class="ms-auto">
                        <span class="navbar-text me-3">
                            Welcome, <?php echo htmlspecialchars($_SESSION['username']); ?>
                        </span>
                        <a href="/pages/logout.php" class="btn btn-outline-danger">
                            <i class="fas fa-sign-out-alt"></i> Logout
                        </a>
                    </div>
                </div>
            </nav>

            <!-- Main Content -->
            <div class="container-fluid">
                <?php if (isset($_SESSION['message'])): ?>
                    <div class="alert alert-<?php echo $_SESSION['message_type']; ?> alert-dismissible fade show" role="alert">
                        <?php 
                        echo $_SESSION['message'];
                        unset($_SESSION['message']);
                        unset($_SESSION['message_type']);
                        ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                <?php endif; ?> 