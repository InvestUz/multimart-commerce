<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\App;

class Product extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'user_id',
        'vendor_id',
        'category_id',
        'sub_category_id',
        'brand_id',
        'name',
        'slug',
        'description',
        'short_description',
        'price',
        'compare_price',
        'cost_price',
        'old_price',
        'discount_percentage',
        'stock',
        'sku',
        'sizes',
        'colors',
        'specifications',
        'tags',
        'brand',
        'model',
        'condition',
        'weight',
        'dimensions',
        'is_active',
        'is_featured',
        'average_rating',
        'total_reviews',
        'views',
        'total_sales',
        'meta_title',
        'meta_description',
        'meta_keywords',
        // Multilingual fields
        'name_translations',
        'description_translations',
        'short_description_translations',
        'meta_title_translations',
        'meta_description_translations',
        'meta_keywords_translations',
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'old_price' => 'decimal:2',
        'discount_percentage' => 'integer',
        'stock' => 'integer',
        'sizes' => 'array',
        'colors' => 'array',
        'specifications' => 'array',
        'weight' => 'decimal:2',
        'is_active' => 'boolean',
        'is_featured' => 'boolean',
        'average_rating' => 'decimal:2',
        'total_reviews' => 'integer',
        'views' => 'integer',
        'total_sales' => 'integer',
        // Multilingual casts
        'name_translations' => 'array',
        'description_translations' => 'array',
        'short_description_translations' => 'array',
        'meta_title_translations' => 'array',
        'meta_description_translations' => 'array',
        'meta_keywords_translations' => 'array',
    ];

    protected $appends = ['discount_amount', 'is_in_stock'];

    // ============================================
    // Boot Method
    // ============================================

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($product) {
            if (empty($product->slug)) {
                $product->slug = Str::slug($product->name);
            }
            if (empty($product->sku)) {
                $product->sku = 'PRD-' . strtoupper(Str::random(8));
            }
        });

        static::updating(function ($product) {
            if ($product->isDirty('name') && empty($product->slug)) {
                $product->slug = Str::slug($product->name);
            }
        });
    }

    // ============================================
    // Relationships
    // ============================================

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function vendor()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function subCategory()
    {
        return $this->belongsTo(SubCategory::class);
    }

    public function brand()
    {
        return $this->belongsTo(Brand::class);
    }

    public function images()
    {
        return $this->hasMany(ProductImage::class)->orderBy('order');
    }

    public function primaryImage()
    {
        return $this->hasOne(ProductImage::class)->where('is_primary', true);
    }

    public function variants()
    {
        return $this->hasMany(ProductVariant::class);
    }

    public function activeVariants()
    {
        return $this->hasMany(ProductVariant::class)->where('is_active', true);
    }

    public function reviews()
    {
        return $this->hasMany(Review::class);
    }

    public function approvedReviews()
    {
        return $this->hasMany(Review::class)->where('is_approved', true);
    }

    public function wishlists()
    {
        return $this->hasMany(Wishlist::class);
    }

    public function carts()
    {
        return $this->hasMany(Cart::class);
    }

    public function orderItems()
    {
        return $this->hasMany(OrderItem::class);
    }

    public function views()
    {
        return $this->hasMany(ProductView::class);
    }

    public function flashSales()
    {
        return $this->belongsToMany(FlashSale::class, 'flash_sale_products')
            ->withPivot(['flash_price', 'discount_percentage', 'quantity_limit', 'quantity_sold', 'per_user_limit'])
            ->withTimestamps();
    }

    public function activeFlashSale()
    {
        return $this->belongsToMany(FlashSale::class, 'flash_sale_products')
            ->where('is_active', true)
            ->where('starts_at', '<=', now())
            ->where('ends_at', '>=', now())
            ->withPivot(['flash_price', 'discount_percentage', 'quantity_limit', 'quantity_sold', 'per_user_limit'])
            ->first();
    }

    // ============================================
    // Scopes
    // ============================================

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeFeatured($query)
    {
        return $query->where('is_featured', true);
    }

    public function scopeInStock($query)
    {
        return $query->where('stock', '>', 0);
    }

    public function scopeByCategory($query, $categoryId)
    {
        return $query->where('category_id', $categoryId);
    }

    public function scopeBySubCategory($query, $subCategoryId)
    {
        return $query->where('sub_category_id', $subCategoryId);
    }

    public function scopeByBrand($query, $brandId)
    {
        return $query->where('brand_id', $brandId);
    }

    public function scopeByVendor($query, $vendorId)
    {
        return $query->where('user_id', $vendorId);
    }

    public function scopePriceRange($query, $min, $max)
    {
        return $query->whereBetween('price', [$min, $max]);
    }

    public function scopeSearch($query, $search)
    {
        return $query->where(function($q) use ($search) {
            $q->where('name', 'like', "%{$search}%")
              ->orWhere('description', 'like', "%{$search}%")
              ->orWhere('sku', 'like', "%{$search}%");
        });
    }

    public function scopePopular($query)
    {
        return $query->orderBy('total_sales', 'desc');
    }

    public function scopeTrending($query)
    {
        return $query->orderBy('views', 'desc');
    }

    public function scopeTopRated($query)
    {
        return $query->where('total_reviews', '>', 0)
                     ->orderBy('average_rating', 'desc');
    }

    public function scopeLatest($query)
    {
        return $query->orderBy('created_at', 'desc');
    }

    public function scopeOnSale($query)
    {
        return $query->where('discount_percentage', '>', 0);
    }

    // ============================================
    // Accessors
    // ============================================

    public function getNameAttribute($value)
    {
        $locale = App::getLocale();
        $translations = $this->name_translations;

        if ($translations && isset($translations[$locale])) {
            return $translations[$locale];
        }

        return $value;
    }

    public function getDescriptionAttribute($value)
    {
        $locale = App::getLocale();
        $translations = $this->description_translations;

        if ($translations && isset($translations[$locale])) {
            return $translations[$locale];
        }

        return $value;
    }

    public function getShortDescriptionAttribute($value)
    {
        $locale = App::getLocale();
        $translations = $this->short_description_translations;

        if ($translations && isset($translations[$locale])) {
            return $translations[$locale];
        }

        return $value;
    }

    public function getMetaTitleAttribute($value)
    {
        $locale = App::getLocale();
        $translations = $this->meta_title_translations;

        if ($translations && isset($translations[$locale])) {
            return $translations[$locale];
        }

        return $value;
    }

    public function getMetaDescriptionAttribute($value)
    {
        $locale = App::getLocale();
        $translations = $this->meta_description_translations;

        if ($translations && isset($translations[$locale])) {
            return $translations[$locale];
        }

        return $value;
    }

    public function getMetaKeywordsAttribute($value)
    {
        $locale = App::getLocale();
        $translations = $this->meta_keywords_translations;

        if ($translations && isset($translations[$locale])) {
            return $translations[$locale];
        }

        return $value;
    }

    public function getDiscountAmountAttribute()
    {
        if ($this->old_price && $this->old_price > $this->price) {
            return $this->old_price - $this->price;
        }
        return 0;
    }

    public function getIsInStockAttribute(): bool
    {
        return $this->stock > 0;
    }

    public function getUrlAttribute(): string
    {
        return route('products.show', $this->slug);
    }

    public function getPrimaryImageUrlAttribute(): ?string
    {
        $primaryImage = $this->primaryImage;
        if ($primaryImage) {
            return asset('storage/' . $primaryImage->image_path);
        }

        $firstImage = $this->images()->first();
        return $firstImage ? asset('storage/' . $firstImage->image_path) : null;
    }

    public function getFormattedPriceAttribute(): string
    {
        return '$' . number_format($this->price, 2);
    }

    public function getFormattedOldPriceAttribute(): ?string
    {
        return $this->old_price ? '$' . number_format($this->old_price, 2) : null;
    }

    public function getRatingStarsAttribute(): string
    {
        $fullStars = floor($this->average_rating);
        $halfStar = ($this->average_rating - $fullStars) >= 0.5 ? 1 : 0;
        $emptyStars = 5 - $fullStars - $halfStar;

        return str_repeat('★', $fullStars) .
               str_repeat('☆', $halfStar) .
               str_repeat('☆', $emptyStars);
    }

    // ============================================
    // Business Logic Methods
    // ============================================

    public function incrementViews(?User $user = null, ?string $ipAddress = null): void
    {
        $this->increment('views');

        ProductView::create([
            'product_id' => $this->id,
            'user_id' => $user?->id,
            'ip_address' => $ipAddress,
            'user_agent' => request()->userAgent(),
            'viewed_at' => now(),
        ]);
    }

    public function updateRating(): void
    {
        $averageRating = $this->approvedReviews()->avg('rating');
        $totalReviews = $this->approvedReviews()->count();

        $this->update([
            'average_rating' => $averageRating ?? 0,
            'total_reviews' => $totalReviews,
        ]);
    }

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

    public function incrementSales(int $quantity = 1): void
    {
        $this->increment('total_sales', $quantity);
    }

    public function hasDiscount(): bool
    {
        return $this->discount_percentage > 0 || ($this->old_price && $this->old_price > $this->price);
    }

    public function isInWishlist(?User $user = null): bool
    {
        if (!$user) {
            return false;
        }
        return $this->wishlists()->where('user_id', $user->id)->exists();
    }

    public function isInCart(?User $user = null): bool
    {
        if (!$user) {
            return false;
        }
        return $this->carts()->where('user_id', $user->id)->exists();
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

    public function hasVariants(): bool
    {
        return $this->variants()->exists();
    }

    public function isOnFlashSale(): bool
    {
        return $this->activeFlashSale() !== null;
    }

    public function getFlashSalePrice(): ?float
    {
        $flashSale = $this->activeFlashSale();
        return $flashSale ? $flashSale->pivot->flash_price : null;
    }

    public function getEffectivePrice(): float
    {
        return $this->getFlashSalePrice() ?? $this->price;
    }

    // ============================================
    // Multilingual Methods
    // ============================================

    public function setNameTranslation(string $locale, string $value): void
    {
        $translations = $this->name_translations ?? [];
        $translations[$locale] = $value;
        $this->name_translations = $translations;
    }

    public function setDescriptionTranslation(string $locale, string $value): void
    {
        $translations = $this->description_translations ?? [];
        $translations[$locale] = $value;
        $this->description_translations = $translations;
    }

    public function setShortDescriptionTranslation(string $locale, string $value): void
    {
        $translations = $this->short_description_translations ?? [];
        $translations[$locale] = $value;
        $this->short_description_translations = $translations;
    }

    public function setMetaTitleTranslation(string $locale, string $value): void
    {
        $translations = $this->meta_title_translations ?? [];
        $translations[$locale] = $value;
        $this->meta_title_translations = $translations;
    }

    public function setMetaDescriptionTranslation(string $locale, string $value): void
    {
        $translations = $this->meta_description_translations ?? [];
        $translations[$locale] = $value;
        $this->meta_description_translations = $translations;
    }

    public function setMetaKeywordsTranslation(string $locale, string $value): void
    {
        $translations = $this->meta_keywords_translations ?? [];
        $translations[$locale] = $value;
        $this->meta_keywords_translations = $translations;
    }

    public function getAvailableLocales(): array
    {
        return ['en', 'ru', 'uz']; // Based on the language files in the project
    }
}
