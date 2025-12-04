<?php

namespace App\Http\Controllers;

use App\Models\Review;
use App\Models\Product;
use App\Models\Order;
use App\Models\User;
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

        // Check if order is delivered (optional - remove if you want to allow reviews anytime)
        if (!in_array($order->status, ['delivered', 'completed'])) {
            return back()->with('error', 'You can only review products from delivered orders.');
        }

        $review = Review::create([
            'user_id' => auth()->id(),
            'product_id' => $validated['product_id'],
            'order_id' => $validated['order_id'],
            'rating' => $validated['rating'],
            'title' => $validated['title'] ?? null,
            'comment' => $validated['comment'],
            'is_approved' => false,
            'is_verified_purchase' => true, // Mark as verified purchase
        ]);

        // Notify vendor and admins
        $this->notifyAboutNewReview($review);

        return back()->with('success', 'Review submitted successfully! It will be visible after approval.');
    }

    /**
     * Notify vendor and admins about new review
     */
    protected function notifyAboutNewReview(Review $review)
    {
        // Notify vendor
        $product = $review->product;
        if ($product->vendor) {
            $product->vendor->notify(new \App\Notifications\NewReviewPosted($review));
        }

        // Notify admins
        $admins = User::where('role', 'super_admin')->get();
        foreach ($admins as $admin) {
            $admin->notify(new \App\Notifications\NewReviewPosted($review));
        }
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
