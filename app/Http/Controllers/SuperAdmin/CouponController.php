<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\Coupon;
use Illuminate\Http\Request;

class CouponController extends Controller
{
    public function index()
    {
        $coupons = Coupon::withCount('orders')->latest()->paginate(20);
        return view('super-admin.coupons.index', compact('coupons'));
    }

    public function create()
    {
        return view('super-admin.coupons.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'code' => 'required|string|max:50|unique:coupons,code',
            'type' => 'required|in:percentage,fixed',
            'value' => 'required|numeric|min:0',
            'min_purchase' => 'nullable|numeric|min:0',
            'max_discount' => 'nullable|numeric|min:0',
            'usage_limit' => 'nullable|integer|min:1',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after:start_date',
            'is_active' => 'boolean',
        ]);

        $validated['code'] = strtoupper($validated['code']);

        Coupon::create($validated);

        return redirect()->route('super-admin.coupons.index')
            ->with('success', 'Coupon created successfully!');
    }

    public function show(Coupon $coupon)
    {
        $coupon->load('orders');
        return view('super-admin.coupons.show', compact('coupon'));
    }

    public function edit(Coupon $coupon)
    {
        return view('super-admin.coupons.edit', compact('coupon'));
    }

    public function update(Request $request, Coupon $coupon)
    {
        $validated = $request->validate([
            'code' => 'required|string|max:50|unique:coupons,code,' . $coupon->id,
            'type' => 'required|in:percentage,fixed',
            'value' => 'required|numeric|min:0',
            'min_purchase' => 'nullable|numeric|min:0',
            'max_discount' => 'nullable|numeric|min:0',
            'usage_limit' => 'nullable|integer|min:1',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after:start_date',
            'is_active' => 'boolean',
        ]);

        $validated['code'] = strtoupper($validated['code']);

        $coupon->update($validated);

        return redirect()->route('super-admin.coupons.index')
            ->with('success', 'Coupon updated successfully!');
    }

    public function destroy(Coupon $coupon)
    {
        $coupon->delete();

        return redirect()->route('super-admin.coupons.index')
            ->with('success', 'Coupon deleted successfully!');
    }
}
