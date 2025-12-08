<?php

namespace App\Policies;

use App\Models\Product;
use App\Models\User;

class ProductPolicy
{
    /**
     * Determine if the user can view the product.
     */
    public function view(User $user, Product $product): bool
    {
        // Super admin can view all products
        if ($user->role === 'super_admin') {
            return true;
        }

        // Vendor can only view their own products
        if ($user->role === 'vendor') {
            return $product->user_id === $user->id;
        }

        return false;
    }

    /**
     * Determine if the user can create products.
     */
    public function create(User $user): bool
    {
        // Only vendors and super admin can create products
        return in_array($user->role, ['vendor', 'super_admin']);
    }

    /**
     * Determine if the user can update the product.
     */
    public function update(User $user, Product $product): bool
    {
        // Super admin can update all products
        if ($user->role === 'super_admin') {
            return true;
        }

        // Vendor can only update their own products
        if ($user->role === 'vendor') {
            return $product->user_id === $user->id;
        }

        return false;
    }

    /**
     * Determine if the user can delete the product.
     */
    public function delete(User $user, Product $product): bool
    {
        // Super admin can delete all products
        if ($user->role === 'super_admin') {
            return true;
        }

        // Vendor can only delete their own products
        if ($user->role === 'vendor') {
            return $product->user_id === $user->id;
        }

        return false;
    }
}
