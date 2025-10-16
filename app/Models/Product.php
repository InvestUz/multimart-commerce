<?php

namespace App\Models;

use App\Traits\HasRatings;
use App\Traits\Sluggable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;


class Product extends Model
{
    use HasFactory, SoftDeletes, HasRatings, Sluggable;

    protected $fillable = [
        'user_id',
        'category_id',
        'sub_category_id',  // Add this line
        'name',
        'slug',
        'description',
        'price',
        'old_price',
        'discount_percentage',
        'stock',
        'sku',
        'sizes',
        'colors',
        'specifications',
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
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'old_price' => 'decimal:2',
        'weight' => 'decimal:2',
        'is_active' => 'boolean',
        'is_featured' => 'boolean',
        'sizes' => 'array',
        'colors' => 'array',
        'specifications' => 'array',
        'average_rating' => 'decimal:2',
        'discount_percentage' => 'integer',
        'stock' => 'integer',
        'views' => 'integer',
        'total_reviews' => 'integer',
        'total_sales' => 'integer',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($product) {
            if (empty($product->slug)) {
                $product->slug = Str::slug($product->name);
            }
            if (empty($product->sku)) {
                $product->sku = 'SKU-' . strtoupper(Str::random(10));
            }
        });

        static::updating(function ($product) {
            if ($product->isDirty('name') && !$product->isDirty('slug')) {
                $product->slug = Str::slug($product->name);
            }
        });
    }

    // Add this relationship:
    public function subCategory()
    {
        return $this->belongsTo(SubCategory::class);
    }

    // Add this scope:
    public function scopeBySubCategory($query, $subCategoryId)
    {
        return $query->where('sub_category_id', $subCategoryId);
    }

    // Relationships
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

    public function images()
    {
        return $this->hasMany(ProductImage::class)->orderBy('order');
    }

    public function primaryImage()
    {
        return $this->hasOne(ProductImage::class)->where('is_primary', true);
    }

    public function reviews()
    {
        return $this->hasMany(Review::class);
    }

    public function approvedReviews()
    {
        return $this->hasMany(Review::class)->where('is_approved', true);
    }

    public function carts()
    {
        return $this->hasMany(Cart::class);
    }

    public function wishlists()
    {
        return $this->hasMany(Wishlist::class);
    }

    public function orderItems()
    {
        return $this->hasMany(OrderItem::class);
    }

    public function views()
    {
        return $this->hasMany(ProductView::class);
    }

    // Scopes
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

    public function scopeByVendor($query, $vendorId)
    {
        return $query->where('user_id', $vendorId);
    }

    public function scopeByCategory($query, $categoryId)
    {
        return $query->where('category_id', $categoryId);
    }

    public function scopeSearch($query, $term)
    {
        return $query->where(function ($q) use ($term) {
            $q->where('name', 'like', "%{$term}%")
                ->orWhere('description', 'like', "%{$term}%")
                ->orWhere('sku', 'like', "%{$term}%")
                ->orWhere('brand', 'like', "%{$term}%");
        });
    }

    // Helper methods
    public function hasDiscount()
    {
        return $this->old_price && $this->old_price > $this->price;
    }

    public function calculateDiscount()
    {
        if ($this->hasDiscount()) {
            return round((($this->old_price - $this->price) / $this->old_price) * 100);
        }
        return 0;
    }

    public function updateRating()
    {
        $reviews = $this->reviews()->where('is_approved', true);
        $this->average_rating = $reviews->avg('rating') ?? 0;
        $this->total_reviews = $reviews->count();
        $this->save();
    }

    public function incrementViews()
    {
        $this->increment('views');
    }

    public function isInStock()
    {
        return $this->stock > 0;
    }

    public function canBePurchased($quantity = 1)
    {
        return $this->is_active && $this->stock >= $quantity;
    }

    // Accessors
    public function getPrimaryImageUrlAttribute()
    {
        if ($this->primaryImage) {
            return asset('storage/' . $this->primaryImage->image_path);
        }
        return asset('images/no-image.png');
    }

    public function getFormattedPriceAttribute()
    {
        return '$' . number_format($this->price, 2);
    }

    public function getFormattedOldPriceAttribute()
    {
        return $this->old_price ? '$' . number_format($this->old_price, 2) : null;
    }

    public function getStockStatusAttribute()
    {
        if ($this->stock > 10) {
            return 'In Stock';
        } elseif ($this->stock > 0) {
            return 'Low Stock';
        }
        return 'Out of Stock';
    }

    public function getStockStatusColorAttribute()
    {
        if ($this->stock > 10) {
            return 'green';
        } elseif ($this->stock > 0) {
            return 'orange';
        }
        return 'red';
    }
}
