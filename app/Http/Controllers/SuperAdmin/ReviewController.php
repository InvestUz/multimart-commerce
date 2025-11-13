<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\Review;
use Illuminate\Http\Request;

class ReviewController extends Controller
{
    public function index(Request $request)
    {
        $query = Review::with(['user', 'product', 'order']);

        if ($request->filled('status')) {
            $isApproved = $request->status === 'approved';
            $query->where('is_approved', $isApproved);
        }

        $reviews = $query->latest()->paginate(20);

        return view('super-admin.reviews.index', compact('reviews'));
    }

    public function approve(Review $review)
    {
        $review->update(['is_approved' => true]);

        return response()->json([
            'success' => true,
            'message' => 'Review approved successfully!'
        ]);
    }

    public function reject(Review $review)
    {
        $review->update(['is_approved' => false]);

        return response()->json([
            'success' => true,
            'message' => 'Review rejected!'
        ]);
    }

    public function destroy(Review $review)
    {
        $review->delete();

        return redirect()->route('super-admin.reviews.index')
            ->with('success', 'Review deleted successfully!');
    }
}
