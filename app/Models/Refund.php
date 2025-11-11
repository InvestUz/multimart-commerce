<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Refund extends Model
{
    use HasFactory;

    protected $fillable = [
        'refund_number', 'order_id', 'order_item_id', 'user_id',
        'reason', 'description', 'images', 'refund_amount',
        'status', 'refund_method', 'admin_notes', 'approved_at',
        'rejected_at', 'completed_at',
    ];

    protected $casts = [
        'images' => 'array',
        'refund_amount' => 'decimal:2',
        'approved_at' => 'datetime',
        'rejected_at' => 'datetime',
        'completed_at' => 'datetime',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($refund) {
            if (empty($refund->refund_number)) {
                $refund->refund_number = 'REF-' . date('Ymd') . '-' . strtoupper(Str::random(6));
            }
        });
    }

    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    public function orderItem()
    {
        return $this->belongsTo(OrderItem::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopeApproved($query)
    {
        return $query->where('status', 'approved');
    }

    public function scopeRejected($query)
    {
        return $query->where('status', 'rejected');
    }

    public function scopeCompleted($query)
    {
        return $query->where('status', 'completed');
    }

    public function approve(?string $notes = null): void
    {
        $this->update([
            'status' => 'approved',
            'admin_notes' => $notes,
            'approved_at' => now(),
        ]);
    }

    public function reject(?string $notes = null): void
    {
        $this->update([
            'status' => 'rejected',
            'admin_notes' => $notes,
            'rejected_at' => now(),
        ]);
    }

    public function complete(): void
    {
        $this->update([
            'status' => 'completed',
            'completed_at' => now(),
        ]);

        // Update order payment status if needed
        if ($this->order->payment_status === 'paid') {
            $this->order->update(['payment_status' => 'refunded']);
        }

        // Restore stock
        if ($this->orderItem) {
            $this->orderItem->product->incrementStock($this->orderItem->quantity);
        }
    }
}
