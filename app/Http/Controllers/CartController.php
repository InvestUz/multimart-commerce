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
            $this->authorize('update', $cart);

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
            $this->authorize('delete', $cart);
            
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

        $coupon = Coupon::where('code', strtoupper($validated['coupon_code']))
            ->where('is_active', true)
            ->where('starts_at', '<=', now())
            ->where('expires_at', '>=', now())
            ->first();

        if (!$coupon) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid coupon code.'
            ], 400);
        }

        // Get cart items
        $cartItems = auth()->user()->cart()->get();
        $subtotal = $cartItems->sum(function ($item) {
            return $item->price * $item->quantity;
        });

        // Check minimum purchase requirement
        if ($subtotal < $coupon->min_purchase) {
            return response()->json([
                'success' => false,
                'message' => 'Minimum purchase of $' . number_format($coupon->min_purchase, 2) . ' required for this coupon.'
            ], 400);
        }

        // Check usage limit
        if ($coupon->max_uses && $coupon->used_count >= $coupon->max_uses) {
            return response()->json([
                'success' => false,
                'message' => 'Coupon usage limit exceeded.'
            ], 400);
        }

        // Calculate discount
        $discount = $coupon->type === 'percentage' 
            ? ($subtotal * $coupon->value) / 100 
            : $coupon->value;

        return response()->json([
            'success' => true,
            'message' => 'Coupon applied successfully!',
            'discount' => $discount,
            'coupon_code' => $coupon->code
        ]);
    }
}