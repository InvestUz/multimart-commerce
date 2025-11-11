<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Cart extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'product_id',
        'quantity',
        'size',
        'color',
        'price',
    ];

    protected $casts = [
        'quantity' => 'integer',
        'price' => 'decimal:2',
    ];

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

    // ============================================
    // Scopes
    // ============================================

    public function scopeByUser($query, $userId)
    {
        return $query->where('user_id', $userId);
    }

    // ============================================
    // Accessors
    // ============================================

    public function getSubtotalAttribute(): float
    {
        return $this->quantity * $this->price;
    }

    public function getFormattedPriceAttribute(): string
    {
        return '$' . number_format($this->price, 2);
    }

    public function getFormattedSubtotalAttribute(): string
    {
        return '$' . number_format($this->subtotal, 2);
    }

    // ============================================
    // Business Logic Methods
    // ============================================

    public function updateQuantity(int $quantity): bool
    {
        if ($quantity <= 0) {
            $this->delete();
            return false;
        }

        // Check if product has enough stock
        if ($this->product->stock < $quantity) {
            return false;
        }

        $this->update(['quantity' => $quantity]);
        return true;
    }

    public function incrementQuantity(int $amount = 1): bool
    {
        return $this->updateQuantity($this->quantity + $amount);
    }

    public function decrementQuantity(int $amount = 1): bool
    {
        return $this->updateQuantity($this->quantity - $amount);
    }

    public function isAvailable(): bool
    {
        if (!$this->product->is_active) {
            return false;
        }

        if (!$this->product->is_in_stock) {
            return false;
        }

        if ($this->product->stock < $this->quantity) {
            return false;
        }

        return true;
    }

    public function updatePrice(): void
    {
        $this->update(['price' => $this->product->effective_price]);
    }

    public static function getTotalForUser(User $user): float
    {
        return static::where('user_id', $user->id)
            ->get()
            ->sum('subtotal');
    }

    public static function getItemsCountForUser(User $user): int
    {
        return static::where('user_id', $user->id)->sum('quantity');
    }

    public static function clearForUser(User $user): void
    {
        static::where('user_id', $user->id)->delete();
    }
}
