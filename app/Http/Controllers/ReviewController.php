<?php

namespace App\Http\Controllers;

use App\Models\Review;
use App\Models\Product;
use App\Models\Order;
use Illuminate\Http\Request;

class ReviewController extends Controller
{
    public function store(Request $request)
    {
        $validated = $request->validate([
            'product_id' => 'required|exists:products,id',
            'order_id' => 'required|exists:orders,id',
            'rating' => 'required|integer|min:1|max:5',
            'title' => 'required|string|max:255',
            'comment' => 'required|string|max:2000',
        ]);

        $order = Order::findOrFail($validated['order_id']);
        $this->authorize('view', $order);

        // Check if order contains this product
        $orderItem = $order->items()
            ->where('product_id', $validated['product_id'])
            ->first();

        if (!$orderItem) {
            return back()->with('error', 'You can only review products you have purchased.');
        }

        // Check if already reviewed
        $existingReview = Review::where('user_id', auth()->id())
            ->where('product_id', $validated['product_id'])
            ->where('order_id', $validated['order_id'])
            ->first();

        if ($existingReview) {
            return back()->with('error', 'You have already reviewed this product.');
        }

        Review::create([
            'user_id' => auth()->id(),
            'product_id' => $validated['product_id'],
            'order_id' => $validated['order_id'],
            'rating' => $validated['rating'],
            'title' => $validated['title'],
            'comment' => $validated['comment'],
            'is_approved' => false,
        ]);

        return back()->with('success', 'Review submitted successfully! It will be visible after approval.');
    }

    public function update(Request $request, Review $review)
    {
        $this->authorize('update', $review);

        $validated = $request->validate([
            'rating' => 'required|integer|min:1|max:5',
            'title' => 'required|string|max:255',
            'comment' => 'required|string|max:2000',
        ]);

        $review->update($validated);

        return back()->with('success', 'Review updated successfully!');
    }

    public function destroy(Review $review)
    {
        $this->authorize('delete', $review);
        $review->delete();

        return back()->with('success', 'Review deleted successfully!');
    }
}
