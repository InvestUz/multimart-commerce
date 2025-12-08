<?php

namespace App\Policies;

use App\Models\Order;
use App\Models\User;

class OrderPolicy
{
    /**
     * Determine if the user can view the order.
     */
    public function view(User $user, Order $order): bool
    {
        // User can view their own orders
        if ($user->id === $order->user_id) {
            return true;
        }

        // Super admin can view all orders
        if ($user->role === 'super_admin') {
            return true;
        }

        // Vendor can view orders containing their products
        if ($user->role === 'vendor') {
            return $order->items()->where('vendor_id', $user->id)->exists();
        }

        return false;
    }

    /**
     * Determine if the user can update the order.
     */
    public function update(User $user, Order $order): bool
    {
        // Only the customer who placed the order can cancel it
        if ($user->id === $order->user_id) {
            return true;
        }

        // Super admin can update any order
        if ($user->role === 'super_admin') {
            return true;
        }

        // Vendor can update orders containing their products
        if ($user->role === 'vendor') {
            return $order->items()->where('vendor_id', $user->id)->exists();
        }

        return false;
    }

    /**
     * Determine if the user can update order status.
     * This is specifically for status updates (not general order updates).
     */
    public function updateStatus(User $user, Order $order): bool
    {
        // Super admin can update any order status
        if ($user->role === 'super_admin') {
            return true;
        }

        // Vendor can update status for orders containing their products
        if ($user->role === 'vendor') {
            return $order->items()->where('vendor_id', $user->id)->exists();
        }

        return false;
    }

    /**
     * Determine if the user can update payment status.
     */
    public function updatePaymentStatus(User $user, Order $order): bool
    {
        // Only super admin can update payment status
        if ($user->role === 'super_admin') {
            return true;
        }

        // Vendor can also update payment status for their orders
        if ($user->role === 'vendor') {
            return $order->items()->where('vendor_id', $user->id)->exists();
        }

        return false;
    }

    /**
     * Determine if the user can delete the order.
     */
    public function delete(User $user, Order $order): bool
    {
        // Only super admin can delete orders
        return $user->role === 'super_admin';
    }
}
