<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class VendorBankAccount extends Model
{
    use HasFactory;

    protected $fillable = [
        'vendor_id', 'bank_name', 'account_holder_name', 'account_number',
        'iban', 'swift_code', 'routing_number', 'branch_name',
        'branch_code', 'is_primary', 'is_verified',
    ];

    protected $casts = [
        'is_primary' => 'boolean',
        'is_verified' => 'boolean',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($account) {
            if ($account->is_primary) {
                static::where('vendor_id', $account->vendor_id)->update(['is_primary' => false]);
            }
        });

        static::updating(function ($account) {
            if ($account->isDirty('is_primary') && $account->is_primary) {
                static::where('vendor_id', $account->vendor_id)
                    ->where('id', '!=', $account->id)
                    ->update(['is_primary' => false]);
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

    public function scopePrimary($query)
    {
        return $query->where('is_primary', true);
    }

    public function scopeVerified($query)
    {
        return $query->where('is_verified', true);
    }

    public function makePrimary(): void
    {
        static::where('vendor_id', $this->vendor_id)->update(['is_primary' => false]);
        $this->update(['is_primary' => true]);
    }

    public function verify(): void
    {
        $this->update(['is_verified' => true]);
    }
}
