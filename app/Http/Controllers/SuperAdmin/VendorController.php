<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;

class VendorController extends Controller
{
    public function index(Request $request)
    {
        $query = User::where('role', 'vendor')
            ->withCount('products');

        // Add search filter
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('store_name', 'like', "%{$search}%");
            });
        }

        // Add status filter
        if ($request->filled('status')) {
            $isActive = $request->status === 'active' ? 1 : 0;
            $query->where('is_active', $isActive);
        }

        $vendors = $query->paginate(12);

        // Calculate total revenue for each vendor
        foreach ($vendors as $vendor) {
            $vendor->total_revenue = $vendor->vendorOrderItems()
                ->whereHas('order', function($q) {
                    $q->where('payment_status', 'paid');
                })
                ->sum('total');
        }

        return view('super-admin.vendors.index', compact('vendors'));
    }

    public function show(User $vendor)
    {
        if ($vendor->role !== 'vendor') {
            abort(404);
        }

        $vendor->load(['products' => function($query) {
            $query->withCount('orderItems')->latest()->take(10);
        }]);

        // Calculate statistics
        $totalRevenue = $vendor->vendorOrderItems()
            ->whereHas('order', function($q) {
                $q->where('payment_status', 'paid');
            })
            ->sum('total');

        $totalOrders = $vendor->vendorOrderItems()
            ->distinct('order_id')
            ->count('order_id');

        $pendingOrders = $vendor->vendorOrderItems()
            ->whereHas('order', function($q) {
                $q->where('status', 'pending');
            })
            ->distinct('order_id')
            ->count('order_id');

        return view('super-admin.vendors.show', compact('vendor', 'totalRevenue', 'totalOrders', 'pendingOrders'));
    }

    public function toggleStatus(User $vendor)
    {
        if ($vendor->role !== 'vendor') {
            abort(404);
        }

        $vendor->update([
            'is_active' => !$vendor->is_active
        ]);

        return back()->with('success', 'Vendor status updated successfully!');
    }

    public function approve(User $vendor)
    {
        if ($vendor->role !== 'vendor') {
            abort(404);
        }

        $vendor->update([
            'is_active' => true,
            'email_verified_at' => now()
        ]);

        return back()->with('success', 'Vendor approved successfully!');
    }

    public function destroy(User $vendor)
    {
        if ($vendor->role !== 'vendor') {
            abort(404);
        }

        // Check if vendor has any orders
        $hasOrders = $vendor->vendorOrderItems()->exists();

        if ($hasOrders) {
            return back()->with('error', 'Cannot delete vendor with existing orders. Deactivate instead.');
        }

        // Delete vendor's products
        $vendor->products()->delete();

        // Delete vendor
        $vendor->delete();

        return redirect()->route('super-admin.vendors.index')
            ->with('success', 'Vendor deleted successfully!');
    }
}
