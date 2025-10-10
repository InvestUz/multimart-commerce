<?php

namespace App\Http\Controllers;

use App\Models\Review;
use App\Models\Product;
use App\Models\Order;
use Illuminate\Http\Request;

class ReviewController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function store(Request $request)
    {
        $request->validate([
            'product_id' => 'required|exists:products,id',
            'rating' => 'required|integer|min:1|max:5',
            'comment' => 'nullable|string|max:1000',
        ]);

        // Check if user already reviewed this product
        $existingReview = Review::where('user_id', auth()->id())
            ->where('product_id', $request->product_id)
            ->first();

        if ($existingReview) {
            return response()->json([
                'success' => false,
                'message' => 'You have already reviewed this product!'
            ], 422);
        }

        // Check if user purchased this product
        $hasPurchased = Order::where('user_id', auth()->id())
            ->whereHas('items', function ($query) use ($request) {
                $query->where('product_id', $request->product_id);
            })
            ->where('status', 'delivered')
            ->exists();

        $review = Review::create([
            'user_id' => auth()->id(),
            'product_id' => $request->product_id,
            'rating' => $request->rating,
            'comment' => $request->comment,
            'is_approved' => false, // Requires admin approval
            'is_verified_purchase' => $hasPurchased,
        ]);

        // Update product rating (even if not approved yet, for immediate feedback)
        $product = Product::find($request->product_id);
        $product->updateRating();

        return response()->json([
            'success' => true,
            'message' => 'Review submitted successfully! It will be visible after admin approval.'
        ]);
    }

    public function update(Request $request, Review $review)
    {
        if ($review->user_id !== auth()->id()) {
            abort(403);
        }

        $request->validate([
            'rating' => 'required|integer|min:1|max:5',
            'comment' => 'nullable|string|max:1000',
        ]);

        $review->update([
            'rating' => $request->rating,
            'comment' => $request->comment,
            'is_approved' => false, // Reset approval status
        ]);

        // Update product rating
        $review->product->updateRating();

        return redirect()->back()->with('success', 'Review updated successfully!');
    }

    public function destroy(Review $review)
    {
        if ($review->user_id !== auth()->id() && !auth()->user()->isSuperAdmin()) {
            abort(403);
        }

        $productId = $review->product_id;
        $review->delete();

        // Update product rating
        $product = Product::find($productId);
        if ($product) {
            $product->updateRating();
        }

        return redirect()->back()->with('success', 'Review deleted successfully!');
    }
}