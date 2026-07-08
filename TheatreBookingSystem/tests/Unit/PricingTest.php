<?php
use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\Attributes\DataProvider;

final class PricingTest extends TestCase
{
    // ------------------------------------------------------------------
    // Seat pricing
    // ------------------------------------------------------------------

    public static function multiplierProvider(): array
    {
        return [
            'premium rows cost 50% more' => ['Premium', 1.5],
            'economy rows cost 20% less' => ['Economy', 0.8],
            'regular rows cost base price' => ['Regular', 1.0],
            'unknown category falls back to base' => ['VIP', 1.0],
        ];
    }

    #[DataProvider('multiplierProvider')]
    public function testSeatCategoryMultiplier(string $category, float $expected): void
    {
        $this->assertSame($expected, seatCategoryMultiplier($category));
    }

    public function testSeatsTotalMixesCategories(): void
    {
        // 10 * 1.5 + 10 * 1.0 + 10 * 0.8 = 33
        $total = calculateSeatsTotal(10.0, ['Premium', 'Regular', 'Economy']);
        $this->assertEqualsWithDelta(33.0, $total, 0.001);
    }

    public function testSeatsTotalOfNoSeatsIsZero(): void
    {
        $this->assertSame(0.0, calculateSeatsTotal(10.0, []));
    }

    // ------------------------------------------------------------------
    // Discount calculation
    // ------------------------------------------------------------------

    private function coupon(array $overrides = []): array
    {
        return $overrides + [
            'discount_type'   => 'percentage',
            'discount_amount' => 10,
            'max_discount'    => null,
            'is_active'       => 1,
            'valid_from'      => '2000-01-01',
            'valid_to'        => '2099-12-31',
            'min_purchase'    => 0,
            'max_uses'        => null,
            'times_used'      => 0,
        ];
    }

    public function testPercentageDiscount(): void
    {
        $coupon = $this->coupon(['discount_amount' => 20]);
        $this->assertEqualsWithDelta(20.0, calculateDiscount($coupon, 100.0), 0.001);
    }

    public function testPercentageDiscountIsCappedAtMaxDiscount(): void
    {
        $coupon = $this->coupon(['discount_amount' => 50, 'max_discount' => 15]);
        $this->assertEqualsWithDelta(15.0, calculateDiscount($coupon, 100.0), 0.001);
    }

    public function testFixedDiscount(): void
    {
        $coupon = $this->coupon(['discount_type' => 'fixed', 'discount_amount' => 25]);
        $this->assertEqualsWithDelta(25.0, calculateDiscount($coupon, 100.0), 0.001);
    }

    public function testFixedDiscountNeverExceedsTotal(): void
    {
        $coupon = $this->coupon(['discount_type' => 'fixed', 'discount_amount' => 25]);
        $this->assertEqualsWithDelta(10.0, calculateDiscount($coupon, 10.0), 0.001);
    }

    // ------------------------------------------------------------------
    // Coupon applicability
    // ------------------------------------------------------------------

    public function testActiveCouponInWindowApplies(): void
    {
        $this->assertTrue(isCouponApplicable($this->coupon(), 50.0, '2026-06-15'));
    }

    public function testInactiveCouponDoesNotApply(): void
    {
        $this->assertFalse(isCouponApplicable($this->coupon(['is_active' => 0]), 50.0, '2026-06-15'));
    }

    public function testExpiredCouponDoesNotApply(): void
    {
        $coupon = $this->coupon(['valid_to' => '2026-01-31']);
        $this->assertFalse(isCouponApplicable($coupon, 50.0, '2026-02-01'));
    }

    public function testFutureCouponDoesNotApplyYet(): void
    {
        $coupon = $this->coupon(['valid_from' => '2026-07-01']);
        $this->assertFalse(isCouponApplicable($coupon, 50.0, '2026-06-30'));
    }

    public function testCouponAppliesOnBoundaryDates(): void
    {
        $coupon = $this->coupon(['valid_from' => '2026-06-01', 'valid_to' => '2026-06-30']);
        $this->assertTrue(isCouponApplicable($coupon, 50.0, '2026-06-01'));
        $this->assertTrue(isCouponApplicable($coupon, 50.0, '2026-06-30'));
    }

    public function testMinPurchaseIsEnforced(): void
    {
        $coupon = $this->coupon(['min_purchase' => 100]);
        $this->assertFalse(isCouponApplicable($coupon, 99.99, '2026-06-15'));
        $this->assertTrue(isCouponApplicable($coupon, 100.0, '2026-06-15'));
    }

    public function testUsageCapIsEnforced(): void
    {
        $coupon = $this->coupon(['max_uses' => 5, 'times_used' => 5]);
        $this->assertFalse(isCouponApplicable($coupon, 50.0, '2026-06-15'));

        $coupon['times_used'] = 4;
        $this->assertTrue(isCouponApplicable($coupon, 50.0, '2026-06-15'));
    }

    public function testUnlimitedUsesWhenMaxUsesIsNull(): void
    {
        $coupon = $this->coupon(['max_uses' => null, 'times_used' => 9999]);
        $this->assertTrue(isCouponApplicable($coupon, 50.0, '2026-06-15'));
    }
}
