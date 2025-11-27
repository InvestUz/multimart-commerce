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

        // Count distinct orders (not order items)
        $totalOrders = $vendor->vendorOrderItems()
            ->distinct('order_id')
            ->count('order_id');

        // Pending orders - check order status, not order_item status
        $pendingOrders = $vendor->vendorOrderItems()
            ->whereHas('order', function($q) {
                $q->where('status', 'pending');
            })
            ->distinct('order_id')
            ->count('order_id');

        // Total revenue from paid orders
        $totalRevenue = $vendor->vendorOrderItems()
            ->whereHas('order', function ($q) {
                $q->where('payment_status', 'paid');
            })
            ->sum('total');

        // Pending earnings - delivered orders without payout
        $pendingEarnings = $vendor->vendorOrderItems()
            ->whereHas('order', function($q) {
                $q->where('status', 'delivered')
                  ->where('payment_status', 'paid');
            })
            ->whereNull('payout_id')
            ->sum('total');

        // Recent Orders
        $recentOrders = $vendor->vendorOrderItems()
            ->with(['order.user', 'product'])
            ->whereHas('order')
            ->whereHas('product')
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

        // Monthly Revenue (last 6 months)
        $monthlyRevenue = $vendor->vendorOrderItems()
            ->whereHas('order', function ($q) {
                $q->where('payment_status', 'paid');
            })
            ->where('order_items.created_at', '>=', now()->subMonths(6))
            ->select(
                DB::raw('DATE_FORMAT(order_items.created_at, "%Y-%m") as month'),
                DB::raw('SUM(order_items.total) as revenue')
            )
            ->groupBy('month')
            ->orderBy('month')
            ->get();

        // Low Stock Products
        $lowStockProducts = $vendor->products()
            ->where('stock', '<', 10)
            ->where('is_active', true)
            ->orderBy('stock', 'asc')
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
