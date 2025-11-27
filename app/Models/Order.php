<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_number',
        'user_id',
        'customer_name',
        'customer_email',
        'customer_phone',
        'shipping_address',
        'city',
        'state',
        'postal_code',
        'country',
        'subtotal',
        'shipping_cost',
        'tax',
        'discount',
        'total',
        'payment_method',
        'payment_status',
        'status',
        'notes',
        'admin_notes',
        'paid_at',
        'shipped_at',
        'delivered_at',
    ];

    protected $casts = [
        'subtotal' => 'decimal:2',
        'shipping_cost' => 'decimal:2',
        'tax' => 'decimal:2',
        'discount' => 'decimal:2',
        'total' => 'decimal:2',
        'paid_at' => 'datetime',
        'shipped_at' => 'datetime',
        'delivered_at' => 'datetime',
    ];

    // ============================================
    // Boot Method
    // ============================================

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($order) {
            if (empty($order->order_number)) {
                $order->order_number = static::generateOrderNumber();
            }
        });

        static::updated(function ($order) {
            if ($order->isDirty('status')) {
                $order->addStatusHistory($order->status);
            }
        });
    }

    // ============================================
    // Relationships
    // ============================================

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function items()
    {
        return $this->hasMany(OrderItem::class);
    }

    public function statusHistory()
    {
        return $this->hasMany(OrderStatusHistory::class)->orderBy('created_at', 'desc');
    }

    public function refunds()
    {
        return $this->hasMany(Refund::class);
    }

    public function supportTickets()
    {
        return $this->hasMany(SupportTicket::class);
    }

    /**
     * Get the coupon used for the order.
     */
    public function coupon()
    {
        return $this->belongsTo(Coupon::class);
    }

    /**
     * Get the shipping address for the order.
     */
    public function shippingAddress()
    {
        return $this->belongsTo(UserAddress::class, 'shipping_address_id');
    }

    /**
     * Get the billing address for the order.
     */
    public function billingAddress()
    {
        return $this->belongsTo(UserAddress::class, 'billing_address_id');
    }

    // ============================================
    // Scopes
    // ============================================

    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopeProcessing($query)
    {
        return $query->where('status', 'processing');
    }

    public function scopeShipped($query)
    {
        return $query->where('status', 'shipped');
    }

    public function scopeDelivered($query)
    {
        return $query->where('status', 'delivered');
    }

    public function scopeCancelled($query)
    {
        return $query->where('status', 'cancelled');
    }

    public function scopePaid($query)
    {
        return $query->where('payment_status', 'paid');
    }

    public function scopeUnpaid($query)
    {
        return $query->where('payment_status', 'pending');
    }

    public function scopeByUser($query, $userId)
    {
        return $query->where('user_id', $userId);
    }

    public function scopeRecent($query)
    {
        return $query->orderBy('created_at', 'desc');
    }

    // ============================================
    // Accessors
    // ============================================

    public function getFormattedSubtotalAttribute(): string
    {
        return '$' . number_format($this->subtotal, 2);
    }

    public function getFormattedTotalAttribute(): string
    {
        return '$' . number_format($this->total, 2);
    }

    public function getFormattedShippingCostAttribute(): string
    {
        return '$' . number_format($this->shipping_cost, 2);
    }

    public function getFormattedTaxAttribute(): string
    {
        return '$' . number_format($this->tax, 2);
    }

    public function getFormattedDiscountAttribute(): string
    {
        return '$' . number_format($this->discount, 2);
    }

    public function getStatusBadgeAttribute(): string
    {
        $badges = [
            'pending' => 'warning',
            'processing' => 'info',
            'shipped' => 'primary',
            'delivered' => 'success',
            'cancelled' => 'danger',
            'returned' => 'secondary',
        ];

        return $badges[$this->status] ?? 'secondary';
    }

    public function getPaymentStatusBadgeAttribute(): string
    {
        $badges = [
            'pending' => 'warning',
            'paid' => 'success',
            'failed' => 'danger',
            'refunded' => 'info',
        ];

        return $badges[$this->payment_status] ?? 'secondary';
    }

    public function getFullAddressAttribute(): string
    {
        $parts = [
            $this->shipping_address,
            $this->city,
            $this->state,
            $this->postal_code,
            $this->country,
        ];

        return implode(', ', array_filter($parts));
    }

    // ============================================
    // Business Logic Methods
    // ============================================

    public static function generateOrderNumber(): string
    {
        do {
            $orderNumber = 'ORD-' . date('Ymd') . '-' . strtoupper(\Str::random(6));
        } while (static::where('order_number', $orderNumber)->exists());

        return $orderNumber;
    }

    public function updateStatus(string $status, ?string $note = null, ?User $updatedBy = null): void
    {
        $this->update(['status' => $status]);

        $this->addStatusHistory($status, $note, $updatedBy);

        // Update timestamps based on status
        switch ($status) {
            case 'shipped':
                $this->update(['shipped_at' => now()]);
                break;
            case 'delivered':
                $this->update(['delivered_at' => now()]);
                break;
        }

        // Send notification to customer
        $this->user->notify(new \App\Notifications\OrderStatusUpdated($this, $status));
    }

    public function markAsPaid(): void
    {
        $this->update([
            'payment_status' => 'paid',
            'paid_at' => now(),
        ]);
    }

    public function markAsShipped(): void
    {
        $this->updateStatus('shipped', 'Order has been shipped');
    }

    public function markAsDelivered(): void
    {
        $this->updateStatus('delivered', 'Order has been delivered');
    }

    public function cancel(?string $reason = null): void
    {
        if ($this->canBeCancelled()) {
            $this->updateStatus('cancelled', $reason);

            // Restore stock for all items
            foreach ($this->items as $item) {
                $item->product->incrementStock($item->quantity);
            }
        }
    }

    public function canBeCancelled(): bool
    {
        return in_array($this->status, ['pending', 'processing']);
    }

    public function canBeRefunded(): bool
    {
        return $this->payment_status === 'paid' &&
               in_array($this->status, ['delivered', 'shipped']);
    }

    public function isPaid(): bool
    {
        return $this->payment_status === 'paid';
    }

    public function isPending(): bool
    {
        return $this->status === 'pending';
    }

    public function isDelivered(): bool
    {
        return $this->status === 'delivered';
    }

    public function isCancelled(): bool
    {
        return $this->status === 'cancelled';
    }

    public function addStatusHistory(string $status, ?string $note = null, ?User $updatedBy = null): void
    {
        OrderStatusHistory::create([
            'order_id' => $this->id,
            'status' => $status,
            'note' => $note,
            'updated_by' => $updatedBy?->id,
            'customer_notified' => true,
        ]);
    }

    public function calculateTotals(): void
    {
        $subtotal = $this->items->sum('total');

        // Calculate tax (example: 10% of subtotal)
        $tax = $subtotal * 0.10;

        // Calculate shipping (can be based on rules)
        $shippingCost = $this->calculateShippingCost();

        // Apply discount if any
        $discount = $this->discount ?? 0;

        $total = $subtotal + $tax + $shippingCost - $discount;

        $this->update([
            'subtotal' => $subtotal,
            'tax' => $tax,
            'shipping_cost' => $shippingCost,
            'total' => $total,
        ]);
    }

    protected function calculateShippingCost(): float
    {
        // Free shipping for orders over $100
        if ($this->subtotal >= 100) {
            return 0;
        }

        return 4.99;
    }

    public static function createFromCart(User $user, array $shippingData, array $paymentData): self
    {
        $cartItems = Cart::where('user_id', $user->id)->get();

        if ($cartItems->isEmpty()) {
            throw new \Exception('Cart is empty');
        }

        // Create order
        $order = static::create([
            'user_id' => $user->id,
            'customer_name' => $shippingData['name'],
            'customer_email' => $shippingData['email'],
            'customer_phone' => $shippingData['phone'],
            'shipping_address' => $shippingData['address'],
            'city' => $shippingData['city'] ?? null,
            'state' => $shippingData['state'] ?? null,
            'postal_code' => $shippingData['postal_code'] ?? null,
            'country' => $shippingData['country'] ?? 'Uzbekistan',
            'payment_method' => $paymentData['method'],
            'notes' => $shippingData['notes'] ?? null,
        ]);

        // Create order items
        foreach ($cartItems as $cartItem) {
            OrderItem::create([
                'order_id' => $order->id,
                'product_id' => $cartItem->product_id,
                'vendor_id' => $cartItem->product->user_id,
                'product_name' => $cartItem->product->name,
                'product_sku' => $cartItem->product->sku,
                'quantity' => $cartItem->quantity,
                'price' => $cartItem->price,
                'size' => $cartItem->size,
                'color' => $cartItem->color,
                'total' => $cartItem->subtotal,
            ]);

            // Decrement stock
            $cartItem->product->decrementStock($cartItem->quantity);

            // Increment sales
            $cartItem->product->incrementSales($cartItem->quantity);
        }

        // Calculate totals
        $order->calculateTotals();

        // Clear cart
        Cart::clearForUser($user);

        return $order;
    }
}
