<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\Review;
use Illuminate\Http\Request;

class ReviewController extends Controller
{
    public function index(Request $request)
    {
        $query = Review::with(['user', 'product']);

        // Filter by approval status
        if ($request->filled('status')) {
            if ($request->status === 'approved') {
                $query->where('is_approved', true);
            } elseif ($request->status === 'pending') {
                $query->where('is_approved', false);
            }
        }

        // Filter by rating
        if ($request->filled('rating')) {
            $query->where('rating', $request->rating);
        }

        // Search by product or user
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->whereHas('product', function ($pq) use ($search) {
                    $pq->where('name', 'like', "%{$search}%");
                })->orWhereHas('user', function ($uq) use ($search) {
                    $uq->where('name', 'like', "%{$search}%")
                        ->orWhere('email', 'like', "%{$search}%");
                });
            });
        }

        $reviews = $query->latest()->paginate(20);

        return view('super-admin.reviews.index', compact('reviews'));
    }

    public function approve(Review $review)
    {
        $review->update(['is_approved' => true]);

        // Update product rating
        $review->product->updateRating();

        return redirect()->back()
            ->with('success', 'Review approved successfully!');
    }

    public function reject(Review $review)
    {
        $review->update(['is_approved' => false]);

        // Update product rating
        $review->product->updateRating();

        return redirect()->back()
            ->with('success', 'Review rejected successfully!');
    }

    public function respond(Request $request, Review $review)
    {
        $request->validate([
            'admin_response' => 'required|string|max:1000',
        ]);

        $review->update([
            'admin_response' => $request->admin_response,
            'responded_at' => now(),
        ]);

        return redirect()->back()
            ->with('success', 'Response added successfully!');
    }

    public function destroy(Review $review)
    {
        $productId = $review->product_id;
        $review->delete();

        // Update product rating
        $product = \App\Models\Product::find($productId);
        if ($product) {
            $product->updateRating();
        }

        return redirect()->back()
            ->with('success', 'Review deleted successfully!');
    }
}
