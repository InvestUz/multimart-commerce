<?php
// ============================================
// SUPER ADMIN CONTROLLER 1: DashboardController
// File: app/Http/Controllers/SuperAdmin/DashboardController.php
// ============================================

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Product;
use App\Models\Order;
use App\Models\Category;
use App\Models\Review;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        // Statistics
        $totalVendors = User::where('role', 'vendor')->count();
        $activeVendors = User::where('role', 'vendor')->where('is_active', true)->count();
        $totalCustomers = User::where('role', 'customer')->count();
        $totalProducts = Product::count();
        $activeProducts = Product::where('is_active', true)->count();
        $totalOrders = Order::count();
        $totalRevenue = Order::where('status', 'delivered')->sum('total');
        $pendingOrders = Order::where('status', 'pending')->count();
        $pendingReviews = Review::where('is_approved', false)->count();

        // Recent orders
        $recentOrders = Order::with(['user', 'items'])
            ->latest()
            ->take(10)
            ->get();

        // Top products by views
        $topProducts = Product::with(['primaryImage', 'category', 'user'])
            ->orderBy('views', 'desc')
            ->take(10)
            ->get();

        // Top products by sales
        $topSellingProducts = Product::with(['primaryImage', 'category', 'user'])
            ->orderBy('total_sales', 'desc')
            ->take(10)
            ->get();

        // Monthly revenue chart data (last 12 months)
        $monthlyRevenue = Order::select(
                DB::raw('DATE_FORMAT(created_at, "%Y-%m") as month'),
                DB::raw('SUM(total) as revenue'),
                DB::raw('COUNT(*) as orders')
            )
            ->where('status', 'delivered')
            ->where('created_at', '>=', now()->subMonths(12))
            ->groupBy('month')
            ->orderBy('month')
            ->get();

        // Orders by status
        $ordersByStatus = Order::select('status', DB::raw('count(*) as count'))
            ->groupBy('status')
            ->get()
            ->pluck('count', 'status');

        return view('super-admin.dashboard', compact(
            'totalVendors',
            'activeVendors',
            'totalCustomers',
            'totalProducts',
            'activeProducts',
            'totalOrders',
            'totalRevenue',
            'pendingOrders',
            'pendingReviews',
            'recentOrders',
            'topProducts',
            'topSellingProducts',
            'monthlyRevenue',
            'ordersByStatus'
        ));
    }
}

