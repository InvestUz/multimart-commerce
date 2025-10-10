<?php

namespace App\Http\Controllers;

use App\Models\Cart;
use App\Models\Order;
use App\Models\OrderItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class OrderController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function checkout()
    {
        $cartItems = Cart::with(['product.primaryImage', 'product.user'])
            ->where('user_id', auth()->id())
            ->get();

        if ($cartItems->isEmpty()) {
            return redirect()->route('cart.index')->with('error', 'Your cart is empty!');
        }

        // Validate all items are still available
        foreach ($cartItems as $item) {
            if (!$item->isAvailable()) {
                return redirect()->route('cart.index')
                    ->with('error', "Product '{$item->product->name}' is no longer available.");
            }
        }

        $subtotal = $cartItems->sum(function ($item) {
            return $item->price * $item->quantity;
        });

        $shipping = 4.99;
        $total = $subtotal + $shipping;

        return view('checkout', compact('cartItems', 'subtotal', 'shipping', 'total'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'customer_name' => 'required|string|max:255',
            'customer_email' => 'required|email',
            'customer_phone' => 'required|string|max:20',
            'shipping_address' => 'required|string',
            'city' => 'nullable|string|max:100',
            'postal_code' => 'nullable|string|max:20',
            'payment_method' => 'required|in:cash_on_delivery,credit_card,paypal',
            'notes' => 'nullable|string|max:1000',
        ]);

        $cartItems = Cart::with('product.user')
            ->where('user_id', auth()->id())
            ->get();

        if ($cartItems->isEmpty()) {
            return redirect()->route('cart.index')->with('error', 'Your cart is empty!');
        }

        // Validate all items before processing
        foreach ($cartItems as $item) {
            if (!$item->isAvailable()) {
                return redirect()->route('cart.index')
                    ->with('error', "Product '{$item->product->name}' is no longer available.");
            }
        }

        DB::beginTransaction();

        try {
            $subtotal = $cartItems->sum(function ($item) {
                return $item->price * $item->quantity;
            });

            $shipping = 4.99;
            $total = $subtotal + $shipping;

            // Create order
            $order = Order::create([
                'user_id' => auth()->id(),
                'customer_name' => $request->customer_name,
                'customer_email' => $request->customer_email,
                'customer_phone' => $request->customer_phone,
                'shipping_address' => $request->shipping_address,
                'city' => $request->city,
                'postal_code' => $request->postal_code,
                'country' => 'Uzbekistan',
                'subtotal' => $subtotal,
                'shipping_cost' => $shipping,
                'total' => $total,
                'payment_method' => $request->payment_method,
                'status' => 'pending',
                'payment_status' => 'pending',
                'notes' => $request->notes,
            ]);

            // Create order items
            foreach ($cartItems as $cartItem) {
                OrderItem::create([
                    'order_id' => $order->id,
                    'product_id' => $cartItem->product_id,
                    'vendor_id' => $cartItem->product->user_id,
                    'product_name' => $cartItem->product->name,
                    'product_sku' => $cartItem->product->sku,
                    'quantity' => $cartItem->quantity,
                    'price' => $cartItem->price,
                    'size' => $cartItem->size,
                    'color' => $cartItem->color,
                    'total' => $cartItem->price * $cartItem->quantity,
                ]);

                // Reduce stock
                $cartItem->product->decrement('stock', $cartItem->quantity);
                $cartItem->product->increment('total_sales', $cartItem->quantity);
            }

            // Clear cart
            Cart::where('user_id', auth()->id())->delete();

            DB::commit();

            return redirect()->route('orders.show', $order)
                ->with('success', 'Order placed successfully! Order number: ' . $order->order_number);
        } catch (\Exception $e) {
            DB::rollBack();

            return redirect()->back()
                ->with('error', 'Failed to place order. Please try again.')
                ->withInput();
        }
    }

    public function index()
    {
        $orders = Order::where('user_id', auth()->id())
            ->with(['items.product.primaryImage'])
            ->latest()
            ->paginate(10);

        return view('orders.index', compact('orders'));
    }

    public function show(Order $order)
    {
        // Check authorization
        if ($order->user_id !== auth()->id() && !auth()->user()->isSuperAdmin()) {
            abort(403, 'Unauthorized access to this order.');
        }

        $order->load(['items.product.primaryImage', 'items.vendor']);

        return view('orders.show', compact('order'));
    }

    public function cancel(Order $order)
    {
        // Check authorization
        if ($order->user_id !== auth()->id()) {
            abort(403);
        }

        if (!$order->canBeCancelled()) {
            return redirect()->back()->with('error', 'This order cannot be cancelled.');
        }

        DB::beginTransaction();

        try {
            // Restore stock
            foreach ($order->items as $item) {
                if ($item->product) {
                    $item->product->increment('stock', $item->quantity);
                    $item->product->decrement('total_sales', $item->quantity);
                }
            }

            $order->update(['status' => 'cancelled']);

            DB::commit();

            return redirect()->back()->with('success', 'Order cancelled successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Failed to cancel order. Please try again.');
        }
    }
}
