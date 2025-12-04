<?php

namespace App\Policies;

use App\Models\Review;
use App\Models\User;

class ReviewPolicy
{
    /**
     * Determine if the user can view the review.
     */
    public function view(User $user, Review $review): bool
    {
        return true; // Reviews are public
    }

    /**
     * Determine if the user can create a review.
     */
    public function create(User $user): bool
    {
        return $user->role === 'customer';
    }

    /**
     * Determine if the user can update the review.
     */
    public function update(User $user, Review $review): bool
    {
        // Only the author can update their review
        return $user->id === $review->user_id;
    }

    /**
     * Determine if the user can delete the review.
     */
    public function delete(User $user, Review $review): bool
    {
        // Author or super admin can delete
        return $user->id === $review->user_id || $user->role === 'super_admin';
    }
}
