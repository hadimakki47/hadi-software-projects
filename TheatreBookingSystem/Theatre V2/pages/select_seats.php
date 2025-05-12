<?php
require_once __DIR__ . '/../includes/db_config.php';
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/functions.php';

include __DIR__ . '/../templates/header.php';

// Redirect if not logged in
if (!isLoggedIn()) {
    $_SESSION['message'] = "Please login to book tickets";
    $_SESSION['message_type'] = "error";
    header("Location: /pages/login.php");
    exit();
}

// Check if showtime ID is provided
if (!isset($_GET['id']) || empty($_GET['id'])) {
    $_SESSION['message'] = "Invalid showtime ID";
    $_SESSION['message_type'] = "error";
    header("Location: /pages/shows.php");
    exit();
}

$showtime_id = (int)$_GET['id'];
$showtime = getShowtime($showtime_id);

// Check if showtime exists
if (!$showtime) {
    $_SESSION['message'] = "Showtime not found";
    $_SESSION['message_type'] = "error";
    header("Location: /pages/shows.php");
    exit();
}

// Get seat information for this showtime
$stmt = $conn->prepare("SELECT s.id, s.seat_row, s.seat_number, s.category, s.is_booked,
                            CASE 
                                WHEN s.is_booked = 1 OR bd.id IS NOT NULL THEN 'booked' 
                                ELSE 'available' 
                            END AS status
                        FROM seats s
                        LEFT JOIN booking_details bd ON s.id = bd.seat_id
                        WHERE s.showtime_id = ?
                        ORDER BY s.seat_row, s.seat_number");
$stmt->bind_param("i", $showtime_id);
$stmt->execute();
$result = $stmt->get_result();
$seats = [];
while ($row = $result->fetch_assoc()) {
    $seats[] = $row;
}

// Organize seats by row
$rows = [];
foreach ($seats as $seat) {
    $seatClass = 'seat ' . strtolower($seat['category']);
    if ($seat['status'] === 'booked') {
        $seatClass .= ' booked';
    }
    $rows[$seat['seat_row']][] = [
        'id' => $seat['id'],
        'number' => $seat['seat_number'],
        'category' => strtolower($seat['category']),
        'status' => $seat['status'],
        'class' => $seatClass
    ];
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!isset($_POST['seats']) || empty($_POST['seats'])) {
        $error = "Please select at least one seat";
    } else {
        $selected_seats = $_POST['seats'];
        $total_amount = calculateTicketPrice($showtime_id, $selected_seats);
        
        $_SESSION['booking_data'] = [
            'showtime_id' => $showtime_id,
            'seats' => $selected_seats,
            'total_amount' => $total_amount
        ];
        
        header('Location: /pages/confirm_booking.php');
        exit();
    }
}
?>

<style>
.seating-chart {
    margin: 30px 0;
    text-align: center;
    position: relative;
}

.screen {
    background: #d9d9d9;
    height: 40px;
    line-height: 40px;
    border-radius: 5px;
    margin-bottom: 30px;
    box-shadow: 0 3px 10px rgba(0,0,0,0.1);
    text-align: center;
    font-weight: bold;
    color: #333;
}

.seats-grid {
    display: grid;
    grid-template-columns: repeat(10, 1fr);
    gap: 10px;
    margin: 0 auto;
    max-width: 600px;
}

.seat-container {
    position: relative;
}

.seat-checkbox {
    position: absolute;
    opacity: 0;
    height: 0;
    width: 0;
}

.seat {
    display: flex;
    align-items: center;
    justify-content: center;
    width: 40px;
    height: 40px;
    border-radius: 5px;
    cursor: pointer;
    font-size: 12px;
    font-weight: bold;
    margin: 0 auto;
    transition: all 0.2s ease-in-out;
    border: 2px solid #bbb;
}

.seat.premium {
    background-color: #28a745;
    color: white;
    border-color: #218838;
}

.seat.regular {
    background-color: #007bff;
    color: white;
    border-color: #0069d9;
}

.seat.economy {
    background-color: #17a2b8;
    color: white;
    border-color: #138496;
}

.seat.booked {
    background-color: #dc3545;
    color: white;
    border-color: #c82333;
    cursor: not-allowed;
    opacity: 0.6;
}

.seat-checkbox:checked + .seat:not(.booked) {
    background-color: #ffc107;
    border-color: #e0a800;
    color: #000;
    transform: scale(1.1);
}

.seat-legend {
    display: flex;
    justify-content: center;
    gap: 20px;
    margin-top: 20px;
}

.legend-item {
    display: flex;
    align-items: center;
    gap: 5px;
    font-size: 14px;
}

