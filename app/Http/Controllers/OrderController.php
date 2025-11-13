<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Address;
use App\Models\Coupon;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;

class OrderController extends Controller
{
    public function checkout()
    {
        $cartItems = auth()->user()->cart()
            ->with(['product.images', 'product.vendor', 'variant'])
            ->get();

        if ($cartItems->isEmpty()) {
            return redirect()->route('cart.index')
                ->with('error', 'Your cart is empty!');
        }

        $subtotal = $cartItems->sum(function ($item) {
            return $item->price * $item->quantity;
        });

        $shippingAddresses = auth()->user()->addresses()
            ->where('type', 'shipping')
            ->get();

        $billingAddresses = auth()->user()->addresses()
            ->where('type', 'billing')
            ->get();

        return view('checkout', compact(
            'cartItems',
            'subtotal',
            'shippingAddresses',
            'billingAddresses'
        ));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'shipping_address_id' => 'required|exists:addresses,id',
            'billing_address_id' => 'required|exists:addresses,id',
            'payment_method' => 'required|in:cod,card,paypal',
            'coupon_code' => 'nullable|string',
            'notes' => 'nullable|string|max:1000',
        ]);

        $cartItems = auth()->user()->cart()
            ->with(['product', 'variant'])
            ->get();

        if ($cartItems->isEmpty()) {
            return back()->with('error', 'Your cart is empty!');
        }

        // Check stock availability
        foreach ($cartItems as $item) {
            if ($item->product->stock < $item->quantity) {
                return back()->with('error', "Insufficient stock for {$item->product->name}");
            }
        }

        $subtotal = $cartItems->sum(function ($item) {
            return $item->price * $item->quantity;
        });

        $discount = 0;
        $coupon = null;

        // Apply coupon if provided
        if ($request->coupon_code) {
            $coupon = Coupon::where('code', $request->coupon_code)
                ->where('is_active', true)
                ->where('start_date', '<=', now())
                ->where('end_date', '>=', now())
                ->first();

            if ($coupon && $subtotal >= $coupon->min_purchase) {
                if ($coupon->type === 'percentage') {
                    $discount = $subtotal * ($coupon->value / 100);
                    if ($coupon->max_discount) {
                        $discount = min($discount, $coupon->max_discount);
                    }
                } else {
                    $discount = $coupon->value;
                }

                if ($coupon->usage_limit && $coupon->used_count >= $coupon->usage_limit) {
                    return back()->with('error', 'Coupon usage limit exceeded');
                }
            }
        }

        $shippingCost = 10; // You can make this dynamic
        $tax = ($subtotal - $discount) * 0.1; // 10% tax
        $total = $subtotal - $discount + $shippingCost + $tax;

        DB::beginTransaction();
        try {
            // Create order
            $order = Order::create([
                'user_id' => auth()->id(),
                'order_number' => 'ORD-' . strtoupper(Str::random(10)),
                'subtotal' => $subtotal,
                'discount' => $discount,
                'shipping_cost' => $shippingCost,
                'tax' => $tax,
                'total' => $total,
                'coupon_id' => $coupon?->id,
                'payment_method' => $validated['payment_method'],
                'payment_status' => $validated['payment_method'] === 'cod' ? 'pending' : 'paid',
                'status' => 'pending',
                'notes' => $validated['notes'],
                'shipping_address_id' => $validated['shipping_address_id'],
                'billing_address_id' => $validated['billing_address_id'],
            ]);

            // Create order items
            foreach ($cartItems as $item) {
                OrderItem::create([
                    'order_id' => $order->id,
                    'product_id' => $item->product_id,
                    'variant_id' => $item->variant_id,
                    'vendor_id' => $item->product->vendor_id,
                    'quantity' => $item->quantity,
                    'price' => $item->price,
                    'total' => $item->price * $item->quantity,
                    'status' => 'pending',
                ]);

                // Reduce stock
                $item->product->decrement('stock', $item->quantity);
            }

            // Update coupon usage
            if ($coupon) {
                $coupon->increment('used_count');
            }

            // Clear cart
            auth()->user()->cart()->delete();

            DB::commit();

            return redirect()->route('orders.show', $order)
                ->with('success', 'Order placed successfully!');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Failed to place order. Please try again.');
        }
    }

    public function index()
    {
        $orders = auth()->user()->orders()
            ->with(['items.product.images'])
            ->latest()
            ->paginate(10);

        return view('orders.index', compact('orders'));
    }

    public function show(Order $order)
    {
        $this->authorize('view', $order);

        $order->load([
            'items.product.images',
            'items.vendor',
            'shippingAddress',
            'billingAddress',
            'coupon'
        ]);

        return view('orders.show', compact('order'));
    }

    public function cancel(Order $order)
    {
        $this->authorize('update', $order);

        if (!in_array($order->status, ['pending', 'processing'])) {
            return back()->with('error', 'This order cannot be cancelled.');
        }

        DB::beginTransaction();
        try {
            $order->update(['status' => 'cancelled']);

            // Restore stock
            foreach ($order->items as $item) {
                $item->product->increment('stock', $item->quantity);
                $item->update(['status' => 'cancelled']);
            }

            DB::commit();

            return back()->with('success', 'Order cancelled successfully!');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Failed to cancel order.');
        }
    }

    public function invoice(Order $order)
    {
        $this->authorize('view', $order);

        $order->load([
            'items.product',
            'items.vendor',
            'shippingAddress',
            'billingAddress',
            'user'
        ]);

        return view('orders.invoice', compact('order'));
    }
}
