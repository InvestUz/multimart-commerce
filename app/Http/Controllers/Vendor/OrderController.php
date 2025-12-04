<?php

namespace App\Http\Controllers\Vendor;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\OrderItem;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    public function index(Request $request)
    {
        $query = OrderItem::where('vendor_id', auth()->id())
            ->with(['order.user', 'product.images']);

        if ($request->filled('status')) {
            $query->where('vendor_status', $request->status);
        }

        $orders = $query->latest()->paginate(20);

        return view('vendor.orders.index', compact('orders'));
    }

    public function show(Order $order)
    {
        // Verify vendor has items in this order
        $hasItems = $order->items()
            ->where('vendor_id', auth()->id())
            ->exists();

        if (!$hasItems) {
            abort(403, 'Unauthorized access');
        }

        $order->load([
            'user',
            'items' => function ($query) {
                $query->where('vendor_id', auth()->id())
                    ->with('product.images');
            },
            'shippingAddress',
            'billingAddress'
        ]);

        return view('vendor.orders.show', compact('order'));
    }

    public function updateStatus(Request $request, Order $order)
    {
        // Verify vendor has items in this order
        $hasItems = $order->items()
            ->where('vendor_id', auth()->id())
            ->exists();

        if (!$hasItems) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized access'
            ], 403);
        }

        $validated = $request->validate([
            'status' => 'required|in:pending,processing,shipped,delivered,cancelled'
        ]);

        // Update vendor_status for all items belonging to this vendor in this order
        $order->items()
            ->where('vendor_id', auth()->id())
            ->update(['vendor_status' => $validated['status']]);

        return response()->json([
            'success' => true,
            'message' => 'Order status updated successfully!'
        ]);
    }

    public function ship(Request $request, Order $order)
    {
        // Verify vendor has items in this order
        $hasItems = $order->items()
            ->where('vendor_id', auth()->id())
            ->exists();

        if (!$hasItems) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized access'
            ], 403);
        }

        $validated = $request->validate([
            'tracking_number' => 'required|string|max:255',
            'carrier' => 'required|string|max:255',
        ]);

        $orderItems = $order->items()
            ->where('vendor_id', auth()->id())
            ->get();

        if ($orderItems->isEmpty()) {
            return response()->json([
                'success' => false,
                'message' => 'No items found for this vendor.'
            ], 400);
        }

        foreach ($orderItems as $item) {
            $item->update([
                'vendor_status' => 'shipped',
                'tracking_number' => $validated['tracking_number'],
                'carrier' => $validated['carrier'],
            ]);
        }

        return response()->json([
            'success' => true,
            'message' => 'Order marked as shipped!'
        ]);
    }
}
