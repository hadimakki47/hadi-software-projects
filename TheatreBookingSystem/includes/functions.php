<?php
require_once __DIR__ . '/pricing.php';

/**
 * Data-access layer. Every query goes through a prepared statement.
 */

/**
 * Run a prepared statement and return the executed mysqli_stmt.
 *
 * @param string $types  bind_param type string ('' when the query has no placeholders)
 * @param array  $params values to bind
 */
function db_stmt(mysqli $conn, string $sql, string $types = '', array $params = []): mysqli_stmt {
    $stmt = $conn->prepare($sql);
    if (!$stmt) {
        throw new Exception('Prepare failed: ' . $conn->error);
    }
    if ($types !== '') {
        $stmt->bind_param($types, ...$params);
    }
    if (!$stmt->execute()) {
        $error = $stmt->error;
        $stmt->close();
        throw new Exception('Execute failed: ' . $error);
    }
    return $stmt;
}

/** All result rows as associative arrays. */
function db_rows(mysqli $conn, string $sql, string $types = '', array $params = []): array {
    $stmt = db_stmt($conn, $sql, $types, $params);
    $rows = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    $stmt->close();
    return $rows;
}

/** First result row, or null. */
function db_row(mysqli $conn, string $sql, string $types = '', array $params = []): ?array {
    $rows = db_rows($conn, $sql, $types, $params);
    return $rows[0] ?? null;
}

/** Execute a write statement; returns the number of affected rows. */
function db_exec(mysqli $conn, string $sql, string $types = '', array $params = []): int {
    $stmt = db_stmt($conn, $sql, $types, $params);
    $affected = $stmt->affected_rows;
    $stmt->close();
    return $affected;
}

// ---------------------------------------------------------------------------
// Shows & showtimes
// ---------------------------------------------------------------------------

function getAllShows() {
    global $conn;
    return db_rows($conn, "SELECT * FROM shows ORDER BY title");
}

function getShow($show_id) {
    global $conn;
    return db_row($conn, "SELECT * FROM shows WHERE id = ?", 'i', [(int)$show_id]);
}

function getShowtimes($show_id) {
    global $conn;
    return db_rows(
        $conn,
        "SELECT * FROM showtimes WHERE show_id = ? AND date >= CURDATE() ORDER BY date, time",
        'i',
        [(int)$show_id]
    );
}

function getShowtime($showtime_id) {
    global $conn;
    return db_row(
        $conn,
        "SELECT s.*, sh.title, sh.description, sh.image_url
         FROM showtimes s
         JOIN shows sh ON s.show_id = sh.id
         WHERE s.id = ?",
        'i',
        [(int)$showtime_id]
    );
}

function getSeats($showtime_id) {
    global $conn;
    return db_rows(
        $conn,
        "SELECT * FROM seats WHERE showtime_id = ? ORDER BY seat_row, seat_number",
        'i',
        [(int)$showtime_id]
    );
}

function addShow($title, $description, $duration, $language, $genre, $age_rating, $image_url) {
    global $conn;

    db_exec(
        $conn,
        "INSERT INTO shows (title, description, duration, language, genre, age_rating, image_url)
         VALUES (?, ?, ?, ?, ?, ?, ?)",
        'ssissss',
        [$title, $description, (int)$duration, $language, $genre, $age_rating, $image_url]
    );
    return $conn->insert_id;
}

function updateShow($id, $title, $description, $duration, $language, $genre, $age_rating, $image_url) {
    global $conn;

    db_exec(
        $conn,
        "UPDATE shows SET title = ?, description = ?, duration = ?, language = ?,
                genre = ?, age_rating = ?, image_url = ?
         WHERE id = ?",
        'ssissssi',
        [$title, $description, (int)$duration, $language, $genre, $age_rating, $image_url, (int)$id]
    );
    return true;
}

function deleteShow($id) {
    global $conn;

    $id = (int)$id;
    $conn->begin_transaction();
    try {
        // Remove seats and showtimes belonging to the show, then the show itself
        db_exec(
            $conn,
            "DELETE s FROM seats s JOIN showtimes st ON s.showtime_id = st.id WHERE st.show_id = ?",
            'i',
            [$id]
        );
        db_exec($conn, "DELETE FROM showtimes WHERE show_id = ?", 'i', [$id]);
        db_exec($conn, "DELETE FROM shows WHERE id = ?", 'i', [$id]);
        $conn->commit();
        return true;
    } catch (Exception $e) {
        $conn->rollback();
        error_log('deleteShow failed: ' . $e->getMessage());
        return false;
    }
}

