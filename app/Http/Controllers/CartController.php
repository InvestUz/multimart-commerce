<?php

namespace App\Http\Controllers;

use App\Models\Cart;
use App\Models\Product;
use Illuminate\Http\Request;

class CartController extends Controller
{
    public function index()
    {
        $cartItems = auth()->user()->cart()
            ->with(['product.images', 'product.vendor', 'variant'])
            ->get();

        $subtotal = $cartItems->sum(function ($item) {
            return $item->price * $item->quantity;
        });

        return view('cart.index', compact('cartItems', 'subtotal'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'product_id' => 'required|exists:products,id',
            'variant_id' => 'nullable|exists:product_variants,id',
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
                'message' => 'Insufficient stock available.'
            ], 400);
        }

        // Check if item already in cart
        $existingCart = auth()->user()->cart()
            ->where('product_id', $validated['product_id'])
            ->where('variant_id', $validated['variant_id'] ?? null)
            ->first();

        if ($existingCart) {
            $newQuantity = $existingCart->quantity + $validated['quantity'];

            if ($product->stock < $newQuantity) {
                return response()->json([
                    'success' => false,
                    'message' => 'Cannot add more items. Stock limit reached.'
                ], 400);
            }

            $existingCart->update(['quantity' => $newQuantity]);
        } else {
            Cart::create([
                'user_id' => auth()->id(),
                'product_id' => $validated['product_id'],
                'variant_id' => $validated['variant_id'] ?? null,
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
    }

    public function update(Request $request, Cart $cart)
    {
        $this->authorize('update', $cart);

        $validated = $request->validate([
            'quantity' => 'required|integer|min:1',
        ]);

        if ($cart->product->stock < $validated['quantity']) {
            return response()->json([
                'success' => false,
                'message' => 'Insufficient stock available.'
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
    }

    public function destroy(Cart $cart)
    {
        $this->authorize('delete', $cart);
        $cart->delete();

        $cartCount = auth()->user()->cart()->count();

        return response()->json([
            'success' => true,
            'message' => 'Item removed from cart!',
            'cart_count' => $cartCount
        ]);
    }

    public function count()
    {
        $count = auth()->user()->cart()->count();

        return response()->json(['count' => $count]);
    }

    public function clear()
    {
        auth()->user()->cart()->delete();

        return response()->json([
            'success' => true,
            'message' => 'Cart cleared successfully!'
        ]);
    }
}
