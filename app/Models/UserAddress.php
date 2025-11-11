<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserAddress extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id', 'label', 'full_name', 'phone', 'address_line1',
        'address_line2', 'city', 'state', 'postal_code', 'country', 'is_default',
    ];

    protected $casts = ['is_default' => 'boolean'];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($address) {
            if ($address->is_default) {
                static::where('user_id', $address->user_id)->update(['is_default' => false]);
            }
        });

        static::updating(function ($address) {
            if ($address->isDirty('is_default') && $address->is_default) {
                static::where('user_id', $address->user_id)
                    ->where('id', '!=', $address->id)
                    ->update(['is_default' => false]);
            }
        });
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function scopeByUser($query, $userId)
    {
        return $query->where('user_id', $userId);
    }

    public function scopeDefault($query)
    {
        return $query->where('is_default', true);
    }

    public function getFullAddressAttribute(): string
    {
        $parts = [
            $this->address_line1, $this->address_line2, $this->city,
            $this->state, $this->postal_code, $this->country,
        ];
        return implode(', ', array_filter($parts));
    }

    public function makeDefault(): void
    {
        static::where('user_id', $this->user_id)->update(['is_default' => false]);
        $this->update(['is_default' => true]);
    }
}
