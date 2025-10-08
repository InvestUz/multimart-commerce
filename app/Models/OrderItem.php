<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrderItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_id',
        'product_id',
        'vendor_id',
        'product_name',
        'product_sku',
        'quantity',
        'price',
        'size',
        'color',
        'total',
        'vendor_status',
        'vendor_notes',
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'total' => 'decimal:2',
        'quantity' => 'integer',
    ];

    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function vendor()
    {
        return $this->belongsTo(User::class, 'vendor_id');
    }

    public function getFormattedPriceAttribute()
    {
        return ' '. number_format($this->price, 2);
    }

    public function getFormattedTotalAttribute()
    {
        return ' '. number_format($this->total, 2);
    }

    public function getVendorStatusColorAttribute()
    {
        return [
            'pending' => 'yellow',
            'processing' => 'blue',
            'shipped' => 'purple',
            'delivered' => 'green',
        ][$this->vendor_status] ?? 'gray';
    }
}
