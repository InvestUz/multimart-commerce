<?php
// app/Http/Controllers/Vendor/DashboardController.php

namespace App\Http\Controllers\Vendor;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\OrderItem;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {

        $vendorId = auth()->id();

        $totalProducts = Product::where('user_id', $vendorId)->count();
        $activeProducts = Product::where('user_id', $vendorId)->where('is_active', true)->count();
        $totalOrders = OrderItem::where('vendor_id', $vendorId)->distinct('order_id')->count();
        $outOfStock = Product::where('user_id', $vendorId)->where('stock', '<=', 0)->count();
        $lowStockProducts = Product::where('user_id', $vendorId)->where('stock', '<=', 5)->count();
        $pendingOrders = OrderItem::where('vendor_id', $vendorId)
            ->whereHas('order', function ($q) {
                $q->where('status', 'pending');
            })
            ->count();
        $totalRevenue = OrderItem::where('vendor_id', $vendorId)
            ->whereHas('order', function ($q) {
                $q->where('status', 'delivered');
            })
            ->sum('total');

        $recentOrders = OrderItem::with(['order.user', 'product'])
            ->where('vendor_id', $vendorId)
            ->latest()
            ->take(10)
            ->get();

        $topProducts = Product::where('user_id', $vendorId)
            ->withCount(['carts', 'wishlists'])
            ->orderBy('views', 'desc')
            ->take(5)
            ->get();

        return view('vendor.dashboard', compact(
            'totalProducts',
            'activeProducts',
            'totalOrders',
            'totalRevenue',
            'recentOrders',
            'topProducts',
            'pendingOrders',
            'outOfStock',
            'lowStockProducts'
        ));
    }
}