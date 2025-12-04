<?php

namespace App\Http\Controllers;

use App\Services\CouponService;
use Illuminate\Http\Request;

class CouponController extends Controller
{
    protected $couponService;

    public function __construct(CouponService $couponService)
    {
        $this->couponService = $couponService;
    }

    /**
     * Validate coupon code via AJAX
     */
    public function validateCoupon(Request $request)
    {
        $request->validate([
            'coupon_code' => 'required|string',
        ]);

        $result = $this->couponService->validateCoupon(
            $request->coupon_code,
            auth()->id()
        );

        if ($result['valid']) {
            // Store coupon code in session for checkout
            session(['applied_coupon' => $request->coupon_code]);
            
            return response()->json([
                'success' => true,
                'message' => $result['message'],
                'discount' => $result['discount'],
                'discount_formatted' => $result['discount_formatted'],
                'tax' => $result['tax'],
                'total' => $result['total'],
            ]);
        }

        return response()->json([
            'success' => false,
            'message' => $result['message'],
        ]);
    }

    /**
     * Remove applied coupon
     */
    public function remove(Request $request)
    {
        session()->forget('applied_coupon');
        
        return response()->json([
            'success' => true,
            'message' => 'Coupon removed successfully.',
        ]);
    }
}
