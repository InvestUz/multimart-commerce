<?php

namespace App\Http\Controllers\Vendor;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        $vendor = auth()->user();

        // Summary Statistics
        $totalProducts = $vendor->products()->count();
        $activeProducts = $vendor->products()->where('is_active', true)->count();
        $totalOrders = $vendor->vendorOrderItems()->count();
        $pendingOrders = $vendor->vendorOrderItems()->where('status', 'pending')->count();

        $totalRevenue = $vendor->vendorOrderItems()
            ->whereHas('order', function ($q) {
                $q->where('payment_status', 'paid');
            })
            ->sum('total');

        $pendingEarnings = $vendor->vendorOrderItems()
            ->where('status', 'delivered')
            ->whereNull('payout_id')
            ->sum('total');

        // Recent Orders
        $recentOrders = $vendor->vendorOrderItems()
            ->with(['order.user', 'product'])
            ->latest()
            ->take(10)
            ->get();

        // Top Selling Products
        $topProducts = $vendor->products()
            ->withCount(['orderItems as total_sold' => function ($query) {
                $query->select(DB::raw('SUM(quantity)'));
            }])
            ->orderBy('total_sold', 'desc')
            ->take(5)
            ->get();

        // Monthly Revenue
        $monthlyRevenue = $vendor->vendorOrderItems()
            ->whereHas('order', function ($q) {
                $q->where('payment_status', 'paid');
            })
            ->where('created_at', '>=', now()->subMonths(6))
            ->select(
                DB::raw('DATE_FORMAT(created_at, "%Y-%m") as month'),
                DB::raw('SUM(total) as revenue')
            )
            ->groupBy('month')
            ->orderBy('month')
            ->get();

        // Low Stock Products
        $lowStockProducts = $vendor->products()
            ->where('stock', '<', 10)
            ->where('is_active', true)
            ->take(5)
            ->get();

        return view('vendor.dashboard', compact(
            'totalProducts',
            'activeProducts',
            'totalOrders',
            'pendingOrders',
            'totalRevenue',
            'pendingEarnings',
            'recentOrders',
            'topProducts',
            'monthlyRevenue',
            'lowStockProducts'
        ));
    }
}
