<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class ProductVariant extends Model
{
    use HasFactory;

    protected $fillable = [
        'product_id',
        'sku',
        'size',
        'color',
        'price',
        'stock',
        'image',
        'is_active',
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'stock' => 'integer',
        'is_active' => 'boolean',
    ];

    // ============================================
    // Boot Method
    // ============================================

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($variant) {
            if (empty($variant->sku)) {
                $variant->sku = 'VAR-' . strtoupper(Str::random(8));
            }
        });
    }

    // ============================================
    // Relationships
    // ============================================

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    // ============================================
    // Scopes
    // ============================================

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeInStock($query)
    {
        return $query->where('stock', '>', 0);
    }

    public function scopeBySize($query, $size)
    {
        return $query->where('size', $size);
    }

    public function scopeByColor($query, $color)
    {
        return $query->where('color', $color);
    }

    // ============================================
    // Accessors
    // ============================================

    public function getImageUrlAttribute(): ?string
    {
        return $this->image ? asset('storage/' . $this->image) : $this->product->primary_image_url;
    }

    public function getEffectivePriceAttribute(): float
    {
        return $this->price ?? $this->product->price;
    }

    public function getIsInStockAttribute(): bool
    {
        return $this->stock > 0;
    }

    public function getDisplayNameAttribute(): string
    {
        $parts = [$this->product->name];

        if ($this->size) {
            $parts[] = $this->size;
        }

        if ($this->color) {
            $parts[] = $this->color;
        }

        return implode(' - ', $parts);
    }

    // ============================================
    // Business Logic Methods
    // ============================================

    public function decrementStock(int $quantity): bool
    {
        if ($this->stock >= $quantity) {
            $this->decrement('stock', $quantity);
            return true;
        }
        return false;
    }

    public function incrementStock(int $quantity): void
    {
        $this->increment('stock', $quantity);
    }

    public function getStockStatusAttribute(): string
    {
        if ($this->stock > 10) {
            return 'In Stock';
        } elseif ($this->stock > 0) {
            return 'Low Stock';
        }
        return 'Out of Stock';
    }
}
