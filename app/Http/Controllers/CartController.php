<?php

namespace App\Http\Controllers;

use App\Models\Cart;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class CartController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        $cartItems = Cart::with(['product.primaryImage', 'product.user'])
            ->where('user_id', auth()->id())
            ->get();

        $subtotal = $cartItems->sum(function ($item) {
            return $item->price * $item->quantity;
        });

        $shipping = 4.99;
        $total = $subtotal + $shipping;

        return view('cart.index', compact('cartItems', 'subtotal', 'shipping', 'total'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'product_id' => 'required|exists:products,id',
            'quantity' => 'required|integer|min:1',
            'size' => 'nullable|string',
            'color' => 'nullable|string',
        ]);

        $product = Product::findOrFail($request->product_id);

        if (!$product->canBePurchased($request->quantity)) {
            return redirect()->back()->with('error', 'Product is not available or insufficient stock!');
        }

        $cartItem = Cart::where('user_id', auth()->id())
            ->where('product_id', $product->id)
            ->where('size', $request->size ?? '')
            ->where('color', $request->color ?? '')
            ->first();

        if ($cartItem) {
            $newQuantity = $cartItem->quantity + $request->quantity;

            if (!$product->canBePurchased($newQuantity)) {
                return redirect()->back()->with('error', 'Not enough stock available!');
            }

            $cartItem->quantity = $newQuantity;
            $cartItem->save();
        } else {
            Cart::create([
                'user_id' => auth()->id(),
                'product_id' => $product->id,
                'quantity' => $request->quantity,
                'size' => $request->size,
                'color' => $request->color,
                'price' => $product->price,
            ]);
        }

        return redirect()->back()->with('success', 'Product added to cart successfully!');
    }

    public function update(Request $request, Cart $cart): JsonResponse
    {
        if ($cart->user_id !== auth()->id()) {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
        }

        $request->validate([
            'quantity' => 'required|integer|min:1',
        ]);

        if (!$cart->product->canBePurchased($request->quantity)) {
            return response()->json(['success' => false, 'message' => 'Not enough stock available!'], 422);
        }

        $cart->update([
            'quantity' => $request->quantity,
        ]);

        $cartItems = Cart::where('user_id', auth()->id())->get();
        $subtotal = $cartItems->sum(function ($item) {
            return $item->price * $item->quantity;
        });

        return response()->json([
            'success' => true,
            'message' => 'Cart updated successfully!',
            'subtotal' => $cart->price * $request->quantity,
            'cart_subtotal' => $subtotal,
            'cart_total' => $subtotal + 4.99,
        ]);
    }

    public function destroy(Cart $cart): JsonResponse
    {
        if ($cart->user_id !== auth()->id()) {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
        }

        $cart->delete();

        $cartItems = Cart::where('user_id', auth()->id())->get();
        $subtotal = $cartItems->sum(function ($item) {
            return $item->price * $item->quantity;
        });

        return response()->json([
            'success' => true,
            'message' => 'Item removed from cart!',
            'cart_count' => $cartItems->sum('quantity'),
            'cart_subtotal' => $subtotal,
            'cart_total' => $subtotal + 4.99,
        ]);
    }

    public function count(): JsonResponse
    {
        $count = 0;

        if (auth()->check()) {
            $count = Cart::where('user_id', auth()->id())->sum('quantity');
        }

        return response()->json(['count' => $count]);
    }
}