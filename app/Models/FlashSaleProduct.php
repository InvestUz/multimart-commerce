<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\Pivot;

class FlashSaleProduct extends Pivot
{
    protected $fillable = [
        'flash_sale_id', 'product_id', 'flash_price',
        'discount_percentage', 'quantity_limit',
        'quantity_sold', 'per_user_limit',
    ];

    protected $casts = [
        'flash_price' => 'decimal:2',
        'discount_percentage' => 'integer',
        'quantity_limit' => 'integer',
        'quantity_sold' => 'integer',
        'per_user_limit' => 'integer',
    ];

    public function flashSale()
    {
        return $this->belongsTo(FlashSale::class);
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function isAvailable(): bool
    {
        if ($this->quantity_limit) {
            return $this->quantity_sold < $this->quantity_limit;
        }
        return true;
    }

    public function canPurchase(int $quantity): bool
    {
        if (!$this->isAvailable()) {
            return false;
        }

        if ($this->quantity_limit) {
            return ($this->quantity_sold + $quantity) <= $this->quantity_limit;
        }

        return true;
    }

    public function incrementSold(int $quantity): void
    {
        $this->increment('quantity_sold', $quantity);
    }
}
