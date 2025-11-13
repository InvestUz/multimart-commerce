<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Product;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        // Total Statistics
        $totalRevenue = Order::where('payment_status', 'paid')->sum('total');
        $totalOrders = Order::count();
        $totalProducts = Product::count();
        $totalVendors = User::where('role', 'vendor')->count();
        $totalCustomers = User::where('role', 'customer')->count();
        $pendingOrders = Order::where('status', 'pending')->count();

        // Recent Orders
        $recentOrders = Order::with(['user', 'items'])
            ->latest()
            ->take(10)
            ->get();

        // Top Products
        $topProducts = Product::withCount('orderItems')
            ->with(['vendor', 'images'])
            ->orderBy('order_items_count', 'desc')
            ->take(10)
            ->get();

        // Monthly Revenue
        $monthlyRevenue = Order::where('payment_status', 'paid')
            ->where('created_at', '>=', now()->subYear())
            ->select(
                DB::raw('DATE_FORMAT(created_at, "%Y-%m") as month'),
                DB::raw('SUM(total) as revenue')
            )
            ->groupBy('month')
            ->orderBy('month')
            ->get();

        // Order Status Distribution
        $orderStatusDistribution = Order::select('status', DB::raw('count(*) as count'))
            ->groupBy('status')
            ->get();

        // Top Vendors - Fixed Query
        // Calculate total revenue from vendor's order items
        $topVendors = User::where('role', 'vendor')
            ->withCount('products')
            ->addSelect([
                'total_revenue' => OrderItem::selectRaw('COALESCE(SUM(total), 0)')
                    ->whereColumn('vendor_id', 'users.id')
                    ->whereHas('order', function($query) {
                        $query->where('payment_status', 'paid');
                    })
            ])
            ->orderBy('total_revenue', 'desc')
            ->take(5)
            ->get();

        return view('super-admin.dashboard', compact(
            'totalRevenue',
            'totalOrders',
            'totalProducts',
            'totalVendors',
            'totalCustomers',
            'pendingOrders',
            'recentOrders',
            'topProducts',
            'monthlyRevenue',
            'orderStatusDistribution',
            'topVendors'
        ));
    }
}
