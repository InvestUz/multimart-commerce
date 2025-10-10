<?php

class ReviewController extends Controller
{
    public function index(Request $request)
    {
        $query = Review::with(['user', 'product.primaryImage', 'product.user']);

        // Filter by approval status
        if ($request->filled('status')) {
            if ($request->status === 'approved') {
                $query->where('is_approved', true);
            } elseif ($request->status === 'pending') {
                $query->where('is_approved', false);
            }
        }

        // Filter by verified purchase
        if ($request->filled('verified')) {
            if ($request->verified === 'yes') {
                $query->where('is_verified_purchase', true);
            } elseif ($request->verified === 'no') {
                $query->where('is_verified_purchase', false);
            }
        }

        // Filter by rating
        if ($request->filled('rating')) {
            $query->where('rating', $request->rating);
        }

        // Filter by product
        if ($request->filled('product_id')) {
            $query->where('product_id', $request->product_id);
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
                })->orWhere('comment', 'like', "%{$search}%");
            });
        }

        // Date range filter
        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        // Sort
        $sortBy = $request->input('sort_by', 'latest');
        switch ($sortBy) {
            case 'oldest':
                $query->oldest();
                break;
            case 'rating_high':
                $query->orderBy('rating', 'desc');
                break;
            case 'rating_low':
                $query->orderBy('rating', 'asc');
                break;
            case 'helpful':
                $query->orderBy('helpful_count', 'desc');
                break;
            default:
                $query->latest();
        }

        $reviews = $query->paginate(20)->withQueryString();

        // Statistics
        $stats = [
            'total' => Review::count(),
            'pending' => Review::where('is_approved', false)->count(),
            'approved' => Review::where('is_approved', true)->count(),
            'verified' => Review::where('is_verified_purchase', true)->count(),
            'average_rating' => Review::where('is_approved', true)->avg('rating'),
        ];

        return view('super-admin.reviews.index', compact('reviews', 'stats'));
    }

    public function show(Review $review)
    {
        $review->load(['user', 'product.primaryImage', 'product.user', 'order']);

        return view('super-admin.reviews.show', compact('review'));
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

    public function toggleApproval(Review $review)
    {
        $newStatus = !$review->is_approved;
        $review->update(['is_approved' => $newStatus]);

        // Update product rating
        $review->product->updateRating();

        $message = $newStatus ? 'approved' : 'rejected';

        return redirect()->back()
            ->with('success', "Review {$message} successfully!");
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

    public function removeResponse(Review $review)
    {
        $review->update([
            'admin_response' => null,
            'responded_at' => null,
        ]);

        return redirect()->back()
            ->with('success', 'Response removed successfully!');
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

    public function bulkApprove(Request $request)
    {
        $request->validate([
            'review_ids' => 'required|array',
            'review_ids.*' => 'exists:reviews,id',
        ]);

        $updated = 0;
        $productIds = [];

        foreach ($request->review_ids as $reviewId) {
            $review = Review::find($reviewId);
            if ($review && !$review->is_approved) {
                $review->update(['is_approved' => true]);
                $productIds[] = $review->product_id;
                $updated++;
            }
        }

        // Update product ratings
        $productIds = array_unique($productIds);
        foreach ($productIds as $productId) {
            $product = \App\Models\Product::find($productId);
            if ($product) {
                $product->updateRating();
            }
        }

        return redirect()->back()
            ->with('success', "{$updated} reviews approved successfully!");
    }

    public function bulkReject(Request $request)
    {
        $request->validate([
            'review_ids' => 'required|array',
            'review_ids.*' => 'exists:reviews,id',
        ]);

        $updated = 0;
        $productIds = [];

        foreach ($request->review_ids as $reviewId) {
            $review = Review::find($reviewId);
            if ($review && $review->is_approved) {
                $review->update(['is_approved' => false]);
                $productIds[] = $review->product_id;
                $updated++;
            }
        }

        // Update product ratings
        $productIds = array_unique($productIds);
        foreach ($productIds as $productId) {
            $product = \App\Models\Product::find($productId);
            if ($product) {
                $product->updateRating();
            }
        }

        return redirect()->back()
            ->with('success', "{$updated} reviews rejected successfully!");
    }

    public function bulkDelete(Request $request)
    {
        $request->validate([
            'review_ids' => 'required|array',
            'review_ids.*' => 'exists:reviews,id',
        ]);

        $deleted = 0;
        $productIds = [];

        foreach ($request->review_ids as $reviewId) {
            $review = Review::find($reviewId);
            if ($review) {
                $productIds[] = $review->product_id;
                $review->delete();
                $deleted++;
            }
        }

        // Update product ratings
        $productIds = array_unique($productIds);
        foreach ($productIds as $productId) {
            $product = \App\Models\Product::find($productId);
            if ($product) {
                $product->updateRating();
            }
        }

        return redirect()->back()
            ->with('success', "{$deleted} reviews deleted successfully!");
    }

    public function statistics()
    {
        $totalReviews = Review::count();
        $approvedReviews = Review::where('is_approved', true)->count();
        $pendingReviews = Review::where('is_approved', false)->count();
        $verifiedReviews = Review::where('is_verified_purchase', true)->count();
        $averageRating = Review::where('is_approved', true)->avg('rating');

        $ratingDistribution = Review::selectRaw('rating, count(*) as count')
            ->where('is_approved', true)
            ->groupBy('rating')
            ->orderBy('rating', 'desc')
            ->pluck('count', 'rating');

        $topReviewedProducts = \App\Models\Product::withCount(['reviews' => function ($q) {
            $q->where('is_approved', true);
        }])
            ->orderBy('reviews_count', 'desc')
            ->take(10)
            ->get();

        $recentReviews = Review::with(['user', 'product'])
            ->where('is_approved', true)
            ->latest()
            ->take(10)
            ->get();

        $monthlyReviews = Review::selectRaw('DATE_FORMAT(created_at, "%Y-%m") as month, count(*) as count')
            ->where('created_at', '>=', now()->subMonths(12))
            ->groupBy('month')
            ->orderBy('month')
            ->get();

        return view('super-admin.reviews.statistics', compact(
            'totalReviews',
            'approvedReviews',
            'pendingReviews',
            'verifiedReviews',
            'averageRating',
            'ratingDistribution',
            'topReviewedProducts',
            'recentReviews',
            'monthlyReviews'
        ));
    }
}
