<?php
// ============================================
// VENDOR CONTROLLER 1: OrderController (Updated)
// File: app/Http/Controllers/Vendor/OrderController.php
// ============================================

namespace App\Http\Controllers\Vendor;

use App\Http\Controllers\Controller;
use App\Models\OrderItem;
use App\Models\Order;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    public function index(Request $request)
    {
        $query = OrderItem::with(['order.user', 'product'])
            ->where('vendor_id', auth()->id());

        // Filter by vendor status
        if ($request->filled('status')) {
            $query->where('vendor_status', $request->status);
        }

        // Filter by order status
        if ($request->filled('order_status')) {
            $query->whereHas('order', function ($q) use ($request) {
                $q->where('status', $request->order_status);
            });
        }

        // Search by order number or customer
        if ($request->filled('search')) {
            $search = $request->search;
            $query->whereHas('order', function ($q) use ($search) {
                $q->where('order_number', 'like', "%{$search}%")
                    ->orWhere('customer_name', 'like', "%{$search}%")
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

        $orderItems = $query->latest()->paginate(20);

        return view('vendor.orders.index', compact('orderItems'));
    }

    public function show(Order $order)
    {
        // Check if vendor has items in this order
        $hasItems = $order->items()->where('vendor_id', auth()->id())->exists();

        if (!$hasItems) {
            abort(403, 'Unauthorized access to this order.');
        }

        $order->load([
            'user',
            'items' => function ($q) {
                $q->where('vendor_id', auth()->id());
            },
            'items.product.primaryImage'
        ]);

        // Calculate vendor's portion of the order
        $vendorTotal = $order->items->sum('total');

        return view('vendor.orders.show', compact('order', 'vendorTotal'));
    }

    public function updateStatus(Request $request, OrderItem $orderItem)
    {
        // Check ownership
        if ($orderItem->vendor_id !== auth()->id()) {
            abort(403, 'Unauthorized access.');
        }

        $request->validate([
            'vendor_status' => 'required|in:pending,processing,shipped,delivered',
            'vendor_notes' => 'nullable|string|max:1000',
        ]);

        $orderItem->update([
            'vendor_status' => $request->vendor_status,
            'vendor_notes' => $request->vendor_notes,
        ]);

        // Check if all items in the order have been delivered by all vendors
        $order = $orderItem->order;
        $allDelivered = $order->items()->where('vendor_status', '!=', 'delivered')->count() === 0;

        if ($allDelivered && $order->status !== 'delivered') {
            $order->update([
                'status' => 'delivered',
                'delivered_at' => now()
            ]);
        }

        return redirect()->back()
            ->with('success', 'Order status updated successfully!');
    }
}
