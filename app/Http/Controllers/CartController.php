<?php

namespace App\Http\Controllers;

use App\Models\Cart;
use App\Models\Coupon;
use App\Models\Product;
use Illuminate\Http\Request;

class CartController extends Controller
{
    public function index()
    {
        $cartItems = auth()->user()->cart()
            ->with(['product.images', 'product.vendor'])
            ->get();

        $subtotal = $cartItems->sum(function ($item) {
            return $item->price * $item->quantity;
        });

        return view('cart.index', compact('cartItems', 'subtotal'));
    }

    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'product_id' => 'required|exists:products,id',
                'quantity' => 'required|integer|min:1',
            ]);

            $product = Product::findOrFail($validated['product_id']);

            if (!$product->is_active) {
                return response()->json([
                    'success' => false,
                    'message' => 'This product is not available.'
                ], 400);
            }

            // Check stock
            if ($product->stock < $validated['quantity']) {
                return response()->json([
                    'success' => false,
                    'message' => 'Insufficient stock available. Only ' . $product->stock . ' items in stock.'
                ], 400);
            }

            // Check if item already in cart
            $existingCart = auth()->user()->cart()
                ->where('product_id', $validated['product_id'])
                ->first();

            if ($existingCart) {
                $newQuantity = $existingCart->quantity + $validated['quantity'];

                if ($product->stock < $newQuantity) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Cannot add more items. Stock limit reached. Only ' . $product->stock . ' items available.'
                    ], 400);
                }

                $existingCart->update(['quantity' => $newQuantity]);
            } else {
                Cart::create([
                    'user_id' => auth()->id(),
                    'product_id' => $validated['product_id'],
                    'quantity' => $validated['quantity'],
                    'price' => $product->price,
                ]);
            }

            $cartCount = auth()->user()->cart()->count();

            return response()->json([
                'success' => true,
                'message' => 'Product added to cart successfully!',
                'cart_count' => $cartCount
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to add product to cart: ' . $e->getMessage()
            ], 500);
        }
    }

    public function update(Request $request, Cart $cart)
    {
        try {
            $validated = $request->validate([
                'quantity' => 'required|integer|min:1',
            ]);

            // Check if the cart item belongs to the authenticated user
            if ($cart->user_id !== auth()->id()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthorized access to cart item.'
                ], 403);
            }

            if ($cart->product->stock < $validated['quantity']) {
                return response()->json([
                    'success' => false,
                    'message' => 'Insufficient stock available. Only ' . $cart->product->stock . ' items in stock.'
                ], 400);
            }

            $cart->update(['quantity' => $validated['quantity']]);

            $cartItems = auth()->user()->cart()->get();
            $subtotal = $cartItems->sum(function ($item) {
                return $item->price * $item->quantity;
            });

            return response()->json([
                'success' => true,
                'message' => 'Cart updated successfully!',
                'subtotal' => $subtotal
            ]);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Cart item not found.'
            ], 404);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to update cart: ' . $e->getMessage()
            ], 500);
        }
    }

    public function destroy(Cart $cart)
    {
        try {
            // Check if the cart item belongs to the authenticated user
            if ($cart->user_id !== auth()->id()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthorized access to cart item.'
                ], 403);
            }
            
            $cart->delete();

            $cartCount = auth()->user()->cart()->count();

            return response()->json([
                'success' => true,
                'message' => 'Item removed from cart!',
                'cart_count' => $cartCount
            ]);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Cart item not found.'
            ], 404);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to remove item from cart: ' . $e->getMessage()
            ], 500);
        }
    }

    public function count()
    {
        try {
            $count = auth()->user()->cart()->count();
            return response()->json(['count' => $count]);
        } catch (\Exception $e) {
            return response()->json(['count' => 0], 500);
        }
    }

    public function clear()
    {
        try {
            auth()->user()->cart()->delete();

            return response()->json([
                'success' => true,
                'message' => 'Cart cleared successfully!'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to clear cart: ' . $e->getMessage()
            ], 500);
        }
    }

    public function applyCoupon(Request $request)
    {
        $validated = $request->validate([
            'coupon_code' => 'required|string',
        ]);

        // Get cart items
        $cartItems = auth()->user()->cart()->get();
        $subtotal = $cartItems->sum(function ($item) {
            return $item->price * $item->quantity;
        });

        $couponCode = strtoupper($validated['coupon_code']);
        
        // First check if coupon exists at all
        $coupon = Coupon::where('code', $couponCode)->first();
        
        if (!$coupon) {
            return response()->json([
                'success' => false,
                'message' => 'The coupon code "' . $validated['coupon_code'] . '" does not exist. Please check the code and try again.'
            ], 400);
        }

        // Check if coupon is active
        if (!$coupon->is_active) {
            return response()->json([
                'success' => false,
                'message' => 'The coupon code "' . $validated['coupon_code'] . '" is not currently active.'
            ], 400);
        }

        // Check if coupon has started
        if ($coupon->starts_at && $coupon->starts_at->isFuture()) {
            return response()->json([
                'success' => false,
                'message' => 'The coupon code "' . $validated['coupon_code'] . '" is not yet valid. It will be available starting ' . $coupon->starts_at->format('M j, Y') . '.'
            ], 400);
        }

        // Check if coupon has expired
        if ($coupon->expires_at && $coupon->expires_at->isPast()) {
            return response()->json([
                'success' => false,
                'message' => 'The coupon code "' . $validated['coupon_code'] . '" has expired. It was valid until ' . $coupon->expires_at->format('M j, Y') . '.'
            ], 400);
        }

        // Check minimum purchase requirement
        if ($subtotal < $coupon->min_purchase) {
            return response()->json([
                'success' => false,
                'message' => 'Minimum purchase of $' . number_format($coupon->min_purchase, 2) . ' required for this coupon. Your current subtotal is $' . number_format($subtotal, 2) . '.'
            ], 400);
        }

        // Check usage limit
        if ($coupon->max_uses && $coupon->used_count >= $coupon->max_uses) {
            return response()->json([
                'success' => false,
                'message' => 'The coupon code "' . $validated['coupon_code'] . '" has reached its maximum usage limit.'
            ], 400);
        }

        // Calculate discount
        $discount = $coupon->type === 'percentage' 
            ? ($subtotal * $coupon->value) / 100 
            : $coupon->value;

        // Calculate updated totals
        $shipping = 10; // Fixed shipping cost
        $tax = ($subtotal - $discount) * 0.1; // 10% tax on (subtotal - discount)
        $total = $subtotal - $discount + $shipping + $tax;

        return response()->json([
            'success' => true,
            'message' => 'Coupon applied successfully!',
            'discount' => $discount,
            'coupon_code' => $coupon->code,
            'subtotal' => $subtotal,
            'tax' => $tax,
            'total' => $total
        ]);
    }
}