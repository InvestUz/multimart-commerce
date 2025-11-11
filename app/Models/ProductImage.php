<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductImage extends Model
{
    use HasFactory;

    protected $fillable = [
        'product_id',
        'image_path',
        'order',
        'is_primary',
    ];

    protected $casts = [
        'order' => 'integer',
        'is_primary' => 'boolean',
    ];

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

    public function scopePrimary($query)
    {
        return $query->where('is_primary', true);
    }

    public function scopeOrdered($query)
    {
        return $query->orderBy('order');
    }

    // ============================================
    // Accessors
    // ============================================

    public function getImageUrlAttribute(): string
    {
        return asset('storage/' . $this->image_path);
    }

    public function getThumbnailUrlAttribute(): string
    {
        // Assuming you have thumbnail generation logic
        $path = str_replace('products/', 'products/thumbnails/', $this->image_path);
        return asset('storage/' . $path);
    }

    // ============================================
    // Business Logic Methods
    // ============================================

    public function makePrimary(): void
    {
        // Remove primary from all other images
        $this->product->images()->update(['is_primary' => false]);

        // Make this image primary
        $this->update(['is_primary' => true]);
    }
}
