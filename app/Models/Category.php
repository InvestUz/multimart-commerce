<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Category extends Model
{
    use HasFactory;

    protected $fillable = [
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

        static::creating(function ($category) {
            if (empty($category->slug)) {
                $category->slug = Str::slug($category->name);
            }
        });

        static::updating(function ($category) {
            if ($category->isDirty('name') && empty($category->slug)) {
                $category->slug = Str::slug($category->name);
            }
        });
    }

    // ============================================
    // Relationships
    // ============================================

    public function subCategories()
    {
        return $this->hasMany(SubCategory::class);
    }

    public function activeSubCategories()
    {
        return $this->hasMany(SubCategory::class)->where('is_active', true)->orderBy('order');
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

    public function scopeWithCounts($query)
    {
        return $query->withCount(['products', 'activeProducts', 'subCategories']);
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
        return route('categories.show', $this->slug);
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

    public function hasProducts(): bool
    {
        return $this->products()->exists();
    }

    public function hasActiveProducts(): bool
    {
        return $this->activeProducts()->exists();
    }
}
