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
            ->withCount(['products', 'orderItems']);

        // Search
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%")
                    ->orWhere('store_name', 'like', "%{$search}%")
                    ->orWhere('phone', 'like', "%{$search}%");
            });
        }

        // Filter by status
        if ($request->filled('status')) {
            if ($request->status === 'active') {
                $query->where('is_active', true);
            } elseif ($request->status === 'inactive') {
                $query->where('is_active', false);
            }
        }

        // Sort
        $sortBy = $request->input('sort_by', 'latest');
        switch ($sortBy) {
            case 'name':
                $query->orderBy('name', 'asc');
                break;
            case 'products':
                $query->orderBy('products_count', 'desc');
                break;
            case 'orders':
                $query->orderBy('order_items_count', 'desc');
                break;
            default:
                $query->latest();
        }

        $vendors = $query->paginate(20)->withQueryString();

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

        $recentOrders = $vendor->orderItems()
            ->with(['order', 'product'])
            ->latest()
            ->take(10)
            ->get();

        return view('super-admin.vendors.show', compact(
            'vendor',
            'totalProducts',
            'activeProducts',
            'totalOrders',
            'totalRevenue',
            'recentProducts',
            'recentOrders'
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

        // Check if vendor has orders
        if ($vendor->orderItems()->count() > 0) {
            return redirect()->back()
                ->with('error', 'Cannot delete vendor with existing orders! Please deactivate the vendor instead.');
        }

        $vendorName = $vendor->name;

        // This will also delete all vendor's products due to cascade
        $vendor->delete();

        return redirect()->route('super-admin.vendors.index')
            ->with('success', "Vendor '{$vendorName}' and all their products deleted successfully!");
    }
}
