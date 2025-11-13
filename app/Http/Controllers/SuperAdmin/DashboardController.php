<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use App\Models\Vendor;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        // Summary Statistics
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

        // Top Selling Products
        $topProducts = Product::withCount(['orderItems as total_sold' => function ($query) {
                $query->select(DB::raw('SUM(quantity)'));
            }])
            ->orderBy('total_sold', 'desc')
            ->take(10)
            ->get();

        // Monthly Revenue Chart Data
        $monthlyRevenue = Order::where('payment_status', 'paid')
            ->where('created_at', '>=', now()->subMonths(12))
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

        // Top Vendors
        $topVendors = User::where('role', 'vendor')
            ->withCount('products')
            ->withSum('vendorOrders as total_revenue', 'total')
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