function addShowtime($show_id, $date, $time, $hall, $price) {
    global $conn;

    db_exec(
        $conn,
        "INSERT INTO showtimes (show_id, date, time, hall, price) VALUES (?, ?, ?, ?, ?)",
        'isssd',
        [(int)$show_id, $date, $time, $hall, (float)$price]
    );
    $showtime_id = $conn->insert_id;

    createSeatsForShowtime($showtime_id, $hall);

    return $showtime_id;
}

function updateShowtime($showtime_id, $date, $time, $hall, $price) {
    global $conn;

    db_exec(
        $conn,
        "UPDATE showtimes SET date = ?, time = ?, hall = ?, price = ? WHERE id = ?",
        'sssdi',
        [$date, $time, $hall, (float)$price, (int)$showtime_id]
    );
    return true;
}

function createSeatsForShowtime($showtime_id, $hall) {
    global $conn;

    $rows = ['A', 'B', 'C', 'D', 'E', 'F', 'G', 'H'];
    $seats_per_row = 10;
    $showtime_id = (int)$showtime_id;

    $stmt = $conn->prepare(
        "INSERT INTO seats (showtime_id, seat_row, seat_number, category) VALUES (?, ?, ?, ?)"
    );

    foreach ($rows as $row) {
        $category = 'Regular';
        if ($row == 'A' || $row == 'B') {
            $category = 'Premium';
        } else if ($row == 'G' || $row == 'H') {
            $category = 'Economy';
        }

        for ($i = 1; $i <= $seats_per_row; $i++) {
            $stmt->bind_param('isis', $showtime_id, $row, $i, $category);
            $stmt->execute();
        }
    }
    $stmt->close();
}

function getShowtimesWithBookingCount($show_id) {
    global $conn;

    $showtimes = db_rows(
        $conn,
        "SELECT s.*,
                (SELECT COUNT(*) FROM bookings b WHERE b.showtime_id = s.id) as booking_count
         FROM showtimes s
         WHERE s.show_id = ?
         ORDER BY s.date DESC, s.time ASC",
        'i',
        [(int)$show_id]
    );

    foreach ($showtimes as &$row) {
        $row['booking_count'] = (int)$row['booking_count'];
    }

    return $showtimes;
}

function deleteShowtime($showtime_id) {
    global $conn;

    $showtime_id = (int)$showtime_id;

    // Cannot delete a showtime people already booked
    $row = db_row($conn, "SELECT COUNT(*) as booking_count FROM bookings WHERE showtime_id = ?", 'i', [$showtime_id]);
    if ($row['booking_count'] > 0) {
        return false;
    }

    $conn->begin_transaction();
    try {
        db_exec($conn, "DELETE FROM seats WHERE showtime_id = ?", 'i', [$showtime_id]);
        db_exec($conn, "DELETE FROM showtimes WHERE id = ?", 'i', [$showtime_id]);
        $conn->commit();
        return true;
    } catch (Exception $e) {
        $conn->rollback();
        error_log('deleteShowtime failed: ' . $e->getMessage());
        return false;
    }
}

// ---------------------------------------------------------------------------
// Bookings
// ---------------------------------------------------------------------------

/**
 * Create a booking atomically: booking row, booking details, and seat locks
 * either all succeed or all roll back. Grabbing a seat that was booked in the
 * meantime (is_booked already 1) aborts the whole transaction.
 */
function createBooking($user_id, $showtime_id, $seat_ids, $total_amount, $payment_data = null) {
    global $conn;

    $conn->begin_transaction();

    try {
        $payment_status = $payment_data['payment_status'] ?? 'Pending';

        db_exec(
            $conn,
            "INSERT INTO bookings (user_id, showtime_id, total_amount, payment_status)
             VALUES (?, ?, ?, ?)",
            'iids',
            [(int)$user_id, (int)$showtime_id, (float)$total_amount, $payment_status]
        );
        $booking_id = $conn->insert_id;

        foreach ($seat_ids as $seat_id) {
            $seat_id = (int)$seat_id;

            db_exec(
                $conn,
                "INSERT INTO booking_details (booking_id, seat_id) VALUES (?, ?)",
                'ii',
                [$booking_id, $seat_id]
            );

            // Guard against double-booking: only lock the seat if still free
            $affected = db_exec(
                $conn,
                "UPDATE seats SET is_booked = 1 WHERE id = ? AND is_booked = 0",
                'i',
                [$seat_id]
            );
            if ($affected === 0) {
                throw new Exception("Seat $seat_id is no longer available");
            }
        }

        if ($payment_data) {
            db_exec(
                $conn,
                "UPDATE bookings SET card_last_four = ?, billing_name = ? WHERE id = ?",
                'ssi',
                [$payment_data['card_last_four'] ?? null, $payment_data['billing_name'] ?? null, $booking_id]
            );
        }

        $conn->commit();
        return $booking_id;
    } catch (Exception $e) {
        $conn->rollback();
        error_log('Booking transaction failed: ' . $e->getMessage());
        return false;
    }
}

