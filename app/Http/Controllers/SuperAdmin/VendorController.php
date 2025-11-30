<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class VendorController extends Controller
{
    public function create()
    {
        return view('super-admin.vendors.create');
    }

    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'name' => 'required|string|max:255',
                'email' => 'required|email|unique:users,email',
                'store_name' => 'required|string|max:255',
                'phone' => 'nullable|string|max:20',
                'password' => 'required|string|min:8|confirmed',
                'address' => 'nullable|string',
                'is_active' => 'boolean',
            ]);

            $validated['password'] = Hash::make($validated['password']);
            $validated['role'] = 'vendor';
            $validated['email_verified_at'] = now();
            $validated['is_active'] = $request->boolean('is_active', true);

            $vendor = User::create($validated);

            return redirect()->route('super-admin.vendors.index')
                ->with('success', __('Vendor created successfully!'));
        } catch (\Exception $e) {
            return back()->withInput()->with('error', __('Failed to create vendor: ') . $e->getMessage());
        }
    }

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

        return back()->with('success', __('Vendor status updated successfully!'));
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

        return back()->with('success', __('Vendor approved successfully!'));
    }

    public function destroy(User $vendor)
    {
        if ($vendor->role !== 'vendor') {
            abort(404);
        }

        // Check if vendor has any orders
        $hasOrders = $vendor->vendorOrderItems()->exists();

        if ($hasOrders) {
            return back()->with('error', __('Cannot delete vendor with existing orders. Deactivate instead.'));
        }

        // Delete vendor's products
        $vendor->products()->delete();

        // Delete vendor
        $vendor->delete();

        return redirect()->route('super-admin.vendors.index')
            ->with('success', __('Vendor deleted successfully!'));
    }

    public function edit(User $vendor)
    {
        if ($vendor->role !== 'vendor') {
            abort(404);
        }

        return view('super-admin.vendors.edit', compact('vendor'));
    }

    public function update(Request $request, User $vendor)
    {
        if ($vendor->role !== 'vendor') {
            abort(404);
        }

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,'.$vendor->id,
            'store_name' => 'required|string|max:255',
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string',
            'is_active' => 'boolean',
        ]);

        // Only update password if provided
        if ($request->filled('password')) {
            $request->validate([
                'password' => 'string|min:8|confirmed',
            ]);
            $validated['password'] = Hash::make($request->password);
        }

        $validated['is_active'] = $request->boolean('is_active', $vendor->is_active);

        $vendor->update($validated);

        return redirect()->route('super-admin.vendors.show', $vendor)
            ->with('success', __('Vendor updated successfully!'));
    }
}
