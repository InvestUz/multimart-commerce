<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrderItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_id',
        'product_id',
        'vendor_id',
        'product_name',
        'product_sku',
        'quantity',
        'price',
        'size',
        'color',
        'total',
        'vendor_status',
        'vendor_notes',
        'payout_id',
    ];

    protected $casts = [
        'quantity' => 'integer',
        'price' => 'decimal:2',
        'total' => 'decimal:2',
    ];

    // ============================================
    // Relationships
    // ============================================

    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function vendor()
    {
        return $this->belongsTo(User::class, 'vendor_id');
    }

    public function refunds()
    {
        return $this->hasMany(Refund::class);
    }

    // ============================================
    // Scopes
    // ============================================

    public function scopeByVendor($query, $vendorId)
    {
        return $query->where('vendor_id', $vendorId);
    }

    public function scopeByProduct($query, $productId)
    {
        return $query->where('product_id', $productId);
    }

    public function scopePending($query)
    {
        return $query->where('vendor_status', 'pending');
    }

    public function scopeProcessing($query)
    {
        return $query->where('vendor_status', 'processing');
    }

    // ============================================
    // Accessors
    // ============================================

    public function getFormattedPriceAttribute(): string
    {
        return '$' . number_format($this->price, 2);
    }

    public function getFormattedTotalAttribute(): string
    {
        return '$' . number_format($this->total, 2);
    }

    public function getDisplayNameAttribute(): string
    {
        $parts = [$this->product_name];

        if ($this->size) {
            $parts[] = "Size: {$this->size}";
        }

        if ($this->color) {
            $parts[] = "Color: {$this->color}";
        }

        return implode(' - ', $parts);
    }

    // ============================================
    // Business Logic Methods
    // ============================================

    public function updateVendorStatus(string $status, ?string $notes = null): void
    {
        $this->update([
            'vendor_status' => $status,
            'vendor_notes' => $notes,
        ]);
    }

    public function canBeRefunded(): bool
    {
        return $this->order->payment_status === 'paid' &&
               !$this->refunds()->where('status', 'completed')->exists();
    }

    public function getTotalRefundedAttribute(): float
    {
        return $this->refunds()
            ->where('status', 'completed')
            ->sum('refund_amount');
    }
}
