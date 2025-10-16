<?php

namespace App\Http\Controllers;

use App\Models\Wishlist;
use App\Models\Product;
use Illuminate\Http\Request;

class WishlistController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        $wishlists = Wishlist::with(['product.primaryImage', 'product.category', 'product.user'])
            ->where('user_id', auth()->id())
            ->latest()
            ->get();

        return view('wishlist.index', compact('wishlists'));
    }

    public function toggle(Request $request)
    {
        $request->validate([
            'product_id' => 'required|exists:products,id',
        ]);

        $userId = auth()->id();
        $productId = $request->product_id;

        $wishlist = Wishlist::where('user_id', $userId)
            ->where('product_id', $productId)
            ->first();

        if ($wishlist) {
            $wishlist->delete();
        } else {
            Wishlist::create([
                'user_id' => $userId,
                'product_id' => $productId,
            ]);
        }

        // Check if it's AJAX request
        if ($request->ajax() || $request->wantsJson()) {
            $wishlistCount = Wishlist::where('user_id', $userId)->count();
            return ['wishlist_count' => $wishlistCount];
        }

        return redirect()->back();
    }

    public function destroy(Wishlist $wishlist, Request $request)
    {
        $userId = auth()->id();

        if ($wishlist->user_id !== $userId) {
            abort(403);
        }

        $wishlist->delete();

        // Check if it's AJAX request
        if ($request->ajax() || $request->wantsJson()) {
            $wishlistCount = Wishlist::where('user_id', $userId)->count();
            return ['wishlist_count' => $wishlistCount];
        }

        return redirect()->back();
    }

    public function count()
    {
        $count = 0;

        if (auth()->check()) {
            $count = Wishlist::where('user_id', auth()->id())->count();
        }

        return ['count' => $count];
    }
}
