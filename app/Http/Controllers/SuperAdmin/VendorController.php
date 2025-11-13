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
            ->withCount('products')
            ->withSum('vendorOrders as total_revenue', 'total');

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('shop_name', 'like', "%{$search}%");
            });
        }

        if ($request->filled('status')) {
            $query->where('is_active', $request->status === 'active');
        }

        $vendors = $query->latest()->paginate(20);

        return view('super-admin.vendors.index', compact('vendors'));
    }

    public function show(User $vendor)
    {
        if ($vendor->role !== 'vendor') {
            abort(404);
        }

        $vendor->load(['products', 'vendorOrders']);
        $totalRevenue = $vendor->vendorOrders()->sum('total');
        $totalProducts = $vendor->products()->count();
        $totalOrders = $vendor->vendorOrders()->count();

        return view('super-admin.vendors.show', compact('vendor', 'totalRevenue', 'totalProducts', 'totalOrders'));
    }

    public function toggleStatus(User $vendor)
    {
        if ($vendor->role !== 'vendor') {
            return response()->json(['success' => false, 'message' => 'Invalid vendor'], 400);
        }

        $vendor->update(['is_active' => !$vendor->is_active]);

        return response()->json([
            'success' => true,
            'message' => 'Vendor status updated successfully!',
            'is_active' => $vendor->is_active
        ]);
    }

    public function approve(User $vendor)
    {
        if ($vendor->role !== 'vendor') {
            return response()->json(['success' => false, 'message' => 'Invalid vendor'], 400);
        }

        $vendor->update(['is_approved' => true, 'is_active' => true]);

        return response()->json([
            'success' => true,
            'message' => 'Vendor approved successfully!'
        ]);
    }

    public function destroy(User $vendor)
    {
        if ($vendor->role !== 'vendor') {
            abort(404);
        }

        if ($vendor->products()->count() > 0) {
            return back()->with('error', 'Cannot delete vendor with existing products.');
        }

        $vendor->delete();

        return redirect()->route('super-admin.vendors.index')
            ->with('success', 'Vendor deleted successfully!');
    }
}
