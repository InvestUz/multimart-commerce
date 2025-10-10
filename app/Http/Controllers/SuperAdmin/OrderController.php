<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    public function index(Request $request)
    {
        $query = Order::with(['user', 'items']);

        // Filter by status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Filter by payment status
        if ($request->filled('payment_status')) {
            $query->where('payment_status', $request->payment_status);
        }

        // Filter by payment method
        if ($request->filled('payment_method')) {
            $query->where('payment_method', $request->payment_method);
        }

        // Search by order number or customer
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('order_number', 'like', "%{$search}%")
                    ->orWhere('customer_name', 'like', "%{$search}%")
                    ->orWhere('customer_email', 'like', "%{$search}%")
                    ->orWhere('customer_phone', 'like', "%{$search}%");
            });
        }

        // Date range filter
        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        // Sort
        $sortBy = $request->input('sort_by', 'latest');
        switch ($sortBy) {
            case 'oldest':
                $query->oldest();
                break;
            case 'total_high':
                $query->orderBy('total', 'desc');
                break;
            case 'total_low':
                $query->orderBy('total', 'asc');
                break;
            default:
                $query->latest();
        }

        $orders = $query->paginate(20)->withQueryString();

        // Statistics for filters
        $stats = [
            'total' => Order::count(),
            'pending' => Order::where('status', 'pending')->count(),
            'processing' => Order::where('status', 'processing')->count(),
            'shipped' => Order::where('status', 'shipped')->count(),
            'delivered' => Order::where('status', 'delivered')->count(),
            'cancelled' => Order::where('status', 'cancelled')->count(),
            'total_revenue' => Order::where('status', 'delivered')->sum('total'),
        ];

        return view('super-admin.orders.index', compact('orders', 'stats'));
    }

    public function show(Order $order)
    {
        $order->load(['user', 'items.product.primaryImage', 'items.vendor']);

        return view('super-admin.orders.show', compact('order'));
    }

    public function updateStatus(Request $request, Order $order)
    {
        $request->validate([
            'status' => 'required|in:pending,processing,shipped,delivered,cancelled,returned',
            'admin_notes' => 'nullable|string|max:1000',
        ]);

        $oldStatus = $order->status;
        $newStatus = $request->status;

        $order->update([
            'status' => $newStatus,
            'admin_notes' => $request->admin_notes,
        ]);

        // Update timestamps based on status
        if ($newStatus === 'shipped' && !$order->shipped_at) {
            $order->update(['shipped_at' => now()]);
        } elseif ($newStatus === 'delivered' && !$order->delivered_at) {
            $order->update([
                'delivered_at' => now(),
                'payment_status' => 'paid', // Auto mark as paid on delivery for COD
            ]);

            // Update all vendor order items to delivered
            $order->items()->update(['vendor_status' => 'delivered']);
        }

        // If cancelled, restore stock
        if ($newStatus === 'cancelled' && $oldStatus !== 'cancelled') {
            foreach ($order->items as $item) {
                if ($item->product) {
                    $item->product->increment('stock', $item->quantity);
                    $item->product->decrement('total_sales', $item->quantity);
                }
            }
        }

        // If uncancelled (changed from cancelled to other status), reduce stock again
        if ($oldStatus === 'cancelled' && $newStatus !== 'cancelled') {
            foreach ($order->items as $item) {
                if ($item->product) {
                    $item->product->decrement('stock', $item->quantity);
                    $item->product->increment('total_sales', $item->quantity);
                }
            }
        }

        return redirect()->back()
            ->with('success', 'Order status updated successfully from ' . ucfirst($oldStatus) . ' to ' . ucfirst($newStatus) . '!');
    }

    public function updatePaymentStatus(Request $request, Order $order)
    {
        $request->validate([
            'payment_status' => 'required|in:pending,paid,failed,refunded',
        ]);

        $data = ['payment_status' => $request->payment_status];

        if ($request->payment_status === 'paid' && !$order->paid_at) {
            $data['paid_at'] = now();
        }

        $order->update($data);

        return redirect()->back()
            ->with('success', 'Payment status updated successfully to ' . ucfirst($request->payment_status) . '!');
    }

    public function destroy(Order $order)
    {
        // Check if order can be deleted (only cancelled or very old delivered orders)
        if (!in_array($order->status, ['cancelled', 'delivered'])) {
            return redirect()->back()
                ->with('error', 'Only cancelled or delivered orders can be deleted!');
        }

        // If order is delivered, check if it's old enough (e.g., 90 days)
        if ($order->status === 'delivered' && $order->delivered_at && $order->delivered_at->diffInDays(now()) < 90) {
            return redirect()->back()
                ->with('error', 'Delivered orders can only be deleted after 90 days!');
        }

        // Restore stock if cancelling
        if ($order->status === 'cancelled') {
            foreach ($order->items as $item) {
                if ($item->product) {
                    $item->product->increment('stock', $item->quantity);
                }
            }
        }

        $orderNumber = $order->order_number;
        $order->delete();

        return redirect()->route('super-admin.orders.index')
            ->with('success', "Order #{$orderNumber} deleted successfully!");
    }

    public function bulkUpdateStatus(Request $request)
    {
        $request->validate([
            'order_ids' => 'required|array',
            'order_ids.*' => 'exists:orders,id',
            'status' => 'required|in:pending,processing,shipped,delivered,cancelled',
        ]);

        $updated = 0;
        foreach ($request->order_ids as $orderId) {
            $order = Order::find($orderId);
            if ($order) {
                $order->update(['status' => $request->status]);
                $updated++;

                // Handle timestamps
                if ($request->status === 'shipped' && !$order->shipped_at) {
                    $order->update(['shipped_at' => now()]);
                } elseif ($request->status === 'delivered' && !$order->delivered_at) {
                    $order->update(['delivered_at' => now()]);
                }
            }
        }

        return redirect()->back()
            ->with('success', "{$updated} orders updated to " . ucfirst($request->status) . " successfully!");
    }

    public function export(Request $request)
    {
        // This method would export orders to CSV/Excel
        // Implementation depends on your export library (e.g., Laravel Excel)

        $query = Order::with(['user', 'items']);

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        $orders = $query->get();

        // For now, return JSON (you can implement CSV export)
        return response()->json($orders);
    }

    public function statistics()
    {
        $totalOrders = Order::count();
        $totalRevenue = Order::where('status', 'delivered')->sum('total');

        $ordersByStatus = Order::selectRaw('status, count(*) as count')
            ->groupBy('status')
            ->pluck('count', 'status');

        $ordersByPaymentMethod = Order::selectRaw('payment_method, count(*) as count')
            ->groupBy('payment_method')
            ->pluck('count', 'payment_method');

        $monthlyRevenue = Order::selectRaw('DATE_FORMAT(created_at, "%Y-%m") as month, SUM(total) as revenue')
            ->where('status', 'delivered')
            ->where('created_at', '>=', now()->subMonths(12))
            ->groupBy('month')
            ->orderBy('month')
            ->get();

        $topCustomers = Order::selectRaw('user_id, customer_name, count(*) as order_count, sum(total) as total_spent')
            ->where('status', 'delivered')
            ->groupBy('user_id', 'customer_name')
            ->orderBy('total_spent', 'desc')
            ->take(10)
            ->get();

        return view('super-admin.orders.statistics', compact(
            'totalOrders',
            'totalRevenue',
            'ordersByStatus',
            'ordersByPaymentMethod',
            'monthlyRevenue',
            'topCustomers'
        ));
    }
}
