<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Wishlist;
use App\Models\Product;

class WishlistController extends Controller
{
    /**
     * Display a listing of the user's wishlist.
     */
    public function index()
    {
        $wishlists = Wishlist::with('product')
            ->where('user_id', auth()->id())
            ->latest()
            ->get();

        return view('wishlist.index', compact('wishlists'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'product_id' => 'required|exists:products,id',
        ]);

        // Check if already in wishlist
        $existing = Wishlist::where('user_id', auth()->id())
            ->where('product_id', $request->product_id)
            ->first();

        if ($existing) {
            return response()->json(['success' => false, 'message' => 'Product already in wishlist']);
        }

        Wishlist::create([
            'user_id' => auth()->id(),
            'product_id' => $request->product_id,
        ]);

        return response()->json(['success' => true, 'message' => 'Product added to wishlist']);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request)
    {
        $request->validate([
            'product_id' => 'required|exists:products,id',
        ]);

        Wishlist::where('user_id', auth()->id())
            ->where('product_id', $request->product_id)
            ->delete();

        return response()->json(['success' => true, 'message' => 'Product removed from wishlist']);
    }

    /**
     * Toggle wishlist status for a product.
     */
    public function toggle(Request $request)
    {
        $request->validate([
            'product_id' => 'required|exists:products,id',
        ]);

        // Check if already in wishlist
        $existing = Wishlist::where('user_id', auth()->id())
            ->where('product_id', $request->product_id)
            ->first();

        if ($existing) {
            // Remove from wishlist
            $existing->delete();
            return response()->json([
                'success' => true, 
                'message' => 'Product removed from wishlist',
                'wishlist_count' => auth()->user()->wishlist->count()
            ]);
        } else {
            // Add to wishlist
            Wishlist::create([
                'user_id' => auth()->id(),
                'product_id' => $request->product_id,
            ]);
            return response()->json([
                'success' => true, 
                'message' => 'Product added to wishlist',
                'wishlist_count' => auth()->user()->wishlist->count()
            ]);
        }
    }

    /**
     * Clear all items from the wishlist.
     */
    public function clear()
    {
        auth()->user()->wishlist()->delete();

        return response()->json([
            'success' => true,
            'message' => 'Wishlist cleared successfully!'
        ]);
    }

    public function count()
    {
        $count = auth()->user()->wishlist()->count();

        return response()->json(['count' => $count]);
    }
}