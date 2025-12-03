<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\UserAddress;
use App\Models\Coupon;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class OrderController extends Controller
{
    public function checkout()
    {
        /** @var \App\Models\User $user */
        $user = auth()->user();
        $cartItems = $user->cart()
            ->with(['product.images', 'product.vendor'])
            ->get();

        if ($cartItems->isEmpty()) {
            return redirect()->route('cart.index')
                ->with('error', 'Your cart is empty!');
        }

        $subtotal = $cartItems->sum(function ($item) {
            return $item->price * $item->quantity;
        });

        // Get all user addresses (since there's no type column)
        $addresses = $user->addresses()->get();
        
        // For now, we'll use all addresses for both shipping and billing
        // In a real application, you might want to add a type column to the database
        $shippingAddresses = $addresses;
        $billingAddresses = $addresses;

        // Get active payment methods
        $paymentMethods = \App\Models\PaymentMethod::active()->ordered()->get();

        return view('checkout', compact(
            'cartItems',
            'subtotal',
            'shippingAddresses',
            'billingAddresses',
            'paymentMethods'
        ));
    }

    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'shipping_address_id' => 'required|integer',
                'billing_address_id' => 'required|integer',
                'payment_method' => 'required|exists:payment_methods,code',
                'coupon_code' => 'nullable|string',
                'notes' => 'nullable|string|max:1000',
            ]);
            
            // Additional validation to check if addresses belong to the user and exist
            /** @var \App\Models\User $user */
            $user = auth()->user();
            $userId = $user->id;
            
            $shippingAddress = UserAddress::where('id', $validated['shipping_address_id'])
                ->where('user_id', $userId)
                ->first();
                
            $billingAddress = UserAddress::where('id', $validated['billing_address_id'])
                ->where('user_id', $userId)
                ->first();
                
            if (!$shippingAddress) {
                return back()->with('error', 'Invalid shipping address selected')->withInput();
            }
            
            if (!$billingAddress) {
                return back()->with('error', 'Invalid billing address selected')->withInput();
            }
            
            // Update validated data with the address objects
            $validated['shipping_address_id'] = $shippingAddress->id;
            $validated['billing_address_id'] = $billingAddress->id;
            
        } catch (\Illuminate\Validation\ValidationException $e) {
            return back()->with('error', 'Validation failed: ' . json_encode($e->errors()))->withInput();
        } catch (\Exception $e) {
            return back()->with('error', 'Validation error: ' . $e->getMessage())->withInput();
        }

        /** @var \App\Models\User $user */
        $user = auth()->user();
        $cartItems = $user->cart()
            ->with(['product'])
            ->get();

        if ($cartItems->isEmpty()) {
            return back()->with('error', 'Your cart is empty!');
        }

        // Check stock availability
        foreach ($cartItems as $item) {
            if ($item->product->stock < $item->quantity) {
                return back()->with('error', "Insufficient stock for {$item->product->name}")->withInput();
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
                ->where('starts_at', '<=', now())
                ->where('expires_at', '>=', now())
                ->first();

            if ($coupon && $subtotal >= $coupon->min_purchase) {
                if ($coupon->type === 'percentage') {
                    $discount = $subtotal * ($coupon->value / 100);
                } else {
                    $discount = $coupon->value;
                }

                if ($coupon->max_uses && $coupon->used_count >= $coupon->max_uses) {
                    return back()->with('error', 'Coupon usage limit exceeded')->withInput();
                }
            } else {
                return back()->with('error', 'Invalid coupon code')->withInput();
            }
        }

        $shippingCost = 10; // You can make this dynamic
        $tax = ($subtotal - $discount) * 0.1; // 10% tax
        $total = $subtotal - $discount + $shippingCost + $tax;

        DB::beginTransaction();
        try {
            // Get address details
            $shippingAddress = UserAddress::find($validated['shipping_address_id']);
            $billingAddress = UserAddress::find($validated['billing_address_id']);
            
            if (!$shippingAddress || !$billingAddress) {
                throw new \Exception('Shipping or billing address not found');
            }

            // Create order
            $order = Order::create([
                'user_id' => $user->id,
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
                // Store address details for reference
                'customer_name' => $shippingAddress->full_name,
                'customer_email' => $user->email,
                'customer_phone' => $shippingAddress->phone,
                'shipping_address' => $shippingAddress->address_line1 . ($shippingAddress->address_line2 ? ', ' . $shippingAddress->address_line2 : ''),
                'city' => $shippingAddress->city,
                'state' => $shippingAddress->state,
                'postal_code' => $shippingAddress->postal_code,
                'country' => $shippingAddress->country,
            ]);

            // Create order items
            foreach ($cartItems as $item) {
                OrderItem::create([
                    'order_id' => $order->id,
                    'product_id' => $item->product_id,
                    'vendor_id' => $item->product->user_id,
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
            $user->cart()->delete();

            // Send notification to admins and vendors
            $this->notifyAdminsAndVendors($order);

            DB::commit();

            return redirect()->route('orders.show', $order)
                ->with('success', 'Order placed successfully!');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Failed to place order: ' . $e->getMessage())->withInput();
        }
    }

    /**
     * Send notification to admins and vendors when an order is placed
     */
    protected function notifyAdminsAndVendors(Order $order)
    {
        // Notify super admins
        $admins = User::where('role', 'super_admin')->get();
        foreach ($admins as $admin) {
            $admin->notify(new \App\Notifications\NewOrderPlaced($order));
        }

        // Notify vendors
        $vendors = $order->items->pluck('vendor')->unique('id');
        foreach ($vendors as $vendor) {
            if ($vendor) {
                $vendor->notify(new \App\Notifications\NewOrderPlaced($order));
            }
        }
    }

    public function index()
    {
        /** @var \App\Models\User $user */
        $user = auth()->user();
        $orders = $user->orders()
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