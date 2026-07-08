<?php
require_once __DIR__ . '/../../includes/db_config.php';
require_once __DIR__ . '/../../includes/auth.php';
require_once __DIR__ . '/../../includes/functions.php';

// Check if user is admin, redirect staff users
if (!isAdmin()) {
    $_SESSION['message'] = "You don't have permission to access booking management.";
    $_SESSION['message_type'] = "danger";
    header('Location: /pages/admin/admin.php');
    exit();
}

include __DIR__ . '/../../templates/admin_header.php';

// Handle actions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['action'])) {
        switch ($_POST['action']) {
            case 'delete':
                $booking_id = (int)$_POST['booking_id'];
                // Add function to delete booking
                // deleteBooking($booking_id);
                $_SESSION['message'] = "Booking deleted successfully!";
                $_SESSION['message_type'] = "success";
                break;
        }
        
        header('Location: ' . $_SERVER['PHP_SELF']);
        exit();
    }
}

// Get all bookings
$bookings = getAllBookings();
?>

<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h4 class="mb-0">Manage Bookings</h4>
        <div>
            <button class="btn btn-outline-primary me-2" id="exportBtn">
                <i class="fas fa-file-export"></i> Export Data
            </button>
            <a href="/pages/admin/admin_booking_reports.php" class="btn btn-outline-info">
                <i class="fas fa-chart-bar"></i> View Reports
            </a>
        </div>
    </div>
    <div class="card-body">
        <div class="row mb-3">
            <div class="col-md-4">
                <select id="statusFilter" class="form-select">
                    <option value="">All Statuses</option>
                    <option value="confirmed">Confirmed</option>
                    <option value="pending">Pending</option>
                    <option value="cancelled">Cancelled</option>
                </select>
            </div>
            <div class="col-md-4">
                <input type="text" id="searchInput" class="form-control" placeholder="Search...">
            </div>
            <div class="col-md-4">
                <div class="input-group">
                    <input type="date" id="dateFilter" class="form-control">
                    <button class="btn btn-primary" id="filterBtn">
                        <i class="fas fa-filter"></i> Filter
                    </button>
                </div>
            </div>
        </div>
        
        <div class="table-responsive">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>User</th>
                        <th>Show</th>
                        <th>Date & Time</th>
                        <th>Seats</th>
                        <th>Amount</th>
                        <th>Status</th>
                        <th>Booking Date</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($bookings)): ?>
                        <tr>
                            <td colspan="9" class="text-center">No bookings found</td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($bookings as $booking): ?>
                            <tr>
                                <td><?php echo $booking['id']; ?></td>
                                <td><?php echo htmlspecialchars($booking['username']); ?></td>
                                <td><?php echo htmlspecialchars($booking['show_title']); ?></td>
                                <td>
                                    <?php echo date('M d, Y', strtotime($booking['date'])); ?><br>
                                    <?php echo date('g:i A', strtotime($booking['time'])); ?>
                                </td>
                                <td>
                                    <button type="button" class="btn btn-sm btn-outline-info" 
                                            data-bs-toggle="modal" 
                                            data-bs-target="#viewSeatsModal"
                                            data-booking-id="<?php echo $booking['id']; ?>"
                                            data-show-title="<?php echo htmlspecialchars($booking['show_title']); ?>">
                                        View Seats
                                    </button>
                                </td>
                                <td>$<?php echo number_format($booking['total_amount'], 2); ?></td>
                                <td>
                                    <span class="badge bg-success">Confirmed</span>
                                </td>
                                <td><?php echo date('M d, Y', strtotime($booking['booking_date'])); ?></td>
                                <td>
                                    <button type="button" class="btn btn-sm btn-primary me-1" 
                                            data-bs-toggle="modal" 
                                            data-bs-target="#viewBookingModal"
                                            data-booking-id="<?php echo $booking['id']; ?>">
                                        <i class="fas fa-eye"></i>
                                    </button>
                                    <button type="button" class="btn btn-sm btn-danger" 
                                            data-bs-toggle="modal" 
                                            data-bs-target="#deleteBookingModal"
                                            data-booking-id="<?php echo $booking['id']; ?>"
                                            data-show-title="<?php echo htmlspecialchars($booking['show_title']); ?>">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- View Booking Modal -->
