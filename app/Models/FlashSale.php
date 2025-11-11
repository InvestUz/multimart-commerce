<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FlashSale extends Model
{
    use HasFactory;

    protected $fillable = [
        'name', 'description', 'banner_image',
        'starts_at', 'ends_at', 'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'starts_at' => 'datetime',
        'ends_at' => 'datetime',
    ];

    public function products()
    {
        return $this->belongsToMany(Product::class, 'flash_sale_products')
            ->withPivot(['flash_price', 'discount_percentage', 'quantity_limit', 'quantity_sold', 'per_user_limit'])
            ->withTimestamps();
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true)
            ->where('starts_at', '<=', now())
            ->where('ends_at', '>=', now());
    }

    public function scopeUpcoming($query)
    {
        return $query->where('is_active', true)
            ->where('starts_at', '>', now());
    }

    public function scopeExpired($query)
    {
        return $query->where('ends_at', '<', now());
    }

    public function isActive(): bool
    {
        return $this->is_active &&
               $this->starts_at <= now() &&
               $this->ends_at >= now();
    }

    public function isUpcoming(): bool
    {
        return $this->is_active && $this->starts_at > now();
    }

    public function isExpired(): bool
    {
        return $this->ends_at < now();
    }

    public function getBannerImageUrlAttribute(): ?string
    {
        return $this->banner_image ? asset('storage/' . $this->banner_image) : null;
    }

    public function getTimeRemainingAttribute(): ?string
    {
        if (!$this->isActive()) {
            return null;
        }

        return $this->ends_at->diffForHumans();
    }
}
