<?php

namespace App\Http\Controllers\Vendor;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ReportController extends Controller
{
    public function sales(Request $request)
    {
        $startDate = $request->input('start_date', now()->startOfMonth());
        $endDate = $request->input('end_date', now());

        $vendor = auth()->user();

        $salesData = $vendor->vendorOrderItems()
            ->whereHas('order', function ($q) {
                $q->where('payment_status', 'paid');
            })
            ->whereBetween('created_at', [$startDate, $endDate])
            ->select(
                DB::raw('DATE(created_at) as date'),
                DB::raw('COUNT(*) as orders'),
                DB::raw('SUM(quantity) as items_sold'),
                DB::raw('SUM(total) as revenue')
            )
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        $totalRevenue = $salesData->sum('revenue');
        $totalOrders = $salesData->sum('orders');
        $totalItemsSold = $salesData->sum('items_sold');

        return view('vendor.reports.sales', compact(
            'salesData',
            'totalRevenue',
            'totalOrders',
            'totalItemsSold',
            'startDate',
            'endDate'
        ));
    }

    public function products(Request $request)
    {
        $vendor = auth()->user();

        $topProducts = $vendor->products()
            ->withCount(['orderItems as total_sold' => function ($query) {
                $query->select(DB::raw('SUM(quantity)'));
            }])
            ->withSum('orderItems as total_revenue', 'total')
            ->orderBy('total_sold', 'desc')
            ->take(20)
            ->get();

        $lowStockProducts = $vendor->products()
            ->where('stock', '<', 10)
            ->where('is_active', true)
            ->get();

        return view('vendor.reports.products', compact('topProducts', 'lowStockProducts'));
    }

    public function orders(Request $request)
    {
        $vendor = auth()->user();

        // Get order items with related data
        $query = $vendor->vendorOrderItems()
            ->with(['order.user', 'product', 'order'])
            ->orderBy('created_at', 'desc');

        // Filter by date range if provided
        if ($request->filled('start_date') && $request->filled('end_date')) {
            $query->whereBetween('created_at', [
                $request->input('start_date'),
                $request->input('end_date')
            ]);
        }

        // Filter by status if provided
        if ($request->filled('status')) {
            $query->where('vendor_status', $request->input('status'));
        }

        $orders = $query->paginate(20);

        // Get status counts for filter
        $statusCounts = $vendor->vendorOrderItems()
            ->select('vendor_status', DB::raw('COUNT(*) as count'))
            ->groupBy('vendor_status')
            ->pluck('count', 'vendor_status');

        return view('vendor.reports.orders', compact('orders', 'statusCounts'));
    }
}