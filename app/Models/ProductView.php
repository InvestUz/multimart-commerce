<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductView extends Model
{
    use HasFactory;

    public $timestamps = false;

    protected $fillable = ['product_id', 'user_id', 'ip_address', 'user_agent', 'viewed_at'];

    protected $casts = ['viewed_at' => 'datetime'];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function scopeRecent($query, $days = 30)
    {
        return $query->where('viewed_at', '>=', now()->subDays($days));
    }

    public function scopeByProduct($query, $productId)
    {
        return $query->where('product_id', $productId);
    }
}
