<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Support\Facades\DB;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'phone',
        'role',
        'is_active',
        'email_verified_at',
        'avatar',
        'store_name',
        'store_description',
        'store_logo',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
        'is_active' => 'boolean',
    ];

    /**
     * Check if user is super admin
     */
    public function isSuperAdmin(): bool
    {
        return $this->role === 'super_admin';
    }

    /**
     * Check if user is admin
     */
    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }

    /**
     * Check if user is vendor
     */
    public function isVendor(): bool
    {
        return $this->role === 'vendor';
    }

    /**
     * Check if user is customer
     */
    public function isCustomer(): bool
    {
        return $this->role === 'customer';
    }

    /**
     * Products created by this vendor
     */
    public function products()
    {
        return $this->hasMany(Product::class);
    }

    /**
     * Orders placed by this customer
     */
    public function orders()
    {
        return $this->hasMany(Order::class);
    }

    /**
     * Order items that belong to this vendor
     */
    public function vendorOrderItems()
    {
        return $this->hasMany(OrderItem::class, 'vendor_id');
    }

    /**
     * Get total revenue for this vendor
     */
    public function getTotalRevenueAttribute()
    {
        return $this->vendorOrderItems()
            ->whereHas('order', function($query) {
                $query->where('payment_status', 'paid');
            })
            ->sum('total');
    }

    /**
     * Get count of orders containing this vendor's products
     */
    public function getVendorOrdersCountAttribute()
    {
        return $this->vendorOrderItems()
            ->distinct('order_id')
            ->count('order_id');
    }

    /**
     * Get unique orders that contain this vendor's products
     */
    public function getVendorOrders()
    {
        $orderIds = $this->vendorOrderItems()->pluck('order_id')->unique();
        return Order::whereIn('id', $orderIds);
    }

    /**
     * Scope to add vendor revenue
     */
    public function scopeWithVendorRevenue($query)
    {
        return $query->addSelect([
            'vendor_revenue' => OrderItem::selectRaw('COALESCE(SUM(total), 0)')
                ->whereColumn('vendor_id', 'users.id')
                ->whereHas('order', function($q) {
                    $q->where('payment_status', 'paid');
                })
        ]);
    }

    /**
     * Addresses belonging to this user
     */
    public function addresses()
    {
        return $this->hasMany(UserAddress::class);
    }

    /**
     * Reviews written by this user
     */
    public function reviews()
    {
        return $this->hasMany(Review::class);
    }

    /**
     * Cart items for this user
     */
    public function cart()
    {
        return $this->hasMany(Cart::class);
    }

    /**
     * Wishlist items for this user
     */
    public function wishlist()
    {
        return $this->hasMany(Wishlist::class);
    }

    /**
     * Notifications for this user
     */
    public function notifications()
    {
        return $this->hasMany(Notification::class);
    }

    /**
     * Support tickets created by this user
     */
    public function supportTickets()
    {
        return $this->hasMany(SupportTicket::class);
    }

    /**
     * Refunds requested by this user
     */
    public function refunds()
    {
        return $this->hasMany(Refund::class);
    }

    /**
     * Get the default shipping address
     */
    public function defaultShippingAddress()
    {
        return $this->hasOne(UserAddress::class)->where('type', 'shipping')->where('is_default', true);
    }

    /**
     * Get the default billing address
     */
    public function defaultBillingAddress()
    {
        return $this->hasOne(UserAddress::class)->where('type', 'billing')->where('is_default', true);
    }
}
