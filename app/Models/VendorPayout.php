<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class VendorPayout extends Model
{
    use HasFactory;

    protected $fillable = [
        'vendor_id', 'payout_number', 'amount', 'commission_rate',
        'commission_amount', 'net_amount', 'method', 'status',
        'period_start', 'period_end', 'total_orders', 'bank_details',
        'notes', 'admin_notes', 'processed_at', 'completed_at',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'commission_rate' => 'decimal:2',
        'commission_amount' => 'decimal:2',
        'net_amount' => 'decimal:2',
        'period_start' => 'date',
        'period_end' => 'date',
        'total_orders' => 'integer',
        'processed_at' => 'datetime',
        'completed_at' => 'datetime',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($payout) {
            if (empty($payout->payout_number)) {
                $payout->payout_number = 'PAY-' . date('Ymd') . '-' . strtoupper(Str::random(6));
            }
        });
    }

    public function vendor()
    {
        return $this->belongsTo(User::class, 'vendor_id');
    }

    public function scopeByVendor($query, $vendorId)
    {
        return $query->where('vendor_id', $vendorId);
    }

    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopeCompleted($query)
    {
        return $query->where('status', 'completed');
    }

    public function markAsProcessing(): void
    {
        $this->update(['status' => 'processing', 'processed_at' => now()]);
    }

    public function markAsCompleted(): void
    {
        $this->update(['status' => 'completed', 'completed_at' => now()]);
    }

    public function calculateCommission(float $commissionRate): void
    {
        $this->commission_rate = $commissionRate;
        $this->commission_amount = ($this->amount * $commissionRate) / 100;
        $this->net_amount = $this->amount - $this->commission_amount;
        $this->save();
    }
}