.legend-item .seat {
    width: 20px;
    height: 20px;
    font-size: 0;
}

.booking-summary {
    background-color: #f8f9fa;
    padding: 20px;
    border-radius: 5px;
    box-shadow: 0 2px 5px rgba(0,0,0,0.1);
}
</style>

<div class="container">
    <h1>Select Seats for <?php echo htmlspecialchars($showtime['title']); ?></h1>
    <p class="lead"><?php echo date('F j, Y', strtotime($showtime['date'])); ?> at <?php echo date('g:i A', strtotime($showtime['time'])); ?></p>
    
    <?php if (isset($error)): ?>
        <div class="alert alert-danger"><?php echo htmlspecialchars($error); ?></div>
    <?php endif; ?>
    
    <div class="row">
        <div class="col-md-8">
            <div class="seating-chart">
                <div class="screen">Screen</div>
                
                <form method="POST" action="">
                    <div class="seats-grid">
                        <?php 
                        $current_row = "";
                        foreach ($rows as $row => $seatsInRow): 
                            // Add row label if this is a new row
                            if ($current_row != $row):
                                $current_row = $row;
                                // If not the first row, close the previous row
                                if ($current_row !== ""): 
                                    echo '</div><div class="seats-grid">';
                                endif;
                                // Add row label
                                echo '<div class="seat-container" style="display: flex; align-items: center; justify-content: center; font-weight: bold;">' . $current_row . '</div>';
                            endif;
                            
                            // Loop through each seat in this row
                            foreach ($seatsInRow as $seat):
                        ?>
                            <div class="seat-container">
                                <input type="checkbox" 
                                       id="seat_<?php echo $seat['id']; ?>" 
                                       name="seats[]" 
                                       value="<?php echo $seat['id']; ?>"
                                       <?php echo $seat['status'] === 'booked' ? 'disabled' : ''; ?>
                                       class="seat-checkbox">
                                <label for="seat_<?php echo $seat['id']; ?>" 
                                       class="seat <?php echo $seat['category']; ?> <?php echo $seat['status'] === 'booked' ? 'booked' : ''; ?>">
                                    <?php echo $current_row . $seat['number']; ?>
                                </label>
                            </div>
                        <?php 
                            endforeach; // End of seats in row loop
                        endforeach; // End of rows loop
                        ?>
                    </div>
                    
                    <div class="seat-legend mt-3">
    <div class="legend-item">
                            <span class="seat premium"></span> Premium ($<?php echo number_format($showtime['price'] * 1.5, 2); ?>)
    </div>
    <div class="legend-item">
                            <span class="seat regular"></span> Regular ($<?php echo number_format($showtime['price'], 2); ?>)
    </div>
    <div class="legend-item">
                            <span class="seat economy"></span> Economy ($<?php echo number_format($showtime['price'] * 0.8, 2); ?>)
    </div>
    <div class="legend-item">
                            <span class="seat booked"></span> Booked
    </div>
</div>

                    <button type="submit" class="btn btn-primary mt-3">Continue to Payment</button>
                </form>
            </div>
        </div>
        
        <div class="col-md-4">
            <div class="booking-summary">
                <h3>Booking Summary</h3>
                <p><strong>Show:</strong> <?php echo htmlspecialchars($showtime['title']); ?></p>
                <p><strong>Date:</strong> <?php echo date('F j, Y', strtotime($showtime['date'])); ?></p>
                <p><strong>Time:</strong> <?php echo date('g:i A', strtotime($showtime['time'])); ?></p>
                <p><strong>Hall:</strong> <?php echo htmlspecialchars($showtime['hall']); ?></p>
                <p><strong>Base Price:</strong> $<?php echo number_format($showtime['price'], 2); ?></p>
                <div class="mt-3">
                    <p><strong>Pricing Structure:</strong></p>
                    <ul>
                        <li>Premium seats: 150% of base price</li>
                        <li>Regular seats: 100% of base price</li>
                        <li>Economy seats: 80% of base price</li>
                    </ul>
                </div>
                <hr>
                <p class="text-muted small">
                    <i class="fas fa-info-circle"></i> Select your seats by clicking on them. Premium seats are in rows A-B, Regular seats in rows C-F, and Economy seats in rows G-H.
                </p>
            </div>
        </div>
    </div>
</div>

<div class="container mt-3">
    <p><a href="show_details.php?id=<?php echo $showtime['show_id']; ?>" class="btn btn-outline-secondary"><i class="fas fa-arrow-left"></i> Back to Show Details</a></p>
    </div>

<?php
include __DIR__ . '/../templates/footer.php';
?> 