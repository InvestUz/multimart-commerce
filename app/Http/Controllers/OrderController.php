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
        $cartItems = auth()->user()->cart()
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
        $addresses = auth()->user()->addresses()->get();
        
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
            // Debug: Log the request data
            \Log::info('Order placement attempt', [
                'user_id' => auth()->id(),
                'request_data' => $request->all()
            ]);
            
            $validated = $request->validate([
                'shipping_address_id' => 'required|integer',
                'billing_address_id' => 'required|integer',
                'payment_method' => 'required|exists:payment_methods,code',
                'coupon_code' => 'nullable|string',
                'notes' => 'nullable|string|max:1000',
            ]);
            
            // Debug: Log validated data
            \Log::info('Order validation passed', [
                'user_id' => auth()->id(),
                'validated_data' => $validated
            ]);
            
            // Additional validation to check if addresses belong to the user and exist
            $userId = auth()->id();
            $shippingAddress = UserAddress::where('id', $validated['shipping_address_id'])
                ->where('user_id', $userId)
                ->first();
                
            $billingAddress = UserAddress::where('id', $validated['billing_address_id'])
                ->where('user_id', $userId)
                ->first();
                
            if (!$shippingAddress) {
                \Log::warning('Invalid shipping address selected', [
                    'user_id' => $userId,
                    'address_id' => $validated['shipping_address_id']
                ]);
                return back()->with('error', 'Invalid shipping address selected')->withInput();
            }
            
            if (!$billingAddress) {
                \Log::warning('Invalid billing address selected', [
                    'user_id' => $userId,
                    'address_id' => $validated['billing_address_id']
                ]);
                return back()->with('error', 'Invalid billing address selected')->withInput();
            }
            
            // Update validated data with the address objects
            $validated['shipping_address_id'] = $shippingAddress->id;
            $validated['billing_address_id'] = $billingAddress->id;
            
            // Debug: Log address validation passed
            \Log::info('Address validation passed', [
                'user_id' => $userId,
                'shipping_address_id' => $shippingAddress->id,
                'billing_address_id' => $billingAddress->id
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            \Log::error('Order validation failed', [
                'user_id' => auth()->id(),
                'errors' => $e->errors(),
                'request_data' => $request->all()
            ]);
            return back()->with('error', 'Validation failed: ' . json_encode($e->errors()))->withInput();
        } catch (\Exception $e) {
            \Log::error('Order validation error', [
                'user_id' => auth()->id(),
                'error' => $e->getMessage(),
                'request_data' => $request->all()
            ]);
            return back()->with('error', 'Validation error: ' . $e->getMessage())->withInput();
        }

        $cartItems = auth()->user()->cart()
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
            // Debug: Log before order creation
            \Log::info('Starting order creation transaction', [
                'user_id' => auth()->id()
            ]);
            
            // Get address details
            $shippingAddress = UserAddress::find($validated['shipping_address_id']);
            $billingAddress = UserAddress::find($validated['billing_address_id']);
            
            if (!$shippingAddress || !$billingAddress) {
                \Log::error('Shipping or billing address not found', [
                    'user_id' => auth()->id(),
                    'shipping_address_id' => $validated['shipping_address_id'],
                    'billing_address_id' => $validated['billing_address_id']
                ]);
                throw new \Exception('Shipping or billing address not found');
            }
            
            // Debug: Log address details
            \Log::info('Address details retrieved', [
                'user_id' => auth()->id(),
                'shipping_address' => $shippingAddress->toArray(),
                'billing_address' => $billingAddress->toArray()
            ]);

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
                // Store address details for reference
                'customer_name' => $shippingAddress->full_name,
                'customer_email' => auth()->user()->email,
                'customer_phone' => $shippingAddress->phone,
                'shipping_address' => $shippingAddress->address_line1 . ($shippingAddress->address_line2 ? ', ' . $shippingAddress->address_line2 : ''),
                'city' => $shippingAddress->city,
                'state' => $shippingAddress->state,
                'postal_code' => $shippingAddress->postal_code,
                'country' => $shippingAddress->country,
            ]);
            
            // Debug: Log order created
            \Log::info('Order created successfully', [
                'user_id' => auth()->id(),
                'order_id' => $order->id,
                'order_number' => $order->order_number
            ]);

            // Create order items
            \Log::info('Creating order items', [
                'user_id' => auth()->id(),
                'order_id' => $order->id,
                'cart_items_count' => $cartItems->count()
            ]);
            
            foreach ($cartItems as $item) {
                \Log::info('Creating order item', [
                    'user_id' => auth()->id(),
                    'order_id' => $order->id,
                    'product_id' => $item->product_id,
                    'quantity' => $item->quantity,
                    'price' => $item->price
                ]);
                
                OrderItem::create([
                    'order_id' => $order->id,
                    'product_id' => $item->product_id,
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
            
            // Debug: Log cart cleared
            \Log::info('Cart cleared', [
                'user_id' => auth()->id()
            ]);

            // Send notification to admins and vendors
            $this->notifyAdminsAndVendors($order);
            
            // Debug: Log notifications sent
            \Log::info('Notifications sent', [
                'user_id' => auth()->id(),
                'order_id' => $order->id
            ]);

            DB::commit();
            
            // Debug: Log transaction committed
            \Log::info('Order transaction committed', [
                'user_id' => auth()->id(),
                'order_id' => $order->id
            ]);

            return redirect()->route('orders.show', $order)
                ->with('success', 'Order placed successfully!');

        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Order placement failed: ' . $e->getMessage(), [
                'user_id' => auth()->id(),
                'request_data' => $request->all(),
                'exception' => $e,
                'trace' => $e->getTraceAsString()
            ]);
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