<div class="modal fade" id="viewBookingModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Booking Details</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="text-center mb-4">
                    <div class="spinner-border text-primary" role="status">
                        <span class="visually-hidden">Loading...</span>
                    </div>
                    <p>Loading booking details...</p>
                </div>
                <div id="bookingDetails" class="d-none">
                    <!-- Details will be loaded here -->
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                <button type="button" class="btn btn-primary" id="printBooking">
                    <i class="fas fa-print"></i> Print Ticket
                </button>
            </div>
        </div>
    </div>
</div>

<!-- View Seats Modal -->
<div class="modal fade" id="viewSeatsModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Seats for <span id="seatShowTitle"></span></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="text-center mb-4" id="seatsLoading">
                    <div class="spinner-border text-primary" role="status">
                        <span class="visually-hidden">Loading...</span>
                    </div>
                    <p>Loading seats...</p>
                </div>
                <ul class="list-group" id="seatsList">
                    <!-- Seats will be loaded here -->
                </ul>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<!-- Delete Booking Modal -->
<div class="modal fade" id="deleteBookingModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Delete Booking</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form method="POST" action="">
                <div class="modal-body">
                    <input type="hidden" name="action" value="delete">
                    <input type="hidden" name="booking_id" id="deleteBookingId">
                    <p>Are you sure you want to delete the booking for <span id="deleteShowTitle" class="fw-bold"></span>?</p>
                    <p class="text-danger">This action cannot be undone! The seats will be released and available for booking again.</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-danger">Delete Booking</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // View Seats Modal
    const viewSeatsModal = document.getElementById('viewSeatsModal');
    if (viewSeatsModal) {
        viewSeatsModal.addEventListener('show.bs.modal', function(event) {
            const button = event.relatedTarget;
            const bookingId = button.getAttribute('data-booking-id');
            const showTitle = button.getAttribute('data-show-title');
            
            document.getElementById('seatShowTitle').textContent = showTitle;
            document.getElementById('seatsLoading').classList.remove('d-none');
            document.getElementById('seatsList').innerHTML = '';
            
            // Mock data - in a real app, you would fetch this via AJAX
            setTimeout(() => {
                document.getElementById('seatsLoading').classList.add('d-none');
                const seatsList = document.getElementById('seatsList');
                
                const seats = [
                    { row: 'A', number: 5, category: 'Premium' },
                    { row: 'A', number: 6, category: 'Premium' },
                    { row: 'B', number: 7, category: 'Regular' }
                ];
                
                seats.forEach(seat => {
                    const item = document.createElement('li');
                    item.className = 'list-group-item d-flex justify-content-between align-items-center';
                    
                    const seatText = document.createTextNode(`Row ${seat.row}, Seat ${seat.number}`);
                    item.appendChild(seatText);
                    
                    const badge = document.createElement('span');
                    badge.className = `badge bg-${seat.category === 'Premium' ? 'success' : seat.category === 'Economy' ? 'info' : 'primary'}`;
                    badge.textContent = seat.category;
                    item.appendChild(badge);
                    
                    seatsList.appendChild(item);
                });
            }, 500);
        });
    }
    
    // Delete Booking Modal
    const deleteBookingModal = document.getElementById('deleteBookingModal');
    if (deleteBookingModal) {
        deleteBookingModal.addEventListener('show.bs.modal', function(event) {
            const button = event.relatedTarget;
            document.getElementById('deleteBookingId').value = button.getAttribute('data-booking-id');
            document.getElementById('deleteShowTitle').textContent = button.getAttribute('data-show-title');
        });
    }
    
    // Filter functionality
    const searchInput = document.getElementById('searchInput');
    const statusFilter = document.getElementById('statusFilter');
    const filterBtn = document.getElementById('filterBtn');
    
    if (filterBtn) {
        filterBtn.addEventListener('click', function() {
            // In a real app, you would implement filtering logic here
            alert('Filtering functionality would be implemented here');
        });
    }
    
    // Export functionality
    const exportBtn = document.getElementById('exportBtn');
    if (exportBtn) {
        exportBtn.addEventListener('click', function() {
            // In a real app, you would implement export functionality here
            alert('Export functionality would be implemented here');
        });
    }
});
</script>

<?php include __DIR__ . '/../../templates/admin_footer.php'; ?> 