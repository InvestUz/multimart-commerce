<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;

class VendorController extends Controller
{
    public function index()
    {
        $vendors = User::where('role', 'vendor')
            ->withCount(['products', 'orderItems'])
            ->withSum('orderItems', 'total')
            ->latest()
            ->paginate(20);

        return view('super-admin.vendors.index', compact('vendors'));
    }

    public function show(User $vendor)
    {
        if ($vendor->role !== 'vendor') {
            abort(404, 'Vendor not found.');
        }

        $vendor->load(['products.primaryImage', 'products.category']);
        
        $totalProducts = $vendor->products()->count();
        $activeProducts = $vendor->products()->where('is_active', true)->count();
        $totalOrders = $vendor->orderItems()->distinct('order_id')->count('order_id');
        $totalRevenue = $vendor->orderItems()
            ->whereHas('order', function ($q) {
                $q->where('status', 'delivered');
            })
            ->sum('total');

        $recentProducts = $vendor->products()
            ->with(['primaryImage', 'category'])
            ->latest()
            ->take(10)
            ->get();

        return view('super-admin.vendors.show', compact(
            'vendor',
            'totalProducts',
            'activeProducts',
            'totalOrders',
            'totalRevenue',
            'recentProducts'
        ));
    }

    public function toggleStatus(User $vendor)
    {
        if ($vendor->role !== 'vendor') {
            abort(404, 'Vendor not found.');
        }

        $vendor->update(['is_active' => !$vendor->is_active]);

        $status = $vendor->is_active ? 'activated' : 'deactivated';

        return redirect()->back()
            ->with('success', "Vendor {$status} successfully!");
    }

    public function destroy(User $vendor)
    {
        if ($vendor->role !== 'vendor') {
            abort(404, 'Vendor not found.');
        }

        // This will also delete all vendor's products due to cascade
        $vendor->delete();

        return redirect()->route('super-admin.vendors.index')
            ->with('success', 'Vendor and all their products deleted successfully!');
    }
}
