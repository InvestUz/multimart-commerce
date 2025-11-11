<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class SubCategory extends Model
{
    use HasFactory;

    protected $fillable = [
        'category_id',
        'name',
        'slug',
        'icon',
        'color',
        'is_active',
        'order',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'order' => 'integer',
    ];

    // ============================================
    // Boot Method
    // ============================================

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($subCategory) {
            if (empty($subCategory->slug)) {
                $subCategory->slug = Str::slug($subCategory->name);
            }
        });

        static::updating(function ($subCategory) {
            if ($subCategory->isDirty('name') && empty($subCategory->slug)) {
                $subCategory->slug = Str::slug($subCategory->name);
            }
        });
    }

    // ============================================
    // Relationships
    // ============================================

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function products()
    {
        return $this->hasMany(Product::class);
    }

    public function activeProducts()
    {
        return $this->hasMany(Product::class)->where('is_active', true);
    }

    // ============================================
    // Scopes
    // ============================================

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeOrdered($query)
    {
        return $query->orderBy('order')->orderBy('name');
    }

    public function scopeByCategory($query, $categoryId)
    {
        return $query->where('category_id', $categoryId);
    }

    // ============================================
    // Accessors
    // ============================================

    public function getIconUrlAttribute(): ?string
    {
        return $this->icon ? asset('storage/' . $this->icon) : null;
    }

    public function getUrlAttribute(): string
    {
        return route('subcategories.show', ['category' => $this->category->slug, 'subcategory' => $this->slug]);
    }

    // ============================================
    // Business Logic Methods
    // ============================================

    public function getProductsCountAttribute()
    {
        return $this->products()->count();
    }

    public function getActiveProductsCountAttribute()
    {
        return $this->activeProducts()->count();
    }
}
