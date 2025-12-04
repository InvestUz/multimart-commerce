<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Models\Coupon;
use App\Models\Cart;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

// Coupon validation endpoint
Route::middleware('auth:sanctum')->post('/validate-coupon', function (Request $request) {
    $request->validate([
        'coupon_code' => 'required|string',
    ]);

    $coupon = Coupon::where('code', $request->coupon_code)
        ->where('is_active', true)
        ->first();

    if (!$coupon) {
        return response()->json([
            'valid' => false,
            'message' => 'Invalid coupon code.',
        ]);
    }

    // Calculate cart subtotal
    $cartItems = Cart::where('user_id', auth()->id())->with('product')->get();
    $subtotal = $cartItems->sum(function ($item) {
        return $item->price * $item->quantity;
    });

    // Validate coupon
    if (!$coupon->isValid($subtotal)) {
        $message = 'Coupon is not valid.';
        
        if ($coupon->starts_at && $coupon->starts_at->isFuture()) {
            $message = 'Coupon is not yet active.';
        } elseif ($coupon->expires_at && $coupon->expires_at->isPast()) {
            $message = 'Coupon has expired.';
        } elseif ($coupon->max_uses && $coupon->used_count >= $coupon->max_uses) {
            $message = 'Coupon usage limit has been reached.';
        } elseif ($subtotal < $coupon->min_purchase) {
            $message = "Minimum purchase amount of $" . number_format($coupon->min_purchase, 2) . " required.";
        }

        return response()->json([
            'valid' => false,
            'message' => $message,
        ]);
    }

    // Calculate discount
    $discount = $coupon->calculateDiscount($subtotal);

    return response()->json([
        'valid' => true,
        'message' => 'Coupon applied successfully!',
        'discount' => $discount,
        'discount_formatted' => '$' . number_format($discount, 2),
        'type' => $coupon->type,
        'value' => $coupon->value,
    ]);
});
