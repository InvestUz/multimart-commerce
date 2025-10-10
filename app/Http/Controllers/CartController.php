<?php


namespace App\Http\Controllers;

use App\Models\Cart;
use App\Models\Product;
use Illuminate\Http\Request;

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

        // Check if product is available
        if (!$product->canBePurchased($request->quantity)) {
            return redirect()->back()->with([
                'success' => false,
                'message' => 'Product is not available or insufficient stock!'
            ], 422);
        }

        // Check if item already exists in cart
        $cartItem = Cart::where('user_id', auth()->id())
            ->where('product_id', $product->id)
            ->where('size', $request->size)
            ->where('color', $request->color)
            ->first();

        if ($cartItem) {
            // Update quantity
            $newQuantity = $cartItem->quantity + $request->quantity;

            if (!$product->canBePurchased($newQuantity)) {
                 return redirect()->back()->with([
                    'success' => false,
                    'message' => 'Not enough stock available!'
                ], 422);
            }

            $cartItem->quantity = $newQuantity;
            $cartItem->save();
        } else {
            // Create new cart item
            Cart::create([
                'user_id' => auth()->id(),
                'product_id' => $product->id,
                'quantity' => $request->quantity,
                'size' => $request->size,
                'color' => $request->color,
                'price' => $product->price,
            ]);
        }

        $cartCount = Cart::where('user_id', auth()->id())->sum('quantity');

         return redirect()->back()->with([
            'success' => true,
            'message' => 'Product added to cart successfully!',
            'cart_count' => $cartCount
        ]);
    }

    public function update(Request $request, Cart $cart)
    {
        if ($cart->user_id !== auth()->id()) {
            abort(403);
        }

        $request->validate([
            'quantity' => 'required|integer|min:1',
        ]);

        // Check stock availability
        if (!$cart->product->canBePurchased($request->quantity)) {
             return redirect()->back()->with([
                'success' => false,
                'message' => 'Not enough stock available!'
            ], 422);
        }

        $cart->update([
            'quantity' => $request->quantity,
        ]);

         return redirect()->back()->with([
            'success' => true,
            'message' => 'Cart updated successfully!',
            'subtotal' => $cart->subtotal
        ]);
    }

    public function destroy(Cart $cart)
    {
        if ($cart->user_id !== auth()->id()) {
            abort(403);
        }

        $cart->delete();

         return redirect()->back()->with([
            'success' => true,
            'message' => 'Item removed from cart!'
        ]);
    }

    public function count()
    {
        $count = 0;

        if (auth()->check()) {
            $count = Cart::where('user_id', auth()->id())->sum('quantity');
        }

         return redirect()->back()->with(['count' => $count]);
    }
}
