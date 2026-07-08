<?php
require_once __DIR__ . '/../includes/db_config.php';
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/functions.php';

// Redirect if not logged in
if (!isLoggedIn()) {
    header('Location: /pages/login.php');
    exit();
}

// Get user info
$user_id = $_SESSION['user_id'];
$sql = "SELECT * FROM users WHERE id = $user_id";
$result = mysqli_query($conn, $sql);
$user = mysqli_fetch_assoc($result);

// Get user's bookings
$bookings = getUserBookings($user_id);

// Get active coupons for display
$sql = "SELECT * FROM coupons 
        WHERE is_active = 1 
        AND valid_from <= CURDATE() 
        AND valid_to >= CURDATE()
        AND (max_uses IS NULL OR times_used < max_uses)
        ORDER BY valid_to ASC";
$result = mysqli_query($conn, $sql);
$active_coupons = [];
while ($row = mysqli_fetch_assoc($result)) {
    $active_coupons[] = $row;
}

include __DIR__ . '/../templates/header.php';
?>

<div class="container my-5">
    <div class="row">
        <!-- Left Sidebar - User Profile -->
        <div class="col-lg-4 mb-4">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">Profile Information</h5>
                </div>
                <div class="card-body">
                    <div class="text-center mb-4">
                        <div class="avatar-placeholder rounded-circle bg-primary text-white d-flex align-items-center justify-content-center mx-auto mb-3" style="width: 100px; height: 100px; font-size: 2.5rem;">
                            <?php echo strtoupper(substr($user['username'], 0, 1)); ?>
                        </div>
                        <h5 class="mb-0"><?php echo htmlspecialchars($user['username']); ?></h5>
                        <p class="text-muted"><?php echo htmlspecialchars($user['email']); ?></p>
                    </div>
                    
                    <div class="mb-3">
                        <span class="text-muted small">Account created:</span>
                        <p><?php echo date('F j, Y', strtotime($user['created_at'])); ?></p>
                    </div>
                    
                    <div class="d-grid gap-2">
                        <button class="btn btn-outline-primary" data-bs-toggle="modal" data-bs-target="#editProfileModal">
                            <i class="fas fa-user-edit me-2"></i>Edit Profile
                        </button>
                        <button class="btn btn-outline-secondary" data-bs-toggle="modal" data-bs-target="#changePasswordModal">
                            <i class="fas fa-key me-2"></i>Change Password
                        </button>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Main Content -->
        <div class="col-lg-8">
            <!-- Coupons Section -->
            <div class="card mb-4">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="card-title mb-0">Available Coupons</h5>
                    <span class="badge bg-primary"><?php echo count($active_coupons); ?> Available</span>
                </div>
                <div class="card-body">
                    <?php if (empty($active_coupons)): ?>
                        <div class="alert alert-info">
                            <i class="fas fa-info-circle me-2"></i>No active coupons available at the moment. Check back later!
                        </div>
                    <?php else: ?>
                        <div class="row">
                            <?php foreach ($active_coupons as $coupon): ?>
                                <div class="col-md-6 mb-3">
                                    <div class="card h-100 border-<?php echo $coupon['discount_type'] == 'percentage' ? 'primary' : 'success'; ?>">
                                        <div class="card-header bg-<?php echo $coupon['discount_type'] == 'percentage' ? 'primary' : 'success'; ?> text-white">
                                            <div class="d-flex justify-content-between align-items-center">
                                                <h6 class="mb-0">
                                                    <?php if ($coupon['discount_type'] == 'percentage'): ?>
                                                        <?php echo $coupon['discount_amount']; ?>% OFF
                                                    <?php else: ?>
                                                        $<?php echo number_format($coupon['discount_amount'], 2); ?> OFF
                                                    <?php endif; ?>
                                                </h6>
                                                <span class="badge bg-white text-dark">
                                                    <?php 
                                                    $days_left = (strtotime($coupon['valid_to']) - time()) / (60 * 60 * 24);
                                                    echo ceil($days_left) . ' days left';
                                                    ?>
                                                </span>
                                            </div>
                                        </div>
                                        <div class="card-body">
                                            <div class="coupon-code text-center mb-2">
                                                <div class="bg-light p-2 rounded">
                                                    <span class="h5 mb-0 fw-bold"><?php echo $coupon['code']; ?></span>
                                                </div>
                                                <button class="btn btn-sm btn-link copy-coupon" data-code="<?php echo $coupon['code']; ?>">
                                                    <i class="fas fa-copy"></i> Copy code
                                                </button>
                                            </div>
                                            
                                            <div class="coupon-details small mt-3">
                                                <?php if (!empty($coupon['min_purchase']) && $coupon['min_purchase'] > 0): ?>
                                                    <div class="mb-1">
                                                        <i class="fas fa-tag me-1"></i> Min. purchase: $<?php echo number_format($coupon['min_purchase'], 2); ?>
                                                    </div>
                                                <?php endif; ?>
                                                
                                                <?php if ($coupon['discount_type'] == 'percentage' && !empty($coupon['max_discount'])): ?>
                                                    <div class="mb-1">
                                                        <i class="fas fa-hand-holding-usd me-1"></i> Max discount: $<?php echo number_format($coupon['max_discount'], 2); ?>
                                                    </div>
                                                <?php endif; ?>
                                                
                                                <div class="mb-1">
                                                    <i class="far fa-calendar-alt me-1"></i> Valid until: <?php echo date('M d, Y', strtotime($coupon['valid_to'])); ?>
                                                </div>
                                                
                                                <?php if (!empty($coupon['max_uses'])): ?>
                                                    <div class="usage-meter mt-2">
                                                        <div class="d-flex justify-content-between align-items-center small">
                                                            <span>Usage: <?php echo $coupon['times_used']; ?>/<?php echo $coupon['max_uses']; ?></span>
                                                            <span class="text-<?php echo $coupon['times_used'] / $coupon['max_uses'] > 0.8 ? 'danger' : 'success'; ?>">
                                                                <?php echo round(($coupon['max_uses'] - $coupon['times_used']) / $coupon['max_uses'] * 100); ?>% left
                                                            </span>
                                                        </div>
                                                        <div class="progress" style="height: 6px;">
                                                            <div class="progress-bar bg-<?php echo $coupon['times_used'] / $coupon['max_uses'] > 0.8 ? 'danger' : 'success'; ?>" 
                                                                 role="progressbar" 
                                                                 style="width: <?php echo $coupon['times_used'] / $coupon['max_uses'] * 100; ?>%" 
                                                                 aria-valuenow="<?php echo $coupon['times_used']; ?>" 
                                                                 aria-valuemin="0" 
                                                                 aria-valuemax="<?php echo $coupon['max_uses']; ?>">
                                                            </div>
                                                        </div>
                                                    </div>
                                                <?php endif; ?>
                                            </div>
                                        </div>
                                        <div class="card-footer bg-white">
                                            <a href="/pages/shows.php" class="btn btn-sm btn-outline-dark w-100">
                                                <i class="fas fa-shopping-cart me-1"></i> Use Now
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
            
            <!-- Recent Bookings -->
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="card-title mb-0">Recent Bookings</h5>
                    <a href="/pages/bookings.php" class="btn btn-sm btn-outline-primary">View All</a>
                </div>
                <div class="card-body">
                    <?php if (empty($bookings)): ?>
                        <div class="alert alert-info">
                            <i class="fas fa-info-circle me-2"></i>You haven't made any bookings yet.
                            <a href="/pages/shows.php" class="alert-link">Browse shows</a>
                        </div>
                    <?php else: ?>
                        <div class="list-group">
                            <?php 
                            // Display only the 3 most recent bookings
                            $recent_bookings = array_slice($bookings, 0, 3);
                            foreach ($recent_bookings as $booking): 
                            ?>
                                <div class="list-group-item list-group-item-action">
                                    <div class="d-flex w-100 justify-content-between">
                                        <h6 class="mb-1"><?php echo htmlspecialchars($booking['show_title']); ?></h6>
                                        <small class="text-muted">
                                            <?php echo date('M d, Y', strtotime($booking['date'])); ?> at 
                                            <?php echo date('g:i A', strtotime($booking['time'])); ?>
                                        </small>
                                    </div>
                                    <p class="mb-1">
                                        Hall: <?php echo htmlspecialchars($booking['hall']); ?> | 
                                        Seats: 
                                        <?php 
                                        $seat_labels = [];
                                        foreach ($booking['seats'] as $seat) {
                                            $seat_labels[] = $seat['seat_row'] . $seat['seat_number'];
                                        }
                                        echo implode(', ', $seat_labels);
                                        ?>
                                    </p>
                                    <small class="text-muted">
                                        Total Amount: $<?php echo number_format($booking['total_amount'], 2); ?>
                                    </small>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Edit Profile Modal -->
