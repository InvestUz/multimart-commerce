<?php

namespace App\Services;

use App\Models\Coupon;
use App\Models\Cart;

class CouponService
{
    /**
     * Validate and apply coupon
     */
    public function validateCoupon(string $couponCode, int $userId)
    {
        $coupon = Coupon::where('code', $couponCode)
            ->where('is_active', true)
            ->first();

        if (!$coupon) {
            return [
                'valid' => false,
                'message' => 'Invalid coupon code.',
            ];
        }

        // Calculate cart subtotal
        $cartItems = Cart::where('user_id', $userId)->with('product')->get();
        $subtotal = $cartItems->sum(function ($item) {
            return $item->price * $item->quantity;
        });

        // Validate coupon
        if (!$coupon->isValid($subtotal)) {
            $message = $this->getInvalidReason($coupon, $subtotal);

            return [
                'valid' => false,
                'message' => $message,
            ];
        }

        // Calculate discount
        $discount = $coupon->calculateDiscount($subtotal);
        $shippingCost = $this->calculateShipping($subtotal);
        $tax = ($subtotal - $discount) * 0.1; // 10% tax
        $total = $subtotal - $discount + $shippingCost + $tax;

        return [
            'valid' => true,
            'message' => 'Coupon applied successfully!',
            'coupon_id' => $coupon->id,
            'discount' => $discount,
            'discount_formatted' => '$' . number_format($discount, 2),
            'type' => $coupon->type,
            'value' => $coupon->value,
            'subtotal' => $subtotal,
            'tax' => $tax,
            'shipping' => $shippingCost,
            'total' => $total,
        ];
    }

    /**
     * Get reason why coupon is invalid
     */
    protected function getInvalidReason(Coupon $coupon, float $subtotal): string
    {
        if ($coupon->starts_at && $coupon->starts_at->isFuture()) {
            return 'Coupon is not yet active.';
        } elseif ($coupon->expires_at && $coupon->expires_at->isPast()) {
            return 'Coupon has expired.';
        } elseif ($coupon->max_uses && $coupon->used_count >= $coupon->max_uses) {
            return 'Coupon usage limit has been reached.';
        } elseif ($subtotal < $coupon->min_purchase) {
            return "Minimum purchase amount of $" . number_format($coupon->min_purchase, 2) . " required.";
        }

        return 'Coupon is not valid.';
    }

    /**
     * Calculate shipping cost
     */
    protected function calculateShipping(float $subtotal): float
    {
        // Free shipping for orders over $100
        return $subtotal >= 100 ? 0 : 10;
    }
}
