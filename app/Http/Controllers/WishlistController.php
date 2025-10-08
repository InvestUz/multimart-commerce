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
        $wishlistItems = Wishlist::with(['product.primaryImage', 'product.category', 'product.user'])
            ->where('user_id', auth()->id())
            ->latest()
            ->get();

        return view('wishlist.index', compact('wishlistItems'));
    }

    public function toggle(Request $request)
    {
        $request->validate([
            'product_id' => 'required|exists:products,id',
        ]);

        $wishlist = Wishlist::where('user_id', auth()->id())
            ->where('product_id', $request->product_id)
            ->first();

        if ($wishlist) {
            // Remove from wishlist
            $wishlist->delete();
            $inWishlist = false;
            $message = 'Removed from wishlist';
        } else {
            // Add to wishlist
            Wishlist::create([
                'user_id' => auth()->id(),
                'product_id' => $request->product_id,
            ]);
            $inWishlist = true;
            $message = 'Added to wishlist';
        }

        return response()->json([
            'success' => true,
            'in_wishlist' => $inWishlist,
            'message' => $message
        ]);
    }

    public function destroy(Wishlist $wishlist)
    {
        if ($wishlist->user_id !== auth()->id()) {
            abort(403);
        }

        $wishlist->delete();

        return redirect()->back()->with('success', 'Item removed from wishlist!');
    }
}