<div class="modal fade" id="editProfileModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Edit Profile</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="update_profile.php" method="POST">
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="username" class="form-label">Username</label>
                        <input type="text" class="form-control" id="username" name="username" value="<?php echo htmlspecialchars($user['username']); ?>" required>
                    </div>
                    <div class="mb-3">
                        <label for="email" class="form-label">Email</label>
                        <input type="email" class="form-control" id="email" name="email" value="<?php echo htmlspecialchars($user['email']); ?>" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Save Changes</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Change Password Modal -->
<div class="modal fade" id="changePasswordModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Change Password</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="change_password.php" method="POST">
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="current_password" class="form-label">Current Password</label>
                        <input type="password" class="form-control" id="current_password" name="current_password" required>
                    </div>
                    <div class="mb-3">
                        <label for="new_password" class="form-label">New Password</label>
                        <input type="password" class="form-control" id="new_password" name="new_password" required>
                    </div>
                    <div class="mb-3">
                        <label for="confirm_password" class="form-label">Confirm New Password</label>
                        <input type="password" class="form-control" id="confirm_password" name="confirm_password" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Change Password</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Copy coupon code functionality
    const copyButtons = document.querySelectorAll('.copy-coupon');
    copyButtons.forEach(button => {
        button.addEventListener('click', function() {
            const code = this.getAttribute('data-code');
            navigator.clipboard.writeText(code).then(() => {
                // Change button text temporarily
                const originalText = this.innerHTML;
                this.innerHTML = '<i class="fas fa-check"></i> Copied!';
                
                setTimeout(() => {
                    this.innerHTML = originalText;
                }, 2000);
            });
        });
    });
});
</script>

<?php include __DIR__ . '/../templates/footer.php'; ?> 