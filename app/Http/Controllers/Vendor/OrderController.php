<?php

namespace App\Http\Controllers\Vendor;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\OrderItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

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
        // DEBUG LOG
        Log::info('Vendor Order Status Update Request:', [
            'order_id' => $order->id,
            'order_number' => $order->order_number,
            'vendor_id' => auth()->id(),
            'requested_status' => $request->input('status'),
            'vendor_name' => auth()->user()->name
        ]);

        try {
            // Authorize using policy
            $this->authorize('updateStatus', $order);

            $validated = $request->validate([
                'status' => 'required|in:pending,processing,shipped,delivered,cancelled'
            ]);

            // Update vendor_status for all items belonging to this vendor in this order
            $updated = $order->items()
                ->where('vendor_id', auth()->id())
                ->update(['vendor_status' => $validated['status']]);

            Log::info('Vendor Order Status Update Result:', [
                'order_id' => $order->id,
                'items_updated' => $updated,
                'new_status' => $validated['status']
            ]);

            if ($updated === 0) {
                Log::warning('No items found for vendor in order:', [
                    'order_id' => $order->id,
                    'vendor_id' => auth()->id()
                ]);
                
                return response()->json([
                    'success' => false,
                    'message' => 'Bu buyurtmada sizga tegishli mahsulotlar topilmadi.'
                ], 404);
            }

            return response()->json([
                'success' => true,
                'message' => '✅ Buyurtma statusi muvaffaqiyatli yangilandi! (' . $updated . ' ta mahsulot)'
            ]);

        } catch (\Illuminate\Auth\Access\AuthorizationException $e) {
            Log::error('Authorization failed for vendor order status update:', [
                'order_id' => $order->id,
                'vendor_id' => auth()->id(),
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'message' => '❌ Ruxsat yo\'q! Bu buyurtmani yangilash huquqingiz yo\'q.'
            ], 403);

        } catch (\Exception $e) {
            Log::error('Error updating vendor order status:', [
                'order_id' => $order->id,
                'vendor_id' => auth()->id(),
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => '❌ Xatolik yuz berdi: ' . $e->getMessage()
            ], 500);
        }
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
