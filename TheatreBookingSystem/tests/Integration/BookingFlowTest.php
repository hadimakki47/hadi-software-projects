<?php
use PHPUnit\Framework\TestCase;

/**
 * End-to-end tests of the booking data layer against a real MySQL/MariaDB.
 *
 * Connection comes from DB_HOST / DB_PORT / DB_USER / DB_PASS / TEST_DB_NAME
 * env vars (see tests/bootstrap.php). The whole class self-skips when no
 * database server is reachable.
 */
final class BookingFlowTest extends TestCase
{
    private static ?mysqli $db = null;
    private static int $userId;

    public static function setUpBeforeClass(): void
    {
        self::$db = test_db_connect();
        if (self::$db === null) {
            return; // setUp() will skip each test
        }

        // functions.php reads the connection from $GLOBALS['conn']
        $GLOBALS['conn'] = self::$db;

        $suffix = uniqid();
        addUser("testuser_$suffix", "test_$suffix@example.com", 'secret123', 'user');
        self::$userId = (int)self::$db->insert_id;
    }

    protected function setUp(): void
    {
        if (self::$db === null) {
            $this->markTestSkipped('No MySQL server reachable — set DB_HOST/DB_PORT/DB_USER/DB_PASS to run integration tests.');
        }
    }

    /** Create a show + showtime and return [$show_id, $showtime_id]. */
    private function makeShowtime(float $price = 10.0): array
    {
        $show_id = addShow('Test Show ' . uniqid(), 'A test show', 120, 'English', 'Drama', 'PG', '');
        $this->assertIsInt($show_id);

        $showtime_id = addShowtime($show_id, date('Y-m-d', strtotime('+7 days')), '19:30:00', 'Hall 1', $price);
        $this->assertIsInt($showtime_id);

        return [$show_id, $showtime_id];
    }

    public function testAddShowtimeGeneratesFullSeatMap(): void
    {
        [, $showtime_id] = $this->makeShowtime();

        $seats = getSeats($showtime_id);
        $this->assertCount(80, $seats, '8 rows × 10 seats');

        $categories = array_count_values(array_column($seats, 'category'));
        $this->assertSame(20, $categories['Premium'], 'rows A+B');
        $this->assertSame(40, $categories['Regular'], 'rows C–F');
        $this->assertSame(20, $categories['Economy'], 'rows G+H');
    }

    public function testTicketPriceUsesCategoryMultipliers(): void
    {
        [, $showtime_id] = $this->makeShowtime(10.0);
        $seats = getSeats($showtime_id);

        $premium = array_values(array_filter($seats, fn($s) => $s['category'] === 'Premium'))[0];
        $regular = array_values(array_filter($seats, fn($s) => $s['category'] === 'Regular'))[0];
        $economy = array_values(array_filter($seats, fn($s) => $s['category'] === 'Economy'))[0];

        $price = calculateTicketPrice($showtime_id, [$premium['id'], $regular['id'], $economy['id']]);
        $this->assertEqualsWithDelta(33.0, $price, 0.001); // 15 + 10 + 8
    }

    public function testBookingMarksSeatsAndIsAtomic(): void
    {
        [, $showtime_id] = $this->makeShowtime(10.0);
        $seats = getSeats($showtime_id);
        $picked = [$seats[0]['id'], $seats[1]['id']];

        $total = calculateTicketPrice($showtime_id, $picked);
        $booking_id = createBooking(self::$userId, $showtime_id, $picked, $total);
        $this->assertNotFalse($booking_id, 'booking should succeed');

        // Both seats are now locked
        foreach (getSeats($showtime_id) as $seat) {
            $expected = in_array($seat['id'], $picked) ? 1 : 0;
            $this->assertEquals($expected, $seat['is_booked'], "seat {$seat['id']}");
        }

        // Booking appears in the user's history with its seats
        $bookings = getUserBookings(self::$userId);
        $ours = array_values(array_filter($bookings, fn($b) => $b['id'] == $booking_id));
        $this->assertCount(1, $ours);
        $this->assertCount(2, $ours[0]['seats']);
    }

    public function testDoubleBookingIsRejectedAndRolledBack(): void
    {
        [, $showtime_id] = $this->makeShowtime(10.0);
        $seats = getSeats($showtime_id);
        $contested = [$seats[0]['id']];

        $first = createBooking(self::$userId, $showtime_id, $contested, 10.0);
        $this->assertNotFalse($first);

        $countBefore = db_row(self::$db, "SELECT COUNT(*) AS n FROM bookings")['n'];

        // Same seat again → whole transaction must fail
        $second = createBooking(self::$userId, $showtime_id, $contested, 10.0);
        $this->assertFalse($second, 'booking an already-taken seat must fail');

        $countAfter = db_row(self::$db, "SELECT COUNT(*) AS n FROM bookings")['n'];
        $this->assertSame($countBefore, $countAfter, 'failed booking must not leave a bookings row behind');
    }

    public function testDeleteShowtimeRefusedWhenBooked(): void
    {
        [, $showtime_id] = $this->makeShowtime(10.0);
        $seats = getSeats($showtime_id);

        createBooking(self::$userId, $showtime_id, [$seats[0]['id']], 15.0);

        $this->assertFalse(deleteShowtime($showtime_id), 'cannot delete a showtime with bookings');
        $this->assertNotNull(getShowtime($showtime_id), 'showtime must still exist');
    }

    public function testCouponLifecycle(): void
    {
        $code = 'TEST' . strtoupper(substr(uniqid(), -6));
        $coupon_id = createCoupon(
            $code,
            20,             // 20%
            'percentage',
            date('Y-m-d', strtotime('-1 day')),
            date('Y-m-d', strtotime('+30 days')),
            50,             // min purchase
            null,
            2               // max 2 uses
        );
        $this->assertIsInt($coupon_id);

        // Below min purchase → rejected
        $this->assertFalse(validateCoupon($code, 49.99));

        // Valid purchase → returns the coupon row
        $coupon = validateCoupon($code, 100.0);
        $this->assertIsArray($coupon);
        $this->assertEqualsWithDelta(20.0, calculateDiscount($coupon, 100.0), 0.001);

        // Use it twice (the cap)
        [, $showtime_id] = $this->makeShowtime(100.0);
        $seats = getSeats($showtime_id);
        $b1 = createBooking(self::$userId, $showtime_id, [$seats[0]['id']], 100.0);
        $b2 = createBooking(self::$userId, $showtime_id, [$seats[1]['id']], 100.0);
        $this->assertTrue(applyCoupon($b1, $coupon_id, 20.0));
        $this->assertTrue(applyCoupon($b2, $coupon_id, 20.0));

        // Usage cap reached → no longer valid
        $this->assertFalse(validateCoupon($code, 100.0));
    }

    public function testUnknownCouponIsRejected(): void
    {
        $this->assertFalse(validateCoupon('NO_SUCH_CODE_' . uniqid(), 100.0));
    }
}
