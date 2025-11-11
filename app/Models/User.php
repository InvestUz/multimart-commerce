<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable implements MustVerifyEmail
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'phone',
        'address',
        'store_name',
        'store_description',
        'store_logo',
        'is_active',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
        'is_active' => 'boolean',
    ];

    // ============================================
    // Role Checks
    // ============================================

    public function isSuperAdmin(): bool
    {
        return $this->role === 'super_admin';
    }

    public function isVendor(): bool
    {
        return $this->role === 'vendor';
    }

    public function isCustomer(): bool
    {
        return $this->role === 'customer';
    }

    // ============================================
    // Relationships - Products
    // ============================================

    public function products()
    {
        return $this->hasMany(Product::class);
    }

    public function activeProducts()
    {
        return $this->hasMany(Product::class)->where('is_active', true);
    }

    // ============================================
    // Relationships - Orders
    // ============================================

    public function orders()
    {
        return $this->hasMany(Order::class);
    }

    public function vendorOrderItems()
    {
        return $this->hasMany(OrderItem::class, 'vendor_id');
    }

    // ============================================
    // Relationships - Cart & Wishlist
    // ============================================

    public function carts()
    {
        return $this->hasMany(Cart::class);
    }

    public function wishlists()
    {
        return $this->hasMany(Wishlist::class);
    }

    public function wishlistProducts()
    {
        return $this->belongsToMany(Product::class, 'wishlists');
    }

    // ============================================
    // Relationships - Reviews
    // ============================================

    public function reviews()
    {
        return $this->hasMany(Review::class);
    }

    // ============================================
    // Relationships - Addresses
    // ============================================

    public function addresses()
    {
        return $this->hasMany(UserAddress::class);
    }

    public function defaultAddress()
    {
        return $this->hasOne(UserAddress::class)->where('is_default', true);
    }

    // ============================================
    // Relationships - Support
    // ============================================

    public function supportTickets()
    {
        return $this->hasMany(SupportTicket::class);
    }

    public function assignedTickets()
    {
        return $this->hasMany(SupportTicket::class, 'assigned_to');
    }

    // ============================================
    // Relationships - Vendor Specific
    // ============================================

    public function followers()
    {
        return $this->hasMany(VendorFollower::class, 'vendor_id');
    }

    public function following()
    {
        return $this->hasMany(VendorFollower::class, 'user_id');
    }

    public function followingVendors()
    {
        return $this->belongsToMany(User::class, 'vendor_followers', 'user_id', 'vendor_id');
    }

    public function payouts()
    {
        return $this->hasMany(VendorPayout::class, 'vendor_id');
    }

    public function bankAccounts()
    {
        return $this->hasMany(VendorBankAccount::class, 'vendor_id');
    }

    public function primaryBankAccount()
    {
        return $this->hasOne(VendorBankAccount::class, 'vendor_id')->where('is_primary', true);
    }

    // ============================================
    // Relationships - Messaging
    // ============================================

    public function customerConversations()
    {
        return $this->hasMany(Conversation::class, 'customer_id');
    }

    public function vendorConversations()
    {
        return $this->hasMany(Conversation::class, 'vendor_id');
    }

    public function messages()
    {
        return $this->hasMany(Message::class, 'sender_id');
    }

    // ============================================
    // Relationships - Activity
    // ============================================

    public function activityLogs()
    {
        return $this->hasMany(ActivityLog::class);
    }

    // ============================================
    // Scopes
    // ============================================

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeVendors($query)
    {
        return $query->where('role', 'vendor');
    }

    public function scopeCustomers($query)
    {
        return $query->where('role', 'customer');
    }

    public function scopeAdmins($query)
    {
        return $query->where('role', 'super_admin');
    }

    // ============================================
    // Accessors
    // ============================================

    public function getFullAddressAttribute(): ?string
    {
        return $this->address;
    }

    public function getStoreLogoUrlAttribute(): ?string
    {
        return $this->store_logo ? asset('storage/' . $this->store_logo) : null;
    }

    // ============================================
    // Business Logic Methods
    // ============================================

    public function getTotalSalesAttribute()
    {
        if (!$this->isVendor()) {
            return 0;
        }

        return $this->products()->sum('total_sales');
    }

    public function getTotalRevenueAttribute()
    {
        if (!$this->isVendor()) {
            return 0;
        }

        return $this->vendorOrderItems()
            ->whereHas('order', function($q) {
                $q->where('payment_status', 'paid');
            })
            ->sum('total');
    }

    public function getFollowersCountAttribute()
    {
        if (!$this->isVendor()) {
            return 0;
        }

        return $this->followers()->count();
    }

    public function isFollowedBy(User $user): bool
    {
        return $this->followers()->where('user_id', $user->id)->exists();
    }

    public function hasProductInCart(Product $product): bool
    {
        return $this->carts()->where('product_id', $product->id)->exists();
    }

    public function hasProductInWishlist(Product $product): bool
    {
        return $this->wishlists()->where('product_id', $product->id)->exists();
    }

    public function getCartTotalAttribute()
    {
        return $this->carts()->sum(\DB::raw('quantity * price'));
    }

    public function getCartItemsCountAttribute()
    {
        return $this->carts()->sum('quantity');
    }

    public function getPendingOrdersCountAttribute()
    {
        return $this->orders()->where('status', 'pending')->count();
    }

    public function getUnreadNotificationsCountAttribute()
    {
        return $this->unreadNotifications()->count();
    }
}