function getUserBookings($user_id) {
    global $conn;

    $bookings = db_rows(
        $conn,
        "SELECT b.*, sh.title as show_title, s.date, s.time, s.hall
         FROM bookings b
         JOIN showtimes s ON b.showtime_id = s.id
         JOIN shows sh ON s.show_id = sh.id
         WHERE b.user_id = ?
         ORDER BY b.booking_date DESC",
        'i',
        [(int)$user_id]
    );

    foreach ($bookings as &$booking) {
        $booking['seats'] = db_rows(
            $conn,
            "SELECT s.seat_row, s.seat_number, s.category
             FROM booking_details bd
             JOIN seats s ON bd.seat_id = s.id
             WHERE bd.booking_id = ?",
            'i',
            [(int)$booking['id']]
        );
    }

    return $bookings;
}

function getAllBookings() {
    global $conn;

    return db_rows(
        $conn,
        "SELECT b.id, b.user_id, b.showtime_id, b.total_amount, b.booking_date,
                u.username, s.title as show_title, st.date, st.time
         FROM bookings b
         JOIN users u ON b.user_id = u.id
         JOIN showtimes st ON b.showtime_id = st.id
         JOIN shows s ON st.show_id = s.id
         ORDER BY b.booking_date DESC"
    );
}

/**
 * Price a seat selection: showtime base price × per-seat category multiplier.
 */
function calculateTicketPrice($showtime_id, $seat_ids) {
    global $conn;

    $showtime_id = (int)$showtime_id;
    $showtime = db_row($conn, "SELECT price FROM showtimes WHERE id = ?", 'i', [$showtime_id]);
    if (!$showtime) {
        return 0;
    }

    $categories = [];
    foreach ($seat_ids as $seat_id) {
        $seat = db_row(
            $conn,
            "SELECT category FROM seats WHERE id = ? AND showtime_id = ?",
            'ii',
            [(int)$seat_id, $showtime_id]
        );
        if ($seat) {
            $categories[] = $seat['category'];
        }
    }

    return calculateSeatsTotal((float)$showtime['price'], $categories);
}

// ---------------------------------------------------------------------------
// Users
// ---------------------------------------------------------------------------

function getAllUsers() {
    global $conn;
    return db_rows($conn, "SELECT * FROM users ORDER BY id");
}

function addUser($username, $email, $password, $role) {
    global $conn;

    $hashed_password = password_hash($password, PASSWORD_DEFAULT);
    try {
        db_exec(
            $conn,
            "INSERT INTO users (username, email, password, role) VALUES (?, ?, ?, ?)",
            'ssss',
            [$username, $email, $hashed_password, $role]
        );
        return true;
    } catch (Exception $e) {
        error_log('addUser failed: ' . $e->getMessage());
        return false;
    }
}

function updateUser($id, $username, $email, $role) {
    global $conn;

    db_exec(
        $conn,
        "UPDATE users SET username = ?, email = ?, role = ? WHERE id = ?",
        'sssi',
        [$username, $email, $role, (int)$id]
    );
    return true;
}

function deleteUser($id) {
    global $conn;

    $id = (int)$id;
    $conn->begin_transaction();
    try {
        // Remove the user's booking details, bookings, then the user
        db_exec(
            $conn,
            "DELETE bd FROM booking_details bd JOIN bookings b ON bd.booking_id = b.id WHERE b.user_id = ?",
            'i',
            [$id]
        );
        db_exec($conn, "DELETE FROM bookings WHERE user_id = ?", 'i', [$id]);
        db_exec($conn, "DELETE FROM users WHERE id = ?", 'i', [$id]);
        $conn->commit();
        return true;
    } catch (Exception $e) {
        $conn->rollback();
        error_log('deleteUser failed: ' . $e->getMessage());
        return false;
    }
}

// ---------------------------------------------------------------------------
// Dashboard stats
// ---------------------------------------------------------------------------

function getTotalShows() {
    global $conn;
    return db_row($conn, "SELECT COUNT(*) as total FROM shows")['total'];
}

