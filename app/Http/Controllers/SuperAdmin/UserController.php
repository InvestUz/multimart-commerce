<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function index(Request $request)
    {
        $query = User::query();

        // Add search filter
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('phone', 'like', "%{$search}%");
            });
        }

        // Add role filter
        if ($request->filled('role')) {
            $query->where('role', $request->role);
        }

        // Add status filter
        if ($request->filled('status')) {
            $isActive = $request->status === 'active' ? 1 : 0;
            $query->where('is_active', $isActive);
        }

        $users = $query->latest()->paginate(20);

        return view('super-admin.users.index', compact('users'));
    }

    public function show(User $user)
    {
        // Load relationships based on user role
        if ($user->role === 'vendor') {
            $user->load(['products' => function($query) {
                $query->latest()->take(10);
            }]);

            // Calculate vendor statistics
            $totalRevenue = $user->vendorOrderItems()
                ->whereHas('order', function($q) {
                    $q->where('payment_status', 'paid');
                })
                ->sum('total');

            $totalOrders = $user->vendorOrderItems()
                ->distinct('order_id')
                ->count('order_id');

            $stats = compact('totalRevenue', 'totalOrders');
        } elseif ($user->role === 'customer') {
            $user->load(['orders' => function($query) {
                $query->latest()->take(10);
            }]);

            // Calculate customer statistics
            $totalOrders = $user->orders()->count();
            $totalSpent = $user->orders()
                ->where('payment_status', 'paid')
                ->sum('total');

            $stats = compact('totalOrders', 'totalSpent');
        } else {
            $stats = [];
        }

        return view('super-admin.users.show', compact('user', 'stats'));
    }

    public function toggleStatus(User $user)
    {
        // Prevent disabling own account
        if ($user->id === auth()->id()) {
            return back()->with('error', 'You cannot deactivate your own account!');
        }

        // Prevent disabling super admin accounts
        if ($user->role === 'super_admin' && auth()->user()->role !== 'super_admin') {
            return back()->with('error', 'You cannot modify super admin accounts!');
        }

        $user->update([
            'is_active' => !$user->is_active
        ]);

        return back()->with('success', 'User status updated successfully!');
    }

    public function destroy(User $user)
    {
        // Prevent deleting own account
        if ($user->id === auth()->id()) {
            return back()->with('error', 'You cannot delete your own account!');
        }

        // Prevent deleting super admin accounts
        if ($user->role === 'super_admin') {
            return back()->with('error', 'Super admin accounts cannot be deleted!');
        }

        // Check if user has orders (for customers)
        if ($user->role === 'customer' && $user->orders()->exists()) {
            return back()->with('error', 'Cannot delete user with existing orders. Deactivate instead.');
        }

        // Check if vendor has orders
        if ($user->role === 'vendor' && $user->vendorOrderItems()->exists()) {
            return back()->with('error', 'Cannot delete vendor with existing orders. Deactivate instead.');
        }

        // Delete related data
        if ($user->role === 'vendor') {
            $user->products()->delete();
        }

        $user->cart()->delete();
        $user->wishlist()->delete();
        $user->reviews()->delete();

        // Delete user
        $user->delete();

        return redirect()->route('super-admin.users.index')
            ->with('success', 'User deleted successfully!');
    }
}
