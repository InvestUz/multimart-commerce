<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ShippingMethod extends Model
{
    use HasFactory;

    protected $fillable = [
        'name', 'description', 'cost', 'free_shipping_threshold',
        'estimated_delivery', 'is_active', 'order',
    ];

    protected $casts = [
        'cost' => 'decimal:2',
        'free_shipping_threshold' => 'decimal:2',
        'is_active' => 'boolean',
        'order' => 'integer',
    ];

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeOrdered($query)
    {
        return $query->orderBy('order');
    }

    public function calculateCost($cartTotal): float
    {
        if ($this->free_shipping_threshold && $cartTotal >= $this->free_shipping_threshold) {
            return 0;
        }
        return $this->cost;
    }

    public function isFreeFor($cartTotal): bool
    {
        return $this->free_shipping_threshold && $cartTotal >= $this->free_shipping_threshold;
    }
}
