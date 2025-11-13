<?php

namespace App\Http\Controllers\Vendor;

use App\Http\Controllers\Controller;
use App\Models\Review;
use Illuminate\Http\Request;

class ReviewController extends Controller
{
    public function index(Request $request)
    {
        $query = Review::whereHas('product', function ($q) {
                $q->where('vendor_id', auth()->id());
            })
            ->with(['user', 'product']);

        if ($request->filled('status')) {
            $isApproved = $request->status === 'approved';
            $query->where('is_approved', $isApproved);
        }

        $reviews = $query->latest()->paginate(20);

        return view('vendor.reviews.index', compact('reviews'));
    }

    public function respond(Request $request, Review $review)
    {
        if ($review->product->vendor_id !== auth()->id()) {
            abort(403);
        }

        $validated = $request->validate([
            'vendor_response' => 'required|string|max:2000',
        ]);

        $review->update($validated);

        return back()->with('success', 'Response added successfully!');
    }
}