function getTotalUsers() {
    global $conn;
    return db_row($conn, "SELECT COUNT(*) as total FROM users")['total'];
}

function getTotalBookings() {
    global $conn;
    return db_row($conn, "SELECT COUNT(*) as total FROM bookings")['total'];
}

function getTotalRevenue() {
    global $conn;
    $row = db_row($conn, "SELECT SUM(total_amount) as total_revenue FROM bookings");
    return $row['total_revenue'] ?? 0;
}

function getRevenueByDateRange($startDate, $endDate) {
    global $conn;

    return db_rows(
        $conn,
        "SELECT DATE(booking_date) as date, SUM(total_amount) as daily_revenue
         FROM bookings
         WHERE DATE(booking_date) BETWEEN ? AND ?
         GROUP BY DATE(booking_date)
         ORDER BY date ASC",
        'ss',
        [$startDate, $endDate]
    );
}

function getRevenueByShow() {
    global $conn;

    return db_rows(
        $conn,
        "SELECT s.id, s.title, COUNT(b.id) as booking_count, SUM(b.total_amount) as revenue
         FROM shows s
         JOIN showtimes st ON s.id = st.show_id
         JOIN bookings b ON st.id = b.showtime_id
         GROUP BY s.id
         ORDER BY revenue DESC"
    );
}

// ---------------------------------------------------------------------------
// Coupons
// ---------------------------------------------------------------------------

function getAllCoupons() {
    global $conn;
    return db_rows($conn, "SELECT * FROM coupons ORDER BY created_at DESC");
}

function getCoupon($id) {
    global $conn;
    return db_row($conn, "SELECT * FROM coupons WHERE id = ?", 'i', [(int)$id]);
}

function getCouponByCode($code) {
    global $conn;
    return db_row($conn, "SELECT * FROM coupons WHERE code = ?", 's', [$code]);
}

function createCoupon($code, $discount_amount, $discount_type, $valid_from, $valid_to, $min_purchase = 0, $max_discount = null, $max_uses = null) {
    global $conn;

    db_exec(
        $conn,
        "INSERT INTO coupons (code, discount_amount, discount_type, valid_from, valid_to, min_purchase, max_discount, max_uses)
         VALUES (?, ?, ?, ?, ?, ?, ?, ?)",
        'sdsssddi',
        [
            $code,
            (float)$discount_amount,
            $discount_type,
            $valid_from,
            $valid_to,
            (float)$min_purchase,
            $max_discount !== null ? (float)$max_discount : null,
            $max_uses !== null ? (int)$max_uses : null,
        ]
    );
    return $conn->insert_id;
}

function updateCoupon($id, $code, $discount_amount, $discount_type, $valid_from, $valid_to, $min_purchase = 0, $max_discount = null, $max_uses = null, $is_active = true) {
    global $conn;

    db_exec(
        $conn,
        "UPDATE coupons SET code = ?, discount_amount = ?, discount_type = ?, valid_from = ?,
                valid_to = ?, min_purchase = ?, max_discount = ?, max_uses = ?, is_active = ?
         WHERE id = ?",
        'sdsssddiii',
        [
            $code,
            (float)$discount_amount,
            $discount_type,
            $valid_from,
            $valid_to,
            (float)$min_purchase,
            $max_discount !== null ? (float)$max_discount : null,
            $max_uses !== null ? (int)$max_uses : null,
            $is_active ? 1 : 0,
            (int)$id,
        ]
    );
    return true;
}

function deleteCoupon($id) {
    global $conn;
    db_exec($conn, "DELETE FROM coupons WHERE id = ?", 'i', [(int)$id]);
    return true;
}

/**
 * Return the coupon row if the code can be applied to this amount, else false.
 * Validity rules live in isCouponApplicable() (includes/pricing.php).
 */
function validateCoupon($code, $total_amount) {
    $coupon = getCouponByCode($code);
    if ($coupon && isCouponApplicable($coupon, (float)$total_amount)) {
        return $coupon;
    }
    return false;
}

function applyCoupon($booking_id, $coupon_id, $discount_amount) {
    global $conn;

    $conn->begin_transaction();
    try {
        db_exec(
            $conn,
            "INSERT INTO booking_coupons (booking_id, coupon_id, discount_amount) VALUES (?, ?, ?)",
            'iid',
            [(int)$booking_id, (int)$coupon_id, (float)$discount_amount]
        );
        db_exec(
            $conn,
            "UPDATE coupons SET times_used = times_used + 1 WHERE id = ?",
            'i',
            [(int)$coupon_id]
        );
        $conn->commit();
        return true;
    } catch (Exception $e) {
        $conn->rollback();
        error_log('applyCoupon failed: ' . $e->getMessage());
        return false;
    }
}

