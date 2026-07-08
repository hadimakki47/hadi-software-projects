<?php
/**
 * Pure business logic for pricing and coupons.
 *
 * These functions have no database or session dependencies, which makes
 * them directly unit-testable (see tests/PricingTest.php).
 */

/**
 * Price multiplier for a seat category.
 */
function seatCategoryMultiplier(string $category): float {
    switch ($category) {
        case 'Premium':
            return 1.5;
        case 'Economy':
            return 0.8;
        default:
            return 1.0;
    }
}

/**
 * Total price for a set of seats given the showtime's base price.
 *
 * @param float    $base_price Base ticket price of the showtime
 * @param string[] $categories One category per selected seat
 */
function calculateSeatsTotal(float $base_price, array $categories): float {
    $total = 0.0;
    foreach ($categories as $category) {
        $total += $base_price * seatCategoryMultiplier($category);
    }
    return $total;
}

/**
 * Discount value a coupon yields on a given amount.
 *
 * Percentage coupons are capped at max_discount (when set);
 * fixed coupons never exceed the total amount.
 *
 * @param array $coupon Row from the coupons table
 */
function calculateDiscount(array $coupon, float $total_amount): float {
    if ($coupon['discount_type'] == 'percentage') {
        $discount = $total_amount * ($coupon['discount_amount'] / 100);

        if (!is_null($coupon['max_discount']) && $discount > $coupon['max_discount']) {
            $discount = (float)$coupon['max_discount'];
        }
    } else {
        $discount = (float)$coupon['discount_amount'];

        if ($discount > $total_amount) {
            $discount = $total_amount;
        }
    }

    return $discount;
}

/**
 * Whether a coupon can be applied to a purchase.
 *
 * Checks active flag, validity window, minimum purchase, and usage cap.
 *
 * @param array       $coupon  Row from the coupons table
 * @param float       $total   Purchase amount before discount
 * @param string|null $on_date Date to evaluate against (Y-m-d), defaults to today
 */
function isCouponApplicable(array $coupon, float $total, ?string $on_date = null): bool {
    $on_date = $on_date ?? date('Y-m-d');

    if (empty($coupon['is_active'])) {
        return false;
    }
    if ($coupon['valid_from'] > $on_date || $coupon['valid_to'] < $on_date) {
        return false;
    }
    if ((float)$coupon['min_purchase'] > $total) {
        return false;
    }
    if (!is_null($coupon['max_uses']) && (int)$coupon['times_used'] >= (int)$coupon['max_uses']) {
        return false;
    }

    return true;
}
