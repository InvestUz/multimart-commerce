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
        $query = auth()->user()->vendorOrderItems()
            ->with(['order.user', 'product']);

        if ($request->filled('status')) {
            $query->where('status', $request->status);
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

    public function updateStatus(Request $request, OrderItem $orderItem)
    {
        if ($orderItem->vendor_id !== auth()->id()) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized'
            ], 403);
        }

        $validated = $request->validate([
            'status' => 'required|in:pending,processing,shipped,delivered,cancelled'
        ]);

        $orderItem->update(['status' => $validated['status']]);

        return response()->json([
            'success' => true,
            'message' => 'Order status updated successfully!'
        ]);
    }

    public function ship(Request $request, Order $order)
    {
        $validated = $request->validate([
            'tracking_number' => 'required|string|max:255',
            'carrier' => 'required|string|max:255',
        ]);

        $orderItems = $order->items()
            ->where('vendor_id', auth()->id())
            ->get();

        if ($orderItems->isEmpty()) {
            return back()->with('error', 'No items found for this vendor.');
        }

        foreach ($orderItems as $item) {
            $item->update([
                'status' => 'shipped',
                'tracking_number' => $validated['tracking_number'],
                'carrier' => $validated['carrier'],
            ]);
        }

        return back()->with('success', 'Order marked as shipped!');
    }
}