// ---------------------------------------------------------------------------
// Live support chat
// ---------------------------------------------------------------------------

function createSupportConversation($user_id, $subject) {
    global $conn;

    try {
        db_exec(
            $conn,
            "INSERT INTO support_conversations (user_id, subject) VALUES (?, ?)",
            'is',
            [(int)$user_id, $subject]
        );
        return $conn->insert_id;
    } catch (Exception $e) {
        error_log('createSupportConversation failed: ' . $e->getMessage());
        return false;
    }
}

function addSupportMessage($conversation_id, $sender_id, $message) {
    global $conn;

    try {
        db_exec(
            $conn,
            "UPDATE support_conversations SET updated_at = CURRENT_TIMESTAMP WHERE id = ?",
            'i',
            [(int)$conversation_id]
        );
        db_exec(
            $conn,
            "INSERT INTO support_messages (conversation_id, sender_id, message) VALUES (?, ?, ?)",
            'iis',
            [(int)$conversation_id, (int)$sender_id, $message]
        );
        return $conn->insert_id;
    } catch (Exception $e) {
        error_log('addSupportMessage failed: ' . $e->getMessage());
        return false;
    }
}

function getUserSupportConversations($user_id) {
    global $conn;

    $user_id = (int)$user_id;
    return db_rows(
        $conn,
        "SELECT sc.*,
                (SELECT COUNT(*) FROM support_messages sm
                 WHERE sm.conversation_id = sc.id AND sm.sender_id != ? AND sm.is_read = 0) as unread_count
         FROM support_conversations sc
         WHERE sc.user_id = ?
         ORDER BY sc.updated_at DESC",
        'ii',
        [$user_id, $user_id]
    );
}

function getAllSupportConversations($status = 'all') {
    global $conn;

    // The support tables are created lazily by database/create_support_tables.php
    $check = $conn->query("SHOW TABLES LIKE 'support_conversations'");
    if (!$check || $check->num_rows === 0) {
        return [];
    }

    $sql = "SELECT sc.*, u.username as user_username,
            (SELECT COUNT(*) FROM support_messages sm
             WHERE sm.conversation_id = sc.id AND sm.sender_id = sc.user_id AND sm.is_read = 0) as unread_count
            FROM support_conversations sc
            JOIN users u ON sc.user_id = u.id";

    if ($status !== 'all') {
        return db_rows($conn, $sql . " WHERE sc.status = ? ORDER BY sc.updated_at DESC", 's', [$status]);
    }
    return db_rows($conn, $sql . " ORDER BY sc.updated_at DESC");
}

function getSupportMessages($conversation_id, $current_user_id) {
    global $conn;

    $check = $conn->query("SHOW TABLES LIKE 'support_messages'");
    if (!$check || $check->num_rows === 0) {
        return ['conversation' => null, 'messages' => []];
    }

    $conversation_id = (int)$conversation_id;
    $current_user_id = (int)$current_user_id;

    $conversation = db_row(
        $conn,
        "SELECT sc.*, u.username as user_username
         FROM support_conversations sc
         JOIN users u ON sc.user_id = u.id
         WHERE sc.id = ?",
        'i',
        [$conversation_id]
    );

    if (!$conversation) {
        return ['conversation' => null, 'messages' => []];
    }

    $messages = db_rows(
        $conn,
        "SELECT sm.*, u.username as sender_username, u.role as sender_role
         FROM support_messages sm
         JOIN users u ON sm.sender_id = u.id
         WHERE sm.conversation_id = ?
         ORDER BY sm.created_at ASC",
        'i',
        [$conversation_id]
    );

    // Mark messages from the other party as read
    db_exec(
        $conn,
        "UPDATE support_messages SET is_read = 1
         WHERE conversation_id = ? AND sender_id != ? AND is_read = 0",
        'ii',
        [$conversation_id, $current_user_id]
    );

    return ['conversation' => $conversation, 'messages' => $messages];
}

function closeSupportConversation($conversation_id) {
    global $conn;
    db_exec($conn, "UPDATE support_conversations SET status = 'closed' WHERE id = ?", 'i', [(int)$conversation_id]);
    return true;
}

function reopenSupportConversation($conversation_id) {
    global $conn;
    db_exec($conn, "UPDATE support_conversations SET status = 'open' WHERE id = ?", 'i', [(int)$conversation_id]);
    return true;
}
