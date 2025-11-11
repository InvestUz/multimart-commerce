<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Review extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'product_id',
        'order_id',
        'rating',
        'comment',
        'images',
        'is_approved',
        'is_verified_purchase',
        'admin_response',
        'responded_at',
        'helpful_count',
        'not_helpful_count',
    ];

    protected $casts = [
        'rating' => 'integer',
        'images' => 'array',
        'is_approved' => 'boolean',
        'is_verified_purchase' => 'boolean',
        'helpful_count' => 'integer',
        'not_helpful_count' => 'integer',
        'responded_at' => 'datetime',
    ];

    // ============================================
    // Boot Method
    // ============================================

    protected static function boot()
    {
        parent::boot();

        static::created(function ($review) {
            $review->product->updateRating();
        });

        static::updated(function ($review) {
            if ($review->isDirty(['rating', 'is_approved'])) {
                $review->product->updateRating();
            }
        });

        static::deleted(function ($review) {
            $review->product->updateRating();
        });
    }

    // ============================================
    // Relationships
    // ============================================

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    // ============================================
    // Scopes
    // ============================================

    public function scopeApproved($query)
    {
        return $query->where('is_approved', true);
    }

    public function scopeVerifiedPurchase($query)
    {
        return $query->where('is_verified_purchase', true);
    }

    public function scopeByRating($query, $rating)
    {
        return $query->where('rating', $rating);
    }

    public function scopeHighRated($query)
    {
        return $query->where('rating', '>=', 4);
    }

    public function scopeLowRated($query)
    {
        return $query->where('rating', '<=', 2);
    }

    public function scopeRecent($query)
    {
        return $query->orderBy('created_at', 'desc');
    }

    public function scopeHelpful($query)
    {
        return $query->orderBy('helpful_count', 'desc');
    }

    // ============================================
    // Accessors
    // ============================================

    public function getRatingStarsAttribute(): string
    {
        return str_repeat('★', $this->rating) . str_repeat('☆', 5 - $this->rating);
    }

    public function getHelpfulPercentageAttribute(): int
    {
        $total = $this->helpful_count + $this->not_helpful_count;
        if ($total === 0) {
            return 0;
        }
        return round(($this->helpful_count / $total) * 100);
    }

    // ============================================
    // Business Logic Methods
    // ============================================

    public function approve(): void
    {
        $this->update(['is_approved' => true]);
    }

    public function reject(): void
    {
        $this->update(['is_approved' => false]);
    }

    public function addAdminResponse(string $response): void
    {
        $this->update([
            'admin_response' => $response,
            'responded_at' => now(),
        ]);
    }

    public function markHelpful(): void
    {
        $this->increment('helpful_count');
    }

    public function markNotHelpful(): void
    {
        $this->increment('not_helpful_count');
    }

    public function hasImages(): bool
    {
        return !empty($this->images);
    }
}
