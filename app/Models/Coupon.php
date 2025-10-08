<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Coupon extends Model
{
    use HasFactory;

    protected $fillable = [
        'code',
        'type',
        'value',
        'min_purchase',
        'max_uses',
        'used_count',
        'starts_at',
        'expires_at',
        'is_active',
    ];

    protected $casts = [
        'value' => 'decimal:2',
        'min_purchase' => 'decimal:2',
        'max_uses' => 'integer',
        'used_count' => 'integer',
        'is_active' => 'boolean',
        'starts_at' => 'datetime',
        'expires_at' => 'datetime',
    ];

    public function scopeActive($query)
    {
        return $query->where('is_active', true)
            ->where(function ($q) {
                $q->whereNull('starts_at')
                    ->orWhere('starts_at', '<=', now());
            })
            ->where(function ($q) {
                $q->whereNull('expires_at')
                    ->orWhere('expires_at', '>=', now());
            });
    }

    public function isValid()
    {
        // Check if active
        if (!$this->is_active) {
            return false;
        }

        // Check start date
        if ($this->starts_at && $this->starts_at->isFuture()) {
            return false;
        }

        // Check expiry date
        if ($this->expires_at && $this->expires_at->isPast()) {
            return false;
        }

        // Check usage limit
        if ($this->max_uses && $this->used_count >= $this->max_uses) {
            return false;
        }

        return true;
    }

    public function canBeUsed($cartTotal)
    {
        return $this->isValid() && $cartTotal >= $this->min_purchase;
    }

    public function calculateDiscount($cartTotal)
    {
        if (!$this->canBeUsed($cartTotal)) {
            return 0;
        }

        if ($this->type === 'percentage') {
            return ($cartTotal * $this->value) / 100;
        }

        return $this->value;
    }

    public function incrementUsage()
    {
        $this->increment('used_count');
    }
}
